# Navigation & Menus System - Quick Reference

## Overview

This theme includes a complete WordPress menu system with:
- 3 registered menu locations
- Custom menu walker for advanced styling
- Mobile menu toggle support
- Keyboard navigation
- Dark mode support
- Accessibility features

---

## Menu Locations

| Location | Label | Used In |
|----------|-------|---------|
| `primary-menu` | Primary Navigation | parts/header.html |
| `footer-menu` | Footer Menu | parts/footer.html |
| `mobile-menu` | Mobile Menu (optional) | Available for custom use |

---

## Setting Up Menus in WordPress Admin

1. Log in to WordPress admin
2. Go to **Appearance > Menus**
3. Create a new menu or select existing
4. Add menu items as needed
5. Under **Display location**, check boxes for locations to assign menu
6. Save menu

### Primary Menu Structure (Recommended)

```
Home
Solutions
  ├─ Passenger Monitoring Systems
  └─ Fleet Safe Pro
Case Studies
Blog
About
Contact
```

### Footer Menu Structure (Recommended)

```
Home
Solutions
About
Blog
Contact
Privacy Policy
```

---

## Using Menus in Templates

### Display Primary Menu

```html
<?php stitch_consulting_primary_menu(); ?>
```

### Display Footer Menu

```html
<?php stitch_consulting_footer_menu(); ?>
```

### Display Custom Menu

```php
<?php
wp_nav_menu( array(
    'theme_location' => 'primary-menu',
    'menu_class'     => 'my-custom-class',
    'container'      => 'nav',
    'depth'          => 2,
) );
?>
```

---

## Check if Menu is Assigned

```php
<?php
if ( stitch_consulting_has_menu( 'primary-menu' ) ) {
    echo 'Primary menu is assigned';
}
?>
```

---

## Get Menu Items

```php
<?php
$items = stitch_consulting_get_menu_items( 'primary-menu' );

foreach ( $items as $item ) {
    echo $item->title; // Menu item title
    echo $item->url;   // Menu item URL
}
?>
```

---

## Styling Menu Items

### Active Menu Item

