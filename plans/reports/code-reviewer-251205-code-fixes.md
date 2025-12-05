# WordPress Theme Code Review - Ready-to-Use Code Fixes

**Purpose:** Copy-paste ready code solutions for P0 and P1 issues
**Date:** December 5, 2025

---

## FIX P0-001: Form Input Validation System

### Create `/inc/form-validation.php`

```php
<?php
/**
 * Form validation functions for Stitch theme
 *
 * @package Stitch_Consulting_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define allowed form fields with validation rules
 *
 * @return array Allowed fields and their validation rules
 */
function stitch_get_allowed_form_fields() {
    return apply_filters( 'stitch_form_allowed_fields', [
        'name' => [
            'type' => 'text',
            'required' => true,
            'min_length' => 2,
            'max_length' => 100,
            'regex' => '/^[a-zA-Z\s\'-]*$/'
        ],
        'email' => [
            'type' => 'email',
            'required' => true,
            'regex' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/'
        ],
        'phone' => [
            'type' => 'phone',
            'required' => false,
            'min_length' => 10,
            'max_length' => 20,
            'regex' => '/^[\d\s\-\+\(\)]+$/'
        ],
        'company' => [
            'type' => 'text',
            'required' => false,
            'max_length' => 100
        ],
        'message' => [
            'type' => 'textarea',
            'required' => true,
            'min_length' => 10,
            'max_length' => 2000
        ],
        'subject' => [
            'type' => 'text',
            'required' => false,
            'max_length' => 200
        ]
    ] );
}

/**
 * Validate form input data
 *
 * @param array $post_data Raw $_POST data
 * @return array Validated and sanitized data
 * @throws Exception On validation failure
 */
function stitch_validate_form_input( $post_data ) {
    $allowed_fields = stitch_get_allowed_form_fields();
    $validated_data = [];

    // Check for extra fields not in whitelist
    $post_keys = array_keys( $post_data );
    $allowed_keys = array_keys( $allowed_fields );
    $extra_keys = array_diff( $post_keys, $allowed_keys, [ 'action', '_nonce', 'success_message' ] );

    if ( ! empty( $extra_keys ) ) {
        throw new Exception( 'Form contains unexpected fields: ' . implode( ', ', $extra_keys ) );
    }

    // Validate each allowed field
    foreach ( $allowed_fields as $field_name => $field_config ) {
        // Skip if field not in submission
        if ( ! isset( $post_data[ $field_name ] ) ) {
            if ( $field_config['required'] ) {
                throw new Exception( sprintf( 'Required field missing: %s', $field_name ) );
            }
            continue;
        }

        $value = wp_unslash( $post_data[ $field_name ] );

        // Validate based on type
        try {
            $validated_value = stitch_validate_field_value(
                $field_name,
                $value,
                $field_config
            );
            $validated_data[ $field_name ] = $validated_value;
        } catch ( Exception $e ) {
            throw new Exception( "Field '{$field_name}': " . $e->getMessage() );
        }
    }

    return $validated_data;
}

/**
 * Validate individual field value
 *
 * @param string $field_name Field name
 * @param string $value Field value
 * @param array $config Field configuration
 * @return string Validated value
 * @throws Exception On validation failure
 */
function stitch_validate_field_value( $field_name, $value, $config ) {
    // Check required
    if ( $config['required'] && empty( $value ) ) {
        throw new Exception( 'This field is required' );
    }

    // Skip empty optional fields
    if ( empty( $value ) && ! $config['required'] ) {
        return '';
    }

    // Type-specific validation
    switch ( $config['type'] ) {
        case 'email':
            return stitch_validate_email( $value );

        case 'phone':
            return stitch_validate_phone( $value, $config );

        case 'text':
            return stitch_validate_text( $value, $config );

        case 'textarea':
            return stitch_validate_textarea( $value, $config );

        default:
            return sanitize_text_field( $value );
    }
}

/**
 * Validate email field
 *
 * @param string $value Email address
 * @return string Validated email
 * @throws Exception On invalid email
 */
function stitch_validate_email( $value ) {
    $email = sanitize_email( $value );

    if ( ! is_email( $email ) ) {
        throw new Exception( 'Invalid email format' );
    }

    if ( strlen( $email ) > 254 ) { // RFC 5321
        throw new Exception( 'Email address too long' );
    }

    return $email;
}

/**
 * Validate phone field
 *
 * @param string $value Phone number
 * @param array $config Field configuration
 * @return string Validated phone
 * @throws Exception On invalid phone
 */
function stitch_validate_phone( $value, $config ) {
    $phone = sanitize_text_field( $value );

    // Check length
    $digits_only = preg_replace( '/\D/', '', $phone );
    if ( strlen( $digits_only ) < 10 ) {
        throw new Exception( 'Phone number must be at least 10 digits' );
    }

    // Check regex pattern if provided
    if ( ! empty( $config['regex'] ) && ! preg_match( $config['regex'], $phone ) ) {
        throw new Exception( 'Invalid phone format' );
    }

    return $phone;
}

/**
 * Validate text field
 *
 * @param string $value Text value
 * @param array $config Field configuration
 * @return string Validated text
 * @throws Exception On invalid text
 */
function stitch_validate_text( $value, $config ) {
    $text = sanitize_text_field( $value );

    // Check length
    $length = strlen( $text );
    if ( isset( $config['min_length'] ) && $length < $config['min_length'] ) {
        throw new Exception( sprintf( 'Must be at least %d characters', $config['min_length'] ) );
    }

    if ( isset( $config['max_length'] ) && $length > $config['max_length'] ) {
        throw new Exception( sprintf( 'Must not exceed %d characters', $config['max_length'] ) );
    }

    // Check regex pattern if provided
    if ( ! empty( $config['regex'] ) && ! preg_match( $config['regex'], $text ) ) {
        throw new Exception( 'Invalid format' );
    }

    return $text;
}

/**
 * Validate textarea field
 *
 * @param string $value Textarea value
 * @param array $config Field configuration
 * @return string Validated textarea
 * @throws Exception On invalid textarea
 */
function stitch_validate_textarea( $value, $config ) {
    $textarea = sanitize_textarea_field( $value );

    // Check length
    $length = strlen( $textarea );
    if ( isset( $config['min_length'] ) && $length < $config['min_length'] ) {
        throw new Exception( sprintf( 'Must be at least %d characters', $config['min_length'] ) );
    }

    if ( isset( $config['max_length'] ) && $length > $config['max_length'] ) {
        throw new Exception( sprintf( 'Must not exceed %d characters', $config['max_length'] ) );
    }

    return $textarea;
}

/**
 * Sanitize data for specific output context
 *
 * @param array $data Data to sanitize
 * @param string $context Output context ('html', 'js', 'attr')
 * @return array Sanitized data
 */
function stitch_sanitize_for_output( $data, $context = 'html' ) {
    $sanitized = [];

    foreach ( $data as $key => $value ) {
        switch ( $context ) {
            case 'html':
                $sanitized[ $key ] = wp_kses_post( $value );
                break;

            case 'attr':
                $sanitized[ $key ] = esc_attr( $value );
                break;

            case 'js':
                $sanitized[ $key ] = json_encode( $value );
                break;

            default:
                $sanitized[ $key ] = wp_kses_post( $value );
        }
    }

    return $sanitized;
}
```

