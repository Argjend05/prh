<?php
get_header();
?>

<div class="prh-default-page">
    <?php while ( have_posts() ) : the_post(); ?>
<div class="prh-default-content">
            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
