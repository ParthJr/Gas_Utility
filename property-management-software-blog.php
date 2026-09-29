<?php
/**
 * StayFlow — Dynamic Blog Listing Page
 * Dynamically queries published articles from the Super Admin database.
 */

require_once __DIR__ . '/includes/config.php';
$page_title = 'StayFlow Blog - PG Management Tips, Trends & Insights';
$page_description = 'Read expert articles on PG management strategies, automated rent collection tips, tenant KYC compliance, and hostel occupancy growth.';
$page_keywords = 'PG management software, hostel billing, rent management, StayFlow blog';
$page_canonical = 'https://stayflow.antideploy.app/property-management-software-blog.php';
$page_og_image = 'https://images.prismic.io/staticmania/aPD-K55xUNkB2D2X_og-image.jpg';

// Fetch published blogs from database
$blogs = [];
try {
    require_once __DIR__ . '/pg-management-system/config/database.php';
    $db = getDB();
    $stmtB = $db->query("SELECT * FROM `blogs` WHERE `status` = 'published' ORDER BY `published_at` DESC, `id` DESC");
    $blogs = $stmtB->fetchAll(PDO::FETCH_ASSOC);
} catch (\Throwable $e) {
    // Fallback gracefully
}

include __DIR__ . '/includes/header.php';
?>

