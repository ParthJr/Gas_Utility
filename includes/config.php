<?php
/**
 * StayFlow SaaS Platform Configuration
 * Central configuration for SaaS authentication & module entry points.
 * Connects the StayFlow marketing website directly to the real SaaS platform at pg-management-system/
 */

if (!defined('CANONICAL_DOMAIN')) {
    define('CANONICAL_DOMAIN', 'https://stayflow.antideploy.app');
}
if (!defined('SITE_URL')) {
    define('SITE_URL', 'https://stayflow.antideploy.app');
}
if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

// Platform domain locked to https://stayflow.antideploy.app

if (!defined('SAAS_BASE_URL')) {
    $env_base = getenv('SAAS_BASE_URL');
    if ($env_base !== false && !empty($env_base)) {
        define('SAAS_BASE_URL', rtrim($env_base, '/'));
    } else {
        // Use relative path so browser never leaks internal deployment/fly.dev host
        define('SAAS_BASE_URL', '/pg-management-system');
    }
}

// SaaS Authentication & Onboarding (Relative paths keep user on public domain)
if (!defined('SAAS_LOGIN_URL')) {
    define('SAAS_LOGIN_URL', '/login.php');
}
if (!defined('SAAS_SIGNUP_URL')) {
    define('SAAS_SIGNUP_URL', '/onboarding/wizard.php');
}
if (!defined('SAAS_FORGOT_URL')) {
    define('SAAS_FORGOT_URL', '/forgot_password.php');
}

// SaaS Role Dashboards
if (!defined('SAAS_DASHBOARD_URL')) {
    define('SAAS_DASHBOARD_URL', '/admin/index.php');
}
if (!defined('SAAS_SUPER_ADMIN_URL')) {
    define('SAAS_SUPER_ADMIN_URL', '/super-admin/index.php');
}
if (!defined('SAAS_RESIDENT_PORTAL_URL')) {
    define('SAAS_RESIDENT_PORTAL_URL', '/resident/index.php');
}

// SaaS Module Routes
if (!defined('SAAS_PROPERTIES_URL')) {
    define('SAAS_PROPERTIES_URL', '/admin/buildings.php');
}
if (!defined('SAAS_ROOMS_URL')) {
    define('SAAS_ROOMS_URL', '/admin/rooms.php');
}
if (!defined('SAAS_RESIDENTS_URL')) {
    define('SAAS_RESIDENTS_URL', '/admin/tenants.php');
}
if (!defined('SAAS_BILLING_URL')) {
    define('SAAS_BILLING_URL', '/admin/billing/invoices.php');
}
if (!defined('SAAS_PAYMENTS_URL')) {
    define('SAAS_PAYMENTS_URL', '/admin/billing/payments.php');
}
if (!defined('SAAS_COMPLAINTS_URL')) {
    define('SAAS_COMPLAINTS_URL', '/admin/complaints.php');
}
if (!defined('SAAS_STAFF_URL')) {
    define('SAAS_STAFF_URL', '/admin/staff.php');
}
if (!defined('SAAS_GATEPASS_URL')) {
    define('SAAS_GATEPASS_URL', '/admin/gatepass.php');
}
if (!defined('SAAS_MEALS_URL')) {
    define('SAAS_MEALS_URL', '/admin/meals.php');
}
if (!defined('SAAS_REPORTS_URL')) {
    define('SAAS_REPORTS_URL', '/admin/billing/reports.php');
}

// StayFlow WhatsApp Contact
if (!defined('WHATSAPP_PHONE')) {
    define('WHATSAPP_PHONE', '+91 7622008118');
}
if (!defined('WHATSAPP_URL')) {
    define('WHATSAPP_URL', 'https://wa.me/917622008118');
}
