<?php
/**
 * Stitch Consulting Theme Functions
 *
 * @package Stitch_Consulting_Theme
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define theme constants
 */
if ( ! defined( 'STITCH_CONSULTING_THEME_VERSION' ) ) {
	define( 'STITCH_CONSULTING_THEME_VERSION', '1.0.0' );
}

if ( ! defined( 'STITCH_CONSULTING_THEME_DIR' ) ) {
	define( 'STITCH_CONSULTING_THEME_DIR', get_template_directory() );
}

if ( ! defined( 'STITCH_CONSULTING_THEME_URI' ) ) {
	define( 'STITCH_CONSULTING_THEME_URI', get_template_directory_uri() );
}

/**
 * Load textdomain for translations
 */
load_theme_textdomain( 'stitch-consulting', STITCH_CONSULTING_THEME_DIR . '/languages' );

/**
 * Include menu setup
 *
 * Register navigation menus and add theme support for navigation blocks
 */
require_once STITCH_CONSULTING_THEME_DIR . '/inc/menu-setup.php';

/**
 * Include menu helper functions
 */
if ( file_exists( STITCH_CONSULTING_THEME_DIR . '/functions/menus.php' ) ) {
	require_once STITCH_CONSULTING_THEME_DIR . '/functions/menus.php';
}

/**
 * Register theme supports
 */
function stitch_consulting_theme_support() {
	// Add support for title tag
	add_theme_support( 'title-tag' );

	// Add support for custom logo
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 100,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Add support for post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add support for HTML5
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// Add support for responsive embeds
	add_theme_support( 'responsive-embeds' );

	// Add support for editor styles
	add_theme_support( 'editor-styles' );

	// Add support for block styles
	add_theme_support( 'wp-block-styles' );

	// Add support for wide alignment
	add_theme_support( 'align-wide' );
}

add_action( 'after_setup_theme', 'stitch_consulting_theme_support' );

/**
 * Enqueue global styles
 */
function stitch_consulting_enqueue_styles() {
	// Enqueue main stylesheet
	wp_enqueue_style(
		'stitch-consulting-style',
		STITCH_CONSULTING_THEME_URI . '/style.css',
		array(),
		filemtime( STITCH_CONSULTING_THEME_DIR . '/style.css' )
	);
}

add_action( 'wp_enqueue_scripts', 'stitch_consulting_enqueue_styles' );

/**
 * Enqueue admin styles
 */
function stitch_consulting_enqueue_admin_styles() {
	wp_enqueue_style(
		'stitch-consulting-admin',
		STITCH_CONSULTING_THEME_URI . '/assets/css/admin.css',
		array(),
		filemtime( STITCH_CONSULTING_THEME_DIR . '/assets/css/admin.css' )
	);
}

add_action( 'admin_enqueue_scripts', 'stitch_consulting_enqueue_admin_styles' );

/**
 * Custom excerpt length
 */
function stitch_consulting_excerpt_length( $length ) {
	return 25;
}

add_filter( 'excerpt_length', 'stitch_consulting_excerpt_length' );

/**
 * Custom excerpt more
 */
function stitch_consulting_excerpt_more( $more ) {
	return ' &hellip;';
}

add_filter( 'excerpt_more', 'stitch_consulting_excerpt_more' );
