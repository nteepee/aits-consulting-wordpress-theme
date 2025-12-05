<?php
/**
 * Block Registration and Loading
 *
 * Registers all custom Gutenberg blocks for the Stitch theme
 */

if ( ! function_exists( 'stitch_register_blocks' ) ) {
	function stitch_register_blocks() {
		$blocks = [
			'hero',
			'cta',
			'feature-card',
			'card-grid',
			'testimonial',
			'form',
			'stats'
		];

		foreach ( $blocks as $block ) {
			$block_path = get_theme_file_path( "blocks/{$block}/block.json" );
			if ( file_exists( $block_path ) ) {
				register_block_type( $block_path );
			}
		}
	}

	add_action( 'init', 'stitch_register_blocks' );
}

if ( ! function_exists( 'stitch_enqueue_block_assets' ) ) {
	function stitch_enqueue_block_assets() {
		// Enqueue Material Symbols font for blocks
		wp_enqueue_style(
			'material-symbols',
			'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200',
			[],
			'1.0'
		);

		// Enqueue Inter font
		wp_enqueue_style(
			'inter-font',
			'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900',
			[],
			'1.0'
		);
	}

	add_action( 'wp_enqueue_scripts', 'stitch_enqueue_block_assets' );
	add_action( 'enqueue_block_editor_assets', 'stitch_enqueue_block_assets' );
}

if ( ! function_exists( 'stitch_register_block_category' ) ) {
	function stitch_register_block_category( $block_categories, $editor_context ) {
		return array_merge(
			[
				[
					'slug'  => 'stitch',
					'title' => __( 'Stitch Blocks', 'stitch' ),
					'icon'  => 'layout'
				]
			],
			$block_categories
		);
	}

	add_filter( 'block_categories_all', 'stitch_register_block_category', 10, 2 );
}

if ( ! function_exists( 'stitch_enqueue_form_handler' ) ) {
	function stitch_enqueue_form_handler() {
		// Load form handler for form block AJAX processing
		require_once get_theme_file_path( 'blocks/form/form-handler.php' );
	}

	add_action( 'wp_loaded', 'stitch_enqueue_form_handler' );
}

/**
 * Block Configuration - Enable specific supports for all blocks
 */
if ( ! function_exists( 'stitch_setup_block_supports' ) ) {
	function stitch_setup_block_supports() {
		// Add theme support for block styles
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );

		// Enable block alignments
		add_theme_support( 'align-wide' );

		// Add custom color palette if theme.json is not available
		if ( ! function_exists( 'wp_theme_has_theme_json' ) || ! wp_theme_has_theme_json() ) {
			add_theme_support(
				'editor-color-palette',
				[
					[
						'name'  => __( 'Primary Blue', 'stitch' ),
						'slug'  => 'primary',
						'color' => '#195de6'
					],
					[
						'name'  => __( 'Background Dark', 'stitch' ),
						'slug'  => 'bg-dark',
						'color' => '#0A0A0A'
					],
					[
						'name'  => __( 'Surface Dark', 'stitch' ),
						'slug'  => 'surface-dark',
						'color' => '#141414'
					],
					[
						'name'  => __( 'Text Light', 'stitch' ),
						'slug'  => 'text-light',
						'color' => '#E9ECEF'
					],
					[
						'name'  => __( 'White', 'stitch' ),
						'slug'  => 'white',
						'color' => '#FFFFFF'
					]
				]
			);
		}
	}

	add_action( 'after_setup_theme', 'stitch_setup_block_supports' );
}

/**
 * Enqueue block scripts and styles
 */
if ( ! function_exists( 'stitch_enqueue_block_scripts' ) ) {
	function stitch_enqueue_block_scripts() {
		// Enqueue common block styles
		$blocks_dir = get_theme_file_path( 'blocks' );
		$theme_url = get_theme_file_uri();

		if ( is_dir( $blocks_dir ) ) {
			$blocks = array_diff( scandir( $blocks_dir ), [ '.', '..' ] );

			foreach ( $blocks as $block ) {
				$style_file = "{$blocks_dir}/{$block}/style.css";
				if ( file_exists( $style_file ) ) {
					wp_enqueue_style(
						"stitch-block-{$block}",
						"{$theme_url}/blocks/{$block}/style.css",
						[],
						filemtime( $style_file )
					);
				}
			}
		}
	}

	add_action( 'wp_enqueue_scripts', 'stitch_enqueue_block_scripts' );
}
