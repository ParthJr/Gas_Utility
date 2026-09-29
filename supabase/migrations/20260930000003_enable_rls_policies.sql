-- ========================================================
-- StayFlow Smart PG Management System
-- Supabase Migration 3: Row Level Security (RLS) Policies
-- ========================================================

-- 1. Helper function to get current user organization_id
CREATE OR REPLACE FUNCTION get_auth_org_id()
RETURNS INTEGER AS $$
DECLARE
    org_id INTEGER;
BEGIN
    SELECT organization_id INTO org_id FROM users WHERE email = auth.email() LIMIT 1;
    RETURN org_id;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- 2. Helper function to check if current user is super admin
CREATE OR REPLACE FUNCTION is_super_admin()
RETURNS BOOLEAN AS $$
DECLARE
    is_sa BOOLEAN;
BEGIN
    SELECT (role_code = 'super_admin' OR role = 'admin') INTO is_sa FROM users WHERE email = auth.email() LIMIT 1;
    RETURN COALESCE(is_sa, false);
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- RLS for users
ALTER TABLE "users" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_users" ON "users";
CREATE POLICY "service_role_all_on_users" ON "users" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_users" ON "users";
CREATE POLICY "super_admin_all_on_users" ON "users" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_users" ON "users";
CREATE POLICY "tenant_isolation_on_users" ON "users" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for buildings
ALTER TABLE "buildings" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_buildings" ON "buildings";
CREATE POLICY "service_role_all_on_buildings" ON "buildings" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_buildings" ON "buildings";
CREATE POLICY "super_admin_all_on_buildings" ON "buildings" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_buildings" ON "buildings";
CREATE POLICY "tenant_isolation_on_buildings" ON "buildings" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for floors
ALTER TABLE "floors" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_floors" ON "floors";
CREATE POLICY "service_role_all_on_floors" ON "floors" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_floors" ON "floors";
CREATE POLICY "super_admin_all_on_floors" ON "floors" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_floors" ON "floors";
CREATE POLICY "tenant_isolation_on_floors" ON "floors" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for rooms
ALTER TABLE "rooms" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_rooms" ON "rooms";
CREATE POLICY "service_role_all_on_rooms" ON "rooms" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_rooms" ON "rooms";
CREATE POLICY "super_admin_all_on_rooms" ON "rooms" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_rooms" ON "rooms";
CREATE POLICY "tenant_isolation_on_rooms" ON "rooms" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for beds
ALTER TABLE "beds" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_beds" ON "beds";
CREATE POLICY "service_role_all_on_beds" ON "beds" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_beds" ON "beds";
CREATE POLICY "super_admin_all_on_beds" ON "beds" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_beds" ON "beds";
CREATE POLICY "tenant_isolation_on_beds" ON "beds" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for bed_price_history
ALTER TABLE "bed_price_history" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_bed_price_history" ON "bed_price_history";
CREATE POLICY "service_role_all_on_bed_price_history" ON "bed_price_history" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_bed_price_history" ON "bed_price_history";
CREATE POLICY "super_admin_all_on_bed_price_history" ON "bed_price_history" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_bed_price_history" ON "bed_price_history";
CREATE POLICY "tenant_isolation_on_bed_price_history" ON "bed_price_history" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for tenant_bookings
ALTER TABLE "tenant_bookings" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_tenant_bookings" ON "tenant_bookings";
CREATE POLICY "service_role_all_on_tenant_bookings" ON "tenant_bookings" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_tenant_bookings" ON "tenant_bookings";
CREATE POLICY "super_admin_all_on_tenant_bookings" ON "tenant_bookings" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_tenant_bookings" ON "tenant_bookings";
CREATE POLICY "tenant_isolation_on_tenant_bookings" ON "tenant_bookings" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for complaints
ALTER TABLE "complaints" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_complaints" ON "complaints";
CREATE POLICY "service_role_all_on_complaints" ON "complaints" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_complaints" ON "complaints";
CREATE POLICY "super_admin_all_on_complaints" ON "complaints" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_complaints" ON "complaints";
CREATE POLICY "tenant_isolation_on_complaints" ON "complaints" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for gatepasses
ALTER TABLE "gatepasses" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_gatepasses" ON "gatepasses";
CREATE POLICY "service_role_all_on_gatepasses" ON "gatepasses" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_gatepasses" ON "gatepasses";
CREATE POLICY "super_admin_all_on_gatepasses" ON "gatepasses" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_gatepasses" ON "gatepasses";
CREATE POLICY "tenant_isolation_on_gatepasses" ON "gatepasses" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for gate_pass_movements
ALTER TABLE "gate_pass_movements" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_gate_pass_movements" ON "gate_pass_movements";
CREATE POLICY "service_role_all_on_gate_pass_movements" ON "gate_pass_movements" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_gate_pass_movements" ON "gate_pass_movements";
CREATE POLICY "super_admin_all_on_gate_pass_movements" ON "gate_pass_movements" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_gate_pass_movements" ON "gate_pass_movements";
CREATE POLICY "tenant_isolation_on_gate_pass_movements" ON "gate_pass_movements" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for invoices
ALTER TABLE "invoices" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_invoices" ON "invoices";
CREATE POLICY "service_role_all_on_invoices" ON "invoices" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_invoices" ON "invoices";
CREATE POLICY "super_admin_all_on_invoices" ON "invoices" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_invoices" ON "invoices";
CREATE POLICY "tenant_isolation_on_invoices" ON "invoices" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for payments
ALTER TABLE "payments" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_payments" ON "payments";
CREATE POLICY "service_role_all_on_payments" ON "payments" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_payments" ON "payments";
CREATE POLICY "super_admin_all_on_payments" ON "payments" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_payments" ON "payments";
CREATE POLICY "tenant_isolation_on_payments" ON "payments" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for meal_attendance
ALTER TABLE "meal_attendance" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_meal_attendance" ON "meal_attendance";
CREATE POLICY "service_role_all_on_meal_attendance" ON "meal_attendance" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_meal_attendance" ON "meal_attendance";
CREATE POLICY "super_admin_all_on_meal_attendance" ON "meal_attendance" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_meal_attendance" ON "meal_attendance";
CREATE POLICY "tenant_isolation_on_meal_attendance" ON "meal_attendance" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for meal_menus
ALTER TABLE "meal_menus" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_meal_menus" ON "meal_menus";
CREATE POLICY "service_role_all_on_meal_menus" ON "meal_menus" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_meal_menus" ON "meal_menus";
CREATE POLICY "super_admin_all_on_meal_menus" ON "meal_menus" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_meal_menus" ON "meal_menus";
CREATE POLICY "tenant_isolation_on_meal_menus" ON "meal_menus" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for eb_readings
ALTER TABLE "eb_readings" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_eb_readings" ON "eb_readings";
CREATE POLICY "service_role_all_on_eb_readings" ON "eb_readings" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_eb_readings" ON "eb_readings";
CREATE POLICY "super_admin_all_on_eb_readings" ON "eb_readings" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_eb_readings" ON "eb_readings";
CREATE POLICY "tenant_isolation_on_eb_readings" ON "eb_readings" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for announcements
ALTER TABLE "announcements" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_announcements" ON "announcements";
CREATE POLICY "service_role_all_on_announcements" ON "announcements" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_announcements" ON "announcements";
CREATE POLICY "super_admin_all_on_announcements" ON "announcements" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_announcements" ON "announcements";
CREATE POLICY "tenant_isolation_on_announcements" ON "announcements" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for notifications
ALTER TABLE "notifications" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_notifications" ON "notifications";
CREATE POLICY "service_role_all_on_notifications" ON "notifications" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_notifications" ON "notifications";
CREATE POLICY "super_admin_all_on_notifications" ON "notifications" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_notifications" ON "notifications";
CREATE POLICY "tenant_isolation_on_notifications" ON "notifications" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for notification_recipients
ALTER TABLE "notification_recipients" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_notification_recipients" ON "notification_recipients";
CREATE POLICY "service_role_all_on_notification_recipients" ON "notification_recipients" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_notification_recipients" ON "notification_recipients";
CREATE POLICY "super_admin_all_on_notification_recipients" ON "notification_recipients" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_notification_recipients" ON "notification_recipients";
CREATE POLICY "tenant_isolation_on_notification_recipients" ON "notification_recipients" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for notification_logs
ALTER TABLE "notification_logs" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_notification_logs" ON "notification_logs";
CREATE POLICY "service_role_all_on_notification_logs" ON "notification_logs" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_notification_logs" ON "notification_logs";
CREATE POLICY "super_admin_all_on_notification_logs" ON "notification_logs" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_notification_logs" ON "notification_logs";
CREATE POLICY "tenant_isolation_on_notification_logs" ON "notification_logs" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for subscriptions
ALTER TABLE "subscriptions" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_subscriptions" ON "subscriptions";
CREATE POLICY "service_role_all_on_subscriptions" ON "subscriptions" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_subscriptions" ON "subscriptions";
CREATE POLICY "super_admin_all_on_subscriptions" ON "subscriptions" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_subscriptions" ON "subscriptions";
CREATE POLICY "tenant_isolation_on_subscriptions" ON "subscriptions" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for system_settings
ALTER TABLE "system_settings" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_system_settings" ON "system_settings";
CREATE POLICY "service_role_all_on_system_settings" ON "system_settings" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_system_settings" ON "system_settings";
CREATE POLICY "super_admin_all_on_system_settings" ON "system_settings" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_system_settings" ON "system_settings";
CREATE POLICY "tenant_isolation_on_system_settings" ON "system_settings" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for user_building_assignments
ALTER TABLE "user_building_assignments" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_user_building_assignments" ON "user_building_assignments";
CREATE POLICY "service_role_all_on_user_building_assignments" ON "user_building_assignments" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_user_building_assignments" ON "user_building_assignments";
CREATE POLICY "super_admin_all_on_user_building_assignments" ON "user_building_assignments" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_user_building_assignments" ON "user_building_assignments";
CREATE POLICY "tenant_isolation_on_user_building_assignments" ON "user_building_assignments" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- RLS for audit_logs
ALTER TABLE "audit_logs" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_audit_logs" ON "audit_logs";
CREATE POLICY "service_role_all_on_audit_logs" ON "audit_logs" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "super_admin_all_on_audit_logs" ON "audit_logs";
CREATE POLICY "super_admin_all_on_audit_logs" ON "audit_logs" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());
DROP POLICY IF EXISTS "tenant_isolation_on_audit_logs" ON "audit_logs";
CREATE POLICY "tenant_isolation_on_audit_logs" ON "audit_logs" FOR ALL TO authenticated USING (organization_id = get_auth_org_id()) WITH CHECK (organization_id = get_auth_org_id());

