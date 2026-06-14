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

    if (! empty($meta_query)) {
        $query->set('meta_query', $meta_query);
    }
}
