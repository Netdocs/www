<?php

/**
 * Ajax post scipts for single post
 *
 * @return bool
 * @author  @sameast
 */
function streamium_single_video_scripts() {

    if( is_single() )
    {

    	global $post;

    	$nonce = wp_create_nonce( 'single_nonce' );
    	$s3videoid = get_post_meta( $post->ID, 's3bubble_video_code_meta_box_text', true );
    	$youtube = false;
    	$youtubeCode = get_post_meta( $post->ID, 's3bubble_video_youtube_code_meta_box_text', true );
    	$stream = get_post_meta( $post->ID, 'streamium_live_stream_meta_box_text', true );
    	$streamiumVideoTrailer = get_post_meta( $post->ID, 'streamium_video_trailer_meta_box_text', true );
    	$poster   = wp_get_attachment_image_src( get_post_thumbnail_id(), 'streamium-home-slider' ); 
    	
    	if(is_user_logged_in()){
    		$userId = get_current_user_id();
    		$percentageWatched = get_post_meta( $post->ID, 'user_' . $userId, true );
    	}

    	if(pathinfo($s3videoid, PATHINFO_DIRNAME) !== "."){
		    $s3videoid = pathinfo($s3videoid, PATHINFO_BASENAME);
		}

		// Setup a array for codes
		$codes = [];

		// Check for resume
		$resume = !empty($percentageWatched) ? $percentageWatched : 0;

		// Check for a video trailer
		if(isset($_GET['trailer']) && isset($streamiumVideoTrailer)){
			$codes[] = $streamiumVideoTrailer;
			$resume = 0;
		}

		// Check if this post has programs
		$episodes = get_post_meta(get_the_ID(), 'repeatable_fields' , true);
		if(!empty($episodes)) {
			foreach ($episodes as $key => $value) : 
				$codes[] = $value['codes'];
			endforeach;
			$resume = 0;
		}else{

			if(!empty($youtubeCode)){
				$youtube = true;
				$codes[] = $youtubeCode;
			}else{
				$codes[] = $s3videoid;
			}
			
		}

		// Setup premium
        wp_localize_script( 'streamium-production', 'video_post_object', 
            array( 
                'post_id' => $post->ID,
                'subTitle' => "You're watching",
                'title' => $post->post_title,
                'para' => trim(stripslashes(strip_tags($post->post_excerpt))),
                'percentage' => $resume,
                'codes' => $codes,
                'stream' => $stream,
                'youtube' => $youtube,
                'poster' => esc_url($poster[0]),
                'nonce' => $nonce
            )
        ); 

    } 

}

add_action('wp_enqueue_scripts', 'streamium_single_video_scripts');

/**
 * Ajax post scipts for content
 *
 * @return bool
 * @author  @sameast
 */
