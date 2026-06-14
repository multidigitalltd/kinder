<?php
/**
 * Product meta fields.
 *
 * @package KinderToysCore
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('woocommerce_product_options_general_product_data', 'kindertoys_core_product_fields');
function kindertoys_core_product_fields(): void
{
    if (! function_exists('woocommerce_wp_text_input')) {
        return;
    }

    echo '<div class="options_group">';

    woocommerce_wp_text_input([
        'id'          => '_kindertoys_age',
        'label'       => __('Recommended age', 'kindertoys-core'),
        'placeholder' => '3+',
        'desc_tip'    => true,
        'description' => __('Displayed in product cards and product facts.', 'kindertoys-core'),
    ]);

    woocommerce_wp_text_input([
        'id'          => '_kindertoys_badge',
        'label'       => __('Custom badge', 'kindertoys-core'),
        'placeholder' => __('Best seller', 'kindertoys-core'),
        'desc_tip'    => true,
        'description' => __('Optional short badge shown by the theme.', 'kindertoys-core'),
    ]);

    woocommerce_wp_text_input([
        'id'          => '_kindertoys_brand_label',
        'label'       => __('Brand label', 'kindertoys-core'),
        'placeholder' => 'LEGO',
    ]);

    woocommerce_wp_text_input([
        'id'          => '_kindertoys_pieces',
        'label'       => __('Pieces / units', 'kindertoys-core'),
        'placeholder' => '1,036',
    ]);

    woocommerce_wp_text_input([
        'id'          => '_kindertoys_players',
        'label'       => __('Players', 'kindertoys-core'),
        'placeholder' => '1-4',
    ]);

    woocommerce_wp_text_input([
        'id'          => '_kindertoys_play_time',
        'label'       => __('Play/build time', 'kindertoys-core'),
        'placeholder' => '~45 דקות',
    ]);

    if (function_exists('woocommerce_wp_textarea_input')) {
        woocommerce_wp_textarea_input([
            'id'          => '_kindertoys_highlights',
            'label'       => __('Product highlights', 'kindertoys-core'),
            'placeholder' => __("One highlight per line", 'kindertoys-core'),
            'desc_tip'    => true,
            'description' => __('Shown near the buy box.', 'kindertoys-core'),
        ]);

        woocommerce_wp_textarea_input([
            'id'          => '_kindertoys_in_box',
            'label'       => __('In the box', 'kindertoys-core'),
            'placeholder' => __("One item per line", 'kindertoys-core'),
            'desc_tip'    => true,
            'description' => __('Shown in the product details panel.', 'kindertoys-core'),
        ]);
    }

    echo '</div>';
}

add_action('woocommerce_admin_process_product_object', 'kindertoys_core_save_product_fields');
function kindertoys_core_save_product_fields(WC_Product $product): void
{
    $fields = [
        '_kindertoys_age',
        '_kindertoys_badge',
        '_kindertoys_brand_label',
        '_kindertoys_pieces',
        '_kindertoys_players',
        '_kindertoys_play_time',
    ];

    foreach ($fields as $field) {
        // WooCommerce validates the product edit nonce before this hook runs.
        if (isset($_POST[$field])) {
            $product->update_meta_data($field, sanitize_text_field(wp_unslash($_POST[$field])));
        }
    }

    foreach (['_kindertoys_highlights', '_kindertoys_in_box'] as $field) {
        // WooCommerce validates the product edit nonce before this hook runs.
        if (isset($_POST[$field])) {
            $product->update_meta_data($field, sanitize_textarea_field(wp_unslash($_POST[$field])));
        }
    }
}

function kindertoys_core_get_product_meta(WC_Product $product, string $key): string
{
    return (string) $product->get_meta('_kindertoys_' . $key, true);
}

function kindertoys_core_get_product_meta_lines(WC_Product $product, string $key): array
{
    $value = kindertoys_core_get_product_meta($product, $key);
    $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];

    return array_values(array_filter(array_map('trim', $lines)));
}
