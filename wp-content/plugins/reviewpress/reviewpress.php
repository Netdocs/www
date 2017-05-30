<?php
	/**
	 * Plugin Name: ReviewPress
	 * Plugin URI: http://wpbrigade.com/wordpress/plugins/reviewpress/
	 * Description: ReviewPress is the best WordPress plugin for Reviews and Ratings. It works for posts, pages and custom post types and includes Google reCaptcha as well.
	 * Version: 1.0.5
	 * Author: WPBrigade
	 * Author URI: http://WPBrigade.com/
	 * Text Domain: reviewpress
	 * Domain Path: /languages
	 *
	 * @package ReviewPress
	 */

// Create a helper function for easy SDK access.
function reviewpress_fs() {
    global $reviewpress_fs;

    if ( ! isset( $reviewpress_fs ) ) {
        // Include Freemius SDK.
        require_once dirname(__FILE__) . '/freemius/start.php';

        $reviewpress_fs = fs_dynamic_init( array(
            'id'                => '664',
            'slug'              => 'reviewpress',
            'type'              => 'plugin',
            'public_key'        => 'pk_b64b60d12f4185f31e175836c31ef',
            'is_premium'        => false,
            'has_addons'        => false,
            'has_paid_plans'    => false,
            'menu'              => array(
                'slug'       => 'edit.php?post_type=cptreviewpress',
                'account'    => false,
                'contact'    => false,
            ),
        ) );
    }

    return $reviewpress_fs;
}

