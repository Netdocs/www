<?php
/* 
=== Category to Pages WUD ===
Contributors: wistudat.be
Plugin Name: Category to Pages WUD
Donate Reason: Stand together to help those in need!
Donate link: https://www.icrc.org/eng/donations/
Description: Unique Page Categories and Page Tags.
Author: Danny WUD
Author URI: https://wud-plugins.com
Plugin URI: https://wud-plugins.com
Tags: category pages, categories page, categories pages, category to page, page category, page categories, pages category, pages categories, tags page, tag pages, category, categories, tag, tags, page, pages, tag to page, add category, add tag
Requires at least: 3.6
Tested up to: 4.7
Stable tag: 2.1.5
Version: 2.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: category-to-pages-wud
Domain Path: /languages
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
//==============================================================================//
$ctp_version='2.1.5';
// Store the latest version.
if (get_option('pcwud_wud_version')!=$ctp_version) {pcwud_wud_update();}
//==============================================================================//
global $template;
// Actions
		add_action('admin_menu', 'cattopage_wud_create_menu');
		add_filter( 'plugin_action_links', 'cattopage_wud_action_links', 10, 5 );
		add_action('admin_enqueue_scripts', 'cattopage_wud_styling');
		add_action('init', 'cattopage_wud_site_page');
		add_action('plugins_loaded', 'cattopage_wud_languages');
		add_action('wp_head', 'show_wud_ctp_template');
		add_filter("the_content", "ctp_change_to_excerpt");
		add_action( 'init', 'my_add_excerpts_to_pages' );
		add_shortcode('wudcatlist', 'ctp_short_code_cat_list');
		add_shortcode('wudtaglist', 'ctp_short_code_tag_list');
		add_shortcode('wudcatdrop', 'ctp_short_code_cat_drop');
		add_shortcode('wudtagdrop', 'ctp_short_code_tag_drop');
		
//Use the same categories and tags for pages, as it is for post.		
	if(get_option('cattopage_wud_unique')=="0"){
		add_action('init', 'cattopage_wud_reg_cat_tag');
		add_action('plugins_loaded','cattopage_wud_reg_cat_tag');
		if ( ! is_admin()) {
			add_action( 'pre_get_posts', 'cattopage_wud_cat_tag_archives' );
		}		
	}	
	
//Show Category and ord tag link on pages
if ( ! is_admin()) {
	//below the tiltle
	if (get_option('cattopage_wud_index_pos')==0){
		add_filter( 'the_title', 'cattopage_wud_titles', 10, 2);
	}
	//above the content
	elseif (get_option('cattopage_wud_index_pos')==1){
		add_filter ('the_content', 'cattopage_wud_titles_in_page');
	}
	else{
		add_filter( 'the_title', 'cattopage_wud_titles', 10, 2);
	}
	
}
	
//Use unique categories and tags for pages	
	 if(get_option('cattopage_wud_unique')=="1"){
		add_action( 'init', 'wud_custom_cats', 0 );
		add_action( 'init', 'wud_custom_tags', 0 );	
	 }

//Debug used template file	
function show_wud_ctp_template() {
    global $template;
    $temp = basename($template);
	//echo $temp;
}

//If setting page excerpt is activated
function my_add_excerpts_to_pages() {
	if(get_option('cattopage_wud_exp_yes')==1){
		add_post_type_support( 'page', 'excerpt' );
	}
}

//If setting excerpt and if is archive page and if is pages
function ctp_change_to_excerpt($content) {
	global $post, $excerpt;
	if ( is_archive() && get_option('cattopage_wud_exp_yes')==1 && $post->post_type =="page" ) {
		//Unique page excerpt
		if( $post->post_excerpt && post_type_supports( 'page', 'excerpt' )) {
			$ctp_excerpt = $post->post_excerpt;
			$pattern = '~http(s)?://[^\s]*~i';
			$content = preg_replace($pattern, '', $ctp_excerpt);			
		}
		//Make excerpt from content
		else{
			$ctp_excerpt = strip_shortcodes ( wp_trim_words ( $content, get_option('cattopage_wud_exp_lenght') ) );
			$pattern = '~http(s)?://[^\s]*~i';
			$content = preg_replace($pattern, '', $ctp_excerpt);		
		}
	}
	return $content;
}

//Shortcode to show categories anywhere LIST
function ctp_short_code_cat_list($atts) {
	$ctp_show = "categories";
	if(get_option('cattopage_wud_unique')=="0"){
		$ctp_show = "category";
	}
	$ctp_title = get_option('cattopage_wud_widget_title1');
	$result = NULL;
	
	$categories = get_categories( array(
		'orderby' => 'name',
		'order'   => 'ASC',
		'post_type' => array('page','post'),
		'taxonomy' => $ctp_show
	) );
	$result .= "<strong>".$ctp_title."</strong>";
	foreach( $categories as $category ) {
		$category_link = sprintf( 
			'<a href="%1$s" alt="%2$s">%3$s</a>',
			esc_url( get_category_link( $category->term_id ) ),
			esc_attr( sprintf( '%s', $category->name ) ),
			esc_html( $category->name )
		);		 
		$result .= '<br>' . sprintf( '%s', $category_link ) . ' ('. $category->count.') ';
	} 
	return $result."<br><br>";
}

//Shortcode to show categories anywhere DROP
function ctp_short_code_cat_drop($atts) {
	$ctp_show = "categories";
	if(get_option('cattopage_wud_unique')=="0"){
		$ctp_show = "category";
	}	
	$ctp_title = get_option('cattopage_wud_widget_title1');
	$result = NULL;
	
	$result .= "<strong>".$ctp_title."</strong><br>";	
	$result .= '<select name="event-dropdown" onchange=\'document.location.href=this.options[this.selectedIndex].value;\'>'; 
		$categories = get_categories( array(
			'orderby' => 'name',
			'order'   => 'ASC',
			'post_type' => array('page','post'),
			'taxonomy' => $ctp_show
		) ); 
        foreach ($categories as $category) {
            $result .= '<option value="'.get_option('home').'/categories/'.$category->slug.'">';
            $result .= $category->cat_name;
            $result .= ' ('.$category->category_count.')';
            $result .= '</option>';
        }

	$result .= '</select>';
	return $result."<br><br>";
}

//Shortcode to show tags anywhere LIST
function ctp_short_code_tag_list($atts) {
	$ctp_show = "tags";
	if(get_option('cattopage_wud_unique')=="0"){
		$ctp_show = "post_tag";
	}	
	$ctp_title = get_option('cattopage_wud_widget_title2');
	$result = NULL;
	
	$categories = get_categories( array(
		'orderby' => 'name',
		'order'   => 'ASC',
		'post_type' => array('page','post'),
		'taxonomy' => $ctp_show
	) );
	$result .= "<strong>".$ctp_title."</strong>";
	foreach( $categories as $category ) {
		$category_link = sprintf( 
			'<a href="%1$s" alt="%2$s">%3$s</a>',
			esc_url( get_category_link( $category->term_id ) ),
			esc_attr( sprintf( '%s', $category->name ) ),
			esc_html( $category->name )
		);		 
		$result .= '<br>' . sprintf( '%s', $category_link ) . ' ('. $category->count.') ';
	} 
	return $result."<br><br>";
}

//Shortcode to show tags anywhere DROP
function ctp_short_code_tag_drop($atts) {
	$ctp_show = "tags";
	if(get_option('cattopage_wud_unique')=="0"){
		$ctp_show = "post_tag";
	}	
	$ctp_title = get_option('cattopage_wud_widget_title2');
	$result = NULL;

	$result .= "<strong>".$ctp_title."</strong><br>";	
	$result .= '<select name="event-dropdown" onchange=\'document.location.href=this.options[this.selectedIndex].value;\'>'; 
		$categories = get_categories( array(
			'orderby' => 'name',
			'order'   => 'ASC',
			'post_type' => array('page','post'),
			'taxonomy' => $ctp_show
		) ); 
        foreach ($categories as $category) {
            $result .= '<option value="'.get_option('home').'/tags/'.$category->slug.'">';
            $result .= $category->cat_name;
            $result .= ' ('.$category->category_count.')';
            $result .= '</option>';
        }

	$result .= '</select>';
	return $result."<br><br>";	
}



// grid-wud languages
function cattopage_wud_languages() {
	load_plugin_textdomain( 'category-to-pages-wud', false, dirname(plugin_basename( __FILE__ ) ) . '/languages' );
}
	 
function cattopage_wud_site_page(){
	wp_enqueue_script('jquery');
	wp_register_script('cattopage_wud_script', plugins_url( 'js/cat-to-page.js', __FILE__ ), array('jquery'), null, true );
	wp_enqueue_script('cattopage_wud_script');	
	wp_enqueue_style( 'cattopage_wud_site_style' );
	wp_enqueue_style( 'cattopage_wud_site_style', plugins_url('css/category-to-pages-wud.css', __FILE__ ), false, null );
}

// CSS for admin
function cattopage_wud_styling($hook) {
	if   ( $hook == "toplevel_page_category-to-pages-wud" ) {
		wp_enqueue_style( 'cattopage_wud_admin_style' );
		wp_enqueue_style( 'cattopage_wud_admin_style', plugins_url('css/admin.css', __FILE__ ), false, null );
     }
}

// Settings page menu item	
function cattopage_wud_create_menu() {
	add_menu_page( 'Page Category WUD', 'Cat to Page WUD', 'manage_options', 'category-to-pages-wud', 'cattopage_wud_settings_page', plugins_url('images/wud_icon.png', __FILE__ ) );
}

// category-to-pages-wud options page (menu options by plugins)
function cattopage_wud_action_links( $actions, $pcwud_set ){
		static $plugin;
		if (!isset($plugin))
			$plugin = plugin_basename(__FILE__);
		if ($plugin == $pcwud_set) {
				$settings_page = array('settings' => '<a href="'.admin_url("admin.php").'?page=category-to-pages-wud">' . __('Settings', 'General') . '</a>');
				$support_link = array('support' => '<a href="https://wordpress.org/support/plugin/category-to-pages-wud" target="_blank">Support</a>');				
					$actions = array_merge($support_link, $actions);
					$actions = array_merge($settings_page, $actions);
			}			
			return $actions;
}

// If someone already used this function ... deny errors ...	
if(!function_exists('cattopage_wud_settings_page')){
// Admin settings page	
function cattopage_wud_settings_page(){
		echo '<div class="ctp-wud-admin-table">';
		echo "<form name='cattopage_wud_form' method='post' action=".admin_url('admin.php')."?page=category-to-pages-wud>";
		echo'<h2 class="ctp-wud-admin-h2">'.__("Adds easily Post Categories to Pages!", "category-to-pages-wud").' (Version : '.get_option('pcwud_wud_version').')</h2>';
		echo '<img src="' . plugins_url( 'images/logo-category-to-pages-wud.png', __FILE__ ) . '" > ';
		echo '<a id="ctp-rate-it" href="https://wordpress.org/support/plugin/category-to-pages-wud" target="_blank" title="'.__("100% FREE PRO SUPPORT", "category-to-pages-wud").'" ><img src="' . plugins_url( 'images/wud-support.png', __FILE__ ) . '" ></a>';
		
	// Save the values to WP_OPTIONS
	if ( isset($_POST['ctp_opt_hidden']) && $_POST['ctp_opt_hidden'] == 'Y' && isset( $_POST['cattopage-wud-save'] ) && wp_verify_nonce($_POST['cattopage-wud-save'], 'cattopage-wud-check')) {
		
		// Check and save
		if ( isset($_POST['cattopage_wud_cat']) && $_POST['cattopage_wud_cat']=='1') {$cattopage_wud_cat = 'page';} else{$cattopage_wud_cat ='';}
		update_option('cattopage_wud_cat', filter_var($cattopage_wud_cat, FILTER_SANITIZE_STRING));

		if ( isset($_POST['cattopage_wud_unique']) && $_POST['cattopage_wud_unique']=='1') {$cattopage_wud_unique = '1';} else{$cattopage_wud_unique ='0';}
		update_option('cattopage_wud_unique', filter_var($cattopage_wud_unique, FILTER_SANITIZE_STRING));
		
		if ( isset($_POST['cattopage_wud_tag']) && $_POST['cattopage_wud_tag']=='1') {$cattopage_wud_tag = 'page';} else{$cattopage_wud_tag ='';}
		update_option('cattopage_wud_tag', filter_var($cattopage_wud_tag, FILTER_SANITIZE_STRING));
		
		if ( isset($_POST['cattopage_wud_title']) && $_POST['cattopage_wud_title']=='1') {$cattopage_wud_title = 'page';} else{$cattopage_wud_title ='';}
		update_option('cattopage_wud_title', filter_var($cattopage_wud_title, FILTER_SANITIZE_STRING));
		
		if ( isset($_POST['cattopage_wud_title_size']) && $_POST['cattopage_wud_title_size']=='') {$cattopage_wud_title_size ='16';} else{$cattopage_wud_title_size=$_POST['cattopage_wud_title_size'];}
		update_option('cattopage_wud_title_size', filter_var($cattopage_wud_title_size, FILTER_SANITIZE_STRING));

		if ( isset($_POST['cattopage_wud_quantity']) && $_POST['cattopage_wud_quantity']=='') {$cattopage_wud_quantity ='5';} else{$cattopage_wud_quantity=$_POST['cattopage_wud_quantity'];}
		update_option('cattopage_wud_quantity', filter_var($cattopage_wud_quantity, FILTER_SANITIZE_STRING));
		
		if ( isset($_POST['cattopage_wud_title_font']) && $_POST['cattopage_wud_title_font']=='') {$cattopage_wud_title_font ='inherit';} else{$cattopage_wud_title_font=$_POST['cattopage_wud_title_font'];}
		update_option('cattopage_wud_title_font', filter_var($cattopage_wud_title_font, FILTER_SANITIZE_STRING));		

		if ( isset($_POST['cattopage_wud_index_pos']) && $_POST['cattopage_wud_index_pos']=='') {$cattopage_wud_index_pos ='0';} else{$cattopage_wud_index_pos=$_POST['cattopage_wud_index_pos'];}
		update_option('cattopage_wud_index_pos', filter_var($cattopage_wud_index_pos, FILTER_SANITIZE_STRING));

		if ( isset($_POST['cattopage_wud_widget_option1']) && $_POST['cattopage_wud_widget_option1']=='1') {$cattopage_wud_widget_option1 = '1';} else{$cattopage_wud_widget_option1 ='0';}
		update_option('cattopage_wud_widget_option1', filter_var($cattopage_wud_widget_option1, FILTER_SANITIZE_STRING));
		
		if ( isset($_POST['cattopage_wud_widget_option2']) && $_POST['cattopage_wud_widget_option2']=='1') {$cattopage_wud_widget_option2 = '1';} else{$cattopage_wud_widget_option2 ='0';}
		update_option('cattopage_wud_widget_option2', filter_var($cattopage_wud_widget_option2, FILTER_SANITIZE_STRING));
		
		if ( isset($_POST['cattopage_wud_widget_parent']) && $_POST['cattopage_wud_widget_parent']=='1') {$cattopage_wud_widget_parent = '1';} else{$cattopage_wud_widget_parent ='0';}
		update_option('cattopage_wud_widget_parent', filter_var($cattopage_wud_widget_parent, FILTER_SANITIZE_STRING));
		
		if ( isset($_POST['cattopage_wud_exp_yes']) && $_POST['cattopage_wud_exp_yes']=='1') {$cattopage_wud_exp_yes = '1';} else{$cattopage_wud_exp_yes ='0';}
		update_option('cattopage_wud_exp_yes', filter_var($cattopage_wud_exp_yes, FILTER_SANITIZE_STRING));		
		
		if ( isset($_POST['cattopage_wud_exp_lenght']) ) {$cattopage_wud_exp_lenght =$_POST['cattopage_wud_exp_lenght'];}
		update_option('cattopage_wud_exp_lenght', filter_var($cattopage_wud_exp_lenght, FILTER_SANITIZE_STRING));

		if ( isset($_POST['cattopage_wud_widget_title1']) ) {$cattopage_wud_widget_title1 =$_POST['cattopage_wud_widget_title1'];}
		update_option('cattopage_wud_widget_title1', filter_var($_POST['cattopage_wud_widget_title1'], FILTER_SANITIZE_STRING));
		
		if ( isset($_POST['cattopage_wud_widget_title2']) ) {$cattopage_wud_widget_title2 =$_POST['cattopage_wud_widget_title2'];}
		update_option('cattopage_wud_widget_title2', filter_var($_POST['cattopage_wud_widget_title2'], FILTER_SANITIZE_STRING));
	

	//load options		
		// Saved message
		if( empty($error) ){
		echo '<div class="updated"><p><strong>'.__("Busy to save the settings ... one moment please.", "category-to-pages-wud").'</strong></p></div>';
		wud_custom_cats();
		wud_custom_tags();
		flush_rewrite_rules();
		echo '<meta http-equiv="refresh" content="1">';
		}
		
		// If some error occured
		else{
			echo "<div class='error'><p><strong>";
			foreach ( $error as $key=>$val ) {
				_e($val, 'ctp-wud'); 
				echo "<br/>";
			}
				echo "</strong></p></div>";
		}
	} 
	
	// READ the used vaiables
	else {
		$cattopage_wud_cat = get_option('cattopage_wud_cat');
		$cattopage_wud_unique = get_option('cattopage_wud_unique');
		$cattopage_wud_tag = get_option('cattopage_wud_tag');
		$cattopage_wud_title = get_option('cattopage_wud_title');
		$cattopage_wud_title_size = get_option('cattopage_wud_title_size');
		if(!get_option('cattopage_wud_title_size')){$cattopage_wud_title_size="16";}
		$cattopage_wud_quantity = get_option('cattopage_wud_quantity');
		if(!get_option('cattopage_wud_quantity')){$cattopage_wud_quantity="5";}		
		$cattopage_wud_title_font = get_option('cattopage_wud_title_font');
		$cattopage_wud_index_pos = get_option('cattopage_wud_index_pos');
		$cattopage_wud_widget_option1 = get_option('cattopage_wud_widget_option1');
		$cattopage_wud_widget_option2 = get_option('cattopage_wud_widget_option2');
		$cattopage_wud_widget_parent = get_option('cattopage_wud_widget_parent');
		$cattopage_wud_exp_yes = get_option('cattopage_wud_exp_yes');
		$cattopage_wud_exp_lenght = get_option('cattopage_wud_exp_lenght');
		$cattopage_wud_widget_title1 = get_option('cattopage_wud_widget_title1');
		$cattopage_wud_widget_title2 = get_option('cattopage_wud_widget_title2');
	}
		wp_nonce_field('cattopage-wud-check','cattopage-wud-save'); 
		echo '<br>';
		echo "<input type='hidden' name='ctp_opt_hidden' value='Y'>";
		echo '<hr><br>';
		
		echo "<div class='ctp-wud-wrap-d'>";
		echo '<strong>TIP:</strong> Use one or more of these Shortcodes in a post/page or widtget, to display the categories or tags.<br>';
		echo '<b>[wudcatlist]</b> = Displays categories as list. - <b>[wudtaglist]</b> = Displays tags as list.<br>';
		echo '<b>[wudcatdrop]</b> = Displays categories as drop down. - <b>[wudtagdrop]</b> = Displays tags as drop down.<br><br>';
		echo 'Please notice that all the settings on this page are related to the Wordpress pages and not to Wordpress posts!';
		echo "</div>";
		
	// ADMIN Left	$cattopage_wud_widget_option1	
		echo "<div class='ctp-wud-wrap-a'>";
		echo'<div id="ctp-wud-tip"><b class="ctp-trigger">?</b><div class="tooltip">'.__("If activated:<br>Categories are available for posts and pages.<br><br>If not activated:<br>Categories are only available for posts (WordPress standard).", "category-to-pages-wud").'</div></div>';				
		echo '<label>'.__("Add Categories to pages", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_cat" type="checkbox" value="1" '. checked( $cattopage_wud_cat, "page", false ) .'/>';
		
		echo '<br><br>';
		echo '</div>';
		
	// ADMIN Right	$cattopage_wud_widget_option2	
		echo "<div class='ctp-wud-wrap-2'>";
		echo'<div id="ctp-wud-tip"><b class="ctp-trigger">?</b><div class="tooltip">'.__("If activated:<br>Tags are available for posts and pages.<br><br>If not activated:<br>Tags are only available for posts (WordPress standard).", "category-to-pages-wud").'</div></div>';
		echo '<label>'.__("Add Tag to pages", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_tag" type="checkbox" value="1" '. checked( $cattopage_wud_tag, "page", false ) .'/>';
		echo '<br><br>';
		echo '</div>';
		
		echo "<div class='ctp-wud-wrap-b'>";
		echo '<label><b>'.__("Show the Category/Tag Title on the Page.", "category-to-pages-wud").'</b></label><br><br>';
		echo'<div id="ctp-wud-tip"><b class="ctp-trigger">?</b><div class="tooltip">'.__("Show the categories and tags, just BELOW the Page Title or on TOP of your Page Content.<br><br>Depending the Theme you are using, you can choose here where to place the Categories and Tags.", "category-to-pages-wud").'</div></div>';				
		echo '<label>'.__("Show", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_title" type="checkbox" value="1" '. checked( $cattopage_wud_title, "page", false ) .'/>';
		


		echo '<br><br>';
		
		echo '<label>'.__("Place it below the Title or on top of the Content", "category-to-pages-wud").'</label> ';
		echo '<select name="cattopage_wud_index_pos" style="float:right;">';
		echo     '<option value="0"'; if ( $cattopage_wud_index_pos == "0" ){echo 'selected="selected"';} echo '>Title</option>';
		echo     '<option value="1"'; if ( $cattopage_wud_index_pos == "1" ){echo 'selected="selected"';} echo '>Content</option>';
		echo '</select><br>';


		echo '<br><br><label>'.__("Font size", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_title_size" type="number"  min="12" max="34" value="'.$cattopage_wud_title_size.'"/><br><br>';
		echo '<label>'.__("Font Family", "category-to-pages-wud").'</label> ';
		echo '<select name="cattopage_wud_title_font" style="float:right;">';
		echo     '<option value="inherit"'; if ( $cattopage_wud_title_font == "inherit" ){echo 'selected="selected"';} echo '>Inherit</option>';
		echo     '<option value="initial"'; if ( $cattopage_wud_title_font == "initial" ){echo 'selected="selected"';} echo '>Initial</option>';
		echo     '<option value="Arial"'; if ( $cattopage_wud_title_font == "Arial" ){echo 'selected="selected"';} echo '>Arial</option>';
		echo     '<option value="Times New Roman"'; if ( $cattopage_wud_title_font == "Times New Roman" ){echo 'selected="selected"';} echo '>Times New Roman</option>';
		echo     '<option value="Georgia"'; if ( $cattopage_wud_title_font == "Georgia" ){echo 'selected="selected"';} echo '>Georgia</option>';
		echo     '<option value="Serif"'; if ( $cattopage_wud_title_font == "Serif" ){echo 'selected="selected"';} echo '>Serif</option>';
		echo     '<option value="Helvetica"'; if ( $cattopage_wud_title_font == "Helvetica" ){echo 'selected="selected"';} echo '>Helvetica</option>';
		echo     '<option value="Lucida Sans Unicode"'; if ( $cattopage_wud_title_font == "Lucida Sans Unicode" ){echo 'selected="selected"';} echo '>Lucida Sans Unicode</option>';
		echo     '<option value="Tahoma"'; if ( $cattopage_wud_title_font == "Tahoma" ){echo 'selected="selected"';} echo '>Tahoma</option>';
		echo     '<option value="Verdana"'; if ( $cattopage_wud_title_font == "Verdana" ){echo 'selected="selected"';} echo '>Verdana</option>';
		echo     '<option value="Courier New"'; if ( $cattopage_wud_title_font == "Courier New" ){echo 'selected="selected"';} echo '>Courier New</option>';
		echo     '<option value="Lucida Console"'; if ( $cattopage_wud_title_font == "Lucida Console" ){echo 'selected="selected"';} echo '>Lucida Console</option>';
		echo '</select><br><br>';		
		echo '<br><br>';
		echo "<br></div>";
		
		echo "<div class='ctp-wud-wrap-3'>";
		echo '<label>'.__("<b>", "category-to-pages-wud").'<u>'.__("Unique", "category-to-pages-wud").'</u>'.__(" page categories/tags", "category-to-pages-wud").': </b></label><br><br>';
		echo '<div id="ctp-wud-tip"><b class="ctp-trigger">?</b><div class="tooltip">'.__("If activated:<br>Categories and tags are unique for pages.<br><br>If not activated:<br>Page categories and tags are the same as they are for posts.", "category-to-pages-wud").'</div></div>';		
		echo '<label>'.__("Activate ", "category-to-pages-wud").'</label><input class="ctp-wud-right" name="cattopage_wud_unique" type="checkbox" value="1" '. checked( $cattopage_wud_unique, "1", false ) .'/>';
		echo '<br><label><br>'.__("Front-end result (URL)", "category-to-pages-wud").': </label>';
		if(get_option('cattopage_wud_unique')=="1"){
		echo '<br><label>'.__("Category = http://your_url/", "category-to-pages-wud").'<b style="color:red">'.__("categories</b>", "category-to-pages-wud").'</b>/...</label><br><label>'.__("Tag = http://your_url/", "category-to-pages-wud").'<b style="color:red">'.__("tags", "category-to-pages-wud").'</b>/...</label>';		
		}
		else{
		echo '<br><label>'.__("Category = http://your_url/", "category-to-pages-wud").'<b style="color:blue">'.__("category</b>", "category-to-pages-wud").'</b>/...</label><br><label>'.__("Tag = http://your_url/", "category-to-pages-wud").'<b style="color:blue">'.__("tag", "category-to-pages-wud").'</b>/...</label>';					
		}	
		echo '<br></div>';

		
		echo "<div class='ctp-wud-wrap-4'>";
		echo '<label>'.__("<b>", "category-to-pages-wud").'<u>'.__("Widget", "category-to-pages-wud").'</u>'.__(" Category to Pages", "category-to-pages-wud").': </b></label><br><br>';
		echo'<div id="ctp-wud-tip"><b class="ctp-trigger">?</b><div class="tooltip">'.__("If activated:<br>A list from maximum 5 page descriptions with URL's are displayed per Category and/or Tag.<br><br>If not activated:<br>No pages descriptions with URL's are displayed.", "category-to-pages-wud").'</div></div>';		
		echo '<label>'.__("Show Category and Tag pages ", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_widget_option1" type="checkbox" value="1" '. checked( $cattopage_wud_widget_option1, "1", false ) .'/>';
		echo '<br><br>';
		echo'<div id="ctp-wud-tip"><b class="ctp-trigger">?</b><div class="tooltip">'.__("If activated:<br>A button will appear to show the pages descriptions with URL's.<br>", "category-to-pages-wud").'</div></div>';
		echo '<label>'.__("Show a button to display the pages ", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_widget_option2" type="checkbox" value="1" '. checked( $cattopage_wud_widget_option2, "1", false ) .'/>';
		echo '<br><br>';
		echo '<label>'.__("Quantity pages to display (max.: 50)", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_quantity" type="number"  min="5" max="50" value="'.$cattopage_wud_quantity.'"/><br><br>';
		echo '<br>';
		echo'<div id="ctp-wud-tip"><b class="ctp-trigger">?</b><div class="tooltip">'.__("If activated:<br>It shows the parent categories pages only.<br><br>If not activated:<br>It shows the parent and child (sub) categories pages together.<br>", "category-to-pages-wud").'</div></div>';
		echo '<label>'.__("Show only Parent Categories ", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_widget_parent" type="checkbox" value="1" '. checked( $cattopage_wud_widget_parent, "1", false ) .'/>';
		echo '<br><br>';
		echo '<label>'.__("Page Category Description", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_widget_title1" type="text" value="'.$cattopage_wud_widget_title1.'"/><br><br>';
		echo '<label>'.__("Page Tag Description", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_widget_title2" type="text" value="'.$cattopage_wud_widget_title2.'"/><br><br>';		
		echo "<br></div>";


		echo "<div class='ctp-wud-wrap-c'>";
		echo'<div id="ctp-wud-tip"><b class="ctp-trigger">?</b><div class="tooltip">'.__("If activated:<br>Unique excerpts are available for pages and posts.<br><br>If not activated:<br>Excerpts are only available for posts (WordPress standard).", "category-to-pages-wud").'</div></div>';
		echo '<label>'.__("Use excerpts for pages", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_exp_yes" type="checkbox" value="1" '. checked( $cattopage_wud_exp_yes, "1", false ) .'/>';
		echo '<br><br>';
		echo '<label>'.__("Excerpt length in words (max.: 150)", "category-to-pages-wud").': </label><input class="ctp-wud-right" name="cattopage_wud_exp_lenght" type="number"  min="5" max="150" value="'.$cattopage_wud_exp_lenght.'"/><br><br>';
		echo "<br></div>";
		
		echo '<div class="clear"></div><br><hr>';
	// ADMIN Submit		
		echo '<input type="submit" name="Submit" class="button-primary" id="ctp-wud-adm-subm" value="'.__("Save Changes", "category-to-pages-wud").'" /><br><br>';
		echo "</form>";
		echo '<br><a href="https://wud-plugins.com" class="button-primary" id="ctp-adm-wud" target="_blank">'.__("Visit our website", "category-to-pages-wud").'</a>  <a href="https://wordpress.org/support/plugin/category-to-pages-wud" class="button-primary" id="ctp-adm-wud" target="_blank">'.__("Get FREE Support", "category-to-pages-wud").'</a>';
		echo ' <a href="https://wud-plugins.com/contact-us/" class="button-primary" id="ctp-adm-wud-or" target="_blank">'.__("Contact", "category-to-pages-wud").'</a><br>';
		echo '</div>';	
	
} // END cattopage_wud_settings_page
} // END check function

	
//-> Register the category and tag to post and pages
function cattopage_wud_reg_cat_tag(){ 
	$cattopage_wud_tag = get_option('cattopage_wud_tag');
	$cattopage_wud_cat = get_option('cattopage_wud_cat');
	
		register_taxonomy_for_object_type('post_tag', $cattopage_wud_tag); 
		register_taxonomy_for_object_type('category', $cattopage_wud_cat);
}


// Use the page category/tag if enabled
function cattopage_wud_cat_tag_archives( $wp_query ) {
	$cattopage_wud_tag = get_option('cattopage_wud_tag');
	$cattopage_wud_cat = get_option('cattopage_wud_cat');		
		$my_cat_array = array('post',$cattopage_wud_cat);
		$my_tag_array = array('post',$cattopage_wud_tag);
	
// Category post_type to post and page 
 if ( $wp_query->get( 'category_name' ) || $wp_query->get( 'cat' ) ){
	$wp_query->set( 'post_type', $my_cat_array );
 }

// Tag post_type to post and page
 if ( $wp_query->get( 'tag' ) ){
	$wp_query->set( 'post_type', $my_tag_array );
 }
}

/* 
function wud_custom_permalink(){ //[NOT IN USE FOR NOW]
		global $wp;
		$url = home_url( $wp->request );
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		$current_slug = substr($wp->request, strrpos($wp->request, '/') + 1);
		if ( $post = get_page_by_path( $current_slug, OBJECT, 'page' ) ){
			global $wp_rewrite; 
			$wp_rewrite->set_permalink_structure('/%category%/%pagename%/'); 
			update_option( "rewrite_rules", FALSE ); 
			$wp_rewrite->flush_rules( true );
		return $url;
		}
		else{
			global $wp_rewrite; 
			$wp_rewrite->set_permalink_structure('/%category%/%postname%/'); 
			update_option( "rewrite_rules", FALSE ); 
			$wp_rewrite->flush_rules( true );		
		return $url;		
		} 
}
add_action('wp', 'wud_custom_permalink'); 
*/


	
//-> Get page URL by category or categories
function wud_custom_urls( $url, $post ){
		$permalink = get_option('permalink_structure');
		//If no permalink structure or its admin panel, return the original URL
		if($permalink !== "/%category%/%postname%/" || is_admin()){
			return $url;
		}		
		$my_cat= NULL;
		$wud_post = get_post( $post );
		$post_type = $wud_post->post_type;
		$replace = $wud_post->post_name;
		//Only pages
		if($post_type=="page"){
			//Original WP category
			if(get_option('cattopage_wud_unique')=="0"){
				$terms = wp_get_post_terms( $wud_post->ID, 'category');
					if($terms){
					//If sub from categories, search parent
					if($terms[0]->parent !== 0){				
						$my_cat_nr= $terms[0]->parent.'/';
						$my_cat_id=get_term_by('id', $my_cat_nr, 'category');						
						$my_cat=$my_cat_id->slug.'/';
					}
					else{
						$my_cat= $terms[0]->slug.'/';
					}
				}			
			}
			//Custom category [NOT IN USE FOR NOW]
/*
 			else{
				$terms = wp_get_post_terms( $wud_post->ID, 'categories');
				if($terms){
					//If sub from categories, search parent
					if($terms[0]->parent !== 0){echo "A";					
						$my_cat_nr= $terms[0]->parent.'/';
						$my_cat_id=get_term_by('id', $my_cat_nr, 'categories');						
						$my_cat=$my_cat_id->slug.'/';
					}
					else{echo "B";
						$my_cat= $terms[0]->slug.'/';
					}
				}
			} 
*/	   
		}
		//If the URL haves already a category
		if (strpos($url, $my_cat) !== false) {
			return $url;
		} 		
	$url = str_replace($wud_post->post_name, $my_cat.$replace, $url );
	return $url;
}
add_filter( 'page_link', 'wud_custom_urls', 'edit_files', 2 );


