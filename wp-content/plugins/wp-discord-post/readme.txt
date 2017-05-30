=== WP Discord Post ===
Contributors: nicolamustone
Tags: discord, post, publish, server, chat, gaming, streaming, twitch, community, blog
Requires at least: 4.4
Tested up to: 4.7.3
Stable tag: 1.0.5
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WP Discord Post uses a Discord bot and Webhook URL to write in a channel in a Discord server when a post is published on a blog.

== Description ==

WP Discord Post is a free plugin for WordPress that uses a Discord bot and Webhook URL to write in your desired channel in your Discord server whenever a new post is published on your blog.

You can configure it by going to Settings > General > WP Discord Post and filling in all the details. The fields are all required. Click on the links “Learn more” in the description of the fields to learn how to get the necessary data.

== Installation ==

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of WP Discord Post, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “WP Discord Post” and click Search Plugins. Once you’ve found the plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading this plugin plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Changelog ==

= 1.0.5 =
* Added option to mention @everyone in Discord. Activate it from Settings > General.

= 1.0.4 =
* Removed quotes for the post title. They are only causing issues.

= 1.0.3 =
* Replace &quot; entity from the message sent to Discord with a plain “ (quote symbol). Discord does not convert HTML entities to their respective symbol.

= 1.0.2 =

* Fixed a typo in the message sent to Discord.

= 1.0.1 =
* Added the article title in the message sent to Discord.
* Added the filter `wp_discord_request_args` to filter the request arguments before to send it to Discord.

= 1.0.0 =

* First release!
