<?php
/**
 * Theme Support & WordPress Feature Registration
 *
 * @package StitchConsultingTheme
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme features
 */
add_action( 'after_setup_theme', function() {
	/**
	 * Add theme support for various WordPress features
	 */
	
	// Classic theme support
	add_theme_support( 'title-tag' );
	
	// Custom logo support
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 100,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-tagline' ),
	) );
	
	// Featured images (thumbnails)
	add_theme_support( 'post-thumbnails' );
	
	// HTML5 support for forms, comments, galleries, captions, scripts, styles
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	
	// Feed links (RSS, Atom)
	add_theme_support( 'automatic-feed-links' );
	
	// Responsive embeds
	add_theme_support( 'responsive-embeds' );
	
	// Widget block editor
	add_theme_support( 'customize-selective-refresh-widgets' );
	
	// Block styles
	add_theme_support( 'wp-block-styles' );
	
	// Dark editor style
	add_theme_support( 'dark-editor-style' );
	
	// WooCommerce support (if needed for Phase 06)
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	
	// Gutenberg block editor enhancements
	add_theme_support( 'editor-styles' );
	add_theme_support( 'editor-color-palette' );
	add_theme_support( 'editor-font-sizes' );
	add_theme_support( 'custom-spacing' );
	add_theme_support( 'custom-line-height' );
	add_theme_support( 'custom-units' );
	
	// Experimental features for FSE (Full Site Editing)
	add_theme_support( 'block-templates' );
	
} );

/**
 * Add editor stylesheet
 */
add_action( 'enqueue_block_editor_assets', function() {
	wp_enqueue_style(
		'stitch-editor-style',
		STITCH_THEME_ASSETS . '/css/editor.css',
		array( 'wp-edit-blocks' ),
		STITCH_THEME_VERSION
	);
} );

/**
 * Register custom color palettes for editor
 */
add_action( 'after_setup_theme', function() {
	$palette = array(
		array(
			'name'  => __( 'Primary', 'stitch-consulting-theme' ),
			'slug'  => 'primary',
			'color' => '#195de6',
		),
		array(
			'name'  => __( 'Secondary', 'stitch-consulting-theme' ),
			'slug'  => 'secondary',
			'color' => '#003366',
		),
		array(
			'name'  => __( 'Dark Background', 'stitch-consulting-theme' ),
			'slug'  => 'dark-bg',
			'color' => '#0A0A0A',
		),
		array(
			'name'  => __( 'Light Background', 'stitch-consulting-theme' ),
			'slug'  => 'light-bg',
			'color' => '#f6f6f8',
		),
		array(
			'name'  => __( 'Surface Dark', 'stitch-consulting-theme' ),
			'slug'  => 'surface-dark',
			'color' => '#141414',
		),
		array(
			'name'  => __( 'White', 'stitch-consulting-theme' ),
			'slug'  => 'white',
			'color' => '#FFFFFF',
		),
		array(
			'name'  => __( 'Border Dark', 'stitch-consulting-theme' ),
			'slug'  => 'border-dark',
			'color' => '#262626',
		),
		array(
			'name'  => __( 'Text Muted', 'stitch-consulting-theme' ),
			'slug'  => 'text-muted',
			'color' => '#A3A3A3',
		),
	);
	
	add_theme_support( 'editor-color-palette', $palette );
} );

/**
 * Customize editor default layout width
 */
add_filter( 'block_editor_settings_all', function( $settings ) {
	$settings['maxWidth'] = 960;
	
	return $settings;
} );

/**
 * Add custom CSS classes for block editor
 */
add_filter( 'body_class', function( $classes ) {
	if ( is_admin() ) {
		$classes[] = 'stitch-admin';
		$classes[] = 'stitch-theme-active';
	}
	
	return $classes;
} );

/**
 * Disable WordPress theme features we don't need
 */
add_action( 'after_setup_theme', function() {
	// Remove support for comments (optional, can be enabled later)
	// remove_post_type_support( 'post', 'comments' );
	// remove_post_type_support( 'page', 'comments' );
	
	// Remove WordPress comment form default styles
	add_filter( 'option_comment_form_default_url', '__return_empty_string' );
} );

/**
 * Register sidebar (widgets) if needed
 */
add_action( 'widgets_init', function() {
	register_sidebar( array(
		'name'          => __( 'Primary Sidebar', 'stitch-consulting-theme' ),
		'id'            => 'primary-sidebar',
		'description'   => __( 'Main sidebar', 'stitch-consulting-theme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
} );
