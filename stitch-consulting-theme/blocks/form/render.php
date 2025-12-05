<?php
/**
 * Form Block Render - Server-side rendering with full WCAG 2.1 AA accessibility
 *
 * Renders the form block on the frontend with comprehensive ARIA attributes
 * for screen reader support, semantic HTML5, and keyboard navigation.
 *
 * @var array  $attributes Block attributes
 * @var string $content    Block content
 * @var object $block      Block object
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Extract and sanitize attributes
$fields = isset( $attributes['fields'] ) ? $attributes['fields'] : array();
$form_label = isset( $attributes['formLabel'] ) ? sanitize_text_field( $attributes['formLabel'] ) : 'Contact Form';
$submit_button_text = isset( $attributes['submitButtonText'] ) ? sanitize_text_field( $attributes['submitButtonText'] ) : 'Send Message';
$success_message = isset( $attributes['successMessage'] ) ? sanitize_text_field( $attributes['successMessage'] ) : 'Thank you!';
$form_action = isset( $attributes['formAction'] ) ? sanitize_text_field( $attributes['formAction'] ) : 'email';
$block_classes = isset( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';

// Generate unique form ID for accessibility
$form_id = 'wp-block-stitch-form-' . md5( wp_json_encode( $attributes ) . time() );
$error_container_id = 'form-errors-' . substr( $form_id, -8 );
$success_container_id = 'form-success-' . substr( $form_id, -8 );
?>

<div class="wp-block-stitch-form <?php echo $block_classes; ?>" style="padding: 32px; background-color: #141414; border-radius: 12px; border: 1px solid #262626;">
	<!-- Form with complete ARIA attributes -->
	<form
		id="<?php echo esc_attr( $form_id ); ?>"
		method="POST"
		class="wp-block-stitch-form__form"
		role="form"
		aria-label="<?php echo esc_attr( $form_label ); ?>"
		aria-describedby="<?php echo esc_attr( $error_container_id ); ?>"
		style="display: flex; flex-direction: column; gap: 16px;"
		onsubmit="return handleFormSubmit(event, this);"
	>
		<!-- Error container with aria-live region for form-level errors -->
		<div
			id="<?php echo esc_attr( $error_container_id ); ?>"
			class="form-error-container"
			role="alert"
			aria-live="polite"
			aria-atomic="true"
			style="display: none; padding: 12px 16px; background-color: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; border-radius: 6px; color: #ef4444; margin-bottom: 16px;"
		></div>

		<!-- Success container for confirmation messages -->
		<div
			id="<?php echo esc_attr( $success_container_id ); ?>"
			class="form-success-container"
			role="status"
			aria-live="polite"
			aria-atomic="true"
			style="display: none; padding: 12px 16px; background-color: rgba(34, 197, 94, 0.1); border: 1px solid #22c55e; border-radius: 6px; color: #22c55e; margin-bottom: 16px;"
		></div>

		<!-- Fieldset with Legend for semantic grouping -->
		<fieldset style="border: none; padding: 0; margin: 0;">
			<legend style="font-size: 1.125rem; font-weight: 700; color: #FFFFFF; margin-bottom: 16px;">
				<?php echo esc_html( $form_label ); ?>
			</legend>

			<?php foreach ( $fields as $field ) : ?>
				<?php
				// Extract field properties with defaults
				$field_id = isset( $field['id'] ) ? sanitize_text_field( $field['id'] ) : '';
				$field_type = isset( $field['type'] ) ? sanitize_text_field( $field['type'] ) : 'text';
				$field_label = isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '';
				$field_placeholder = isset( $field['placeholder'] ) ? sanitize_text_field( $field['placeholder'] ) : '';
				$field_required = isset( $field['required'] ) ? (bool) $field['required'] : false;
				$field_error_message = isset( $field['errorMessage'] ) ? sanitize_text_field( $field['errorMessage'] ) : '';

				// Generate unique error message container ID
				$field_error_id = 'error-' . esc_attr( $field_id );
				?>
				<!-- Form field wrapper with proper spacing and accessibility -->
				<div style="display: flex; flex-direction: column; gap: 4px; margin-bottom: 12px;">
					<!-- Label with required indicator for screen readers -->
					<label
						for="<?php echo esc_attr( $field_id ); ?>"
						style="font-size: 0.875rem; font-weight: 600; color: #A3A3A3;"
					>
						<?php echo esc_html( $field_label ); ?>
						<?php if ( $field_required ) : ?>
							<!-- Required indicator with aria-label for clarity -->
							<span aria-label="required" style="color: #EF4444;">*</span>
						<?php endif; ?>
					</label>

					<?php if ( 'textarea' === $field_type ) : ?>
						<!-- Textarea with ARIA attributes for accessibility -->
						<textarea
							id="<?php echo esc_attr( $field_id ); ?>"
							name="<?php echo esc_attr( $field_id ); ?>"
							placeholder="<?php echo esc_attr( $field_placeholder ); ?>"
							aria-required="<?php echo $field_required ? 'true' : 'false'; ?>"
							aria-describedby="<?php echo esc_attr( $field_error_id ); ?>"
							<?php echo $field_required ? 'required' : ''; ?>
							style="padding: 12px; border-radius: 6px; border: 1px solid #262626; background-color: #0A0A0A; color: #FFFFFF; font-size: 0.95rem; font-family: Inter, sans-serif; min-height: 100px; resize: vertical; transition: border-color 0.2s ease;"
						></textarea>
					<?php else : ?>
						<!-- Input field with ARIA attributes for accessibility -->
						<input
							type="<?php echo esc_attr( $field_type ); ?>"
							id="<?php echo esc_attr( $field_id ); ?>"
							name="<?php echo esc_attr( $field_id ); ?>"
							placeholder="<?php echo esc_attr( $field_placeholder ); ?>"
							aria-required="<?php echo $field_required ? 'true' : 'false'; ?>"
							aria-describedby="<?php echo esc_attr( $field_error_id ); ?>"
							<?php echo $field_required ? 'required' : ''; ?>
							style="padding: 12px; border-radius: 6px; border: 1px solid #262626; background-color: #0A0A0A; color: #FFFFFF; font-size: 0.95rem; font-family: Inter, sans-serif; height: 44px; transition: border-color 0.2s ease;"
						/>
					<?php endif; ?>

					<!-- Field-specific error message container with ARIA attributes -->
					<div
						id="<?php echo esc_attr( $field_error_id ); ?>"
						role="alert"
						class="error-message"
						aria-live="polite"
						aria-atomic="true"
						style="color: #d32f2f; font-size: 0.875rem; margin-top: 0.25rem; min-height: 1.25rem; display: none;"
					></div>
				</div>
			<?php endforeach; ?>
		</fieldset>

		<!-- Hidden form fields for AJAX processing -->
		<input type="hidden" name="action" value="<?php echo esc_attr( 'stitch_form_' . $form_action ); ?>" />
		<input type="hidden" name="success_message" value="<?php echo esc_attr( $success_message ); ?>" />
		<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'stitch_form_nonce' ); ?>" />

		<!-- Submit button with accessibility attributes -->
		<button
			type="submit"
			class="wp-block-stitch-form__button"
			aria-label="<?php echo esc_attr( $submit_button_text ); ?>"
			aria-disabled="false"
			style="padding: 12px 32px; border-radius: 6px; background-color: #195de6; color: #FFFFFF; font-size: 1rem; font-weight: 700; border: none; cursor: pointer; margin-top: 8px; transition: all 0.3s ease;"
		>
			<?php echo esc_html( $submit_button_text ); ?>
		</button>
	</form>
</div>

<!-- Enhanced form submission handler with accessibility support -->
<script>
( function() {
	'use strict';

	/**
	 * Display error messages in both field-level and form-level containers
	 *
	 * @param {Object} errors Object with field IDs as keys and error messages as values
	 * @param {string} formId The ID of the form element
	 */
	function displayFieldErrors( errors, formId ) {
		const form = document.getElementById( formId );
		if ( ! form ) return;

		// Clear previous errors first
		form.querySelectorAll( '[role="alert"][class="error-message"]' ).forEach( function( el ) {
			el.textContent = '';
			el.style.display = 'none';
		} );

		// Add field-level errors
		Object.keys( errors ).forEach( function( fieldId ) {
			const errorElement = document.getElementById( 'error-' + fieldId );
			if ( errorElement ) {
				errorElement.textContent = errors[ fieldId ];
				errorElement.style.display = 'block';

				// Also update input field border for visual feedback
				const inputElement = document.getElementById( fieldId );
				if ( inputElement ) {
					inputElement.style.borderColor = '#ef4444';
					inputElement.setAttribute( 'aria-invalid', 'true' );
				}
			}
		} );

		// Update form-level error container
		const errorContainerId = 'form-errors-' + formId.split( '-' ).pop();
		const errorContainer = document.getElementById( errorContainerId );
		if ( errorContainer ) {
			errorContainer.textContent = 'Please correct the errors above and try again.';
			errorContainer.style.display = 'block';
		}
	}

	/**
	 * Clear error states from all form fields
	 *
	 * @param {HTMLFormElement} form The form element
	 */
	function clearFieldErrors( form ) {
		form.querySelectorAll( 'input, textarea' ).forEach( function( field ) {
			field.style.borderColor = '#262626';
			field.setAttribute( 'aria-invalid', 'false' );
		} );

		form.querySelectorAll( '[role="alert"][class="error-message"]' ).forEach( function( el ) {
			el.textContent = '';
			el.style.display = 'none';
		} );

		// Clear form-level error container
		const errorContainer = form.parentElement.querySelector( '[role="alert"][class="form-error-container"]' );
		if ( errorContainer ) {
			errorContainer.textContent = '';
			errorContainer.style.display = 'none';
		}
	}

	/**
	 * Display success message in form-level success container
	 *
	 * @param {string} message Success message to display
	 * @param {HTMLFormElement} form The form element
	 */
	function displaySuccessMessage( message, form ) {
		const successContainer = form.parentElement.querySelector( '[role="status"][class="form-success-container"]' );
		if ( successContainer ) {
			successContainer.textContent = message;
			successContainer.style.display = 'block';

			// Focus on success container for screen readers
			successContainer.focus();

			// Auto-hide after 5 seconds
			setTimeout( function() {
				successContainer.style.display = 'none';
			}, 5000 );
		}
	}

	/**
	 * Handle form submission via AJAX
	 *
	 * @param {Event} event The form submit event
	 * @param {HTMLFormElement} form The form element
	 * @return {boolean} Always returns false to prevent default behavior
	 */
	function handleFormSubmit( event, form ) {
		event.preventDefault();

		// Clear previous error states
		clearFieldErrors( form );

		// Get form ID for error container reference
		const formId = form.id;

		// Collect form data
		const formData = new FormData( form );
		const data = Object.fromEntries( formData );

		// Get submit button and disable it
		const submitButton = form.querySelector( 'button[type="submit"]' );
		if ( submitButton ) {
			submitButton.disabled = true;
			submitButton.setAttribute( 'aria-disabled', 'true' );
			submitButton.textContent = 'Submitting...';
		}

		// Send form data to server
		fetch( '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			body: new URLSearchParams( data )
		} )
		.then( function( response ) {
			return response.json();
		} )
		.then( function( result ) {
			if ( result.success ) {
				// Success: display message and reset form
				displaySuccessMessage( data.success_message, form );
				form.reset();

				// Focus management - return focus to form label
				const legend = form.querySelector( 'legend' );
				if ( legend ) {
					legend.focus();
				}
			} else {
				// Error: display error messages
				const errors = result.data && result.data.errors ? result.data.errors : {};
				displayFieldErrors( errors, formId );

				// Display generic error message if no field errors
				if ( Object.keys( errors ).length === 0 ) {
					const errorContainer = form.parentElement.querySelector( '[role="alert"][class="form-error-container"]' );
					if ( errorContainer ) {
						errorContainer.textContent = result.data && result.data.message ? result.data.message : 'An error occurred. Please try again.';
						errorContainer.style.display = 'block';
					}
				}
			}
		} )
		.catch( function( error ) {
			console.error( 'Form submission error:', error );

			// Display generic error
			const errorContainer = form.parentElement.querySelector( '[role="alert"][class="form-error-container"]' );
			if ( errorContainer ) {
				errorContainer.textContent = 'An error occurred while submitting the form. Please try again.';
				errorContainer.style.display = 'block';
			}
		} )
		.finally( function() {
			// Re-enable submit button
			if ( submitButton ) {
				submitButton.disabled = false;
				submitButton.setAttribute( 'aria-disabled', 'false' );
				submitButton.textContent = '<?php echo esc_js( $submit_button_text ); ?>';
			}
		} );

		return false;
	}

	// Make handleFormSubmit available globally
	window.handleFormSubmit = handleFormSubmit;

	// Initialize when form is ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function() {
			// Focus management for better accessibility
			const form = document.getElementById( '<?php echo esc_js( $form_id ); ?>' );
			if ( form ) {
				// Add focus styling for better visibility
				const inputs = form.querySelectorAll( 'input, textarea' );
				inputs.forEach( function( input ) {
					input.addEventListener( 'focus', function() {
						this.style.borderColor = '#195de6';
						this.style.boxShadow = '0 0 0 3px rgba(25, 93, 230, 0.1)';
					} );
					input.addEventListener( 'blur', function() {
						if ( this.getAttribute( 'aria-invalid' ) !== 'true' ) {
							this.style.borderColor = '#262626';
							this.style.boxShadow = 'none';
						}
					} );
				} );
			}
		} );
	}
} )();
</script>
