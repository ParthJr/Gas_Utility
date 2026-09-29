<?php
$page_title = "Integrations & Tools - StayFlow PG Management";
$page_description = "Discover how StayFlow integrates with WhatsApp Cloud API, payment gateways (UPI, GPay), biometric locks, and accounting exports.";
include 'includes/site_header.php';
?>

<main class="pt-24 pb-16">
  <section class="py-20 bg-slate-900 text-white text-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-xs font-semibold uppercase tracking-wider mb-6">
        <i class="fa-solid fa-plug"></i> Ecosystem Integrations
      </span>
      <h1 class="text-4xl md:text-5xl font-extrabold mb-6">Connect Your Operational Ecosystem</h1>
      <p class="text-lg text-slate-300 max-w-3xl mx-auto mb-8">
        StayFlow connects seamlessly with WhatsApp Cloud messaging, UPI payment QR systems, and accounting exports.
      </p>
    </div>
  </section>

  <section class="py-16 bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid md:grid-cols-3 gap-8">
        <div class="p-8 rounded-2xl bg-slate-900 border border-slate-800">
          <div class="text-emerald-400 text-4xl mb-4"><i class="fa-brands fa-whatsapp"></i></div>
          <h3 class="text-xl font-bold text-white mb-2">WhatsApp Cloud Messaging</h3>
          <p class="text-slate-400 text-sm">Send official payment reminders and automated digital PDF rent receipts via WhatsApp API.</p>
        </div>

        <div class="p-8 rounded-2xl bg-slate-900 border border-slate-800">
          <div class="text-indigo-400 text-4xl mb-4"><i class="fa-solid fa-qrcode"></i></div>
          <h3 class="text-xl font-bold text-white mb-2">UPI & QR Code Payments</h3>
          <p class="text-slate-400 text-sm">Accept direct UPI payments via GPay, PhonePe, and Paytm with instant verification.</p>
        </div>

        <div class="p-8 rounded-2xl bg-slate-900 border border-slate-800">
          <div class="text-amber-400 text-4xl mb-4"><i class="fa-solid fa-file-csv"></i></div>
          <h3 class="text-xl font-bold text-white mb-2">Accounting & Excel Export</h3>
          <p class="text-slate-400 text-sm">Export monthly transaction ledgers directly to CSV or Excel for Tally and CA filings.</p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include 'includes/site_footer.php'; ?>
