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

add_filter('script_loader_tag', 'kindertoys_defer_theme_scripts', 10, 3);
function kindertoys_defer_theme_scripts(string $tag, string $handle, string $src): string
{
    if ('kindertoys-theme' !== $handle || false !== strpos($tag, ' defer')) {
        return $tag;
    }

    return str_replace(' src=', ' defer src=', $tag);
}

add_filter('wp_resource_hints', 'kindertoys_resource_hints', 10, 2);
function kindertoys_resource_hints(array $urls, string $relation_type): array
{
    if ('preconnect' === $relation_type) {
        $urls[] = home_url();
    }

    return array_values(array_unique($urls));
}
