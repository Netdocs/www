<?php

/*-----------------------------------------------------------------------------------

	Here we have all the custom functions for the theme.
	Please be extremely cautious editing this file,
	When things go wrong, they tend to go wrong in a big way.
	You have been warned!

-------------------------------------------------------------------------------------*/

if ( ! isset( $content_width ) ) $content_width = 900;

// woocommerce fixes
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

/*-----------------------------------------------------------------------------------*/
/*	Theme set up
/*-----------------------------------------------------------------------------------*/

if (!function_exists('streamium_theme_setup')) {
    function streamium_theme_setup() {	
    	/* Configure WP 2.9+ Thumbnails ---------------------------------------------*/
    	add_theme_support('post-thumbnails');
        add_theme_support( 'automatic-feed-links' );
        add_image_size( 'streamium-video-poster', 600, 338, true ); // (cropped)
        add_image_size( 'streamium-video-category', 285, 160 );
        add_image_size( 'streamium-home-slider', 1600, 900 ); 
        add_image_size( 'streamium-site-logo', 0, 56, true ); 
        add_theme_support( 'title-tag' );
    } 
}
add_action('after_setup_theme', 'streamium_theme_setup');

function cloudfrontSwitch($url){

  if ( get_theme_mod( 'streamium_enable_cloudfront' ) ){
  
    return str_replace(get_site_url(),get_theme_mod( 'streamium_enable_cloudfront_url' ),$url);

  }else{
  
    return $url;
  
  } 

}

/*-----------------------------------------------------------------------------------*/
/*	Register javascript and css
/*-----------------------------------------------------------------------------------*/

