<?php
/**
 * Error 404 template.
 *
 * @package KinderToys
 */

declare(strict_types=1);

get_header();
?>
<main id="primary" class="site-main kt-not-found">
    <section class="kt-container kt-404">
        <div class="kt-404__copy">
            <p class="kt-hero__pill"><span></span><?php esc_html_e('אופס, המשחק הזה התחבא', 'kindertoys'); ?></p>
            <h1><?php esc_html_e('404', 'kindertoys'); ?></h1>
            <h2><?php esc_html_e('לא מצאנו את העמוד שחיפשתם', 'kindertoys'); ?></h2>
            <p><?php esc_html_e('אפשר לחפש מוצר, לחזור לחנות או לקפוץ לקטגוריות הפופולריות ולהמשיך משם.', 'kindertoys'); ?></p>
            <form role="search" method="get" class="kt-404__search" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="kt-404-search"><?php esc_html_e('חיפוש מוצרים', 'kindertoys'); ?></label>
                <input id="kt-404-search" type="search" name="s" placeholder="<?php esc_attr_e('חפשו משחק, מותג או קטגוריה', 'kindertoys'); ?>">
                <input type="hidden" name="post_type" value="product">
                <button class="kt-button" type="submit"><?php esc_html_e('חיפוש', 'kindertoys'); ?></button>
            </form>
            <div class="kt-404__actions">
                <a class="kt-button" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('לדף הבית', 'kindertoys'); ?></a>
                <a class="kt-button kt-button--light" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>"><?php esc_html_e('לחנות', 'kindertoys'); ?></a>
            </div>
        </div>
        <div class="kt-404__media" aria-hidden="true">
            <img src="<?php echo kindertoys_asset_uri('images/mascot-point.png'); ?>" alt="" width="720" height="720" loading="eager">
        </div>
    </section>
    <?php if (taxonomy_exists('product_cat')) : ?>
        <section class="kt-container kt-section kt-404__categories">
            <div class="kt-section__head">
                <p class="kt-eyebrow"><?php esc_html_e('אולי זה כאן', 'kindertoys'); ?></p>
                <h2><?php esc_html_e('קטגוריות פופולריות', 'kindertoys'); ?></h2>
            </div>
            <?php echo do_shortcode('[kindertoys_categories limit="4"]'); ?>
        </section>
    <?php endif; ?>
</main>
<?php
get_footer();
