<?php
/**
 * Asset loading.
 *
 * @package KinderToys
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', 'kindertoys_enqueue_assets');
function kindertoys_enqueue_assets(): void
{
    kindertoys_enqueue_style('kindertoys-base', 'assets/css/base.css');
    kindertoys_enqueue_style('kindertoys-components', 'assets/css/components.css', ['kindertoys-base']);

    if (kindertoys_is_woocommerce_context()) {
        kindertoys_enqueue_style('kindertoys-woocommerce', 'assets/css/woocommerce.css', ['kindertoys-components']);
    }

    kindertoys_enqueue_inline_settings_css();
    kindertoys_enqueue_script('kindertoys-theme', 'assets/js/theme.js');
    wp_localize_script('kindertoys-theme', 'kindertoysAjax', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('kindertoys_ajax'),
        'cartUrl' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'),
        'checkoutUrl' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/'),
        'i18n' => [
            'searching' => __('מחפשים...', 'kindertoys'),
            'noResults' => __('לא נמצאו תוצאות', 'kindertoys'),
            'error' => __('משהו לא נטען. נסו שוב.', 'kindertoys'),
        ],
    ]);
}

add_action('wp_footer', 'kindertoys_print_ajax_settings', 5);
function kindertoys_print_ajax_settings(): void
{
    $settings = [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('kindertoys_ajax'),
        'cartUrl' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'),
        'checkoutUrl' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/'),
        'i18n' => [
            'searching' => __('מחפשים...', 'kindertoys'),
            'noResults' => __('לא נמצאו תוצאות', 'kindertoys'),
            'error' => __('משהו לא נטען. נסו שוב.', 'kindertoys'),
        ],
    ];

    echo '<script id="kindertoys-ajax-settings">window.kindertoysAjax=' . wp_json_encode($settings) . ';</script>' . "\n";
}

add_action('wp_head', 'kindertoys_preload_primary_font', 1);
function kindertoys_preload_primary_font(): void
{
    $font_url = (string) kindertoys_setting('body_font_regular_url', '');

    if ('' === $font_url || ! str_ends_with(strtolower($font_url), '.woff2')) {
        return;
    }

    echo '<link rel="preload" href="' . esc_url($font_url) . '" as="font" type="font/woff2" crossorigin>' . "\n";
}

function kindertoys_enqueue_style(string $handle, string $relative_path, array $deps = []): void
{
    $path = KINDERTOYS_THEME_DIR . '/' . $relative_path;
    $uri  = KINDERTOYS_THEME_URI . '/' . $relative_path;
    $ver  = file_exists($path) ? (string) filemtime($path) : KINDERTOYS_THEME_VERSION;

    wp_enqueue_style($handle, $uri, $deps, $ver);
}

function kindertoys_enqueue_script(string $handle, string $relative_path, array $deps = []): void
{
    $path = KINDERTOYS_THEME_DIR . '/' . $relative_path;
    $uri  = KINDERTOYS_THEME_URI . '/' . $relative_path;
    $ver  = file_exists($path) ? (string) filemtime($path) : KINDERTOYS_THEME_VERSION;

    wp_enqueue_script($handle, $uri, $deps, $ver, ['strategy' => 'defer', 'in_footer' => true]);
}

function kindertoys_is_woocommerce_context(): bool
{
    $is_product_search = is_search() && (! isset($_GET['post_type']) || 'product' === sanitize_key((string) wp_unslash($_GET['post_type'])));

    return class_exists('WooCommerce') && (is_front_page() || is_woocommerce() || is_cart() || is_checkout() || is_account_page() || $is_product_search);
}

function kindertoys_enqueue_inline_settings_css(): void
{
    $fallback = (string) kindertoys_setting('font_family', '"Ploni", "Arial", system-ui, sans-serif');
    $body_font = (string) kindertoys_setting('body_font_family', 'Ploni');
    $display_font = (string) kindertoys_setting('display_font_family', 'PloniYad');
    $display_semibold_url = (string) kindertoys_setting('display_font_semibold_url', '');
    $display_bold_url = (string) kindertoys_setting('display_font_bold_url', '');
    $display_black_url = (string) kindertoys_setting('display_font_black_url', '');

    $body_font = kindertoys_css_font_name($body_font, 'Ploni');
    $display_font = kindertoys_css_font_name($display_font, 'PloniYad');
    $fallback = kindertoys_css_font_stack($fallback, '"Ploni", "Arial", system-ui, sans-serif');
    $body_regular_url = (string) kindertoys_setting('body_font_regular_url', '');
    $body_medium_url = (string) kindertoys_setting('body_font_medium_url', '');
    $body_semibold_url = (string) kindertoys_setting('body_font_semibold_url', '');
    $body_bold_url = (string) kindertoys_setting('body_font_bold_url', '');
    $body_black_url = (string) kindertoys_setting('body_font_black_url', '');

    $css = '';
    $css .= kindertoys_font_face_css($body_font, $body_regular_url, 400);
    $css .= kindertoys_font_face_css($body_font, '' !== trim($body_medium_url) ? $body_medium_url : $body_regular_url, 500);
    $css .= kindertoys_font_face_css($body_font, '' !== trim($body_semibold_url) ? $body_semibold_url : ('' !== trim($body_bold_url) ? $body_bold_url : $body_regular_url), 600);
    $css .= kindertoys_font_face_css($body_font, '' !== trim($body_bold_url) ? $body_bold_url : ('' !== trim($body_semibold_url) ? $body_semibold_url : $body_regular_url), 700);
    $css .= kindertoys_font_face_css($body_font, '' !== trim($body_black_url) ? $body_black_url : ('' !== trim($body_bold_url) ? $body_bold_url : $body_regular_url), 900);
    $css .= kindertoys_font_face_css($display_font, $display_semibold_url, 700);
    $css .= kindertoys_font_face_css($display_font, $display_bold_url, 800);
    $css .= kindertoys_font_face_css($display_font, $display_black_url, 900);

    $has_display_heavy = '' !== trim($display_semibold_url . $display_bold_url . $display_black_url);
    $display_stack = $has_display_heavy ? '"' . $display_font . '","' . $body_font . '",' . $fallback : '"' . $body_font . '",' . $fallback;
    $css .= ':root{--kt-font:"' . $body_font . '",' . $fallback . ';--kt-display-font:' . $display_stack . ';}';

    wp_add_inline_style('kindertoys-base', $css);
}

function kindertoys_font_face_css(string $family, string $url, int $weight): string
{
    if ('' === trim($url)) {
        return '';
    }

    $url = esc_url_raw($url);
    $format = str_ends_with(strtolower($url), '.woff2') ? 'woff2' : 'woff';

    return '@font-face{font-family:"' . $family . '";src:url("' . esc_url($url) . '") format("' . $format . '");font-weight:' . $weight . ';font-style:normal;font-display:swap;}';
}

function kindertoys_css_font_name(string $value, string $fallback): string
{
    $value = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', wp_strip_all_tags($value)) ?: '';

    return '' !== trim($value) ? trim($value) : $fallback;
}

function kindertoys_css_font_stack(string $value, string $fallback): string
{
    $value = preg_replace('/[^a-zA-Z0-9\s,"\-_(),]/', '', wp_strip_all_tags($value)) ?: '';

    return '' !== trim($value) ? trim($value) : $fallback;
}