-- Public Read-Only RLS for plans
ALTER TABLE "plans" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_plans" ON "plans";
CREATE POLICY "service_role_all_on_plans" ON "plans" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_select_on_plans" ON "plans";
CREATE POLICY "public_select_on_plans" ON "plans" FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "admin_modify_on_plans" ON "plans";
CREATE POLICY "admin_modify_on_plans" ON "plans" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());

-- Public Read-Only RLS for features
ALTER TABLE "features" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_features" ON "features";
CREATE POLICY "service_role_all_on_features" ON "features" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_select_on_features" ON "features";
CREATE POLICY "public_select_on_features" ON "features" FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "admin_modify_on_features" ON "features";
CREATE POLICY "admin_modify_on_features" ON "features" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());

-- Public Read-Only RLS for plan_features
ALTER TABLE "plan_features" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_plan_features" ON "plan_features";
CREATE POLICY "service_role_all_on_plan_features" ON "plan_features" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_select_on_plan_features" ON "plan_features";
CREATE POLICY "public_select_on_plan_features" ON "plan_features" FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "admin_modify_on_plan_features" ON "plan_features";
CREATE POLICY "admin_modify_on_plan_features" ON "plan_features" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());

-- Public Read-Only RLS for blogs
ALTER TABLE "blogs" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_blogs" ON "blogs";
CREATE POLICY "service_role_all_on_blogs" ON "blogs" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_select_on_blogs" ON "blogs";
CREATE POLICY "public_select_on_blogs" ON "blogs" FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "admin_modify_on_blogs" ON "blogs";
CREATE POLICY "admin_modify_on_blogs" ON "blogs" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());