//-> Register the unique category and tag to pages
function wud_custom_cats() {
 if(get_option('cattopage_wud_cat')=="page"){	 
  $labels = array(
    'name' => _x( 'Page Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Page Categories' ),
    'all_items' => __( 'All Page Categories' ),
    'parent_item' => __( 'Parent Page Category' ),
    'parent_item_colon' => __( 'Parent Page Category:' ),
    'edit_item' => __( 'Edit Page Category' ), 
    'update_item' => __( 'Update Page Category' ),
    'add_new_item' => __( 'Add New Page Category' ),
    'new_item_name' => __( 'New Page Category Name' ),
    'menu_name' => __( 'Page Categories' ),
  ); 	

  $args = array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
	'public' => true,
    'show_admin_column' => true,
	'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array('slug' => 'categories', 'with_front' => false)
  );
  register_taxonomy('categories',array('page'),$args);
  
 }
}

// UNIQUE TAG
function wud_custom_tags() {
 if(get_option('cattopage_wud_tag')=="page"){ 
  $labels = array(
    'name' => _x( 'Page Tags', 'taxonomy general name' ),
    'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Page Tags' ),
    'popular_items' => __( 'Popular Page Tags' ),
    'all_items' => __( 'All Page Tags' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Page Tag' ), 
    'update_item' => __( 'Update Page Tag' ),
    'add_new_item' => __( 'Add New Page Tag' ),
    'new_item_name' => __( 'New Page Tag Name' ),
    'separate_items_with_commas' => __( 'Separate Page Tags with commas' ),
    'add_or_remove_items' => __( 'Add or remove Page Tags' ),
    'choose_from_most_used' => __( 'Choose from the most used Page Tags' ),
    'menu_name' => __( 'Page Tags' ),
  ); 

  $args = array(
    'hierarchical' => false,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'tags', 'with_front' => false ),
  );
  register_taxonomy('tags',array('page'), $args);
 }
}

