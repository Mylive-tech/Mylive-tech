=== WooCommerce Checkout Manager ===
Contributors: Emark
Donate Link: http://www.trottyzone.com/donation/
Tags: woocommerce, cart, checkout, field, shipping, billing, order, manage, manager, require, remove, editor, custom, options, payment, sell, buy, pay, tax, notice, re-order, addition, amount, exempt, sale, affiliate, commerce, e-commerce, store, shop, stock, variable, trottyzone, wordpress, woothemes
Requires at least: 3.0
Tested up to: 4.2
Stable tag: 3.6.8
License: GPLv2 or later

Manages WooCommerce Checkout

== Description ==

This extension plugin gives you capabilities to manage your fields on your [WooCommerce](http://wordpress.org/plugins/woocommerce/) checkout page.

= FEATURES =
* Add new fields to the checkout page and re-order them.
* Make checkout fields optional.
* Remove & Make required fields. 
* Added fields will appear on Order Summary, Receipt and Back-end in Orders.
* Enable/ Disable "Additional Fields" section name on the Order Summary and Receipt.
* **Four ( 4 )** fields types included: Text Input + Check Box + Select Options + Date Picker.
* Compatible with [WPML](http://wpml.org/) | [WooCommerce Print Invoice & Delivery Note](http://wordpress.org/plugins/woocommerce-delivery-notes/) | [ WooCommerce Order/Customer CSV Export](http://www.woothemes.com/products/ordercustomer-csv-export/)

= PRO VERSION =
[WooCommerce Checkout Manager Pro](http://www.trottyzone.com/product/woocommerce-checkout-manager-pro/) offers these cool nifty extra features:

* Add new fields to the **Billing** and **Shipping** Section **separately** from Additional Section. 
* These fields can be edited on your customers **account** page.
* **Thirteen ( 13 )** field types included: Text Area + Password + Radio + Select + Pre-defined Check Box + Time Picker + **Text/ Html Swapper** + Color Picker + Heading + Multi-Select + Multi-Checkbox etc...
* Create Conditional Fields.
* Create field to remove tax.
* Create field to add additional amount.
* Replace Text using Text/ Html Swapper.
* Allow Customers to **Upload files** for each order.
* Show or Hide added field for Specific Product or Category Only.
* Display **Payment Method** and Shipping Method used by customer.
* Disable any added field from Checkout details page and Order Receipt.
* **Retain fields information** for customers when they navigate back and forth from checkout.
* Disable Billing Address fields for chosen shipping goods. Which makes them visible only for virtual goods.
* **DatePicker:** Change the default format (dd-mm-yy), Set Minimum Date and Maximum Date, Disable days in the week (Sun – Sat).
* **TimePicker:** Includes restriction of both start and end hours, set the minutes interval and manually input labels.
* Area to insert your own **Custom CSS**.
* Display **Order Time**.
* Set Default State for checkout.
* **Import/ Export** added fields data.
* Fields label can accept html characters.
* Re-position the added fields: Before Shipping Form, After Shipping Form, Before Billing Form, After Billing Form or After Order Notes
* **Insert Notice:** Before Customer Address Fields and Before Order Summary on checkout page.


= INCLUDED TRANSLATIONS =

* SPANISH
* GERMAN
* ITALIAN
* DUTCH
* FINNISH
* FRENCH
* NORWEGIAN
* POLISH
* VIETNAMSE
* EUROPEAN PORTUGUESE
* BRAZILIAN PORTUGUESE
* BULGARIAN by Ivo Minchev
* CHINESE by Sid Lo


Would you like this plugin translated in your own language? 
Do you want to update an existing translation?

Find out how at [Translating WooCommerce Checkout Manager in your Language](http://www.trottyzone.com/translating-woocommerce-checkout-manager-in-your-language/).


= ------------ THANK YOU FOR DOWNLOADING ------------ =



== Installation ==

= Minimum Requirements =
* WooCommerce 2.2 +
* WordPress 3.8 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

= WP installation =
1. Log in to your WordPress dashboard
2. Navigate to the Plugins menu and click Add New.
2. Click Upload Plugin
3. Click Choose File and select downloaded zip file
4. Click Install Now

= NP: The downloaded zip file is the file that you download from wordpress.org. =

= FTP installation =
The manual installation method involves downloading the plugin and uploading it to your webserver via an FTP application. The WordPress codex contains instructions on how to do this here.

= Updating =
Automatic updates are delivered just like any other WordPress plugin.


== Frequently Asked Questions ==

= How to fix fields that are not showing on checkout page properly? = 
Usually this is an CSS issue. If your theme comes with the option to input your very own custom CSS, you can use the abbreviation field name as part of the CSS code to set the fields in the way that you want. 

Example :
`#myfield1_field {
	float: right;
}`

= How do I review the data from the custom fields? = 
Your order data can be reviewed in each order. By default your "Custom Fields" section should be showing allowing you to see the custom fields data.
If the fields are not showing, follow these steps:

1. Go to your desired Order.
2. Click "Screen Options"
3. Check "Custom Fields"
4. Scroll down till you see "Custom Fields" section.

= How do you access saved data to be used with WooCommerce PDF Invoices & Packing Slips? = 
The above plugin requests that you code the fields in the template. To access the saved data, use the abbreviation name of the field. As we are using the first abbreviation field as an example. Where "myfield1" is the abbreviation name, and "My custom field:" is the label.

Example:
`<?php $wpo_wcpdf->custom_field('myfield1', 'My custom field:'); ?>`


== Screenshots ==



1. SETTINGS PAGE

2. ORDER SUMMARY

3. RECEIPT

4. INPUT TYPE

5. CHECKBOX

6. DATE PICKER

7. SELECT OPTIONS

== Changelog ==
= 1.0 =
Initial

= 1.2 =
Added required attribute removal

= 1.3 =
bug fix!

= 1.4 =
More features added.

= 1.5 =
some bugs fixed

= 1.6 =
more bugs fixed

= 1.7 =
add/remove required field for each new fields

= 2.0 =
Custom fields data are added to the receipt

= 2.1 =
Checkout process fix

= 2.2 =
bug fix

= 2.3 =
Additional features

= 2.4 =
Localization Ready

= 2.5 =
Added features for shipping

= 2.6 =
remove fields for shipping

= 2.7 =
required attribute bug fix and included translations

= 2.8 =
Bug fixes

= 2.9 =
Bug fixes

= 3.0 =
Javascript fix and rename fields inserted

= 3.1 =
bug fix

= 3.2 =
code review

= 3.3 =
fields positioning, fixed.

= 3.4 =
bug fixed.

= 3.5 =
Select date function, included.

= 3.5.1 =
Select option and checkbox functions, included.

= 3.5.2 =
updating to standard.

= 3.5.3 =
bug fix- force selection for option and minor fix.

= 3.5.4 =
Added feature.

= 3.5.5 =
Translations updated

= 3.5.6 =
Included translations - Vietnamse, Italian, European Portuguese, Brazilian Portuguese
Layout fixed on Order Summary Page

= 3.5.7 =
Bug fix.

= 3.5.8 =
Bug fix.

= 3.5.81 =
Bulgarian language by Ivo Minchev

= 3.5.9 =
Bug fix.

= 3.6 =
Bug fixes.

= 3.6.1 =
Compatibility with 2.1.7 WooCommerce && WPML

= 3.6.2 =
WPML bug fix

= 3.6.3 =
WPML bug fix 2 (translation for e-mails)

= 3.6.4 =
WPML bug fixes 3 

= 3.6.5 =
WPML bug fixes 4

= 3.6.6 =
GUI + Code clean up.
Multi-lang Save issue fix.

= 3.6.7 =
Add Error Fix.
Add WooCommerce Order/Customer CSV Export support
Able to Change additional information header

= 3.6.8 =
Add Error Fix 2.
GUI upgrade.