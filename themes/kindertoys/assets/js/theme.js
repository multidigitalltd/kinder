(() => {
  const ajax = window.kindertoysAjax || {};
  const menuToggle = document.querySelector("[data-menu-toggle]");
  const nav = document.querySelector("[data-site-nav]");
  const menuCloseEls = document.querySelectorAll("[data-menu-close]");
  const a11yToggle = document.querySelector("[data-a11y-toggle]");
  const cartDrawer = document.querySelector("[data-cart-drawer]");
  const cartOpeners = document.querySelectorAll("[data-cart-drawer-open]");
  const cartCloseEls = document.querySelectorAll("[data-cart-drawer-close]");
  const wishlistDrawer = document.querySelector("[data-wishlist-drawer]");
  const wishlistOpeners = document.querySelectorAll("[data-wishlist-open]");
  const wishlistCloseEls = document.querySelectorAll("[data-wishlist-close]");
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

  const a11yKey = "kindertoys_a11y";
  const getA11yPrefs = () => {
    try {
      return JSON.parse(window.localStorage.getItem(a11yKey) || "{}") || {};
    } catch (error) {
      return {};
    }
  };
  const setA11yPrefs = (prefs) => {
    window.localStorage.setItem(a11yKey, JSON.stringify(prefs));
    document.documentElement.classList.toggle("kt-a11y-text", Boolean(prefs.text));
    document.documentElement.classList.toggle("kt-a11y-contrast", Boolean(prefs.contrast));
    document.documentElement.classList.toggle("kt-a11y-links", Boolean(prefs.links));
    document.documentElement.classList.toggle("kt-a11y-motion", Boolean(prefs.motion));
  };
  setA11yPrefs(getA11yPrefs());

  if (a11yToggle) {
    const panel = document.createElement("div");
    panel.className = "kt-a11y-panel";
    panel.hidden = true;
    panel.innerHTML = `
      <strong>נגישות</strong>
      <button type="button" data-a11y-option="text">הגדלת טקסט</button>
      <button type="button" data-a11y-option="contrast">ניגודיות גבוהה</button>
      <button type="button" data-a11y-option="links">הדגשת קישורים</button>
      <button type="button" data-a11y-option="motion">צמצום תנועה</button>
      <button type="button" data-a11y-reset>איפוס</button>
    `;
    a11yToggle.after(panel);
    a11yToggle.setAttribute("aria-expanded", "false");

    a11yToggle.addEventListener("click", () => {
      panel.hidden = !panel.hidden;
      a11yToggle.setAttribute("aria-expanded", String(!panel.hidden));
    });

    panel.addEventListener("click", (event) => {
      const option = event.target.closest("[data-a11y-option]");
      const reset = event.target.closest("[data-a11y-reset]");
      if (!option && !reset) {
        return;
      }
      const prefs = reset ? {} : getA11yPrefs();
      if (option) {
        const key = option.getAttribute("data-a11y-option");
        prefs[key] = !prefs[key];
      }
      setA11yPrefs(prefs);
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

  const wishlistKey = "kindertoys_wishlist";
  const getWishlist = () => {
    try {
      const ids = JSON.parse(window.localStorage.getItem(wishlistKey) || "[]");
      return Array.isArray(ids) ? ids.map(String) : [];
    } catch (error) {
      return [];
    }
  };

  const setWishlist = (ids) => {
    window.localStorage.setItem(wishlistKey, JSON.stringify([...new Set(ids.map(String))]));
    syncWishlistButtons();
  };

  const syncWishlistButtons = () => {
    const ids = new Set(getWishlist());
    document.querySelectorAll("[data-wishlist-toggle]").forEach((button) => {
      const isActive = ids.has(String(button.getAttribute("data-product-id")));
      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-pressed", String(isActive));
    });
  };

  const loadWishlist = async () => {
    const target = document.querySelector("[data-wishlist-items]");
    if (!target || !ajax.ajaxUrl || !ajax.nonce) {
      return;
    }

    const body = new URLSearchParams({ action: "kindertoys_wishlist_products", nonce: ajax.nonce });
    getWishlist().forEach((id) => body.append("ids[]", id));
    const response = await fetch(ajax.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body,
    });
    const result = await response.json();
    target.innerHTML = result?.data?.html || "";
  };

  const openWishlist = () => {
    if (!wishlistDrawer) {
      return;
    }
    loadWishlist();
    wishlistDrawer.classList.add("is-open");
    wishlistDrawer.setAttribute("aria-hidden", "false");
    document.documentElement.classList.add("kt-wishlist-open");
    wishlistDrawer.querySelector("[data-wishlist-close]")?.focus();
  };

  const closeWishlist = () => {
    if (!wishlistDrawer) {
      return;
    }
    wishlistDrawer.classList.remove("is-open");
    wishlistDrawer.setAttribute("aria-hidden", "true");
    document.documentElement.classList.remove("kt-wishlist-open");
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

  wishlistOpeners.forEach((opener) => {
    opener.addEventListener("click", (event) => {
      if (!wishlistDrawer) {
        return;
      }
      event.preventDefault();
      openWishlist();
    });
  });

  wishlistCloseEls.forEach((button) => {
    button.addEventListener("click", closeWishlist);
  });

  document.addEventListener("click", (event) => {
    const toggle = event.target.closest("[data-wishlist-toggle]");
    if (!toggle) {
      return;
    }
    event.preventDefault();
    const id = String(toggle.getAttribute("data-product-id") || "");
    const ids = getWishlist();
    const isRemoving = ids.includes(id);
    setWishlist(isRemoving ? ids.filter((item) => item !== id) : [...ids, id]);
    showToast(isRemoving ? "הוסר מהמועדפים" : "נוסף למועדפים");
  });

  document.addEventListener("click", (event) => {
    const remove = event.target.closest("[data-wishlist-remove]");
    const item = remove?.closest("[data-product-id]");
    if (!remove || !item) {
      return;
    }
    const id = String(item.getAttribute("data-product-id") || "");
    setWishlist(getWishlist().filter((itemId) => itemId !== id));
    showToast("הוסר מהמועדפים");
    loadWishlist();
  });

  document.addEventListener("click", async (event) => {
    const button = event.target.closest("[data-wishlist-add-to-cart]");
    if (!button || !ajax.ajaxUrl || !ajax.nonce) {
      return;
    }

    event.preventDefault();
    button.disabled = true;
    const body = new URLSearchParams({
      action: "kindertoys_add_product_to_cart",
      nonce: ajax.nonce,
      product_id: button.getAttribute("data-product-id") || "",
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
        showToast(result.data?.message || "המוצר נוסף לסל", "success");
      } else {
        showToast(result?.data?.message || ajax.i18n?.error || "לא הצלחנו להוסיף לסל", "error");
      }
    } finally {
      button.disabled = false;
    }
  });

  const setCartBusy = (isBusy) => {
    cartDrawer?.classList.toggle("is-busy", isBusy);
  };

  const showToast = (message, type = "info") => {
    if (!message) {
      return;
    }

    let toast = document.querySelector("[data-kt-toast]");
    if (!toast) {
      toast = document.createElement("div");
      toast.className = "kt-toast";
      toast.setAttribute("role", "status");
      toast.setAttribute("aria-live", "polite");
      toast.setAttribute("data-kt-toast", "");
      document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.dataset.type = type;
    toast.classList.add("is-visible");
    window.clearTimeout(showToast.timer);
    showToast.timer = window.setTimeout(() => toast.classList.remove("is-visible"), 2600);
  };

  const captureWooNotices = () => {
    document.querySelectorAll(".woocommerce-message, .woocommerce-info, .woocommerce-error").forEach((notice) => {
      if (notice.classList.contains("kt-notice-captured")) {
        return;
      }
      const message = notice.textContent?.replace(/\s+/g, " ").trim();
      if (message) {
        showToast(message, notice.classList.contains("woocommerce-error") ? "error" : "success");
      }
      notice.classList.add("kt-notice-captured");
    });
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

  captureWooNotices();

  const refreshCart = async () => {
    if (!ajax.ajaxUrl || !ajax.nonce) {
      return;
    }

    const url = new URL(ajax.ajaxUrl);
    url.searchParams.set("action", "kindertoys_cart_snapshot");
    url.searchParams.set("nonce", ajax.nonce);

    try {
      const response = await fetch(url.toString(), { credentials: "same-origin" });
      const result = await response.json();
      if (result?.success) {
        updateCartMeta(result.data || {});
      }
    } catch (error) {
      return;
    }
  };

  if (window.jQuery) {
    window.jQuery(document.body).on("added_to_cart removed_from_cart wc_fragments_refreshed", (event, fragments) => {
      applyCartFragments(fragments);
      captureWooNotices();
      refreshCart();
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
        showToast(result.data?.message || "הסל עודכן", "success");
      } else {
        showToast(result?.data?.message || ajax.i18n?.error || "לא הצלחנו לעדכן את הסל", "error");
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

  document.addEventListener("change", async (event) => {
    const input = event.target.closest("[data-checkout-bump-toggle]");
    if (!input || !ajax.ajaxUrl || !ajax.nonce) {
      return;
    }

    input.disabled = true;
    const body = new URLSearchParams({
      action: "kindertoys_toggle_checkout_bump",
      nonce: ajax.nonce,
      product_id: input.getAttribute("data-product-id") || "",
      cart_item_key: input.getAttribute("data-cart-item-key") || "",
      enabled: input.checked ? "1" : "0",
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
        input.setAttribute("data-cart-item-key", result.data?.cart_item_key || "");
        showToast(result.data?.message || "ההזמנה עודכנה", "success");
        window.jQuery?.(document.body).trigger("update_checkout");
        refreshCart();
      } else {
        input.checked = !input.checked;
        showToast(result?.data?.message || ajax.i18n?.error || "לא הצלחנו לעדכן את ההזמנה", "error");
      }
    } finally {
      input.disabled = false;
    }
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
    closeWishlist();
    hideSearchResults();
  });

  syncWishlistButtons();
  refreshCart();
})();
