<?php
/**
 * API: Secure KYC Document Viewer / Downloader Endpoint
 * Enforces authorization checks before streaming private tenant KYC files.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/TenantContext.php';

$userId = (int)($_GET['user_id'] ?? 0);
$type = strtolower(trim($_GET['type'] ?? 'aadhaar'));

$auth = getAuthUser();
if (!$auth) {
    http_response_code(401);
    die("Unauthorized access.");
}

$db = getDB();
$stmt = $db->prepare("SELECT id, organization_id, aadhaar_doc_path, pan_doc_path FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$userId]);
$userRec = $stmt->fetch();

if (!$userRec) {
    http_response_code(404);
    die("User record not found.");
}

// Authorization Enforcement:
// Only the tenant themselves, an admin of the same organization, or a super admin can view the document.
$isSelf = ((int)$auth['id'] === $userId);
$isSameOrgAdmin = ($auth['role'] === 'admin' && (int)$auth['organization_id'] === (int)$userRec['organization_id']);
$isSuper = TenantContext::isSuperAdmin();

if (!$isSelf && !$isSameOrgAdmin && !$isSuper) {
    http_response_code(403);
    die("Forbidden. You do not have permission to view this KYC document.");
}

$filePathRel = ($type === 'pan') ? $userRec['pan_doc_path'] : $userRec['aadhaar_doc_path'];
if (empty($filePathRel)) {
    http_response_code(404);
    die("KYC document not found.");
}

$uploadDir = realpath(__DIR__ . '/../../uploads/kyc');
$fullPath = realpath(__DIR__ . '/../../' . ltrim($filePathRel, '/'));

if (!$fullPath || !file_exists($fullPath) || (strpos($fullPath, realpath(__DIR__ . '/../../uploads')) !== 0)) {
    http_response_code(404);
    die("File does not exist on disk.");
}

$mime = mime_content_type($fullPath) ?: 'application/octet-stream';
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($fullPath));
header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
readfile($fullPath);
exit();
