<?php
/**
 * Form Handler for Stitch Blocks
 *
 * Processes form submissions with complete validation, sanitization,
 * and rate limiting for production security.
 *
 * Features:
 * - Input validation and sanitization
 * - Rate limiting (5 submissions per email per hour)
 * - Security logging for violations
 * - Multiple form action handlers (email, HubSpot, webhook)
 * - CSRF token verification
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates form submission data
 *
 * Performs comprehensive validation on all required fields with proper
 * sanitization and WordPress best practices.
 *
 * @param array $data Form submission data
 * @return array Array of validation errors (empty array if valid)
 */
if ( ! function_exists( 'stitch_validate_form_submission' ) ) {
	function stitch_validate_form_submission( $data ) {
		$errors = array();

		// Sanitize inputs
		$email = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
		$name = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
		$message = isset( $data['message'] ) ? sanitize_textarea_field( $data['message'] ) : '';

		// Validate email field - REQUIRED
		if ( empty( $email ) ) {
			$errors['email'] = 'Email is required';
		} elseif ( ! is_email( $email ) ) {
			$errors['email'] = 'Please enter a valid email address';
		}

		// Validate name field - REQUIRED
		if ( empty( $name ) ) {
			$errors['name'] = 'Name is required';
		} elseif ( strlen( $name ) < 2 ) {
			$errors['name'] = 'Name must be at least 2 characters';
		}

		// Validate message field - REQUIRED with minimum length
		if ( empty( $message ) ) {
			$errors['message'] = 'Message is required';
		} elseif ( strlen( $message ) < 10 ) {
			$errors['message'] = 'Message must be at least 10 characters';
		}

		/**
		 * Allows third-party code to add additional validation rules
		 *
		 * @param array $errors Current validation errors
		 * @param array $data Sanitized form data
		 * @return array Updated validation errors
		 */
		$errors = apply_filters( 'stitch_form_validation_errors', $errors, $data );

		return $errors;
	}
}

/**
 * Checks and enforces rate limiting per email address
 *
 * Limits submissions to 5 per email address per hour using
 * WordPress options for persistence across requests.
 *
 * Security Features:
 * - Hashed email keys for privacy
 * - Automatic cleanup of old submissions
 * - Security logging on violations
 *
 * @param string $email Email address to check
 * @return array Array with 'limited' (bool) and optional 'message' (string)
 */
if ( ! function_exists( 'stitch_check_rate_limit' ) ) {
	function stitch_check_rate_limit( $email ) {
		// Sanitize email for consistent key generation
		$clean_email = sanitize_email( $email );

		// Use hashed email as option key for privacy
		$option_key = 'stitch_form_submissions_' . md5( $clean_email );

		// Get submission timestamps
		$submissions = get_option( $option_key, array() );

		// Ensure it's an array
		if ( ! is_array( $submissions ) ) {
			$submissions = array();
		}

		// Current timestamp
		$now = time();
		$hour_ago = $now - HOUR_IN_SECONDS;

		// Remove submissions older than 1 hour
		$submissions = array_filter(
			$submissions,
			function( $submission_time ) use ( $hour_ago ) {
				return is_numeric( $submission_time ) && $submission_time > $hour_ago;
			}
		);

		// Reindex array after filtering
		$submissions = array_values( $submissions );

		// Check if limit exceeded (5 submissions per hour)
		if ( count( $submissions ) >= 5 ) {
			// Log rate limit violation for security monitoring
			stitch_log_rate_limit_violation( $clean_email );

			return array(
				'limited' => true,
				'message' => 'Too many submissions. Please try again later.'
			);
		}

		// Record this new submission
		$submissions[] = $now;
		update_option( $option_key, $submissions, 'no' );

		return array( 'limited' => false );
	}
}

/**
 * Logs rate limit violations for security monitoring
 *
 * Records rate limit violations to PHP error log including:
 * - Timestamp
 * - Email address (sanitized)
 * - IP address
 *
 * @param string $email Email that triggered rate limit
 * @return void
 */
if ( ! function_exists( 'stitch_log_rate_limit_violation' ) ) {
	function stitch_log_rate_limit_violation( $email ) {
		$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';

		$log_entry = sprintf(
			'[%s] Stitch Form - Rate limit violation for email: %s (IP: %s)',
			wp_date( 'Y-m-d H:i:s' ),
			sanitize_email( $email ),
			$ip_address
		);

		// Log to WordPress error log
		error_log( $log_entry );

		/**
		 * Allows third-party code to handle rate limit violations
		 *
		 * @param string $email Email that triggered limit
		 * @param string $ip_address IP address of request
		 */
		do_action( 'stitch_form_rate_limit_violated', $email, $ip_address );
	}
}

