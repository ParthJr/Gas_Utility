<?php
$page_title = "Platform Features Overview - StayFlow PG Management";
$page_description = "Discover the complete feature suite of StayFlow PG Management software: property management, resident KYC, bed allocation, rent billing, maintenance ticketing, and reports.";
include 'includes/site_header.php';
?>

<main class="pt-24 pb-16">
  <section class="py-20 bg-slate-900 text-white text-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-xs font-semibold uppercase tracking-wider mb-6">
        <i class="fa-solid fa-cubes"></i> Feature Overview
      </span>
      <h1 class="text-4xl md:text-6xl font-extrabold mb-6">Built for Total Property Automation</h1>
      <p class="text-lg text-slate-300 max-w-3xl mx-auto mb-8">
        Every tool, dashboard, and notification system in StayFlow is optimized to save hours of manual admin work.
      </p>
    </div>
  </section>

  <section class="py-16 bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid md:grid-cols-3 gap-8">
        <a href="features-property.php" class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 transition-all block group">
          <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-all">
            <i class="fa-solid fa-building"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Property & Floor Setup</h3>
          <p class="text-slate-400 text-sm">Configure multi-floor buildings, room numbers, bed capacities, and amenities easily.</p>
        </a>

        <a href="features-resident.php" class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 transition-all block group">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-all">
            <i class="fa-solid fa-id-card"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Resident KYC & Portal</h3>
          <p class="text-slate-400 text-sm">Collect Aadhaar/ID copies, emergency contacts, and provide tenants with mobile login portals.</p>
        </a>

        <a href="features-room.php" class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 transition-all block group">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-all">
            <i class="fa-solid fa-bed"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Bed & Room Allocation</h3>
          <p class="text-slate-400 text-sm">Visual color-coded bed map showing occupied, vacant, and under-maintenance beds.</p>
        </a>

        <a href="features-rent.php" class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 transition-all block group">
          <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-all">
            <i class="fa-solid fa-file-invoice-dollar"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Automated Rent & Billing</h3>
          <p class="text-slate-400 text-sm">Auto-generate rent invoices on chosen due dates with electricity and penalty additions.</p>
        </a>

        <a href="features-payment.php" class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 transition-all block group">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-all">
            <i class="fa-solid fa-credit-card"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Instant Payment Tracking</h3>
          <p class="text-slate-400 text-sm">Record cash, UPI, GPay, and bank transfer payments with instant PDF receipt downloads.</p>
        </a>

        <a href="features-maintenance.php" class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 transition-all block group">
          <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-all">
            <i class="fa-solid fa-wrench"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Maintenance Ticketing</h3>
          <p class="text-slate-400 text-sm">Tenants raise issues with photos; assign to staff and track resolution time.</p>
        </a>
      </div>
    </div>
  </section>
</main>

<?php include 'includes/site_footer.php'; ?>
