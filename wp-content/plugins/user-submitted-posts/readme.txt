=== User Submitted Posts ===

Plugin Name: User Submitted Posts
Plugin URI: https://perishablepress.com/user-submitted-posts/
Description: Enables your visitors to submit posts and images from anywhere on your site.
Tags: frontend, submission, publish, upload, share,  community, content, custom fields, files, form, forms, front end, front-end, frontend content, frontend publishing, frontend uploader, generated, generated content, guest, images, login, post, posts, public, publishing, publishing, register, sharing, submit, submissions, submitted, uploader, user generated, user submit, user submitted, user-generated, user-submit, user-submitted, users, visitor
Author: Jeff Starr
Author URI: https://plugin-planet.com/
Donate link: https://m0n.co/donate
Contributors: specialk
Requires at least: 4.1
Tested up to: 4.8
Stable tag: 20170326
Version: 20170326
Text Domain: usp
Domain Path: /languages
License: GPL v2 or later

Easily submit posts and images from the front-end of your site.



== Description ==

**The #1 Plugin for User-Generated Content!**

User Submitted Posts (USP) adds a frontend form via template tag or shortcode that enables your visitors to submit posts and upload images. Just add the following shortcode to any Post, Page, or Widget:

`[user-submitted-posts]`

That's all there is to it! Your site now can accept user generated content. Everything is super easy to customize via Plugin Settings page. 

The USP Form includes the following fields:

* Name
* URL
* Email
* Post Title
* Post Tags
* Anti-Spam/Captcha
* Post Category
* Post Content
* Image Upload

USP Form fields may be set as required, optional, or disabled. You can set the Post Status of submitted posts as "Draft", "Publish Immediately", or publish after some number of approved posts. 

USP also enables users to upload multiple images when submitting a post. You control the min/max number of images and the min/max number of images that may be submitted.

*User Submitted Posts is the first and best plugin for front-end content!*


**Features**

* NEW! Google reCAPTCHA :)
* Let visitors submit posts from anywhere on your site
* Option to set submitted images as WP Featured Images
* NEW! Option to require users to be logged in to use the form
* Option to use WP's built-in rich text editor for post content
* Use template tag or shortcode to display USP form anywhere
* Stops spam via input validation, captcha, and hidden field
* Optionally include post author, title, tags, images, and more
* Redirect user to any URL or current page after post submission
* Includes template tags to display & customize submitted posts
* Display submission form via WP Text (and other) widgets
* Client-side validation with [Parsley](http://parsleyjs.org/)
* HTML5 submission form with streamlined CSS styles
* Option to require unique post titles
* Use your own custom form template and stylesheet
* 35 action/filter hooks for advanced customization
* Make form fields optional or required
* Auto Display Custom Fields and Images
* Shortcode to display all submitted posts

USP is simple to use and built with clean code via the WP API :)

**More Features**

* Translated into 10 languages
* Regularly updated to stay current with WordPress
* Option to receive email alerts for new submitted posts
* Option to set logged-in username as submitted-post author
* Option to set logged-in user&rsquo;s URL as the submitted URL
* Option to set a default submission category via hidden field
* Option to disable loading of external JavaScript file
* Option to specify URL for targeted resource loading
* Multiple emails supported in email alerts
* NEW! Option to disable tracking of IP addresses
* NEW! Option to specify custom email alert subject
* NEW! Option to specify custom email alert message


**Image Uploads**

* Optionally allow/require visitors to upload any number of images
* Specify minimum and maximum width and height for uploaded images
* Specify minimum and maximum allowed image uploads for each post
* Includes jQuery snippet for easy choosing of multiple images
* Automatically display submitted images


**Customization**

* Control which fields are displayed in the submission form
* Choose which categories users are allowed to select
* Assign submitted posts to any registered user
* Customizable success, error, and upload messages
* Plus options for the captcha, auto-publish, and redirect-URL
* Option to use classic form, HTML5 form, or disable only the stylesheet


**Post Management**

* Custom Fields saved w/ each post: name, IP, URL, and image URLs
* Set posts to any status: Draft, Pending, Publish, or Moderate
* One-click filtering of submitted posts on the Admin Posts page
* Includes template tags to display submitted images

Plus much more! Too many features to list them all :)

User Submitted Posts supports translation into any language. Current translations include:

* ar_AR : Arabic (Argentina)
* de_DE : German
* es_ES : Spanish (Spain)
* fa_IR : Persian
* fr_FR : French (France)
* it_IT : Italian
* nl_NL : Dutch
* pt_BR : Portuguese (Brazil)
* ro_RO : Romanian
* ru_RU : Russian
* sr_RS : Serbian
* zh_CN : Chinese (China)



**Pro Version**

