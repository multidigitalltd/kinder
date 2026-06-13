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

    echo '</div>';
}

add_action('woocommerce_admin_process_product_object', 'kindertoys_core_save_product_fields');
function kindertoys_core_save_product_fields(WC_Product $product): void
{
    $fields = [
        '_kindertoys_age',
        '_kindertoys_badge',
        '_kindertoys_brand_label',
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $product->update_meta_data($field, sanitize_text_field(wp_unslash($_POST[$field])));
        }
    }
}

function kindertoys_core_get_product_meta(WC_Product $product, string $key): string
{
    return (string) $product->get_meta('_kindertoys_' . $key, true);
}
