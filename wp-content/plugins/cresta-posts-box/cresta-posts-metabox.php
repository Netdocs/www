<?php
/**
 * Cresta Posts Box Meta Box
 */
 
function cresta_posts_box_add_meta_box() {
	$cpb_options = get_option( 'crestapostsbox_settings' );
	$thePostType = $cpb_options['cresta_posts_box_selected_page'];
	$screens = explode(",",$thePostType);

	foreach ( $screens as $screen ) {

		add_meta_box(
			'cresta_posts_box_sectionid',
			esc_html__( 'Cresta Posts Box', 'cresta-posts-box' ),
			'cresta_posts_box_meta_box_callback',
			$screen,
			'side',
			'low'
		);
	}
}
add_action( 'add_meta_boxes', 'cresta_posts_box_add_meta_box' );

function cresta_posts_box_meta_box_callback( $post ) {
	wp_nonce_field( 'cresta_posts_box_meta_box', 'cresta_meta_box_nonce' );
	$crestaValue = get_post_meta( $post->ID, '_get_cresta_posts_box_plugin', true );
	?>
	<label for="cresta_posts_box_new_field">
        <input type="checkbox" name="cresta_posts_box_new_field" id="cresta_posts_box_new_field" value="1" <?php checked( $crestaValue, '1' ); ?> /><?php esc_html_e( 'Hide Cresta Posts Box in this page?', 'cresta-posts-box' )?>
    </label>
	<?php
}

function cresta_posts_box_save_meta_box_data( $post_id ) {
	if ( ! isset( $_POST['cresta_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['cresta_meta_box_nonce'], 'cresta_posts_box_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	if ( isset( $_POST['cresta_posts_box_new_field'] ) ) {
		update_post_meta( $post_id, '_get_cresta_posts_box_plugin', sanitize_text_field(wp_unslash($_POST['cresta_posts_box_new_field'])) );
	} else {
		delete_post_meta( $post_id, '_get_cresta_posts_box_plugin' );
	}
	
}
add_action( 'save_post', 'cresta_posts_box_save_meta_box_data' );