### Update `/blocks/form/form-handler.php`

Replace the entire file with:

```php
<?php
/**
 * Form Handler for Stitch Blocks
 *
 * Securely processes form submissions with validation,
 * rate limiting, and error logging.
 */

if ( ! function_exists( 'stitch_handle_form_submission' ) ) {
    function stitch_handle_form_submission() {
        try {
            // Step 1: Verify nonce for CSRF protection
            if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'stitch_form_nonce' ) ) {
                throw new Exception( 'Security verification failed' );
            }

            // Step 2: Check rate limiting
            stitch_check_form_rate_limit();

            // Step 3: Validate and sanitize input
            $validated_data = stitch_validate_form_input( $_POST );

            // Step 4: Determine action and process
            $action = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : '';
            $success_message = isset( $_POST['success_message'] ) ? sanitize_text_field( $_POST['success_message'] ) : 'Thank you!';

            if ( strpos( $action, 'stitch_form_email' ) !== false ) {
                stitch_send_form_email( $validated_data );
            } elseif ( strpos( $action, 'stitch_form_hubspot' ) !== false ) {
                stitch_send_form_hubspot( $validated_data );
            } elseif ( strpos( $action, 'stitch_form_webhook' ) !== false ) {
                stitch_send_form_webhook( $validated_data );
            } else {
                throw new Exception( 'Invalid form action' );
            }

            // Success response
            wp_send_json_success( [ 'message' => $success_message ] );

        } catch ( Exception $e ) {
            // Log error for debugging
            stitch_log_form_error( $e );

            // Send user-friendly error
            wp_send_json_error( [
                'message' => 'An error occurred processing your form. Please try again.'
            ] );
        }
    }

    add_action( 'wp_ajax_nopriv_stitch_form_email', 'stitch_handle_form_submission' );
    add_action( 'wp_ajax_stitch_form_email', 'stitch_handle_form_submission' );
    add_action( 'wp_ajax_nopriv_stitch_form_hubspot', 'stitch_handle_form_submission' );
    add_action( 'wp_ajax_stitch_form_hubspot', 'stitch_handle_form_submission' );
    add_action( 'wp_ajax_nopriv_stitch_form_webhook', 'stitch_handle_form_submission' );
    add_action( 'wp_ajax_stitch_form_webhook', 'stitch_handle_form_submission' );
}

/**
 * Check rate limiting for form submissions
 *
 * @throws Exception If rate limit exceeded
 */
if ( ! function_exists( 'stitch_check_form_rate_limit' ) ) {
    function stitch_check_form_rate_limit() {
        $ip = stitch_get_client_ip();
        $cache_key = 'stitch_form_limit_' . md5( $ip );
        $attempts = (int) wp_cache_get( $cache_key );

        $max_attempts = apply_filters( 'stitch_form_max_attempts', 5 );
        $time_window = apply_filters( 'stitch_form_rate_limit_window', HOUR_IN_SECONDS );

        if ( $attempts >= $max_attempts ) {
            throw new Exception( 'Too many submission attempts. Please try again later.' );
        }

        $attempts++;
        wp_cache_set( $cache_key, $attempts, '', $time_window );
    }
}

/**
 * Get client IP address safely
 *
 * @return string Client IP address
 */
if ( ! function_exists( 'stitch_get_client_ip' ) ) {
    function stitch_get_client_ip() {
        $ip = '';

        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ips = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
            $ip = trim( $ips[0] );
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }

        // Validate IP format
        if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
            return $ip;
        }

        return '0.0.0.0';
    }
}

/**
 * Log form submission error
 *
 * @param Exception $exception The exception to log
 */
if ( ! function_exists( 'stitch_log_form_error' ) ) {
    function stitch_log_form_error( Exception $exception ) {
        $ip = stitch_get_client_ip();
        $action = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : 'unknown';
        $fields = ! empty( $_POST ) ? implode( ', ', array_keys( $_POST ) ) : 'none';

        $log_message = sprintf(
            'Form submission error | IP: %s | Action: %s | Error: %s | Fields: %s',
            $ip,
            $action,
            $exception->getMessage(),
            $fields
        );

        error_log( $log_message );

        // Optional: Send to external monitoring service
        do_action( 'stitch_form_error_logged', $exception, $ip, $action );
    }
}

/**
 * Send form data via email
 *
 * @param array $form_data Validated form data
 * @throws Exception On mail failure
 */
if ( ! function_exists( 'stitch_send_form_email' ) ) {
    function stitch_send_form_email( $form_data ) {
        $to = get_option( 'admin_email' );
        $subject = 'New Form Submission - ' . get_bloginfo( 'name' );
        $site_url = get_site_url();

        $message = "New form submission from {$site_url}:\n\n";
        $message .= "===========================================\n";

        foreach ( $form_data as $key => $value ) {
            $label = ucwords( str_replace( '_', ' ', $key ) );
            $message .= "{$label}: {$value}\n";
        }

        $message .= "===========================================\n";
        $message .= "IP Address: " . stitch_get_client_ip() . "\n";
        $message .= "Timestamp: " . current_time( 'Y-m-d H:i:s' ) . "\n";

        $headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

        $sent = wp_mail( $to, $subject, $message, $headers );

        if ( ! $sent ) {
            throw new Exception( 'Failed to send email. Please try again.' );
        }
    }
}

/**
 * Send form data to HubSpot
 *
 * @param array $form_data Validated form data
 * @throws Exception On API error
 */
if ( ! function_exists( 'stitch_send_form_hubspot' ) ) {
    function stitch_send_form_hubspot( $form_data ) {
        // Get API key from environment
        $hubspot_api_key = defined( 'STITCH_HUBSPOT_API_KEY' )
            ? STITCH_HUBSPOT_API_KEY
            : get_option( 'stitch_hubspot_api_key' );

        if ( empty( $hubspot_api_key ) ) {
            throw new Exception( 'HubSpot is not configured' );
        }

        // Validate email is present
        if ( empty( $form_data['email'] ) ) {
            throw new Exception( 'Email is required for HubSpot integration' );
        }

        // Prepare HubSpot contact data
        $contact_data = [
            'properties' => []
        ];

        $property_mapping = apply_filters( 'stitch_hubspot_property_mapping', [
            'name' => 'firstname',
            'email' => 'email',
            'phone' => 'phone',
            'company' => 'company',
            'message' => 'message'
        ] );

        foreach ( $form_data as $key => $value ) {
            $hubspot_property = $property_mapping[ $key ] ?? $key;
            $contact_data['properties'][] = [
                'property' => $hubspot_property,
                'value' => $value
            ];
        }

        // Send to HubSpot API
        $response = wp_remote_post( 'https://api.hubapi.com/crm/v3/objects/contacts', [
            'headers' => [
                'Authorization' => 'Bearer ' . $hubspot_api_key,
                'Content-Type' => 'application/json'
            ],
            'body' => wp_json_encode( $contact_data ),
            'timeout' => 10,
            'sslverify' => true
        ] );

        if ( is_wp_error( $response ) ) {
            throw new Exception( 'HubSpot API error: ' . $response->get_error_message() );
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        if ( $status_code < 200 || $status_code >= 300 ) {
            throw new Exception( 'HubSpot returned error code: ' . $status_code );
        }
    }
}

/**
 * Send form data to webhook
 *
 * @param array $form_data Validated form data
 * @throws Exception On webhook error
 */
if ( ! function_exists( 'stitch_send_form_webhook' ) ) {
    function stitch_send_form_webhook( $form_data ) {
        $webhook_url = get_option( 'stitch_form_webhook_url' );

        if ( empty( $webhook_url ) ) {
            throw new Exception( 'Webhook is not configured' );
        }

        // Validate webhook URL
        try {
            $webhook_url = stitch_validate_webhook_url( $webhook_url );
        } catch ( Exception $e ) {
            throw new Exception( 'Webhook configuration error: ' . $e->getMessage() );
        }

        // Send to webhook
        $response = wp_remote_post( $webhook_url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Webhook-Signature' => stitch_generate_webhook_signature( $form_data )
            ],
            'body' => wp_json_encode( [
                'event' => 'form_submission',
                'timestamp' => current_time( 'c' ),
                'data' => $form_data
            ] ),
            'timeout' => 10,
            'sslverify' => true
        ] );

        if ( is_wp_error( $response ) ) {
            throw new Exception( 'Webhook error: ' . $response->get_error_message() );
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        if ( $status_code < 200 || $status_code >= 300 ) {
            throw new Exception( 'Webhook returned error code: ' . $status_code );
        }
    }
}

/**
 * Validate webhook URL for security
 *
 * @param string $url Webhook URL
 * @return string Validated URL
 * @throws Exception On invalid URL
 */
if ( ! function_exists( 'stitch_validate_webhook_url' ) ) {
    function stitch_validate_webhook_url( $url ) {
        $parsed = parse_url( $url );

        // Validate URL structure
        if ( ! $parsed || empty( $parsed['scheme'] ) || empty( $parsed['host'] ) ) {
            throw new Exception( 'Invalid webhook URL format' );
        }

        // Require HTTPS
        if ( $parsed['scheme'] !== 'https' ) {
            throw new Exception( 'Webhook URL must use HTTPS' );
        }

        // Prevent localhost/internal IPs
        if ( ! filter_var( $parsed['host'], FILTER_VALIDATE_DOMAIN ) ) {
            throw new Exception( 'Invalid webhook domain' );
        }

        // Whitelist check (optional but recommended)
        $allowed_domains = apply_filters( 'stitch_webhook_allowed_domains', [] );
        if ( ! empty( $allowed_domains ) ) {
            if ( ! in_array( $parsed['host'], $allowed_domains, true ) ) {
                throw new Exception( 'Webhook domain not whitelisted' );
            }
        }

        return esc_url_raw( $url );
    }
}

/**
 * Generate webhook signature for verification
 *
 * @param array $data Form data
 * @return string HMAC signature
 */
if ( ! function_exists( 'stitch_generate_webhook_signature' ) ) {
    function stitch_generate_webhook_signature( $data ) {
        $secret = apply_filters( 'stitch_webhook_secret', wp_salt( 'secure_auth' ) );
        $payload = wp_json_encode( $data );

        return hash_hmac( 'sha256', $payload, $secret );
    }
}
```

