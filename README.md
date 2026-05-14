# Digitalis CSS System

Practice-level CSS architecture for Digitalis Web Corp projects.

This is the framework — the package directory is named `lattice-css/`, the PHP namespace is `Lattice\Css`, and the formal name in spec and brand material is "Digitalis CSS System". Sister to the [lattice/](../lattice/) PHP framework. It will become its own repository; for now it develops in-place inside a host project.

## Structure

| Path | Purpose |
|---|---|
| [SPEC.md](SPEC.md) | Architecture spec. The entry point. Conventions, patterns and rules that apply to every Digitalis project. |
| [PROJECT-PROFILE.md](PROJECT-PROFILE.md) | Template. Copy into a project and fill in per-engagement token values, material assignments and extension selections. |
| [scss/](scss/) | Framework SCSS: `_harness.scss`, `_mixins.scss`, and [scss/lib/](scss/lib/README.md) (shared functions and mixins projects opt into). |
| [lattice-css.php](lattice-css.php) | PHP boot. Registers the token parser (`Lattice\Css\Tokens::get` / `::raw`) — see [SPEC §2.2](SPEC.md). |

## Three Layers

| Layer | Stability | Scope |
|---|---|---|
| **Spec** | Stable, rarely changes | Every project follows this |
| **Library** | Evolves, versioned | Projects pick what they need |
| **Project profile** | Per-engagement | Unique to each project |

The spec defines architecture. The library provides tools. The project profile records decisions.

## Toolchain

The framework uses modern Sass module syntax (`@use`, `sass:math`, `sass:color`, etc.) and requires **Dart Sass**. SASSY's default scssphp engine does not support modules. The host project's SASSY integration is responsible for selecting Dart Sass and registering `scss/` as an import path — see [scss/lib/README.md](scss/lib/README.md) and the `sassy-engine` / `sassy-import-paths` filters.
