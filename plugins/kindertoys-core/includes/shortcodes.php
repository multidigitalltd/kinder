<?php
/**
 * Store shortcodes.
 *
 * @package KinderToysCore
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_shortcode('kindertoys_categories', 'kindertoys_core_categories_shortcode');
function kindertoys_core_categories_shortcode(array $atts): string
{
    if (! taxonomy_exists('product_cat')) {
        return '';
    }

    $atts = shortcode_atts([
        'limit' => 6,
    ], $atts, 'kindertoys_categories');

    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'number'     => max(1, (int) $atts['limit']),
        'parent'     => 0,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return '';
    }

    ob_start();
    ?>
    <div class="kt-category-grid">
        <?php foreach ($terms as $term) : ?>
            <?php
            $thumbnail_id = (int) get_term_meta($term->term_id, 'thumbnail_id', true);
            $image        = $thumbnail_id ? wp_get_attachment_image($thumbnail_id, 'kindertoys-card', false, ['loading' => 'lazy']) : '';
            ?>
            <a class="kt-category-card" href="<?php echo esc_url(get_term_link($term)); ?>">
                <span class="kt-category-card__image">
                    <?php echo $image ?: '<span class="kt-category-card__placeholder" aria-hidden="true"></span>'; ?>
                </span>
                <span class="kt-category-card__body">
                    <strong><?php echo esc_html($term->name); ?></strong>
                    <span><?php echo esc_html(sprintf(_n('%s מוצר', '%s מוצרים', (int) $term->count, 'kindertoys-core'), number_format_i18n((int) $term->count))); ?></span>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}
