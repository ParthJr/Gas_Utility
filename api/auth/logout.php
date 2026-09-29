<?php
/**
 * StayFlow PG SaaS — API Auth Logout Handler (/api/auth/logout.php)
 * Handles programmatic/AJAX API logout and returns structured JSON responses.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/AuthService.php';

// Perform secure global logout
AuthService::logout();

$targetUrl = '/login.php';

// If called via direct browser navigation without JSON accept
if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false && !isset($_GET['format'])) {
    header('Location: ' . $targetUrl);
    exit();
}

sendJSON([
    'success' => true,
    'message' => 'Logged out successfully.',
    'redirect' => $targetUrl
]);
