<?php
$page_title = 'StayFlow Tutorials - Step-by-Step PG Management Training';
$page_description = 'Watch and read step-by-step StayFlow tutorials: how to add new beds, run tenant KYC checks, generate rent receipts, and manage utility meters.';
$page_keywords = 'StayFlow tutorials, PG management training, hostel software guide, tenant KYC tutorial';
$page_canonical = 'https://stayflow.antideploy.app/property-management-software-tutorial.php';
$page_og_image = 'https://images.prismic.io/staticmania/aPD-K55xUNkB2D2X_og-image.jpg';
include __DIR__ . '/includes/header.php';
?>

<main>
  <!-- Learn Banner section -->
  <section class="pt-[170px] pb-[100px] md:pb-[200px] xl:pt-[200px]">
    <div class="main-container">
      <div class="flex flex-col items-center gap-y-14 lg:flex-row lg:gap-[100px]">
        <div class="mx-auto max-w-[540px] flex-1 space-y-14 text-center lg:mx-0 lg:max-w-full lg:text-left">
          <div class="space-y-3">
            <h1 data-ns-animate="" data-delay="0.1">
              Built for PG owners & property managers. Adapted to your workflow.
            </h1>
            <p data-ns-animate="" data-delay="0.2">
              StayFlow is designed to simplify every aspect of PG, hostel, and co-living operations. Learn how to
              automate rent collection, verify tenant KYC, manage bed allocations, and track multi-property analytics
              through our video tutorials.
            </p>
          </div>

          <div data-ns-animate="" data-delay="0.3" data-instant="">
            <a href="/property-management-software-services.php"
              class="section-button btn btn-primary btn-xl hover:btn-secondary dark:hover:btn-accent">
              <span>Explore the platform</span>
            </a>
          </div>
        </div>
        <div class="flex-1">
          <figure data-ns-animate="" data-delay="0.3"
            class="w-full max-w-[596px] overflow-hidden rounded-[20px] opacity-0">
            <img src="images/stayflow-blog-rent-automation.png" class="h-full w-full object-cover"
              alt="StayFlow guide on automating PG rent collection via WhatsApp">
          </figure>
        </div>
      </div>
    </div>
  </section>

  <!-- Blog section -->
  <section data-ns-animate="" data-delay="0.1" class="bg-background-2 dark:bg-background-6 py-14 md:py-[100px]">
    <div class="main-container">
      <!-- show after lg devices  -->
      <div role="tablist" data-tab-bar=""
        class="border-stroke-2 dark:border-stroke-6 relative hidden items-center justify-center border-b lg:flex">
        <!-- active-tab-bar  -->
        <div data-active-tab-bar=""
          class="bg-primary-500 absolute bottom-[-0.8px] z-[1] h-[2px] transition-all duration-500 ease-in-out"></div>

        <!-- all  -->
        <button data-tab-button=""
          class="data-[state=selected]:text-secondary text-secondary/60 dark:data-[state=selected]:text-accent dark:text-accent/60 -mb-px cursor-pointer px-10 py-3 focus-visible:outline-0">
          <span class="text-tagline-1 font-medium">All</span>
        </button>

        <!-- finance  -->
        <button data-tab-button=""
          class="data-[state=selected]:text-secondary text-secondary/60 dark:data-[state=selected]:text-accent dark:text-accent/60 -mb-px cursor-pointer px-10 py-3 focus-visible:outline-0">
          <span class="text-tagline-1 font-medium">Finance</span>
        </button>

        <!-- crypto  -->
        <button data-tab-button=""
          class="data-[state=selected]:text-secondary text-secondary/60 dark:data-[state=selected]:text-accent dark:text-accent/60 -mb-px cursor-pointer px-10 py-3 focus-visible:outline-0">
          <span class="text-tagline-1 font-medium">Crypto</span>
        </button>

        <!-- ai software  -->
        <button data-tab-button=""
          class="data-[state=selected]:text-secondary text-secondary/60 dark:data-[state=selected]:text-accent dark:text-accent/60 -mb-px cursor-pointer px-10 py-3 focus-visible:outline-0">
          <span class="text-tagline-1 font-medium">AI Software</span>
        </button>

        <!-- investment  -->
        <button data-tab-button=""
          class="data-[state=selected]:text-secondary text-secondary/60 dark:data-[state=selected]:text-accent dark:text-accent/60 -mb-px cursor-pointer px-10 py-3 focus-visible:outline-0">
          <span class="text-tagline-1 font-medium">Investment</span>
        </button>

        <!-- design  -->
        <button data-tab-button=""
          class="data-[state=selected]:text-secondary text-secondary/60 dark:data-[state=selected]:text-accent dark:text-accent/60 -mb-px cursor-pointer px-10 py-3 focus-visible:outline-0">
          <span class="text-tagline-1 font-medium">Design</span>
        </button>
      </div>

      <!-- mobile  -->
      <div class="grid grid-cols-12 items-center justify-center gap-4 lg:hidden">
        <button data-mobile-tab-button=""
          class="text-tagline-2 text-secondary/60 dark:text-accent/60 border-stroke-2 dark:border-stroke-7 data-[mobile-active='true']:bg-primary-500 data-[mobile-active='true']:border-primary-600 dark:bg-background-7 col-span-4 cursor-pointer rounded-md border bg-white px-3.5 py-2 font-medium transition-all data-[mobile-active='true']:scale-105 data-[mobile-active='true']:text-white md:col-span-2">
          All
        </button>
        <button data-mobile-tab-button=""
          class="text-tagline-2 text-secondary/60 dark:text-accent/60 border-stroke-2 dark:border-stroke-7 data-[mobile-active='true']:bg-primary-500 data-[mobile-active='true']:border-primary-600 dark:bg-background-7 col-span-4 cursor-pointer rounded-md border bg-white px-3.5 py-2 font-medium transition-all data-[mobile-active='true']:scale-105 data-[mobile-active='true']:text-white md:col-span-2">
          Finance
        </button>
        <button data-mobile-tab-button=""
          class="text-tagline-2 text-secondary/60 dark:text-accent/60 border-stroke-2 dark:border-stroke-7 data-[mobile-active='true']:bg-primary-500 data-[mobile-active='true']:border-primary-600 dark:bg-background-7 col-span-4 cursor-pointer rounded-md border bg-white px-3.5 py-2 font-medium transition-all data-[mobile-active='true']:scale-105 data-[mobile-active='true']:text-white md:col-span-2">
          Crypto
        </button>
        <button data-mobile-tab-button=""
          class="text-tagline-2 text-secondary/60 dark:text-accent/60 border-stroke-2 dark:border-stroke-7 data-[mobile-active='true']:bg-primary-500 data-[mobile-active='true']:border-primary-600 dark:bg-background-7 col-span-4 cursor-pointer rounded-md border bg-white px-3.5 py-2 font-medium text-nowrap transition-all data-[mobile-active='true']:scale-105 data-[mobile-active='true']:text-white md:col-span-2">
          AI Software
        </button>
        <button data-mobile-tab-button=""
          class="text-tagline-2 text-secondary/60 dark:text-accent/60 border-stroke-2 dark:border-stroke-7 data-[mobile-active='true']:bg-primary-500 data-[mobile-active='true']:border-primary-600 dark:bg-background-7 col-span-4 cursor-pointer rounded-md border bg-white px-3.5 py-2 font-medium transition-all data-[mobile-active='true']:scale-105 data-[mobile-active='true']:text-white md:col-span-2">
          Investment
        </button>
        <button data-mobile-tab-button=""
          class="text-tagline-2 text-secondary/60 dark:text-accent/60 border-stroke-2 dark:border-stroke-7 data-[mobile-active='true']:bg-primary-500 data-[mobile-active='true']:border-primary-600 dark:bg-background-7 col-span-4 cursor-pointer rounded-md border bg-white px-3.5 py-2 font-medium transition-all data-[mobile-active='true']:scale-105 data-[mobile-active='true']:text-white md:col-span-2">
          Design
        </button>
      </div>

      <div class="mt-20 grid grid-cols-12 gap-y-6 sm:gap-5 md:gap-8">
        <!-- blog one  -->
        <div data-filter-item="" data-filter-category="finance" class="col-span-12 sm:col-span-6 lg:col-span-4">
          <article
            class="dark:bg-background-5 border-stroke-4 dark:border-stroke-5 mx-auto max-w-[500px] overflow-hidden rounded-[20px] border bg-white md:mx-0 md:max-w-full">
            <figure>
              <img src="images/ns-img-428.png" alt="Abstract blurred for Crypto product tour" loading="lazy"
                class="h-full w-full object-cover">
            </figure>
            <div class="space-y-6 p-6">
              <div class="flex items-center gap-3.5 xl:gap-6">
                <span class="badge badge-green">Finance</span>
                <span class="flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                    <path
                      d="M10.5 17.5C14.6421 17.5 18 14.1421 18 10C18 5.85786 14.6421 2.5 10.5 2.5C6.35786 2.5 3 5.85786 3 10C3 14.1421 6.35786 17.5 10.5 17.5Z"
                      class="stroke-secondary dark:stroke-accent" stroke-miterlimit="10"></path>
                    <path d="M10.5 5.625V10H14.875" class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                      stroke-linejoin="round"></path>
                  </svg>
                  <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                    25 Hours
                  </span>
                </span>
                <span class="flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                    <path
                      d="M4.25 16.875C4.25 16.3777 4.44754 15.9008 4.79917 15.5492C5.15081 15.1975 5.62772 15 6.125 15H16.75V2.5H6.125C5.62772 2.5 5.15081 2.69754 4.79917 3.04917C4.44754 3.40081 4.25 3.87772 4.25 4.375V16.875Z"
                      class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M4.25 16.875V17.5H15.5" class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                      stroke-linejoin="round"></path>
                  </svg>
                  <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                    25 Hours
                  </span>
                </span>
              </div>
              <div class="space-y-2">
                <p class="xl:text-heading-5 text-heading-6 text-secondary dark:text-accent">
                  Getting started guide
                </p>
                <p class="line-clamp-2">
                  A step-by-step walkthrough of how to set up your workspace and get your first
                  results.
                </p>
              </div>
              <div>
                <a href="/property-management-software-blog-details.php"
                  class="btn hover:btn-secondary dark:hover:btn-accent dark:btn-transparent btn-white btn-md">
                  <span>Read more</span>
                </a>
              </div>
            </div>
          </article>
        </div>

        <!-- blog two  -->
        <div data-filter-item="" data-filter-category="crypto" class="col-span-12 sm:col-span-6 lg:col-span-4">
          <div>
            <article
              class="dark:bg-background-5 border-stroke-4 dark:border-stroke-5 mx-auto max-w-[500px] overflow-hidden rounded-[20px] border bg-white md:mx-0 md:max-w-full">
              <figure>
                <img src="images/ns-img-459.png" alt="Abstract blurred for Crypto product tour" loading="lazy"
                  class="h-full w-full object-cover">
              </figure>
              <div class="space-y-6 p-6">
                <div class="flex items-center gap-3.5 xl:gap-6">
                  <span class="badge badge-green">Crypto</span>
                  <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                      <path
                        d="M10.5 17.5C14.6421 17.5 18 14.1421 18 10C18 5.85786 14.6421 2.5 10.5 2.5C6.35786 2.5 3 5.85786 3 10C3 14.1421 6.35786 17.5 10.5 17.5Z"
                        class="stroke-secondary dark:stroke-accent" stroke-miterlimit="10"></path>
                      <path d="M10.5 5.625V10H14.875" class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    </svg>
                    <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                      25 Hours
                    </span>
                  </span>
                  <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                      <path
                        d="M4.25 16.875C4.25 16.3777 4.44754 15.9008 4.79917 15.5492C5.15081 15.1975 5.62772 15 6.125 15H16.75V2.5H6.125C5.62772 2.5 5.15081 2.69754 4.79917 3.04917C4.44754 3.40081 4.25 3.87772 4.25 4.375V16.875Z"
                        class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                      </path>
                      <path d="M4.25 16.875V17.5H15.5" class="stroke-secondary dark:stroke-accent"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                      25 Hours
                    </span>
                  </span>
                </div>
                <div class="space-y-2">
                  <p class="xl:text-heading-5 text-heading-6 text-secondary dark:text-accent">
                    Product tour
                  </p>
                  <p class="line-clamp-2">
                    A visual overview of everything you can do inside StayFlow.
                  </p>
                </div>
                <div>
                  <a href="/property-management-software-blog-details.php"
                    class="btn hover:btn-secondary dark:hover:btn-accent dark:btn-transparent btn-white btn-md">
                    <span>Read more</span>
                  </a>
                </div>
              </div>
            </article>
          </div>
        </div>

        <!-- blog three  -->
        <div data-filter-item="" data-filter-category="finance" class="col-span-12 sm:col-span-6 lg:col-span-4">
          <div>
            <article
              class="dark:bg-background-5 border-stroke-4 dark:border-stroke-5 mx-auto max-w-[500px] overflow-hidden rounded-[20px] border bg-white md:mx-0 md:max-w-full">
              <figure>
                <img src="images/ns-img-460.png" alt="Modern office with colorful dashboard lines for Finance dashboard"
                  loading="lazy" class="h-full w-full object-cover">
              </figure>
              <div class="space-y-6 p-6">
                <div class="flex items-center gap-3.5 xl:gap-6">
                  <span class="badge badge-green">Finance</span>
                  <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                      <path
                        d="M10.5 17.5C14.6421 17.5 18 14.1421 18 10C18 5.85786 14.6421 2.5 10.5 2.5C6.35786 2.5 3 5.85786 3 10C3 14.1421 6.35786 17.5 10.5 17.5Z"
                        class="stroke-secondary dark:stroke-accent" stroke-miterlimit="10"></path>
                      <path d="M10.5 5.625V10H14.875" class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    </svg>
                    <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                      25 Hours
                    </span>
                  </span>
                  <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                      <path
                        d="M4.25 16.875C4.25 16.3777 4.44754 15.9008 4.79917 15.5492C5.15081 15.1975 5.62772 15 6.125 15H16.75V2.5H6.125C5.62772 2.5 5.15081 2.69754 4.79917 3.04917C4.44754 3.40081 4.25 3.87772 4.25 4.375V16.875Z"
                        class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                      </path>
                      <path d="M4.25 16.875V17.5H15.5" class="stroke-secondary dark:stroke-accent"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                      25 Hours
                    </span>
                  </span>
                </div>
                <div class="space-y-2">
                  <p class="xl:text-heading-5 text-heading-6 text-secondary dark:text-accent">
                    Setting up your dashboard
                  </p>
                  <p class="line-clamp-2">
                    Customize your experience and create a system that works for you.
                  </p>
                </div>
                <div>
                  <a href="/property-management-software-blog-details.php"
                    class="btn hover:btn-secondary dark:hover:btn-accent dark:btn-transparent btn-white btn-md">
                    <span>Read more</span>
                  </a>
                </div>
              </div>
            </article>
          </div>
        </div>

        <!-- blog four  -->
        <div data-filter-item="" data-filter-category="design" class="col-span-12 sm:col-span-6 lg:col-span-4">
          <div>
            <article
              class="dark:bg-background-5 border-stroke-4 dark:border-stroke-5 mx-auto max-w-[500px] overflow-hidden rounded-[20px] border bg-white md:mx-0 md:max-w-full">
              <figure>
                <img src="images/ns-img-461.png" alt="Abstract red and pink blurred for Design guide" loading="lazy"
                  class="h-full w-full object-cover">
              </figure>
              <div class="space-y-6 p-6">
                <div class="flex items-center gap-3.5 xl:gap-6">
                  <span class="badge badge-green">Design</span>
                  <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                      <path
                        d="M10.5 17.5C14.6421 17.5 18 14.1421 18 10C18 5.85786 14.6421 2.5 10.5 2.5C6.35786 2.5 3 5.85786 3 10C3 14.1421 6.35786 17.5 10.5 17.5Z"
                        class="stroke-secondary dark:stroke-accent" stroke-miterlimit="10"></path>
                      <path d="M10.5 5.625V10H14.875" class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    </svg>
                    <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                      25 Hours
                    </span>
                  </span>
                  <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                      <path
                        d="M4.25 16.875C4.25 16.3777 4.44754 15.9008 4.79917 15.5492C5.15081 15.1975 5.62772 15 6.125 15H16.75V2.5H6.125C5.62772 2.5 5.15081 2.69754 4.79917 3.04917C4.44754 3.40081 4.25 3.87772 4.25 4.375V16.875Z"
                        class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                      </path>
                      <path d="M4.25 16.875V17.5H15.5" class="stroke-secondary dark:stroke-accent"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                      25 Hours
                    </span>
                  </span>
                </div>
                <div class="space-y-2">
                  <p class="xl:text-heading-5 text-heading-6 text-secondary dark:text-accent">
                    Getting started guide
                  </p>
                  <p class="line-clamp-2">
                    A step-by-step walkthrough of how to set up your workspace and get your first
                    results.
                  </p>
                </div>
                <div>
                  <a href="/property-management-software-blog-details.php"
                    class="btn hover:btn-secondary dark:hover:btn-accent dark:btn-transparent btn-white btn-md">
                    <span>Read more</span>
                  </a>
                </div>
              </div>
            </article>
          </div>
        </div>

        <!-- blog five  -->
        <div data-filter-item="" data-filter-category="ai software" class="col-span-12 sm:col-span-6 lg:col-span-4">
          <div>
            <article
              class="dark:bg-background-5 border-stroke-4 dark:border-stroke-5 mx-auto max-w-[500px] overflow-hidden rounded-[20px] border bg-white md:mx-0 md:max-w-full">
              <figure>
                <img src="images/ns-img-462.png" alt="Abstract green and orange blurred for AI software product tour"
                  loading="lazy" class="h-full w-full object-cover">
              </figure>
              <div class="space-y-6 p-6">
                <div class="flex items-center gap-3.5 xl:gap-6">
                  <span class="badge badge-green">AI software</span>
                  <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                      <path
                        d="M10.5 17.5C14.6421 17.5 18 14.1421 18 10C18 5.85786 14.6421 2.5 10.5 2.5C6.35786 2.5 3 5.85786 3 10C3 14.1421 6.35786 17.5 10.5 17.5Z"
                        class="stroke-secondary dark:stroke-accent" stroke-miterlimit="10"></path>
                      <path d="M10.5 5.625V10H14.875" class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    </svg>
                    <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                      25 Hours
                    </span>
                  </span>
                  <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                      <path
                        d="M4.25 16.875C4.25 16.3777 4.44754 15.9008 4.79917 15.5492C5.15081 15.1975 5.62772 15 6.125 15H16.75V2.5H6.125C5.62772 2.5 5.15081 2.69754 4.79917 3.04917C4.44754 3.40081 4.25 3.87772 4.25 4.375V16.875Z"
                        class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                      </path>
                      <path d="M4.25 16.875V17.5H15.5" class="stroke-secondary dark:stroke-accent"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                      25 Hours
                    </span>
                  </span>
                </div>
                <div class="space-y-2">
                  <p class="xl:text-heading-5 text-heading-6 text-secondary dark:text-accent">
                    Product tour
                  </p>
                  <p class="line-clamp-2">
                    A visual overview of everything you can do inside StayFlow.
                  </p>
                </div>
                <div>
                  <a href="/property-management-software-blog-details.php"
                    class="btn hover:btn-secondary dark:hover:btn-accent dark:btn-transparent btn-white btn-md">
                    <span>Read more</span>
                  </a>
                </div>
              </div>
            </article>
          </div>
        </div>

        <!-- blog six  -->
        <div data-filter-item="" data-filter-category="investment" class="col-span-12 sm:col-span-6 lg:col-span-4">
          <div>
            <article
              class="blog-item dark:bg-background-5 border-stroke-4 dark:border-stroke-5 mx-auto max-w-[500px] overflow-hidden rounded-[20px] border bg-white md:mx-0 md:max-w-full">
              <figure>
                <img src="images/ns-img-463.png" alt="Abstract green and gold blurred for Investment dashboard"
                  loading="lazy" class="h-full w-full object-cover">
              </figure>
              <div class="space-y-6 p-6">
                <div class="flex items-center gap-3.5 xl:gap-6">
                  <span class="badge badge-green">Investment</span>
                  <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                      <path
                        d="M10.5 17.5C14.6421 17.5 18 14.1421 18 10C18 5.85786 14.6421 2.5 10.5 2.5C6.35786 2.5 3 5.85786 3 10C3 14.1421 6.35786 17.5 10.5 17.5Z"
                        class="stroke-secondary dark:stroke-accent" stroke-miterlimit="10"></path>
                      <path d="M10.5 5.625V10H14.875" class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    </svg>
                    <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                      25 Hours
                    </span>
                  </span>
                  <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewbox="0 0 21 20" fill="none">
                      <path
                        d="M4.25 16.875C4.25 16.3777 4.44754 15.9008 4.79917 15.5492C5.15081 15.1975 5.62772 15 6.125 15H16.75V2.5H6.125C5.62772 2.5 5.15081 2.69754 4.79917 3.04917C4.44754 3.40081 4.25 3.87772 4.25 4.375V16.875Z"
                        class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                      </path>
                      <path d="M4.25 16.875V17.5H15.5" class="stroke-secondary dark:stroke-accent"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span class="text-tagline-3 text-secondary dark:text-accent font-normal whitespace-nowrap">
                      25 Hours
                    </span>
                  </span>
                </div>
                <div class="space-y-2">
                  <p class="xl:text-heading-5 text-heading-6 text-secondary dark:text-accent">
                    Setting up your dashboard
                  </p>
                  <p class="line-clamp-2">
                    Customize your experience and create a system that works for you.
                  </p>
                </div>
                <div>
                  <a href="/property-management-software-blog-details.php"
                    class="btn hover:btn-secondary dark:hover:btn-accent dark:btn-transparent btn-white btn-md">
                    <span>Read more</span>
                  </a>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
      <div data-ns-animate="" data-delay="0.2" class="mt-14 text-center">
        <a href="/property-management-software-blog.php"
          class="btn btn-secondary dark:btn-transparent hover:btn-primary btn-md">
          <span>Explore the blog</span>
        </a>
      </div>
    </div>
  </section>

  <!-- Feature section -->
  <section
    class="bg-background-2 dark:bg-background-5 pt-[50px] pb-[100px] md:pt-[100px] md:pb-[200px] dark:md:pb-[100px]">
    <div class="main-container">
      <div class="mb-[35px] space-y-5 text-center md:mb-[70px]">
        <span data-ns-animate="" data-delay="0.1" class="badge badge-ivory">More features</span>
        <div class="space-y-3">
          <h2 data-ns-animate="" data-delay="0.2">Featured guides</h2>
          <p data-ns-animate="" data-delay="0.3" class="mx-auto max-w-[578px]">
            Our platform is designed with a focus on performance, usability, and innovation,
            delivering a seamless experience tailored to modern needs.
          </p>
        </div>
      </div>
      <div class="grid grid-cols-12 gap-y-10 md:gap-8">
        <div data-ns-animate="" data-delay="0.4" class="col-span-12 md:col-span-6 lg:col-span-4">
          <div class="space-y-6 text-center">
            <div class="mx-auto">
              <div
                class="feature-icon-anim flex size-14 items-center justify-center rounded-2xl bg-primary-500/10 text-primary-500 dark:bg-primary-400/20 dark:text-primary-300 border border-primary-500/20 dark:border-primary-400/30 shrink-0 shadow-sm"
                style="animation-delay: 0.1s;"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2" />
                  <path d="M9 22v-4h6v4" />
                  <path d="M8 6h.01" />
                  <path d="M16 6h.01" />
                  <path d="M12 6h.01" />
                  <path d="M12 10h.01" />
                  <path d="M12 14h.01" />
                  <path d="M16 10h.01" />
                  <path d="M16 14h.01" />
                  <path d="M8 10h.01" />
                  <path d="M8 14h.01" />
                </svg></div>
            </div>
            <div class="space-y-2">
              <h3 class="text-heading-5">How to automate repetitive</h3>
              <p>Learn how to streamline your workflow with smart automation.</p>
            </div>
          </div>
        </div>
        <div data-ns-animate="" data-delay="0.5" class="col-span-12 md:col-span-6 lg:col-span-4">
          <div class="space-y-6 text-center">
            <div class="mx-auto">
              <div
                class="feature-icon-anim flex size-14 items-center justify-center rounded-2xl bg-primary-500/10 text-primary-500 dark:bg-primary-400/20 dark:text-primary-300 border border-primary-500/20 dark:border-primary-400/30 shrink-0 shadow-sm"
                style="animation-delay: 0.1s;"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2" />
                  <path d="M9 22v-4h6v4" />
                  <path d="M8 6h.01" />
                  <path d="M16 6h.01" />
                  <path d="M12 6h.01" />
                  <path d="M12 10h.01" />
                  <path d="M12 14h.01" />
                  <path d="M16 10h.01" />
                  <path d="M16 14h.01" />
                  <path d="M8 10h.01" />
                  <path d="M8 14h.01" />
                </svg></div>
            </div>
            <div class="space-y-2">
              <h3 class="text-heading-5">Making sense of your data</h3>
              <p>
                Monitor your suppliers, manage purchase orders seamlessly, and keep an eye on your
              </p>
            </div>
          </div>
        </div>
        <div data-ns-animate="" data-delay="0.6" class="col-span-12 md:col-span-6 lg:col-span-4">
          <div class="space-y-6 text-center">
            <div class="mx-auto">
              <div
                class="feature-icon-anim flex size-14 items-center justify-center rounded-2xl bg-primary-500/10 text-primary-500 dark:bg-primary-400/20 dark:text-primary-300 border border-primary-500/20 dark:border-primary-400/30 shrink-0 shadow-sm"
                style="animation-delay: 0.1s;"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2" />
                  <path d="M9 22v-4h6v4" />
                  <path d="M8 6h.01" />
                  <path d="M16 6h.01" />
                  <path d="M12 6h.01" />
                  <path d="M12 10h.01" />
                  <path d="M12 14h.01" />
                  <path d="M16 10h.01" />
                  <path d="M16 14h.01" />
                  <path d="M8 10h.01" />
                  <path d="M8 14h.01" />
                </svg></div>
            </div>
            <div class="space-y-2">
              <h3 class="text-heading-5">Best practices for managing</h3>
              <p>
                Help your team stay aligned and productive using shared dashboards and custom roles.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Tutorials section -->
  <section class="bg-background-3 dark:bg-background-5 pt-[100px] pb-[100px] xl:pt-[200px] dark:pt-[100px]">
    <div class="main-container">
      <div class="mb-[70px] space-y-3 text-center">
        <h2 data-ns-animate="" data-delay="0.1">Video tutorials</h2>
        <p data-ns-animate="" data-delay="0.2" class="mx-auto max-w-[620px]">
          Our innovative, dynamic, and talented team is the driving force behind our success. Each
          member brings a unique blend of expertise
        </p>
      </div>
      <div class="grid grid-cols-12 gap-y-8 md:gap-8">
        <div data-ns-animate="" data-delay="0.3" class="col-span-12 md:col-span-6 lg:col-span-4">
          <div class="group relative h-full overflow-hidden rounded-[20px] md:max-h-[408px]">
            <figure class="h-full w-full">
              <img src="images/stayflow-blog-kyc-guide.png"
                alt="StayFlow guide on tenant KYC compliance for PG operators" class="h-full w-full object-cover">
            </figure>
            <div
              class="bg-secondary/10 absolute right-0 bottom-2.5 left-0 mx-auto w-[95%] rounded-xl p-6 text-center backdrop-blur-[10px] transition-all duration-500 group-hover:translate-y-0 group-hover:opacity-100 md:translate-y-full md:opacity-0">
              <h3 class="text-heading-5 text-accent">Intro to StayFlow</h3>
              <p class="flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                  <path
                    d="M10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5Z"
                    stroke="#FCFCFC" stroke-opacity="0.6" stroke-miterlimit="10"></path>
                  <path d="M10 5.625V10H14.375" stroke="#FCFCFC" stroke-opacity="0.6" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                </svg>
                <span class="text-accent/60 text-tagline-3">25 Hours</span>
              </p>
            </div>
          </div>
        </div>
        <div data-ns-animate="" data-delay="0.4" class="col-span-12 md:col-span-6 lg:col-span-4">
          <div class="group relative h-full overflow-hidden rounded-[20px] md:max-h-[408px]">
            <figure>
              <img src="images/stayflow-blog-meter-billing.png"
                alt="StayFlow guide on electricity sub-meter billing optimization">
            </figure>
            <div
              class="bg-secondary/10 absolute right-0 bottom-2.5 left-0 mx-auto w-[95%] translate-y-0 rounded-xl p-6 text-center opacity-100 backdrop-blur-[10px] transition-all duration-500 group-hover:translate-y-0 group-hover:opacity-100 md:translate-y-full md:opacity-0">
              <h3 class="text-heading-5 text-accent">Intro to React</h3>
              <p class="flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                  <path
                    d="M10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5Z"
                    stroke="#FCFCFC" stroke-opacity="0.6" stroke-miterlimit="10"></path>
                  <path d="M10 5.625V10H14.375" stroke="#FCFCFC" stroke-opacity="0.6" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                </svg>
                <span class="text-accent/60 text-tagline-3">25 Hours</span>
              </p>
            </div>
          </div>
        </div>
        <div data-ns-animate="" data-delay="0.5" class="col-span-12 md:col-span-6 lg:col-span-4">
          <div class="group relative h-full overflow-hidden rounded-[20px] md:max-h-[408px]">
            <figure>
              <img src="images/ns-img-388.png" alt="Intro to JavaScript video tutorial thumbnail">
            </figure>
            <div
              class="bg-secondary/10 absolute right-0 bottom-2.5 left-0 mx-auto w-[95%] translate-y-0 rounded-xl p-6 text-center opacity-100 backdrop-blur-[10px] transition-all duration-500 group-hover:translate-y-0 group-hover:opacity-100 md:translate-y-full md:opacity-0">
              <h3 class="text-heading-5 text-accent">Intro to JavaScript</h3>
              <p class="flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                  <path
                    d="M10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5Z"
                    stroke="#FCFCFC" stroke-opacity="0.6" stroke-miterlimit="10"></path>
                  <path d="M10 5.625V10H14.375" stroke="#FCFCFC" stroke-opacity="0.6" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                </svg>
                <span class="text-accent/60 text-tagline-3">25 Hours</span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Services section -->
  <section class="md:py-[200px] dark:md:py-[100px] sm:py-[100px] py-[50px] bg-background-3 dark:bg-background-5">
    <div data-ns-animate="" data-delay="0.1" class="cards-marquee-container">
      <div class="flex items-center justify-center gap-8 mb-14">
        <!-- results  Item 1-->
        <div
          class="min-w-[360px] min-h-[270px] bg-background-2 hover:bg-secondary dark:bg-background-6 hover:dark:bg-background-8 rounded-[20px] relative p-8 flex flex-col gap-y-8 z-0 overflow-hidden justify-between group transition-all duration-700 ease-in-out ml-8">
          <figure
            class="absolute inset-0 -z-10 opacity-0 group-hover:opacity-100 rotate-[-78deg] -right-[160%] -top-[210%] transition-all duration-1000 ease-in-out transform group-hover:scale-110 select-none pointer-events-none">
            <img src="images/ns-img-516.png" alt="review-bg" class="w-full h-full object-cover">
          </figure>
          <div class="transition-all duration-700 ease-in-out transform group-hover:translate-y-[4px]">
            <p
              class="group-hover:text-white text-secondary/60 dark:text-accent/60 text-lg mb-2 transition-colors duration-700 ease-in-out">
              Total users
            </p>
            <h3
              class="group-hover:text-ns-yellow text-secondary dark:text-accent transition-colors duration-700 ease-in-out">
              2.18+
            </h3>
          </div>
          <p
            class="group-hover:text-accent/60 text-secondary/60 dark:text-accent/60 transition-all duration-700 ease-in-out transform group-hover:translate-y-[-6px] group-hover:opacity-90">
            Million active users
          </p>
        </div>
        <!-- results  Item 2-->
        <div
          class="min-w-[360px] min-h-[270px] bg-background-2 hover:bg-secondary dark:bg-background-6 hover:dark:bg-background-8 rounded-[20px] relative p-8 flex flex-col gap-y-8 z-0 overflow-hidden justify-between group transition-all duration-700 ease-in-out">
          <figure
            class="absolute inset-0 -z-10 opacity-0 group-hover:opacity-100 rotate-[-78deg] -right-[160%] -top-[210%] transition-all duration-1000 ease-in-out transform group-hover:scale-110 select-none pointer-events-none">
            <img src="images/ns-img-516.png" alt="review-bg" class="w-full h-full object-cover">
          </figure>
          <div class="transition-all duration-700 ease-in-out transform group-hover:translate-y-[4px]">
            <p
              class="group-hover:text-white text-secondary/60 dark:text-accent/60 text-lg mb-2 transition-colors duration-700 ease-in-out">
              System uptime
            </p>
            <h3
              class="group-hover:text-ns-yellow text-secondary dark:text-accent transition-colors duration-700 ease-in-out">
              89.7%
            </h3>
          </div>
          <p
            class="group-hover:text-accent/60 text-secondary/60 dark:text-accent/60 transition-all duration-700 ease-in-out transform group-hover:translate-y-[-6px] group-hover:opacity-90">
            Average system availability
          </p>
        </div>
        <!-- results  Item 3-->
        <div
          class="min-w-[360px] min-h-[270px] bg-background-2 hover:bg-secondary dark:bg-background-6 hover:dark:bg-background-8 rounded-[20px] relative p-8 flex flex-col gap-y-8 z-0 overflow-hidden justify-between group transition-all duration-700 ease-in-out">
          <figure
            class="absolute inset-0 -z-10 opacity-0 group-hover:opacity-100 rotate-[-78deg] -right-[160%] -top-[210%] transition-all duration-1000 ease-in-out transform group-hover:scale-110 select-none pointer-events-none">
            <img src="images/ns-img-516.png" alt="review-bg" class="w-full h-full object-cover">
          </figure>
          <div class="transition-all duration-700 ease-in-out transform group-hover:translate-y-[4px]">
            <p
              class="group-hover:text-white text-secondary/60 dark:text-accent/60 text-lg mb-2 transition-colors duration-700 ease-in-out">
              Global reach
            </p>
            <h3
              class="group-hover:text-ns-yellow text-secondary dark:text-accent transition-colors duration-700 ease-in-out">
              70+
            </h3>
          </div>
          <p
            class="group-hover:text-accent/60 text-secondary/60 dark:text-accent/60 transition-all duration-700 ease-in-out transform group-hover:translate-y-[-6px] group-hover:opacity-90">
            Countries served worldwide
          </p>
        </div>
        <!-- results  Item 4-->
        <div
          class="min-w-[360px] min-h-[270px] bg-background-2 hover:bg-secondary dark:bg-background-6 hover:dark:bg-background-8 rounded-[20px] relative p-8 flex flex-col gap-y-8 z-0 overflow-hidden justify-between group transition-all duration-700 ease-in-out">
          <figure
            class="absolute inset-0 -z-10 opacity-0 group-hover:opacity-100 rotate-[-78deg] -right-[160%] -top-[210%] transition-all duration-1000 ease-in-out transform group-hover:scale-110 select-none pointer-events-none">
            <img src="images/ns-img-516.png" alt="review-bg" class="w-full h-full object-cover">
          </figure>
          <div class="transition-all duration-700 ease-in-out transform group-hover:translate-y-[4px]">
            <p
              class="group-hover:text-white text-secondary/60 dark:text-accent/60 text-lg mb-2 transition-colors duration-700 ease-in-out">
              Response time
            </p>
            <h3
              class="group-hover:text-ns-yellow text-secondary dark:text-accent transition-colors duration-700 ease-in-out">
              3 minutes
            </h3>
          </div>
          <p
            class="group-hover:text-accent/60 text-secondary/60 dark:text-accent/60 transition-all duration-700 ease-in-out transform group-hover:translate-y-[-6px] group-hover:opacity-90">
            Average support response
          </p>
        </div>
        <!-- results  Item 5-->
        <div
          class="min-w-[360px] min-h-[270px] bg-background-2 hover:bg-secondary dark:bg-background-6 hover:dark:bg-background-8 rounded-[20px] relative p-8 flex flex-col gap-y-8 z-0 overflow-hidden justify-between group transition-all duration-700 ease-in-out">
          <figure
            class="absolute inset-0 -z-10 opacity-0 group-hover:opacity-100 rotate-[-78deg] -right-[160%] -top-[210%] transition-all duration-1000 ease-in-out transform group-hover:scale-110 select-none pointer-events-none">
            <img src="images/ns-img-516.png" alt="review-bg" class="w-full h-full object-cover">
          </figure>
          <div class="transition-all duration-700 ease-in-out transform group-hover:translate-y-[4px]">
            <p
              class="group-hover:text-white text-secondary/60 dark:text-accent/60 text-lg mb-2 transition-colors duration-700 ease-in-out">
              Enterprise clients
            </p>
            <h3
              class="group-hover:text-ns-yellow text-secondary dark:text-accent transition-colors duration-700 ease-in-out">
              150+
            </h3>
          </div>
          <p
            class="group-hover:text-accent/60 text-secondary/60 dark:text-accent/60 transition-all duration-700 ease-in-out transform group-hover:translate-y-[-6px] group-hover:opacity-90">
            Active enterprise customers
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Integration section -->
  <section class="md:pt-[100px] md:pb-[200px] sm:pb-[100px] pb-[50px]">
    <div
      class="2xl:max-w-[1440px] xl:max-w-[1240px] lg:max-w-[980px] lg:mx-auto sm:mx-10 mx-5 rounded-4xl bg-white dark:bg-background-8 md:py-[70px] py-[35px] relative lg:min-h-[740px] overflow-hidden z-10">
      <figure data-ns-animate="" data-delay="0.6"
        class="absolute top-1/2 left-1/2 -translate-x-1/2 size-[1000px] mx-auto -z-10 rotate-[336deg] select-none pointer-events-none">
        <img src="images/ns-img-508.png" class="size-full" alt="gradient">
      </figure>
      <div class="text-center md:space-y-14 space-y-8">
        <div class="space-y-5">
          <span data-ns-animate="" data-delay="0.1" class="badge badge-green">Integration</span>
          <div class="space-y-3">
            <h2 data-ns-animate="" data-delay="0.2">Templates & tools</h2>
            <p data-ns-animate="" data-delay="0.3" class="max-w-[490px] mx-auto md:px-0 px-5">
              Make setup simple with ready-to-use templates for various projects, ensuring you can hit
              the ground running without any hassle. Whether you're launching a new website
            </p>
          </div>
        </div>
        <div data-ns-animate="" data-delay="0.4" class="text-center integration-button">
          <div data-ns-animate="" data-delay="0.4" class="text-center integration-button">
            <a href="/property-management-software-integration.php"
              class="btn btn-md btn-secondary dark:btn-transparent hover:btn-primary">
              <span>See It in Action</span></a>
          </div>
        </div>
      </div>
      <div
        class="lg:absolute lg:w-1/2 lg:max-w-[400px] 2xl:max-w-[582px] 2xl:left-0 xl:left-20 lg:left-10 left-5 bottom-0 lg:bottom-auto lg:top-0 lg:block hidden">
        <div
          class="flex flex-row flex-wrap lg:flex-col gap-5 lg:gap-[40px] 2xl:ps-[70px] lg:py-[70px] py-10 relative h-full">
          <div class="flex gap-5 lg:gap-8 xl:gap-[75px] lg:ms-[60px] xl:ms-[100px]">
            <figure data-ns-animate="" data-delay="0.2"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/google-meet.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
          </div>
          <div class="flex gap-5 lg:gap-8 xl:gap-[75px] lg:self-start">
            <figure data-ns-animate="" data-delay="0.3"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/gmail.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
            <figure data-ns-animate="" data-delay="0.4"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/marvel.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
          </div>
          <div class="flex gap-5 lg:gap-8 xl:gap-[75px] lg:ms-[60px] xl:ms-[100px]">
            <figure data-ns-animate="" data-delay="0.5"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/confluence.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
            <figure data-ns-animate="" data-delay="0.6"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/framer.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
          </div>
          <div class="flex gap-5 lg:gap-8 xl:gap-[75px] lg:ms-[100px] xl:ms-[185px]">
            <figure data-ns-animate="" data-delay="0.7"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/vl.svg" class="size-full dark:hidden" alt="StayFlow icon">
                <img src="images/icons/vl-white.svg" class="size-full hidden dark:block" alt="StayFlow icon">
              </div>
            </figure>
            <figure data-ns-animate="" data-delay="0.8"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/edge.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
          </div>
        </div>
      </div>
      <div
        class="lg:absolute lg:w-1/2 lg:max-w-[400px] 2xl:max-w-[582px] 2xl:right-0 xl:right-28 lg:right-10 right-5 bottom-0 lg:bottom-auto lg:top-0 lg:block hidden">
        <div
          class="flex flex-row flex-wrap lg:flex-col gap-5 lg:gap-[40px] 2xl:pe-[70px] lg:py-[70px] py-10 relative h-full">
          <div class="flex lg:self-end lg:me-[60px] xl:me-[100px]">
            <figure data-ns-animate="" data-delay="0.2"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/confluence.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
          </div>
          <div class="flex gap-8 lg:gap-[50px] xl:gap-[75px] lg:self-end">
            <figure data-ns-animate="" data-delay="0.3"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/slack.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
            <figure data-ns-animate="" data-delay="0.4"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/snapchat.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
          </div>
          <div class="flex gap-8 lg:gap-[50px] xl:gap-[75px] lg:me-[60px] lg:self-end xl:me-[100px]">
            <figure data-ns-animate="" data-delay="0.5"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/google.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
            <figure data-ns-animate="" data-delay="0.6"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/ms_yammer.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
          </div>
          <div class="flex gap-8 lg:gap-[50px] xl:gap-[75px] lg:me-[100px] xl:me-[185px] self-end">
            <figure data-ns-animate="" data-delay="0.7"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/microsoft.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
            <figure data-ns-animate="" data-delay="0.8"
              class="size-[100px] 2xl:size-[120px] flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
              <div class="size-[80px] 2xl:size-[100px] bg-white dark:bg-background-6 rounded-full p-6">
                <img src="images/icons/figma.svg" class="size-full" alt="StayFlow icon">
              </div>
            </figure>
          </div>
        </div>
      </div>

      <!-- FOR MOBILE -->
      <div class="lg:hidden flex gap-4 flex-wrap items-center justify-center md:mt-20 mt-8 sm:px-10 px-6">
        <figure data-ns-animate="" data-delay="0.4"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/google-meet.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.4"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/gmail.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.4"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/marvel.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.4"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/confluence.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.5"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/framer.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.5"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/vl.svg" class="size-full dark:hidden" alt="StayFlow icon">
            <img src="images/icons/vl-white.svg" class="size-full hidden dark:block" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.5"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/edge.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>

        <figure data-ns-animate="" data-delay="0.5"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/confluence.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.6"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/slack.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.6"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/snapchat.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.6"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/google.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.6"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/ms_yammer.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.7"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/microsoft.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
        <figure data-ns-animate="" data-delay="0.7"
          class="sm:size-[75px] size-14 flex items-center justify-center bg-background-2 dark:bg-background-7 rounded-full">
          <div class="sm:size-[60px] size-10 bg-white dark:bg-background-6 rounded-full p-2">
            <img src="images/icons/figma.svg" class="size-full" alt="StayFlow icon">
          </div>
        </figure>
      </div>
    </div>
  </section>

  <!-- Community section -->
  <section class="bg-background-3 dark:bg-background-5 py-[50px] md:py-[100px]">
    <div class="main-container">
      <div class="mb-[70px] space-y-3 text-center">
        <h2 data-ns-animate="" data-delay="0.1">Join the StayFlow community</h2>
        <p data-ns-animate="" data-delay="0.2">
          Ask questions, share solutions, and connect with other users around the world.
        </p>
      </div>
      <div class="grid grid-cols-12 gap-y-8 md:gap-8">
        <div data-ns-animate="" data-delay="0.3" class="col-span-12 md:col-span-6 lg:col-span-4">
          <div class="bg-background-1 dark:bg-background-6 space-y-6 rounded-[20px] p-7 text-center sm:p-11">
            <div class="mx-auto">
              <div
                class="feature-icon-anim flex size-14 items-center justify-center rounded-2xl bg-primary-500/10 text-primary-500 dark:bg-primary-400/20 dark:text-primary-300 border border-primary-500/20 dark:border-primary-400/30 shrink-0 shadow-sm"
                style="animation-delay: 0.1s;"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2" />
                  <path d="M9 22v-4h6v4" />
                  <path d="M8 6h.01" />
                  <path d="M16 6h.01" />
                  <path d="M12 6h.01" />
                  <path d="M12 10h.01" />
                  <path d="M12 14h.01" />
                  <path d="M16 10h.01" />
                  <path d="M16 14h.01" />
                  <path d="M8 10h.01" />
                  <path d="M8 14h.01" />
                </svg></div>
            </div>
            <div class="space-y-2">
              <h3 class="text-heading-5">Open discussions</h3>
              <p>Learn how to streamline your workflow with smart automation.</p>
            </div>
          </div>
        </div>
        <div data-ns-animate="" data-delay="0.4" class="col-span-12 md:col-span-6 lg:col-span-4">
          <div class="bg-background-1 dark:bg-background-6 space-y-6 rounded-[20px] p-7 text-center sm:p-11">
            <div class="mx-auto">
              <div
                class="feature-icon-anim flex size-14 items-center justify-center rounded-2xl bg-primary-500/10 text-primary-500 dark:bg-primary-400/20 dark:text-primary-300 border border-primary-500/20 dark:border-primary-400/30 shrink-0 shadow-sm"
                style="animation-delay: 0.1s;"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2" />
                  <path d="M9 22v-4h6v4" />
                  <path d="M8 6h.01" />
                  <path d="M16 6h.01" />
                  <path d="M12 6h.01" />
                  <path d="M12 10h.01" />
                  <path d="M12 14h.01" />
                  <path d="M16 10h.01" />
                  <path d="M16 14h.01" />
                  <path d="M8 10h.01" />
                  <path d="M8 14h.01" />
                </svg></div>
            </div>
            <div class="space-y-2">
              <h3 class="text-heading-5">Use case sharing</h3>
              <p>
                Monitor your suppliers, manage purchase orders seamlessly, and keep an eye on your
              </p>
            </div>
          </div>
        </div>
        <div data-ns-animate="" data-delay="0.5" class="col-span-12 md:col-span-6 lg:col-span-4">
          <div class="bg-background-1 dark:bg-background-6 space-y-6 rounded-[20px] p-7 text-center sm:p-11">
            <div class="mx-auto">
              <div
                class="feature-icon-anim flex size-14 items-center justify-center rounded-2xl bg-primary-500/10 text-primary-500 dark:bg-primary-400/20 dark:text-primary-300 border border-primary-500/20 dark:border-primary-400/30 shrink-0 shadow-sm"
                style="animation-delay: 0.1s;"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2" />
                  <path d="M9 22v-4h6v4" />
                  <path d="M8 6h.01" />
                  <path d="M16 6h.01" />
                  <path d="M12 6h.01" />
                  <path d="M12 10h.01" />
                  <path d="M12 14h.01" />
                  <path d="M16 10h.01" />
                  <path d="M16 14h.01" />
                  <path d="M8 10h.01" />
                  <path d="M8 14h.01" />
                </svg></div>
            </div>
            <div class="space-y-2">
              <h3 class="text-heading-5">Feedback & feature requests</h3>
              <p>
                Help your team stay aligned and productive using shared dashboards and custom roles.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA v1 section -->
  <section class="py-[50px] md:py-20 lg:py-28 dark:bg-background-5 bg-white" aria-label="Use Case Overview">
    <div class="main-container">
      <div class="flex items-center flex-col lg:flex-row justify-between">
        <div
          class="xl:max-w-[650px] lg:max-w-[476px] max-[400px]:max-w-[300px] w-full space-y-5 text-center lg:text-left">
          <span data-ns-animate="" data-delay="0.3" class="badge badge-green badge-green">Get started</span>
          <div class="space-y-3">
            <h2 data-ns-animate="" data-delay="0.4"
              class="text-secondary dark:text-accent text-heading-5 sm:text-heading-4 lg:text-heading-2">
              Ready to start earning with StayFlow?
              <span class="text-primary-500 hidden">{=$span-text}</span>
            </h2>
            <p data-ns-animate="" data-delay="0.5">If you have any questions, feel free to reach out to our team.</p>
          </div>
        </div>

        <div
          class="lg:basis-[466px] space-y-6 md:ml-0 xl:ml-[100px] pt-[40px] lg:pt-[67px] w-full sm:w-[80%] md:w-[60%]">
          <form data-ns-animate="" data-delay="0.6" action="/property-management-software-pricing.php" method="get"
            class="flex items-center flex-col gap-5 sm:flex-row justify-start lg:gap-3">
            <input type="email" name="email" id="userEmail-cta-v1" placeholder="Enter your email" required=""
              class="px-[18px] shadow-1 h-12 py-3 placeholder:text-secondary/50 rounded-full border border-stroke-1 lg:max-w-[340px] md:w-[71%] w-full max-[376px]:w-full dark:border-stroke-7 dark:placeholder:text-accent/60 focus:outline-none focus:border-primary-600 dark:focus:border-primary-400 dark:text-accent placeholder:font-normal font-normal">

            <button type="submit"
              class="btn btn-md btn-primary h-12 w-full sm:w-[28%] lg:w-auto hover:btn-secondary dark:hover:btn-accent">
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