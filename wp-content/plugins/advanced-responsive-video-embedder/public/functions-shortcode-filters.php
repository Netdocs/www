<?php

function arve_sc_filter_attr( $a ) {

	$align_class = empty( $a['align'] ) ? '' : ' align' . $a['align'];

	foreach ( array( 'id', 'mp4', 'm4v', 'webm', 'ogv', 'url', 'webtorrent' ) as $att ) {

		if ( ! empty( $a[ $att ] ) && is_string( $a[ $att ] ) ) {

			$wrapper_id = preg_replace( '/[^-a-zA-Z0-9]+/', '', $a[ $att ] );
			$wrapper_id = str_replace(
				array( 'https', 'http', 'wp-contentuploads' ),
				'',
				$wrapper_id
			);
			$wrapper_id = 'video-' . $wrapper_id;
			break;
		}
	}

	if ( empty( $wrapper_id ) ) {
		$a['wrapper_id_error'] = new WP_Error( 'embed_id', __( 'Element ID could not be build, please report this bug.', ARVE_SLUG ) );
	}

	$a['wrapper_attr'] = array(
		'class'         => "arve-wrapper$align_class",
		'data-mode'     => $a['mode'],
		'data-provider' => $a['provider'],
		'id'            => $wrapper_id,
		'style'         => empty( $a['maxwidth'] ) ? false : sprintf( 'max-width:%dpx;', $a['maxwidth'] ),
		// Schema.org
		'itemscope' => '',
		'itemtype'  => 'http://schema.org/VideoObject',
	);

	if( 'html5' == $a['provider'] ) {

		$a['video_attr'] = array(
			'autoplay'    => in_array( $a['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ) ) ? false : $a['autoplay'],
			'class'       => 'arve-video fitvidsignore',
			'controls'    => $a['controls'],
			'loop'        => $a['loop'],
			'poster'      => isset( $a['img_src'] ) ? $a['img_src'] : false,
			'preload'     => $a['preload'],
			'src'         => isset( $a['video_src'] ) ? $a['video_src'] : false,
			'muted'       => $a['muted'],
			'width'       => ! empty( $a['width'] ) ? $a['width'] : false,
			'height'      => ! empty( $a['height'] ) ? $a['height'] : false,
			'playsinline' => $a['playsinline'],
			'webkit-playsinline' => $a['playsinline'],
		);

	} else {

		$properties = arve_get_host_properties();

		$iframe_src = arve_build_iframe_src( $a );
		$iframe_src = arve_add_query_args_to_iframe_src( $iframe_src, $a );
		$iframe_src = arve_add_autoplay_query_arg( $iframe_src, $a );

		if ( 'vimeo' == $a['provider'] && ! empty( $a['start'] ) ) {
			$iframe_src .= '#t=' . (int) $a['start'];
		}

		$iframe_sandbox = 'allow-scripts allow-same-origin allow-popups';

		if ( 'vimeo' == $a['provider'] ) {
			$iframe_sandbox .= ' allow-forms';
		}

		if ( null === $a['disable_flash'] && $properties[ $a['provider'] ]['requires_flash'] ) {
			$iframe_sandbox = false;
		}

		$a['iframe_attr'] = array(
			'allowfullscreen' => '',
			'class'       => 'arve-iframe fitvidsignore',
			'frameborder' => '0',
			'name'        => $a['iframe_name'],
			'scrolling'   => 'no',
			'src'         => $iframe_src,
			'sandbox'     => $iframe_sandbox,
			'width'       => ! empty( $a['width'] )  ? $a['width']  : false,
			'height'      => ! empty( $a['height'] ) ? $a['height'] : false,
		);
	}

	return $a;
}

function arve_sc_filter_validate( $atts ) {

	if ( ! empty( $atts['url'] ) && ! arve_validate_url( $atts['url'] ) ) {
		$atts['url'] = new WP_Error( 'thumbnail', sprintf( __( '<code>%s</code> is not a valid url', ARVE_SLUG ), esc_html( $atts['url'] ) ) );
	}

	$atts['align'] = arve_validate_align( $atts['align'], $atts['provider'] );

	$atts['mode'] = arve_validate_mode( $atts['mode'], $atts['provider'] );

	$atts['autoplay']      = arve_validate_bool( $atts['autoplay'], 'autoplay' );
	$atts['arve_link']     = arve_validate_bool( $atts['arve_link'], 'arve_link' );
	$atts['loop']          = arve_validate_bool( $atts['loop'], 'loop' );
	$atts['controls']      = arve_validate_bool( $atts['controls'], 'controls' );
	$atts['disable_flash'] = arve_validate_bool( $atts['disable_flash'], 'disable_flash' );
	$atts['muted']         = arve_validate_bool( $atts['muted'], 'muted' );
	$atts['playsinline']   = arve_validate_bool( $atts['playsinline'], 'playsinline' );

	$atts['maxwidth']  = (int) $atts['maxwidth'];
	$atts['maxwidth']  = (int) arve_maxwidth_when_aligned( $atts['maxwidth'], $atts['align'] );

	$atts['id'] = arve_id_fixes( $atts['id'], $atts['provider'] );

	$atts['aspect_ratio'] = arve_get_default_aspect_ratio( $atts['aspect_ratio'], $atts['provider'] );
	$atts['aspect_ratio'] = arve_aspect_ratio_fixes( $atts['aspect_ratio'], $atts['provider'], $atts['mode'] );
	$atts['aspect_ratio'] = arve_validate_aspect_ratio( $atts['aspect_ratio'] );

	return $atts;
}

function arve_sc_filter_set_fixed_dimensions( $atts ) {

	$width = 480;

	$atts['width']  = $width;
	$atts['height'] = arve_calculate_height( $width, $atts['aspect_ratio'] );

	return $atts;
}

function arve_sc_filter_sanitise( $atts ) {

	if ( ! empty( $atts['src'] ) ) {
		$atts['url'] = $atts['src'];
	}

	foreach ( $atts as $key => $value ) {

		if ( null === $value ) {
			continue;
		}

		if( ! is_string( $value ) ) {
			$atts[ $key ] = arve_error( sprintf( __( '<code>%s</code> is not a string. Only Strings should be passed to the shortcode function', ARVE_SLUG ), $key ) );
		}
	}

	return $atts;
}

function arve_sc_filter_missing_attribute_check( $atts ) {

	# Old shortcodes
	if ( ! array_key_exists( 'url' , $atts ) ) {
		return $atts;
	}

	$required_attributes   = arve_get_html5_attributes();
	$required_attributes[] = 'url';

	$array = array_intersect_key( $atts, array_flip( $required_attributes ) );

	if( count( array_filter( $array ) ) != count( $array ) ) {

		$atts['missing_atts_error'] = arve_error( sprintf(
			esc_html__( 'The [arve] shortcode needs one of this attributes %s', ARVE_SLUG ),
			implode( $required_attributes ) )
		);
	}

	return $atts;
}

function arve_sc_filter_get_media_gallery_thumbnail( $atts ) {

	if ( empty( $atts['thumbnail'] ) ) {
		return $atts;
	}

	if( is_numeric( $atts['thumbnail'] ) ) {

		$attchment_id = $atts['thumbnail'];

		$atts['img_src']    = arve_get_attachment_image_url_or_srcset( 'url',    $attchment_id );
		$atts['img_srcset'] = arve_get_attachment_image_url_or_srcset( 'srcset', $attchment_id );

	} elseif ( arve_validate_url( $atts['thumbnail'] ) ) {

		$atts['img_src']    = $atts['thumbnail'];
		$atts['img_srcset'] = false;

	} else {

		$atts['img_src'] = new WP_Error( 'thumbnail', __( 'Not a valid thumbnail URL or Media ID given', ARVE_SLUG ) );
	}

	return $atts;
}

function arve_sc_filter_get_media_gallery_video( $atts ) {

	$html5_ext = arve_get_html5_attributes();

	foreach ( $html5_ext as $ext ) {

		if( ! empty( $atts[ $ext ] ) && is_numeric( $atts[ $ext ] ) ) {
			$atts[ $ext ] = wp_get_attachment_url( $atts[ $ext ] );
		}
	}

	return $atts;
}

function arve_sc_filter_detect_provider_and_id_from_url( $atts ) {

	$properties = arve_get_host_properties();

	if ( ! empty( $atts['provider'] ) || empty( $atts['url'] ) ) {
		return $atts;
	}

	foreach ( $properties as $host_id => $host ) :

		if ( empty( $host['regex'] ) ) {
			continue;
		}

		$preg_match = preg_match( '#' . $host['regex'] . '#i', $atts['url'], $matches );

		if ( 1 !== $preg_match ) {
			continue;
		}

		foreach ( $matches as $key => $value ) {

			if ( is_string( $key ) ) {
				$atts[ 'provider' ] = $host_id;
				$atts[ $key ]       = $matches[ $key ];
			}
		}

	endforeach;

	return $atts;
}

function arve_sc_filter_detect_query_args( $atts ) {

	if( empty( $atts['url'] ) ) {
		return $atts;
	}

	$to_extract = array(
		'brightcove' => array( 'videoId', 'something' ),
	);

	foreach ( $to_extract as $provider => $parameters ) {

		if( $provider != $atts['provider'] ) {
			return $atts;
		}

		$query_array = arve_url_query_array( $atts['url'] );

		foreach ( $parameters as $key => $parameter ) {

			$att_name = $atts['provider'] . "_$parameter";

			if( empty( $query_array[ $parameter ] ) ) {
				$atts[ $att_name ] = new WP_Error( $att_name, "$parameter not found in URL" );
			} else {
				$atts[ $att_name ] = $query_array[ $parameter ];
			}
		}
	}

	return $atts;
}

function arve_sc_filter_detect_youtube_playlist( $atts ) {

	if(
		'youtube' != $atts['provider'] ||
		( empty( $atts['url'] ) && empty( $atts['id'] ) )
	) {
		return $atts;
	}

	if( empty($atts['url']) ) {
		# Not a url but it will work
		$url = str_replace( array( '&list=', '&amp;list=' ), '?list=', $atts['id'] );
	} else {
		$url = $atts['url'];
	}

	$query_array = arve_url_query_array( $url );

	if( empty( $query_array['list'] ) ) {
		return $atts;
	}

	$atts['id'] = strtok( $atts['id'], '?' );
	$atts['id'] = strtok( $atts['id'], '&' );

	$atts['youtube_playlist_id'] = $query_array['list'];
	$atts['parameters']         .= 'list=' . $query_array['list'];

	return $atts;
}

function arve_sc_filter_detect_html5( $atts ) {

	if( ! empty( $atts['provider'] ) && 'html5' != $atts['provider'] ) {
		return $atts;
	}

	$html5_extensions = arve_get_html5_attributes();
	$html5_extensions[] = 'url';

	foreach ( $html5_extensions as $ext ):

		if ( ! empty( $atts[ $ext ] ) && $type = arve_check_filetype( $atts[ $ext ], $ext) ) {

			if ( arve_starts_with( $atts[ $ext ], 'https://www.dropbox.com' ) ) {
				$atts[ $ext ] = add_query_arg( 'dl', 1, $atts[ $ext ] );
			}

			$atts['video_sources'][ $type ] = $atts[ $ext ];
		}

		if ( ! empty( $atts['url'] ) && arve_ends_with( $atts['url'], ".$ext" ) ) {

			if ( arve_starts_with( $atts['url'], 'https://www.dropbox.com' ) ) {
				$atts['url'] = add_query_arg( 'dl', 1, $atts['url'] );
			}

			$atts['video_src'] = $atts['url'];
			/*
			$parse_url = parse_url( $atts['url'] );
			$pathinfo  = pathinfo( $parse_url['path'] );

			$url_ext         = $pathinfo['extension'];
			$url_without_ext = $parse_url['scheme'] . '://' . $parse_url['host'] . $path_without_ext;
			*/
		}

	endforeach;

	if( empty( $atts['video_src'] ) && empty( $atts['video_sources'] ) ) {
		return $atts;
	}

	$atts['provider'] = 'html5';
	$atts['video_sources_html'] = '';

	if ( isset( $atts['video_sources'] ) ) {

		foreach ( $atts['video_sources'] as $key => $value ) {
			$atts['video_sources_html'] .= sprintf( '<source type="%s" src="%s">', $key, $value );
		}
	}

	return $atts;
}

function arve_sc_filter_iframe_fallback( $atts ) {

	if ( empty( $atts['provider'] ) ) {

		$atts['provider'] = 'iframe';

		if ( empty( $atts['id'] ) && ! empty( $atts['url'] ) ) {
			$atts['id'] = $atts['url'];
		}
	}

	return $atts;
}

function arve_sc_filter_build_tracks_html( $atts ) {

	if ( 'html5' != $atts['provider'] ) {
		return $atts;
	}

	$atts['video_tracks_html'] = '';

	for ( $n = 1; $n <= ARVE_NUM_TRACKS; $n++ ) {

		if ( empty( $atts[ "track_{$n}" ] ) ) {
			return $atts;
		}

		preg_match( '#-(captions|chapters|descriptions|metadata|subtitles)-([a-z]{2}).vtt$#i', $atts[ "track_{$n}" ], $matches );

		if ( empty( $matches[1] ) ) {
			$atts[ "track_{$n}" ] = new WP_Error( 'track', __( 'Track kind or language code could not detected from filename', ARVE_SLUG ) );
			return $atts;
		}

		$label = empty( $atts[ "track_{$n}_label" ] ) ? arve_get_language_name_from_code( $matches[2] ) : $atts[ "track_{$n}_label" ];

		$attr = array(
			'default' => ( 1 === $n ) ? true : false,
			'kind'    => $matches[1],
			'label'   => $label,
			'src'     => $atts[ "track_{$n}" ],
			'srclang' => $matches[2],
		);

		$atts['video_tracks_html'] .= sprintf( '<track%s>', arve_attr( $attr) );
	}

	return $atts;
}