// Init Freemius.
reviewpress_fs();

	define( 'REVIEWPRESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'REVIEWPRESS_ROOT_PATH', dirname( __FILE__ ) );
	define( 'REVIEWPRESS_VERSION' , '1.0.5' );
	define( 'REVIEWPRESS_SLUG', 'cptreviewpress' );

	/**
	 * Main Class
	 */
	class ReviewPress {
		/**
		 * To create instance of setting class.
		 *
		 * @var $settings_section
		 *
		 * @since 1.0.0
		 */
		private $settings_section;

			/**
			 * Class constructor
			 *
			 * @since 1.0.0
			 */
		function __construct() {

			add_action( 'admin_menu', array( $this, 'review_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'wpbr_admin_scripts' ) ,  10, 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'wpbr_admin_styles' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'wpbr_front_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wpbr_front_styles' ) );

			add_action( 'init' , array( $this, 'wpbr_save_review_form' ) );
			add_action( 'init' , array( $this, 'review_post_type' ) , 1 );

			$this->includes();
			$this->settings_section = new ReviewPress_Setting();

			// $this->pending_reviews = new
			add_action( 'save_post', array( $this, 'save_reviews_values' ), 20, 3 );

			// For custom CSS
			add_action( 'wp_enqueue_scripts', array( $this, 'wpbr_register_style' ), 99 );
			add_action( 'plugins_loaded', array( $this, 'wpbr_maybe_print_css' ) );

			// To add settings default values.
			add_action( 'activated_plugin', array( $this, 'wpbr_setting_default' ) );

			add_action( 'admin_print_styles-post.php', array( $this, 'wpbr_remove_preview_button' ) );
			add_action( 'admin_head-post-new.php' , array( $this, 'wpbr_remove_preview_button' ) );
			add_action( 'init', array( $this, 'wpbr_textdomain' ) );
			add_action( 'admin_menu',array( $this, 'add_pending_reviews_bubble' ) , 99 );

				// initiating ShortCodes
			add_action( 'admin_head', array( $this, 'reviewpress_shortcode_button' ) );
			add_action( 'wp_ajax_reviewpress_advanced_shortcode', array( $this, 'reviewpress_shortcode_view' ) );

			add_action( 'wp_ajax_reviewpress_submit',  array( $this, 'review_submit' ) );
			add_action( 'wp_ajax_nopriv_reviewpress_submit',  array( $this, 'review_submit' ) );

			add_action( 'wp_ajax_get_ajax_reviews',  array( $this, 'get_ajax_reviews' ) );
			add_action( 'wp_ajax_nopriv_get_ajax_reviews',  array( $this, 'get_ajax_reviews' ) );

			add_filter( 'bulk_actions-edit-' . REVIEWPRESS_SLUG, array( $this, 'register_bulk_approve_actions' ) );
			add_filter( 'handle_bulk_actions-edit-' . REVIEWPRESS_SLUG, array( $this,  'bulk_approve_handler' ), 10, 3 );
			add_action( 'admin_notices', array( $this, 'bulk_approve_message' ) );
		}


		public function reviewpress_shortcode_view() {
			include_once( REVIEWPRESS_ROOT_PATH . '/include/shortcode.php' );
			wp_die();
		}


		function get_ajax_reviews() {

			global $post;

			$post_parent_id[] = sanitize_text_field( wp_unslash( $_POST['pageId'] ) );
			$paged            = get_query_var( 'page' ) ? get_query_var( 'page' ) : 1;
			$posts_per_page   = ! empty( $args['number'] ) ? $args['number'] : review_get_option( 'number_of_reviews', 'wpbr_reviews' );

			$query_args = array(
				'post_type'      => 'cptreviewpress',
				'order'          => 'DESC',
				'posts_per_page' => $posts_per_page,
				'paged'          => $paged,
				'post_status'    => 'publish',
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
					$form .=	'<a href="mailto:'. get_post_meta( $post->ID, 'wpbr_review_email', true ) .'">'. get_post_meta( $post->ID, 'wpbr_review_name', true ) .'</a>';
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
		echo $form;
		wp_die();
	}


		function review_submit() {


			$postarr = array(
			'post_author'           => ! empty( $_POST['wpbr_review_user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_user_id'] ) ) : '',
			'post_title'            => ! empty( $_POST['wpbr_review_title'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_title'] ) ) : '',
			'post_status'           => review_get_option( 'auto_approve_rievew', 'wpbr_reviews'),
			//  get_option( 'wpbr_reviews' )['auto_approve_rievew'],
			'post_type'             => 'cptreviewpress',
			'post_parent'           => ! empty( $_POST['wpbr_review_post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_post_id'] ) ) : '',
			);

			$post_id = wp_insert_post( $postarr );

			update_post_meta( $post_id, 'wpbr_review_name', ! empty( $_POST['wpbr_review_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_name'] ) ) : '' );
			update_post_meta( $post_id, 'wpbr_review_title', ! empty( $_POST['wpbr_review_title'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_title'] ) ) : '' );
			update_post_meta( $post_id, 'wpbr_review_email', ! empty( $_POST['wpbr_review_email'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_email'] ) ) : '' );
			update_post_meta( $post_id, 'wpbr_review_rating', ! empty( $_POST['score'] ) ? sanitize_text_field( wp_unslash( $_POST['score'] ) ) : '' );
			update_post_meta( $post_id, 'wpbr_review_message', ! empty( $_POST['wpbr_review_message'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_message'] ) ) : '' );


		 if( review_get_option( 'auto_approve_rievew', 'wpbr_reviews') == 'pending' ) {
			 $message = array(
				 'message'      => 'Your review has been recorded and submitted for approval. Thanks!',
				 'auto_approve' => false
			 );

		 } else {
			 $message = array(
				 'message'      => 'Review Successfully Submit',
				 'auto_approve' => true
			 );
		 }

			echo  json_encode( $message ) ;
			wp_die();
		}

		/**
		 * Add all essentials files.
		 *
		 * @since 1.0.1
		 */
		public function includes() {

			include REVIEWPRESS_ROOT_PATH . '/include/utils.php';
			include REVIEWPRESS_ROOT_PATH . '/include/api/class.settings-api.php';
			include REVIEWPRESS_ROOT_PATH . '/include/class-setting.php';
			include REVIEWPRESS_ROOT_PATH . '/include/class-all-review.php';
			include REVIEWPRESS_ROOT_PATH . '/include/class-shortcode.php';
			include REVIEWPRESS_ROOT_PATH . '/include/class.upgrade.php';
			if ( 'on' === review_get_option( 'google_captcha', 'wpbr_form')  ) { // get_option( 'wpbr_form')['google_captcha']
				include( REVIEWPRESS_ROOT_PATH . '/include/api/recaptchalib.php' );
			}

		}


		/**
		 * Add Rreview_Press Menu.
		 *
		 * @since 1.0.0
		 */
		public function review_menu() {

			add_menu_page( 'ReviewPress', __( 'ReviewPress', 'reviewpress' ), 'manage_options', 'all_reviews', array( $this, 'review_setting_file' ) , 'none', '25' );

			add_submenu_page( 'all_reviews', 'Add Review', __( 'Add Review', 'reviewpress' ), 'manage_options', 'post-new.php?post_type=cptreviewpress' );
			add_submenu_page( 'all_reviews', 'Settings', __( 'Settings', 'reviewpress' ), 'manage_options', 'reviewpress_settings' , array( $this, 'review_setting_file' ) );
			add_submenu_page( 'all_reviews', 'Shortcode', __( 'Shortcode', 'reviewpress' ), 'manage_options', 'reviewpress_shortcode' , array( $this, 'review_setting_file' ) );

		}

		/**
		 * Add js code for admin menu.
		 *
		 * @param  int $hook  suffix for the current admin page.
		 * @since 1.0.0
		 */
		function wpbr_admin_scripts( $hook ) {

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'main-js', plugins_url( 'assets/js/admin-main.js', __FILE__ ), false, REVIEWPRESS_VERSION );

			if ( 'reviewpress_page_reviewpress_settings' === $hook ) {

				wp_enqueue_style( 'codemirror_css', plugins_url( 'assets/codemirror/codemirror.css', __FILE__ ) );
				wp_enqueue_script( 'codemirror', plugins_url( 'assets/codemirror/codemirror.js', __FILE__ ), array( 'jquery' ), REVIEWPRESS_VERSION );
				wp_enqueue_script( 'codemirror_js_css', plugins_url( 'assets/codemirror/css.js', __FILE__ ), array( 'codemirror' ), REVIEWPRESS_VERSION );
				wp_enqueue_script( 'codemirror_activeline', plugins_url( 'assets/codemirror/active-line.js', __FILE__ ), array( 'codemirror' ), REVIEWPRESS_VERSION );

			}

		}

		/**
		 * Add css for admin panel.
		 *
		 * @since 1.0.0
		 */
		public function wpbr_admin_styles() {

			wp_enqueue_style( 'review_styles', plugins_url( 'assets/css/style.css', __FILE__ ), false, REVIEWPRESS_VERSION );
		}

		/**
		 * Add css for front end
		 *
		 * @since 1.0.0
		 */
		public function wpbr_front_styles() {
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'review_styles', plugins_url( 'assets/css/front.css', __FILE__ ), false, REVIEWPRESS_VERSION );
		}

		/**
		 * Add js for front end
		 *
		 * @since 1.0.0
		 */
		function wpbr_front_scripts() {

			wp_enqueue_script( 'jquery' );

			wp_enqueue_script( 'front_main', plugins_url( 'assets/js/front-main.js', __FILE__ ) , $deps = array(), REVIEWPRESS_VERSION , true );
			wp_localize_script( 'front_main', 'reviewpress' ,array(
				'ajaxurl' => admin_url('admin-ajax.php')
				) );

			if ( 'on' === review_get_option( 'google_captcha', 'wpbr_form' ) ) { // get_option( 'wpbr_form' )['google_captcha']
				wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js' );
			}

			wp_enqueue_script( 'raty', plugins_url( 'assets/js/jquery.raty.js', __FILE__ ) , array( 'jquery' ) );

		}


		/**
		 * Include setting classes
		 *
		 * @since 1.0.0
		 */
		function review_setting_file() {

			$screen = get_current_screen();
			if ( strpos( $screen->base, 'reviewpress_settings' ) !== false ) {

				$this->settings_section->plugin_page();

			} else if ( strpos( $screen->base, 'reviewpress_shortcode' ) !== false ) {

				include_once( REVIEWPRESS_ROOT_PATH.'/include/shortcode-page.php' );

			}
		}

		/**
		 * Add textdomain for translation.
		 *
		 * @since 1.0.0
		 */
		public function wpbr_textdomain() {

			load_plugin_textdomain( 'reviewpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Add Review post type
		 *
		 * @since 1.0.0
		 */
		public function review_post_type() {

			$labels = array(
			'name'               => _x( 'Reviews', 'post type general name', 'reviewpress' ),
			'singular_name'      => _x( 'Review', 'post type singular name', 'reviewpress' ),
			'menu_name'          => _x( 'Reviews', 'admin menu', 'reviewpress' ),
			'name_admin_bar'     => _x( 'Review', 'add new on admin bar', 'reviewpress' ),
			'add_new'            => _x( 'Add New', 'review', 'reviewpress' ),
			'add_new_item'       => __( 'Add New Review', 'reviewpress' ),
			'new_item'           => __( 'Add Review', 'reviewpress' ),
			'edit_item'          => __( 'Edit Review', 'reviewpress' ),
			'view_item'          => __( 'View Review', 'reviewpress' ),
			'all_items'          => __( 'All Reviews', 'reviewpress' ),
			'search_items'       => __( 'Search Reviews', 'reviewpress' ),
			'parent_item_colon'  => __( 'Parent Reviews:', 'reviewpress' ),
			'not_found'          => __( 'No reviews found.', 'reviewpress' ),
			'not_found_in_trash' => __( 'No reviews found in Trash.', 'reviewpress' ),
			);

			$args = array(
			'labels'               => $labels,
			'public'               => false,
			'publicly_queryable'   => true,
			'show_ui'              => true,
			'show_in_menu'         => 'all_reviews',
			'query_var'            => true,
			'rewrite'              => array( 'slug' => 'review' ),
			'capability_type'      => 'post',
			'has_archive'          => true,
			'hierarchical'         => false,
			'menu_position'        => null,
			'supports'             => false,
			'register_meta_box_cb' => array( $this , 'review_custom_fields' ),
			);

			register_post_type( 'cptreviewpress', $args );

		}

		/**
		 * Review post type custom meta
		 *
		 * @since 1.0.0
		 */
		function review_custom_fields() {
			add_meta_box( 'wpbr_review_meta_box', 'Fill Fields', array( $this, 'custom_fields_metabox' ), 'cptreviewpress', 'normal', 'high' );

		}

		/**
		 * Review post meta fields
		 *
		 * @since 1.0.0
		 */
		function custom_fields_metabox() {

			global $post;
			$message_value  = get_post_meta( $post->ID, 'wpbr_reviewer_message', $single = true );
			echo '<input type="hidden" name="review_meta_noncename" id="review_meta_noncename" value="' .
			esc_html( wp_create_nonce( plugin_basename( __FILE__ ) ) ) . '" />';

			echo '<label>Name</label>';
			echo '<input type="text" placeholder="Enter Name Here" name="wpbr_review_name" value="'.esc_html( get_post_meta( $post->ID , 'wpbr_review_name' , true ) ).'" class="widefat" />';
			echo '<label>Title</label>';
			echo '<input type="text" placeholder="Enter Title Here" name="wpbr_review_title" value="'. esc_html( get_post_meta( $post->ID , 'wpbr_review_title' , true ) ).'" class="widefat" />';
			echo '<label>Email</label>';
			echo '<input type="email" placeholder="Enter Email Here" name="wpbr_review_email" value="'.esc_html( get_post_meta( $post->ID , 'wpbr_review_email' , true ) ).'" class="widefat" />';
			echo '<label>Rating</label>';
			echo '<input type="number" placeholder="Enter Rating Here" min="1" max="5" step="any" name="wpbr_review_rating" value="'.esc_html( get_post_meta( $post->ID , 'wpbr_review_rating' , true ) ).'" class="widefat" />';
			echo '<label>Parent Post ID</label>';
			echo '<input type="number" min="0" name="wpbr_review_parent_post" value="'.esc_html( wp_get_post_parent_id( $post->ID ) ).'" class="widefat" />';
			echo '<label>Message</label>';
			echo '<textarea placeholder="Enter Message Here" rows="7" cols="7" name="wpbr_review_message" class="widefat">'.esc_html( get_post_meta( $post->ID , 'wpbr_review_message' , true ) ).'</textarea>';

		}

		/**
		 * Save review type meta values
		 *
		 * @param int  $post_id The post ID.
		 * @param post $post The post object.
		 * @param bool $update Whether this is an existing post being updated or not.
		 *
		 * @since 1.0.0
		 */
		function save_reviews_values( $post_id, $post, $update ) {

			if ( 'cptreviewpress' != $post->post_type ) {
				return;
			}

			if ( isset( $_GET['actions'] ) ) {
				return;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
					return;
			}

			if ( ! isset( $_POST['review_meta_noncename'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['review_meta_noncename'] ) ), plugin_basename( __FILE__ ) ) ) {
				return ;
			}

			if ( ! current_user_can( 'edit_post', $post->ID ) ) {
				return ;
			}

			if ( isset( $_POST['wpbr_review_form_submit'] ) ) {
				return;
			}

			remove_action( 'save_post', array( $this, 'save_reviews_values' ) , 20 );

			update_post_meta( $post_id, 'wpbr_review_name', ! empty( $_POST['wpbr_review_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_name'] ) ) : '' );
			update_post_meta( $post_id, 'wpbr_review_title', ! empty( $_POST['wpbr_review_title'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_title'] ) ) : '' );
			update_post_meta( $post_id, 'wpbr_review_email', ! empty( $_POST['wpbr_review_email'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_email'] ) ) : '' );
			update_post_meta( $post_id, 'wpbr_review_rating', ! empty( $_POST['wpbr_review_rating'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_rating'] ) ) : '' );
			update_post_meta( $post_id, 'wpbr_review_message', ! empty( $_POST['wpbr_review_message'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_message'] ) ) : '' );

			wp_update_post( array(
				'ID'          => $post_id,
				'post_parent' => ! empty( $_POST['wpbr_review_parent_post'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_parent_post'] ) ) : '',
				'post_title'  => ! empty( $_POST['wpbr_review_title'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_title'] ) ) : '',
			) );

			add_action( 'save_post', array( $this, 'save_reviews_values' ) , 20 );

		}


		/**
		 * Save review form values
		 *
		 * @since 1.0.0
		 */
		function wpbr_save_review_form() {

			if ( isset( $_POST['wpbr_review_form_submit'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['wpbr_review_form_nonce_field'] ) ), 'wpbr_review_form_action' )  ) {

				$is_reviewed = get_children( array(
					'post_type'   => 'cptreviewpress',
					'post_parent' => sanitize_text_field( wp_unslash( $_POST['wpbr_review_post_id'] ) ), // Input var Okay.
				) );

				$already_reviewed = array_filter( $is_reviewed, function ( $e ) {
					// get_post_meta( $e->ID, 'wpbr_review_email', true )
					if ( get_current_user_id() == $e->post_author  ) {
						return $e->post_author;
					}
				} );

				if ( ! empty( $already_reviewed ) ) {
					return ;
				}

				/**
				*  Google ReCaptcha Validation
				*/
				if ( 'on' ===  $this->settings_section->get_option( 'google_captcha', 'wpbr_form' ) ) { //get_option( 'wpbr_form' )['google_captcha']

					// $secret = get_option( 'wpbr_form' )['google_captcha_secret_key'];
					$secret = $this->settings_section->get_option( 'google_captcha_secret_key', 'wpbr_form' );

					// Empty response.
					$response = null;

					// Check secret key.
					$re_captcha = new ReCaptcha( $secret );

					// if ( isset( $_POST['g-recaptcha-response'] ) && sanitize_key( wp_unslash( $_POST['g-recaptcha-response'] ) ) ) { // Input var okay.
						$response = $re_captcha->verifyResponse( wp_unslash( $_SERVER['REMOTE_ADDR'] ),sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) ); // Input var okay.
					// }

					if ( ! $response->success ) {
						return;
					}
				}

					$postarr = array(
					'post_author'           => ! empty( $_POST['wpbr_review_user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_user_id'] ) ) : '',
					'post_title'            => ! empty( $_POST['wpbr_review_title'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_title'] ) ) : '',
					'post_status'           =>  $this->settings_section->get_option( 'auto_approve_rievew', 'wpbr_reviews' ),
					//  get_option( 'wpbr_reviews' )['auto_approve_rievew']
					'post_type'             => 'cptreviewpress',
					'post_parent'           => ! empty( $_POST['wpbr_review_post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_post_id'] ) ) : '',
					);

					$post_id = wp_insert_post( $postarr );

					update_post_meta( $post_id, 'wpbr_review_name', ! empty( $_POST['wpbr_review_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_name'] ) ) : '' );
					update_post_meta( $post_id, 'wpbr_review_title', ! empty( $_POST['wpbr_review_title'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_title'] ) ) : '' );
					update_post_meta( $post_id, 'wpbr_review_email', ! empty( $_POST['wpbr_review_email'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_email'] ) ) : '' );
					update_post_meta( $post_id, 'wpbr_review_rating', ! empty( $_POST['score'] ) ? sanitize_text_field( wp_unslash( $_POST['score'] ) ) : '' );
					update_post_meta( $post_id, 'wpbr_review_message', ! empty( $_POST['wpbr_review_message'] ) ? sanitize_text_field( wp_unslash( $_POST['wpbr_review_message'] ) ) : '' );


			}
		}

		/**
		 * For enqeue custom styles
		 */
		function wpbr_register_style() {
			$url = home_url();
			if ( is_ssl() ) {
				$url = home_url( '/', 'https' );
			}

			wp_register_style( 'review_press_custom_style', add_query_arg( array( 'review_press_custom_style' => 1 ), $url ) );

			wp_enqueue_style( 'review_press_custom_style' );
		}

		/**
		 * If the query var is set, print the Simple Custom CSS rules.
		 */
		public function wpbr_maybe_print_css() {

			// Only print CSS if this is a stylesheet request.
			if ( ! isset( $_GET['review_press_custom_style'] ) || intval( $_GET['review_press_custom_style'] ) !== 1 ) {
				return;
			}

			ob_start();
			header( 'Content-type: text/css' );
			$options     = review_get_option( 'custom_css', 'wpbr_custom_css' );
			$raw_content = isset( $options ) ? $options : '';
			$content     = wp_kses( $raw_content, array( '\'', '\"' ) );
			$content     = str_replace( '&gt;', '>', $content );
			echo  $content ;
			die();
		}

		/**
		 * Set Default Values of Setting on plugin activation first time
		 *
		 * @since 1.0.0
		 */
		public function wpbr_setting_default() {

			if ( ! get_option( 'wpbr_display' ) && ! get_option( 'wpbr_reviews' )  &&  ! get_option( 'wpbr_form' ) ) {
				update_option('wpbr_display' , array(
					'review_authorization'          => 'all_users',
					'review_icon'                   => 'star',
					'rating_icon_color' 			=> '#0a0000',
				));
				update_option( 'wpbr_reviews', array(
					'auto_approve_rievew'          => 'pending',
					'sort_review'                  => 'rating',
					'number_of_reviews'            => '10',
				));
				update_option( 'wpbr_form', array(
					'google_captcha'                => 'off',
					'google_snippet'                => 'no',
					'name_field_text'               => 'Name',
					'name_field_display'            => 'on',
					'email_field_text'              => 'Email',
					'email_field_display'           => 'on',
					'title_field_text'              => 'Title',
					'title_field_display'           => 'on',
					'rating_field_text'             => 'Rating',
					'review_content_field_text'     => 'Review Comment',
					'review_content_field_display'  => 'on',
					'name_field_required'           => 'on',
					'title_field_required'          => 'on',
					'email_field_required'          => 'on',
					'review_content_field_required' => 'on',
				));
			}
		}

		/**
		 * Remove preveiw button from review post type
		 *
		 * @since 1.0.0
		 */
		function wpbr_remove_preview_button() {

			if ( 'cptreviewpress' == get_current_screen()->post_type ) {
				$style = '';
				$style .= '<style type="text/css">';
				$style .= '#preview-action ,#post-body-content';
				$style .= '{display: none; }';
				$style .= '</style>';

				echo $style;
			}
		}

		/**
		 * Add pending reviews bubble notifcation.
		 *
		 * @since 1.0.0
		 */
		public function add_pending_reviews_bubble() {
			global $wpdb;

			$pend_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts  WHERE post_type = 'cptreviewpress' AND post_status = 'pending' " );

			global $menu;

			foreach ( $menu as $key => $value ) {

				if ( 'ReviewPress' == $menu[ $key ][0] ) {
					$menu[ $key ][0] .= " <span class='update-plugins count-$pend_count'><span class='plugin-count'>" . $pend_count . '</span></span>';
					return;
				}
			}
		}


		/**
		 * Add ShortCode Button.
		 *
		 * @since 1.0.1
		 */
		function reviewpress_shortcode_button() {

			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return;
			}

			if ( get_user_option( 'rich_editing' ) == 'true' ) {

				add_filter( 'mce_external_plugins', array( $this, 'reviewpress_stats_js' ) );
				add_filter( 'mce_buttons',          array( $this, 'register_reviewpress_button' ) );
			}
		}

		/**
		 * Add Js for shoetcode
		 *
		 * @since 1.0.1
		 */
		function reviewpress_stats_js( $plugin_array ) {

			$plugin_array['reviewpressbutton'] = plugins_url( 'assets/js/shortcode.js',  __FILE__   );
			return $plugin_array;
		}

		/**
		 * Register Shortcode Button.
		 *
		 * @since 1.0.1
		 */
		function register_reviewpress_button( $buttons ) {
			array_push( $buttons, '|', 'reviewpressbutton' );
			return $buttons;
		}

		function register_bulk_approve_actions( $bulk_actions ) {
			$bulk_actions[ REVIEWPRESS_SLUG.'-approve' ] = __( 'Approve', 'reviewpress');
			return $bulk_actions;
		}

		function bulk_approve_handler( $redirect_to, $doaction, $post_ids ) {
			if ( $doaction !== REVIEWPRESS_SLUG.'-approve' ) {
				return $redirect_to;
			}

			foreach ( $post_ids as $post_id ) {
				$my_post                = array();
				$my_post['ID']          = sanitize_text_field( wp_unslash( $post_id ) );
				$my_post['post_status'] = 'publish';
				wp_update_post( $my_post );
			}
			$redirect_to = add_query_arg( 'bulk_review_approve', count( $post_ids ), $redirect_to );
			return $redirect_to;
		}

		function bulk_approve_message() {
			if ( ! empty( $_REQUEST['bulk_review_approve'] ) ) {
				$emailed_count = intval( $_REQUEST['bulk_review_approve'] );

				$class = 'notice notice-success';
				$message = __( 'Reviews Approved', 'reviewpress' );

				printf( '<div class="%1$s"><p>%2$s %3$s</p></div>', $class,$emailed_count, $message );

			}
		}

	}

	$reviewpress_instance = new ReviewPress();
