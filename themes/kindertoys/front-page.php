<?php
/**
 * Front page.
 *
 * @package KinderToys
 */

declare(strict_types=1);

get_header();
?>
<main id="primary" class="site-main kt-home">
    <section class="kt-hero">
        <div class="kt-container kt-hero__grid">
            <div class="kt-hero__copy">
                <p class="kt-eyebrow"><?php esc_html_e('׳¢׳•׳׳ ׳©׳ ׳׳©׳—׳§׳™׳ ׳׳™׳׳“׳™׳', 'kindertoys'); ?></p>
                <h1><?php esc_html_e('KinderToys', 'kindertoys'); ?></h1>
                <p><?php esc_html_e('׳¦׳¢׳¦׳•׳¢׳™׳, ׳׳©׳—׳§׳™ ׳§׳•׳₪׳¡׳”, ׳™׳¦׳™׳¨׳” ׳•׳—׳–׳¨׳” ׳׳‘׳™׳× ׳¡׳₪׳¨ - ׳׳¡׳•׳“׳¨, ׳׳”׳™׳¨ ׳•׳ ׳’׳™׳©.', 'kindertoys'); ?></p>
                <div class="kt-hero__actions">
                    <a class="kt-button" href="<?php echo esc_url(home_url('/shop/')); ?>"><?php esc_html_e('׳׳›׳ ׳”׳׳•׳¦׳¨׳™׳', 'kindertoys'); ?></a>
                    <a class="kt-button kt-button--secondary" href="<?php echo esc_url(home_url('/product-category/sale/')); ?>"><?php esc_html_e('׳׳‘׳¦׳¢׳™׳ ׳—׳׳™׳', 'kindertoys'); ?></a>
                </div>
            </div>
            <div class="kt-hero__media" aria-hidden="true">
                <img src="<?php echo kindertoys_asset_uri('images/hero-kids.jpg'); ?>" alt="" width="720" height="480" loading="eager">
            </div>
        </div>
    </section>

    <section class="kt-container kt-trust-strip" aria-label="<?php esc_attr_e('׳™׳×׳¨׳•׳ ׳•׳× ׳”׳—׳ ׳•׳×', 'kindertoys'); ?>">
        <div><?php echo kindertoys_svg_icon('truck'); ?><span><?php esc_html_e('׳׳©׳׳•׳— ׳׳”׳™׳¨', 'kindertoys'); ?></span></div>
        <div><?php echo kindertoys_svg_icon('star'); ?><span><?php esc_html_e('׳׳•׳×׳’׳™׳ ׳׳•׳‘׳™׳׳™׳', 'kindertoys'); ?></span></div>
        <div><?php echo kindertoys_svg_icon('heart'); ?><span><?php esc_html_e('׳©׳™׳¨׳•׳× ׳׳™׳©׳™', 'kindertoys'); ?></span></div>
    </section>

    <?php if (class_exists('WooCommerce')) : ?>
        <section class="kt-container kt-section">
            <div class="kt-section__head">
                <p class="kt-eyebrow"><?php esc_html_e('׳§׳˜׳’׳•׳¨׳™׳•׳× ׳׳•׳‘׳™׳׳•׳×', 'kindertoys'); ?></p>
                <h2><?php esc_html_e('׳‘׳—׳¨׳• ׳׳× ׳”׳¢׳•׳׳ ׳”׳׳×׳׳™׳ ׳׳›׳', 'kindertoys'); ?></h2>
            </div>
            <?php echo do_shortcode('[kindertoys_categories limit="6"]'); ?>
        </section>

        <section class="kt-container kt-section">
            <div class="kt-section__head">
                <p class="kt-eyebrow"><?php esc_html_e('׳׳•׳¦׳¨׳™׳ ׳—׳׳™׳', 'kindertoys'); ?></p>
                <h2><?php esc_html_e('׳”׳ ׳‘׳—׳¨׳™׳ ׳©׳ ׳§׳™׳ ׳“׳™', 'kindertoys'); ?></h2>
            </div>
            <?php echo do_shortcode('[products limit="10" columns="5" orderby="popularity"]'); ?>
        </section>
    <?php endif; ?>
</main>
<?php
get_footer();
