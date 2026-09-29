<?php
/**
 * StayFlow PG Management SaaS - Database & Supabase Diagnostic Tester
 * Visit: /api/test_db.php
 */

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/supabase.php';

echo "<!DOCTYPE html><html><head><title>StayFlow Database & Supabase Diagnostic</title>";
echo "<style>
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#090d16;color:#e2e8f0;padding:30px;line-height:1.5;} 
.card{background:#111827;border:1px solid #1f2937;border-radius:12px;padding:28px;max-width:720px;margin:auto;box-shadow:0 10px 25px -5px rgba(0,0,0,0.5);} 
h2{margin-top:0;color:#f8fafc;font-size:22px;}
.badge{display:inline-block;padding:3px 10px;border-radius:9999px;font-size:12px;font-weight:600;margin-left:8px;}
.badge-green{background:#064e3b;color:#34d399;}
.badge-blue{background:#1e3a8a;color:#60a5fa;}
.badge-yellow{background:#78350f;color:#fbbf24;}
.badge-red{background:#7f1d1d;color:#f87171;}
.success{color:#34d399;font-weight:600;} 
.error{color:#f87171;} 
.info{background:#1e293b;padding:14px;border-radius:8px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:13px;margin:14px 0;border:1px solid #334155;}
.section{margin-top:24px;border-top:1px solid #1f2937;padding-top:16px;}
</style></head><body>";

echo "<div class='card'>";
echo "<h2>StayFlow Database & Supabase Diagnostics</h2>";

// 1. Supabase Connector Status
echo "<div class='section'>";
echo "<h3>1. Supabase Connector</h3>";

$supabaseDb = SupabaseDatabase::getInstance();
$health = $supabaseDb->checkHealth();

echo "<div class='info'>";
echo "SUPABASE_URL: " . (!empty(SUPABASE_URL) ? htmlspecialchars(SUPABASE_URL) : "<span class='error'>Not set</span>") . "<br>";
echo "ANON_KEY: " . (!empty(SUPABASE_ANON_KEY) ? "Configured (••••••••" . substr(SUPABASE_ANON_KEY, -6) . ")" : "<span class='error'>Not set</span>") . "<br>";
echo "SERVICE_ROLE_KEY: " . (!empty(SUPABASE_SERVICE_ROLE_KEY) ? "Configured & Protected (••••••••" . substr(SUPABASE_SERVICE_ROLE_KEY, -6) . ")" : "<span class='error'>Not set</span>") . "<br>";
echo "DIRECT_PG_HOST: " . (!empty(SUPABASE_DB_HOST) ? htmlspecialchars(SUPABASE_DB_HOST) : "<span class='badge badge-yellow'>Optional</span>") . "<br>";
echo "STATUS: " . htmlspecialchars($health['message']) . " (" . $health['latency_ms'] . " ms)<br>";
echo "</div>";

if ($health['rest_api'] || $health['direct_postgres']) {
    echo "<p class='success'>✅ Supabase Connector is Active & Healthy!</p>";
} else {
    echo "<p style='color:#94a3b8;font-size:13px;'>ℹ️ Configure <code>SUPABASE_URL</code>, <code>SUPABASE_ANON_KEY</code>, and <code>SUPABASE_SERVICE_ROLE_KEY</code> in AntiDeploy to activate Supabase.</p>";
}
echo "</div>";

// 2. PDO Database Connection Test
echo "<div class='section'>";
echo "<h3>2. Active Database Connection (getDB)</h3>";

try {
    $pdo = getDB();
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    echo "<p class='success'>✅ Database Connected via PDO Driver: <span class='badge badge-blue'>" . strtoupper($driver) . "</span></p>";

    // Fetch Tables
    if ($driver === 'pgsql') {
        $stmt = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    echo "<p>Found <strong>" . count($tables) . " tables</strong> in database.</p>";
    if (!empty($tables)) {
        echo "<div class='info' style='max-height:140px;overflow-y:auto;'>" . implode(', ', $tables) . "</div>";
    }

    // Check Users
    if (in_array('users', $tables)) {
        $userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "<p class='success'>✅ Users Table verified: <strong>{$userCount} registered users</strong>.</p>";
    }
    
    // Check Plans
    if (in_array('plans', $tables)) {
        $planCount = (int)$pdo->query("SELECT COUNT(*) FROM plans WHERE status = 'active'")->fetchColumn();
        echo "<p class='success'>✅ Plans Table verified: <strong>{$planCount} active subscription plans</strong>.</p>";
    }

} catch (\Throwable $e) {
    echo "<p class='error'>❌ Database Connection Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

echo "</div></body></html>";
