<?php
/**
 * Front page.
 *
 * @package KinderToys
 */

declare(strict_types=1);

get_header();
$promo_images = [
    1 => kindertoys_asset_uri('images/promo-back-to-school.jpg'),
    2 => kindertoys_asset_uri('images/promo-gameboy.jpg'),
    3 => kindertoys_asset_uri('images/promo-blocks.jpg'),
];
?>
<main id="primary" class="site-main kt-home">
    <section class="kt-hero">
        <div class="kt-container kt-hero__grid">
            <div class="kt-hero__copy">
                <p class="kt-hero__pill"><span></span><?php echo esc_html((string) kindertoys_setting('hero_eyebrow', 'חדש בקינדר טויס - קולקציית 2026')); ?></p>
                <h1>
                    <?php echo esc_html((string) kindertoys_setting('hero_title_prefix', 'עולם של')); ?>
                    <span class="kt-hero__highlight"><?php echo esc_html((string) kindertoys_setting('hero_title', 'קסם, משחק ויצירה')); ?></span>
                    <br><span class="kt-hero__accent"><?php echo esc_html((string) kindertoys_setting('hero_title_accent', 'בלחיצה אחת')); ?></span>
                </h1>
                <p><?php echo esc_html((string) kindertoys_setting('hero_text', 'אלפי צעצועים, משחקים, חומרי יצירה וציוד לבית הספר ולגן - משלוח מהיר ושירות אישי מהלב.')); ?></p>
                <div class="kt-hero__actions">
                    <a class="kt-button" href="<?php echo kindertoys_setting_url('hero_primary_url', '/product-category/sale/'); ?>"><?php echo esc_html((string) kindertoys_setting('hero_primary_label', 'לכל המבצעים החמים')); ?></a>
                    <a class="kt-button kt-button--light" href="<?php echo kindertoys_setting_url('hero_secondary_url', '/shop/'); ?>"><?php echo esc_html((string) kindertoys_setting('hero_secondary_label', 'לכל המוצרים')); ?></a>
                </div>
                <div class="kt-hero__proof">
                    <span class="kt-rating" aria-label="<?php esc_attr_e('דירוג לקוחות 4.9 מתוך 5', 'kindertoys'); ?>"><?php echo str_repeat(kindertoys_svg_icon('star'), 5); ?><strong>4.9</strong></span>
                    <span><?php echo kindertoys_svg_icon('truck'); ?><?php esc_html_e('משלוח מחר עד הבית', 'kindertoys'); ?></span>
                    <span><?php echo kindertoys_svg_icon('spark'); ?><?php esc_html_e('+10,000 מוצרים במלאי', 'kindertoys'); ?></span>
                </div>
            </div>
            <div class="kt-hero__media" aria-hidden="true">
                <img src="<?php echo kindertoys_asset_uri('images/hero-kindy-scene.png'); ?>" alt="" width="1024" height="1024" loading="eager">
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
                <p class="kt-eyebrow"><?php echo esc_html((string) kindertoys_setting('promo_section_eyebrow', 'מבצעים חמים')); ?></p>
                <h2><?php echo esc_html((string) kindertoys_setting('promo_section_title', 'המבצעים של קינדי')); ?></h2>
            </div>
            <div class="kt-promo-grid">
                <?php for ($i = 1; $i <= 3; $i++) : ?>
                    <?php
                    $classes = ['kt-promo', 1 === $i ? 'kt-promo--big kt-promo--red' : (2 === $i ? 'kt-promo--navy' : 'kt-promo--blue')];
                    $image = (string) kindertoys_setting("promo_{$i}_image", '');
                    $image = '' !== $image ? esc_url($image) : $promo_images[$i];
                    ?>
                    <a class="<?php echo esc_attr(implode(' ', $classes)); ?>" href="<?php echo kindertoys_setting_url("promo_{$i}_url", 1 === $i ? '/product-category/back-to-school/' : (2 === $i ? '/shop/' : '/product-category/building/')); ?>">
                        <img src="<?php echo esc_url($image); ?>" alt="" loading="lazy" width="<?php echo 1 === $i ? '720' : '420'; ?>" height="<?php echo 1 === $i ? '480' : '280'; ?>">
                        <span><?php echo esc_html((string) kindertoys_setting("promo_{$i}_badge", '')); ?></span>
                        <strong><?php echo esc_html((string) kindertoys_setting("promo_{$i}_title", '')); ?></strong>
                        <small><?php echo esc_html((string) kindertoys_setting("promo_{$i}_text", '')); ?></small>
                    </a>
                <?php endfor; ?>
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
                <a href="<?php echo esc_url(add_query_arg('age', $age[0], home_url('/shop/'))); ?>">
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
                <a href="<?php echo esc_url(add_query_arg('brand', $brand, home_url('/shop/'))); ?>"><?php echo esc_html($brand); ?></a>
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