**USP Pro** now available at [Plugin Planet](https://plugin-planet.com/usp-pro/)!

Pro version includes many, many more features and settings, with unlimited custom forms, infinite custom fields, multimedia file uploads, and much more. [Check it out &raquo;](https://plugin-planet.com/usp-pro/)



== Installation ==

**Installation**

1. Upload the plugin to your blog and activate
2. Visit the USP settings to configure your options

[More info on installing WP plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)


**Usage**

To display the form on any WP Post or Page, add the shortcode:

	[user-submitted-posts]

Or, to display the form anywhere in your theme, add the template tag:

	<?php if (function_exists('user_submitted_posts')) user_submitted_posts(); ?>


**Customizing the form**

There are three main ways of customizing the form:

* Plugin settings, you can show/hide fields, configure options, etc.
* Custom form template (see "Custom Submission Form" for more info)
* By using USP action/filter hooks (advanced)

USP Hooks:

`Filters:
usp_post_status
usp_post_author
usp_form_shortcode
usp_mail_subject
usp_mail_message
usp_new_post
usp_input_validate
usp_require_login
usp_default_title

Actions:
usp_submit_success
usp_submit_error
usp_insert_before
usp_insert_after
usp_files_before
usp_files_after`

Check out the [complete list of action hooks for User Submitted Posts](https://perishablepress.com/action-filter-hooks-user-submitted-posts/)

More info about [WordPress Actions and Filters](http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters)


**Custom Submission Form**

Out of the box, User Submitted Posts provides a highly configurable submission form. Simply visit the plugin settings to control which fields are displayed, set the Challenge Question, configure submitted images, and much more. 

There are situations, however, where advanced form configuration may be required. In order to allow for this, USP makes it possible to create a custom submission form. Here are the steps:

First, copy these two plugin files:

	/user-submitted-posts/resources/usp.css
	/user-submitted-posts/views/submission-form.php

Then, paste those two files into a directory named `/usp/` in your theme:

	/wp-content/themes/your-theme/usp/usp.css
	/wp-content/themes/your-theme/usp/submission-form.php

Lastly, visit the plugin settings and change "Form style" to "Custom Form + CSS". You now may customize the two files as desired; they will not be overwritten when the plugin is updated. 

Alternately, you can set the option "Form style" to "HTML5 Form + Disable CSS" to use the default USP form along with your own CSS. FYI: here is a list of [USP CSS selectors](https://m0n.co/e). 

Or, to go further with unlimited custom forms, [check out USP Pro](https://plugin-planet.com/usp-pro/) :)



**Displaying submitted posts**

User-submitted posts are handled by WordPress as regular Posts. So they are displayed along with other posts according to your WP Theme. Additionally, each submitted post includes a set of Custom Fields that include the following information:

* `is_submission` - indicates that the post is a user-submitted post
* `user_submit_image` - the URL of the submitted image (one custom field per image)
* `user_submit_ip` - the IP address of the submitted-post author
* `user_submit_name` - the name of the submitted-post author
* `user_submit_url` - the submitted URL

There are numerous ways to display these Custom Fields. The easiest way is to visit the plugin settings and configure the options available under "Auto-Display Content". There you can enable auto-display of submitted email address, URL, and images. Note that submitted images also are uploaded to the WP Media Library.

For more flexibility, you can use a variety of WP Template Tags (e.g., [get_post_meta()](https://codex.wordpress.org/Function_Reference/get_post_meta)) to display Custom Fields. Here are some tutorials for more information:

* [WordPress Custom Fields, Part I: The Basics](https://perishablepress.com/wordpress-custom-fields-tutorial/)
* [WordPress Custom Fields, Part II: Tips and Tricks](https://perishablepress.com/wordpress-custom-fields-tips-tricks/)

And here are some tutorials that may help with custom display of submitted images:

* [Display all images attached to post](https://wp-mix.com/display-images-attached-post/)
* [Display images with links](https://wp-mix.com/display-images-with-user-submitted-posts/)

Also, here is a [Helper Plugin to display Custom Fields](https://plugin-planet.com/usp-pro-custom-field-helper-plugin/). It originally is designed for use with USP Pro, but also works great with the free version of USP.


**Auto Display Images**

To automatically display submitted images on the frontend, visit the plugin settings, "Images Auto-Display" and select whether to display the images before or after post content. Save changes.


**Featured Images**

To set submitted images as Featured Images (aka Post Thumbnails) for submitted posts, visit the plugin settings, "Image Uploads" and check the box to enable "Featured Image". Save changes.


**Shortcodes**

User Submitted Posts includes two shortcodes:

* `[user-submitted-posts]` - displays the form on any Post or Page
* `[usp_display_posts]` - displays list of all submitted posts

The `[user-submitted-posts]` shortcode does not have any attributes. You simply include it wherever you want to display the form.

The `[usp_display_posts]` shortcode has two optional attributes, "userid" and "numposts". Examples:

* `[usp_display_posts userid="1"]` : displays all submitted posts by registered user with ID = 1
* `[usp_display_posts userid="Pat Smith"]` : displays all submitted posts by author name "Pat Smith"
* `[usp_display_posts userid="all"]` : displays all submitted posts by all users/authors
* `[usp_display_posts userid="all" numposts="5"]` : limit to 5 posts

By default `[usp_display_posts]` displays all submitted posts by all authors. So the attributes can be used to customize as desired. Note that the Pro version of USP provides many more options for the [display-posts shortcode](https://plugin-planet.com/usp-pro-display-list-submitted-posts/).


**Template tags**

USP also includes a set of template tags for customizing and displaying submitted posts:

	/*
		Display the USP Form
		Usage: <?php if (function_exists('user_submitted_posts')) user_submitted_posts(); ?>
	*/
	
	user_submitted_posts()
	
	
	
	/* 
		Check if post is a submitted post
		Returns true or false
		Usage: <?php if (usp_is_public_submission()) return true; ?>
	*/
	
	usp_is_public_submission()
	
	
	
	/* 
		Get all image URLs
		Returns an array of image URLs that are attached to the current submitted post
		Usage: <?php $images = usp_get_post_images(); foreach ($images as $image) echo $image; ?>
	*/
	
	usp_get_post_images()
	
	
	
	/* 
		Display all images
		Outputs a set of <img> tags for images attached to the current submitted post
		Usage: <?php usp_post_attachments($size, $beforeUrl, $afterUrl, $numberImages, $postId); ?>
		Parameters:
			$size         = image size: thumbnail, medium, large or full -> default = full
			$beforeUrl    = text/markup displayed before each image URL  -> default = <img src="
			$afterUrl     = text/markup displayed after each image URL   -> default = " />
			$numberImages = number of images to display for each post    -> default = false (display all)
			$postId       = an optional post ID to use                   -> default = uses global post
	*/
	
	usp_post_attachments()
	
	
	
	/* 
		Display submitted author name and URL
		This tag displays one of the following:
			- The author's submitted name as a link (if both 'User Name' and 'User URL' fields are included in the form)
			- The author's submitted name as plain text (if 'User Name' is included in the form, but 'User URL' is not included)
			- The author's registered username as a link to the author's post archive (if 'User Name' is not included in the form)
			
		Usage: <?php usp_author_link(); ?>
	*/
	
	usp_author_link()


These template tags should work out of the box when included in your theme template file(s). Keep in mind that for some of the tags to work, there must be some existing submitted posts and/or images available. 

The source code for these tags is located in `/library/template-tags.php`.


**Upgrades**

To upgrade User Submitted Posts, remove the old version and replace with the new version. Or just click "Update" from the Plugins screen and let WordPress do it for you automatically.

__Note:__ uninstalling the plugin from the WP Plugins screen results in the removal of all settings from the WP database. Submitted posts are NOT removed if you deactivate the plugin, reset default options, or uninstall the plugins; that is, all submitted posts must be removed manually.


**Restore Default Options**

To restore default plugin options, either uninstall/reinstall the plugin, or visit the plugin settings &gt; Restore Default Options.


**Uninstalling**

User Submitted Posts cleans up after itself. All plugin settings will be removed from your database when the plugin is uninstalled via the Plugins screen. Submitted posts are NOT removed if you deactivate the plugin, reset default options, or uninstall the plugins; that is, _all submitted posts must be removed manually_.


**Pro Version**

Pro version of USP now available! USP Pro includes many more awesome features and settings, with unlimited custom forms, infinite custom fields, multimedia file uploads, and much, much more.

* [Check out USP Pro for virtually limitless form-building action &raquo;](https://plugin-planet.com/usp-pro/) 
* [Read what users are saying about USP Pro &raquo;](https://plugin-planet.com/testimonials/)



== Upgrade Notice ==

To upgrade User Submitted Posts, remove the old version and replace with the new version. Or just click "Update" from the Plugins screen and let WordPress do it for you automatically.

__Note:__ uninstalling the plugin from the WP Plugins screen results in the removal of all settings from the WP database. Submitted posts are NOT removed if you deactivate the plugin, reset default options, or uninstall the plugins; that is, all submitted posts must be removed manually.



== Screenshots ==

1. USP Settings Screen (panels toggled closed)
2. USP Plugin Settings, showing default options (panels toggle open/closed)
3. USP Form (with all fields enabled)
4. USP Form (with just a few fields enabled)
5. Example showing how to display the form on a Page (using a shortcode)

More screenshots and infos available at the [USP Homepage](https://perishablepress.com/user-submitted-posts/)



== Frequently Asked Questions ==

**Can you add this feature or that feature?**

Please check the [Pro version of USP](https://plugin-planet.com/usp-pro/), which includes many of the most commonly requested features from users. The free version also may include new features in future updates.


**Images are not uploaded or displaying**

If everything is configured properly, USP will display submitted images on the front-end. If that is not happening, here are some things to check:

* Make sure that the setting "Images Auto-Display" is enabled
* And/or make sure that the setting "Featured Image" is enabled
* And/or make sure that your theme is set up to display submitted images

Assuming that everything is set up to display submitted images, here are some further things to check:

* Is there any error message when trying to submit an image? 
* Check that the submitted images are uploaded to the Media Library
* Check that the URL of the submitted image is attached to the submitted post as a Custom Field (on Edit Post screen)
* Check the permission settings on the upload folder(s) by ensuring that you can successfully upload image files directly via the Media Uploader
* Double-check that all the "Image Uploads" settings make sense, and that the images being uploaded meet the specified requirements

Note: when changing permissions on files and folders, it is important to use the least-restrictive settings possible. If you have to use more permissive settings, it is important to secure the directory against malicious activity. For more information check out: [Secure Media Uploads](https://digwp.com/2012/09/secure-media-uploads/)


**How to set submitted image as the featured image?**

Here are the steps:

1. Visit USP settings &gt; Options panel &gt; Image Uploads &gt; Featured Image
2. Check the box and click "Save Settings" to save your changes

Note that this setting merely assigns the submitted image as the Featured Image; it's up to your theme's single.php file to include `the_post_thumbnail()` to display the Featured Images. If your theme is not so equipped, [check out this tutorial at WP-Mix](https://wp-mix.com/set-attachment-featured-image/).


**How to require login?**

Visit the plugin settings and enable the option to "Require User Login". That will display the submission form only to logged-in users. To go further and require login for other types of content, there are many techniques available to you. For more information check out my WP-Mix post, [WordPress Require User Login](https://wp-mix.com/wordpress-require-user-login/), which provides a good summary of the possibilities. Also note: [USP Pro includes built-in shortcodes](https://plugin-planet.com/usp-pro-display-form-logged-in-users/) to display forms and other content to registered/logged-in users and/or guests/logged-out users.


**How do I change the appearance of the submission form?**

The easiest way to customize the form is via the plugin settings. There you can choose one of the following form configurations:

* HTML5 Form + Default CSS (Recommended)
* HTML5 Form + Disable CSS (Provide your own styles)
* Custom Form + Custom CSS (Provide your own form template & styles)

Additionally, you can configure the settings to show/hide specific fields, control the number and size of submitted images, customize the Challenge Question, and much more.

To go beyond what's possible with the plugin settings, USP enables creation of a custom submission form. To learn how, check out the "Custom Submission Form" section under [Installation](https://wordpress.org/plugins/user-submitted-posts/installation/). And for advanced customization, developers can use [USP action and filter hooks](https://perishablepress.com/action-filter-hooks-user-submitted-posts/).

Or, to go further with unlimited custom forms, [check out USP Pro](https://plugin-planet.com/usp-pro/) :)


**What about security and spam?**

User Submitted Posts uses the WordPress API to keep everything secure, fast, and flexible. The plugin also features a Challenge Question and hidden anti-spam field to stop automated spam and bad bots.


**How do I display success and error messages when the "Redirect URL" setting is enabled?**

Add the following template tag in your theme template, wherever you want to display the success/error messages: `<?php echo usp_redirect_message(); ?>`. You can then style the output via: `#usp-error-message` and `.usp-error` (for errors), and `#usp-success-message` (for the success message).


**Can I include video?**

The free version of USP supports uploads of images only, but some hosted videos may be included in the submitted content (textarea) by simply including the video URL on its own line. See [this page](http://codex.wordpress.org/Embeds) for more info. Note that [USP Pro](https://plugin-planet.com/usp-pro/) enables users to [upload video and much more](https://plugin-planet.com/usp-pro-allowed-file-types/#file-formats).


**How do I reset the plugin settings? Will it erase all of my submitted posts?**

To reset plugin settings to factory defaults:

1. Visit "Restore Default Options" in the plugin settings
2. Check the box and save your changes
3. Deactivate the plugin and then reactivate it
4. Plugin settings now are restored to defaults

And no, restoring default settings does not delete any submitted posts. Even if you completely remove the plugin, the submitted posts will not be deleted. You have to remove them manually, if desired.


**Wanted to be sure that the plugin does not require the exec() / exec.php or url_fopen functions, as both of those have been disabled on our host server for security reasons.**

User Submitted Posts does not use any of "the exec() / exec.php or url_fopen functions". So you're good to go.


**The Name and URL fields are not displayed in the form, even though they are set to display in the plugin options.**

The setting "Registered Username" when enabled will automatically hide the Name field. Likewise the setting "User Profile URL" when enabled will automatically hide the URL field. Try enabling these settings and refreshing the form page.


**I'm new to WordPress and just installed your plugin User Submitted posts. What template do I add the code to have it work everywhere.**

It really depends on the theme, as each tends to use template files differently.. it also depends on where on the page you would like to display the form, for example the sidebar (sidebar.php), the footer (footer.php), and so forth. Also, chances are that you'll need to add the form to more than one template file, for example index.php and page.php, etc. A good first place to try would be the sidebar, or maybe index.php and then go from there.


**I have the option for multiple image uploads enabled in the plug-in settings however it does not work on the site. When you click on the Add another image text nothing happens.**

The "Add another image" link is dependent on the required JavaScript being included in the page. Check the plugin setting to "Include JavaScript?" and you should be good to go.


**I really like the new Rich Text editor, but the Add Media button only shows up if I'm logged in to the site, and so nobody else can see it. Is there a way to change that so that all readers wanting to submit something can use that button?**

As far as I know the user must be logged in to have access to file uploads, Media Library and the uploader. This is a security measure aimed at preventing foul play. The Pro version of USP, however, provides an option to [enable Add Media uploads for all user levels](https://plugin-planet.com/usp-pro-enable-non-admin-users-upload-media/).


**I have it set so that articles get submitted under the users name. Sometimes when a user submits an article the article gets submitted under the users name, and other times set as the default user as set in the settings. When the article gets set to the default user I cannot change it in wordpress, I need to copy and paste the whole article to a new article and then set it to the proper user.**

The registered username of the submitter can be used for post author only when the user is logged in to WordPress (otherwise it's impossible for WordPress to know their identity). Thus, to resolve the dilemma posed in the question, one solution is to require users to be logged in to submit posts. See the previous Q&amp;A, "How to require login?". See also the related settings, "Registered Username" and "User Profile URL".


**Can you explains how the setting 'Registered Username' works?**

Yes, here is a summary:

* When "Registered Username" is enabled:
	* If the user is logged in, their registered username is used as the Post Author
	* If the user is logged out, the setting "Assigned Author" is used as the Post Author
* When "Registered Username" is disabled:
	* The setting "Assigned Author" always is used as the Post Author for all users (whether logged in or not)


**When displaying the post author and author URL for submitted posts, my theme uses the default assigned author. How can I change it so that my theme uses the submitted author name and author URL instead of the default assigned author?**

This functionality is built in to the Pro version of USP, but it's also possible using either of the following methods:

* Replace your theme's current author tags with USP's `usp_author_link()`
* By adding some custom code to your theme's functions.php file. If interested, please [contact me directly](https://perishablepress.com/contact/) and I will send the code and steps to implement.


**Why doesn't the USP shortcode work when added to the WP Text widget?**

By default, WordPress does not enable shortcodes in widgets. I have added a plugin setting called "Enable Shortcodes" that will enable any/all shortcodes to work in widgets. Enable that setting and you should be good to go. Note: the "Enable Shortcodes" setting applies to all shortcodes, even those of other plugins. Check out WP-Mix for more information on [enabling shortcodes in widgets](https://wp-mix.com/enable-shortcodes-widgets/).


**How can I change the default Post Title?**

When the Post Title field is not included in the submission form, USP automatically uses the default: "User Submitted Post". To customize the default Post Title, you can use the provided USP filter hook, `usp_default_title`. Here is an example: `function usp_customize_default_title($title, $time) { return $title .' - '. $time; } add_filter('usp_default_title', 'usp_customize_default_title', 10, 2);`. This will append a unique date/time string to the default Post Title.


**How can I translate this plugin?**

Currently the easiest and most flexible method is to [use GlotPress to translate USP](https://translate.wordpress.org/projects/wp-plugins/user-submitted-posts). That is the recommended translation route going forward, but for the time being you may also translate using a plugin such as [Loco Translate](https://wordpress.org/plugins/loco-translate/). FYI, USP's translation files are located in the `/languages/` directory.


**How can I customize the login-required message?**

When the setting "Require User Login" is enabled, and the user is not logged in, they will see a message that says, "Please log in to submit content!". To customize this, add the following code to your theme's functions.php file: `function usp_customize_login_required_message($message) { return '<p>Please <a href="http://example.com/wp-login.php">register</a> to submit content.</p>'; } add_filter('usp_require_login', 'usp_customize_login_required_message');`. Then customize the return line however is desired. This trick uses USP's `usp_require_login` [hook](https://codex.wordpress.org/Plugin_API/Hooks).


**My form has lots of extra spacing between each field, how to fix?**

There are numerous reasons why a form might be displayed with too much spacing between the fields. Here are some possible things to look at:

* Theme CSS may be interfering with its own styles; solution: examine theme styles and edit as needed.
* The USP shortcode may be wrapped with code tags; solution: remove the code tags.
* There may be interference from some other plugin/theme; solution: [troubleshoot your plugins and themes](https://perishablepress.com/how-to-troubleshoot-wordpress/) (check out the sections on troubleshooting plugins and themes).


**The height of my form fields are all messed up, how to fix?**

USP provides its own, very minimal styles. Nothing that sets the height for any form fields. So if your field heights look weird, most likely your theme or a plugin is adding its own CSS styles. To resolve, you can try adding this bit of CSS to your stylesheet: `#usp_form .usp-input, #usp_form .usp-select, #usp_form .usp-textarea, #usp_form .usp-submit { height: initial; }`. That line basically resets the height property of all form fields to the original value. So it should override any other height styles.


**How can I display the form again after the user has successfully submitted a post?**

Follow the steps to create a custom form, but use `submission-form-alt.php` instead of `submission-form.php`. Note the alternate form template still needs a way to clear cookies after successful form submission, so not recommended for public sites.


**What is the plugin setting for the 'From' email header?**

That setting enables you to customize the address used as the "From" header for email messages. If your email address is a domain-based address, then this setting should be the same as the previous Email setting. Otherwise, if you are using a 3rd-party email service, this setting should be a local, domain-based address. If you find that email messages are getting sent to the spam bin, this setting may help.


**Is it possible to set up conditional redirects?**

I haven't tried it myself, but it might be possible. In the form code, there is a hidden input that specifies the redirect URL. The hidden input has a name attribute with a value of `redirect-override`. So you could target this input with some JavaScript, and then change the value of the hidden field based on whatever criteria you need.


**Questions? Feedback?**

Send any questions or feedback via my [contact form](https://perishablepress.com/contact/). Thanks! :)



== Support development of this plugin ==

I develop and maintain this free plugin with love for the WordPress community. To show support, you can [make a cash donation](https://m0n.co/donate), [bitcoin donation](https://m0n.co/bitcoin), or purchase one of my books: 

* [The Tao of WordPress](https://wp-tao.com/)
* [Digging into WordPress](https://digwp.com/)
* [.htaccess made easy](https://htaccessbook.com/)
* [WordPress Themes In Depth](https://wp-tao.com/wordpress-themes-book/)

And/or purchase one of my premium WordPress plugins:

* [BBQ Pro](https://plugin-planet.com/bbq-pro/) - Pro version of Block Bad Queries
* [Blackhole Pro](https://plugin-planet.com/blackhole-pro/) - Pro version of Blackhole for Bad Bots
* [SES Pro](https://plugin-planet.com/ses-pro/) - Super-simple &amp; flexible email signup forms
* [USP Pro](https://plugin-planet.com/usp-pro/) - Pro version of User Submitted Posts

Links, tweets and likes also appreciated. Thanks! :)



== Changelog ==

**20170326**

* Adds Google reCAPTCHA (anti-spam) field
* Adds function to clear form cookies if user logs out
* Adds new Arabic translation (Thanks to Abdeslam Lachhab)
* Adds new french translation by Milehan
* Improves plugin documentation
* Changes "USP" post-filter button to display for WP Posts
* Adds option to send HTML-format email alerts
* Fixes undefined variable notice for filter link
* Adds `%%title_parent%%` for auto-display images
* Adds `%%title%%` for auto-display email and url
* Adds class `.usp-submit` to submit button
* Fixes email validation bug when email optional
* Adds filter hook `usp_editor_content` for `wp_editor()`
* Adds filter hook `usp_return_form` for return link
* Adds fallback for `wp_add_inline_script` for WP &lt; 4.5
* Tweaks sanitization of post content to improve security
* Updates show support panel in plugin settings
* Reorders plugin action links
* Improves default options functionality
* Replaces global `$wp_version` with `get_bloginfo('version')`
* Adds option to customize the "From" email address for alerts
* Refines display of settings panels
* Adds new IP-detection script
* Generates new default translation template
* Tests on WordPress version 4.8

**20161122**

Important! The `/custom/` directory is deprecated. If you are using a custom form template, please move it to `/wp-content/your-theme/usp/`. For more information, check out the "Custom Submission Form" section under [Installation](https://wordpress.org/plugins/user-submitted-posts/installation/).

* Changed `get_template_directory()` to `get_stylesheet_directory()` in `usp_display_form()`
* Changed `get_template_directory()` to `get_stylesheet_directory()` in `usp_enqueueResources()`
* Changed `get_template_directory_uri()` to `get_stylesheet_directory_uri()` in `usp_enqueueResources()`

**20161119**

Important! The `/custom/` directory is deprecated. If you are using a custom form template, please move it to `/wp-content/your-theme/usp/`. For more information, check out the "Custom Submission Form" section under [Installation](https://wordpress.org/plugins/user-submitted-posts/installation/).

* Moved custom form template to theme directory
* Refactored and combined `usp_js_vars()` with `usp_enqueueResources()`
* Refactored `usp_load_admin_styles()`
* Refactored `add_usp_links()` function
* Refined `usp_display_form()` function
* Removed `usp_editor_style()` and file
* Removed deprecated `usp_currentPageURL()`
* Removed filter hook `usp_current_page`
* Fine-tuned submission form and styles
* Improved default styling of WP RTE field
* Reorganized and refined plugin settings page
* Upgraded Parsley.js to version 2.6.0
* Deprecated JS function `usp_check_files()`
* Bugfix: conflict with "Require User Login" and "Registered Username"
* Bugfix: conflict with "Require User Login" and "User Profile URL"
* Bugfix: support nag re-appears after each settings save
* Added `usp_default_title` filter hook for default post titles
* Changed form label from "Post URL" to "User URL" on plugin settings page
* Added missing parameter to `usp_check_images()`
* Reorganized and streamlined plugin file structure
* Streamlined some global variables
* Added some missing translation strings
* Updated plugin author URL
* Updated Twitter URL to https
* Changed stable tag from trunk to latest version
* Updated URL for rate this plugin links
* Regenerated default translation template
* Tested on WordPress version 4.7 (beta)

**20160815**

* Fine-tuned the plugin settings page
* Replaced `_e()` with `esc_html_e()` or `esc_attr_e()`
* Replaced `__()` with `esc_html__()` or `esc_attr__()`
* Added plugin icons and larger banner image
* Improved translation support
* Added more allowed tags and attributes to relevant plugin settings
* Removed usp_addNewPostStatus() function (deprecated hook)
* Fixed bug where required URL field shows error if User Profile URL enabled
* Added more allowed tags and attributes to submitted post content
* Refined logic of usp_checkForPublicSubmission() function
* Added usp_redirect_message() for error messages when Redirect URL enabled 
* Changed hook priority for usp_checkForPublicSubmission() function
* Added Russian translation (thanks to [Nick Lysenko](https://twitter.com/unbirth7))
* Added setting to display form only to logged-in users
* Fine-tuned default form styles and error messages
* Generated new translation template
* Tested on WordPress 4.6

**20160411**

* Cleaned up plugin tags
* Added new Dutch translation (thanks to [Berend](http://botoboto.com/))
* Added Italian translation (thanks to [Rosario](http://rosariomonaco.com/))
* Replaced icon with retina version
* Added screenshot to readme/docs
* Added retina version of banner
* Reorganized and refreshed readme.txt
* Tested on WordPress version 4.5 beta

**20160215**

* Fixes XSS vulnerability (thanks to [Panagiotis Vagenas](https://twitter.com/panVagenas))
* Updates descriptions for settings "Registered Username" and "User Profile URL"
* Adds `[usp_display_posts]` shortcode to display list of user submitted posts
* Adds UTF-8 default parameter to get_option('blog_charset')
* Replaces get_currentuserinfo() with wp_get_current_user()
* Removes quotes from charset in email headers
* Adds screenshots to readme.txt/documentation
* Cleans up readme.txt/documentation
* Tested on WordPress 4.5 alpha

**20151113**

Note: the CSS and JavaScript for the plugin settings page is now moved to their own external files. Please clear your browser cache and/or force refresh the settings page to load the new files!

* Added options to auto-display custom fields and images
* "USP" button on Posts screen now displays all USP Posts (not just Pending)
* Added Dutch translation (Thanks to [Erik Kroon](http://www.punchcreative.nl/))
* Added German translation (Thanks to [Michael](https://wordpress.org/support/topic/image-problem-german-translation))
* Added check for `$post` in `usp_is_public_submission()`
* Removed width from `a#usp_add-another` in `usp.css`
* Fixed custom markup for "Add Another" link
* Added option to show fields but not require
* Added `usp_check_required()` function
* Added setting to enable shortcodes in widgets
* Added `get_currentuserinfo()` where required
* Added `esc_url()` to sanitize URI strings
* Removed unnecessary `mail()` headers (Thanks to [Jason Hendriks](http://www.codingmonkey.ca/))
* Refined `usp_send_mail_alert()`
* Fixed sending alerts to multiple email addresses
* Added option to disable tracking of IP addresses
* Added option to specify customize email alert subject
* Added option to specify customize email alert message
* Reorganized and streamlined settings page
* Added option to disable default USP styles for custom forms
* Replaced USP graphics with retina versions
* Added `usp_load_admin_styles()` to enqueue settings styles
* Replaced `load_custom_admin_css()` with `usp_load_admin_styles()`
* Moved JavaScript and CSS to their own external files
* Added option to publish as "Draft" Post Status
* Removed deprecated `usp_answer` and `usp_form_width` options
* Added more attributes to `$allowed_atts`
* Added `usp_form_display_options()`
* Added `usp_auto_display_options()`
* Added hooks: 
	* `usp_post_draft`
	* `usp_image_args`
	* `usp_image_title`
	* `usp_image_thumb`
	* `usp_image_medium`
	* `usp_image_large`
	* `usp_image_full`
	* `usp_image_custom_size`
	* `usp_image_custom`
	* `usp_email_custom_field`
	* `usp_url_custom_field`
* Added `usp_auto_display_images()`, `usp_auto_display_email()`, `usp_auto_display_url()`
* Added `usp_replace_image_vars()`
* USP Meta Box not displayed if no data to display
* Fixed bug with targeted loading of USP stylesheet
* Updated heading hierarchy in plugin settings
* Updated translation template file
* Updated minimum version requirement
* Tested on WordPress 4.4 beta

**20150808**

* Tested on WordPress 4.3
* Updated minimum version requirement

**20150507**

* Tested with WP 4.2 + 4.3 (alpha)
* Changes a few "http" links to "https"
* Fixes XSS vulnerability with add_query_arg()
* Adds isset() to stop some minor PHP warnings
* Fixes mixed content warning for https sites
* Adds support for exif_imagetype when needed
* Adds Arabic translation, thanks to Amine CH
* Adds Spanish translation, thanks to Clara Roldán

**20150319**

* Tested with latest version of WP (4.1)
* Increases minimum version to WP 3.8
* Removes deprecated screen_icon()
* Adds $usp_wp_vers for version check
* Streamline/fine-tune plugin code
* Adds Text Domain and Domain Path to file header
* Adds alert panel to plugin settings page
* Adds Serbo-Croatian translation - thanks [Borisa Djuraskovic](http://www.webhostinghub.com/)
* Adds Chinese translation - thanks Xing
* Improves error handling
* Improves post author process
* Improves post-submission process
* Improves code in submission-form.php
* Adds nonce security to submission process
* Adds proper headers to email alert
* Adds Email field to the form (hidden by default)
* Adds USP Info meta box to Post Edit screen (Props: Nathan Clough)
* Adds specific error messages for fields/files (e.g., min, max, required)
* Adds option to disable required attributes
* Adds usp_post_status filter hook
* Adds usp_file_key filter hook
* Adds usp_post_data filter hook
* Adds usp_editor_settings filter hook
* Adds usp_error_message filter hook
* Adds usp_post_moderate filter hook
* Adds usp_post_publish filter hook
* Adds usp_post_approve filter hook
* Adds drag_drop_upload to visual/rich-text editor
* Adds option to require unique post titles
* Changes approved-post count to check for name/IP instead of URL/IP
* Changes class .hidden to .usp-hidden in default submission form
* Changes class .no-js to .usp-no-js in default submission form
* Changes class .js to .usp-js in default submission form
* Replaces sanitize_text_field() with esc_url() for URL field
* Replaces default .mo/.po templates with .pot template
* Fixes bug where encoded characters are removed from URL
* Fixes various bugs and PHP notices

**20140930**

* Removes required attribute from default form textarea
* Removes "exclude" from type on redirect-override in default form
* Adds class "exclude" and "hidden" to redirect-override in default form

**20140927**

* Tested on latest version of WordPress (4.0)
* Increases min-version requirement to 3.7
* Improves layout and styles of plugin settings page
* Adds Romanian translation - thanks [Hideg Andras](http://www.blue-design.ro/)
* Adds Persian (Farsi) translation - thanks [Pooya Behravesh](http://icomp.ir/)
* Adds French translation - thanks [Mirko Humbert](http://www.designer-daily.com/) and [Matthieu Solente](http://copier-coller.com/)
* Updates default mo/po translation files
* Updates Parsley.js to version 2.0
* Updates usp.css with styles for Parsley 2.0
* Updates captcha-check script for Parsley 2.0
* Updates markup in default form for Parsley 2.0
* Replaces call to wp-load.php with wp_print_scripts
* Replaces sac.php with individual JavaScript libraries
* Improves logic of usp_enqueueResources() function
* Improves logic of min-file check JavaScript
* Removes ?version from enqueued resources
* Adds option to use "custom" form and stylesheet
* Removes deprecated "classic" form, submission-form-classic.php and usp-classic.css
* Removes `novalidate` from default form
* Removes `data-type="url"` from default form
* Removes `.usp-required` classes from default form
* Removes `id="user-submitted-tags"` from default form
* Removes `<div class="usp-error"></div>` from default form
* Adds "Please select a category.." to category select field
* Updates CSS for default form, see list at https://m0n.co/e
* Replaces some stripslashes() with sanitize_text_field()
* Replaces some htmlentities() with sanitize_text_field()
* Fixes bug where too big/small images would not trigger error
* Adds post id and error as query variable in return URL
* Adds sanitize_text_field() to usp_currentPageURL()
* Adds the following filter hooks:
	* `usp_post_status`
	* `usp_post_author`
	* `usp_form_shortcode`
	* `usp_mail_subject`
	* `usp_mail_message`
	* `usp_new_post`
	* `usp_input_validate`
* Adds the following action hooks:
	* `usp_submit_success`
	* `usp_submit_error`
	* `usp_insert_before`
	* `usp_insert_after`
	* `usp_files_before`
	* `usp_files_after`
	* `usp_current_page`

**20140308**

* usp_require_wp_version() now runs only on plugin activation

**20140123**

* Tested with latest version of WordPress (3.8)
* Added trailing slash to load_plugin_textdomain()
* Increased WP minimum version requirement from 3.3 to 3.5
* Added popout info about Pro version now available
* Added Spanish translation; thanks to [María Iglesias](http://www.globalcultura.com/)
* Change CSS for "USP" button to display after the "Filter" button on edit.php
* Added 8px margin to "Empty Trash" button on the Post Trash screen
* Changed handle from "uspContent" to "uspcontent" for wp_editor()
* Added class ".usp-required" to input fields (for use with JavaScript)
* Fixed issue of submitted posts going to Trash when a specific number of images is required AND the user submits the form without selecting the required number of images. JavaScript now checks for required image(s) and will not allow the form to be submitted until the user has selected the correct number of images.
* Improved logic responsible for displaying file input fields and the "Add Another Image" button
* Added option to display custom markup for "Add Another Image" button
* Replaced select fields with number inputs for settings "minimum/maximum number of images"
* Added `href`, `rel`, and `target` attributes to $allowed_atts
* Made default options translatable, generated new mo/po templates
* Streamlined plugin settings intro panel

**20131107**

* Added i18n support
* Added uninstall.php file
* Removed "&Delta;" from `die()`
* Added "rate this plugin" links
* Added Brazilian Portuguese translation; thanks to [Daniel Lemes](http://www.tutoriart.com.br/)
* Added notes about support for multiple email addresses for email alerts
* Increased `line-height` on settings page `<td>` elements
* Added `.inline` class to some plugin settings
* Changed CSS for `#usp_admin_filter_posts` in usp-admin.css
* Changed link text on Post filter button from "User Submitted Posts" to "USP"
* Fixed backwards setting for captcha case-sensitivity
* Added `is_object($post)` to `usp_display_featured_image`; Thanks to [Larry Holish](holish.net)
* Changed `application/x-javascript` to `application/javascript` in usp.php
* Removed `getUrlVars` function and changed "forget input values" to use a simpler regex; Thanks to [Larry Holish](holish.net)
* Tricked out `wp_editor` with complete array in both submission-form files
* Added note on settings screen about deprecating the "classic" submit form
* Replaced `wp-blog-header.php` with `wp-load.php` in usp.php
* Improved sanitization of POST variables
* Added check for empty content when content textarea is displayed on form
* Removed closing `?>` from user-submitted-posts.php
* Tested with latest version of WordPress (3.7)
* Fleshed out readme.txt with even more infos
* General code cleanup and maintenance

**20130720**

* Added option to set attachment as featured image
* Improved localization support (.mo and .po)
* Added optional use of WP's built-in rich text editor
* Added custom stylesheet for WP's rich text editor
* Replace antispam placeholder in submission-form.php
* Improved jQuery for "add another image" functionality
* Added jQuery script to remember form input values via cookies
* Added data validation for input fields via Parsley @ http://parsleyjs.org
* Overview and Updates panels now toggled open by default
* Updated CSS styles for HTML5 and Classic forms
* Improved logic for form verification JavaScript
* Resolved numerous PHP notices and warnings
* Updated readme.txt with more infos
* General code check n clean

**20130104**

* Added explanation of plugin functionality in readme.txt
* Fixed character encoding issue for author name
* Added margins to submit buttons (to fix WP's new CSS)
* Removed "anti-spam" text from captcha placeholder attribute
* usp_post_attachments() tag now accepts custom sizes
* Added temp fix for warning: "getimagesize(): Filename cannot be empty"
* Restyled USP filter button on admin Posts pages

**20121120**

* added id to tag input field in submission-form.php
* enabled option to disable loading of external JavaScript file
* enabled option to specify URL for targeted resource loading
* added `fieldset { border: 0; }` to usp.css stylesheet
* increased width of anti-spam input field (via usp.css)
* changed the order of input fields in submission-form.php
* fixed loading of resources on success and error pages
* added field for custom content to display before the USP form
* enable HMTL for success, error, and upload messages
* fixed issue with content not getting included in posts

**20121119**

* increased default image width and height
* comment out output start in three files
* remove echo output for input value attributes
* cleaned up placeholders with clearer infos
* remove usp_validateContent() function
* remove conditional if for content in usp_checkForPublicSubmission() [1]
* [1] default text no longer added to posts when empty
* remove content validation in usp_createPublicSubmission()
* added option to receive email alert for new submissions
* added option to set author as current user
* added option to set author url as usp url
* added option to set category as hidden
* submission-form.php &amp; submission-form-classic.php: changed markup output for success &amp; error messages

**20121108**

* Fixed non-submission when title and other fields are hidden

**20121107**

* Rebuilt plugin and optimized code using current WP API
* Redesigned settings page, toggling panels, better structure, more info, etc.
* Errors now redirect to specified page (if set) or current page
* Fixed bug to allow for unlimited number of uploaded images
* Cleaned up template tags, added inline comments
* Optimized/enhanced the user-submission form
* Added option to restore default settings
* Added settings link from Plugins page
* Renamed CSS and JavaScript files
* Added challenge question captcha
* Added hidden field for security
* Added option for custom success message
* Submission form now retains entered value if error
* Added placeholder attributes to the form fields
* Submissions including invalid upload files now redirect to form with error message
* Fixed default author of submitted posts
* the_author_link is not filterable, so created new function usp_author_link
* moved admin styles from form stylesheet to admin-only stylesheet
* Added new HTML5 form and stylesheet, kept originals as "classic" version

**1.0**

* Initial release


