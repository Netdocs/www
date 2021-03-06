<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Html;
use ReflectionClass;
use ReflectionMethod;

class Settings
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Html
	 */
	protected $html;

	/**
	 * @var array
	 */
	protected $settings;

	public function __construct( App $app, Html $html )
	{
		$this->app      = $app;
		$this->html     = $html;
		$this->settings = [];
	}

	/**
	 * Add a setting default
	 *
	 * @param string $formId
	 *
	 * @return void
	 */
	public function addSetting( $formId, array $args )
	{
		$args = $this->normalizePaths( $formId, $args );

		if( isset( $args['name'] )) {
			$this->settings[ $args['name']] = $this->getDefault( $args );
		}

		$this->html->addfield( $formId, $args );
	}

	/**
	 * Get the default field value
	 *
	 * @return string
	 */
	public function getDefault( array $args )
	{
		isset( $args['default'] ) ?: $args['default'] = '';
		isset( $args['placeholder'] ) ?: $args['placeholder'] = '';

		if( $args['default'] === ':placeholder' ) {
			$args['default'] = $args['placeholder'];
		}

		if( strpos( $args['type'], 'yesno' ) !== false && empty( $args['default'] )) {
			$args['default'] = 'no';
		}

		return $args['default'];
	}

	/**
	 * Get the default settings
	 *
	 * @return array
	 */
	public function getSettings()
	{
		$this->register();

		return $this->settings;
	}

	/**
	 * @param string $path
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function normalizePath( $path, $prefix )
	{
		return substr( $path, 0, strlen( $prefix )) != $prefix
			? sprintf( '%s.%s', $prefix, $path )
			: $path;
	}

	/**
	 * @param string $formId
	 *
	 * @return array
	 */
	public function normalizePaths( $formId, array $args )
	{
		$prefix = strtolower( str_replace( '/', '.', $formId ));

		if( isset( $args['name'] ) && is_string( $args['name'] )) {
			$args['name'] = $this->normalizePath( $args['name'], $prefix );
		}

		if( isset( $args['depends'] ) && is_array( $args['depends'] )) {
			$depends = [];
			foreach( $args['depends'] as $path => $value ) {
				$depends[ $this->normalizePath( $path, $prefix ) ] = $value;
			}
			$args['depends'] = $depends;
		}

		return $args;
	}

	/**
	 * Register the settings for each form
	 *
	 * @return void
	 *
	 * @action admin_init
	 */
	public function register()
	{
		if( !empty( $this->settings ))return;

		$methods = (new ReflectionClass( __CLASS__ ))->getMethods( ReflectionMethod::IS_PROTECTED );

		foreach( $methods as $method ) {
			if( substr( $method->name, 0, 3 ) === 'set' ) {
				$this->{$method->name}();
			}
		}
	}

	/**
	 * @return void
	 */
	protected function setGeneral()
	{
		$formId = 'settings/general';

		$this->html->createForm( $formId, [
			'action' => admin_url( 'options.php' ),
			'nonce'  => $this->app->id . '-settings',
			'submit' => __( 'Save Settings', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'yesno_inline',
			'name'    => 'require.approval',
			'label'   => __( 'Require approval', 'site-reviews' ),
			'default' => 'yes',
			'desc'    => __( 'Set the status of new review submissions to pending.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'yesno_inline',
			'name'  => 'require.login',
			'label' => __( 'Require login', 'site-reviews' ),
			'desc'  => __( 'Only allow review submissions from registered users.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'radio',
			'name'    => 'notification',
			'label'   => __( 'Notifications', 'site-reviews' ),
			'default' => 'none',
			'options' => [
				'none'    => __( 'Do not send review notifications', 'site-reviews' ),
				'default' => sprintf( __( 'Send to administrator <code>%s</code>', 'site-reviews' ), get_option( 'admin_email' )),
				'custom'  => __( 'Send to one or more email addresses', 'site-reviews' ),
				'webhook' => __( 'Send to <a href="https://slack.com/">Slack</a>', 'site-reviews' ),
			],
		]);

		$this->addSetting( $formId, [
			'type'    => 'text',
			'name'    => 'notification_email',
			'label'   => __( 'Send notification emails to', 'site-reviews' ),
			'depends' => [
				'notification' => 'custom',
			],
			'placeholder' => __( 'Separate multiple emails with a comma', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'url',
			'name'    => 'webhook_url',
			'label'   => __( 'Webhook URL', 'site-reviews' ),
			'depends' => [
				'notification' => 'webhook',
			],
			'desc' => sprintf(
				__( 'To send notifications to Slack, <a href="%s">create a new Incoming WebHook</a> and then paste the provided Webhook URL in the field above.', 'site-reviews' ),
				esc_url( 'https://slack.com/apps/new/A0F7XDUAZ-incoming-webhooks' )
			),
		]);

		$this->addSetting( $formId, [
			'type'    => 'code',
			'name'    => 'notification_message',
			'label'   => __( 'Notification template', 'site-reviews' ),
			'rows'    => 9,
			'depends' => [
				'notification' => ['custom', 'default', 'webhook'],
			],
			'default' => $this->html->renderTemplate( 'email/templates/review-notification', [], 'return' ),
			'desc' => 'To restore the default text, save an empty template.
				If you are sending notifications to Slack then this template will only be used as a fallback in the event that <a href="https://api.slack.com/docs/attachments">Message Attachments</a> have been disabled.<br>
				Available template tags:<br>
				<code>{review_rating}</code> - The review rating number (1-5)<br>
				<code>{review_title}</code> - The review title<br>
				<code>{review_content}</code> - The review content<br>
				<code>{review_author}</code> - The review author<br>
				<code>{review_email}</code> - The email of the review author<br>
				<code>{review_ip}</code> - The IP address of the review author<br>
				<code>{review_link}</code> - The link to edit/view a review',
		]);
	}

	/**
	 * @return void
	 */
	protected function setReviews()
	{
		$formId = 'settings/reviews';

		$this->html->createForm( $formId, [
			'action' => admin_url( 'options.php' ),
			'nonce'  => $this->app->id . '-settings',
			'submit' => __( 'Save Settings', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'yesno_inline',
			'name'  => 'avatars.enabled',
			'label' => __( 'Enable Avatars', 'site-reviews' ),
			'desc'  => __( 'Display reviewer avatars. These are generated from the email address of the reviewer using <a href="https://gravatar.com">Gravatar</a>.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'yesno_inline',
			'name'  => 'date.enabled',
			'label' => __( 'Enable Custom Dates', 'site-reviews' ),
			'desc'  => sprintf( __( 'The default date format is the one set in your <a href="%s">WordPress settings<a>.', 'site-reviews' ), get_admin_url( null, 'options-general.php' )),
		]);

		$this->addSetting( $formId, [
			'type'    => 'text',
			'name'    => 'date.format',
			'label'   => __( 'Date Format', 'site-reviews' ),
			'default' => get_option( 'date_format' ),
			'desc'    => sprintf( __( 'Enter a custom date format (<a href="%s">documentation on date and time formatting</a>).', 'site-reviews' ), 'https://codex.wordpress.org/Formatting_Date_and_Time' ),
			'depends' => [
				'date.enabled' => 'yes',
			],
		]);

		$this->addSetting( $formId, [
			'type'  => 'yesno_inline',
			'name'  => 'excerpt.enabled',
			'label' => __( 'Enable Excerpts', 'site-reviews' ),
			'desc'  => __( 'Display an excerpt instead of the full review.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'number',
			'name'    => 'excerpt.length',
			'label'   => __( 'Excerpt Length', 'site-reviews' ),
			'default' => '55',
			'desc'    => __( 'Set the excerpt word length.', 'site-reviews' ),
			'depends' => [
				'excerpt.enabled' => 'yes',
			],
		]);
	}

	/**
	 * @return void
	 */
	protected function setReviewsForm()
	{
		$formId = 'settings/reviews-form';

		$this->html->createForm( $formId, [
			'action' => admin_url( 'options.php' ),
			'nonce'  => $this->app->id . '-settings',
			'submit' => __( 'Save Settings', 'site-reviews' ),
		]);

		$this->html->addfield( $formId, [
			'type'  => 'heading',
			'value' => __( 'Invisible reCAPTCHA', 'site-reviews' ),
			'desc'  => __( 'Invisible reCAPTCHA is a free anti-spam service from Google. To use it, you will need to <a href="http://www.google.com/recaptcha/admin" target="_blank">sign up for an API key pair</a> for your site.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'select',
			'name'  => 'recaptcha.integration',
			'label' => __( 'Use reCAPTCHA?', 'site-reviews' ),
			'options' => [
				'' => __( 'Do not use reCAPTCHA', 'site-reviews' ),
				'custom' => __( 'Custom Integration', 'site-reviews' ),
				'invisible-recaptcha' => _x( 'Plugin: Invisible reCaptcha', 'plugin name', 'site-reviews' ),
			],
			'desc'  => __( 'If you are already using a reCAPTCHA plugin listed here, please select it. Otherwise choose "Custom Integration".', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'recaptcha.key',
			'label' => __( 'Site Key', 'site-reviews' ),
			'depends' => [
				'recaptcha.integration' => 'custom',
			],
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'recaptcha.secret',
			'label' => __( 'Site Secret', 'site-reviews' ),
			'depends' => [
				'recaptcha.integration' => 'custom',
			],
		]);

		$this->addSetting( $formId, [
			'type'  => 'select',
			'name'  => 'recaptcha.position',
			'label' => __( 'Badge Position', 'site-reviews' ),
			'options' => [
				'bottomleft' => 'Bottom Left',
				'bottomright' => 'Bottom Right',
				'inline' => 'Inline',
			],
			'depends' => [
				'recaptcha.integration' => 'custom',
			],
		]);

		$this->html->addfield( $formId, [
			'type'  => 'heading',
			'value' => __( 'Form Labels', 'site-reviews' ),
			'desc'  => __( 'Customize the label text for the review submission form fields.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'rating.label',
			'label' => __( 'Rating label', 'site-reviews' ),
			'placeholder' => __( 'Your overall rating', 'site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'title.label',
			'label' => __( 'Title label', 'site-reviews' ),
			'placeholder' => __( 'Title of your review', 'site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'content.label',
			'label' => __( 'Content label', 'site-reviews' ),
			'placeholder' => __( 'Your review', 'site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'name.label',
			'label' => __( 'Name label', 'site-reviews' ),
			'placeholder' => __( 'Your name', 'site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'email.label',
			'label' => __( 'Email label', 'site-reviews' ),
			'placeholder' => __( 'Your email', 'site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'textarea',
			'name'  => 'terms.label',
			'label' => __( 'Terms label', 'site-reviews' ),
			'placeholder' => __( 'This review is based on my own experience and is my genuine opinion.', 'site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'submit.label',
			'label' => __( 'Submit Button label', 'site-reviews' ),
			'placeholder' => __( 'Submit your review', 'site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->html->addfield( $formId, [
			'type'  => 'heading',
			'value' => __( 'Form Placeholders', 'site-reviews' ),
			'desc'  => __( 'Customize the placeholder text for the review submission form fields. Use a single space character to disable the placeholder text.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'title.placeholder',
			'class' => 'large-text',
			'label' => __( 'Title placeholder', 'site-reviews' ),
			'placeholder' => __( 'Summarize your review or highlight an interesting detail', 'site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'content.placeholder',
			'class' => 'large-text',
			'label' => __( 'Content placeholder', 'site-reviews' ),
			'placeholder' => __( 'Tell people your review', 'site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'name.placeholder',
			'class' => 'large-text',
			'label' => __( 'Name placeholder', 'site-reviews' ),
			'placeholder' => __( 'Tell us your name', 'site-reviews' ),
			'default' => ':placeholder',
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'email.placeholder',
			'class' => 'large-text',
			'label' => __( 'Email placeholder', 'site-reviews' ),
			'placeholder' => __( 'Tell us your email', 'site-reviews' ),
			'default' => ':placeholder',
		]);
	}
}
