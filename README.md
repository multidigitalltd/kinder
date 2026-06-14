# KinderToys WordPress

Lean WordPress/WooCommerce implementation for KinderToys, based on the Lovable redesign.

The repository is intentionally split into:

- `themes/kindertoys` - visual layer, WooCommerce templates, frontend assets.
- `plugins/kindertoys-core` - business/UI utilities that should survive a future theme change.
- `docs` - implementation notes, migration map, performance and cache guidance.

## Principles

- WooCommerce is the only required third-party plugin.
- The theme renders native WordPress/WooCommerce HTML, not a React app wrapper.
- Assets are small, dependency-free, and loaded conditionally.
- Business logic belongs in `kindertoys-core`; presentation belongs in the theme.
- The markup is RTL-first, semantic, keyboard accessible, and cache friendly.

## Local install

Copy or symlink:

- `themes/kindertoys` to `wp-content/themes/kindertoys`
- `plugins/kindertoys-core` to `wp-content/plugins/kindertoys-core`

Then activate WooCommerce, the plugin, and the theme.

## Current Status

Implemented:

- Lean WooCommerce theme foundation.
- KinderToys Core plugin for product fields and reusable category sections.
- KinderToys admin settings screen for font, phone, WhatsApp, top bar text, hero text and promo banners.
- Header with search, cart count, account/wishlist links, fallback mega menu and mobile drawer.
- Lovable-inspired home page sections rendered natively in PHP.
- WooCommerce product cards, product page facts/highlights/tabs, archive polish.
- Cart, checkout and account styling without deep template overrides.
- Cache and staging QA documentation.

Still requires staging verification with real WooCommerce data, payment gateways and shipping methods.

## Store Settings

After activating `kindertoys-core`, go to:

`WordPress Admin > KinderToys`

You can manage:

- Font family.
- Body/display font file URLs from the Media Library.
- Top bar text.
- Phone and WhatsApp numbers.
- Search placeholder.
- Home hero text and buttons.
- Promo banner text, links and image URLs.
- Section headings for categories, products, age and brands.
- Hero image URL.
- Top promo marquee items.

## Menu Control

Use `Appearance > Menus` and assign the menu to `Primary menu`.

The hard-coded fallback menu is only a safety net for a fresh install. For pixel-accurate production navigation, every label, order, URL and hierarchy should come from the WordPress menu manager.

### Fonts

Upload font files in `Media > Add New`, preferably `.woff2`.

Then copy each uploaded file URL into:

- `Body regular font file URL`
- `Body bold font file URL`
- `Display regular font file URL`
- `Display bold font file URL`

Use the font name fields to match the uploaded font family name, for example `Ploni` and `PloniYad`.
