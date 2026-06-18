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

  // Focus management for modal drawers: trap Tab within the open dialog and
  // restore focus to the trigger on close (WCAG 2.4.3 / 2.1.2).
  let trapHandler = null;
  let trapReturnFocus = null;

  const focusableIn = (container) =>
    Array.from(
      container.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
      )
    ).filter((el) => el.offsetWidth > 0 || el.offsetHeight > 0 || el === document.activeElement);

  const trapFocus = (panel) => {
    trapReturnFocus = document.activeElement;
    trapHandler = (event) => {
      if (event.key !== "Tab") {
        return;
      }
      const items = focusableIn(panel);
      if (!items.length) {
        return;
      }
      const first = items[0];
      const last = items[items.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    };
    document.addEventListener("keydown", trapHandler, true);
  };

  const releaseFocus = () => {
    if (trapHandler) {
      document.removeEventListener("keydown", trapHandler, true);
      trapHandler = null;
    }
    if (trapReturnFocus && typeof trapReturnFocus.focus === "function") {
      trapReturnFocus.focus();
    }
    trapReturnFocus = null;
  };

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
      button.addEventListener("click", () => {
        const wasOpen = nav.classList.contains("is-open");
        closeMenu();
        if (wasOpen) {
          menuToggle.focus();
        }
      });
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
    closeWishlist();
    cartDrawer.classList.add("is-open");
    cartDrawer.setAttribute("aria-hidden", "false");
    document.documentElement.classList.add("kt-cart-open");
    trapFocus(cartDrawer.querySelector('[role="dialog"]') || cartDrawer);
    cartDrawer.querySelector("[data-cart-drawer-close]")?.focus();
  };

  const closeCart = () => {
    if (!cartDrawer || !cartDrawer.classList.contains("is-open")) {
      return;
    }
    cartDrawer.classList.remove("is-open");
    cartDrawer.setAttribute("aria-hidden", "true");
    document.documentElement.classList.remove("kt-cart-open");
    releaseFocus();
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
    closeCart();
    loadWishlist();
    wishlistDrawer.classList.add("is-open");
    wishlistDrawer.setAttribute("aria-hidden", "false");
    document.documentElement.classList.add("kt-wishlist-open");
    trapFocus(wishlistDrawer.querySelector('[role="dialog"]') || wishlistDrawer);
    wishlistDrawer.querySelector("[data-wishlist-close]")?.focus();
  };

  const closeWishlist = () => {
    if (!wishlistDrawer || !wishlistDrawer.classList.contains("is-open")) {
      return;
    }
    wishlistDrawer.classList.remove("is-open");
    wishlistDrawer.setAttribute("aria-hidden", "true");
    document.documentElement.classList.remove("kt-wishlist-open");
    releaseFocus();
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
    if (drawerItems && data.items) {
      drawerItems.innerHTML = data.items;
      window.setTimeout(() => {
        if (typeof prepareSaveCartPanel === "function") {
          prepareSaveCartPanel();
        }
      }, 0);
    }
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

  const prepareSaveCartPanel = () => {
    const button = document.querySelector("[data-save-cart]");
    const field = document.querySelector(".kt-save-cart-email");
    const input = field?.querySelector("[data-save-cart-email]");
    if (!button || !field || !input || button.dataset.saveCartPrepared) {
      return;
    }

    button.dataset.saveCartPrepared = "1";
    button.dataset.saveCartReady = "0";
    field.hidden = true;
    input.required = false;

    const close = document.createElement("button");
    close.type = "button";
    close.className = "kt-save-cart-close";
    close.setAttribute("data-save-cart-close", "");
    close.setAttribute("aria-label", "סגירת שמירת סל");
    close.textContent = "סגירה";
    field.after(close);
    close.hidden = true;
  };

  prepareSaveCartPanel();

  document.addEventListener(
    "click",
    (event) => {
      const button = event.target.closest("[data-save-cart]");
      const field = document.querySelector(".kt-save-cart-email");
      const close = document.querySelector("[data-save-cart-close]");
      const input = field?.querySelector("[data-save-cart-email]");
      if (!button || !field || !input) {
        return;
      }

      if (button.dataset.saveCartReady === "1") {
        return;
      }

      event.preventDefault();
      event.stopImmediatePropagation();
      field.hidden = false;
      close && (close.hidden = false);
      input.required = false;
      button.dataset.saveCartReady = "1";
      button.textContent = "יצירת קישור לסל";
      input.focus();
    },
    true
  );

  document.addEventListener("click", (event) => {
    const close = event.target.closest("[data-save-cart-close]");
    const button = document.querySelector("[data-save-cart]");
    const field = document.querySelector(".kt-save-cart-email");
    const resultBox = document.querySelector("[data-save-cart-result]");
    const input = field?.querySelector("[data-save-cart-email]");
    if (!close || !button || !field || !input) {
      return;
    }

    event.preventDefault();
    field.hidden = true;
    close.hidden = true;
    resultBox && (resultBox.hidden = true);
    input.value = "";
    button.dataset.saveCartReady = "0";
    button.textContent = "שמירת הסל להמשך";
  });

  document.addEventListener("click", async (event) => {
    const button = event.target.closest("[data-save-cart]");
    const resultBox = document.querySelector("[data-save-cart-result]");
    const emailInput = document.querySelector("[data-save-cart-email]");
    if (!button || !ajax.ajaxUrl || !ajax.nonce) {
      return;
    }

    event.preventDefault();
    button.disabled = true;
    const body = new URLSearchParams({
      action: "kindertoys_save_cart",
      nonce: ajax.nonce,
    });
    if (emailInput?.value) {
      body.set("email", emailInput.value.trim());
    }

    try {
      const response = await fetch(ajax.ajaxUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body,
      });
      const result = await response.json();
      if (result?.success && result.data?.url) {
        if (resultBox) {
          resultBox.hidden = false;
          resultBox.innerHTML = `<span>${result.data.message || "הסל נשמר"}</span><input readonly value="${result.data.url}"><button type="button" data-copy-saved-cart>העתקה</button>`;
        }
        showToast(result.data.message || "הסל נשמר", "success");
      } else {
        showToast(result?.data?.message || "לא הצלחנו לשמור את הסל", "error");
      }
    } finally {
      button.disabled = false;
    }
  });

  document.addEventListener("change", (event) => {
    const toggle = event.target.closest("[data-save-cart-email-toggle]");
    if (!toggle) {
      return;
    }

    const field = document.querySelector(".kt-save-cart-email");
    const input = field?.querySelector("[data-save-cart-email]");
    if (!field || !input) {
      return;
    }

    field.hidden = !toggle.checked;
    input.required = toggle.checked;
    if (toggle.checked) {
      input.focus();
    } else {
      input.value = "";
    }
  });

  document.addEventListener("click", async (event) => {
    const button = event.target.closest("[data-copy-saved-cart]");
    const input = button?.parentElement?.querySelector("input");
    if (!button || !input) {
      return;
    }

    try {
      await navigator.clipboard.writeText(input.value);
      showToast("הקישור הועתק", "success");
    } catch (error) {
      input.select();
      showToast("אפשר להעתיק את הקישור מהשדה", "info");
    }
  });

  const enhanceSavedCartResult = () => {
    const resultBox = document.querySelector("[data-save-cart-result]");
    const input = resultBox?.querySelector("input");
    if (!resultBox || !input || resultBox.querySelector("[data-share-saved-cart]")) {
      return;
    }

    const actions = document.createElement("div");
    actions.className = "kt-save-cart-result__actions";
    const share = document.createElement("button");
    share.type = "button";
    share.setAttribute("data-share-saved-cart", "");
    share.textContent = "שיתוף";
    actions.appendChild(share);
    resultBox.appendChild(actions);
  };

  const saveCartResult = document.querySelector("[data-save-cart-result]");
  if (saveCartResult && "MutationObserver" in window) {
    new MutationObserver(enhanceSavedCartResult).observe(saveCartResult, { childList: true, subtree: true });
  }

  document.addEventListener("click", async (event) => {
    const button = event.target.closest("[data-share-saved-cart]");
    const input = document.querySelector("[data-save-cart-result] input");
    if (!button || !input) {
      return;
    }

    event.preventDefault();
    if (navigator.share) {
      try {
        await navigator.share({ title: document.title, url: input.value });
        showToast("הקישור נפתח לשיתוף", "success");
        return;
      } catch (error) {
        return;
      }
    }

    try {
      await navigator.clipboard.writeText(input.value);
      showToast("הקישור הועתק לשיתוף", "success");
    } catch (error) {
      input.select();
      showToast("אפשר להעתיק את הקישור מהשדה", "info");
    }
  });

  document.addEventListener("click", (event) => {
    const button = event.target.closest("[data-single-qty]");
    const quantity = button?.closest(".quantity");
    const input = quantity?.querySelector("input.qty");
    if (!button || !input) {
      return;
    }

    event.preventDefault();
    const step = Number.parseFloat(input.getAttribute("step") || "1") || 1;
    const min = Number.parseFloat(input.getAttribute("min") || "0") || 0;
    const max = Number.parseFloat(input.getAttribute("max") || "");
    const current = Number.parseFloat(input.value || "0") || 0;
    let next = current + Number.parseFloat(button.getAttribute("data-single-qty") || "0") * step;
    next = Math.max(min, next);
    if (!Number.isNaN(max)) {
      next = Math.min(max, next);
    }
    input.value = String(next);
    input.dispatchEvent(new Event("change", { bubbles: true }));
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

  document.addEventListener("submit", async (event) => {
    const form = event.target.closest("[data-waitlist-form]");
    if (!form || !ajax.ajaxUrl || !ajax.nonce) {
      return;
    }

    event.preventDefault();
    const button = form.querySelector("button[type='submit']");
    button && (button.disabled = true);
    const body = new URLSearchParams(new FormData(form));
    body.set("action", "kindertoys_waitlist_signup");
    body.set("nonce", ajax.nonce);

    try {
      const response = await fetch(ajax.ajaxUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body,
      });
      const result = await response.json();
      if (result?.success) {
        form.reset();
        showToast(result.data?.message || "נרשמתם לרשימת ההמתנה", "success");
      } else {
        showToast(result?.data?.message || ajax.i18n?.error || "לא הצלחנו לשמור את ההרשמה", "error");
      }
    } finally {
      button && (button.disabled = false);
    }
  });

  const stickyAtc = document.querySelector("[data-sticky-atc]");
  const originalAtc = document.querySelector("form.cart .single_add_to_cart_button");
  if (stickyAtc && originalAtc && "IntersectionObserver" in window) {
    const observer = new IntersectionObserver(
      ([entry]) => {
        const show = !entry.isIntersecting && window.scrollY > 280;
        stickyAtc.classList.toggle("is-visible", show);
        stickyAtc.setAttribute("aria-hidden", String(!show));
      },
      { threshold: 0.08 }
    );
    observer.observe(originalAtc);
  }

  document.addEventListener("click", (event) => {
    const button = event.target.closest("[data-sticky-atc-submit]");
    const original = document.querySelector("form.cart .single_add_to_cart_button");
    if (!button || !original) {
      return;
    }

    event.preventDefault();
    original.click();
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

  // Keyboard navigation for the live-search listbox (WCAG 2.1.1).
  const searchResultLinks = () =>
    searchResults ? Array.from(searchResults.querySelectorAll("a")) : [];

  searchInput?.addEventListener("keydown", (event) => {
    if (event.key !== "ArrowDown" || !searchResults || searchResults.hidden) {
      return;
    }
    const links = searchResultLinks();
    if (links.length) {
      event.preventDefault();
      links[0].focus();
    }
  });

  searchResults?.addEventListener("keydown", (event) => {
    const links = searchResultLinks();
    if (!links.length) {
      return;
    }
    const index = links.indexOf(document.activeElement);
    if (event.key === "ArrowDown") {
      event.preventDefault();
      (links[index + 1] || links[0]).focus();
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      if (index <= 0) {
        searchInput?.focus();
      } else {
        links[index - 1].focus();
      }
    } else if (event.key === "Escape") {
      hideSearchResults();
      searchInput?.focus();
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

    const menuWasOpen = nav && nav.classList.contains("is-open");
    closeMenu();
    closeCart();
    closeWishlist();
    hideSearchResults();
    if (menuWasOpen && menuToggle) {
      menuToggle.focus();
    }
  });

  // Replace the (possibly page-cached / expired) localized nonce with a fresh
  // one from an uncached endpoint, so AJAX keeps working behind LiteSpeed /
  // Cloudflare full-page cache. Runs before the first cart action.
  const refreshNonce = async () => {
    if (!ajax.ajaxUrl) {
      return;
    }
    try {
      const url = new URL(ajax.ajaxUrl);
      url.searchParams.set("action", "kindertoys_refresh_nonce");
      const response = await fetch(url.toString(), { credentials: "same-origin", cache: "no-store" });
      const result = await response.json();
      if (result?.success && result.data?.nonce) {
        ajax.nonce = result.data.nonce;
      }
    } catch (error) {
      // Keep the localized nonce as a best-effort fallback.
    }
  };

  syncWishlistButtons();
  refreshNonce().then(refreshCart);
})();
