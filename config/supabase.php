<?php
/**
 * StayFlow PG Management SaaS - Supabase Connector Configuration
 * 
 * Central configuration layer for Supabase integration:
 * - Securely resolves Supabase API and Database credentials from environment variables.
 * - Supports AntiDeploy native Supabase integration, Fly.io secrets, Docker ENV, and .env files.
 * - Protects service_role secret from being exposed to the browser.
 * - Provides connection validation and health check utilities.
 */

declare(strict_types=1);

if (!function_exists('supabase_env')) {
    function supabase_env(string $key, ?string $default = null): ?string {
        $val = getenv($key);
        if ($val !== false && $val !== '') {
            return (string)$val;
        }
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string)$_ENV[$key];
        }
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string)$_SERVER[$key];
        }
        return $default;
    }
}

// --------------------------------------------------------------------------
// 1. Resolve Supabase Environment Variables
// --------------------------------------------------------------------------
$supabaseUrl = supabase_env('SUPABASE_URL');
$supabaseAnonKey = supabase_env('SUPABASE_ANON_KEY');
$supabaseServiceKey = supabase_env('SUPABASE_SERVICE_ROLE_KEY') ?: supabase_env('SUPABASE_SECRET_KEY');

// Supabase PostgreSQL Direct Pooler Connection Credentials
$supabaseDbHost = supabase_env('SUPABASE_DB_HOST');
$supabaseDbPort = (int)(supabase_env('SUPABASE_DB_PORT', '6543') ?: 6543);
$supabaseDbName = supabase_env('SUPABASE_DB_NAME', 'postgres') ?: 'postgres';
$supabaseDbUser = supabase_env('SUPABASE_DB_USER', 'postgres');
$supabaseDbPass = supabase_env('SUPABASE_DB_PASS') ?: supabase_env('SUPABASE_DB_PASSWORD', '');

// Parse DATABASE_URL / SUPABASE_DATABASE_URL if provided by AntiDeploy
$supabaseDatabaseUrl = supabase_env('DATABASE_URL') ?: supabase_env('SUPABASE_DATABASE_URL');
if (!empty($supabaseDatabaseUrl) && (str_starts_with($supabaseDatabaseUrl, 'postgres://') || str_starts_with($supabaseDatabaseUrl, 'postgresql://'))) {
    $parsed = parse_url($supabaseDatabaseUrl);
    if (is_array($parsed)) {
        if (!empty($parsed['host'])) $supabaseDbHost = $parsed['host'];
        if (!empty($parsed['port'])) $supabaseDbPort = (int)$parsed['port'];
        if (!empty($parsed['user'])) $supabaseDbUser = urldecode($parsed['user']);
        if (isset($parsed['pass'])) $supabaseDbPass = urldecode($parsed['pass']);
        if (!empty($parsed['path'])) $supabaseDbName = ltrim($parsed['path'], '/');
    }
}

// --------------------------------------------------------------------------
// 2. Define Constants Safely
// --------------------------------------------------------------------------
if (!defined('SUPABASE_URL')) {
    define('SUPABASE_URL', rtrim((string)$supabaseUrl, '/'));
}
if (!defined('SUPABASE_ANON_KEY')) {
    define('SUPABASE_ANON_KEY', (string)$supabaseAnonKey);
}
if (!defined('SUPABASE_SERVICE_ROLE_KEY')) {
    define('SUPABASE_SERVICE_ROLE_KEY', (string)$supabaseServiceKey);
}
if (!defined('SUPABASE_DB_HOST')) {
    define('SUPABASE_DB_HOST', (string)$supabaseDbHost);
}
if (!defined('SUPABASE_DB_PORT')) {
    define('SUPABASE_DB_PORT', $supabaseDbPort);
}
if (!defined('SUPABASE_DB_NAME')) {
    define('SUPABASE_DB_NAME', (string)$supabaseDbName);
}
if (!defined('SUPABASE_DB_USER')) {
    define('SUPABASE_DB_USER', (string)$supabaseDbUser);
}
if (!defined('SUPABASE_DB_PASS')) {
    define('SUPABASE_DB_PASS', (string)$supabaseDbPass);
}

// --------------------------------------------------------------------------
// 3. Security & Utility Helper Functions
// --------------------------------------------------------------------------

/**
 * Check if Supabase connection credentials are configured.
 */
function isSupabaseConfigured(): bool {
    return (!empty(SUPABASE_URL) && (!empty(SUPABASE_ANON_KEY) || !empty(SUPABASE_SERVICE_ROLE_KEY)))
        || (!empty(SUPABASE_DB_HOST) && !empty(SUPABASE_DB_PASS));
}

/**
 * Returns safe Supabase configuration for client use (NEVER includes service role key).
 */
function getSupabaseClientConfig(): array {
    return [
        'url' => SUPABASE_URL,
        'anon_key' => SUPABASE_ANON_KEY,
        'is_configured' => isSupabaseConfigured()
    ];
}

/**
 * Autoload the Supabase Services
 */
require_once __DIR__ . '/../services/supabase/SupabaseClient.php';
require_once __DIR__ . '/../services/supabase/SupabaseAuth.php';
require_once __DIR__ . '/../services/supabase/SupabaseDatabase.php';
