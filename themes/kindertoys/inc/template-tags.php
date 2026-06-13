<?php
/**
 * Template helpers.
 *
 * @package KinderToys
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

function kindertoys_asset_uri(string $path): string
{
    return esc_url(KINDERTOYS_THEME_URI . '/assets/' . ltrim($path, '/'));
}

function kindertoys_cart_count(): int
{
    if (! function_exists('WC') || ! WC()->cart) {
        return 0;
    }

    return (int) WC()->cart->get_cart_contents_count();
}

function kindertoys_cart_total(): string
{
    if (! function_exists('WC') || ! WC()->cart) {
        return '';
    }

    return wp_kses_post(WC()->cart->get_cart_total());
}

function kindertoys_svg_icon(string $name): string
{
    $icons = [
        'search' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.3-4.3m1.3-5.2a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>',
        'cart'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6h15l-1.7 8.5a2 2 0 0 1-2 1.5H9a2 2 0 0 1-2-1.6L5 3H2"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/></svg>',
        'user'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>',
        'heart'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"/></svg>',
        'menu'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
        'close'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>',
        'truck'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg>',
        'star'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9Z"/></svg>',
    ];

    return $icons[$name] ?? '';
}

function kindertoys_default_menu(): void
{
    $items = [
        home_url('/shop/') => __('׳›׳ ׳”׳׳•׳¦׳¨׳™׳', 'kindertoys'),
        home_url('/product-category/toys/') => __('׳¦׳¢׳¦׳•׳¢׳™׳', 'kindertoys'),
        home_url('/product-category/board-games/') => __('׳׳©׳—׳§׳™ ׳§׳•׳₪׳¡׳”', 'kindertoys'),
        home_url('/product-category/back-to-school/') => __('׳—׳–׳¨׳” ׳׳‘׳™׳× ׳¡׳₪׳¨', 'kindertoys'),
        home_url('/product-category/sale/') => __('׳׳‘׳¦׳¢׳™׳ ׳—׳׳™׳', 'kindertoys'),
    ];

    echo '<ul class="kt-nav__list">';
    foreach ($items as $url => $label) {
        echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
    }
    echo '</ul>';
}
