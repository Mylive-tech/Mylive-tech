=== Far Future Expiry Header ===
Contributors: Tips and Tricks HQ
Donate link: http://www.tipsandtricks-hq.com/wordpress-far-future-expiration-plugin-5980
Tags: cache, expiry, expiry header, far future expiration, expires header, wp-cache, minify, gzip, javascript, css, compression, speed,
Requires at least: 3.5
Tested up to: 4.3
Stable tag: 1.3
License: GPLv2 or later

This plugin will add a far future expiry header for various file types to improve page load speed of your site

== Description ==

This plugin offers a few lightweight features to speed up your WordPress site without much hassle. 

= Far Future Expiry =

When the feature is enabled, this plugin will modify your .htaccess file by inserting code which will add expires headers for common static file types.

Expiry header specifies a time far enough in the future so that browsers won't try to re-fetch images, CSS, javascript etc files that haven't changed (this reduces the number of HTTP requests) and hence the performance improvement on subsequent page views.

= Gzip Compression =

You can also enable Gzip compression on your site using this plugin. Gzip compression will speed up your WordPress site by compressing the page output and sending it to your visitors browser.

When enabled, the plugin will do gzip compression if the visitor's browser can handle it.

This feature may conflict with a few other plugins. So disable this feature if you need to use a plugin which doesn't work with gzip compression.

== Installation ==

1. Upload the far-future-expiration.zip file from the Plugins -> Add New page in the WordPress administration panel.
2. Activate the plugin through the "Plugins" menu in the WordPress administration panel.

== Usage ==

To use this plugin do the following:

1) Ensure that the "mod_expires" module is enabled from your host's main configuration file 

2) Check with your hosting provider or if you have access to the httpd.conf file the following line should be uncommented:
LoadModule expires_module modules/mod_expires.so

3) Enable the "Far Future Expiration" checkbox

4) Set the number of days till expiry

5) Select the file types you wish to enable the "far future expiration" feature for by using the checkboxes in the "File Types" section

NOTE: When you use this plugin, the file selected file types are cached in the browser until they expire. Therefore you should not use this on files that change frequently.

== Frequently Asked Questions ==

= Can I set a far future expiry header with this plugin? =
Yes

= Can I eanble gzip compression on my site using this plugin? =
yes
 
== Changelog ==

= 1.3 =
- Added a new feature to enable gzip compression on the site

= 1.2 =
- Fixed a minor bug with the htacess rules.

= 1.1 = 
- First commit to wp repo

== Upgrade Notice ==
None

== Screenshots ==
Visit the plugin site at http://www.tipsandtricks-hq.com/wordpress-far-future-expiration-plugin-5980 for screenshots and more info.
