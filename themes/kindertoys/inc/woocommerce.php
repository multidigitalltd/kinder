<?php
/**
 * WooCommerce integration.
 *
 * @package KinderToys
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', 'kindertoys_woocommerce_hooks', 20);
function kindertoys_woocommerce_hooks(): void
{
    if (! class_exists('WooCommerce')) {
        return;
    }

    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

    add_action('woocommerce_before_main_content', 'kindertoys_woo_wrapper_open', 10);
    add_action('woocommerce_before_main_content', 'kindertoys_product_breadcrumb', 11);
    add_action('woocommerce_after_main_content', 'kindertoys_woo_wrapper_close', 10);
    add_action('woocommerce_before_single_product', 'kindertoys_product_breadcrumb', 1);
    add_action('woocommerce_before_shop_loop', 'kindertoys_archive_filters', 18);
    add_action('woocommerce_after_shop_loop', 'kindertoys_category_bottom_description', 30);
    add_action('woocommerce_before_quantity_input_field', 'kindertoys_single_quantity_minus');
    add_action('woocommerce_after_quantity_input_field', 'kindertoys_single_quantity_plus');

    remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    add_action('woocommerce_before_shop_loop_item_title', 'kindertoys_product_card_media', 10);

    remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
    add_action('woocommerce_shop_loop_item_title', 'kindertoys_product_card_title', 10);

    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    add_action('woocommerce_after_shop_loop_item', 'kindertoys_product_card_actions', 10);

    add_action('woocommerce_single_product_summary', 'kindertoys_single_product_brand', 4);
    add_action('woocommerce_single_product_summary', 'kindertoys_single_product_facts', 25);
    add_action('woocommerce_single_product_summary', 'kindertoys_single_product_highlights', 26);
    add_action('woocommerce_single_product_summary', 'kindertoys_single_out_of_stock_panel', 31);
    add_action('woocommerce_single_product_summary', 'kindertoys_single_wishlist_button', 32);
    add_action('woocommerce_single_product_summary', 'kindertoys_single_product_trust', 35);

    add_filter('woocommerce_product_tabs', 'kindertoys_product_tabs');
    add_filter('woocommerce_add_to_cart_fragments', 'kindertoys_cart_fragments');
    add_filter('woocommerce_structured_data_product', 'kindertoys_product_schema', 20, 2);
    add_filter('woocommerce_get_stock_html', 'kindertoys_stock_html', 20, 2);
    add_filter('posts_clauses', 'kindertoys_outofstock_last_clauses', 20, 2);
    add_action('woocommerce_before_cart_totals', 'kindertoys_cart_free_shipping_progress');
    add_action('woocommerce_review_order_before_payment', 'kindertoys_checkout_bump');
    add_action('woocommerce_before_calculate_totals', 'kindertoys_apply_checkout_bump_discount', 99);
    add_filter('woocommerce_add_cart_item_data', 'kindertoys_checkout_bump_cart_item_data', 10, 2);
    add_filter('woocommerce_get_cart_item_from_session', 'kindertoys_restore_checkout_bump_cart_item', 10, 2);
    add_action('woocommerce_checkout_create_order_line_item', 'kindertoys_checkout_bump_order_item_meta', 10, 4);

    add_action('wp_footer', 'kindertoys_cart_drawer', 20);
    add_action('wp_footer', 'kindertoys_wishlist_drawer', 21);
    add_action('wp_footer', 'kindertoys_sticky_add_to_cart', 22);

    add_action('wp_ajax_kindertoys_update_cart_item', 'kindertoys_ajax_update_cart_item');
    add_action('wp_ajax_nopriv_kindertoys_update_cart_item', 'kindertoys_ajax_update_cart_item');
    add_action('wp_ajax_kindertoys_cart_snapshot', 'kindertoys_ajax_cart_snapshot');
    add_action('wp_ajax_nopriv_kindertoys_cart_snapshot', 'kindertoys_ajax_cart_snapshot');
    add_action('wp_ajax_kindertoys_search_products', 'kindertoys_ajax_search_products');
    add_action('wp_ajax_nopriv_kindertoys_search_products', 'kindertoys_ajax_search_products');
    add_action('wp_ajax_kindertoys_wishlist_products', 'kindertoys_ajax_wishlist_products');
    add_action('wp_ajax_nopriv_kindertoys_wishlist_products', 'kindertoys_ajax_wishlist_products');
    add_action('wp_ajax_kindertoys_add_product_to_cart', 'kindertoys_ajax_add_product_to_cart');
    add_action('wp_ajax_nopriv_kindertoys_add_product_to_cart', 'kindertoys_ajax_add_product_to_cart');
    add_action('wp_ajax_kindertoys_toggle_checkout_bump', 'kindertoys_ajax_toggle_checkout_bump');
    add_action('wp_ajax_nopriv_kindertoys_toggle_checkout_bump', 'kindertoys_ajax_toggle_checkout_bump');
    add_action('wp_ajax_kindertoys_save_cart', 'kindertoys_ajax_save_cart');
    add_action('wp_ajax_nopriv_kindertoys_save_cart', 'kindertoys_ajax_save_cart');
    add_action('template_redirect', 'kindertoys_restore_saved_cart');
}

function kindertoys_woo_wrapper_open(): void
{
    echo '<main id="primary" class="site-main kt-container kt-woo-main">';
}

function kindertoys_woo_wrapper_close(): void
{
    echo '</main>';
}

function kindertoys_product_breadcrumb(): void
{
    static $rendered = false;

    if ($rendered) {
        return;
    }

    if (! is_product()) {
        return;
    }

    $rendered = true;
    $items = [
        [
            'url' => home_url('/'),
            'label' => __('ראשי', 'kindertoys'),
        ],
    ];

    $terms = get_the_terms(get_the_ID(), 'product_cat');
    if (is_array($terms) && ! empty($terms)) {
        usort($terms, static fn (WP_Term $a, WP_Term $b): int => $a->parent <=> $b->parent);
        $term = end($terms);
        if ($term instanceof WP_Term) {
            $items[] = [
                'url' => get_term_link($term),
                'label' => $term->name,
            ];
        }
    }

    $items[] = [
        'url' => '',
        'label' => get_the_title(),
    ];

    echo '<nav class="woocommerce-breadcrumb kt-breadcrumb" aria-label="' . esc_attr__('פירורי לחם', 'kindertoys') . '">';
    foreach ($items as $index => $item) {
        if ($index > 0) {
            echo '<span class="kt-breadcrumb__sep" aria-hidden="true">/</span>';
        }
        if ('' !== $item['url'] && ! is_wp_error($item['url'])) {
            echo '<a href="' . esc_url((string) $item['url']) . '">' . esc_html((string) $item['label']) . '</a>';
        } else {
            echo '<span aria-current="page">' . esc_html((string) $item['label']) . '</span>';
        }
    }
    echo '</nav>';
}

function kindertoys_category_bottom_description(): void
{
    if (! is_product_category()) {
        return;
    }

    $term = get_queried_object();
    if (! $term instanceof WP_Term || '' === trim((string) $term->description)) {
        return;
    }

    echo '<section class="kt-category-description" aria-label="' . esc_attr__('תיאור הקטגוריה', 'kindertoys') . '">';
    echo '<h2>' . esc_html(sprintf(__('עוד על %s', 'kindertoys'), $term->name)) . '</h2>';
    echo '<div class="kt-category-description__content">' . wp_kses_post(wpautop($term->description)) . '</div>';
    echo '</section>';
}

function kindertoys_product_schema(array $markup, WC_Product $product): array
{
    $sku = $product->get_sku();
    if ('' !== $sku) {
        $markup['sku'] = $sku;
        $markup['mpn'] = $markup['mpn'] ?? $sku;
    }

    if (function_exists('kindertoys_core_get_product_meta')) {
        $brand = kindertoys_core_get_product_meta($product, 'brand_label');
        if ('' !== $brand) {
            $markup['brand'] = [
                '@type' => 'Brand',
                'name' => $brand,
            ];
        }
    }

    if (empty($markup['image'])) {
        $image_id = $product->get_image_id();
        $image = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
        if ($image) {
            $markup['image'] = [$image];
        }
    }

    if (isset($markup['offers']) && is_array($markup['offers'])) {
        $threshold = max(0, (float) kindertoys_setting('free_shipping_threshold', '299'));
        $country = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', (string) kindertoys_setting('schema_shipping_country', 'IL')) ?: 'IL', 0, 2));
        $return_days = max(0, absint(kindertoys_setting('schema_return_days', '14')));
        $markup['offers']['hasMerchantReturnPolicy'] = [
            '@type' => 'MerchantReturnPolicy',
            'applicableCountry' => $country,
            'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
            'merchantReturnDays' => $return_days,
            'returnMethod' => 'https://schema.org/ReturnByMail',
            'returnFees' => 'https://schema.org/ReturnFeesCustomerResponsibility',
        ];

        $markup['offers']['shippingDetails'] = [
            '@type' => 'OfferShippingDetails',
            'shippingDestination' => [
                '@type' => 'DefinedRegion',
                'addressCountry' => $country,
            ],
            'shippingRate' => [
                '@type' => 'MonetaryAmount',
                'value' => 0,
                'currency' => get_woocommerce_currency(),
            ],
        ];

        if ($threshold > 0) {
            $markup['offers']['shippingDetails']['freeShippingThreshold'] = [
                '@type' => 'MonetaryAmount',
                'value' => $threshold,
                'currency' => get_woocommerce_currency(),
            ];
        }
    }

    return $markup;
}

function kindertoys_archive_filters(): void
{
    if (! (function_exists('is_shop') && (is_shop() || is_product_taxonomy()))) {
        return;
    }

    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => 0,
        'number' => 40,
    ]);
    $current_category = isset($_GET['product_cat']) && ! is_array($_GET['product_cat']) ? sanitize_title(wp_unslash((string) $_GET['product_cat'])) : '';
    $min_price = isset($_GET['min_price']) && ! is_array($_GET['min_price']) ? wc_format_decimal(wp_unslash((string) $_GET['min_price'])) : '';
    $max_price = isset($_GET['max_price']) && ! is_array($_GET['max_price']) ? wc_format_decimal(wp_unslash((string) $_GET['max_price'])) : '';
    $stock = isset($_GET['stock']) && 'instock' === sanitize_key((string) wp_unslash($_GET['stock']));
    $sale = isset($_GET['sale']) && '1' === (string) wp_unslash($_GET['sale']);
    ?>
    <section class="kt-archive-filters" aria-label="<?php esc_attr_e('סינון מוצרים', 'kindertoys'); ?>">
        <form method="get" action="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
            <div class="kt-filter-field">
                <label for="kt-filter-category"><?php esc_html_e('קטגוריה', 'kindertoys'); ?></label>
                <select id="kt-filter-category" name="product_cat">
                    <option value=""><?php esc_html_e('כל הקטגוריות', 'kindertoys'); ?></option>
                    <?php if (! is_wp_error($categories)) : ?>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo esc_attr($category->slug); ?>" <?php selected($current_category, $category->slug); ?>><?php echo esc_html($category->name); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="kt-filter-field kt-filter-field--price">
                <label><?php esc_html_e('מחיר', 'kindertoys'); ?></label>
                <span>
                    <input type="number" min="0" step="1" name="min_price" value="<?php echo esc_attr((string) $min_price); ?>" placeholder="<?php esc_attr_e('מ-', 'kindertoys'); ?>">
                    <input type="number" min="0" step="1" name="max_price" value="<?php echo esc_attr((string) $max_price); ?>" placeholder="<?php esc_attr_e('עד', 'kindertoys'); ?>">
                </span>
            </div>
            <label class="kt-filter-check">
                <input type="checkbox" name="stock" value="instock" <?php checked($stock); ?>>
                <span><?php esc_html_e('רק במלאי', 'kindertoys'); ?></span>
            </label>
            <label class="kt-filter-check">
                <input type="checkbox" name="sale" value="1" <?php checked($sale); ?>>
                <span><?php esc_html_e('מבצעים', 'kindertoys'); ?></span>
            </label>
            <button class="kt-button" type="submit"><?php esc_html_e('סינון', 'kindertoys'); ?></button>
            <a class="kt-button kt-button--light" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('איפוס', 'kindertoys'); ?></a>
            <?php
            foreach ($_GET as $key => $value) {
                if (in_array($key, ['product_cat', 'min_price', 'max_price', 'stock', 'sale', 'paged'], true) || is_array($value)) {
                    continue;
                }
                echo '<input type="hidden" name="' . esc_attr((string) $key) . '" value="' . esc_attr(wp_unslash((string) $value)) . '">';
            }
            ?>
        </form>
    </section>
    <?php
}

function kindertoys_product_card_media(): void
{
    global $product;

    if (! $product instanceof WC_Product) {
        return;
    }

    echo '<div class="kt-product-card__media">';

    $custom_badge = kindertoys_product_badge_text($product);

    if (! $product->is_in_stock()) {
        echo '<span class="kt-badge kt-badge--stock">' . esc_html__('אזל מהמלאי', 'kindertoys') . '</span>';
    } elseif ('' !== $custom_badge) {
        echo '<span class="kt-badge">' . esc_html($custom_badge) . '</span>';
    }

    echo '<button class="kt-product-card__wish" type="button" data-wishlist-toggle data-product-id="' . esc_attr((string) $product->get_id()) . '" aria-pressed="false" aria-label="' . esc_attr__('הוספה למועדפים', 'kindertoys') . '">' . kindertoys_svg_icon('heart') . '</button>';
    echo '<a class="kt-product-card__media-link" href="' . esc_url($product->get_permalink()) . '" aria-label="' . esc_attr($product->get_name()) . '">' . woocommerce_get_product_thumbnail('kindertoys-card') . '</a>';
    echo '</div>';
}

function kindertoys_product_badge_text(WC_Product $product): string
{
    $product_badge = function_exists('kindertoys_core_get_product_meta') ? kindertoys_core_get_product_meta($product, 'badge') : '';
    if ('' !== $product_badge) {
        return $product_badge;
    }

    $rules = preg_split('/\r\n|\r|\n/', (string) kindertoys_setting('product_badge_category_rules', '')) ?: [];
    foreach ($rules as $rule) {
        [$slug, $label] = array_pad(array_map('trim', explode('|', $rule, 2)), 2, '');
        if ('' !== $slug && '' !== $label && has_term($slug, 'product_cat', $product->get_id())) {
            return $label;
        }
    }

    if ($product->is_on_sale()) {
        return (string) kindertoys_setting('product_badge_sale_text', '');
    }

    return '';
}

function kindertoys_product_card_title(): void
{
    global $product;

    if ($product instanceof WC_Product && function_exists('kindertoys_core_get_product_meta')) {
        $brand = kindertoys_core_get_product_meta($product, 'brand_label');
        if ('' !== $brand) {
            echo '<div class="kt-product-card__brand">' . esc_html($brand) . '</div>';
        }
    }

    $url = $product instanceof WC_Product ? $product->get_permalink() : get_permalink();
    echo '<h2 class="woocommerce-loop-product__title kt-product-card__title"><a href="' . esc_url($url) . '">' . esc_html(get_the_title()) . '</a></h2>';
}

function kindertoys_product_card_actions(): void
{
    global $product;

    if (! $product instanceof WC_Product) {
        return;
    }

    $url = $product->add_to_cart_url();
    $label = $product->add_to_cart_description();
    $classes = implode(' ', array_filter([
        'button',
        'kt-product-card__add',
        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
        $product->supports('ajax_add_to_cart') ? 'ajax_add_to_cart' : '',
    ]));
    $attributes = wc_implode_html_attributes([
        'href' => esc_url($url),
        'data-quantity' => '1',
        'data-product_id' => (string) $product->get_id(),
        'data-product_sku' => $product->get_sku(),
        'aria-label' => $label,
        'rel' => 'nofollow',
        'class' => $classes,
    ]);

    echo '<div class="kt-product-card__actions">';
    echo '<a ' . $attributes . '><span class="screen-reader-text">' . esc_html($product->add_to_cart_text()) . '</span>' . kindertoys_svg_icon('plus') . '</a>';
    echo '</div>';
}

function kindertoys_single_quantity_minus(): void
{
    if (! is_product()) {
        return;
    }

    echo '<button class="kt-single-qty__button" type="button" data-single-qty="-1" aria-label="' . esc_attr__('הפחת כמות', 'kindertoys') . '">-</button>';
}

function kindertoys_single_quantity_plus(): void
{
    if (! is_product()) {
        return;
    }

    echo '<button class="kt-single-qty__button" type="button" data-single-qty="1" aria-label="' . esc_attr__('הוסף כמות', 'kindertoys') . '">+</button>';
}

function kindertoys_cart_drawer_products(): void
{
    echo '<div class="kt-cart-drawer__scroll"><ul class="kt-cart-drawer__list">';
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'] ?? null;
        if (! $product instanceof WC_Product || ! $product->exists()) {
            continue;
        }

        $quantity = (int) $cart_item['quantity'];
        echo '<li class="kt-cart-drawer__item" data-cart-item="' . esc_attr($cart_item_key) . '">';
        echo '<a class="kt-cart-drawer__thumb" href="' . esc_url($product->get_permalink($cart_item)) . '">' . $product->get_image('woocommerce_thumbnail') . '</a>';
        echo '<div class="kt-cart-drawer__body">';
        echo '<a class="kt-cart-drawer__name" href="' . esc_url($product->get_permalink($cart_item)) . '">' . esc_html($product->get_name()) . '</a>';
        echo '<span class="kt-cart-drawer__price">' . wp_kses_post(WC()->cart->get_product_price($product)) . '</span>';
        echo '<div class="kt-qty-control" aria-label="' . esc_attr__('עדכון כמות', 'kindertoys') . '">';
        echo '<button type="button" data-cart-qty="-1" aria-label="' . esc_attr__('הפחת כמות', 'kindertoys') . '">-</button>';
        echo '<input type="number" min="0" step="1" value="' . esc_attr((string) $quantity) . '" aria-label="' . esc_attr__('כמות', 'kindertoys') . '" data-cart-qty-input>';
        echo '<button type="button" data-cart-qty="1" aria-label="' . esc_attr__('הוסף כמות', 'kindertoys') . '">+</button>';
        echo '</div>';
        echo '</div>';
        echo '<button class="kt-cart-drawer__remove" type="button" data-cart-remove aria-label="' . esc_attr__('הסר מוצר מהסל', 'kindertoys') . '">' . kindertoys_svg_icon('close') . '</button>';
        echo '</li>';
    }
    echo '</ul></div>';
}

function kindertoys_wishlist_drawer(): void
{
    if (! class_exists('WooCommerce')) {
        return;
    }
    ?>
    <aside class="kt-wishlist-drawer" aria-hidden="true" aria-labelledby="kt-wishlist-title" data-wishlist-drawer>
        <div class="kt-wishlist-drawer__panel" role="dialog" aria-modal="true">
            <header class="kt-cart-drawer__head">
                <div>
                    <span><?php esc_html_e('מועדפים', 'kindertoys'); ?></span>
                    <h2 id="kt-wishlist-title"><?php esc_html_e('המוצרים שאהבתם', 'kindertoys'); ?></h2>
                </div>
                <button class="kt-icon-button" type="button" aria-label="<?php esc_attr_e('סגור מועדפים', 'kindertoys'); ?>" data-wishlist-close><?php echo kindertoys_svg_icon('close'); ?></button>
            </header>
            <div class="kt-wishlist-drawer__items" data-wishlist-items>
                <div class="kt-cart-drawer__empty">
                    <?php echo kindertoys_svg_icon('heart'); ?>
                    <strong><?php esc_html_e('עדיין אין מועדפים', 'kindertoys'); ?></strong>
                    <span><?php esc_html_e('לחצו על הלב בכרטיס מוצר כדי לשמור אותו כאן.', 'kindertoys'); ?></span>
                </div>
            </div>
        </div>
        <button class="kt-cart-drawer__backdrop" type="button" aria-label="<?php esc_attr_e('סגור מועדפים', 'kindertoys'); ?>" data-wishlist-close></button>
    </aside>
    <?php
}

function kindertoys_single_product_brand(): void
{
    global $product;

    if (! $product instanceof WC_Product || ! function_exists('kindertoys_core_get_product_meta')) {
        return;
    }

    $brand = kindertoys_core_get_product_meta($product, 'brand_label');
    $badge = kindertoys_product_badge_text($product);

    $sale_badge_text = $product->is_on_sale() ? (string) kindertoys_setting('product_badge_sale_text', '') : '';

    if ('' === $brand && '' === $badge && '' === $sale_badge_text && $product->is_in_stock()) {
        return;
    }

    echo '<div class="kt-single-kicker">';

    if ('' !== $brand) {
        echo '<span class="kt-single-kicker__brand">' . esc_html($brand) . '</span>';
    }

    if (! $product->is_in_stock()) {
        echo '<span class="kt-badge kt-badge--inline kt-badge--stock">' . esc_html__('אזל מהמלאי', 'kindertoys') . '</span>';
    } elseif ('' !== $badge) {
        echo '<span class="kt-badge kt-badge--inline">' . esc_html($badge) . '</span>';
    } elseif ('' !== $sale_badge_text) {
        echo '<span class="kt-badge kt-badge--inline">' . esc_html($sale_badge_text) . '</span>';
    }

    echo '</div>';
}

function kindertoys_single_out_of_stock_panel(): void
{
    global $product;

    if (! $product instanceof WC_Product || $product->is_in_stock()) {
        return;
    }

    ?>
    <section class="kt-waitlist" aria-label="<?php esc_attr_e('רשימת המתנה', 'kindertoys'); ?>">
        <strong><?php esc_html_e('המוצר אזל מהמלאי', 'kindertoys'); ?></strong>
        <span><?php esc_html_e('השאירו פרטים ונעדכן אתכם ברגע שהוא חוזר.', 'kindertoys'); ?></span>
        <form data-waitlist-form>
            <input type="hidden" name="product_id" value="<?php echo esc_attr((string) $product->get_id()); ?>">
            <div class="kt-hp" aria-hidden="true">
                <label><?php esc_html_e('אל תמלאו שדה זה', 'kindertoys'); ?><input type="text" name="kt_hp_url" tabindex="-1" autocomplete="off" value=""></label>
            </div>
            <label>
                <span class="screen-reader-text"><?php esc_html_e('שם מלא', 'kindertoys'); ?></span>
                <input type="text" name="name" autocomplete="name" placeholder="<?php esc_attr_e('שם מלא', 'kindertoys'); ?>" required>
            </label>
            <label>
                <span class="screen-reader-text"><?php esc_html_e('אימייל', 'kindertoys'); ?></span>
                <input type="email" name="email" autocomplete="email" placeholder="<?php esc_attr_e('אימייל לעדכון', 'kindertoys'); ?>" required>
            </label>
            <button class="kt-button" type="submit"><?php esc_html_e('עדכנו אותי כשחוזר', 'kindertoys'); ?></button>
            <label class="kt-waitlist__terms">
                <input type="checkbox" name="terms" value="1" required>
                <span><?php esc_html_e('אני מאשר/ת לקבל עדכון במייל כשהמוצר חוזר למלאי.', 'kindertoys'); ?></span>
            </label>
        </form>
    </section>
    <?php
}

function kindertoys_single_wishlist_button(): void
{
    global $product;

    if (! $product instanceof WC_Product) {
        return;
    }

    echo '<button class="kt-single-wishlist" type="button" data-wishlist-toggle data-product-id="' . esc_attr((string) $product->get_id()) . '" aria-pressed="false">';
    echo kindertoys_svg_icon('heart') . '<span>' . esc_html__('שמירה למועדפים', 'kindertoys') . '</span>';
    echo '</button>';
}

function kindertoys_sticky_add_to_cart(): void
{
    if (! is_product()) {
        return;
    }

    global $product;
    if (! $product instanceof WC_Product) {
        return;
    }

    $is_available = $product->is_purchasable() && $product->is_in_stock();
    ?>
    <aside class="kt-sticky-atc" data-sticky-atc aria-hidden="true">
        <div class="kt-sticky-atc__inner">
            <span class="kt-sticky-atc__thumb"><?php echo $product->get_image('woocommerce_thumbnail'); ?></span>
            <span class="kt-sticky-atc__body">
                <strong><?php echo esc_html($product->get_name()); ?></strong>
                <span><?php echo wp_kses_post($product->get_price_html()); ?></span>
            </span>
            <?php if ($is_available) : ?>
                <button class="kt-button" type="button" data-sticky-atc-submit><?php esc_html_e('הוספה לסל', 'kindertoys'); ?></button>
            <?php else : ?>
                <a class="kt-button" href="#primary"><?php esc_html_e('עדכנו אותי כשחוזר', 'kindertoys'); ?></a>
            <?php endif; ?>
        </div>
    </aside>
    <?php
}

function kindertoys_stock_html(string $html, WC_Product $product): string
{
    if ($product->is_in_stock()) {
        return '';
    }

    return '<p class="stock out-of-stock kt-stock-alert">' . esc_html__('אזל מהמלאי', 'kindertoys') . '</p>';
}

function kindertoys_outofstock_last_clauses(array $clauses, WP_Query $query): array
{
    if (is_admin() || ! $query->is_main_query() || ! (is_shop() || is_product_taxonomy())) {
        return $clauses;
    }

    global $wpdb;
    if (! str_contains($clauses['join'], 'kt_stock_status')) {
        $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS kt_stock_status ON ({$wpdb->posts}.ID = kt_stock_status.post_id AND kt_stock_status.meta_key = '_stock_status') ";
    }

    $stock_order = "CASE WHEN kt_stock_status.meta_value = 'outofstock' THEN 1 ELSE 0 END ASC";
    $clauses['orderby'] = '' !== trim($clauses['orderby']) ? $stock_order . ', ' . $clauses['orderby'] : $stock_order;

    return $clauses;
}

function kindertoys_single_product_facts(): void
{
    global $product;

    if (! $product instanceof WC_Product || ! function_exists('kindertoys_core_get_product_meta')) {
        return;
    }

    $facts = [
        __('גיל', 'kindertoys') => kindertoys_core_get_product_meta($product, 'age'),
        __('חלקים', 'kindertoys') => kindertoys_core_get_product_meta($product, 'pieces'),
        __('זמן', 'kindertoys') => kindertoys_core_get_product_meta($product, 'play_time'),
        __('שחקנים', 'kindertoys') => kindertoys_core_get_product_meta($product, 'players'),
    ];

    $facts = array_filter($facts);

    if (empty($facts)) {
        return;
    }

    echo '<dl class="kt-product-facts">';
    foreach ($facts as $label => $value) {
        echo '<div><dt>' . esc_html($label) . '</dt><dd>' . esc_html($value) . '</dd></div>';
    }
    echo '</dl>';
}

function kindertoys_single_product_highlights(): void
{
    global $product;

    if (! $product instanceof WC_Product || ! function_exists('kindertoys_core_get_product_meta_lines')) {
        return;
    }

    $highlights = kindertoys_core_get_product_meta_lines($product, 'highlights');

    if (empty($highlights)) {
        return;
    }

    echo '<ul class="kt-product-highlights">';
    foreach ($highlights as $highlight) {
        echo '<li>' . kindertoys_svg_icon('check') . '<span>' . esc_html($highlight) . '</span></li>';
    }
    echo '</ul>';
}

function kindertoys_single_product_trust(): void
{
    $items = [
        ['truck', __('משלוח חינם מעל 299 ₪', 'kindertoys')],
        ['rotate', __('החזרה תוך 14 יום', 'kindertoys')],
        ['shield', __('תשלום מאובטח', 'kindertoys')],
    ];

    echo '<div class="kt-product-trust">';
    foreach ($items as [$icon, $label]) {
        echo '<span>' . kindertoys_svg_icon($icon) . esc_html($label) . '</span>';
    }
    echo '</div>';
}

function kindertoys_product_tabs(array $tabs): array
{
    global $product;

    if (! $product instanceof WC_Product || ! function_exists('kindertoys_core_get_product_meta_lines')) {
        return $tabs;
    }

    if (isset($tabs['description'])) {
        $tabs['description']['title'] = __('תיאור', 'kindertoys');
        $tabs['description']['priority'] = 10;
    }

    $in_box = kindertoys_core_get_product_meta_lines($product, 'in_box');

    if (! empty($in_box)) {
        $tabs['kindertoys_in_box'] = [
            'title' => __('מה בקופסה?', 'kindertoys'),
            'priority' => 18,
            'callback' => 'kindertoys_in_box_tab',
        ];
    }

    $tabs['kindertoys_shipping'] = [
        'title' => __('משלוחים והחזרות', 'kindertoys'),
        'priority' => 50,
        'callback' => 'kindertoys_shipping_tab',
    ];

    $tabs['kindertoys_faq'] = [
        'title' => __('שאלות נפוצות', 'kindertoys'),
        'priority' => 60,
        'callback' => 'kindertoys_faq_tab',
    ];

    return $tabs;
}

function kindertoys_in_box_tab(): void
{
    global $product;

    if (! $product instanceof WC_Product || ! function_exists('kindertoys_core_get_product_meta_lines')) {
        return;
    }

    echo '<div class="kt-in-box"><ul>';
    foreach (kindertoys_core_get_product_meta_lines($product, 'in_box') as $item) {
        echo '<li>' . kindertoys_svg_icon('gift') . '<span>' . esc_html($item) . '</span></li>';
    }
    echo '</ul></div>';
}

function kindertoys_shipping_tab(): void
{
    echo '<div class="kt-info-tab">';
    echo '<p>' . esc_html__('משלוח מהיר עד הבית, ומשלוח חינם בהזמנות מעל 299 ש"ח בהתאם למדיניות האתר.', 'kindertoys') . '</p>';
    echo '<p>' . esc_html__('ניתן להחזיר מוצרים שלא נפתחו בתוך 14 יום, בהתאם לתנאי ההחזרה והשירות.', 'kindertoys') . '</p>';
    echo '</div>';
}

function kindertoys_faq_tab(): void
{
    echo '<div class="kt-info-tab">';
    echo '<h3>' . esc_html__('המוצר מתאים כמתנה?', 'kindertoys') . '</h3>';
    echo '<p>' . esc_html__('כן. מומלץ לבדוק את גיל היעד ופרטי המוצר לפני ההזמנה.', 'kindertoys') . '</p>';
    echo '<h3>' . esc_html__('איך יודעים שהמוצר במלאי?', 'kindertoys') . '</h3>';
    echo '<p>' . esc_html__('אם ניתן להוסיף את המוצר לסל, הוא זמין להזמנה. אם אזל מהמלאי אפשר להירשם לעדכון.', 'kindertoys') . '</p>';
    echo '</div>';
}

function kindertoys_cart_fragments(array $fragments): array
{
    ob_start();
    ?>
    <span class="kt-cart-count" data-cart-count><?php echo esc_html((string) kindertoys_cart_count()); ?></span>
    <?php
    $fragments['[data-cart-count]'] = ob_get_clean();

    ob_start();
    ?>
    <strong class="kt-cart-total" data-cart-total><?php echo kindertoys_cart_total(); ?></strong>
    <?php
    $fragments['[data-cart-total]'] = ob_get_clean();

    ob_start();
    kindertoys_cart_drawer_items();
    $fragments['[data-cart-drawer-items]'] = ob_get_clean();

    ob_start();
    kindertoys_cart_free_shipping_progress();
    $fragments['[data-cart-progress]'] = ob_get_clean();

    return $fragments;
}

function kindertoys_free_shipping_data(): array
{
    $threshold = max(0, (float) kindertoys_setting('free_shipping_threshold', '299'));
    $subtotal = 0.0;
    if (function_exists('WC') && WC()->cart) {
        $subtotal = method_exists(WC()->cart, 'get_displayed_subtotal')
            ? (float) WC()->cart->get_displayed_subtotal()
            : (float) WC()->cart->get_subtotal();
    }
    $remaining = max(0, $threshold - $subtotal);
    $percent = $threshold > 0 ? min(100, (int) round(($subtotal / $threshold) * 100)) : 0;

    return [
        'threshold' => $threshold,
        'subtotal' => $subtotal,
        'remaining' => $remaining,
        'percent' => $percent,
    ];
}

function kindertoys_cart_free_shipping_progress(): void
{
    $data = kindertoys_free_shipping_data();
    if ($data['threshold'] <= 0 || ! function_exists('wc_price')) {
        return;
    }

    $is_complete = $data['remaining'] <= 0;
    ?>
    <div class="kt-cart-progress" data-cart-progress>
        <div class="kt-cart-progress__text">
            <?php if ($is_complete) : ?>
                <strong><?php esc_html_e('מעולה, הגעתם למשלוח חינם', 'kindertoys'); ?></strong>
                <span><?php esc_html_e('אפשר להמשיך לתשלום בראש שקט.', 'kindertoys'); ?></span>
            <?php else : ?>
                <strong><?php echo wp_kses_post(sprintf(__('נשארו %s למשלוח חינם', 'kindertoys'), wc_price($data['remaining']))); ?></strong>
                <span><?php echo esc_html((string) kindertoys_setting('free_shipping_hint_text', 'עוד מוצר קטן יכול לסגור את הפינה.')); ?></span>
            <?php endif; ?>
        </div>
        <span class="kt-cart-progress__bar" aria-hidden="true"><i style="width: <?php echo esc_attr((string) $data['percent']); ?>%"></i></span>
    </div>
    <?php
}

function kindertoys_cart_drawer(): void
{
    if (! class_exists('WooCommerce')) {
        return;
    }
    ?>
    <aside class="kt-cart-drawer" aria-hidden="true" aria-labelledby="kt-cart-drawer-title" data-cart-drawer>
        <div class="kt-cart-drawer__panel" role="dialog" aria-modal="true">
            <header class="kt-cart-drawer__head">
                <div>
                    <span><?php esc_html_e('סל הקניות', 'kindertoys'); ?></span>
                    <h2 id="kt-cart-drawer-title"><?php esc_html_e('המוצרים שבחרתם', 'kindertoys'); ?></h2>
                </div>
                <button class="kt-icon-button" type="button" aria-label="<?php esc_attr_e('סגור סל', 'kindertoys'); ?>" data-cart-drawer-close><?php echo kindertoys_svg_icon('close'); ?></button>
            </header>
            <div class="kt-cart-drawer__items" data-cart-drawer-items>
                <?php kindertoys_cart_drawer_items(); ?>
            </div>
        </div>
        <button class="kt-cart-drawer__backdrop" type="button" aria-label="<?php esc_attr_e('סגור סל', 'kindertoys'); ?>" data-cart-drawer-close></button>
    </aside>
    <?php
}

function kindertoys_cart_drawer_items(): void
{
    if (! function_exists('WC') || ! WC()->cart || WC()->cart->is_empty()) {
        echo '<div class="kt-cart-drawer__empty">';
        echo kindertoys_svg_icon('cart');
        echo '<strong>' . esc_html__('הסל עדיין ריק', 'kindertoys') . '</strong>';
        echo '<span>' . esc_html__('אפשר להוסיף מוצרים ולהמשיך לקנות בלי לעזוב את העמוד.', 'kindertoys') . '</span>';
        echo '<a class="kt-button" href="' . esc_url(wc_get_page_permalink('shop')) . '">' . esc_html__('לחנות', 'kindertoys') . '</a>';
        echo '</div>';
        return;
    }

    kindertoys_cart_free_shipping_progress();
    kindertoys_cart_drawer_products();
    echo '<footer class="kt-cart-drawer__foot">';
    echo '<div><span>' . esc_html__('סה"כ ביניים', 'kindertoys') . '</span><strong>' . wp_kses_post(WC()->cart->get_cart_subtotal()) . '</strong></div>';
    echo '<a class="kt-button" href="' . esc_url(wc_get_checkout_url()) . '">' . esc_html__('לתשלום', 'kindertoys') . '</a>';
    echo '<label class="kt-save-cart-email-toggle"><input type="checkbox" data-save-cart-email-toggle><span>' . esc_html__('שלחו לי את קישור הסל למייל', 'kindertoys') . '</span></label>';
    echo '<label class="kt-save-cart-email" hidden><span>' . esc_html__('אימייל לשליחת הקישור', 'kindertoys') . '</span><input type="email" data-save-cart-email autocomplete="email" placeholder="' . esc_attr__('your@email.com', 'kindertoys') . '"></label>';
    echo '<button class="kt-save-cart-button" type="button" data-save-cart>' . esc_html__('שמירת הסל להמשך', 'kindertoys') . '</button>';
    echo '<div class="kt-save-cart-result" data-save-cart-result hidden></div>';
    echo '</footer>';
}

function kindertoys_ajax_update_cart_item(): void
{
    check_ajax_referer('kindertoys_ajax', 'nonce');

    if (! function_exists('WC') || ! WC()->cart) {
        wp_send_json_error(['message' => __('WooCommerce לא זמין כרגע.', 'kindertoys')], 400);
    }

    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash((string) $_POST['cart_item_key'])) : '';
    $quantity = isset($_POST['quantity']) ? max(0, absint($_POST['quantity'])) : 0;

    if ('' === $cart_item_key || ! isset(WC()->cart->cart_contents[$cart_item_key])) {
        wp_send_json_error(['message' => __('המוצר לא נמצא בסל.', 'kindertoys')], 404);
    }

    WC()->cart->set_quantity($cart_item_key, $quantity, true);
    WC()->cart->calculate_totals();

    ob_start();
    kindertoys_cart_drawer_items();
    $items = ob_get_clean();

    wp_send_json_success([
        'items' => $items,
        'count' => kindertoys_cart_count(),
        'total' => kindertoys_cart_total(),
    ]);
}

function kindertoys_ajax_cart_snapshot(): void
{
    check_ajax_referer('kindertoys_ajax', 'nonce');

    if (! function_exists('WC') || ! WC()->cart) {
        wp_send_json_error(['message' => __('WooCommerce לא זמין כרגע.', 'kindertoys')], 400);
    }

    ob_start();
    kindertoys_cart_drawer_items();
    $items = ob_get_clean();

    wp_send_json_success([
        'items' => $items,
        'count' => kindertoys_cart_count(),
        'total' => kindertoys_cart_total(),
    ]);
}

function kindertoys_saved_cart_payload(): array
{
    $items = [];

    if (! function_exists('WC') || ! WC()->cart) {
        return $items;
    }

    foreach (WC()->cart->get_cart() as $cart_item) {
        $product = $cart_item['data'] ?? null;
        if (! $product instanceof WC_Product || ! $product->exists()) {
            continue;
        }

        $items[] = [
            'product_id' => (int) ($cart_item['product_id'] ?? 0),
            'variation_id' => (int) ($cart_item['variation_id'] ?? 0),
            'quantity' => max(1, (int) ($cart_item['quantity'] ?? 1)),
            'variation' => isset($cart_item['variation']) && is_array($cart_item['variation']) ? $cart_item['variation'] : [],
            'name' => $product->get_name(),
            'price' => (float) $product->get_price(),
        ];
    }

    return $items;
}

function kindertoys_client_ip(): string
{
    // Use the TCP peer (REMOTE_ADDR) only. Proxy headers such as
    // X-Forwarded-For / X-Real-IP are client-spoofable, so trusting them would
    // let a bot rotate the value and mint a fresh rate-limit key per request.
    // Behind a CDN, restore the real visitor IP into REMOTE_ADDR at the edge.
    return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
}

/**
 * Lightweight per-IP rate limit for public AJAX actions.
 *
 * @return bool True when the request is allowed, false when the limit is reached.
 */