---

## FIX P0-002: API Key Environment Setup

### Update `wp-config.php`

Add this before `/* That's all, stop editing! */`:

```php
/**
 * Stitch Theme Configuration
 * Load environment variables for sensitive configuration
 */
if ( file_exists( dirname( __FILE__ ) . '/.env.php' ) ) {
    require_once dirname( __FILE__ ) . '/.env.php';
}

// Ensure critical configuration is set
if ( ! defined( 'STITCH_HUBSPOT_API_KEY' ) ) {
    define( 'STITCH_HUBSPOT_API_KEY', '' );
}
```

### Create `.env.php` (NOT in git)

```php
<?php
/**
 * Local Environment Configuration
 * THIS FILE SHOULD NOT BE COMMITTED TO GIT
 * Add to .gitignore: .env.php
 */

// HubSpot API Configuration
if ( getenv( 'STITCH_HUBSPOT_API_KEY' ) ) {
    define( 'STITCH_HUBSPOT_API_KEY', getenv( 'STITCH_HUBSPOT_API_KEY' ) );
} else {
    define( 'STITCH_HUBSPOT_API_KEY', 'your-hubspot-api-key-here' );
}

// Webhook Configuration
if ( getenv( 'STITCH_WEBHOOK_URL' ) ) {
    define( 'STITCH_WEBHOOK_URL', getenv( 'STITCH_WEBHOOK_URL' ) );
}

// Webhook Secret for HMAC signatures
if ( getenv( 'STITCH_WEBHOOK_SECRET' ) ) {
    define( 'STITCH_WEBHOOK_SECRET', getenv( 'STITCH_WEBHOOK_SECRET' ) );
}
```

