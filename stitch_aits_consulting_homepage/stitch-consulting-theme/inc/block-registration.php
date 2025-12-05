<?php
/**
 * Block Type Registration
 *
 * This file registers custom Gutenberg block types for the theme.
 * Individual block implementations are loaded from the /blocks directory.
 *
 * @package StitchConsultingTheme
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom block types
 *
 * This hook is called during 'init' action and allows themes/plugins
 * to register their custom block types.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
add_action( 'init', function() {
	
	/**
	 * Register Hero Block (Phase 02)
	 * This will be implemented in Phase 02
	 */
	// register_block_type( STITCH_THEME_DIR . '/blocks/hero' );
	
	/**
	 * Register CTA Block (Phase 02)
	 * This will be implemented in Phase 02
	 */
	// register_block_type( STITCH_THEME_DIR . '/blocks/cta' );
	
	/**
	 * Register Card Block (Phase 02)
	 * This will be implemented in Phase 02
	 */
	// register_block_type( STITCH_THEME_DIR . '/blocks/card' );
	
	// Trigger custom action for external block registration
	do_action( 'stitch_register_custom_blocks' );
	
} );

/**
 * Allow dynamic blocks in theme
 */
add_filter( 'should_load_remote_block_patterns', '__return_false' );

/**
 * Register block categories for organization
 */
add_filter( 'block_categories_all', function( $categories, $post ) {
	// Remove default core categories
	$categories = array_filter( $categories, function( $cat ) {
		return in_array( $cat['slug'], array( 'common', 'formatting', 'layout', 'widgets', 'embed' ), true );
	} );
	
	// Add custom Stitch Consulting category
	array_unshift( $categories, array(
		'slug'  => 'stitch-consulting',
		'title' => __( 'Stitch Consulting Blocks', 'stitch-consulting-theme' ),
		'icon'  => 'star-filled',
	) );
	
	return $categories;
}, 10, 2 );

/**
 * Register block styles
 */
add_action( 'init', function() {
	// Register button block styles
	register_block_style( 'core/button', array(
		'name'      => 'primary',
		'label'     => __( 'Primary', 'stitch-consulting-theme' ),
		'isDefault' => true,
	) );
	
	register_block_style( 'core/button', array(
		'name'  => 'secondary',
		'label' => __( 'Secondary', 'stitch-consulting-theme' ),
	) );
	
	register_block_style( 'core/button', array(
		'name'  => 'outline',
		'label' => __( 'Outline', 'stitch-consulting-theme' ),
	) );
	
	// Register columns block styles
	register_block_style( 'core/columns', array(
		'name'      => 'default',
		'label'     => __( 'Default', 'stitch-consulting-theme' ),
		'isDefault' => true,
	) );
	
	// Register quote block styles
	register_block_style( 'core/quote', array(
		'name'      => 'default',
		'label'     => __( 'Default', 'stitch-consulting-theme' ),
		'isDefault' => true,
	) );
	
	register_block_style( 'core/quote', array(
		'name'  => 'large',
		'label' => __( 'Large Quote', 'stitch-consulting-theme' ),
	) );
} );

/**
 * Filter allowed blocks in editor
 */
add_filter( 'allowed_block_types_all', function( $allowed_blocks, $post ) {
	// Core blocks we want to allow
	$allowed = array(
		'core/paragraph',
		'core/heading',
		'core/image',
		'core/gallery',
		'core/list',
		'core/quote',
		'core/button',
		'core/buttons',
		'core/columns',
		'core/column',
		'core/group',
		'core/spacer',
		'core/separator',
		'core/table',
		'core/html',
		'core/code',
		'core/preformatted',
		'core/media-text',
		'core/pullquote',
		'core/verse',
		'core/search',
		'core/site-title',
		'core/site-tagline',
		'core/navigation',
		'core/social-links',
		'core/social-link',
		'core/archives',
		'core/calendar',
		'core/categories',
		'core/latest-posts',
		'core/shortcode',
		'core/embed',
		'core/video',
		'core/audio',
		'core/file',
	);
	
	// Add custom Stitch blocks (registered by Phase 02)
	$custom_blocks = apply_filters( 'stitch_allowed_custom_blocks', array() );
	
	return array_merge( $allowed, $custom_blocks );
}, 10, 2 );

/**
 * Enqueue custom block scripts and styles
 */
add_action( 'enqueue_block_assets', function() {
	// Enqueue block styles
	wp_enqueue_style(
		'stitch-blocks',
		STITCH_THEME_ASSETS . '/css/blocks.css',
		array(),
		STITCH_THEME_VERSION
	);
} );

/**
 * Enqueue block editor assets
 */
add_action( 'enqueue_block_editor_assets', function() {
	// Enqueue editor script for block registration
	wp_enqueue_script(
		'stitch-block-editor',
		STITCH_THEME_ASSETS . '/js/block-editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-rich-text' ),
		STITCH_THEME_VERSION,
		true
	);
} );
