# HubSpot Integration - Quick Start Guide

## 5-Minute Setup

### 1. Add to wp-config.php

```php
define( 'HUBSPOT_PORTAL_ID', 'YOUR_PORTAL_ID' );
define( 'HUBSPOT_API_KEY', 'YOUR_API_KEY' );
define( 'HUBSPOT_WEBHOOK_KEY', 'YOUR_WEBHOOK_KEY' ); // Optional
```

### 2. Get Your Credentials

1. Go to HubSpot > Settings > Integrations > Private Apps
2. Create app or use existing
3. Copy Portal ID from app dashboard
4. Copy API token from Private App

### 3. Add Form to Page

1. Edit a page in WordPress
2. Add block: search "HubSpot Form"
3. Set Portal ID and Form ID
4. Publish page

---

## Common Tasks

### Check If HubSpot is Configured

```php
$hubspot = stitch_get_hubspot_client();
if ( $hubspot->is_configured() ) {
    echo 'HubSpot is ready!';
}
```

### Manually Submit a Contact

```php
$hubspot = stitch_get_hubspot_client();
$result = $hubspot->upsert_contact( 'user@example.com', array(
    'firstname' => 'John',
    'lastname' => 'Doe',
    'phone' => '+1-555-0123',
    'company' => 'ACME Inc',
) );

if ( is_wp_error( $result ) ) {
    echo 'Error: ' . $result->get_error_message();
} else {
    echo 'Contact created/updated!';
}
```

### Get a Contact from HubSpot

```php
$contact = stitch_get_hubspot_contact( 'user@example.com' );
if ( ! is_wp_error( $contact ) ) {
    echo 'Contact ID: ' . $contact['id'];
    echo 'Email: ' . $contact['properties']['email'];
}
```

### Check Form Submissions

1. Go to WordPress admin
2. Select **Form Submissions** from sidebar
3. View all submissions with email and date

### Submit Form via API

```bash
curl -X POST https://your-site.com/wp-json/stitch/v1/form-submit \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "firstname": "John",
    "lastname": "Doe",
    "phone": "+1-555-0123",
    "company": "ACME Inc",
    "message": "Interested in your services"
  }'
```

---

## Troubleshooting

### "HubSpot is not properly configured"

Check wp-config.php has:
```php
define( 'HUBSPOT_PORTAL_ID', '...' );
define( 'HUBSPOT_API_KEY', '...' );
```

### Form Not Submitting

1. Check browser console for errors
2. Check WordPress debug log: `wp-content/debug.log`
3. Verify API key has correct scopes in HubSpot

### API Key Invalid

1. Go to HubSpot > Settings > Integrations > Private Apps
2. Regenerate token
3. Update wp-config.php

### Rate Limit Error

Max 5 submissions per email per hour. Wait before retrying.

---

## Security Checklist

- [x] API key in wp-config.php (never in code)
- [x] HTTPS enabled on production
- [x] Form data sanitized
- [x] Webhook signature verified
- [x] Rate limiting enabled

---

## API Endpoints

### Form Submission
```
POST /wp-json/stitch/v1/form-submit
```

### Webhook
```
POST /wp-json/stitch/v1/hubspot-webhook
```

---

## Helper Functions Reference

```php
// Get client
stitch_get_hubspot_client()

// Sync contact
stitch_sync_contact_to_hubspot( $email, $data )

// Get contact
stitch_get_hubspot_contact( $email )

// Log submission
stitch_log_form_submission( $email, $data, $source )

// Validate data
stitch_validate_form_data( $data )

// Sanitize data
stitch_sanitize_form_data( $data )

// Get form embed code
stitch_get_hubspot_form_embed( $portal_id, $form_id )
```

---

## Support

Full documentation: See `HUBSPOT-SETUP.md`
Implementation report: See `PHASE-06-REPORT.md`
