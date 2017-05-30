<?php

/**
 * ReviePress handle all review section.
 *
 * @package ReviewPress
 */

/**
 * Class to handle all review section.
 */
class Review_All_Review {

	/**
	 * Class constructor.
	 */
	function __construct() {

		add_filter( 'manage_cptreviewpress_posts_columns' , array( $this, 'cptreviewpress_new_columns' ) );
		add_action( 'manage_cptreviewpress_posts_custom_column', array( $this, 'manage_cptreviewpress_new_columns' ), 10, 2 );
		add_filter( 'manage_edit-cptreviewpress_sortable_columns', array( $this, 'cptreviewpress_sortable_columns' ), 10 , 1 );
		add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'wpbr_new_row_action' ), 10, 2 );
		// add_action( 'load-edit.php', array( $this, 'change_status'  ) , 10 );
		add_action( 'admin_menu', array( $this, 'change_status' ) , 10 );
		add_filter( 'post_updated_messages', array( $this, 'review_updated_messages' ) );

	}


	/**
	 * Add new columns for review post type
	 *
	 * @param  [array] $columns [columns name].
	 * @return [array]          [new columns].
	 *
	 * @since 1.0.0
	 */
	public function cptreviewpress_new_columns( $columns ) {

		$new_columns = array(
		'comment'         => __( 'Comments' , 'reviewpress' ),
		'page_post_title' => __( 'Page/Post Title' , 'reviewpress' ),
		'rating'          => __( 'Rating' , 'reviewpress' ),
		'status'          => __( 'Status' , 'reviewpress' ),
		'author_email'    => __( 'Author Email' , 'reviewpress' ),
		'date'            => __( 'Date' , 'reviewpress' ),
		);
		unset( $columns['date'] );
		unset( $columns['comments'] );
		return array_merge( $columns, $new_columns );
	}

	/**
	 * Add data in new columns
	 *
	 * @param  [array] $column  [columns name].
	 * @param  [int]   $post_id [current post id].
	 *
	 * @since 1.0.0
	 */
	public function manage_cptreviewpress_new_columns( $column, $post_id ) {

		global $post;

		switch ( $column ) {

			case 'comment' :
				echo esc_textarea( substr( get_post_meta( $post_id , 'wpbr_review_message' , true ) , 0 , 80 ) );
		  break;

			case 'status':
				echo esc_textarea( get_post_status( $post_id ) );
		  break;

			case 'page_post_title':
				echo  esc_textarea( get_the_title( wp_get_post_parent_id( $post_id ) ) );
		  break;

			case 'rating':
				echo esc_textarea( get_post_meta( $post_id , 'wpbr_review_rating' , true ) );
		  break;

			case 'author_email':
				echo esc_textarea( get_post_meta( $post_id , 'wpbr_review_email', true ) );
		  break;

			default:
		  break;
		}
	}

	/**
	 * Sort new columns
	 *
	 * @param  [array] $sortable_columns [name of coloums].
	 *
	 * @since 1.0.0
	 */
	public function cptreviewpress_sortable_columns( $sortable_columns ) {

		$sortable_columns['rating']          = 'rating';
		$sortable_columns['comment']         = 'comment';
		$sortable_columns['page_post_title'] = 'page_post_title';
		$sortable_columns['status']          = 'status';
		$sortable_columns['author_email']    = 'author_email';

		return $sortable_columns;

	}

	/**
	 * Remove some action from review posts.
	 *
	 * @param  [array] $actions [get pre actions name].
	 * @param  [array] $post    [current post id].
	 *
	 * @since 1.0.0
	 */
	public function remove_quick_edit( $actions, $post ) {

		global $current_screen;
		if (  'cptreviewpress' !== $current_screen->post_type ) { return $actions; }

		unset( $actions['view'] );
		unset( $actions['inline hide-if-no-js'] );

		return $actions;
	}

	/**
	 * Add new actions.
	 *
	 * @param  [array] $actions [get pre actions name].
	 * @param  [array] $post    [current post object].
	 *
	 * @since 1.0.0
	 */
	public function wpbr_new_row_action( $actions, $post ) {
		// Check for your post type.
		if ( 'cptreviewpress' == $post->post_type ) {

			if ( get_post_status( $post ) != 'publish' ) {
				$nonce = wp_create_nonce( 'quick-publish-action' );
				$link = admin_url( "edit.php?post_type=cptreviewpress&update_id={$post->ID}&_wpnonce={$nonce}&actions=approve" );
				$actions['approve'] = "<a href='$link'>Approve</a>";
			} else if ( get_post_status( $post ) == 'publish' ) {
				$nonce = wp_create_nonce( 'quick-publish-action' );
				$link = admin_url( "edit.php?post_type=cptreviewpress&update_id={$post->ID}&_wpnonce={$nonce}&actions=pending" );
				$actions['pending'] = "<a href='$link'>Pending</a>";

			}
		}
		return $actions;
	}

	/**
	 * Change status of posts i.e approve->pending vicevarse
	 *
	 * @since 1.0.
	 */
	public function change_status() {
		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_key( $_GET['_wpnonce'] ) : null;
		if ( wp_verify_nonce( $nonce, 'quick-publish-action' ) && isset( $_GET['update_id'] ) && isset( $_GET['actions'] ) && 'approve' === $_GET['actions']  ) {
			$my_post                = array();
			$my_post['ID']          = sanitize_text_field( wp_unslash( $_GET['update_id'] ) );
			$my_post['post_status'] = 'publish';
			wp_update_post( $my_post );
		} else if ( wp_verify_nonce( $nonce, 'quick-publish-action' ) && isset( $_GET['update_id'] ) && isset( $_GET['actions'] ) && 'pending' === $_GET['actions']  ) {
			$my_post                = array();
			$my_post['ID']          = sanitize_text_field( wp_unslash( $_GET['update_id'] ) );
			$my_post['post_status'] = 'pending';
			wp_update_post( $my_post );
		}
	}



	/**
	 * Update messages for review post.
	 *
	 * @param [array] $messages Existing post update messages.
	 *
	 * @return [array] Return New Messages.
	 */
	function review_updated_messages( $messages ) {

		global $post;
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages['cptreviewpress'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Review updated.' , 'reviewpress' ),
			2  => __( 'Review field updated.' , 'reviewpress' ),
			3  => __( 'Review field deleted.' , 'reviewpress' ),
			4  => __( 'Review updated.' , 'reviewpress' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Review restored to revision from %s' , 'reviewpress' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Review published.' , 'reviewpress' ),
			7  => __( 'Review saved.' , 'reviewpress' ),
			8  => __( 'Review submitted.' , 'reviewpress' ),
			9  => sprintf(
				__( 'Review scheduled for: <strong>%1$s</strong>.' , 'reviewpress' ),
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Review draft updated.' , 'reviewpress' ),
		);

		return $messages;
	}
}

$instance = new Review_All_Review();