-- Public Read-Only RLS for whitepapers
ALTER TABLE "whitepapers" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_whitepapers" ON "whitepapers";
CREATE POLICY "service_role_all_on_whitepapers" ON "whitepapers" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_select_on_whitepapers" ON "whitepapers";
CREATE POLICY "public_select_on_whitepapers" ON "whitepapers" FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "admin_modify_on_whitepapers" ON "whitepapers";
CREATE POLICY "admin_modify_on_whitepapers" ON "whitepapers" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());

-- Public Read-Only RLS for team_members
ALTER TABLE "team_members" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_team_members" ON "team_members";
CREATE POLICY "service_role_all_on_team_members" ON "team_members" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_select_on_team_members" ON "team_members";
CREATE POLICY "public_select_on_team_members" ON "team_members" FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "admin_modify_on_team_members" ON "team_members";
CREATE POLICY "admin_modify_on_team_members" ON "team_members" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());

-- Public Read-Only RLS for promotional_posters
ALTER TABLE "promotional_posters" ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "service_role_all_on_promotional_posters" ON "promotional_posters";
CREATE POLICY "service_role_all_on_promotional_posters" ON "promotional_posters" FOR ALL TO service_role USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_select_on_promotional_posters" ON "promotional_posters";
CREATE POLICY "public_select_on_promotional_posters" ON "promotional_posters" FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "admin_modify_on_promotional_posters" ON "promotional_posters";
CREATE POLICY "admin_modify_on_promotional_posters" ON "promotional_posters" FOR ALL TO authenticated USING (is_super_admin()) WITH CHECK (is_super_admin());