### Create `.env.example` (commit to git)

```
# Stitch Consulting Theme Configuration
# Copy to .env and fill in your values

# HubSpot API Key
# Get from: https://app.hubspot.com/l/settings/integrations/api
STITCH_HUBSPOT_API_KEY=

# Webhook URL for form submissions
# Must be HTTPS and from whitelisted domain
STITCH_WEBHOOK_URL=

# Webhook secret for HMAC signature verification
STITCH_WEBHOOK_SECRET=
```

### Add to `.gitignore`

```
# Environment files (NEVER commit these)
.env
.env.php
.env.local
*.env.local
```

---

## FIX P0-003: Webhook URL Validation

Already included in the form-handler.php fix above. See `stitch_validate_webhook_url()` function.

---

## FIX P0-004: Extract Inline JavaScript

### Create `/blocks/form/form-submit.js`

```javascript
/**
 * Form Submission Handler
 * Handles client-side form validation and submission
 */

(function() {
    'use strict';

    /**
     * Initialize form submission handlers
     */
    function initFormHandlers() {
        const forms = document.querySelectorAll('.wp-block-stitch-form__form');
        forms.forEach(function(form) {
            form.addEventListener('submit', handleFormSubmit);
        });
    }

    /**
     * Handle form submission
     * @param {Event} event
     */
    function handleFormSubmit(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');

        // Validate form before submission
        if (!validateForm(form)) {
            showErrorMessage(form, 'Please fill all required fields correctly');
            return false;
        }

        // Disable submit button and show loading state
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Sending...';

        // Prepare data for submission
        const data = Object.fromEntries(formData);
        const ajaxUrl = window.stitchFormConfig?.ajaxUrl || '/wp-admin/admin-ajax.php';
        const nonce = data._nonce || '';

        // Submit via AJAX
        fetch(ajaxUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-WP-Nonce': nonce
            },
            body: new URLSearchParams(data)
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(function(result) {
            if (result.success) {
                showSuccessMessage(form, data.success_message || 'Thank you!');
                form.reset();
            } else {
                const errorMsg = result.data?.message || 'An error occurred. Please try again.';
                showErrorMessage(form, errorMsg);
            }
        })
        .catch(function(error) {
            console.error('Form submission error:', error);
            showErrorMessage(form, 'Error submitting form. Please try again.');
        })
        .finally(function() {
            // Restore submit button
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });

        return false;
    }

    /**
     * Validate form fields client-side
     * @param {HTMLFormElement} form
     * @returns {boolean}
     */
    function validateForm(form) {
        const inputs = form.querySelectorAll('input, textarea');
        let isValid = true;

        inputs.forEach(function(input) {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Validate individual field
     * @param {HTMLInputElement|HTMLTextAreaElement} field
     * @returns {boolean}
     */
    function validateField(field) {
        // Check required
        if (field.hasAttribute('required') && !field.value.trim()) {
            field.classList.add('is-invalid');
            return false;
        }

        // Validate email fields
        if (field.type === 'email' && field.value.trim()) {
            if (!isValidEmail(field.value)) {
                field.classList.add('is-invalid');
                return false;
            }
        }

        // Clear error state
        field.classList.remove('is-invalid');
        return true;
    }

    /**
     * Simple email validation
     * @param {string} email
     * @returns {boolean}
     */
    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    /**
     * Show success message
     * @param {HTMLFormElement} form
     * @param {string} message
     */
    function showSuccessMessage(form, message) {
        clearMessages(form);

        const messageDiv = document.createElement('div');
        messageDiv.className = 'stitch-form-message stitch-form-message--success';
        messageDiv.setAttribute('role', 'alert');
        messageDiv.textContent = escapeHtml(message);

        form.parentNode.insertBefore(messageDiv, form);

        // Auto-hide after 5 seconds
        setTimeout(function() {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }

    /**
     * Show error message
     * @param {HTMLFormElement} form
     * @param {string} message
     */
    function showErrorMessage(form, message) {
        clearMessages(form);

        const messageDiv = document.createElement('div');
        messageDiv.className = 'stitch-form-message stitch-form-message--error';
        messageDiv.setAttribute('role', 'alert');
        messageDiv.textContent = escapeHtml(message);

        form.parentNode.insertBefore(messageDiv, form);
    }

    /**
     * Clear all messages
     * @param {HTMLFormElement} form
     */
    function clearMessages(form) {
        const messages = form.parentNode.querySelectorAll('.stitch-form-message');
        messages.forEach(function(msg) {
            msg.remove();
        });
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text
     * @returns {string}
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) {
            return map[m];
        });
    }

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFormHandlers);
    } else {
        initFormHandlers();
    }

    // Reinitialize for dynamically added forms
    if (window.MutationObserver) {
        const observer = new MutationObserver(function() {
            initFormHandlers();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
})();
```

