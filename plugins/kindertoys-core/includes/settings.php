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
        'body_font_regular_url' => '',
        'body_font_medium_url' => '',
        'body_font_semibold_url' => '',
        'body_font_bold_url' => '',
        'body_font_black_url' => '',
        'display_font_family' => 'PloniYad',
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
        'featured_products_mode' => 'popular',
        'featured_products_limit' => '10',
        'featured_category_ids' => [],
        'featured_product_ids' => [],
        'free_shipping_threshold' => '299',
        'schema_shipping_country' => 'IL',
        'schema_return_days' => '14',
        'saved_cart_webhook_url' => '',
        'saved_cart_email_to' => '',
        'checkout_bump_enabled' => '1',
        'checkout_bump_product_id' => '0',
        'checkout_bump_title' => 'רוצים להוסיף עוד משהו קטן?',
        'checkout_bump_text' => 'הוסיפו מוצר משלים להזמנה בלחיצה אחת, בלי לעזוב את התשלום.',
        'checkout_bump_discount_percent' => '0',
        'footer_about_title' => 'KinderToys',
        'footer_about_text' => 'משחקים, צעצועים ומוצרי יצירה לילדים - בחוויית קנייה מהירה, נגישה ושמחה.',
        'footer_service_title' => 'שירות לקוחות',
        'footer_contact_label' => 'צור קשר',
        'footer_contact_url' => '/contact/',
        'footer_shipping_label' => 'משלוחים והחזרות',
        'footer_shipping_url' => '/shipping-returns/',
        'footer_bottom_text' => '',
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

add_action('admin_enqueue_scripts', 'kindertoys_core_admin_assets');
function kindertoys_core_admin_assets(string $hook): void
{
    if ('toplevel_page_kindertoys-settings' !== $hook) {
        return;
    }

    wp_enqueue_media();
}

