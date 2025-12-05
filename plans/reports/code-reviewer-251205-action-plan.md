# WordPress Theme Code Review - Prioritized Action Plan

**Created:** December 5, 2025
**Priority Level:** CRITICAL - DO NOT DEPLOY TO PRODUCTION

---

## EXECUTIVE ACTION SUMMARY

### Production Readiness: NO-GO

| Metric | Status | Target |
|--------|--------|--------|
| Critical Issues (P0) | 4 Found | 0 Required |
| High Priority (P1) | 6 Found | Must Fix |
| Security Scan Pass | FAIL | PASS |
| Accessibility (WCAG 2.1) | Partial | AA Complete |
| Test Coverage | None | 70%+ |
| **Production Ready** | **NO** | **YES** |

---

## BLOCKING ISSUES (Must Fix Before Production)

### Issue P0-001: Form Input Validation Missing
**Impact:** SQL Injection, XSS Vulnerability
**Risk Level:** CRITICAL
**Estimated Effort:** 3-4 hours
**Priority:** HIGHEST

**Current State:**
```php
// /blocks/form/form-handler.php (line 15)
$form_data = $_POST;  // NO VALIDATION
// Directly used without checking field types or allowed values
```

**Action Items:**
1. [ ] Create `/inc/form-validation.php` with whitelist validators
2. [ ] Define allowed form fields with type mappings
3. [ ] Implement type-specific validation functions
4. [ ] Add unit tests for validation
5. [ ] Update form handler to use validators
6. [ ] Test with invalid/malicious inputs

**Code Template:** See Appendix A in main review

**Owner:** Backend Developer
**Timeline:** Complete by end of Day 1

---

### Issue P0-002: HubSpot API Key in Plaintext DB
**Impact:** API Key Exposure, Account Compromise
**Risk Level:** CRITICAL
**Estimated Effort:** 2 hours
**Priority:** HIGHEST

**Current State:**
```php
// /blocks/form/form-handler.php (line 63)
$hubspot_api_key = get_option( 'stitch_hubspot_api_key' );
// Stored in wp_options table, visible in exports/backups
```

**Action Items:**
1. [ ] Create `.env.example` file with required secrets
2. [ ] Add environment variable loading to wp-config.php
3. [ ] Update form handler to use constants
4. [ ] Create migration script to remove DB stored keys
5. [ ] Add validation to ensure key is from environment
6. [ ] Document setup instructions for deployment

**Implementation:**
```php
// In wp-config.php or .env (via wp-cli)
define( 'STITCH_HUBSPOT_API_KEY', 'your-api-key-here' );

// In form handler
if ( ! defined( 'STITCH_HUBSPOT_API_KEY' ) ) {
    throw new Exception( 'HubSpot API key not configured' );
}
$api_key = STITCH_HUBSPOT_API_KEY;
```

**Owner:** DevOps / Backend Developer
**Timeline:** Complete by end of Day 1

---

### Issue P0-003: Webhook URL Not Validated
**Impact:** Data Exfiltration, SSRF Risk
**Risk Level:** CRITICAL
**Estimated Effort:** 1.5 hours
**Priority:** HIGHEST

**Current State:**
```php
// /blocks/form/form-handler.php (line 101)
$webhook_url = get_option( 'stitch_form_webhook_url' );
$response = wp_remote_post( $webhook_url, [...] );
// No validation - could be any URL attacker sets
```

**Action Items:**
1. [ ] Create webhook URL validation function
2. [ ] Implement domain whitelist check
3. [ ] Add URL format validation
4. [ ] Add SSL certificate verification
5. [ ] Test with invalid URLs (should fail safely)
6. [ ] Document webhook setup instructions

