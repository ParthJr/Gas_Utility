<?php
/**
 * StayFlow Notification Service
 */
declare(strict_types=1);

if (!class_exists('NotificationService')) {
    class NotificationService {
        public static function getAdminUnreadCount(int $orgId, ?int $bldId = null): int {
            try {
                $db = getDB();
                $sql = "SELECT COUNT(*) FROM complaints WHERE organization_id = ? AND status = 'open'";
                $params = [$orgId];
                if ($bldId !== null) {
                    $sql .= " AND building_id = ?";
                    $params[] = $bldId;
                }
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                return (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                return 0;
            }
        }
    }
}
