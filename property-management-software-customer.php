<?php
$page_title = 'StayFlow Customers - Trusted by PG & Hostel Operators';
$page_description = 'Meet the growing community of PG owners, student housing providers, and co-living managers who rely on StayFlow every day.';
$page_keywords = 'StayFlow customers, PG owner showcase, hostel manager community';
$page_canonical = 'https://stayflow.antideploy.app/property-management-software-customer.php';
$page_og_image = 'https://images.prismic.io/staticmania/aPD-K55xUNkB2D2X_og-image.jpg';
include __DIR__ . '/includes/header.php';
?>

<main>
  <!-- Customer Hero -->

<section class="xl:pt-[180px] md:pt-42 sm:pt-36 pt-32" aria-label="customer hero section">
  <div class="main-container">
    <!-- Hero content -->
    <div class="text-center space-y-4 pb-20 lg:pb-28">
      <h1 data-ns-animate="" data-delay="0.1" class="font-normal lg:text-heading-2">
        Customer stories
      </h1>
      <p data-ns-animate="" data-delay="0.2" class="text-secondary/60 dark:text-accent/60 lg:max-w-[518px] lg:mx-auto">
        Discover case studies on how Supabase is being used around the world to quickly create
        outstanding products and set new industry standards.
      </p>
    </div>
  </div>
</section>

  <!-- Customer Success Stories -->