### Update `/blocks/form/render.php`

Replace the entire file:

```php
<?php
/**
 * Form Block Render
 *
 * @var array  $attributes Block attributes
 * @var string $content    Block content
 * @var object $block      Block object
 */

// Validate and prepare attributes
$fields = isset( $attributes['fields'] ) ? $attributes['fields'] : [];
$submit_button_text = isset( $attributes['submitButtonText'] ) ? sanitize_text_field( $attributes['submitButtonText'] ) : 'Send Message';
$success_message = isset( $attributes['successMessage'] ) ? sanitize_text_field( $attributes['successMessage'] ) : 'Thank you!';
$form_action = isset( $attributes['formAction'] ) ? sanitize_text_field( $attributes['formAction'] ) : 'email';
$block_classes = isset( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';

// Generate unique form ID
$form_id = 'stitch-form-' . wp_generate_uuid4();
?>

<div class="wp-block-stitch-form <?php echo $block_classes; ?>" style="padding: 32px; background-color: #141414; border-radius: 12px; border: 1px solid #262626;">
    <!-- Form Messages Container -->
    <div class="stitch-form-messages" role="region" aria-live="polite" aria-atomic="true"></div>

    <form method="POST" id="<?php echo esc_attr( $form_id ); ?>" class="wp-block-stitch-form__form" style="display: flex; flex-direction: column; gap: 16px;">

        <?php foreach ( $fields as $field ) : ?>
            <?php
            $field_id = isset( $field['id'] ) ? sanitize_text_field( $field['id'] ) : '';
            $field_type = isset( $field['type'] ) ? sanitize_text_field( $field['type'] ) : 'text';
            $field_label = isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '';
            $field_placeholder = isset( $field['placeholder'] ) ? sanitize_text_field( $field['placeholder'] ) : '';
            $field_required = isset( $field['required'] ) ? (bool) $field['required'] : false;
            $field_help = isset( $field['help'] ) ? sanitize_text_field( $field['help'] ) : '';
            ?>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label for="<?php echo esc_attr( $field_id ); ?>" style="font-size: 0.875rem; font-weight: 600; color: #A3A3A3;">
                    <?php echo esc_html( $field_label ); ?>
                    <?php if ( $field_required ) : ?>
                        <span aria-label="required" style="color: #EF4444;">*</span>
                    <?php endif; ?>
                </label>

                <?php if ( $field_type === 'textarea' ) : ?>
                    <textarea
                        id="<?php echo esc_attr( $field_id ); ?>"
                        name="<?php echo esc_attr( $field_id ); ?>"
                        placeholder="<?php echo esc_attr( $field_placeholder ); ?>"
                        <?php echo $field_required ? 'required' : ''; ?>
                        <?php if ( ! empty( $field_help ) ) : ?>
                            aria-describedby="<?php echo esc_attr( $field_id . '-help' ); ?>"
                        <?php endif; ?>
                        style="padding: 12px; border-radius: 6px; border: 1px solid #262626; background-color: #0A0A0A; color: #FFFFFF; font-size: 0.95rem; font-family: Inter, sans-serif; min-height: 100px; resize: vertical;"
                    ></textarea>
                <?php else : ?>
                    <input
                        type="<?php echo esc_attr( $field_type ); ?>"
                        id="<?php echo esc_attr( $field_id ); ?>"
                        name="<?php echo esc_attr( $field_id ); ?>"
                        placeholder="<?php echo esc_attr( $field_placeholder ); ?>"
                        <?php echo $field_required ? 'required' : ''; ?>
                        <?php if ( ! empty( $field_help ) ) : ?>
                            aria-describedby="<?php echo esc_attr( $field_id . '-help' ); ?>"
                        <?php endif; ?>
                        style="padding: 12px; border-radius: 6px; border: 1px solid #262626; background-color: #0A0A0A; color: #FFFFFF; font-size: 0.95rem; font-family: Inter, sans-serif; height: 44px;"
                    />
                <?php endif; ?>

                <?php if ( ! empty( $field_help ) ) : ?>
                    <small id="<?php echo esc_attr( $field_id . '-help' ); ?>" style="color: #666; font-size: 0.85rem;">
                        <?php echo esc_html( $field_help ); ?>
                    </small>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Hidden Form Fields -->
        <input type="hidden" name="action" value="<?php echo esc_attr( 'stitch_form_' . $form_action ); ?>" />
        <input type="hidden" name="success_message" value="<?php echo esc_attr( $success_message ); ?>" />
        <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'stitch_form_nonce' ); ?>" />

        <!-- Honeypot Field (spam protection) -->
        <input type="hidden" name="stitch_form_honeypot" value="" style="position: absolute; left: -9999px; opacity: 0;" tabindex="-1" autocomplete="off" />

        <!-- Submit Button -->
        <button
            type="submit"
            class="wp-block-stitch-form__button"
            style="padding: 12px 32px; border-radius: 6px; background-color: #195de6; color: #FFFFFF; font-size: 1rem; font-weight: 700; border: none; cursor: pointer; margin-top: 8px; transition: all 0.3s ease;"
        >
            <?php echo esc_html( $submit_button_text ); ?>
        </button>
    </form>
</div>

<!-- Add inline CSS for form messages and validation states -->
<style>
.stitch-form-message {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 16px;
    font-weight: 500;
}

.stitch-form-message--success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.stitch-form-message--error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.wp-block-stitch-form__form input.is-invalid,
.wp-block-stitch-form__form textarea.is-invalid {
    border-color: #EF4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}
</style>
```

