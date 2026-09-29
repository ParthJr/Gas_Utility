<?php
/**
 * StayFlow Entitlement Service
 */
declare(strict_types=1);

if (!class_exists('EntitlementService')) {
    class EntitlementService {
        public static function getEnabledFeatures(int $orgId): array {
            try {
                $db = getDB();
                $stmt = $db->prepare("
                    SELECT f.feature_key 
                    FROM features f
                    JOIN plan_features pf ON f.id = pf.feature_id
                    JOIN organizations o ON pf.plan_id = o.subscription_plan_id
                    WHERE o.id = ? AND pf.is_enabled = 1 AND f.is_active = 1
                ");
                $stmt->execute([$orgId]);
                $features = $stmt->fetchAll(PDO::FETCH_COLUMN);
                if (!empty($features)) {
                    return $features;
                }
            } catch (\Throwable $e) {
                // Fallback default enabled features if DB is empty/unreachable
            }
            return [
                'property_building_management',
                'room_bed_management',
                'resident_management',
                'billing_invoices',
                'complaints_management',
                'gatepass_security',
                'meals_management'
            ];
        }

        public static function requireFeature(int $orgId, string $featureKey): bool {
            $enabled = self::getEnabledFeatures($orgId);
            return in_array($featureKey, $enabled, true);
        }
    }
}