if (!function_exists('streamium_enqueue_scripts')) {
	function streamium_enqueue_scripts() {

        /*wp_enqueue_script( 'streamium-bootstrap', cloudfrontSwitch(get_template_directory_uri()) . '/dist/js/fontawesome.min.js', array( 'jquery') );*/
        wp_enqueue_script( 'streamium-bootstrap', cloudfrontSwitch(get_template_directory_uri()) . '/dist/js/bootstrap.min.js', array( 'jquery') );
	      wp_enqueue_script( 'streamium-slick', cloudfrontSwitch(get_template_directory_uri()) . '/dist/extras/slick/slick.min.js', array( 'jquery') );
        wp_enqueue_script( 'streamium-modernizr', cloudfrontSwitch(get_template_directory_uri()) . '/dist/js/modernizr.min.js', array( 'jquery') );
        wp_enqueue_script( 'streamium-jquery.mobile.custom', cloudfrontSwitch(get_template_directory_uri()) . '/dist/js/jquery.mobile.custom.min.js', array( 'jquery') );
        wp_enqueue_script( 'streamium-menu', cloudfrontSwitch(get_template_directory_uri()) . '/dist/js/menu.min.js', array( 'jquery') );
        wp_enqueue_script( 'streamium-modal', cloudfrontSwitch(get_template_directory_uri()) . '/dist/js/comments.min.js', array( 'jquery') );
        wp_enqueue_script( 'streamium-info', cloudfrontSwitch(get_template_directory_uri()) . '/dist/js/jquery.flexslider-min.js', array( 'jquery') );
        wp_enqueue_script( 'streamium-velocity', cloudfrontSwitch(get_template_directory_uri()) . '/dist/js/masonry.pkgd.min.js', array( 'jquery') );
		wp_enqueue_script( 'streamium-s3bubble-cdn', cloudfrontSwitch(get_template_directory_uri()) . '/dist/js/s3bubble.min.js', array( 'jquery') );
        /*wp_enqueue_script( 'streamium-s3bubble-cdn', 'https://s3.amazonaws.com/s3bubble.assets/streamium/s3bubble-hosted-cdn.min.js' );*/
        wp_enqueue_script( 'streamium-scripts', cloudfrontSwitch(get_template_directory_uri()) . '/dist/js/main.min.js', array( 'jquery') );
        wp_localize_script( 'streamium-scripts', 'streamium_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

        if( is_singular() ) {
            wp_enqueue_script('comment-reply'); // loads the javascript required for threaded comments
        }

        /* Register styles -----------------------------------------------------*/
        wp_enqueue_style( 'streamium-styles', get_stylesheet_uri() );
        wp_enqueue_style('streamium-reset', cloudfrontSwitch(get_template_directory_uri()) . '/dist/css/bootstrap.min.css');  
        wp_enqueue_style('streamium-menu', cloudfrontSwitch(get_template_directory_uri()) . '/dist/css/menu.min.css');
        wp_enqueue_style('streamium-modal', cloudfrontSwitch(get_template_directory_uri()) . '/dist/css/comments.min.css');
        wp_enqueue_style('streamium-info', cloudfrontSwitch(get_template_directory_uri()) . '/dist/css/info.min.css');
        wp_enqueue_style('streamium-slick', cloudfrontSwitch(get_template_directory_uri()) . '/dist/extras/slick/slick.min.css');
        wp_enqueue_style('streamium-slick-theme', cloudfrontSwitch(get_template_directory_uri()) . '/dist/extras/slick/slick-theme.min.css');
        wp_enqueue_style('streamium-main', cloudfrontSwitch(get_template_directory_uri()) . '/dist/css/main.min.css');       
        wp_enqueue_style('streamium-woocommerce', cloudfrontSwitch(get_template_directory_uri()) . '/dist/css/woocommerce.min.css');
        wp_enqueue_style('streamium-s3bubble-cdn', cloudfrontSwitch(get_template_directory_uri()) . '/dist/css/s3bubble.min.css');  
        wp_enqueue_style('streamium-merged', cloudfrontSwitch(get_template_directory_uri()) . '/dist/css/mergedstyle.css');  
		wp_enqueue_style('streamium-none', cloudfrontSwitch(get_template_directory_uri()) . '/dist/css/none.css');  
		wp_enqueue_style('streamium-getstarted', cloudfrontSwitch(get_template_directory_uri()) . '/dist/css/getstarted.css');  
		/*wp_enqueue_style('streamium-s3bubble-cdn', 'https://s3.amazonaws.com/s3bubble.assets/streamium/s3bubble-hosted-cdn.min.css');*/
		

        

	}
}

add_action('wp_enqueue_scripts', 'streamium_enqueue_scripts');

 
function so_27023433_disable_checkout_script(){
    wp_dequeue_script( 'wc-checkout' );
}
//add_action( 'wp_enqueue_scripts', 'so_27023433_disable_checkout_script' );

/*-----------------------------------------------------------------------------------*/
/*  New theme customizer options
/*-----------------------------------------------------------------------------------*/
class Streamium_Customize {
   /**
    * This hooks into 'customize_register' (available as of WP 3.4) and allows
    * you to add new sections and controls to the Theme Customize screen.
    * 
    * Note: To enable instant preview, we have to actually write a bit of custom
    * javascript. See live_preview() for more.
    *  
    * @see add_action('customize_register',$func)
    * @param \WP_Customize_Manager $wp_customize
    * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/
    * @since Streamium 1.0
    */
   public static function register ( $wp_customize ) {

      // allow the user to remove the powered by link
      $wp_customize->add_setting('streamium_remove_powered_by_s3bubble');
      
      $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_remove_powered_by_s3bubble',
        array(
          'label' => 'Replace Powered By S3Bubble Text',
          'section' => 'title_tagline',
          'settings' => 'streamium_remove_powered_by_s3bubble',
        )) 
      );

      $wp_customize->remove_control('display_header_text');

      $wp_customize->add_section( 'streamium_options', 
         array(
            'title' => __( 'Streamium Options', 'streamium' ), 
            'priority' => 35, 
            'capability' => 'edit_theme_options', 
            'description' => __('Allows you to customize some example settings for Streamium.', 'streamium'),
         ) 
      );

      $wp_customize->add_section( 'streamium_logo_section' , array(
          'title'       => __( 'Streamium Styles', 'streamium' ),
          'priority'    => 30,
          'description' => 'Upload a logo to replace the default site name and description in the header',
      ) );

       $wp_customize->add_setting('tutorial_btn', array(
          'default' => false
      ));
      $wp_customize->add_control(
          new WP_Customize_Control(
              $wp_customize,
              'tutorial_btn',
              array(
                  'label'     => __('Remove tutorial header', 'streamium'),
                  'section'   => 'streamium_logo_section',
                  'settings'  => 'tutorial_btn',
                  'type'      => 'checkbox',
              )
          )
      );

      $wp_customize->add_setting( 'streamium_logo' );

      $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'streamium_logo', array(
          'label'    => __( 'Logo', 'streamium' ),
          'section'  => 'streamium_logo_section',
          'settings' => 'streamium_logo',
      ) ) );
      
      // plans background image
      $wp_customize->add_setting( 'streamium_plans_bg' );

      $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'streamium_plans_bg', array(
          'label'    => __( 'Background', 'streamium' ),
          'section'  => 'streamium_logo_section',
          'settings' => 'streamium_plans_bg',
      ) ) );

      $wp_customize->add_setting( 'link_textcolor', 
         array(
            'default' => '#2BA6CB', 
            'type' => 'theme_mod', 
            'capability' => 'edit_theme_options', 
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color'
         ) 
      );      
          
      $wp_customize->add_control( new WP_Customize_Color_Control( 
         $wp_customize, 
         'streamium_link_textcolor', 
         array(
            'label' => __( 'Main Theme Color', 'streamium' ), 
            'section' => 'colors', 
            'settings' => 'link_textcolor', 
            'priority' => 10, 
         ) 
      ) );

      // Add site options
      $wp_customize->add_section('streamium_global_options' , array(
          'title'     => __('Global Options', 'streamium'),
          'priority'  => 1
      ));

      $wp_customize->add_setting('streamium_global_options_homepage_desktop', array(
          'default'  => '-1'
      ));

      $wp_customize->add_control(
          new WP_Customize_Control(
              $wp_customize,
              'streamium_global_options_homepage_desktop',
              array(
                  'label'     => __('Maximum carousel videos - Desktop', 'streamium'),
                  'section'   => 'streamium_global_options',
                  'settings'  => 'streamium_global_options_homepage_desktop',
                  'type'      => 'select',
                  'choices' => array(
                      '-1'  => __( '-1' ),
                      '6'   => __( '6' ),
                      '12'  => __( '12' ),
                      '18'  => __( '18' ),
                      '24'  => __( '24' ),
                      '30'  => __( '30' )
                  )
              )
          )
      );

      /*$wp_customize->add_section('streamium_cdn_section' , array(
          'title'     => __('AWS CDN Options', 'streamium'),
          'priority'  => 1020
      ));

      $wp_customize->add_setting('streamium_enable_cloudfront', array(
          'default'    => false
      ));

      $wp_customize->add_control(
          new WP_Customize_Control(
              $wp_customize,
              'streamium_enable_cloudfront',
              array(
                  'label'     => __('Enable Cloudfront urls', 'streamium'),
                  'section'   => 'streamium_cdn_section',
                  'settings'  => 'streamium_enable_cloudfront',
                  'type'      => 'checkbox',
              )
          )
      );

      // allow the user to remove the powered by link
      $wp_customize->add_setting('streamium_enable_cloudfront_url');
      
      $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_remove_powered_by_s3bubble',
        array(
          'label' => 'Cloudfront/S3 url',
          'section' => 'streamium_cdn_section',
          'settings' => 'streamium_enable_cloudfront_url',
        )) 
      );*/
  
   }

   /**
    * This will output the custom WordPress settings to the live theme's WP head.
    * 
    * Used by hook: 'wp_head'
    * 
    * @see add_action('wp_head',$func)
    * @since Streamium 1.0
    */
   public static function header_output() {
      ?>
      <!--Customizer CSS--> 
      <style type="text/css">
           <?php self::generate_css('a', 'color', 'link_textcolor'); ?>
           <?php self::generate_css('a:focus', 'color', 'link_textcolor'); ?>
           <?php self::generate_css('a:hover', 'color', 'link_textcolor'); ?>
           <?php self::generate_css('#place_order', 'background-color', 'link_textcolor'); ?>
           <?php self::generate_css('.pagination a:hover', 'background-color', 'link_textcolor'); ?>
           <?php self::generate_css('.pagination .current', 'background-color', 'link_textcolor'); ?>
           <?php self::generate_css('.slick-dots li.slick-active button', 'background-color', 'link_textcolor'); ?>
           <?php self::generate_css('.progress-bar', 'background-color', 'link_textcolor'); ?>
           <?php self::generate_css('.button', 'background', 'link_textcolor'); ?>
           <?php self::generate_css('.label.heart', 'color', 'link_textcolor'); ?>
           <?php self::generate_css('.progress-bar .progress', 'background', 'link_textcolor'); ?> 
           <?php self::generate_css('.cd-main-header .cd-logo', 'color', 'link_textcolor'); ?>
           <?php self::generate_css('.carousels .tile_play i, .content-overlay .home-slider-play-icon i, .static-row .tile_play i, .s3bubble-details-full .home-slider-play-icon i', 'color', 'link_textcolor'); ?>
           <?php self::generate_css('.cd-primary-nav .cd-secondary-nav a:hover', 'color', 'link_textcolor'); ?>
           <?php self::generate_css('.cd-overlay', 'background-color', 'link_textcolor'); ?>
           <?php self::generate_css('.cd-primary-nav>li>a:hover', 'color', 'link_textcolor'); ?>
           <?php self::generate_css('.cd-primary-nav .cd-nav-gallery .cd-nav-item h3', 'color', 'link_textcolor'); ?>
           <?php self::generate_css('.cd-primary-nav .cd-nav-icons .cd-nav-item h3', 'color', 'link_textcolor'); ?>
           <?php self::generate_css('.has-children > a:hover::before, .has-children > a:hover::after, .go-back a:hover::before, .go-back a:hover::after', 'background', 'link_textcolor'); ?>
           <?php self::generate_css('#submit, #place_order', 'background', 'link_textcolor'); ?>
           <?php self::generate_css('.post-type-archive, .woocommerce-cart, .woocommerce-account, .woocommerce-checkout, .woocommerce-page', 'background-image', 'streamium_plans_bg', 'url(', ')'); ?>
           <?php self::generate_css('.checkout-button, .woocommerce-thankyou-order-received, .add_to_cart_button', 'background', 'link_textcolor','',' !important'); ?>
           <?php self::generate_css('.tile', 'border-color', 'link_textcolor','',' !important'); ?>
           <?php self::generate_css('.woocommerce-message', 'border-top-color', 'link_textcolor','',' !important'); ?>
           <?php self::generate_css('.woocommerce-message:before', 'color', 'link_textcolor','',' !important'); ?>
           
      </style> 
      <!--/Customizer CSS-->
      <?php
   }
   
   /**
    * This outputs the javascript needed to automate the live settings preview.
    * Also keep in mind that this function isn't necessary unless your settings 
    * are using 'transport'=>'postMessage' instead of the default 'transport'
    * => 'refresh'
    * 
    * Used by hook: 'customize_preview_init'
    * 
    * @see add_action('customize_preview_init',$func)
    * @since Streamium 1.0
    */
   public static function live_preview() {
      wp_enqueue_script( 
           'streamium-themecustomizer', // Give the script a unique ID
           get_template_directory_uri() . '/dist/js/theme-customizer.js', // Define the path to the JS file
           array(  'jquery', 'customize-preview' ), // Define dependencies
           '', // Define a version (optional) 
           true // Specify whether to put in footer (leave this true)
      );
   }

    /**
     * This will generate a line of CSS for use in header output. If the setting
     * ($mod_name) has no defined value, the CSS will not be output.
     * 
     * @uses get_theme_mod()
     * @param string $selector CSS selector
     * @param string $style The name of the CSS *property* to modify
     * @param string $mod_name The name of the 'theme_mod' option to fetch
     * @param string $prefix Optional. Anything that needs to be output before the CSS property
     * @param string $postfix Optional. Anything that needs to be output after the CSS property
     * @param bool $echo Optional. Whether to print directly to the page (default: true).
     * @return string Returns a single line of CSS with selectors and a property.
     * @since Streamium 1.0
     */
    public static function generate_css( $selector, $style, $mod_name, $prefix='', $postfix='', $echo=true ) {
      $return = '';
      $mod = get_theme_mod($mod_name);
      if ( ! empty( $mod ) ) {
         $return = sprintf('%s { %s:%s; }',
            $selector,
            $style,
            $prefix.$mod.$postfix
         );
         if ( $echo ) {
            echo $return;
         }
      }
      return $return;
    }
}

