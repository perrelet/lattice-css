<?php

namespace Lattice\Css;

/**
 * Injects a token-sourced colour palette into the block editor via
 * theme.json filtering.
 *
 * Each palette entry's colour value is emitted as `var(--{token})` rather
 * than a baked hex, so `[data-theme]` scope reassignments apply at render
 * time. The swatch preview in the editor sidebar resolves the token
 * against the canvas root (default scope), so it shows the canonical
 * `:root` value declared in tokens.css. Sections that reassign the token
 * render in their own scope. Swatch ≠ render for reassigning tokens is
 * the inherent cost of cascade-driven theming.
 *
 * Projects declare the palette via the `lattice_css_editor_palette`
 * filter:
 *
 *     add_filter('lattice_css_editor_palette', function () {
 *         return [
 *             'accent'    => 'Accent',
 *             'highlight' => 'Highlight',
 *             'text-1'    => 'Ink',
 *         ];
 *     });
 *
 * Key   — token name (no `--` prefix; matches the Tokens API).
 *         Becomes the WP slug. Emits as `.has-{slug}-color`,
 *         `.has-{slug}-background-color`.
 * Value — display name shown in the swatch tooltip.
 *
 * Tokens referenced but missing from tokens.css are silently dropped to
 * avoid emitting broken `var()` references.
 *
 * Default with no filter registered: empty array; this class is a no-op.
 * Other theme.json settings (custom-colour toggle, gradients, etc.) are
 * out of scope here — projects hook `wp_theme_json_data_theme` directly
 * for those.
 */
class Editor_Palette {

    public static function register (): void {

        add_filter('wp_theme_json_data_theme', [self::class, 'inject']);

    }

    public static function inject ($theme_json) {

        $palette = self::build();

        if (empty($palette)) return $theme_json;

        return $theme_json->update_with([
            'version'  => 2,
            'settings' => [
                'color' => [
                    'palette' => $palette,
                ],
            ],
        ]);

    }

    public static function build (): array {

        $entries = apply_filters('lattice_css_editor_palette', []);

        if (empty($entries) || !is_array($entries)) return [];

        $palette = [];

        foreach ($entries as $token => $name) {

            // Skip undeclared tokens to avoid baking dead var() references
            // into .has-*-color rules.
            if (Tokens::raw($token) === null) continue;

            $palette[] = [
                'slug'  => $token,
                'name'  => $name,
                'color' => "var(--{$token})",
            ];

        }

        return $palette;

    }

}
