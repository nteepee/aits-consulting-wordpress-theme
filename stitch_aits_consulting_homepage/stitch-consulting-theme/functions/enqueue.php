<?php
/**
 * Enqueue Styles and Scripts
 *
 * @package StitchConsultingTheme
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend styles
 */
add_action( 'wp_enqueue_scripts', function() {
	// Enqueue theme styles
	wp_enqueue_style(
		'stitch-style',
		STITCH_THEME_ASSETS . '/css/style.css',
		array(),
		STITCH_THEME_VERSION,
		'all'
	);
	
	// Enqueue CSS variables
	wp_enqueue_style(
		'stitch-variables',
		STITCH_THEME_ASSETS . '/css/variables.css',
		array(),
		STITCH_THEME_VERSION,
		'all'
	);
	
	// Enqueue custom fonts via Google Fonts
	wp_enqueue_style(
		'stitch-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap',
		array(),
		STITCH_THEME_VERSION,
		'all'
	);
	
	// Add inline styles for dark mode support
	wp_add_inline_style( 'stitch-style', stitch_get_dark_mode_styles() );
	
	// Comment reply script
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
} );

/**
 * Enqueue frontend scripts
 */
add_action( 'wp_enqueue_scripts', function() {
	// Enqueue main theme script
	wp_enqueue_script(
		'stitch-main',
		STITCH_THEME_ASSETS . '/js/main.js',
		array(),
		STITCH_THEME_VERSION,
		true
	);
	
	// Localize script with theme data
	wp_localize_script( 'stitch-main', 'stitchTheme', array(
		'themeUrl'  => STITCH_THEME_URI,
		'assetsUrl' => STITCH_THEME_ASSETS,
		'isDarkMode' => get_theme_mod( 'dark_mode_enabled', true ),
	) );
} );

/**
 * Enqueue editor styles
 */
add_action( 'enqueue_block_editor_assets', function() {
	wp_enqueue_style(
		'stitch-editor-style',
		STITCH_THEME_ASSETS . '/css/editor.css',
		array( 'wp-edit-blocks' ),
		STITCH_THEME_VERSION,
		'all'
	);
	
	// Enqueue editor script
	wp_enqueue_script(
		'stitch-editor-script',
		STITCH_THEME_ASSETS . '/js/editor.js',
		array( 'wp-blocks', 'wp-dom' ),
		STITCH_THEME_VERSION,
		true
	);
} );

/**
 * Dequeue unnecessary WordPress styles
 */
add_action( 'wp_enqueue_scripts', function() {
	// Dequeue default WordPress emoji styles
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	
	// Dequeue Block Library CSS if not using blocks
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-block-style' );
}, 100 );

/**
 * Get dark mode styles
 *
 * @return string CSS for dark mode
 */
function stitch_get_dark_mode_styles() {
	$css = '
	:root {
		--stitch-color-bg-dark: #0A0A0A;
		--stitch-color-bg-light: #f6f6f8;
		--stitch-color-surface-dark: #141414;
		--stitch-color-primary: #195de6;
		--stitch-color-text-white: #FFFFFF;
		--stitch-color-text-muted: #A3A3A3;
		--stitch-color-border-dark: #262626;
	}
	
	@media (prefers-color-scheme: dark) {
		body {
			background-color: var(--stitch-color-bg-dark);
			color: var(--stitch-color-text-white);
		}
		
		a {
			color: var(--stitch-color-primary);
		}
		
		a:hover {
			opacity: 0.8;
		}
	}
	
	@media (prefers-color-scheme: light) {
		body {
			background-color: var(--stitch-color-bg-light);
			color: #333;
		}
		
		a {
			color: var(--stitch-color-primary);
		}
	}
	';
	
	return $css;
}
