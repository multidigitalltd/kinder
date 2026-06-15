<?php
/**
 * Search results template.
 *
 * @package KinderToys
 */

declare(strict_types=1);

get_header();

global $wp_query;

$search_query = get_search_query();
$is_product_search = class_exists('WooCommerce') && (! isset($_GET['post_type']) || 'product' === sanitize_key((string) wp_unslash($_GET['post_type'])));
?>
<main id="primary" class="site-main kt-container kt-search-page">
    <header class="kt-search-page__hero">
        <p class="kt-eyebrow"><?php esc_html_e('תוצאות חיפוש', 'kindertoys'); ?></p>
        <h1>
            <?php if ('' !== $search_query) : ?>
                <?php echo esc_html(sprintf(__('חיפשתם: %s', 'kindertoys'), $search_query)); ?>
            <?php else : ?>
                <?php esc_html_e('מה תרצו למצוא?', 'kindertoys'); ?>
            <?php endif; ?>
        </h1>
        <form role="search" method="get" class="kt-search-page__form" action="<?php echo esc_url(home_url('/')); ?>">
            <label class="screen-reader-text" for="kt-search-page-field"><?php esc_html_e('חיפוש מוצרים', 'kindertoys'); ?></label>
            <input id="kt-search-page-field" type="search" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="<?php esc_attr_e('חפשו משחק, מותג או קטגוריה', 'kindertoys'); ?>">
            <input type="hidden" name="post_type" value="product">
            <button class="kt-button" type="submit"><?php esc_html_e('חיפוש', 'kindertoys'); ?></button>
        </form>
    </header>

    <?php if ($is_product_search && function_exists('woocommerce_product_loop_start')) : ?>
        <?php if (have_posts()) : ?>
            <section class="kt-search-page__results" aria-label="<?php esc_attr_e('מוצרים שנמצאו', 'kindertoys'); ?>">
                <div class="kt-search-page__meta">
                    <strong><?php echo esc_html(sprintf(_n('נמצא מוצר אחד', 'נמצאו %s מוצרים', (int) $wp_query->found_posts, 'kindertoys'), number_format_i18n((int) $wp_query->found_posts))); ?></strong>
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('לכל החנות', 'kindertoys'); ?></a>
                </div>
                <?php woocommerce_product_loop_start(); ?>
                <?php while (have_posts()) : ?>
                    <?php the_post(); ?>
                    <?php wc_get_template_part('content', 'product'); ?>
                <?php endwhile; ?>
                <?php woocommerce_product_loop_end(); ?>
                <?php the_posts_pagination(); ?>
            </section>
        <?php else : ?>
            <section class="kt-search-page__empty">
                <?php echo kindertoys_svg_icon('search'); ?>
                <h2><?php esc_html_e('לא מצאנו מוצר שמתאים לחיפוש הזה', 'kindertoys'); ?></h2>
                <p><?php esc_html_e('אפשר לנסות מילה קצרה יותר, שם מותג, או לקפוץ לקטגוריות הפופולריות.', 'kindertoys'); ?></p>
                <div class="kt-404__actions">
                    <a class="kt-button" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('לכל החנות', 'kindertoys'); ?></a>
                    <a class="kt-button kt-button--light" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('לדף הבית', 'kindertoys'); ?></a>
                </div>
            </section>
            <section class="kt-section kt-search-page__categories">
                <div class="kt-section__head">
                    <p class="kt-eyebrow"><?php esc_html_e('אולי זה כאן', 'kindertoys'); ?></p>
                    <h2><?php esc_html_e('קטגוריות פופולריות', 'kindertoys'); ?></h2>
                </div>
                <?php echo do_shortcode('[kindertoys_categories limit="6"]'); ?>
            </section>
        <?php endif; ?>
    <?php else : ?>
        <?php if (have_posts()) : ?>
            <section class="kt-content">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('kt-entry'); ?>>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php the_excerpt(); ?>
                    </article>
                <?php endwhile; ?>
                <?php the_posts_pagination(); ?>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php
get_footer();
