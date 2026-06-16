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

/**
 * Waitlist entries are stored per product in post meta (not in one global
 * option), so a signup only reads/writes that product's small list. This
 * avoids the unbounded single blob and the cross-product race that could drop
 * unrelated entries.
 */
const KINDERTOYS_CORE_WAITLIST_META = '_kindertoys_waitlist';

function kindertoys_core_get_waitlist_for_product(int $product_id): array
{
    $items = get_post_meta($product_id, KINDERTOYS_CORE_WAITLIST_META, true);

    return is_array($items) ? $items : [];
}

function kindertoys_core_save_waitlist_for_product(int $product_id, array $items): void
{
    if (empty($items)) {
        delete_post_meta($product_id, KINDERTOYS_CORE_WAITLIST_META);

        return;
    }

    update_post_meta($product_id, KINDERTOYS_CORE_WAITLIST_META, $items);
}

function kindertoys_core_waitlist_add(int $product_id, string $name, string $email): bool
{
    $product = wc_get_product($product_id);
    if (! $product instanceof WC_Product || $product->is_in_stock() || ! is_email($email)) {
        return false;
    }

    $items = kindertoys_core_get_waitlist_for_product($product_id);
    $email_key = sanitize_email($email);

    $items[$email_key] = [
        'name' => sanitize_text_field($name),
        'email' => $email_key,
        'created_at' => time(),
        'notified_at' => 0,
    ];

    kindertoys_core_save_waitlist_for_product($product_id, $items);

    return true;
}

function kindertoys_core_client_ip(): string
{
    // Use the TCP peer (REMOTE_ADDR) only. Proxy headers such as
    // X-Forwarded-For / X-Real-IP are client-spoofable, so trusting them would
    // let a bot rotate the value and mint a fresh rate-limit key per request.
    // Behind a CDN, restore the real visitor IP into REMOTE_ADDR at the edge.
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

add_action('wp_ajax_kindertoys_waitlist_signup', 'kindertoys_core_ajax_waitlist_signup');
add_action('wp_ajax_nopriv_kindertoys_waitlist_signup', 'kindertoys_core_ajax_waitlist_signup');
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

/**
 * When a product comes back in stock, queue a background job instead of
 * sending every email inline (which would block the request that flipped the
 * stock status, e.g. an order, restock or CSV import).
 */
add_action('woocommerce_product_set_stock_status', 'kindertoys_core_queue_waitlist_notify', 10, 2);
function kindertoys_core_queue_waitlist_notify(int $product_id, string $stock_status): void
{
    if ('instock' !== $stock_status) {
        return;
    }

    if (empty(kindertoys_core_get_waitlist_for_product($product_id))) {
        return;
    }

    kindertoys_core_schedule_waitlist_batch($product_id);
}

function kindertoys_core_schedule_waitlist_batch(int $product_id): void
{
    $args = [$product_id];

    if (function_exists('as_enqueue_async_action') && function_exists('as_next_scheduled_action')) {
        if (! as_next_scheduled_action('kindertoys_core_waitlist_batch', $args, 'kindertoys')) {
            as_enqueue_async_action('kindertoys_core_waitlist_batch', $args, 'kindertoys');
        }

        return;
    }

    if (! wp_next_scheduled('kindertoys_core_waitlist_batch', $args)) {
        wp_schedule_single_event(time() + 30, 'kindertoys_core_waitlist_batch', $args);
    }
}

add_action('kindertoys_core_waitlist_batch', 'kindertoys_core_run_waitlist_batch', 10, 1);
function kindertoys_core_run_waitlist_batch($product_id): void
{
    $product_id = absint($product_id);
    $product = $product_id > 0 ? wc_get_product($product_id) : null;
    if (! $product instanceof WC_Product || ! $product->is_in_stock()) {
        return;
    }

    $items = kindertoys_core_get_waitlist_for_product($product_id);
    if (empty($items)) {
        return;
    }

    $product_name = $product->get_name();
    $product_url = get_permalink($product_id);
    $site = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
    $subject_template = (string) kindertoys_core_get_setting('waitlist_email_subject', 'המוצר {product} חזר למלאי');
    $body_template = (string) kindertoys_core_get_setting('waitlist_email_body', "היי {name},\n\nהמוצר {product} חזר למלאי ואפשר להשלים הזמנה כאן:\n{url}");

    $batch_limit = 25;
    $sent = 0;
    $remaining = 0;

    foreach ($items as $email => $item) {
        if (! empty($item['notified_at']) || ! is_email((string) $email)) {
            continue;
        }

        if ($sent >= $batch_limit) {
            $remaining++;
            continue;
        }

        $name = trim((string) ($item['name'] ?? ''));
        $replacements = [
            '{name}' => '' !== $name ? $name : __('לקוח/ה', 'kindertoys-core'),
            '{product}' => $product_name,
            '{url}' => $product_url,
            '{site}' => $site,
        ];

        wp_mail((string) $email, strtr($subject_template, $replacements), strtr($body_template, $replacements));
        $items[$email]['notified_at'] = time();
        $sent++;
    }

    kindertoys_core_save_waitlist_for_product($product_id, $items);

    // More recipients than the batch cap: schedule another pass.
    if ($remaining > 0) {
        kindertoys_core_schedule_waitlist_batch($product_id);
    }
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

    $product_ids = get_posts([
        'post_type'              => 'product',
        'post_status'            => 'any',
        'posts_per_page'         => 200,
        'fields'                 => 'ids',
        'meta_key'               => KINDERTOYS_CORE_WAITLIST_META,
        'no_found_rows'          => true,
        'update_post_term_cache' => false,
    ]);
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
                <?php if (empty($product_ids)) : ?>
                    <tr><td colspan="5"><?php esc_html_e('עדיין אין נרשמים.', 'kindertoys-core'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($product_ids as $product_id) : ?>
                    <?php foreach (kindertoys_core_get_waitlist_for_product((int) $product_id) as $item) : ?>
                        <tr>
                            <td><a href="<?php echo esc_url((string) get_edit_post_link((int) $product_id)); ?>"><?php echo esc_html(get_the_title((int) $product_id)); ?></a></td>
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
