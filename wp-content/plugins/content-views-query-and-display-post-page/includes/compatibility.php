<?php
/**
 * Resolved conflict with other plugins
 *
 * @package   PT_Content_Views
 * @author    PT Guy <http://www.contentviewspro.com/>
 * @license   GPL-2.0+
 * @link      http://www.contentviewspro.com/
 * @copyright 2016 PT Guy
 */
/**
 * Autoptimize
 * Disable "Force JavaScript in <head>"
 *
 * @since 1.8.6
 */
add_filter( 'autoptimize_filter_js_defer', 'cv_comp_plugin_autoptimize', 10, 1 );
function cv_comp_plugin_autoptimize( $defer ) {
	$defer = "defer ";
	return $defer;
}

/**
 * Page Builder by SiteOrigin
 * Excerpt is incorrect (not updated)
 * @update 1.9.9 Apply the "the_content" to work with any verion of that plugin
 * @since 1.8.8
 */
add_filter( 'pt_cv_field_content_excerpt', 'cv_comp_plugin_siteoriginbuilder', 9, 3 );
function cv_comp_plugin_siteoriginbuilder( $args, $fargs, $this_post ) {

	if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
		if ( !isset( $this_post->cv_so_content ) ) {
			$this_post->cv_so_content = apply_filters( 'the_content', $args );
		}

		$args = $this_post->cv_so_content;
	}

	return $args;
}

/**
 * FacetWP
 * Missing posts in output when access page with parameters 'fwp_*' of FacetWP plugin
 *
 * @since 1.9.3
 */
add_filter( 'facetwp_is_main_query', 'cv_comp_plugin_facetwp', 999, 2 );
function cv_comp_plugin_facetwp( $is_main_query, $query ) {
	if ( $query->get( 'cv_get_view' ) || $query->get( 'by_contentviews' ) ) {
		$is_main_query = false;
	}

	return $is_main_query;
}

# "View maybe not exist" error, caused by custom filter hook (which modifies `post_type` in WordPress query) of another plugin
add_action( 'pre_get_posts', 'cv_comp_no_view_found', 999 );
function cv_comp_no_view_found( $query ) {
	if ( $query->get( 'cv_get_view' ) ) {
		$query->set( 'post_type', PT_CV_POST_TYPE );
	}

	return $query;
}

/**
 * Divi theme
 * Remove line break holder of Divi theme from excerpt
 *
 * @since 1.9.5
 */
add_filter( 'pt_cv_before_generate_excerpt', 'cv_comp_theme_divi_linebreak' );
function cv_comp_theme_divi_linebreak( $args ) {
	if ( defined( 'ET_CORE_VERSION' ) ) {
		$args = str_replace( array( '<!-- [et_pb_line_break_holder] -->', '&lt;!-- [et_pb_line_break_holder] --&gt;' ), '', $args );
	}

	return $args;
}

/**
 * Divi theme
 * Collapsible doesn't toggle on click heading, Scrollable doesn't slide on click next/prev button
 *
 * @since 1.9.7.1
 */
add_filter( PT_CV_PREFIX_ . 'wrapper_class', 'cv_comp_theme_divi_scroll' );
function cv_comp_theme_divi_scroll( $args ) {
	if ( defined( 'ET_CORE_VERSION' ) ) {
		$args .= ' ' . 'et_smooth_scroll_disabled';
	}
	return $args;
}

/**
 * Visual Composer
 * Shortcode is visible in content, when do Ajax pagination
 *
 * @since 1.9.6
 */
add_action( 'pt_cv_before_content', 'cv_comp_plugin_visualcomposer', 9 );
function cv_comp_plugin_visualcomposer() {
	if ( (defined( 'PT_CV_DOING_PAGINATION' ) || defined( 'PT_CV_DOING_PREVIEW' )) && class_exists( 'WPBMap' ) && method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
		WPBMap::addAllMappedShortcodes();
	}
}

// Fix: Sort by doesn't work
add_action( 'pre_get_posts', 'cv_comp_wrong_sortby', 9 );
function cv_comp_wrong_sortby( $query ) {
	if ( $query->get( 'by_contentviews' ) ) {
		/**
		 * "Post Types Order" plugin
		 * @since 1.9.6
		 */
		$query->set( 'ignore_custom_sort', true );

		/**
		 * "Simple Custom Post Order" plugin
		 * @since 1.9.8
		 */
		if ( defined( 'SCPORDER_URL' ) ) {
			add_filter( 'option_scporder_options', '__return_false', 10, 2 );
		}
	}

	return $query;
}

/**
 * OptimizePress plugin
 * Content Views style & script were not loaded in page created by OptimizePress plugin
 * @since 1.9.8
 */
if ( function_exists( 'opRemoveScripts' ) ) {
	remove_action( 'wp_print_scripts', 'opRemoveScripts', 10 );
}
if ( function_exists( 'opRemoveStyles' ) ) {
	remove_action( 'wp_print_styles', 'opRemoveStyles', 10 );
}