function streamium_get_dynamic_content() {

	global $wpdb;

	// Get params
	$cat = $_REQUEST['cat'];
	$postId = (int) $_REQUEST['post_id'];
 
    if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'streamium_likes_nonce' ) || ! isset( $_REQUEST['nonce'] ) ) {
       	
       	echo json_encode(
	    	array(
	    		'error' => true,
	    		'message' => 'We could not find this post.'
	    	)
	    );

    }

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

    	$post_object = get_post( $postId );

    	if(!empty($post_object)){

    		$like_text = '';
    		$buildMeta = '';
		    if ( get_theme_mod( 'streamium_enable_premium' ) ) {

		    	$buildMeta = '<ul>';

				// Tags
				$posttags = get_the_tags($postId);
				$staring = 'Staring: ';
				if ($posttags) {
					$numItems = count($posttags);
					$i = 0;
				  	foreach($posttags as $tag) {

					  	$staring .= '<a href="/?s=' . esc_html( $tag->name ) . '">' . ucwords($tag->name) . '</a>';
					  	if(++$i !== $numItems) {
				    		$staring .= ', ';
				  		}

				    }
				    $buildMeta .= '<li class="synopis-meta-spacer">' . $staring . '</li>';
				}
				
				// Cats
				$query = get_post_taxonomies( $postId );
				$tax = isset($query[1]) ? $query[1] : "";
				$categories = get_terms( $tax, array('hide_empty' => false) );
				$genres = 'Genres: ';
				if ($categories) {
					$numItems = count($categories);
					$g = 0;
				  	foreach($categories as $cats) {

				  		$genres .= '<a href="' . esc_url( get_category_link( $cats->term_id ) ) . '">' . ucwords($cats->name) . '</a>';
				  		if(++$g !== $numItems) {
				    		$genres .= ', ';
				  		}

				  	}
				  	$buildMeta .= '<li class="synopis-meta-spacer">' . $genres . '</li>';
				}

				// If its a tv list episodes
				$episodes = get_post_meta($postId, 'repeatable_fields' , true);
				if(!empty($episodes)) {

					$buildMeta .= '<li class="synopis-meta-spacer">Epsodes: <a>' . count($episodes) . '</a></li>';

				}

				// Release date
				$buildMeta .= '<li class="synopis-meta-spacer">Released: <a href="/?s=all&date=' . get_the_date('Y/m/d', $postId) . '">' . get_the_date('l, F j, Y', $postId) . '</a></li></ul>';

				// Likes and reviews
		        $nonce = wp_create_nonce( 'streamium_likes_nonce' );
		    	$link = admin_url('admin-ajax.php?action=streamium_likes&post_id='. $postId .'&nonce='.$nonce);

		        $like_text = '<div class="synopis-premium-meta hidden-xs">
		        				<a id="like-count-' . $postId . '" class="streamium-review-like-btn streamium-btns streamium-reviews-btns" data-toggle="tooltip" title="CLICK TO LIKE!" data-id="' . $postId . '" data-nonce="' . $nonce . '">' . get_streamium_likes($postId) . '</a>
		        				<a class="streamium-list-reviews streamium-btns streamium-reviews-btns" data-id="' . $postId . '" data-nonce="' . $nonce . '">Read reviews</a>
							</div>';

		    }

		    $content = $post_object->post_content . $buildMeta . $like_text;
		    if(streamium_get_device('device') != 'desktop'){
		    	$content = (empty($post_object->post_excerpt) ? strip_tags($post_object->post_content) : $post_object->post_excerpt);
		    }
	    	$fullImage  = wp_get_attachment_image_src( get_post_thumbnail_id( $postId ), 'streamium-home-slider' ); 
	    	$streamiumVideoTrailer = get_post_meta( $postId, 'streamium_video_trailer_meta_box_text', true );

	    	echo json_encode(
		    	array(
		    		'error' => false,
		    		'cat' => $cat,
		    		'title' => $post_object->post_title,
		    		'content' => $content,
		    		'bgimage' =>  isset($fullImage) ? $fullImage[0] : "",
		    		'trailer' => $streamiumVideoTrailer,
		    		'href' => get_permalink($postId),
		    		'post' => $post_object
		    	)
		    );

	    }else{

	    	echo json_encode(
		    	array(
		    		'error' => true,
		    		'message' => 'We could not find this post.'
		    	)
		    );

	    }

        die();

    }
    else {
        
        wp_redirect( get_permalink( $_REQUEST['post_id'] ) );
        exit();

    }

}

add_action( 'wp_ajax_nopriv_streamium_get_dynamic_content', 'streamium_get_dynamic_content' );
add_action( 'wp_ajax_streamium_get_dynamic_content', 'streamium_get_dynamic_content' );

function streamium_custom_post_types_general( $hook_suffix ){

    if( in_array($hook_suffix, array('post.php', 'post-new.php') ) ){
        
        $screen = get_current_screen();

        if( is_object( $screen ) && in_array($screen->post_type, array('movie', 'tv','sport','kid'))){

            // Register, enqueue scripts and styles here
            wp_enqueue_script( 'streamium-admin-custom-post-type-general', get_template_directory_uri() . '/production/js/custom.post.type.general.min.js', array( 'jquery' ),'1.1', true );

        }

        if( is_object( $screen ) && in_array($screen->post_type, array('stream'))){

            // Register, enqueue scripts and styles here
            wp_enqueue_script( 'streamium-admin-custom-post-type-stream', get_template_directory_uri() . '/production/js/custom.post.type.stream.min.js', array( 'jquery' ),'1.1', true );

        }
    }
}

add_action( 'admin_enqueue_scripts', 'streamium_custom_post_types_general');