<?php
/**
 * Out-of-stock product waitlist.
 *
 * @package KinderToysCore
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

const KINDERTOYS_CORE_WAITLIST_OPTION = 'kindertoys_core_waitlist';

function kindertoys_core_get_waitlist(): array
{
    $waitlist = get_option(KINDERTOYS_CORE_WAITLIST_OPTION, []);

    return is_array($waitlist) ? $waitlist : [];
}

function kindertoys_core_get_waitlist_for_product(int $product_id): array
{
    $waitlist = kindertoys_core_get_waitlist();
    $items = $waitlist[$product_id] ?? [];

    return is_array($items) ? $items : [];
}

function kindertoys_core_waitlist_add(int $product_id, string $name, string $email): bool
{
    $product = wc_get_product($product_id);
    if (! $product instanceof WC_Product || $product->is_in_stock() || ! is_email($email)) {
        return false;
    }

    $waitlist = kindertoys_core_get_waitlist();
    $items = kindertoys_core_get_waitlist_for_product($product_id);
    $email_key = sanitize_email($email);

    $items[$email_key] = [
        'name' => sanitize_text_field($name),
        'email' => $email_key,
        'created_at' => time(),
        'notified_at' => 0,
    ];

    $waitlist[$product_id] = $items;

    return update_option(KINDERTOYS_CORE_WAITLIST_OPTION, $waitlist, false);
}

add_action('wp_ajax_kindertoys_waitlist_signup', 'kindertoys_core_ajax_waitlist_signup');
add_action('wp_ajax_nopriv_kindertoys_waitlist_signup', 'kindertoys_core_ajax_waitlist_signup');
function kindertoys_core_client_ip(): string
{
    if (class_exists('WC_Geolocation')) {
        $ip = WC_Geolocation::get_ip_address();
        if ('' !== $ip) {
            return $ip;
        }
    }

    return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
}

/**
 * Lightweight per-IP rate limit for public AJAX actions.
 *
 * @return bool True when the request is allowed, false when the limit is reached.
 */
function kindertoys_core_rate_limit(string $bucket, int $limit, int $window): bool
{
    $key = 'kt_rl_' . md5($bucket . '|' . kindertoys_core_client_ip());
    $count = (int) get_transient($key);

    if ($count >= $limit) {
        return false;
    }

    set_transient($key, $count + 1, $window);

    return true;
}

