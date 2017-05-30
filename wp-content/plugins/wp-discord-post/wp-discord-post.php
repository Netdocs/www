<?php
/**
 * WP Discord Post
 *
 * @author      Nicola Mustone
 * @license     GPL-2.0+
 *
 * Plugin Name: WP Discord Post
 * Plugin URI:  https://wordpress.org/plugins/wp-discord-post/
 * Description: A Discord integration that sends a message on your desired Discord server and channel for every new post published.
 * Version:     1.0.5
 * Author:      Nicola Mustone
 * Author URI:  https://nicola.blog/
 * Text Domain: wp-discord-post
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class of the plugin WP Discord Post. Handles the bot and the admin settings.
 */
class WP_Discord_Post {
	/**
	 * Adds the required hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'publish_post', array( $this, 'send_post' ), 10, 2 );

		$this->load_textdomain();
	}

	/**
	 * Inits the settings pag.e
	 */
	public function settings_init() {
		add_settings_section(
			'wp_discord_post_settings',
			esc_html__( 'WP Discord Post', 'wp-discord-post' ),
			array( $this, 'settings_callback' ),
			'general'
		);

		add_settings_field(
			'wp_discord_post_bot_username',
			esc_html__( 'Bot Username', 'wp-discord-post' ),
			array( $this, 'print_bot_username_field' ),
			'general',
			'wp_discord_post_settings'
		);

		add_settings_field(
			'wp_discord_post_avatar_url',
			esc_html__( 'Avatar URL', 'wp-discord-post' ),
			array( $this, 'print_avatar_url_field' ),
			'general',
			'wp_discord_post_settings'
		);

		add_settings_field(
			'wp_discord_post_bot_token',
			esc_html__( 'Discord Bot Token', 'wp-discord-post' ),
			array( $this, 'print_bot_token_field' ),
			'general',
			'wp_discord_post_settings'
		);

		add_settings_field(
			'wp_discord_post_webhook_url',
			esc_html__( 'Discord Webhook URL', 'wp-discord-post' ),
			array( $this, 'print_webhook_url_field' ),
			'general',
			'wp_discord_post_settings'
		);

		add_settings_field(
			'wp_discord_post_mention_everyone',
			esc_html__( 'Mention Everyone', 'wp-discord-post' ),
			array( $this, 'print_mention_everyone_field' ),
			'general',
			'wp_discord_post_settings'
		);

		register_setting( 'general', 'wp_discord_post_bot_username' );
		register_setting( 'general', 'wp_discord_post_avatar_url' );
		register_setting( 'general', 'wp_discord_post_bot_token' );
		register_setting( 'general', 'wp_discord_post_webhook_url' );
		register_setting( 'general', 'wp_discord_post_mention_everyone' );
	}

	/**
	 * Prints the description in the settings page.
	 */
	public function settings_callback() {
		esc_html_e( 'Configure your WP Discord Post instance to write on your Discord server', 'wp-discord-post' );
	}

	/**
	 * Prints the Bot Username settings field.
	 */
	public function print_bot_username_field() {
		$value = get_option( 'wp_discord_post_bot_username' );

		echo '<input type="text" name="wp_discord_post_bot_username" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . esc_html__( 'The username that you want to use for the bot on your Discord server.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Avatar URL settings field.
	 */
	public function print_avatar_url_field() {
		$value = get_option( 'wp_discord_post_avatar_url' );

		echo '<input type="text" name="wp_discord_post_avatar_url" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . esc_html__( 'The URL of the avatar that you want to use for the bot on your Discord server.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Prints the Bot Token settings field.
	 */
	public function print_bot_token_field() {
		$value = get_option( 'wp_discord_post_bot_token' );

		echo '<input type="text" name="wp_discord_post_bot_token" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The token of your Discord bot. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://discordapp.com/developers/docs/intro">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_webhook_url_field() {
		$value = get_option( 'wp_discord_post_webhook_url' );

		echo '<input type="text" name="wp_discord_post_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The username that you want to use for the bot on your Discord server. %1$sLearn more%2$s', 'wp-discord-post' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_mention_everyone_field() {
		$value = get_option( 'wp_discord_post_mention_everyone' );

		echo '<input type="checkbox" name="wp_discord_post_mention_everyone" value="yes"' . checked( 'yes', $value, false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Mention @everyone when sending the message to Discord.', 'wp-discord-post' ) . '</span>';
	}

	/**
	 * Sends the post to Discord using the specified webhook URL and Bot token.
	 *
	 * @param  int     $id   The post ID.
	 * @param  WP_Post $post The post object.
	 */
	public function send_post( $id, $post ) {
		// Check if the post has been already published.
		if ( ! $this->is_new_post( $id ) ) {
			return;
		}

		$author = $post->post_author;
		$author = get_user_by( 'ID', $author );
		$author = $author->display_name;

		$bot_username     = get_option( 'wp_discord_post_bot_username' );
		$bot_avatar       = get_option( 'wp_discord_post_avatar_url' );
		$bot_token        = get_option( 'wp_discord_post_bot_token' );
		$webhook_url      = get_option( 'wp_discord_post_webhook_url' );
		$mention_everyone = get_option( 'wp_discord_post_mention_everyone' );

		if ( 'yes' === $mention_everyone ) {
			$author = '@everyone ' . $author;
		}

		$args = array(
			'content'    => sprintf( esc_html__( '%1$s just published the article %2$s on their blog: %3$s', 'wp-discord-post' ), $author, esc_html( $post->post_title ), get_permalink( $id ) ),
			'username'   => esc_html( $bot_username ),
			'avatar_url' => esc_url( $bot_avatar ),
		);

		$args = apply_filters( 'wp_discord_request_args', $args );

		$request = array(
			'headers' => array(
				'Authorization' => 'Bot ' . esc_html( $bot_token ),
				'Content-Type'  => 'application/json',
			),
			'body' => wp_json_encode( $args ),
		);

		$response = wp_remote_post( esc_url( $webhook_url ), $request );

		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			add_post_meta( $id, '_wp_discord_post_published', 'yes' );
		}
	}

	/**
	 * Checks if a post has been published already or not.
	 *
	 * @param  int $post_id The post ID.
	 * @return bool
	 */
	public function is_new_post( $post_id ) {
		return 'yes' !== get_post_meta( $post_id, '_wp_discord_post_published', true );
	}

	/**
	 * Loads the plugin localization files.
	 */
	public function load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-discord-post' );
		load_textdomain( 'wp-discord-post', WP_LANG_DIR . '/wp-discord-post/discord-post-' . $locale . '.mo' );
		load_plugin_textdomain( 'wp-discord-post', false, plugin_basename( __DIR__ ) . '/languages' );
	}
}

new WP_Discord_Post();
