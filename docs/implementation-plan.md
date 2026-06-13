# Implementation Plan

## Source

The Lovable export is a React/TanStack prototype. It contains the visual source of truth:

- Home sections: top bar, header, hero, trust strip, categories, promo banners, products, age rail, brands, mascot area, testimonials, store info, footer.
- Product page UI: gallery, badges, buy box, quantity, wishlist affordance, tabs, related products.
- Brand language: RTL, red/navy/blue/yellow palette, soft surfaces, compact cards, mascot imagery.

The export does not include live WooCommerce behavior. Product data, categories, cart counts, search, and add-to-cart states are mostly static demo code.

## Target Architecture

```text
WordPress + WooCommerce
  themes/kindertoys
    visual templates, WooCommerce layout, assets
  plugins/kindertoys-core
    fields, shortcodes, utility helpers, future business logic
```

## Conversion Order

1. Foundation: theme setup, Woo support, menus, image sizes, CSS tokens.
2. Header and footer: native menus, search, cart count, account links, mobile navigation.
3. Product cards and loops: use WooCommerce product objects.
4. Home page sections: render from selected categories/products and theme/plugin settings.
5. Product page: gallery, buy box, facts, tabs, related products.
6. Archive pages: categories, filters, ordering, pagination.
7. Cart/checkout/account polish.
8. Accessibility, cache, Core Web Vitals, SEO schema review.

## Keep In Theme

- Header/footer.
- Layout, typography, CSS tokens.
- WooCommerce template structure.
- Product card presentation.
- Small UI JavaScript.

## Keep In Plugin

- Custom product meta: age, pieces, players, brand label, badge text.
- Shortcodes/blocks for reusable store sections.
- Admin settings and future integrations.
- Any logic that must persist if the theme changes.
