=== APH Merge Scripts ===
Contributors: agusph
Tags: minify, merge scripts, merge styles, combine styles, combine scripts, queue
Requires at least: 3.5
Tested up to: 4.7
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Merge and minify CSS & javascript files into one file. Easy to use. Support remote file - Javascript & CSS files hosted on other server or CDN

== Description ==

Combining and minifying javascript dan stylesheet files can improve the speed the website as it will reduce the number of HTTP Request and reduce the size of the downloaded file.

This plugin helps you to do all of that. It will automatically detect and combine javascript and CSS files loaded by a page, into a single file javascript and CSS.

** Features. **

* Easy to use.
* Merge all CSS and Javascript files into one file - Automatically detect scripts and styles that are required on a page, and then combine them into one file.
* There is a tool in the admin bar that makes it easy to add / manage scripts loaded by a page.
* Support localized script.
* Support remote scripts (Javascript and CSS files hosted on CDN).  Built-in parallel download for optimum download time.
* Support YUI Compressor. We don't use closure compiler as it is not 100% safe. <a href="http://webdevcorner.com/compress-javascript-file-using-closure-compiler/#compress-file" target="_blank">read here</a> 
* CSS & Script files URL cleaned from query string, leverage browser cache.
* Easy to view and manage merged files.
* There is an option to put merged script to the header or footer
* Automatically unload CSS and Javascript files that are not found

== Installation ==

Install & activate the plugin. Modify some options in the settings page to suits your need.

== How To Use ==

While loading a page, the plugin will automatically detect the scripts and styles used on the page using WP enqueue system.

However, in certain circumstances, there are scripts and styles that are not detected automatically, such as akismet-form that usually comes late in the wp_footer

To check that kind of scripts, first, enable "Show Manage Scripts Menu on the Admin Bar" option in the plugin settings page. This option will add a menu to the admin-bar menu. This menu will appear when you open any page except for the admin page. Now, through that menu, you can choose scripts you want to merge or exclude easily.

== Screenshots ==

1. Admin Settings
2. Manage scripts loaded in a page through admin bar

== Changelog ==

= 1.3.1 - September 4, 2016 =
* Fix chahe check issue

= 1.3 - September 1, 2016 =
* Add support to remote scripts (Javascript and CSS files hosted on other servers or CDN)
* Add option to manage remote files
* Add option to 404 page
* Fix some issues
* Minor performance improvements

= 1.2 - August 21, 2016 =
* Remove "File name & location" option, as now, we use predefined file name + hash to force user to download the latest merged file
* Disable creating merged file on 404 HTTP Response, as it caused some problems when WordPress load non-existing CSS or js files
* Use JShring as default Javascript Minifier, as it safer than the current one.
* Fix issues on "APH Show Scripts" Menu on Admin Bar
* Add "Show Handles" link to show handles of merged files
* Fix other issues

= 1.1 - July 31, 2016 =
* Add support to YUI Compressor
* Remove query string from CSS and javascript's file URL
* Fix some issues
* Add uninstall file
* Rewrite source code to meet WordPress coding standard
* Change admin options interface

= 1.0 =
Initial Public Release