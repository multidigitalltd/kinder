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

    add_action('woocommerce_before_main_content', 'kindertoys_woo_wrapper_open', 10);
    add_action('woocommerce_after_main_content', 'kindertoys_woo_wrapper_close', 10);

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
    add_action('woocommerce_single_product_summary', 'kindertoys_single_product_trust', 35);

    add_filter('woocommerce_product_tabs', 'kindertoys_product_tabs');
    add_filter('woocommerce_add_to_cart_fragments', 'kindertoys_cart_fragments');

    add_action('wp_footer', 'kindertoys_cart_drawer', 20);
    add_action('wp_footer', 'kindertoys_wishlist_drawer', 21);

    add_action('wp_ajax_kindertoys_update_cart_item', 'kindertoys_ajax_update_cart_item');
    add_action('wp_ajax_nopriv_kindertoys_update_cart_item', 'kindertoys_ajax_update_cart_item');
    add_action('wp_ajax_kindertoys_cart_snapshot', 'kindertoys_ajax_cart_snapshot');
    add_action('wp_ajax_nopriv_kindertoys_cart_snapshot', 'kindertoys_ajax_cart_snapshot');
    add_action('wp_ajax_kindertoys_search_products', 'kindertoys_ajax_search_products');
    add_action('wp_ajax_nopriv_kindertoys_search_products', 'kindertoys_ajax_search_products');
    add_action('wp_ajax_kindertoys_wishlist_products', 'kindertoys_ajax_wishlist_products');
    add_action('wp_ajax_nopriv_kindertoys_wishlist_products', 'kindertoys_ajax_wishlist_products');
}

function kindertoys_woo_wrapper_open(): void
{
    echo '<main id="primary" class="site-main kt-container kt-woo-main">';
}

function kindertoys_woo_wrapper_close(): void
{
    echo '</main>';
}

function kindertoys_product_card_media(): void
{
    global $product;

    if (! $product instanceof WC_Product) {
        return;
    }

    echo '<div class="kt-product-card__media">';

    $custom_badge = function_exists('kindertoys_core_get_product_meta') ? kindertoys_core_get_product_meta($product, 'badge') : '';

    if ($product->is_on_sale()) {
        echo '<span class="kt-badge kt-badge--sale">' . esc_html__('Sale', 'kindertoys') . '</span>';
    } elseif ('' !== $custom_badge) {
        echo '<span class="kt-badge">' . esc_html($custom_badge) . '</span>';
    }

    echo '<button class="kt-product-card__wish" type="button" data-wishlist-toggle data-product-id="' . esc_attr((string) $product->get_id()) . '" aria-pressed="false" aria-label="' . esc_attr__('הוספה למועדפים', 'kindertoys') . '">' . kindertoys_svg_icon('heart') . '</button>';
    echo '<a class="kt-product-card__media-link" href="' . esc_url($product->get_permalink()) . '" aria-label="' . esc_attr($product->get_name()) . '">' . woocommerce_get_product_thumbnail('kindertoys-card') . '</a>';
    echo '</div>';
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
    $badge = kindertoys_core_get_product_meta($product, 'badge');

    if ('' === $brand && '' === $badge && ! $product->is_on_sale()) {
        return;
    }

    echo '<div class="kt-single-kicker">';

    if ('' !== $brand) {
        echo '<span class="kt-single-kicker__brand">' . esc_html($brand) . '</span>';
    }

    if ($product->is_on_sale()) {
        echo '<span class="kt-badge kt-badge--inline">' . esc_html__('מבצע', 'kindertoys') . '</span>';
    } elseif ('' !== $badge) {
        echo '<span class="kt-badge kt-badge--inline">' . esc_html($badge) . '</span>';
    }

    echo '</div>';
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

    $in_box = kindertoys_core_get_product_meta_lines($product, 'in_box');

    if (! empty($in_box)) {
        $tabs['kindertoys_in_box'] = [
            'title' => __('מה בקופסה?', 'kindertoys'),
            'priority' => 18,
            'callback' => 'kindertoys_in_box_tab',
        ];
    }

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

    return $fragments;
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

    echo '<ul class="kt-cart-drawer__list">';
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
    echo '</ul>';

    echo '<footer class="kt-cart-drawer__foot">';
    echo '<div><span>' . esc_html__('סה"כ ביניים', 'kindertoys') . '</span><strong>' . wp_kses_post(WC()->cart->get_cart_subtotal()) . '</strong></div>';
    echo '<a class="kt-button" href="' . esc_url(wc_get_checkout_url()) . '">' . esc_html__('לתשלום', 'kindertoys') . '</a>';
    echo '<a class="kt-button kt-button--light" href="' . esc_url(wc_get_cart_url()) . '">' . esc_html__('צפייה ועריכה בסל', 'kindertoys') . '</a>';
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