Active menu items automatically get the class `is-active` and have:
- Primary blue color (#195de6)
- Underline indicator
- Increased font weight (700)

### Hover State

Menu items show:
- Hover color: Primary color (#195de6)
- Background: Semi-transparent blue (5% opacity)
- Smooth transition (200ms)

### Submenus

- Displayed on hover (desktop)
- Absolute positioned dropdown
- White background with border
- Drop shadow effect

---

## Mobile Menu

### How It Works

- Automatically shows hamburger button on screens < 768px
- Click hamburger to open full-screen overlay menu
- Click menu items or background to close
- Press Escape key to close

### Customizing Mobile Breakpoint

Edit `/assets/css/navigation.css`:

```css
@media (max-width: 768px) {
  /* Mobile styles here */
}
```

Change `768px` to desired breakpoint.

---

## Keyboard Navigation

| Key | Action |
|-----|--------|
| Tab | Navigate to next menu item |
| Shift+Tab | Navigate to previous menu item |
| Enter | Open submenu or follow link |
| Escape | Close mobile menu overlay |
| Arrow Up/Down | Navigate between menu items |

---

## Accessibility Features

- Full keyboard navigation support
- ARIA-friendly markup from Walker
- High color contrast (WCAG AA compliant)
- Skip links support
- Focus indicators on all interactive elements
- Reduced motion support for animations

---

## Dark Mode

The menu automatically adapts to dark mode:
- Text color: White instead of black
- Link color: Light blue (#5b9fff)
- Background: Dark gray
- Proper contrast maintained

Enable via:
- User OS dark mode preference
- WordPress dark mode plugin
- CSS media query: `@media (prefers-color-scheme: dark)`

---

## Customizing Menu Styling

### CSS Variables

Edit `/style.css` `:root` section:

```css
:root {
  --wp-preset-color-primary: #195de6;
  --wp-navigation-item-color-hover: #195de6;
  --wp-navigation-item-bg-hover: rgba(25, 93, 230, 0.05);
  --wp-navigation-gap: 1rem;
}
```

### CSS Classes Added by Walker

- `.menu-item-{ID}` - Menu item wrapper
- `.is-active` - Current page indicator
- `.wp-block-navigation-item` - Block navigation item
- `.current-menu-item` - WordPress default active class
- `.current-post-parent` - Parent of current post

---

## Customizing Menu Walker

### Extend the Walker

```php
class My_Custom_Nav_Walker extends Stitch_Consulting_Nav_Walker {
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        // Custom logic
        parent::start_el( $output, $item, $depth, $args, $id );
    }
}

// Use in wp_nav_menu()
wp_nav_menu( array(
    'walker' => new My_Custom_Nav_Walker(),
) );
```

### Filter Menu Item Title

```php
add_filter( 'stitch_consulting_nav_menu_item_title', function( $title, $item, $args, $depth ) {
    if ( 'Contact' === $title ) {
        $title = '📧 ' . $title;
    }
    return $title;
}, 10, 4 );
```

---

## Template Usage Examples

### In header.html (Block Template)

```html
<!-- wp:navigation {"overlayMenu":"always"} /-->
```

### In custom template (PHP)

```php
<?php
if ( has_nav_menu( 'primary-menu' ) ) {
    wp_nav_menu( array(
        'theme_location' => 'primary-menu',
        'depth'          => 2,
        'fallback_cb'    => 'wp_page_menu',
    ) );
}
?>
```

---

## Troubleshooting

### Menu not showing in admin

1. Check WordPress version (requires 6.0+)
2. Verify `functions.php` is being loaded
3. Check browser console for JavaScript errors

### Mobile toggle not working

1. Verify `assets/js/navigation.js` is loaded
2. Check if JavaScript is enabled in browser
3. Verify breakpoint in `assets/css/navigation.css` matches your viewport

### Submenu not appearing

1. Create nested menu items in WordPress admin:
   - Create parent item (e.g., "Solutions")
   - Create child items indented under parent
2. Set menu depth to 2 or higher in `wp_nav_menu()` args

### Active indicator not showing

1. Ensure menu item URL matches current page URL
2. Check `current-menu-item` class is applied
3. Verify CSS is loaded: check browser DevTools Styles tab

### Dark mode not working

1. Verify OS dark mode setting
2. Check `@media (prefers-color-scheme: dark)` in CSS
3. Test with Firefox DevTools: Settings > Emulate CSS media feature prefers-color-scheme

---

## Files Related to Menus

```
stitch-consulting-theme/
├── inc/
│   └── menu-setup.php           # Menu registration & support
├── functions/
│   └── menus.php                # Helper functions & walker
├── functions.php                # Loads menu-setup.php
├── parts/
│   ├── header.html              # Primary menu display
│   └── footer.html              # Footer menu display
├── assets/
│   ├── css/
│   │   └── navigation.css       # Menu styling
│   └── js/
│       └── navigation.js        # Mobile toggle & keyboard nav
└── style.css                    # Global styles with CSS variables
```

---

## Performance Considerations

- Menu items cached by WordPress `wp_get_nav_menu_items()`
- CSS variables cascade efficiently
- JavaScript uses event delegation (minimal listeners)
- Mobile menu toggle doesn't load hidden content
- Minimal FOUC (Flash of Unstyled Content) with CSS approach

---

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari 14+, Chrome Mobile)

CSS custom properties and modern JavaScript patterns used.

---

## Additional Resources

- [WordPress Menus Documentation](https://developer.wordpress.org/plugins/menus/)
- [Walker_Nav_Menu Class](https://developer.wordpress.org/reference/classes/walker_nav_menu/)
- [WordPress Navigation Block](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-navigation/)
- [Stitch Consulting Theme Docs](../docs/)

---

## Support & Questions

For issues or questions:
1. Check browser console for errors (F12)
2. Verify menu is assigned in WordPress admin
3. Check file paths match your installation
4. Ensure WordPress 6.0+ is installed

---

**Theme Version:** 1.0.0
**Last Updated:** 2025-12-05
**Status:** Production Ready
