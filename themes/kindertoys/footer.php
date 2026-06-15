<?php
/**
 * Site footer.
 *
 * @package KinderToys
 */

declare(strict_types=1);
?>
<?php kindertoys_floating_actions(); ?>
<footer class="kt-footer">
    <div class="kt-container kt-footer__grid">
        <section>
            <h2><?php echo esc_html((string) kindertoys_setting('footer_about_title', 'KinderToys')); ?></h2>
            <p><?php echo esc_html((string) kindertoys_setting('footer_about_text', 'משחקים, צעצועים ומוצרי יצירה לילדים - בחוויית קנייה מהירה, נגישה ושמחה.')); ?></p>
        </section>
        <section>
            <h2><?php echo esc_html((string) kindertoys_setting('footer_service_title', 'שירות לקוחות')); ?></h2>
            <ul>
                <li><a href="<?php echo kindertoys_phone_href(); ?>"><?php echo esc_html((string) kindertoys_setting('phone', '03-5293383')); ?></a></li>
                <li><a href="<?php echo kindertoys_setting_url('footer_contact_url', '/contact/'); ?>"><?php echo esc_html((string) kindertoys_setting('footer_contact_label', 'צור קשר')); ?></a></li>
                <li><a href="<?php echo kindertoys_setting_url('footer_shipping_url', '/shipping-returns/'); ?>"><?php echo esc_html((string) kindertoys_setting('footer_shipping_label', 'משלוחים והחזרות')); ?></a></li>
            </ul>
        </section>
        <section>
            <h2><?php esc_html_e('ניווט', 'kindertoys'); ?></h2>
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => 'kt-footer__menu',
                'fallback_cb'    => false,
                'depth'          => 1,
            ]);
            ?>
        </section>
    </div>
    <div class="kt-footer__bottom">
        <div class="kt-container">
            <?php $footer_bottom = (string) kindertoys_setting('footer_bottom_text', ''); ?>
            <small><?php echo '' !== $footer_bottom ? esc_html($footer_bottom) : '&copy; ' . esc_html((string) gmdate('Y')) . ' ' . esc_html(get_bloginfo('name')); ?></small>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
