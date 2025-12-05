# HubSpot Integration Setup Guide

## Overview

This WordPress theme includes full HubSpot integration for lead capture, form submissions, and contact management. The integration provides:

- HubSpot API client for contact management
- REST API endpoint for form submissions
- Webhook handling for lead notifications
- Custom HubSpot form block for Gutenberg
- Form submission logging and tracking
- Rate limiting and security features

## Prerequisites

1. Active HubSpot account (Professional plan or higher recommended for API access)
2. HubSpot Portal ID and API Key
3. WordPress 6.0 or higher
4. PHP 7.4 or higher

## Configuration

### Step 1: Get HubSpot Credentials

1. Log in to your HubSpot account
2. Navigate to **Settings** > **Integrations** > **Private Apps**
3. Create a new Private App with the following scopes:
   - `crm.objects.contacts.read`
   - `crm.objects.contacts.write`
4. Copy the **Access Token** (this is your API Key)
5. Find your **Portal ID** in HubSpot settings or in your account URL (e.g., `app.hubspot.com/contacts/portal/123456`)

### Step 2: Configure WordPress

Add the following constants to your **wp-config.php** file:

```php
// HubSpot Configuration
define( 'HUBSPOT_PORTAL_ID', 'YOUR_PORTAL_ID_HERE' );
define( 'HUBSPOT_API_KEY', 'YOUR_PRIVATE_APP_ACCESS_TOKEN_HERE' );
define( 'HUBSPOT_WEBHOOK_KEY', 'YOUR_WEBHOOK_SIGNING_KEY_HERE' );
```

**Important:** Never share these credentials or commit them to version control.

### Step 3: Create a Custom HubSpot Form Block

1. Go to any page/post editor
2. Click the **+** button to add a block
3. Search for **HubSpot Form**
4. Click to add the block
5. In the block settings (right panel):
   - Enter your **Portal ID** (from Step 1)
   - Enter your **Form ID** (from your HubSpot account)
   - Optionally add a title and enable "Show Title"
6. Save/Publish the page

### Step 4: Set Up Form Submissions

#### Option A: Using HubSpot Form Block

The HubSpot Form block automatically handles form submissions through HubSpot's native functionality.

#### Option B: Using REST API Endpoint

You can submit form data directly to the WordPress REST API:

**Endpoint:** `POST /wp-json/stitch/v1/form-submit`

**Required Parameters:**
- `email` (string, required) - Contact email address
- `firstname` (string, optional) - First name
- `lastname` (string, optional) - Last name
- `phone` (string, optional) - Phone number
- `company` (string, optional) - Company name
- `message` (string, optional) - Message/inquiry details

**Example Request:**

```bash
curl -X POST https://your-site.com/wp-json/stitch/v1/form-submit \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "firstname": "John",
    "lastname": "Doe",
    "phone": "+1-555-0123",
    "company": "Acme Corp",
    "message": "I am interested in your services."
  }'
```

**Example Response (Success):**

```json
{
  "success": true,
  "message": "Thank you for your submission. We will be in touch soon!"
}
```

**Example Response (Error):**

```json
{
  "code": "invalid_email",
  "message": "Please provide a valid email address",
  "data": {
    "status": 400
  }
}
```

### Step 5: Set Up Webhooks (Optional)

To receive notifications when new leads are added to HubSpot:

1. In HubSpot, go to **Settings** > **Integrations** > **Webhooks**
2. Click **Create webhook**
3. Set the Request URL to:
   ```
   https://your-site.com/wp-json/stitch/v1/hubspot-webhook
   ```
4. Select event types to subscribe to (e.g., Contact created, Contact updated)
5. Copy the **Signing Key** and add it to **wp-config.php** as `HUBSPOT_WEBHOOK_KEY`
6. Save the webhook

## Features

### 1. Contact Management

#### Upsert Contact (Create or Update)

```php
$hubspot = stitch_get_hubspot_client();
$result = $hubspot->upsert_contact( 'john@example.com', array(
    'firstname' => 'John',
    'lastname' => 'Doe',
    'phone' => '+1-555-0123',
    'company' => 'Acme Corp',
) );
```

#### Get Contact by Email

```php
$hubspot = stitch_get_hubspot_client();
$contact = $hubspot->get_contact_by_email( 'john@example.com' );
if ( ! is_wp_error( $contact ) ) {
    echo 'Contact ID: ' . $contact['id'];
}
```

### 2. Form Submission Logging

All form submissions are automatically logged as custom post types (`stitch_form_log`) in WordPress. You can view them in the admin panel under **Form Submissions**.

Each submission includes:
- Email address
- Submission source (HubSpot, Contact Form 7, etc.)
- Full form data
- API response
- Submission timestamp

### 3. Rate Limiting

To prevent spam, the API endpoint enforces rate limiting:
- **Limit:** 5 submissions per email per hour
- **Response:** 429 Too Many Requests if limit exceeded

To modify the rate limit, edit the `stitch_handle_form_submission()` function in `inc/hubspot-integration.php`.

### 4. Security Features

