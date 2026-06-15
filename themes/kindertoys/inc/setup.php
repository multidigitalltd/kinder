<?php
/**
 * Theme setup.
 *
 * @package KinderToys
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', 'kindertoys_setup');
function kindertoys_setup(): void
{
    load_theme_textdomain('kindertoys', KINDERTOYS_THEME_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo', [
        'height'      => 96,
        'width'       => 260,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    add_image_size('kindertoys-card', 520, 520, true);
    add_image_size('kindertoys-hero', 1440, 720, true);

    register_nav_menus([
        'primary' => __('Primary menu', 'kindertoys'),
        'footer'  => __('Footer menu', 'kindertoys'),
    ]);
}

add_filter('body_class', 'kindertoys_body_classes');
function kindertoys_body_classes(array $classes): array
{
    $classes[] = 'kindertoys';
    $classes[] = 'kindertoys-rtl';

    if (function_exists('kindertoys_is_woocommerce_context') && kindertoys_is_woocommerce_context()) {
        $classes[] = 'kindertoys-woo';
    }

    return $classes;
}

add_action('admin_notices', 'kindertoys_core_version_notice');
function kindertoys_core_version_notice(): void
{
    if (! current_user_can('activate_plugins')) {
        return;
    }

    if (defined('KINDERTOYS_CORE_VERSION') && version_compare((string) KINDERTOYS_CORE_VERSION, KINDERTOYS_THEME_VERSION, '>=')) {
        return;
    }

    $message = defined('KINDERTOYS_CORE_VERSION')
        ? __('KinderToys Core is active but older than the theme. Upload the matching plugin ZIP manually.', 'kindertoys')
        : __('KinderToys Core is not active. Upload and activate the matching plugin ZIP manually.', 'kindertoys');

    echo '<div class="notice notice-warning"><p>' . esc_html($message) . '</p></div>';
}
