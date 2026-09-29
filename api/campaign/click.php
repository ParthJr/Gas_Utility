<?php
/**
 * PG-Core Engine — Promotional Poster Click Analytics Redirect Router
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/PosterService.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$posterId = (int)($_GET['id'] ?? 0);
$orgId = (int)($_GET['org_id'] ?? 0);
$userId = (int)($_SESSION['user_id'] ?? 0);

if ($posterId > 0) {
    PosterService::recordClick($posterId, $orgId ?: null, $userId ?: null);
}

$redirect = $_GET['redirect'] ?? '';
if (empty($redirect)) {
    $redirect = '../../admin/index.php';
}

header('Location: ' . $redirect);
exit();
