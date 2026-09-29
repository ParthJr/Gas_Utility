<?php
$page_title = "500 Application Error - StayFlow";
$page_description = "An unexpected error occurred while processing your request on StayFlow.";
include 'includes/site_header.php';
?>

<main class="pt-24 pb-16 min-h-[70vh] flex items-center justify-center">
  <div class="max-w-3xl mx-auto px-4 text-center">
    <div class="w-20 h-20 rounded-3xl bg-rose-500/10 text-rose-400 border border-rose-500/20 flex items-center justify-center text-4xl mx-auto mb-6">
      <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    <span class="inline-block px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 text-xs font-semibold uppercase tracking-wider mb-4 border border-amber-500/20">
      Error 500
    </span>
    <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6">Something Went Wrong</h1>
    <p class="text-lg text-slate-300 max-w-xl mx-auto mb-8 leading-relaxed">
      We experienced a temporary application issue. Please try refreshing the page or contact our support team.
    </p>
    <div class="flex flex-wrap justify-center gap-4">
      <a href="javascript:location.reload();" class="px-6 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-lg transition-all flex items-center gap-2">
        <i class="fa-solid fa-rotate-right"></i> Try Again
      </a>
      <a href="landing.php" class="px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold transition-all flex items-center gap-2">
        <i class="fa-solid fa-house"></i> Back to Home
      </a>
      <a href="contact.php" class="px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold transition-all flex items-center gap-2">
        <i class="fa-solid fa-headset"></i> Contact Support
      </a>
    </div>
  </div>
</main>

<?php include 'includes/site_footer.php'; ?>
