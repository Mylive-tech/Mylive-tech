=== Plugin Name ===
Contributors: cxThemes
Tags: woocommerce, email, customize, customise, edit, colors, text, preview, template, communication, send, test
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 2.31
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WooCommerce Email Customizer plugin allows you to fully customize the styling, colors, logo and text in the emails sent from your WooCommerce store.

== Description ==

Create Beautiful Customized WooCommerce Emails:
Email Customizer enables full customization of your WooCommerce emails. Customize colors, header & footer format, add custom links, link to your social networks, and now even customize what the email says. You no longer need to be a developer to do this.

Currently customization is something that only a developer can do by going into the code and editing the template files, which isn't really an option for a non-programmer. We wanted to give you an environment that is simple to use, gives you a live preview of your customizations, and can send a test email when you are done. That's what Email Customizer for WooCommerce does.

The plugin also adds functionality to your WooCommerce Orders page so you are able to open a preview of any of the email templates (New Order, Invoice, Processing Order, etc), and send/resend that email to your customer or yourself.

Email Customizer for WooCommerce has made managing the email communications sent from our store much simpler and more beautiful - making our whole operation look and sounds as solid as it is. We think it can do the same for you.

Great For
* Customizing of the  the styling, colors, header & footer format, add custom links, link to your social networks, and now even customize what the email says.
* Tailor what your customer reads and sees, before you send it - helping your operation to look and sound as solid as it is.
* Developers who can now also easily preview changes as they develop, modify or enhance their email template files.
* Shop Managers who want to preview & send/resend emails (New Order, Invoice, etc) right from the WooCommerce Order page.

Happy Conversions!


== Documentation ==

Please see the included PDF for full instructions on how to use this plugin.
 
 
== Changelog ==

2.31
* Fixed so is_woocommerce_active() check does also works for multisite installations.

2.30
* Open all a-href links in the email preview in a new target window, rather than in the preview iframe.
* Change colorpick to ec-colorpick to guard against multiple initializations on some platforms.
* New system that will notify you with a compatibility warning when about to preview unknown email types, and provide options before proceeding.

2.29
* Refactor the plugin class so plugin is initialized as early as possible. Please let us know if any problems.
* Change how we check WooCommerce version number.

2.28
* Fixed so translations are back for all strings. Please let us know if we missed any.

2.27
* Make sure email shortcodes like [ec_order] are applied early as possible - to make sure they work with all Form Based payment gateways e.g. Payment Express.

2.26
* Apply styles to the WooCommerce emails - backorder, low_stock, no_stock.

2.25
* Enable support and editing of the newer emails refunded_order and cancelled_order.
* Template spring clean to bring inline with the latest woocommerce template updates.
* Moved the CSS inlining to a helper function rather than in the template.

2.24
* Change the registering of the templates up the load order from init to plugins_loaded for a consistent template load.

2.23
* Further improvements to shortcodes so [ec_firstname] and [ec_lastname] can be used in all the emails.

2.22
* Make sure [ec_shortcodes] are not mixed up across certain bulk email operations.
* Notification in preview when using ec_customer_note outside of Customer Note email where its intended.

2.21
* Improve shortcodes so Firstname and Lastname can be used in more of the emails, like New Account.
* Display a notification in the email preview when using the order shortcode in the New Account email. WooCommerce has not yet created the order at this point.

2.20
* Moved css inline-ing into the footer template so it's not reliant on the woocoomerce_email_footer action to be applied. Fixes blank emails in wc smart coupons.

2.19
* Added Internationalization how-to to the the docs.
* Updated the language files.
* UI Text changes.
* Changes to the order and priority of the loaded language files. Will not effect anyone who is already using internationalization.
* Changed where in the code the WooCommerce and version number checking is done.
* Made more strings translatable.
* Escaped all add_query_args and remove_query_args for security.
* Updated PluginUpdateChecker class.

2.18
* Changed the way clean CSS is passed to Emogrifier
* Force utf8 format in the email header if for some unique reason it has not been set or has been stripped.
* Change is_woocommerce_active method so it is not interfered with by another plugin to avoid Non-Static notices.
* Avoid writing empty address blocks and headings in the email if they are not set.
* Changed to singleton class initialisation
* Show in the dropdown if there are no Orders to preview.
* Changed name of Emogrifier class to EmogrifierEC to not interfere with WC.
* Fix no rendering of the default WC email in the preview.

2.17
* Rewrite shortcode logic and the way that template args are shared with them.
* Changed default email texts to use the multipurpose ec_order shortcode that works on the front and back end.

2.16
* Fixed notice with wc_get_template filter.

2.15
* Changed our WooCommerce version support - you can read all about it here https://helpcx.zendesk.com/hc/en-us/articles/202241041/
* Changed all deprecated woocommerce_ to wc_ functions.
* Changed order queries to use the new order status from WC2.2
* Updated to the newer wc_get_template filter available in WC2.2
* Added an 800 recent order limit showing in the orders dropdown to avoid memory overload on massive queries.

2.14
* Added [ec_order] shortcode that can be used in admin or user emails and uses the correct order link automatically. It accepts arguments that control it's display e.g. [ec_order show="#, number, date, link, container" hide="date, link"]. (more documentation coming soon)
* Added classes to the shortcodes so they can be individually targeted and styled.
* Changed order number on order dropdown to use get_order_number.
* Updated templates to display download links in only the correct places.
* Changed default method on shortcodes to use parse_args.

2.13
* Fixed payment details appearing in admin emails.
* Added preprocessing of emails subject during test send so the smart tags are converted and don't stay in the subject of the test email.

2.12
* Fixed bug notice in email when customer is creating account on first order.

2.11
* Emogrifier class changes - convert anonymous functions used for preg_replace_callback to be methods to extend support for older versions of php without anonymous functions support.

2.10
* Added a backup mb_convert_encoding function for cases where older hosting servers do not have have php_mbstring module turned on - please ask that your hosts enable this as you could run into encoding issues when using certain special characters - for this or other plugins that deal with character encoding.

2.09
* Added Custom CSS customization option so you can have full control over the style of your emails.
* Change css compiler to only pull classes from local <style> block in template to avoid style clashes and compiling errors.
* Added sanitization of css before it is compiled to avoid errors.
* Removed a few of the unwanted notices in WP_DEBUG mode.

2.08
* Email templates css tweaks.
* Name change from Email Control to Email Customizer.

2.07
* Improved language translation functionality. Create a folder called email-control in the WordPress language folder and put the appropriately named .mo file, this will override ours and will not be overwritten on plugin update.
* e.g. wp-content/languages/email-control/email-control-en_US.mo

2.06
* Small css tweaks
* Fixed get_settings function

2.05
*** NEW *** Supreme template. loads more customizations - email width, header logo position, custom nav links (eg facebook, twitter, etc), footer logo and layout, and many more.
* Added all the same customizations improvements to the Deluxe template too.
* Added all the untranslated strings so they can now be loclalized (please let us know if we missed any).
* Moved hook 'woocommerce_email_before_order_table' to better position in templates.
* Added shortcode [ec_user_order_link] for user to see their order in their account on your site.

2.04
* Fixed terminate php block in emogrifier.
* Changed all require includes to relative file paths.

2.03
* Fixed css causing headings to rendered too small.
* Fixed bug with emogrifier require not finding path.

2.02
* Fixed bug stopping edit changes saving.

2.01
* Fixed compatibility bugs with older WooCommerce versions.
* CSS various tweaks.

2.0
* You can now customize the the styling, colors, logo and text in your emails.

1.0
* Initial release.
