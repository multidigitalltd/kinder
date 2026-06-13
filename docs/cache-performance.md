# Cache And Performance Notes

## Page Cache

The theme avoids user-specific output on cacheable pages. Dynamic cart fragments are isolated to WooCommerce AJAX and can be bypassed by a cache plugin.

Recommended exclusions:

- `/cart/`
- `/checkout/`
- `/my-account/`
- WooCommerce AJAX endpoints.

Recommended cached pages:

- Home page.
- Product archives.
- Category archives.
- Product pages for anonymous users.

## Assets

- CSS is split into `base.css`, `components.css`, and `woocommerce.css`.
- `woocommerce.css` is only enqueued when WooCommerce content is shown.
- `theme.js` is small, deferred, and dependency-free.
- Versioning uses `filemtime()` for cache busting.

## Images

- Use WooCommerce thumbnails for cards.
- Upload real product images in WebP/AVIF where possible.
- Keep mascot and decorative assets compressed.
- Avoid layout shifts with fixed aspect ratios.

## WordPress/WooCommerce

- Disable unused block styles if the store does not use block checkout/cart.
- Keep cart fragments only where cart UI needs live updates.
- Prefer object cache + page cache on production.
- Avoid query-heavy widgets in the global header.
