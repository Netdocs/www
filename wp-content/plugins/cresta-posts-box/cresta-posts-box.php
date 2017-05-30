<?php
/**
 * Plugin Name: Cresta Posts Box
 * Plugin URI: https://crestaproject.com/downloads/cresta-posts-box/
 * Description: <strong>*** <a href="https://crestaproject.com/downloads/cresta-posts-box/" target="_blank">Get Cresta Posts Box PRO</a> ***</strong> Show the next or previous post in a box that appears when the user scrolls to the bottom of a current post.
 * Version: 1.0.9
 * Author: CrestaProject - Rizzo Andrea
 * Author URI: https://crestaproject.com
 * Domain Path: /languages
 * Text Domain: cresta-posts-box
 * License: GPL2
 */
 
define( 'CRESTA_POSTS_BOX_PLUGIN_VERSION', '1.0.9' );
add_action('admin_menu', 'cresta_posts_box_menu');
add_action('admin_init', 'register_posts_box_button_setting' );
add_action('wp_enqueue_scripts', 'cresta_posts_box_wp_enqueue_scripts');
add_action('admin_enqueue_scripts', 'cresta_posts_box_admin_enqueue_scripts');

require_once( dirname( __FILE__ ) . '/cresta-posts-metabox.php' );

function cresta_posts_box_menu() {
	global $cresta_box_options_page;
	$cresta_box_options_page = add_menu_page( esc_html__( 'Cresta Posts Box Settings', 'cresta-posts-box' ), 'CPB FREE', 'manage_options', 'cresta-posts-box', 'cresta_posts_box_option', esc_url(plugins_url( '/cresta-posts-box/images/cpb-icon.png' )), 81 );
}

