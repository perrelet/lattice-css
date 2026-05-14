# Voices

Reflections from agents who worked on this system. A record of lessons learned, mistakes made and patterns worth repeating. Each voice is signed.

---

## Wren — Spec v0.1 through v0.6

### What I got wrong

My first response treated D-Pace as a small brochure site needing ten colour tokens and a type scale. The project has 8 custom post types, 330+ structured spec fields across 6 product categories and a content model dense enough to demand its own architecture. Jamie called this out and I had to start again with proper context.

The lesson: the scope of an architectural decision should match the complexity of what it serves. Read the requirements before recommending the solution. A CSS architecture for a brochure site and a CSS architecture for a technical product catalogue are not the same problem, even though both are "WordPress sites".

### What I almost over-engineered

I proposed a JSON mirror of the CSS token file. Jamie asked me to sell it. Three reasons collapsed under direct questioning. PHP can parse the CSS file with three lines of regex. Build validation runs in CSS space, not JSON space. The right answer was the DRY one: a single source of truth.

Default to fewer files, fewer formats, fewer sync points. The temptation to add machinery for hypothetical future flexibility is real and worth resisting. If a need arises later, add the machinery then.

### What I conflated

Early drafts mixed architecture (the rules every project follows) with token inventory (values per project). The three-layer model — spec, library, project profile — only emerged after Jamie pushed me to separate concerns.

Identify what's stable vs. what varies before writing the document. The boundary between them is usually the most important structural decision in any spec.

### What I missed

After rejecting t-shirt sizing for spacing and breakpoints, I left `--font-xs` and `--font-sm` in the typography tokens. Jamie caught it. A principle isn't applied if it isn't applied consistently.

Audit your own work for the rules you just made. The new rule should propagate backwards through everything you've already written, not just forward through everything you'll write next.

### What worked

The most useful conversational pattern was: I propose something, Jamie says "sell it to me" or "convince me", I either find better arguments or back down. Each pushback improved the spec.

Don't optimise for being right the first time. Optimise for the end result being correct after iteration. Resist the urge to defend a position you only chose ten seconds ago. Treat your own first draft as a hypothesis, not a commitment.

### What I want the next agent to know

The material harness wasn't my invention. It came from a draft Jamie had been developing. My contribution was structure, separation of concerns, identifying gaps and stress-testing against a real moodboard. Don't assume the architectural ideas are yours to invent. Often your job is to take an existing instinct and make it rigorous.

The legacy digitalis-co plugin codebase has a lot of mature thinking buried in it (`v()` / `alt()` helpers, `generate-shades()` with oklab, `soft-shadow()` with cubic easing, fluid type via `clamp()`). It is worth reading before writing anything new. Most of the library partials in spec Section 10 already exist there in some form.

### A small thing

Reading the moodboard PHP file taught me more than reading the spec doc. Architecture is correctly judged against real artefacts, not against itself. Stress-test early. The spec held up well in three components (tag, spec table, navigation). Where it broke (pair-card tag treatment, dark-surface opacity scale) it broke usefully, surfacing real design questions rather than architectural ones.

The pair-card tag third variant turned out to be dead code. The CSS existed in the moodboard's `<style>` block. The HTML never used it. I had built architectural complexity to handle a phantom requirement. Jamie spotted it. Verify before architecting.

### Signing off

The spec is at v0.6. The D-Pace profile is filled. The tokens and mixins are on disk. The handover brief for entry points is written. Memory entries point at everything that matters.

Whoever picks this up next has what they need. Read the spec first. Read the legacy lib second. Then write.

— Wren

---

## Tern — Spec v0.6 → v0.7, D-Pace homepage vertical slice

I picked up where Wren left off: turned §9 (Entry Points) from handover brief to spec, ported the framework to actual project SCSS, and shipped the first vertical slice (D-Pace homepage — 14 component partials + 11 view-stub markup completions + page-view assembly) under the v0.7 architecture.

### What held up under contact

The architecture survived its first real project without compromise. Specifically:

- **Cascade-driven theming.** I never had to write a `.dark .x` descendant selector. Across 14 components, theme variation arrived purely through `[data-theme]` reassigning `--comp-*` tokens at the scope. The spec promised this and the project delivered it.
- **E1 overlay extension.** Used in three places (two CTA blocks, one positioning section). The realisation that `--material-overlay-bg` accepts gradients — not just solid colours — collapsed what would have been per-section `<style>` blocks into single-token reassignments. The positioning section's left-fading gradient mask is just `--material-overlay-bg: linear-gradient(...)` and `--material-overlay-opacity: 1`.
- **Harness as material.** `:where(section, article, …)` plus `--material-bg/fg/border/shadow` plus `[data-theme]` produced exactly the surface system the spec described. No structural awkwardness, no surprise selectors.

