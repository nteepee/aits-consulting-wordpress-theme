<?php
/**
 * Render callback for Hero Landing Block
 *
 * @param array  $attributes Block attributes
 * @param string $content    Block content
 * @param object $block      Block object
 */

$background_image = isset($attributes['backgroundImage']) ? esc_url($attributes['backgroundImage']) : '';
$heading = isset($attributes['heading']) ? esc_html($attributes['heading']) : 'Revolutionizing Fleet Safety';
$subheading = isset($attributes['subheading']) ? esc_html($attributes['subheading']) : 'Through Data Intelligence';
$description = isset($attributes['description']) ? esc_html($attributes['description']) : '';
$primary_button_text = isset($attributes['primaryButtonText']) ? esc_html($attributes['primaryButtonText']) : 'Explore Our Solutions';
$primary_button_url = isset($attributes['primaryButtonUrl']) ? esc_url($attributes['primaryButtonUrl']) : '#';
$secondary_button_text = isset($attributes['secondaryButtonText']) ? esc_html($attributes['secondaryButtonText']) : 'Learn More';
$secondary_button_url = isset($attributes['secondaryButtonUrl']) ? esc_url($attributes['secondaryButtonUrl']) : '#';

$block_attrs = 'wp-block-stitch-hero-landing';
if (isset($attributes['align'])) {
	$block_attrs .= ' align' . esc_attr($attributes['align']);
}
?>

<section class="<?php echo esc_attr($block_attrs); ?> hero wp-block-stitch-hero-landing" style="position: relative; background-size: cover; background-position: center; min-height: 100vh; height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; <?php echo $background_image ? 'background-image: url(' . esc_attr($background_image) . ');' : ''; ?>">
	<div class="hero-overlay" style="position: absolute; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 1;"></div>
	<div class="hero-content" style="position: relative; z-index: 10; text-align: center; color: #FFFFFF; padding: 4rem 3rem; max-width: 55rem; margin: 0 auto;">
		<div class="hero-label" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.1em; color: #3D8BFF; margin-bottom: 1rem; display: block;">
			<?php _e('Featured', 'stitch-consulting'); ?>
		</div>

		<h1 class="hero-heading" style="font-size: 3rem; font-weight: 700; margin-bottom: 2rem; line-height: 1.3; letter-spacing: -0.01em;">
			<span class="gradient-text" style="background: linear-gradient(90deg, #3D8BFF, #AB23FF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; color: transparent;"><?php echo wp_kses_post($heading); ?></span>
			<?php if ($subheading): ?>
				<br /><?php echo wp_kses_post($subheading); ?>
			<?php endif; ?>
		</h1>

		<?php if ($description): ?>
			<p class="hero-description" style="font-size: 1.125rem; color: #A0A0A0; margin-bottom: 2rem; max-width: 32rem; margin-left: auto; margin-right: auto; line-height: 1.6;">
				<?php echo wp_kses_post($description); ?>
			</p>
		<?php endif; ?>

		<div class="hero-buttons" style="display: flex; flex-direction: row; gap: 1.5rem; justify-content: center; flex-wrap: wrap; margin-top: 1rem;">
			<a href="<?php echo esc_url($primary_button_url); ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.875rem 2.5rem; border-radius: 0.375rem; font-weight: 600; transition: all 300ms ease-out; border: none; cursor: pointer; text-decoration: none; font-size: 0.95rem; background-color: #195de6; color: #FFFFFF; box-shadow: 0 4px 12px rgba(25, 93, 230, 0.25);">
				<?php echo esc_html($primary_button_text); ?>
			</a>
			<a href="<?php echo esc_url($secondary_button_url); ?>" class="btn btn-secondary" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.875rem 2.5rem; border-radius: 0.375rem; font-weight: 600; transition: all 300ms ease-out; border: 1px solid #3D3D3D; cursor: pointer; text-decoration: none; font-size: 0.95rem; background-color: transparent; color: #FFFFFF;">
				<?php echo esc_html($secondary_button_text); ?>
			</a>
		</div>
	</div>
</section>