function cresta_posts_box_setting_link($links) { 
	$settings_link = array(
		'<a href="' . admin_url('admin.php?page=cresta-posts-box') . '">' . esc_html__( 'Settings','cresta-posts-box') . '</a>',
	);
	return array_merge( $links, $settings_link );
}
function cresta_box_meta_links( $links, $file ) {
	if ( strpos( $file, 'cresta-posts-box.php' ) !== false ) {
		$new_links = array(
			'<a href="https://crestaproject.com/downloads/cresta-posts-box/" target="_blank" rel="external" >' . esc_html__( 'Upgrade to PRO','cresta-posts-box' ) . '</a>', 
		);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'cresta_posts_box_setting_link' );
add_filter('plugin_row_meta', 'cresta_box_meta_links', 10 , 2 );


function crestapostsbox_textdomain() {
    load_plugin_textdomain( 'cresta-posts-box', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'crestapostsbox_textdomain' );


/* Plugin enqueue style and script */
function cresta_posts_box_wp_enqueue_scripts() {
	$cresta_posts_current_post_type = get_post_type();
	$cpb_options = get_option( 'crestapostsbox_settings' );
	$box_show_on = explode (',',$cpb_options['cresta_posts_box_selected_page'] );
	if ( is_singular() && in_array( $cresta_posts_current_post_type, $box_show_on ) ) {
		wp_enqueue_style( 'cresta-posts-box-style', plugins_url('css/cresta-posts-box-style.css',__FILE__), array(), CRESTA_POSTS_BOX_PLUGIN_VERSION);
		wp_enqueue_script( 'cresta-posts-box-js', plugins_url('js/jquery.cresta-posts-box.js',__FILE__), array('jquery'), CRESTA_POSTS_BOX_PLUGIN_VERSION, true );
	}
}

/* Plugin enqueue admin style and script */
function cresta_posts_box_admin_enqueue_scripts( $hook ) {
	global $cresta_box_options_page;
	if ( $hook == $cresta_box_options_page ) {
		wp_enqueue_style( 'cresta-social-admin-style', plugins_url('css/cresta-posts-box-admin-css.css',__FILE__), array(), CRESTA_POSTS_BOX_PLUGIN_VERSION);
		wp_enqueue_script( 'cresta-posts-box-admin-js', plugins_url('js/jquery.cresta-posts-box-admin-js.js',__FILE__), array('jquery'), CRESTA_POSTS_BOX_PLUGIN_VERSION, true );
	}
}

/* Register Settings */
function register_posts_box_button_setting() {
	register_setting( 'cpbplugin', 'crestapostsbox_settings','crestapostsbox_options_validate' );
	$cpb_options_arr = array(
		'cresta_posts_box_selected_page' => 'post',		
		'cresta_posts_box_font_size' => '13',
		'cresta_posts_box_line_height' => '20',
		'cresta_posts_box_box_shadow' => '1',
		'cresta_posts_box_z_index' => '99',
		'cresta_posts_box_float' => 'right',
		'cresta_posts_box_width' => '380',
		'cresta_posts_box_position_bottom' => '10',
		'cresta_posts_box_position_left_right' => '0',
		'cresta_posts_box_title' => 'Recommended',
		'cresta_posts_box_close_button' => '1',
		'cresta_posts_box_what_show' => 'previous',
		'cresta_posts_box_show_image' => '1',
		'cresta_posts_box_image_width' => '80',
		'cresta_posts_box_image_height' => '80',
		'cresta_posts_box_show_excerpt' => '1',
		'cresta_posts_box_excerpt_words' => '10',
		'cresta_posts_box_mobile' => '1',
		'cresta_posts_box_credit' => '0',
		'cresta_posts_box_custom_css' => '',
	);
	add_option( 'crestapostsbox_settings', $cpb_options_arr );
}

/* CSS Code filter to header */ 
function cresta_posts_box_css_top() {
		$cresta_posts_current_post_type = get_post_type();
		$cpb_options = get_option( 'crestapostsbox_settings' );
		$box_show_on = explode (',',$cpb_options['cresta_posts_box_selected_page'] );
		if ( is_singular() && in_array( $cresta_posts_current_post_type, $box_show_on ) ) {
			$what_show = $cpb_options['cresta_posts_box_what_show'];
			if ($what_show == 'previous') {
				$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
				if ( ! $previous) {
					return;
				}
			} elseif ($what_show == 'next') {
				$next = get_adjacent_post( false, '', false );
				if ( ! $next) {
					return;
				}
			}
			$checkCrestaMetaBox = get_post_meta(get_the_ID(), '_get_cresta_posts_box_plugin', true);
			if ( $checkCrestaMetaBox == '1' ) {
				return;
			}
				$font_size = $cpb_options ['cresta_posts_box_font_size'];
				$line_height = $cpb_options['cresta_posts_box_line_height'];
				$z_index = $cpb_options['cresta_posts_box_z_index'];
				$box_position = $cpb_options['cresta_posts_box_float'];
				$box_width = $cpb_options['cresta_posts_box_width'];
				$box_position_bottom = $cpb_options['cresta_posts_box_position_bottom'];
				$box_position_left_right = $cpb_options['cresta_posts_box_position_left_right'];
				$show_image = $cpb_options['cresta_posts_box_show_image'];
				$box_shadow = $cpb_options['cresta_posts_box_box_shadow'];
				
				echo "<style type='text/css'>";
				if (current_theme_supports('post-thumbnails') && $show_image == '1' ) {
					$image_width = $cpb_options['cresta_posts_box_image_width'];
					$image_height = $cpb_options['cresta_posts_box_image_height'];
					echo "
					.crestaBoxImage {height: ". intval($image_height) ."px; }
					.crestaBoxImage img {width: ". intval($image_width) ."px; }
					";
				}
				if ($box_shadow == '1') {
					echo "
					.crestaPostsBox.show { -webkit-box-shadow: 0 0 3px rgba(0, 0, 0, 0.2); -moz-box-shadow: 0 0 3px rgba(0, 0, 0, 0.2); box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);}
					";
				}
				echo "
					.crestaPostsBox { width: ". intval($box_width) ."px; ". esc_attr($box_position) .": -". intval($box_width) ."px; bottom: ". intval($box_position_bottom) ."%; z-index: ". intval($z_index) ."; font-size: ". intval($font_size) ."px; line-height: ". intval($line_height) ."px; }
					.crestaPostsBox.show {". esc_attr($box_position) .": ". esc_attr($box_position_left_right) ."px;}
					@media all and (max-width: 767px) {
						.crestaPostsBox {". esc_attr($box_position) .": -100%;}
						.crestaPostsBox.show {". esc_attr($box_position) .": 0px;}
					}
				";
				echo "</style>";
		}
}
add_action('wp_head', 'cresta_posts_box_css_top');

/* This is the output */
function add_cresta_posts_box() {
	if (is_singular()) {
		$cpb_options = get_option( 'crestapostsbox_settings' );
		$box_show_on = explode (',',$cpb_options['cresta_posts_box_selected_page'] );
		$args = array(
			'public'   => true,
		);
		$post_types = get_post_types( $args, 'names', 'and' ); 
		foreach ( $post_types as $post_type ) { 
			if ( is_singular( $post_type ) && !in_array( $post_type, $box_show_on )  ) {
				return;
			}
		}
		$checkCrestaMetaBox = get_post_meta(get_the_ID(), '_get_cresta_posts_box_plugin', true);
		if ( $checkCrestaMetaBox == '1' ) {
			return;
		}
		$what_show = $cpb_options['cresta_posts_box_what_show'];
		/* Display Previous Post */
		if ($what_show == 'previous') {
			$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
			if ( ! $previous ) {
				return;
			} else {
				$box_title = $cpb_options['cresta_posts_box_title'];
				$close_button = $cpb_options['cresta_posts_box_close_button'];
				$show_image = $cpb_options['cresta_posts_box_show_image'];
				$show_excerpt = $cpb_options['cresta_posts_box_show_excerpt'];
				$show_mobile = $cpb_options['cresta_posts_box_mobile'];
				$show_credit = $cpb_options['cresta_posts_box_credit'];
				if ($show_mobile == '1') {
					$the_mobile = 'isMobile';
				} else {
					$the_mobile = 'noMobile';
				}
				echo '<div class="crestaPostsBox active ' . esc_attr( $the_mobile ) . '">';
				if ($close_button == '1') {
					echo '<span class="crestaCloseBox"></span>';
				}
				echo '<span class="crestaBoxName">' . esc_html( $box_title ) . '</span>';
				echo '<div class="crestaPostsBoxContent">';
				if (current_theme_supports('post-thumbnails') && has_post_thumbnail( $previous->ID ) && $show_image == '1' ) {
					$prevthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $previous->ID ), 'medium' );
					echo '<div class="crestaBoxImage"><img src="' . esc_url( $prevthumb[0] ) . '" alt="' . esc_attr( $box_title ) . '"/></div>';
				}
				previous_post_link( '<div class="cresta-nav-previous">%link</div>' );
				if ($show_excerpt == '1') {
					$excerpt_words = $cpb_options['cresta_posts_box_excerpt_words'];
					$excerpt = $previous->post_excerpt ? $previous->post_excerpt :apply_filters('get_the_excerpt', $previous->post_content);
					echo '<div class="crestaPostsBoxExcerpt">' . wp_trim_words( $excerpt, intval($excerpt_words), '&hellip;') . '</div>';
				}
				echo '</div>';
				if ($show_credit == '1') {
					echo '
						<div class="crestaPostsBoxCredit"><a target="_blank" href="https://crestaproject.com/downloads/cresta-posts-box/" title="' .esc_attr__('CrestaProject', 'cresta-posts-box'). '">' .esc_html__('Cresta Posts Box by CP', 'cresta-posts-box'). '</a></div>
					';
				}
				echo '</div>';
			}
		/* Display Next Post */
		} elseif ($what_show == 'next') {
			$next = get_adjacent_post( false, '', false );
			if ( ! $next ) {
				return;
			} else {
				$box_title = $cpb_options['cresta_posts_box_title'];
				$close_button = $cpb_options['cresta_posts_box_close_button'];
				$show_image = $cpb_options['cresta_posts_box_show_image'];
				$show_excerpt = $cpb_options['cresta_posts_box_show_excerpt'];
				$show_mobile = $cpb_options['cresta_posts_box_mobile'];
				$show_credit = $cpb_options['cresta_posts_box_credit'];
				if ($show_mobile == '1') {
					$the_mobile = 'isMobile';
				} else {
					$the_mobile = 'noMobile';
				}
				echo '<div class="crestaPostsBox active ' . esc_attr( $the_mobile ) . '">';
				if ($close_button == '1') {
					echo '<span class="crestaCloseBox"></span>';
				}
				echo '<span class="crestaBoxName">' . esc_html( $box_title ) . '</span>';
				echo '<div class="crestaPostsBoxContent">';
				if (current_theme_supports('post-thumbnails') && has_post_thumbnail( $next->ID ) && $show_image == '1' ) {
					$nextthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $next->ID ), 'medium' );
					echo '<div class="crestaBoxImage"><img src="' . esc_url( $nextthumb[0] ) . '" alt="' . esc_attr( $box_title ) . '"/></div>';
				}
				next_post_link( '<div class="cresta-nav-next">%link</div>' );
				if ($show_excerpt == '1') {
					$excerpt_words = $cpb_options['cresta_posts_box_excerpt_words'];
					$excerpt = $next->post_excerpt ? $next->post_excerpt :apply_filters('get_the_excerpt', $next->post_content);
					echo '<div class="crestaPostsBoxExcerpt">' . wp_trim_words( $excerpt, intval($excerpt_words), '&hellip;') . '</div>';
				}
				echo '</div>';
				if ($show_credit == '1') {
					echo '
						<div class="crestaPostsBoxCredit"><a target="_blank" href="https://crestaproject.com/downloads/cresta-posts-box/" title="' . esc_attr__('CrestaProject', 'cresta-posts-box'). '">' . esc_html__('Cresta Posts Box by CP', 'cresta-posts-box'). '</a></div>
					';
				}
				echo '</div>';
			}
		}
	}
}
add_action('wp_footer', 'add_cresta_posts_box');

