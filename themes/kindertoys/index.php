<?php
/**
 * Default template.
 *
 * @package KinderToys
 */

declare(strict_types=1);

get_header();
?>
<main id="primary" class="site-main kt-container kt-content">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class('kt-entry'); ?>>
                <header class="kt-entry__header">
                    <h1><?php the_title(); ?></h1>
                </header>
                <div class="kt-entry__content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <h1><?php esc_html_e('׳׳ ׳ ׳׳¦׳׳• ׳×׳•׳¦׳׳•׳×', 'kindertoys'); ?></h1>
    <?php endif; ?>
</main>
<?php
get_footer();
