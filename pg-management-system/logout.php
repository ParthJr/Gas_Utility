<?php
/**
 * StayFlow PG SaaS — Universal Root Logout Handler (/logout.php)
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/services/AuthService.php';

// Perform secure global logout (session destroy, token revocation, cookie purging)
AuthService::logout();

// Determine if request expects JSON response
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
    || (isset($_GET['format']) && $_GET['format'] === 'json')
    || (!empty($_POST['action']) && $_POST['action'] === 'logout');

$targetUrl = '/login.php';

if ($isAjax) {
    sendJSON([
        'success' => true,
        'message' => 'Logged out successfully.',
        'redirect' => $targetUrl
    ]);
}

header('Location: ' . $targetUrl);
exit();
