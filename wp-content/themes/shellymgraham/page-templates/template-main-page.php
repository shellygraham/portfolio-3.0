<?php
/**
 * Template Name: Main page
 *
 * This template display content at full with, with no sidebars.
 * Please note that this is the WordPress construct of pages and that other 'pages' on your WordPress site will use a different template.
 *
 * @package some_like_it_neat
 */

get_header(); ?>
<div id="intro-wrapper">
	<div id="intro">
		<div class="intro-container">
			<?php while ( have_posts() ) : the_post(); ?>
	
			<?php the_content(); ?>
	
			<?php endwhile; // end of the loop. ?>
		</div>
	</div>
</div>
<div id="skills-wrapper">
	<div id="skills">
		<div class="skills-container">
			<div class="skills-list">
				<h2>Senior Level Experience</h2>
				<?php if( have_rows('skills-list') ): ?>
					<ul>
					<?php while ( have_rows('skills-list') ) : the_row(); ?>
						<li><?php the_sub_field('skill'); ?></li>
					<?php endwhile; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
			<div id="cta">
				<a href="/resume" class="all-work"><h3>View Resumé</h3></a>
			</div>
	</div>
</div>
<div id="work-wrapper">
	<div id="work">
		<div class="work-container">
			<div class="work-grid">
				<h2>Featured Projects</h2>
				<ul>
					<?php $args = array( 'post_type' => 'work_projects', 'posts_per_page' => 6 );
						$loop = new WP_Query( $args );
						while ( $loop->have_posts() ) : $loop->the_post(); ?>
						<li><span class="burns" style="background-image:url(<?php the_field('work-thumb'); ?>)"></span>
							<a href="<?php the_permalink(); ?>" style="background-image:url(<?php the_field('work-thumb-dk'); ?>)">
								<div class="knockout">
								  	<svg viewBox="0 0 200 125">
								    	<rect fill="#c06718" x="0" y="0" width="200" height="125" fill-opacity="1" mask="url(#<?php the_permalink(); ?>)"/>
										<mask id="<?php the_permalink(); ?>">
										<rect fill="#fff" x="0" y="0" width="200" height="125"></rect>
								  	<?php if(get_field('title_top') && get_field('title_middle') && get_field('title_bottom')) { ?>
										<text y="38" fill="#000" text-anchor="middle" x="50%">
											<?php echo get_field('title_top'); ?>
										</text>
										<text y="71" fill="#000" text-anchor="middle" x="50%">
											<?php echo get_field('title_middle'); ?>
										</text>
										<text y="102" fill="#000" text-anchor="middle" x="50%">
											<?php echo get_field('title_bottom'); ?>
										</text>
										
									<?php } else if(get_field('title_top') && get_field('title_middle')) { ?>
									
										<text y="56" fill="#000" text-anchor="middle" x="50%">
											<?php echo get_field('title_top'); ?>
										</text>
										<text y="88" fill="#000" text-anchor="middle" x="50%">
											<?php echo get_field('title_middle'); ?>
										</text>
										
									<?php } else if (get_field('title_top')) { ?>
									
										<text y="71" fill="#000" text-anchor="middle" x="50%">
											<?php echo get_field('title_top'); ?>
										</text>
										
									<?php } ?>
									</svg>
								</div>
							</a>
						</li>
					<?php endwhile;?>
					<?php wp_reset_query(); ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<div id="cta" class="home-work">
	<a href="/work" class="all-work"><h3>More Projects</h3></a>
</div>
<?php get_footer(); ?>