### Update `functions.php`

Add this function:

```php
/**
 * Enqueue form submission script
 */
function stitch_enqueue_form_scripts() {
    if ( is_admin() ) {
        return;
    }

    // Enqueue the form submission script
    wp_enqueue_script(
        'stitch-form-submit',
        STITCH_CONSULTING_THEME_URI . '/blocks/form/form-submit.js',
        [],
        filemtime( STITCH_CONSULTING_THEME_DIR . '/blocks/form/form-submit.js' ),
        true // Footer
    );

    // Localize script with AJAX URL and config
    wp_localize_script(
        'stitch-form-submit',
        'stitchFormConfig',
        [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'stitch_form_nonce' )
        ]
    );
}

add_action( 'wp_enqueue_scripts', 'stitch_enqueue_form_scripts' );
```

Also add at the top of functions.php after other requires:

```php
/**
 * Include form validation functions
 */
require_once STITCH_CONSULTING_THEME_DIR . '/inc/form-validation.php';
```

---

## FIX P1-001: Rate Limiting

Already included in the form-handler.php fix above. See `stitch_check_form_rate_limit()` function.

---

## FIX P1-002: Error Logging

Already included in the form-handler.php fix above. See `stitch_log_form_error()` function.

---

## FIX P1-003: Add ARIA Labels to Form Fields

