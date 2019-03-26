<?php
/**
 * The Template for displaying all single posts.
 *
 * @package some_like_it_neat
 */

get_header(); ?>

<div id="intro" class="project">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if( get_field('layout_type') != 'gallery_grid' ): ?>
	<div class="intro-container">
		<h2><?php the_title(); ?></h2>
			<?php the_content();
				$term_names = wp_get_post_terms($post->ID, 'technologies', array('fields' => 'names'));			
				if ($term_names) :
				echo '<p>Technologies used:</p>';
				echo '<ul>';
				foreach( $term_names as $name ) {
				    echo '<li>' . $name . '</li>';
				}
				echo '</ul>';
				endif;
			?>
	</div>
			<?php if( get_field('site_url')) { ?>
	<div class="media-container">
		<a href="<?php echo get_field('site_url'); ?>" target="_blank"><?php the_post_thumbnail('tablet'); ?><span>Visit Site</span></a>
	</div>
			<?php } else { ?>
	<div class="media-container">
				<?php the_post_thumbnail('tablet'); ?>
	</div>
			<?php } ?>			
		<?php endif; ?>
		
		<?php if( get_field('layout_type') == 'gallery_grid' ): ?>
	<div class="intro-container wide">
		<h2><?php the_title(); ?></h2>
			<?php the_content(); ?>
	</div>
	<div class="intro-container tech">
			<?php $term_names = wp_get_post_terms($post->ID, 'technologies', array('fields' => 'names'));			
				if ($term_names) :
					echo '<h3>Technologies used:</h3>';
					echo '<ul>';
					foreach( $term_names as $name ) {
					    echo '<li>' . $name . '</li>';
					}
					echo '</ul>';
				endif;
				if( get_field('site_url')) { ?>
					<a href="<?php echo get_field('site_url'); ?>" target="_blank"><span>Visit Site</span></a>
			<?php } ?>
	</div>
	<div class="media-container active">
		<div class="slider-for">
			<?php
				if( have_rows('gallery_images') ):
					while ( have_rows('gallery_images') ) : the_row();
						echo '<div class="item focus-image">';
						$image = get_sub_field('image');
						$size = 'gallery-focus';
						echo wp_get_attachment_image( $image, $size );
						echo '<p>' . get_sub_field('caption') . '</p>';
						echo '</div>';
					endwhile;
				endif;
			?>
		</div>
	</div>
	<div class="gallery-container gallery-nav">
		<div class="slider-nav">
			<?php
				if( have_rows('gallery_images') ):
					while ( have_rows('gallery_images') ) : the_row();
						echo '<div class="item gallery-item">';
						$image = get_sub_field('image');
						$size = 'gallery-thumbs';
						echo wp_get_attachment_image( $image, $size );
						echo '<p>' . get_sub_field('caption_small') . '</p>';
						echo '</div>';
					endwhile;
				endif;
			?>
		</div>
	</div>
		<?php endif; ?>
	<?php endwhile; ?>
</div>

<section id="more-projects">
	<h3>Related Projects</h3>
	<div class="more-container">
		<div class="thumbs-content">
			<?php $posts = get_field('related_project');
			if( $posts ): ?>
		    <ul>
		    <?php foreach( $posts as $post): // variable must be called $post (IMPORTANT) ?>
		        <?php setup_postdata($post); ?>
		        <li><span class="burns" style="background-image:url(<?php the_field('work-thumb'); ?>)"></span>
		            <a href="<?php the_permalink(); ?>" style="background-image:url(<?php the_field('work-thumb-dk'); ?>)">
						<div class="knockout">
						  	<svg viewBox="0 0 200 125">
						    	<rect fill="#464645" x="0" y="0" width="200" height="125" fill-opacity="1" mask="url(#<?php the_permalink(); ?>)"/>
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
		    <?php endforeach; ?>
		    </ul>
		    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
		<?php endif; ?>
		</div>
	</div>
</section><!-- container -->
<div id="cta" class="light">
	<a href="/work" class="all-work"><h3>More Projects</h3></a>
</div>
<?php get_footer(); ?>
