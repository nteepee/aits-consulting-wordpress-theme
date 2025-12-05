<?php
/**
 * Menu helper functions and walkers with full WCAG 2.1 AA accessibility
 *
 * Provides menu rendering with comprehensive ARIA attributes,
 * semantic HTML5, and keyboard navigation support.
 *
 * @package Stitch_Consulting_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get menu items for a specific menu location.
 *
 * Retrieves menu items for a given menu location with
 * support for nested items and custom filtering.
 *
 * @param string $location Menu location slug.
 * @return array Array of menu items or empty array if menu not set.
 */
function stitch_consulting_get_menu_items( $location ) {
	if ( ! has_nav_menu( $location ) ) {
		return array();
	}

	$menu_object = wp_get_nav_menu_object( $location );

	if ( ! $menu_object ) {
		return array();
	}

	$menu_items = wp_get_nav_menu_items( $menu_object->term_id );

	return is_array( $menu_items ) ? $menu_items : array();
}

/**
 * Check if menu exists and is assigned to a location.
 *
 * @param string $location Menu location slug.
 * @return bool True if menu is assigned to location.
 */
function stitch_consulting_has_menu( $location ) {
	return has_nav_menu( $location );
}

/**
 * Get the primary menu location name.
 *
 * @return string Menu location slug.
 */
function stitch_consulting_get_primary_menu_location() {
	return 'primary-menu';
}

/**
 * Get the footer menu location name.
 *
 * @return string Menu location slug.
 */
function stitch_consulting_get_footer_menu_location() {
	return 'footer-menu';
}

/**
 * Get the mobile menu location name.
 *
 * @return string Menu location slug.
 */
function stitch_consulting_get_mobile_menu_location() {
	return 'mobile-menu';
}

/**
 * Custom menu walker for primary navigation with WCAG 2.1 AA accessibility
 *
 * Extends Walker_Nav_Menu to add:
 * - ARIA attributes for menu structure
 * - Proper roles for menu items and submenus
 * - Active state management
 * - Semantic HTML structure
 *
 * @see Walker_Nav_Menu
 */
class Stitch_Consulting_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Start element (menu item).
	 *
	 * Renders a menu item with proper ARIA attributes and roles
	 * for keyboard navigation and screen reader support.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item Menu item data object.
	 * @param int      $depth Depth of menu item. Used for padding.
	 * @param stdClass $args An object of wp_nav_menu() arguments.
	 * @param int      $id Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		// Build CSS classes for list item
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		// Add active/current class for this item if it's the current page.
		if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current-post-parent', $classes, true ) ) {
			$classes[] = 'is-active';
		}

		/**
		 * Filters the CSS classes applied to a menu item's anchor element.
		 *
		 * @param string[] $classes The CSS classes that are applied to the menu item's `<a>` element.
		 * @param WP_Post  $item The current menu item object.
		 * @param stdClass $args An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of the menu item.
		 */
		$classes = apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth );
		$classes = implode( ' ', $classes );
		$classes = $classes ? sprintf( ' class="%s"', esc_attr( $classes ) ) : '';

		/**
		 * Filters the ID applied to a menu item's list item element.
		 *
		 * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param WP_Post  $item The current menu item object.
		 * @param stdClass $args An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of the menu item.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? sprintf( ' id="%s"', esc_attr( $id ) ) : '';

		// Add role="none" to list item for ARIA compliance (semantic HTML)
		$output .= $indent . '<li role="none"' . $id . $classes . '>';

		// Build anchor tag attributes
		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @param array    $atts The HTML attributes applied to the menu item's `<a>` element.
		 * @param WP_Post  $item The current menu item object.
		 * @param stdClass $args An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of the menu item.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		// Build HTML attribute string
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/**
		 * Add ARIA attributes for menu structure and interaction
		 */
		$aria_attrs = ' role="menuitem"';

		// Check if item has children (submenu)
		if ( in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
			// Parent menu items have submenu
			$aria_attrs .= ' aria-haspopup="menu" aria-expanded="false"';
		}

		/** This filter is documented in wp-includes/nav-menu-template.php */
		$title = apply_filters( 'nav_menu_item_title', $item->title, $item, $args, $depth );

		/**
		 * Filters a menu item's title.
		 *
		 * @param string   $title The menu item's title.
		 * @param WP_Post  $item The current menu item object.
		 * @param stdClass $args An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of the menu item.
		 */
		$title = apply_filters( 'stitch_consulting_nav_menu_item_title', $title, $item, $args, $depth );

		// Render anchor with ARIA attributes
		$output .= $indent . '<a' . $attributes . $aria_attrs . '>';
		$output .= $indent . esc_html( $title );
		$output .= '</a>';
	}

	/**
	 * Start submenu (ul/ol) element.
	 *
	 * Adds ARIA attributes for submenus including:
	 * - role="menu" for submenu structure
	 * - aria-hidden for visibility state
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth Depth of the submenu. Starts at 0.
	 * @param stdClass $args An object of wp_nav_menu() arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		// Add role="menu" and aria-hidden="true" for submenus
		// aria-hidden is managed by JavaScript
		$output .= $n . $indent . '<ul role="menu" aria-hidden="true" style="display:none;">' . $n;
	}

	/**
	 * End submenu element.
	 *
	 * Closes submenu list with proper formatting.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth Depth of the submenu.
	 * @param stdClass $args An object of wp_nav_menu() arguments.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';
		$output .= $indent . '</ul>' . $n;
	}
}

/**
 * Display primary navigation menu with accessibility.
 *
 * Outputs the primary navigation menu with custom walker
 * for enhanced styling capabilities and ARIA support.
 *
 * @param array $args Optional. Additional arguments for wp_nav_menu().
 * @return void
 */
