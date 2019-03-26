<?php
/**
 * Template Name: Resume
 *
 * This template display content at full with, with no sidebars.
 * Please note that this is the WordPress construct of pages and that other 'pages' on your WordPress site will use a different template.
 *
 * @package some_like_it_neat
 */

get_header(); ?>

<div id="intro" class="project resume">

	<?php while ( have_posts() ) : the_post(); ?>

	<div class="title-container">
		<h2><?php the_title(); ?></h2>
	</div>
	<div class="intro-container">
		<?php the_content(); ?>
	</div>
	<div class="side-container">
		<?php the_field('right_side_content'); ?>
	</div>
		
	<?php endwhile;?>
</div>
	
<?php get_footer(); ?>
