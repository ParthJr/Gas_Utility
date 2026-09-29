<?php
$page_title = 'StayFlow Privacy Policy - Data Protection & Security';
$page_description = 'Read StayFlow';
$page_keywords = 'StayFlow privacy policy, PG software data security, tenant KYC privacy, property management data protection';
$page_canonical = 'https://stayflow.antideploy.app/property-management-software-privacy.php';
$page_og_image = 'https://images.prismic.io/staticmania/aPD-K55xUNkB2D2X_og-image.jpg';
include __DIR__ . '/includes/header.php';
?>

<main>
  <!-- Privacy section -->
  <section class="pt-32 pb-[100px] sm:pt-36 md:pt-42 xl:pt-[180px]">
    <div class="main-container">
      <div class="privacy-policy space-y-[75px]">
        <div class="space-y-2">
          <h1 data-ns-animate="" data-delay="0.1">Data protection guidelines</h1>
          <p data-ns-animate="" data-delay="0.2">
            <span class="text-secondary dark:text-accent">StayFlow</span> is a PG and property management platform. We
            specialize in empowering PG owners, hostel operators, and co-living providers to streamline room allocation,
            rent collection, and tenant KYC efficiently. We are committed to protecting your privacy and handling all
            data transparently.
          </p>
        </div>
        <div data-ns-animate="" data-delay="0.3" class="space-y-2">
          <h4>StayFlow privacy policy</h4>
          <p>
            This Privacy Policy describes how your personal and property information is collected, used, and protected
            when you visit, subscribe, register, or use
            <a href="https://stayflow.antideploy.app/" class="text-secondary dark:text-accent">
              https://stayflow.antideploy.app/
            </a>
            (the "Site").
          </p>
        </div>
        <div data-ns-animate="" data-delay="0.4" class="space-y-6">
          <div class="space-y-2">
            <h4>Personal information we collect</h4>
            <p>
              When you visit the Site, we automatically collect certain information about your device,
              including information about your web browser, IP address, time zone, and cookies
              installed on your device. Additionally, as you browse the Site, we collect information
              about the individual pages you view, what websites or search terms referred you to the
              Site, and how you interact with the Site. We call this automatically-collected
              information
              <span class="text-secondary dark:text-accent">"Device Information." </span>
            </p>
          </div>

          <ul data-ns-animate="" data-delay="0.5"
            class="text-tagline-1 text-secondary/60 dark:text-accent/60 list-inside space-y-3 font-normal">
            <li>
              <strong class="text-secondary dark:text-accent font-normal">Cookies – </strong>
              Data files placed on your device, often including an anonymous unique identifier. <br>
              ( Learn more about cookies and how to disable them:
              <a href="http://www.allaboutcookies.org" class="text-secondary">
                http://www.allaboutcookies.org
              </a>
              )
            </li>
            <li>
              <strong class="text-secondary dark:text-accent font-normal"> Log Files – </strong>
              Track actions on the Site and collect IP address, browser type, ISP, referring/exit
              pages, and timestamps.
            </li>
            <li>
              <strong class="text-secondary dark:text-accent font-normal">
                Web Beacons, Tags, and Pixels –
              </strong>
              Electronic files to monitor site usage and interaction.
            </li>
            <li>
              <strong class="text-secondary dark:text-accent font-normal">
                Google Analytics and Pixels –
              </strong>
              Collect traffic-related information and interaction behavior.
            </li>
          </ul>
        </div>

        <div>
          <div class="grid grid-cols-12 gap-y-[100px] lg:gap-[100px]">
            <div class="col-span-12 lg:col-span-6">
              <div data-ns-animate="" data-delay="0.6" class="mb-[70px] text-left">
                <p class="max-w-[550px]">
                  When you make or attempt to purchase through the Site, we collect information such
                  as your name, email address, billing address, shipping address, payment details, and
                  any other relevant data necessary to process your order.
                </p>
              </div>
              <figure data-ns-animate="" data-delay="0.7"
                class="w-full max-w-[595px] self-end overflow-hidden rounded-[20px]">
                <img src="images/stayflow-kyc-verification.png" class="size-full object-cover"
                  alt="StayFlow digital tenant KYC ID verification card and document vault">
              </figure>
            </div>
            <div data-ns-animate="" data-delay="0.8" class="col-span-12 lg:col-span-6">
              <form class="dark:bg-background-8 rounded-[20px] bg-white p-6 lg:p-[42px]">
                <fieldset class="mb-8 space-y-2">
                  <label for="name" class="text-tagline-1 text-secondary dark:text-accent block font-medium">
                    Your name
                  </label>
                  <input type="text" name="name" id="name" placeholder="Enter your name"
                    class="dark:text-accent dark:bg-background-6 border-stroke-3 dark:border-stroke-7 bg-background-1 focus-visible:outline-primary-500 placeholder:text-tagline-1 placeholder:text-secondary/60 dark:placeholder:text-accent/60 shadow-1 block h-12 w-full rounded-full border px-[18px] py-3 font-normal placeholder:font-normal focus-visible:outline">
                </fieldset>
                <fieldset class="mb-8 space-y-2">
                  <label for="billing-shipping-addresses"
                    class="text-tagline-1 text-secondary dark:text-accent block font-medium">
                    Billing and shipping addresses
                  </label>
                  <input type="text" name="billing-shipping-addresses" id="billing-shipping-addresses"
                    placeholder="Billing and shipping addresses"
                    class="dark:text-accent dark:bg-background-6 border-stroke-3 dark:border-stroke-7 bg-background-1 focus-visible:outline-primary-500 placeholder:text-tagline-1 placeholder:text-secondary/60 dark:placeholder:text-accent/60 shadow-1 block h-12 w-full rounded-full border px-[18px] py-3 font-normal placeholder:font-normal focus-visible:outline">
                </fieldset>
                <fieldset class="mb-8 space-y-2">
                  <label for="payment-information"
                    class="text-tagline-1 text-secondary dark:text-accent block font-medium">
                    Payment information
                  </label>
                  <input type="text" name="payment-information" id="payment-information"
                    placeholder="credit card, PayPal, or bank details"
                    class="dark:text-accent dark:bg-background-6 border-stroke-3 dark:border-stroke-7 bg-background-1 focus-visible:outline-primary-500 placeholder:text-tagline-1 placeholder:text-secondary/60 dark:placeholder:text-accent/60 shadow-1 block h-12 w-full rounded-full border px-[18px] py-3 font-normal placeholder:font-normal focus-visible:outline">
                </fieldset>
                <fieldset class="space-y-2">
                  <label for="email" class="text-tagline-1 text-secondary dark:text-accent block font-medium">
                    Email address
                  </label>
                  <input type="text" name="email" id="email" placeholder="Enter your email address"
                    class="dark:text-accent dark:bg-background-6 border-stroke-3 dark:border-stroke-7 bg-background-1 focus-visible:outline-primary-500 placeholder:text-tagline-1 placeholder:text-secondary/60 dark:placeholder:text-accent/60 shadow-1 block h-12 w-full rounded-full border px-[18px] py-3 font-normal placeholder:font-normal focus-visible:outline">
                </fieldset>

                <fieldset class="mt-4 mb-4 flex items-center gap-2">
                  <label for="agree-terms" class="flex items-center gap-x-3">
                    <input id="agree-terms" type="checkbox" class="peer sr-only" required="">
                    <span
                      class="border-stroke-3 dark:border-stroke-7 after:bg-primary-500 peer-checked:border-primary-500 relative size-4 cursor-pointer rounded-full border after:absolute after:top-1/2 after:left-1/2 after:size-2.5 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:opacity-0 peer-checked:after:opacity-100"></span>
                  </label>
                  <label for="agree-terms" class="text-tagline-3 text-secondary/60 dark:text-accent/60 cursor-pointer">
                    I agree with the
                    <a href="#" class="text-primary-500 text-tagline-3 underline">terms and conditions</a>
                  </label>
                </fieldset>
                <button type="submit"
                  class="btn dark:btn-accent btn-md btn-secondary hover:btn-primary w-full first-letter:uppercase before:content-none">
                  Submit
                </button>
              </form>
            </div>
          </div>
        </div>

        <div data-ns-animate="" data-delay="0.5" class="space-y-6">
          <div class="space-y-2">
            <h4>How we use your personal information</h4>
            <p>We use the collected Order Information to:</p>
          </div>
          <ul class="text-tagline-1 text-secondary/60 dark:text-accent/60 list-inside space-y-3 font-normal">
            <li>Process your orders, payments, and generate invoices</li>
            <li>Communicate with you</li>
            <li>Screen for potential fraud or risks</li>
            <li>
              Provide you with information or promotions related to our services, when aligned with
              your preferences
            </li>
          </ul>
          <div class="space-y-2">
            <p class="text-secondary dark:text-accent">We use the collected Order Information to:</p>

            <ul class="text-tagline-1 text-secondary/60 dark:text-accent/60 list-inside space-y-3 font-normal">
              <li>Improve and optimize the Site experience</li>
              <li>Analyze customer interactions for performance tracking</li>
              <li>Screen for potential risk and fraud</li>
            </ul>
          </div>
        </div>

        <div data-ns-animate="" data-delay="0.6" class="space-y-6">
          <div class="space-y-2">
            <h4>Sharing your personal information</h4>
            <p>
              We share your Personal Information with trusted third-party service providers to help us
              operate effectively:
            </p>
          </div>
          <ul class="text-tagline-1 text-secondary/60 dark:text-accent/60 list-inside space-y-3 font-normal">
            <li>
              Google Analytics: To understand customer interactions and optimize experience (Learn
              more:
              <a href="#" class="text-secondary dark:text-accent"> Google Privacy Policy </a>)
            </li>
            <li>Payment processors (PayPal, Stripe)</li>
          </ul>
        </div>

        <div data-ns-animate="" data-delay="0.7" class="space-y-6">
          <div class="space-y-2">
            <h4>Do not track</h4>
            <p>
              Please note, we do not alter our Site’s data collection practices when we detect a "Do
              Not Track" signal from your browser.
            </p>
          </div>
        </div>

        <div data-ns-animate="" data-delay="0.8" class="space-y-6">
          <div class="space-y-2">
            <h4>Your rights</h4>
            <p>If you are a resident of the European Economic Area (EEA):</p>
          </div>
          <ul class="text-tagline-1 text-secondary/60 dark:text-accent/60 list-inside space-y-3 font-normal">
            <li>You have the right to access, update, or delete your personal information.</li>
            <li>
              If you wish to exercise these rights, please contact us at
              <a href="mailto:support@stayflow.antideploy.app" class="text-secondary dark:text-accent">
                support@stayflow.antideploy.app
              </a>
            </li>
          </ul>
        </div>

        <div data-ns-animate="" data-delay="0.9" class="space-y-6">
          <div class="space-y-2">
            <h4>Data retention</h4>
            <p>
              We will retain your Order Information for our records unless you ask us to delete this
              information.
            </p>
          </div>
        </div>

        <div data-ns-animate="" data-delay="1" class="space-y-6">
          <div class="space-y-2">
            <h4>Minors</h4>
            <p>Our Site is not intended for individuals under the age of 18.</p>
          </div>
        </div>

        <div data-ns-animate="" data-delay="1.1" class="space-y-6">
          <div class="space-y-2">
            <h4>Changes</h4>
            <p>
              We may update this Privacy Policy periodically to reflect changes to our practices or
              for other operational, legal, or regulatory reasons.
            </p>
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
          <span data-ns-animate="" data-delay="0.3" class="badge badge-green badge-yellow-v2">Get started</span>
          <div class="space-y-3">
            <h2 data-ns-animate="" data-delay="0.4"
              class="text-secondary dark:text-accent text-heading-5 sm:text-heading-4 lg:text-heading-2">
              Automate your PG management with StayFlow
              <span class="text-primary-500">Security</span>
            </h2>
            <p data-ns-animate="" data-delay="0.5">Start your 14-day free trial today to simplify rent collection,
              resident onboarding, and room management.</p>
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