function stitch_consulting_primary_menu( $args = array() ) {
	$defaults = array(
		'theme_location'  => stitch_consulting_get_primary_menu_location(),
		'menu_class'      => 'primary-navigation',
		'container'       => 'nav',
		'container_class' => 'primary-navigation-wrapper',
		'container_id'    => 'primary-navigation-wrapper',
		'fallback_cb'     => 'wp_page_menu',
		'depth'           => 2,
		'walker'          => new Stitch_Consulting_Nav_Walker(),
		'item_spacing'    => 'preserve',
	);

	$args = wp_parse_args( $args, $defaults );

	wp_nav_menu( $args );
}

/**
 * Display footer menu with accessibility.
 *
 * Outputs the footer menu with simplified styling and ARIA support.
 *
 * @param array $args Optional. Additional arguments for wp_nav_menu().
 * @return void
 */
function stitch_consulting_footer_menu( $args = array() ) {
	$defaults = array(
		'theme_location'  => stitch_consulting_get_footer_menu_location(),
		'menu_class'      => 'footer-navigation',
		'container'       => 'nav',
		'container_class' => 'footer-navigation-wrapper',
		'container_id'    => 'footer-navigation-wrapper',
		'fallback_cb'     => 'wp_page_menu',
		'depth'           => 1,
		'item_spacing'    => 'preserve',
	);

	$args = wp_parse_args( $args, $defaults );

	wp_nav_menu( $args );
}

/**
 * Get menu item link properties with proper escaping.
 *
 * @param WP_Post $item Menu item object.
 * @return array Array with 'url' and 'title' keys.
 */
function stitch_consulting_get_menu_item_link( $item ) {
	return array(
		'url'   => ! empty( $item->url ) ? esc_url( $item->url ) : '',
		'title' => ! empty( $item->title ) ? esc_html( $item->title ) : '',
	);
}

/**
 * Check if a menu item is the current page.
 *
 * Checks if menu item has active state indicators.
 *
 * @param WP_Post $item Menu item object.
 * @return bool True if item is current page.
 */
function stitch_consulting_is_menu_item_active( $item ) {
	if ( empty( $item->classes ) ) {
		return false;
	}

	return in_array( 'current-menu-item', (array) $item->classes, true ) ||
		   in_array( 'current-post-parent', (array) $item->classes, true );
}

/**
 * Enqueue navigation JavaScript for ARIA state management
 *
 * Loads the navigation.js file which handles:
 * - aria-expanded state updates
 * - aria-hidden state management
 * - Keyboard navigation (Escape, Arrow keys)
 * - Mobile menu toggle
 * - Screen reader announcements
 *
 * @return void
 */
function stitch_consulting_enqueue_navigation_js() {
	wp_enqueue_script(
		'stitch-consulting-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		'1.0.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'stitch_consulting_enqueue_navigation_js' );
