<?php
/**
 * Hero Block Render
 *
 * @var array  $attributes Block attributes
 * @var string $content    Block content
 * @var object $block      Block object
 */

$heading = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
$subheading = isset( $attributes['subheading'] ) ? sanitize_text_field( $attributes['subheading'] ) : '';
$background_image_url = isset( $attributes['backgroundImageUrl'] ) ? esc_url( $attributes['backgroundImageUrl'] ) : '';
$overlay_opacity = isset( $attributes['overlayOpacity'] ) ? (float) $attributes['overlayOpacity'] : 0.5;
$primary_button_text = isset( $attributes['primaryButtonText'] ) ? sanitize_text_field( $attributes['primaryButtonText'] ) : '';
$primary_button_url = isset( $attributes['primaryButtonUrl'] ) ? esc_url( $attributes['primaryButtonUrl'] ) : '';
$secondary_button_text = isset( $attributes['secondaryButtonText'] ) ? sanitize_text_field( $attributes['secondaryButtonText'] ) : '';
$secondary_button_url = isset( $attributes['secondaryButtonUrl'] ) ? esc_url( $attributes['secondaryButtonUrl'] ) : '';
$min_height = isset( $attributes['minHeight'] ) ? sanitize_text_field( $attributes['minHeight'] ) : '600px';

$block_classes = isset( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';
?>

<div class="wp-block-stitch-hero <?php echo $block_classes; ?>">
	<div
		class="wp-block-stitch-hero__container"
		style="<?php echo $background_image_url ? 'background-image: url(' . $background_image_url . ');' : ''; ?> background-size: cover; background-position: center; min-height: <?php echo esc_attr( $min_height ); ?>; position: relative;"
	>
		<div
			class="wp-block-stitch-hero__overlay"
			style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(10, 10, 10, <?php echo $overlay_opacity; ?>); z-index: 1;"
		></div>

		<div class="wp-block-stitch-hero__content" style="position: relative; z-index: 2; padding: 80px 40px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: #fff; min-height: <?php echo esc_attr( $min_height ); ?>;">
			<?php if ( $heading ) : ?>
				<h1 class="wp-block-stitch-hero__heading" style="font-size: 3.75rem; font-weight: 900; line-height: 1.2; margin-bottom: 20px; max-width: 800px;">
					<?php echo wp_kses_post( $heading ); ?>
				</h1>
			<?php endif; ?>

			<?php if ( $subheading ) : ?>
				<p class="wp-block-stitch-hero__subheading" style="font-size: 1.25rem; line-height: 1.6; margin-bottom: 40px; max-width: 600px; color: #E9ECEF;">
					<?php echo wp_kses_post( $subheading ); ?>
				</p>
			<?php endif; ?>

			<div class="wp-block-stitch-hero__buttons" style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
				<?php if ( $primary_button_text && $primary_button_url ) : ?>
					<a
						href="<?php echo $primary_button_url; ?>"
						class="wp-block-stitch-hero__button wp-block-stitch-hero__button--primary"
						style="background-color: #195de6; color: #fff; padding: 12px 32px; font-size: 1rem; font-weight: 700; border-radius: 8px; text-decoration: none; display: inline-block; transition: all 0.3s ease;"
					>
						<?php echo esc_html( $primary_button_text ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $secondary_button_text && $secondary_button_url ) : ?>
					<a
						href="<?php echo $secondary_button_url; ?>"
						class="wp-block-stitch-hero__button wp-block-stitch-hero__button--secondary"
						style="background-color: transparent; color: #fff; border: 2px solid #fff; padding: 10px 30px; font-size: 1rem; font-weight: 700; border-radius: 8px; text-decoration: none; display: inline-block; transition: all 0.3s ease;"
					>
						<?php echo esc_html( $secondary_button_text ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
