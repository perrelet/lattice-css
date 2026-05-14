# Project Profile Template

**Status:** Template
**Usage:** Copy this file into a project and fill in the sections. This records the design system decisions for a specific engagement.

---

## Project

| Field | Value |
|---|---|
| Project name | |
| Client | |
| Start date | |
| Spec version | v0.7 |

---

## 1. Token Inventory

### 1.1 Surfaces

```css
:root {
    /* --surface-0: ;    base plane */
    /* --surface-1: ;    default content */
    /* --surface-2: ;    raised / nested */
    /* add or remove levels as needed */
}
```

### 1.2 Text

```css
:root {
    /* --text-1: ;       primary */
    /* --text-2: ;       secondary */
    /* --text-3: ;       tertiary / muted */
}
```

Include dark context overrides inside `[data-theme="dark"]` if the project uses a dark theme scope.

### 1.3 Borders

```css
:root {
    /* --border-subtle: ; */
    /* --border-strong: ; */
}
```

### 1.4 Interactive States

```css
:root {
    /* --interactive-bg: ; */
    /* --interactive-bg-hover: ; */
    /* --interactive-bg-active: ; */
    /* --interactive-bg-disabled: ; */
    /* --interactive-fg: ; */
    /* --interactive-fg-disabled: ; */
    /* --focus-ring: ; */
}
```

### 1.5 Intent

```css
:root {
    /* --accent: ; */
    /* --accent-contrast: ; */
    /* add success, warning, danger, highlight pairs as needed */
}
```

### 1.6 Typography: Stacks

```css
:root {
    /* --sans: ; */
    /* --mono: ; */
    /* --label: ;   optional — only if a distinct display/label face is used */
}
```

### 1.7 Typography: Scale

**Body range (fixed):**

```css
:root {
    /* project defines steps and naming */
    /* e.g. --font-caption, --font-ui, --font-body */
}
```

**Heading range (fluid via `clamp()`):**

```css
:root {
    /* e.g. --font-h3: clamp(min, preferred, max); */
    /* e.g. --font-h2: clamp(min, preferred, max); */
    /* e.g. --font-h1: clamp(min, preferred, max); */
}
```

**Generation method:** (e.g. ratio-based via `type.ratio()` + `type.step()` from the library composed with project-side `clamp()`, or hand-picked min/max values)

### 1.8 Typography: Weight

```css
:root {
    /* --weight-regular: ; */
    /* --weight-medium: ; */
    /* --weight-semibold: ; */
    /* --weight-bold: ; */
}
```

### 1.9 Typography: Line-Height

```css
:root {
    /* --leading-body: ; */
    /* --leading-heading: ; */
    /* --leading-dense: ; */
}
```

### 1.10 Spacing

```css
:root {
    --u: 0.5rem;    /* base grid unit — adjust per project */

    /* Fractional stops: underscore = denominator */
    /* e.g. --u_2: calc(var(--u) / 2); */
    /* e.g. --u3_2: calc(var(--u) * 3 / 2); */

    /* Integer stops */
    /* e.g. --u2: calc(var(--u) * 2); */
    /* e.g. --u3: calc(var(--u) * 3); */

    /* one-off values use calc(var(--u) * N) inline */
}
```

### 1.11 Transitions

```css
:root {
    /* --duration-fast: ; */
    /* --duration-default: ; */
    /* --easing-default: ; */
}
```

### 1.12 Shadows / Elevation (optional)

```css
:root {
    /* --shadow-1: ; */
    /* --shadow-2: ; */
}
```

**Generation method:** (e.g. library `shadow.box()` / `shadow.drop()` / `shadow.text()`, manual, other)

### 1.13 Geometry (optional)

```css
:root {
    /* e.g. --chamfer: polygon(...); */
    /* e.g. --radius-1, --radius-2 */
}
```

### 1.14 Density Contexts (optional)

Describe how tokens shift in dense vs. sparse contexts. Overriding `--u` in a density scope scales all derived spacing tokens automatically. List the `data-density` attribute values used and which additional tokens are overridden.

---

## 2. Material Assignments

Define how materials are applied to structural elements in this project.

```css
/* Base plane */
body {
    /* background: var(--surface-0); */
    /* color: var(--text-1); */
}

/* Primary content surface */
main > :where(section, article) {
    /* --material-bg: ; */
    /* --material-fg: ; */
    /* --material-border: ; */
    /* --material-shadow: ; */
}

/* Nested surfaces */
/* ... */
```

### 2.1 Containment

```css
:root {
    /* --material-contain: ;   max content width */
    /* --material-pad: ;       minimum inline padding */
}
```

### 2.2 Theme Scopes

List the `[data-theme]` values used in this project and what they override. Note any scopes that are set dynamically via JS.

---

## 3. Breakpoints

```scss
// Starts from spec defaults — adjust or extend as needed
$breakpoints: (
    mobile-port:  400px,
    mobile-land:  640px,
    tablet:       1024px,
    desktop:      1440px,
);
```

---

## 4. Container Query Breakpoints

```scss
$container-breakpoints: (
    // define per project
);
```

---

## 5. Active Extensions

| Extension | Active | Notes |
|---|---|---|
| E1. Overlay / effects | No | |
| E2. Density contexts | No | |
| E3. Capture / hole | No | |
| E4. Material composition mixins | No | |

---

## 6. Library Selections

| Library partial | Used | Notes |
|---|---|---|
| `_var.scss` | | |
| `_color.scss` | | |
| `_type.scss` | | |
| `_shadow.scss` | | |
| `_geometry.scss` | | |
| `_breakpoint.scss` | | |

---

## 7. Project-Specific Mixins

List any multi-property composition mixins defined for this project (shape, effects, material bundles).

---

## 8. External Library Integrations

List external libraries that need styling boundaries (e.g. Gravity Forms, WooCommerce, block editor plugins).

| Library | Boundary selector | Notes |
|---|---|---|
| | | |

---

## 9. Notes

Project-specific conventions, decisions or constraints not covered above.