//Show Category and ord tag title on pages
function cattopage_wud_titles( $title , $id = null ) {
		global $post;
	
	$cats_title = NULL;
	$tags_title = NULL;
	//Font Size
	$sizect = get_option('cattopage_wud_title_size');
	if(empty($sizect)){$sizect="12";}
	//Line Size
	$sizel=$sizect+1;
	//Font Family
	$fontct = get_option('cattopage_wud_title_font');
	if(empty($fontct)){$fontct="inherit";}

	if(!empty($post)){	
	//If UNIQUE Categories and Tags
		if(get_option('cattopage_wud_unique')=="1"){
				if(get_option('cattopage_wud_cat')=="page"){
					$cats_title = 	get_the_term_list( $post->ID, 'categories', '', ', ' );
				}
				if(get_option('cattopage_wud_tag')=="page"){
					$tags_title = 	get_the_term_list( $post->ID, 'tags', '', ', ' );
				}
		}
	//If WordPress Categories and Tags
		else{	
				if(get_option('cattopage_wud_cat')=="page"){
					$cats_title = 	get_the_term_list( $post->ID, 'category', '', ', ' );
				}
				if(get_option('cattopage_wud_tag')=="page"){
					$tags_title = 	get_the_term_list( $post->ID, 'post_tag', '', ', ' );
				}					
		}
	}	
	//If nothing is in the loop ... return
    if(!in_the_loop()){return $title;}

	//If Oké, display the Title('s)
     if(is_page() && get_option('cattopage_wud_title')=='page' ){
			if(!empty($cats_title)){
				$cats_title = "<p class='ctp-wud-title' style= 'font-family:".$fontct."; font-size: ".$sizect."px; line-height: ".$sizel."px; margin: 0px; margin-top: 4px;'><span class='wudicon wudicon-category' style='font-size: ".$sizect."px;'> </span>".$cats_title."</p>";
			}
			if(!empty($tags_title)){
				$tags_title = "<p class='ctp-wud-title' style= 'font-family:".$fontct."; font-size: ".$sizect."px; line-height: ".$sizel."px; margin: 0px; margin-top: 4px;'><span class='wudicon wudicon-tag' style='font-size: ".$sizect."px;'> </span>".$tags_title."</p>";
			}
		//Build the new Title ...
		$title .= $cats_title.$tags_title;
	} 
    return $title;
}

