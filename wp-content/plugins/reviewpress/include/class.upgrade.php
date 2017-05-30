<?php
/**
 *	Upgrade class to upgrade the old ReviewPess from 0.4 to 1.0.0
 *
 * @since 1.0.0
 */
class ReviewPress_Upgrade {


	function __construct() {

		add_action( 'admin_init', array( $this, 'save_routine_action' ), 9 );
		add_action( 'admin_init', array( $this, 'load_routine' ), 10 );

	}

	/**
	 * Load upgrade routine on plugin loaded.
	 *
	 * @since 1.0.0
	 */
	public function load_routine() {

		global $wpdb;
		$is_old_review = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts  WHERE post_type = 'reviews'" );
		//var_dump($is_old_review);
		if ( $is_old_review > 0 ) {

			if ( ! get_option( 'REVIEWPRESS_VERSION' ) ) {
				add_action( 'admin_notices' , array( $this, 'upgrade_routine_message' ) );
			}
		} else {
			update_option( 'REVIEWPRESS_VERSION', REVIEWPRESS_VERSION );
		}

	}

	/**
	 * Show admin notice.
	 *
	 * @since 1.0.0
	 */
	public function upgrade_routine_message() {

		echo '<div class="updated notice reviewpress">
        <p><strong>Important Notice: </strong>Are you upgrading ReviewPress from older version 0.4 to 1.x ? <br/> Do you want to copy all your previous Reviews into new ReviewPress ? </p>
		<a class="button-primary" href="' . admin_url( 'admin.php?page=reviewpress_settings&reviewpress_upgrade=yes' ) . '">Yes, Process</a>
		<a class="button-secondary" href="' . admin_url( 'admin.php?page=reviewpress_settings&reviewpress_upgrade=cancel' ) . '">No, Skip</a>
		</div>';

	}

	/**
	 * save userinput on routine message
	 *
	 * @since 1.0.0
	 */
	public function save_routine_action() {

		if ( isset( $_GET['reviewpress_upgrade'] ) && $_GET['reviewpress_upgrade'] == 'cancel'  ) {
			update_option( 'REVIEWPRESS_VERSION', REVIEWPRESS_VERSION );
		}

		if ( isset( $_GET['reviewpress_upgrade'] ) && $_GET['reviewpress_upgrade'] == 'yes' && ! get_option( 'REVIEWPRESS_UPGRADE' ) ) {

			$this->copy_reviews();
			update_option( 'REVIEWPRESS_UPGRADE', '1' );
			update_option( 'REVIEWPRESS_VERSION', REVIEWPRESS_VERSION );

		}

	}

	/**
	 * Copy all reviews from 'reviews' CPT into ReviewPress CTP 'cptreviewpress'.
	 *
	 * @since 1.0.0
	 */
	public function copy_reviews() {

		$args = array(
			'post_type'   => 'reviews',
			'post_status' => 'publish',
		);

		$previous_posts = get_posts( $args );

		foreach ( $previous_posts as $previous_post ) {

			$postarr = array(
				'post_author' => $previous_post->post_author,
				'post_title'  => $previous_post->post_title,
				'post_date'   => $previous_post->post_date,
				'post_status' => 'publish',
				'post_type'   => REVIEWPRESS_SLUG,
				'post_parent' => '0',
			);

			$post_id  = wp_insert_post( $postarr );

			update_post_meta( $post_id, 'wpbr_review_title', $previous_post->post_title );
			update_post_meta( $post_id, 'wpbr_review_message', $previous_post->post_content );
		}
	}
}

$ReviewPress_Upgrade_Instance = new ReviewPress_Upgrade();

?>