/**
 * Handles form submission via AJAX
 *
 * Complete form submission handler that:
 * 1. Verifies CSRF nonce token
 * 2. Validates all form data
 * 3. Checks rate limiting
 * 4. Routes to appropriate handler (email, HubSpot, webhook)
 *
 * @return void Sends JSON response via wp_send_json_*
 */
if ( ! function_exists( 'stitch_handle_form_submission' ) ) {
	function stitch_handle_form_submission() {
		// Verify nonce for CSRF protection
		if ( ! isset( $_POST['_nonce'] ) ) {
			wp_send_json_error(
				array( 'message' => 'Security check failed: missing nonce' ),
				400
			);
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'stitch_form_nonce' ) ) {
			wp_send_json_error(
				array( 'message' => 'Security check failed: invalid nonce' ),
				403
			);
		}

		// Extract and sanitize action
		$action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';

		if ( empty( $action ) ) {
			wp_send_json_error(
				array( 'message' => 'Invalid form action' ),
				400
			);
		}

		// Get success message
		$success_message = isset( $_POST['success_message'] )
			? sanitize_text_field( wp_unslash( $_POST['success_message'] ) )
			: 'Thank you! We\'ll get back to you soon.';

		// Extract email for rate limiting check
		$email = isset( $_POST['email'] )
			? sanitize_email( wp_unslash( $_POST['email'] ) )
			: '';

		// Prepare form data by copying and cleaning POST data
		$form_data = array();
		foreach ( $_POST as $key => $value ) {
			// Skip system fields
			if ( in_array( $key, array( 'action', '_nonce', 'success_message' ), true ) ) {
				continue;
			}

			// Sanitize based on field type
			if ( 'email' === $key ) {
				$form_data[ $key ] = sanitize_email( wp_unslash( $value ) );
			} elseif ( 'message' === $key || is_array( $value ) ) {
				$form_data[ $key ] = sanitize_textarea_field( wp_unslash( $value ) );
			} else {
				$form_data[ $key ] = sanitize_text_field( wp_unslash( $value ) );
			}
		}

		// STEP 1: Validate form data
		$validation_errors = stitch_validate_form_submission( $form_data );
		if ( ! empty( $validation_errors ) ) {
			wp_send_json_error(
				array(
					'message' => 'Please correct the following errors',
					'errors' => $validation_errors
				),
				422
			);
		}

		// STEP 2: Check rate limiting
		if ( ! empty( $email ) ) {
			$rate_limit_check = stitch_check_rate_limit( $email );
			if ( $rate_limit_check['limited'] ) {
				wp_send_json_error(
					array( 'message' => $rate_limit_check['message'] ),
					429
				);
			}
		}

		// STEP 3: Process form based on action
		try {
			if ( false !== strpos( $action, 'stitch_form_email' ) ) {
				stitch_send_form_email( $form_data );
			} elseif ( false !== strpos( $action, 'stitch_form_hubspot' ) ) {
				stitch_send_form_hubspot( $form_data );
			} elseif ( false !== strpos( $action, 'stitch_form_webhook' ) ) {
				stitch_send_form_webhook( $form_data );
			} else {
				throw new Exception( 'Invalid form action: ' . esc_html( $action ) );
			}

			/**
			 * Fires after successful form submission
			 *
			 * @param array $form_data Submitted form data
			 * @param string $action Form action type
			 */
			do_action( 'stitch_form_submission_success', $form_data, $action );

			wp_send_json_success(
				array( 'message' => $success_message ),
				200
			);
		} catch ( Exception $e ) {
			// Log error for debugging
			error_log( 'Stitch Form Error: ' . $e->getMessage() );

			/**
			 * Fires when form submission fails
			 *
			 * @param Exception $exception The exception that was thrown
			 * @param array $form_data Submitted form data
			 */
			do_action( 'stitch_form_submission_failed', $e, $form_data );

			wp_send_json_error(
				array( 'message' => 'An error occurred. Please try again later.' ),
				500
			);
		}
	}

	// Register AJAX handlers for both authenticated and unauthenticated users
	add_action( 'wp_ajax_nopriv_stitch_form_email', 'stitch_handle_form_submission' );
	add_action( 'wp_ajax_stitch_form_email', 'stitch_handle_form_submission' );
	add_action( 'wp_ajax_nopriv_stitch_form_hubspot', 'stitch_handle_form_submission' );
	add_action( 'wp_ajax_stitch_form_hubspot', 'stitch_handle_form_submission' );
	add_action( 'wp_ajax_nopriv_stitch_form_webhook', 'stitch_handle_form_submission' );
	add_action( 'wp_ajax_stitch_form_webhook', 'stitch_handle_form_submission' );
}