function cattopage_wud_titles_in_page($content) {	
   if(is_page()) {	   
 
		global $post;
	
	$cats_title = NULL;
	$tags_title = NULL;
	//Font Size
	$sizect = get_option('cattopage_wud_title_size');
	if(empty($sizect)){$sizect="12";}
	//Line Size
	$sizel=$sizect+1;
	//Font Family
	$fontct = get_option('cattopage_wud_title_font');
	if(empty($fontct)){$fontct="inherit";}

	if(!empty($post)){	
	//If UNIQUE Categories and Tags
		if(get_option('cattopage_wud_unique')=="1"){
				if(get_option('cattopage_wud_cat')=="page"){
					$cats_title = 	get_the_term_list( $post->ID, 'categories', '', ', ' );
				}
				if(get_option('cattopage_wud_tag')=="page"){
					$tags_title = 	get_the_term_list( $post->ID, 'tags', '', ', ' );
				}
		}
	//If WordPress Categories and Tags
		else{	
				if(get_option('cattopage_wud_cat')=="page"){
					$cats_title = 	get_the_term_list( $post->ID, 'category', '', ', ' );
				}
				if(get_option('cattopage_wud_tag')=="page"){
					$tags_title = 	get_the_term_list( $post->ID, 'post_tag', '', ', ' );
				}					
		}
	}	

	//If Oké, display the Title('s)
     if(is_page() && get_option('cattopage_wud_title')=='page' ){
			if(!empty($cats_title)){
				$cats_title = "<p class='ctp-wud-title' style= 'font-family:".$fontct."; font-size: ".$sizect."px; line-height: ".$sizel."px; margin: 0px; margin-top: 4px;'><span class='wudicon wudicon-category' style='font-size: ".$sizect."px;'> </span>".$cats_title."</p>";
			}
			if(!empty($tags_title)){
				$tags_title = "<p class='ctp-wud-title' style= 'font-family:".$fontct."; font-size: ".$sizect."px; line-height: ".$sizel."px; margin: 0px; margin-top: 4px;'><span class='wudicon wudicon-tag' style='font-size: ".$sizect."px;'> </span>".$tags_title."</p>";
			}
		//Build the new Title ...
		$catstags = '<div style="margin-bottom:20px;">'.$cats_title.$tags_title.'</div>';
	}
	  $content = $catstags.$content;
   }
   return $content;
}

