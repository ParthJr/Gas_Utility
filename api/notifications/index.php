<?php
/**
 * API: Tenant-Scoped Notification Center API
 * Manages notifications, unread counts, mark-as-read, and dismissal.
 * Strictly Scoped to Tenant Organization & Active Property with Role-Based Isolation.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/TenantContext.php';
require_once __DIR__ . '/../../services/NotificationService.php';
require_once __DIR__ . '/../../services/AuditService.php';

header('Content-Type: application/json; charset=utf-8');

$authUser = getAuthUser();
if (!$authUser) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: Please log in.'], 401);
}

$currentUserId = (int)$authUser['id'];
$orgId = TenantContext::getOrgId() ?: (!empty($authUser['organization_id']) ? (int)$authUser['organization_id'] : 0);

if ($currentUserId <= 0 || $orgId <= 0) {
    sendJSON(['success' => false, 'message' => 'Unauthorized: Invalid organization or user session.'], 401);
}

$activeBldId = TenantContext::getBuildingId() ?: (!empty($authUser['building_id']) ? (int)$authUser['building_id'] : null);
$method = $_SERVER['REQUEST_METHOD'];

$userRole = strtolower((string)($authUser['role_code'] ?? ($authUser['role'] ?? ($_SESSION['user_role_code'] ?? ($_SESSION['user_role'] ?? 'tenant')))));
$isAdmin = in_array($userRole, ['admin', 'super_admin', 'staff', 'owner', 'pg_owner', 'manager']);

// Helper to format human-readable relative time
function getRelativeTime(string $datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = (int)floor($diff / 60);
        return "{$mins}m ago";
    } elseif ($diff < 86400) {
        $hours = (int)floor($diff / 3600);
        return "{$hours}h ago";
    } elseif ($diff < 172800) {
        return 'Yesterday, ' . date('H:i', $time);
    } else {
        return date('d M, H:i', $time);
    }
}

// Helper to determine notification icon & link
function getNotificationMeta(string $type, ?string $actionUrl, bool $isAdmin): array {
    $t = strtoupper(trim($type));
    $icon = 'bi-bell-fill';
    $color = 'text-primary';
    $badgeBg = 'bg-primary-subtle text-primary';
    $url = $actionUrl ?: 'javascript:void(0)';

    if (strpos($t, 'PAYMENT') !== false || strpos($t, 'INVOICE') !== false || strpos($t, 'EB_') !== false || strpos($t, 'BILLING') !== false) {
        $icon = 'bi-receipt';
        $color = 'text-warning';
        $badgeBg = 'bg-warning-subtle text-warning';
        if (empty($actionUrl) || $actionUrl === 'dues.php') {
            $url = $isAdmin ? 'billing/invoices.php' : 'dues.php';
        }
    } elseif (strpos($t, 'WHATSAPP') !== false || strpos($t, 'BROADCAST') !== false || strpos($t, 'COMMUNICATION') !== false) {
        $icon = 'bi-whatsapp';
        $color = 'text-success';
        $badgeBg = 'bg-success-subtle text-success';
        if (empty($actionUrl)) $url = $isAdmin ? 'whatsapp.php' : 'index.php';
    } elseif (strpos($t, 'GATEPASS') !== false) {
        $icon = 'bi-qr-code-scan';
        $color = 'text-info';
        $badgeBg = 'bg-info-subtle text-info';
        if (empty($actionUrl)) $url = 'gatepass.php';
    } elseif (strpos($t, 'MAINTENANCE') !== false || strpos($t, 'COMPLAINT') !== false || strpos($t, 'TICKET') !== false) {
        $icon = 'bi-tools';
        $color = 'text-secondary';
        $badgeBg = 'bg-secondary-subtle text-secondary';
        if (empty($actionUrl)) $url = 'complaints.php';
    } elseif (strpos($t, 'RESIDENT') !== false || strpos($t, 'ONBOARDING') !== false || strpos($t, 'KYC') !== false || strpos($t, 'TENANT') !== false) {
        $icon = 'bi-people-fill';
        $color = 'text-primary';
        $badgeBg = 'bg-primary-subtle text-primary';
        if (empty($actionUrl)) $url = $isAdmin ? 'tenants.php' : 'profile.php';
    } elseif (strpos($t, 'ROOM') !== false || strpos($t, 'BED') !== false) {
        $icon = 'bi-door-open-fill';
        $color = 'text-dark';
        $badgeBg = 'bg-dark-subtle text-dark';
        if (empty($actionUrl)) $url = $isAdmin ? 'rooms.php' : 'index.php';
    } elseif (strpos($t, 'PLAN') !== false || strpos($t, 'SUBSCRIPTION') !== false) {
        $icon = 'bi-gem';
        $color = 'text-indigo';
        $badgeBg = 'bg-indigo-subtle text-indigo';
        if (empty($actionUrl)) $url = 'plans.php';
    } elseif (strpos($t, 'PROPERTY') !== false || strpos($t, 'BUILDING') !== false) {
        $icon = 'bi-buildings';
        $color = 'text-primary';
        $badgeBg = 'bg-primary-subtle text-primary';
        if (empty($actionUrl)) $url = $isAdmin ? 'buildings.php' : 'index.php';
    } elseif (strpos($t, 'ANNOUNCEMENT') !== false) {
        $icon = 'bi-megaphone-fill';
        $color = 'text-warning';
        $badgeBg = 'bg-warning-subtle text-warning';
        if (empty($actionUrl)) $url = 'index.php';
    }

    return [
        'icon' => $icon,
        'color' => $color,
        'badgeBg' => $badgeBg,
        'url' => $url
    ];
}

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    if ($action === 'count') {
        $unreadCount = $isAdmin 
            ? NotificationService::getAdminUnreadCount($orgId, $activeBldId)
            : NotificationService::getUnreadCount($orgId, $currentUserId, $activeBldId);

        sendJSON(['success' => true, 'unread_count' => $unreadCount]);
    }

    if ($action === 'list') {
        if ($isAdmin) {
            $unreadCount = NotificationService::getAdminUnreadCount($orgId, $activeBldId);
            $rows = NotificationService::getAdminNotifications($orgId, $activeBldId, 30);
        } else {
            $unreadCount = NotificationService::getUnreadCount($orgId, $currentUserId, $activeBldId);
            $rows = NotificationService::getResidentNotifications($orgId, $currentUserId, 30, $activeBldId);
        }

        $notifications = [];
        foreach ($rows as $r) {
            $meta = getNotificationMeta($r['type'] ?? '', $r['action_url'], $isAdmin);
            $bldTag = !empty($r['building_name']) ? $r['building_name'] : null;

            $notifications[] = [
                'id' => (int)$r['id'],
                'type' => $r['type'],
                'title' => $r['title'],
                'message' => $r['message'],
                'property_name' => $bldTag,
                'is_read' => (bool)$r['is_read'],
                'priority' => $r['priority'],
                'created_at' => $r['created_at'],
                'time_ago' => getRelativeTime($r['created_at']),
                'icon' => $meta['icon'],
                'color' => $meta['color'],
                'badgeBg' => $meta['badgeBg'],
                'url' => $meta['url']
            ];
        }

        sendJSON([
            'success' => true,
            'unread_count' => $unreadCount,
            'total_count' => count($notifications),
            'notifications' => $notifications,
            'data' => $notifications
        ]);
    }
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? '';

    // 1. Mark notification(s) as read
    if ($action === 'mark_read') {
        $notifId = (int)($input['id'] ?? 0);
        $markAll = !empty($input['mark_all']);

        if ($markAll) {
            if ($isAdmin) {
                NotificationService::markAdminAllRead($orgId, $activeBldId);
            } else {
                NotificationService::markAllRead($orgId, $currentUserId, $activeBldId);
            }

            $newUnread = $isAdmin 
                ? NotificationService::getAdminUnreadCount($orgId, $activeBldId)
                : NotificationService::getUnreadCount($orgId, $currentUserId, $activeBldId);

            sendJSON(['success' => true, 'message' => "All notifications marked as read.", 'unread_count' => $newUnread]);
        } elseif ($notifId > 0) {
            if ($isAdmin) {
                NotificationService::markAdminNotifRead($orgId, $notifId);
            } else {
                NotificationService::markResidentNotifRead($orgId, $currentUserId, $notifId);
            }

            $newUnread = $isAdmin 
                ? NotificationService::getAdminUnreadCount($orgId, $activeBldId)
                : NotificationService::getUnreadCount($orgId, $currentUserId, $activeBldId);

            sendJSON(['success' => true, 'message' => "Notification marked as read.", 'unread_count' => $newUnread]);
        } else {
            sendJSON(['success' => false, 'message' => 'Valid Notification ID or mark_all flag required.'], 400);
        }
    }

    // 2. Dismiss / Delete a notification
    elseif ($action === 'dismiss' || $action === 'delete') {
        $notifId = (int)($input['id'] ?? 0);
        if ($notifId <= 0) {
            sendJSON(['success' => false, 'message' => 'Valid Notification ID required.'], 400);
        }

        NotificationService::dismissNotification($orgId, $notifId, $currentUserId, $isAdmin);
        $newUnread = $isAdmin 
            ? NotificationService::getAdminUnreadCount($orgId, $activeBldId)
            : NotificationService::getUnreadCount($orgId, $currentUserId, $activeBldId);

        sendJSON(['success' => true, 'message' => "Notification dismissed successfully.", 'unread_count' => $newUnread]);
    }
}