// Setup the Theme Customizer settings and controls...
add_action( 'customize_register' , array( 'Streamium_Customize' , 'register' ) );

// Output custom CSS to live site
add_action( 'wp_head' , array( 'Streamium_Customize' , 'header_output' ) );

// Enqueue live preview javascript in Theme Customizer admin screen
add_action( 'customize_preview_init' , array( 'Streamium_Customize' , 'live_preview' ) );

/*
* Adds a main nav menu if needed
* @author sameast
* @none
*/ 
function streamium_register_menu() {
    register_nav_menu('streamium-header-menu',__( 'Header Menu', 'streamium' ));
}
add_action( 'init', 'streamium_register_menu' );

function streamium_remove_ul( $menu ){
    return preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );
}
add_filter( 'wp_nav_menu', 'streamium_remove_ul' );

/**
 * Adds a pagination plugin for multiple videos
 *
 * @return bool
 * @author  @sameast
 */
function streamium_pagination($pages = '', $range = 4){

     $showitems = ($range * 2)+1;  
 
     global $paged;
     if(empty($paged)) $paged = 1;
 
     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   
 
     if(1 != $pages)
     {
         echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";
 
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
 
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}

/**
 * Adds a pagination plugin for multiple videos
 *
 * @return bool
 * @author  @sameast
 */
function streamium_remove_all_recently_watched(){

    $allposts = get_posts( 'numberposts=-1&post_type=post&post_status=any' );
    foreach( $allposts as $postinfo ) {
      delete_post_meta( $postinfo->ID, 'recently_watched' );
      delete_post_meta( $postinfo->ID, 'recently_watched_user_id' );
      $inspiration = get_post_meta( $postinfo->ID, 'post_inspiration' );
    }

}

/**
 * Adds a new user bucket
 *
 * @return bool
 * @author  @sameast
 */
add_action( 'add_meta_boxes', 's3bubble_video_code_meta_box_add' );
function s3bubble_video_code_meta_box_add(){

    add_meta_box( 's3bubble-meta-video-id', 'S3Bubble Video', 's3bubble_meta_video_id', 'post', 'side', 'high' );

}

function s3bubble_meta_video_id(){

    // $post is already set, and contains an object: the WordPress post
    global $post;
    $values = get_post_custom( $post->ID );
    $text = isset( $values['s3bubble_video_code_meta_box_text'] ) ? $values['s3bubble_video_code_meta_box_text'] : '';
    // We'll use this nonce field later on when saving.
    wp_nonce_field( 's3bubble_video_code_meta_box_nonce', 'meta_box_nonce' );
    ?>
    <p>
        <label for="s3bubble_video_code_meta_box_text">Enter full video url</label>
        <input type="text" name="s3bubble_video_code_meta_box_text" id="s3bubble_video_code_meta_box_text" class="large-text" value="<?php echo $text[0]; ?>" />
    </p>

    <?php    

}

add_action( 'save_post', 's3bubble_video_code_meta_box_save' );
function s3bubble_video_code_meta_box_save( $post_id )
{
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 's3bubble_video_code_meta_box_nonce' ) ) return;
     
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;
     
    // Make sure your data is set before trying to save it
    if( isset( $_POST['s3bubble_video_code_meta_box_text'] ) ){

      if (strpos($_POST['s3bubble_video_code_meta_box_text'],'s3bubble') !== false) {
        update_post_meta( $post_id, 's3bubble_video_code_meta_box_text', $_POST['s3bubble_video_code_meta_box_text'] );
      }

    }
        
}


/**
 * Adds a new user bucket
 *
 * @return bool
 * @author  @sameast
 */
function streamium_user_reviews_callback(){

    global $withcomments;
    $post_id = $_POST['pid'];
    $comments = get_comments(array( 'post_id' =>  $post_id ));
    if ( $comments )
    {
        $comms = "<ul>";
        foreach ( $comments as $comment )
        {
             $comms .= '<li class="cd-testimonials-item">
                <p>' . $comment->comment_content . '</p>
                <div class="cd-author">
                  ' . get_avatar( $comment, 32 ) . '
                  <ul class="cd-author-info">
                    <li>' . $comment->comment_author . '</li>
                    <li>' . get_post_field('post_title', $post_id) . '</li>
                  </ul>
                </div>
              </li>';
        }
        $comms .= "</ul>";
    }

    echo $comms;
  
    wp_die(); // this is required to return a proper result
}

add_action('wp_ajax_streamium_user_reviews', 'streamium_user_reviews_callback');
add_action( 'wp_ajax_nopriv_streamium_user_reviews', 'streamium_user_reviews_callback' );


add_filter('show_admin_bar', '__return_false');