function kindertoys_rate_limit(string $bucket, int $limit, int $window): bool
{
    $key = 'kt_rl_' . md5($bucket . '|' . kindertoys_client_ip());
    $count = (int) get_transient($key);

    if ($count >= $limit) {
        return false;
    }

    set_transient($key, $count + 1, $window);

    return true;
}

function kindertoys_ajax_save_cart(): void
{
    check_ajax_referer('kindertoys_ajax', 'nonce');

    if (! function_exists('WC') || ! WC()->cart || WC()->cart->is_empty()) {
        wp_send_json_error(['message' => __('אין מוצרים בסל לשמירה.', 'kindertoys')], 400);
    }

    if (! kindertoys_rate_limit('save_cart', 5, 10 * MINUTE_IN_SECONDS)) {
        wp_send_json_error(['message' => __('יותר מדי בקשות. נסו שוב בעוד כמה דקות.', 'kindertoys')], 429);
    }

    $token = wp_generate_password(24, false, false);
    $items = kindertoys_saved_cart_payload();
    $customer_email = isset($_POST['email']) ? sanitize_email(wp_unslash((string) $_POST['email'])) : '';
    $payload = [
        'token' => $token,
        'items' => $items,
        'created_at' => time(),
        'cart_total' => wp_strip_all_tags(WC()->cart->get_cart_total()),
        'cart_url' => add_query_arg('kt_restore_cart', $token, wc_get_cart_url()),
        'customer_email' => $customer_email,
    ];

    set_transient('kindertoys_saved_cart_' . $token, $payload, 30 * DAY_IN_SECONDS);

    $email_to = sanitize_email((string) kindertoys_setting('saved_cart_email_to', ''));
    if ('' === $email_to) {
        $email_to = get_option('admin_email');
    }
    if (is_email($email_to)) {
        wp_mail(
            $email_to,
            sprintf(__('סל שמור חדש באתר %s', 'kindertoys'), wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES)),
            sprintf("%s\n\n%s", __('נוצר סל שמור חדש. קישור לשחזור:', 'kindertoys'), $payload['cart_url'])
        );
    }

    if (is_email($customer_email)) {
        $site = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $replacements = [
            '{site}' => $site,
            '{url}' => $payload['cart_url'],
        ];
        $subject = strtr((string) kindertoys_setting('saved_cart_customer_subject', 'הסל שלך נשמר ב-{site}'), $replacements);
        $message = strtr((string) kindertoys_setting('saved_cart_customer_body', "היי,\n\nשמנו לך בצד את הסל. אפשר לחזור אליו מכל מכשיר דרך הקישור:\n{url}\n\nהקישור תקף ל-30 יום."), $replacements);
        wp_mail($customer_email, $subject, $message);
    }

    $webhook = (string) kindertoys_setting('saved_cart_webhook_url', '');
    if ('' !== $webhook) {
        wp_remote_post(esc_url_raw($webhook), [
            'timeout' => 8,
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'body' => wp_json_encode($payload),
        ]);
    }

    wp_send_json_success([
        'url' => $payload['cart_url'],
        'message' => __('הסל נשמר. אפשר לחזור אליו מהקישור הזה.', 'kindertoys'),
    ]);
}

