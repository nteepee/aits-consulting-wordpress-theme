# WordPress FSE Block Themes Research Report
**Date:** 2025-12-04 | **Version:** WordPress 6.0+ standards

---

## 1. Theme.json Design System Structure

**Core Purpose:** theme.json functions as the central interface between theme configuration and block editor. It defines global settings, styles, and design tokens without requiring code.

**Top-Level Sections:**
- `$schema` / `version`: Schema reference and format version
- `settings`: Defines block controls, presets (colors, fonts, spacing, sizes)
- `styles`: Applies colors, typography, custom CSS to website and blocks
- `customTemplates`, `templateParts`, `patterns`: Content definitions

**Hierarchy Levels (Inheritance):**
1. Global settings (root level `settings` object)
2. Elements (`settings > elements > link, button, heading`)
3. Blocks (`settings > blocks > "core/paragraph"`)

CSS custom properties auto-generate via schema `--wp--custom--<variable-name>`. Hierarchical inheritance ensures predictable propagation across editor and frontend.

---

## 2. Block Template Patterns & Reusability

**Templates:** Define overall structure for specific content types (single posts, pages, archives). Stored as HTML files in `/templates/` folder (replacing classic PHP hierarchy). WordPress searches: database → child theme `/templates/` → main theme `/templates/` → defaults.

**Template Parts:** Reusable, synced structural elements outside content area (header, footer, sidebar). Changes propagate site-wide automatically. Cannot be inserted into individual posts/pages—only used in templates.

**Block Patterns:** Pre-configured block groups serving as content starting points. Ideal for inserting into post/page content within templates. Enable rapid content creation with consistent structure.

**Archive Hierarchy (WordPress Template Hierarchy Rules Apply):**
- `archive.html` → `category.html` / `tag.html` / `taxonomy.html`
- `home.html` / `front-page.html` (homepage)
- `page.html` (single page)
- `single.html` (single post)

---

## 3. Template Hierarchy for Posts/Pages/Archives

**Block Theme Hierarchy Structure:**
Same as classic themes but uses HTML instead of PHP. Query string determines template matching order.

**Specific Templates:**
- **Home:** Displays posts homepage or static homepage if configured
- **Archive:** Categories, tags, custom taxonomies
- **Single Page:** `page.html`
- **Single Post:** `single.html` or post-type-specific variants
- **Fallback:** `index.html` (catches all unmatched queries)

**Template Part Linking:** Templates incorporate template parts for modular structure. Synced parts ensure consistent header/footer/sidebar behavior across all templates without duplication.

---

## 4. Navigation & Menu Implementation

**FSE Navigation Block:** Primary menu system using native Navigation block in Site Editor. Enables:
- Add/rearrange/remove items
- Link to pages, posts, categories, custom URLs
- Customize styles, alignment, responsive behavior
- Automatic page-list generation on first insertion

**Automatic Menu Generation:** Navigation block auto-converts published pages into list when first added. Convert to manual mode for custom menu structure.

**Current Limitation:** No "menu location" concept in block themes—traditional `register_nav_menu()` locations don't apply. Native Navigation block lacks fallback to classic menus.

**Solution:** "Classic Menu in Navigation Block" plugin bridges gap—enables Appearance → Menus admin screen for block themes and adds location settings to Navigation block.

**Submenu Support:** Create nested menus via submenu items within Navigation block UI.

---

## 5. Global Styles & CSS Variables

**CSS Variable Generation:** theme.json declarations auto-convert to CSS custom properties: `--wp--preset--color--primary`, `--wp--custom--spacing--base`, etc.

**Preset Categories (Auto-Generate CSS Vars):**
- Colors: `--wp--preset--color--{name}`
- Font sizes: `--wp--preset--font-size--{name}`
- Spacing/sizing: `--wp--preset--spacing--{name}`
- Custom values: `--wp--custom--{name}`

**Inheritance Model:** Global settings at root affect all blocks. Block-level overrides inherit from global defaults. Settings propagate predictably via hierarchical cascading.

**Style Variations:** Inherit theme.json base values. Overrides in variation files replace parent values. Enable theme switcher functionality with multiple design systems.

