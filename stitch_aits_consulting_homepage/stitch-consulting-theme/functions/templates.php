<?php
/**
 * Template Helper Functions
 *
 * Provides utility functions for page and post templates including:
 * - Breadcrumb navigation
 * - Social sharing functionality
 * - Related content queries
 *
 * @package StitchConsultingTheme
 * @subpackage Functions
 * @version 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get breadcrumb trail for current page/post
 *
 * @return array Array of breadcrumb items with url and title
 */
function stitch_get_breadcrumbs() {
	$breadcrumbs = array(
		array(
			'url'   => home_url( '/' ),
			'title' => __( 'Home', 'stitch-consulting-theme' ),
		),
	);

	// Add category/archive breadcrumbs
	if ( is_category() ) {
		$cat = get_queried_object();
		if ( $cat->parent ) {
			$parent_cat = get_cat_name( $cat->parent );
			$breadcrumbs[] = array(
				'url'   => get_category_link( $cat->parent ),
				'title' => $parent_cat,
			);
		}
		$breadcrumbs[] = array(
			'url'   => get_category_link( $cat->term_id ),
			'title' => $cat->name,
		);
	}

	// Add archive breadcrumb for single posts
	if ( is_single() ) {
		$post_type = get_post_type();
		if ( 'post' === $post_type ) {
			$breadcrumbs[] = array(
				'url'   => home_url( '/blog/' ),
				'title' => __( 'Blog', 'stitch-consulting-theme' ),
			);
		} elseif ( 'case-study' === $post_type ) {
			$breadcrumbs[] = array(
				'url'   => home_url( '/case-studies/' ),
				'title' => __( 'Case Studies', 'stitch-consulting-theme' ),
			);
		} elseif ( 'product' === $post_type ) {
			$breadcrumbs[] = array(
				'url'   => home_url( '/solutions/' ),
				'title' => __( 'Solutions', 'stitch-consulting-theme' ),
			);
		}

		// Add primary category if exists
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$cat = $categories[0];
			$breadcrumbs[] = array(
				'url'   => get_category_link( $cat->term_id ),
				'title' => $cat->name,
			);
		}

		// Add post title
		$breadcrumbs[] = array(
			'url'   => '',
			'title' => get_the_title(),
		);
	}

	// Add page breadcrumb
	if ( is_page() && ! is_front_page() ) {
		$page_title = get_the_title();
		$breadcrumbs[] = array(
			'url'   => '',
			'title' => $page_title,
		);
	}

	/**
	 * Filter breadcrumbs before returning
	 *
	 * @param array $breadcrumbs Array of breadcrumb items
	 */
	return apply_filters( 'stitch_breadcrumbs', $breadcrumbs );
}

/**
 * Display breadcrumb navigation HTML
 *
 * @param string $separator Separator between breadcrumbs
 */
function stitch_breadcrumbs( $separator = ' / ' ) {
	$breadcrumbs = stitch_get_breadcrumbs();
	$html        = '<nav class="breadcrumbs" role="navigation" aria-label="Breadcrumb">';
	$html       .= '<ol itemscope itemtype="https://schema.org/BreadcrumbList">';

	$count = count( $breadcrumbs );
	for ( $i = 0; $i < $count; $i++ ) {
		$breadcrumb = $breadcrumbs[ $i ];
		$position   = $i + 1;

		$html .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';

		if ( $breadcrumb['url'] ) {
			$html .= '<a itemprop="item" href="' . esc_url( $breadcrumb['url'] ) . '">';
			$html .= '<span itemprop="name">' . esc_html( $breadcrumb['title'] ) . '</span>';
			$html .= '</a>';
		} else {
			$html .= '<span itemprop="name">' . esc_html( $breadcrumb['title'] ) . '</span>';
		}

		$html .= '<meta itemprop="position" content="' . esc_attr( $position ) . '" />';
		$html .= '</li>';

		if ( $i < $count - 1 ) {
			$html .= '<li>' . $separator . '</li>';
		}
	}

	$html .= '</ol>';
	$html .= '</nav>';

	echo wp_kses_post( $html );
}

/**
 * Get related posts/articles
 *
 * @param int    $post_id Post ID (defaults to current post)
 * @param int    $number Number of related posts to return (default: 3)
 * @param string $post_type Post type (default: 'post')
 * @return array Array of WP_Post objects
 */
