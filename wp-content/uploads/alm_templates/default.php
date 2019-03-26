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