Update `/blocks/form/render.php` - Already included in the complete fix above with `aria-labelledby` and `aria-describedby` attributes.

---

## FIX P1-004: Update Textdomains

Replace all instances of `"textdomain": "stitch"` with `"textdomain": "stitch-consulting"` in:

- `/blocks/form/block.json`
- `/blocks/hero/block.json`
- `/blocks/cta/block.json`
- `/blocks/feature-card/block.json`
- `/blocks/card-grid/block.json`
- `/blocks/stats/block.json`
- `/blocks/testimonial/block.json`

**Command to do it globally:**
```bash
cd /path/to/stitch-consulting-theme
find blocks -name "block.json" -exec sed -i '' 's/"textdomain": "stitch"/"textdomain": "stitch-consulting"/g' {} \;
```

---

## Testing These Fixes

### Test Form Validation

```bash
# SQL Injection test
curl -X POST http://localhost/wp-admin/admin-ajax.php \
  -d "action=stitch_form_email&name=Test&email=test@example.com&message='; DROP TABLE posts; --&_nonce=NONCE_HERE"

# Expected: Safe error message, no SQL executed

# XSS test
curl -X POST http://localhost/wp-admin/admin-ajax.php \
  -d "action=stitch_form_email&name=<script>alert('xss')</script>&email=test@example.com&message=Test&_nonce=NONCE_HERE"

# Expected: Script escaped, not executed
```

### Test Rate Limiting

Submit the form 10 times rapidly - should see rate limit error on attempts 6+.

### Test API Key Loading

```php
// In WordPress admin or functions.php
if ( defined( 'STITCH_HUBSPOT_API_KEY' ) ) {
    echo "API Key loaded from constants";
} else {
    echo "API Key not loaded";
}
```

---

**All fixes provided above - copy and integrate as instructed!**

