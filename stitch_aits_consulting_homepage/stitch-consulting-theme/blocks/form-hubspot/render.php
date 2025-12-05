<?php
/**
 * HubSpot Form Block - Frontend Render
 *
 * @package Stitch_Consulting_Theme
 * @subpackage Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$portal_id = isset( $attributes['portalId'] ) ? sanitize_text_field( $attributes['portalId'] ) : '';
$form_id = isset( $attributes['formId'] ) ? sanitize_text_field( $attributes['formId'] ) : '';
$title = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : '';
$description = isset( $attributes['description'] ) ? wp_kses_post( $attributes['description'] ) : '';
$show_title = isset( $attributes['showTitle'] ) ? $attributes['showTitle'] : false;
$align = isset( $attributes['align'] ) ? sanitize_text_field( $attributes['align'] ) : 'none';

// Check if portal and form IDs are configured
if ( empty( $portal_id ) || empty( $form_id ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<div style="padding: 20px; background: #f0f0f0; border: 1px solid #ccc; border-radius: 4px;">';
		echo '<p><strong>HubSpot Form:</strong> Please configure Portal ID and Form ID in block settings.</p>';
		echo '</div>';
	}
	return;
}

$align_class = ( 'none' !== $align ) ? 'align' . $align : '';
?>

<div class="wp-block-stitch-form-hubspot <?php echo esc_attr( $align_class ); ?>">
	<?php if ( $show_title && ! empty( $title ) ) : ?>
		<h2 class="hubspot-form-title"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $description ) ) : ?>
		<div class="hubspot-form-description">
			<?php echo wp_kses_post( $description ); ?>
		</div>
	<?php endif; ?>

	<div id="hubspot-form-<?php echo esc_attr( $form_id ); ?>" class="hubspot-form-container"></div>

	<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/embed/v2.js"></script>
	<script>
		if ( window.hbspt && window.hbspt.forms ) {
			hbspt.forms.create({
				region: "na1",
				portalId: "<?php echo esc_js( $portal_id ); ?>",
				formId: "<?php echo esc_js( $form_id ); ?>",
				target: "#hubspot-form-<?php echo esc_js( $form_id ); ?>"
			});
		}
	</script>
</div>
