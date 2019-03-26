<?php
/**
 * Template Name: Projects page
 *
 * This template display content at full with, with no sidebars.
 * Please note that this is the WordPress construct of pages and that other 'pages' on your WordPress site will use a different template.
 *
 * @package some_like_it_neat
 */

get_header(); ?>

<div id="work" class="all-projects">
	<div class="work-container">
		<div class="work-grid">
			<h2>All Projects</h2>
			<div class="filters-grp">
				<h3 class="filter">Filter by:</h3>
				<span id="clear-filters">Reset Filters</span>
			</div>
			<?php echo do_shortcode('[ajax_load_more_filters id="projects" target="project_list"]'); ?>
			
			<?php echo do_shortcode('[ajax_load_more id="project_list" container_type="ul" target="projects" filters="true" post_type="work_projects" posts_per_page="12"]'); ?>

		</div>
	</div>
</div>
<div id="skills" class="alt">
	<div class="skills-container">
		<div class="skills-list">
		<h2>Clients</h2>
				<ul>

				<?php $args = array( 'post_type' => 'work_projects', 'posts_per_page' => -1 );
					$loop = new WP_Query( $args );
					while ( $loop->have_posts() ) : $loop->the_post(); ?>
					<li>
						<a href="<?php the_field('site_url'); ?>"><?php the_title(); ?></a>
					</li>
				<?php endwhile;?>
				<?php wp_reset_query(); ?>
			<?php if( have_rows('client-list') ): ?>
				<?php while ( have_rows('client-list') ) : the_row(); ?>
					<li><a href="<?php the_sub_field('client-url'); ?>" target="_blank"><?php the_sub_field('client'); ?></a></li>
				<?php endwhile; ?>
			<?php endif; ?>
				</ul>
		</div>
	</div>
</div>

<?php get_footer(); ?>