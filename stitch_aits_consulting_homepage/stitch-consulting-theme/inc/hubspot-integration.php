<?php
/**
 * HubSpot Integration
 *
 * Provides HubSpot API client, form submission handling, and webhook processing
 *
 * @package Stitch_Consulting_Theme
 * @subpackage HubSpot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * HubSpot API Client Class
 */
class Stitch_HubSpot_API {

	/**
	 * HubSpot API base URL
	 */
	const API_BASE = 'https://api.hubapi.com';

	/**
	 * HubSpot Portal ID
	 */
	private $portal_id;

	/**
	 * HubSpot API Key
	 */
	private $api_key;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->api_key = defined( 'HUBSPOT_API_KEY' ) ? HUBSPOT_API_KEY : '';
		$this->portal_id = defined( 'HUBSPOT_PORTAL_ID' ) ? HUBSPOT_PORTAL_ID : '';
	}

	/**
	 * Check if HubSpot is configured
	 *
	 * @return bool
	 */
	public function is_configured() {
		return ! empty( $this->api_key ) && ! empty( $this->portal_id );
	}

	/**
	 * Get portal ID
	 *
	 * @return string
	 */
	public function get_portal_id() {
		return $this->portal_id;
	}

	/**
	 * Submit a contact to HubSpot
	 *
	 * @param array $data Contact data with email, firstname, lastname, phone, etc.
	 * @return array|WP_Error API response or error
	 */
	public function submit_contact( $data ) {
		if ( ! $this->is_configured() ) {
			return new WP_Error(
				'hubspot_not_configured',
				__( 'HubSpot is not properly configured.', 'stitch-consulting' )
			);
		}

		$endpoint = self::API_BASE . '/crm/v3/objects/contacts';

		// Prepare contact properties
		$properties = $this->prepare_contact_properties( $data );

		$body = array(
			'properties' => $properties,
		);

		$response = wp_remote_post(
			$endpoint,
			array(
				'headers'   => array(
					'Authorization' => 'Bearer ' . $this->api_key,
					'Content-Type'  => 'application/json',
				),
				'body'      => wp_json_encode( $body ),
				'timeout'   => 30,
				'sslverify' => true,
			)
		);

		// Log the request
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log(
				'[HubSpot] Contact submission: ' . wp_json_encode(
					array(
						'endpoint' => $endpoint,
						'properties' => $properties,
						'response_code' => wp_remote_retrieve_response_code( $response ),
					)
				)
			);
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $status_code >= 400 ) {
			return new WP_Error(
				'hubspot_api_error',
				isset( $body['message'] ) ? $body['message'] : __( 'HubSpot API error', 'stitch-consulting' ),
				array( 'status_code' => $status_code, 'body' => $body )
			);
		}

		return $body;
	}

	/**
	 * Create or update a contact
	 *
	 * @param string $email Contact email.
	 * @param array  $data Contact data.
	 * @return array|WP_Error API response or error
	 */
	public function upsert_contact( $email, $data ) {
		if ( ! $this->is_configured() ) {
			return new WP_Error(
				'hubspot_not_configured',
				__( 'HubSpot is not properly configured.', 'stitch-consulting' )
			);
		}

		$endpoint = self::API_BASE . '/crm/v3/objects/contacts/batch/upsert';

		// Prepare contact properties
		$properties = $this->prepare_contact_properties( $data );

		$body = array(
			'inputs' => array(
				array(
					'idProperty' => 'email',
					'id'         => $email,
					'properties' => $properties,
				),
			),
		);

		$response = wp_remote_post(
			$endpoint,
			array(
				'headers'   => array(
					'Authorization' => 'Bearer ' . $this->api_key,
					'Content-Type'  => 'application/json',
				),
				'body'      => wp_json_encode( $body ),
				'timeout'   => 30,
				'sslverify' => true,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $status_code >= 400 ) {
			return new WP_Error(
				'hubspot_api_error',
				isset( $response_body['message'] ) ? $response_body['message'] : __( 'HubSpot API error', 'stitch-consulting' ),
				array( 'status_code' => $status_code, 'body' => $response_body )
			);
		}

		return $response_body;
	}

	/**
	 * Prepare contact properties for HubSpot API
	 *
	 * @param array $data Raw form data.
	 * @return array Formatted properties array
	 */
	private function prepare_contact_properties( $data ) {
		$properties = array();

		// Map common form fields to HubSpot properties
		$field_mapping = array(
			'email'              => 'email',
			'firstname'          => 'firstname',
			'firstname_or_name'  => 'firstname',
			'first_name'         => 'firstname',
			'lastname'           => 'lastname',
			'lastname_or_name'   => 'lastname',
			'last_name'          => 'lastname',
			'phone'              => 'phone',
			'company'            => 'company',
			'message'            => 'message',
			'website'            => 'website',
		);

		foreach ( $data as $key => $value ) {
			$hs_key = isset( $field_mapping[ $key ] ) ? $field_mapping[ $key ] : $key;
			if ( ! empty( $value ) ) {
				$properties[] = array(
					'name'  => $hs_key,
					'value' => sanitize_text_field( $value ),
				);
			}
		}

		return $properties;
	}

	/**
	 * Verify HubSpot webhook signature
	 *
	 * @param string $body Request body.
	 * @param string $signature Request signature header.
	 * @param string $signature_version Signature version.
	 * @param string $request_timestamp Request timestamp.
	 * @return bool
	 */
	public function verify_webhook_signature( $body, $signature, $signature_version = 'v1', $request_timestamp = '' ) {
		if ( ! $this->is_configured() || empty( $signature ) ) {
			return false;
		}

		// Get webhook signing key from wp-config constant
		$webhook_key = defined( 'HUBSPOT_WEBHOOK_KEY' ) ? HUBSPOT_WEBHOOK_KEY : '';
		if ( empty( $webhook_key ) ) {
			return false;
		}

		if ( 'v1' === $signature_version ) {
			// v1 signature: sourceString = method + url + body + timestamp + webhook signing secret
			// For webhook, we typically use: body + webhook_key
			$source_string = $body . $webhook_key;
			$expected_signature = hash_hmac( 'sha256', $source_string, $webhook_key );
			return hash_equals( $signature, $expected_signature );
		}

		return false;
	}

	/**
	 * Get contact by email
	 *
	 * @param string $email Contact email.
	 * @return array|WP_Error API response or error
	 */
	public function get_contact_by_email( $email ) {
		if ( ! $this->is_configured() ) {
			return new WP_Error(
				'hubspot_not_configured',
				__( 'HubSpot is not properly configured.', 'stitch-consulting' )
			);
		}

		$endpoint = self::API_BASE . '/crm/v3/objects/contacts/' . urlencode( $email ) . '?idProperty=email';

		$response = wp_remote_get(
			$endpoint,
			array(
				'headers'   => array(
					'Authorization' => 'Bearer ' . $this->api_key,
				),
				'timeout'   => 30,
				'sslverify' => true,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $status_code >= 400 ) {
			return new WP_Error(
				'hubspot_api_error',
				isset( $body['message'] ) ? $body['message'] : __( 'HubSpot API error', 'stitch-consulting' ),
				array( 'status_code' => $status_code, 'body' => $body )
			);
		}

		return $body;
	}
}

/**
 * Initialize HubSpot API client
 *
 * @return Stitch_HubSpot_API
 */
function stitch_get_hubspot_client() {
	static $client = null;

	if ( null === $client ) {
		$client = new Stitch_HubSpot_API();
	}

	return $client;
}

/**
 * Register REST API endpoint for form submissions
 */
add_action( 'rest_api_init', function() {
	register_rest_route(
		'stitch/v1',
		'/form-submit',
		array(
			'methods'             => 'POST',
			'callback'            => 'stitch_handle_form_submission',
			'permission_callback' => '__return_true',
			'args'                => array(
				'email'    => array(
					'type'     => 'string',
					'required' => true,
					'sanitize_callback' => 'sanitize_email',
				),
				'firstname' => array(
					'type'     => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'lastname' => array(
					'type'     => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'phone'   => array(
					'type'     => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'company' => array(
					'type'     => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'message' => array(
					'type'     => 'string',
					'sanitize_callback' => 'sanitize_textarea_field',
				),
				'form_id' => array(
					'type'     => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
} );

/**
 * Handle form submission
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response|WP_Error
 */
function stitch_handle_form_submission( $request ) {
	// Verify nonce if available
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( ! empty( $nonce ) && ! wp_verify_nonce( $nonce, 'stitch_form_nonce' ) ) {
		return new WP_Error(
			'invalid_nonce',
			__( 'Security check failed', 'stitch-consulting' ),
			array( 'status' => 403 )
		);
	}

	$params = $request->get_json_params();

	// Validate email
	if ( empty( $params['email'] ) || ! is_email( $params['email'] ) ) {
		return new WP_Error(
			'invalid_email',
			__( 'Please provide a valid email address', 'stitch-consulting' ),
			array( 'status' => 400 )
		);
	}

	// Rate limiting check
	$rate_limit_key = 'stitch_form_' . strtolower( $params['email'] );
	$attempt_count = get_transient( $rate_limit_key );
	if ( false !== $attempt_count && $attempt_count >= 5 ) {
		return new WP_Error(
			'rate_limit_exceeded',
			__( 'Too many submission attempts. Please try again later.', 'stitch-consulting' ),
			array( 'status' => 429 )
		);
	}

	// Get HubSpot client
	$hubspot = stitch_get_hubspot_client();

	if ( ! $hubspot->is_configured() ) {
		return new WP_Error(
			'hubspot_not_configured',
			__( 'Form submission is not available at this time.', 'stitch-consulting' ),
			array( 'status' => 503 )
		);
	}

	// Prepare data for submission
	$data = array(
		'email'      => $params['email'],
		'firstname'  => isset( $params['firstname'] ) ? $params['firstname'] : '',
		'lastname'   => isset( $params['lastname'] ) ? $params['lastname'] : '',
		'phone'      => isset( $params['phone'] ) ? $params['phone'] : '',
		'company'    => isset( $params['company'] ) ? $params['company'] : '',
		'message'    => isset( $params['message'] ) ? $params['message'] : '',
	);

	// Submit to HubSpot
	$result = $hubspot->upsert_contact( $params['email'], $data );

	if ( is_wp_error( $result ) ) {
		error_log( 'HubSpot form submission error: ' . $result->get_error_message() );
		return $result;
	}

	// Update rate limit
	set_transient( $rate_limit_key, ( $attempt_count ? $attempt_count + 1 : 1 ), HOUR_IN_SECONDS );

	// Log submission
	if ( function_exists( 'stitch_log_form_submission' ) ) {
		stitch_log_form_submission( $params['email'], $data, 'hubspot', $result );
	}

	return rest_ensure_response(
		array(
			'success' => true,
			'message' => __( 'Thank you for your submission. We will be in touch soon!', 'stitch-consulting' ),
		)
	);
}

/**
 * Register webhook endpoint for HubSpot callbacks
 */
add_action( 'rest_api_init', function() {
	register_rest_route(
		'stitch/v1',
		'/hubspot-webhook',
		array(
			'methods'             => 'POST',
			'callback'            => 'stitch_handle_hubspot_webhook',
			'permission_callback' => '__return_true',
		)
	);
} );

/**
 * Handle HubSpot webhook
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response|WP_Error
 */
function stitch_handle_hubspot_webhook( $request ) {
	$body = $request->get_body();
	$signature = $request->get_header( 'x-hubspot-signature' );

	$hubspot = stitch_get_hubspot_client();

	// Verify webhook signature
	if ( ! $hubspot->verify_webhook_signature( $body, $signature ) ) {
		error_log( 'HubSpot webhook signature verification failed' );
		return new WP_Error(
			'invalid_signature',
			__( 'Invalid webhook signature', 'stitch-consulting' ),
			array( 'status' => 403 )
		);
	}

	$data = json_decode( $body, true );

	// Log webhook event
	error_log( 'HubSpot webhook received: ' . wp_json_encode( $data ) );

	if ( function_exists( 'stitch_process_hubspot_event' ) ) {
		do_action( 'stitch_hubspot_webhook', $data );
	}

	return rest_ensure_response(
		array(
			'success' => true,
		)
	);
}

/**
 * Enqueue HubSpot tracking script
 */
add_action( 'wp_footer', function() {
	$hubspot = stitch_get_hubspot_client();

	if ( ! $hubspot->is_configured() ) {
		return;
	}

	$portal_id = $hubspot->get_portal_id();
	?>
	<!-- HubSpot Analytics -->
	<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/<?php echo esc_attr( $portal_id ); ?>.js"></script>
	<?php
} );