function kindertoys_restore_saved_cart(): void
{
    if (! isset($_GET['kt_restore_cart']) || is_admin() || ! function_exists('WC')) {
        return;
    }

    if (null === WC()->cart && function_exists('wc_load_cart')) {
        wc_load_cart();
    }

    if (! WC()->cart) {
        return;
    }

    $token = sanitize_text_field(wp_unslash((string) $_GET['kt_restore_cart']));
    if ('' === $token || ! preg_match('/^[A-Za-z0-9]{12,40}$/', $token)) {
        return;
    }

    $payload = get_transient('kindertoys_saved_cart_' . $token);
    if (! is_array($payload) || empty($payload['items']) || ! is_array($payload['items'])) {
        wc_add_notice(__('הקישור לסל השמור לא נמצא או שפג תוקפו.', 'kindertoys'), 'error');
        return;
    }

    WC()->cart->empty_cart();

    foreach ($payload['items'] as $item) {
        if (! is_array($item)) {
            continue;
        }

        $product_id = absint($item['product_id'] ?? 0);
        $variation_id = absint($item['variation_id'] ?? 0);
        $quantity = max(1, absint($item['quantity'] ?? 1));
        $variation = isset($item['variation']) && is_array($item['variation']) ? array_map('wc_clean', $item['variation']) : [];

        if ($product_id > 0) {
            WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);
        }
    }

    WC()->cart->calculate_totals();
    wc_add_notice(__('הסל השמור שוחזר בהצלחה.', 'kindertoys'), 'success');
    wp_safe_redirect(remove_query_arg('kt_restore_cart', wc_get_cart_url()));
    exit;
}

