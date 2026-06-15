(() => {
  const ajax = window.kindertoysAjax || {};
  const menuToggle = document.querySelector("[data-menu-toggle]");
  const nav = document.querySelector("[data-site-nav]");
  const menuCloseEls = document.querySelectorAll("[data-menu-close]");
  const a11yToggle = document.querySelector("[data-a11y-toggle]");
  const cartDrawer = document.querySelector("[data-cart-drawer]");
  const cartOpeners = document.querySelectorAll("[data-cart-drawer-open]");
  const cartCloseEls = document.querySelectorAll("[data-cart-drawer-close]");
  const searchForm = document.querySelector("[data-live-search]");
  const searchInput = document.querySelector("[data-live-search-input]");
  const searchResults = document.querySelector("[data-live-search-results]");
  let searchTimer = null;
  let lastSearch = "";

  const closeMenu = () => {
    if (!menuToggle || !nav) {
      return;
    }

    nav.classList.remove("is-open");
    document.documentElement.classList.remove("kt-menu-open");
    menuToggle.setAttribute("aria-expanded", "false");
  };

  if (menuToggle && nav) {
    menuToggle.addEventListener("click", () => {
      const isOpen = nav.classList.toggle("is-open");
      document.documentElement.classList.toggle("kt-menu-open", isOpen);
      menuToggle.setAttribute("aria-expanded", String(isOpen));
    });

    menuCloseEls.forEach((button) => {
      button.addEventListener("click", closeMenu);
    });
  }

  if (a11yToggle) {
    a11yToggle.addEventListener("click", () => {
      const isActive = document.documentElement.classList.toggle("kt-a11y-boost");
      a11yToggle.setAttribute("aria-pressed", String(isActive));
    });
  }

  const openCart = () => {
    if (!cartDrawer) {
      return;
    }
    cartDrawer.classList.add("is-open");
    cartDrawer.setAttribute("aria-hidden", "false");
    document.documentElement.classList.add("kt-cart-open");
    cartDrawer.querySelector("[data-cart-drawer-close]")?.focus();
  };

  const closeCart = () => {
    if (!cartDrawer) {
      return;
    }
    cartDrawer.classList.remove("is-open");
    cartDrawer.setAttribute("aria-hidden", "true");
    document.documentElement.classList.remove("kt-cart-open");
  };

  cartOpeners.forEach((opener) => {
    opener.addEventListener("click", (event) => {
      if (!cartDrawer) {
        return;
      }
      event.preventDefault();
      openCart();
    });
  });

  cartCloseEls.forEach((button) => {
    button.addEventListener("click", closeCart);
  });

  const setCartBusy = (isBusy) => {
    cartDrawer?.classList.toggle("is-busy", isBusy);
  };

  const updateCartMeta = (data) => {
    const drawerItems = document.querySelector("[data-cart-drawer-items]");
    drawerItems && data.items && (drawerItems.innerHTML = data.items);
    document.querySelectorAll("[data-cart-count]").forEach((el) => {
      el.textContent = String(data.count ?? "0");
    });
    document.querySelectorAll("[data-cart-total]").forEach((el) => {
      el.innerHTML = data.total || "";
    });
  };

  const applyCartFragments = (fragments) => {
    if (!fragments) {
      return;
    }

    Object.entries(fragments).forEach(([selector, html]) => {
      if (typeof html !== "string") {
        return;
      }

      document.querySelectorAll(selector).forEach((node) => {
        node.outerHTML = html;
      });
    });
  };

  if (window.jQuery) {
    window.jQuery(document.body).on("added_to_cart removed_from_cart wc_fragments_refreshed", (event, fragments) => {
      applyCartFragments(fragments);
    });
  }

  const postCartUpdate = async (cartItemKey, quantity) => {
    if (!ajax.ajaxUrl || !ajax.nonce) {
      return;
    }

    setCartBusy(true);
    const body = new URLSearchParams({
      action: "kindertoys_update_cart_item",
      nonce: ajax.nonce,
      cart_item_key: cartItemKey,
      quantity: String(Math.max(0, quantity)),
    });

    try {
      const response = await fetch(ajax.ajaxUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body,
      });
      const result = await response.json();
      if (result?.success) {
        updateCartMeta(result.data || {});
      }
    } finally {
      setCartBusy(false);
    }
  };

  document.addEventListener("click", (event) => {
    const button = event.target.closest("[data-cart-qty], [data-cart-remove]");
    if (!button) {
      return;
    }

    const item = button.closest("[data-cart-item]");
    const input = item?.querySelector("[data-cart-qty-input]");
    if (!item || !input) {
      return;
    }

    const current = Number.parseInt(input.value || "0", 10);
    const next = button.hasAttribute("data-cart-remove")
      ? 0
      : current + Number.parseInt(button.getAttribute("data-cart-qty") || "0", 10);

    postCartUpdate(item.getAttribute("data-cart-item"), next);
  });

  document.addEventListener("change", (event) => {
    const input = event.target.closest("[data-cart-qty-input]");
    const item = input?.closest("[data-cart-item]");
    if (!input || !item) {
      return;
    }

    postCartUpdate(item.getAttribute("data-cart-item"), Number.parseInt(input.value || "0", 10));
  });

  const hideSearchResults = () => {
    if (!searchInput || !searchResults) {
      return;
    }
    searchInput.setAttribute("aria-expanded", "false");
    searchResults.hidden = true;
    searchResults.innerHTML = "";
  };

  const showSearchResults = (html) => {
    if (!searchInput || !searchResults) {
      return;
    }
    searchResults.innerHTML = html;
    searchResults.hidden = false;
    searchInput.setAttribute("aria-expanded", "true");
  };

  const runSearch = async (term) => {
    if (!ajax.ajaxUrl || !ajax.nonce || term.length < 2 || term === lastSearch) {
      term.length < 2 && hideSearchResults();
      return;
    }

    lastSearch = term;
    showSearchResults(`<div class="kt-search-results__empty">${ajax.i18n?.searching || "..."}</div>`);

    const url = new URL(ajax.ajaxUrl);
    url.searchParams.set("action", "kindertoys_search_products");
    url.searchParams.set("nonce", ajax.nonce);
    url.searchParams.set("term", term);

    try {
      const response = await fetch(url.toString(), { credentials: "same-origin" });
      const result = await response.json();
      showSearchResults(result?.data?.html || `<div class="kt-search-results__empty">${ajax.i18n?.noResults || ""}</div>`);
    } catch (error) {
      showSearchResults(`<div class="kt-search-results__empty">${ajax.i18n?.error || ""}</div>`);
    }
  };

  searchInput?.addEventListener("input", () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(() => runSearch(searchInput.value.trim()), 180);
  });

  searchInput?.addEventListener("focus", () => {
    const term = searchInput.value.trim();
    if (term.length >= 2) {
      runSearch(term);
    }
  });

  document.addEventListener("click", (event) => {
    if (searchForm && !searchForm.contains(event.target)) {
      hideSearchResults();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") {
      return;
    }

    closeMenu();
    closeCart();
    hideSearchResults();
  });
})();
