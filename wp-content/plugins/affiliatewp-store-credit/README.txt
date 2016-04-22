=== AffiliateWP - Store Credit ===
Plugin Name: AffiliateWP - Store Credit
Plugin URI: http://affiliatewp.com
Description: Pay AffiliateWP referrals as store credit. Currently supports WooCommerce and Easy Digital Downloads.
Author: ramiabraham
Contributors: ryanduff, ramiabraham, mordauk, sumobi, patrickgarman, section214
Tags: affiliatewp, affiliates, store credit, woo, woocommerce, easy digital downloads, edd
License: GPLv2 or later
Tested up to: 4.4
Stable tag: 2.0
Requires at least: 3.5

== Description ==

> This plugin requires [AffiliateWP](http://affiliatewp.com/ "AffiliateWP") v1.7+ in order to function.

This plugin allows you to pay your affiliates in store credit. At this time it supports the WooCommerce and Easy Digital Downloads integrations in AffiliateWP.

= WooCommerce requirements =

To use this plugin with WooCommerce, you need AffiliateWP and the main WooCommerce plugin.

= Easy Digital Downloads requirements =

To use this plugin with Easy Digital Downloads, you need AffiliateWP, Easy Digital Downloads, and the [EDD Wallet extension](https://easydigitaldownloads.com/downloads/wallet/).

== Installation ==

* Install and activate.

* When marking an AffiliateWP referral paid, it adds the total to the user's credit balance. If for some reason you go back and mark it unpaid, this plugin will also remove the referral amount from the balance.

* On the checkout page, if the user has credit available, it will show a notice and ask them if they want to use it. Based on the credit available and order total, it will create a 1 time use coupon code for the lower amount and automatically apply it to the order. i.e. for a $100 order and $50 credit, it would generate a $50 coupon since the order is more. If the order is $25 and they have $50 in credit, it will generate a coupon for the $25 order total, and leave them with a $25 credit balance after checkout.

* Upon successful checkout, the one time use coupon code is grabbed and the coupon total is deducted from their available balance.

== Frequently Asked Questions ==

* Does this support Easy Digital Downloads?

A: Yes it does!

* Does this support WooCommerce?

A: Yes it does!

* Does this support any other e-Commerce plugins?

A: Not at this time.

== Screenshots ==


== Changelog ==

= 2.0 =

* Added support for Easy Digital Downloads

= 1.1 =
* Fix for WooCommerce 2.3.3+: Run checkout actions after cart is loaded from session.

= 1.0 =
* Initial release.


