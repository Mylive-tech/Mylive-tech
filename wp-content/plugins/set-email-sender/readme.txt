=== Plugin Name ===
Contributors: citynetwork
Donate link: https://citynetwork.se/
Tags: mail
Requires at least: 2.2
Tested up to: 4.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin makes WordPress specify the sender email address correctly in outgoing emails.

== Description ==

WordPress by default does not send emails with a correctly specified sending email address.
By default outgoing emails from a WordPress installation is sent with user id of webserver
process and hostname of webserver as the email address. This isn't correct and many hosting
providers block such emails, mainly because of spam.

So this plugin makes sure that a correct sender address is set, by setting the Sender property
of the PHPMailer object in WordPress.

== Installation ==

1. Upload `setemailsender.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What configuration options are available? =

None, at the moment.

= Which sending email address will be set? =

The same as in the From header, which is the email address a receiver sees in the email client.

== Changelog ==

= 0.1 =
* Initial release.