/* Add Custom CSS Code */
function header_custom_code_cresta_posts_box() {
	$cresta_posts_current_post_type = get_post_type();
	$cpb_options = get_option( 'crestapostsbox_settings' );
	$box_show_on = explode (',',$cpb_options['cresta_posts_box_selected_page'] );
	if ( is_singular() && in_array( $cresta_posts_current_post_type, $box_show_on ) ) {
		$custom_css = $cpb_options['cresta_posts_box_custom_css'];
		if ( $custom_css ) {
			echo '<!--Start CPB Custom CSS--><style type="text/css">' . esc_html($custom_css) . '</style><!--End CPB Custom CSS-->';
		}
	}
}
add_action('wp_head', 'header_custom_code_cresta_posts_box');
	
/* Add Cresta Box filter class ad the bottom of content */
function bottom_class_filter_cresta_posts_box($content) {
	$cresta_posts_current_post_type = get_post_type();
	$cpb_options = get_option( 'crestapostsbox_settings' );
	$box_show_on = explode (',',$cpb_options['cresta_posts_box_selected_page'] );
	if ( is_singular() && in_array( $cresta_posts_current_post_type, $box_show_on ) ) {
		$add_the_class = '<div class="cresta-box-class"></div>';
		$content .= $add_the_class;
	}
	return $content;
}
add_filter('the_content', 'bottom_class_filter_cresta_posts_box' ); 

