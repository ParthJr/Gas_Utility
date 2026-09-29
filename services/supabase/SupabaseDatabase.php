<?php
/**
 * StayFlow PG Management SaaS - Supabase Database Connector
 * 
 * Provides unified database access to Supabase:
 * - Direct PostgreSQL PDO connection with SSL and connection pooling
 * - Fallback / REST API query bridge via PostgREST
 * - Health verification and diagnostic telemetry
 */

declare(strict_types=1);

require_once __DIR__ . '/SupabaseClient.php';

class SupabaseDatabase {
    private static ?SupabaseDatabase $instance = null;
    private ?PDO $pdo = null;
    private SupabaseClient $client;

    private function __construct() {
        $this->client = new SupabaseClient();
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getClient(): SupabaseClient {
        return $this->client;
    }

    /**
     * Returns a fluent query builder for a Supabase table.
     */
    public function table(string $name): SupabaseQueryBuilder {
        return $this->client->from($name);
    }

    /**
     * Returns a native PDO PostgreSQL connection to Supabase if configured and supported.
     */
    public function getPDO(): ?PDO {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $host = defined('SUPABASE_DB_HOST') ? SUPABASE_DB_HOST : '';
        $port = defined('SUPABASE_DB_PORT') ? (int)SUPABASE_DB_PORT : 6543;
        $dbname = defined('SUPABASE_DB_NAME') ? SUPABASE_DB_NAME : 'postgres';
        $user = defined('SUPABASE_DB_USER') ? SUPABASE_DB_USER : 'postgres';
        $pass = defined('SUPABASE_DB_PASS') ? SUPABASE_DB_PASS : '';

        if (empty($host) || empty($pass)) {
            return null;
        }

        // Verify PDO pgsql driver is loaded
        if (!in_array('pgsql', PDO::getAvailableDrivers(), true)) {
            error_log('[StayFlow Supabase Warning] pdo_pgsql driver is not loaded in PHP. Using REST API fallback.');
            return null;
        }

        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s;sslmode=require',
            $host,
            $port,
            $dbname
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
            return $this->pdo;
        } catch (\PDOException $e) {
            error_log('[StayFlow Supabase Error] PostgreSQL connection failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Performs a health check against the Supabase connector.
     */
    public function checkHealth(): array {
        $start = microtime(true);
        $status = [
            'provider' => 'Supabase',
            'configured' => isSupabaseConfigured(),
            'rest_api' => false,
            'direct_postgres' => false,
            'latency_ms' => 0,
            'message' => 'Pending check'
        ];

        if (!$status['configured']) {
            $status['message'] = 'Supabase environment variables not configured.';
            return $status;
        }

        // 1. Check REST API
        $res = $this->client->from('plans')->select('id')->limit(1)->execute();
        if ($res['success']) {
            $status['rest_api'] = true;
            $status['message'] = 'Connected via Supabase PostgREST API.';
        } elseif ($res['status'] === 401 || $res['status'] === 403) {
            $status['message'] = 'Supabase reachable, but API key authentication failed: ' . ($res['error'] ?? '');
        }

        // 2. Check Direct PostgreSQL PDO
        $pdo = $this->getPDO();
        if ($pdo) {
            try {
                $stmt = $pdo->query('SELECT 1');
                if ($stmt && $stmt->fetchColumn() == 1) {
                    $status['direct_postgres'] = true;
                    $status['message'] = 'Connected via Supabase PostgreSQL Direct Pooler.';
                }
            } catch (\Throwable $e) {
                // Keep error in log
            }
        }

        $status['latency_ms'] = round((microtime(true) - $start) * 1000, 2);
        return $status;
    }
}
