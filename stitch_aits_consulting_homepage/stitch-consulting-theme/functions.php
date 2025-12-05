<?php
/**
 * Stitch Consulting Theme Functions
 *
 * @package StitchConsultingTheme
 * @version 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define theme constants
define( 'STITCH_THEME_VERSION', '1.0.0' );
define( 'STITCH_THEME_DIR', get_template_directory() );
define( 'STITCH_THEME_URI', get_template_directory_uri() );
define( 'STITCH_THEME_ASSETS', STITCH_THEME_URI . '/assets' );

/**
 * Load textdomain for translations
 */
add_action( 'after_setup_theme', function() {
	load_theme_textdomain( 'stitch-consulting-theme', STITCH_THEME_DIR . '/languages' );
} );

/**
 * Load modular theme functions
 */
require_once STITCH_THEME_DIR . '/functions/setup.php';
require_once STITCH_THEME_DIR . '/functions/enqueue.php';
require_once STITCH_THEME_DIR . '/functions/blocks.php';
require_once STITCH_THEME_DIR . '/functions/hubspot.php';
require_once STITCH_THEME_DIR . '/functions/templates.php';

/**
 * Load includes
 */
require_once STITCH_THEME_DIR . '/inc/theme-support.php';
require_once STITCH_THEME_DIR . '/inc/block-registration.php';
require_once STITCH_THEME_DIR . '/inc/hubspot-integration.php';

/**
 * Register theme features
 */
add_action( 'after_setup_theme', function() {
	// Add theme support
	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 100,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'dark-editor-style' );
} );

/**
 * Register custom block patterns
 */
add_action( 'init', function() {
	// Register block pattern category
	register_block_pattern_category( 'stitch-consulting', array(
		'label' => __( 'Stitch Consulting', 'stitch-consulting-theme' ),
	) );
} );

/**
 * Block editor settings
 */
add_filter( 'block_editor_settings_all', function( $settings ) {
	// Disable custom colors if theme.json defines palette
	$settings['__experimentalFeatures']['color']['custom'] = false;
	
	return $settings;
} );

/**
 * Gutenberg editor settings
 */
add_action( 'enqueue_block_editor_assets', function() {
	wp_enqueue_style(
		'stitch-block-editor',
		STITCH_THEME_ASSETS . '/css/editor.css',
		array(),
		STITCH_THEME_VERSION
	);
} );

/**
 * Custom post types and taxonomies
 * (To be extended by child plugins or phases)
 */

/**
 * Custom hooks
 */
do_action( 'stitch_theme_init' );
