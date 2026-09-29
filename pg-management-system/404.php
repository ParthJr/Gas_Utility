<?php
$page_title = "404 Page Not Found - StayFlow";
$page_description = "The page you requested could not be found on StayFlow.";
include 'includes/site_header.php';
?>

<main class="pt-24 pb-16 min-h-[70vh] flex items-center justify-center">
  <div class="max-w-3xl mx-auto px-4 text-center">
    <div class="w-20 h-20 rounded-3xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center text-4xl mx-auto mb-6">
      <i class="fa-solid fa-compass"></i>
    </div>
    <span class="inline-block px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 text-xs font-semibold uppercase tracking-wider mb-4 border border-rose-500/20">
      Error 404
    </span>
    <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6">Page Not Found</h1>
    <p class="text-lg text-slate-300 max-w-xl mx-auto mb-8 leading-relaxed">
      Sorry, the page or link you are looking for has been moved, renamed, or does not exist.
    </p>
    <div class="flex flex-wrap justify-center gap-4">
      <a href="landing.php" class="px-6 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-lg transition-all flex items-center gap-2">
        <i class="fa-solid fa-house"></i> Back to Home
      </a>
      <a href="features.php" class="px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold transition-all flex items-center gap-2">
        <i class="fa-solid fa-cubes"></i> Explore Features
      </a>
      <a href="contact.php" class="px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold transition-all flex items-center gap-2">
        <i class="fa-solid fa-headset"></i> Contact Support
      </a>
    </div>
  </div>
</main>

<?php include 'includes/site_footer.php'; ?>