function kindertoys_core_sanitize_settings(mixed $input): array
{
    $input = is_array($input) ? $input : [];
    $defaults = kindertoys_core_default_settings();
    $output = [];

    foreach ($defaults as $key => $default) {
        $value = $input[$key] ?? $default;

        if (in_array($key, ['featured_category_ids', 'featured_product_ids'], true)) {
            $values = is_array($value) ? $value : [];
            $output[$key] = array_values(array_filter(array_map('absint', $values)));
            continue;
        }

        if ('featured_products_limit' === $key) {
            $output[$key] = (string) min(20, max(1, absint($value)));
            continue;
        }

        if (in_array($key, ['free_shipping_threshold', 'schema_return_days'], true)) {
            $output[$key] = (string) max(0, absint($value));
            continue;
        }

        if ('checkout_bump_product_id' === $key) {
            $output[$key] = (string) absint($value);
            continue;
        }

        if ('checkout_bump_discount_percent' === $key) {
            $output[$key] = (string) min(80, max(0, absint($value)));
            continue;
        }

        if ('checkout_bump_enabled' === $key) {
            $output[$key] = empty($value) ? '0' : '1';
            continue;
        }

        if ('featured_products_mode' === $key) {
            $allowed = ['popular', 'new', 'sale', 'category', 'manual'];
            $mode = sanitize_key((string) $value);
            $output[$key] = in_array($mode, $allowed, true) ? $mode : 'popular';
            continue;
        }

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
                <?php kindertoys_core_text_field($settings, 'body_font_regular_url', __('Body regular font URL - 400', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'body_font_medium_url', __('Body medium font URL - 500', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'body_font_semibold_url', __('Body semibold font URL - 600', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'body_font_bold_url', __('Body bold font URL - 700', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'body_font_black_url', __('Body black font URL - 900', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'display_font_family', __('Display/headings font name', 'kindertoys-core'), 'Example: PloniYad'); ?>
                <?php kindertoys_core_text_field($settings, 'display_font_semibold_url', __('Display semibold font URL - 700', 'kindertoys-core'), 'Only use a real semibold/bold heading font here. Do not upload a light file.'); ?>
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
                <?php kindertoys_core_featured_products_fields($settings); ?>
                <?php kindertoys_core_text_field($settings, 'free_shipping_threshold', __('Free shipping threshold', 'kindertoys-core'), 'Cart drawer and cart page progress amount. Use 0 to hide.'); ?>
                <?php kindertoys_core_text_field($settings, 'schema_shipping_country', __('Schema shipping country', 'kindertoys-core'), 'ISO country code, for example IL.'); ?>
                <?php kindertoys_core_text_field($settings, 'schema_return_days', __('Schema return days', 'kindertoys-core'), 'Used for Product merchant listing return policy.'); ?>
                <?php kindertoys_core_text_field($settings, 'saved_cart_webhook_url', __('Saved cart webhook URL', 'kindertoys-core'), 'Optional. Called when a customer saves a cart.'); ?>
                <?php kindertoys_core_text_field($settings, 'saved_cart_email_to', __('Saved cart email recipient', 'kindertoys-core'), 'Optional. Leave empty to use the site admin email.'); ?>
                <?php kindertoys_core_checkout_bump_fields($settings); ?>
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

            <h2><?php esc_html_e('Footer', 'kindertoys-core'); ?></h2>
            <table class="form-table" role="presentation">
                <?php kindertoys_core_text_field($settings, 'footer_about_title', __('Footer about title', 'kindertoys-core')); ?>
                <?php kindertoys_core_textarea_field($settings, 'footer_about_text', __('Footer about text', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'footer_service_title', __('Footer service title', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'footer_contact_label', __('Footer contact label', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'footer_contact_url', __('Footer contact URL', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'footer_shipping_label', __('Footer shipping label', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'footer_shipping_url', __('Footer shipping URL', 'kindertoys-core')); ?>
                <?php kindertoys_core_text_field($settings, 'footer_bottom_text', __('Footer bottom text', 'kindertoys-core'), 'Leave empty to show copyright and site name.'); ?>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <script>
        (() => {
            document.querySelectorAll('[data-kt-filter-select]').forEach((input) => {
                const select = document.getElementById(input.getAttribute('data-kt-filter-select'));
                const options = select ? Array.from(select.options) : [];
                input.addEventListener('input', () => {
                    const term = input.value.trim().toLowerCase();
                    options.forEach((option) => {
                        option.hidden = term !== '' && !option.text.toLowerCase().includes(term);
                    });
                });
            });

            document.querySelectorAll('[data-kt-media-target]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    if (!window.wp || !wp.media) {
                        return;
                    }
                    const input = document.getElementById(button.getAttribute('data-kt-media-target'));
                    const frame = wp.media({ title: button.textContent.trim(), multiple: false, library: { type: 'image' } });
                    frame.on('select', () => {
                        const attachment = frame.state().get('selection').first()?.toJSON();
                        if (input && attachment?.url) {
                            input.value = attachment.url;
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                    frame.open();
                });
            });
        })();
    </script>
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
            <?php if (str_ends_with($key, '_image') || 'hero_image_url' === $key) : ?>
                <button class="button" type="button" data-kt-media-target="<?php echo esc_attr($key); ?>"><?php esc_html_e('Upload image', 'kindertoys-core'); ?></button>
            <?php endif; ?>
            <?php if ('' !== $description) : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </td>
    </tr>
    <?php
}

function kindertoys_core_textarea_field(array $settings, string $key, string $label, string $description = ''): void
{
    $name = KINDERTOYS_CORE_SETTINGS_OPTION . '[' . $key . ']';
    ?>
    <tr>
        <th scope="row"><label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
        <td>
            <textarea class="large-text" rows="4" id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($name); ?>"><?php echo esc_textarea((string) ($settings[$key] ?? '')); ?></textarea>
            <?php if ('' !== $description) : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </td>
    </tr>
    <?php
}

function kindertoys_core_featured_products_fields(array $settings): void
{
    $option = KINDERTOYS_CORE_SETTINGS_OPTION;
    $mode = (string) ($settings['featured_products_mode'] ?? 'popular');
    $selected_categories = array_map('absint', (array) ($settings['featured_category_ids'] ?? []));
    $selected_products = array_map('absint', (array) ($settings['featured_product_ids'] ?? []));
    $categories = taxonomy_exists('product_cat') ? get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'number' => 100,
    ]) : [];
    $products = post_type_exists('product') ? get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 120,
        'orderby' => 'title',
        'order' => 'ASC',
        'fields' => 'ids',
    ]) : [];
    ?>
    <tr>
        <th scope="row"><label for="featured_products_mode"><?php esc_html_e('Featured products source', 'kindertoys-core'); ?></label></th>
        <td>
            <select id="featured_products_mode" name="<?php echo esc_attr($option); ?>[featured_products_mode]">
                <option value="popular" <?php selected($mode, 'popular'); ?>><?php esc_html_e('Popular / best sellers', 'kindertoys-core'); ?></option>
                <option value="new" <?php selected($mode, 'new'); ?>><?php esc_html_e('Newest products', 'kindertoys-core'); ?></option>
                <option value="sale" <?php selected($mode, 'sale'); ?>><?php esc_html_e('Sale products', 'kindertoys-core'); ?></option>
                <option value="category" <?php selected($mode, 'category'); ?>><?php esc_html_e('Selected categories', 'kindertoys-core'); ?></option>
                <option value="manual" <?php selected($mode, 'manual'); ?>><?php esc_html_e('Selected products', 'kindertoys-core'); ?></option>
            </select>
            <p class="description"><?php esc_html_e('Choose what appears in the Kinder favorites section without writing shortcodes.', 'kindertoys-core'); ?></p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="featured_products_limit"><?php esc_html_e('Number of products', 'kindertoys-core'); ?></label></th>
        <td><input type="number" min="1" max="20" id="featured_products_limit" name="<?php echo esc_attr($option); ?>[featured_products_limit]" value="<?php echo esc_attr((string) ($settings['featured_products_limit'] ?? '10')); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><label for="featured_category_ids"><?php esc_html_e('Featured categories', 'kindertoys-core'); ?></label></th>
        <td>
            <select id="featured_category_ids" name="<?php echo esc_attr($option); ?>[featured_category_ids][]" multiple size="8" style="min-width:320px;">
                <?php if (! is_wp_error($categories)) : ?>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo esc_attr((string) $category->term_id); ?>" <?php selected(in_array((int) $category->term_id, $selected_categories, true)); ?>><?php echo esc_html($category->name); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <p><input type="search" class="regular-text" data-kt-filter-select="featured_category_ids" placeholder="<?php esc_attr_e('Search categories', 'kindertoys-core'); ?>"></p>
            <p class="description"><?php esc_html_e('Used when source is Selected categories. Hold Ctrl/Cmd to choose more than one.', 'kindertoys-core'); ?></p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="featured_product_ids"><?php esc_html_e('Featured products', 'kindertoys-core'); ?></label></th>
        <td>
            <select id="featured_product_ids" name="<?php echo esc_attr($option); ?>[featured_product_ids][]" multiple size="10" style="min-width:420px;">
                <?php foreach ($products as $product_id) : ?>
                    <option value="<?php echo esc_attr((string) $product_id); ?>" <?php selected(in_array((int) $product_id, $selected_products, true)); ?>><?php echo esc_html(get_the_title($product_id)); ?></option>
                <?php endforeach; ?>
            </select>
            <p><input type="search" class="regular-text" data-kt-filter-select="featured_product_ids" placeholder="<?php esc_attr_e('Search products', 'kindertoys-core'); ?>"></p>
            <p class="description"><?php esc_html_e('Used when source is Selected products. Hold Ctrl/Cmd to choose more than one.', 'kindertoys-core'); ?></p>
        </td>
    </tr>
    <?php
}

function kindertoys_core_checkout_bump_fields(array $settings): void
{
    $option = KINDERTOYS_CORE_SETTINGS_OPTION;
    $product_id = absint($settings['checkout_bump_product_id'] ?? 0);
    $products = post_type_exists('product') ? get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 120,
        'orderby' => 'title',
        'order' => 'ASC',
        'fields' => 'ids',
    ]) : [];
    ?>
    <tr><th colspan="2"><h3><?php esc_html_e('Checkout order bump', 'kindertoys-core'); ?></h3></th></tr>
    <tr>
        <th scope="row"><?php esc_html_e('Enable checkout bump', 'kindertoys-core'); ?></th>
        <td>
            <label>
                <input type="hidden" name="<?php echo esc_attr($option); ?>[checkout_bump_enabled]" value="0">
                <input type="checkbox" name="<?php echo esc_attr($option); ?>[checkout_bump_enabled]" value="1" <?php checked((string) ($settings['checkout_bump_enabled'] ?? '1'), '1'); ?>>
                <?php esc_html_e('Show a lightweight upsell inside checkout', 'kindertoys-core'); ?>
            </label>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="checkout_bump_product_id"><?php esc_html_e('Bump product', 'kindertoys-core'); ?></label></th>
        <td>
            <select id="checkout_bump_product_id" name="<?php echo esc_attr($option); ?>[checkout_bump_product_id]" style="min-width:420px;">
                <option value="0"><?php esc_html_e('Choose product', 'kindertoys-core'); ?></option>
                <?php foreach ($products as $id) : ?>
                    <option value="<?php echo esc_attr((string) $id); ?>" <?php selected($product_id, (int) $id); ?>><?php echo esc_html(get_the_title($id)); ?></option>
                <?php endforeach; ?>
            </select>
            <p><input type="search" class="regular-text" data-kt-filter-select="checkout_bump_product_id" placeholder="<?php esc_attr_e('Search products', 'kindertoys-core'); ?>"></p>
        </td>
    </tr>
    <?php kindertoys_core_text_field($settings, 'checkout_bump_title', __('Bump title', 'kindertoys-core')); ?>
    <?php kindertoys_core_text_field($settings, 'checkout_bump_text', __('Bump text', 'kindertoys-core')); ?>
    <?php kindertoys_core_text_field($settings, 'checkout_bump_discount_percent', __('Bump discount percent', 'kindertoys-core'), '0 means no discount.'); ?>
    <?php
}
