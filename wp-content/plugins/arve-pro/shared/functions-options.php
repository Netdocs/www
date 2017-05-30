<?php

function arve_pro_get_min_suffix() {
	return ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
}

function arve_pro_get_options() {

	$options  = get_option( 'arve_options_pro', array() );
	$defaults = arve_pro_get_options_defaults();

	return wp_parse_args( $options, $defaults );
}

function arve_pro_get_options_defaults() {

	return array(
		#'key_status'            => null,
		#'key'                   => '',
		'thumbnail_fallback'    => '',
		'transient_expire_time' => 86400, # 1 day
		'play_icon_style'       => 'youtube',
		'disable_links'         => false,
		'grow'                  => true,
		'hide_title'            => false,
		'hover_effect'          => 'zoom',
		'inview_lazyload'       => true,
	);
}

function arve_pro_get_settings_definitions() {

	$options = arve_pro_get_options();

	$properties = array(
		array(
			'attr'          => 'thumbnail_fallback',
			'hide_from_sc'  => true,
			'label'         => __( 'Thumbnail Fallback', ARVE_SLUG ),
			'type'          => 'text',
			'meta'          => array(
				'placeholder' => __( 'URL or media gallery image ID used for thumbnail', ARVE_SLUG )
			)
		),
		array(
			'attr'        => 'hide_title',
			'label'       => esc_html__('Hide Title', ARVE_SLUG ),
			'type'        => 'bool',
			'description' => esc_html__( 'Usefull when the thumbnail image already displays the video title (Lazyload mode). The title will still be used for SEO.' ),
		),
		array(
			'attr'        => 'grow',
			'label'       => __('Grow on Play', ARVE_SLUG ),
			'type'        => 'bool',
			'description' => __('Grow video to container element width on play? (Lazyload Mode)', ARVE_SLUG),
		),
		array(
			'attr'    => 'play_icon_style',
			'label'   => __('Play Button', ARVE_SLUG ),
			'type'    => 'select',
			'options' => array(
				''        => sprintf( esc_html__( 'Default (current setting: %s)', 'arve-pro' ), $options['play_icon_style'] ),
				'youtube' => __( 'Youtube style', ARVE_SLUG ),
				'circle'  => __( 'Circle',        ARVE_SLUG ),
				'none'    => __( 'No play image', ARVE_SLUG )
			),
		),
		array(
			'attr'          => 'hover_effect',
			'hide_from_sc'  => true,
			'label'         => __( 'Hover Effect', ARVE_SLUG ),
			'type'          => 'select',
			'options'       => array(
				'zoom'      => __( 'Zoom Thumbnail', ARVE_SLUG ),
				'rectangle' => __( 'Move Rectangle in', ARVE_SLUG ),
				'none'      => __( 'None', ARVE_SLUG ),
			),
		),
		array(
			'attr'         => 'disable_links',
			'hide_from_sc' => true,
			'label'        => esc_html__('Disable links', ARVE_SLUG),
			'type'         => 'bool',
			'description'  => __( 'Prevent ARVE embeds to open new popups/tabs/windows from links inside video embeds. Note this also breaks all kinds of sharing functionality and the like. (Pro Addon)' ),
		),
		array(
			'attr'         => 'transient_expire_time',
			'hide_from_sc' => true,
			'label'        => __( 'External Image Cache Time', ARVE_SLUG ),
			'type'         => 'number',
			'description'  => __( '(seconds) This plugin uses WordPress transients to cache video thumbnail URLS that greatly speeds up Page loading. This setting defines how long external image URLs are beeing stored without contacting the hosts APIs again. For example: hour - 3600, day - 86400, week - 604800.', ARVE_SLUG ),
		),
		array(
			'attr'         => 'inview_lazyload',
			'hide_from_sc' => true,
			'label'        => __( 'Inview Lazyload', ARVE_SLUG ),
			'type'         => 'bool',
			'description'  => __( 'The inview lazyload mode videos as they come into the screen as a workarround for the problem that it otherwise needs two touches to play a lazyloaded video because mobile browsers prevent autoplay. Note that this will prevent users to see your custom thumbnails or titles!', ARVE_SLUG ),
		),
	);

	$options = arve_pro_get_options();

	foreach ( $properties as $key => $value ) {

		if( 'bool' == $value['type'] ) {

			$properties[ $key ]['type']          = 'select';
			$properties[ $key ]['sanitise_func'] = 'boolval';
			$properties[ $key ]['options']       = array(
				''    => sprintf( __( 'Default (current setting: %s)', ARVE_SLUG ), $options[ $value['attr'] ] ? __( 'Yes', ARVE_SLUG ) : __( 'No', ARVE_SLUG ) ),
				'yes' => __( 'Yes', ARVE_SLUG ),
				'no'  => __( 'No',  ARVE_SLUG ),
			);
		}
	}

	return $properties;
}


function arve_get_array_key_by_value( $array, $field, $value ) {

	 foreach( $array as $key => $array_value ) {

			if ( $array_value[ $field ] === $value ) {
				return $key;
			}
   }

	 return false;
}

function NEWarve_get_default_option( $option ) {

	$props = arve_pro_get_settings_definitions();

	return $props[ $option ]['default'];
}

function NEWarve_validate_select_option( $option, $new_value, $old_value ) {

	$props = arve_pro_get_settings_definitions();

	if ( array_key_exists( $new_value, $props[ $option ]['options'] ) ) {
		return $new_value;
	} elseif ( array_key_exists( $old_value, $props[ $option ]['options'] ) ) {
		return $old_value;
	} else {
		return $props[ $option ]['default'];
	}
}

/*
add_filter( 'pre_update_option_foo', function ( $new_value, $old_value ) use ( $option ) {

	$props = arve_pro_get_settings_definitions();

	if ( array_key_exists( $new_value, $props[ $option ]['options'] ) ) {
		return $new_value;
	} elseif ( array_key_exists( $old_value, $props[ $option ]['options'] ) ) {
		return $old_value;
	} else {
		return $props[ $option ]['default'];
	}

}, 10, 2 );
*/