**Implementation:**
```php
function stitch_validate_webhook_url( $url ) {
    $parsed = parse_url( $url );

    if ( ! $parsed || empty( $parsed['scheme'] ) || empty( $parsed['host'] ) ) {
        throw new Exception( 'Invalid webhook URL format' );
    }

    if ( ! in_array( $parsed['scheme'], [ 'https' ], true ) ) {
        throw new Exception( 'Webhook must use HTTPS' );
    }

    // Whitelist check (required for production)
    $allowed = apply_filters( 'stitch_webhook_allowed_domains', [
        'hooks.slack.com',
        'api.example.com'
    ] );

    if ( ! in_array( $parsed['host'], $allowed, true ) ) {
        throw new Exception( 'Webhook domain not whitelisted' );
    }

    return $url;
}
```

**Owner:** Backend Developer
**Timeline:** Complete by mid-Day 1

---

### Issue P0-004: Inline JavaScript Violates CSP
**Impact:** Content Security Policy Violation
**Risk Level:** CRITICAL
**Estimated Effort:** 2 hours
**Priority:** HIGHEST

**Current State:**
```php
// /blocks/form/render.php (lines 70-101)
<script>
    function handleFormSubmit(event, form) { ... }
</script>
// Inline script in template - breaks CSP headers
```

**Action Items:**
1. [ ] Create `/blocks/form/form-submit.js` external script
2. [ ] Move all form handling logic to external file
3. [ ] Register script with proper dependencies
4. [ ] Add nonce localization
5. [ ] Test form submission works
6. [ ] Verify no inline scripts remain

**Implementation:** See Appendix B in main review

**Owner:** Frontend Developer
**Timeline:** Complete by end of Day 1

---

## HIGH PRIORITY ISSUES (P1 - Must Complete Week 1)

### Issue P1-001: Missing Rate Limiting
**Impact:** DDoS, Spam Attacks
**Effort:** 2 hours
**Timeline:** Day 2

**Checklist:**
- [ ] Implement IP-based rate limiting
- [ ] Set to 5 submissions per hour per IP
- [ ] Add error message for rate limit exceeded
- [ ] Make limits configurable via filters
- [ ] Test with multiple rapid requests

**Code Location:** `/blocks/form/form-handler.php` (add at start of handler)

---

### Issue P1-002: No Form Error Logging
**Impact:** Unable to Debug Form Failures
**Effort:** 1.5 hours
**Timeline:** Day 2

**Checklist:**
- [ ] Add error logging on exceptions
- [ ] Log to WordPress error log
- [ ] Include IP address in logs
- [ ] Include sanitized form field names
- [ ] Create admin dashboard widget to show recent errors
- [ ] Test error logging works

---

### Issue P1-003: Missing ARIA Labels
**Impact:** WCAG 2.1 A Failure
**Effort:** 1.5 hours
**Timeline:** Day 2

**Files Affected:**
- [ ] `/blocks/form/render.php` - Add aria-labelledby
- [ ] `/parts/header.html` - Add role="navigation"
- [ ] Navigation block - Add aria-current for active items

**Test:** Screen reader (NVDA/JAWS) testing

---

### Issue P1-004: Inconsistent Textdomain
**Impact:** Translation System Breaks
**Effort:** 1 hour
**Timeline:** Day 2

**Checklist:**
- [ ] Audit all 7 blocks for textdomain
- [ ] Change 'stitch' to 'stitch-consulting' everywhere
- [ ] Test translation loading works
- [ ] Verify no hardcoded English strings in JS

---

### Issue P1-005: Missing Configuration Notices
**Impact:** Admin Misconfiguration
**Effort:** 1.5 hours
**Timeline:** Day 2

**Checklist:**
- [ ] Create admin notice system
- [ ] Add notice for missing HubSpot API key
- [ ] Add notice for unconfigured webhook
- [ ] Display only to admins
- [ ] Test notices appear/disappear properly

---

### Issue P1-006: No CSRF Protection on Admin Actions
**Impact:** CSRF Attacks
**Effort:** 1 hour
**Timeline:** Day 3

**Checklist:**
- [ ] Add nonce field to any admin forms
- [ ] Verify nonce on form submission
- [ ] Add to theme options if needed
- [ ] Test with invalid nonce (should fail)

---

## MEDIUM PRIORITY IMPROVEMENTS (P2 - Week 2)