**CSS Reference Constraints:** Layout properties require explicit values (pixel/em/rem), not CSS variable references. Color/typography properties support variable references.

---

## 6. Dark Mode Support

**Browser Standards Approach:**
- Use `color-scheme: light dark` property (95%+ browser support)
- Employ CSS `light-dark()` function for dual-color definitions (86%+ support)
- Let browser auto-switch based on OS `@media (prefers-color-scheme: dark)` preference

**Implementation in Block Themes:**
1. Set `color-scheme` via `styles.css` (no direct theme.json support yet)
2. Define CSS variables for light mode in base preset
3. Override CSS variables inside `@media (prefers-color-scheme: dark)` query

**Dark Mode Toggle Block:**
- Custom `.theme-dark` class applied when toggled
- Target with `.theme-dark` selector to reassign color variables
- Store user preference in localStorage for persistence

**Example Pattern:**
```
/* Base (light) */
--wp--preset--color--primary: #0066cc;

/* Dark override */
@media (prefers-color-scheme: dark) {
  :root { --wp--preset--color--primary: #4da6ff; }
}
```

**Plugins:** WP Dark Mode, Dark Mode Toggle Block, Classic Menu in Navigation Block all support dark variants.

---

## WordPress 6.0+ Standards Summary

**Modern FSE Baseline:**
- WordPress 5.9 introduced FSE (blocks-first templating)
- WordPress 6.0+ stabilized block editor, global styles maturity
- WordPress 6.2+ added advanced layout controls
- Twenty Twenty-Five (2025 default) exemplifies current best practices

**Key Modernizations:**
- JSON-based design system replaces theme customizer options
- Block-first architecture eliminates PHP templates
- Native CSS variables simplify child theme customization
- Interactivity API enables client-side state without JavaScript dependencies
- Synced template parts reduce maintenance burden

---

## Key Research Sources

1. [Global Settings & Styles (theme.json) – Block Editor Handbook](https://developer.wordpress.org/block-editor/how-to-guides/themes/global-settings-and-styles/)
2. [Introduction to theme.json – Theme Handbook](https://developer.wordpress.org/themes/global-settings-and-styles/introduction-to-theme-json/)
3. [Theme.json Version 3 Reference](https://developer.wordpress.org/block-editor/reference-guides/theme-json-reference/theme-json-living/)
4. [Template Hierarchy – Theme Handbook](https://developer.wordpress.org/themes/basics/template-hierarchy/)
5. [Overview of WordPress block theme terms and hierarchy](https://learn.wordpress.org/lesson/overview-of-wordpress-block-theme-terms-and-hierarchy/)
6. [Mastering light and dark mode styling in block themes – WordPress Developer Blog](https://developer.wordpress.org/news/2024/12/mastering-light-and-dark-mode-styling-in-block-themes/)
7. [Mastering Navigation in 2025 WordPress Editor](https://www.captain-design.com/blog/mastering-navigation-in-the-2025-wordpress-editor-a-step-by-step-guide/)
8. [WordPress Navigation Menus in FSE](https://maxiblocks.com/conquer-your-websites-navigation-with-wordpress-full-site-editing-fse/)
9. [Creating WordPress block themes - Full Site Editing](https://fullsiteediting.com/lessons/creating-block-based-themes/)
10. [Global Styles & theme.json - Full Site Editing](https://fullsiteediting.com/lessons/global-styles/)
11. [Adding and using custom settings in theme.json – WordPress Developer Blog](https://developer.wordpress.org/news/2023/08/adding-and-using-custom-settings-in-theme-json/)
12. [21 Best WordPress Full Site Editing Themes of 2025](https://www.wpbeginner.com/showcase/best-wordpress-full-site-editing-themes/)

---

## Unresolved Questions / Areas Requiring Further Investigation

- **Custom Block Integration:** How to register custom blocks with FSE template constraints
- **Performance Optimization:** Lazy-loading strategies for FSE sites with complex template hierarchies
- **Translation Support:** i18n best practices within JSON-based design systems
- **Backwards Compatibility:** Deprecation timeline for classic theme features post-6.5