<main>
  <!-- Article Blog Carousel Section -->
  <section class="pt-32 pb-14 sm:pt-36 md:pt-42 md:pb-16 lg:pb-[88px] xl:pt-[180px] xl:pb-[100px]">
    <div class="main-container">
      <div class="space-y-10 md:space-y-[70px]">
        <h1 data-ns-animate="" data-delay="0.2" class="mx-auto max-w-[700px] text-center">
          Latest articles published by StayFlow
        </h1>
        <div class="relative" data-ns-animate="" data-delay="0.3">
          <div class="swiper blog-article-swiper">
            <div class="swiper-wrapper">
              <?php if (!empty($blogs)): ?>
                <?php foreach (array_slice($blogs, 0, 4) as $slide): 
                    $slideUrl = '/blog/' . htmlspecialchars($slide['slug']);
                    $slideImg = !empty($slide['featured_image']) ? htmlspecialchars($slide['featured_image']) : 'images/ns-img-492.png';
                ?>
                <div class="swiper-slide">
                  <article class="scale-100 transition-transform duration-500 hover:scale-[99%] hover:transition-transform hover:duration-500">
                    <figure class="max-h-[550px] w-full overflow-hidden rounded-t-[20px]">
                      <img src="<?= $slideImg ?>" alt="<?= htmlspecialchars($slide['title']) ?>" class="h-full w-full object-cover">
                    </figure>
                    <div class="bg-background-1 dark:bg-background-6 space-y-6 rounded-b-[20px] px-4 py-8 md:p-8">
                      <div class="flex items-center gap-2">
                        <span class="badge badge-green mr-1"><?= htmlspecialchars($slide['category'] ?? 'General') ?></span>
                        <span rel="author" class="text-tagline-3 text-secondary/60 dark:text-accent/60 font-normal"><?= htmlspecialchars($slide['author'] ?? 'StayFlow') ?></span>
                        <span class="h-[6px] w-[5px] rounded-full bg-[#ECE8FF]"> </span>
                        <time datetime="<?= date('Y-m-d', strtotime($slide['published_at'] ?? $slide['created_at'])) ?>"
                          class="text-tagline-3 text-secondary/60 dark:text-accent/60 font-normal">
                          <?= date('F j, Y', strtotime($slide['published_at'] ?? $slide['created_at'])) ?>
                        </time>
                      </div>
                      <div>
                        <h3 class="sm:text-heading-5 text-tagline-1 mb-2 font-normal">
                          <a href="<?= $slideUrl ?>" aria-label="<?= htmlspecialchars($slide['title']) ?>">
                            <?= htmlspecialchars($slide['title']) ?>
                          </a>
                        </h3>
                        <p class="sm:text-tagline-1 text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal line-clamp-2">
                          <?= htmlspecialchars($slide['excerpt'] ?? '') ?>
                        </p>
                      </div>
                      <div>
                        <a href="<?= $slideUrl ?>"
                          class="btn btn-md btn-white hover:btn-primary dark:btn-transparent inline-block"
                          aria-label="<?= htmlspecialchars($slide['title']) ?>">
                          <span>Read more</span>
                        </a>
                      </div>
                    </div>
                  </article>
                </div>
                <?php endforeach; ?>
              <?php else: ?>
                <!-- Static Fallback Slide -->
                <div class="swiper-slide">
                  <article class="scale-100 transition-transform duration-500 hover:scale-[99%]">
                    <figure class="max-h-[550px] w-full overflow-hidden rounded-t-[20px]">
                      <img src="images/ns-img-492.png" alt="How to Automate PG Rent Collection" class="h-full w-full object-cover">
                    </figure>
                    <div class="bg-background-1 dark:bg-background-6 space-y-6 rounded-b-[20px] px-4 py-8 md:p-8">
                      <div class="flex items-center gap-2">
                        <span class="badge badge-green mr-1">Billing</span>
                        <span class="text-tagline-3 text-secondary/60 dark:text-accent/60 font-normal">StayFlow Team</span>
                      </div>
                      <div>
                        <h3 class="sm:text-heading-5 text-tagline-1 mb-2 font-normal">
                          <a href="/property-management-software-blog-details.php">How to Automate PG Rent Collection with WhatsApp</a>
                        </h3>
                      </div>
                    </div>
                  </article>
                </div>
              <?php endif; ?>
            </div>
            <div class="pagination-bullets mt-5 md:mt-14"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Our Blog Grid Section -->
  <section class="py-14 md:py-16 lg:py-[88px] xl:py-[100px]">
    <div class="main-container">
      <div class="text-center space-y-3 mb-10 md:mb-[70px]">
        <h2 data-ns-animate="" data-delay="0.1">
          Our recent
          <span class="text-primary-500 inline-block">news &amp; insights</span>
        </h2>

        <p data-ns-animate="" data-delay="0.2" class="max-w-[738px] mx-auto">
          Explore expert guidance, product innovations, and real-world case studies shaping hostel and co-living operations.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-7 md:gap-10">
        <?php if (!empty($blogs)): ?>
          <?php foreach ($blogs as $card): 
              $cardUrl = '/blog/' . htmlspecialchars($card['slug']);
              $cardImg = !empty($card['featured_image']) ? htmlspecialchars($card['featured_image']) : 'images/ns-img-428.png';
          ?>
          <article data-ns-animate="" data-delay="0.3" class="group">
            <div class="bg-background-1 dark:bg-background-6 rounded-[20px] overflow-hidden relative md:min-h-[552px] min-h-[480px] scale-100 hover:scale-[102%] transition-transform duration-500 hover:transition-transform hover:duration-500">
              <figure class="md:min-h-[260px] md:max-h-[260px] max-w-full xl:max-w-[409px] overflow-hidden">
                <img src="<?= $cardImg ?>" alt="<?= htmlspecialchars($card['title']) ?>"
                  loading="lazy" class="w-full h-full object-cover">
              </figure>
              <div class="px-4 py-6 md:p-6 space-y-6">
                <div class="flex items-center gap-2">
                  <span class="badge badge-green"><?= htmlspecialchars($card['category'] ?? 'General') ?></span>
                  <span rel="author" class="text-tagline-3 font-normal text-secondary/60 dark:text-accent/60 text-nowrap"><?= htmlspecialchars($card['author'] ?? 'StayFlow') ?></span>
                  <span class="w-[5px] h-[6px] bg-[#ECE8FF] rounded-full"> </span>
                  <time datetime="<?= date('Y-m-d', strtotime($card['published_at'] ?? $card['created_at'])) ?>"
                    class="text-tagline-3 font-normal text-secondary/60 dark:text-accent/60 text-nowrap">
                    <?= date('M d, Y', strtotime($card['published_at'] ?? $card['created_at'])) ?>
                  </time>
                </div>
                <div>
                  <h3 class="font-normal sm:text-heading-5 text-tagline-1 mb-2 line-clamp-2">
                    <a href="<?= $cardUrl ?>" aria-label="<?= htmlspecialchars($card['title']) ?>">
                      <?= htmlspecialchars($card['title']) ?>
                    </a>
                  </h3>
                  <p class="sm:text-tagline-1 text-tagline-2 font-normal text-secondary/60 dark:text-accent/60 line-clamp-2">
                    <?= htmlspecialchars($card['excerpt'] ?? '') ?>
                  </p>
                </div>
                <div>
                  <a href="<?= $cardUrl ?>"
                    class="btn btn-md btn-white hover:btn-primary dark:btn-transparent inline-block absolute bottom-6"
                    aria-label="<?= htmlspecialchars($card['title']) ?>">
                    <span>Read more</span>
                  </a>
                </div>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<!-- Footer v3 -->
<?php include __DIR__ . '/includes/footer.php'; ?>
