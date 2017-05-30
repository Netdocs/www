<?php get_header(); ?>
	<main class="cd-main-content">
		<section class="hero">
			<div class="hero-slider">
				<?php 
     				
     				$query = $wp_query->get_queried_object(); 
     				$tax = isset($query->taxonomies[1]) ? $query->taxonomies[1] : "";
     				$rewrite = (get_theme_mod( 'streamium_section_input_taxonomy_' . $tax )) ? get_theme_mod( 'streamium_section_input_taxonomy_' . $tax ) : $tax; 

					$args = array(
						'post_status' => 'publish',
						'posts_per_page' => -1,
						'post_type' => $query->name,
						'meta_key' => 'streamium_tv_featured_checkbox_value',
						'meta_value' => '1'
					);
					  
					$loop = new WP_Query( $args ); 
					$sliderPostCount = 0;
					if($loop->have_posts()):
						while ( $loop->have_posts() ) : $loop->the_post();
							global $post;
						    $image   = wp_get_attachment_image_src( get_post_thumbnail_id(), 'streamium-home-slider' ); 
							$title   = wp_trim_words( get_the_title(), $num_words = 10, $more = '... ' );
							$percentage = get_post_meta( get_the_ID(), 'percentage', true );
							$streamiumVideoTrailer = get_post_meta( get_the_ID(), 'streamium_video_trailer_meta_box_text', true );
							$streamiumFeaturedVideo = get_post_meta( get_the_ID(), 'streamium_featured_video_meta_box_text', true );
							$nonce = wp_create_nonce( 'streamium_likes_nonce' );
					        $link = admin_url('admin-ajax.php?action=streamium_likes&post_id='.get_the_ID().'&nonce='.$nonce);
					        $content = (streamium_get_device('device') == 'desktop') ? get_the_content() : get_the_excerpt();
 
					?>
					<div class="slider-block" style="background-image: url(<?php echo esc_url($image[0]); ?>);">
						<article class="content-overlay">
							<div class="container-fluid rel">
								<div class="row rel">
									<div class="col-sm-5 col-xs-5 rel">
										<div class="synopis-outer">
											<div class="synopis-middle">
												<div class="synopis-inner">
													<h2><?php echo (isset($title) ? $title : __( 'No Title', 'streamium' )); ?></h2>
													<div class="synopis content hidden-xs">
														<?php echo $content; ?>
														<ul>
															<?php do_action('synopis_multi_meta'); ?>
														</ul>
													</div>
													
													<?php if(get_theme_mod( 'streamium_enable_premium' )) : ?>
														<div class="synopis-premium-meta hidden-xs">
															<a id="like-count-<?php echo get_the_ID(); ?>" class="streamium-review-like-btn streamium-btns streamium-reviews-btns" data-toggle="tooltip" title="CLICK TO LIKE!" data-id="<?php echo get_the_ID(); ?>" data-nonce="<?php echo $nonce; ?>">	<?php echo get_streamium_likes(get_the_ID()); ?>
															</a>
										                    <a class="streamium-list-reviews streamium-btns streamium-reviews-btns" data-id="<?php echo get_the_ID(); ?>" data-nonce="<?php echo $nonce; ?>">Read reviews</a>
														</div>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-7 col-xs-7 rel">
										<a class="play-icon-wrap" href="<?php the_permalink(); ?>">
											<div class="play-icon-wrap-rel">
												<div class="play-icon-wrap-rel-ring"></div>
												<span class="play-icon-wrap-rel-play">
													<i class="fa fa-play fa-3x" aria-hidden="true"></i>
									        	</span>
								        	</div>
							        	</a>
							        	<?php if ( ! empty( $streamiumVideoTrailer ) && get_theme_mod( 'streamium_enable_premium' ) ) : ?>
								        	<a class="synopis-video-trailer streamium-btns hidden-xs" href="<?php the_permalink(); ?>?trailer=true">Watch Trailer</a>
								        <?php endif; ?>
									</div>
								</div>
							</div>
						</article><!--/.content-overlay-->
					</div>
					<?php
					    $sliderPostCount++; 
						endwhile; 
					else : 
					endif;
					wp_reset_query(); 
				?>
			</div><!--/.hero-slider-->
		</section><!--/.hero-->

		<?php 

		  	$categories = get_terms( $tax, array('hide_empty' => false) );
		  	foreach ($categories as $category) : 

				$args = array(
				    'posts_per_page' => (int)get_theme_mod( 'streamium_global_options_homepage_desktop' ),
				    //'post_type' => $query->name,
				    'tax_query' => array(
				        array(
				            'taxonomy'  => $category->taxonomy,
				            'field'     => 'term_id',
				            'terms'     => $category->term_id,
				        )
				    )
				);

				$loop = new WP_Query( $args );

				if($loop->have_posts()):
		?>
	  	<section class="videos">
			<div class="container-fluid">
				<div class="row">
					<div class="col-sm-12">
						<div class="video-header">
							<h3><?php echo ucfirst($category->name); ?></h3>
							<a class="see-all" href="<?php echo esc_url( home_url('/' . $rewrite . '/' . $category->slug ) ); ?>">View all</a>
						</div>
					</div><!--/.col-sm-12-->
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="prev_next"></div>
						<div class="carousels" id="<?php echo $category->slug; ?>">
					  	<?php
							
							while ( $loop->have_posts() ) : $loop->the_post();
							if ( has_post_thumbnail() ) : // thumbnail check 
							$image  = wp_get_attachment_image_src( get_post_thumbnail_id(), 'streamium-video-tile' );
							$imageExpanded   = wp_get_attachment_image_src( get_post_thumbnail_id(), 'streamium-video-tile-expanded' );
							$nonce = wp_create_nonce( 'streamium_likes_nonce' );
							$trimexcerpt = !empty(get_the_excerpt()) ? get_the_excerpt() : get_the_content();  

						?>
							<div class="tile" data-id="<?php the_ID(); ?>" data-nonce="<?php echo $nonce; ?>" data-cat="<?php echo $category->slug; ?>">

								<div class="tile_inner" style="background-image: url(<?php echo esc_url($image[0]); ?>);">

									<?php do_action('streamium_video_payment'); ?>

									<div class="content">
								      <div class="overlay" style="background-image: url(<?php echo esc_url($imageExpanded[0]); ?>);">
								        <div class="overlay-gradient"></div>
								        <a class="play-icon-wrap hidden-xs" href="<?php the_permalink(); ?>">
											<div class="play-icon-wrap-rel">
												<div class="play-icon-wrap-rel-ring"></div>
												<span class="play-icon-wrap-rel-play">
													<i class="fa fa-play fa-1x" aria-hidden="true"></i>
									        	</span>
								        	</div>
							        	</a>
							          	<div class="overlay-meta hidden-xs">
							            	<h4><?php the_title(); ?></h4>
							            	<p><?php echo wp_trim_words( $trimexcerpt, $num_words = 25, $more = '...' ); ?></p>            	
							            	<a data-id="<?php the_ID(); ?>" data-nonce="<?php echo $nonce; ?>" data-cat="<?php echo $category->slug; ?>" class="tile_meta_more_info hidden-xs"><i class="icon-streamium" aria-hidden="true"></i></a>
							          	</div>
								      </div>
								    </div>

								    <?php do_action('streamium_video_extra_meta'); ?>

								</div>

								<?php do_action('synopis_video_progress'); ?>

						    </div>
						<?php
								
							endif; 
							endwhile;
							endif;
							wp_reset_query();
						?>
						</div><!--/.carousel-->
					</div><!--/.col-sm-12-->
				</div><!--/.row-->
			</div><!--/.container-->
		</section><!--/.videos-->
		<section class="s3bubble-details-full <?php echo $category->slug; ?>">
			<div class="s3bubble-details-full-overlay"></div>
			<div class="container-fluid s3bubble-details-inner-content">
				<div class="row">
					<div class="col-sm-5 col-xs-5 rel">
						<div class="synopis-outer">
							<div class="synopis-middle">
								<div class="synopis-inner">
									<h2 class="synopis hidden-xs"></h2>
									<div class="synopis content"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-7 col-xs-7 rel">
						<a class="play-icon-wrap synopis" href="#">
							<div class="play-icon-wrap-rel">
								<div class="play-icon-wrap-rel-ring"></div>
								<span class="play-icon-wrap-rel-play">
									<i class="fa fa-play fa-3x" aria-hidden="true"></i>
					        	</span>
				        	</div>
			        	</a>
			        	<a href="#" class="synopis-video-trailer streamium-btns hidden-xs">Watch Trailer</a>
			        	<a href="#" class="s3bubble-details-inner-close"><i class="fa fa-times" aria-hidden="true"></i></a>
					</div><!--/.col-sm-12-->
				</div><!--/.row-->
			</div><!--/.container-->
		</section><!--/.videos-->
			
		<?php  	
			endforeach; 
		?>

		<div class="main-spacer"></div>

<?php get_footer(); ?>