function kindertoys_core_ajax_waitlist_signup(): void
{
    check_ajax_referer('kindertoys_ajax', 'nonce');

    // Honeypot: a real user never fills this hidden field; reject silently so bots get a success-looking reply.
    if (! empty($_POST['kt_hp_url'])) {
        wp_send_json_success(['message' => __('נרשמתם. נעדכן אתכם כשהמוצר יחזור למלאי.', 'kindertoys-core')]);
    }

    if (! kindertoys_core_rate_limit('waitlist', 5, 10 * MINUTE_IN_SECONDS)) {
        wp_send_json_error(['message' => __('יותר מדי בקשות. נסו שוב בעוד כמה דקות.', 'kindertoys-core')], 429);
    }

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash((string) $_POST['name'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash((string) $_POST['email'])) : '';
    $terms = isset($_POST['terms']) && '1' === (string) wp_unslash($_POST['terms']);

    if ($product_id <= 0 || '' === $name || ! is_email($email)) {
        wp_send_json_error(['message' => __('נא למלא שם ואימייל תקין.', 'kindertoys-core')], 400);
    }

    if (! $terms) {
        wp_send_json_error(['message' => __('צריך לאשר קבלת עדכון במייל כדי להירשם.', 'kindertoys-core')], 400);
    }

    if (! kindertoys_core_waitlist_add($product_id, $name, $email)) {
        wp_send_json_error(['message' => __('לא ניתן להצטרף לרשימת ההמתנה למוצר הזה כרגע.', 'kindertoys-core')], 400);
    }

    wp_send_json_success(['message' => __('נרשמתם. נעדכן אתכם כשהמוצר יחזור למלאי.', 'kindertoys-core')]);
}

add_action('woocommerce_product_set_stock_status', 'kindertoys_core_notify_waitlist_on_stock', 10, 3);
function kindertoys_core_notify_waitlist_on_stock(int $product_id, string $stock_status, WC_Product $product): void
{
    if ('instock' !== $stock_status) {
        return;
    }

    $waitlist = kindertoys_core_get_waitlist();
    $items = kindertoys_core_get_waitlist_for_product($product_id);
    if (empty($items)) {
        return;
    }

    $product_name = $product->get_name();
    $product_url = get_permalink($product_id);
    $site = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
    $subject_template = (string) kindertoys_core_get_setting('waitlist_email_subject', 'המוצר {product} חזר למלאי');
    $body_template = (string) kindertoys_core_get_setting('waitlist_email_body', "היי {name},\n\nהמוצר {product} חזר למלאי ואפשר להשלים הזמנה כאן:\n{url}");

    foreach ($items as $email => $item) {
        if (! empty($item['notified_at']) || ! is_email((string) $email)) {
            continue;
        }

        $name = trim((string) ($item['name'] ?? ''));
        $replacements = [
            '{name}' => '' !== $name ? $name : __('לקוח/ה', 'kindertoys-core'),
            '{product}' => $product_name,
            '{url}' => $product_url,
            '{site}' => $site,
        ];
        $subject = strtr($subject_template, $replacements);
        $message = strtr($body_template, $replacements);

        wp_mail((string) $email, $subject, $message);
        $items[$email]['notified_at'] = time();
    }

    $waitlist[$product_id] = $items;
    update_option(KINDERTOYS_CORE_WAITLIST_OPTION, $waitlist, false);
}

add_action('admin_menu', 'kindertoys_core_register_waitlist_page');
function kindertoys_core_register_waitlist_page(): void
{
    add_submenu_page(
        'kindertoys-settings',
        __('רשימת המתנה', 'kindertoys-core'),
        __('רשימת המתנה', 'kindertoys-core'),
        'manage_woocommerce',
        'kindertoys-waitlist',
        'kindertoys_core_render_waitlist_page'
    );
}

function kindertoys_core_render_waitlist_page(): void
{
    if (! current_user_can('manage_woocommerce')) {
        return;
    }

    $waitlist = kindertoys_core_get_waitlist();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('רשימת המתנה למוצרים שאזלו', 'kindertoys-core'); ?></h1>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('מוצר', 'kindertoys-core'); ?></th>
                    <th><?php esc_html_e('שם', 'kindertoys-core'); ?></th>
                    <th><?php esc_html_e('אימייל', 'kindertoys-core'); ?></th>
                    <th><?php esc_html_e('נרשם', 'kindertoys-core'); ?></th>
                    <th><?php esc_html_e('נשלחה הודעה', 'kindertoys-core'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($waitlist)) : ?>
                    <tr><td colspan="5"><?php esc_html_e('עדיין אין נרשמים.', 'kindertoys-core'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($waitlist as $product_id => $items) : ?>
                    <?php foreach ((array) $items as $item) : ?>
                        <tr>
                            <td><a href="<?php echo esc_url(get_edit_post_link((int) $product_id)); ?>"><?php echo esc_html(get_the_title((int) $product_id)); ?></a></td>
                            <td><?php echo esc_html((string) ($item['name'] ?? '')); ?></td>
                            <td><a href="mailto:<?php echo esc_attr((string) ($item['email'] ?? '')); ?>"><?php echo esc_html((string) ($item['email'] ?? '')); ?></a></td>
                            <td><?php echo ! empty($item['created_at']) ? esc_html(wp_date('d/m/Y H:i', (int) $item['created_at'])) : ''; ?></td>
                            <td><?php echo ! empty($item['notified_at']) ? esc_html(wp_date('d/m/Y H:i', (int) $item['notified_at'])) : esc_html__('לא', 'kindertoys-core'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
