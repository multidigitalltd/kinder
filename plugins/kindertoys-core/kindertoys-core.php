<?php
/**
 * Plugin Name: KinderToys Core
 * Description: Store-specific functionality for KinderToys. Keeps business utilities outside the theme.
 * Version: 0.1.0
 * Author: Multidigital
 * Text Domain: kindertoys-core
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('KINDERTOYS_CORE_VERSION', '0.1.0');
define('KINDERTOYS_CORE_DIR', plugin_dir_path(__FILE__));

require_once KINDERTOYS_CORE_DIR . 'includes/product-meta.php';
require_once KINDERTOYS_CORE_DIR . 'includes/shortcodes.php';
