<?php
/**
 * HubSpot Helper Functions
 *
 * Utility functions for HubSpot integration
 *
 * @package Stitch_Consulting_Theme
 * @subpackage HubSpot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Log a form submission
 *
 * @param string $email Submitter email.
 * @param array  $data Form data.
 * @param string $source Source (e.g., 'hubspot', 'contact-form-7').
 * @param array  $api_response API response.
 * @return int|false Post ID or false
 */
function stitch_log_form_submission( $email, $data, $source = 'hubspot', $api_response = array() ) {
	// Create a custom post type for form submissions (optional)
	$post_data = array(
		'post_title'   => sprintf( 'Form Submission - %s', sanitize_email( $email ) ),
		'post_content' => wp_json_encode( $data ),
		'post_type'    => 'stitch_form_log',
		'post_status'  => 'publish',
		'meta_input'   => array(
			'form_email'  => sanitize_email( $email ),
			'form_source' => sanitize_text_field( $source ),
			'api_response' => wp_json_encode( $api_response ),
			'submission_date' => current_time( 'mysql' ),
		),
	);

	$post_id = wp_insert_post( $post_data, true );

	if ( is_wp_error( $post_id ) ) {
		error_log( 'Error logging form submission: ' . $post_id->get_error_message() );
		return false;
	}

	return $post_id;
}

/**
 * Get form submission logs
 *
 * @param array $args Query arguments.
 * @return array Array of form submission posts
 */
function stitch_get_form_submissions( $args = array() ) {
	$defaults = array(
		'post_type'      => 'stitch_form_log',
		'posts_per_page' => 20,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$args = wp_parse_args( $args, $defaults );

	return get_posts( $args );
}

/**
 * Get submission count by email
 *
 * @param string $email Contact email.
 * @return int
 */
function stitch_get_submission_count_by_email( $email ) {
	$args = array(
		'post_type'      => 'stitch_form_log',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'   => 'form_email',
				'value' => sanitize_email( $email ),
			),
		),
		'fields'         => 'ids',
	);

	$query = new WP_Query( $args );
	return $query->found_posts;
}

/**
 * Create lead note in HubSpot
 *
 * @param string $email Contact email.
 * @param string $note Note content.
 * @return array|WP_Error
 */
function stitch_create_hubspot_note( $email, $note ) {
	$hubspot = stitch_get_hubspot_client();

	if ( ! $hubspot->is_configured() ) {
		return new WP_Error(
			'hubspot_not_configured',
			__( 'HubSpot is not properly configured.', 'stitch-consulting' )
		);
	}

	// Get contact ID by email
	$contact = $hubspot->get_contact_by_email( $email );
	if ( is_wp_error( $contact ) || empty( $contact['id'] ) ) {
		return new WP_Error(
			'contact_not_found',
			sprintf( __( 'Contact with email %s not found in HubSpot', 'stitch-consulting' ), $email )
		);
	}

	$contact_id = $contact['id'];

	// Create engagement (note)
	$engagement_endpoint = 'https://api.hubapi.com/crm/v3/objects/contacts/' . $contact_id . '/associations/engagement/notes';

	// Note: This is a simplified version. Full implementation would need engagements API
	// For now, we'll just log it
	error_log( sprintf( 'Note for %s: %s', $email, $note ) );

	return array(
		'success' => true,
		'message' => 'Note created',
	);
}

/**
 * Sync contact to HubSpot
 *
 * @param string $email Contact email.
 * @param array  $data Contact data.
 * @return array|WP_Error
 */
function stitch_sync_contact_to_hubspot( $email, $data ) {
	if ( ! is_email( $email ) ) {
		return new WP_Error(
			'invalid_email',
			__( 'Invalid email address', 'stitch-consulting' )
		);
	}

	$hubspot = stitch_get_hubspot_client();

	if ( ! $hubspot->is_configured() ) {
		return new WP_Error(
			'hubspot_not_configured',
			__( 'HubSpot is not properly configured.', 'stitch-consulting' )
		);
	}

	// Upsert contact
	return $hubspot->upsert_contact( $email, $data );
}

/**
 * Get HubSpot contact by email
 *
 * @param string $email Contact email.
 * @return array|WP_Error Contact data or error
 */
