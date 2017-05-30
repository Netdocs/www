<?php

/**
 * Add option to the WordPress theme customizer
 *
 * @return null
 * @author  @sameast
 */
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

      // Remove options
      $wp_customize->remove_section('colors');

      // STREAMIUM STYLES SECTION
      $wp_customize->add_section( 'streamium_styles', array(
          'title'       => __( 'Streamium Styles', 'streamium' ),
          'priority'    => 30,
          'description' => 'Here you can set the Streamium styles',
      ) );

      // Remove tutorial block
      $wp_customize->add_setting('tutorial_btn', array(
          'default' => false
      ));
      $wp_customize->add_control(
          new WP_Customize_Control(
              $wp_customize,
              'tutorial_btn',
              array(
                  'label'     => __('Remove tutorial header', 'streamium'),
                  'section'   => 'streamium_styles',
                  'settings'  => 'tutorial_btn',
                  'type'      => 'checkbox',
              )
          )
      );

      // Logo block
      $wp_customize->add_setting( 'streamium_logo' );

      $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'streamium_logo', array(
          'label'    => __( 'Logo', 'streamium' ),
          'section'  => 'streamium_styles',
          'settings' => 'streamium_logo',
      ) ) );

      // Full background block
      $wp_customize->add_setting( 'streamium_global_bg' );

      $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'streamium_global_bg', array(
          'label'    => __( 'Global Background', 'streamium' ),
          'section'  => 'streamium_styles',
          'settings' => 'streamium_global_bg',
      ) ) );
      
      // Full background block
      $wp_customize->add_setting( 'streamium_plans_bg' );

      $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'streamium_plans_bg', array(
          'label'    => __( 'Background', 'streamium' ),
          'section'  => 'streamium_styles',
          'settings' => 'streamium_plans_bg',
      ) ) );

      // Link text color
      $wp_customize->add_setting(
          'link_textcolor',
          array(
              'default'     => '#F66E38',
              'sanitize_callback' => 'sanitize_hex_color'
          )
      );
   
      $wp_customize->add_control(
          new WP_Customize_Color_Control(
              $wp_customize,
              'link_textcolor',
              array(
                  'label'      => __( 'Main Link Color', 'streamium' ),
                  'section'    => 'streamium_styles',
                  'settings'   => 'link_textcolor'
              ) 
          )
      );

      // Caro heading text color
      $wp_customize->add_setting(
          'streamium_carousel_heading_color',
          array(
              'default'     => '#f2f2f2',
              'sanitize_callback' => 'sanitize_hex_color'
          )
      );
   
      $wp_customize->add_control(
          new WP_Customize_Color_Control(
              $wp_customize,
              'streamium_carousel_heading_color',
              array(
                  'label'      => __( 'Carousel Headings Color', 'streamium' ),
                  'section'    => 'streamium_styles',
                  'settings'   => 'streamium_carousel_heading_color'
              ) 
          )
      );
      
      // Main background color
      $wp_customize->add_setting(
          'streamium_background_color',
          array(
              'default'     => '#141414',
              'sanitize_callback' => 'sanitize_hex_color'
          )
      );
   
      $wp_customize->add_control(
          new WP_Customize_Color_Control(
              $wp_customize,
              'streamium_background_color',
              array(
                  'label'      => __( 'Background Color', 'streamium' ),
                  'section'    => 'streamium_styles',
                  'settings'   => 'streamium_background_color'
              ) 
          )
      );

      // Use google font
      $wp_customize->add_setting('streamium_google_font');
      
      $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_google_font',
        array(
          'label' => 'Google Font Url',
          'description' => 'Please only enter the full google font url<br>Leave blank to use "Helvetica Neue"<br><a href="https://fonts.google.com/" target="_blank">https://fonts.google.com</a>',
          'section' => 'streamium_styles',
          'settings' => 'streamium_google_font',
        )) 
      );

      $wp_customize->add_setting('streamium_synopsis_para', array(
          'default'  => '1.3vw'
      ));

      $wp_customize->add_control(
          new WP_Customize_Control(
              $wp_customize,
              'streamium_synopsis_para',
              array(
                  'label'     => __('Synopsis Paragraph Text', 'streamium'),
                  'section'   => 'streamium_styles',
                  'settings'  => 'streamium_synopsis_para',
                  'type'      => 'select',
                  'choices' => array( 
                      '1.6vw'  => __( '1.6vw' ),
                      '1.5vw'  => __( '1.5vw' ),
                      '1.4vw'  => __( '1.4vw' ),
                      '1.3vw'  => __( '1.3vw' ),
                      '1.2vw'  => __( '1.2vw' ),
                      '1.1vw'  => __( '1.1vw' ),
                      '1vw'    => __( '1vw' ),
                      '0.9.vw' => __( '0.9vw' ),
                      '0.8.vw' => __( '0.8vw' )
                  )
              )
          )
      );          
      

      // SITE IDENTITY SECTION
      $wp_customize->add_setting( 'streamium_main_post_type', array(
        'default' => 'movie',
        'sanitize_callback' => 'streamium_sanitize_customizer_main_tax',
      ) );

      function streamium_sanitize_customizer_main_tax( $value ) {

          switch ($value) {
            case 'movie':
              set_theme_mod( "streamium_main_tax", "movies" );
              break;
            case 'tv':
              set_theme_mod( "streamium_main_tax", "programs" );
              break;
            case 'sport':
              set_theme_mod( "streamium_main_tax", "sports" );
              break;
            case 'kid':
              set_theme_mod( "streamium_main_tax", "kids" );
              break;
            case 'stream':
              set_theme_mod( "streamium_main_tax", "streams" );
              break;
          }

          return $value;

      }

      $wp_customize->add_control( 'streamium_main_post_type', array(
        'type' => 'radio',
        'section' => 'title_tagline', // Add a default or your own section
        'label' => __( 'Select the main post type to show on the homepage' ),
        'description' => __( 'This is a custom radio input.' ),
        'choices' => array(
          'movie' => __( 'Movies' ),
          'tv' => __( 'TV Programs' ),
          'sport' => __( 'Sports' ),
          'kid' => __( 'Kids' ),
          'stream' => __( 'Streams' )
        ),
      ) );

      $wp_customize->add_setting('streamium_remove_powered_by_s3bubble');
      
      $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_remove_powered_by_s3bubble',
        array(
          'label' => 'Replace Powered By S3Bubble Text',
          'section' => 'title_tagline',
          'settings' => 'streamium_remove_powered_by_s3bubble',
        )) 
      );

      $wp_customize->add_setting('streamium_genre_text');

      $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_genre_text',
        array(
          'label' => 'Replace Genre Text',
          'section' => 'title_tagline',
          'settings' => 'streamium_genre_text',
        )) 
      );

      $wp_customize->remove_control('display_header_text');

      $wp_customize->add_setting('streamium_global_options_homepage_desktop', array(
          'default'  => '-1'
      ));

      $wp_customize->add_control(
          new WP_Customize_Control(
              $wp_customize,
              'streamium_global_options_homepage_desktop',
              array(
                  'label'     => __('Maximum carousel videos - Desktop', 'streamium'),
                  'section'   => 'title_tagline',
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

      // AWS MEDIA SECTION
      $wp_customize->add_section('streamium_aws_media_uploader_section' , array(
          'title'     => __('AWS Media Uploader', 'streamium'),
          'description' => 'For infomation on how to setup the uploader with S3Bubble please watch this video<br><a href="https://www.youtube.com/watch?v=FUqN-b1MSrc" target="_blank">AWS direct uploader setup</a>',
          'priority'  => 1020
      ));

      $wp_customize->add_setting('streamium_aws_media_uploader_access_key');
      
      $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_aws_media_uploader_access_key',
        array(
          'label' => 'AWS Access Key',
          'section' => 'streamium_aws_media_uploader_section',
          'settings' => 'streamium_aws_media_uploader_access_key',
          'type' => 'password',
          'input_attrs' => array( 'id' => 'streamium_aws_media_uploader_access_key' )
        )) 
      );

      $wp_customize->add_setting('streamium_aws_media_uploader_secret_key');

      $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_aws_media_uploader_secret_key',
        array(
          'label' => 'AWS Secret Key',
          'section' => 'streamium_aws_media_uploader_section',
          'settings' => 'streamium_aws_media_uploader_secret_key',
          'type' => 'password',
          'input_attrs' => array( 'id' => 'streamium_aws_media_uploader_secret_key' )
        )) 
      );

      $wp_customize->add_setting('streamium_aws_media_uploader_notification_email');

      $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_aws_media_uploader_notification_email',
        array(
          'label' => 'Notification Email',
          'section' => 'streamium_aws_media_uploader_section',
          'settings' => 'streamium_aws_media_uploader_notification_email',
        )) 
      );

        $postTypes = array(
            array('tax' => 'movies','type' => 'movie','menu' => 'Movies'),
            array('tax' => 'programs','type' => 'tv','menu' => 'TV Programs'),
            array('tax' => 'sports','type' => 'sport','menu' => 'Sport'),
            array('tax' => 'kids','type' => 'kid','menu' => 'Kids'),
            array('tax' => 'streams','type' => 'stream','menu' => 'Streams')
        );

        foreach ($postTypes as $key => $value) :

            $tax = $value['tax'];
            $type = $value['type'];
            $menu = $value['menu'];

            // MOVIE SECTION
            $wp_customize->add_section('streamium_section_' . $type , array(
                'title'     => __( $menu . ' Options', 'streamium'),
                'description' => 'For infomation on how to setup the uploader with S3Bubble please watch this video<br><a href="https://www.youtube.com/watch?v=FUqN-b1MSrc" target="_blank">AWS direct uploader setup</a>',
                'priority'  => 1019
            ));

            $wp_customize->add_setting('streamium_section_input_menu_text_' . $type, array(
                'default'    => $menu
            ));

            $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_section_input_menu_text_' . $type,
              array(
                'label' => 'Change Top Menu Text',
                'section' => 'streamium_section_' . $type,
                'settings' => 'streamium_section_input_menu_text_' . $type
              )) 
            );

            $wp_customize->add_setting('streamium_section_input_taxonomy_' . $tax, array(
                'default'    => $tax,
                'sanitize_callback' => 'streamium_sanitize_customizer_text',
            ));

            $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_section_input_taxonomy_' . $tax,
              array(
                'label' => 'Change Taxonomy',
                'section' => 'streamium_section_' . $type,
                'settings' => 'streamium_section_input_taxonomy_' . $tax
              )) 
            );

            $wp_customize->add_setting('streamium_section_input_posttype_' . $type, array(
                'default'    => $type,
                'sanitize_callback' => 'streamium_sanitize_customizer_text',
            ));

            $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'streamium_section_input_posttype_' . $type,
              array(
                'label' => 'Change Post Type',
                'section' => 'streamium_section_' . $type,
                'settings' => 'streamium_section_input_posttype_' . $type
              )) 
            );

            $wp_customize->add_setting('streamium_section_checkbox_enable_' . $tax, array(
                'default'    => false
            ));

            $wp_customize->add_control(
                new WP_Customize_Control(
                    $wp_customize,
                    'streamium_section_checkbox_enable_' . $tax,
                    array(
                        'label'     => __('Enable ' . $menu, 'streamium'),
                        'section'   => 'streamium_section_' . $type,
                        'settings'  => 'streamium_section_checkbox_enable_' . $tax,
                        'type'      => 'checkbox',
                    )
                )
            );

        endforeach;

      // PREMIUM SECTION
      $wp_customize->add_section('streamium_premium_section' , array(
          'title'     => __('Premium Options', 'streamium'),
          'priority'  => 1021
      ));

      /*$wp_customize->add_setting('streamium_enable_tv', array(
          'default'    => false
      ));

      $wp_customize->add_control(
          new WP_Customize_Control(
              $wp_customize,
              'streamium_enable_tv',
              array(
                  'label'     => __('Enable TV Programs', 'streamium'),
                  'section'   => 'streamium_premium_section',
                  'settings'  => 'streamium_enable_tv',
                  'type'      => 'checkbox',
              )
          )
      );*/

      function streamium_sanitize_customizer_text( $value ) {
          
          if($value != ""){
            
            return strtolower(sanitize_text_field($value));
            
          }
      
      }
  
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

      $url = get_theme_mod( 'streamium_google_font' );
      $family = 'Helvetica';
      if(!empty($url)){

        $parts = parse_url($url);
        if(isset($parts['query'])){
            parse_str($parts['query'], $query);
            $family = isset($query['family']) ? $query['family'] : "";
            if (strpos($family, ':') !== false) {
                $family = substr($family, 0, strrpos($family, ':'));
            }
        }

      }

      ?>
      <!--Customizer CSS--> 
      <style type="text/css">

            <?php if(get_theme_mod( 'streamium_google_font' )){ ?>
              @import url('<?php echo $url; ?>');
              .h1, .h2, .h3, h1, h2, h3, h4, .cd-logo {
                font-family: '<?php echo $family; ?>', sans-serif !important;
              }
           <?php } ?>
           /* Theme colors */
           <?php self::generate_css('.archive, .home, .search, .single', 'background-color', 'streamium_background_color','',' !important'); ?>
           <?php self::generate_css('.video-header h3, .see-all', 'color', 'streamium_carousel_heading_color','',' !important'); ?>

           /* link and background colors */
           <?php self::generate_css('.page a, a:focus, a:hover, .cd-main-header .cd-logo, .play-icon-wrap i, .cd-primary-nav .cd-secondary-nav a:hover, .cd-primary-nav>li>a:hover, .cd-primary-nav .cd-nav-gallery .cd-nav-item h3, .cd-primary-nav .cd-nav-icons .cd-nav-item h3, .woocommerce-message:before, .woocommerce-info::before', 'color', 'link_textcolor'); ?>

           <?php self::generate_css('#place_order, .pagination a:hover, .pagination .current, .slick-dots li.slick-active button, .progress-bar, .button, .cd-overlay, .has-children > a:hover::before, .has-children > a:hover::after, .go-back a:hover::before, .go-back a:hover::after, #submit, #place_order, .checkout-button, .woocommerce-thankyou-order-received, .add_to_cart_button, .confirm, .streamium-btns, .streamium-extra-meta', 'background-color', 'link_textcolor','',' !important'); ?>
           <?php if(streamium_get_device('device') == 'desktop') : 
              self::generate_css('.synopis-inner .content', 'font-size', 'streamium_synopsis_para','',' !important'); 
            endif;
           ?>

           <?php self::generate_css('.post-type-archive, .woocommerce-cart, .woocommerce-account, .woocommerce-checkout, .woocommerce-page', 'background-image', 'streamium_plans_bg', 'url(', ')'); ?>
           <?php self::generate_css('body', 'background-image', 'streamium_global_bg', 'url(', ') !important'); ?>

           <?php self::generate_css('.tile', 'border-color', 'link_textcolor','',' !important'); ?>
           <?php self::generate_css('.woocommerce-message, .woocommerce-info', 'border-top-color', 'link_textcolor','',' !important'); ?>
 
           /* Override media player brand colors */
           <?php self::generate_css('button.vjs-play-control.vjs-control.vjs-button.vjs-paused, .vjs-play-progress, .vjs-volume-level', 'background-color', 'link_textcolor','',' !important'); ?>
           
           .streamium-list-reviews { background: #000 !important;}
          
      </style> 
      <!--/Customizer CSS-->
      <?php
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