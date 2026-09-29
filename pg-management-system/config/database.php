<?php
/**
 * StayFlow PG Management SaaS - Production Database Connection Manager
 * 
 * Production-ready database connector that:
 * - Reads DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT from environment variables (Fly.io secrets, Docker ENV, etc.)
 * - Parses DATABASE_URL / MYSQL_URL connection strings if provided
 * - Supports local .env file parsing as fallback
 * - NEVER exposes passwords or credentials in the browser
 * - Uses robust PDO with proper error handling and connection pooling
 * - Safe for ALL PHP pages across the application
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// ----------------------------------------------------
// 1. Helper to Parse .env Files if Not Loaded in ENV
// ----------------------------------------------------
if (!function_exists('stayflow_load_env')) {
    function stayflow_load_env(string $filePath): void {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return;
        }
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (strpos($line, '=') !== false) {
                [$key, $val] = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val);
                // Strip enclosing single/double quotes
                if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                    (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                    $val = substr($val, 1, -1);
                }
                if (!array_key_exists($key, $_ENV) && getenv($key) === false) {
                    putenv("{$key}={$val}");
                    $_ENV[$key] = $val;
                    $_SERVER[$key] = $val;
                }
            }
        }
    }
}

// Attempt to load .env from standard project locations
$possibleEnvPaths = [
    __DIR__ . '/../.env',
    __DIR__ . '/../../.env',
    dirname(__DIR__, 2) . '/.env',
    dirname(__DIR__, 2) . '/pg-management-system/.env',
    ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/.env',
    ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/pg-management-system/.env',
];
foreach ($possibleEnvPaths as $envPath) {
    if (!empty($envPath) && file_exists($envPath)) {
        stayflow_load_env($envPath);
        break;
    }
}

// Load Supabase Connector Configuration
if (file_exists(__DIR__ . '/../../config/supabase.php')) {
    require_once __DIR__ . '/../../config/supabase.php';
} elseif (file_exists(__DIR__ . '/../config/supabase.php')) {
    require_once __DIR__ . '/../config/supabase.php';
}

// ----------------------------------------------------
// 2. Resolve Database Connection Settings from ENV
// ----------------------------------------------------
if (!function_exists('stayflow_env')) {
    function stayflow_env(string $key, ?string $default = null): ?string {
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

$dbHost = stayflow_env('DB_HOST');
$dbName = stayflow_env('DB_NAME');
$dbUser = stayflow_env('DB_USER');
$dbPass = stayflow_env('DB_PASS', stayflow_env('DB_PASSWORD', ''));
$dbPort = (int)(stayflow_env('DB_PORT', '3306') ?: 3306);
$dbCharset = stayflow_env('DB_CHARSET', 'utf8mb4') ?: 'utf8mb4';

// Check for DATABASE_URL / MYSQL_URL (common in Fly.io, Heroku, Render)
$databaseUrl = stayflow_env('DATABASE_URL') ?: stayflow_env('MYSQL_URL');
if ($databaseUrl) {
    $parsed = parse_url($databaseUrl);
    if (is_array($parsed)) {
        if (!empty($parsed['host'])) $dbHost = $parsed['host'];
        if (!empty($parsed['port'])) $dbPort = (int)$parsed['port'];
        if (!empty($parsed['user'])) $dbUser = urldecode($parsed['user']);
        if (isset($parsed['pass'])) $dbPass = urldecode($parsed['pass']);
        if (!empty($parsed['path'])) $dbName = ltrim($parsed['path'], '/');
    }
}

// Define Constants if Not Already Defined
if (!defined('DB_HOST')) define('DB_HOST', $dbHost ?: '127.0.0.1');
if (!defined('DB_NAME')) define('DB_NAME', $dbName ?: 'stayflow');
if (!defined('DB_USER')) define('DB_USER', $dbUser ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', $dbPass ?: '');
if (!defined('DB_PORT')) define('DB_PORT', $dbPort);
if (!defined('DB_CHARSET')) define('DB_CHARSET', $dbCharset);

if (!defined('APP_NAME')) define('APP_NAME', stayflow_env('APP_NAME', 'StayFlow PG Management System'));
if (!defined('APP_ENV')) define('APP_ENV', stayflow_env('APP_ENV', 'production'));
if (!defined('APP_DEBUG')) define('APP_DEBUG', filter_var(stayflow_env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));
$resolvedAppUrl = stayflow_env('APP_URL', 'https://stayflow.antideploy.app');
if (empty($resolvedAppUrl) || str_contains($resolvedAppUrl, 'fly.dev') || str_contains($resolvedAppUrl, 'ad-stayflow')) {
    $resolvedAppUrl = 'https://stayflow.antideploy.app';
}
if (!defined('APP_URL')) define('APP_URL', rtrim($resolvedAppUrl, '/'));
if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', (int)(stayflow_env('SESSION_LIFETIME', '604800') ?: 604800));

// ----------------------------------------------------
// 3. Singleton Database Connection Providers
// ----------------------------------------------------

/**
 * Returns a shared PDO instance.
 * Throws PDOException on failure so caller can catch it and handle gracefully.
 * Never exposes credentials to browser output.
 */
