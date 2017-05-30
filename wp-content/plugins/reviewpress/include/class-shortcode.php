<?php

/**
* Handle all shortcode for plugin
*
* @category Class
* @package ReviewPress
*/
class ReviewPress_Shortcodes{

	/**
	* Constructor of closs
	*/
	function __construct() {

		add_shortcode( 'REVIEWPRESS_FORM', array( $this, 'review_press_form_shortcode' ) );
		add_shortcode( 'REVIEWPRESS_SHOW', array( $this, 'review_press_show_shortcode' ) );
		add_shortcode( 'REVIEWPRESS_RICH_SNIPPET' , array( $this, 'review_press_add_rich_data' ) );
	}

	/**
	* To show form on front-end
	*
	* @since 1.0.0
	*/
	public function review_press_form_shortcode() {

		if ( ! is_singular( ) ) {
			return ;
		}

		$user_authorization = review_get_option( 'review_authorization', 'wpbr_display' );
		$form = '';
		$is_success = true;

		if ( 'login_users' === $user_authorization ) {
			if ( ! is_user_logged_in() ) {
				return '<span class="wpbr_review_show">
				Be the first to leave a review.
				<em><a href="'.wp_login_url( get_permalink() ).'">Login to Review</a></em>
				</span>';
			}
		}


		if ( 'on' === review_get_option('google_captcha', 'wpbr_form') &&  isset( $_POST['wpbr_review_form_submit'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['wpbr_review_form_nonce_field'] ) ), 'wpbr_review_form_action' ) ) {

			$secret = review_get_option( 'google_captcha_secret_key', 'wpbr_form' );

			// Empty response.
			$response = null;

			// Check secret key.
			$re_captcha = new ReCaptcha( $secret );

			$response = $re_captcha->verifyResponse( wp_unslash( $_SERVER['REMOTE_ADDR'] ),sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) ); // Input var okay.

