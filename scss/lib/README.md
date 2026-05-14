# Library

Shared SCSS functions and utility mixins. Projects `@use` the partials they need.

All partials are values-in / values-out: functions return values and mixins set
properties. They do not write CSS custom properties as a side effect, do not
reference project-specific token names, and do not generate selector variants.
See [SPEC.md §10.2](../../SPEC.md).

## Partials

| Partial | Surface | Purpose |
|---|---|---|
| [`_var.scss`](_var.scss) | `v($name, ...)` | CSS variable shorthand with chained-fallback support |
| [`_breakpoint.scss`](_breakpoint.scss) | `at($name)`, `container-at($name)` | Media and container query mixins (SPEC §6) |
| [`_color.scss`](_color.scss) | `mix($a, $b, $stop)`, `tints($base, $n)`, `shades($base, $n)` | Runtime colour mixing via `color-mix(in oklab, ...)` |
| [`_type.scss`](_type.scss) | `ratio($name)`, `step($base, $ratio, $level)`, `text-clip` | Type scale ratios and step calculation; vendor stack for `background-clip: text` |
| [`_shadow.scss`](_shadow.scss) | `box`, `drop`, `text` (functions and mixins) | Multi-layer perceptually-eased shadows for box-shadow / filter / text-shadow |
| [`_geometry.scss`](_geometry.scss) | `chamfer($d)` | Clip-path chamfer (octagonal cut corners) |

## Usage

The framework's `scss/` directory is registered as a SASSY import path (via the
`sassy-import-paths` filter in the project's SASSY integration), so library
partials are imported by name without referencing the framework's mount
location:

```scss
@use 'lib/breakpoint' as bp;
@use 'lib/color' as color;
@use 'lib/shadow' as shadow;

.card {
    background: color.mix(var(--accent), white, 30%);
    @include shadow.box-shadow(0px, 16px, 32px, 0.1);

    @include bp.at(tablet) {
        padding: 2rem;
    }
}
```

## Toolchain

The framework uses modern Sass module syntax (`@use`, `@forward`, the
`sass:math` / `sass:color` / `sass:list` / `sass:map` / `sass:string` modules).
This requires **Dart Sass**. The default `scssphp` engine in SASSY does not
support modules. Two filters in SASSY wire this up:

- `sassy-engine` — return a `Sassy\Dart_Sass_Engine` instance to swap the engine.
- `sassy-dart-sass-binary` — return the path to the `sass` binary (or define
  `SASSY_DART_SASS_BIN`).

See [SASSY README](../../../../sassy/README.md).
