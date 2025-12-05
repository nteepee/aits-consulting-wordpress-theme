<?php
/**
 * Menu setup and registration
 *
 * @package Stitch_Consulting_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme menu locations.
 *
 * Registers primary navigation and footer menu locations
 * for WordPress menu management.
 *
 * @hook after_setup_theme
 * @return void
 */
function stitch_consulting_register_menus() {
	register_nav_menus(
		array(
			'primary-menu'   => esc_html__( 'Primary Navigation', 'stitch-consulting' ),
			'footer-menu'    => esc_html__( 'Footer Menu', 'stitch-consulting' ),
			'mobile-menu'    => esc_html__( 'Mobile Menu (optional)', 'stitch-consulting' ),
		)
	);
}

add_action( 'after_setup_theme', 'stitch_consulting_register_menus' );

/**
 * Add theme support for navigation blocks.
 *
 * Enables WordPress native navigation block support with
 * responsive overlays for mobile menu handling.
 *
 * @hook after_setup_theme
 * @return void
 */
function stitch_consulting_add_navigation_support() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
}

add_action( 'after_setup_theme', 'stitch_consulting_add_navigation_support' );

/**
 * Enqueue navigation styles.
 *
 * Loads CSS for navigation blocks and mobile menu interaction.
 *
 * @hook wp_enqueue_scripts
 * @return void
 */
function stitch_consulting_enqueue_navigation_styles() {
	wp_enqueue_style(
		'stitch-consulting-navigation',
		get_template_directory_uri() . '/assets/css/navigation.css',
		array(),
		filemtime( get_template_directory() . '/assets/css/navigation.css' )
	);
}

add_action( 'wp_enqueue_scripts', 'stitch_consulting_enqueue_navigation_styles' );

/**
 * Enqueue navigation scripts for mobile menu toggle.
 *
 * Loads JavaScript for mobile menu hamburger toggle and interaction.
 *
 * @hook wp_enqueue_scripts
 * @return void
 */
function stitch_consulting_enqueue_navigation_scripts() {
	wp_enqueue_script(
		'stitch-consulting-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		filemtime( get_template_directory() . '/assets/js/navigation.js' ),
		true
	);
}

add_action( 'wp_enqueue_scripts', 'stitch_consulting_enqueue_navigation_scripts' );
