<?php
/**
 * Block Registration & Management
 *
 * @package StitchConsultingTheme
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom block types
 */
add_action( 'init', function() {
	// Register blocks from inc/block-registration.php
	do_action( 'stitch_register_blocks' );
} );

/**
 * Filter allowed block types
 */
add_filter( 'allowed_block_types_all', function( $allowed_blocks, $post ) {
	// Define allowed core blocks
	$core_blocks = array(
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
	);
	
	// Allow custom Gutenberg blocks (from Phase 02)
	$custom_blocks = array(
		// 'stitch/hero' - will be added when Phase 02 registers
		// 'stitch/cta' - will be added when Phase 02 registers
	);
	
	return array_merge( $core_blocks, $custom_blocks );
}, 10, 2 );

/**
 * Register default block patterns
 */
add_action( 'init', function() {
	// Hero section pattern
	register_block_pattern(
		'stitch-consulting/hero-section',
		array(
			'title'       => __( 'Hero Section', 'stitch-consulting-theme' ),
			'description' => __( 'A hero section with title and CTA', 'stitch-consulting-theme' ),
			'content'     => '<!-- wp:group {"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull"><!-- wp:heading {"level":1} -->
<h1>' . __( 'Welcome to Our Site', 'stitch-consulting-theme' ) . '</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . __( 'This is a hero section pattern.', 'stitch-consulting-theme' ) . '</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link">' . __( 'Get Started', 'stitch-consulting-theme' ) . '</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->',
			'categories'  => array( 'stitch-consulting' ),
			'keywords'    => array( 'hero', 'section', 'cta' ),
			'isDefault'   => false,
		)
	);
	
	// Feature cards pattern
	register_block_pattern(
		'stitch-consulting/feature-cards',
		array(
			'title'       => __( 'Feature Cards', 'stitch-consulting-theme' ),
			'description' => __( 'Three feature cards in a grid', 'stitch-consulting-theme' ),
			'content'     => '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3>' . __( 'Feature One', 'stitch-consulting-theme' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . __( 'Feature description goes here.', 'stitch-consulting-theme' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3>' . __( 'Feature Two', 'stitch-consulting-theme' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . __( 'Feature description goes here.', 'stitch-consulting-theme' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3>' . __( 'Feature Three', 'stitch-consulting-theme' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . __( 'Feature description goes here.', 'stitch-consulting-theme' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
			'categories'  => array( 'stitch-consulting' ),
			'keywords'    => array( 'feature', 'cards', 'columns' ),
			'isDefault'   => false,
		)
	);
} );

/**
 * Restrict blocks in specific contexts
 */
add_filter( 'default_content', function( $post_content, $post ) {
	// Add default content structure for new posts
	return $post_content;
}, 10, 2 );

/**
 * Add block category
 */
add_filter( 'block_categories_all', function( $categories, $post ) {
	return array_merge(
		array(
			array(
				'slug'  => 'stitch-consulting',
				'title' => __( 'Stitch Consulting', 'stitch-consulting-theme' ),
				'icon'  => null,
			),
		),
		$categories
	);
}, 10, 2 );

/**
 * Filter block editor default settings
 */
add_filter( 'block_editor_settings_all', function( $settings ) {
	// Disable custom sizes
	$settings['__experimentalSpacing']['customSpacing'] = false;
	
	// Disable custom units
	$settings['__experimentalDimensions']['customUnits'] = false;
	
	return $settings;
} );
