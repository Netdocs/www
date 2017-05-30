<?php
/*
Plugin Name: Broken Link Checker for YouTube
Plugin URI: https://wordpress.org/plugins/broken-link-checker-for-youtube/
Description: Can automatically validate YouTube embeds in your posts.
Version: 1.1
Released: November 12th, 2015
Author: Super Blog Me
Author URI: http://www.superblogme.com/
License: GPL2
*/

defined( 'ABSPATH' ) or die( "Oops! This is a WordPress plugin and should not be called directly.\n" );

////////////////////////////////////////////////////////////////////////////////////////////

if(!class_exists('YouTube_Link_Checker'))
{

require_once plugin_dir_path( __FILE__ ) . 'youtube-link-functions.php';

    class YouTube_Link_Checker extends YouTube_Link_Functions
    {
	protected $version = "1.1";

        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            	// register actions
		add_action('admin_init', array(&$this, 'admin_init'));
		add_action('admin_menu', array(&$this, 'add_menu'));
        } // END public function __construct

	/**
	 * hook into WP's admin_init action hook
	 */
	public function admin_init()
	{
    		// Set up the settings for this plugin
    		$this->init_settings();
	} // END public static function activate


	/**
	 * Initialize some custom settings
	 */     
	public function init_settings()
	{
		// set defaults if needed
		add_option( 'ytlc_logfile', plugin_dir_path( __FILE__ ) . 'logfile.txt' );

	} // END public function init_custom_settings()

	/**
	* add a menu
	*/     
	public function add_menu()
	{
		$page = add_management_page('Broken Link Checker for YouTube Settings', 'YouTube Checker', 'manage_options', 'youtube_link_checker', array(&$this, 'plugin_settings_page'));
	} // END public function add_menu()

//----- PLUGIN settings page ---------------------------------------------------------------------

	/**
	* Menu Callback
	*/     
	public function plugin_settings_page()
	{
		if(!current_user_can('manage_options'))
		{
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
    		// Render the settings template
		include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
		if ( isset( $_POST['scan_now'] ) ) {
			$this->check_for_broken_links();
			echo "<div id='message' class='updated'><p>" . __('Scan Complete.','youtube-link-checker') . "</p></div>";
		}

	} // END public function plugin_settings_page()

    } // END class YouTube_Link_Checker
} // END if(!class_exists('YouTube_Link_Checker'))

////////////////////////////////////////////////////////////////////////////////////////////

if(class_exists('YouTube_Link_Checker'))
{
	// instantiate the plugin class
	$youtube_link_checker = new YouTube_Link_Checker();
}

////////////////////////////////////////////////////////////////////////////////////////////
