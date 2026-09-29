<?php
$page_title = 'StayFlow Pricing - Flexible Plans for PG & Hostel Management Software';
$page_description = 'Transparent pricing plans for StayFlow PG Management System. Manage tenants, room allocations, automated rent collection, and operations for single or multi-branch properties.';
$page_keywords = 'PG Management System pricing, PG software plans, Hostel Management Software pricing, Paying Guest Management System cost, Property Management System subscription, StayFlow';
$page_canonical = 'https://stayflow.antideploy.app/property-management-software-pricing.php';
$page_og_image = 'https://images.prismic.io/staticmania/aPD-K55xUNkB2D2X_og-image.jpg';

require_once __DIR__ . '/includes/config.php';

// Safe Database Fetching with Graceful Fallback
$plans = [];
$categories = [
    'property' => 'Property & Building Operations',
    'resident' => 'Resident App & Portal',
    'billing' => 'Billing, Invoices & Payments',
    'communication' => 'Communication & Automation',
    'maintenance' => 'Maintenance & Workflows',
    'food' => 'Food, Kitchen & Dining',
    'security' => 'Security & QR Gate Pass',
    'reports' => 'Business Analytics & Reports',
    'saas' => 'Platform & Integrations'
];
$features = [];
$matrix = [];

try {
    require_once __DIR__ . '/pg-management-system/config/database.php';
    $db = getDB();

    // 1. Fetch all ACTIVE plans in defined display order (Super Admin is single source of truth)
    $stmtPlans = $db->query("
        SELECT id, plan_code, plan_slug, name, description, price_monthly, price_yearly, 
               setup_fee, currency, billing_type, display_order, is_recommended, 
               cta_text, cta_action, trial_enabled, trial_days, yearly_discount_pct, 
               max_buildings, max_rooms, max_beds, max_residents, max_staff, max_storage_gb, max_api_requests
        FROM plans 
        WHERE status = 'active'
        ORDER BY display_order ASC, id ASC
    ");
    $plans = $stmtPlans->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch all customer-facing features (strictly excluding internal admin/super-admin tools)
    $stmtFeat = $db->query("
        SELECT id, feature_key, feature_name, description, category, sort_order
        FROM features 
        WHERE is_active = 1 AND status = 'active'
          AND category IN ('property', 'resident', 'billing', 'communication', 'maintenance', 'food', 'security', 'reports', 'saas')
          AND feature_key NOT IN ('audit_security_logs', 'internal_plan_management', 'customer_overrides', 'super_admin', 'platform_overview')
        ORDER BY CASE category 
            WHEN 'property' THEN 1 
            WHEN 'resident' THEN 2 
            WHEN 'billing' THEN 3 
            WHEN 'communication' THEN 4 
            WHEN 'maintenance' THEN 5 
            WHEN 'food' THEN 6 
            WHEN 'security' THEN 7 
            WHEN 'reports' THEN 8 
            WHEN 'saas' THEN 9 
            ELSE 10 END, sort_order ASC, id ASC
    ");
    $features = $stmtFeat->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch plan-feature matrix
    $stmtPf = $db->query("
        SELECT pf.plan_id, pf.feature_id, pf.is_enabled, pf.limit_value
        FROM plan_features pf
        JOIN plans p ON pf.plan_id = p.id
        WHERE p.status = 'active'
    ");
    while ($row = $stmtPf->fetch(PDO::FETCH_ASSOC)) {
        $matrix[(int)$row['feature_id']][(int)$row['plan_id']] = [
            'is_enabled' => (int)$row['is_enabled'],
            'limit_value' => trim((string)($row['limit_value'] ?? ''))
        ];
    }
} catch (\Throwable $e) {
    error_log('[Pricing Page DB Notice] ' . $e->getMessage());
    // Safe graceful fallback plans in case DB is temporarily unreachable
    $plans = [
        [
            'id' => 1, 'plan_code' => 'STARTER', 'plan_slug' => 'starter', 'name' => 'Starter',
            'description' => 'Ideal for single property owners and boutique co-living spaces.',
            'price_monthly' => 1950, 'price_yearly' => 19500, 'currency' => 'INR', 'billing_type' => 'recurring', 'is_recommended' => 0,
            'cta_text' => 'Start Free Trial', 'cta_action' => 'signup', 'trial_enabled' => 1, 'trial_days' => 14,
            'max_buildings' => 2, 'max_beds' => 100, 'max_staff' => 2, 'max_residents' => 100, 'max_rooms' => -1
        ],
        [
            'id' => 2, 'plan_code' => 'GROWTH', 'plan_slug' => 'growth', 'name' => 'Growth',
            'description' => 'For growing co-living operators with multiple properties.',
            'price_monthly' => 4999, 'price_yearly' => 49990, 'currency' => 'INR', 'billing_type' => 'recurring', 'is_recommended' => 1,
            'cta_text' => 'Start Free Trial', 'cta_action' => 'signup', 'trial_enabled' => 1, 'trial_days' => 14,
            'max_buildings' => 5, 'max_beds' => 200, 'max_staff' => 5, 'max_residents' => 200, 'max_rooms' => 100
        ],
        [
            'id' => 3, 'plan_code' => 'PRO', 'plan_slug' => 'pro', 'name' => 'Scale',
            'description' => 'Comprehensive multi-property automation, dining, and QR passes.',
            'price_monthly' => 7999, 'price_yearly' => 79990, 'currency' => 'INR', 'billing_type' => 'recurring', 'is_recommended' => 0,
            'cta_text' => 'Start Free Trial', 'cta_action' => 'signup', 'trial_enabled' => 1, 'trial_days' => 14,
            'max_buildings' => 15, 'max_beds' => 600, 'max_staff' => 15, 'max_residents' => 600, 'max_rooms' => 300
        ],
        [
            'id' => 4, 'plan_code' => 'CUSTOM', 'plan_slug' => 'custom', 'name' => 'Enterprise',
            'description' => 'Bespoke deployments, custom SLA, and full REST API access.',
            'price_monthly' => 9999, 'price_yearly' => 99990, 'currency' => 'INR', 'billing_type' => 'custom', 'is_recommended' => 0,
            'cta_text' => 'Contact Sales', 'cta_action' => 'whatsapp', 'trial_enabled' => 0, 'trial_days' => 0,
            'max_buildings' => -1, 'max_beds' => -1, 'max_staff' => -1, 'max_residents' => -1, 'max_rooms' => -1
        ]
    ];
    $features = [
        ['id' => 1, 'category' => 'property', 'feature_name' => 'Room & Bed Occupancy Grid'],
        ['id' => 2, 'category' => 'resident', 'feature_name' => 'Resident App & Digital KYC'],
        ['id' => 3, 'category' => 'billing', 'feature_name' => 'Automated Rent Invoicing & Receipts'],
        ['id' => 4, 'category' => 'communication', 'feature_name' => 'WhatsApp Rent Due Reminders'],
        ['id' => 5, 'category' => 'maintenance', 'feature_name' => 'Resident Ticket & Complaint Helpdesk'],
        ['id' => 6, 'category' => 'food', 'feature_name' => 'Meal Planning & Kitchen Opt-In'],
        ['id' => 7, 'category' => 'security', 'feature_name' => 'Digital Gate Pass with QR Scanning'],
        ['id' => 8, 'category' => 'reports', 'feature_name' => 'Revenue & Financial Analytics']
    ];
    foreach ($features as $df) {
        foreach ([1, 2, 3, 4] as $pId) {
            $matrix[$df['id']][$pId] = ['is_enabled' => 1, 'limit_value' => 'Included'];
        }
    }
}

function getPlanCurrencySymbol(string $curr): string {
    switch (strtoupper($curr)) {
        case 'INR': return '₹';
        case 'USD': return '$';
        case 'GBP': return '£';
        case 'EUR': return '€';
        default: return $curr . ' ';
    }
}

// Group features by category
$featuresByCategory = [];
foreach ($features as $f) {
    $cat = $f['category'];
    if (isset($categories[$cat])) {
        $featuresByCategory[$cat][] = $f;
    }
}

// Initial cycle detection (Monthly default, or URL query param)
$initialCycle = (isset($_GET['cycle']) && strtolower(trim((string)$_GET['cycle'])) === 'yearly') ? 'yearly' : 'monthly';
$isInitialYearly = ($initialCycle === 'yearly');

include __DIR__ . '/includes/header.php';
?>

<main>
  <!-- Pricing section -->
  <section data-ns-animate="" data-delay="0.1"
    class="pb-20 md:pb-[100px] lg:pb-[150px] xl:pb-[200px] xl:pt-[180px] md:pt-42 sm:pt-36 pt-32">
    <div
      class="max-w-[1440px] w-full mx-auto rounded-2xl space-y-[50px] bg-background-1 dark:bg-black py-[80px] px-5 md:px-6 lg:px-10 xl:px-16">
      
      <!-- Section Heading -->
      <div class="max-w-2xl mx-auto text-center space-y-3">
        <span data-ns-animate="" data-delay="0.2" class="badge badge-cyan-v2">Our pricing</span>
        <h1 data-ns-animate="" data-delay="0.3">PG Management System &amp; Hostel Software Pricing</h1>
        <p data-ns-animate="" data-delay="0.32" class="text-secondary/60 dark:text-accent/60 text-tagline-1">
          Simple, transparent, and scalable pricing plans tailored for PG owners, student hostels, and co-living operators.
        </p>

        <!-- Interactive Monthly / Yearly Toggle -->
        <div class="flex items-center justify-center pt-6 pb-2 relative z-30">
          <div class="inline-flex items-center justify-center gap-3 sm:gap-4 p-2 rounded-full bg-background-2 dark:bg-background-8 border border-stroke-3 dark:border-stroke-7 shadow-sm max-w-full">
            <span id="labelMonthly" 
                  onclick="setPricingCycle('monthly')" 
                  class="text-tagline-1 <?= $isInitialYearly ? 'font-medium text-secondary/60 dark:text-accent/60' : 'font-bold text-secondary dark:text-accent' ?> transition-colors cursor-pointer select-none py-1 px-3 rounded-full hover:opacity-80"
                  role="button" tabindex="0">
              Monthly
            </span>
            
            <button type="button" 
                    id="pricingBillingToggle" 
                    onclick="togglePricingCycle()" 
                    class="relative inline-flex h-8 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent <?= $isInitialYearly ? 'bg-primary' : 'bg-secondary/20 dark:bg-accent/20' ?> p-0.5 transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary align-middle" 
                    role="switch" 
                    aria-checked="<?= $isInitialYearly ? 'true' : 'false' ?>" 
                    aria-label="Toggle annual billing">
              <span id="pricingToggleThumb" class="pointer-events-none inline-block h-6 w-6 transform rounded-full <?= $isInitialYearly ? 'translate-x-6 bg-white' : 'translate-x-0 bg-primary' ?> shadow-md ring-0 transition-transform duration-300 ease-in-out"></span>
            </button>

            <span id="labelYearly" 
                  onclick="setPricingCycle('yearly')" 
                  class="text-tagline-1 <?= $isInitialYearly ? 'font-bold text-secondary dark:text-accent' : 'font-medium text-secondary/60 dark:text-accent/60' ?> transition-colors flex items-center gap-2 cursor-pointer select-none py-1 px-3 rounded-full hover:opacity-80"
                  role="button" tabindex="0">
              Yearly <span class="badge badge-cyan-v2 text-xs py-0.5 px-2.5 font-bold pointer-events-none shrink-0">Save up to 20%</span>
            </span>
          </div>
        </div>
      </div>


      <!-- Pricing Comparison Matrix Table -->
      <div class="overflow-x-auto pb-4 -mx-5 px-5 md:-mx-6 md:px-6 lg:-mx-10 lg:px-10 xl:-mx-16 xl:px-16">
        <table class="w-full border-collapse text-left" style="min-width: 1020px;">
          <!-- Table Header / Pricing Cards -->
          <thead>
            <tr>
              <th class="w-[300px] min-w-[280px] p-4 align-bottom bg-background-1 dark:bg-black sticky left-0 z-20">
                <div class="space-y-2">
                  <h3 class="text-heading-6 text-secondary dark:text-accent font-bold">What’s included</h3>
                  <p class="text-xs text-secondary/60 dark:text-accent/60">Compare live platform features & capacity across all StayFlow tiers.</p>
                </div>
              </th>
              <?php foreach ($plans as $plan): 
                $isCustom = (($plan['billing_type'] ?? '') === 'custom' || ($plan['plan_code'] ?? '') === 'CUSTOM');
                $planCtaUrl = $isCustom ? WHATSAPP_URL : ('/onboarding/wizard.php?plan=' . urlencode($plan['plan_slug'] ?? '') . '&cycle=' . $initialCycle);
                $planCtaText = $isCustom ? 'Contact Sales' : (!empty($plan['trial_enabled']) ? 'Start Free Trial' : 'Get Started');
                if (!empty($plan['cta_text']) && $plan['cta_text'] !== 'Book Now') {
                    $planCtaText = htmlspecialchars($plan['cta_text']);
                }
                $isRecommended = (int)($plan['is_recommended'] ?? 0) === 1;
                $displayPrice = $isInitialYearly ? (float)$plan['price_yearly'] : (float)$plan['price_monthly'];
                $displayCycle = $isInitialYearly ? '/year' : '/month';
              ?>
              <th class="p-3 align-top min-w-[220px]" style="width: calc((100% - 300px) / <?= max(1, count($plans)) ?>);">
                <?php if ($isRecommended): ?>
                <!-- Recommended / Popular Highlight Card -->
                <div class="rounded-[20px] relative py-8 px-6 bg-secondary overflow-hidden h-full flex flex-col justify-between shadow-xl border border-primary/30">
                  <div class="absolute z-20 h-full w-full -top-28 -right-20 pointer-events-none">
                    <img src="images/ns-img-496.png" alt="pricing bg" class="opacity-80">
                  </div>
                  <div class="relative z-30">
                    <div class="flex items-center justify-between mb-3">
                      <p class="text-tagline-1 text-accent font-bold">
                        <?= htmlspecialchars($plan['name']) ?>
                      </p>
                      <span class="badge badge-cyan-v2 text-xs py-0.5 px-2">Popular</span>
                    </div>
                    <div class="flex items-baseline gap-1 my-2">
                      <h3 class="text-heading-5 font-bold text-accent">
                        <span class="plan-price-val"
                              data-plan-code="<?= htmlspecialchars($plan['plan_code']) ?>"
                              data-currency="<?= htmlspecialchars(getPlanCurrencySymbol($plan['currency'])) ?>"
                              data-monthly="<?= number_format((float)$plan['price_monthly'], 0) ?>"
                              data-yearly="<?= number_format((float)$plan['price_yearly'], 0) ?>">
                          <?= getPlanCurrencySymbol($plan['currency']) . number_format($displayPrice, 0) ?>
                        </span>
                      </h3>
                      <span class="plan-cycle-text text-tagline-2 text-accent/80 font-medium"><?= $displayCycle ?></span>
                    </div>
                    <p class="text-accent/70 text-xs mt-2 line-clamp-2"><?= htmlspecialchars($plan['description']) ?></p>
                  </div>
                  <div class="relative z-30 pt-6">
                    <a href="<?= $planCtaUrl ?>"
                      data-base-url="<?= $planCtaUrl ?>"
                      data-is-custom="<?= $isCustom ? '1' : '0' ?>"
                      data-plan="<?= htmlspecialchars($plan['plan_slug']) ?>"
                      class="plan-cta-btn btn btn-primary hover:btn-white dark:hover:btn-accent btn-md w-full before:content-none first-letter:uppercase text-center font-bold">
                      <?= $planCtaText ?>
                    </a>
                  </div>
                </div>
                <?php else: ?>
                <!-- Standard Plan Card -->
                <div class="rounded-[20px] py-8 px-6 bg-background-3 dark:bg-background-8 h-full flex flex-col justify-between border border-stroke-4 dark:border-stroke-7">
                  <div>
                    <p class="text-tagline-1 text-secondary/70 dark:text-accent/70 font-semibold mb-3">
                      <?= htmlspecialchars($plan['name']) ?>
                    </p>
                    <div class="flex items-baseline gap-1 my-2">
                      <h3 class="text-heading-5 font-bold text-secondary dark:text-accent">
                        <span class="plan-price-val"
                              data-plan-code="<?= htmlspecialchars($plan['plan_code']) ?>"
                              data-currency="<?= htmlspecialchars(getPlanCurrencySymbol($plan['currency'])) ?>"
                              data-monthly="<?= number_format((float)$plan['price_monthly'], 0) ?>"
                              data-yearly="<?= number_format((float)$plan['price_yearly'], 0) ?>">
                          <?= getPlanCurrencySymbol($plan['currency']) . number_format($displayPrice, 0) ?>
                        </span>
                      </h3>
                      <span class="plan-cycle-text text-tagline-2 text-secondary/60 dark:text-accent/60 font-medium"><?= $displayCycle ?></span>
                    </div>
                    <p class="text-secondary/60 dark:text-accent/60 text-xs mt-2 line-clamp-2"><?= htmlspecialchars($plan['description']) ?></p>
                  </div>
                  <div class="pt-6">
                    <a href="<?= $planCtaUrl ?>"
                      data-base-url="<?= $planCtaUrl ?>"
                      data-is-custom="<?= $isCustom ? '1' : '0' ?>"
                      data-plan="<?= htmlspecialchars($plan['plan_slug']) ?>"
                      class="plan-cta-btn btn btn-white hover:btn-primary dark:btn-white-dark btn-md w-full before:content-none first-letter:uppercase text-center font-bold border border-stroke-4 dark:border-stroke-7">
                      <?= $planCtaText ?>
                    </a>
                  </div>
                </div>
                <?php endif; ?>
              </th>
              <?php endforeach; ?>
            </tr>
          </thead>

          <!-- Table Body / Features and Limits Matrix -->
          <tbody>
            <!-- Core Operational Limits Category -->
            <tr>
              <td colspan="<?= count($plans) + 1 ?>" class="py-3 px-4 text-xs font-extrabold uppercase tracking-wider text-secondary dark:text-accent bg-secondary/5 dark:bg-accent/5 rounded-lg border-y border-b-stroke-4 dark:border-stroke-7">
                Core Capacity & Platform Limits
              </td>
            </tr>

            <!-- Limit: Buildings / Properties -->
            <tr>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 font-medium text-secondary/80 dark:text-accent/80 text-tagline-1 bg-background-1 dark:bg-black sticky left-0 z-10">
                Properties / Buildings Allowed
              </td>
              <?php foreach ($plans as $p): ?>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 text-center font-bold text-secondary dark:text-accent text-sm">
                <?= (int)$p['max_buildings'] === -1 ? 'Unlimited' : ((int)$p['max_buildings'] . ' Properties') ?>
              </td>
              <?php endforeach; ?>
            </tr>

            <!-- Limit: Rooms -->
            <tr>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 font-medium text-secondary/80 dark:text-accent/80 text-tagline-1 bg-background-1 dark:bg-black sticky left-0 z-10">
                Rooms Capacity
              </td>
              <?php foreach ($plans as $p): ?>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 text-center font-bold text-secondary dark:text-accent text-sm">
                <?= (int)$p['max_rooms'] === -1 ? 'Unlimited' : ((int)$p['max_rooms'] . ' Rooms') ?>
              </td>
              <?php endforeach; ?>
            </tr>

            <!-- Limit: Beds -->
            <tr>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 font-medium text-secondary/80 dark:text-accent/80 text-tagline-1 bg-background-1 dark:bg-black sticky left-0 z-10">
                Beds Managed
              </td>
              <?php foreach ($plans as $p): ?>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 text-center font-bold text-secondary dark:text-accent text-sm">
                <?= (int)$p['max_beds'] === -1 ? 'Unlimited' : ((int)$p['max_beds'] . ' Beds') ?>
              </td>
              <?php endforeach; ?>
            </tr>

            <!-- Limit: Residents -->
            <tr>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 font-medium text-secondary/80 dark:text-accent/80 text-tagline-1 bg-background-1 dark:bg-black sticky left-0 z-10">
                Resident User Capacity
              </td>
              <?php foreach ($plans as $p): ?>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 text-center font-bold text-secondary dark:text-accent text-sm">
                <?= (int)$p['max_residents'] === -1 ? 'Unlimited' : ((int)$p['max_residents'] . ' Residents') ?>
              </td>
              <?php endforeach; ?>
            </tr>

            <!-- Limit: Staff -->
            <tr>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 font-medium text-secondary/80 dark:text-accent/80 text-tagline-1 bg-background-1 dark:bg-black sticky left-0 z-10">
                Staff & Manager Accounts
              </td>
              <?php foreach ($plans as $p): ?>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 text-center font-bold text-secondary dark:text-accent text-sm">
                <?= (int)$p['max_staff'] === -1 ? 'Unlimited' : ((int)$p['max_staff'] . ' Staff') ?>
              </td>
              <?php endforeach; ?>
            </tr>

            <!-- Dynamic Category Features Loops -->
            <?php foreach ($categories as $catKey => $catTitle): 
              if (empty($featuresByCategory[$catKey])) continue;
            ?>
            <tr>
              <td colspan="<?= count($plans) + 1 ?>" class="py-3 px-4 text-xs font-extrabold uppercase tracking-wider text-secondary dark:text-accent bg-secondary/5 dark:bg-accent/5 rounded-lg border-y border-b-stroke-4 dark:border-stroke-7">
                <?= htmlspecialchars($catTitle) ?>
              </td>
            </tr>

            <?php foreach ($featuresByCategory[$catKey] as $feat): 
              $fId = (int)$feat['id'];
            ?>
            <tr>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 font-normal text-secondary/80 dark:text-accent/80 text-tagline-1 bg-background-1 dark:bg-black sticky left-0 z-10">
                <?= htmlspecialchars($feat['feature_name']) ?>
              </td>
              <?php foreach ($plans as $p): 
                $pId = (int)$p['id'];
                $cell = $matrix[$fId][$pId] ?? ['is_enabled' => 0, 'limit_value' => ''];
                $isEnabled = (int)$cell['is_enabled'] === 1;
                $limitVal = $cell['limit_value'];
              ?>
              <td class="h-14 px-4 py-3 border-b border-b-stroke-4 dark:border-stroke-7 text-center">
                <?php if ($isEnabled): ?>
                  <?php if (!empty($limitVal)): ?>
                    <span class="font-bold text-xs text-secondary dark:text-accent"><?= htmlspecialchars($limitVal) ?></span>
                  <?php else: ?>
                    <!-- Active SVG Checkmark -->
                    <svg width="18" height="18" viewbox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0 mx-auto">
                      <rect width="18" height="18" rx="9" class="fill-secondary dark:fill-accent"></rect>
                      <path d="M8.31661 12.7561L13.7491 7.42144C14.0836 7.0959 14.0836 6.5697 13.7491 6.24416C13.4145 5.91861 12.8736 5.91861 12.539 6.24416L7.7116 10.9901L5.46096 8.78807C5.12636 8.46253 4.58554 8.46253 4.25095 8.78807C3.91635 9.11362 3.91635 9.63982 4.25095 9.96536L7.1066 12.7561C7.27347 12.9184 7.49253 13 7.7116 13C7.93067 13 8.14974 12.9184 8.31661 12.7561Z" class="fill-white dark:fill-black"></path>
                    </svg>
                  <?php endif; ?>
                <?php else: ?>
                  <!-- Disabled / Inactive SVG -->
                  <svg width="18" height="18" viewbox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0 mx-auto">
                    <rect width="18" height="18" rx="9" class="fill-secondary/20 dark:fill-accent/20"></rect>
                    <path d="M8.31661 12.7561L13.7491 7.42144C14.0836 7.0959 14.0836 6.5697 13.7491 6.24416C13.4145 5.91861 12.8736 5.91861 12.539 6.24416L7.7116 10.9901L5.46096 8.78807C5.12636 8.46253 4.58554 8.46253 4.25095 8.78807C3.91635 9.11362 3.91635 9.63982 4.25095 9.96536L7.1066 12.7561C7.27347 12.9184 7.49253 13 7.7116 13C7.93067 13 8.14974 12.9184 8.31661 12.7561Z" class="fill-white dark:fill-black"></path>
                  </svg>
                <?php endif; ?>
              </td>
              <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
          </tbody>

          <!-- Table Footer / Action Buttons -->
          <tfoot>
            <tr>
              <td class="p-4 sticky left-0 z-10 bg-background-1 dark:bg-black"></td>
              <?php foreach ($plans as $p): 
                $isCustom = (($p['billing_type'] ?? '') === 'custom' || ($p['plan_code'] ?? '') === 'CUSTOM');
                $planCtaUrl = $isCustom ? WHATSAPP_URL : ('/onboarding/wizard.php?plan=' . urlencode($p['plan_slug'] ?? '') . '&cycle=monthly');
                $planCtaText = $isCustom ? 'Contact Sales' : (!empty($p['trial_enabled']) ? 'Start Free Trial' : 'Get Started');
                if (!empty($p['cta_text']) && $p['cta_text'] !== 'Book Now') {
                    $planCtaText = htmlspecialchars($p['cta_text']);
                }
              ?>
              <td class="pt-8 pb-4 text-center">
                <div class="btn btn-primary hover:btn-white-dark dark:hover:btn-accent btn-md w-fit mx-auto">
                  <a href="<?= $planCtaUrl ?>"
                     data-base-url="<?= $planCtaUrl ?>"
                     data-is-custom="<?= $isCustom ? '1' : '0' ?>"
                     data-plan="<?= htmlspecialchars($p['plan_slug']) ?>"
                     class="plan-cta-btn">
                    <span><?= $planCtaText ?></span>
                  </a>
                </div>
              </td>
              <?php endforeach; ?>
            </tr>
          </tfoot>
        </table>
      </div>

    </div>
  </section>

  <!-- Interactive Monthly/Yearly Toggle Script -->
  <script>
    (function() {
      var currentCycle = 'monthly';

      window.setPricingCycle = function(cycle, persist) {
        if (cycle !== 'yearly' && cycle !== 'monthly') {
          cycle = 'monthly';
        }
        currentCycle = cycle;
        var isYearly = (cycle === 'yearly');

        var toggle = document.getElementById('pricingBillingToggle');
        var thumb = document.getElementById('pricingToggleThumb');
        var lblMonthly = document.getElementById('labelMonthly');
        var lblYearly = document.getElementById('labelYearly');

        if (toggle) {
          toggle.setAttribute('aria-checked', isYearly.toString());
          if (isYearly) {
            toggle.classList.remove('bg-secondary/20', 'dark:bg-accent/20');
            toggle.classList.add('bg-primary');
          } else {
            toggle.classList.remove('bg-primary');
            toggle.classList.add('bg-secondary/20', 'dark:bg-accent/20');
          }
        }

        if (thumb) {
          if (isYearly) {
            thumb.classList.remove('translate-x-0', 'bg-primary');
            thumb.classList.add('translate-x-6', 'bg-white');
          } else {
            thumb.classList.remove('translate-x-6', 'bg-white');
            thumb.classList.add('translate-x-0', 'bg-primary');
          }
        }

        if (lblMonthly) {
          if (isYearly) {
            lblMonthly.classList.remove('text-secondary', 'dark:text-accent', 'font-bold');
            lblMonthly.classList.add('text-secondary/60', 'dark:text-accent/60', 'font-medium');
          } else {
            lblMonthly.classList.remove('text-secondary/60', 'dark:text-accent/60', 'font-medium');
            lblMonthly.classList.add('text-secondary', 'dark:text-accent', 'font-bold');
          }
        }

        if (lblYearly) {
          if (isYearly) {
            lblYearly.classList.remove('text-secondary/60', 'dark:text-accent/60', 'font-medium');
            lblYearly.classList.add('text-secondary', 'dark:text-accent', 'font-bold');
          } else {
            lblYearly.classList.remove('text-secondary', 'dark:text-accent', 'font-bold');
            lblYearly.classList.add('text-secondary/60', 'dark:text-accent/60', 'font-medium');
          }
        }

        // Update all price numbers across all plans
        document.querySelectorAll('.plan-price-val').forEach(function(el) {
          var curr = el.getAttribute('data-currency') || '₹';
          var val = isYearly ? el.getAttribute('data-yearly') : el.getAttribute('data-monthly');
          if (val) {
            el.textContent = curr + val;
          }
        });

        // Update all billing cycle labels
        document.querySelectorAll('.plan-cycle-text').forEach(function(el) {
          el.textContent = isYearly ? '/year' : '/month';
        });

        // Update CTA links to include selected cycle
        document.querySelectorAll('.plan-cta-btn').forEach(function(btn) {
          var isCustom = btn.getAttribute('data-is-custom') === '1';
          var baseUrl = btn.getAttribute('data-base-url');
          if (!isCustom && baseUrl && baseUrl.indexOf('signup.php') !== -1) {
            var cleanUrl = baseUrl.replace(/([?&])cycle=(monthly|yearly)(&|$)/, '$1').replace(/[?&]$/, '');
            var sep = cleanUrl.indexOf('?') !== -1 ? '&' : '?';
            btn.setAttribute('href', cleanUrl + sep + 'cycle=' + cycle);
          }
        });

        // Persistence in localStorage and URL
        if (persist !== false) {
          try {
            localStorage.setItem('stayflow_pricing_cycle', cycle);
          } catch (e) {}

          if (window.history && window.history.replaceState) {
            try {
              var u = new URL(window.location.href);
              u.searchParams.set('cycle', cycle);
              window.history.replaceState(null, '', u.toString());
            } catch (e) {}
          }
        }
      };

      window.togglePricingCycle = function() {
        var next = (currentCycle === 'yearly') ? 'monthly' : 'yearly';
        window.setPricingCycle(next, true);
      };

      function initCycle() {
        var initial = 'monthly';
        try {
          var params = new URLSearchParams(window.location.search);
          var qCycle = params.get('cycle');
          if (qCycle === 'yearly' || qCycle === 'monthly') {
            initial = qCycle;
          } else {
            var stored = localStorage.getItem('stayflow_pricing_cycle');
            if (stored === 'yearly' || stored === 'monthly') {
              initial = stored;
            }
          }
        } catch (e) {}

        window.setPricingCycle(initial, false);
      }

      // Run immediately and on DOM ready
      initCycle();
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCycle);
      }
      window.addEventListener('load', initCycle);
    })();
  </script>


  <!-- Client Feedback section -->
  <section>
    <div data-ns-animate="" data-delay="0.2"
      class="main-container text-center rounded-4xl py-[100px] -z-0 bg-background-3 dark:bg-background-5 overflow-hidden relative">
      <div class="absolute -top-[164%] rotate-[21deg] -left-[35%] w-full h-full -z-10 select-none pointer-events-none"
        aria-hidden="true">
        <img src="images/ns-img-498.png" alt="Decorative gradient background overlay" class="scale-[60%]">
      </div>

      <!-- Customer Testimonial -->
      <article class="text-center space-y-4 pb-10">
        <!-- Customer Avatars -->
        <div class="flex justify-center -space-x-2.5 cursor-pointer" role="group" aria-label="Customer avatars">
          <img class="inline-block size-[38px] rounded-full ring-2 ring-accent dark:ring-black bg-ns-yellow"
            src="images/ns-avatar-1.png" alt="Customer avatar 1" width="38" height="38">
          <img class="inline-block size-[38px] rounded-full ring-2 ring-accent dark:ring-black bg-ns-red"
            src="images/ns-avatar-2.png" alt="Customer avatar 2" width="38" height="38">
          <img class="inline-block size-[38px] rounded-full relative z-0 ring-2 ring-accent dark:ring-black bg-ns-green"
            src="images/ns-avatar-3.png" alt="Customer avatar 3" width="38" height="38">
          <div
            class="inline-flex items-center relative z-10 justify-center size-[38px] bg-ns-cyan rounded-full ring-2 ring-accent dark:ring-black text-secondary/80 dark:text-accent/80 text-tagline-3 font-medium">
            99+
          </div>
        </div>

        <!-- Trust Metrics -->
        <div>
          <p class="text-tagline-2 text-secondary dark:text-accent font-medium">Tushed by 20k+</p>
          <p class="text-tagline-3">Customers Across the Globe</p>
        </div>
      </article>

      <!-- Client Logos -->
      <div class="max-w-[1130px] mx-auto relative">
        <div
          class="absolute left-0 top-5 h-full w-[7%] bg-gradient-to-r from-background-3 to-transparent dark:from-background-5 z-40">
        </div>
        <div
          class="absolute right-0 top-5 h-full w-[7%] bg-gradient-to-l from-background-3 to-transparent dark:from-background-5 z-40">
        </div>

        <div class="logos-marquee-container">
          <div class="flex items-center justify-center border-t border-secondary/10 dark:border-accent/10 pt-10 gap-8"
            role="group">
            <figure class="min-w-[140px] md:min-w-[201px] ml-8">
              <img src="images/icons/client-logo-6.svg" alt="StayFlow" class="lg:w-auto inline-block dark:hidden"
                width="120" height="40">

              <img src="images/icons/client-logo-6-dark.svg" alt="StayFlow" class="lg:w-auto hidden dark:block"
                width="120" height="40">
            </figure>
            <figure class="min-w-[140px] md:min-w-[201px]">
              <img src="images/icons/client-logo-7.svg" alt="StayFlow" class="lg:w-auto inline-block dark:hidden"
                width="120" height="40">
              <img src="images/icons/client-logo-7-dark.svg" alt="StayFlow" class="lg:w-auto hidden dark:block"
                width="120" height="40">
            </figure>
            <figure class="min-w-[140px] md:min-w-[201px]">
              <img src="images/icons/client-logo-8.svg" alt="StayFlow" class="lg:w-auto inline-block dark:hidden"
                width="120" height="40">
              <img src="images/icons/client-logo-8-dark.svg" alt="StayFlow" class="lg:w-auto hidden dark:block"
                width="120" height="40">
            </figure>
            <figure class="min-w-[140px] md:min-w-[201px]">
              <img src="images/icons/client-logo-9.svg" alt="StayFlow" class="lg:w-auto inline-block dark:hidden"
                width="120" height="40">
              <img src="images/icons/client-logo-9-dark.svg" alt="StayFlow" class="lg:w-auto hidden dark:block"
                width="120" height="40">
            </figure>
            <figure class="min-w-[140px] md:min-w-[201px]">
              <img src="images/icons/client-logo-10.svg" alt="StayFlow" class="lg:w-auto inline-block dark:hidden"
                width="120" height="40">
              <img src="images/icons/client-logo-10-dark.svg" alt="StayFlow" class="lg:w-auto hidden dark:block"
                width="120" height="40">
            </figure>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ section -->
  <section class="py-[50px] md:py-[70px] lg:py-[85px] xl:pb-[100px] xl:pt-[200px]"
    aria-label="Frequently Asked Questions">
    <div class="main-container">
      <div class="flex items-center flex-col gap-8 lg:gap-0 lg:flex-row justify-between">
        <!-- faq content -->
        <div class="space-y-14 text-center lg:text-left flex-1">
          <!-- faq heading  -->
          <div class="space-y-5">
            <span data-ns-animate="" data-delay="0.2" class="badge badge-yellow-v2">FAQ</span>
            <h2 data-ns-animate="" data-delay="0.3" class="lg:max-w-[439px] mx-auto lg:mx-0" id="faq-heading">
              Common inquiries from clients
            </h2>
          </div>

          <!-- faq accordion  -->
          <div data-ns-animate="" data-delay="0.4" class="accordion max-w-[576px] mx-auto lg:mx-0 w-full" role="region">
            <div class="accordion-item active-accordion">
              <div class="dark:bg-black bg-white rounded-[20px] px-6 md:px-8 accordion-item">
                <button class="accordion-action flex items-center cursor-pointer justify-between py-6 md:py-8 w-full">
                  <span class="flex-1 text-left text-lg sm:text-heading-6 font-normal text-secondary dark:text-accent">
                    What is StayFlow and who is it for?
                  </span>
                  <span class="accordion-arrow ml-2.5 block sm:ml-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor"
                      width="16" height="16">
                      <path stroke-opacity="0.8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        d="m19.5 8.25-7.5 7.5-7.5-7.5" class="stroke-secondary dark:stroke-accent"></path>
                    </svg>
                  </span>
                </button>
                <div class="accordion-content">
                  <div class="border-t border-t-stroke-2 dark:border-t-stroke-6 pt-6 pb-8">
                    <p>StayFlow is an all-in-one PG &amp; Property Management Software designed for PG owners, hostel
                      managers, co-living operators, and student housing providers. It centralizes room allocation,
                      resident KYC, rent collection, WhatsApp reminders, and daily operations.</p>
                  </div>
                </div>
              </div>
              <div class="dark:bg-black bg-white rounded-[20px] px-6 md:px-8 accordion-item">
                <button class="accordion-action flex items-center cursor-pointer justify-between py-6 md:py-8 w-full">
                  <span class="flex-1 text-left text-lg sm:text-heading-6 font-normal text-secondary dark:text-accent">
                    Does StayFlow support automated rent reminders?
                  </span>
                  <span class="accordion-arrow ml-2.5 block sm:ml-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor"
                      width="16" height="16">
                      <path stroke-opacity="0.8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        d="m19.5 8.25-7.5 7.5-7.5-7.5" class="stroke-secondary dark:stroke-accent"></path>
                    </svg>
                  </span>
                </button>
                <div class="accordion-content">
                  <div class="border-t border-t-stroke-2 dark:border-t-stroke-6 pt-6 pb-8">
                    <p>Yes. StayFlow sends automated rent reminders via WhatsApp and SMS, reducing payment delays
                      without any manual follow-up from your staff.</p>
                  </div>
                </div>
              </div>
              <div class="dark:bg-black bg-white rounded-[20px] px-6 md:px-8 accordion-item">
                <button class="accordion-action flex items-center cursor-pointer justify-between py-6 md:py-8 w-full">
                  <span class="flex-1 text-left text-lg sm:text-heading-6 font-normal text-secondary dark:text-accent">
                    Can I manage multiple PG properties from one account?
                  </span>
                  <span class="accordion-arrow ml-2.5 block sm:ml-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor"
                      width="16" height="16">
                      <path stroke-opacity="0.8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        d="m19.5 8.25-7.5 7.5-7.5-7.5" class="stroke-secondary dark:stroke-accent"></path>
                    </svg>
                  </span>
                </button>
                <div class="accordion-content">
                  <div class="border-t border-t-stroke-2 dark:border-t-stroke-6 pt-6 pb-8">
                    <p>Absolutely. StayFlow supports multi-property management from a single dashboard, giving you full
                      visibility across all your PG buildings and locations.</p>
                  </div>
                </div>
              </div>
              <div class="dark:bg-black bg-white rounded-[20px] px-6 md:px-8 accordion-item">
                <button class="accordion-action flex items-center cursor-pointer justify-between py-6 md:py-8 w-full">
                  <span class="flex-1 text-left text-lg sm:text-heading-6 font-normal text-secondary dark:text-accent">
                    Is resident KYC and document verification included?
                  </span>
                  <span class="accordion-arrow ml-2.5 block sm:ml-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor"
                      width="16" height="16">
                      <path stroke-opacity="0.8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        d="m19.5 8.25-7.5 7.5-7.5-7.5" class="stroke-secondary dark:stroke-accent"></path>
                    </svg>
                  </span>
                </button>
                <div class="accordion-content">
                  <div class="border-t border-t-stroke-2 dark:border-t-stroke-6 pt-6 pb-8">
                    <p>Yes. StayFlow includes a built-in KYC module for collecting, storing, and verifying
                      government-issued ID documents digitally, keeping your resident database secure and compliant.</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <button class="accordion-action flex items-center cursor-pointer justify-between pt-6 pb-6 w-full">
                <span
                  class="flex-1 text-left xl:text-heading-6 text-tagline-1 font-normal text-secondary dark:text-accent">
                  What kinds of services should I anticipate ?
                </span>
                <!-- Accordian Icon -->
                <span class="accordion-arrow ml-2.5 block sm:ml-auto">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor"
                    width="16" height="16">
                    <path stroke-opacity="0.8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                      d="m19.5 8.25-7.5 7.5-7.5-7.5" class="stroke-secondary dark:stroke-accent"></path>
                  </svg>
                </span>

              </button>
              <div class="accordion-content">
                <div class="border-t border-t-stroke-3 dark:border-t-stroke-7 pt-6 pb-6">
                  <p>
                    When working with a business agency, you can typically anticipate a wide range of
                    services tailored to support and grow your business.
                  </p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <button class="accordion-action flex items-center cursor-pointer justify-between pt-6 pb-6 w-full">
                <span
                  class="flex-1 text-left xl:text-heading-6 text-tagline-1 font-normal text-secondary dark:text-accent">
                  How often should I consider updating my website?
                </span>
                <!-- Accordian Icon -->
                <span class="accordion-arrow ml-2.5 block sm:ml-auto">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor"
                    width="16" height="16">
                    <path stroke-opacity="0.8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                      d="m19.5 8.25-7.5 7.5-7.5-7.5" class="stroke-secondary dark:stroke-accent"></path>
                  </svg>
                </span>

              </button>
              <div class="accordion-content">
                <div class="border-t border-t-stroke-3 dark:border-t-stroke-7 pt-6 pb-6">
                  <p>
                    When collaborating with a business agency, you can generally expect an extensive
                    array of services designed to not only support your current operations but also to
                    foster growth and innovation within your business. These services often include
                    strategic planning, marketing solutions, financial consulting, and operational
                    improvements.
                  </p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <button class="accordion-action flex items-center cursor-pointer justify-between pt-6 pb-6 w-full">
                <span
                  class="flex-1 text-left xl:text-heading-6 text-tagline-1 font-normal text-secondary dark:text-accent">
                  How often is it recommended to refresh my website?
                </span>
                <!-- Accordian Icon -->
                <span class="accordion-arrow ml-2.5 block sm:ml-auto">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke="currentColor"
                    width="16" height="16">
                    <path stroke-opacity="0.8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                      d="m19.5 8.25-7.5 7.5-7.5-7.5" class="stroke-secondary dark:stroke-accent"></path>
                  </svg>
                </span>

              </button>
              <div class="accordion-content">
                <div class="border-t border-t-stroke-3 dark:border-t-stroke-7 pt-6 pb-6">
                  <p>
                    When collaborating with a business agency, you can generally expect an extensive
                    array of services designed to not only support your current operations but also to
                    foster growth and innovation within your business. These services often include
                    strategic planning, marketing solutions, financial consulting, and operational
                    improvements.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- faq image -->
        <figure data-ns-animate="" data-delay="0.3" class="w-full relative max-w-[684px] overflow-hidden flex-1">
          <img class="size-full object-cover dark:hidden" src="images/ns-img-52.png"
            alt="Business agency services illustration" loading="lazy">
          <img class="size-full object-cover dark:inline-block hidden" src="images/ns-img-dark-31.png"
            alt="Business agency services illustration" loading="lazy">
        </figure>
      </div>
    </div>
  </section>

  <!-- CTA v1 section -->
  <section class="py-[50px] md:py-20 lg:py-28 dark:bg-background-7 bg-accent" aria-label="Use Case Overview">
    <div class="main-container">
      <div class="flex items-center flex-col lg:flex-row justify-between">
        <div
          class="xl:max-w-[650px] lg:max-w-[476px] max-[400px]:max-w-[300px] w-full space-y-5 text-center lg:text-left">
          <span data-ns-animate="" data-delay="0.3" class="badge badge-green badge-cyan">Get started</span>
          <div class="space-y-3">
            <h2 data-ns-animate="" data-delay="0.4"
              class="text-secondary dark:text-accent text-heading-5 sm:text-heading-4 lg:text-heading-2">
              Build a complete website using the assistance
              <span class="text-primary-500 hidden">{=$span-text}</span>
            </h2>
            <p data-ns-animate="" data-delay="0.5">Start your free trial today and see your ideas come to life easily
              and creatively.</p>
          </div>
        </div>

        <div
          class="lg:basis-[466px] space-y-6 md:ml-0 xl:ml-[100px] pt-[40px] lg:pt-[67px] w-full sm:w-[80%] md:w-[60%]">
          <form data-ns-animate="" data-delay="0.6" action="<?php echo SAAS_SIGNUP_URL; ?>" method="get"
            class="flex items-center flex-col gap-5 sm:flex-row justify-start lg:gap-3">
            <input type="email" name="email" id="userEmail-cta-v1" placeholder="Enter your email" required=""
              class="px-[18px] shadow-1 h-12 py-3 placeholder:text-secondary/50 rounded-full border border-stroke-1 lg:max-w-[340px] md:w-[71%] w-full max-[376px]:w-full dark:border-stroke-7 dark:placeholder:text-accent/60 focus:outline-none focus:border-primary-600 dark:focus:border-primary-400 dark:text-accent placeholder:font-normal font-normal">

            <button type="submit"
              class="btn btn-md btn-primary h-12 w-full sm:w-[28%] lg:w-auto btn-primary hover:btn-secondary dark:hover:btn-accent">
              <span>Get started</span>
            </button>
          </form>
          <ul class="flex flex-row items-center justify-center gap-x-4 sm:gap-x-6 sm:gap-y-0 gap-y-5 lg:justify-start">
            <li data-ns-animate="" data-delay="0.7" class="flex items-center justify-center gap-2">
              <span
                class="size-[18px] bg-secondary dark:bg-accent rounded-full flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewbox="0 0 10 7" fill="none"
                  aria-hidden="true" class="fill-white dark:fill-secondary">
                  <path
                    d="M4.31661 6.75605L9.74905 1.42144C10.0836 1.0959 10.0836 0.569702 9.74905 0.244158C9.41446 -0.081386 8.87363 -0.081386 8.53904 0.244158L3.7116 4.99012L1.46096 2.78807C1.12636 2.46253 0.585538 2.46253 0.250945 2.78807C-0.0836483 3.11362 -0.0836483 3.63982 0.250945 3.96536L3.1066 6.75605C3.27347 6.91841 3.49253 7 3.7116 7C3.93067 7 4.14974 6.91841 4.31661 6.75605Z">
                  </path>
                </svg>
              </span>
              <p class="text-tagline-3 sm:text-tagline-2">No credit card required</p>
            </li>
            <li data-ns-animate="" data-delay="0.8" class="flex items-center justify-center gap-2">
              <span
                class="size-[18px] bg-secondary dark:bg-accent rounded-full flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewbox="0 0 10 7" fill="none"
                  aria-hidden="true" class="fill-white dark:fill-secondary">
                  <path
                    d="M4.31661 6.75605L9.74905 1.42144C10.0836 1.0959 10.0836 0.569702 9.74905 0.244158C9.41446 -0.081386 8.87363 -0.081386 8.53904 0.244158L3.7116 4.99012L1.46096 2.78807C1.12636 2.46253 0.585538 2.46253 0.250945 2.78807C-0.0836483 3.11362 -0.0836483 3.63982 0.250945 3.96536L3.1066 6.75605C3.27347 6.91841 3.49253 7 3.7116 7C3.93067 7 4.14974 6.91841 4.31661 6.75605Z">
                  </path>
                </svg>
              </span>
              <p class="text-tagline-3 sm:text-tagline-2">14-Day free trial</p>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>

</main>
<!-- Footer v3 -->

<?php include __DIR__ . '/includes/footer.php'; ?>