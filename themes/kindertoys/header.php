<?php
/**
 * Site header.
 *
 * @package KinderToys
 */

declare(strict_types=1);
?><!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#primary"><?php esc_html_e('דלג לתוכן', 'kindertoys'); ?></a>

<header class="kt-header" data-site-header>
    <div class="kt-top-promos" aria-label="<?php esc_attr_e('עדכונים ומבצעים', 'kindertoys'); ?>">
        <span><?php echo kindertoys_svg_icon('gift'); ?><?php esc_html_e('מועדון קינדי - 5% חזרה על כל קניה', 'kindertoys'); ?></span>
        <span><?php echo kindertoys_svg_icon('spark'); ?><?php esc_html_e('קולקציית חזרה לבית הספר 2026 נחתה', 'kindertoys'); ?></span>
        <span><?php echo kindertoys_svg_icon('shield'); ?><?php esc_html_e('תשלום מאובטח PCI + SSL', 'kindertoys'); ?></span>
    </div>
    <div class="kt-shipping-bar">
        <span class="kt-icon"><?php echo kindertoys_svg_icon('truck'); ?></span>
        <span><?php echo esc_html((string) kindertoys_setting('top_bar_text', 'משלוח מהיר חינם מעל 299 ₪ | מועדון הלקוחות - 10% הנחה בקניה הראשונה')); ?></span>
    </div>

    <div class="kt-container kt-header__main">
        <button class="kt-icon-button kt-header__menu-toggle" type="button" data-menu-toggle aria-expanded="false" aria-controls="site-navigation">
            <span class="screen-reader-text"><?php esc_html_e('פתח תפריט', 'kindertoys'); ?></span>
            <?php echo kindertoys_svg_icon('menu'); ?>
        </button>

        <a class="kt-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
            <?php
            if (has_custom_logo()) {
                the_custom_logo();
            } else {
                echo '<img src="' . kindertoys_asset_uri('images/logo.png') . '" alt="' . esc_attr(get_bloginfo('name')) . '" width="190" height="58">';
            }
            ?>
        </a>

        <form role="search" method="get" class="kt-search" action="<?php echo esc_url(home_url('/')); ?>">
            <label class="screen-reader-text" for="kt-search-field"><?php esc_html_e('חיפוש מוצרים', 'kindertoys'); ?></label>
            <span class="kt-search__icon"><?php echo kindertoys_svg_icon('search'); ?></span>
            <input id="kt-search-field" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php echo esc_attr((string) kindertoys_setting('search_placeholder', 'חפשו משחקים, מותגים או קטגוריות...')); ?>">
            <input type="hidden" name="post_type" value="product">
            <button class="kt-button kt-search__submit" type="submit"><?php esc_html_e('חיפוש', 'kindertoys'); ?></button>
        </form>

        <div class="kt-header__actions">
            <a class="kt-phone-link" href="<?php echo kindertoys_phone_href(); ?>"><?php echo kindertoys_svg_icon('phone'); ?><span><?php echo esc_html((string) kindertoys_setting('phone', '03-5293383')); ?></span></a>
            <a class="kt-icon-button" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : wp_login_url()); ?>" aria-label="<?php esc_attr_e('התחברות', 'kindertoys'); ?>">
                <?php echo kindertoys_svg_icon('user'); ?>
            </a>
            <a class="kt-icon-button" href="<?php echo esc_url(home_url('/wishlist/')); ?>" aria-label="<?php esc_attr_e('מועדפים', 'kindertoys'); ?>">
                <?php echo kindertoys_svg_icon('heart'); ?>
            </a>
            <a class="kt-cart-link" href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')); ?>" aria-label="<?php esc_attr_e('סל קניות', 'kindertoys'); ?>">
                <span class="kt-cart-link__text">
                    <span><?php esc_html_e('סל הקניות', 'kindertoys'); ?></span>
                    <strong class="kt-cart-total" data-cart-total></strong>
                </span>
                <span class="kt-cart-link__icon">
                    <?php echo kindertoys_svg_icon('cart'); ?>
                    <span class="kt-cart-count" data-cart-count>0</span>
                </span>
            </a>
        </div>
    </div>

    <nav id="site-navigation" class="kt-nav" data-site-nav aria-label="<?php esc_attr_e('תפריט ראשי', 'kindertoys'); ?>">
        <div class="kt-container kt-nav__inner">
            <div class="kt-nav__mobile-head">
                <strong><?php esc_html_e('תפריט', 'kindertoys'); ?></strong>
                <button class="kt-icon-button" type="button" data-menu-close aria-label="<?php esc_attr_e('סגור תפריט', 'kindertoys'); ?>"><?php echo kindertoys_svg_icon('close'); ?></button>
            </div>
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'kt-nav__list',
                'fallback_cb'    => 'kindertoys_default_menu',
                'depth'          => 3,
            ]);
            ?>
        </div>
    </nav>
    <div class="kt-nav-backdrop" data-menu-close></div>
</header>