function stitch_get_related_posts( $post_id = 0, $number = 3, $post_type = 'post' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// Get categories of current post
	$categories = wp_get_post_categories( $post_id );

	if ( empty( $categories ) ) {
		// If no categories, just get recent posts
		$args = array(
			'post_type'      => $post_type,
			'posts_per_page' => $number,
			'post__not_in'   => array( $post_id ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
	} else {
		// Get posts in same categories
		$args = array(
			'post_type'      => $post_type,
			'posts_per_page' => $number,
			'category__in'   => $categories,
			'post__not_in'   => array( $post_id ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
	}

	/**
	 * Filter related posts query args
	 *
	 * @param array $args WP_Query arguments
	 * @param int   $post_id Current post ID
	 */
	$args = apply_filters( 'stitch_related_posts_args', $args, $post_id );

	$related = new WP_Query( $args );

	return $related->get_posts();
}

/**
 * Generate social share URL
 *
 * @param string $network Social network (linkedin, twitter, facebook, email)
 * @param string $url URL to share (defaults to current post)
 * @param string $title Title to share (defaults to current post title)
 * @return string Social network share URL
 */
function stitch_get_social_share_url( $network, $url = '', $title = '' ) {
	if ( ! $url ) {
		$url = get_the_permalink();
	}
	if ( ! $title ) {
		$title = get_the_title();
	}

	$url   = urlencode( $url );
	$title = urlencode( $title );

	switch ( $network ) {
		case 'linkedin':
			return sprintf( 'https://www.linkedin.com/sharing/share-offsite/?url=%s', $url );

		case 'twitter':
		case 'x':
			return sprintf( 'https://twitter.com/intent/tweet?text=%s&url=%s', $title, $url );

		case 'facebook':
			return sprintf( 'https://www.facebook.com/sharer/sharer.php?u=%s', $url );

		case 'email':
			return sprintf(
				'mailto:?subject=%s&body=%s',
				$title,
				urlencode( $url )
			);

		default:
			return '';
	}
}

/**
 * Check if post has featured image
 *
 * @param int $post_id Post ID (defaults to current post)
 * @return bool True if post has featured image
 */
function stitch_has_featured_image( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return (bool) has_post_thumbnail( $post_id );
}

/**
 * Get estimated reading time for post
 *
 * @param int $post_id Post ID (defaults to current post)
 * @param int $words_per_minute Words per minute (default: 200)
 * @return int Estimated reading time in minutes
 */
function stitch_get_reading_time( $post_id = 0, $words_per_minute = 200 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post = get_post( $post_id );

	// Strip tags and count words
	$content      = wp_strip_all_tags( $post->post_content );
	$word_count   = str_word_count( $content );
	$reading_time = ceil( $word_count / $words_per_minute );

	/**
	 * Filter reading time
	 *
	 * @param int $reading_time Estimated reading time in minutes
	 * @param int $word_count Total word count
	 * @param int $post_id Post ID
	 */
	return apply_filters( 'stitch_reading_time', max( 1, $reading_time ), $word_count, $post_id );
}

/**
 * Get post author social links (if ACF or custom fields exist)
 *
 * @param int $user_id User ID (defaults to post author)
 * @return array Array of social links
 */
function stitch_get_author_social_links( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_the_author_meta( 'ID' );
	}

	$socials = array();

	/**
	 * Filter author social links
	 *
	 * @param array $socials Array of social links
	 * @param int   $user_id User ID
	 */
	return apply_filters( 'stitch_author_social_links', $socials, $user_id );
}

/**
 * Register custom post types needed for templates
 * (This is a placeholder - actual registration should be in inc/block-registration.php)
 */
function stitch_register_custom_post_types() {
	// Case Study CPT
	if ( ! post_type_exists( 'case-study' ) ) {
		register_post_type(
			'case-study',
			array(
				'label'       => __( 'Case Studies', 'stitch-consulting-theme' ),
				'description' => __( 'Case study content', 'stitch-consulting-theme' ),
				'public'      => true,
				'show_ui'     => true,
				'show_in_rest' => true,
				'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'comments' ),
				'has_archive' => true,
				'rewrite'     => array( 'slug' => 'case-studies' ),
				'menu_icon'   => 'dashicons-portfolio',
			)
		);
	}

	// Product CPT
	if ( ! post_type_exists( 'product' ) ) {
		register_post_type(
			'product',
			array(
				'label'       => __( 'Solutions', 'stitch-consulting-theme' ),
				'description' => __( 'Product/Solution content', 'stitch-consulting-theme' ),
				'public'      => true,
				'show_ui'     => true,
				'show_in_rest' => true,
				'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
				'has_archive' => true,
				'rewrite'     => array( 'slug' => 'solutions' ),
				'menu_icon'   => 'dashicons-star-filled',
			)
		);
	}
}

// Hook for custom post type registration
add_action( 'init', 'stitch_register_custom_post_types', 5 );
