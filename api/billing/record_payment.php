<?php
/**
 * API: Billing - Record Payment & Update Invoice Status
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/WhatsAppMetaService.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'POST method required.'], 405);
}

$user = requireAuth();
$orgId = TenantContext::getOrgId();
$db = getDB();

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$invoiceId = (int)($input['invoice_id'] ?? 0);
$amount = (float)($input['amount'] ?? 0);
$mode = $input['payment_mode'] ?? 'upi';
$txRef = trim($input['transaction_ref'] ?? '');
$notes = trim($input['notes'] ?? '');

if ($invoiceId <= 0 || $amount <= 0) {
    sendJSON(['success' => false, 'message' => 'Valid Invoice ID and positive Amount are required.'], 400);
}

// Fetch invoice (Scoped to organization if not super admin)
$stmtInv = $db->prepare("SELECT * FROM invoices WHERE id = ?");
$stmtInv->execute([$invoiceId]);
$inv = $stmtInv->fetch();

if (!$inv) {
    sendJSON(['success' => false, 'message' => 'Invoice not found.'], 404);
}

// Enforce tenant boundary
if ($orgId && (int)$inv['organization_id'] !== (int)$orgId && !TenantContext::isSuperAdmin()) {
    sendJSON(['success' => false, 'message' => 'Invoice does not belong to your organization.'], 403);
}

// If current user is a tenant, ensure they are paying their own invoice
if ($user['role'] === 'tenant' && (int)$inv['tenant_id'] !== (int)$user['id']) {
    sendJSON(['success' => false, 'message' => 'You cannot pay for other tenants.'], 403);
}

$db->beginTransaction();

try {
    $paymentNo = generateCode('PAY');
    $stmtPay = $db->prepare("
        INSERT INTO payments (payment_no, organization_id, building_id, invoice_id, tenant_id, amount, payment_mode, transaction_ref, recorded_by, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtPay->execute([$paymentNo, $inv['organization_id'], $inv['building_id'], $invoiceId, $inv['tenant_id'], $amount, $mode, $txRef, $user['id'], $notes]);
    $payId = $db->lastInsertId();

    // Update invoice total paid amount and status
    $newPaid = (float)$inv['paid_amount'] + $amount;
    $newStatus = ($newPaid >= (float)$inv['total_amount']) ? 'paid' : (($newPaid > 0) ? 'partial' : 'pending');

    $stmtUpInv = $db->prepare("UPDATE invoices SET paid_amount = ?, status = ? WHERE id = ? AND organization_id = ?");
    $stmtUpInv->execute([$newPaid, $newStatus, $invoiceId, $inv['organization_id']]);

    $db->commit();

    // Send WhatsApp payment receipt notification
    $stmtTenant = $db->prepare("SELECT name, phone FROM users WHERE id = ?");
    $stmtTenant->execute([$inv['tenant_id']]);
    $tenant = $stmtTenant->fetch();

    if ($tenant) {
        WhatsAppMetaService::sendPaymentReceipt($tenant['phone'], $tenant['name'], $paymentNo, $amount, $inv['invoice_no']);
    }

    sendJSON([
        'success' => true,
        'message' => "Payment of \u20b9{$amount} recorded successfully! Receipt #{$paymentNo}",
        'receipt_no' => $paymentNo,
        'payment_id' => $payId,
        'invoice_status' => $newStatus,
        'balance_remaining' => max(0, (float)$inv['total_amount'] - $newPaid)
    ]);

} catch (Exception $e) {
    $db->rollBack();
    sendJSON(['success' => false, 'message' => 'Error recording payment: ' . $e->getMessage()], 500);
}