function kindertoys_ajax_add_product_to_cart(): void
{
    check_ajax_referer('kindertoys_ajax', 'nonce');

    if (! function_exists('WC') || ! WC()->cart) {
        wp_send_json_error(['message' => __('WooCommerce לא זמין כרגע.', 'kindertoys')], 400);
    }

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $product = $product_id > 0 ? wc_get_product($product_id) : null;

    if (! $product instanceof WC_Product || ! $product->is_purchasable() || ! $product->is_in_stock()) {
        wp_send_json_error(['message' => __('המוצר לא זמין להוספה לסל.', 'kindertoys')], 400);
    }

    $added = WC()->cart->add_to_cart($product_id, 1);
    if (! $added) {
        wp_send_json_error(['message' => __('לא הצלחנו להוסיף את המוצר לסל.', 'kindertoys')], 400);
    }

    WC()->cart->calculate_totals();

    ob_start();
    kindertoys_cart_drawer_items();
    $items = ob_get_clean();

    wp_send_json_success([
        'items' => $items,
        'count' => kindertoys_cart_count(),
        'total' => kindertoys_cart_total(),
        'message' => __('המוצר נוסף לסל', 'kindertoys'),
    ]);
}

function kindertoys_checkout_bump_product(): ?WC_Product
{
    if ('1' !== (string) kindertoys_setting('checkout_bump_enabled', '1')) {
        return null;
    }

    $product_id = absint(kindertoys_setting('checkout_bump_product_id', '0'));
    if ($product_id <= 0) {
        return null;
    }

    $product = wc_get_product($product_id);
    if (! $product instanceof WC_Product || ! $product->is_purchasable() || ! $product->is_in_stock()) {
        return null;
    }

    return $product;
}

