<?php
header("Location: /property-management-software-referral-program.php", true, 301);
exit();
$page_title = "Referral Program - StayFlow";
$page_description = "Refer fellow PG owners to StayFlow and earn cash rewards or subscription discounts.";
include 'includes/site_header.php';
?>

<main class="pt-24 pb-16">
  <section class="py-20 bg-slate-900 text-white text-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-semibold uppercase tracking-wider mb-6">
        <i class="fa-solid fa-gift"></i> Refer & Earn
      </span>
      <h1 class="text-4xl md:text-5xl font-extrabold mb-6">Earn Rewards for Every PG Owner You Refer</h1>
      <p class="text-lg text-slate-300 max-w-3xl mx-auto mb-8">
        Love StayFlow? Recommend StayFlow to fellow property managers in your city and get 20% recurring referral commission or free subscription credits.
      </p>
      <a href="https://wa.me/917622008118?text=Hi%20StayFlow%2C%20I%27m%20interested%20in%20joining%20the%20Referral%20Program." target="_blank" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 font-bold text-white shadow-xl transition-all">
        <i class="fa-brands fa-whatsapp text-xl"></i> Join Referral Program
      </a>
    </div>
  </section>
</main>

<?php include 'includes/site_footer.php'; ?>
