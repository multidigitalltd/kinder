<?php
/**
 * KinderToys theme bootstrap.
 *
 * @package KinderToys
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('KINDERTOYS_THEME_VERSION', '0.3.2');
define('KINDERTOYS_THEME_DIR', get_template_directory());
define('KINDERTOYS_THEME_URI', get_template_directory_uri());

$kindertoys_files = [
    'inc/setup.php',
    'inc/assets.php',
    'inc/performance.php',
    'inc/template-tags.php',
    'inc/woocommerce.php',
];

foreach ($kindertoys_files as $kindertoys_file) {
    require_once KINDERTOYS_THEME_DIR . '/' . $kindertoys_file;
}
