=== Post Grid & List for WordPress - Content Views ===
Contributors: PT Guy
Donate link: https://www.contentviewspro.com/pricing/?utm_source=wporg&utm_medium=link&utm_campaign=donate
Tags: post, posts, page, pages, grid, author, category, categories, tag, responsive, title, thumbnail, content
Requires at least: 3.3
Tested up to: 4.7.5
Stable tag: 1.9.8.21
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display recent or any posts by category, tag, author, ID in responsive grid, list layout easier than ever, without coding.

== Description ==

Content Views helps you to display any WordPress posts on any page, in responsive grid and list easily, without coding:

* display recent posts in a second
* display posts on page, sidebar widgets, theme file
* display posts by category, tag, author, ID, keyword, status
* display pages by author, ID, keyword, status
* display children of a parent page
* sort posts by title, date, ID
* display any post data: featured image, title, full content or excerpt, meta fields (date, author, category, tag, comment count)
* display featured image in any size (thumbnail, medium, large, full...)
* limit number of posts to display
* enable/disable pagination (ajax, non-ajax)
* custom actions, filters hook for developers

It provides you a friendly form to filter & display WordPress posts quickly, in 3 simple steps:

* Step 1: Filter any posts (by ID, category, tag, author, keyword, status)
* Step 2: Select layout (grid, scrollable list, collapsible list) to display your posts. You can change grid columns, select post data to display (title, featured image, full content or excerpt, meta fields), enable pagination...
* Step 3: Paste generated shortcode to anywhere you want (page content, text widget, theme template file...)


