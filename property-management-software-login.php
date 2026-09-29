<?php
require_once __DIR__ . '/includes/config.php';
if (!headers_sent()) {
  header('Location: ' . SAAS_LOGIN_URL);
  exit();
}
$page_title = 'StayFlow Login - Access Your PG Management Portal';
$page_description = 'Log in to your StayFlow dashboard to manage rooms, beds, resident KYC records, rent receipts, and staff activity across all your properties.';
$page_keywords = 'StayFlow login, PG owner portal login, hostel manager login, tenant portal';
$page_canonical = 'https://stayflow.antideploy.app/property-management-software-login.php';
$page_og_image = 'https://images.prismic.io/staticmania/aPD-K55xUNkB2D2X_og-image.jpg';
include __DIR__ . '/includes/header.php';
?>

<main>
  <!-- Login hero v1 section -->
  <section class="lg:pt-[180px] pt-[120px] lg:pb-[100px] pb-[70px]">
    <div class="main-container">
      <div data-ns-animate="" data-delay="0.1"
        class="max-w-[400px] mx-auto bg-background-1 dark:bg-background-6 rounded-[20px] py-14 px-8">
        <form action="<?php echo SAAS_LOGIN_URL; ?>" method="get" class="mb-6">
          <fieldset class="space-y-2 mb-4">
            <label for="email" class="block text-tagline-2 font-medium text-secondary dark:text-accent select-none">
              Your email
            </label>
            <input type="email" id="email" name="email" class="auth-form-input" placeholder="Email address">
          </fieldset>
          <fieldset class="space-y-2 mb-3">
            <label for="password" class="block text-tagline-2 font-medium text-secondary dark:text-accent select-none">
              Password
            </label>
            <input type="password" id="password" name="password" class="auth-form-input"
              placeholder="At least 8 characters">
          </fieldset>
          <div class="flex items-center justify-between">
            <div>
              <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="terms" class="peer sr-only">
                <span
                  class="size-5 rounded-full border border-stroke-3 dark:border-stroke-7 relative after:absolute after:size-3 after:bg-primary-500 after:rounded-full after:top-1/2 after:left-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:opacity-0 peer-checked:after:opacity-100 peer-checked:border-primary-500 cursor-pointer"></span>
                <span class="text-tagline-2 text-secondary font-medium select-none dark:text-accent">
                  Remember me
                </span>
              </label>
            </div>
            <div>
              <a href="<?php echo SAAS_FORGOT_URL; ?>"
                class="text-tagline-2 text-secondary font-medium underline dark:text-accent">Forgot password?</a>
            </div>
          </div>
          <div class="mt-8">
            <button type="submit"
              class="btn btn-md btn-primary hover:btn-secondary dark:hover:btn-accent w-full before:content-none first-letter:uppercase">
              Log In
            </button>
          </div>
        </form>
        <div>
          <p
            class="text-center text-tagline-2 text-secondary font-normal flex items-center justify-center gap-1 dark:text-accent">
            Not registered yet?
            <a href="/property-management-software-pricing.php"
              class="text-tagline-1 font-medium footer-link-v2">Create an Account </a>
          </p>
          <div class="py-8 text-center">
            <p class="text-tagline-2 font-normal text-secondary dark:text-accent">Or</p>
          </div>
          <div class="space-y-4">
            <a href="<?php echo SAAS_LOGIN_URL; ?>"
              class="flex items-center justify-center gap-2 w-full border border-stroke-3 py-3 px-8 rounded-full cursor-pointer dark:border-stroke-7 hover:bg-primary-500 group transition-colors duration-500 ease-in-out">
              <span class="size-6 block">
                <img src="images/icons/google.svg" alt="StayFlow icon" class="size-full">
              </span>
              <span
                class="text-tagline-2 font-medium text-secondary dark:text-accent group-hover:text-accent transition-colors duration-500 ease-in-out">
                Continue with Google
              </span>
            </a>
            <a href="<?php echo SAAS_LOGIN_URL; ?>"
              class="flex items-center justify-center gap-2 w-full border border-stroke-3 py-3 px-8 rounded-full cursor-pointer dark:border-stroke-7 hover:bg-primary-500 group transition-colors duration-500 ease-in-out">
              <span class="size-6 block">
                <img src="images/icons/facebook-v2.svg" alt="StayFlow icon" class="size-full">
              </span>
              <span
                class="text-tagline-2 font-medium text-secondary dark:text-accent group-hover:text-accent transition-colors duration-500 ease-in-out">
                Continue with facebook
              </span>
            </a>
            <a href="<?php echo SAAS_LOGIN_URL; ?>"
              class="flex items-center justify-center gap-2 w-full border border-stroke-3 py-3 px-8 rounded-full cursor-pointer dark:border-stroke-7 hover:bg-primary-500 group transition-colors duration-500 ease-in-out">
              <span class="size-6 block">
                <img src="images/icons/apple.svg" alt="StayFlow icon" class="size-full dark:hidden">
                <img src="images/icons/apple-dark.svg" alt="StayFlow icon" class="size-full hidden dark:block">
              </span>
              <span
                class="text-tagline-2 font-medium text-secondary dark:text-accent group-hover:text-accent transition-colors duration-500 ease-in-out">
                Continue with apple
              </span>
            </a>
            <a href="<?php echo SAAS_LOGIN_URL; ?>"
              class="flex items-center justify-center gap-2 w-full border border-stroke-3 py-3 px-8 rounded-full cursor-pointer dark:border-stroke-7 hover:bg-primary-500 group transition-colors duration-500 ease-in-out">
              <span class="size-6 block">
                <img src="images/icons/microsoft.svg" alt="StayFlow icon" class="size-full">
              </span>
              <span
                class="text-tagline-2 font-medium text-secondary dark:text-accent group-hover:text-accent transition-colors duration-500 ease-in-out">
                Continue with microsoft
              </span>
            </a>
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
            <h1 data-ns-animate="" data-delay="0.4"
              class="text-secondary dark:text-accent text-heading-5 sm:text-heading-4 lg:text-heading-2">
              Build a complete website using the assistance
              <span class="text-primary-500 hidden">{=$span-text}</span>
            </h1>
            <p data-ns-animate="" data-delay="0.5">Start your free trial today and see your ideas come to life easily
              and creatively.</p>
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