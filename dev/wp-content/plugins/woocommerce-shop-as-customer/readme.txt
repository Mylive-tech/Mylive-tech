=== Plugin Name ===
Contributors: cxThemes
Tags: woocommerce, account, shop, profile, customer, edit, admin, switch, permission, testing, rights
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 1.14
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shop as Customer is a plugin for WooCommerce that let's you easily switch and use your store as any Customer.

== Description ==

What it Does
Shop as Customer allows a store Administrator or Shop Manager to shop the front-end of the store as another User, allowing all functionality such as plugins that only work on the product or cart pages and not the Admin Order page, to function normally as if they were that Customer. 

Once installed, the Administrator can easily switch to another user with 2 clicks, then shop the store as that user would see it, with all functionality and plugins that would only be visible to that user, working perfectly. Such as those that work with User roles, inputting custom variables (meta) on product order that are not supported in the Admin Orders screen, etc. This enables incredibly quick and simple manual order creation. And with another click they can switch back to the Administrator or Shop Manager.

Once a cart is created for a User while shopping as them, instead of seeing only the pay button on the checkout page, the Administrator or Shop Manager now has convenient buttons to also email the invoice directly to that User for payment or link to the Order created in the Admin section. 

Features:
* Quickly switch to any Customer
* View and use your store as that Customer
* Quickly switch back to Administrator
* Works with plugins specific to Product & Cart pages or Users.
* Create and send Invoices easily and quickly
* No more fighting with the Admin Orders screen!

Happy Conversions!

== Documentation ==

Please see the included PDF for full instructions on how to use this plugin.

== Changelog ==

1.14
* Fixed issue where-request-to-pay email could send twice if the order-complete page is refreshed after the email has sent.
* Changed so the cart is linked to the current shopping-as user, it does not persist across switches.
* Fixed so is_woocommerce_active() check does also works for multisite installations.

1.13
* Refactor the plugin class so plugin is initialized as early as possible. Please let us know if any problems.
* Changed behaviour to switch immediately on choosing the user to switch to without the need to click the switch button.
* Added a check to see if the user still exists before returning list of previously switched users in the UI, so you don't switch to a non-existent user.
* Fixed broken image refs in the CSS.

1.12
* Fixed 'Invalid payment type' error when trying to create an order in WC 2.4.

1.11
* Removed unused CSS classes with images causing errors with mod_pagespeed.

1.10
* Added username to order note when order was created using Shop as Customer.
* Add additional Shop as Customer notice at the top of the edit order page if order was created using Shop as Customer.
* Remove jquery autocomplete from enqueued scripts - Fixes Revolution slider clash.
* Security upgrades.

1.09
* Added Internationalization how-to to the the docs.
* Updated the language files.
* Changes to the order and priority of the loaded language files. Will not effect anyone who is already using internationalization.
* Changed where in the code the WooCommerce and version number checking is done.
* Made more strings translatable.
* Escaped all add_query_args and remove_query_args for security.
* Updated PluginUpdateChecker class.

1.08
* Moved ajax checkout actions into shop-as-customer conditional check in order to prevent it taking effect on non logged in users.
* Ensure our ajax override checkout has priority 1 to override default WooCommerce.

1.07
* Changed our WooCommerce version support - you can read all about it here https://helpcx.zendesk.com/hc/en-us/articles/202241041/
* Change the way emails are sent to stop double emails in WC2.3
* Remove legacy code.
* Fixed possible non static notice.
* CSS styling tweaks.

1.06
* Fixed bug in debug mode causing notice output before header.

1.05
* Fixed notice about version constant.

1.04
* Fixed security allowing shop_manager to switch to administrator.

1.03
* Added note to Order showing which admin placed the order on behalf of the customer.
* Improved user search speed.
* Fixed warnings from deprecated functions.
* Fixed broken Chozen image path in css.

1.02
* Updated language files with all the new UI text.

1.01
* Added ability to Checkout through Payment Gateway on behalf of Customer.
* Changed front-end Checkout UI design and added tooltips to explain each step of the process.
* Changed front-end Checkout so the Invoice Email doesn't send automatically - you now do so by clicking the button on the second page if you choose to.

1.0
* Initial release.
