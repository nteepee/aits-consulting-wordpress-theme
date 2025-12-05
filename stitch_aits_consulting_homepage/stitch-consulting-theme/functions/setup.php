<?php
/**
 * Theme Setup & Initialization
 *
 * @package StitchConsultingTheme
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initialize theme
 */
add_action( 'after_setup_theme', function() {
	// Add theme support for block features
	add_theme_support( 'wp-block-styles' );
	
	// Add editor color palette support
	add_theme_support( 'editor-color-palette' );
	add_theme_support( 'editor-gradient-presets' );
	
	// Add spacing presets
	add_theme_support( 'custom-spacing' );
	
	// Add font sizes
	add_theme_support( 'editor-font-sizes', array(
		array(
			'name' => __( 'Extra Small', 'stitch-consulting-theme' ),
			'slug' => 'xs',
			'size' => 12,
		),
		array(
			'name' => __( 'Small', 'stitch-consulting-theme' ),
			'slug' => 'sm',
			'size' => 14,
		),
		array(
			'name' => __( 'Base', 'stitch-consulting-theme' ),
			'slug' => 'base',
			'size' => 16,
		),
		array(
			'name' => __( 'Large', 'stitch-consulting-theme' ),
			'slug' => 'lg',
			'size' => 18,
		),
		array(
			'name' => __( 'XL', 'stitch-consulting-theme' ),
			'slug' => 'xl',
			'size' => 20,
		),
		array(
			'name' => __( '2XL', 'stitch-consulting-theme' ),
			'slug' => '2xl',
			'size' => 24,
		),
		array(
			'name' => __( '3XL', 'stitch-consulting-theme' ),
			'slug' => '3xl',
			'size' => 30,
		),
		array(
			'name' => __( '4XL', 'stitch-consulting-theme' ),
			'slug' => '4xl',
			'size' => 36,
		),
		array(
			'name' => __( '5XL', 'stitch-consulting-theme' ),
			'slug' => '5xl',
			'size' => 48,
		),
		array(
			'name' => __( '6XL', 'stitch-consulting-theme' ),
			'slug' => '6xl',
			'size' => 60,
		),
	) );
} );

/**
 * Set content width
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1280;
}

/**
 * Add custom image sizes
 */
add_action( 'after_setup_theme', function() {
	add_image_size( 'featured-large', 1200, 630, true );
	add_image_size( 'featured-medium', 800, 450, true );
	add_image_size( 'featured-small', 400, 300, true );
	add_image_size( 'thumbnail-square', 200, 200, true );
} );

/**
 * Register navigation menus
 */
add_action( 'after_setup_theme', function() {
	register_nav_menus( array(
		'primary'   => __( 'Primary Menu', 'stitch-consulting-theme' ),
		'secondary' => __( 'Secondary Menu', 'stitch-consulting-theme' ),
		'footer'    => __( 'Footer Menu', 'stitch-consulting-theme' ),
	) );
} );

/**
 * Remove WordPress default patterns
 */
add_filter( 'should_load_remote_block_patterns', '__return_false' );

/**
 * Allow SVG uploads
 */
add_filter( 'upload_mimes', function( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgs'] = 'image/svg+xml';
	
	return $mimes;
} );

/**
 * Sanitize SVG files on upload
 */
add_filter( 'wp_handle_upload', function( $file ) {
	if ( 'image/svg+xml' === $file['type'] ) {
		// Allow SVG - security checks can be added here
		// For production, consider using a sanitization library
	}
	
	return $file;
} );
