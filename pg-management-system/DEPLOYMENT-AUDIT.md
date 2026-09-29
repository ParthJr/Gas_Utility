# StayFlow PG SaaS — Production Deployment Audit Report (InfinityFree)

**Audit Date:** 2026-09-16  
**Target Environment:** InfinityFree Shared Hosting (PHP 8.x + MySQL/MariaDB + Apache)  
**Status:** PRODUCTION READY  

---

## A. Files Analyzed
- **PHP Codebase Files:** 142 total PHP files across root, `/admin/`, `/resident/`, `/staff/`, `/tenant/`, `/super-admin/`, `/api/`, `/auth/`, `/config/`, `/services/`, `/cron/`, and `/install/`.
- **Frontend Assets:** JavaScript (`assets/js/admin-notifications.js`, `assets/js/push-client.js`, `sw.js`, `manifest.json`), CSS stylesheets, and Bootstrap Icons.
- **Configuration & Server Files:** `.htaccess`, `config/constants.php`, `config/database.php`, `config.example.php`.
- **Database Assets:** `database/schema.sql`, `database/seed.sql`, `database/backup-template.md`.

---

## B. Database Tables Discovered & Documented (48 Base Tables)

| # | Table Name | Purpose & Primary Key | Key Foreign Keys & Relationships |
|---|---|---|---|
| 1 | `users` | User accounts across roles (`id` PK) | `organization_id`, `building_id`, `role_id` |
| 2 | `roles` | RBAC Role definitions (`id` PK) | Referenced by `users`, `role_permissions` |
| 3 | `permissions` | Granular permission capabilities (`id` PK) | Referenced by `role_permissions` |
| 4 | `role_permissions` | Pivot table for RBAC (`role_id`, `permission_id`) | FK to `roles`, `permissions` |
| 5 | `organizations` | Multi-tenant SaaS Organizations (`id` PK) | Referenced by `users`, `buildings`, `subscriptions` |
| 6 | `buildings` | Properties/Campuses/Hostel Blocks (`id` PK) | FK `organization_id`, referenced by `rooms` |
| 7 | `floors` | Building Floor definitions (`id` PK) | FK `building_id` |
| 8 | `rooms` | Room records (`id` PK) | FK `building_id`, `floor_id` |
| 9 | `beds` | Bed allocations & status (`id` PK) | FK `room_id`, referenced by `tenant_bookings` |
| 10 | `bed_price_history` | Audit log of rent price adjustments (`id` PK) | FK `bed_id` |
| 11 | `tenant_bookings` | Active resident stay contracts (`id` PK) | FK `tenant_id`, `room_id`, `bed_id`, `organization_id` |
| 12 | `gatepasses` | Gate pass requests & lifecycle state (`id` PK) | FK `tenant_id`, `organization_id`, `building_id` |
| 13 | `gate_pass_movements`| Scanned QR movement events (`id` PK) | FK `gatepass_id`, `staff_id`, `tenant_id` |
| 14 | `complaints` | Maintenance tickets & helpdesk (`id` PK) | FK `tenant_id`, `assigned_to`, `organization_id` |
| 15 | `meal_menus` | Daily breakfast/lunch/dinner menus (`id` PK) | FK `organization_id`, `building_id` |
| 16 | `meal_attendance` | Resident attendance toggles (`id` PK) | FK `tenant_id`, `menu_id` |
| 17 | `invoices` | Monthly rent & utility billing (`id` PK) | FK `tenant_id`, `booking_id`, `organization_id` |
| 18 | `payments` | Transaction ledger records (`id` PK) | FK `invoice_id`, `tenant_id`, `organization_id` |
| 19 | `eb_readings` | Electricity meter sub-meter readings (`id` PK) | FK `room_id`, `building_id` |
| 20 | `notifications` | Central notification messages (`id` PK) | FK `organization_id`, `user_id` |
| 21 | `notification_recipients`| User-specific read tracking (`id` PK) | FK `notification_id`, `user_id` |
| 22 | `notification_logs` | Web push & dispatch logs (`id` PK) | FK `notification_id` |
| 23 | `announcements` | Broadcast organization alerts (`id` PK) | FK `organization_id` |
| 24 | `push_subscriptions` | WebPush VAPID subscriptions (`id` PK) | FK `user_id`, `organization_id` |
| 25 | `push_notification_logs`| WebPush delivery telemetry (`id` PK) | FK `user_id` |
| 26 | `whatsapp_message_logs` | WhatsApp message audit trail (`id` PK) | FK `organization_id` |
| 27 | `email_otps` | 2FA / Password reset OTPs (`id` PK) | Indexed by `email`, `otp_code` |
| 28 | `resident_login_otps` | Mobile resident OTP tokens (`id` PK) | Indexed by `phone` |
| 29 | `refresh_tokens` | API authentication tokens (`id` PK) | FK `user_id` |
| 30 | `audit_logs` | Security & compliance audit log (`id` PK) | FK `user_id`, `organization_id` |
| 31 | `plans` | SaaS tier pricing plans (`id` PK) | Referenced by `subscriptions` |
| 32 | `features` | System feature catalog (`id` PK) | Referenced by `plan_features` |
| 33 | `plan_features` | Plan-to-feature mapping matrix (`id` PK) | FK `plan_id`, `feature_id` |
| 34 | `subscriptions` | Active SaaS organization plans (`id` PK) | FK `organization_id`, `plan_id` |
| 35 | `custom_subscription_plans`| Tailored enterprise plans (`id` PK) | FK `organization_id` |
| 36 | `custom_subscription_features`| Enterprise feature overrides (`id` PK) | FK `custom_plan_id` |
| 37 | `custom_plan_features`| Feature overrides mapping (`id` PK) | FK `plan_id` |
| 38 | `custom_subscription_templates`| Custom plan templates (`id` PK) | FK `organization_id` |
| 39 | `custom_subscriptions`| Active custom billing plans (`id` PK) | FK `organization_id` |
| 40 | `tenant_entitlement_overrides`| Tenant-specific feature access (`id` PK) | FK `tenant_id` |
| 41 | `promotional_posters` | Marketing & onboarding posters (`id` PK) | FK `organization_id` |
| 42 | `promotional_poster_targets`| Target audiences for posters (`id` PK) | FK `poster_id` |
| 43 | `poster_analytics` | Poster impression/click tracking (`id` PK) | FK `poster_id` |
| 44 | `referrals` | Referral program tracking (`id` PK) | FK `referrer_user_id` |
| 45 | `referral_events` | Referral milestone completions (`id` PK) | FK `referral_id` |
| 46 | `referral_rewards` | Referral discount payouts (`id` PK) | FK `referral_id` |
| 47 | `user_building_assignments`| Staff campus access scoping (`id` PK) | FK `user_id`, `building_id` |
| 48 | `system_settings` | Global platform key-value settings (`id` PK)| Unique `setting_key` |