//Show Categories and ord tags as widget 
wp_register_sidebar_widget(
    'cattopage_wud_widget',
    'Category to Page WUD',
    'cattopage_wud_widget_display',
    array(
        'description' => 'Summarize page categories and/or tags'
    )
);

wp_register_widget_control(
	'cattopage_wud_widget',
	'cattopage_wud_widget',
	'cattopage_wud_widget_control'
);

//Categories and ord tags Widget settings
function cattopage_wud_widget_control($args=array(), $params=array()) {
	
	if (isset($_POST['submitted'])) {
			update_option('cattopage_wud_widget_title', filter_var($_POST['cattopage_wud_widget_title'], FILTER_SANITIZE_STRING));
		
		if($_POST['cattopage_wud_widget_cats']==""){$_POST['cattopage_wud_widget_cats']="0";}
			update_option('cattopage_wud_widget_cats', filter_var($_POST['cattopage_wud_widget_cats'], FILTER_SANITIZE_STRING));
			
		if($_POST['cattopage_wud_widget_tags']==""){$_POST['cattopage_wud_widget_tags']="0";}
			update_option('cattopage_wud_widget_tags', filter_var($_POST['cattopage_wud_widget_tags'], FILTER_SANITIZE_STRING));
			
			update_option('cattopage_wud_widget_font1', filter_var($_POST['cattopage_wud_widget_font1'], FILTER_SANITIZE_STRING));
			update_option('cattopage_wud_widget_title1', filter_var($_POST['cattopage_wud_widget_title1'], FILTER_SANITIZE_STRING));
			update_option('cattopage_wud_widget_font2', filter_var($_POST['cattopage_wud_widget_font2'], FILTER_SANITIZE_STRING));
			update_option('cattopage_wud_widget_title2', filter_var($_POST['cattopage_wud_widget_title2'], FILTER_SANITIZE_STRING));
	}

	//load options
	$cattopage_wud_widget_title = get_option('cattopage_wud_widget_title');
	$cattopage_wud_widget_cats = get_option('cattopage_wud_widget_cats');
	$cattopage_wud_widget_tags = get_option('cattopage_wud_widget_tags');
	$cattopage_wud_widget_font1 = get_option('cattopage_wud_widget_font1');
	$cattopage_wud_widget_title1 = get_option('cattopage_wud_widget_title1');
	$cattopage_wud_widget_font2 = get_option('cattopage_wud_widget_font2');
	$cattopage_wud_widget_title2 = get_option('cattopage_wud_widget_title2');
	?>
	
	<br><br>
	<b><?php echo __("Widget Title", "category-to-pages-wud"); ?>: </b>
	<input style="float:right; width:60%;" type="text" class="widefat" name="cattopage_wud_widget_title" value="<?php echo stripslashes($cattopage_wud_widget_title); ?>" />
	<br><br>	
	<hr><br>
	
	<b><?php echo __("Show Page Categories", "category-to-pages-wud"); ?>: </b>
	<input style="float:right;" type="checkbox" name="cattopage_wud_widget_cats" value="1" <?php echo ($cattopage_wud_widget_cats==1 ? 'checked' : '');?> /> 
	<br><br>
	<?php echo __("Page Category Title", "category-to-pages-wud"); ?>: 
	<input style="float:right; width:60%;" type="text" class="widefat" name="cattopage_wud_widget_title1" value="<?php echo stripslashes($cattopage_wud_widget_title1); ?>" />
	<br><br>	
	<?php echo __("CCS Font Title", "category-to-pages-wud"); ?>: 	
		<select name="cattopage_wud_widget_font1" style="float:right;">
		<option value="normal" <?php if ( $cattopage_wud_widget_font1 == "normal" ){echo 'selected="selected"';} echo '>normal'; ?></option>
		<option value="strong" <?php if ( $cattopage_wud_widget_font1 == "strong" ){echo 'selected="selected"';} echo '>strong'; ?></option>
		<option value="h1" <?php if ( $cattopage_wud_widget_font1 == "h1" ){echo 'selected="selected"';} echo '>h1'; ?></option>
		<option value="h2" <?php if ( $cattopage_wud_widget_font1 == "h2" ){echo 'selected="selected"';} echo '>h2'; ?></option>
		<option value="h3" <?php if ( $cattopage_wud_widget_font1 == "h3" ){echo 'selected="selected"';} echo '>h3'; ?></option>
		</select> 
	<br><br>	
	<hr><br>
	
	<b><?php echo __("Show Page Tags", "category-to-pages-wud"); ?>: </b>
	<input style="float:right;" type="checkbox" name="cattopage_wud_widget_tags" value="1" <?php echo ($cattopage_wud_widget_tags==1 ? 'checked' : '');?> /> 
	<br><br>
	<?php echo __("Page Tagg Title", "category-to-pages-wud"); ?>: 
	<input style="float:right; width:60%;" type="text" class="widefat" name="cattopage_wud_widget_title2" value="<?php echo stripslashes($cattopage_wud_widget_title2); ?>" />
	<br><br>	
	<?php echo __("CCS Font Title", "category-to-pages-wud"); ?>: 
		<select name="cattopage_wud_widget_font2" style="float:right;">
		<option value="normal" <?php if ( $cattopage_wud_widget_font2 == "normal" ){echo 'selected="selected"';} echo '>normal'; ?></option>
		<option value="strong" <?php if ( $cattopage_wud_widget_font2 == "strong" ){echo 'selected="selected"';} echo '>strong'; ?></option>
		<option value="h1" <?php if ( $cattopage_wud_widget_font2 == "h1" ){echo 'selected="selected"';} echo '>h1'; ?></option>
		<option value="h2" <?php if ( $cattopage_wud_widget_font2 == "h2" ){echo 'selected="selected"';} echo '>h2'; ?></option>
		<option value="h3" <?php if ( $cattopage_wud_widget_font2 == "h3" ){echo 'selected="selected"';} echo '>h3'; ?></option>
		</select> 
	<br><br>
	
	<input type="hidden" name="submitted" value="1" />
	<?php
}

