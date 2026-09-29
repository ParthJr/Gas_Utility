<?php
/**
 * StayFlow — Dynamic Blog Details Page
 * Driven by database-managed articles from Super Admin.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/pg-management-system/config/database.php';
require_once __DIR__ . '/pg-management-system/services/SlugService.php';

$db = getDB();

// 1. Extract requested slug
$slug = trim($_GET['slug'] ?? '');

// 2. Check 301 Permanent Redirect History for changed slugs
if (!empty($slug)) {
    $redirectTarget = SlugService::resolveRedirect($db, 'blog', $slug);
    if ($redirectTarget && $redirectTarget !== $slug) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: /blog/" . urlencode($redirectTarget));
        exit();
    }
}

// 3. Query Blog from Database
$blog = null;
$isPreview = isset($_GET['preview']);

if (!empty($slug)) {
    if ($isPreview) {
        $stmtBlog = $db->prepare("SELECT * FROM `blogs` WHERE `slug` = ? LIMIT 1");
        $stmtBlog->execute([$slug]);
    } else {
        $stmtBlog = $db->prepare("SELECT * FROM `blogs` WHERE `slug` = ? AND `status` = 'published' LIMIT 1");
        $stmtBlog->execute([$slug]);
    }
    $blog = $stmtBlog->fetch(PDO::FETCH_ASSOC);
}

// Fallback: If no slug requested or draft requested without preview, load the latest published article
if (!$blog) {
    $stmtLatest = $db->query("SELECT * FROM `blogs` WHERE `status` = 'published' ORDER BY `published_at` DESC, `id` DESC LIMIT 1");
    $blog = $stmtLatest->fetch(PDO::FETCH_ASSOC);
}

// If database is completely empty
if (!$blog) {
    header("HTTP/1.0 404 Not Found");
    echo "<!DOCTYPE html><html><head><title>Article Not Found | StayFlow</title></head><body style='font-family:sans-serif;text-align:center;padding:100px;'><h1>Article Not Found</h1><p>The requested blog article does not exist or has been unpublished.</p><a href='/property-management-software-blog.php'>Browse All Articles</a></body></html>";
    exit();
}

// SEO & Metadata Configuration
$page_title = !empty($blog['seo_title']) ? $blog['seo_title'] : ($blog['title'] . ' | StayFlow');
$page_description = !empty($blog['meta_description']) ? $blog['meta_description'] : (!empty($blog['excerpt']) ? $blog['excerpt'] : substr(strip_tags($blog['content'] ?? ''), 0, 160));
$page_keywords = !empty($blog['focus_keyword']) ? $blog['focus_keyword'] : 'Property management software, StayFlow blog, PG management';

$canonicalDomain = defined('CANONICAL_DOMAIN') ? CANONICAL_DOMAIN : 'https://stayflow.antideploy.app';
$page_canonical = !empty($blog['canonical_url']) ? $blog['canonical_url'] : ($canonicalDomain . '/blog/' . $blog['slug']);

$imgSrc = !empty($blog['featured_image']) ? $blog['featured_image'] : 'images/ns-img-492.png';
$page_og_image = !empty($blog['og_image']) ? ($canonicalDomain . '/' . ltrim($blog['og_image'], '/')) : ($canonicalDomain . '/' . ltrim($imgSrc, '/'));
$page_og_type = 'article';

// Dynamic JSON-LD Structured Data
$schemaPublished = !empty($blog['published_at']) ? date('c', strtotime($blog['published_at'])) : date('c', strtotime($blog['created_at']));
$schemaModified = !empty($blog['updated_at']) ? date('c', strtotime($blog['updated_at'])) : $schemaPublished;
$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $blog['title'],
    'description' => $page_description,
    'image' => [$page_og_image],
    'datePublished' => $schemaPublished,
    'dateModified' => $schemaModified,
    'author' => [
        '@type' => 'Person',
        'name' => $blog['author'] ?? 'StayFlow Team'
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

// Social share links
$shareUrl = urlencode($page_canonical);
$shareTitle = urlencode($blog['title']);

include __DIR__ . '/includes/header.php';
?>

<main>
  <!-- Details Body -->
  <section class="pt-32 pb-14 sm:pt-36 md:pt-42 md:pb-16 lg:pb-[88px] xl:pt-[180px] xl:pb-[200px]">
    <div class="main-container">
      <div class="mx-auto max-w-[1209px] space-y-3">
        <h1 data-ns-animate="" data-delay="0.1" class="max-w-[884px]">
          <?= htmlspecialchars($blog['title']) ?>
        </h1>
        <div class="flex items-center gap-3">
          <figure data-ns-animate="" data-delay="0.2" class="size-12 overflow-hidden rounded-full bg-[#ECEAED]">
            <img src="<?= htmlspecialchars(!empty($blog['author_avatar']) ? $blog['author_avatar'] : 'images/ns-avatar-6.png') ?>" class="object-cover object-center" alt="<?= htmlspecialchars($blog['author'] ?? 'StayFlow') ?>'s avatar" width="48"
              height="48" loading="lazy">
          </figure>
          <div>
            <h3 data-ns-animate="" data-delay="0.3" class="text-tagline-1 font-medium"><?= htmlspecialchars($blog['author'] ?? 'StayFlow Team') ?></h3>
            <time datetime="<?= date('Y-m-d', strtotime($blog['published_at'] ?? $blog['created_at'])) ?>" data-ns-animate="" data-delay="0.4"
              class="text-tagline-2 text-secondary/60 dark:text-accent/60 flex items-center gap-2 font-normal">
              <?= date('F j, Y', strtotime($blog['published_at'] ?? $blog['created_at'])) ?> <span>•</span> <?= (int)($blog['read_time_min'] ?? 5) ?> min read
            </time>
          </div>
        </div>
      </div>
      <figure data-ns-animate="" data-delay="0.4"
        class="my-10 max-w-full overflow-hidden rounded-lg md:my-[70px] md:rounded-4xl">
        <img src="<?= htmlspecialchars($imgSrc) ?>" class="h-full w-full object-cover object-center"
          alt="<?= htmlspecialchars($blog['title']) ?>">
      </figure>

      <!-- Blog details-body (Database Driven) -->
      <article class="details-body">
        <?= $blog['content'] ?>
      </article>

      <!-- details-footer (Social Sharing) -->
      <div data-ns-animate="" data-delay="0.2" class="mx-auto mt-[70px] max-w-[950px] space-y-4">
        <h5 class="text-heading-6">Share this post</h5>
        <ul class="flex items-center gap-2.5">
          <!-- Facebook -->
          <li
            class="group/social-link border-secondary/10 dark:border-stroke-7 hover:bg-primary-500 hover:border-primary-500 inline-flex items-center justify-center rounded-full border p-2.5 transition-all duration-300 ease-in-out hover:scale-110 hover:shadow-lg">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                <path
                  d="M18.75 10.0535C18.75 5.19145 14.8325 1.25 10 1.25C5.16751 1.25 1.25 5.19145 1.25 10.0535C1.25 14.4475 4.44973 18.0896 8.63281 18.75V12.5982H6.41113V10.0535H8.63281V8.11396C8.63281 5.90759 9.93916 4.68886 11.9378 4.68886C12.8948 4.68886 13.8965 4.8608 13.8965 4.8608V7.02728H12.7932C11.7063 7.02728 11.3672 7.70594 11.3672 8.40282V10.0535H13.7939L13.406 12.5982H11.3672V18.75C15.5503 18.0896 18.75 14.4475 18.75 10.0535Z"
                  class="fill-secondary dark:fill-accent group-hover/social-link:fill-accent transition-all duration-300 ease-in-out">
                </path>
              </svg>
            </a>
          </li>
          <!-- Twitter / X -->
          <li
            class="group/social-link border-secondary/10 dark:border-stroke-7 hover:bg-primary-500 hover:border-primary-500 inline-flex items-center justify-center rounded-full border p-2.5 transition-all duration-300 ease-in-out hover:scale-110 hover:shadow-lg">
            <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on X">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                <path
                  d="M10 1.25C5.16562 1.25 1.25 5.16562 1.25 10C1.25 13.8719 3.75469 17.1422 7.23281 18.3016C7.67031 18.3781 7.83437 18.1156 7.83437 17.8859C7.83437 17.6781 7.82344 16.9891 7.82344 16.2563C5.625 16.6609 5.05625 15.7203 4.88125 15.2281C4.78281 14.9766 4.35625 14.2 3.98438 13.9922C3.67812 13.8281 3.24063 13.4234 3.97344 13.4125C4.6625 13.4016 5.15469 14.0469 5.31875 14.3094C6.10625 15.6328 7.36406 15.2609 7.86719 15.0312C7.94375 14.4625 8.17344 14.0797 8.425 13.8609C6.47813 13.6422 4.44375 12.8875 4.44375 9.54062C4.44375 8.58906 4.78281 7.80156 5.34062 7.18906C5.25313 6.97031 4.94687 6.07344 5.42812 4.87031C5.42812 4.87031 6.16094 4.64063 7.83437 5.76719C8.53438 5.57031 9.27813 5.47187 10.0219 5.47187C10.7656 5.47187 11.5094 5.57031 12.2094 5.76719C13.8828 4.62969 14.6156 4.87031 14.6156 4.87031C15.0969 6.07344 14.7906 6.97031 14.7031 7.18906C15.2609 7.80156 15.6 8.57812 15.6 9.54062C15.6 12.8984 13.5547 13.6422 11.6078 13.8609C11.925 14.1344 12.1984 14.6594 12.1984 15.4797C12.1984 16.65 12.1875 17.5906 12.1875 17.8859C12.1875 18.1156 12.3516 18.3891 12.7891 18.3016C14.5261 17.7152 16.0355 16.5988 17.1048 15.1096C18.1741 13.6204 18.7495 11.8333 18.75 10C18.75 5.16562 14.8344 1.25 10 1.25Z"
                  class="fill-secondary dark:fill-accent group-hover/social-link:fill-accent transition-all duration-300 ease-in-out">
                </path>
              </svg>
            </a>
          </li>
          <!-- LinkedIn -->
          <li
            class="group/social-link border-secondary/10 dark:border-stroke-7 hover:bg-primary-500 hover:border-primary-500 inline-flex items-center justify-center rounded-full border p-2.5 transition-all duration-300 ease-in-out hover:scale-110 hover:shadow-lg">
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on LinkedIn">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                <path
                  d="M10.0007 1C6.35854 1 3.07729 3.19375 1.6851 6.55469C0.292911 9.91562 1.06166 13.7875 3.6351 16.3609C6.20854 18.9344 10.0804 19.7031 13.4413 18.3109C16.807 16.9234 19.0007 13.6422 19.0007 10C19.0007 5.03125 14.9695 1 10.0007 1ZM7.3851 14.7391H5.42104V8.41094H7.3851V14.7391ZM6.40072 7.54844C5.93666 7.54844 5.51947 7.27187 5.34135 6.84531C5.16322 6.41875 5.25697 5.92656 5.5851 5.59844C5.90854 5.27031 6.40072 5.17188 6.82729 5.34531C7.25385 5.51875 7.5351 5.93594 7.53979 6.39531C7.53979 7.03281 7.03354 7.54375 6.40072 7.54844ZM14.7398 14.7391H12.7757V11.6594C12.7757 10.9234 12.7617 9.98594 11.7538 9.98594C10.746 9.98594 10.5679 10.7828 10.5679 11.6078V14.7391H8.61322V8.41094H10.4976V9.27344H10.5257C10.7882 8.77656 11.4257 8.25156 12.382 8.25156C14.3695 8.25156 14.7351 9.55937 14.7351 11.2609V14.7391H14.7398Z"
                  class="fill-secondary dark:fill-accent group-hover/social-link:fill-accent transition-all duration-300 ease-in-out">
                </path>
              </svg>
            </a>
          </li>
        </ul>
      </div>

      <article data-ns-animate="" data-delay="0.2" class="mx-auto mt-10 max-w-[850px] md:mt-[72px]">
        <div class="mb-[70px] space-y-4">
          <h5 class="text-heading-4">Comments</h5>
          <div class="flex items-center gap-3">
            <figure class="size-14 overflow-hidden rounded-2xl bg-linear-[156deg,_#FFF_32.92%,_#A585FF_91%]">
              <img src="images/ns-avatar-6.png" class="object-cover object-center" alt="Esther Howard's avatar"
                width="56" height="56" loading="lazy">
            </figure>
            <div>
              <h3 class="text-tagline-1 font-medium">Esther Howard</h3>

              <time datetime="2024-04-17"
                class="text-tagline-2 text-secondary/60 dark:text-accent/60 flex items-center gap-2 font-normal">
                Apr 17, 2024
              </time>
            </div>
          </div>
          <p>
            This article provides excellent insights into automating property management workflows.
            Real-time digital receipts and instant WhatsApp links eliminated all manual friction across our properties.
          </p>
          <h6 class="text-tagline-1">Reply</h6>
        </div>

        <div class="dark:bg-background-8 max-w-[850px] rounded-[20px] bg-white px-4 py-6 md:w-full md:p-6 lg:p-[42px]">
          <form action="#" method="post">
            <!-- name field  -->
            <fieldset class="mb-8 flex w-full flex-col items-start justify-start gap-2">
              <label for="fullName" class="text-tagline-1 text-secondary dark:text-accent font-medium">Full Name</label>
              <input type="text" name="fullName" id="fullName" required="" placeholder="Enter your name"
                class="placeholder:text-tagline-1 dark:placeholder:text-accent/60 dark:text-accent border-stroke-3 dark:border-stroke-6 focus-visible:outline-primary-500 w-full rounded-full border px-[18px] py-3 font-normal placeholder:font-normal focus-visible:outline"
                aria-required="true">
            </fieldset>

            <!-- email field  -->
            <fieldset class="mb-8 flex w-full flex-col items-start justify-start gap-2">
              <label for="emailAddress" class="text-tagline-1 text-secondary dark:text-accent font-medium">Email
                address</label>
              <input type="email" required="" name="emailAddress" id="emailAddress" placeholder="Enter your email"
                class="placeholder:text-tagline-1 dark:placeholder:text-accent/60 dark:text-accent border-stroke-3 dark:border-stroke-6 focus-visible:outline-primary-500 w-full rounded-full border px-[18px] py-3 font-normal placeholder:font-normal focus-visible:outline"
                aria-required="true">
            </fieldset>

            <!-- message field  -->
            <fieldset class="mb-4 flex w-full flex-col items-start justify-start gap-2">
              <label for="messages" class="text-tagline-1 text-secondary dark:text-accent font-medium">Message</label>
              <textarea name="messages" id="messages" required="" placeholder="Enter your message"
                class="placeholder:text-tagline-1 dark:placeholder:text-accent/60 dark:text-accent border-stroke-3 dark:border-stroke-6 focus-visible:outline-primary-500 min-h-[120px] w-full resize-none rounded-xl border px-[18px] py-3 font-normal placeholder:font-normal focus-visible:outline"
                aria-required="true"></textarea>
            </fieldset>

            <!-- terms and conditions checkbox -->
            <fieldset class="mb-4 flex items-center gap-2">
              <label for="agree-terms" class="flex items-center gap-x-3">
                <input id="agree-terms" type="checkbox" class="peer sr-only" required="">
                <span
                  class="border-stroke-3 dark:border-stroke-7 after:bg-primary-500 peer-checked:border-primary-500 relative size-4 cursor-pointer rounded-full border after:absolute after:top-1/2 after:left-1/2 after:size-2.5 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:opacity-0 peer-checked:after:opacity-100"></span>
              </label>
              <label for="agree-terms" class="text-tagline-3 text-secondary/60 dark:text-accent/60 cursor-pointer">
                I agree with the
                <a href="/property-management-software-terms.php" class="text-primary-500 text-tagline-3 underline">terms and conditions</a>
              </label>
            </fieldset>

            <!-- submit button -->
            <button type="submit"
              class="btn btn-secondary dark:btn-accent btn-md hover:btn-primary w-full first-letter:uppercase before:content-none"
              aria-label="Submit comment form">
              Submit
            </button>
          </form>
        </div>
      </article>
    </div>
  </section>

  <!-- CTA v1 section -->
  <section class="py-[50px] md:py-20 lg:py-28 dark:bg-background-7 bg-white" aria-label="Use Case Overview">
    <div class="main-container">
      <div class="flex items-center flex-col lg:flex-row justify-between">
        <div
          class="xl:max-w-[650px] lg:max-w-[476px] max-[400px]:max-w-[300px] w-full space-y-5 text-center lg:text-left">
          <span data-ns-animate="" data-delay="0.3" class="badge badge-green badge-yellow-v2">Get started</span>
          <div class="space-y-3">
            <h2 data-ns-animate="" data-delay="0.4"
              class="text-secondary dark:text-accent text-heading-5 sm:text-heading-4 lg:text-heading-2">
              Transform your hostel or PG operations today
            </h2>
            <p data-ns-animate="" data-delay="0.5">Start your 14-day free trial today and experience automated rent collection firsthand.</p>
          </div>
        </div>

        <div
          class="lg:basis-[466px] space-y-6 md:ml-0 xl:ml-[100px] pt-[40px] lg:pt-[67px] w-full sm:w-[80%] md:w-[60%]">
          <form data-ns-animate="" data-delay="0.6" action="/property-management-software-pricing.php" method="get"
            class="flex items-center flex-col gap-5 sm:flex-row justify-start lg:gap-3">
            <input type="email" name="email" id="userEmail-cta-v1" placeholder="Enter your email" required=""
              class="px-[18px] shadow-1 h-12 py-3 placeholder:text-secondary/50 rounded-full border border-stroke-1 lg:max-w-[340px] md:w-[71%] w-full max-[376px]:w-full dark:border-stroke-7 dark:placeholder:text-accent/60 focus:outline-none focus:border-primary-600 dark:focus:border-primary-400 dark:text-accent placeholder:font-normal font-normal">

            <button type="submit"
              class="btn btn-md btn-primary h-12 w-full sm:w-[28%] lg:w-auto btn-primary hover:btn-secondary dark:hover:btn-white">
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
