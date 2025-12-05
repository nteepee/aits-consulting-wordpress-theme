<?php
/**
 * Testimonial Block Render
 *
 * @var array  $attributes Block attributes
 * @var string $content    Block content
 * @var object $block      Block object
 */

$quote = isset( $attributes['quote'] ) ? sanitize_text_field( $attributes['quote'] ) : '';
$author = isset( $attributes['author'] ) ? sanitize_text_field( $attributes['author'] ) : '';
$role = isset( $attributes['role'] ) ? sanitize_text_field( $attributes['role'] ) : '';
$author_image_url = isset( $attributes['authorImageUrl'] ) ? esc_url( $attributes['authorImageUrl'] ) : '';
$rating = isset( $attributes['rating'] ) ? (float) $attributes['rating'] : 5;

$block_classes = isset( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';

// Helper to render star rating
$full_stars = floor( $rating );
$half_star = ( $rating - $full_stars ) > 0 ? 1 : 0;
$empty_stars = 5 - $full_stars - $half_star;
$stars = str_repeat( '★', $full_stars ) . str_repeat( '☆', $empty_stars );
?>

<div class="wp-block-stitch-testimonial <?php echo $block_classes; ?>" style="padding: 32px; background-color: #141414; border-radius: 12px; border: 1px solid #262626;">
	<div style="display: flex; gap: 20px; align-items: flex-start;">
		<?php if ( $author_image_url ) : ?>
			<img src="<?php echo $author_image_url; ?>" alt="<?php echo esc_attr( $author ); ?>" class="wp-block-stitch-testimonial__image" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; flex-shrink: 0;" />
		<?php endif; ?>

		<div style="flex: 1;">
			<div class="wp-block-stitch-testimonial__rating" style="font-size: 1.25rem; color: #195de6; margin-bottom: 12px; line-height: 1;">
				<?php echo esc_html( $stars ); ?>
			</div>

			<?php if ( $quote ) : ?>
				<blockquote class="wp-block-stitch-testimonial__quote" style="font-size: 1rem; line-height: 1.6; margin-bottom: 16px; margin-top: 0; margin-left: 0; margin-right: 0; font-style: italic; color: #E9ECEF;">
					<?php echo wp_kses_post( $quote ); ?>
				</blockquote>
			<?php endif; ?>

			<div>
				<?php if ( $author ) : ?>
					<p class="wp-block-stitch-testimonial__author" style="font-size: 0.95rem; font-weight: 600; margin-bottom: 4px; margin-top: 0; color: #FFFFFF;">
						<?php echo esc_html( $author ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $role ) : ?>
					<p class="wp-block-stitch-testimonial__role" style="font-size: 0.85rem; margin-bottom: 0; margin-top: 0; color: #A3A3A3;">
						<?php echo esc_html( $role ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
