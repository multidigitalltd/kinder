<?php
/**
 * Template helpers.
 *
 * @package KinderToys
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

function kindertoys_asset_uri(string $path): string
{
    return esc_url(KINDERTOYS_THEME_URI . '/assets/' . ltrim($path, '/'));
}

function kindertoys_setting(string $key, mixed $fallback = ''): mixed
{
    if (function_exists('kindertoys_core_get_setting')) {
        return kindertoys_core_get_setting($key, $fallback);
    }

    return $fallback;
}

function kindertoys_setting_url(string $key, string $fallback = '#'): string
{
    $value = (string) kindertoys_setting($key, $fallback);

    if (function_exists('kindertoys_core_url')) {
        return esc_url(kindertoys_core_url($value));
    }

    if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
        return esc_url($value);
    }

    return esc_url(home_url('/' . ltrim($value, '/')));
}

function kindertoys_phone_href(): string
{
    if (function_exists('kindertoys_core_tel_href')) {
        return esc_url(kindertoys_core_tel_href());
    }

    return 'tel:035293383';
}

function kindertoys_cart_count(): int
{
    if (! function_exists('WC') || ! WC()->cart) {
        return 0;
    }

    return (int) WC()->cart->get_cart_contents_count();
}

function kindertoys_cart_total(): string
{
    if (! function_exists('WC') || ! WC()->cart) {
        return '';
    }

    return wp_kses_post(WC()->cart->get_cart_total());
}

function kindertoys_svg_icon(string $name): string
{
    $icons = [
        'search' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.3-4.3m1.3-5.2a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>',
        'cart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6h15l-1.7 8.5a2 2 0 0 1-2 1.5H9a2 2 0 0 1-2-1.6L5 3H2"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/></svg>',
        'user' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>',
        'heart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"/></svg>',
        'menu' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
        'close' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>',
        'truck' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg>',
        'star' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9Z"/></svg>',
        'spark' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l1.7 5.3L19 10l-5.3 1.7L12 17l-1.7-5.3L5 10l5.3-1.7L12 3Z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z"/></svg>',
        'gift' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12v9H4v-9M2 7h20v5H2zM12 7v14M12 7H8.5A2.5 2.5 0 1 1 11 4.5V7ZM12 7h3.5A2.5 2.5 0 1 0 13 4.5V7Z"/></svg>',
        'shield' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/></svg>',
        'rotate' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 0 1 15.5-6.2L21 8"/><path d="M21 3v5h-5M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/></svg>',
        'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m20 6-11 11-5-5"/></svg>',
        'pin' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s7-5.4 7-12a7 7 0 1 0-14 0c0 6.6 7 12 7 12Z"/><circle cx="12" cy="10" r="2.5"/></svg>',
        'phone' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.7.6 2.5a2 2 0 0 1-.5 2.1L8 9.5a16 16 0 0 0 6.5 6.5l1.2-1.2a2 2 0 0 1 2.1-.5c.8.3 1.6.5 2.5.6A2 2 0 0 1 22 16.9Z"/></svg>',
        'clock' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
        'mail' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4z"/><path d="m4 7 8 6 8-6"/></svg>',
        'wa' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 11.8a8.5 8.5 0 0 1-12.6 7.4L3 20.5l1.3-4.7a8.5 8.5 0 1 1 16.2-4Z"/><path d="M8.8 8.5c.2-.4.4-.4.7-.4h.5c.2 0 .4 0 .5.4l.7 1.7c.1.2.1.4-.1.6l-.4.5c-.2.2-.2.3-.1.5.4.8 1.1 1.5 2 1.9.2.1.4.1.5-.1l.6-.7c.2-.2.4-.2.6-.1l1.7.8c.3.1.4.3.4.5 0 .5-.3 1.4-.9 1.7-.6.4-1.8.4-3.5-.4-2.9-1.3-4.7-4-4.9-5.8-.1-.9.4-1.6.7-2.1Z"/></svg>',
        'access' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2"/><path d="M4 9h16M12 7v14M8 21l4-8 4 8"/></svg>',
        'grid' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="6" height="6" rx="1.4"/><rect x="14" y="4" width="6" height="6" rx="1.4"/><rect x="4" y="14" width="6" height="6" rx="1.4"/><rect x="14" y="14" width="6" height="6" rx="1.4"/></svg>',
        'cube' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.8 7.5 4.4 7.5-4.4M12 12.2v8.3"/></svg>',
        'school' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 21V9l7-5 7 5v12"/><path d="M9 21v-7h6v7M4 21h16M9 10h6"/></svg>',
        'palette' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 0 0 0 18h1.3a2.1 2.1 0 0 0 1.2-3.8 1.3 1.3 0 0 1 .7-2.4H17a4 4 0 0 0 4-4C21 6.5 17 3 12 3Z"/><circle cx="7.8" cy="10" r=".8"/><circle cx="10.5" cy="7.8" r=".8"/><circle cx="14" cy="8" r=".8"/></svg>',
        'bear' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="7.5" cy="6.8" r="2.4"/><circle cx="16.5" cy="6.8" r="2.4"/><circle cx="12" cy="12.5" r="7"/><path d="M9.2 12h.1M14.7 12h.1"/><path d="M10 15.2c1.2.8 2.8.8 4 0"/></svg>',
        'globe' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>',
        'chevron-down' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>',
    ];

    return $icons[$name] ?? '';
}

add_filter('walker_nav_menu_start_el', 'kindertoys_primary_menu_icons', 10, 4);

function kindertoys_primary_menu_icons(string $item_output, WP_Post $menu_item, int $depth, stdClass $args): string
{
    if (0 !== $depth || empty($args->theme_location) || 'primary' !== $args->theme_location) {
        return $item_output;
    }

    if (str_contains($item_output, 'kt-nav-icon')) {
        return $item_output;
    }

    $icon_map = [
        'kt-icon-grid' => 'grid',
        'kt-icon-cube' => 'cube',
        'kt-icon-school' => 'school',
        'kt-icon-palette' => 'palette',
        'kt-icon-bear' => 'bear',
        'kt-icon-globe' => 'globe',
        'kt-icon-spark' => 'spark',
    ];
    $icons = array_values($icon_map);
    $classes = is_array($menu_item->classes) ? $menu_item->classes : [];
    $index = max(0, ((int) $menu_item->menu_order - 1) % count($icons));
    $icon_name = $icons[$index];
    foreach ($icon_map as $class => $mapped_icon) {
        if (in_array($class, $classes, true)) {
            $icon_name = $mapped_icon;
            break;
        }
    }
    $has_children = in_array('menu-item-has-children', $classes, true);
    $caret = $has_children ? '<span class="kt-nav-caret">' . kindertoys_svg_icon('chevron-down') . '</span>' : '';
    $icon = $caret . '<span class="kt-nav-icon">' . kindertoys_svg_icon($icon_name) . '</span>';

    return (string) preg_replace('/(<a\b[^>]*>)/', '$1' . $icon, $item_output, 1);
}

function kindertoys_default_menu(): void
{
    $items = [
        [
            'url' => home_url('/shop/'),
            'label' => __('כל הקטגוריות', 'kindertoys'),
            'icon' => 'grid',
            'columns' => [
                __('פופולרי', 'kindertoys') => ['חדש באתר', 'רבי מכר', 'מבצעי השבוע', 'מתנות עד 100 ₪'],
                __('לפי תחום', 'kindertoys') => ['משחקי קופסה', 'יצירה', 'בובות', 'לגו ובנייה', 'פאזלים'],
                __('לפי גיל', 'kindertoys') => ['0-2', '3-5', '6-8', '9-12', 'נוער'],
                __('מותגים מובילים', 'kindertoys') => ['LEGO', 'Playmobil', 'Ravensburger', 'Bruder'],
            ],
        ],
        [
            'url' => home_url('/product-category/games/'),
            'label' => __('משחקים ותעסוקה', 'kindertoys'),
            'icon' => 'cube',
            'columns' => [
                __('משחקי קופסה', 'kindertoys') => ['משפחתי', 'אסטרטגיה', 'מסיבה', 'ילדים צעירים'],
                __('תעסוקה', 'kindertoys') => ['פאזלים', 'ספרי פעילות', 'מדבקות', 'מגנטים'],
                __('למידה', 'kindertoys') => ['מדע וניסויים', 'מתמטיקה', 'חשיבה ולוגיקה'],
            ],
        ],
        [
            'url' => home_url('/product-category/back-to-school/'),
            'label' => __('חזרה לבית ספר', 'kindertoys'),
            'icon' => 'school',
            'columns' => [
                __('ילקוטים ותיקים', 'kindertoys') => ['כיתה א', 'כיתות ב-ג', 'חטיבה', 'תיקי גב'],
                __('כלי כתיבה', 'kindertoys') => ['עטים', 'עפרונות', 'טושים', 'מחקים ומחדדים'],
                __('ציוד נלווה', 'kindertoys') => ['מחברות', 'תיקיות', 'סנדוויציות ובקבוקים'],
            ],
        ],
        ['url' => home_url('/product-category/art/'), 'label' => __('יצירה ואומנות', 'kindertoys'), 'icon' => 'palette', 'columns' => []],
        ['url' => home_url('/product-category/kindergarten/'), 'label' => __('הכל לגננת ולגן', 'kindertoys'), 'icon' => 'bear', 'columns' => []],
        ['url' => home_url('/product-category/summer/'), 'label' => __('מוצרי קיץ', 'kindertoys'), 'icon' => 'globe', 'columns' => []],
        ['url' => home_url('/product-category/sale/'), 'label' => __('מבצעים חמים', 'kindertoys'), 'icon' => 'spark', 'highlight' => true, 'columns' => []],
    ];

    echo '<ul class="kt-nav__list">';
    foreach ($items as $item) {
        $classes = ! empty($item['highlight']) ? ' class="kt-nav__item kt-nav__item--highlight"' : ' class="kt-nav__item"';
        $icon = kindertoys_svg_icon((string) ($item['icon'] ?? 'grid'));
        $dropdown_icon = ! empty($item['columns']) ? '<span class="kt-nav-caret">' . kindertoys_svg_icon('chevron-down') . '</span>' : '';
        echo '<li' . $classes . '><a href="' . esc_url($item['url']) . '">' . $dropdown_icon . '<span class="kt-nav-icon">' . $icon . '</span><span>' . esc_html($item['label']) . '</span></a>';

        if (! empty($item['columns'])) {
            echo '<div class="kt-mega" role="group" aria-label="' . esc_attr($item['label']) . '"><div class="kt-mega__columns">';
            foreach ($item['columns'] as $title => $links) {
                echo '<section><h3>' . esc_html($title) . '</h3><ul>';
                foreach ($links as $link) {
                    echo '<li><a href="' . esc_url(add_query_arg('brand', $link, home_url('/shop/'))) . '">' . esc_html($link) . '</a></li>';
                }
                echo '</ul></section>';
            }
            echo '</div><aside class="kt-mega__feature"><strong>' . esc_html__('מבצעי סוף שבוע', 'kindertoys') . '</strong><span>' . esc_html__('עד 40% הנחה על מאות פריטים', 'kindertoys') . '</span><a href="' . esc_url(home_url('/product-category/sale/')) . '">' . esc_html__('לכל המבצעים', 'kindertoys') . '</a></aside></div>';
        }

        echo '</li>';
    }
    echo '</ul>';
}

function kindertoys_floating_actions(): void
{
    ?>
    <div class="kt-floating-actions" aria-label="<?php esc_attr_e('פעולות מהירות', 'kindertoys'); ?>">
        <?php $whatsapp = preg_replace('/[^\d]/', '', (string) kindertoys_setting('whatsapp', '97235293383')); ?>
        <a class="kt-float kt-float--wa" href="<?php echo esc_url('https://wa.me/' . $whatsapp); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('צאט בוואטסאפ', 'kindertoys'); ?>">
            <?php echo kindertoys_svg_icon('wa'); ?>
        </a>
        <button class="kt-float kt-float--access" type="button" data-a11y-toggle aria-pressed="false" aria-label="<?php esc_attr_e('הגדלת טקסט וניגודיות', 'kindertoys'); ?>">
            <?php echo kindertoys_svg_icon('access'); ?>
        </button>
    </div>
    <?php
}