[youtube https://www.youtube.com/watch?v=drxqtCiaw4I]


= Premium features: =

* More amazing layouts: Pinterest, Masonry, Facebook Timeline...
* Unlimited output with drag & drop, custom font, unlimited color...
* Replace layout of Category page, Blog page, Search result page, Archive page... by amazing layout
* Support custom post type plugins: WooCommerce, Easy Digital Downloads, Events Manager...
* Support custom field plugins: Advanced Custom Fields, Pods framework, Toolset Types...
* Support membership plugins: Paid Memberships Pro, Members, Ultimate Member
* Support translation plugins: WPML, Polylang, qTranslate
* Filterable output by categories, tags... with cool animation
* Nice animation on mouse over featured image
* Ajax pagination: load more, infinite scroll
* And much more...

[Get Content Views Pro](https://www.contentviewspro.com/?utm_source=wordpress&utm_medium=plugin&utm_campaign=content-views "Get Content Views Pro").

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Content Views'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `content-views-query-and-display-post-page.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `content-views-query-and-display-post-page.zip`
2. Extract the `content-views-query-and-display-post-page` directory to your computer
3. Upload the `content-views-query-and-display-post-page` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard



== Frequently Asked Questions ==

= How to start? =

In WordPress Admin menu, click Content Views > Add New

= What is 'View'? =

'View' is a custom post type which Content Views plugin stores all settings to filter & display your posts

= How many Views I can create? =

You can create Unlimited Views, in Unlimited websites

= How to edit a View? =

In WordPress Admin menu, click Content Views. Paste View ID or title to text field beside "Search Views" button, then click the button to search.
Click on Title of View you want to edit.
You will be forwarded to View page.

= How to use View on my home page? =

If you are using a static page as home page, you should paste shortcode **[pt_view id="VIEW_ID"]** to editor of that page.
If you are using "Your latest posts" as home page, you should paste this code `<?php echo do_shortcode("[pt_view id=VIEW_ID]"); ?>` to a theme file: `front-page.php` or `home.php` or `index.php`.



== Screenshots ==

1. Content Views plugin overview
2. Display Setting form to customize output of queried posts at front-end
3. Query and display in Grid layout (Show Title, Thumbnail)
4. Query and display in Grid layout (Show Title, Thumbnail, Content) with Pagination
5. Query and display in Collapsible layout
6. Query and display in Slider layout



== Changelog ==

= 1.9.8.21 - 2017/05/24 =
* New: Add setting to not wrap text around image (when show thumbnail on the left/right of text)
* Tweak: Add class to each pagination item
* Tweak: Change class of scrollable carousel element
* Tweak: Update README.txt
* Dev: Add some filters

= 1.9.8.1 - 2017/04/26 =
* Fix: Compatible with latest version 2.5.2 of **Page Builder by SiteOrigin** plugin (excerpt of posts built with that plugin is invalid after update)
* Tweak: Add `pt_cv_taxonomy_settings` filter to modify taxonomy settings

= 1.9.8 - 2017/04/17 =
* Update: Remove default font-size of Title
* Fix: Sort by functionality doesn't work correctly with "Simple Custom Post Order" plugin active
* Fix: View output doesn't appear correctly in page created by OptimizePress plugin

= 1.9.7 - 2017/03/28 =
* New: Add setting to change HTML tag for Title
* Update: Show message in Preview panel when enabled Pagination but there is only 1 page
* Tweak: More useful information in Content Views >> Settings page
* Improvement: Refactor some code to improve performance

= 1.9.6 - 2017/03/08 =
* Fix: Shortcode of "Visual Composer" plugin is visible in content of posts loaded by Ajax pagination
* Fix: Show wrong order of posts, caused by impact of "Post Types Order" plugin
* Update: Add Romania translation, thanks to "Dan Caragea"
* Update: Add Dutch translation, thanks to "At Voogt"
* Update: Add Chinese translation, thanks to "JIGE-HNM (iojpe dot com)"
* Update: Add Russian translation, thanks to @snakecom
* Tweak: Add some filters to customize authors list, thumbnail, excerpt

= 1.9.5 - 2017/02/08 =
* Fix: In some cases, HTML output of excerpt is broken when allows HTML tags in excerpt
* Improvement: More clear and simple label, text for some settings
* Update: Remove `<!-- [et_pb_line_break_holder] -->` of Divi theme from excerpt
* Update: Hide empty `<p>` tag from excerpt (which can add unnecessary blank space to content area)
* Update: Add default font-style normal for link & button

= 1.9.4 - 2017/01/25 =
* New: Show link to add new View from Admin bar

= 1.9.3.3 - 2017/01/21 =
* Fix: Correct output of Scrollable list in Tablet devices

= 1.9.3.2 - 2017/01/11 =
* Update: simplify label of some settings in Add/Edit View page
* Improvement: simplify code base and improve performance of Grid, Collapsible List
* Improvement: some style adjustments for Scrollable List

= 1.9.3.1 - 2016/12/27 =
* Update: Save temporary View data to WordPress transient if SESSION is not available
* Improvement: Show all content of Collapsible list when printing

= 1.9.3 - 2016/11/22 =
* Improvement: Remove prefix "by", "in" (before taxonomy, author) to save translation effort
* Improvement: More accurate, time-saving mechanism to remove raw shortcodes text of other plugins in post content
* Fix: "View maybe not exist" error, caused by custom filter hook (which modifies `post_type` in WordPress query) of another plugin

= 1.9.2.1 - 2016/11/05 =
* Tweak: Update message when something went wrong, show exact error in Preview panel
* Tweak: Update description for keyword setting
* Tweak: Introduce filter "pt_cv_link_html" to modify HTML output of title, thumbnail, read-more button

= 1.9.2 - 2016/10/24 =
* Revert force_balance_tags() for item HTML wrapper, to prevent broken output
* Tweak: Remove unused functions
* Tweak: Introduce filter "pt_cv_pagination_text" to customize pagination text

= 1.9.1 - 2016/10/10 =
* Fix: XSS security flaw (a big thank to Tristan Madani)
* Tweak: Improve logic/coding in settings processing, view output functions
* Tweak: Update some descriptions in View dashboard

= 1.9.0 - 2016/09/19 =
* Update: Make excerpt length works with languages which don't use spaces between words
* Update: Validate session ID before using to prevent session hijacking

= 1.8.9 - August 19 2016 =
* New: Add wrapper for Grid items, to facilitate adding custom style (background color, border, padding, margin)
* Fix: W3C validator (duplicate ID "pt-cv-page-1")
* Update: Remove text of `[caption]` shortcode from excerpt
* Update: Clear `view_count` post meta & related functions
* Tweak: Change sort by option "Created date" to "Published date"
* Tweak: Remove filter "excerpt_clean_tags", add filter "tag_to_remove" to exclude content of any HTML tags from excerpt

= 1.8.8 - August 03 2016 =
* New: Able to edit **Read More** text
* Improvement: Minify and combine styles, scripts to save bandwidth and improve performance
* Fix: Excerpt (of content was built with **Page Builder by SiteOrigin** plugin) is not updated
* Fix: [Scrollable List] Indicators don't change active status
* Update: [Collapsible List] Allow HTML tags (`<b>, <br>, <code>, <em>, <i>, <img>, <big>, <small>, <span>, <strong>, <sub>, <sup>, <label>, <cite>`) in heading

= 1.8.7 - July 25 2016 =
* Fix: Broken View output when put View shortcode in Text element of Divi Builder plugin
* Fix: Shortcode of another plugin is visible in Preview panel
* Tweak: Add filter to show all collapsible items at page load

= 1.8.6 - June 27 2016 =
* Fix: Conflict with Autoptimize plugin (when enabled `forced JS in HEAD`)
* Fix: Incorrect number of words in excerpt when uses `\xC2\xA0` or `&nbsp;` as space
* Improvement: Able to resize Preview box
* Tweak: Add filter hook to create custom output completely

= 1.8.5 - May 27 2016 =
* New: Add setting to configure responsive output for Mobile, Tablet devices easily
* Update: Hide all notices of other plugins in Add/Edit View page
* Improvement: Faster performance with optimized core filter "item_col_class"

= 1.8.4.1 - May 11 2016 =
* Fix: [View dashboard] Term boxes under "Select taxonomy" panel are not shown after saving View

= 1.8.4 - May 09 2016 =
* New: Add option to enable/disable "Open first item by default" for Collapsible List
* Update: Leverage WordPress core translations (for "Read More", "No posts found." ...) to minimize user translation effort
* Update: Update setting text in View dashboard to improve usability

= 1.8.3 - April 15 2016 =
* New: [Collapsible list] Show first item by default
* Fix: Blank excerpt when post content was made all by shortcodes
* Fix: Little style issue of numeric text field in WordPress 4.5
* Fix: Remove unwanted styles (uniform.aristo ...) of another plugin in View page (it caused setting options are overlapping each others)
* Update: Uncheck "Show Author", "Show Comment" by default

= 1.8.2 - April 01 2016 =
* Update: Remove default font-size 14 pixels for post content in View
* Tweak: Show post id as data for item (facilitate to customize individual post style)
* Tweak: Add some filters to customizing View elements by PHP code

= 1.8.1 - March 21 2016 =
* Improvement: Equal column width in grid of 5,7,8,9,10,11 columns (in prior versions, last column was biggest)
* Fix: Conflict with theme/plugin which uses Bootstrap library

= 1.8.0.2 - March 12 2016 =
* Update: Remove notice message (when there is Javascript errors in active theme/another plugin). It caused confusion.

= 1.8.0.1 - March 02 2016 =
* Tweak: Update Notice message at front-end (when Javascript error occurs) to avoid misunderstand

= 1.8.0.0 - March 01 2016 =
* Improvement: Detect Javascript errors (which can stop Content Views from working properly), show guide to try to solve it automatically
* Improvement: Prevent error "Permission denied" by session_start() in some hostings
* Improvement: Prevent conflicts with dropdown Menu in theme
* Improvement: Clearer shortcode information in View edit page
* Improvement: Add 2 clear notices in View dashboard about term, thumbnail
* Improvement: Increase performance by optimizing styles, scripts
* Improvement: Print friendly (remove plain text URL after each link in Print mode)
* Update: Disable option "Don't load Bootstrap 3 style & script"

= 1.7.8 - February 06 2016 =
* Improvement: [supports qTranslate-X plugin] Generate valid excerpt of post in current language
* Fixed: Some bugs of pagination in special cases
* Update: Drop support for "vpage" parameter in (Ajax) Numbered pagination to prevent ambiguous logic

= 1.7.7 - January 12 2016 =
* Bug fixed: Content floats after View output
* Tweak: Code relates to grid system
* Tweak: Update filter "page_attr"

= 1.7.6 - January 11 2016 =
* Update: Set Administrator (instead of Editor) as default user role who can add, edit, delete View
* Update: Do not wrap items in output to rows anymore
* Update: Disable sub View by default
* Improvement: Better View dashboard (simplified text & description, improved styles & scripts, improved display in Tablet)
* Improvement: Better performance by better solution



== Upgrade Notice ==

= 1.6.8 =
Major update with lot of improvements