function kindertoys_checkout_bump_cart_item_key(int $product_id): string
{
    if (! function_exists('WC') || ! WC()->cart) {
        return '';
    }

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if ((int) ($cart_item['product_id'] ?? 0) === $product_id && ! empty($cart_item['kindertoys_checkout_bump'])) {
            return (string) $cart_item_key;
        }
    }

    return '';
}

function kindertoys_checkout_bump(): void
{
    $product = kindertoys_checkout_bump_product();
    if (! $product instanceof WC_Product) {
        return;
    }

    $discount = min(80, max(0, absint(kindertoys_setting('checkout_bump_discount_percent', '0'))));
    $cart_item_key = kindertoys_checkout_bump_cart_item_key($product->get_id());
    $is_added = '' !== $cart_item_key;
    ?>
    <section class="kt-checkout-bump" data-checkout-bump>
        <label class="kt-checkout-bump__inner">
            <input type="checkbox" data-checkout-bump-toggle data-product-id="<?php echo esc_attr((string) $product->get_id()); ?>" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>" <?php checked($is_added); ?>>
            <span class="kt-checkout-bump__media"><?php echo $product->get_image('woocommerce_thumbnail'); ?></span>
            <span class="kt-checkout-bump__body">
                <strong><?php echo esc_html((string) kindertoys_setting('checkout_bump_title', 'רוצים להוסיף עוד משהו קטן?')); ?></strong>
                <small><?php echo esc_html((string) kindertoys_setting('checkout_bump_text', 'הוסיפו מוצר משלים להזמנה בלחיצה אחת, בלי לעזוב את התשלום.')); ?></small>
                <span><?php echo esc_html($product->get_name()); ?> · <?php echo wp_kses_post($product->get_price_html()); ?></span>
            </span>
            <?php if ($discount > 0) : ?>
                <span class="kt-checkout-bump__badge"><?php echo esc_html(sprintf(__('%d%% הנחה', 'kindertoys'), $discount)); ?></span>
            <?php endif; ?>
        </label>
    </section>
    <?php
}

