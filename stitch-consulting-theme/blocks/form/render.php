<?php
/**
 * Form Block Render
 *
 * @var array  $attributes Block attributes
 * @var string $content    Block content
 * @var object $block      Block object
 */

$fields = isset( $attributes['fields'] ) ? $attributes['fields'] : [];
$submit_button_text = isset( $attributes['submitButtonText'] ) ? sanitize_text_field( $attributes['submitButtonText'] ) : 'Send Message';
$success_message = isset( $attributes['successMessage'] ) ? sanitize_text_field( $attributes['successMessage'] ) : 'Thank you!';
$form_action = isset( $attributes['formAction'] ) ? sanitize_text_field( $attributes['formAction'] ) : 'email';

$block_classes = isset( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';
?>

<div class="wp-block-stitch-form <?php echo $block_classes; ?>" style="padding: 32px; background-color: #141414; border-radius: 12px; border: 1px solid #262626;">
	<form method="POST" class="wp-block-stitch-form__form" style="display: flex; flex-direction: column; gap: 16px;" onsubmit="return handleFormSubmit(event, this);">
		<?php foreach ( $fields as $field ) : ?>
			<?php
			$field_id = isset( $field['id'] ) ? sanitize_text_field( $field['id'] ) : '';
			$field_type = isset( $field['type'] ) ? sanitize_text_field( $field['type'] ) : 'text';
			$field_label = isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '';
			$field_placeholder = isset( $field['placeholder'] ) ? sanitize_text_field( $field['placeholder'] ) : '';
			$field_required = isset( $field['required'] ) ? (bool) $field['required'] : false;
			?>
			<div style="display: flex; flex-direction: column; gap: 4px;">
				<label for="<?php echo esc_attr( $field_id ); ?>" style="font-size: 0.875rem; font-weight: 600; color: #A3A3A3;">
					<?php echo esc_html( $field_label ); ?>
					<?php if ( $field_required ) : ?>
						<span style="color: #EF4444;">*</span>
					<?php endif; ?>
				</label>
				<?php if ( $field_type === 'textarea' ) : ?>
					<textarea
						id="<?php echo esc_attr( $field_id ); ?>"
						name="<?php echo esc_attr( $field_id ); ?>"
						placeholder="<?php echo esc_attr( $field_placeholder ); ?>"
						<?php echo $field_required ? 'required' : ''; ?>
						style="padding: 12px; border-radius: 6px; border: 1px solid #262626; background-color: #0A0A0A; color: #FFFFFF; font-size: 0.95rem; font-family: Inter, sans-serif; min-height: 100px; resize: vertical;"
					></textarea>
				<?php else : ?>
					<input
						type="<?php echo esc_attr( $field_type ); ?>"
						id="<?php echo esc_attr( $field_id ); ?>"
						name="<?php echo esc_attr( $field_id ); ?>"
						placeholder="<?php echo esc_attr( $field_placeholder ); ?>"
						<?php echo $field_required ? 'required' : ''; ?>
						style="padding: 12px; border-radius: 6px; border: 1px solid #262626; background-color: #0A0A0A; color: #FFFFFF; font-size: 0.95rem; font-family: Inter, sans-serif; height: 44px;"
					/>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

		<input type="hidden" name="action" value="<?php echo esc_attr( 'stitch_form_' . $form_action ); ?>" />
		<input type="hidden" name="success_message" value="<?php echo esc_attr( $success_message ); ?>" />
		<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'stitch_form_nonce' ); ?>" />

		<button
			type="submit"
			class="wp-block-stitch-form__button"
			style="padding: 12px 32px; border-radius: 6px; background-color: #195de6; color: #FFFFFF; font-size: 1rem; font-weight: 700; border: none; cursor: pointer; margin-top: 8px; transition: all 0.3s ease;"
		>
			<?php echo esc_html( $submit_button_text ); ?>
		</button>
	</form>
</div>

<script>
function handleFormSubmit(event, form) {
	event.preventDefault();

	const formData = new FormData(form);
	const data = Object.fromEntries(formData);

	// Send form data to server
	fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body: new URLSearchParams(data)
	})
	.then(response => response.json())
	.then(result => {
		if (result.success) {
			alert(data.success_message);
			form.reset();
		} else {
			alert('Error: ' + (result.message || 'Something went wrong'));
		}
	})
	.catch(error => {
		console.error('Error:', error);
		alert('Error submitting form');
	});

	return false;
}
</script>
