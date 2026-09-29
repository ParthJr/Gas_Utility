<?php
/**
 * API: Gate Pass - WhatsApp Parent Alert & Messaging Engine
 * Handles preview, automatic template dispatch, custom personal messages, manual wa.me logging, and audit history.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/TenantContext.php';
require_once __DIR__ . '/../../services/WhatsAppGatePassService.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = getAuthUser();
if (!$user) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: Please log in'], 401);
}

$userRole = strtolower((string)($user['role_code'] ?? ($user['role'] ?? 'tenant')));
if (!in_array($userRole, ['admin', 'super_admin', 'owner', 'staff', 'security', 'manager'])) {
    sendJSON(['success' => false, 'message' => 'Access Denied: Only staff and admin can send parent alerts.'], 403);
}

$orgId = TenantContext::getOrgId();
if (!$orgId) {
    $orgId = (int)($user['organization_id'] ?? 1);
}

$senderUserId = (int)$user['id'];
$senderName = $user['name'] ?? 'Staff Member';

$method = $_SERVER['REQUEST_METHOD'];
$input = ($method === 'POST') 
    ? (json_decode(file_get_contents('php://input'), true) ?? $_POST) 
    : $_GET;

$action = $input['action'] ?? 'send_template';
$passId = (int)($input['gatepass_id'] ?? 0);

if ($passId <= 0) {
    sendJSON(['success' => false, 'message' => 'Valid Gate Pass ID is required.'], 400);
}

// 1. ACTION: PREVIEW TEMPLATE OR GET CONTEXT
if ($action === 'preview') {
    $eventType = strtoupper((string)($input['event_type'] ?? 'OUT'));
    $ctx = WhatsAppGatePassService::getPassContext($passId, $orgId);

    if (!$ctx) {
        sendJSON(['success' => false, 'code' => 'INVALID_GATEPASS', 'message' => 'Gate Pass not found or unauthorized.'], 404);
    }

    $templateText = WhatsAppGatePassService::renderTemplate($eventType, $ctx);
    $cleanPhone = WhatsAppHelper::formatPhoneNumber($ctx['parent_phone']);
    $waUrl = !empty($cleanPhone) ? WhatsAppGatePassService::getWaRedirectUrl($cleanPhone, $templateText) : '';
    $isConfigured = WhatsAppGatePassService::isMetaApiConfigured();
    $alreadySent = WhatsAppGatePassService::isDuplicateAlert($passId, $eventType);

    sendJSON([
        'success' => true,
        'has_parent_phone' => $ctx['has_parent_phone'],
        'parent_name' => $ctx['parent_name'],
        'parent_phone' => $ctx['parent_phone'],
        'formatted_phone' => $cleanPhone,
        'event_type' => $eventType,
        'preview_message' => $templateText,
        'is_api_configured' => $isConfigured,
        'wa_url' => $waUrl,
        'already_sent' => $alreadySent,
        'context' => $ctx
    ]);
}

// 2. ACTION: SEND AUTOMATIC TEMPLATE MESSAGE
elseif ($action === 'send_template' || $action === 'send_alert') {
    $eventType = strtoupper((string)($input['event_type'] ?? 'OUT'));
    $res = WhatsAppGatePassService::sendAutomaticAlert($passId, $eventType, $senderUserId, $senderName, $orgId);

    if (!$res['success'] && ($res['code'] ?? '') === 'PARENT_CONTACT_MISSING') {
        sendJSON([
            'success' => false,
            'code' => 'PARENT_CONTACT_MISSING',
            'message' => 'Parent WhatsApp Not Available: No parent/guardian WhatsApp number is available for this resident.',
            'context' => $res['context'] ?? null
        ], 422);
    }

    sendJSON($res);
}

// 3. ACTION: SEND CUSTOM / PERSONAL MESSAGE
elseif ($action === 'send_custom' || $action === 'send_personal') {
    $customMessage = (string)($input['message'] ?? '');
    if (empty(trim($customMessage))) {
        sendJSON(['success' => false, 'message' => 'Custom message text cannot be empty.'], 400);
    }

    $res = WhatsAppGatePassService::sendPersonalMessage($passId, $customMessage, $senderUserId, $senderName, $orgId);

    if (!$res['success'] && ($res['code'] ?? '') === 'PARENT_CONTACT_MISSING') {
        sendJSON([
            'success' => false,
            'code' => 'PARENT_CONTACT_MISSING',
            'message' => 'Parent WhatsApp Not Available: No parent/guardian WhatsApp number is available for this resident.'
        ], 422);
    }

    sendJSON($res);
}

// 4. ACTION: LOG MANUAL WA.ME OPEN
elseif ($action === 'log_manual' || $action === 'log_open') {
    $ctx = WhatsAppGatePassService::getPassContext($passId, $orgId);
    if (!$ctx) {
        sendJSON(['success' => false, 'message' => 'Invalid Gate Pass.'], 404);
    }

    $messageText = (string)($input['message'] ?? '');
    $cleanPhone = WhatsAppHelper::formatPhoneNumber($ctx['parent_phone']);

    $logId = WhatsAppGatePassService::logMessage([
        'organization_id' => $ctx['organization_id'],
        'building_id' => $ctx['building_id'],
        'resident_id' => $ctx['resident_id'],
        'gate_pass_id' => $ctx['id'],
        'recipient_type' => 'parent',
        'recipient_name' => $ctx['parent_name'],
        'phone_number' => $cleanPhone,
        'message_type' => 'direct_wa_redirect',
        'message_text' => $messageText,
        'template_name' => 'MANUAL_WA_ME',
        'sent_by_user_id' => $senderUserId,
        'sent_by_user_name' => $senderName,
        'provider' => 'wa_redirect',
        'status' => 'OPENED_IN_WHATSAPP'
    ]);

    sendJSON([
        'success' => true,
        'log_id' => $logId,
        'status' => 'OPENED_IN_WHATSAPP',
        'message' => 'WhatsApp opened in new tab.'
    ]);
}

// 5. ACTION: MESSAGE HISTORY AUDIT LOGS
elseif ($action === 'history' || $action === 'logs') {
    $logs = WhatsAppGatePassService::getMessageLogs($passId, $orgId);
    sendJSON([
        'success' => true,
        'gatepass_id' => $passId,
        'logs' => $logs
    ]);
}

else {
    sendJSON(['success' => false, 'message' => 'Invalid action specified.'], 400);
}
