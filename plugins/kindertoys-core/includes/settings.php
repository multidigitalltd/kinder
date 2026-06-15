<?php
/**
 * Store settings.
 *
 * @package KinderToysCore
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

const KINDERTOYS_CORE_SETTINGS_OPTION = 'kindertoys_core_settings';

function kindertoys_core_default_settings(): array
{
    return [
        'body_font_family' => 'Ploni',
        'body_font_light_url' => '',
        'body_font_regular_url' => '',
        'body_font_medium_url' => '',
        'body_font_semibold_url' => '',
        'body_font_bold_url' => '',
        'body_font_black_url' => '',
        'display_font_family' => 'PloniYad',
        'display_font_regular_url' => '',
        'display_font_semibold_url' => '',
        'display_font_bold_url' => '',
        'display_font_black_url' => '',
        'font_family' => '"Ploni", "Arial", system-ui, sans-serif',
        'top_bar_text' => 'משלוח מהיר חינם מעל 299 ₪ | מועדון הלקוחות - 10% הנחה בקניה הראשונה',
        'top_promo_1' => 'משלוח חינם בהזמנה מעל 299 ₪',
        'top_promo_2' => 'מועדון קינדי - 5% חזרה על כל קניה',
        'top_promo_3' => 'קולקציית חזרה לבית הספר 2026 נחתה',
        'top_promo_4' => 'תשלום מאובטח SSL + PCI',
        'top_promo_5' => 'שירות אישי 03-5293383',
        'phone' => '03-5293383',
        'whatsapp' => '97235293383',
        'search_placeholder' => 'חפשו משחקים, מותגים או קטגוריות...',
        'hero_eyebrow' => 'חדש בקינדר טויס - קולקציית 2026',
        'hero_title_prefix' => 'עולם של',
        'hero_title' => 'קסם, משחק ויצירה',
        'hero_title_accent' => 'בלחיצה אחת',
        'hero_text' => 'אלפי צעצועים, משחקים, חומרי יצירה וציוד לבית הספר ולגן - משלוח מהיר ושירות אישי מהלב.',
        'hero_primary_label' => 'לכל המבצעים החמים',
        'hero_primary_url' => '/product-category/sale/',
        'hero_secondary_label' => 'לכל המוצרים',
        'hero_secondary_url' => '/shop/',
        'hero_image_url' => '',
        'hero_rating_text' => '4.9',
        'hero_rating_suffix' => '(+50,000 הורים)',
        'hero_shipping_text' => 'משלוח מחר עד הבית',
        'hero_inventory_text' => '+10,000 מוצרים במלאי',
        'hero_float_top_text' => '+120 מוצרים חדשים',
        'hero_float_bottom_text' => 'חינם מעל 299 ₪',
        'categories_eyebrow' => 'קטגוריות מובילות',
        'categories_title' => 'בחרו את העולם המתאים לכם',
        'products_eyebrow' => 'מוצרים חמים',
        'products_title' => 'הנבחרים של קינדי',
        'featured_products_shortcode' => '[products limit="10" columns="5" orderby="popularity" stock_status="instock" visibility="visible"]',
        'age_eyebrow' => 'בוחרים לפי גיל',
        'age_title' => 'למצוא את המתנה המושלמת',
        'brands_eyebrow' => 'מותגים אהובים',
        'brands_title' => 'רק המקוריים והאיכותיים',
        'promo_section_eyebrow' => 'מבצעים חמים',
        'promo_section_title' => 'המבצעים של קינדי',
        'promo_1_badge' => 'מוגבל בזמן',
        'promo_1_title' => 'חזרה לבית הספר עד 40% הנחה',
        'promo_1_text' => 'ילקוטים, קלמרים, מחברות וכלי כתיבה במחירי השקה',
        'promo_1_url' => '/product-category/back-to-school/',
        'promo_1_image' => '',
        'promo_2_badge' => 'חדש בקינדי',
        'promo_2_title' => 'משחקים אלקטרוניים',
        'promo_2_text' => 'דגמים נבחרים במלאי',
        'promo_2_url' => '/shop/',
        'promo_2_image' => '',
        'promo_3_badge' => 'מחיר מיוחד',
        'promo_3_title' => 'בנייה ולגו בכל הסדרות',
        'promo_3_text' => 'מתנות שילדים באמת אוהבים',
        'promo_3_url' => '/product-category/building/',
        'promo_3_image' => '',
    ];
}

function kindertoys_core_get_settings(): array
{
    $settings = get_option(KINDERTOYS_CORE_SETTINGS_OPTION, []);

    $settings = wp_parse_args(is_array($settings) ? $settings : [], kindertoys_core_default_settings());

    if (str_starts_with((string) $settings['hero_title'], 'עולם של ')) {
        $settings['hero_title'] = trim((string) preg_replace('/^עולם של\s+/u', '', (string) $settings['hero_title']));
    }

    return $settings;
}

function kindertoys_core_get_setting(string $key, mixed $fallback = ''): mixed
{
    $settings = kindertoys_core_get_settings();

    return $settings[$key] ?? $fallback;
}

function kindertoys_core_tel_href(?string $phone = null): string
{
    $phone = $phone ?: (string) kindertoys_core_get_setting('phone');
    $phone = preg_replace('/[^\d+]/', '', $phone) ?: '';

    return '' !== $phone ? 'tel:' . $phone : '#';
}

function kindertoys_core_url(string $value): string
{
    if ('' === trim($value)) {
        return '#';
    }

    if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
        return $value;
    }

    return home_url('/' . ltrim($value, '/'));
}

add_action('admin_menu', 'kindertoys_core_register_settings_page');
function kindertoys_core_register_settings_page(): void
{
    add_menu_page(
        __('KinderToys', 'kindertoys-core'),
        __('KinderToys', 'kindertoys-core'),
        'manage_options',
        'kindertoys-settings',
        'kindertoys_core_render_settings_page',
        'dashicons-store',
        58
    );
}

add_filter('upload_mimes', 'kindertoys_core_allow_font_uploads');
function kindertoys_core_allow_font_uploads(array $mimes): array
{
    $mimes['woff'] = 'font/woff';
    $mimes['woff2'] = 'font/woff2';
    $mimes['ttf'] = 'font/ttf';
    $mimes['otf'] = 'font/otf';

    return $mimes;
}

add_action('admin_init', 'kindertoys_core_register_settings');
function kindertoys_core_register_settings(): void
{
    register_setting('kindertoys_core_settings_group', KINDERTOYS_CORE_SETTINGS_OPTION, [
        'type' => 'array',
        'sanitize_callback' => 'kindertoys_core_sanitize_settings',
        'default' => kindertoys_core_default_settings(),
    ]);
}

function kindertoys_core_sanitize_settings(mixed $input): array
{
    $input = is_array($input) ? $input : [];
    $defaults = kindertoys_core_default_settings();
    $output = [];

    foreach ($defaults as $key => $default) {
        $value = $input[$key] ?? $default;

        if (str_ends_with($key, '_url') || str_ends_with($key, '_image')) {
            $output[$key] = esc_url_raw((string) $value);
            continue;
        }

        if (in_array($key, ['font_family', 'body_font_family', 'display_font_family'], true)) {
            $output[$key] = sanitize_text_field((string) $value);
            continue;
        }

        $output[$key] = sanitize_text_field((string) $value);
    }

    return wp_parse_args($output, $defaults);
}

function kindertoys_core_render_settings_page(): void
{
    if (! current_user_can('manage_options')) {
        return;
    }

    $settings = kindertoys_core_get_settings();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('KinderToys Settings', 'kindertoys-core'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('kindertoys_core_settings_group'); ?>

            <h2><?php esc_html_e('Brand and header', 'kindertoys-core'); ?></h2>
            <table class="form-table" role="presentation">
                <?php kindertoys_core_text_field($settings, 'body_font_family', __('Body font name', 'kindertoys-core'), 'Example: Ploni'); ?>
                <?php kindertoys_core_text_field($settings, 'body_font_light_url', __('Body light font URL - 300', 'kindertoys-core'), 'Upload a .woff2/.woff file to Media Library and paste its URL here.'); ?>
                <?php kindertoys_core_text_field($settings, 'body_font_regular_url', __('Body regular font URL - 400', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'body_font_medium_url', __('Body medium font URL - 500', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'body_font_semibold_url', __('Body semibold font URL - 600', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'body_font_bold_url', __('Body bold font URL - 700', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'body_font_black_url', __('Body black font URL - 900', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'display_font_family', __('Display/headings font name', 'kindertoys-core'), 'Example: PloniYad'); ?>
                <?php kindertoys_core_text_field($settings, 'display_font_regular_url', __('Display regular font URL - 400', 'kindertoys-core'), 'Used for headings if provided.'); ?>
                <?php kindertoys_core_text_field($settings, 'display_font_semibold_url', __('Display semibold font URL - 600', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'display_font_bold_url', __('Display bold font URL - 800', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'display_font_black_url', __('Display black font URL - 900', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'font_family', __('Fallback font stack', 'kindertoys-core'), 'Example: "Ploni", "Arial", system-ui, sans-serif'); ?>
                <?php kindertoys_core_text_field($settings, 'top_bar_text', __('Top bar text', 'kindertoys-core')); ?>
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <?php kindertoys_core_text_field($settings, "top_promo_{$i}", sprintf(__('Top promo item %d', 'kindertoys-core'), $i)); ?>
                <?php endfor; ?>
                <?php kindertoys_core_text_field($settings, 'phone', __('Phone', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'whatsapp', __('WhatsApp number', 'kindertoys-core'), 'International format, digits only. Example: 97235293383'); ?>
                <?php kindertoys_core_text_field($settings, 'search_placeholder', __('Search placeholder', 'kindertoys-core')); ?>
            </table>

            <h2><?php esc_html_e('Home hero', 'kindertoys-core'); ?></h2>
            <table class="form-table" role="presentation">
                <?php kindertoys_core_text_field($settings, 'hero_eyebrow', __('Hero eyebrow', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_title_prefix', __('Hero title prefix', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_title', __('Hero highlighted title', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_title_accent', __('Hero title accent', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_text', __('Hero text', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_primary_label', __('Primary button label', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_primary_url', __('Primary button URL', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_secondary_label', __('Secondary button label', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_secondary_url', __('Secondary button URL', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_image_url', __('Hero image URL', 'kindertoys-core'), 'Leave empty to use the bundled Lovable image.'); ?>
                <?php kindertoys_core_text_field($settings, 'hero_rating_text', __('Hero rating text', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_rating_suffix', __('Hero rating suffix', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_shipping_text', __('Hero shipping proof text', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_inventory_text', __('Hero inventory proof text', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_float_top_text', __('Hero top floating label', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_float_bottom_text', __('Hero bottom floating label', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'categories_eyebrow', __('Categories eyebrow', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'categories_title', __('Categories title', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'products_eyebrow', __('Products eyebrow', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'products_title', __('Products title', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'featured_products_shortcode', __('Featured products shortcode', 'kindertoys-core'), 'Examples: [products ids="12,34,56" columns="5"] or [products category="lego" limit="10" columns="5"]'); ?>
                <?php kindertoys_core_text_field($settings, 'age_eyebrow', __('Age section eyebrow', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'age_title', __('Age section title', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'brands_eyebrow', __('Brands eyebrow', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'brands_title', __('Brands title', 'kindertoys-core')); ?>
            </table>

            <h2><?php esc_html_e('Promo banners', 'kindertoys-core'); ?></h2>
            <table class="form-table" role="presentation">
                <?php kindertoys_core_text_field($settings, 'promo_section_eyebrow', __('Promo section eyebrow', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'promo_section_title', __('Promo section title', 'kindertoys-core')); ?>
                <?php for ($i = 1; $i <= 3; $i++) : ?>
                    <tr><th colspan="2"><h3><?php echo esc_html(sprintf(__('Banner %d', 'kindertoys-core'), $i)); ?></h3></th></tr>
                    <?php kindertoys_core_text_field($settings, "promo_{$i}_badge", __('Badge', 'kindertoys-core')); ?>
                    <?php kindertoys_core_text_field($settings, "promo_{$i}_title", __('Title', 'kindertoys-core')); ?>
                    <?php kindertoys_core_text_field($settings, "promo_{$i}_text", __('Text', 'kindertoys-core')); ?>
                    <?php kindertoys_core_text_field($settings, "promo_{$i}_url", __('Link URL', 'kindertoys-core')); ?>
                    <?php kindertoys_core_text_field($settings, "promo_{$i}_image", __('Image URL', 'kindertoys-core'), 'Leave empty to use the bundled image.'); ?>
                <?php endfor; ?>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function kindertoys_core_text_field(array $settings, string $key, string $label, string $description = ''): void
{
    $name = KINDERTOYS_CORE_SETTINGS_OPTION . '[' . $key . ']';
    ?>
    <tr>
        <th scope="row"><label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
        <td>
            <input class="regular-text" type="text" id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr((string) ($settings[$key] ?? '')); ?>">
            <?php if ('' !== $description) : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </td>
    </tr>
    <?php
}
