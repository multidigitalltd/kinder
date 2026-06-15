# Header, Menu, Cart and Search Management

## Main menu

Manage the top blue menu from **WordPress Admin > Appearance > Menus**.

1. Create or edit the main menu.
2. Assign it to the **Primary menu** location.
3. Add WooCommerce categories, custom links, pages, or product category links.
4. Drag items under a parent item to create dropdown/sub-category menus.

## Menu icons

For top-level menu items, enable **Screen Options > CSS Classes** inside the Menus screen.

Add one of these CSS classes to a top-level item:

- `kt-icon-grid`
- `kt-icon-cube`
- `kt-icon-school`
- `kt-icon-palette`
- `kt-icon-bear`
- `kt-icon-globe`
- `kt-icon-spark`

If no class is set, the theme assigns icons by menu order.

## Fonts

Upload font files in **Media Library** and paste their URLs in **Admin > KinderToys**.

Supported fields:

- Body: 300, 400, 500, 600, 700, 900
- Display/headings: 400, 600, 800, 900

Prefer `.woff2` files for speed.

## Cart drawer

The header cart opens a lightweight WooCommerce cart drawer. It supports:

- Quantity increase/decrease
- Product removal
- Live cart count and total
- Links to cart and checkout

The drawer uses AJAX and is cache-friendly.

## Live search

The search field uses lightweight AJAX product search with up to 6 results and a link to the full search results page.