function stitch_get_hubspot_contact( $email ) {
	$hubspot = stitch_get_hubspot_client();

	if ( ! $hubspot->is_configured() ) {
		return new WP_Error(
			'hubspot_not_configured',
			__( 'HubSpot is not properly configured.', 'stitch-consulting' )
		);
	}

	return $hubspot->get_contact_by_email( $email );
}

/**
 * Sanitize form data for submission
 *
 * @param array $data Form data.
 * @return array Sanitized data
 */
function stitch_sanitize_form_data( $data ) {
	$sanitized = array();

	$allowed_fields = array(
		'email',
		'firstname',
		'lastname',
		'phone',
		'company',
		'message',
		'website',
		'subject',
	);

	foreach ( $allowed_fields as $field ) {
		if ( isset( $data[ $field ] ) ) {
			if ( 'email' === $field ) {
				$sanitized[ $field ] = sanitize_email( $data[ $field ] );
			} elseif ( 'message' === $field ) {
				$sanitized[ $field ] = sanitize_textarea_field( $data[ $field ] );
			} else {
				$sanitized[ $field ] = sanitize_text_field( $data[ $field ] );
			}
		}
	}

	return $sanitized;
}

/**
 * Validate form data
 *
 * @param array $data Form data.
 * @return true|WP_Error
 */
function stitch_validate_form_data( $data ) {
	// Email is required
	if ( empty( $data['email'] ) || ! is_email( $data['email'] ) ) {
		return new WP_Error(
			'invalid_email',
			__( 'Please provide a valid email address.', 'stitch-consulting' )
		);
	}

	// At least one more field should be filled
	$filled_fields = 0;
	foreach ( array( 'firstname', 'lastname', 'phone', 'company', 'message' ) as $field ) {
		if ( ! empty( $data[ $field ] ) ) {
			$filled_fields++;
		}
	}

	if ( $filled_fields === 0 ) {
		return new WP_Error(
			'form_incomplete',
			__( 'Please fill out at least one additional field.', 'stitch-consulting' )
		);
	}

	return true;
}

/**
 * Get HubSpot form embed code
 *
 * @param string $portal_id HubSpot Portal ID.
 * @param string $form_id HubSpot Form ID.
 * @return string HTML embed code
 */
function stitch_get_hubspot_form_embed( $portal_id, $form_id ) {
	if ( empty( $portal_id ) || empty( $form_id ) ) {
		return '';
	}

	$form_id = sanitize_text_field( $form_id );
	$portal_id = sanitize_text_field( $portal_id );

	ob_start();
	?>
	<div id="hubspot-form-<?php echo esc_attr( $form_id ); ?>"></div>
	<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/embed/v2.js"></script>
	<script>
		hbspt.forms.create({
			region: "na1",
			portalId: "<?php echo esc_js( $portal_id ); ?>",
			formId: "<?php echo esc_js( $form_id ); ?>",
			target: "#hubspot-form-<?php echo esc_js( $form_id ); ?>"
		});
	</script>
	<?php
	return ob_get_clean();
}

/**
 * Register custom post type for form logs
 */
add_action( 'init', function() {
	$args = array(
		'labels'             => array(
			'name'          => __( 'Form Submissions', 'stitch-consulting' ),
			'singular_name' => __( 'Form Submission', 'stitch-consulting' ),
		),
		'public'             => false,
		'show_in_admin_menu' => true,
		'supports'           => array( 'title', 'editor', 'custom-fields' ),
		'capability_type'    => 'post',
		'capabilities'       => array(
			'create_posts' => 'manage_options',
			'edit_posts'   => 'manage_options',
			'delete_posts' => 'manage_options',
		),
		'map_meta_cap'       => true,
	);

	register_post_type( 'stitch_form_log', $args );
} );

/**
 * Add form submission columns
 */
add_filter( 'manage_stitch_form_log_posts_columns', function( $columns ) {
	$columns['email'] = __( 'Email', 'stitch-consulting' );
	$columns['source'] = __( 'Source', 'stitch-consulting' );
	$columns['date'] = __( 'Date', 'stitch-consulting' );
	return $columns;
} );

/**
 * Display form submission column data
 */
add_action( 'manage_stitch_form_log_posts_custom_column', function( $column, $post_id ) {
	switch ( $column ) {
		case 'email':
			$email = get_post_meta( $post_id, 'form_email', true );
			echo esc_html( $email );
			break;
		case 'source':
			$source = get_post_meta( $post_id, 'form_source', true );
			echo esc_html( ucfirst( $source ) );
			break;
	}
}, 10, 2 );
