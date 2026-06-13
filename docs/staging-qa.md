# Staging QA Checklist

Run this on a staging copy of `kindertoys.co.il` with WooCommerce active and real products.

## Install

1. Upload and activate `kindertoys-core`.
2. Upload and activate `kindertoys`.
3. Set a static front page if needed.
4. Assign menus:
   - Primary menu.
   - Footer menu.
5. Set the custom logo if the default bundled logo should be replaced.

## Content

For 10-20 representative products, fill:

- Brand label.
- Badge.
- Recommended age.
- Pieces / units.
- Players.
- Play/build time.
- Product highlights, one per line.
- In the box, one item per line.

## Visual QA

Check desktop, tablet, and mobile:

- Header layout, sticky behavior, search, cart count.
- Mega menu hover/focus and mobile drawer.
- Home hero and mascot framing.
- Category grid with real category images.
- Product cards with long Hebrew product names.
- Product page gallery, buy box, facts, highlights, tabs, related products.
- Cart table and totals.
- Checkout fields, payment gateway blocks, shipping methods.
- My account navigation.

## Functional QA

- Product search returns product results.
- Add to cart from product card.
- Add to cart from product page.
- Quantity changes in cart.
- Coupon apply/remove.
- Shipping calculation.
- Checkout with each active payment method.
- Order confirmation page.
- Login, logout, password reset.

## Accessibility QA

- Keyboard navigation through header, menu, product cards, cart and checkout.
- Visible focus states.
- Escape closes mobile menu.
- Form labels are announced.
- Product images have meaningful alt text where needed.
- Color contrast on red/navy/blue surfaces.

## Performance QA

- Home page cached for anonymous visitors.
- Product and category pages cached for anonymous visitors.
- Cart, checkout, account and order endpoints excluded from page cache.
- Images served in modern formats where possible.
- No render-blocking third-party scripts beyond required payment/shipping tools.
- Test Core Web Vitals on mobile after cache warmup.
