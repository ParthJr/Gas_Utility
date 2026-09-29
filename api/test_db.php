<?php
/**
 * Diagnostic Database Connection Tester for StayFlow Deployment
 * Visit: /api/test_db.php
 */

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../config/database.php';

echo "<!DOCTYPE html><html><head><title>Database Connection Test</title>";
echo "<style>body{font-family:sans-serif;background:#0f172a;color:#fff;padding:30px;} .card{background:#1e293b;border-radius:12px;padding:24px;max-width:600px;margin:auto;} .success{color:#4ade80;} .error{color:#f87171;} .info{background:#334155;padding:12px;border-radius:8px;font-family:monospace;font-size:13px;margin:12px 0;}</style></head><body>";

echo "<div class='card'>";
echo "<h2>🔍 PG-Core Engine — Database Connection Diagnostic</h2>";

echo "<div class='info'>";
echo "DB_HOST: " . htmlspecialchars(DB_HOST) . "<br>";
echo "DB_NAME: " . htmlspecialchars(DB_NAME) . "<br>";
echo "DB_USER: " . htmlspecialchars(DB_USER) . "<br>";
echo "DB_PASS: " . (strlen(DB_PASS) > 0 ? "•••••••• (" . strlen(DB_PASS) . " chars)" : "<span class='error'>EMPTY</span>") . "<br>";
echo "</div>";

try {
    $pdo = getDB();

    echo "<h3 class='success'>✅ Master Database Connected Successfully via getDB()!</h3>";

    // Check Tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        echo "<p class='error'>⚠️ Connected to database, but <strong>NO TABLES</strong> found!</p>";
        echo "<p>Please import <code>database/full_cpanel_database_backup.sql</code> into phpMyAdmin.</p>";
    } else {
        echo "<p class='success'>Found <strong>" . count($tables) . " tables</strong> in database:</p>";
        echo "<div class='info'>" . implode(', ', $tables) . "</div>";

        // Check Users
        if (in_array('users', $tables)) {
            $userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            echo "<p class='success'>Found <strong>{$userCount} users</strong> in users table.</p>";
        }
    }

} catch (PDOException $e) {
    echo "<h3 class='error'>❌ Connection Failed!</h3>";
    echo "<p class='error'><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<hr style='border-color:#475569;'>";
    echo "<h4>How to fix:</h4>";
    echo "<ol style='font-size:13px;line-height:1.6;color:#cbd5e1;'>";
    echo "<li>Verify database host, database name, and user credentials in your environment variables (<code>DB_HOST</code>, <code>DB_NAME</code>, <code>DB_USER</code>, <code>DB_PASS</code>).</li>";
    echo "<li>If running on Fly.io, set them with <code>fly secrets set DB_HOST=... DB_NAME=... DB_USER=... DB_PASS=...</code></li>";
    echo "<li>Make sure the remote MySQL host permits incoming connections from Fly.io / production servers.</li>";
    echo "</ol>";
}

echo "</div></body></html>";
