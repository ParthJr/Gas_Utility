<?php
/**
 * StayFlow Marketing Website — Dynamic Team Listing Page
 * 
 * 100% Visual UI & Layout Preservation.
 * Dynamically driven from Super Admin Team Management database.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/pg-management-system/config/database.php';

try {
    $db = getDB();
    $stmtTeam = $db->query("SELECT * FROM `team_members` WHERE `status` = 'published' ORDER BY `display_order` ASC, `id` ASC");
    $teamList = $stmtTeam->fetchAll(PDO::FETCH_ASSOC);
} catch (\Throwable $e) {
    $teamList = [];
}

$page_title = 'StayFlow Team - Building the Future of PG Management';
$page_description = 'Meet the team behind StayFlow. Our engineers, product designers, and property management experts are dedicated to simplifying PG and hostel operations.';
$page_keywords = 'StayFlow team, property management software founders, StayFlow company';
$siteUrl = defined('CANONICAL_DOMAIN') ? rtrim(CANONICAL_DOMAIN, '/') : 'https://stayflow.antideploy.app';
$page_canonical = $siteUrl . '/property-management-software-team.php';

$page_og_image = 'https://images.prismic.io/staticmania/aPD-K55xUNkB2D2X_og-image.jpg';
include __DIR__ . '/includes/header.php';
?>

<main>
  <!-- Team Members section -->
  <section class="pt-14 pb-14 md:pt-16 md:pb-16 lg:pt-[88px] lg:pb-[88px] xl:pt-[180px] xl:pb-[100px]">
    <div class="main-container space-y-[70px]">
      <div class="space-y-5 text-center">
        <span data-ns-animate="" data-delay="0.1" class="badge badge-cyan">Our team</span>
        <div class="mx-auto max-w-[620px] space-y-3">
          <h1 data-ns-animate="" data-delay="0.2">Our innovative, dynamic and talented team</h1>
          <p data-ns-animate="" data-delay="0.3">
            Our innovative, dynamic, and talented team is the driving force behind our success. Each
            member brings a unique blend of expertise.
          </p>
        </div>
      </div>

      <div class="grid grid-cols-12 max-sm:gap-y-8 sm:gap-5 md:gap-8">
        <?php if (empty($teamList)): ?>
          <div class="col-span-12 text-center py-12 text-secondary/60 dark:text-accent/60">
            <p>Our team profiles are currently being updated. Please check back soon!</p>
          </div>
        <?php else: ?>
          <?php 
          $delay = 0.3;
          foreach ($teamList as $m): 
            $delay += 0.1;
            $memberUrl = '/team/' . htmlspecialchars($m['slug']);

          ?>
            <div data-ns-animate="" data-delay="<?= number_format($delay, 1) ?>" class="col-span-12 sm:col-span-6 lg:col-span-4">
              <div
                class="group dark:bg-background-9 relative z-10 overflow-hidden rounded-[20px] bg-white p-3">
                <figure class="mx-auto overflow-hidden lg:max-w-[408px] h-[340px]">
                  <a href="<?= $memberUrl ?>">
                    <img src="<?= htmlspecialchars($m['profile_image'] ?: 'images/ns-img-389.png') ?>" alt="<?= htmlspecialchars($m['name']) ?>"
                      class="bg-background-3 dark:bg-background-5 h-full w-full rounded-2xl object-cover hover:scale-105 transition-all duration-500">
                  </a>
                </figure>
                <div
                  class="shadow-1 dark:bg-background-8 ease-team-ease-1 absolute bottom-7 left-1/2 z-20 mx-auto w-[calc(100%-44px)] max-w-[384px] -translate-x-1/2 cursor-pointer space-y-3 rounded-xl bg-white p-6 transition-all duration-[400ms] sm:bottom-5 lg:translate-y-[30%] lg:scale-[90%] lg:opacity-0 lg:group-hover:translate-y-0 lg:group-hover:scale-100 lg:group-hover:opacity-100">
                  <div class="text-center">
                    <h3 class="text-heading-5 text-secondary dark:text-accent font-normal">
                      <a href="<?= $memberUrl ?>" class="hover:underline"> <?= htmlspecialchars($m['name']) ?> </a>
                    </h3>
                    <p class="text-tagline-2 text-secondary/40 dark:text-accent/40 font-normal">
                      <?= htmlspecialchars($m['designation']) ?>
                    </p>
                  </div>
                  <!-- Social Links -->
                  <div
                    class="flex items-center justify-center gap-3 lg:opacity-0 lg:group-hover:opacity-100 lg:scale-75 lg:group-hover:scale-100 transition-all duration-[400ms] ease-team-ease-1">
                    <?php if (!empty($m['facebook_url'])): ?>
                      <a href="<?= htmlspecialchars($m['facebook_url']) ?>" target="_blank" rel="noopener noreferrer" class="group/social-link" title="Facebook">
                        <span class="sr-only">Facebook profile</span>
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="7" height="16" viewbox="0 0 7 16" fill="none">
                            <path
                              d="M2.25 15C2.25 15.4142 2.58579 15.75 3 15.75C3.41421 15.75 3.75 15.4142 3.75 15H2.25ZM3.75 7C3.75 6.58579 3.41421 6.25 3 6.25C2.58579 6.25 2.25 6.58579 2.25 7H3.75ZM6 1.75C6.41421 1.75 6.75 1.41421 6.75 1C6.75 0.585786 6.41421 0.25 6 0.25V1.75ZM3 4H2.25H3ZM2.25 7C2.25 7.41421 2.58579 7.75 3 7.75C3.41421 7.75 3.75 7.41421 3.75 7H2.25ZM3 6.25C2.58579 6.25 2.25 6.58579 2.25 7C2.25 7.41421 2.58579 7.75 3 7.75V6.25ZM5 7.75C5.41421 7.75 5.75 7.41421 5.75 7C5.75 6.58579 5.41421 6.25 5 6.25V7.75ZM3 7.75C3.41421 7.75 3.75 7.41421 3.75 7C3.75 6.58579 3.41421 6.25 3 6.25V7.75ZM1 6.25C0.585786 6.25 0.25 6.58579 0.25 7C0.25 7.41421 0.585786 7.75 1 7.75V6.25ZM3 15H3.75V7H3H2.25V15H3ZM6 1V0.25C3.92893 0.25 2.25 1.92893 2.25 4H3H3.75C3.75 2.75736 4.75736 1.75 6 1.75V1ZM3 4H2.25V7H3H3.75V4H3ZM3 7V7.75H5V7V6.25H3V7ZM3 7V6.25H1V7V7.75H3V7Z"
                              class="fill-secondary/40 dark:fill-accent/40 group-hover/social-link:fill-secondary dark:group-hover/social-link:fill-accent transition-colors duration-300 ease-team-ease-1">
                            </path>
                          </svg>
                        </span>
                      </a>
                      <div class="h-[22px] w-px bg-stroke-1 dark:bg-accent/10"></div>
                    <?php endif; ?>

                    <?php if (!empty($m['linkedin_url'])): ?>
                      <a href="<?= htmlspecialchars($m['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer" class="group/social-link" title="LinkedIn">
                        <span class="sr-only">Linkedin profile</span>
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="11" viewbox="0 0 13 11" fill="none">
                            <path
                              d="M2.25 4C2.25 3.58579 1.91421 3.25 1.5 3.25C1.08579 3.25 0.75 3.58579 0.75 4H2.25ZM0.75 10C0.75 10.4142 1.08579 10.75 1.5 10.75C1.91421 10.75 2.25 10.4142 2.25 10H0.75ZM10.75 10C10.75 10.4142 11.0858 10.75 11.5 10.75C11.9142 10.75 12.25 10.4142 12.25 10H10.75ZM5.5 7H4.75H5.5ZM4.75 10C4.75 10.4142 5.08579 10.75 5.5 10.75C5.91421 10.75 6.25 10.4142 6.25 10H4.75ZM2.25 1C2.25 0.585786 1.91421 0.25 1.5 0.25C1.08579 0.25 0.75 0.585786 0.75 1H2.25ZM0.75 2C0.75 2.41421 1.08579 2.75 1.5 2.75C1.91421 2.75 2.25 2.41421 2.25 2H0.75ZM1.5 4H0.75V10H1.5H2.25V4H1.5ZM11.5 10H12.25V7H11.5H10.75V10H11.5ZM11.5 7H12.25C12.25 4.92893 10.5711 3.25 8.5 3.25V4V4.75C9.74264 4.75 10.75 5.75736 10.75 7H11.5ZM8.5 4V3.25C6.42893 3.25 4.75 4.92893 4.75 7H5.5H6.25C6.25 5.75736 7.25736 4.75 8.5 4.75V4ZM5.5 7H4.75V10H5.5H6.25V7H5.5ZM1.5 1H0.75V2H1.5H2.25V1H1.5Z"
                              class="fill-secondary/40 dark:fill-accent/40 group-hover/social-link:fill-secondary dark:group-hover/social-link:fill-accent transition-colors duration-300 ease-team-ease-1">
                            </path>
                          </svg>
                        </span>
                      </a>
                      <div class="h-[22px] w-px bg-stroke-1 dark:bg-accent/10"></div>
                    <?php endif; ?>

                    <?php if (!empty($m['dribbble_url'])): ?>
                      <a href="<?= htmlspecialchars($m['dribbble_url']) ?>" target="_blank" rel="noopener noreferrer" class="group/social-link" title="Dribbble">
                        <span class="sr-only">Dribbble profile</span>
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewbox="0 0 16 16" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M9.81146 14.7617C6.69789 15.5957 3.41731 14.1957 1.86521 11.3707C0.313116 8.54567 0.890795 5.02595 3.26447 2.84524C5.63814 0.66452 9.19411 0.386619 11.8777 2.1721C14.5614 3.95759 15.6788 7.34483 14.5845 10.3767C13.8079 12.532 12.0248 14.1702 9.81146 14.7617Z"
                              class="stroke-secondary/40 dark:stroke-accent/40 group-hover/social-link:stroke-secondary dark:group-hover/social-link:stroke-accent transition-colors duration-300 ease-team-ease-1"
                              stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                              d="M9.06142 14.7162C9.03653 15.1297 9.35153 15.485 9.765 15.5099C10.1785 15.5348 10.5338 15.2198 10.5587 14.8063L9.06142 14.7162ZM6.84286 0.874373C6.64188 0.512186 6.18534 0.381502 5.82315 0.582483C5.46097 0.783464 5.33028 1.24 5.53126 1.60219L6.84286 0.874373ZM13.2187 2.9035C13.3591 2.5138 13.157 2.08408 12.7673 1.94368C12.3776 1.80328 11.9479 2.00537 11.8075 2.39506L13.2187 2.9035ZM7.74006 7.03428L7.54644 6.30971L7.54546 6.30997L7.74006 7.03428ZM1.89802 5.05032C1.58158 4.78304 1.10838 4.82289 0.841101 5.13932C0.573819 5.45576 0.613667 5.92896 0.930105 6.19624L1.89802 5.05032ZM2.77955 13.0958C2.63901 13.4855 2.84095 13.9153 3.23059 14.0558C3.62023 14.1963 4.05003 13.9944 4.19057 13.6048L2.77955 13.0958ZM8.25822 8.96384L8.06412 8.23939L8.25822 8.96384ZM14.1013 10.9494C14.4178 11.2166 14.891 11.1766 15.1582 10.8601C15.4254 10.5435 15.3854 10.0703 15.0688 9.80317L14.1013 10.9494ZM9.81006 14.7613L10.5587 14.8063C10.7186 12.1509 10.1178 9.27114 9.32769 6.78072C8.53534 4.28333 7.53363 2.11922 6.84286 0.874373L6.18706 1.23828L5.53126 1.60219C6.17449 2.76135 7.13628 4.83373 7.89793 7.23434C8.66179 9.64192 9.20557 12.3216 9.06142 14.7162L9.81006 14.7613ZM12.5131 2.64928L11.8075 2.39506C11.1142 4.31922 9.52233 5.7817 7.54644 6.30971L7.74006 7.03428L7.93369 7.75886C10.3844 7.10397 12.3588 5.29004 13.2187 2.9035L12.5131 2.64928ZM7.74006 7.03428L7.54546 6.30997C5.57029 6.84064 3.46046 6.37005 1.89802 5.05032L1.41406 5.62328L0.930105 6.19624C2.86801 7.83311 5.48485 8.41679 7.93467 7.75859L7.74006 7.03428ZM3.48506 13.3503L4.19057 13.6048C4.88464 11.6805 6.47642 10.2177 8.45232 9.68829L8.25822 8.96384L8.06412 8.23939C5.614 8.89585 3.64019 10.7097 2.77955 13.0958L3.48506 13.3503ZM8.25822 8.96384L8.45232 9.68829C10.4282 9.15889 12.5381 9.62992 14.1013 10.9494L14.5851 10.3763L15.0688 9.80317C13.1305 8.16701 10.5142 7.58293 8.06412 8.23939L8.25822 8.96384Z"
                              class="fill-secondary/40 dark:fill-accent/40 group-hover/social-link:fill-secondary dark:group-hover/social-link:fill-accent transition-colors duration-300 ease-team-ease-1">
                            </path>
                          </svg>
                        </span>
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>