| Issue | Effort | Timeline |
|-------|--------|----------|
| Analytics tracking in forms | 1 hour | Day 3 |
| Hero image lazy loading | 1.5 hours | Day 4 |
| i18n strings in JavaScript | 2 hours | Day 4 |
| Unit test suite creation | 4 hours | Day 5 |
| Performance optimization | 3 hours | Week 2 |

---

## IMPLEMENTATION SCHEDULE

### Phase 1: Critical Security Fixes (Days 1-3)

**Day 1: Core Security**
```
Morning (4 hours):
- P0-001: Form input validation implementation
- P0-002: HubSpot API key environment setup
- P0-003: Webhook URL validation

Afternoon (4 hours):
- P0-004: Inline JavaScript extraction
- Code review of P0 fixes
- Initial testing in development
```

**Day 2: Support Systems**
```
Morning (4 hours):
- P1-001: Rate limiting implementation
- P1-002: Error logging system
- P1-003: ARIA labels

Afternoon (4 hours):
- P1-004: Textdomain standardization
- P1-005: Configuration notices
- Bug fixes from initial testing
```

**Day 3: Hardening & Testing**
```
Morning (4 hours):
- P1-006: CSRF protection
- Security header validation
- Database cleaning (remove old API keys)

Afternoon (4 hours):
- Full security testing
- Penetration testing simulation
- Code review by security specialist
```

### Phase 2: Quality Assurance (Days 4-5)

**Day 4: Testing & Validation**
```
Morning (4 hours):
- Automated security scanning
- Accessibility audit (Lighthouse)
- Manual form submission testing

Afternoon (4 hours):
- Cross-browser testing
- Mobile responsiveness check
- Performance profiling
```

**Day 5: Documentation & Deployment**
```
Morning (4 hours):
- Security guide documentation
- Setup instructions for production
- Change log creation

Afternoon (4 hours):
- Staging environment deployment
- Final integration testing
- Deployment checklist verification
```

---

## TESTING REQUIREMENTS

### Security Testing Checklist

- [ ] **SQL Injection Tests**
  - Test form with `' OR '1'='1`
  - Test with script tags in form fields
  - Test with HTML entities
  - Expected: All sanitized/escaped

- [ ] **XSS Prevention Tests**
  - Test with `<script>alert('xss')</script>`
  - Test with `<img src=x onerror=alert()>`
  - Test with event handlers in attributes
  - Expected: All escaped in output

- [ ] **CSRF Protection Tests**
  - Remove nonce and submit form
  - Expected: Request fails

- [ ] **Rate Limiting Tests**
  - Submit form 10 times rapidly
  - Expected: 6th+ request fails with rate limit message

- [ ] **Webhook Security Tests**
  - Try setting webhook to `http://` (should fail)
  - Try setting webhook to random domain (should fail)
  - Expected: Only https and whitelisted domains work

- [ ] **API Key Security Tests**
  - Search WordPress options for exposed keys
  - Check database exports for keys
  - Expected: Keys only in environment variables

### Accessibility Testing Checklist

- [ ] **Keyboard Navigation**
  - Tab through entire form
  - Escape closes any dialogs
  - All interactive elements reachable

- [ ] **Screen Reader Testing**
  - Test with NVDA (Windows)
  - Test with JAWS if available
  - Verify form labels announced correctly
  - Verify error messages announced

- [ ] **Color Contrast**
  - Run Lighthouse accessibility audit
  - Target: 95+ score
  - Check WCAG AA compliance

---

## DEPLOYMENT CHECKLIST

### Pre-Production Steps

- [ ] All P0 issues resolved and tested
- [ ] All P1 issues resolved and tested
- [ ] Security audit passed
- [ ] Code review approved by tech lead
- [ ] Staging environment passes full test suite
- [ ] Performance meets targets (LCP < 2.5s, CLS < 0.1)
- [ ] Accessibility score >= 95 on Lighthouse
- [ ] Database migration for removed keys executed

### Deployment Day

