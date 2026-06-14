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
        'font_family' => '"Ploni", "Arial", system-ui, sans-serif',
        'top_bar_text' => 'משלוח מהיר חינם מעל 299 ₪ | מועדון הלקוחות - 10% הנחה בקניה הראשונה',
        'phone' => '03-5293383',
        'whatsapp' => '97235293383',
        'search_placeholder' => 'חפשו משחקים, מותגים או קטגוריות...',
        'hero_eyebrow' => 'חדש בקינדר טויס - קולקציית 2026',
        'hero_title' => 'עולם של קסם, משחק ויצירה',
        'hero_title_accent' => 'בלחיצה אחת',
        'hero_text' => 'אלפי צעצועים, משחקים, חומרי יצירה וציוד לבית הספר ולגן - משלוח מהיר ושירות אישי מהלב.',
        'hero_primary_label' => 'לכל המבצעים החמים',
        'hero_primary_url' => '/product-category/sale/',
        'hero_secondary_label' => 'לכל המוצרים',
        'hero_secondary_url' => '/shop/',
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

    return wp_parse_args(is_array($settings) ? $settings : [], kindertoys_core_default_settings());
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

add_action('admin_init', 'kindertoys_core_register_settings');
function kindertoys_core_register_settings(): void
{
    register_setting('kindertoys_core_settings_group', KINDERTOYS_CORE_SETTINGS_OPTION, [
        'type' => 'array',
        'sanitize_callback' => 'kindertoys_core_sanitize_settings',
        'default' => kindertoys_core_default_settings(),
    ]);
}

function kindertoys_core_sanitize_settings(array $input): array
{
    $defaults = kindertoys_core_default_settings();
    $output = [];

    foreach ($defaults as $key => $default) {
        $value = $input[$key] ?? $default;

        if (str_ends_with($key, '_url') || str_ends_with($key, '_image')) {
            $output[$key] = esc_url_raw((string) $value);
            continue;
        }

        if ('font_family' === $key) {
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
                <?php kindertoys_core_text_field($settings, 'font_family', __('Font family', 'kindertoys-core'), 'Example: "Ploni", "Arial", system-ui, sans-serif'); ?>
                <?php kindertoys_core_text_field($settings, 'top_bar_text', __('Top bar text', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'phone', __('Phone', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'whatsapp', __('WhatsApp number', 'kindertoys-core'), 'International format, digits only. Example: 97235293383'); ?>
                <?php kindertoys_core_text_field($settings, 'search_placeholder', __('Search placeholder', 'kindertoys-core')); ?>
            </table>

            <h2><?php esc_html_e('Home hero', 'kindertoys-core'); ?></h2>
            <table class="form-table" role="presentation">
                <?php kindertoys_core_text_field($settings, 'hero_eyebrow', __('Hero eyebrow', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_title', __('Hero title', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_title_accent', __('Hero title accent', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_text', __('Hero text', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_primary_label', __('Primary button label', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_primary_url', __('Primary button URL', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_secondary_label', __('Secondary button label', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'hero_secondary_url', __('Secondary button URL', 'kindertoys-core')); ?>
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