<section class="max-sm:overflow-x-hidden max-sm:overflow-y-auto" aria-label="customer success stories section">
  <div class="main-container space-y-14">
    <!-- dropdown stories -->

    <div class="flex items-center gap-1 max-lg:justify-center md:gap-3">
      <div class="relative z-10" data-ns-animate="" data-delay="0.1">
        <button name="industry" aria-label="Select industry" class="dropdown-button border-stroke-1 dark:border-stroke-7 dark:bg-background-7 text-secondary dark:text-accent md:text-tagline-1 text-tagline-3 flex cursor-pointer items-center rounded-md border bg-white px-2 py-1 font-medium md:gap-4 md:rounded-[10px] md:px-6 md:py-3.5">
          <span>All Industries</span>
          <span class="max-[500px]:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" class="transition-transform duration-300">
              <path d="M6 9L12 15L18 9" class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </span>
        </button>

        <div class="customer-dropdown-menu bg-background-12 dark:bg-background-6 pointer-events-none absolute top-full left-0 z-50 mt-2 w-full min-w-[200px] overflow-hidden rounded-xl p-2 opacity-0 transition-all duration-300 md:min-w-[300px]">
          <div class="dark:bg-background-8 text-secondary dark:text-accent text-tagline-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] bg-white px-6 py-4 transition-colors hover:bg-white/80" data-value="all" data-filter-type="industry">
            <span>All Industries</span>
            <span>60</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="ai" data-filter-type="industry">
            <span>Ai</span>
            <span>12</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="saas" data-filter-type="industry">
            <span>SaaS</span>
            <span>24</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="social" data-filter-type="industry">
            <span>Social</span>
            <span>5</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="spacetech" data-filter-type="industry">
            <span>Spacetech</span>
            <span>3</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="fintech" data-filter-type="industry">
            <span>Fintech</span>
            <span>2</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="media" data-filter-type="industry">
            <span>Media</span>
            <span>14</span>
          </div>
        </div>
      </div>
      <div class="relative z-10" data-ns-animate="" data-delay="0.2">
        <button name="product" aria-label="Select product" class="dropdown-button border-stroke-1 dark:border-stroke-7 dark:bg-background-7 text-secondary dark:text-accent md:text-tagline-1 text-tagline-3 flex cursor-pointer items-center gap-4 rounded-md border bg-white px-2 py-1 font-medium md:rounded-[10px] md:px-6 md:py-3.5">
          <span>All Products</span>
          <span class="max-[500px]:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" class="transition-transform duration-300">
              <path d="M6 9L12 15L18 9" class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </span>
        </button>

        <div class="customer-dropdown-menu bg-background-12 dark:bg-background-6 pointer-events-none absolute top-full left-0 z-50 mt-2 w-full min-w-[200px] overflow-hidden rounded-xl p-2 opacity-0 transition-all duration-300 md:min-w-[300px]">
          <div class="dark:bg-background-8 text-secondary dark:text-accent text-tagline-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] bg-white px-6 py-4 transition-colors hover:bg-white/80" data-value="all" data-filter-type="product">
            <span>All Products</span>
            <span>45</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="analytics" data-filter-type="product">
            <span>Analytics</span>
            <span>15</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="dashboard" data-filter-type="product">
            <span>Dashboard</span>
            <span>12</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="api" data-filter-type="product">
            <span>API</span>
            <span>10</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="integration" data-filter-type="product">
            <span>Integration</span>
            <span>8</span>
          </div>
        </div>
      </div>
      <div class="relative z-10" data-ns-animate="" data-delay="0.3">
        <button name="service" aria-label="Select service" class="dropdown-button border-stroke-1 dark:border-stroke-7 dark:bg-background-7 text-secondary dark:text-accent md:text-tagline-1 text-tagline-3 flex cursor-pointer items-center gap-4 rounded-md border bg-white px-2 py-1 font-medium md:rounded-[10px] md:px-6 md:py-3.5">
          <span>All Services</span>
          <span class="max-[500px]:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" class="transition-transform duration-300">
              <path d="M6 9L12 15L18 9" class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </span>
        </button>

        <div class="customer-dropdown-menu bg-background-12 dark:bg-background-6 pointer-events-none absolute top-full left-0 z-50 mt-2 w-full min-w-[200px] overflow-hidden rounded-xl p-2 opacity-0 transition-all duration-300 md:min-w-[300px]">
          <div class="dark:bg-background-8 text-secondary dark:text-accent text-tagline-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] bg-white px-6 py-4 transition-colors hover:bg-white/80" data-value="all" data-filter-type="service">
            <span>All Services</span>
            <span>38</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="consulting" data-filter-type="service">
            <span>Consulting</span>
            <span>12</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="support" data-filter-type="service">
            <span>Support</span>
            <span>10</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="training" data-filter-type="service">
            <span>Training</span>
            <span>8</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="maintenance" data-filter-type="service">
            <span>Maintenance</span>
            <span>8</span>
          </div>
        </div>
      </div>
      <div class="relative z-10" data-ns-animate="" data-delay="0.4">
        <button name="solution" aria-label="Select solution" class="dropdown-button border-stroke-1 dark:border-stroke-7 dark:bg-background-7 text-secondary dark:text-accent md:text-tagline-1 text-tagline-3 flex cursor-pointer items-center gap-4 rounded-md border bg-white px-2 py-1 font-medium md:rounded-[10px] md:px-6 md:py-3.5">
          <span>All Solutions</span>
          <span class="max-[500px]:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" class="transition-transform duration-300">
              <path d="M6 9L12 15L18 9" class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </span>
        </button>

        <div class="customer-dropdown-menu bg-background-12 dark:bg-background-6 pointer-events-none absolute top-full z-50 mt-2 w-full min-w-[200px] overflow-hidden rounded-xl p-2 opacity-0 transition-all duration-300 max-sm:right-0 sm:left-0 md:min-w-[300px]">
          <div class="dark:bg-background-8 text-secondary dark:text-accent text-tagline-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] bg-white px-6 py-4 transition-colors hover:bg-white/80" data-value="all" data-filter-type="solution">
            <span>All Solutions</span>
            <span>52</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="cloud" data-filter-type="solution">
            <span>Cloud</span>
            <span>18</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="enterprise" data-filter-type="solution">
            <span>Enterprise</span>
            <span>15</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="custom" data-filter-type="solution">
            <span>Custom</span>
            <span>12</span>
          </div>
          <div class="text-secondary dark:text-accent text-tagline-1 hover:bg-stroke-1 dark:hover:bg-background-5 flex cursor-pointer items-center justify-between rounded-[10px] px-6 py-4 transition-colors" data-value="mobile" data-filter-type="solution">
            <span>Mobile</span>
            <span>7</span>
          </div>
        </div>
      </div>
    </div>

    <!-- success stories -->
    <div>
      <div class="success-stories-cards grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <!-- Stripe -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="fintech" data-product="analytics" data-service="consulting" data-solution="cloud" data-ns-animate="" data-delay="0.1">
          <figure class="w-[123px]">
            <img src="images/icons/stripe.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/stripe-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              How Stripe scaled its payment operations using StayFlow Analytics to gain unified
              insights across millions of transactions.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Notion 1 -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="saas" data-product="dashboard" data-service="support" data-solution="enterprise" data-ns-animate="" data-delay="0.2">
          <figure class="w-[123px]">
            <img src="images/icons/notion-v2.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/notion-v2-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              From Hackathon to Funded Startup: How Soshi Built an AI Social Media Manager on
              StayFlow.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Dropbox 1 -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="saas" data-product="integration" data-service="training" data-solution="enterprise" data-ns-animate="" data-delay="0.3">
          <figure class="w-[123px]">
            <img src="images/icons/dropbox-v2.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/dropbox-v2-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              How Dropbox leveraged StayFlow Integrations to centralize analytics and improve
              decision-making across global teams.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Squarespace -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="media" data-product="analytics" data-service="consulting" data-solution="cloud" data-ns-animate="" data-delay="0.4">
          <figure class="w-[123px]">
            <img src="images/icons/squarespace.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/squarespace-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              How Squarespace's growth team used StayFlow Funnels to identify user friction points and
              optimize conversion paths.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>

        <!-- ScaPic -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="ai" data-product="api" data-service="consulting" data-solution="custom" data-ns-animate="" data-delay="0.1">
          <figure class="w-[123px]">
            <img src="images/icons/scapic.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/scapic-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              From Hackathon to Funded Startup: How Soshi Built an AI Social Media Manager on
              StayFlow.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Discord -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="social" data-product="analytics" data-service="training" data-solution="enterprise" data-ns-animate="" data-delay="0.2">
          <figure class="w-[123px]">
            <img src="images/icons/discord.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/discord-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              How Discord's data team used StayFlow AI Predictions to forecast engagement trends and
              boost retention.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Hotjar -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="saas" data-product="dashboard" data-service="consulting" data-solution="enterprise" data-ns-animate="" data-delay="0.3">
          <figure class="w-[123px]">
            <img src="images/icons/hotjar.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/hotjar-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              Hotjar used StayFlow Dashboards to consolidate metrics and deliver faster insights to
              product teams.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Lattice -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="saas" data-product="dashboard" data-service="support" data-solution="enterprise" data-ns-animate="" data-delay="0.4">
          <figure class="w-[123px]">
            <img src="images/icons/lattice.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/lattice-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              How Lattice leveraged StayFlow Reports to track engagement metrics and improve company
              culture.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Notion 2 -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="saas" data-product="api" data-service="support" data-solution="cloud" data-ns-animate="" data-delay="0.1">
          <figure class="w-[123px]">
            <img src="images/icons/spotify.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/spotify-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              Discover how Notion automated user onboarding and reduced churn by 22% using StayFlow
              Workflow Builder.
            </p>
            <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
              <span>Read more</span>
            </a>
          </div>
        </div>

        <!-- Spotify -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="media" data-product="api" data-service="consulting" data-solution="enterprise" data-ns-animate="" data-delay="0.2">
          <figure class="w-[123px]">
            <img src="images/icons/asana-v2.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/spotify-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              Spotify integrated StayFlow Intelligence to personalize recommendations and deepen
              audience engagement.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Asana -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="saas" data-product="dashboard" data-service="training" data-solution="enterprise" data-ns-animate="" data-delay="0.3">
          <figure class="w-[123px]">
            <img src="images/icons/stripe.svg" alt="StayFlow icon" class="h-full w-full object-cover">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              How Asana used StayFlow cross-project dashboards to improve visibility and speed up
              project delivery.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Dropbox 2 -->
        <div class="story-card dark:bg-background-5 space-y-6 rounded-[20px] bg-white p-8" data-industry="saas" data-product="integration" data-service="support" data-solution="cloud" data-ns-animate="" data-delay="0.4">
          <figure class="w-[123px]">
            <img src="images/icons/notion-v2.svg" alt="StayFlow icon" class="inline-block h-full w-full object-cover dark:hidden">
            <img src="images/icons/notion-v2-dark.svg" alt="StayFlow icon" class="hidden h-full w-full object-cover dark:block">
          </figure>
          <div class="space-y-13">
            <p class="text-tagline-1">
              How Dropbox utilized StayFlow Integrations to unify analytics and enhance
              decision-making for teams worldwide.
            </p>
            <div>
              <a href="/property-management-software-customer-details.php" class="btn btn-md btn-white hover:btn-primary dark:btn-transparent mt-13">
                <span>Read more</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

  <!-- Customer CTA Section -->
    
