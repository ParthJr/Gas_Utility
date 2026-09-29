<?php
/**
 * StayFlow API — Staff Management & Access Control
 * Handles Password Reset, Staff Removal (Soft-delete), and Status Toggling
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/EntitlementService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Method not allowed. POST required.'], 405);
}

$admin = requireAuth('admin');
$orgId = TenantContext::getOrgId();
$currUserId = (int)($_SESSION['user_id'] ?? 0);

EntitlementService::requireFeature($orgId, 'staff_management');

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = trim($input['action'] ?? '');
$csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

if (!verifyCsrfToken($csrfToken)) {
    sendJSON(['success' => false, 'message' => 'Invalid security token (CSRF). Please refresh and try again.'], 403);
}

$db = getDB();

// ----------------------------------------------------
// 1. RESET STAFF PASSWORD
// ----------------------------------------------------
if ($action === 'reset_password') {
    $staffId = (int)($input['staff_id'] ?? 0);
    $newPassword = (string)($input['new_password'] ?? '');
    $confirmPassword = (string)($input['confirm_password'] ?? '');

    if ($staffId <= 0) {
        sendJSON(['success' => false, 'message' => 'Valid Staff ID is required.'], 400);
    }

    if (empty($newPassword) || strlen($newPassword) < 8) {
        sendJSON(['success' => false, 'message' => 'Password must be at least 8 characters long.'], 400);
    }

    if ($newPassword !== $confirmPassword) {
        sendJSON(['success' => false, 'message' => 'New password and confirm password do not match.'], 400);
    }

    // Verify staff exists in current organization and is not a tenant
    $stmt = $db->prepare("
        SELECT u.id, u.name, u.username, u.email, u.building_id, u.role, u.role_id, r.role_code
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE u.id = ? AND u.organization_id = ? AND u.status != 'removed'
    ");
    $stmt->execute([$staffId, $orgId]);
    $target = $stmt->fetch();

    if (!$target) {
        sendJSON(['success' => false, 'message' => 'Staff member not found or access denied.'], 404);
    }

    // Role privilege check: Non-owner cannot reset owner's password
    if ($target['role_code'] === 'owner' && !TenantContext::isOwner() && !TenantContext::isSuperAdmin()) {
        sendJSON(['success' => false, 'message' => 'Only the organization owner can reset owner account credentials.'], 403);
    }

    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

    try {
        $stmtUpd = $db->prepare("
            UPDATE users 
            SET password = ?, password_changed_at = NOW(), failed_login_attempts = 0, locked_until = NULL, updated_at = NOW()
            WHERE id = ? AND organization_id = ?
        ");
        $stmtUpd->execute([$hash, $staffId, $orgId]);

        // Revoke active sessions / tokens for this staff member
        $stmtToken = $db->prepare("DELETE FROM refresh_tokens WHERE user_id = ?");
        $stmtToken->execute([$staffId]);

        AuditService::log('STAFF_PASSWORD_RESET', 'users', (int)$staffId, [
            'staff_name' => $target['name'],
            'username' => $target['username'],
            'reset_by' => $_SESSION['user_name'] ?? 'Admin'
        ], [
            'staff_id' => $staffId,
            'organization_id' => $orgId
        ]);

        sendJSON([
            'success' => true,
            'message' => "Password for '{$target['name']}' has been reset successfully.",
            'staff_id' => $staffId
        ]);
    } catch (\Throwable $e) {
        sendJSON(['success' => false, 'message' => 'Database error while resetting password.'], 500);
    }
}

// ----------------------------------------------------
// 2. REMOVE STAFF MEMBER (Soft-Delete & Revoke Access)
// ----------------------------------------------------
elseif ($action === 'remove_staff') {
    $staffId = (int)($input['staff_id'] ?? 0);

    if ($staffId <= 0) {
        sendJSON(['success' => false, 'message' => 'Valid Staff ID is required.'], 400);
    }

    // Self-removal protection
    if ($staffId === $currUserId) {
        sendJSON(['success' => false, 'message' => 'You cannot remove your own account.'], 400);
    }

    // Verify staff exists in current organization
    $stmt = $db->prepare("
        SELECT u.id, u.name, u.username, u.email, u.building_id, u.role, u.role_id, r.role_code, r.name as role_name
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE u.id = ? AND u.organization_id = ? AND u.status != 'removed'
    ");
    $stmt->execute([$staffId, $orgId]);
    $target = $stmt->fetch();

    if (!$target) {
        sendJSON(['success' => false, 'message' => 'Staff member not found or already removed.'], 404);
    }

    // Root / Owner protection
    if ($target['role_code'] === 'owner' && !TenantContext::isOwner() && !TenantContext::isSuperAdmin()) {
        sendJSON(['success' => false, 'message' => 'Only the organization owner can remove owner accounts.'], 403);
    }

    try {
        // Soft delete: set status to 'removed'
        $stmtRem = $db->prepare("
            UPDATE users 
            SET status = 'removed', updated_at = NOW() 
            WHERE id = ? AND organization_id = ?
        ");
        $stmtRem->execute([$staffId, $orgId]);

        // Remove building assignment
        $db->prepare("DELETE FROM user_building_assignments WHERE user_id = ?")->execute([$staffId]);

        // Invalidate all tokens
        $db->prepare("DELETE FROM refresh_tokens WHERE user_id = ?")->execute([$staffId]);

        AuditService::log('STAFF_REMOVED', 'users', (int)$staffId, [
            'staff_name' => $target['name'],
            'role' => $target['role_name'] ?? 'Staff',
            'removed_by' => $_SESSION['user_name'] ?? 'Admin'
        ], [
            'staff_id' => $staffId,
            'organization_id' => $orgId
        ]);

        sendJSON([
            'success' => true,
            'message' => "Staff member '{$target['name']}' has been removed successfully.",
            'staff_id' => $staffId
        ]);
    } catch (\Throwable $e) {
        sendJSON(['success' => false, 'message' => 'Database error while removing staff member.'], 500);
    }
}

// ----------------------------------------------------
// 3. ACTIVATE / DEACTIVATE STAFF (Toggle Status)
// ----------------------------------------------------
elseif ($action === 'toggle_status') {
    $staffId = (int)($input['staff_id'] ?? $input['user_id'] ?? 0);
    $newStatus = trim($input['status'] ?? '');

    if ($staffId <= 0) {
        sendJSON(['success' => false, 'message' => 'Valid Staff ID is required.'], 400);
    }

    if (!in_array($newStatus, ['active', 'inactive'], true)) {
        sendJSON(['success' => false, 'message' => 'Invalid status value. Must be active or inactive.'], 400);
    }

    if ($staffId === $currUserId) {
        sendJSON(['success' => false, 'message' => 'You cannot modify your own active account status.'], 400);
    }

    $stmt = $db->prepare("
        SELECT u.id, u.name, u.username, u.email, u.building_id, u.role, u.role_id, r.role_code
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE u.id = ? AND u.organization_id = ? AND u.status != 'removed'
    ");
    $stmt->execute([$staffId, $orgId]);
    $target = $stmt->fetch();

    if (!$target) {
        sendJSON(['success' => false, 'message' => 'Staff member not found or access denied.'], 404);
    }

    if ($target['role_code'] === 'owner' && !TenantContext::isOwner() && !TenantContext::isSuperAdmin()) {
        sendJSON(['success' => false, 'message' => 'Only the organization owner can modify owner accounts.'], 403);
    }

    try {
        $stmtToggle = $db->prepare("
            UPDATE users 
            SET status = ?, updated_at = NOW() 
            WHERE id = ? AND organization_id = ?
        ");
        $stmtToggle->execute([$newStatus, $staffId, $orgId]);

        if ($newStatus === 'inactive') {
            $db->prepare("DELETE FROM refresh_tokens WHERE user_id = ?")->execute([$staffId]);
        }

        AuditService::log('STAFF_STATUS_TOGGLED', 'users', (int)$staffId, [
            'staff_name' => $target['name'],
            'new_status' => $newStatus,
            'updated_by' => $_SESSION['user_name'] ?? 'Admin'
        ], [
            'staff_id' => $staffId,
            'organization_id' => $orgId
        ]);

        sendJSON([
            'success' => true,
            'message' => "Staff status updated to " . strtoupper($newStatus) . ".",
            'staff_id' => $staffId,
            'status' => $newStatus
        ]);
    } catch (\Throwable $e) {
        sendJSON(['success' => false, 'message' => 'Database error while updating staff status.'], 500);
    }
}

else {
    sendJSON(['success' => false, 'message' => 'Invalid action specified.'], 400);
}
