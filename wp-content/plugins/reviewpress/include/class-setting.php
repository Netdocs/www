<?php

/**
 * ReviePress Setting section.
 *
 * @package ReviewPress
 */
if ( ! class_exists( 'ReviewPress_Setting' ) ) :
	class ReviewPress_Setting {

		/**
		 * Private member.
		 */
		private $settings_api;

		/**
		 * Constructor of class.
		 */
		function __construct() {
			$this->settings_api = new ReviewPress_Settings_API ;

			add_action( 'admin_init', array( $this, 'admin_init' ) );
		}

		/**
		 * Call in costructor.
		 */
		function admin_init() {

			// Set the settings.
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );

			// Initialize settings.
			$this->settings_api->admin_init();
		}

		/**
		 * Generate setting section
		 */
		function get_settings_sections() {
			$sections = array(
			array(
			   'id'    => 'wpbr_display',
			   'title' => __( 'Display Settings', 'reviewpress' ),
			),
			array(
			   'id'    => 'wpbr_reviews',
			   'title' => __( 'Reviews Settings', 'reviewpress' ),
			),
			array(
			   'id'    => 'wpbr_form',
			   'title' => __( 'Review Form Settings', 'reviewpress' ),
			),
			array(
			   'id'    => 'wpbr_custom_css',
			   'title' => __( 'Custom CSS', 'reviewpress' ),
			   ),
			);
			return $sections;
		}

		 /**
		  * Returns all the settings fields
		  *
		  * @return array settings fields
		  *
		  * @since 1.0.0
		  */
		function get_settings_fields() {

			// $post_type = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ) );
			$settings_fields = array(
			  'wpbr_display' => array(
				 array(
					'name'    => 'review_authorization',
					'label'   => __( 'Review Authorization', 'reviewpress' ),
					'desc'    => __( 'Select user who can give review', 'reviewpress' ),
					'type'    => 'radio',
					'options' => array(
					   'all_users'   => __( 'All Users','reviewpress' ),
					   'login_users' => __( 'Just Login Users','reviewpress' ),
					),
					'default' => 'all_users',
				 ),
				 array(
					'name'        => 'review_users',
					'label'       => __( 'Select Users', 'reviewpress' ),
					'type'        => 'text',
					'field-class' => 'hidden',
					'desc'        => __( 'Select user who can give review', 'reviewpress' ),
				 ),
				 array(
					'name'    => 'review_icon',
					'label'   => __( 'Rating Icon', 'reviewpress' ),
					'desc'    => __( 'Select icon for rating', 'reviewpress' ),
					'type'    => 'radio',
					'options' => array(
					   'star'  => __( 'Star','reviewpress' ),
					   'heart' => __( 'Heart','reviewpress' ),
					),
					'default' => 'star',
				 ),
				 array(
					'name'    => 'rating_icon_color',
					'label'   => __( 'Rating Icon Color', 'reviewpress' ),
					'desc'    => __( 'Select Color for Rating Icons', 'reviewpress' ),
					'type'    => 'color',
					'default' => '#ea9c00',
				 ),
			  ),
			  'wpbr_reviews' => array(
				 array(
					'name'    => 'auto_approve_rievew',
					'label'   => __( 'Auto Approve Reviews', 'reviewpress' ),
					'desc'    => __( 'Select Yes to Automatically Approve Reviews', 'reviewpress' ),
					'type'    => 'radio',
					'default' => 'pending',
					'options' => array(
					   'publish' => __( 'Yes','reviewpress' ),
					   'pending'  => __( 'No','reviewpress' ),
					),

				 ),
				 array(
					'name'    => 'sort_review',
					'label'   => __( 'Sort Reviews By', 'reviewpress' ),
					'type'    => 'select',
					'options' => array(
					   'rating' => __( 'Rating','reviewpress' ),
					   'date'   => __( 'Date','reviewpress' ),
					),
				 ),
				 array(
					'name'    => 'number_of_reviews',
					'label'   => __( 'Number of Reviews', 'reviewpress' ),
					'desc'    => __( 'Select Number of Reviews you want to show', 'reviewpress' ),
					'type'    => 'number',
					'default' => '10',
					'sanitize_callback' => 'sanitize_text_field ',
				 ),
			  ),
			  'wpbr_form' => array(
				 array(
					'name'    => 'google_captcha',
					'label'   => __( 'Add Google Captcha', 'reviewpress' ),
					'desc'    => __( 'To prevent Spamming', 'reviewpress' ),
					'type'    => 'checkbox',
				 ),
				 array(
					'name'        => 'google_captcha_site_key',
					'label'       => __( 'Site Key', 'reviewpress' ),
					'desc'        => __( 'Enter Google Captcha Site Key', 'reviewpress' ),
					'type'        => 'text',
					'default'     => '',
					'field-class' => 'hidden',
					'sanitize_callback' => 'sanitize_text_field ',
				 ),
				 array(
					'name'        => 'google_captcha_secret_key',
					'label'       => __( 'Secret key', 'reviewpress' ),
					'desc'        => __( 'Enter Google Captcha Secret Key', 'reviewpress' ),
					'type'        => 'text',
					'default'     => '',
					'field-class' => 'hidden',
					'sanitize_callback' => 'sanitize_text_field ',
				 ),
				 array(
					'name'    => 'google_snippet',
					'label'   => __( 'Add Google Rich Snippets', 'reviewpress' ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
					   'yes' => __( 'Yes','reviewpress' ),
					   'no'  => __( 'No','reviewpress' ),
					),
				 ),
				 array(
					'name'              => 'name_field_text',
					'label'             => __( 'Name Field', 'reviewpress' ),
					'type'              => 'text',
					'default'           => 'Name',
					'sanitize_callback' => 'sanitize_text_field ',
				 ),
				 array(
					'name'        => 'name_field_required',
					'label'       => __( 'Require', 'reviewpress' ),
					'type'        => 'checkbox',
				 ),
				 array(
					'name'        => 'name_field_display',
					'label'       => __( 'Display', 'reviewpress' ),
					'type'        => 'checkbox',
					'default'     => 'on',
				 ),
				 array(
					'name'        => 'email_field_text',
					'label'       => __( 'Email Field', 'reviewpress' ),
					'type'        => 'text',
					'default'     => 'Email',
					'sanitize_callback' => 'sanitize_text_field ',
				 ),
				 array(
					'name'        => 'email_field_required',
					'label'       => __( 'Require', 'reviewpress' ),
					'type'        => 'checkbox',
				 ),
				 array(
					'name'        => 'email_field_display',
					'label'       => __( 'Display', 'reviewpress' ),
					'type'        => 'checkbox',
					'default'     => 'on',
				 ),
				 array(
					'name'        => 'title_field_text',
					'label'       => __( 'Title Field', 'reviewpress' ),
					'type'        => 'text',
					'default'     => 'Title',
					'sanitize_callback' => 'sanitize_text_field ',
				 ),
				 array(
					'name'        => 'title_field_required',
					'label'       => __( 'Require', 'reviewpress' ),
					'type'        => 'checkbox',
				 ),
				 array(
					'name'        => 'title_field_display',
					'label'       => __( 'Display', 'reviewpress' ),
					'type'        => 'checkbox',
					'default'     => 'on',
				 ),
				 array(
					'name'        => 'rating_field_text',
					'label'       => __( 'Rating Field', 'reviewpress' ),
					'type'        => 'text',
					'default'     => 'Rating',
					'sanitize_callback' => 'sanitize_text_field ',
				 ),
				 array(
					'name'        => 'review_content_field_text',
					'label'       => __( 'Review Comment', 'reviewpress' ),
					'type'        => 'text',
					'default'     => 'Review Comment',
					'sanitize_callback' => 'sanitize_text_field ',
				 ),
				 array(
					'name'        => 'review_content_field_required',
					'label'       => __( 'Require', 'reviewpress' ),
					'type'        => 'checkbox',
				 ),
				 array(
					'name'         => 'review_content_field_display',
					'label'        => __( 'Display', 'reviewpress' ),
					'type'         => 'checkbox',
					'default'      => 'on',
					),
				 ),
				 'wpbr_custom_css' => array(
					array(
					   'name'              => 'custom_css',
					   'label'             => '',
					   'type'              => 'textarea',
					   'sanitize_callback' => 'wp_strip_all_tags',
					),
				 ),

				 );

				 return $settings_fields;
		}

		/**
		 * Generate spread the word section.
		 *
		 * @since 1.0.0
		 */
		function plugin_page() {

			echo '<div id="" class="wrap"><h2 class="opt-title"><span id="icon-options-general" class="analytics-options"><img src="" alt=""></span>
		 ReviewPress Settings</h2></div>';

			echo "<div class='wpbr-wrap'><div class='wpbr-tabsWrapper'>";
			echo '<div class="wpbr-button-container top">
						<div class="setting-notification">'.
							__( 'Settings have changed, you should save them!' , 'reviewpress' )
						.'</div>
                  <input type="submit" class="wpbrmedia-settings-submit button button-primary button-big" value="'.esc_html__( 'Save Settings','reviewpress' ).'" id="wpbr_save_setting_top">
                  </div>';
			echo '<div id="review-setting" class="">';

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();

			echo '</div>';
			echo '<div class="wpbr-button-container bottom">
                  <div class="wpbr-social-links alignleft">
                  <a href="https://twitter.com/wpbrigade" class="twitter" target="_blank"><span class="dashicons dashicons-twitter"></span></a>
                  <a href="https://www.facebook.com/WPBrigade" class="facebook" target="_blank"><span class="dashicons dashicons-facebook"></span></a>
                  <a href="https://profiles.wordpress.org/WPBrigade/" class="wordpress" target="_blank"><span class="dashicons dashicons-wordpress"></span></a>
                  <a href="http://wpbrigade.com/feed/" class="rss" target="_blank"><span class="dashicons dashicons-rss"></span></a>
                  </div>
                  <input type="submit" class="wpbrmedia-settings-submit button button-primary button-big" value="'.esc_html__( 'Save Settings','reviewpress' ).'" id="wpbr_save_setting_bottom">
                  </div>';
			echo '</div>';

			?>
           <div class="metabox-holder wpbr-sidebar">
              <div class="sidebar postbox">
				 <h2><?php esc_html_e( 'Spread the Word' , 'reviewpress' )?></h2>
            <ul>
					<li>
						<a href="http://twitter.com/share?text=This is Best WordPress Review  Plugin&url=http://wordpress.org&hashtags=ReviewPress,WordPress" data-count="none"  class="button twitter" target="_blank" title="Post to Twitter Now"><?php esc_html_e( 'Share on Twitter' , 'reviewpress' )?><span class="dashicons dashicons-twitter"></span></a>
					</li>

					<li>
						<a href="https://www.facebook.com/sharer/sharer.php?u=https://wordpress.org" class="button facebook" target="_blank" title="Post to Facebook Now"><?php esc_html_e( 'Share on Facebook' , 'reviewpress' )?><span class="dashicons dashicons-facebook"></span>
						</a>
					</li>

					<li>
						<a href="#" class="button wordpress" target="_blank" title="Rate on Wordpress.org"><?php esc_html_e( 'Rate on Wordpress.org' , 'reviewpress' )?><span class="dashicons dashicons-wordpress"></span>
						</a>
					</li>
					<li>
						<a href="http://wpbrigade.com/feed/" class="button rss" target="_blank" title="Subscribe to our Feeds"><?php esc_html_e( 'Subscribe to our Feeds' , 'reviewpress' )?><span class="dashicons dashicons-rss"></span>
						</a>
					</li>
				</ul>
              </div>
			  </div>

				<?php
		}

			   /**
				* Get all the pages
				*
				* @return array page names with key value pairs
				*
				* @since 1.0.0
				*/
		function get_pages() {
			$pages = get_pages();
			$pages_options = array();
			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}

			return $pages_options;
		}
	}
		 endif;