			if ( ! $response->success ) {
				$is_success = false;
				$form .= '<h2>Invalid Captcha!</h2>';
			}
		}


		if ( isset( $_POST['wpbr_review_form_submit'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['wpbr_review_form_nonce_field'] ) ), 'wpbr_review_form_action' ) && $is_success === true ) {
			$success_message = '<h2>Review Submit!</h2>';
			return $success_message ;
		}

		// $is_reviewed = get_children( array(
		// 	'post_type'   => 'cptreviewpress',
		// 	'post_parent' => get_the_id(),
		// ) );
		//
		// $already_reviewed = array_filter( $is_reviewed, function ( $e ) {
		// 	if ( get_current_user_id() == $e->post_author  ) {
		// 		return $e->post_author;
		// 	}
		// } );
		//
		// if ( ! empty( $already_reviewed ) ) {
		// 	return '<span class="wpbr_review_show">
		// 	' . esc_html__('You already reviewed.', 'reviewpress' ) .
		// 	'</span>';
		// }



		$form .= '<div id="wpbr_review_form_wrapper"><span class="wpbr_review_show">' . esc_html__('Leave Your Review', 'reviewpress') . '</span>';

		ob_start(); ?>
		<form class="" action="" method="POST" id="wpbr_review_form" name="wpbr_review_form">

			<?php if ( 'on' === review_get_option( 'name_field_display', 'wpbr_form') ) : ?>
				<div>
					<label class="wpbr_review_name"><?php echo review_get_option( 'name_field_text', 'wpbr_form' ) ?></label>
					<input type="text" name="wpbr_review_name" value="" <?php echo review_get_option( 'name_field_required', 'wpbr_form' ) == 'on' ? "required" : '' ?>>
				</div>
			<?php endif; ?>

			<?php if ( 'on' ===  review_get_option( 'title_field_display', 'wpbr_form' ) ) : ?>
				<div>
					<label class="wpbr_review_title"><?php echo esc_html( review_get_option( 'title_field_text', 'wpbr_form' ) )?></label>
					<input type="text" name="wpbr_review_title" value="<?php echo isset( $_POST['wpbr_review_title'] ) ? $_POST['wpbr_review_title'] : '' ?>" <?php echo review_get_option( 'title_field_display', 'wpbr_form' ) == 'on' ? "required" : '' ?> >
				</div>
			<?php endif; ?>

			<?php if ( 'on' === review_get_option( 'email_field_display', 'wpbr_form' ) ) :  ?>
				<div>
					<label class="wpbr_review_email"><?php echo esc_html( review_get_option( 'email_field_text', 'wpbr_form' ) )?></label>
					<input type="email" name="wpbr_review_email" value="<?php 	if ( is_user_logged_in() ) { echo  wp_get_current_user()->user_email; } else if(  isset( $_POST['wpbr_review_email'] ) ) { echo $_POST['wpbr_review_email']; } ?>"  <?php echo is_user_logged_in() ? 'readonly' : '' ?> <?php echo review_get_option( 'email_field_required', 'wpbr_form' ) == 'on' ? "required" : '' ?>>
					</div>
				<?php endif ?>

				<div>
					<label class="wpbr_review_rating"><?php echo esc_html( review_get_option( 'rating_field_text', 'wpbr_form' ) ) ?></label>
					<?php if ( 'heart' === review_get_option( 'review_icon', 'wpbr_display' ) ) : ?>
						<div id="heartType" style="color:<?php echo esc_html( review_get_option( 'rating_icon_color', 'wpbr_display' ) )?>" data-score="<?php  echo isset( $_POST['score'] ) ? $_POST['score'] : '' ?>" ></div>
					<?php else : ?>
						<div id="starType" style="color:<?php echo esc_html( review_get_option( 'rating_icon_color', 'wpbr_display' ) ) ?>" data-score="<?php  echo isset( $_POST['score'] ) ? $_POST['score'] : '' ?>"></div>
					<?php endif; ?>
				</div>
				<?php if ( 'on' === review_get_option( 'review_content_field_display', 'wpbr_form' ) ) : ?>
					<div>
						<label class="wpbr_review_message"><?php echo esc_html( review_get_option( 'review_content_field_text', 'wpbr_form' ) )?></label>
						<textarea name="wpbr_review_message" rows="3" cols="10"  <?php echo review_get_option( 'email_field_required', 'wpbr_form' ) == 'on' ? "required" : '' ?>><?php echo isset( $_POST['wpbr_review_message'] ) ? $_POST['wpbr_review_message'] : '' ?></textarea>
					</div>
				<?php endif ?>

				<input type="hidden" name="wpbr_review_post_id" value="<?php echo the_ID(); ?>">
				<input type="hidden" name="wpbr_review_user_id" value="<?php if ( get_current_user_id() ) echo get_current_user_id(); ?>">
				<input type="hidden" name="wpbr_review_post_page_title" value="<?php echo get_the_title() ?>">

				<?php
				if ( 'on' === review_get_option( 'google_captcha', 'wpbr_form' ) ) {
					echo '<div class="g-recaptcha" data-sitekey="'. esc_html( review_get_option( 'google_captcha_site_key', 'wpbr_form' ) ).'"> </div>';
				}
				?>
				<?php //wp_nonce_field( 'wpbr_review_form_action', 'wpbr_review_form_nonce_field' ); ?>

				<div class="submit_button">
					<span class="captcha-error"></span>
					<img src="<?php echo admin_url('/images/spinner.gif') ?>" style="display:none" class="spinner-loader">
					<input type="submit" name="wpbr_review_form_submit"  value="Submit">
					<span class="reviewpress-message"></span>
				</div>

			</form>
			<?php

			$form .= ob_get_clean();
			$form .= '</div>';
			return $form;
		}

		/**
		* To show user reviews
		*
		* @param [array] $args [shortcode args].
		*
		* @since 1.0.0
		*/
		public function review_press_show_shortcode( $args ) {

			if ( ! is_singular( ) ) {
				return;
			}

			global $post;

			shortcode_atts( array(
				'category' => '',
				'id'       => '',
				'number'   => '',
			), $args );

			$post_parent_id[] = ! empty( $args['id'] ) ? (int) $args['id']           : $post->ID ;
			$paged            = get_query_var( 'page' ) ? get_query_var( 'page' ) : 1;
			$posts_per_page   = ! empty( $args['number'] ) ? $args['number'] : review_get_option( 'number_of_reviews', 'wpbr_reviews' );

			$query_args = array(
				'post_type'      => 'cptreviewpress',
				'order'          => 'DESC',
				'posts_per_page' => $posts_per_page,
				'paged'          => $paged,
			);

			if ( ! empty( $args['category'] ) ) {
				if ( 'all' != $args['category'] ) {
					$args = array(
						'category_name'    => $args['category'],
						'post_status'      => 'publish',
					);

					$posts_array = get_posts( $args );
					$post_parent_id = array();
					foreach ( $posts_array as $post ) {
						$post_parent_id[] = $post->ID;
					}

					! empty( $posts_array ) ? $query_args['post_parent__in'] = $post_parent_id : $query_args['post_parent__in'] = array( null ) ;
				}
			} else {
				$query_args['post_parent__in'] = $post_parent_id;
			}

			if ( 'rating' === review_get_option( 'sort_review', 'wpbr_reviews' ) ) {
				$query_args['meta_key'] = 'wpbr_review_rating';
				$query_args['orderby']  = 'meta_value_num';
			} else {
				$query_args['orderby'] = 'date';
			}

			$query = new WP_Query( $query_args );
			// $form = '<div id="review_press_show" class="reviewpres-reviews-wrapper" >
			// 	<div class="review_press_inner">
			// 		<span class="review_press_title">WPBrigade</span>
			// 		<label id="raty_1" style="color:#000" class="reviewpress-rating"></label>
			// 		<div class="review_press_meta">
			// 			<span class="review_press_name"><a href="mailto:admin@domain.com">Burhan Dodhy</a></span>
			// 			<time>4 days, 22 hours ago</time>
			// 		</div> <!--  .review_press_meta -->
			// 		<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
			//
			// 	</div>
			// 	<div class="review_press_inner">
			// 		<span class="review_press_title">WPBrigade</span>
			// 		<label id="raty_2" style="color:#000" class="reviewpress-rating"></label>
			// 		<div class="review_press_meta">
			// 			<span class="review_press_name"><a href="mailto:admin@domain.com">Burhan Dodhy</a></span>
			// 			<time>4 days, 22 hours ago</time>
			// 		</div> <!--  .review_press_meta -->
			// 		<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
			//
			// 	</div>
			// </div>
			// <script>
			//            jQuery("#raty_1, #raty_2").raty({
			//               readOnly : true,
			//               cancel   : false,
			//               half     : true,
			//               score    : "3.7",
			//               starType : \'i\',
			//               starHalf : \'wpbr-star-half\',
			//               starOff  : \'wpbr-star-off\',
			//               starOn   : \'wpbr-star-on\',
			//            });
			//            </script>';
			$form = '<div id="review_press_show" class="reviewpres-reviews-wrapper" >';

			if ( $query->have_posts() ) :

				$form .= '<span class="wpbr_review_show">' . esc_html__('What people say.', 'reviewpress') . '</span>';

				while ( $query->have_posts() ) : $query->the_post();

				$form .= '<div class="review_press_inner">';

				// Title.
				if ( 'on' === review_get_option( 'title_field_display', 'wpbr_form' ) ) :
					$form .= '<span class="review_press_title">'. get_post_meta( $post->ID ,'wpbr_review_title',true ) .'</span>';
				endif;


				$form .= '<label id="raty_'. $post->ID.'_'.$query->current_post .'" style="color:'. review_get_option( 'rating_icon_color', 'wpbr_display' ) .'" class="reviewpress-rating"></label>';


				$form .= '<div class="review_press_meta">
								<span class="review_press_name">';

				// Review Name.
				if ( 'on' === review_get_option( 'name_field_display', 'wpbr_form' ) ) :					
					// $form .=	'<a href="mailto:'. get_post_meta( $post->ID, 'wpbr_review_email', true ) .'">'. get_post_meta( $post->ID, 'wpbr_review_name', true ) .'</a>';
					$form .= get_post_meta( $post->ID, 'wpbr_review_name', true ) ;
				endif;


				$form .= '</span>
				<time>'. get_the_date('F j, Y') .' - '. get_the_time('g:i a') .'</time>
				</div> <!--  .review_press_meta -->';

				$form .= '<p>'. get_post_meta( $post->ID , 'wpbr_review_message', true ) .'</p>';

				$form .= '</div>';
				// 	$form .= '<div class="wpbr_review_inner">';
				//
				// 	if ( 'on' === review_get_option( 'title_field_display', 'wpbr_form' ) ) :
				// 		$form .= '<div>
				//           <label class="wpbr_review_title">'. review_get_option( 'title_field_text', 'wpbr_form' ) .'</label>
				//           <label>'.get_post_meta( $post->ID ,'wpbr_review_title',true ) .'</label>
				//           </div>';
				// endif;
				//
				// 	if ( 'on' === review_get_option( 'name_field_display', 'wpbr_form' ) ) :
				// 		$form .= '<div>
				//           <label class="wpbr_review_title">'. review_get_option( 'name_field_text', 'wpbr_form' ) .'</label>
				//           <label>'.get_post_meta( $post->ID ,'wpbr_review_name',true ) .'</label>
				//           </div>';
				// endif;
				//
				// 	if ( 'on' === review_get_option( 'email_field_display', 'wpbr_form' ) ) :
				// 		$form .= '<div>
				//           <label class="wpbr_review_title">'. review_get_option( 'email_field_text', 'wpbr_form' ) .'</label>
				//           <label>'.get_post_meta( $post->ID ,'wpbr_review_email',true ) .'</label>
				//           </div>';
				// endif;
				//
				// 	$form .= '<div><label class="wpbr_review_title">'. review_get_option( 'rating_field_text', 'wpbr_form' ) .' </label>';
				// 	$form .= "<label id='raty_{$post->ID}_{$query->current_post}' style='color:".  review_get_option( 'rating_icon_color', 'wpbr_display' ) ."'></label></div>";
				//
				// 	if ( 'on' === review_get_option( 'review_content_field_display', 'wpbr_form' ) ) :
				// 		$form .= '<div>
				//           <label class="wpbr_review_title">'. review_get_option( 'review_content_field_text', 'wpbr_form' ) .'</label>
				//           <label>'.get_post_meta( $post->ID ,'wpbr_review_message',true ) .'</label>
				//           </div>';
				// endif;
				//
				if ( 'star' === review_get_option( 'review_icon', 'wpbr_display' ) ) {
					$form .= "<script>
					jQuery('#raty_{$post->ID}_{$query->current_post}').raty({
						readOnly : true,
						cancel   : false,
						half     : true,
						score    : '".get_post_meta( $post->ID ,'wpbr_review_rating',true )."',
						starType : 'i',
						starHalf : 'wpbr-star-half',
						starOff  : 'wpbr-star-off',
						starOn   : 'wpbr-star-on',
					});
					</script>";
				} else {
					$form .= "<script>
					jQuery('#raty_{$post->ID}_{$query->current_post}').raty({
						readOnly : true,
						cancel   : false,
						half     : true,
						score    : '".get_post_meta( $post->ID ,'wpbr_review_rating',true )."',
						starType : 'i',
						starHalf : 'wpbr-heart-half',
						starOff  : 'wpbr-heart-off',
						starOn   : 'wpbr-heart-on',
					});
					</script>";
				}
				//
				// 	$form .= '</div><br />';
			endwhile;

			if ( '1' != $paged ) {
				$form .= '<div class="alignleft"> <a href="'. home_url( add_query_arg( null, null ) ) . '?page='. ($paged - 1 ) .'" >Previous Page</a></div>';
			}

			if ( $paged != $query->max_num_pages ) {
				$form .= '<div class="alignright"> <a href="'. home_url( add_query_arg( null, null ) ) . '?page='.($paged + 1) .'" >Next Page</a></div> ';
			}

		endif;
		$form .= '</div>';
		wp_reset_postdata();
		return $form;

	}

	/**
	* To add schema.org rich data
	*
	* @param [array] $args [shortcode args].
	*
	* @since 1.0.0
	*/
	public function review_press_add_rich_data( $args ) {
		global $post;

		shortcode_atts( array(
			'category' => '',
			'id'       => '',
		), $args );

		$query_args = array(
			'post_type'      => 'cptreviewpress',
			'order'          => 'DESC',
		);

		$post_parent_id[] = ! empty( $args['id'] ) ? $args['id']           : $post->ID ;

		if ( ! empty( $args['category'] ) ) {
			if ( 'all' != $args['category'] ) {
				$args = array(
					'category_name'    => $args['category'],
					'post_status'      => 'publish',
				);

				$posts_array = get_posts( $args );
				$post_parent_id = array();
				foreach ( $posts_array as $post ) {
					$post_parent_id[] = $post->ID;
				}

				! empty( $posts_array ) ? $query_args['post_parent__in'] = $post_parent_id : $query_args['post_parent__in'] = array( null ) ;

			}
		} else {
			$query_args['post_parent__in'] = $post_parent_id;
		}

		$query = new WP_Query( $query_args );

		$total_rating = 0;
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post();

			$total_rating += intval( get_post_meta( $post->ID ,'wpbr_review_rating',true ) );
			$parent_title = get_the_title( wp_get_post_parent_id( $post->ID ) );

		endwhile;
		$rich_rating = "<div id='raty_rich' style='color:". review_get_option( 'rating_icon_color', 'wpbr_display' ) ."'></div>";

		if ( 'star' === review_get_option( 'review_icon', 'wpbr_display' ) ) {
			$rich_rating .= "<script>
			jQuery('#raty_rich').raty({
				readOnly : true,
				cancel   : false,
				half     : true,
				score    : '".round( $total_rating / $query->post_count , 2 )."',
				starType : 'i',
				starHalf : 'wpbr-star-half',
				starOff  : 'wpbr-star-off',
				starOn   : 'wpbr-star-on',
			});
			</script>";
		} else {
			$rich_rating .= "<script>
			jQuery('#raty_rich').raty({
				readOnly : true,
				cancel   : false,
				half     : true,
				score    : '".round( $total_rating / $query->post_count , 2 )."',
				starType : 'i',
				starHalf : 'wpbr-heart-half',
				starOff  : 'wpbr-heart-off',
				starOn   : 'wpbr-heart-on',
			});
			</script>";
		}

		ob_start();
		?>

		<div itemscope itemtype="http://schema.org/Product">
			<span itemprop="name" style="display:none"><?php echo esc_html( $parent_title ); ?></span>
			<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
				Rated <span itemprop="ratingValue"><?php echo esc_html( round( $total_rating / $query->post_count , 2 ) ); ?></span>/5 based on <span itemprop="reviewCount"><?php echo esc_html( $query->post_count ); ?></span> reviews
				<div style="display:none">
					<span itemprop="bestRating">5</span>
					<span itemprop="worstRating">1</span>
				</div>
			</div>
		</div>

		<?php
		$rich_rating .= ob_get_clean();

		return $rich_rating;

	endif;
	wp_reset_postdata();

}
}

$ReviewPress_Shortcodes_instance = new ReviewPress_Shortcodes();
?>
