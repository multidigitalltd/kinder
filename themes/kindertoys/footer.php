<?php
/**
 * Site footer.
 *
 * @package KinderToys
 */

declare(strict_types=1);
?>
<footer class="kt-footer">
    <div class="kt-container kt-footer__grid">
        <section>
            <h2><?php esc_html_e('KinderToys', 'kindertoys'); ?></h2>
            <p><?php esc_html_e('׳׳©׳—׳§׳™׳, ׳¦׳¢׳¦׳•׳¢׳™׳ ׳•׳׳•׳¦׳¨׳™ ׳™׳¦׳™׳¨׳” ׳׳™׳׳“׳™׳ - ׳‘׳—׳•׳•׳™׳™׳× ׳§׳ ׳™׳” ׳׳”׳™׳¨׳”, ׳ ׳’׳™׳©׳” ׳•׳©׳׳—׳”.', 'kindertoys'); ?></p>
        </section>
        <section>
            <h2><?php esc_html_e('׳©׳™׳¨׳•׳× ׳׳§׳•׳—׳•׳×', 'kindertoys'); ?></h2>
            <ul>
                <li><a href="tel:035293383">03-5293383</a></li>
                <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('׳¦׳•׳¨ ׳§׳©׳¨', 'kindertoys'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/shipping-returns/')); ?>"><?php esc_html_e('׳׳©׳׳•׳—׳™׳ ׳•׳”׳—׳–׳¨׳•׳×', 'kindertoys'); ?></a></li>
            </ul>
        </section>
        <section>
            <h2><?php esc_html_e('׳ ׳™׳•׳•׳˜', 'kindertoys'); ?></h2>
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
            <small>&copy; <?php echo esc_html((string) gmdate('Y')); ?> <?php bloginfo('name'); ?></small>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
