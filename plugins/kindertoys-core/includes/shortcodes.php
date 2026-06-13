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
                    <span><?php echo esc_html(sprintf(_n('%s product', '%s products', (int) $term->count, 'kindertoys-core'), number_format_i18n((int) $term->count))); ?></span>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}

add_action('wp_enqueue_scripts', 'kindertoys_core_category_styles');
function kindertoys_core_category_styles(): void
{
    $css = '
    .kt-category-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:1rem}
    .kt-category-card{overflow:hidden;border:1px solid var(--kt-border,#e5e9f2);border-radius:12px;background:#fff;color:var(--kt-navy,#18336d);transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease}
    .kt-category-card:hover{border-color:rgba(217,39,50,.3);box-shadow:var(--kt-shadow,0 12px 32px rgba(15,35,70,.08));transform:translateY(-2px)}
    .kt-category-card__image{display:block;aspect-ratio:1;overflow:hidden;background:var(--kt-blue-soft,#e7f1ff)}
    .kt-category-card__image img{width:100%;height:100%;object-fit:cover}
    .kt-category-card__placeholder{display:block;width:100%;height:100%;background:linear-gradient(135deg,#e7f1ff,#fde8ea)}
    .kt-category-card__body{display:grid;gap:.25rem;padding:.85rem;text-align:center}
    .kt-category-card__body strong{font-size:1rem;font-weight:900}
    .kt-category-card__body span{color:var(--kt-muted,#647089);font-size:.82rem;font-weight:700}
    @media(max-width:1000px){.kt-category-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
    @media(max-width:640px){.kt-category-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem}}
    ';

    wp_register_style('kindertoys-core-inline', false, [], KINDERTOYS_CORE_VERSION);
    wp_enqueue_style('kindertoys-core-inline');
    wp_add_inline_style('kindertoys-core-inline', $css);
}
