<?php
/**
 * CTA Block Render
 *
 * @var array  $attributes Block attributes
 * @var string $content    Block content
 * @var object $block      Block object
 */

$heading = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
$description = isset( $attributes['description'] ) ? sanitize_text_field( $attributes['description'] ) : '';
$button_text = isset( $attributes['buttonText'] ) ? sanitize_text_field( $attributes['buttonText'] ) : '';
$button_url = isset( $attributes['buttonUrl'] ) ? esc_url( $attributes['buttonUrl'] ) : '';
$background_color = isset( $attributes['backgroundColor'] ) ? sanitize_text_field( $attributes['backgroundColor'] ) : 'primary';
$text_alignment = isset( $attributes['textAlignment'] ) ? sanitize_text_field( $attributes['textAlignment'] ) : 'center';

$background_color_map = [
	'primary' => '#195de6',
	'dark' => '#0A0A0A',
	'surface' => '#141414'
];

$bg_color = isset( $background_color_map[ $background_color ] ) ? $background_color_map[ $background_color ] : $background_color_map['primary'];
$text_color = $background_color === 'primary' ? '#fff' : '#E9ECEF';

$block_classes = isset( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';
?>

<div class="wp-block-stitch-cta wp-block-stitch-cta--<?php echo esc_attr( $background_color ); ?> <?php echo $block_classes; ?>" style="background-color: <?php echo esc_attr( $bg_color ); ?>;">
	<div class="wp-block-stitch-cta__content" style="padding: 60px 40px; text-align: <?php echo esc_attr( $text_alignment ); ?>; color: <?php echo esc_attr( $text_color ); ?>; max-width: 800px; margin: 0 auto;">
		<?php if ( $heading ) : ?>
			<h2 class="wp-block-stitch-cta__heading" style="font-size: 2.25rem; font-weight: 700; margin-bottom: 20px; margin-top: 0;">
				<?php echo wp_kses_post( $heading ); ?>
			</h2>
		<?php endif; ?>

		<?php if ( $description ) : ?>
			<p class="wp-block-stitch-cta__description" style="font-size: 1rem; line-height: 1.6; margin-bottom: 30px; margin-top: 0; opacity: 0.9;">
				<?php echo wp_kses_post( $description ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $button_text && $button_url ) : ?>
			<a href="<?php echo $button_url; ?>" class="wp-block-stitch-cta__button" style="display: inline-block; background-color: #fff; color: #195de6; padding: 12px 32px; font-size: 1rem; font-weight: 700; border-radius: 8px; text-decoration: none; transition: all 0.3s ease;">
				<?php echo esc_html( $button_text ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
