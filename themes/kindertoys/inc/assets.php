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
    return class_exists('WooCommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page());
}

function kindertoys_enqueue_inline_settings_css(): void
{
    $font = (string) kindertoys_setting('font_family', '"Ploni", "Arial", system-ui, sans-serif');

    if ('' === trim($font)) {
        return;
    }

    $font = preg_replace('/[^a-zA-Z0-9\s,"\-_(),]/', '', wp_strip_all_tags($font)) ?: '';

    if ('' === trim($font)) {
        return;
    }

    wp_add_inline_style('kindertoys-base', ':root{--kt-font:' . $font . ';}');
}
