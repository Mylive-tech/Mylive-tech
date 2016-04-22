=== Widget Logic Visual ===
Contributors: totalbounty,jtprattmedia
Donate link: http://www.totalbounty.com
Tags: widget, admin, conditional tags, filter, context, visual
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: trunk

Widget Logic Visual Version lets you control on which pages widgets appear using WP's conditional tags without having to know how conditional tags work.  You cna visually point and click where you want the widget to appear.

== Description ==
Control, limit, and restrict what webpages widgets are shown on - point and click visual display.  Replaces original widget logic plugin because anyone can use it easily without knowing any code or template tags, "it just works".

*UPDATE* - now contains ability to visually add widget limitations or exceptions for display OR the ability to add conditional tag code (for advanced users).  You get the best of both worlds!

The original Widget Logic plugin is very useful because it allows you to restrict the display of widgets to specific pages using WordPress "conditional tags".  The only problem is that non-technical people don't know how to use conditional tags.

Another Plugin by: [Total Bounty Marketplace](http://www.totalbounty.com "Total Bounty Marketplace")

WordPress is now used by nearly 70 million websites worldwide and the majority of those website owners are non-technical people.  We created Widget Logic Visual Version for all of those people.  Now you can easily just point and click what sections of your WordPress website you want a widget to display on without knowing code, or "conditional tags" or any of that at all.

You can select to restrict view of widgets to the homepage, specific posts or pages, tags or categories, author pages, etc.  You can create just about any combination of any of those you'd like as well.  

For instance, you could choose to display a widget on only the homepage and your "about" page, or specific tag or category pages - nearly any combination you can think of.

Here's a video tutorial:

http://www.youtube.com/watch?v=ApP2A3rWtyU

Post plugins questions and comments in the forum:  [Widget Logic Visual Forum](http://www.totalbounty.com/forums/topic/widget-logic-visual-version/ "Widget Logic Visual Forum")

== Installation ==

1. Upload the plugin to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That's it. The plugin has no settings page, and the options are displayed at the bottom of all widgets

Here's a video tutorial:
http://www.youtube.com/watch?v=ApP2A3rWtyU

Post plugins questions and comments in the forum:  [Widget Logic Visual Forum](http://www.totalbounty.com/forums/topic/widget-logic-visual-version/ "Widget Logic Visual Forum")

== Frequently Asked Questions ==

= Why isn't it working? =

Try switching to the WP default theme

If that doesn't work try deactivacting all your plugins.

If either of those issues makes the plugin work fine - then activate your plugins one by one until you find the one that is the conflict (or the theme).

Here's a video tutorial:
http://www.youtube.com/watch?v=ApP2A3rWtyU

Post plugins questions and comments in the forum:  [Widget Logic Visual Forum](http://www.totalbounty.com/forums/topic/widget-logic-visual-version/ "Widget Logic Visual Forum")

== Screenshots ==

1. Once the plugin is installed, the bottom of every widget has a new "edit visibility" button like the one in this image.  Just click that button to restrict where the widget will display on your website (otherwise it will show on all pages)
2. Once you click that buton you get a lightbox popup window to choose "add new limitation (visual)" or "using conditional tag code (advanced).  Click one or the other to get started.
3. Once you click "add new" you get a dropdown to choose where to show the widget (homepage, posts, pages, tags, categories, 404 page, author pages).  Choose the places you want to show the widget or click "except" to not show the widget on those pages. This example shows adding a limitation for all category pages. Choose all or any you'd like to display on.
4.  In this example we see how if you uncheck "all categories" a multi-select box appears where you can select one or more individual category pages (if you don't want them all).  The same thing happens if you uncheck "all" for posts, pages, authors, attachments, tags, etc.
5.  An example of adding a limitation to make a widget show on every page of the website except the homepage.
6.  This is an example of adding conditional tag code.  You must add the code and check the "activate the code" and click "update" for it to work.  This is in case you want to try a conditional tag, de-activate it (to try a visual version) and then come back later to activate it again (the code you last used is saved in the box).
7.  This is an error illustrating what will happen if you try to add both conditional tag code and a visual limitation (you can't have both, it's one or the other)

== Changelog ==

= 1.5.2 =

fixed bug which made widgets not always place right, but in doing so had to roll back undefined index errors fix in last release - and will fix that again in the next release once we find the root cause

= 1.5.1 =

* fixed undefined index errors

* added images folder that was didn't make it into SVN in the last release (fixes a few broken images)

= 1.5 =
* edited the limitation lightbox to have "X" to make it more intuitive to close

* adding explanation text to the lightbox to help first time users

* added option in lightbox for users to "add new limitation" (visual style) or "use conditional tag code" (for advanced users)

* every time you add/edit limitations or conditional tag code the widget options now autosave

* added an error routine that tells you that you can't run Widget Logic Visual and the original Widget Logic plugin at the same time (previously had conflict).

= 1.4 =
added lightbox and multiple rule abilities

= 1.3 =
added except functionalty

= 1.2 =
added the ability to add multiple limitaions

= 1.1 =
Initial plugin Widget Logic Visual Version 1.1 is released

