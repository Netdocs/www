<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			video.php
//		Description:
//			This file renders videos, video images and video meta.
//		Copyright:
//			Copyright (c) 2016 Ternstyle LLC.
//		License:
//			This software is licensed under the terms of the End User License Agreement (EULA) 
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit:
//			http://www.ternstyle.us/automatic-video-posts-plugin-for-wordpress/license.html
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('the_post','WP_ayvpp_video_init');
add_filter('the_content','WP_ayvpp_content');
add_filter('post_thumbnail_size','WP_ayvpp_thumbnail_size');
add_filter('post_thumbnail_html','WP_ayvpp_thumbnail',0,5);
//                                *******************************                                 //
//________________________________** RENDER VIDEO              **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_ayvpp_video_init() {
	global $ayvpp_options,$post,$ayvpp_video;
	$ayvpp_video = new youtube_video($ayvpp_options);	
}
function WP_ayvpp_content($c='') {
	global $ayvpp_options,$ayvpp_video;

	if(!isset($ayvpp_video->meta['_ayvpp_video']) or empty($ayvpp_video->meta['_ayvpp_video'])) {
		return $c;
	}

	$v = '';
	if(is_single()) {
		$v = ((isset($ayvpp_options['content_display_meta']) and $ayvpp_options['content_display_meta']) ? $ayvpp_video->video().$ayvpp_video->meta_show() : $ayvpp_video->video());
	}
	elseif(isset($ayvpp_options['video_post_list_show']) and $ayvpp_options['video_post_list_show']) {
		$v = $ayvpp_video->video();
	}
	
	return $ayvpp_options['content_top'] ? $c.$v : $v.$c;
	
}
function WP_ayvpp_thumbnail_size($s) {
	global $ayvpp_options,$ayvpp_image_size;
	if(!isset($ayvpp_options['import_thumbnails']) or $ayvpp_options['import_thumbnails'] == 1) {
		return $s;
	}
	$ayvpp_image_size = $s;
	return $s;
}
function WP_ayvpp_thumbnail($i,$b,$c,$s,$v) {
	global $ayvpp_image_size,$_wp_additional_image_sizes,$ayvpp_video,$post,$ayvpp_options;
	
	if((isset($ayvpp_options['thumbs_show']) and (int)$ayvpp_options['thumbs_show'] == 0) or !isset($ayvpp_options['thumbs_show'])) {
		return $i;
	}
	if(!isset($ayvpp_options['import_thumbnails']) or $ayvpp_options['import_thumbnails'] == 1) {
		return $i;
	}

	WP_ayvpp_video_init();
	
	$t = $ayvpp_video->thumb('*');

	if(!isset($ayvpp_video->meta['_ayvpp_video']) or empty($ayvpp_video->meta['_ayvpp_video']) or !$t) {
		return $i;
	}
	
	$s = $ayvpp_image_size;
	if(isset($_wp_additional_image_sizes[$s]['width'])) {
		$w = intval($_wp_additional_image_sizes[$s]['width']);
		$h = intval($_wp_additional_image_sizes[$s]['height']);
		$c = intval($_wp_additional_image_sizes[$s]['crop']);
	}
	else {
		$w = get_option("{$s}_size_w");
		$h = get_option("{$s}_size_h");
		$c = get_option("{$s}_crop");
	}
	$c = $c ? '1' : '0';
	
	$s = '<img src="'.AYVPP_URL.'/tools/timthumb.php?src='.$t.'&w='.$w.'&h='.$h.'&zc='.$c.'" alt="'.$post->post_title.'" title="'.$post->post_title.'"';
	$s .= isset($v['class']) ? 'class="'.$v['class'].'"' : '';
	$s .= isset($v['width']) ? 'width="'.$v['width'].'"' : '';
	$s .= isset($v['height']) ? 'height="'.$v['height'].'"' : '';
	$s .= ' />';
	return $s;
	//return '<img src="'.AYVPP_URL.'/tools/timthumb.php?src=http://img.youtube.com/vi/'.$m.'/0.jpg&w='.$w.'&h='.$h.'&zc='.$c.'" alt="" title="'.$i.'" />';

}

/****************************************Terminate Script******************************************/
?>