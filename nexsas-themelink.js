(function () {
  if (globalThis.window === undefined) return;
  if (globalThis.window.__NEXSAS_DEMO_SHOWCASE_LOADED__) return;

  globalThis.window.__NEXSAS_DEMO_SHOWCASE_LOADED__ = true;

  const tech = {
    nextjs: "https://nextsaas-api.vercel.app/api/nextjs?query=homepage",
    html: "https://nextsaas-api.vercel.app/api/html?query=homepage",
  };

  const purchaseByTech = {
    nextjs:
      "https://themeforest.net/checkout/from_item/59610421?license=regular&support=bundle_6month",
    html: "https://themeforest.net/checkout/from_item/59358848?license=regular&support=bundle_6month",
  };

  const htmlPurchaseByRef = {
    ui8: "https://ui8.net/pixels71-4adba0/products/nexsas--saas--ai-startup-tailwind-template",
  };

  const STORAGE_KEY = "activeDemoCard";
  const ROOT_ID = "nexsas-demo-showcase-root";

  const styles = `
    #${ROOT_ID} * {
      box-sizing: border-box;
      padding: 0px;
      margin: 0px;
    }

    #${ROOT_ID} .nds-hidden {
      display: none !important;
    }

    #${ROOT_ID} .nds-trigger {
      writing-mode: sideways-lr;
      text-orientation: mixed;
      position: fixed;
      right: 0;
      bottom: 50%;
      transform: translateY(50%);
      z-index: 999999;
      display: flex;
      align-items: center;
      gap: 16px;
      border: 0;
      border-radius: 12px 0 0 12px;
      background: #DE4A40;
      padding: 16px 4px 4px 4px;
      cursor: pointer;
      color: #F5F5F7;
      font-weight: 500;
      box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    #${ROOT_ID} .nds-trigger-text {
      font-size: 16px;
      line-height: 150%;
      color: #F5F5F7;
      font-weight: 500;
      letter-spacing: 0.02em;
    }

    #${ROOT_ID} .nds-modal {
      position: fixed;
      inset: 0;
      z-index: 999999;
      height: 100vh;
      width: 100%;
      transform-origin: center center;
      backface-visibility: hidden;
      will-change: transform, opacity, filter;
      transition:
        transform 0.9s cubic-bezier(0.23, 1, 0.32, 1),
        opacity 0.9s cubic-bezier(0.23, 1, 0.32, 1),
        filter 0.9s cubic-bezier(0.23, 1, 0.32, 1);
    }

    #${ROOT_ID} .nds-modal.nds-closed {
      transform: translateX(100%) scale(0.65) rotateY(20deg);
      opacity: 0;
      filter: blur(22px);
      pointer-events: none;
    }

    #${ROOT_ID} .nds-modal.nds-open {
      transform: translateX(0) scale(1) rotateY(0deg);
      opacity: 1;
      filter: blur(0);
      pointer-events: auto;
    }

    #${ROOT_ID} .nds-overlay {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 100%;
      overflow-y: auto;
      background: #F1F4F6;
      padding: 56px 16px 64px;
      touch-action: pan-y;
      -webkit-overflow-scrolling: touch;
      overscroll-behavior: contain;
    }

    #${ROOT_ID} .nds-inner {
      max-width: 1560px;
      margin: 0 auto;
    }

    #${ROOT_ID} .nds-heading {
      margin-bottom: 48px;
      text-align: center;
      color: #1c1c1c;
      font-family: Inter, Arial, sans-serif;
      font-size: 48px;
      font-weight: 500;
      line-height: 1.2;
    }

    #${ROOT_ID} .nds-close {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000000;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 6px solid #fff;
      border-radius: 999px;
      background: #23262D;
      padding: 12px;
      cursor: pointer;
      box-shadow: 0 1px 2px rgba(0,0,0,0.15);
    }

    #${ROOT_ID} .nds-list {
      display: grid;
      grid-template-columns: repeat(12, minmax(0, 1fr));
      gap: 20px;
    }

    #${ROOT_ID} .nds-item {
      grid-column: span 12 / span 12;
    }

    @media (min-width: 768px) {
      #${ROOT_ID} .nds-item {
        grid-column: span 6 / span 6;
      }

      #${ROOT_ID} .nds-close {
        top: 40px;
        right: 40px;
        border-width: 8px;
        padding: 16px;
      }
    }

    @media (min-width: 1280px) {
      #${ROOT_ID} .nds-item {
        grid-column: span 4 / span 4;
      }

      #${ROOT_ID} .nds-list {
        gap: 12px;
      }
    }

    #${ROOT_ID} .nds-card {
      display: block;
      max-width: 500px;
      margin: 0 auto;
      border: 1px solid rgba(255,255,255,0.14);
      border-radius: 36px;
      padding: 8px;
      text-decoration: none;
      transition: all 0.3s ease-in-out;
    }

    #${ROOT_ID} .nds-card:hover,
    #${ROOT_ID} .nds-card.nds-active {
      border-color: #8B5CF6;
      box-shadow: 0 0 0 1px #8B5CF6 inset;
    }

    #${ROOT_ID} .nds-card-inner {
      background: #fff;
      border-radius: 28px;
      padding: 8px;
      box-shadow: 0 1px 4px rgba(16,24,40,0.10);
      transition: all 0.4s ease-in-out;
    }

    #${ROOT_ID} .nds-card:hover .nds-card-inner,
    #${ROOT_ID} .nds-card.nds-active .nds-card-inner {
      box-shadow: 0 8px 6px rgba(16,24,40,0.16);
    }

    #${ROOT_ID} .nds-figure {
      max-height: 351px;
      overflow: hidden;
      border-radius: 20px;
      background: #f3f4f6;
    }

    #${ROOT_ID} .nds-image {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    #${ROOT_ID} .nds-card-title {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 16px 12px;
      color: rgba(15,17,21,0.82);
      text-align: center;
      font-family: Inter, Arial, sans-serif;
      font-size: 18px;
      font-weight: 500;
      line-height: 1.5;
    }

    #${ROOT_ID} .nds-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 999px;
      background: #C6F56F;
      color: rgba(15,17,21,0.82);
      padding: 5px 16px;
      font-size: 12px;
      font-weight: 600;
      line-height: 1;
      white-space: nowrap;
    }

    #${ROOT_ID} .nds-error {
      grid-column: span 12 / span 12;
      padding: 32px 16px;
      text-align: center;
      color: #cbd5e1;
      font-family: Inter, Arial, sans-serif;
    }

    #${ROOT_ID} .nds-purchase {
      position: fixed;
      right: 50px;
      bottom: 50px;
      z-index: 999999;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border-radius: 999px;
      background: #87E64B;
      padding: 8px 8px 8px 20px;
      text-decoration: none;
      transition:
        background-color 0.3s ease,
        box-shadow 0.3s ease,
        transform 0.3s ease;
    }

    #${ROOT_ID} .nds-purchase-label {
      color: #050816;
      font-size: 14px;
      font-weight: 400;
      line-height: 150%;
      white-space: nowrap;
      transition: color 0.3s ease;
    }

    #${ROOT_ID} .nds-purchase-icon {
      display: inline-flex;
      width: 24px;
      height: 24px;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    #${ROOT_ID} .nds-purchase-icon img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    #${ROOT_ID} .nds-purchase-ui8 {
      position: fixed;
      right: 50px;
      bottom: 50px;
      z-index: 999999;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 48px;
      border-radius: 999px;
      background: #1a1a1c;
      color: #f5f5f7;
      padding: 12px 20px;
      text-decoration: none;
      font-family: Inter, Arial, sans-serif;
      font-size: 14px;
      font-weight: 500;
      line-height: 1;
      white-space: nowrap;
      box-shadow: 0 8px 30px rgba(0,0,0,0.18);
      transition:
        transform 0.3s ease,
        box-shadow 0.3s ease,
        background-color 0.3s ease;
    }

    #${ROOT_ID} .nds-purchase-ui8:hover {
      transform: translateY(-1px);
      box-shadow: 0 12px 32px rgba(0,0,0,0.22);
      background: #111113;
    }
  `;

  const markup = `
    <button id="nds-open" class="nds-trigger" aria-label="Open demo showcase">
      <span>
        <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
          <path d="M10 44C4.47715 44 0 39.5228 0 34L0 0L44 0L44 44L10 44Z" fill="#1A1A1C" fill-opacity="0.1" />
          <path d="M16.1728 24.1932L14 21.9995L16.1728 19.8059L18.3456 21.9995L16.1728 24.1932Z" fill="#F5F5F7" />
          <path d="M23.2157 12.6936L25.3886 10.5L27.5614 12.6936L25.3886 14.8873L23.2157 12.6936Z" fill="#F5F5F7" />
          <path d="M19.2456 27.2955L17.0728 25.1018L19.2456 22.9082L21.4184 25.1018L19.2456 27.2955Z" fill="#F5F5F7" />
          <path d="M20.1429 15.7959L22.3157 13.6023L24.4885 15.7959L22.3157 17.9895L20.1429 15.7959Z" fill="#F5F5F7" />
          <path d="M22.3184 30.3977L20.1456 28.2041L22.3184 26.0105L24.4912 28.2041L22.3184 30.3977Z" fill="#F5F5F7" />
          <path d="M17.0701 18.8982L19.2429 16.7045L21.4157 18.8982L19.2429 21.0918L17.0701 18.8982Z" fill="#F5F5F7" />
          <path d="M25.3912 33.5L23.2184 31.3064L25.3912 29.1127L27.564 31.3064L25.3912 33.5Z" fill="#F5F5F7" />
          <path d="M20.1429 22.0005L22.3157 19.8068L24.4885 22.0005L22.3157 24.1941L20.1429 22.0005Z" fill="#F5F5F7" />
          <path d="M25.6544 22.0005L27.8272 19.8068L30 22.0005L27.8272 24.1941L25.6544 22.0005Z" fill="#F5F5F7" />
        </svg>
      </span>
      <span class="nds-trigger-text">
        <span class="nds-count">43</span>+ Pre built demos
      </span>
    </button>

    <a
      href="#"
      target="_blank"
      rel="noreferrer"
      class="nds-purchase"
    >
      <span class="nds-purchase-label">Purchase</span>
      <figure class="nds-purchase-icon">
        <img
          src="https://demo-data.pixels71.com/images/themeforest-logo.svg"
          alt="themeforest logo"
        />
      </figure>
    </a>

    <a
      href="#"
      target="_blank"
      rel="noreferrer"
      class="nds-purchase-ui8 nds-hidden"
    >
      <span>Purchase Now</span>
    </a>

    <div id="nds-modal" class="nds-modal nds-closed" aria-hidden="true" data-lenis-prevent="true">
      <button id="nds-close" class="nds-close" aria-label="Close demo showcase">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 6 6 18" stroke="white"></path>
          <path d="m6 6 12 12" stroke="white"></path>
        </svg>
      </button>

      <div class="nds-overlay">
        <div class="nds-inner">
          <div class="nds-heading">
            <span class="nds-count">43</span>+ Pre-built websites
          </div>

          <div id="nds-list" class="nds-list"></div>
        </div>
      </div>
    </div>
  `;

  function escapeHtml(value) {
    return String(value ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function withUi8Ref(href) {
    const value = String(href ?? "").trim();
    if (!value || value === "#") return value || "#";

    const pageParams = new URLSearchParams(globalThis.location.search);
    if (pageParams.get("ref") !== "ui8") return value;

    try {
      const isAbsoluteHttpUrl = /^https?:\/\//i.test(value);
      if (!isAbsoluteHttpUrl) return value;

      const url = new URL(value);
      url.searchParams.set("ref", "ui8");
      return url.toString();
    } catch {
      return value;
    }
  }

  function buildCard(item, index, activeHref) {
    const href = withUi8Ref(item.url || item.href || "#");
    const title = escapeHtml(item.title || "Untitled Demo");
    const image = escapeHtml(item.image || "");
    const isActive = activeHref === href ? " nds-active" : "";
    const badge = item.newRelease ? `<span class="nds-badge">New</span>` : "";

    return `
      <div class="nds-item">
        <a target="_blank" href="${escapeHtml(href)}" data-card-index="${index}" class="nds-card${isActive}">
          <div class="nds-card-inner">
            <figure class="nds-figure">
              <img src="${image}" alt="${title}" class="nds-image" />
            </figure>
            <h2 class="nds-card-title">
              ${title}
              ${badge}
            </h2>
          </div>
        </a>
      </div>
    `;
  }

  function lockBodyScroll() {
    document.body.dataset.ndsOverflow = document.body.style.overflow || "";
    document.body.style.overflow = "hidden";
  }

  function unlockBodyScroll() {
    document.body.style.overflow = document.body.dataset.ndsOverflow || "";
    delete document.body.dataset.ndsOverflow;
  }

  function openModal(modal) {
    modal.classList.remove("nds-closed");
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        modal.classList.add("nds-open");
        modal.setAttribute("aria-hidden", "false");
      });
    });
    lockBodyScroll();
  }

  function closeModal(modal) {
    modal.classList.remove("nds-open");
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        modal.classList.add("nds-closed");
        modal.setAttribute("aria-hidden", "true");
      });
    });
    unlockBodyScroll();
  }

  function setCounts(root, count) {
    root.querySelectorAll(".nds-count").forEach((el) => {
      el.textContent = String(count);
    });
  }

  function getScriptParams() {
    let script = document.currentScript;
    if (!script) {
      const scripts = document.querySelectorAll(
        'script[src*="nexsas-themelink.js"]',
      );
      script = scripts[scripts.length - 1];
    }

    if (!script) return {};

    const url = new URL(script.src, globalThis.location.origin);
    const params = new URLSearchParams(url.search);

    return Object.fromEntries(params.entries());
  }

  function getPageParams() {
    const params = new URLSearchParams(globalThis.location.search);
    return Object.fromEntries(params.entries());
  }

  function getActiveHref(items, params) {
    const byHref = (it) => it && withUi8Ref(it.url || it.href || "#");

    const savedHref = localStorage.getItem(STORAGE_KEY);
    const validSavedHref =
      savedHref && items.some((item) => byHref(item) === savedHref)
        ? savedHref
        : null;

    const activeItemId =
      params?.activeItemId == null ? "" : String(params.activeItemId);
    if (activeItemId) {
      const activeItem =
        items.find((item) => String(item.id ?? "") === activeItemId) ||
        items.find((item) => String(item.activeItemId ?? "") === activeItemId);

      const href = activeItem ? byHref(activeItem) : null;
      if (href) {
        localStorage.setItem(STORAGE_KEY, href);
        return href;
      }
    }

    const fallback = validSavedHref || byHref(items[0]) || null;
    if (fallback) localStorage.setItem(STORAGE_KEY, fallback);
    return fallback;
  }

  function renderCards(root, items, activeHref) {
    const list = root.querySelector("#nds-list");
    if (!list) return;

    list.innerHTML = items
      .map((item, index) => buildCard(item, index, activeHref))
      .join("");

    list.querySelectorAll(".nds-card").forEach((card) => {
      card.addEventListener("click", () => {
        const href = card.getAttribute("href");
        if (!href) return;

        localStorage.setItem(STORAGE_KEY, href);

        list.querySelectorAll(".nds-card").forEach((c) => {
          c.classList.remove("nds-active");
        });

        card.classList.add("nds-active");
      });
    });
  }

  function normalizeItems(data) {
    const arr =
      (Array.isArray(data) && data) ||
      (Array.isArray(data?.items) && data.items) ||
      (Array.isArray(data?.data) && data.data) ||
      (Array.isArray(data?.result) && data.result) ||
      [];

    return arr.map((item) => ({
      id: item?.id ?? item?.demoId ?? item?.activeItemId ?? null,
      title: item?.title,
      image: item?.image,
      newRelease: !!item?.newRelease,
      href: item?.url || item?.href || "#",
    }));
  }

  async function fetchShowcaseItems(apiUrl) {
    const response = await fetch(apiUrl);

    if (!response.ok) {
      throw new Error("Failed to fetch showcase data");
    }

    const data = await response.json();
    return normalizeItems(data);
  }

  function mountWidget() {
    const root = document.createElement("div");
    root.id = ROOT_ID;

    const style = document.createElement("style");
    style.textContent = styles;

    root.innerHTML = markup;
    root.prepend(style);

    document.body.appendChild(root);
    return root;
  }

  async function init() {
    const params = getScriptParams();
    const pageParams = getPageParams();
    console.log(params, pageParams);
    const techKey = (params.tech || "nextjs").toString().toLowerCase();
    const apiUrl = tech[techKey] || tech.nextjs;
    const pageRef = (pageParams.ref || "").toString().toLowerCase();
    const isUi8Purchase = pageRef === "ui8";
    const purchaseHref = isUi8Purchase
      ? htmlPurchaseByRef.ui8
      : purchaseByTech[techKey] || purchaseByTech.nextjs;

    try {
      const items = await fetchShowcaseItems(apiUrl);
      if (!items.length) return;

      const root = mountWidget();
      const modal = root.querySelector("#nds-modal");
      const openBtn = root.querySelector("#nds-open");
      const closeBtn = root.querySelector("#nds-close");
      const purchaseLink = root.querySelector(".nds-purchase");
      const purchaseUi8Link = root.querySelector(".nds-purchase-ui8");

      if (!modal || !openBtn || !closeBtn) return;

      if (isUi8Purchase) {
        if (purchaseLink) {
          purchaseLink.classList.add("nds-hidden");
        }

        if (purchaseUi8Link && purchaseHref) {
          purchaseUi8Link.setAttribute("href", purchaseHref);
          purchaseUi8Link.classList.remove("nds-hidden");
        }
      } else if (purchaseLink && purchaseHref) {
        purchaseLink.setAttribute("href", purchaseHref);
      }

      openBtn.addEventListener("click", () => openModal(modal));
      closeBtn.addEventListener("click", () => closeModal(modal));

      const activeHref = getActiveHref(items, params);
      setCounts(root, items.length);
      renderCards(root, items, activeHref);
    } catch {}
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init, { once: true });
  } else {
    init();
  }
})();