//Categories and ord tags Widget display
function cattopage_wud_widget_display($args=array(), $params=array()) {
	//load options
	$cattopage_wud_widget_title = get_option('cattopage_wud_widget_title');
	$cattopage_wud_widget_cats = get_option('cattopage_wud_widget_cats');
	$cattopage_wud_widget_tags = get_option('cattopage_wud_widget_tags');
	
	//widget output
	echo stripslashes($args['before_widget']);

	echo stripslashes($args['before_title']);
	echo stripslashes($cattopage_wud_widget_title);
	echo stripslashes($args['after_title']);
	
	echo '<div class="textwidget">';
	
	if($cattopage_wud_widget_cats=="1"){
		if(get_option('cattopage_wud_unique')=="0"){
			echo cattopage_wud_widget_urls("category");
		}
		else{
			echo cattopage_wud_widget_urls("categories");
		}		
	}
	
	if($cattopage_wud_widget_tags=="1"){		
		if(get_option('cattopage_wud_unique')=="0"){
			echo cattopage_wud_widget_urls("post_tag");
		}
		else{
			echo cattopage_wud_widget_urls("tags");
		}
	}
	
	echo '</div>';//close div.textwidget
  echo stripslashes($args['after_widget']);
}

//Categories and ord tags Widget content
function cattopage_wud_widget_urls($cat_tag){
	global $post;
	$cattopage_wud_widget_font1 = get_option('cattopage_wud_widget_font1');
	$cattopage_wud_widget_title1 = get_option('cattopage_wud_widget_title1');
	$cattopage_wud_widget_font2 = get_option('cattopage_wud_widget_font2');
	$cattopage_wud_widget_title2 = get_option('cattopage_wud_widget_title2');
	$cattopage_wud_widget_option1 = get_option('cattopage_wud_widget_option1');
	$cattopage_wud_widget_option2 = get_option('cattopage_wud_widget_option2');
	$cattopage_wud_widget_parent = get_option('cattopage_wud_widget_parent');
	$cattopage_wud_quantity = get_option('cattopage_wud_quantity');
	if(!get_option('cattopage_wud_quantity')){$cattopage_wud_quantity="5";}		
	
	$args = get_terms($cat_tag, array('parent' => 0, 'orderby' => 'slug', 'hide_empty' => true));	
	
if ( ! empty( $args ) && ! is_wp_error( $args ) ) {
    $count = count( $args );
    $i = 0;
    $term_list = '<p class="wud_cat_tag_css">';
		if(!empty($cat_tag) && ($cat_tag=="categories" || $cat_tag=="category")){
			$term_list .= '<'.$cattopage_wud_widget_font1.'><span class="wudicon wudicon-category"></span> '.$cattopage_wud_widget_title1.'</'.$cattopage_wud_widget_font1.'>';
			if($cattopage_wud_widget_font1=="normal" || $cattopage_wud_widget_font1=="strong"){$term_list .= '<br>';}
		}
		if(!empty($cat_tag) && ($cat_tag=="tags" || $cat_tag=="post_tag")){
			$term_list .= '<'.$cattopage_wud_widget_font2.'><span class="wudicon wudicon-tag"></span> '.$cattopage_wud_widget_title2.'</'.$cattopage_wud_widget_font2.'>';
			if($cattopage_wud_widget_font2=="normal" || $cattopage_wud_widget_font2=="strong"){$term_list .= '<br>';}
		}
		
	if($cat_tag=="categories" || $cat_tag=="category" || $cat_tag=="tags" || $cat_tag=="post_tag") {
		
		foreach ($args as $pterm) {
			$xterms = get_terms($cat_tag, array('parent' => $pterm->term_id, 'orderby' => 'slug', 'hide_empty' => false));	
	//-> CAT OR TAG
			$cattopage_wud_cnt= substr(round(microtime(true) * 1000),10,3);
		
			if($cattopage_wud_widget_option2=="1"){
				$term_list .= '<button ClickResult="'.$cattopage_wud_cnt.'" class="cattopage_wud_split" id="cattopage_wud_split"><span>+</span></button> ';
			}
			
			//If current page haves this category or tag
			$return = is_object_in_term( $post->ID, $cat_tag, $pterm->slug );
				if(!empty($return)){$term_list .='<b>';} 
			$term_list .= '<a href="' . esc_url( get_term_link( $pterm ) ) . '">' . $pterm->name . '</a><br>';
				if(!empty($return)){$term_list .='</b>';}
				
			//Show pages URL
			if($cattopage_wud_widget_option1=="1"){	
				if($cattopage_wud_widget_option2=="1"){
					$term_list .= '<div class="cattopage_wud_items" id="cattopage_wud_split_'.$cattopage_wud_cnt.'">';
				}
				$argspost = array( 'posts_per_page' => $cattopage_wud_quantity, 'post_status'	=> 'publish', 'post_type' => array('page','post'), 'offset'=> 0, 'tax_query' => array(array('taxonomy' => $cat_tag, 'field' => 'slug', 'terms' => array($pterm->slug))),);
				$myposts = get_posts( $argspost );
				foreach ( $myposts as $postwud ){ 
				
				//Check or this is the PARENT category (no child/sub)
				$terms = get_the_terms($postwud->ID, $cat_tag);
				$term_parent=0;
				if($terms){
				   foreach ($terms as $term) {
					 if (($term->parent) == 0) {$term_parent=0;}
					   else{
						   //If parameter parent is not 1, show also childs (subs)
						   if($cattopage_wud_widget_parent =="1"){
							   $term_parent=1;
							}
						 }  
					}
				}
								
				//If is a Tag or the Category is Parent
				if($term_parent==0){$term_list .= '&nbsp;&#8627;&nbsp;<a href="'.esc_url(get_permalink($postwud->ID)).'">'.$postwud->post_title.'</a><br>';}			
				}
				if($cattopage_wud_widget_option2=="1"){
					$term_list .= '</div>';
				}
			}		
			foreach ($xterms as $term) {
	//-> SUB CAT OR SUB TAG
				$cattopage_wud_cnt= substr(round(microtime(true) * 1000),10,3);

				if($cattopage_wud_widget_option2=="1"){
					$term_list .= '<button ClickResult="'.$cattopage_wud_cnt.'" class="cattopage_wud_split" id="cattopage_wud_split"><span>+</span></button> ';
				}
				
				//If current page haves this category or tag
				$return = is_object_in_term( $post->ID, $cat_tag, $term->slug );
					if(!empty($return)){$term_list .='<b>';} 
				$term_list .= '&#9492; &nbsp;<a href="' . esc_url( get_term_link( $term ) ) . '">' . $term->name . '</a><br>'; 
					if(!empty($return)){$term_list .='</b>';}
					
				//Show pages URL
				if($cattopage_wud_widget_option1=="1"){	
					if($cattopage_wud_widget_option2=="1"){
						$term_list .= '<div class="cattopage_wud_items" id="cattopage_wud_split_'.$cattopage_wud_cnt.'">';
					}
					$argspost = array( 'posts_per_page' => $cattopage_wud_quantity, 'post_status'	=> 'publish', 'post_type' => array('page','post'), 'offset'=> 0, 'tax_query' => array(array('taxonomy' => $cat_tag, 'field' => 'slug', 'terms' => array($term->slug))),);
					$myposts = get_posts( $argspost );
					foreach ( $myposts as $postwud ){ 
					$term_list .= '&nbsp;&#8627;&nbsp;<a href="'.esc_url(get_permalink($postwud->ID)).'">'.$postwud->post_title.'</a><br>';
					}
					if($cattopage_wud_widget_option2=="1"){
						$term_list .= '</div>';
					}
				}				
			}
		}
		
		return $term_list;
	}
	else{
		$term_list .= '</p>';
		return $term_list;
	}
}
}

