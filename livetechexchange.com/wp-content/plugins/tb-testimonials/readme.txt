=== TBTestimonials ===
Contributors: ansimation
Donate link: http://travisballard.com/donate/
Tags: testimonial, testimonials, quote, quotes, business, client, customer, testimony, reference, ajax, widget, testimonial widget, custom post type
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 1.7.3

Powerful Testimonial Management. Create and use custom Output Templates anywhere on your site. Use the shortcode, function, or included widget to display your testimonials to your users.

== Description ==

Powerful Testimonial Management. Create and use custom Output Templates anywhere on your site. Use the shortcode, function, or included widget to display your testimonials to your users.
-
This plugin lets you manage testimonials as a separate type of information in your WordPress blog, which makes it easier to include them in other blog posts and pages.

Less technical description by Jacob Share :)

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder `tb-testimonials` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click Testimonials menu item and add a testimonial. It's below the Posts menu item. The post title is where you put the testimonial author's name.
4. Use the shortcode generator button ( 2 speach bubbles ) in the visual editor to add a shortcode for testimonials to any post or page on your site.
5. Activate widget if you want to use it.
6. Create a custom testimonial.php if you want to display permalinks differently from single.php.

== Frequently Asked Questions ==

= I'm getting a PHP error =
* Are you running PHP5 or greater? It's required.
* Are you running WordPress 3.0 or later? It's required as well.

== Screenshots ==

1. Main testimonial listing
2. Add a testimonial page
3. General Plugin Settings
4. Output Templates
5. Template Tags
6. Create New Template
7. Documentation

== Changelog ==

= 1.7.3 =
* Fixes bug in shortcode output where a single ID is used. - Thanks Kai!
* Fixes bug in widget where new users after 7.0 was released could not change output template due to legacy api code.

= 1.7.2 =
* Fixes a bug in tbtestimonial() function where category names being passed do not work correctly because of is_numeric conditional

= 1.7.1 =
* Fixes bug in widget when not previously using the optional (now mandatory) template API introduced in 1.6

= 1.7 =
* Removed option to switch to template api. It is now active by default.
* Added list of user defined tags to the "tags" tab on the Output Templates page. Should make remembering your custom tags a little easier. Especially if you have a few of them.
* New shortcode generator in TinyMCE with more options.
* Fixed a few bugs that are present when using a filtered post type
* Removed TBar to make users "happy happy happy". Merry Christmas, Jon.

= 1.6.5 =
* 1.6.4 was a bust and the filter did not work. This solves that as well as changing it in the shortcode generator popup window.
* Changed references of the post type to refer to a single variable in the event of a post type conflict later on down the road.
* Added filter for post type so it can be changed in the event of a conflict.

= 1.6.4 =
* Added filter to change the shortcode in the event of a conflict.

= 1.6.3 =
* Fixed CSS issue with selected tabs in admin area. 3.5 Changed class names to ui-state-active instead of ui-tabs-selected.

= 1.6.2 =
* Added TBar - Promotional Tool for newly launched Travis Ballard Design

