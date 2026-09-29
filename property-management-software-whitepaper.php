<?php
/**
 * StayFlow — Dynamic Whitepapers Listing Page
 * Dynamically queries published whitepapers from the database.
 */

require_once __DIR__ . '/includes/config.php';
$page_title = 'StayFlow Whitepapers - Industry Guides on Property Tech';
$page_description = 'Download in-depth whitepapers and research reports on modernizing PG operations, co-living technology trends, and smart hostel management.';
$page_keywords = 'StayFlow whitepapers, PG industry research, hostel technology report';
$page_canonical = 'https://stayflow.antideploy.app/property-management-software-whitepaper.php';
$page_og_image = 'https://images.prismic.io/staticmania/aPD-K55xUNkB2D2X_og-image.jpg';

// Fetch published whitepapers from database
$whitepapers = [];
try {
    require_once __DIR__ . '/pg-management-system/config/database.php';
    $db = getDB();
    $stmtW = $db->query("SELECT * FROM `whitepapers` WHERE `status` = 'published' ORDER BY `published_at` DESC, `id` DESC");
    $whitepapers = $stmtW->fetchAll(PDO::FETCH_ASSOC);
} catch (\Throwable $e) {
    // Fallback gracefully
}

include __DIR__ . '/includes/header.php';
?>

<main>
  <section class="pt-[100px] pb-16 lg:pt-[140px] lg:pb-20 xl:pt-[170px] xl:pb-28">
    <div class="main-container">
      <div class="space-y-6 text-center">
        <span data-ns-animate="" data-delay="0.1" class="badge badge-green-v2">Whitepapers &amp; Insights</span>
        <div class="space-y-4">
          <h1 data-ns-animate="" data-delay="0.2" class="font-normal">
            Deep research. Real data.<br>
            Actionable property management strategies.
          </h1>
          <p data-ns-animate="" data-delay="0.3">
            Explore our research whitepapers packed with expert insights, operational metrics, and growth playbooks for modern hostel &amp; co-living operators.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="mx-5">
      <div class="max-w-[1880px] mx-auto bg-background-12 dark:bg-background-7 rounded-[30px] xl:py-[174px] lg:py-[120px] md:py-20 py-16">
        <div class="main-container">
          <div class="grid grid-cols-12 gap-6 items-stretch">
            <?php if (!empty($whitepapers)): 
                $featuredWp = $whitepapers[0];
                $featUrl = '/whitepaper/' . htmlspecialchars($featuredWp['slug']);
                $featImg = !empty($featuredWp['featured_image']) ? htmlspecialchars($featuredWp['featured_image']) : 'images/ns-img-405.jpg';
            ?>
            <!-- Featured Whitepaper Card -->
            <div data-ns-animate="" data-delay="0.1" class="col-span-12">
              <article class="p-2 rounded-4xl w-full bg-white dark:bg-background-6 group flex md:flex-row flex-col items-start gap-5 justify-between">
                <div class="max-w-[472px] w-full p-5 flex flex-col justify-between gap-y-5 h-full items-start md:min-h-[405px]">
                  <div>
                    <span class="badge badge-green mb-3"><?= htmlspecialchars($featuredWp['badge_text'] ?? 'Industry Report') ?></span>
                    <h2 class="text-heading-5 font-normal">
                      <?= htmlspecialchars($featuredWp['title']) ?>
                    </h2>
                    <p class="mt-2 text-secondary/70 dark:text-accent/70">
                      <?= htmlspecialchars($featuredWp['excerpt'] ?? '') ?>
                    </p>
                  </div>
                  <div>
                    <a href="<?= $featUrl ?>" class="btn btn-md btn-accent">
                      <span>Read More</span>
                    </a>
                  </div>
                </div>
                <figure class="md:max-w-[613px] w-full md:min-h-[405px] rounded-[20px] overflow-hidden">
                  <img src="<?= $featImg ?>" alt="<?= htmlspecialchars($featuredWp['title']) ?>" class="w-full h-full object-cover rounded-[20px] md:min-h-[405px] group-hover:scale-105 transition-all duration-500 ease-in-out">
                </figure>
              </article>
            </div>

            <!-- Remaining Whitepapers in Grid -->
            <?php foreach (array_slice($whitepapers, 1) as $wCard): 
                $wUrl = '/whitepaper/' . htmlspecialchars($wCard['slug']);
                $wImg = !empty($wCard['featured_image']) ? htmlspecialchars($wCard['featured_image']) : 'images/ns-img-406.jpg';
            ?>
            <div data-ns-animate="" data-delay="0.2" class="col-span-12 sm:col-span-6 lg:col-span-4">
              <article class="p-2 bg-white dark:bg-background-6 rounded-[20px] h-full flex flex-col">
                <figure class="rounded-xl overflow-hidden flex-shrink-0 aspect-[4/3]">
                  <img src="<?= $wImg ?>" alt="<?= htmlspecialchars($wCard['title']) ?>" class="w-full h-full object-cover rounded-xl">
                </figure>
                <div class="pt-7 pb-6 px-5 space-y-10 flex-grow flex flex-col">
                  <div class="space-y-2 flex-grow">
                    <span class="badge badge-cyan text-xs mb-1"><?= htmlspecialchars($wCard['badge_text'] ?? 'Research') ?></span>
                    <h2 class="text-heading-6 font-normal line-clamp-2"><?= htmlspecialchars($wCard['title']) ?></h2>
                    <p class="text-secondary/70 dark:text-accent/70 line-clamp-3">
                      <?= htmlspecialchars($wCard['excerpt'] ?? '') ?>
                    </p>
                  </div>
                  <div>
                    <a href="<?= $wUrl ?>" class="btn btn-md btn-accent">
                      <span>Read More</span>
                    </a>
                  </div>
                </div>
              </article>
            </div>
            <?php endforeach; ?>

            <?php else: ?>
            <div class="col-span-12 text-center py-10 text-muted">
              <h3>No published whitepapers found.</h3>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Footer v3 -->
<?php include __DIR__ . '/includes/footer.php'; ?>