function cresta_posts_box_option() {
	ob_start();
	?>
	<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2><?php esc_html_e('Cresta Posts Box FREE', 'cresta-posts-box'); ?></h2><a class="crestaButtonUpgrade" href="https://crestaproject.com/downloads/cresta-posts-box/" target="_blank" title="See Details: Cresta Posts Box PRO"><?php esc_html_e('Upgrade to PRO version!', 'cresta-posts-box'); ?></a>
	<?php
	if( isset($_GET['settings-updated']) && $_GET['settings-updated'] ) {
		echo '<div id="message" class="updated"><p>'.esc_html__('Settings Saved...', 'cresta-posts-box').'</p></div>';
	}
	?>
	<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
	<!-- main content -->
    <div id="post-body-content">
	<div class="meta-box-sortables ui-sortable">
	<div class="postbox">
    <div class="inside">
	<form method="post" action="options.php">
		
		<?php
		settings_fields( 'cpbplugin' ); 
		$cpb_options = get_option( 'crestapostsbox_settings' );
		?>
		<h3><div class="dashicons dashicons-art space"></div><?php esc_html_e( 'Box Style', 'cresta-posts-box' ); ?></h3>
		<table class="form-table">
					<tbody>				
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Font Size', 'cresta-posts-box' ); ?></th>
							<td class="range-slider">
								<input type='range' class="range-box-font-size input-range" name='crestapostsbox_settings[cresta_posts_box_font_size]' value='<?php echo intval($cpb_options['cresta_posts_box_font_size']); ?>' min="5" max="30">
								<span class="range-show-font-size range-value"></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Line Height', 'cresta-posts-box' ); ?></th>
							<td class="range-slider">						
								<input type='range' class="range-box-line-height input-range" name='crestapostsbox_settings[cresta_posts_box_line_height]' value='<?php echo intval($cpb_options['cresta_posts_box_line_height']); ?>' min="0" max="50">
								<span class="range-show-line-height range-value"></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Width', 'cresta-posts-box' ); ?></th>
							<td class="range-slider">						
								<input type='range' class="range-box-box-width input-range" name='crestapostsbox_settings[cresta_posts_box_width]' value='<?php echo intval($cpb_options['cresta_posts_box_width']); ?>' min="0" max="1000" step="10">
								<span class="range-show-box-width range-value"></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Show Box Shadow', 'cresta-posts-box' ); ?></th>
							<td>						
								<input type="checkbox" name="crestapostsbox_settings[cresta_posts_box_box_shadow]" value="1" <?php checked( $cpb_options['cresta_posts_box_box_shadow'], '1' ); ?>>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Z-Index', 'cresta-posts-box' ); ?></th>
							<td>						
								<input type='number' name='crestapostsbox_settings[cresta_posts_box_z_index]' value='<?php echo intval($cpb_options['cresta_posts_box_z_index']); ?>' min="0" max="999999">
								<span class="description"><?php esc_html_e('Increase this number if the box is covered by other items on the screen.', 'cresta-posts-box'); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Background Color', 'cresta-posts-box' ); ?></th>
							<td>						
								<label class="crestaDisabled"><span><?php esc_html_e( 'PRO Version', 'cresta-posts-box' ); ?></span></label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Text Box Color', 'cresta-posts-box' ); ?></th>
							<td>						
								<label class="crestaDisabled"><span><?php esc_html_e( 'PRO Version', 'cresta-posts-box' ); ?></span></label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Link Box Color', 'cresta-posts-box' ); ?></th>
							<td>						
								<label class="crestaDisabled"><span><?php esc_html_e( 'PRO Version', 'cresta-posts-box' ); ?></span></label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Border Color', 'cresta-posts-box' ); ?></th>
							<td>		
								<label class="crestaDisabled"><span><?php esc_html_e( 'PRO Version', 'cresta-posts-box' ); ?></span></label>
							</td>
						</tr>
					</tbody>	
		</table>
		<h3><div class="dashicons dashicons-admin-settings space"></div><?php esc_html_e( 'Box Position', 'cresta-posts-box' ); ?></h3>
		<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Position', 'cresta-posts-box' ); ?></th>
							<td>	
								<ul>
									<li>
										<label><input type="radio" name='crestapostsbox_settings[cresta_posts_box_float]' value='left' <?php checked( 'left', $cpb_options['cresta_posts_box_float'] ); ?>><?php _e('Left', 'cresta-posts-box'); ?></label>
									</li>
									<li>
										<label><input type="radio" name='crestapostsbox_settings[cresta_posts_box_float]' value='right' <?php checked( 'right', $cpb_options['cresta_posts_box_float'] ); ?>><?php _e('Right', 'cresta-posts-box'); ?></label>
									</li>
								</ul>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Distance from left or right', 'cresta-posts-box' ); ?></th>
							<td class="range-slider">						
								<input type='range' class="range-box-distance-leftright input-range" name='crestapostsbox_settings[cresta_posts_box_position_left_right]' value='<?php echo intval($cpb_options['cresta_posts_box_position_left_right']); ?>' min="0" max="100">
								<span class="range-show-distance-leftright range-value"></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Distance from bottom', 'cresta-posts-box' ); ?></th>
							<td class="range-slider">						
								<input type='range' class="range-box-distance-bottom input-range" name='crestapostsbox_settings[cresta_posts_box_position_bottom]' value='<?php echo intval($cpb_options['cresta_posts_box_position_bottom']); ?>' min="0" max="100">
								<span class="range-show-distance-bottom range-value"></span>
							</td>
						</tr>
					</tbody>	
		</table>
		<h3><div class="dashicons dashicons-welcome-view-site space"></div><?php esc_html_e( 'Box Display', 'cresta-posts-box' ); ?></h3>
		<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Title', 'cresta-posts-box' ); ?></th>
							<td>						
								<input type="text" name='crestapostsbox_settings[cresta_posts_box_title]' value="<?php echo esc_attr($cpb_options['cresta_posts_box_title']);?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Border Box Width', 'cresta-posts-box' ); ?></th>
							<td class="range-slider">						
								<label class="crestaDisabled"><span><?php esc_html_e( 'PRO Version', 'cresta-posts-box' ); ?></span></label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Border Box Radius', 'cresta-posts-box' ); ?></th>
							<td class="range-slider">						
								<label class="crestaDisabled"><span><?php esc_html_e( 'PRO Version', 'cresta-posts-box' ); ?></span></label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Show', 'cresta-posts-box' ); ?></th>
							<td>	
								<ul>
									<li>
										<label><input type="radio" name='crestapostsbox_settings[cresta_posts_box_what_show]' value='previous' <?php checked( 'previous', $cpb_options['cresta_posts_box_what_show'] ); ?>><?php esc_html_e('Previous Post', 'cresta-posts-box'); ?></label>
									</li>
									<li>
										<label><input type="radio" name='crestapostsbox_settings[cresta_posts_box_what_show]' value='next' <?php checked( 'next', $cpb_options['cresta_posts_box_what_show'] ); ?>><?php esc_html_e('Next Post', 'cresta-posts-box'); ?></label>
									</li>
									<li class="crestaDisabled">
										<label><input type="radio" name='crestaForPRO' disabled><?php esc_html_e('Both Previous and Next Post', 'cresta-posts-box'); ?> <span><?php esc_html_e('PRO Version', 'cresta-posts-box'); ?></span></label>
									</li>
									<li class="crestaDisabled">
										<label><input type="radio" name='crestaForPRO' disabled><?php esc_html_e('Random Post', 'cresta-posts-box'); ?> <span><?php esc_html_e('PRO Version', 'cresta-posts-box'); ?></span></label>
									</li>
									<li class="crestaDisabled">
										<label><input type="radio" name='crestaForPRO' disabled><?php esc_html_e('Related Post', 'cresta-posts-box'); ?> <i><?php esc_html_e('(Based on tags, works only with Post and Custom Post Type if they have enabled the use of tags)', 'cresta-posts-box'); ?></i> <span><?php esc_html_e('PRO Version', 'cresta-posts-box'); ?></span></label>
									</li>
								</ul>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Show Close Button', 'cresta-posts-box' ); ?></th>
							<td>						
								<input type="checkbox" name="crestapostsbox_settings[cresta_posts_box_close_button]" value="1" <?php checked( $cpb_options['cresta_posts_box_close_button'], '1' ); ?>>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Show featured image (if available)', 'cresta-posts-box' ); ?></th>
							<td>						
								<input type="checkbox" name="crestapostsbox_settings[cresta_posts_box_show_image]" value="1" <?php checked( $cpb_options['cresta_posts_box_show_image'], '1' ); ?>>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Featured image width', 'cresta-posts-box' ); ?></th>
							<td class="range-slider">						
								<input type='range' class="range-box-image-width input-range" name='crestapostsbox_settings[cresta_posts_box_image_width]' value='<?php echo intval($cpb_options['cresta_posts_box_image_width']); ?>' min="40" max="600">
								<span class="range-show-image-width range-value"></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Featured image box height', 'cresta-posts-box' ); ?></th>
							<td class="range-slider">						
								<input type='range' class="range-box-image-height input-range" name='crestapostsbox_settings[cresta_posts_box_image_height]' value='<?php echo intval($cpb_options['cresta_posts_box_image_height']); ?>' min="40" max="600">
								<span class="range-show-image-height range-value"></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Show Excerpt', 'cresta-posts-box' ); ?></th>
							<td>						
								<input type="checkbox" name="crestapostsbox_settings[cresta_posts_box_show_excerpt]" value="1" <?php checked( $cpb_options['cresta_posts_box_show_excerpt'], '1' ); ?>>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Number of words to show', 'cresta-posts-box' ); ?></th>
							<td class="range-slider">					
								<input type='range' class="range-box-excerpt-words input-range" name='crestapostsbox_settings[cresta_posts_box_excerpt_words]' value='<?php echo intval($cpb_options['cresta_posts_box_excerpt_words']); ?>' min="1" max="50">
								<span class="range-show-excerpt-words range-value"></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Show on mobile devices and tablets', 'cresta-posts-box' ); ?></th>
							<td>						
								<input type="checkbox" name="crestapostsbox_settings[cresta_posts_box_mobile]" value="1" <?php checked( $cpb_options['cresta_posts_box_mobile'], '1' ); ?>>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Show CrestaProject credits :)', 'cresta-posts-box' ); ?></th>
							<td>						
								<input type="checkbox" name="crestapostsbox_settings[cresta_posts_box_credit]" value="1" <?php checked( $cpb_options['cresta_posts_box_credit'], '1' ); ?>>
							</td>
						</tr>
					</tbody>	
		</table>
		<h3><div class="dashicons dashicons-search space"></div><?php esc_html_e( 'Show on', 'cresta-posts-box' ); ?></h3>
		<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php _e( 'Show on', 'cresta-posts-box' ); ?></th>
							<td>
							<?php
								$box_show_on = explode (',',$cpb_options['cresta_posts_box_selected_page'] );
								$args = array(
									'public'   => true,
								);
								$post_types = get_post_types( $args, 'names', 'and' ); 
								echo '<ul>';
								foreach ( $post_types  as $post_type ) { 
									$post_type_name = get_post_type_object( $post_type );
									?>
									<li>
										<input type="checkbox" <?php if(in_array( $post_type ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestapostsbox_settings[cresta_posts_box_selected_page][]" value="<?php echo esc_attr($post_type); ?>"/><?php echo esc_attr($post_type_name->labels->singular_name); ?>
									</li>
								<?php
								}
								echo '</ul>';
							?>
							</td>
						</tr>		
					</tbody>	
		</table>
		<h3><div class="dashicons dashicons-admin-generic space"></div><?php esc_html_e( 'Advanced', 'cresta-posts-box' ); ?></h3>
		<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Custom CSS Code', 'cresta-posts-box' ); ?></th>
							<td>
								<textarea name="crestapostsbox_settings[cresta_posts_box_custom_css]" class="large-text code" rows="10"><?php echo esc_textarea($cpb_options['cresta_posts_box_custom_css']); ?></textarea>
								<span class="description"><?php esc_html_e( 'Write here your custom CSS code if you want to customize the style of the box', 'cresta-posts-box' ); ?></span>
							</td>
						</tr>		
					</tbody>	
		</table>
		
		<?php submit_button(); ?>
		
	</form>
	</div> <!-- .inside -->
	</div> <!-- .postbox -->
	</div> <!-- .meta-box-sortables .ui-sortable -->
	</div> <!-- post-body-content -->
	<!-- sidebar -->
                <div id="postbox-container-1" class="postbox-container">
                    <div class="meta-box-sortables">
                        <div class="postbox">
                            <h3><span><div class="dashicons dashicons-star-filled"></div> <?php esc_html_e( 'Rate it!', 'cresta-posts-box' ); ?></span></h3>
                            <div class="inside">
								Don't forget to rate <strong>Cresta Posts Box</strong> on WordPress Pugins Directory.<br/>
								We really appreciate it ;)
                                <br/>
								<img src="<?php echo esc_url( plugins_url( '/images/5-stars.png' , __FILE__ )); ?>">
								<br/>
								<a class="crestaButton" href="https://wordpress.org/plugins/cresta-posts-box/"title="<?php esc_html_e( 'Rate Cresta Posts Box on Wordpress Plugins Directory', 'cresta-posts-box' ); ?>" class="btn btn-primary" target="_blank"><?php esc_html_e( 'Rate Cresta Posts Box', 'cresta-posts-box' ); ?></a>
                            </div> <!-- .inside -->
                        </div> <!-- .postbox -->

                        <div class="postbox" style="border: 2px solid #d54e21;">
                            
                            <h3><span><div class="dashicons dashicons-megaphone"></div> <?php esc_html_e( 'Need More? Get the PRO version', 'cresta-posts-box' ); ?></span></h3>
                            <div class="inside">
                                <a href="https://crestaproject.com/downloads/cresta-posts-box/" target="_blank" alt="Get Cresta Posts Box PRO"><img src="<?php echo esc_url(plugins_url( '/images/banner-cresta-posts-box-pro.png' , __FILE__ )); ?>"></a><br/>
								Get <strong>Cresta Posts Box PRO</strong> for only <strong>12,99€</strong>.<br/>
								<ul>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Show both previous and next post</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Show Random Post</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Show Related Post</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Set box background color</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Set box text and link color</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> Set border box and border radius box</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> 5 Animations</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> 20% discount code for all CrestaProject Themes</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> 1 year updates and support</li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> and Much More...</li>
								</ul>
								<a class="crestaButton" href="https://crestaproject.com/downloads/cresta-posts-box/" target="_blank" title="<?php esc_html_e( 'More Details', 'cresta-posts-box' ); ?>"><?php esc_html_e( 'More Details', 'cresta-posts-box' ); ?></a>
                            </div> <!-- .inside -->
                         </div> <!-- .postbox -->
						<div class="postbox" style="border: 2px solid #0074a2;">
                            
                            <h3><span><div class="dashicons dashicons-admin-plugins"></div> Cresta Social Share Counter Plugin</span></h3>
                            <div class="inside">
                                <a href="https://crestaproject.com/downloads/cresta-social-share-counter/" target="_blank" alt="Get Cresta Social Share Counter"><img src="<?php echo plugins_url( '/images/banner-cresta-social-share-counter.png' , __FILE__ ); ?>"></a><br/>
								Share your posts and pages quickly and easily with <strong>Cresta Social Share Counter</strong> showing the share count.
								<a class="crestaButton" href="https://crestaproject.com/downloads/cresta-social-share-counter/" target="_blank" title="Cresta Social Share Counter">Available in FREE and PRO version</a>
                            </div> <!-- .inside -->
                         </div> <!-- .postbox -->
                    </div> <!-- .meta-box-sortables -->
                </div> <!-- #postbox-container-1 .postbox-container -->
    </div> <!-- #post-body .metabox-holder .columns-2 -->
    <br class="clear">
    </div> <!-- #poststuff -->
	</div>
	<?php
	echo ob_get_clean();
}