- [ ] Backup production database
- [ ] Deploy to staging - verify functionality
- [ ] Smoke test all forms
- [ ] Check error logs for issues
- [ ] Deploy to production in low-traffic window
- [ ] Monitor error logs for 24 hours
- [ ] Verify all form submissions working

### Post-Deployment

- [ ] 7-day monitoring period
- [ ] Check error logs daily
- [ ] Get user feedback on forms
- [ ] Performance monitoring active
- [ ] Security monitoring enabled

---

## ENVIRONMENT SETUP GUIDE

### Local Development

```bash
# 1. Create .env file in theme root
cp .env.example .env

# 2. Set required variables
STITCH_HUBSPOT_API_KEY="your-api-key-here"
STITCH_FORM_WEBHOOK_URL="https://hooks.slack.com/services/..."

# 3. Add to wp-config.php
require_once dirname(__FILE__) . '/.env.php';

# 4. Create .env.php (not in git)
<?php
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        if (!defined($key)) {
            define($key, $value);
        }
    }
}
```

### Staging/Production

```bash
# 1. Use environment variables directly
export STITCH_HUBSPOT_API_KEY="production-key-here"

# 2. Or use wp-cli
wp config set STITCH_HUBSPOT_API_KEY "production-key-here"

# 3. Verify installation
wp config get STITCH_HUBSPOT_API_KEY
```

---

## ROLLBACK PLAN

If critical issues discovered in production:

1. **Immediate Actions**
   - Disable form block via plugin or conditional
   - Revert to previous theme version
   - Notify all stakeholders

2. **Recovery Steps**
   ```bash
   wp theme rollback stitch-consulting-theme
   wp cache flush
   # Verify site functionality
   ```

3. **Analysis & Fixes**
   - Reproduce issue in staging
   - Apply fixes
   - Full test cycle before re-deploy

---

## SUCCESS CRITERIA

### Phase 1 Complete When:

✓ All 4 P0 issues resolved
✓ All 6 P1 issues resolved
✓ Security testing all passed
✓ Code reviewed and approved

### Production Ready When:

✓ P0 + P1 + Critical P2 issues resolved
✓ Full test suite passed
✓ Staging environment validated
✓ Accessibility score >= 95
✓ Security scan: PASS
✓ Performance targets met
✓ Team sign-off obtained

---

## SIGN-OFF

| Role | Status | Date |
|------|--------|------|
| Developer | Assigned | - |
| QA Lead | Pending | - |
| Security | Pending | - |
| Project Manager | Pending | - |

---

## APPENDICES

### Appendix A: File Modification Summary

**Files Requiring Changes:**
1. `/blocks/form/form-handler.php` - Add validation, logging, rate limiting
2. `/blocks/form/render.php` - Move inline script, add ARIA labels
3. `/blocks/form/form-submit.js` - NEW FILE
4. `/inc/form-validation.php` - NEW FILE
5. `/functions.php` - Add script enqueuing, configuration checks
6. All `/blocks/*/block.json` - Update textdomain
7. `/blocks/form/block.json` - Update textdomain
8. `.env.example` - NEW FILE
9. `wp-config.php` - Add environment variable loading

**No Changes Needed:**
- CSS files (secure as-is)
- Template HTML (secure after P0 fixes)
- Navigation system (secure as-is)

### Appendix B: Test Case Examples

**Form Injection Test Case:**
```
Input: '; DROP TABLE wp_posts; --
Expected Output: Safely escaped, no SQL execution
Test Result: [ PASS / FAIL ]
```

**XSS Test Case:**
```
Input: <img src=x onerror="alert('xss')">
Expected Output: Escaped in HTML context
Test Result: [ PASS / FAIL ]
```

**Rate Limit Test Case:**
```
Rapid Submissions: 10 in 2 seconds
Expected: First 5 succeed, 6-10 fail with rate limit message
Test Result: [ PASS / FAIL ]
```

---

**Report Generated:** 2025-12-05
**Prepared For:** Development Team & Project Stakeholders
**Next Review:** Upon completion of Day 3 security fixes

