<?php
/**
 * API: Update Resident Profile & Contract Terms (Persistent & Atomic)
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/ResidentService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

$admin = requireAuth('admin');
$orgId = TenantContext::getOrgId();

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$tenantId = (int)($input['tenant_id'] ?? $input['id'] ?? 0);

if ($tenantId <= 0) {
    sendJSON(['success' => false, 'message' => 'Valid Resident ID is required.'], 400);
}

// Handle KYC Document Photo Upload if present in $_FILES
if (!empty($_FILES['kyc_document_photo']['name']) && $_FILES['kyc_document_photo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['kyc_document_photo'];
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowedExts)) {
        sendJSON(['success' => false, 'message' => 'Invalid file format. Only JPG, JPEG, PNG, and WEBP are allowed.'], 400);
    }
    
    if ($file['size'] > 5242880) { // 5MB
        sendJSON(['success' => false, 'message' => 'File size exceeds maximum limit of 5 MB.'], 400);
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $allowedMimes)) {
        sendJSON(['success' => false, 'message' => 'Invalid file content. Please upload a genuine image file.'], 400);
    }
    
    $uploadKycDir = __DIR__ . '/../../uploads/kyc';
    if (!is_dir($uploadKycDir)) {
        @mkdir($uploadKycDir, 0755, true);
    }
    
    $filename = 'kyc_' . $tenantId . '_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $uploadKycDir . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        sendJSON(['success' => false, 'message' => 'Failed to save uploaded KYC document photo.'], 500);
    }
    
    $input['kyc_document_photo'] = 'uploads/kyc/' . $filename;
}

$res = ResidentService::updateResident($orgId, $tenantId, $input);

if (!$res['success']) {
    sendJSON($res, 400);
}

sendJSON($res);
