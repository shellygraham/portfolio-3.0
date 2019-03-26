<?php
/**
 * Template Name: Full-Width, No Sidebars
 *
 * This template display content at full with, with no sidebars.
 * Please note that this is the WordPress construct of pages and that other 'pages' on your WordPress site will use a different template.
 *
 * @package some_like_it_neat
 */

get_header(); ?>

<div id="primary" class="content-area">

		<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'page-templates/template-parts/content', 'page' ); ?>

		<?php endwhile; // end of the loop. ?>

</div><!-- #primary -->

<?php get_footer(); ?>