function pcwud_wud_update(){
		global $ctp_version; 
			//Update version number
			update_option('pcwud_wud_version', $ctp_version);
			//Update new fields		
			if (get_option('cattopage_wud_cat')=='') {update_option('cattopage_wud_cat', '');}
			if (get_option('cattopage_wud_unique')=='') {update_option('cattopage_wud_unique', 0);}
			if (get_option('cattopage_wud_tag')=='') {update_option('cattopage_wud_tag', '');}
			if (get_option('cattopage_wud_title')=='') {update_option('cattopage_wud_title', '');}
			if (get_option('cattopage_wud_title_size')=='') {update_option('cattopage_wud_title_size', 16);}
			if (get_option('cattopage_wud_quantity')=='') {update_option('cattopage_wud_quantity', 5);}
			if (get_option('cattopage_wud_title_font')=='') {update_option('cattopage_wud_title_font', 'inherit');}
			if (get_option('cattopage_wud_index_pos')=='') {update_option('cattopage_wud_index_pos', 0);}
			if (get_option('cattopage_wud_widget_option1')=='') {update_option('cattopage_wud_widget_option1', 0);}
			if (get_option('cattopage_wud_widget_option2')=='') {update_option('cattopage_wud_widget_option2', 0);}
			if (get_option('cattopage_wud_widget_parent')=='') {update_option('cattopage_wud_widget_parent', 0);}
			if (get_option('cattopage_wud_exp_yes')=='') {update_option('cattopage_wud_exp_yes', 0);}
			if (get_option('cattopage_wud_exp_lenght')=='') {update_option('cattopage_wud_exp_lenght', 20);}	
			if (get_option('cattopage_wud_widget_title1')=='') {update_option('cattopage_wud_widget_title1', '');}
			if (get_option('cattopage_wud_widget_title2')=='') {update_option('cattopage_wud_widget_title2', '');}			
}

?>
