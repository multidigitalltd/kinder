(() => {
  const menuToggle = document.querySelector("[data-menu-toggle]");
  const nav = document.querySelector("[data-site-nav]");
  const menuCloseEls = document.querySelectorAll("[data-menu-close]");
  const a11yToggle = document.querySelector("[data-a11y-toggle]");

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

    document.addEventListener("keydown", (event) => {
      if (event.key !== "Escape" || !nav.classList.contains("is-open")) {
        return;
      }

      closeMenu();
      menuToggle.focus();
    });
  }

  if (a11yToggle) {
    a11yToggle.addEventListener("click", () => {
      const isActive = document.documentElement.classList.toggle("kt-a11y-boost");
      a11yToggle.setAttribute("aria-pressed", String(isActive));
    });
  }
})();