function getDB(): PDO {
    static $pdoInstance = null;

    if ($pdoInstance instanceof PDO) {
        return $pdoInstance;
    }

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 5,
    ];

    // 1. Supabase Direct Connection (PostgreSQL Pooler)
    if (class_exists('SupabaseDatabase')) {
        $supabaseDb = SupabaseDatabase::getInstance();
        $supabasePdo = $supabaseDb->getPDO();
        if ($supabasePdo instanceof PDO) {
            $pdoInstance = $supabasePdo;
            return $pdoInstance;
        }
    }

    // 2. MySQL / MariaDB Connection
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    try {
        $pdoInstance = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdoInstance;
    } catch (\PDOException $e) {
        error_log(sprintf('[StayFlow DB Error] Failed connecting to database host "%s", DB "%s": %s', DB_HOST, DB_NAME, $e->getMessage()));
        // Re-throw cleanly without leaking credentials
        throw new \PDOException("Database connection could not be established to " . htmlspecialchars(DB_HOST) . ". Please check credentials.", (int)$e->getCode());
    }
}

/**
 * Returns the SupabaseDatabase instance for direct Supabase operations.
 */
function getSupabase(): SupabaseDatabase {
    return SupabaseDatabase::getInstance();
}

/**
 * Returns a shared MySQLi connection if needed by legacy scripts.
 */
function getMysqli(): mysqli {
    static $mysqliInstance = null;

    if ($mysqliInstance instanceof mysqli && @$mysqliInstance->ping()) {
        return $mysqliInstance;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $mysqliInstance = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        $mysqliInstance->set_charset(DB_CHARSET);
        return $mysqliInstance;
    } catch (\mysqli_sql_exception $e) {
        error_log(sprintf('[StayFlow DB Error] MySQLi connection error on host "%s": %s', DB_HOST, $e->getMessage()));
        throw new \RuntimeException("MySQLi database connection failed.", (int)$e->getCode());
    }
}

// ----------------------------------------------------
// 4. Common Authentication & Tenant Helpers
// ----------------------------------------------------
if (!function_exists('sendJSON')) {
    function sendJSON(array $data, int $statusCode = 200): void {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
}

if (!function_exists('getAuthUser')) {
    function getAuthUser(): ?array {
        if (!empty($_SESSION['user_id']) && !empty($_SESSION['user'])) {
            return $_SESSION['user'];
        }
        return null;
    }
}

if (!function_exists('requireAuth')) {
    function requireAuth(): array {
        $user = getAuthUser();
        if (!$user) {
            $returnTo = urlencode($_SERVER['REQUEST_URI'] ?? '/');
            header('Location: /login.php?redirect=' . $returnTo);
            exit();
        }
        return $user;
    }
}

// TenantContext Class Stub for multi-tenant administration
if (!class_exists('TenantContext')) {
    class TenantContext {
        public static function getOrgId(): int {
            return (int)($_SESSION['org_id'] ?? 1);
        }
        public static function setOrgId(int $id): void {
            $_SESSION['org_id'] = $id;
        }
        public static function getBuildingId(): ?int {
            return isset($_SESSION['building_id']) ? (int)$_SESSION['building_id'] : null;
        }
        public static function setBuildingId(int $id): bool {
            $_SESSION['building_id'] = $id;
            return true;
        }
        public static function isSuperAdmin(): bool {
            return ($_SESSION['user_role_code'] ?? '') === 'super_admin';
        }
        public static function isImpersonating(): bool {
            return !empty($_SESSION['impersonating']);
        }
        public static function getUserBuildings(): array {
            return [];
        }
        public static function getOrgInfo(): array {
            return [
                'company_name' => 'StayFlow Management',
                'plan_name'    => 'Enterprise'
            ];
        }
        public static function getBuildingInfo(): array {
            return [
                'building_name' => 'Main Campus'
            ];
        }
    }
}

// AuthService Helper Class
if (!class_exists('AuthService')) {
    class AuthService {
        public static function getHomeRoute(array $user): string {
            $role = $user['role_code'] ?? ($user['role'] ?? 'staff');
            if ($role === 'super_admin') return '/super-admin/index.php';
            if ($role === 'tenant') return '/resident/index.php';
            return '/admin/index.php';
        }
        public static function attemptLogin(string $identifier, string $password): array {
            // 1. If Supabase Auth is configured, attempt Supabase Auth when email is provided
            if (function_exists('isSupabaseConfigured') && isSupabaseConfigured() && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                try {
                    $auth = new SupabaseAuth();
                    $res = $auth->signIn($identifier, $password);
                    if ($res['success']) {
                        $db = getDB();
                        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
                        $stmt->execute([$identifier]);
                        $user = $stmt->fetch();
                        if ($user) {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['user'] = $user;
                            $_SESSION['user_role_code'] = $user['role_code'] ?? 'admin';
                            return ['success' => true, 'user' => $user];
                        }
                    }
                } catch (\Throwable $e) {
                    error_log("[SupabaseAuth Login Notice] " . $e->getMessage());
                }
            }

            // 2. Local Database & Password Hash check
            try {
                $db = getDB();
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR username = ? OR phone = ? LIMIT 1");
                $stmt->execute([$identifier, $identifier, $identifier]);
                $user = $stmt->fetch();
                if ($user && password_verify($password, $user['password_hash'] ?? '')) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user'] = $user;
                    $_SESSION['user_role_code'] = $user['role_code'] ?? 'admin';
                    return ['success' => true, 'user' => $user];
                }
            } catch (\Throwable $e) {
                error_log("[AuthService DB Error] " . $e->getMessage());
            }
            return ['success' => false, 'message' => 'Invalid username/email or password.'];
        }
    }
}
