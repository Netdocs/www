<?php

//Main Plugin Class

class Interactive_Map {

    static $instance;

    //Constructor of the Class
    public function __construct() {

        self::$instance = $this;

        add_action('admin_menu', array($this, 'wp_interactive_map_menu'));
        add_action('admin_init', array($this, 'wp_interactive_map_settings'));
        add_shortcode('interactive-map', array($this, 'interactive_map_func'));

        add_action('admin_enqueue_scripts', array(
            $this,
            'Interactive_Map_Admin_Scripts'
        ));

        add_action('wp_enqueue_scripts', array(
            $this,
            'Interactive_Map_Frontend_Scripts'
        ));
    }

    public function wp_interactive_map_menu() {
        add_plugins_page('Interactive Map Settings', 'Interactive Map Settings', 'manage_options', 'interactive-map-settings', array($this, 'load_map_settings_page'), '', 85);
    }

    public function load_map_settings_page() {
        if (file_exists(plugin_dir_path(__DIR__) . '/views/interactive-map-settings.php')) {
            require plugin_dir_path(__DIR__) . '/views/interactive-map-settings.php';
        } else {
            die('<br /><h3>Plugin Installation is Incomplete. Please install the plugin again or make sure you have copied all the plugin files.</h3>');
        }
    }

    /* Function to include scripts necessary for the plugin.
     * Scripts are saved in the JS folder of the plugin.
     */

    public function Interactive_Map_Admin_Scripts() {

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('map-color-picker', plugins_url('js/interactive-admin-map.js', __DIR__), array(), '1.0.0', true);
    }

    public function Interactive_Map_Frontend_Scripts() {

        wp_enqueue_style('interactive-main-style', plugins_url('css/jqvmap.css', __DIR__));
        wp_enqueue_style('interactive-map-style', plugins_url('css/style.css', __DIR__));
        wp_enqueue_script('vmap-js-handler', plugins_url('js/jquery.vmap.js', __DIR__), array(), '1.0.0', true);
        wp_enqueue_script('vmap-worldmap-handler', plugins_url('js/maps/jquery.vmap.' . get_option('map_type') . '.js', __DIR__), array(), '1.0.0', true);
        wp_localize_script('vmap-worldmap-handler', 'interactivemap', array(
            'map' => get_option('map_type'),
            'map_color' => get_option('map_color'),
            'map_background_color' => get_option('map_background_color'),
            'map_border_color' => get_option('map_border_color'),
            'map_zoom' => get_option('map_zoom'),
            'map_region_hover_color' => get_option('map_region_hover_color'),
            'map_labels' => get_option('map_labels'),
            'map_tooltip' => get_option('map_tooltip'),
            'map_border_width' => get_option('map_border_width'),
            'map_border_opacity' => get_option('map_border_opacity'),
            'map_selected_color' => get_option('map_selected_color'),
            'map_multiselect' => get_option('map_multiselect'),
        ));
        wp_enqueue_script('map-frontend-handler', plugins_url('js/interactive-map.js', __DIR__), array(), '1.0.0', true);
    }

    public function wp_interactive_map_settings() {

        register_setting('interactive-map-settings-group', 'map_multiselect');
        register_setting('interactive-map-settings-group', 'map_selected_color');
        register_setting('interactive-map-settings-group', 'map_border_opacity');
        register_setting('interactive-map-settings-group', 'map_border_width');
        register_setting('interactive-map-settings-group', 'map_width');
        register_setting('interactive-map-settings-group', 'map_height');
        register_setting('interactive-map-settings-group', 'map_type');
        register_setting('interactive-map-settings-group', 'map_color');
        register_setting('interactive-map-settings-group', 'map_background_color');
        register_setting('interactive-map-settings-group', 'map_border_color');
        register_setting('interactive-map-settings-group', 'map_zoom');
        register_setting('interactive-map-settings-group', 'map_region_hover_color');
        register_setting('interactive-map-settings-group', 'map_labels');
        register_setting('interactive-map-settings-group', 'map_tooltip');
    }

    public function interactive_map_func($atts) {
        return '<div id=vmap style="width: ' . get_option('map_width') . 'px; height: ' . get_option('map_height') . 'px;"></div>';
    }

}

?>