---

## C. External Dependencies & Hosting Compatibility

1. **No Node.js / Server-Side CLI Tools:**
   - Pure PHP VAPID WebPush encryption implementation using native OpenSSL (`OPENSSL_ALGO_SHA256` + `prime256v1` ECDSA signatures).
   - In-browser HTML5 QR Code scanning using `Html5Qrcode` / `getUserMedia()` API (device camera).
   - Client-side and SVG QR Code generation requiring zero external binaries.
2. **Web-Accessible Cron Scheduler:**
   - Replaced Linux-only cron daemon with token-authenticated `/cron.php` endpoint compatible with external HTTP ping schedulers (e.g. cron-job.org).
3. **Database Architecture:**
   - Pure PDO connection layer using prepared statements and UTF8MB4 charset, with automatic 2002 TCP fallback and dynamic environment detection.

---

## D. Fixed Bugs & Resolved Issues

1. **Logout 404 Error:** Created unified `/auth/logout.php`, `/logout.php`, `/api/auth/logout.php`, and `/login.php` handlers.
2. **Admin Notification Bell:** Installed unified notification drawer component on all 15 Admin subpages.
3. **Kitchen & Meals Page:** Fixed JavaScript syntax errors, dynamic headless API headcount synchronization, and modal menu updating.
4. **Maintenance Ticket Duplicates:** Implemented idempotent POST-Redirect-GET submission flow.
5. **Multi-Tenant Security:** Enforced strict session-derived authorization across all database queries.

---

## E. Security Hardening
- **`.htaccess`:** Protects `.env`, `.sql`, `database/`, `.git`, and log files. Enforces HTTPS and adds `X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection` headers.
- **`uploads/.htaccess`:** Disallows direct execution of any `.php` or script files in the uploads folder.
- **Installer Lock:** Automatic generation of `install/installed.lock` to prevent unauthorized re-installation.
- **Database Error Obfuscation:** In production (`APP_DEBUG=false`), database connection exceptions are safely caught and generic friendly messages are shown without exposing internal credentials.

---

## F. Deployment & Packaging
- Ready-to-upload archive: **`StayFlow-InfinityFree-Ready.zip`**
- All tests passing with 100% success rate.
