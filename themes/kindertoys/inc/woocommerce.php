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

    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    add_action('woocommerce_before_shop_loop_item_title', 'kindertoys_product_card_media', 10);

    remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
    add_action('woocommerce_shop_loop_item_title', 'kindertoys_product_card_title', 10);

    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    add_action('woocommerce_after_shop_loop_item', 'kindertoys_product_card_actions', 10);

    add_filter('woocommerce_add_to_cart_fragments', 'kindertoys_cart_fragments');
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

    echo woocommerce_get_product_thumbnail('kindertoys-card');
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

    echo '<h2 class="woocommerce-loop-product__title kt-product-card__title">' . esc_html(get_the_title()) . '</h2>';
}

function kindertoys_product_card_actions(): void
{
    global $product;

    if (! $product instanceof WC_Product) {
        return;
    }

    echo '<div class="kt-product-card__actions">';
    woocommerce_template_loop_add_to_cart([
        'class' => implode(' ', array_filter([
            'button',
            'kt-button',
            'kt-button--cart',
            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
            $product->supports('ajax_add_to_cart') ? 'ajax_add_to_cart' : '',
        ])),
    ]);
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
    <span class="kt-cart-total" data-cart-total><?php echo kindertoys_cart_total(); ?></span>
    <?php
    $fragments['[data-cart-total]'] = ob_get_clean();

    return $fragments;
}
