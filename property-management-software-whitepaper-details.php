<?php
/**
 * StayFlow — Dynamic Whitepaper Details Page
 * Driven by database-managed reports from Super Admin.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/pg-management-system/config/database.php';
require_once __DIR__ . '/pg-management-system/services/SlugService.php';

$db = getDB();

// 1. Extract requested slug
$slug = trim($_GET['slug'] ?? '');

// 2. Check 301 Permanent Redirect History for changed slugs
if (!empty($slug)) {
    $redirectTarget = SlugService::resolveRedirect($db, 'whitepaper', $slug);
    if ($redirectTarget && $redirectTarget !== $slug) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: /whitepaper/" . urlencode($redirectTarget));
        exit();
    }
}

// 3. Query Whitepaper
$wp = null;
$isPreview = isset($_GET['preview']);

if (!empty($slug)) {
    if ($isPreview) {
        $stmtWp = $db->prepare("SELECT * FROM `whitepapers` WHERE `slug` = ? LIMIT 1");
        $stmtWp->execute([$slug]);
    } else {
        $stmtWp = $db->prepare("SELECT * FROM `whitepapers` WHERE `slug` = ? AND `status` = 'published' LIMIT 1");
        $stmtWp->execute([$slug]);
    }
    $wp = $stmtWp->fetch(PDO::FETCH_ASSOC);
}

// Fallback: load latest published whitepaper
if (!$wp) {
    $stmtLatest = $db->query("SELECT * FROM `whitepapers` WHERE `status` = 'published' ORDER BY `published_at` DESC, `id` DESC LIMIT 1");
    $wp = $stmtLatest->fetch(PDO::FETCH_ASSOC);
}

if (!$wp) {
    header("HTTP/1.0 404 Not Found");
    echo "<!DOCTYPE html><html><head><title>Whitepaper Not Found | StayFlow</title></head><body style='font-family:sans-serif;text-align:center;padding:100px;'><h1>Whitepaper Not Found</h1><p>The requested research report does not exist or has been unpublished.</p><a href='/property-management-software-whitepaper.php'>Browse All Whitepapers</a></body></html>";
    exit();
}

// Parse learning points & takeaways
$learningPoints = [];
if (!empty($wp['learning_points_json'])) {
    $learningPoints = json_decode($wp['learning_points_json'], true) ?: [];
}
$keyTakeaways = [];
if (!empty($wp['key_takeaways_json'])) {
    $keyTakeaways = json_decode($wp['key_takeaways_json'], true) ?: [];
}

// SEO & Metadata Configuration
$page_title = !empty($wp['seo_title']) ? $wp['seo_title'] : ($wp['title'] . ' | StayFlow Whitepaper');
$page_description = !empty($wp['meta_description']) ? $wp['meta_description'] : (!empty($wp['excerpt']) ? $wp['excerpt'] : substr(strip_tags($wp['overview'] ?? ''), 0, 160));
$page_keywords = !empty($wp['focus_keyword']) ? $wp['focus_keyword'] : 'StayFlow whitepaper, PG industry research, hostel technology report';

$canonicalDomain = defined('CANONICAL_DOMAIN') ? CANONICAL_DOMAIN : 'https://stayflow.antideploy.app';
$canonicalSlug = !empty($wp['slug']) ? $wp['slug'] : 'scaling-securely-in-the-cloud';
$page_canonical = !empty($wp['canonical_url']) ? $wp['canonical_url'] : ($canonicalDomain . '/whitepaper/' . $canonicalSlug);

$coverImg = !empty($wp['featured_image']) ? $wp['featured_image'] : 'images/ns-img-404.jpg';
$page_og_image = !empty($wp['og_image']) ? ($canonicalDomain . '/' . ltrim($wp['og_image'], '/')) : ($canonicalDomain . '/' . ltrim($coverImg, '/'));
$page_og_type = 'article';

// Dynamic JSON-LD Structured Data
$schemaPublished = !empty($wp['published_at']) ? date('c', strtotime($wp['published_at'])) : date('c', strtotime($wp['created_at']));
$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'Report',
    'headline' => $wp['title'],
    'description' => $page_description,
    'image' => [$page_og_image],
    'datePublished' => $schemaPublished,
    'author' => [
        '@type' => 'Organization',
        'name' => $wp['author'] ?? 'StayFlow Research'
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'StayFlow',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $canonicalDomain . '/images/shared/main-logo.svg'
        ]
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => $page_canonical
    ]
];
$extra_head = '<script type="application/ld+json">' . json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

include __DIR__ . '/includes/header.php';
?>

<main>
  <section class="pt-[100px] pb-16 lg:pt-[140px] lg:pb-20 xl:pt-[170px] xl:pb-28">
    <div class="main-container">
      <div class="space-y-5">
        <span data-ns-animate="" data-delay="0.1" class="badge badge-green-v2">
          <?= htmlspecialchars($wp['badge_text'] ?? 'Industry Research') ?>
        </span>
        <div class="space-y-4">
          <h1 data-ns-animate="" data-delay="0.2" class="font-normal">
            <?= htmlspecialchars($wp['title']) ?>
          </h1>
          <p data-ns-animate="" data-delay="0.3">
            <?= nl2br(htmlspecialchars($wp['excerpt'] ?? '')) ?>
          </p>
        </div>
      </div>
    </div>
  </section>

  <section data-ns-animate="" data-delay="0.4">
    <div class="main-container">
      <div class="bg-background-3 dark:bg-background-7 flex flex-col-reverse items-center justify-between gap-x-10 rounded-4xl p-2 lg:flex-row xl:gap-x-14">
        <div class="w-full p-6 lg:max-w-[560px]">
          <div class="mb-8 space-y-5">
            <h2 class="text-heading-5 font-normal">Overview</h2>
            <p class="text-secondary/80 dark:text-accent/80">
              <?= nl2br(htmlspecialchars($wp['overview'] ?? '')) ?>
            </p>
          </div>
          <div>
            <h3 class="text-heading-5 font-normal">You’ll learn how to</h3>
            <ul class="mt-4 mb-10.5 space-y-3">
              <?php if (!empty($learningPoints)): ?>
                <?php foreach ($learningPoints as $pt): ?>
                <li class="flex items-center gap-x-2">
                  <span class="bg-secondary dark:bg-accent flex size-5 shrink-0 items-center justify-center rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewbox="0 0 10 7" fill="none">
                      <path d="M4.31661 6.75605L9.74905 1.42144C10.0836 1.0959 10.0836 0.569702 9.74905 0.244158C9.41446 -0.081386 8.87363 -0.081386 8.53904 0.244158L3.7116 4.99012L1.46096 2.78807C1.12636 2.46253 0.585538 2.46253 0.250945 2.78807C-0.0836483 3.11362 -0.0836483 3.63982 0.250945 3.96536L3.1066 6.75605C3.27347 6.91841 3.49253 7 3.7116 7C3.93067 7 4.14974 6.91841 4.31661 6.75605Z" class="fill-accent dark:fill-secondary"></path>
                    </svg>
                  </span>
                  <p class="text-secondary/80 dark:text-accent/80">
                    <?= htmlspecialchars($pt) ?>
                  </p>
                </li>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="flex items-center gap-x-2">
                  <p class="text-secondary/80 dark:text-accent/80">Comprehensive multi-property operational frameworks.</p>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
        <figure class="min-h-[300px] w-full overflow-hidden rounded-[20px] lg:min-h-[490px] lg:max-w-[613px]">
          <img src="<?= htmlspecialchars($coverImg) ?>" alt="<?= htmlspecialchars($wp['title']) ?>" class="h-full min-h-[300px] w-full object-cover lg:min-h-[490px]">
        </figure>
      </div>
    </div>
  </section>

  <section data-ns-animate="" data-delay="0.5" class="xl:py-19 py-16">
    <div class="main-container">
      <h2 class="text-heading-5 font-normal">Key takeaways</h2>
      <ul class="py-6">
        <?php if (!empty($keyTakeaways)): ?>
          <?php foreach ($keyTakeaways as $idx => $kw): ?>
          <li class="flex items-center gap-2 p-3">
            <div class="size-11 bg-background-4 dark:bg-background-7 rounded-full flex items-center justify-center shrink-0 p-1">
              <div class="size-9 bg-white dark:bg-background-5 rounded-full flex items-center justify-center shrink-0 shadow-[0_1px_2px_0_rgba(0,0,0,0.15)] text-tagline-1 font-medium text-secondary dark:text-accent">
                <?= $idx + 1 ?>
              </div>
            </div>
            <p class="text-secondary/80 dark:text-accent/80">
              <?= htmlspecialchars($kw) ?>
            </p>
          </li>
          <?php if ($idx < count($keyTakeaways) - 1): ?>
          <li><div class="h-px bg-stroke-1 dark:bg-background-7 w-full"></div></li>
          <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
      <?php if (!empty($wp['content'])): ?>
      <div class="max-w-3xl w-full text-secondary/90 dark:text-accent/90">
        <?= $wp['content'] ?>
      </div>
      <?php endif; ?>

      <div class="mt-14">
        <div data-ns-animate="" data-delay="0.4" data-direction="left" data-offset="50" data-instant="" class="max-w-max">
          <?php if (!empty($wp['pdf_file'])): ?>
          <a href="<?= htmlspecialchars($wp['pdf_file']) ?>" target="_blank" class="btn btn-secondary hover:btn-primary dark:hover:btn-primary dark:btn-accent btn-lg md:btn-xl w-full md:w-auto mx-auto md:mx-0">
            <i class="bi bi-file-earmark-arrow-down me-2"></i><span>Download Whitepaper (PDF)</span>
          </a>
          <?php else: ?>
          <a href="/property-management-software-pricing.php" class="btn btn-secondary hover:btn-primary dark:hover:btn-primary dark:btn-accent btn-lg md:btn-xl w-full md:w-auto mx-auto md:mx-0">
            <span>Get Started with StayFlow</span>
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Footer v3 -->
<?php include __DIR__ . '/includes/footer.php'; ?>