<section class="py-16 lg:py-22 xl:py-28">
  <div class="main-container">
    <div class="flex flex-col items-center justify-center gap-y-5 text-center md:flex-row md:gap-x-14.5">
      <p class="text-secondary dark:text-accent text-tagline-1" data-ns-animate="" data-delay="0.1">
        <span class="pr-5.5">We protect your data.</span>
        <a href="/property-management-software-security.php" class="text-primary-500 {=$text-class} underline">More on Security</a>
      </p>
      <ul class=" flex flex-col items-center justify-center gap-y-5 sm:flex-row sm:justify-start sm:gap-x-6 sm:gap-y-0">
        <li data-ns-animate="" data-delay="0.2" class="flex items-center gap-2">
          <span class="bg-secondary dark:bg-accent flex size-5 shrink-0 items-center justify-center rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewbox="0 0 10 7" fill="none">
              <path d="M4.31661 6.75605L9.74905 1.42144C10.0836 1.0959 10.0836 0.569702 9.74905 0.244158C9.41446 -0.081386 8.87363 -0.081386 8.53904 0.244158L3.7116 4.99012L1.46096 2.78807C1.12636 2.46253 0.585538 2.46253 0.250945 2.78807C-0.0836483 3.11362 -0.0836483 3.63982 0.250945 3.96536L3.1066 6.75605C3.27347 6.91841 3.49253 7 3.7116 7C3.93067 7 4.14974 6.91841 4.31661 6.75605Z" class="dark:fill-secondary fill-white"></path>
            </svg>
          </span>
          <span class="text-secondary dark:text-accent text-tagline-3 sm:text-tagline-2 font-normal">SOC2 Type 2 Certified</span>
        </li>
        <li data-ns-animate="" data-delay="0.3" class="flex items-center gap-2">
          <span class="bg-secondary dark:bg-accent flex size-5 shrink-0 items-center justify-center rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewbox="0 0 10 7" fill="none">
              <path d="M4.31661 6.75605L9.74905 1.42144C10.0836 1.0959 10.0836 0.569702 9.74905 0.244158C9.41446 -0.081386 8.87363 -0.081386 8.53904 0.244158L3.7116 4.99012L1.46096 2.78807C1.12636 2.46253 0.585538 2.46253 0.250945 2.78807C-0.0836483 3.11362 -0.0836483 3.63982 0.250945 3.96536L3.1066 6.75605C3.27347 6.91841 3.49253 7 3.7116 7C3.93067 7 4.14974 6.91841 4.31661 6.75605Z" class="dark:fill-secondary fill-white"></path>
            </svg>
          </span>
          <span class="dark:text-accent text-secondary text-tagline-3 sm:text-tagline-2 font-normal">HIPAA Compliant</span>
        </li>
      </ul>
    </div>
  </div>
</section>

    </main>
  <!-- Footer v3 -->

<?php include __DIR__ . '/includes/footer.php'; ?>
