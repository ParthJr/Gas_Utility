<?php require_once __DIR__ . '/config.php'; ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="title"
    content="<?php echo isset($page_title) ? htmlspecialchars($page_title) : 'StayFlow – PG & Property Management Software'; ?>">
  <meta name="description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : ''; ?>">
  <?php if (isset($page_keywords) && !empty($page_keywords)): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
  <?php endif; ?>
  <meta name="author" content="StayFlow">
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  <meta name="googlebot" content="index, follow">
  <meta name="bingbot" content="index, follow">
  <meta name="language" content="English">
  <meta name="geo.region" content="US">
  <meta name="geo.placename" content="United States">

  <base href="https://stayflow.antideploy.app/">
  <?php if (isset($page_canonical) && !empty($page_canonical)): ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($page_canonical); ?>">
  <?php endif; ?>


  <meta name="rating" content="General">
  <meta name="distribution" content="Global">
  <meta name="revisit-after" content="7 days">
  <meta name="expires" content="never">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url"
    content="<?php echo isset($page_canonical) ? htmlspecialchars($page_canonical) : 'https://stayflow.antideploy.app/'; ?>">
  <meta property="og:title"
    content="<?php echo isset($page_title) ? htmlspecialchars($page_title) : 'StayFlow – PG & Property Management Software'; ?>">
  <meta property="og:description"
    content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : ''; ?>">
  <meta property="og:image"
    content="<?php echo isset($page_og_image) ? htmlspecialchars($page_og_image) : 'https://images.prismic.io/staticmania/aPD-K55xUNkB2D2X_og-image.jpg'; ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="StayFlow – PG & Property Management Software">
  <meta property="og:site_name" content="StayFlow">
  <meta property="og:locale" content="en_US">
  <meta property="article:author" content="StayFlow">
  <meta property="article:publisher" content="StayFlow">

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@StayFlow">
  <meta name="twitter:creator" content="@StayFlow">
  <meta name="twitter:url"
    content="<?php echo isset($page_canonical) ? htmlspecialchars($page_canonical) : 'https://stayflow.antideploy.app/'; ?>">
  <meta name="twitter:title"
    content="<?php echo isset($page_title) ? htmlspecialchars($page_title) : 'StayFlow – PG & Property Management Software'; ?>">
  <meta name="twitter:description"
    content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : ''; ?>">
  <meta name="twitter:image"
    content="<?php echo isset($page_og_image) ? htmlspecialchars($page_og_image) : 'https://images.prismic.io/staticmania/aPD-K55xUNkB2D2X_og-image.jpg'; ?>">
  <meta name="twitter:image:alt" content="StayFlow – PG & Property Management Software">

  <meta name="theme-color" content="#000000">
  <meta name="msapplication-TileColor" content="#000000">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="StayFlow">
  <meta name="application-name" content="StayFlow">
  <meta name="msapplication-tooltip" content="StayFlow - PG & Property Management Software">
  <meta name="msapplication-starturl" content="/">

  <link rel="icon" type="image/png" href="favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/svg+xml" href="favicon.svg">
  <link rel="shortcut icon" href="favicon.ico">
  <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
  <link rel="manifest" href="site.webmanifest">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap">

  <title>
    <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'StayFlow – PG & Property Management Software'; ?>
  </title>

  <link rel="stylesheet" href="assets/main.css">
  <style>
    @keyframes featureIconFadeUp {
      0% {
        opacity: 0;
        transform: translateY(8px) scale(0.92);
      }

      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .feature-icon-anim {
      animation: featureIconFadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
    }

    .feature-item-row:hover .feature-icon-anim {
      transform: translateY(-2px) scale(1.08) rotate(2deg);
    }

    @media (prefers-reduced-motion: reduce) {
      .feature-icon-anim {
        animation: none !important;
        transition: none !important;
      }

      .feature-item-row:hover .feature-icon-anim {
        transform: none !important;
      }
    }

    /* StayFlow Responsive Brand Logo & Dark Mode Styling */
    .stayflow-brand-logo,
    .logo img {
      width: auto;
      height: 52px;
      max-width: 220px;
      object-fit: contain;
      display: block;
      transition: filter 0.3s ease, transform 0.2s ease;
    }

    @media (max-width: 1024px) {
      .stayflow-brand-logo,
      .logo img {
        height: 46px;
        max-width: 190px;
      }
    }

    @media (max-width: 768px) {
      .stayflow-brand-logo,
      .logo img {
        height: 40px;
        max-width: 165px;
      }
    }

    /* Dark Mode Visibility: Enhances dark navy text against dark backgrounds while preserving green/blue and transparency */
    .dark .stayflow-brand-logo,
    .dark .logo img,
    [data-theme="dark"] .stayflow-brand-logo,
    [data-theme="dark"] .logo img,
    .dark-mode .stayflow-brand-logo,
    .dark-mode .logo img {
      filter: brightness(1.22) drop-shadow(0 0 1px rgba(255, 255, 255, 0.8)) drop-shadow(0 1px 3px rgba(255, 255, 255, 0.25));
    }
  </style>
  <?php if (isset($extra_head))
    echo $extra_head; ?>
</head>

<body class="bg-background-3 dark:bg-background-7">
  <header>
    <div
      class="header-one lp:!max-w-[1290px] bg-background-2 dark:border-stroke-6 dark:bg-background-9 fixed top-5 left-1/2 z-50 mx-auto flex w-full max-w-[350px] -translate-x-1/2 items-center justify-between rounded-full px-2.5 py-2.5 opacity-0 backdrop-blur-[15px] max-[400px]:max-w-[350px] min-[425px]:max-w-[375px] min-[500px]:max-w-[450px] sm:max-w-[540px] md:max-w-[720px] lg:max-w-[960px] xl:max-w-[1140px] xl:py-0 dark:border"
      data-ns-animate="" data-direction="up" data-offset="100">
      <div>
        <a href="/" class="flex items-center logo brand-logo" title="StayFlow – Smart PG Management System">
          <span class="sr-only">StayFlow – Smart PG Management System</span>
          <figure class="flex items-center">
            <img src="images/shared/stayflow-logo.png" alt="StayFlow – Smart PG Management System" class="stayflow-brand-logo">
          </figure>
        </a>
      </div>

      <nav class="hidden items-center xl:flex">
        <ul class="flex items-center">
          <li class="nav-item relative cursor-pointer py-2.5" data-menu="company-mega-menu">
            <a href="#"
              class="hover:border-stroke-2 dark:hover:border-stroke-7 text-tagline-1 text-secondary/60 hover:text-secondary dark:text-accent/60 dark:hover:text-accent flex items-center gap-1 rounded-full border border-transparent px-4 py-2 font-normal transition-all duration-200">
              <span>Company</span>
              <span class="nav-arrow block origin-center translate-y-px transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                </svg>
              </span>
            </a>
            <div>
              <div
                class="dropdown-menu-bridge pointer-events-none absolute top-full left-1/2 z-40 h-3 w-full min-w-[752px] -translate-x-1/2 bg-transparent opacity-0">
              </div>
              <div id="company-mega-menu"
                class="dropdown-menu dark:bg-background-6 border-stroke-1 pointer-events-none absolute top-full left-1/2 z-50 mt-2 flex w-full -translate-x-1/2 items-start gap-y-6 rounded-[20px] border bg-white p-4 opacity-0 transition-all duration-300 md:w-[752px] md:gap-x-6 dark:border-white/10">
                <ul id="product-dropdown-menu" class="flex-1 space-y-1">
                  <li>
                    <a href="/property-management-software-about.php"
                      class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                      <div
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                      </div>

                      <div
                        class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                          <path
                            d="M10.0003 18.3334C14.6027 18.3334 18.3337 14.6024 18.3337 10C18.3337 5.39765 14.6027 1.66669 10.0003 1.66669C5.39795 1.66669 1.66699 5.39765 1.66699 10C1.66699 14.6024 5.39795 18.3334 10.0003 18.3334Z"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path d="M10 13.3333H10.0083" class="stroke-secondary dark:stroke-accent"
                            stroke-linecap="round" stroke-linejoin="round"></path>
                          <path d="M10 6.66669V10" class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        </svg>

                      </div>
                      <div class="relative z-10">
                        <p class="text-tagline-1 text-secondary dark:text-accent font-normal">About Us</p>
                        <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                          See how others are using StayFlow
                        </p>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="bg-stroke-4 dark:bg-background-7 mx-auto h-[1px] w-[calc(100%-16px)]"></div>
                  </li>
                  <li>
                    <a href="/property-management-software-services.php"
                      class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                      <div
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                      </div>

                      <div
                        class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                          <path
                            d="M10 13.75C12.0711 13.75 13.75 12.0711 13.75 10C13.75 7.92893 12.0711 6.25 10 6.25C7.92893 6.25 6.25 7.92893 6.25 10C6.25 12.0711 7.92893 13.75 10 13.75Z"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path
                            d="M9.59975 3.4495L8.12557 2.34419C8.04647 2.28469 7.95437 2.24484 7.85685 2.22792C7.75933 2.211 7.65918 2.2175 7.56467 2.24688C7.1081 2.38985 6.66515 2.57312 6.24103 2.79452C6.15323 2.84057 6.07765 2.90685 6.02051 2.98787C5.96338 3.06889 5.92633 3.16233 5.91242 3.26049L5.65182 5.08478C5.55248 5.17284 5.45523 5.26442 5.36007 5.35953C5.26493 5.45467 5.17332 5.55195 5.08525 5.65136L5.08521 5.65139L3.26124 5.91223C3.16324 5.92608 3.06993 5.96303 2.98901 6.02002C2.90809 6.07702 2.84187 6.15242 2.79581 6.24003C2.57407 6.66397 2.39044 7.10678 2.2471 7.56323C2.21759 7.65787 2.211 7.75818 2.22789 7.85587C2.24478 7.95356 2.28466 8.04584 2.34423 8.12508L3.44993 9.59932C3.44195 9.73182 3.43794 9.86535 3.4379 9.9999C3.4379 10.1344 3.44191 10.268 3.44993 10.4006L3.44993 10.4006L2.34462 11.8748C2.28512 11.9539 2.24527 12.046 2.22835 12.1435C2.21143 12.241 2.21793 12.3412 2.2473 12.4357C2.39028 12.8923 2.57355 13.3352 2.79495 13.7593C2.841 13.8471 2.90727 13.9227 2.98829 13.9799C3.06931 14.037 3.16276 14.074 3.26092 14.088L5.08521 14.3486C5.17326 14.4479 5.26484 14.5451 5.35996 14.6403C5.45509 14.7354 5.55237 14.827 5.65179 14.9151L5.65182 14.9152L5.91265 16.7391C5.92651 16.8371 5.96345 16.9304 6.02045 17.0114C6.07744 17.0923 6.15285 17.1585 6.24046 17.2046C6.66439 17.4263 7.1072 17.6099 7.56365 17.7533C7.6583 17.7828 7.7586 17.7894 7.8563 17.7725C7.95399 17.7556 8.04626 17.7157 8.1255 17.6561L9.59974 16.5504C9.73225 16.5584 9.86577 16.5624 10.0003 16.5625C10.1349 16.5625 10.2684 16.5585 10.401 16.5504L10.4011 16.5504L11.8752 17.6558C11.9543 17.7153 12.0464 17.7551 12.144 17.772C12.2415 17.7889 12.3416 17.7824 12.4361 17.7531C12.8927 17.6101 13.3357 17.4268 13.7598 17.2054C13.8476 17.1594 13.9232 17.0931 13.9803 17.0121C14.0374 16.9311 14.0745 16.8376 14.0884 16.7395L14.349 14.9152C14.4483 14.8271 14.5456 14.7355 14.6407 14.6404C14.7359 14.5453 14.8275 14.448 14.9156 14.3486L14.9156 14.3486L16.7396 14.0877C16.8376 14.0739 16.9309 14.0369 17.0118 13.9799C17.0927 13.9229 17.1589 13.8475 17.205 13.7599C17.4267 13.336 17.6104 12.8932 17.7537 12.4367C17.7832 12.3421 17.7898 12.2418 17.7729 12.1441C17.756 12.0464 17.7161 11.9541 17.6566 11.8749L16.5509 10.4006C16.5589 10.2681 16.5629 10.1346 16.5629 10.0001C16.5629 9.86551 16.5589 9.73195 16.5509 9.59937L16.5509 9.59932L17.6562 8.12514C17.7157 8.04605 17.7555 7.95394 17.7725 7.85642C17.7894 7.7589 17.7829 7.65876 17.7535 7.56424C17.6105 7.10767 17.4273 6.66472 17.2059 6.2406C17.1598 6.15281 17.0935 6.07722 17.0125 6.02008C16.9315 5.96295 16.838 5.9259 16.7399 5.91199L14.9156 5.65139C14.8275 5.55206 14.736 5.45481 14.6408 5.35964C14.5457 5.2645 14.4484 5.1729 14.349 5.08482L14.349 5.08478L14.0881 3.26082C14.0743 3.16281 14.0373 3.06951 13.9804 2.98858C13.9234 2.90766 13.848 2.84144 13.7603 2.79539C13.3364 2.57364 12.8936 2.39002 12.4371 2.24668C12.3425 2.21716 12.2422 2.21057 12.1445 2.22746C12.0468 2.24435 11.9545 2.28423 11.8753 2.34381L10.4011 3.4495C10.2685 3.44152 10.135 3.43751 10.0005 3.43747C9.86593 3.43747 9.73237 3.44148 9.5998 3.4495L9.59975 3.4495Z"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                        </svg>

                      </div>
                      <div class="relative z-10">
                        <p class="text-tagline-1 text-secondary dark:text-accent font-normal">Services</p>
                        <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                          See how others are using StayFlow
                        </p>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="bg-stroke-4 dark:bg-background-7 mx-auto h-[1px] w-[calc(100%-16px)]"></div>
                  </li>
                  <li>
                    <a href="/property-management-software-team.php"
                      class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                      <div
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                      </div>

                      <div
                        class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                          <path
                            d="M19.167 17.5V15.8333C19.1664 15.0948 18.9206 14.3773 18.4681 13.7936C18.0156 13.2099 17.3821 12.793 16.667 12.6083"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path
                            d="M14.1663 17.5V15.8333C14.1663 14.9493 13.8152 14.1014 13.19 13.4763C12.5649 12.8512 11.7171 12.5 10.833 12.5H4.16634C3.28229 12.5 2.43444 12.8512 1.80932 13.4763C1.1842 14.1014 0.833008 14.9493 0.833008 15.8333V17.5"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path
                            d="M13.333 2.60834C14.05 2.79192 14.6855 3.20892 15.1394 3.7936C15.5932 4.37827 15.8395 5.09736 15.8395 5.8375C15.8395 6.57765 15.5932 7.29674 15.1394 7.88141C14.6855 8.46609 14.05 8.88309 13.333 9.06667"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path
                            d="M7.50033 9.16667C9.34127 9.16667 10.8337 7.67428 10.8337 5.83333C10.8337 3.99238 9.34127 2.5 7.50033 2.5C5.65938 2.5 4.16699 3.99238 4.16699 5.83333C4.16699 7.67428 5.65938 9.16667 7.50033 9.16667Z"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                        </svg>

                      </div>
                      <div class="relative z-10">
                        <p class="text-tagline-1 text-secondary dark:text-accent font-normal">Our Team</p>
                        <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                          Dynamic content solutions
                        </p>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="bg-stroke-4 dark:bg-background-7 mx-auto h-[1px] w-[calc(100%-16px)]"></div>
                  </li>
                  <li>
                    <a href="/property-management-software-career.php"
                      class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                      <div
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                      </div>

                      <div
                        class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewbox="0 0 16 15" fill="none">
                          <path
                            d="M14.875 3H1.125C0.779822 3 0.5 3.27982 0.5 3.625V13.625C0.5 13.9702 0.779822 14.25 1.125 14.25H14.875C15.2202 14.25 15.5 13.9702 15.5 13.625V3.625C15.5 3.27982 15.2202 3 14.875 3Z"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path
                            d="M11.124 3V1.75C11.124 1.41848 10.9923 1.10054 10.7579 0.866116C10.5235 0.631696 10.2055 0.5 9.87402 0.5H6.12402C5.7925 0.5 5.47456 0.631696 5.24014 0.866116C5.00572 1.10054 4.87402 1.41848 4.87402 1.75V3"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path
                            d="M15.5001 7.24292C13.2207 8.56171 10.633 9.25416 7.99949 9.25002C5.36647 9.25416 2.77919 8.56195 0.5 7.24358"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path d="M7.06152 6.75H8.93652" class="stroke-secondary dark:stroke-accent"
                            stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>

                      </div>
                      <div class="relative z-10">
                        <p class="text-tagline-1 text-secondary dark:text-accent font-normal">Career</p>
                        <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                          Free marketing templates
                        </p>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="bg-stroke-4 dark:bg-background-7 mx-auto h-[1px] w-[calc(100%-16px)]"></div>
                  </li>
                  <li>
                    <a href="/property-management-software-manifesto.php"
                      class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                      <div
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                      </div>

                      <div
                        class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewbox="0 0 16 16" fill="none">
                          <path d="M7.375 10.5H4.875V8L12.375 0.5L14.875 3L7.375 10.5Z"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path d="M10.5 2.375L13 4.875" class="stroke-secondary dark:stroke-accent"
                            stroke-linecap="round" stroke-linejoin="round"></path>
                          <path
                            d="M14.25 7.375V14.25C14.25 14.4158 14.1842 14.5747 14.0669 14.6919C13.9497 14.8092 13.7908 14.875 13.625 14.875H1.125C0.95924 14.875 0.800269 14.8092 0.683058 14.6919C0.565848 14.5747 0.5 14.4158 0.5 14.25V1.75C0.5 1.58424 0.565848 1.42527 0.683058 1.30806C0.800269 1.19085 0.95924 1.125 1.125 1.125H8"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                        </svg>

                      </div>
                      <div class="relative z-10">
                        <p class="text-tagline-1 text-secondary dark:text-accent font-normal">Our Manifesto</p>
                        <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                          Our core values and principles
                        </p>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="bg-stroke-4 dark:bg-background-7 mx-auto h-[1px] w-[calc(100%-16px)]"></div>
                  </li>
                  <li>
                    <a href="/property-management-software-why-choose-us.php"
                      class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                      <div
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                      </div>

                      <div
                        class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewbox="0 0 18 18" fill="none">
                          <path
                            d="M2.87976 14.3702C2.16069 13.6512 2.63762 12.1415 2.27162 11.257C1.89222 10.34 0.5 9.60195 0.5 8.62497C0.5 7.64801 1.89222 6.91 2.27163 5.99303C2.63763 5.10846 2.1607 3.59882 2.87976 2.87976C3.59882 2.16069 5.10846 2.63762 5.99304 2.27162C6.91002 1.89222 7.64805 0.5 8.62503 0.5C9.60199 0.5 10.34 1.89222 11.257 2.27163C12.1415 2.63763 13.6512 2.1607 14.3702 2.87976C15.0893 3.59882 14.6124 5.10846 14.9784 5.99304C15.3578 6.91002 16.75 7.64805 16.75 8.62503C16.75 9.60199 15.3578 10.34 14.9784 11.257C14.6124 12.1415 15.0893 13.6512 14.3702 14.3702C13.6512 15.0893 12.1415 14.6124 11.257 14.9784C10.34 15.3578 9.60195 16.75 8.62497 16.75C7.64801 16.75 6.91 15.3578 5.99303 14.9784C5.10846 14.6124 3.59882 15.0893 2.87976 14.3702Z"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path
                            d="M8.625 13.625C9.14277 13.625 9.5625 13.2053 9.5625 12.6875C9.5625 12.1697 9.14277 11.75 8.625 11.75C8.10723 11.75 7.6875 12.1697 7.6875 12.6875C7.6875 13.2053 8.10723 13.625 8.625 13.625Z"
                            class="fill-secondary dark:fill-accent"></path>
                          <path
                            d="M8.625 9.87537V9.25037C9.05765 9.25037 9.48058 9.12207 9.84031 8.88171C10.2 8.64134 10.4804 8.2997 10.646 7.89999C10.8116 7.50027 10.8549 7.06044 10.7705 6.63611C10.6861 6.21177 10.4777 5.822 10.1718 5.51607C9.86587 5.21014 9.47609 5.0018 9.05176 4.9174C8.62743 4.83299 8.18759 4.87631 7.78788 5.04188C7.38817 5.20745 7.04653 5.48782 6.80616 5.84756C6.5658 6.20729 6.4375 6.63022 6.4375 7.06287"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                        </svg>

                      </div>
                      <div class="relative z-10">
                        <p class="text-tagline-1 text-secondary dark:text-accent font-normal">Why Choose Us</p>
                        <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                          Our unique selling points and competitive advantages
                        </p>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="bg-stroke-4 dark:bg-background-7 mx-auto h-[1px] w-[calc(100%-16px)]"></div>
                  </li>
                  <li>
                    <a href="/property-management-software-customer.php"
                      class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                      <div
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                      </div>

                      <div
                        class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="17" viewbox="0 0 14 17" fill="none">
                          <path
                            d="M9.25015 1.75H12.375C12.5408 1.75 12.6997 1.81585 12.8169 1.93306C12.9342 2.05027 13 2.20924 13 2.375V15.5C13 15.6658 12.9342 15.8247 12.8169 15.9419C12.6997 16.0592 12.5408 16.125 12.375 16.125H1.125C0.95924 16.125 0.800269 16.0592 0.683058 15.9419C0.565848 15.8247 0.5 15.6658 0.5 15.5V2.375C0.5 2.20924 0.565848 2.05027 0.683058 1.93306C0.800269 1.81585 0.95924 1.75 1.125 1.75H4.24983"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                          <path
                            d="M3.625 4.25V3.625C3.625 2.7962 3.95424 2.00134 4.54029 1.41529C5.12634 0.82924 5.9212 0.5 6.75 0.5C7.5788 0.5 8.37366 0.82924 8.95971 1.41529C9.54576 2.00134 9.875 2.7962 9.875 3.625V4.25H3.625Z"
                            class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                        </svg>

                      </div>
                      <div class="relative z-10">
                        <p class="text-tagline-1 text-secondary dark:text-accent font-normal">Customers</p>
                        <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                          Schedule your demo today
                        </p>
                      </div>
                    </a>
                  </li>
                </ul>
                <div class="flex-1 space-y-[15px]">
                  <ul id="product-dropdown-menu" class="flex-1 space-y-1">
                    <li>
                      <a href="/property-management-software-use-cases.php"
                        class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                        </div>

                        <div
                          class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="18" viewbox="0 0 14 18"
                            fill="none">
                            <path d="M3.625 16.7501H9.875" class="stroke-secondary dark:stroke-accent"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M6.75 14.2501V9.87506" class="stroke-secondary dark:stroke-accent"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M4.25 7.37506L6.75 9.87506L9.25 7.37506"
                              class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                              stroke-linejoin="round"></path>
                            <path
                              d="M2.89873 11.6728C2.15556 11.0924 1.55363 10.351 1.13818 9.50449C0.722727 8.65796 0.504568 7.72828 0.500096 6.78531C0.481375 3.39763 3.2125 0.581526 6.5993 0.501812C7.91169 0.470211 9.20072 0.852645 10.2835 1.59487C11.3664 2.33709 12.188 3.40141 12.6319 4.63687C13.0757 5.87232 13.1194 7.21618 12.7565 8.47781C12.3937 9.73945 11.6428 10.8548 10.6104 11.6657C10.383 11.8418 10.1986 12.0674 10.0712 12.3253C9.94381 12.5832 9.87672 12.8667 9.87502 13.1544L9.87501 13.625C9.87501 13.7908 9.80916 13.9498 9.69195 14.067C9.57474 14.1842 9.41577 14.25 9.25001 14.25H4.24999C4.08423 14.25 3.92526 14.1842 3.80805 14.067C3.69084 13.9498 3.62499 13.7908 3.62499 13.625L3.62498 13.1539C3.62427 12.8681 3.55851 12.5862 3.43268 12.3296C3.30685 12.073 3.12425 11.8484 2.89873 11.6728V11.6728Z"
                              class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                              stroke-linejoin="round"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <p class="text-tagline-1 text-secondary dark:text-accent font-normal">Use Cases</p>
                          <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                            See how others are using StayFlow
                          </p>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="bg-stroke-4 dark:bg-background-7 mx-auto h-[1px] w-[calc(100%-16px)]"></div>
                    </li>
                    <li>
                      <a href="/property-management-software-case-study.php"
                        class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                        </div>

                        <div
                          class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewbox="0 0 16 16"
                            fill="none">
                            <path
                              d="M7.0625 13.625C10.6869 13.625 13.625 10.6869 13.625 7.0625C13.625 3.43813 10.6869 0.5 7.0625 0.5C3.43813 0.5 0.5 3.43813 0.5 7.0625C0.5 10.6869 3.43813 13.625 7.0625 13.625Z"
                              class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                              stroke-linejoin="round"></path>
                            <path d="M11.7021 11.7031L15.4991 15.5" class="stroke-secondary dark:stroke-accent"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <p class="text-tagline-1 text-secondary dark:text-accent font-normal">Case Studies</p>
                          <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                            Real-world examples of our work
                          </p>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="bg-stroke-4 dark:bg-background-7 mx-auto h-[1px] w-[calc(100%-16px)]"></div>
                    </li>
                    <li>
                      <a href="/property-management-software-testimonial.php"
                        class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                        </div>

                        <div
                          class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewbox="0 0 16 15"
                            fill="none">
                            <path
                              d="M4.04304 11.8966L1.52717 14.0115C1.43607 14.0881 1.32499 14.1371 1.207 14.1527C1.08901 14.1683 0.969011 14.1499 0.861119 14.0997C0.753228 14.0494 0.661928 13.9694 0.597956 13.869C0.533984 13.7687 0.5 13.6521 0.5 13.5331V1.125C0.5 0.95924 0.565848 0.800269 0.683058 0.683058C0.800269 0.565848 0.95924 0.5 1.125 0.5H14.875C15.0408 0.5 15.1997 0.565848 15.3169 0.683058C15.4342 0.800269 15.5 0.95924 15.5 1.125V11.125C15.5 11.2908 15.4342 11.4497 15.3169 11.5669C15.1997 11.6842 15.0408 11.75 14.875 11.75H4.44522C4.29808 11.75 4.15567 11.8019 4.04304 11.8966Z"
                              class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                              stroke-linejoin="round"></path>
                            <path
                              d="M8 7.06244C8.51777 7.06244 8.9375 6.64271 8.9375 6.12494C8.9375 5.60717 8.51777 5.18744 8 5.18744C7.48223 5.18744 7.0625 5.60717 7.0625 6.12494C7.0625 6.64271 7.48223 7.06244 8 7.06244Z"
                              class="fill-secondary dark:fill-accent"></path>
                            <path
                              d="M4.25 7.06244C4.76777 7.06244 5.1875 6.64271 5.1875 6.12494C5.1875 5.60717 4.76777 5.18744 4.25 5.18744C3.73223 5.18744 3.3125 5.60717 3.3125 6.12494C3.3125 6.64271 3.73223 7.06244 4.25 7.06244Z"
                              class="fill-secondary dark:fill-accent"></path>
                            <path
                              d="M11.75 7.06244C12.2678 7.06244 12.6875 6.64271 12.6875 6.12494C12.6875 5.60717 12.2678 5.18744 11.75 5.18744C11.2322 5.18744 10.8125 5.60717 10.8125 6.12494C10.8125 6.64271 11.2322 7.06244 11.75 7.06244Z"
                              class="fill-secondary dark:fill-accent"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <p class="text-tagline-1 text-secondary dark:text-accent font-normal">Testimonials</p>
                          <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                            What our customers say about us
                          </p>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="bg-stroke-4 dark:bg-background-7 mx-auto h-[1px] w-[calc(100%-16px)]"></div>
                    </li>
                    <li>
                      <a href="/property-management-software-changelog.php"
                        class="group relative flex items-start gap-3 rounded-2xl p-3 transition-all duration-300">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                        </div>

                        <div
                          class="dark:bg-background-6 shadow-14 border-stroke-1 dark:border-background-7 relative z-10 flex size-11 items-center justify-center rounded-[10px] border bg-white p-3">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewbox="0 0 16 16"
                            fill="none">
                            <path
                              d="M5.15965 12.1317C4.27577 14.7833 0.740234 14.7833 0.740234 14.7833C0.740234 14.7833 0.740234 11.2478 3.39188 10.3639"
                              class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                              stroke-linejoin="round"></path>
                            <path
                              d="M13.1152 5.94448L7.8119 11.2478L4.27637 7.71224L9.57967 2.40894C11.5878 0.400808 13.5959 0.420396 14.4524 0.548173C14.5842 0.56783 14.7063 0.629324 14.8005 0.723595C14.8948 0.817866 14.9563 0.939927 14.976 1.07179C15.1037 1.9282 15.1233 3.93634 13.1152 5.94448Z"
                              class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                              stroke-linejoin="round"></path>
                            <path
                              d="M12.2309 6.82837V11.8728C12.2309 11.9549 12.2148 12.0361 12.1834 12.112C12.152 12.1878 12.1059 12.2567 12.0479 12.3147L9.52118 14.8414C9.44119 14.9214 9.34099 14.9782 9.23126 15.0057C9.12153 15.0332 9.0064 15.0305 8.89813 14.9977C8.78985 14.9649 8.69251 14.9033 8.61648 14.8196C8.54044 14.7358 8.48856 14.633 8.46638 14.5221L7.81152 11.2478"
                              class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                              stroke-linejoin="round"></path>
                            <path
                              d="M8.69612 3.29285H3.6517C3.56962 3.29285 3.48835 3.30901 3.41252 3.34042C3.33669 3.37183 3.26779 3.41787 3.20976 3.4759L0.683058 6.0026C0.603067 6.0826 0.546286 6.1828 0.51877 6.29253C0.491254 6.40225 0.494036 6.51739 0.526818 6.62566C0.559599 6.73393 0.621152 6.83128 0.704913 6.90731C0.788673 6.98335 0.8915 7.03522 1.00243 7.05741L4.2767 7.71226"
                              class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                              stroke-linejoin="round"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <p class="text-tagline-1 text-secondary dark:text-accent font-normal">Changelog</p>
                          <p class="text-tagline-2 text-secondary/60 dark:text-accent/60 font-normal">
                            Latest updates and improvements
                          </p>
                        </div>
                      </a>
                    </li>
                  </ul>
                  <figure class="relative min-h-[220px] w-full max-w-full overflow-hidden rounded-[14px]">
                    <img src="images/stayflow-whats-new.jpg"
                      alt="StayFlow automated WhatsApp rent reminder feature update"
                      class="h-full w-full rounded-[14px] object-cover">
                  </figure>
                </div>
              </div>
            </div>

          </li>
          <li class="nav-item relative cursor-pointer py-2.5" data-menu="platform-mega-menu">
            <a href="#"
              class="hover:border-stroke-2 dark:hover:border-stroke-7 text-tagline-1 text-secondary/60 hover:text-secondary dark:text-accent/60 dark:hover:text-accent flex items-center gap-1 rounded-full border border-transparent px-4 py-2 font-normal transition-all duration-200">
              <span>Platform</span>
              <span class="nav-arrow block origin-center translate-y-px transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                </svg>
              </span>
            </a>
            <div>
              <div
                class="dropdown-menu-bridge fixed left-1/2 -translate-x-1/2 top-full w-full h-3 z-40 lg:w-[970px] pointer-events-none bg-transparent opacity-0">
              </div>
              <div id="platform-mega-menu"
                class="dropdown-menu fixed left-1/2 -translate-x-1/2 top-full mt-2 pointer-events-none transition-all duration-300 opacity-0 lg:w-[970px] w-full bg-white dark:bg-background-6 z-50 rounded-[20px] p-6 border border-stroke-1 dark:border-white/10">
                <div class="grid grid-cols-12 gap-x-8 gap-y-6">
                  <div class="col-span-4">
                    <div>
                      <a href="/property-management-software-features.php"
                        class="rounded-xl p-3 flex items-start gap-2 group transition-all duration-300 relative">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400 group-hover:bg-primary-500/10 border border-transparent group-hover:border-primary-200 dark:group-hover:border-primary-500 rounded-xl">
                        </div>

                        <div
                          class="mt-1 size-9 rounded-lg border border-stroke-1 dark:border-background-7 bg-white dark:bg-background-6 flex items-center justify-center shrink-0 p-2 shadow-14 transition-all duration-300 group-hover:bg-primary-500 relative z-10">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"
                            fill="none">
                            <path
                              d="M10.0003 1.66669L12.5753 6.88335L18.3337 7.72502L14.167 11.7834L15.1503 17.5167L10.0003 14.8084L4.85033 17.5167L5.83366 11.7834L1.66699 7.72502L7.42533 6.88335L10.0003 1.66669Z"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <div class="flex items-center justify-between gap-2">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Features</p>
                            <div
                              class="delay-300 group-hover:opacity-100 opacity-0 transition-all duration-300 -translate-x-2 group-hover:translate-x-0">
                              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewbox="0 0 24 24" fill="none">
                                <path d="M8 5H10V7H8V5Z" class="fill-primary-500"></path>
                                <path d="M11 8H13V10H11V8Z" class="fill-primary-500"></path>
                                <path d="M14 11H16V13H14V11Z" class="fill-primary-500"></path>
                                <path d="M11 14H13V16H11V14Z" class="fill-primary-500"></path>
                                <path d="M8 17H10V19H8V17Z" class="fill-primary-500"></path>
                              </svg>
                            </div>
                          </div>
                          <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                            Make cool SaaS experiences for your users!
                          </p>
                        </div>
                      </a>

                      <a href="/property-management-software-integration.php"
                        class="rounded-xl p-3 flex items-start gap-2 group transition-all duration-300 relative">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400 group-hover:bg-primary-500/10 border border-transparent group-hover:border-primary-200 dark:group-hover:border-primary-500 rounded-xl">
                        </div>

                        <div
                          class="mt-1 size-9 rounded-lg border border-stroke-1 dark:border-background-7 bg-white dark:bg-background-6 flex items-center justify-center shrink-0 p-2 shadow-14 transition-all duration-300 group-hover:bg-primary-500 relative z-10">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"
                            fill="none">
                            <path
                              d="M8.33301 10.8333C8.69088 11.3118 9.14747 11.7077 9.6718 11.9941C10.1961 12.2806 10.7759 12.4509 11.3719 12.4936C11.9678 12.5363 12.566 12.4503 13.1258 12.2415C13.6856 12.0327 14.1939 11.7059 14.6163 11.2833L17.1163 8.78335C17.8753 7.9975 18.2953 6.94499 18.2858 5.85251C18.2763 4.76002 17.8381 3.71497 17.0656 2.94243C16.2931 2.1699 15.248 1.7317 14.1555 1.7222C13.063 1.71271 12.0105 2.13269 11.2247 2.89168L9.79134 4.31668"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                              d="M11.6668 9.16665C11.309 8.68821 10.8524 8.29233 10.328 8.00587C9.80371 7.7194 9.22391 7.54905 8.62796 7.50637C8.03201 7.46369 7.43384 7.54968 6.87405 7.7585C6.31425 7.96732 5.8059 8.29409 5.3835 8.71665L2.8835 11.2167C2.12451 12.0025 1.70453 13.055 1.71402 14.1475C1.72352 15.24 2.16172 16.285 2.93426 17.0576C3.70679 17.8301 4.75184 18.2683 5.84433 18.2778C6.93681 18.2873 7.98932 17.8673 8.77517 17.1083L10.2002 15.6833"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <div class="flex items-center justify-between gap-2">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">
                              Integrations
                            </p>
                            <div
                              class="delay-300 group-hover:opacity-100 opacity-0 transition-all duration-300 -translate-x-2 group-hover:translate-x-0">
                              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewbox="0 0 24 24" fill="none">
                                <path d="M8 5H10V7H8V5Z" class="fill-primary-500"></path>
                                <path d="M11 8H13V10H11V8Z" class="fill-primary-500"></path>
                                <path d="M14 11H16V13H14V11Z" class="fill-primary-500"></path>
                                <path d="M11 14H13V16H11V14Z" class="fill-primary-500"></path>
                                <path d="M8 17H10V19H8V17Z" class="fill-primary-500"></path>
                              </svg>
                            </div>
                          </div>
                          <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                            Increase engagement and growth while reducing churn.
                          </p>
                        </div>
                      </a>

                      <a href="/property-management-software-process.php"
                        class="rounded-xl p-3 flex items-start gap-2 group transition-all duration-300 relative">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400 group-hover:bg-primary-500/10 border border-transparent group-hover:border-primary-200 dark:group-hover:border-primary-500 rounded-xl">
                        </div>

                        <div
                          class="mt-1 size-9 rounded-lg border border-stroke-1 dark:border-background-7 bg-white dark:bg-background-6 flex items-center justify-center shrink-0 p-2 shadow-14 transition-all duration-300 group-hover:bg-primary-500 relative z-10">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"
                            fill="none">
                            <path
                              d="M4.99978 16.875C4.9177 16.875 4.83643 16.8589 4.7606 16.8274C4.68477 16.796 4.61587 16.75 4.55784 16.692C4.4998 16.6339 4.45376 16.565 4.42235 16.4892C4.39095 16.4134 4.37478 16.3321 4.37478 16.25L4.37482 12.9145C4.0414 13.0724 3.67346 13.1434 3.30522 13.121C2.93698 13.0985 2.58041 12.9832 2.26867 12.786C1.95693 12.5887 1.70015 12.3157 1.52222 11.9926C1.34428 11.6694 1.25098 11.3064 1.25098 10.9375C1.25098 10.5686 1.34428 10.2057 1.52222 9.88248C1.70015 9.5593 1.95693 9.28637 2.26867 9.08908C2.58041 8.8918 2.93698 8.77656 3.30522 8.75408C3.67346 8.73161 4.0414 8.80263 4.37482 8.96055L4.37478 5.62503C4.37478 5.54295 4.39095 5.46167 4.42236 5.38584C4.45377 5.31001 4.4998 5.24111 4.55784 5.18308C4.61588 5.12504 4.68478 5.079 4.76061 5.04759C4.83644 5.01618 4.91771 5.00002 4.99979 5.00002L8.6478 5.00006C8.48989 4.66664 8.41886 4.2987 8.44134 3.93046C8.46381 3.56222 8.57905 3.20565 8.77634 2.89391C8.97363 2.58217 9.24656 2.32539 9.56974 2.14746C9.89292 1.96953 10.2558 1.87622 10.6248 1.87622C10.9937 1.87622 11.3566 1.96953 11.6798 2.14746C12.003 2.32539 12.2759 2.58217 12.4732 2.89391C12.6705 3.20565 12.7857 3.56222 12.8082 3.93046C12.8307 4.2987 12.7597 4.66664 12.6017 5.00006L16.2497 5.00002C16.4155 5.00002 16.5745 5.06586 16.6917 5.18307C16.8089 5.30028 16.8747 5.45925 16.8748 5.62501L16.8748 8.96055C16.5414 8.80263 16.1734 8.73161 15.8052 8.75408C15.437 8.77655 15.0804 8.8918 14.7686 9.08908C14.4569 9.28637 14.2001 9.5593 14.0222 9.88248C13.8443 10.2057 13.7509 10.5686 13.7509 10.9375C13.7509 11.3064 13.8443 11.6694 14.0222 11.9926C14.2001 12.3157 14.4569 12.5887 14.7686 12.786C15.0804 12.9832 15.437 13.0985 15.8052 13.121C16.1734 13.1434 16.5414 13.0724 16.8748 12.9145L16.8748 16.25C16.8747 16.4158 16.8089 16.5748 16.6917 16.692C16.5745 16.8092 16.4155 16.875 16.2498 16.875L4.99978 16.875Z"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <div class="flex items-center justify-between gap-2">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Process</p>
                            <div
                              class="delay-300 group-hover:opacity-100 opacity-0 transition-all duration-300 -translate-x-2 group-hover:translate-x-0">
                              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewbox="0 0 24 24" fill="none">
                                <path d="M8 5H10V7H8V5Z" class="fill-primary-500"></path>
                                <path d="M11 8H13V10H11V8Z" class="fill-primary-500"></path>
                                <path d="M14 11H16V13H14V11Z" class="fill-primary-500"></path>
                                <path d="M11 14H13V16H11V14Z" class="fill-primary-500"></path>
                                <path d="M8 17H10V19H8V17Z" class="fill-primary-500"></path>
                              </svg>
                            </div>
                          </div>
                          <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                            Unify data, operations, and insights into a single source of truth.
                          </p>
                        </div>
                      </a>
                      <a href="/property-management-software-press.php"
                        class="rounded-xl p-3 flex items-start gap-2 group transition-all duration-300 relative">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400 group-hover:bg-primary-500/10 border border-transparent group-hover:border-primary-200 dark:group-hover:border-primary-500 rounded-xl">
                        </div>

                        <div
                          class="mt-1 size-9 rounded-lg border border-stroke-1 dark:border-background-7 bg-white dark:bg-background-6 flex items-center justify-center shrink-0 p-2 shadow-14 transition-all duration-300 group-hover:bg-primary-500 relative z-10">
                          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewbox="0 0 20 20" fill="none"
                            class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out">
                            <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z">
                            </path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <div class="flex items-center justify-between gap-2">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Press</p>
                            <div
                              class="delay-300 group-hover:opacity-100 opacity-0 transition-all duration-300 -translate-x-2 group-hover:translate-x-0">
                              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewbox="0 0 24 24" fill="none">
                                <path d="M8 5H10V7H8V5Z" class="fill-primary-500"></path>
                                <path d="M11 8H13V10H11V8Z" class="fill-primary-500"></path>
                                <path d="M14 11H16V13H14V11Z" class="fill-primary-500"></path>
                                <path d="M11 14H13V16H11V14Z" class="fill-primary-500"></path>
                                <path d="M8 17H10V19H8V17Z" class="fill-primary-500"></path>
                              </svg>
                            </div>
                          </div>
                          <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                            See what others are saying about us.
                          </p>
                        </div>
                      </a>
                    </div>
                  </div>
                  <div class="col-span-4">
                    <div>
                      <a href="/property-management-software-analytics.php"
                        class="rounded-xl p-3 flex items-start gap-2 group transition-all duration-300 relative">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400 group-hover:bg-primary-500/10 border border-transparent group-hover:border-primary-200 dark:group-hover:border-primary-500 rounded-xl">
                        </div>

                        <div
                          class="mt-1 size-9 rounded-lg border border-stroke-1 dark:border-background-7 bg-white dark:bg-background-6 flex items-center justify-center shrink-0 p-2 shadow-14 transition-all duration-300 group-hover:bg-primary-500 relative z-10">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"
                            fill="none">
                            <path d="M5 16.6667V11.6667"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M15 16.6666V8.33331"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M10 16.6666V3.33331"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <div class="flex items-center justify-between gap-2">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Analytics</p>
                            <div
                              class="delay-300 group-hover:opacity-100 opacity-0 transition-all duration-300 -translate-x-2 group-hover:translate-x-0">
                              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewbox="0 0 24 24" fill="none">
                                <path d="M8 5H10V7H8V5Z" class="fill-primary-500"></path>
                                <path d="M11 8H13V10H11V8Z" class="fill-primary-500"></path>
                                <path d="M14 11H16V13H14V11Z" class="fill-primary-500"></path>
                                <path d="M11 14H13V16H11V14Z" class="fill-primary-500"></path>
                                <path d="M8 17H10V19H8V17Z" class="fill-primary-500"></path>
                              </svg>
                            </div>
                          </div>
                          <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                            Guide users through seamless setup.
                          </p>
                        </div>
                      </a>

                      <a href="/property-management-software-security.php"
                        class="rounded-xl p-3 flex items-start gap-2 group transition-all duration-300 relative">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400 group-hover:bg-primary-500/10 border border-transparent group-hover:border-primary-200 dark:group-hover:border-primary-500 rounded-xl">
                        </div>

                        <div
                          class="mt-1 size-9 rounded-lg border border-stroke-1 dark:border-background-7 bg-white dark:bg-background-6 flex items-center justify-center shrink-0 p-2 shadow-14 transition-all duration-300 group-hover:bg-primary-500 relative z-10">
                          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewbox="0 0 15 16"
                            fill="none">
                            <path
                              d="M0.5 5.70833V1.125C0.5 0.95924 0.565848 0.800269 0.683058 0.683058C0.800269 0.565848 0.95924 0.5 1.125 0.5H13.625C13.7908 0.5 13.9497 0.565848 14.0669 0.683058C14.1842 0.800269 14.25 0.95924 14.25 1.125V5.70833C14.25 12.272 8.67922 14.4466 7.56689 14.8154C7.44254 14.8582 7.30746 14.8582 7.18311 14.8154C6.07078 14.4466 0.5 12.272 0.5 5.70833Z"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M10.8125 4.875L6.22914 9.25L3.9375 7.0625"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <div class="flex items-center justify-between gap-2">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Security</p>
                            <div
                              class="delay-300 group-hover:opacity-100 opacity-0 transition-all duration-300 -translate-x-2 group-hover:translate-x-0">
                              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewbox="0 0 24 24" fill="none">
                                <path d="M8 5H10V7H8V5Z" class="fill-primary-500"></path>
                                <path d="M11 8H13V10H11V8Z" class="fill-primary-500"></path>
                                <path d="M14 11H16V13H14V11Z" class="fill-primary-500"></path>
                                <path d="M11 14H13V16H11V14Z" class="fill-primary-500"></path>
                                <path d="M8 17H10V19H8V17Z" class="fill-primary-500"></path>
                              </svg>
                            </div>
                          </div>
                          <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                            Automate tasks and boost engagement.
                          </p>
                        </div>
                      </a>

                      <a href="/property-management-software-whitepaper.php"
                        class="rounded-xl p-3 flex items-start gap-2 group transition-all duration-300 relative">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400 group-hover:bg-primary-500/10 border border-transparent group-hover:border-primary-200 dark:group-hover:border-primary-500 rounded-xl">
                        </div>

                        <div
                          class="mt-1 size-9 rounded-lg border border-stroke-1 dark:border-background-7 bg-white dark:bg-background-6 flex items-center justify-center shrink-0 p-2 shadow-14 transition-all duration-300 group-hover:bg-primary-500 relative z-10">
                          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="17" viewbox="0 0 14 17"
                            fill="none">
                            <path d="M4.25 10.5H9.25"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M4.25 8H9.25"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                              d="M9.25015 1.75H12.375C12.5408 1.75 12.6997 1.81585 12.8169 1.93306C12.9342 2.05027 13 2.20924 13 2.375V15.5C13 15.6658 12.9342 15.8247 12.8169 15.9419C12.6997 16.0592 12.5408 16.125 12.375 16.125H1.125C0.95924 16.125 0.800269 16.0592 0.683058 15.9419C0.565848 15.8247 0.5 15.6658 0.5 15.5V2.375C0.5 2.20924 0.565848 2.05027 0.683058 1.93306C0.800269 1.81585 0.95924 1.75 1.125 1.75H4.24983"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                              d="M3.625 4.25V3.625C3.625 2.7962 3.95424 2.00134 4.54029 1.41529C5.12634 0.82924 5.9212 0.5 6.75 0.5C7.5788 0.5 8.37366 0.82924 8.95971 1.41529C9.54576 2.00134 9.875 2.7962 9.875 3.625V4.25H3.625Z"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <div class="flex items-center justify-between gap-2">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Whitepaper</p>
                            <div
                              class="delay-300 group-hover:opacity-100 opacity-0 transition-all duration-300 -translate-x-2 group-hover:translate-x-0">
                              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewbox="0 0 24 24" fill="none">
                                <path d="M8 5H10V7H8V5Z" class="fill-primary-500"></path>
                                <path d="M11 8H13V10H11V8Z" class="fill-primary-500"></path>
                                <path d="M14 11H16V13H14V11Z" class="fill-primary-500"></path>
                                <path d="M11 14H13V16H11V14Z" class="fill-primary-500"></path>
                                <path d="M8 17H10V19H8V17Z" class="fill-primary-500"></path>
                              </svg>
                            </div>
                          </div>
                          <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                            Store and manage data securely.
                          </p>
                        </div>
                      </a>

                      <div class="mt-7">
                        <div class="group">
                          <a href="/property-management-software-features.php"
                            class="inline-flex items-center justify-center rounded-full cursor-pointer gap-1.5 group-hover:bg-primary-500 text-center transition-all duration-500 ease-in-out font-medium text-nowrap lowercase bg-white border border-stroke-1 hover:border-primary-500 text-secondary group-hover:text-white px-5 py-3 text-tagline-1">
                            <span
                              class="inline-block transition-transform duration-300 ease-in-out first-letter:uppercase">
                              Build overview</span>

                            <div class="relative overflow-hidden size-6">
                              <!-- one  -->
                              <span
                                class="group-hover:translate-x-1 -translate-x-6 absolute inset-0 transition-transform duration-300 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24"
                                  fill="none">
                                  <path d="M11 5H13V7H11V5Z" fill="white"></path>
                                  <path d="M5 5H7V7H5V5Z" fill="white"></path>
                                  <path d="M14 8H16V10H14V8Z" fill="white"></path>
                                  <path d="M8 8H10V10H8V8Z" fill="white"></path>
                                  <path d="M17 11H19V13H17V11Z" fill="white"></path>
                                  <path d="M11 11H13V13H11V11Z" fill="white"></path>
                                  <path d="M14 14H16V16H14V14Z" fill="white"></path>
                                  <path d="M8 14H10V16H8V14Z" fill="white"></path>
                                  <path d="M11 17H13V19H11V17Z" fill="white"></path>
                                  <path d="M5 17H7V19H5V17Z" fill="white"></path>
                                </svg>
                              </span>

                              <!-- two  -->
                              <span
                                class="group-hover:translate-x-6 absolute -translate-x-2 transition-transform duration-300 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24"
                                  fill="none">
                                  <path d="M11 5H13V7H11V5Z" fill="#1A1A1C"></path>
                                  <path d="M5 5H7V7H5V5Z" fill="#1A1A1C"></path>
                                  <path d="M14 8H16V10H14V8Z" fill="#1A1A1C"></path>
                                  <path d="M8 8H10V10H8V8Z" fill="#1A1A1C"></path>
                                  <path d="M17 11H19V13H17V11Z" fill="#1A1A1C"></path>
                                  <path d="M11 11H13V13H11V11Z" fill="#1A1A1C"></path>
                                  <path d="M14 14H16V16H14V14Z" fill="#1A1A1C"></path>
                                  <path d="M8 14H10V16H8V14Z" fill="#1A1A1C"></path>
                                  <path d="M11 17H13V19H11V17Z" fill="#1A1A1C"></path>
                                  <path d="M5 17H7V19H5V17Z" fill="#1A1A1C"></path>
                                </svg>
                              </span>
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-span-4">
                    <div>
                      <figure class="overflow-hidden rounded-xl max-w-full w-full mb-2.5">
                        <img src="images/ns-img-427.jpg" alt="What's New" class="w-full h-full object-cover rounded-xl">
                      </figure>
                      <a href="/property-management-software-brandkit.php"
                        class="rounded-xl p-3 flex items-start gap-2 group transition-all duration-300 relative">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400 group-hover:bg-primary-500/10 border border-transparent group-hover:border-primary-200 dark:group-hover:border-primary-500 rounded-xl">
                        </div>

                        <div
                          class="mt-1 size-9 rounded-lg border border-stroke-1 dark:border-background-7 bg-white dark:bg-background-6 flex items-center justify-center shrink-0 p-2 shadow-14 transition-all duration-300 group-hover:bg-primary-500 relative z-10">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewbox="0 0 16 16"
                            fill="none">
                            <path
                              d="M0.543702 11.7331L2.43333 1.01647C2.46212 0.853231 2.55457 0.708109 2.69035 0.613033C2.82613 0.517957 2.99412 0.480714 3.15737 0.509498L7.4659 1.26921C7.62914 1.29799 7.77426 1.39045 7.86934 1.52623C7.96442 1.66201 8.00166 1.83 7.97287 1.99324L6.0736 12.7646C5.94571 13.4898 5.53849 14.136 4.93939 14.5642C4.3403 14.9925 3.59713 15.1687 2.86954 15.055C1.31152 14.8109 0.269853 13.2862 0.543702 11.7331Z"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                              d="M6.9194 7.96721L12.6197 5.89249C12.7754 5.8358 12.9473 5.8433 13.0976 5.91336C13.2478 5.98341 13.364 6.11027 13.4207 6.26603L14.9171 10.3772C14.9451 10.4543 14.9577 10.5362 14.9542 10.6182C14.9506 10.7002 14.9309 10.7807 14.8962 10.8551C14.8615 10.9295 14.8125 10.9963 14.752 11.0518C14.6915 11.1072 14.6206 11.1502 14.5435 11.1783L4.26562 14.9191"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                              d="M14.8662 10.9116V14.4637C14.8662 14.6294 14.8004 14.7884 14.6832 14.9056C14.5659 15.0228 14.407 15.0887 14.2412 15.0887H3.30371"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                              d="M3.30371 13.2137C3.82148 13.2137 4.24121 12.794 4.24121 12.2762C4.24121 11.7585 3.82148 11.3387 3.30371 11.3387C2.78594 11.3387 2.36621 11.7585 2.36621 12.2762C2.36621 12.794 2.78594 13.2137 3.30371 13.2137Z"
                              class="fill-secondary dark:fill-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out">
                            </path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <div class="flex items-center justify-between gap-2">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Brandkit</p>
                            <div
                              class="delay-300 group-hover:opacity-100 opacity-0 transition-all duration-300 -translate-x-2 group-hover:translate-x-0">
                              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewbox="0 0 24 24" fill="none">
                                <path d="M8 5H10V7H8V5Z" class="fill-primary-500"></path>
                                <path d="M11 8H13V10H11V8Z" class="fill-primary-500"></path>
                                <path d="M14 11H16V13H14V11Z" class="fill-primary-500"></path>
                                <path d="M11 14H13V16H11V14Z" class="fill-primary-500"></path>
                                <path d="M8 17H10V19H8V17Z" class="fill-primary-500"></path>
                              </svg>
                            </div>
                          </div>
                          <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                            Extend workflows with one-click app connections.
                          </p>
                        </div>
                      </a>

                      <a href="/property-management-software-download.php"
                        class="rounded-xl p-3 flex items-start gap-2 group transition-all duration-300 relative">
                        <div
                          class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400 group-hover:bg-primary-500/10 border border-transparent group-hover:border-primary-200 dark:group-hover:border-primary-500 rounded-xl">
                        </div>

                        <div
                          class="mt-1 size-9 rounded-lg border border-stroke-1 dark:border-background-7 bg-white dark:bg-background-6 flex items-center justify-center shrink-0 p-2 shadow-14 transition-all duration-300 group-hover:bg-primary-500 relative z-10">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"
                            fill="none">
                            <path
                              d="M17.5 12.5V15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5H4.16667C3.72464 17.5 3.30072 17.3244 2.98816 17.0118C2.67559 16.6993 2.5 16.2754 2.5 15.8333V12.5"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M5.83301 8.33331L9.99967 12.5L14.1663 8.33331"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M10 12.5V2.5"
                              class="stroke-secondary dark:stroke-accent group-hover:stroke-white dark:group-hover:stroke-accent transition-all duration-300 ease-in-out"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                          </svg>

                        </div>
                        <div class="relative z-10">
                          <div class="flex items-center justify-between gap-2">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Download</p>
                            <div
                              class="delay-300 group-hover:opacity-100 opacity-0 transition-all duration-300 -translate-x-2 group-hover:translate-x-0">
                              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewbox="0 0 24 24" fill="none">
                                <path d="M8 5H10V7H8V5Z" class="fill-primary-500"></path>
                                <path d="M11 8H13V10H11V8Z" class="fill-primary-500"></path>
                                <path d="M14 11H16V13H14V11Z" class="fill-primary-500"></path>
                                <path d="M11 14H13V16H11V14Z" class="fill-primary-500"></path>
                                <path d="M8 17H10V19H8V17Z" class="fill-primary-500"></path>
                              </svg>
                            </div>
                          </div>
                          <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                            Scale fast with flexibility and performance combined.
                          </p>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </li>
          <li class="nav-item relative cursor-pointer py-2.5" data-menu="resources-dropdown-menu">
            <a href="#"
              class="hover:border-stroke-2 dark:hover:border-stroke-7 text-tagline-1 text-secondary/60 hover:text-secondary dark:text-accent/60 dark:hover:text-accent flex items-center gap-1 rounded-full border border-transparent px-4 py-2 font-normal transition-all duration-200">
              <span>Resources</span>
              <span class="nav-arrow block origin-center translate-y-px transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                </svg>
              </span>
            </a>
            <div>
              <div
                class="dropdown-menu-bridge absolute left-1/2 -translate-x-1/2 top-full w-full h-3 z-40 min-w-[320px] pointer-events-none bg-transparent opacity-0">
              </div>
              <ul id="resources-dropdown-menu"
                class="dropdown-menu mt-2 w-[320px] rounded-3xl p-3 bg-white dark:bg-background-6 shadow-14 absolute left-1/2 -translate-x-1/2 top-full z-50 pointer-events-none transition-all duration-300 opacity-0 space-y-1 border border-stroke-1 dark:border-white/10">
                <li>
                  <a href="/property-management-software-blog.php"
                    class="p-3 rounded-2xl flex items-start gap-3 transition-all duration-300 relative group">
                    <div
                      class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                    </div>

                    <div
                      class="size-11 shrink-0 p-2 rounded-[10px] bg-white dark:bg-background-6 relative z-10 shadow-14 border border-stroke-1 dark:border-background-7 flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewbox="0 0 16 16" fill="none">
                        <path
                          d="M4.875 14.6161H1.125C0.95924 14.6161 0.800269 14.5503 0.683058 14.4331C0.565848 14.3159 0.5 14.1569 0.5 13.9911V10.5C0.5 10.4179 0.516166 10.3367 0.547575 10.2608C0.578984 10.185 0.625022 10.1161 0.683059 10.0581L10.0581 0.683058C10.1753 0.565848 10.3342 0.5 10.5 0.5C10.6658 0.5 10.8247 0.565848 10.9419 0.683058L14.4331 4.17418C14.5503 4.29139 14.6161 4.45036 14.6161 4.61612C14.6161 4.78188 14.5503 4.94085 14.4331 5.05806L4.875 14.6161Z"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path d="M8 2.74109L12.375 7.11609" class="stroke-secondary dark:stroke-accent"
                          stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>

                    </div>
                    <div class="relative z-10">
                      <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Blog</p>
                      <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                        Latest articles and insights
                      </p>
                    </div>
                  </a>
                </li>
                <li>
                  <div class="h-[1px] bg-stroke-4 dark:bg-background-7 w-[calc(100%-16px)] mx-auto"></div>
                </li>
                <li>
                  <a href="/property-management-software-tutorial.php"
                    class="p-3 rounded-2xl flex items-start gap-3 transition-all duration-300 relative group">
                    <div
                      class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                    </div>

                    <div
                      class="size-11 shrink-0 p-2 rounded-[10px] bg-white dark:bg-background-6 relative z-10 shadow-14 border border-stroke-1 dark:border-background-7 flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="18" viewbox="0 0 14 18" fill="none">
                        <path d="M3.625 16.7501H9.875" class="stroke-secondary dark:stroke-accent"
                          stroke-linecap="round" stroke-linejoin="round"></path>
                        <path
                          d="M2.89873 11.6728C2.15556 11.0924 1.55363 10.351 1.13818 9.50449C0.722728 8.65796 0.504568 7.72828 0.500096 6.78531C0.481376 3.39763 3.2125 0.581526 6.5993 0.501812C7.91169 0.470211 9.20072 0.852645 10.2835 1.59487C11.3664 2.33709 12.188 3.40141 12.6319 4.63687C13.0757 5.87232 13.1194 7.21618 12.7565 8.47781C12.3937 9.73945 11.6428 10.8548 10.6104 11.6657C10.383 11.8418 10.1986 12.0674 10.0712 12.3253C9.94381 12.5832 9.87672 12.8667 9.87502 13.1544L9.87501 13.625C9.87501 13.7908 9.80916 13.9498 9.69195 14.067C9.57474 14.1842 9.41577 14.25 9.25001 14.25H4.24999C4.08423 14.25 3.92526 14.1842 3.80805 14.067C3.69084 13.9498 3.62499 13.7908 3.62499 13.625L3.62498 13.1539C3.62427 12.8681 3.55851 12.5862 3.43268 12.3296C3.30685 12.073 3.12425 11.8484 2.89873 11.6728V11.6728Z"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path
                          d="M7.38477 3.06134C8.14923 3.19128 8.85448 3.55538 9.40313 4.10336C9.95178 4.65133 10.3167 5.35613 10.4476 6.12043"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                      </svg>

                    </div>
                    <div class="relative z-10">
                      <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Tutorial</p>
                      <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                        Step-by-step guides and tips
                      </p>
                    </div>
                  </a>
                </li>
                <li>
                  <div class="h-[1px] bg-stroke-4 dark:bg-background-7 w-[calc(100%-16px)] mx-auto"></div>
                </li>
                <li>
                  <a href="/property-management-software-faq.php"
                    class="p-3 rounded-2xl flex items-start gap-3 transition-all duration-300 relative group">
                    <div
                      class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                    </div>

                    <div
                      class="size-11 shrink-0 p-2 rounded-[10px] bg-white dark:bg-background-6 relative z-10 shadow-14 border border-stroke-1 dark:border-background-7 flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewbox="0 0 18 18" fill="none">
                        <path
                          d="M2.87976 14.3702C2.16069 13.6512 2.63762 12.1415 2.27162 11.257C1.89222 10.34 0.5 9.60195 0.5 8.62497C0.5 7.64801 1.89222 6.91 2.27163 5.99303C2.63763 5.10846 2.1607 3.59882 2.87976 2.87976C3.59882 2.16069 5.10846 2.63762 5.99304 2.27162C6.91002 1.89222 7.64805 0.5 8.62503 0.5C9.60199 0.5 10.34 1.89222 11.257 2.27163C12.1415 2.63763 13.6512 2.1607 14.3702 2.87976C15.0893 3.59882 14.6124 5.10846 14.9784 5.99304C15.3578 6.91002 16.75 7.64805 16.75 8.62503C16.75 9.60199 15.3578 10.34 14.9784 11.257C14.6124 12.1415 15.0893 13.6512 14.3702 14.3702C13.6512 15.0893 12.1415 14.6124 11.257 14.9784C10.34 15.3578 9.60195 16.75 8.62497 16.75C7.64801 16.75 6.91 15.3578 5.99303 14.9784C5.10846 14.6124 3.59882 15.0893 2.87976 14.3702Z"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path
                          d="M8.625 13.625C9.14277 13.625 9.5625 13.2053 9.5625 12.6875C9.5625 12.1697 9.14277 11.75 8.625 11.75C8.10723 11.75 7.6875 12.1697 7.6875 12.6875C7.6875 13.2053 8.10723 13.625 8.625 13.625Z"
                          class="fill-secondary dark:fill-accent"></path>
                        <path
                          d="M8.625 9.87537V9.25037C9.05765 9.25037 9.48058 9.12207 9.84031 8.88171C10.2 8.64134 10.4804 8.2997 10.646 7.89999C10.8116 7.50027 10.8549 7.06044 10.7705 6.63611C10.6861 6.21177 10.4777 5.822 10.1718 5.51607C9.86587 5.21014 9.47609 5.0018 9.05176 4.9174C8.62743 4.83299 8.18759 4.87631 7.78788 5.04188C7.38817 5.20745 7.04653 5.48782 6.80616 5.84756C6.5658 6.20729 6.4375 6.63022 6.4375 7.06287"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                      </svg>

                    </div>
                    <div class="relative z-10">
                      <p class="text-tagline-1 font-normal text-secondary dark:text-accent">FAQ</p>
                      <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                        Common questions answered
                      </p>
                    </div>
                  </a>
                </li>
                <li>
                  <div class="h-[1px] bg-stroke-4 dark:bg-background-7 w-[calc(100%-16px)] mx-auto"></div>
                </li>
                <li>
                  <a href="/property-management-software-glossary.php"
                    class="p-3 rounded-2xl flex items-start gap-3 transition-all duration-300 relative group">
                    <div
                      class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                    </div>

                    <div
                      class="size-11 shrink-0 p-2 rounded-[10px] bg-white dark:bg-background-6 relative z-10 shadow-14 border border-stroke-1 dark:border-background-7 flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewbox="0 0 14 16" fill="none">
                        <path
                          d="M0.5 14.875C0.5 14.3777 0.697544 13.9008 1.04917 13.5492C1.40081 13.1975 1.87772 13 2.375 13H13V0.5H2.375C1.87772 0.5 1.40081 0.697544 1.04917 1.04917C0.697544 1.40081 0.5 1.87772 0.5 2.375V14.875Z"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path d="M0.5 14.875V15.5H11.75" class="stroke-secondary dark:stroke-accent"
                          stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>

                    </div>
                    <div class="relative z-10">
                      <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Glossary</p>
                      <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                        Terms and definitions
                      </p>
                    </div>
                  </a>
                </li>
                <li>
                  <div class="h-[1px] bg-stroke-4 dark:bg-background-7 w-[calc(100%-16px)] mx-auto"></div>
                </li>
                <li>
                  <a href="/property-management-software-support.php"
                    class="p-3 rounded-2xl flex items-start gap-3 transition-all duration-300 relative group">
                    <div
                      class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                    </div>

                    <div
                      class="size-11 shrink-0 p-2 rounded-[10px] bg-white dark:bg-background-6 relative z-10 shadow-14 border border-stroke-1 dark:border-background-7 flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="17" height="18" viewbox="0 0 17 18" fill="none">
                        <path
                          d="M15.6137 8H13.1137C12.7822 8 12.4643 8.1317 12.2299 8.36612C11.9954 8.60054 11.8637 8.91848 11.8637 9.25V12.375C11.8637 12.7065 11.9954 13.0245 12.2299 13.2589C12.4643 13.4933 12.7822 13.625 13.1137 13.625H14.3637C14.6953 13.625 15.0132 13.4933 15.2476 13.2589C15.482 13.0245 15.6137 12.7065 15.6137 12.375V8ZM15.6137 8C15.6137 7.01031 15.4179 6.03039 15.0374 5.11675C14.6569 4.20311 14.0994 3.3738 13.3969 2.67664C12.6944 1.97947 11.8609 1.42823 10.9444 1.0547C10.0279 0.68116 9.04654 0.492712 8.05687 0.500216C7.0672 0.492712 6.08583 0.68116 5.16932 1.0547C4.25282 1.42823 3.41931 1.97947 2.71684 2.67664C2.01437 3.3738 1.45683 4.20311 1.07636 5.11675C0.695883 6.03039 0.5 7.01031 0.5 8V12.375C0.5 12.7065 0.631696 13.0245 0.866116 13.2589C1.10054 13.4933 1.41848 13.625 1.75 13.625H3C3.33152 13.625 3.64946 13.4933 3.88388 13.2589C4.1183 13.0245 4.25 12.7065 4.25 12.375V9.25C4.25 8.91848 4.1183 8.60054 3.88388 8.36612C3.64946 8.1317 3.33152 8 3 8H0.5"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path
                          d="M15.6137 12.375V14.25C15.6137 14.913 15.3503 15.5489 14.8815 16.0178C14.4127 16.4866 13.7768 16.75 13.1137 16.75H8.625"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                      </svg>

                    </div>
                    <div class="relative z-10">
                      <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Support</p>
                      <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                        Get help and assistance
                      </p>
                    </div>
                  </a>
                </li>
                <li>
                  <div class="h-[1px] bg-stroke-4 dark:bg-background-7 w-[calc(100%-16px)] mx-auto"></div>
                </li>
                <li>
                  <a href="/property-management-software-success-stories.php"
                    class="p-3 rounded-2xl flex items-start gap-3 transition-all duration-300 relative group">
                    <div
                      class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                    </div>

                    <div
                      class="size-11 shrink-0 p-2 rounded-[10px] bg-white dark:bg-background-6 relative z-10 shadow-14 border border-stroke-1 dark:border-background-7 flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="19" viewbox="0 0 14 19" fill="none">
                        <path
                          d="M6.75 13C10.2018 13 13 10.2018 13 6.75C13 3.29822 10.2018 0.5 6.75 0.5C3.29822 0.5 0.5 3.29822 0.5 6.75C0.5 10.2018 3.29822 13 6.75 13Z"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path
                          d="M6.75 10.5C8.82107 10.5 10.5 8.82107 10.5 6.75C10.5 4.67893 8.82107 3 6.75 3C4.67893 3 3 4.67893 3 6.75C3 8.82107 4.67893 10.5 6.75 10.5Z"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path d="M10.501 11.7496V18L6.75043 16.125L3.00098 18V11.7502"
                          class="stroke-secondary dark:stroke-accent" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                      </svg>

                    </div>
                    <div class="relative z-10">
                      <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Success Stories</p>
                      <p class="text-tagline-2 font-normal text-secondary/60 dark:text-accent/60">
                        Real customer success stories
                      </p>
                    </div>
                  </a>
                </li>
              </ul>
            </div>

          </li>
          <li class="nav-item relative cursor-pointer py-2.5" data-menu="plan-and-support-mega-menu">
            <a href="#"
              class="hover:border-stroke-2 dark:hover:border-stroke-7 text-tagline-1 text-secondary/60 hover:text-secondary dark:text-accent/60 dark:hover:text-accent flex items-center gap-1 rounded-full border border-transparent px-4 py-2 font-normal transition-all duration-200">
              <span>Plans & Support</span>
              <span class="nav-arrow block origin-center translate-y-px transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                </svg>
              </span>
            </a>
            <div>
              <div
                class="dropdown-menu-bridge fixed left-1/2 -translate-x-1/2 top-full w-full h-3 z-40 lg:w-[972px] pointer-events-none bg-transparent opacity-0">
              </div>
              <div id="plan-and-support-mega-menu"
                class="dropdown-menu fixed left-1/2 -translate-x-1/2 top-full mt-2 pointer-events-none transition-all duration-300 opacity-0 lg:w-[972px] w-full bg-white dark:bg-background-6 border border-stroke-1 dark:border-white/10 z-50 rounded-[20px] p-6">
                <div class="grid grid-cols-12 items-start md:gap-x-6 gap-y-6">
                  <div class="col-span-4">
                    <p class="text-tagline-2 font-medium text-secondary/60 dark:text-accent/60 p-3">Account</p>
                    <ul>
                      <li>
                        <a href="<?php echo SAAS_LOGIN_URL; ?>" class="flex items-center gap-3 p-3 relative group">
                          <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                          </div>

                          <div
                            class="mt-1 size-10 shrink-0 rounded-lg p-1 border border-stroke-1 dark:border-white/10 flex items-center justify-center relative z-10 shadow-14">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewbox="0 0 17 17"
                              fill="none">
                              <path
                                d="M5.27861 8.21139C4.79681 7.00965 4.74472 5.67841 5.13115 4.44269C5.51757 3.20698 6.3188 2.14259 7.39941 1.42943C8.48002 0.716267 9.77372 0.398085 11.0618 0.528665C12.35 0.659244 13.5535 1.23057 14.469 2.14608C15.3845 3.06159 15.9558 4.26513 16.0864 5.55325C16.217 6.84137 15.8988 8.13508 15.1856 9.21568C14.4725 10.2963 13.4081 11.0975 12.1724 11.4839C10.9367 11.8704 9.60542 11.8183 8.40368 11.3365L8.40373 11.3364L7.375 12.3651H5.5V14.2401H3.625V16.1151H0.5V12.9901L5.27875 8.21133L5.27861 8.21139Z"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path opacity="0.5"
                                d="M12.0625 4.86505C12.2351 4.86505 12.375 4.72514 12.375 4.55255C12.375 4.37996 12.2351 4.24005 12.0625 4.24005C11.8899 4.24005 11.75 4.37996 11.75 4.55255C11.75 4.72514 11.8899 4.86505 12.0625 4.86505Z"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path
                                d="M12.0625 5.49005C12.5803 5.49005 13 5.07032 13 4.55255C13 4.03478 12.5803 3.61505 12.0625 3.61505C11.5447 3.61505 11.125 4.03478 11.125 4.55255C11.125 5.07032 11.5447 5.49005 12.0625 5.49005Z"
                                class="fill-secondary dark:fill-accent"></path>
                            </svg>

                          </div>
                          <div class="relative z-10">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Login</p>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="/property-management-software-pricing.php"
                          class="flex items-center gap-3 p-3 relative group">
                          <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                          </div>

                          <div
                            class="mt-1 size-10 shrink-0 rounded-lg p-1 border border-stroke-1 dark:border-white/10 flex items-center justify-center relative z-10 shadow-14">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewbox="0 0 17 16"
                              fill="none">
                              <path
                                d="M8.0791 10.5C10.8405 10.5 13.0791 8.26142 13.0791 5.5C13.0791 2.73858 10.8405 0.5 8.0791 0.5C5.31768 0.5 3.0791 2.73858 3.0791 5.5C3.0791 8.26142 5.31768 10.5 8.0791 10.5Z"
                                class="stroke-secondary dark:stroke-accent" stroke-miterlimit="10"></path>
                              <path
                                d="M0.5 14.8743C1.26841 13.5442 2.37329 12.4398 3.70366 11.672C5.03403 10.9042 6.54303 10.5 8.07907 10.5C9.61512 10.5 11.1241 10.9043 12.4545 11.6721C13.7848 12.44 14.8897 13.5444 15.6581 14.8744"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            </svg>

                          </div>
                          <div class="relative z-10">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">
                              Create Account
                            </p>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="/property-management-software-referral-program.php"
                          class="flex items-center gap-3 p-3 relative group">
                          <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                          </div>

                          <div
                            class="mt-1 size-10 shrink-0 rounded-lg p-1 border border-stroke-1 dark:border-white/10 flex items-center justify-center relative z-10 shadow-14">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewbox="0 0 16 14"
                              fill="none">
                              <path d="M11.75 8L15.5 4.25L11.75 0.5" class="stroke-secondary dark:stroke-accent"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                              <path
                                d="M13 13H1.125C0.95924 13 0.800269 12.9342 0.683058 12.8169C0.565848 12.6997 0.5 12.5408 0.5 12.375V3"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path
                                d="M3.86133 9.87497C4.27758 8.26502 5.21687 6.83895 6.53164 5.82081C7.8464 4.80266 9.46214 4.25013 11.125 4.25H15.5"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            </svg>

                          </div>
                          <div class="relative z-10">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">
                              Referral Program
                            </p>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="/property-management-software-affiliate.php"
                          class="flex items-center gap-3 p-3 relative group">
                          <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                          </div>

                          <div
                            class="mt-1 size-10 shrink-0 rounded-lg p-1 border border-stroke-1 dark:border-white/10 flex items-center justify-center relative z-10 shadow-14">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="14" viewbox="0 0 20 14"
                              fill="none">
                              <path
                                d="M18.4574 5.59968L16.5264 6.56521L14.0264 1.77755L15.9771 0.80219C16.1237 0.728911 16.2931 0.716117 16.449 0.766565C16.6049 0.817013 16.7348 0.926666 16.8106 1.07191L18.7319 4.75137C18.7704 4.82494 18.7937 4.90543 18.8007 4.98814C18.8077 5.07084 18.7981 5.15411 18.7726 5.23308C18.747 5.31205 18.706 5.38514 18.6519 5.44808C18.5978 5.51102 18.5317 5.56256 18.4574 5.59968V5.59968Z"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path
                                d="M2.77656 6.48147L0.845492 5.51594C0.771253 5.47882 0.705152 5.42729 0.651046 5.36434C0.59694 5.3014 0.555912 5.22831 0.530358 5.14934C0.504804 5.07038 0.495236 4.98711 0.502211 4.9044C0.509186 4.82169 0.532565 4.7412 0.570984 4.66763L2.49231 0.988176C2.56815 0.842928 2.698 0.733274 2.8539 0.682826C3.0098 0.632377 3.17927 0.64517 3.32583 0.718449L5.27656 1.69381L2.77656 6.48147Z"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path
                                d="M16.5264 6.56524L15.2764 8.02758L12.4016 10.9024C12.3253 10.9787 12.2305 11.0339 12.1265 11.0627C12.0225 11.0915 11.9128 11.0929 11.8081 11.0668L7.28018 9.93478C7.19525 9.91355 7.11573 9.87472 7.04677 9.82079L2.77637 6.48151"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path
                                d="M15.277 8.02759L11.8395 5.52759L10.8395 6.27759C10.4068 6.60214 9.88044 6.77759 9.33951 6.77759C8.79859 6.77759 8.27225 6.60214 7.83951 6.27759L7.41602 5.95997C7.34438 5.90624 7.28512 5.83775 7.24226 5.75913C7.1994 5.68051 7.17394 5.59359 7.16759 5.50427C7.16124 5.41495 7.17416 5.32531 7.20547 5.24142C7.23678 5.15753 7.28576 5.08134 7.34907 5.01803L10.4065 1.96065C10.4645 1.90261 10.5334 1.85657 10.6092 1.82516C10.685 1.79375 10.7663 1.77759 10.8484 1.77759H14.027"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path
                                d="M5.32129 1.69385L9.33066 0.524979C9.47389 0.483224 9.62732 0.494078 9.76324 0.555579L12.4639 1.77758"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path
                                d="M8.40137 12.7151L6.04672 12.1264C5.95117 12.1025 5.86262 12.0564 5.78829 11.9918L4.02637 10.4603"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            </svg>

                          </div>
                          <div class="relative z-10">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Affiliate</p>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="/property-management-software-affiliate-policy.php"
                          class="flex items-center gap-3 p-3 relative group">
                          <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                          </div>

                          <div
                            class="mt-1 size-10 shrink-0 rounded-lg p-1 border border-stroke-1 dark:border-white/10 flex items-center justify-center relative z-10 shadow-14">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"
                              fill="none">
                              <path
                                d="M11.6663 1.66669H4.99967C4.55765 1.66669 4.13372 1.84228 3.82116 2.15484C3.5086 2.4674 3.33301 2.89133 3.33301 3.33335V16.6667C3.33301 17.1087 3.5086 17.5326 3.82116 17.8452C4.13372 18.1578 4.55765 18.3334 4.99967 18.3334H14.9997C15.4417 18.3334 15.8656 18.1578 16.1782 17.8452C16.4907 17.5326 16.6663 17.1087 16.6663 16.6667V6.66669L11.6663 1.66669Z"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path d="M13.3337 14.1667H6.66699" class="stroke-secondary dark:stroke-accent"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                              <path d="M13.3337 10.8333H6.66699" class="stroke-secondary dark:stroke-accent"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                              <path d="M8.33366 7.5H7.50033H6.66699" class="stroke-secondary dark:stroke-accent"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                              <path d="M11.667 1.66669V6.66669H16.667" class="stroke-secondary dark:stroke-accent"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>

                          </div>
                          <div class="relative z-10">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">
                              Affiliate Policy
                            </p>
                          </div>
                        </a>
                      </li>
                    </ul>
                  </div>
                  <div class="col-span-4">
                    <p class="text-tagline-2 font-medium text-secondary/60 dark:text-accent/60 p-3">Legal</p>
                    <ul>
                      <li>
                        <a href="/property-management-software-terms.php"
                          class="flex items-center gap-3 p-3 relative group">
                          <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                          </div>

                          <div
                            class="mt-1 size-10 shrink-0 rounded-lg p-1 border border-stroke-1 dark:border-white/10 flex items-center justify-center relative z-10 shadow-14">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"
                              fill="none">
                              <path
                                d="M10.833 1.66669H4.99967C4.55765 1.66669 4.13372 1.84228 3.82116 2.15484C3.5086 2.4674 3.33301 2.89133 3.33301 3.33335V16.6667C3.33301 17.1087 3.5086 17.5326 3.82116 17.8452C4.13372 18.1578 4.55765 18.3334 4.99967 18.3334H14.9997C15.4417 18.3334 15.8656 18.1578 16.1782 17.8452C16.4907 17.5326 16.6663 17.1087 16.6663 16.6667V7.50002L10.833 1.66669Z"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path d="M10.833 1.66669V7.50002H16.6663" class="stroke-secondary dark:stroke-accent"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>

                          </div>
                          <div class="relative z-10">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">
                              Terms & Conditions
                            </p>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="/property-management-software-privacy.php"
                          class="flex items-center gap-3 p-3 relative group">
                          <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                          </div>

                          <div
                            class="mt-1 size-10 shrink-0 rounded-lg p-1 border border-stroke-1 dark:border-white/10 flex items-center justify-center relative z-10 shadow-14">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"
                              fill="none">
                              <path
                                d="M9.99967 18.3334C9.99967 18.3334 16.6663 15 16.6663 10V4.16669L9.99967 1.66669L3.33301 4.16669V10C3.33301 15 9.99967 18.3334 9.99967 18.3334Z"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            </svg>

                          </div>
                          <div class="relative z-10">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">
                              Privacy Policy
                            </p>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="/property-management-software-refund.php"
                          class="flex items-center gap-3 p-3 relative group">
                          <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                          </div>

                          <div
                            class="mt-1 size-10 shrink-0 rounded-lg p-1 border border-stroke-1 dark:border-white/10 flex items-center justify-center relative z-10 shadow-14">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"
                              fill="none">
                              <g clip-path="url(#clip0_4483_29796)">
                                <path d="M10 0.833313V19.1666" class="stroke-secondary dark:stroke-accent"
                                  stroke-linecap="round" stroke-linejoin="round"></path>
                                <path
                                  d="M14.1667 4.16669H7.91667C7.14312 4.16669 6.40125 4.47398 5.85427 5.02096C5.30729 5.56794 5 6.30981 5 7.08335C5 7.8569 5.30729 8.59877 5.85427 9.14575C6.40125 9.69273 7.14312 10 7.91667 10H12.0833C12.8569 10 13.5987 10.3073 14.1457 10.8543C14.6927 11.4013 15 12.1431 15 12.9167C15 13.6902 14.6927 14.4321 14.1457 14.9791C13.5987 15.5261 12.8569 15.8334 12.0833 15.8334H5"
                                  class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                  stroke-linejoin="round"></path>
                              </g>
                              <defs>
                                <clippath id="clip0_4483_29796">
                                  <rect width="20" height="20" fill="white"></rect>
                                </clippath>
                              </defs>
                            </svg>

                          </div>
                          <div class="relative z-10">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">
                              Refund Policy
                            </p>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="/property-management-software-gdpr.php"
                          class="flex items-center gap-3 p-3 relative group">
                          <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                          </div>

                          <div
                            class="mt-1 size-10 shrink-0 rounded-lg p-1 border border-stroke-1 dark:border-white/10 flex items-center justify-center relative z-10 shadow-14">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"
                              fill="none">
                              <path
                                d="M16.6663 17.5V15.8333C16.6663 14.9493 16.3152 14.1014 15.69 13.4763C15.0649 12.8512 14.2171 12.5 13.333 12.5H6.66634C5.78229 12.5 4.93444 12.8512 4.30932 13.4763C3.6842 14.1014 3.33301 14.9493 3.33301 15.8333V17.5"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path
                                d="M10.0003 9.16667C11.8413 9.16667 13.3337 7.67428 13.3337 5.83333C13.3337 3.99238 11.8413 2.5 10.0003 2.5C8.15938 2.5 6.66699 3.99238 6.66699 5.83333C6.66699 7.67428 8.15938 9.16667 10.0003 9.16667Z"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            </svg>

                          </div>
                          <div class="relative z-10">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">GDPR</p>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="/property-management-software-legal.php"
                          class="flex items-center gap-3 p-3 relative group">
                          <div
                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-background-3 dark:bg-background-7 opacity-0 group-hover:opacity-100 rounded-[10px] z-0 transition-all duration-400">
                          </div>

                          <div
                            class="mt-1 size-10 shrink-0 rounded-lg p-1 border border-stroke-1 dark:border-white/10 flex items-center justify-center relative z-10 shadow-14">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewbox="0 0 14 16"
                              fill="none">
                              <path
                                d="M12.3756 15.5H1.125C0.95924 15.5 0.800268 15.4342 0.683058 15.3169C0.565848 15.1997 0.5 15.0408 0.5 14.875V1.125C0.5 0.95924 0.565848 0.800269 0.683058 0.683058C0.800268 0.565848 0.95924 0.5 1.125 0.5H8.62561L13.0006 4.875V14.875C13.0006 14.9571 12.9844 15.0383 12.953 15.1142C12.9216 15.19 12.8756 15.2589 12.8176 15.3169C12.7595 15.375 12.6906 15.421 12.6148 15.4524C12.539 15.4838 12.4577 15.5 12.3756 15.5Z"
                                class="stroke-secondary dark:stroke-accent" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                              <path d="M8.62598 0.5V4.875H13.0016" class="stroke-secondary dark:stroke-accent"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                              <path d="M4.25098 8.625H9.25098" class="stroke-secondary dark:stroke-accent"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                              <path d="M4.25098 11.125H9.25098" class="stroke-secondary dark:stroke-accent"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>

                          </div>
                          <div class="relative z-10">
                            <p class="text-tagline-1 font-normal text-secondary dark:text-accent">Legal</p>
                          </div>
                        </a>
                      </li>
                    </ul>
                  </div>
                  <div class="col-span-4">
                    <p class="text-tagline-2 font-medium text-secondary/60 dark:text-accent/60 p-3 mb-2">
                      What's New
                    </p>

                    <a href="/property-management-software-changelog.php" class="block">
                      <article
                        class="p-2 rounded-[30px] border border-stroke-1 dark:border-white/10 w-full space-y-3 group">
                        <figure class="w-full h-full rounded-3xl overflow-hidden">
                          <img src="images/stayflow-changelog-cover.jpg" alt="StayFlow PG software release notes cover"
                            class="w-full h-full object-cover rounded-3xl group-hover:scale-105 transition-transform duration-500 ease-in-out">
                        </figure>
                        <div class="space-y-7 p-2">
                          <p class="text-tagline-2 font-normal text-secondary dark:text-accent">
                            Property Management Software Changelog
                          </p>

                          <a href="#"
                            class="group-hover:bg-primary-500 group bg-secondary transition-all duration-500 ease-in-out ring-[6px] ring-background-12 dark:ring-background-7 rounded-[40px] w-16 relative overflow-hidden flex items-center justify-center space-y-5 h-9.5 px-5 py-2">
                            <figure class="size-6 relative items-center justify-center overflow-hidden">
                              <img src="images/icons/new-arrow-white.svg" alt="StayFlow icon"
                                class="size-full absolute inset-0 -translate-x-6 object-cover group-hover:translate-x-1 transition-transform duration-400 ease-in-out">
                              <img src="images/icons/new-arrow-white.svg" alt="StayFlow icon"
                                class="size-full object-cover group-hover:translate-x-6 transition-transform duration-400 ease-in-out">
                            </figure>
                          </a>
                        </div>
                      </article>
                    </a>
                  </div>
                </div>
              </div>
            </div>

          </li>
          <li class="nav-item relative cursor-pointer py-2.5">
            <a href="/property-management-software-pricing.php"
              class="hover:border-stroke-2 dark:hover:border-stroke-7 text-tagline-1 text-secondary/60 hover:text-secondary dark:text-accent/60 dark:hover:text-accent flex items-center gap-1 rounded-full border border-transparent px-4 py-2 font-normal transition-all duration-200">
              <span>Pricing</span>
            </a>
          </li>
        </ul>
      </nav>

      <div class="hidden items-center justify-center xl:flex gap-3">
        <a href="<?php echo SAAS_LOGIN_URL; ?>"
          class="text-tagline-1 font-medium text-secondary hover:text-primary-500 dark:text-accent dark:hover:text-primary-400 px-3 py-2 transition-colors">
          <span>Log in</span>
        </a>
        <a href="/property-management-software-pricing.php"
          class="btn btn-md btn-secondary dark:btn-accent hover:btn-primary">
          <span>Get started</span>
        </a>
      </div>
      <div class="block xl:hidden">
        <button
          class="nav-hamburger bg-background-4 dark:bg-background-6 flex size-12 cursor-pointer flex-col items-center justify-center gap-[5px] rounded-full">
          <span class="sr-only">Menu</span>
          <span class="bg-stroke-9 dark:bg-stroke-1 block h-0.5 w-6"></span>
          <span class="bg-stroke-9 dark:bg-stroke-1 block h-0.5 w-6"></span>
          <span class="bg-stroke-9 dark:bg-stroke-1 block h-0.5 w-6"></span>
        </button>
      </div>
    </div>
    <!-- Mobile Menu -->

    <aside
      class="sidebar dark:bg-background-8 scroll-bar fixed top-0 right-0 z-[9999] h-screen w-full translate-x-full rounded-l-3xl bg-white transition-all duration-300 sm:w-1/2 xl:hidden">
      <div class="space-y-4 p-5 sm:p-8 lg:p-9">
        <div class="flex items-center justify-between">
          <a href="/" class="flex items-center logo brand-logo" title="StayFlow – Smart PG Management System">
            <span class="sr-only">StayFlow – Smart PG Management System</span>
            <figure class="flex items-center">
              <img src="images/shared/stayflow-logo.png" alt="StayFlow – Smart PG Management System" class="stayflow-brand-logo">
            </figure>
          </a>
          <button
            class="nav-hamburger-close bg-background-4 dark:bg-background-9 relative flex size-10 cursor-pointer flex-col items-center justify-center gap-1.5 rounded-full">
            <span class="sr-only">Close Menu</span>
            <span class="bg-stroke-9/60 dark:bg-stroke-1 absolute block h-0.5 w-4 rotate-45"></span>
            <span class="bg-stroke-9/60 dark:bg-stroke-1 absolute block h-0.5 w-4 -rotate-45"></span>
          </button>
        </div>
        <div class="scroll-bar mt-6 h-[85vh] w-full overflow-x-hidden overflow-y-auto pb-10">
          <p
            class="text-secondary dark:text-accent text-tagline-1 before:bg-stroke-4 dark:before:bg-stroke-6 relative mb-2 block font-normal before:absolute before:top-1/2 before:-right-16 before:h-px before:w-full before:-translate-y-1/2 before:content-['']">
            Menu
          </p>
          <ul class="space-y-2">
            <li class="space-y-2">
              <button class="mobile-menu-toggle flex w-full cursor-pointer items-center justify-between py-2.5"
                data-menu="company">
                <span class="text-secondary/60 dark:text-accent/60 text-tagline-1 block font-normal">Company</span>

                <span class="menu-arrow rotate-90 transition-transform duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                    <path d="M10 12L14 8L10 4" class="stroke-secondary/60 dark:stroke-accent/60" stroke-width="1.5"
                      stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </span>
              </button>
              <ul class="mobile-submenu block" data-submenu="company">
                <li>
                  <a href="/property-management-software-about.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">About
                    Us</a>
                </li>
                <li>
                  <a href="/property-management-software-services.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Services</a>
                </li>
                <li>
                  <a href="/property-management-software-team.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Our
                    Team</a>
                </li>
                <li>
                  <a href="/property-management-software-career.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Career</a>
                </li>
                <li>
                  <a href="/property-management-software-manifesto.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Our
                    Manifesto</a>
                </li>
                <li>
                  <a href="/property-management-software-why-choose-us.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Why
                    Choose Us</a>
                </li>
                <li>
                  <a href="/property-management-software-customer.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Customers</a>
                </li>
                <li>
                  <a href="/property-management-software-use-cases.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Use
                    Cases</a>
                </li>
                <li>
                  <a href="/property-management-software-case-study.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Case
                    Studies</a>
                </li>
                <li>
                  <a href="/property-management-software-testimonial.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Testimonials</a>
                </li>
                <li>
                  <a href="/property-management-software-changelog.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Changelog</a>
                </li>
              </ul>
            </li>
            <li class="space-y-2">
              <button class="mobile-menu-toggle flex w-full cursor-pointer items-center justify-between py-2.5"
                data-menu="platform">
                <span
                  class="text-secondary/60 dark:text-accent/60 text-tagline-1 relative block font-normal">Platform</span>

                <span class="menu-arrow transition-transform duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                    <path d="M10 12L14 8L10 4" class="stroke-secondary/60 dark:stroke-accent/60" stroke-width="1.5"
                      stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </span>
              </button>
              <ul class="mobile-submenu hidden" data-submenu="platform">
                <li>
                  <a href="/property-management-software-features.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Features</a>
                </li>
                <li>
                  <a href="/property-management-software-integration.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Integrations</a>
                </li>
                <li>
                  <a href="/property-management-software-process.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Process</a>
                </li>
                <li>
                  <a href="/property-management-software-analytics.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Analytics</a>
                </li>
                <li>
                  <a href="/property-management-software-security.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Security</a>
                </li>
                <li>
                  <a href="/property-management-software-whitepaper.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Whitepaper</a>
                </li>
                <li>
                  <a href="/property-management-software-features.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Build
                    overview</a>
                </li>
                <li>
                  <a href="/property-management-software-brandkit.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Brandkit</a>
                </li>
                <li>
                  <a href="/property-management-software-download.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Download</a>
                </li>
              </ul>
            </li>
            <li class="space-y-2">
              <button class="mobile-menu-toggle flex w-full cursor-pointer items-center justify-between py-2.5"
                data-menu="resources">
                <span
                  class="text-secondary/60 dark:text-accent/60 text-tagline-1 relative block font-normal">Resources</span>

                <span class="menu-arrow transition-transform duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                    <path d="M10 12L14 8L10 4" class="stroke-secondary/60 dark:stroke-accent/60" stroke-width="1.5"
                      stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </span>
              </button>
              <ul class="mobile-submenu hidden" data-submenu="resources">
                <li>
                  <a href="/property-management-software-blog.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Blog</a>
                </li>
                <li>
                  <a href="/property-management-software-tutorial.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Tutorial</a>
                </li>
                <li>
                  <a href="/property-management-software-faq.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">FAQ</a>
                </li>
                <li>
                  <a href="/property-management-software-glossary.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Glossary</a>
                </li>
                <li>
                  <a href="/property-management-software-support.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Support</a>
                </li>
                <li>
                  <a href="/property-management-software-success-stories.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Success
                    Stories</a>
                </li>
              </ul>
            </li>
            <li class="space-y-2">
              <button class="mobile-menu-toggle flex w-full cursor-pointer items-center justify-between py-2.5"
                data-menu="plans-support">
                <span class="text-secondary/60 dark:text-accent/60 text-tagline-1 relative block font-normal">Plans &
                  Support</span>

                <span class="menu-arrow transition-transform duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20" fill="none">
                    <path d="M10 12L14 8L10 4" class="stroke-secondary/60 dark:stroke-accent/60" stroke-width="1.5"
                      stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </span>
              </button>
              <ul class="mobile-submenu hidden" data-submenu="plans-support">
                <li>
                  <a href="/property-management-software-pricing.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Pricing</a>
                </li>
                <li>
                  <a href="<?php echo SAAS_LOGIN_URL; ?>"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Login</a>
                </li>
                <li>
                  <a href="/property-management-software-pricing.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Create
                    Account</a>
                </li>
                <li>
                  <a href="/property-management-software-referral-program.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Referral
                    Program</a>
                </li>
                <li>
                  <a href="/property-management-software-affiliate.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Affiliate</a>
                </li>
                <li>
                  <a href="/property-management-software-affiliate-policy.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Affiliate
                    Policy</a>
                </li>
                <li>
                  <a href="/property-management-software-terms.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Terms
                    & Conditions</a>
                </li>
                <li>
                  <a href="/property-management-software-privacy.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Privacy
                    Policy</a>
                </li>
                <li>
                  <a href="/property-management-software-refund.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Refund
                    Policy</a>
                </li>
                <li>
                  <a href="/property-management-software-gdpr.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">GDPR</a>
                </li>
                <li>
                  <a href="/property-management-software-legal.php"
                    class="text-tagline-1 text-secondary dark:text-accent ml-4 block py-2.5 text-left font-normal transition-all duration-200">Legal</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </aside>

  </header>