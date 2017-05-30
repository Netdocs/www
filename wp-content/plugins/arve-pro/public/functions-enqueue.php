<?php

function arve_pro_action_register_styles() {

	$min = arve_pro_get_min_suffix();

	wp_register_style(
		'lity',
		ARVE_PRO_NODE_URL . 'lity/dist/lity.min.css',
		array(),
		'2.2.2'
	);

	wp_register_style(
		'arve-pro',
		ARVE_PRO_PUBLIC_URL . "arve-pro$min.css",
		array( 'lity' ),
		ARVE_PRO_VERSION
	);
}

function arve_pro_action_register_scripts() {

	$min = arve_pro_get_min_suffix();

	wp_register_script( 'inview',              ARVE_PRO_NODE_URL . 'jquery-inview/jquery.inview.min.js',          array( 'jquery' ), '1.1.2', true );
	wp_register_script( 'lity',                ARVE_PRO_NODE_URL . 'lity/dist/lity.min.js',                       array( 'jquery' ), '2.2.2', true );
	wp_register_script( 'object-fit-polyfill', ARVE_PRO_NODE_URL . 'objectFitPolyfill/dist/objectFitPolyfill.basic.min.js', array(), '2.0.4', true );
	wp_register_script( 'vimeo-jsapi',         ARVE_PRO_NODE_URL . '@vimeo/player/dist/player.min.js',                      array(), '2.0.1', true );
	wp_register_script( 'youtube-jsapi',       'https://www.youtube.com/iframe_api',                                        array(), ARVE_PRO_VERSION, true );
	wp_register_script( 'arve-pro',            ARVE_PRO_PUBLIC_URL . "arve-pro$min.js",    array( 'jquery', 'object-fit-polyfill' ), ARVE_PRO_VERSION, true );
}