= 1.6.1 =
* fixed bug in single shortcode output
* added order and orderby attributes to the shortcode and tbtestimonial() function
* added option to show a random testimonial from a category. thanks to powderflask ( http://wordpress.org/support/profile/powderflask ) for the patch.
* jquery_in_footer is now false by default
* changed showposts to posts_per_page in shortcode generator window. this window needs an overhaul.
* fixed undefined index bug in testimonial.php templates
* added wp_reset_query at end of widget to fix loop bugs

= 1.6.0 =
* New Template system based on the Twig Template Enging for PHP
* Create new templates to use in your testimonials
* New tbt_template_functions hook
* Added ability to show testimonials from categories using the shortcode and tbtestimonial() function
* Added ability to define a template to use in shortcode and tbtestimonial() function
* Added reset query to hopefully fix a couple bugs some users were having
* Fixed ; bug in tbtestimonials_prepare_preloader function
* Replaced some instanced of showposts with posts_per_page
* Fixed js_in_footer undefined index notice

= 1.5.9 =
* Fixes SSL issues with preloader objects.

= 1.5.8 =
* Fixed script and style enqueue issues with WordPress 3.3
* Fixed issue with default_loader.gif when FORCE_SSL was set to true.

= 1.5.7 =
* Added option to load Javascript in the footer or the header. Default loading in the footer caused some JavaScipt conflicts with other plugins.

= 1.5.6 =
* Fixed "Call to undefined function get_post_thumbnail_id()" bug in image uploader due to init process.
* Added syntax highliting to template tags in output settings
* Fixed to show menu item in WordPress 3.1, Testimonials menu item was MIA

= 1.5.5 =
* Fixed bug where array chunk value was less than or equal to 0
* Changed .hidden and .clear CSS class names as to be not-so-generic so unwanted rules do not get added.
* Escape user input in widget for added security.
* Removed whitespace at end of inc/tinymce3/tinymce.php
* Removed widget-options.php

= 1.5.4 =
* Merry Christmas
* Fixed bug where company information was not being saved. Nonce was MIA.
* Added option to disable the loading of jquery cycle so if users already utilize it in their themes it doesn't get reloaded

= 1.5.3 =
* Fixed a bug in template tags where tags were showing in output. Forgot to change $ret to $return during last updates

= 1.5.2 =
* Documentation UI/UX overhaul
* Bug in %permalink% template tag fixed

= 1.5.1 =
* bug in testimonial listing ( admin area and using 'all' in the shortcode ) where has_post_thumbnail was undefined. fixed by including post-thumbnail-teplate.php when undefined.

= 1.5 =
* switch categories over to custom taxonomies
* remove filters from excerpt
* new preloader api to keep preloaders in-tact after upgrading
* sorting options built into widget
* use the built in editor to edit the plugin css
* rewritten widget. default transition is a true crossfade now. there's also a bunch of new transitions thanks to jquery cycle.
* featured image will now replace gravatars
* documentation page

= 1.4.5. =
* Added random testimonial functionality. to display a random testimonial just pass the fucntion or shortcode with no args. tbtestimonial(), [testimonial]

= 1.4.4 =
* forgot to remove references to ba-dotimeout javascript in widget thus causing error logs to report it as missing. Thanks to Bob @ Aquanauts for catching this :)

= 1.4.3 =
* Bug Fix - Removed ba-dotimeout.js in leau of setTimeout. Was causing issues on a user's site, it's not needed anyway.

= 1.4.2 =
* Fixed notice in widget settings about undefined index
* Added a couple new filters to make extending easier. tbtestimonials_template_tags, tbtestimonials_template_tag_replacements, tbtestimonials_widget_syntax, tbtestimonials_single_syntax, tbtestimonials_listing_syntax
* Fixed conflict with NextGen Gallery
* Fixed conflict with MultiSite


= 1.4.1 =
* Automatically fix double quote bug if present
* Function to insert testimonial programatically into theme

= 1.4 =
* Added syntax highliting to Output Setting page.
* Fixed bug in output code where there was an extra " in &lt;div class="testimonial-data"&gt; - Users with this already installed will have to fix it manually.
* Added new template tag for %testimonail_excerpt% to Output Settings.
* Fixed bug causing admin javascript to not work correctly.
* Added if/else conditionals to output syntax

= 1.3.2 =
* Run the_content filters on testimonial content. Now renders shortcodes in testimonials.
* Made testimonial custom post type be hierarchical so now you can use the menu order to order the output of the testimonials when displaying all of them.

= 1.3.1 =
* Fixed sprintf bug when using categories

= 1.3 =
* Moved settings page to Testimonials sub-menu
* Added Output Syntax Options
* Added shortcode to output all testimonials. button added in TinyMCE popup as well.

= 1.2 =
* Fixed bug in widget when showing all testimonials using -1 as the display count. Widget still looped unless option was set to not loop.
* Added categories to testimonials
* Added a plugin settings page where you can change the author prefix, select to use the included stylesheet or not, change your loading graphic, disable the loading graphic, change loading text / position.
* Removed menu position. now falls wherever it wants in menu.
* Laid groundwork for Company Name & Company URL meta box that is coming in 1.3

= 1.1 =
* fixed typo
* changed h2 in widget to use theme defined before_title and after_title.

= 1.0 =
* Initial Import of plugin