/**
 * Sends form submission via email
 *
 * Emails form data to the site admin with proper formatting.
 *
 * @param array $form_data Sanitized form data
 * @return void
 * @throws Exception If email fails to send
 */
if ( ! function_exists( 'stitch_send_form_email' ) ) {
	function stitch_send_form_email( $form_data ) {
		$to = get_option( 'admin_email' );
		$subject = sprintf(
			'New Form Submission from %s',
			get_bloginfo( 'name' )
		);

		// Build email message
		$message = "New form submission:\n\n";
		foreach ( $form_data as $key => $value ) {
			$label = ucfirst( str_replace( array( '_', '-' ), ' ', $key ) );
			$message .= $label . ": " . sanitize_text_field( $value ) . "\n";
		}

		$message .= "\n--- Submission Details ---\n";
		$message .= 'Time: ' . wp_date( 'Y-m-d H:i:s' ) . "\n";
		$message .= 'IP Address: ' . ( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown' ) . "\n";

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

		// Send email
		$email_sent = wp_mail( $to, $subject, $message, $headers );

		if ( ! $email_sent ) {
			throw new Exception( 'Failed to send email' );
		}
	}
}

/**
 * Sends form submission to HubSpot
 *
 * Creates or updates HubSpot contact with form submission data.
 *
 * @param array $form_data Sanitized form data
 * @return void
 * @throws Exception If HubSpot API fails
 */
if ( ! function_exists( 'stitch_send_form_hubspot' ) ) {
	function stitch_send_form_hubspot( $form_data ) {
		$hubspot_api_key = get_option( 'stitch_hubspot_api_key' );
		if ( empty( $hubspot_api_key ) ) {
			throw new Exception( 'HubSpot API key not configured' );
		}

		$email = isset( $form_data['email'] ) ? sanitize_email( $form_data['email'] ) : '';
		if ( empty( $email ) ) {
			throw new Exception( 'Email is required for HubSpot submission' );
		}

		// Prepare HubSpot contact data
		$contact_data = array(
			'properties' => array()
		);

		foreach ( $form_data as $key => $value ) {
			$contact_data['properties'][] = array(
				'property' => sanitize_text_field( $key ),
				'value' => sanitize_text_field( $value )
			);
		}

		$response = wp_remote_post(
			'https://api.hubapi.com/crm/v3/objects/contacts',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . sanitize_text_field( $hubspot_api_key ),
					'Content-Type' => 'application/json'
				),
				'body' => wp_json_encode( $contact_data ),
				'timeout' => 30
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( 'HubSpot API Error: ' . $response->get_error_message() );
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code < 200 || $status_code >= 300 ) {
			$body = wp_remote_retrieve_body( $response );
			throw new Exception( 'HubSpot API returned status ' . $status_code );
		}
	}
}

/**
 * Sends form submission to webhook URL
 *
 * Posts form data to configured webhook URL for custom integrations.
 *
 * @param array $form_data Sanitized form data
 * @return void
 * @throws Exception If webhook fails
 */
if ( ! function_exists( 'stitch_send_form_webhook' ) ) {
	function stitch_send_form_webhook( $form_data ) {
		$webhook_url = get_option( 'stitch_form_webhook_url' );
		if ( empty( $webhook_url ) ) {
			throw new Exception( 'Webhook URL not configured' );
		}

		$response = wp_remote_post(
			esc_url_raw( $webhook_url ),
			array(
				'headers' => array(
					'Content-Type' => 'application/json'
				),
				'body' => wp_json_encode( $form_data ),
				'timeout' => 30
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( 'Webhook Error: ' . $response->get_error_message() );
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code < 200 || $status_code >= 300 ) {
			throw new Exception( 'Webhook returned status ' . $status_code );
		}
	}
}