function kindertoys_checkout_bump_cart_item_data(array $data, int $product_id): array
{
    if (empty($_POST['kindertoys_checkout_bump'])) {
        return $data;
    }

    $bump_product = kindertoys_checkout_bump_product();
    if (! $bump_product instanceof WC_Product || $product_id !== $bump_product->get_id()) {
        return $data;
    }

    $discount = min(80, max(0, absint(kindertoys_setting('checkout_bump_discount_percent', '0'))));
    $data['kindertoys_checkout_bump'] = true;
    $data['kindertoys_checkout_bump_discount'] = $discount;
    $data['unique_key'] = md5('kindertoys_bump_' . $product_id . '_' . wp_rand());

    return $data;
}

function kindertoys_restore_checkout_bump_cart_item(array $item, array $values): array
{
    if (! empty($values['kindertoys_checkout_bump'])) {
        $item['kindertoys_checkout_bump'] = true;
        $item['kindertoys_checkout_bump_discount'] = absint($values['kindertoys_checkout_bump_discount'] ?? 0);
    }

    return $item;
}

function kindertoys_apply_checkout_bump_discount(WC_Cart $cart): void
{
    if (is_admin() && ! defined('DOING_AJAX')) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item) {
        if (empty($cart_item['kindertoys_checkout_bump']) || empty($cart_item['kindertoys_checkout_bump_discount'])) {
            continue;
        }

        $product = $cart_item['data'] ?? null;
        if (! $product instanceof WC_Product) {
            continue;
        }

        $discount = min(80, max(0, (int) $cart_item['kindertoys_checkout_bump_discount']));
        $regular = (float) $product->get_regular_price();
        $base = $regular > 0 ? $regular : (float) $product->get_price();
        if ($base > 0 && $discount > 0) {
            $product->set_price((string) round($base * (100 - $discount) / 100, wc_get_price_decimals()));
        }
    }
}

