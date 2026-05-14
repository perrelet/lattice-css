<?php

namespace Lattice\Css;

/**
 * Reads tokens from a CSS file and exposes them to PHP.
 *
 * Tokens are declared inside :root { ... } as `--name: value;`. Values may be
 * literals, var() chains, or opaque expressions (color-mix, calc, etc).
 *
 * - get($name)  resolved value (var() chains followed; opaque expressions
 *               returned verbatim; null if undefined)
 * - raw($name)  literal value as authored, no resolution
 * - all()       full map; resolved by default
 *
 * Output is cached per file mtime + content hash via WP transients.
 */
class Tokens {

    const MAX_DEPTH = 16;

    private static ?array $raw      = null;
    private static ?array $resolved = null;

    public static function get (string $name): ?string {

        self::load();
        $name = ltrim($name, '-');
        return self::$resolved[$name] ?? null;

    }

    public static function raw (string $name): ?string {

        self::load();
        $name = ltrim($name, '-');
        return self::$raw[$name] ?? null;

    }

    public static function all (bool $resolved = true): array {

        self::load();
        return $resolved ? self::$resolved : self::$raw;

    }

    public static function path (): string {

        if (defined('LATTICE_CSS_TOKENS_PATH')) {
            $default = LATTICE_CSS_TOKENS_PATH;
        } else {
            $default = dirname(rtrim(LATTICE_CSS_PATH, '/')) . '/assets/css/tokens.css';
        }

        return apply_filters('lattice_css_tokens_path', $default);

    }

    public static function flush (): void {

        self::$raw      = null;
        self::$resolved = null;

    }

    //

    private static function load (): void {

        if (self::$raw !== null) return;

        $path = self::path();

        if (!file_exists($path)) {
            self::$raw      = [];
            self::$resolved = [];
            return;
        }

        $content = file_get_contents($path);
        $key     = 'lattice_css_tokens_' . md5($path . '|' . filemtime($path) . '|' . md5($content));

        $cached = get_transient($key);

        if ($cached !== false) {
            self::$raw      = $cached['raw'];
            self::$resolved = $cached['resolved'];
            return;
        }

        self::$raw      = self::parse($content);
        self::$resolved = [];

        foreach (self::$raw as $name => $value) {
            self::$resolved[$name] = self::resolve($value, [$name]);
        }

        set_transient($key, [
            'raw'      => self::$raw,
            'resolved' => self::$resolved,
        ], DAY_IN_SECONDS);

    }

    private static function parse (string $content): array {

        $tokens = [];

        // Capture :root { ... } blocks. Nested braces are not supported
        // (token values do not contain { } in practice).
        if (!preg_match_all('/:root\s*\{([^}]*)\}/', $content, $blocks)) return $tokens;

        foreach ($blocks[1] as $block) {

            // Strip CSS comments before extracting declarations.
            $clean = preg_replace('!/\*.*?\*/!s', '', $block);

            if (preg_match_all('/^\s*--([a-zA-Z][\w-]*)\s*:\s*([^;]+);/m', $clean, $matches, PREG_SET_ORDER)) {

                foreach ($matches as $m) {
                    $tokens[$m[1]] = trim($m[2]);
                }

            }

        }

        return $tokens;

    }

    private static function resolve (string $value, array $visited = [], int $depth = 0): string {

        if ($depth > self::MAX_DEPTH) {
            throw new \RuntimeException(
                'lattice-css: token resolution depth exceeded for "' . $value . '" via ' . implode(' -> ', $visited)
            );
        }

        return preg_replace_callback(
            '/var\(\s*--([a-zA-Z][\w-]*)(?:\s*,[^)]*)?\s*\)/',
            function ($m) use ($visited, $depth) {

                $name = $m[1];

                if (in_array($name, $visited, true)) {
                    throw new \RuntimeException(
                        'lattice-css: token cycle detected: ' . implode(' -> ', [...$visited, $name])
                    );
                }

                if (!isset(self::$raw[$name])) return $m[0];

                return self::resolve(self::$raw[$name], [...$visited, $name], $depth + 1);

            },
            $value
        );

    }

}
