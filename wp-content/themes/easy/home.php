<?php get_header(); ?>
    <?php
    $args = [
        'posts_per_page' => 6,
        'post_type' => 'post',
        'order' => 'DESC'
    ];

    $the_query = new WP_Query($args);
    ?>
    <?php if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post(); ?>
        <?php get_template_part('template-parts/content'); ?>
    <?php endwhile; endif; ?>
<?php get_footer(); ?>