<?php
/**
 * Form Handler for Stitch Blocks
 * Processes form submissions from the Form block
 */

if ( ! function_exists( 'stitch_handle_form_submission' ) ) {
	function stitch_handle_form_submission() {
		// Verify nonce
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'stitch_form_nonce' ) ) {
			wp_send_json_error( [ 'message' => 'Security check failed' ] );
		}

		$action = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : '';
		$form_data = $_POST;
		unset( $form_data['action'] );
		unset( $form_data['_nonce'] );
		unset( $form_data['success_message'] );

		$success_message = isset( $_POST['success_message'] ) ? sanitize_text_field( $_POST['success_message'] ) : 'Thank you!';

		try {
			if ( strpos( $action, 'stitch_form_email' ) !== false ) {
				stitch_send_form_email( $form_data );
			} elseif ( strpos( $action, 'stitch_form_hubspot' ) !== false ) {
				stitch_send_form_hubspot( $form_data );
			} elseif ( strpos( $action, 'stitch_form_webhook' ) !== false ) {
				stitch_send_form_webhook( $form_data );
			}

			wp_send_json_success( [ 'message' => $success_message ] );
		} catch ( Exception $e ) {
			wp_send_json_error( [ 'message' => $e->getMessage() ] );
		}
	}

	add_action( 'wp_ajax_nopriv_stitch_form_email', 'stitch_handle_form_submission' );
	add_action( 'wp_ajax_stitch_form_email', 'stitch_handle_form_submission' );
	add_action( 'wp_ajax_nopriv_stitch_form_hubspot', 'stitch_handle_form_submission' );
	add_action( 'wp_ajax_stitch_form_hubspot', 'stitch_handle_form_submission' );
	add_action( 'wp_ajax_nopriv_stitch_form_webhook', 'stitch_handle_form_submission' );
	add_action( 'wp_ajax_stitch_form_webhook', 'stitch_handle_form_submission' );
}

if ( ! function_exists( 'stitch_send_form_email' ) ) {
	function stitch_send_form_email( $form_data ) {
		$to = get_option( 'admin_email' );
		$subject = 'New Form Submission from ' . get_bloginfo( 'name' );
		$message = "New form submission:\n\n";

		foreach ( $form_data as $key => $value ) {
			$message .= ucfirst( $key ) . ": " . sanitize_text_field( $value ) . "\n";
		}

		$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

		wp_mail( $to, $subject, $message, $headers );
	}
}

if ( ! function_exists( 'stitch_send_form_hubspot' ) ) {
	function stitch_send_form_hubspot( $form_data ) {
		$hubspot_api_key = get_option( 'stitch_hubspot_api_key' );
		if ( empty( $hubspot_api_key ) ) {
			throw new Exception( 'HubSpot API key not configured' );
		}

		$email = isset( $form_data['email'] ) ? sanitize_email( $form_data['email'] ) : '';
		if ( empty( $email ) ) {
			throw new Exception( 'Email is required' );
		}

		// Prepare HubSpot contact data
		$contact_data = [
			'properties' => []
		];

		foreach ( $form_data as $key => $value ) {
			$contact_data['properties'][] = [
				'property' => $key,
				'value' => sanitize_text_field( $value )
			];
		}

		$response = wp_remote_post( 'https://api.hubapi.com/crm/v3/objects/contacts', [
			'headers' => [
				'Authorization' => 'Bearer ' . $hubspot_api_key,
				'Content-Type' => 'application/json'
			],
			'body' => wp_json_encode( $contact_data )
		] );

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}
	}
}

if ( ! function_exists( 'stitch_send_form_webhook' ) ) {
	function stitch_send_form_webhook( $form_data ) {
		$webhook_url = get_option( 'stitch_form_webhook_url' );
		if ( empty( $webhook_url ) ) {
			throw new Exception( 'Webhook URL not configured' );
		}

		$response = wp_remote_post( $webhook_url, [
			'headers' => [
				'Content-Type' => 'application/json'
			],
			'body' => wp_json_encode( $form_data )
		] );

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}
	}
}