function kindertoys_checkout_bump_order_item_meta(WC_Order_Item_Product $item, string $cart_item_key, array $values, WC_Order $order): void
{
    if (! empty($values['kindertoys_checkout_bump'])) {
        $item->add_meta_data('_kindertoys_checkout_bump', '1', true);
    }
}

function kindertoys_ajax_toggle_checkout_bump(): void
{
    check_ajax_referer('kindertoys_ajax', 'nonce');

    if (! function_exists('WC') || ! WC()->cart) {
        wp_send_json_error(['message' => __('WooCommerce לא זמין כרגע.', 'kindertoys')], 400);
    }

    $product = kindertoys_checkout_bump_product();
    if (! $product instanceof WC_Product) {
        wp_send_json_error(['message' => __('מוצר האפסייל לא זמין כרגע.', 'kindertoys')], 400);
    }

    $enabled = isset($_POST['enabled']) && '1' === (string) wp_unslash($_POST['enabled']);
    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash((string) $_POST['cart_item_key'])) : '';

    if ($enabled) {
        $existing = kindertoys_checkout_bump_cart_item_key($product->get_id());
        $new_key = '' !== $existing ? $existing : WC()->cart->add_to_cart($product->get_id(), 1, 0, [], [
            'kindertoys_checkout_bump' => true,
            'kindertoys_checkout_bump_discount' => absint(kindertoys_setting('checkout_bump_discount_percent', '0')),
            'unique_key' => md5('kindertoys_bump_' . $product->get_id() . '_' . wp_rand()),
        ]);
        if (! $new_key) {
            wp_send_json_error(['message' => __('לא הצלחנו להוסיף את מוצר האפסייל.', 'kindertoys')], 400);
        }
        WC()->cart->calculate_totals();
        wp_send_json_success(['cart_item_key' => $new_key, 'message' => __('המוצר נוסף להזמנה', 'kindertoys')]);
    }

    if ('' !== $cart_item_key && isset(WC()->cart->cart_contents[$cart_item_key])) {
        WC()->cart->remove_cart_item($cart_item_key);
    } else {
        $existing = kindertoys_checkout_bump_cart_item_key($product->get_id());
        if ('' !== $existing) {
            WC()->cart->remove_cart_item($existing);
        }
    }

    WC()->cart->calculate_totals();
    wp_send_json_success(['cart_item_key' => '', 'message' => __('המוצר הוסר מההזמנה', 'kindertoys')]);
}