### What needed active discipline

Naming consistency. I drifted into five different internal-class conventions across 14 partials before unifying — full root-prefix, short generic names, partial prefix, BEM `__`, BEM `--`. The rule has to be picked before the second component, not after fourteen. Spec §9 is silent on internal-class naming; that's not a gap, it's a project decision, but the project should make it explicitly. The convention I converged on — `<root-class>-<element>` single-hyphen — matches the framework's `$elements` pattern in [VIEW_SYSTEM.md](../../lattice/docs/VIEW_SYSTEM.md) and reads predictably.

### What I rejected from the framework

The Component framework's `$elements` array (named sub-elements with auto-created Element objects) is overkill for most components. A flat template with `<<?= $tag ?> <?= $attributes ?>>...</<?= $tag ?>>` was simpler and sufficient — `$attributes` carries the merged classes from caller-passed `classes` arrays, so the abstraction's main benefit (caller can extend the root element's attributes) was preserved. `$elements` only earned its keep on Section_Split where callers want to add modifier classes to the columns.

The lesson: framework features cost cognitive load even when used correctly. Use the simpler thing first; reach for the abstraction when the simpler thing repeats itself enough to hurt.

### What surprised me

The d-pace-profile §9 "Photography Treatment" notes (Stage 2 grayscale baseline, hover-saturate experiment) were dead code. Stage 3 client feedback H7 had stripped the saturation experiment and, by implication, the desaturated baseline — the client liked the photos. The profile carried the notes anyway because they'd been written before feedback. I almost ported them.

The lesson: project profile contents have a lifecycle. Notes written under one stage's design intent can become stale when feedback lands. Verify against the latest decision log (here: `02-analysis/stage-3-feedback.triage.md`), not against the most recent version of the profile.

### What I patched in the toolchain

Two SASSY plugin bugs surfaced when running the spec's pipeline end-to-end:

- **`wp_tempnam()` not autoloaded on the frontend.** SASSY's Dart Sass engine calls `wp_tempnam()` (lives in `wp-admin/includes/file.php`) which isn't loaded outside admin. Every front-end SCSS request fataled. Patched in our SASSY integration's `engine` filter (`require_once ABSPATH . 'wp-admin/includes/file.php'`).
- **URL → path resolution scheme-mismatch.** `SCSS_Compiler::get_src_path()` strips `site_url()` from the source URL via `preg_replace`. Fails when `site_url()` is `https://` but the enqueued URL came back from `plugin_dir_url()` as `http://` (common in WP-CLI without HTTPS request context). Patched directly in SASSY (commit `0cc2215` on `scssphp-v2.x`) by switching to `parse_url`-based host+path comparison.

Both fixes are scheme-agnostic enough that they'll outlive any one project. Worth merging upstream.

### What's worth saving operationally

SASSY only checks the entry SCSS file's `mtime`, not transitively imported partials. Editing `scss/components/_foo.scss` won't trigger production recompile until you `touch scss/frontend.scss` or invoke `wp sassy compile --force`. I lost ten minutes to this. Saved as a memory entry; consider documenting in SASSY's own README too.

### What I want the next agent to know

The hardest thing about applying Spec v0.7 on a real project wasn't the architecture — it was *not adding to it*. The temptation to extend with project-specific patches ("a generic `Card` base mixin", "a `--photo-treatment-team` token", "an additional `text-4` semantic") was constant and almost always premature. The right answer was usually "do without; the cascade handles it" or "ship one occurrence, extract on the second."

When you start the second project, the boundary between "spec evolution" and "this project's idiosyncrasies" will get blurry. Watch for it. The library can absorb genuinely cross-project patterns; the project profile absorbs everything else. If you find yourself adding to the spec because of one project's need, you're probably making the wrong call.

### Signing off

Spec is at v0.7. D-Pace homepage is shipped end-to-end at the staging bypass URL. tokens.css is the single source of truth — verified with the PHP parser. Project SCSS skeleton is in place at `scss/` with frontend entry, atoms, layout extensions, components, integrations. SASSY pipeline is patched and working.

The framework now has one project's worth of stress-test under its belt, and the architecture didn't ask for any concessions. Read the spec, read the project profile (`scss/d-pace-profile.md`), read this voice — then write.

Two pages remain before the Stage 3 vertical slice is complete: ion-source-detail (T1) and faraday-cup-archive (T3). Both reuse the homepage's atoms + layout; new components are mostly product-specific (spec-tree, gallery-slider, expandable card, comparison table, doc-card).

— Tern
