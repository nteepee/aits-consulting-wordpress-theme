<?php
/**
 * Feature Card Block Render
 *
 * @var array  $attributes Block attributes
 * @var string $content    Block content
 * @var object $block      Block object
 */

$icon = isset( $attributes['icon'] ) ? sanitize_text_field( $attributes['icon'] ) : 'star';
$title = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : '';
$description = isset( $attributes['description'] ) ? sanitize_text_field( $attributes['description'] ) : '';
$link_text = isset( $attributes['linkText'] ) ? sanitize_text_field( $attributes['linkText'] ) : '';
$link_url = isset( $attributes['linkUrl'] ) ? esc_url( $attributes['linkUrl'] ) : '';
$variant = isset( $attributes['variant'] ) ? sanitize_text_field( $attributes['variant'] ) : 'default';

$block_classes = isset( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';
$border_style = $variant === 'bordered' ? 'border: 1px solid #262626;' : '';
?>

<div class="wp-block-stitch-feature-card wp-block-stitch-feature-card--<?php echo esc_attr( $variant ); ?> <?php echo $block_classes; ?>" style="padding: 24px; background-color: #141414; <?php echo $border_style; ?> border-radius: 12px; transition: all 0.3s ease;">
	<div class="wp-block-stitch-feature-card__icon" style="font-size: 2rem; margin-bottom: 16px; font-family: 'Material Symbols Outlined'; color: #195de6;">
		<?php echo esc_html( $icon ); ?>
	</div>

	<?php if ( $title ) : ?>
		<h3 class="wp-block-stitch-feature-card__title" style="font-size: 1.25rem; font-weight: 700; margin-bottom: 12px; margin-top: 0; color: #FFFFFF;">
			<?php echo wp_kses_post( $title ); ?>
		</h3>
	<?php endif; ?>

	<?php if ( $description ) : ?>
		<p class="wp-block-stitch-feature-card__description" style="font-size: 0.95rem; line-height: 1.6; margin-bottom: 16px; margin-top: 0; color: #A3A3A3;">
			<?php echo wp_kses_post( $description ); ?>
		</p>
	<?php endif; ?>

	<?php if ( $link_text && $link_url ) : ?>
		<a href="<?php echo $link_url; ?>" class="wp-block-stitch-feature-card__link" style="color: #195de6; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
			<?php echo esc_html( $link_text ); ?> →
		</a>
	<?php endif; ?>
</div>