/* Validate options */
function crestapostsbox_options_validate($input) {
	if($input['cresta_posts_box_selected_page'] != '' && is_array($input['cresta_posts_box_selected_page'])) {
		$box_show_on = implode(',',$input['cresta_posts_box_selected_page']);
		$input['cresta_posts_box_selected_page'] = wp_filter_nohtml_kses($box_show_on); 
	} else {
		$input['cresta_posts_box_selected_page'] = 'post'; 
	}
	$input['cresta_posts_box_font_size'] = sanitize_text_field(absint($input['cresta_posts_box_font_size']));
	$input['cresta_posts_box_line_height'] = sanitize_text_field(absint($input['cresta_posts_box_line_height']));
	$input['cresta_posts_box_width'] = sanitize_text_field(absint($input['cresta_posts_box_width']));
	$input['cresta_posts_box_box_shadow'] = wp_filter_nohtml_kses($input['cresta_posts_box_box_shadow']);
	$input['cresta_posts_box_z_index'] = sanitize_text_field(absint($input['cresta_posts_box_z_index']));
	$input['cresta_posts_box_float'] = wp_filter_nohtml_kses($input['cresta_posts_box_float']);
	$input['cresta_posts_box_position_left_right'] = sanitize_text_field(absint($input['cresta_posts_box_position_left_right']));
	$input['cresta_posts_box_position_bottom'] = sanitize_text_field(absint($input['cresta_posts_box_position_bottom']));
	$input['cresta_posts_box_title'] = sanitize_text_field($input['cresta_posts_box_title']);
	$input['cresta_posts_box_what_show'] = wp_filter_nohtml_kses($input['cresta_posts_box_what_show']);
	$input['cresta_posts_box_close_button'] = wp_filter_nohtml_kses($input['cresta_posts_box_close_button']);
	$input['cresta_posts_box_show_image'] = wp_filter_nohtml_kses($input['cresta_posts_box_show_image']);
	$input['cresta_posts_box_image_width'] = sanitize_text_field(absint($input['cresta_posts_box_image_width']));
	$input['cresta_posts_box_image_height'] = sanitize_text_field(absint($input['cresta_posts_box_image_height']));
	$input['cresta_posts_box_show_excerpt'] = wp_filter_nohtml_kses($input['cresta_posts_box_show_excerpt']);
	$input['cresta_posts_box_excerpt_words'] = sanitize_text_field(absint($input['cresta_posts_box_excerpt_words']));
	$input['cresta_posts_box_mobile'] = wp_filter_nohtml_kses($input['cresta_posts_box_mobile']);
	$input['cresta_posts_box_credit'] = wp_filter_nohtml_kses($input['cresta_posts_box_credit']);
	$input['cresta_posts_box_custom_css'] = wp_filter_nohtml_kses($input['cresta_posts_box_custom_css']);
	return $input;
}
?>