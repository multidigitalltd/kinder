<?php
/**
 * Product archive filters.
 *
 * @package KinderToysCore
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('pre_get_posts', 'kindertoys_core_apply_product_filters');
function kindertoys_core_apply_product_filters(WP_Query $query): void
{
    if (is_admin() || ! $query->is_main_query()) {
        return;
    }

    $is_product_context = (function_exists('is_shop') && is_shop())
        || (function_exists('is_product_taxonomy') && is_product_taxonomy())
        || ('product' === $query->get('post_type'));

    if (! $is_product_context) {
        return;
    }

    $meta_query = (array) $query->get('meta_query');
    $tax_query = (array) $query->get('tax_query');

    if (isset($_GET['age']) && ! is_array($_GET['age'])) {
        $age = sanitize_text_field(wp_unslash($_GET['age']));
        if ('' !== $age) {
            $meta_query[] = [
                'key' => '_kindertoys_age',
                'value' => $age,
                'compare' => '=',
            ];
        }
    }

    if (isset($_GET['brand']) && ! is_array($_GET['brand'])) {
        $brand = sanitize_text_field(wp_unslash($_GET['brand']));
        if ('' !== $brand) {
            $meta_query[] = [
                'key' => '_kindertoys_brand_label',
                'value' => $brand,
                'compare' => 'LIKE',
            ];
        }
    }

    if (isset($_GET['stock']) && 'instock' === sanitize_key((string) wp_unslash($_GET['stock']))) {
        $meta_query[] = [
            'key' => '_stock_status',
            'value' => 'instock',
            'compare' => '=',
        ];
    }

    if (isset($_GET['sale']) && '1' === (string) wp_unslash($_GET['sale'])) {
        $query->set('post__in', array_merge([0], wc_get_product_ids_on_sale()));
    }

    if (isset($_GET['min_price']) && ! is_array($_GET['min_price'])) {
        $raw_min_price = wp_unslash((string) $_GET['min_price']);
        $min_price = max(0, (float) (function_exists('wc_format_decimal') ? wc_format_decimal($raw_min_price) : preg_replace('/[^\d.]/', '', $raw_min_price)));
        if ($min_price > 0) {
            $meta_query[] = [
                'key' => '_price',
                'value' => $min_price,
                'compare' => '>=',
                'type' => 'DECIMAL(10,2)',
            ];
        }
    }

    if (isset($_GET['max_price']) && ! is_array($_GET['max_price'])) {
        $raw_max_price = wp_unslash((string) $_GET['max_price']);
        $max_price = max(0, (float) (function_exists('wc_format_decimal') ? wc_format_decimal($raw_max_price) : preg_replace('/[^\d.]/', '', $raw_max_price)));
        if ($max_price > 0) {
            $meta_query[] = [
                'key' => '_price',
                'value' => $max_price,
                'compare' => '<=',
                'type' => 'DECIMAL(10,2)',
            ];
        }
    }

    if (isset($_GET['product_cat']) && ! is_array($_GET['product_cat'])) {
        $category = sanitize_title(wp_unslash((string) $_GET['product_cat']));
        if ('' !== $category && taxonomy_exists('product_cat')) {
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => [$category],
            ];
        }
    }

    if (! empty($meta_query)) {
        $query->set('meta_query', $meta_query);
    }

    if (! empty($tax_query)) {
        $query->set('tax_query', $tax_query);
    }
}
