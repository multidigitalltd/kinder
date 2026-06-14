<?php
/**
 * Performance and cache-friendly defaults.
 *
 * @package KinderToys
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

add_action('wp_enqueue_scripts', 'kindertoys_trim_core_assets', 100);
function kindertoys_trim_core_assets(): void
{
    if (! is_user_logged_in()) {
        wp_dequeue_style('classic-theme-styles');
        wp_dequeue_style('global-styles');
    }
}