- **API Key Storage:** Credentials stored in wp-config.php (server-side only)
- **Input Sanitization:** All form inputs are sanitized and validated
- **Nonce Protection:** CSRF tokens validated on form submissions
- **Webhook Signature Verification:** HubSpot webhook signatures are verified
- **HTTPS Required:** SSL verification enabled for all API calls
- **Email Validation:** Strict email format validation

## Helper Functions

### Get HubSpot Client

```php
$hubspot = stitch_get_hubspot_client();
```

### Sync Contact to HubSpot

```php
$result = stitch_sync_contact_to_hubspot( 'john@example.com', array(
    'firstname' => 'John',
    'lastname' => 'Doe',
) );
```

### Get HubSpot Contact

```php
$contact = stitch_get_hubspot_contact( 'john@example.com' );
```

### Log Form Submission

```php
stitch_log_form_submission( 'john@example.com', $form_data, 'hubspot', $api_response );
```

### Sanitize Form Data

```php
$clean_data = stitch_sanitize_form_data( $raw_form_data );
```

### Validate Form Data

```php
$validation = stitch_validate_form_data( $form_data );
if ( is_wp_error( $validation ) ) {
    echo 'Error: ' . $validation->get_error_message();
}
```

## Troubleshooting

### Forms not submitting

1. Verify `HUBSPOT_PORTAL_ID` and `HUBSPOT_API_KEY` are set in wp-config.php
2. Check WordPress debug log for errors: `wp-content/debug.log`
3. Ensure your HubSpot API key has the required scopes
4. Verify HTTPS is enabled on your site

### "HubSpot is not properly configured" error

- Ensure both `HUBSPOT_PORTAL_ID` and `HUBSPOT_API_KEY` constants are defined in wp-config.php
- Verify the values are correct (check for typos, spaces, etc.)

### Webhooks not working

1. Verify the webhook URL is correct: `https://your-site.com/wp-json/stitch/v1/hubspot-webhook`
2. Ensure `HUBSPOT_WEBHOOK_KEY` is set in wp-config.php
3. Check WordPress error log for webhook processing errors
4. Verify HTTPS and SSL certificates are valid

### Rate limiting too strict

Edit the rate limit in `inc/hubspot-integration.php`:

```php
// Change 5 to desired limit
if ( false !== $attempt_count && $attempt_count >= 5 ) {
```

### API Key Exposed

If your HubSpot API key is accidentally exposed:

1. Immediately regenerate the Private App token in HubSpot
2. Update wp-config.php with the new token
3. Review HubSpot audit logs for unauthorized access

## File Structure

```
stitch-consulting-theme/
├── inc/
│   └── hubspot-integration.php    # API client, endpoints, webhooks
├── functions/
│   └── hubspot.php                # Helper functions, logging
├── blocks/
│   └── form-hubspot/              # Custom HubSpot form block
│       ├── block.json
│       ├── edit.js
│       ├── save.js
│       ├── render.php
│       ├── edit.css
│       └── style.css
└── HUBSPOT-SETUP.md               # This file
```

## API Reference

### Stitch_HubSpot_API Class

#### `is_configured()`

Check if HubSpot is properly configured.

**Returns:** `bool`

#### `get_portal_id()`

Get the HubSpot Portal ID.

**Returns:** `string`

#### `submit_contact( $data )`

Submit a new contact to HubSpot.

**Parameters:**
- `$data` (array) - Contact data

**Returns:** `array|WP_Error`

#### `upsert_contact( $email, $data )`

Create or update a contact in HubSpot.

**Parameters:**
- `$email` (string) - Contact email
- `$data` (array) - Contact data

**Returns:** `array|WP_Error`

#### `get_contact_by_email( $email )`

Retrieve a contact from HubSpot by email.

**Parameters:**
- `$email` (string) - Contact email

**Returns:** `array|WP_Error`

#### `verify_webhook_signature( $body, $signature, $signature_version, $request_timestamp )`

Verify HubSpot webhook signature.

**Parameters:**
- `$body` (string) - Request body
- `$signature` (string) - Signature header
- `$signature_version` (string) - Signature version (default: 'v1')
- `$request_timestamp` (string) - Request timestamp

**Returns:** `bool`

## Best Practices

1. **Regular Backups:** Keep regular backups of your WordPress database
2. **Test First:** Test form submissions in staging before production
3. **Monitor Logs:** Check debug logs regularly for errors
4. **API Key Rotation:** Rotate API keys periodically for security
5. **Rate Limiting:** Adjust rate limits based on your expected traffic
6. **Error Handling:** Implement proper error handling in custom integrations
7. **Data Privacy:** Ensure compliance with GDPR and privacy regulations

## Support & Further Documentation

- [HubSpot API Documentation](https://developers.hubspot.com/docs/crm/apis/overview)
- [WordPress REST API Documentation](https://developer.wordpress.org/rest-api/)
- [Theme Development Guidelines](../docs/code-standards.md)

## Changelog

### Version 1.0.0 (Initial Release)

- HubSpot API integration
- Form submission handler
- Custom HubSpot form block
- Webhook handling
- Form submission logging
- Rate limiting
- Security features
