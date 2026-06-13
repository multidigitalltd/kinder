<?php
/**
 * Front page.
 *
 * @package KinderToys
 */

declare(strict_types=1);

get_header();
?>
<main id="primary" class="site-main kt-home">
    <section class="kt-hero">
        <div class="kt-container kt-hero__grid">
            <div class="kt-hero__copy">
                <p class="kt-hero__pill"><span></span><?php esc_html_e('חדש בקינדר טויס - קולקציית 2026', 'kindertoys'); ?></p>
                <h1><?php esc_html_e('עולם של קסם, משחק ויצירה', 'kindertoys'); ?><br><span><?php esc_html_e('בלחיצה אחת', 'kindertoys'); ?></span></h1>
                <p><?php esc_html_e('אלפי צעצועים, משחקים, חומרי יצירה וציוד לבית הספר ולגן - משלוח מהיר ושירות אישי מהלב.', 'kindertoys'); ?></p>
                <div class="kt-hero__actions">
                    <a class="kt-button" href="<?php echo esc_url(home_url('/product-category/sale/')); ?>"><?php esc_html_e('לכל המבצעים החמים', 'kindertoys'); ?></a>
                    <a class="kt-button kt-button--light" href="<?php echo esc_url(home_url('/shop/')); ?>"><?php esc_html_e('לכל המוצרים', 'kindertoys'); ?></a>
                </div>
                <div class="kt-hero__proof">
                    <span class="kt-rating"><?php echo str_repeat(kindertoys_svg_icon('star'), 5); ?><strong>4.9</strong></span>
                    <span><?php echo kindertoys_svg_icon('truck'); ?><?php esc_html_e('משלוח מחר עד הבית', 'kindertoys'); ?></span>
                    <span><?php echo kindertoys_svg_icon('spark'); ?><?php esc_html_e('+10,000 מוצרים במלאי', 'kindertoys'); ?></span>
                </div>
            </div>
            <div class="kt-hero__media" aria-hidden="true">
                <img src="<?php echo kindertoys_asset_uri('images/mascot-wave.png'); ?>" alt="" width="680" height="680" loading="eager">
                <div class="kt-hero__float kt-hero__float--top"><?php echo kindertoys_svg_icon('spark'); ?><strong><?php esc_html_e('+120 מוצרים חדשים', 'kindertoys'); ?></strong></div>
                <div class="kt-hero__float kt-hero__float--bottom"><?php echo kindertoys_svg_icon('truck'); ?><strong><?php esc_html_e('חינם מעל 299 ₪', 'kindertoys'); ?></strong></div>
            </div>
        </div>
    </section>

    <section class="kt-container kt-usp-strip" aria-label="<?php esc_attr_e('יתרונות החנות', 'kindertoys'); ?>">
        <div><?php echo kindertoys_svg_icon('truck'); ?><span><strong><?php esc_html_e('משלוח חינם', 'kindertoys'); ?></strong><?php esc_html_e('בהזמנה מעל 299 ₪', 'kindertoys'); ?></span></div>
        <div><?php echo kindertoys_svg_icon('rotate'); ?><span><strong><?php esc_html_e('החזרה קלה', 'kindertoys'); ?></strong><?php esc_html_e('14 יום ללא התחייבות', 'kindertoys'); ?></span></div>
        <div><?php echo kindertoys_svg_icon('shield'); ?><span><strong><?php esc_html_e('תשלום מאובטח', 'kindertoys'); ?></strong><?php esc_html_e('SSL ואבטחת אשראי', 'kindertoys'); ?></span></div>
        <div><?php echo kindertoys_svg_icon('gift'); ?><span><strong><?php esc_html_e('מועדון קינדי', 'kindertoys'); ?></strong><?php esc_html_e('נקודות, הנחות ומתנות', 'kindertoys'); ?></span></div>
    </section>

    <?php if (class_exists('WooCommerce')) : ?>
        <section class="kt-container kt-section">
            <div class="kt-section__head">
                <p class="kt-eyebrow"><?php esc_html_e('קטגוריות מובילות', 'kindertoys'); ?></p>
                <h2><?php esc_html_e('בחרו את העולם המתאים לכם', 'kindertoys'); ?></h2>
            </div>
            <?php echo do_shortcode('[kindertoys_categories limit="6"]'); ?>
        </section>

        <section class="kt-container kt-section">
            <div class="kt-section__head">
                <p class="kt-eyebrow"><?php esc_html_e('מבצעים חמים', 'kindertoys'); ?></p>
                <h2><?php esc_html_e('המבצעים של קינדי', 'kindertoys'); ?></h2>
            </div>
            <div class="kt-promo-grid">
                <a class="kt-promo kt-promo--big kt-promo--red" href="<?php echo esc_url(home_url('/product-category/back-to-school/')); ?>">
                    <img src="<?php echo kindertoys_asset_uri('images/promo-back-to-school.jpg'); ?>" alt="" loading="lazy" width="720" height="480">
                    <span><?php esc_html_e('מוגבל בזמן', 'kindertoys'); ?></span>
                    <strong><?php esc_html_e('חזרה לבית הספר עד 40% הנחה', 'kindertoys'); ?></strong>
                    <small><?php esc_html_e('ילקוטים, קלמרים, מחברות וכלי כתיבה במחירי השקה', 'kindertoys'); ?></small>
                </a>
                <a class="kt-promo kt-promo--navy" href="<?php echo esc_url(home_url('/shop/')); ?>">
                    <img src="<?php echo kindertoys_asset_uri('images/promo-gameboy.jpg'); ?>" alt="" loading="lazy" width="420" height="280">
                    <span><?php esc_html_e('חדש בקינדי', 'kindertoys'); ?></span>
                    <strong><?php esc_html_e('משחקים אלקטרוניים', 'kindertoys'); ?></strong>
                    <small><?php esc_html_e('דגמים נבחרים במלאי', 'kindertoys'); ?></small>
                </a>
                <a class="kt-promo kt-promo--blue" href="<?php echo esc_url(home_url('/product-category/building/')); ?>">
                    <img src="<?php echo kindertoys_asset_uri('images/promo-blocks.jpg'); ?>" alt="" loading="lazy" width="420" height="280">
                    <span><?php esc_html_e('מחיר מיוחד', 'kindertoys'); ?></span>
                    <strong><?php esc_html_e('בנייה ולגו בכל הסדרות', 'kindertoys'); ?></strong>
                    <small><?php esc_html_e('מתנות שילדים באמת אוהבים', 'kindertoys'); ?></small>
                </a>
            </div>
        </section>

        <section class="kt-container kt-section">
            <div class="kt-section__head">
                <p class="kt-eyebrow"><?php esc_html_e('מוצרים חמים', 'kindertoys'); ?></p>
                <h2><?php esc_html_e('הנבחרים של קינדי', 'kindertoys'); ?></h2>
            </div>
            <?php echo do_shortcode('[products limit="10" columns="5" orderby="popularity"]'); ?>
        </section>
    <?php endif; ?>

    <section class="kt-container kt-section">
        <div class="kt-section__head">
            <p class="kt-eyebrow"><?php esc_html_e('בוחרים לפי גיל', 'kindertoys'); ?></p>
            <h2><?php esc_html_e('למצוא את המתנה המושלמת', 'kindertoys'); ?></h2>
        </div>
        <div class="kt-age-grid">
            <?php foreach ([['0-2', 'תינוקות'], ['3-5', 'גיל הגן'], ['6-8', 'בית ספר יסודי'], ['9-12', 'חטיבת ביניים'], ['13+', 'נוער ונערות']] as $age) : ?>
                <a href="<?php echo esc_url(add_query_arg('age', rawurlencode($age[0]), home_url('/shop/'))); ?>">
                    <span><?php echo kindertoys_svg_icon('gift'); ?></span>
                    <strong><?php echo esc_html($age[0]); ?></strong>
                    <small><?php echo esc_html($age[1]); ?></small>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="kt-container kt-section">
        <div class="kt-section__head">
            <p class="kt-eyebrow"><?php esc_html_e('מותגים אהובים', 'kindertoys'); ?></p>
            <h2><?php esc_html_e('רק המקוריים והאיכותיים', 'kindertoys'); ?></h2>
        </div>
        <div class="kt-brand-grid">
            <?php foreach (['LEGO', 'PLAYMOBIL', 'מודן', 'NICI', 'פלפוט', 'SMASH', 'Melissa & Doug', 'Janod', 'Hape', 'Ravensburger', 'Bruder', 'Schleich'] as $brand) : ?>
                <a href="<?php echo esc_url(add_query_arg(['s' => $brand, 'post_type' => 'product'], home_url('/'))); ?>"><?php echo esc_html($brand); ?></a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="kt-container kt-section">
        <div class="kt-kindy-zone">
            <div>
                <p class="kt-zone-badge"><?php echo kindertoys_svg_icon('gift'); ?><?php esc_html_e('מועדון קינדי', 'kindertoys'); ?></p>
                <h2><?php esc_html_e('הצטרפו לחברים שלנו וקבלו עולם של הטבות', 'kindertoys'); ?></h2>
                <p><?php esc_html_e('חברים נהנים מהנחות בלעדיות, צוברים נקודות על כל קניה ומקבלים מתנות מיוחדות ביום ההולדת.', 'kindertoys'); ?></p>
                <ul>
                    <li><?php echo kindertoys_svg_icon('check'); ?><?php esc_html_e('5% חזרה על כל קניה', 'kindertoys'); ?></li>
                    <li><?php echo kindertoys_svg_icon('check'); ?><?php esc_html_e('מתנה ביום ההולדת', 'kindertoys'); ?></li>
                    <li><?php echo kindertoys_svg_icon('check'); ?><?php esc_html_e('מבצעים בלעדיים', 'kindertoys'); ?></li>
                    <li><?php echo kindertoys_svg_icon('check'); ?><?php esc_html_e('משלוח חינם מהיר', 'kindertoys'); ?></li>
                </ul>
                <a class="kt-button" href="<?php echo esc_url(home_url('/my-account/')); ?>"><?php esc_html_e('הצטרפות חינם', 'kindertoys'); ?></a>
            </div>
            <img src="<?php echo kindertoys_asset_uri('images/mascot-celebrate.png'); ?>" alt="" loading="lazy" width="720" height="720">
        </div>
    </section>

    <section class="kt-container kt-section">
        <div class="kt-values-grid">
            <article><?php echo kindertoys_svg_icon('shield'); ?><h3><?php esc_html_e('רכישה מאובטחת', 'kindertoys'); ?></h3><p><?php esc_html_e('תשלום בטוח ושמירה על פרטיות הלקוחות.', 'kindertoys'); ?></p></article>
            <article><?php echo kindertoys_svg_icon('spark'); ?><h3><?php esc_html_e('הכל תחת גג אחד', 'kindertoys'); ?></h3><p><?php esc_html_e('משחקים, יצירה, כתיבה וציוד לגנים במלאי רחב.', 'kindertoys'); ?></p></article>
            <article><?php echo kindertoys_svg_icon('heart'); ?><h3><?php esc_html_e('שירות אישי', 'kindertoys'); ?></h3><p><?php esc_html_e('ייעוץ אישי בטלפון ובוואטסאפ לפי גיל וצורך.', 'kindertoys'); ?></p></article>
            <article><?php echo kindertoys_svg_icon('truck'); ?><h3><?php esc_html_e('משלוחים מהירים', 'kindertoys'); ?></h3><p><?php esc_html_e('אספקה מהירה והחזרות נוחות לכל הארץ.', 'kindertoys'); ?></p></article>
        </div>
    </section>

    <section class="kt-container kt-section">
        <div class="kt-store-info">
            <div>
                <p class="kt-zone-badge"><?php echo kindertoys_svg_icon('pin'); ?><?php esc_html_e('בקרו אותנו', 'kindertoys'); ?></p>
                <h2><?php esc_html_e('בואו לחנות שלנו בבני ברק', 'kindertoys'); ?></h2>
                <p><?php esc_html_e('חוויית קניה אמיתית, שירות אישי וייעוץ מקצועי - לראות, לגעת ולבחור מאלפי מוצרים.', 'kindertoys'); ?></p>
                <ul>
                    <li><?php echo kindertoys_svg_icon('pin'); ?><?php esc_html_e('הרב יעקב לנדא 7, בני ברק', 'kindertoys'); ?></li>
                    <li><?php echo kindertoys_svg_icon('phone'); ?><a href="tel:035293383">03-5293383</a></li>
                    <li><?php echo kindertoys_svg_icon('clock'); ?><?php esc_html_e('א-ה 9:00-21:00 | ו 9:00-14:00', 'kindertoys'); ?></li>
                </ul>
            </div>
            <img src="<?php echo kindertoys_asset_uri('images/mascot-point.png'); ?>" alt="" loading="lazy" width="720" height="720">
        </div>
    </section>
</main>
<?php
get_footer();
