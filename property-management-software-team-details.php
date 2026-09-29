<?php
/**
 * StayFlow Marketing Website — Dynamic Team Member Details Page
 * 
 * 100% Visual UI & Layout Preservation.
 * Dynamically driven from Super Admin Team Management database.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/pg-management-system/config/database.php';
require_once __DIR__ . '/pg-management-system/services/SlugService.php';

$db = getDB();

// 1. Resolve Requested Slug or ID
$slug = trim($_GET['slug'] ?? '');
$id = (int)($_GET['id'] ?? 0);

// Old ID URL Compatibility -> 301 Redirect to Clean Slug URL
if ($id > 0 && empty($slug)) {
    $stmtId = $db->prepare("SELECT `slug` FROM `team_members` WHERE `id` = ? LIMIT 1");
    $stmtId->execute([$id]);
    $targetSlug = $stmtId->fetchColumn();
    if ($targetSlug) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: /team/" . $targetSlug);
        exit;
    }
}

// Check 301 Redirect History
if (!empty($slug)) {
    $newSlug = SlugService::resolveRedirect($db, 'team', $slug);
    if ($newSlug) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: /team/" . $newSlug);
        exit;
    }
}


// 2. Fetch Team Member Record
$member = null;
if (!empty($slug)) {
    $stmt = $db->prepare("SELECT * FROM `team_members` WHERE `slug` = ? LIMIT 1");
    $stmt->execute([$slug]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Check Super Admin Preview Access
$isPreview = isset($_GET['preview']) && (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin');

if (!$member || ($member['status'] !== 'published' && !$isPreview)) {
    // Fallback to default published member (Jay Vyas) or 404
    $stmtFb = $db->prepare("SELECT * FROM `team_members` WHERE `status` = 'published' ORDER BY `display_order` ASC, `id` ASC LIMIT 1");
    $stmtFb->execute();
    $member = $stmtFb->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        http_response_code(404);
        die("Team member not found.");
    }
}

// 3. Dynamic SEO & Social Variables
$siteUrl = defined('CANONICAL_DOMAIN') ? rtrim(CANONICAL_DOMAIN, '/') : 'https://stayflow.antideploy.app';
$page_title = !empty($member['seo_title']) ? $member['seo_title'] : htmlspecialchars($member['name']) . " - " . htmlspecialchars($member['designation']) . " | StayFlow Team";
$page_description = !empty($member['meta_description']) ? $member['meta_description'] : (!empty($member['short_bio']) ? $member['short_bio'] : "Meet " . htmlspecialchars($member['name']) . " at StayFlow.");
$page_keywords = htmlspecialchars($member['name']) . ", StayFlow team, " . htmlspecialchars($member['designation']);
$page_canonical = !empty($member['canonical_url']) ? $member['canonical_url'] : $siteUrl . "/team/" . htmlspecialchars($member['slug']);
$page_og_image = !empty($member['og_image']) ? $siteUrl . "/" . $member['og_image'] : $siteUrl . "/" . ($member['profile_image'] ?: 'images/ns-img-389.png');


// Company / Experience Sections Parsing
$companySections = [];
if (!empty($member['company_sections_json'])) {
    $companySections = json_decode($member['company_sections_json'], true) ?: [];
}

include __DIR__ . '/includes/header.php';
?>

<!-- Person JSON-LD Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": <?= json_encode($member['name']) ?>,
  "jobTitle": <?= json_encode($member['designation']) ?>,
  "worksFor": {
    "@type": "Organization",
    "name": "StayFlow",
    "url": <?= json_encode($siteUrl) ?>
  },

  "image": <?= json_encode($page_og_image) ?>,
  "description": <?= json_encode($page_description) ?>,
  "url": <?= json_encode($page_canonical) ?>
  <?php
  $socialList = array_filter([
      $member['linkedin_url'], $member['facebook_url'], $member['instagram_url'],
      $member['twitter_url'], $member['github_url'], $member['dribbble_url'],
      $member['website_url']
  ]);
  if (!empty($socialList)):
  ?>,
  "sameAs": <?= json_encode(array_values($socialList)) ?>
  <?php endif; ?>
}
</script>

<main>
  <!-- Details section -->
  <section class="pb-[50px] md:pb-[70px] lg:pb-[85px] xl:pb-[100px] xl:pt-[180px] md:pt-42 sm:pt-36 pt-32">
    <div class="main-container">
      <div class="grid grid-cols-12 items-start gap-y-[70px] md:gap-y-0 md:gap-x-8 lg:gap-x-[70px]">
        <!-- team member details -->
        <div data-ns-animate="" data-delay="0.2"
          class="col-span-12 md:col-span-5 bg-secondary rounded-[20px] backdrop-blur-[12.5px] border border-background-4 dark:border-background-8 dark:bg-background-8 space-y-2.5 relative overflow-hidden">
          <div
            class="-z-100 absolute -right-[125%] -bottom-[25%] sm:-right-[85%] sm:-bottom-[15%] md:-right-[135%] md:-bottom-[25%] xl:-right-[105%] xl:-bottom-[15%] -rotate-[75deg] size-[860px] pointer-events-none">
            <img src="images/ns-img-516.png" alt="gradient">
          </div>

          <figure class="rounded-b-[20px] flex justify-center overflow-hidden bg-background-1 relative z-1 -ml-[1px]">
            <!-- gradient bg -->
            <figure data-ns-animate="" data-delay="0.2" data-duration="0.8" data-direction="left" data-offset="100"
              class="absolute left-[-31%] top-[-35%] lg:top-[-32%] -z-1 w-[700px] md:w-[550px] lg:w-[750px] xl:w-[940px] select-none pointer-events-none">
              <img src="images/ns-img-498.png" alt="bg image" class="size-full object-cover">
            </figure>

            <img src="<?= htmlspecialchars($member['profile_image'] ?: 'images/ns-img-389.png') ?>"
              alt="<?= htmlspecialchars($member['name']) ?> - <?= htmlspecialchars($member['designation']) ?>"
              class="object-cover max-h-[480px]">
          </figure>
          <div class="p-11 space-y-[22px]">
            <?php if (!empty($member['phone']) || !empty($member['email'])): ?>
              <div class="space-y-2">
                <h4 class="text-accent">Contact information</h4>
                <?php if (!empty($member['phone'])): ?>
                  <p>
                    <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $member['phone'])) ?>" class="text-accent/60"><?= htmlspecialchars($member['phone']) ?></a>
                  </p>
                <?php endif; ?>
                <?php if (!empty($member['email'])): ?>
                  <p>
                    <a href="mailto:<?= htmlspecialchars($member['email']) ?>" class="text-accent/60"><?= htmlspecialchars($member['email']) ?></a>
                  </p>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <!--Social links-->
            <div class="flex items-center gap-2 flex-wrap">
              <!-- Facebook -->
              <?php if (!empty($member['facebook_url'])): ?>
                <a href="<?= htmlspecialchars($member['facebook_url']) ?>" target="_blank" rel="noopener noreferrer"
                  class="size-10 p-2.5 rounded-full border border-accent/10 dark:border-stroke-7 flex items-center justify-center hover:bg-background-1 dark:hover:bg-accent group transition-all duration-500 ease-in-out hover:-translate-y-2"
                  aria-label="Follow <?= htmlspecialchars($member['name']) ?> on Facebook">
                  <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none"
                      aria-hidden="true" focusable="false">
                      <path
                        d="M18.75 10.0535C18.75 5.19145 14.8325 1.25 10 1.25C5.16751 1.25 1.25 5.19145 1.25 10.0535C1.25 14.4475 4.44973 18.0896 8.63281 18.75V12.5982H6.41113V10.0535H8.63281V8.11396C8.63281 5.90759 9.93916 4.68886 11.9378 4.68886C12.8948 4.68886 13.8965 4.8608 13.8965 4.8608V7.02728H12.7932C11.7063 7.02728 11.3672 7.70594 11.3672 8.40282V10.0535H13.7939L13.406 12.5982H11.3672V18.75C15.5503 18.0896 18.75 14.4475 18.75 10.0535Z"
                        fill=""
                        class="fill-accent dark:fill-accent/60 group-hover:fill-secondary group-hover:dark:fill-black transition-all duration-500 ease-in-out">
                      </path>
                    </svg>
                  </span>
                </a>
              <?php endif; ?>

              <!-- Dribbble -->
              <?php if (!empty($member['dribbble_url'])): ?>
                <a href="<?= htmlspecialchars($member['dribbble_url']) ?>" target="_blank" rel="noopener noreferrer"
                  class="size-10 p-2.5 rounded-full border border-accent/10 dark:border-stroke-7 flex items-center justify-center hover:bg-background-1 dark:hover:bg-accent group transition-all duration-500 ease-in-out hover:-translate-y-2"
                  aria-label="Follow <?= htmlspecialchars($member['name']) ?> on Dribbble">
                  <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none"
                      aria-hidden="true" focusable="false">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M10 1.25C5.16947 1.25 1.25 5.16947 1.25 10C1.25 14.8305 5.16947 18.75 10 18.75C14.821 18.75 18.75 14.8305 18.75 10C18.75 5.16947 14.821 1.25 10 1.25ZM15.7795 5.28335C16.8235 6.55504 17.4499 8.17787 17.4688 9.93355C17.2221 9.88614 14.7546 9.38313 12.2682 9.69632C12.2112 9.57295 12.1638 9.44005 12.1068 9.30722C11.955 8.94661 11.7842 8.57648 11.6133 8.22534C14.3655 7.1055 15.6182 5.49214 15.7795 5.28335ZM10 2.54067C11.898 2.54067 13.6347 3.25244 14.9539 4.41974C14.821 4.60955 13.6917 6.11849 11.0344 7.11496C9.81018 4.86578 8.45311 3.02468 8.24431 2.73997C8.8042 2.6071 9.39264 2.54067 10 2.54067ZM6.8208 3.24295C7.02008 3.50868 8.34871 5.35927 9.59192 7.56099C6.09951 8.49106 3.01519 8.47209 2.68303 8.47209C3.16703 6.15645 4.73292 4.22993 6.8208 3.24295ZM2.52169 10.0095C2.52169 9.93355 2.52169 9.85765 2.52169 9.78174C2.84436 9.7912 6.46963 9.83867 10.1993 8.71884C10.4176 9.13637 10.6169 9.56343 10.8067 9.99048C10.7118 10.019 10.6074 10.0475 10.5125 10.0759C6.65944 11.3191 4.60955 14.7166 4.43872 15.0013C3.25244 13.6822 2.52169 11.9265 2.52169 10.0095ZM10 17.4783C8.27275 17.4783 6.67842 16.8899 5.41621 15.9029C5.54908 15.6277 7.06749 12.7047 11.2812 11.2338C11.3001 11.2242 11.3097 11.2242 11.3286 11.2148C12.382 13.9384 12.8091 16.2256 12.923 16.8804C12.0214 17.2695 11.0344 17.4783 10 17.4783ZM14.1662 16.1971C14.0903 15.7416 13.6917 13.5588 12.7142 10.8731C15.0583 10.503 17.1082 11.1104 17.3644 11.1958C17.0418 13.2741 15.846 15.0678 14.1662 16.1971Z"
                        fill=""
                        class="fill-accent dark:fill-accent/60 group-hover:fill-secondary group-hover:dark:fill-black transition-all duration-500 ease-in-out">
                      </path>
                    </svg>
                  </span>
                </a>
              <?php endif; ?>

              <!-- Github -->
              <?php if (!empty($member['github_url'])): ?>
                <a href="<?= htmlspecialchars($member['github_url']) ?>" target="_blank" rel="noopener noreferrer"
                  class="size-10 p-2.5 rounded-full border border-accent/10 dark:border-stroke-7 flex items-center justify-center hover:bg-background-1 dark:hover:bg-accent group transition-all duration-500 ease-in-out hover:-translate-y-2"
                  aria-label="Follow <?= htmlspecialchars($member['name']) ?> on GitHub">
                  <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none"
                      aria-hidden="true" focusable="false">
                      <path
                        d="M10 1.25C5.16562 1.25 1.25 5.16562 1.25 10C1.25 13.8719 3.75469 17.1422 7.23281 18.3016C7.67031 18.3781 7.83437 18.1156 7.83437 17.8859C7.83437 17.6781 7.82344 16.9891 7.82344 16.2563C5.625 16.6609 5.05625 15.7203 4.88125 15.2281C4.78281 14.9766 4.35625 14.2 3.98438 13.9922C3.67812 13.8281 3.24063 13.4234 3.97344 13.4125C4.6625 13.4016 5.15469 14.0469 5.31875 14.3094C6.10625 15.6328 7.36406 15.2609 7.86719 15.0312C7.94375 14.4625 8.17344 14.0797 8.425 13.8609C6.47813 13.6422 4.44375 12.8875 4.44375 9.54062C4.44375 8.58906 4.78281 7.80156 5.34062 7.18906C5.25313 6.97031 4.94687 6.07344 5.42812 4.87031C5.42812 4.87031 6.16094 4.64063 7.83437 5.76719C8.53438 5.57031 9.27813 5.47187 10.0219 5.47187C10.7656 5.47187 11.5094 5.57031 12.2094 5.76719C13.8828 4.62969 14.6156 4.87031 14.6156 4.87031C15.0969 6.07344 14.7906 6.97031 14.7031 7.18906C15.2609 7.80156 15.6 8.57812 15.6 9.54062C15.6 12.8984 13.5547 13.6422 11.6078 13.8609C11.925 14.1344 12.1984 14.6594 12.1984 15.4797C12.1984 16.65 12.1875 17.5906 12.1875 17.8859C12.1875 18.1156 12.3516 18.3891 12.7891 18.3016C14.5261 17.7152 16.0355 16.5988 17.1048 15.1096C18.1741 13.6204 18.7495 11.8333 18.75 10C18.75 5.16562 14.8344 1.25 10 1.25Z"
                        fill=""
                        class="fill-accent dark:fill-accent/60 group-hover:fill-secondary group-hover:dark:fill-black transition-all duration-500 ease-in-out">
                      </path>
                    </svg>
                  </span>
                </a>
              <?php endif; ?>

              <!-- Linkedin -->
              <?php if (!empty($member['linkedin_url'])): ?>
                <a href="<?= htmlspecialchars($member['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer"
                  class="size-10 p-2.5 rounded-full border border-accent/10 dark:border-stroke-7 flex items-center justify-center hover:bg-background-1 dark:hover:bg-accent group transition-all duration-500 ease-in-out hover:-translate-y-2"
                  aria-label="Follow <?= htmlspecialchars($member['name']) ?> on LinkedIn">
                  <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none"
                      aria-hidden="true" focusable="false">
                      <path
                        d="M10.0007 1C6.35854 1 3.07729 3.19375 1.6851 6.55469C0.292911 9.91562 1.06166 13.7875 3.6351 16.3609C6.20854 18.9344 10.0804 19.7031 13.4413 18.3109C16.807 16.9234 19.0007 13.6422 19.0007 10C19.0007 5.03125 14.9695 1 10.0007 1ZM7.3851 14.7391H5.42104V8.41094H7.3851V14.7391ZM6.40072 7.54844C5.93666 7.54844 5.51947 7.27187 5.34135 6.84531C5.16322 6.41875 5.25697 5.92656 5.5851 5.59844C5.90854 5.27031 6.40072 5.17188 6.82729 5.34531C7.25385 5.51875 7.5351 5.93594 7.53979 6.39531C7.53979 7.03281 7.03354 7.54375 6.40072 7.54844ZM14.7398 14.7391H12.7757V11.6594C12.7757 10.9234 12.7617 9.98594 11.7538 9.98594C10.746 9.98594 10.5679 10.7828 10.5679 11.6078V14.7391H8.61322V8.41094H10.4976V9.27344H10.5257C10.7882 8.77656 11.4257 8.25156 12.382 8.25156C14.3695 8.25156 14.7351 9.55937 14.7351 11.2609V14.7391H14.7398Z"
                        fill=""
                        class="fill-accent dark:fill-accent/60 group-hover:fill-secondary group-hover:dark:fill-black transition-all duration-500 ease-in-out">
                      </path>
                    </svg>
                  </span>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- team member description -->
        <div class="col-span-12 md:col-span-7 space-y-18">
          <!--Member details-->
          <div class="space-y-4">
            <div class="space-y-1">
              <span data-ns-animate="" data-delay="0.1"
                class="text-tagline-3 inline-block font-semibold text-secondary dark:text-accent"><?= htmlspecialchars($member['designation']) ?></span>
              <h1 data-ns-animate="" data-delay="0.2"><?= htmlspecialchars($member['name']) ?></h1>
            </div>
            <div class="space-y-5">
              <?php if (!empty($member['short_bio'])): ?>
                <p data-ns-animate="" data-delay="0.3">
                  <?= nl2br(htmlspecialchars($member['short_bio'])) ?>
                </p>
              <?php endif; ?>

              <?php if (!empty($member['full_bio'])): ?>
                <p data-ns-animate="" data-delay="0.4">
                  <?= nl2br(htmlspecialchars($member['full_bio'])) ?>
                </p>
              <?php endif; ?>
            </div>
          </div>

          <!-- Dynamic Company / Experience Sections -->
          <?php if (!empty($companySections)): ?>
            <div class="space-y-12" data-ns-animate="" data-delay="0.6">
              <?php foreach ($companySections as $sec): ?>
                <div class="space-y-4">
                  <div class="space-y-1">
                    <h3 class="text-heading-4 font-semibold text-secondary dark:text-accent"><?= htmlspecialchars($sec['title'] ?? '') ?></h3>
                    <?php if (!empty($sec['subtitle'])): ?>
                      <p class="text-secondary/60 dark:text-accent/60 font-medium"><?= htmlspecialchars($sec['subtitle']) ?></p>
                    <?php endif; ?>
                  </div>
                  <div class="space-y-2">
                    <?php if (!empty($sec['business_name']) || !empty($sec['website_link'])): ?>
                      <p class="text-tagline-1 text-secondary dark:text-accent">
                        <?php if (!empty($sec['business_name'])): ?>
                          <strong>Business:</strong> <?= htmlspecialchars($sec['business_name']) ?>
                        <?php endif; ?>
                        <?php if (!empty($sec['website_link'])): ?>
                          | <strong>Website:</strong> <a href="<?= htmlspecialchars($sec['website_link']) ?>" target="_blank" rel="noopener noreferrer" class="text-primary-500 font-medium hover:underline"><?= htmlspecialchars($sec['website_text'] ?: $sec['website_link']) ?></a>
                        <?php endif; ?>
                      </p>
                    <?php endif; ?>
                    <?php if (!empty($sec['services'])): ?>
                      <p class="text-secondary/80 dark:text-accent/80">
                        <strong>Services:</strong> <?= htmlspecialchars($sec['services']) ?>
                      </p>
                    <?php endif; ?>
                    <?php if (!empty($sec['description'])): ?>
                      <p class="text-secondary/80 dark:text-accent/80">
                        <?= htmlspecialchars($sec['description']) ?>
                      </p>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact section -->
  <section
    class="pt-14 md:pt-16 lg:pt-[88px] xl:pt-[200px] pb-14 md:pb-16 lg:pb-[88px] xl:pb-[200px] bg-background-2 dark:bg-background-7">
    <div class="main-container space-y-[70px]">
      <div class="text-center max-w-[650px] mx-auto space-y-5">
        <span data-ns-animate="" data-delay="0.2" class="badge badge-green">Contact</span>
        <div class="space-y-3">
          <h2 data-ns-animate="" data-delay="0.3">Let us know how we can assist you</h2>
        </div>
      </div>
      <!-- contact form -->
      <div data-ns-animate="" data-delay="0.4"
        class="p-6 lg:p-11 bg-background-3 dark:bg-background-6 rounded-[32px] max-w-[850px] mx-auto w-full">
        <form action="#" method="post" class="grid grid-cols-12 sm:gap-x-8 gap-y-8">
          <!-- name field -->
          <fieldset class="col-span-12 md:col-span-6 flex flex-col gap-y-2 items-start justify-start">
            <label for="fullName" class="text-tagline-1 text-secondary dark:text-accent font-medium">Full Name</label>
            <input type="text" name="fullName" id="fullName" required="" placeholder="Enter your name"
              class="rounded-full placeholder:text-tagline-1 dark:text-accent dark:placeholder:text-accent/60 dark:bg-background-6 border border-stroke-3 dark:border-stroke-7 w-full px-[18px] py-3 focus-visible:outline focus-visible:outline-primary-500 placeholder:font-normal font-normal"
              aria-required="true">
          </fieldset>

          <!-- email field -->
          <fieldset class="col-span-12 md:col-span-6 w-full flex flex-col gap-2 items-start justify-start">
            <label for="emailAddress" class="text-tagline-1 text-secondary dark:text-accent font-medium">Email
              address</label>
            <input type="email" required="" name="emailAddress" id="emailAddress" placeholder="Enter your email"
              class="rounded-full dark:text-accent placeholder:text-tagline-1 dark:placeholder:text-accent/60 dark:bg-background-6 border border-stroke-3 dark:border-stroke-7 w-full px-[18px] py-3 focus-visible:outline focus-visible:outline-primary-500 placeholder:font-normal font-normal"
              aria-required="true">
          </fieldset>

          <!-- message field -->
          <fieldset class="col-span-12 w-full flex flex-col gap-2 items-start justify-start">
            <label for="messages" class="text-tagline-1 text-secondary dark:text-accent font-medium">
              Message
            </label>
            <textarea name="messages" id="messages" required="" placeholder="Enter your message"
              class="rounded-xl dark:text-accent dark:placeholder:text-accent/60 dark:bg-background-6 border border-stroke-3 dark:border-stroke-7 w-full px-[18px] py-3 min-h-[207px] resize-none focus-visible:outline focus-visible:outline-primary-500 placeholder:font-normal font-normal"
              aria-required="true"></textarea>
          </fieldset>

          <!-- submit button -->
          <div class="w-full mt-6 col-span-12">
            <button type="submit"
              class="btn btn-primary hover:btn-secondary dark:hover:btn-accent btn-md w-full mx-auto before:content-none first-letter:uppercase"
              aria-label="Submit contact form">
              Contact now
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- CTA section -->
  <section class="bg-secondary dark:bg-background-5 py-28">
    <div class="main-container">
      <div class="flex items-center flex-col lg:flex-row justify-between">
        <div class="xl:max-w-[650px] lg:max-w-[476px] max-w-[650px] space-y-5">
          <div class="space-y-3">
            <h2 data-ns-animate="" data-delay="0.2" class="text-accent">
              Join the future of cloud software
            </h2>
            <p data-ns-animate="" data-delay="0.3" class="text-accent/60 max-w-[530px]">
              Start your free trial today and experience the power of StayFlow—where efficiency meets
              innovation.
            </p>
          </div>
        </div>

        <div class="md:w-[95%] lg:basis-[466px] space-y-6 md:ml-[42px] xl:ml-[100px] pt-[40px] lg:pt-[67px] w-full">
          <form action="/property-management-software-pricing.php" method="get" data-ns-animate="" data-delay="0.5"
            class="flex items-center flex-col gap-5 md:flex-row justify-start lg:gap-3">
            <input type="email" name="email" id="userEmail" placeholder="Enter your email" required=""
              class="px-[18px] shadow-1 h-12 py-3 placeholder:text-accent/60 rounded-full focus:outline-1 text-accent focus:outline-primary-600 dark:focus:outline-primary-400 bg-accent/5 lg:max-w-[340px] md:w-[71%] w-full dark:border-stroke-7 dark:placeholder:text-accent/60 placeholder:font-normal font-normal">

            <button type="submit"
              class="btn btn-md btn-primary hover:btn-white h-12 w-full max-[376px]:w-[97%%] md:w-auto">
              <span>Get started</span>
            </button>
          </form>

          <ul class="flex items-center justify-start gap-[42px]">
            <li data-ns-animate="" data-delay="0.3" class="flex items-center justify-center gap-2">
              <span class="size-[18px] bg-accent/10 dark:bg-accent rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="8" viewbox="0 0 10 8" fill="none"
                  class="shrink-0">
                  <path d="M9.125 1.375L3.875 6.62477L1.25 4" class="stroke-white dark:stroke-secondary"
                    stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
              </span>
              <p class="text-tagline-2 text-accent dark:text-accent/60">No credit card required</p>
            </li>

            <li data-ns-animate="" data-delay="0.4" class="flex items-center justify-center gap-2">
              <span class="size-[18px] bg-accent/10 dark:bg-accent rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="8" viewbox="0 0 10 8" fill="none"
                  class="shrink-0">
                  <path d="M9.125 1.375L3.875 6.62477L1.25 4" class="stroke-white dark:stroke-secondary"
                    stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
              </span>
              <p class="text-tagline-2 text-accent dark:text-accent/60">14-Day free trial</p>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>