function kindertoys_ajax_search_products(): void
{
    check_ajax_referer('kindertoys_ajax', 'nonce');

    $term = isset($_GET['term']) ? sanitize_text_field(wp_unslash((string) $_GET['term'])) : '';
    $term = trim($term);

    if (strlen($term) < 2) {
        wp_send_json_success(['html' => '']);
    }

    $query = new WP_Query([
        'post_type' => 'product',
        'post_status' => 'publish',
        's' => $term,
        'posts_per_page' => 6,
        'no_found_rows' => true,
        'ignore_sticky_posts' => true,
        'meta_query' => [
            [
                'key' => '_stock_status',
                'value' => 'outofstock',
                'compare' => '!=',
            ],
        ],
    ]);

    ob_start();
    if ($query->have_posts()) {
        echo '<div class="kt-search-results__grid">';
        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product(get_the_ID());
            if (! $product instanceof WC_Product || ! $product->is_in_stock()) {
                continue;
            }
            echo '<a class="kt-search-result" href="' . esc_url(get_permalink()) . '" role="option">';
            echo '<span class="kt-search-result__thumb">' . $product->get_image('woocommerce_thumbnail') . '</span>';
            echo '<span class="kt-search-result__body"><strong>' . esc_html(get_the_title()) . '</strong><small>' . wp_kses_post($product->get_price_html()) . '</small></span>';
            echo '</a>';
        }
        echo '</div>';
        echo '<a class="kt-search-results__all" href="' . esc_url(add_query_arg(['s' => $term, 'post_type' => 'product'], home_url('/'))) . '">' . esc_html__('לכל תוצאות החיפוש', 'kindertoys') . '</a>';
    } else {
        echo '<div class="kt-search-results__empty">' . esc_html__('לא נמצאו מוצרים מתאימים', 'kindertoys') . '</div>';
    }
    wp_reset_postdata();

    wp_send_json_success(['html' => ob_get_clean()]);
}

function kindertoys_ajax_wishlist_products(): void
{
    check_ajax_referer('kindertoys_ajax', 'nonce');

    $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('absint', wp_unslash($_POST['ids'])) : [];
    $ids = array_values(array_filter(array_unique($ids)));

    if (empty($ids)) {
        wp_send_json_success(['html' => kindertoys_wishlist_empty_html()]);
    }

    $query = new WP_Query([
        'post_type' => 'product',
        'post_status' => 'publish',
        'post__in' => $ids,
        'orderby' => 'post__in',
        'posts_per_page' => 20,
        'no_found_rows' => true,
    ]);

    ob_start();
    if ($query->have_posts()) {
        echo '<ul class="kt-wishlist-list">';
        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product(get_the_ID());
            if (! $product instanceof WC_Product) {
                continue;
            }
            echo '<li class="kt-wishlist-item" data-product-id="' . esc_attr((string) $product->get_id()) . '">';
            echo '<a class="kt-cart-drawer__thumb" href="' . esc_url(get_permalink()) . '">' . $product->get_image('woocommerce_thumbnail') . '</a>';
            echo '<div class="kt-cart-drawer__body">';
            echo '<a class="kt-cart-drawer__name" href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a>';
            echo '<span class="kt-cart-drawer__price">' . wp_kses_post($product->get_price_html()) . '</span>';
            if ($product->is_purchasable() && $product->is_in_stock()) {
                echo '<button class="kt-wishlist-item__add" type="button" data-wishlist-add-to-cart data-product-id="' . esc_attr((string) $product->get_id()) . '">' . esc_html__('הוספה לסל', 'kindertoys') . '</button>';
            } else {
                echo '<span class="kt-wishlist-item__stock">' . esc_html__('לא זמין כרגע', 'kindertoys') . '</span>';
            }
            echo '</div>';
            echo '<button class="kt-cart-drawer__remove" type="button" data-wishlist-remove aria-label="' . esc_attr__('הסר מהמועדפים', 'kindertoys') . '">' . kindertoys_svg_icon('close') . '</button>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo kindertoys_wishlist_empty_html();
    }
    wp_reset_postdata();

    wp_send_json_success(['html' => ob_get_clean()]);
}

function kindertoys_wishlist_empty_html(): string
{
    return '<div class="kt-cart-drawer__empty">' . kindertoys_svg_icon('heart') . '<strong>' . esc_html__('עדיין אין מועדפים', 'kindertoys') . '</strong><span>' . esc_html__('לחצו על הלב בכרטיס מוצר כדי לשמור אותו כאן.', 'kindertoys') . '</span></div>';
}
