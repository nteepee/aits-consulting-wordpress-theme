# WordPress Gutenberg Block Development: Modern Best Practices

## Executive Summary
Modern Gutenberg block development prioritizes reusability, performance, and accessibility. Key framework: `create-block` scaffolding tool + clean `block.json` metadata + semantic component architecture. 2025 focus: lightweight markup, lazy-loaded assets, native WordPress components over custom HTML.

## 1. Block Registration & Metadata

**block.json Standard:**
```json
{
  "$schema": "https://schemas.wp.org/wp/6.3/block.json",
  "apiVersion": 3,
  "name": "company/hero",
  "title": "Hero Block",
  "description": "Large hero section with image, headline, CTA",
  "category": "layout",
  "icon": "star",
  "attributes": {
    "title": { "type": "string", "default": "" },
    "subtitle": { "type": "string", "default": "" },
    "imageUrl": { "type": "string", "default": "" },
    "ctaText": { "type": "string", "default": "Learn More" },
    "ctaUrl": { "type": "string", "default": "#" }
  },
  "supports": {
    "anchor": true,
    "align": ["wide", "full"],
    "color": { "background": true, "text": true }
  },
  "editorScript": "file:./index.js",
  "editorStyle": "file:./editor.css",
  "style": "file:./style.css"
}
```

**Registration (functions.php):**
```php
register_block_type( __DIR__ . '/build/hero' );
```

## 2. Create-Block Scaffolding

**Generate block template:**
```bash
npx @wordpress/create-block my-hero --template-slug components-hero
```

Outputs: `block.json`, `src/index.js`, `src/edit.js`, `src/save.js`, `src/style.scss`.

**Key advantage:** Pre-configured build setup (webpack), translation ready, ESLint configured.

## 3. Edit & Save Functions Pattern

**edit.js (Editor view):**
```javascript
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, MediaUpload } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
  const { title, imageUrl, ctaText } = attributes;
  const blockProps = useBlockProps({ className: 'wp-block-company-hero' });

  return (
    <div { ...blockProps }>
      <MediaUpload
        onSelect={(media) => setAttributes({ imageUrl: media.url })}
        render={({ open }) => (
          <Button onClick={open}>
            {imageUrl ? 'Change Image' : 'Select Image'}
          </Button>
        )}
      />
      <RichText
        tagName="h1"
        value={title}
        onChange={(newTitle) => setAttributes({ title: newTitle })}
        placeholder={__('Enter title')}
      />
      <RichText
        tagName="p"
        value={ctaText}
        onChange={(newCta) => setAttributes({ ctaText: newCta })}
      />
    </div>
  );
}
```

**save.js (Frontend output):**
```javascript
import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
  const { title, imageUrl, ctaText, ctaUrl } = attributes;
  return (
    <div { ...useBlockProps.save() }>
      {imageUrl && <img src={imageUrl} alt="" loading="lazy" />}
      <h1>{title}</h1>
      <p>{ctaText}</p>
      <a href={ctaUrl} className="wp-block-button__link">{ctaText}</a>
    </div>
  );
}
```

## 4. Block Variants & Reusability

**Variants in block.json:**
```json
{
  "variations": [
    {
      "name": "hero-dark",
      "title": "Hero - Dark Theme",
      "attributes": {
        "backgroundColor": "dark-gray",
        "textColor": "white"
      },
      "isDefault": true
    },
    {
      "name": "hero-light",
      "title": "Hero - Light Theme",
      "attributes": {
        "backgroundColor": "light-gray",
        "textColor": "black"
      }
    }
  ]
}
```

**Reusable component blocks:**

- **Hero Block:** Full-width header, background image, title, CTA. Supports anchor, wide/full align.
- **CTA Block:** Simplified hero—headline + button. Single column focus.
- **Card Block:** Image + excerpt + link. Grid-friendly. Supports repeatable variants.
- **Form Block:** Input fields, validation. Use `block-supports` for spacing/margin.

## 5. Performance Optimization

**Critical practices:**

1. **Lazy-load images:** `loading="lazy"` in save.js
2. **Minimize dependencies:** Use native `@wordpress/components` over external libraries
3. **Split CSS:** Separate `editor.css` (editor-only styles) from `style.css` (frontend)
4. **Clean markup:** Avoid nested groups when possible; use semantic HTML
5. **Defer non-critical scripts:** Register block script with `defer` flag in `functions.php`
6. **Cache block patterns:** Register patterns once via `register_block_pattern()`, not per post

**Register pattern (functions.php):**
```php
register_block_pattern(
  'company/cta-pattern',
  [
    'title' => 'CTA Section',
    'description' => 'Call-to-action section',
    'content' => '<!-- wp:company/cta {"text":"Sign Up"} /-->',
    'categories' => ['call-to-action']
  ]
);
```

## Key Recommendations

- **Use block.json for all metadata.** Enables automatic registration, translations, schema validation.
- **Always include `useBlockProps`.** Ensures editor-frontend consistency & accessibility attributes.
- **Implement conditional rendering.** Server-side render heavy blocks; save lightweight frontend markup.
- **Test theme compatibility.** Patterns must work across themes; avoid theme-specific selectors.
- **Minimize attribute count.** Fewer attributes = faster editor, cleaner serialized HTML.
- **Document block purpose.** Use `description` in block.json for discoverability.

## Unresolved Questions

- Optimal caching strategy for patterns in high-traffic sites?
- SSR vs. client-side rendering trade-offs for complex interactive blocks?

## Sources
- [Block Editor Handbook - WordPress Developer](https://developer.wordpress.org/block-editor/)
- [Block API Reference - WordPress Developer](https://developer.wordpress.org/block-editor/reference-guides/block-api/)
- [The Future of WordPress Gutenberg: 2025 Predictions](https://belovdigital.agency/blog/the-future-of-wordpress-gutenberg-2025-predictions/)
- [Gutenberg Blocks: Revolutionizing WordPress Development](https://www.bluetickconsultants.com/gutenberg-blocks-the-evolution-and-revolution-in-wordpress-development/)
- [A Developer's Guide: Future of WordPress Gutenberg Block Editor](https://webdevstudios.com/2025/11/25/a-developers-guide-the-future-of-the-wordpress-gutenberg-block-editor/)
- [Block Patterns: Building Reusable Components](https://jonimms.com/how-to-build-and-reuse-wordpress-components-with-block-patterns-with-code-examples/)
- [WordPress Block Patterns: Comprehensive Guide 2025](https://www.themescamp.com/how-to-build-wordpress-block-patterns-a-comprehensive-guide/)
- [How to Create a Hero Section in WordPress](https://wpkind.com/create-hero-section/)
- [WordPress Speed Optimization: 21 Performance Tips 2025](https://rapyd.cloud/blog/wordpress-speed-optimization/)
