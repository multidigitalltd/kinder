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
- Header with search, cart count, account/wishlist links, fallback mega menu and mobile drawer.
- Lovable-inspired home page sections rendered natively in PHP.
- WooCommerce product cards, product page facts/highlights/tabs, archive polish.
- Cart, checkout and account styling without deep template overrides.
- Cache and staging QA documentation.

Still requires staging verification with real WooCommerce data, payment gateways and shipping methods.
