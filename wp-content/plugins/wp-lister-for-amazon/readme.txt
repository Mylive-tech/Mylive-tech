=== WP-Lister Lite for Amazon ===
Contributors: wp-lab
Tags: amazon, woocommerce, products, export
Requires at least: 4.2
Tested up to: 4.4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

List products from WordPress on Amazon.

== Description ==

WP-Lister for Amazon integrates your WooCommerce product catalog with your inventory on Amazon.

= Features =

* list any number of items
* supports all official Amazon category feeds
* supports product variations
* view buy box price and competitor prices
* includes SKU generator tool

= More information and Pro version =

Visit https://www.wplab.com/plugins/wp-lister-for-amazon/ to read more about WP-Lister and the Pro version - including documentation, installation instructions and user reviews.

WP-Lister Pro for Amazon will not only help you list items, but synchronize sales and orders across platforms and features an automatic repricing tool.

== Installation ==

1. Install WP-Lister for Amazon either via the WordPress.org plugin repository, or by uploading the files to your server
2. After activating the plugin, visit the Amazon account settings page and follow the setup instructions at http://docs.wplab.com/article/85-first-time-setup 

== Frequently Asked Questions ==

= What are the requirements to run WP-Lister? =

WP-Lister requires a recent version of WordPress (4.2 or newer) and WooCommerce (2.2 or newer) installed. Your server should run on Linux and have PHP 5.3 or better with cURL support.

= Does WP-Lister support windows servers? =

No, and there are no plans on adding support for IIS.

= Are there any more FAQ? =

Yes, there are. Please check out our growing knowledgebase at https://www.wplab.com/plugins/wp-lister-for-amazon/faq/

== Changelog ==
= 0.9.6.17 =
* added option to bulk remove min/max prices from min/max price wizard 
* added support for AMAZON_CA fulfillment center ID (experimental!) 
* improved checking for processed feeds - avoid feeds being stuck as submitted due to agressive caching plugins 
* improved SKU Generator tool: check existing SKUs and skip products where SKU generation would result in duplicates 
* make sure sale price stays within min/max boundaries - prevent Amazon from throwing price alert and deactivating the listing 
* do not use featured image from parent variation for child variations (avoid same swatch image for all childs) 
* renamed Feed ID to Batch ID and improved title on feed details page 
* removed deprecated feed template Miscellaneous (US) 
* fixed custom main_image_url setting on product level being ignored 
* fixed issue updating product details on PHP5.6 with Suhosin patch installed (and suhosin.post.disallow_nul option on) 
* fixed issue where cancelled orders were stuck as "Pending" (since LastUpdateDate apparently stays the same) 
* fixed SKU generator not showing and processing all missing SKUs (check for NULL meta values) 
* fixed missing success indicator and message when preparing items in bulk from Products page (or matching products or applying lowest prices in bulk) 

= 0.9.6.16 =
* trigger stock status notifications when reducing stock level 
* implemented batch mode for FBA inventory check tools 
* improved inventory check memory requirements - disable autoload for temp data (requires WP4.2+) 
* make sure ASINs have no leading or trailing spaces when creating matched listings from product 
* fixed importing listings with identical SKUs from multiple accounts / sites 
* fixed possible SQL error during import: Column 'post_content' cannot be null 
* fixed possible PHP error on edit account page if MWS credentials are incorrect and no marketplaces were found 
* fixed missing categories when processing multiple browse tree guides for the same feed template 
* fixed fatal error when using Min/Max Price Wizard to set prices based on sale price 
* fixed possible fatal error in Woo_ProductBuilder.php on line 1045 
* fixed fatal error: Redefinition of parameter $quotaMax (PHP7) 
* fixed possible fatal error in ListingsModel.php on line 1679 
* fixed narrow tooltips 
* fixed PHP warning on PHP7 

= 0.9.6.15 =
* improved performance on log and orders pages - database version 33 
* improved performance of processing FBA reports 
* improved inventory check tools - implemented batch processing and improved general performance 
* improved address format for in FBA submission feeds - if shipping_company is set, use company name as AddressFieldOne 
* improved error handling on import - if creating product failed (db insert) 
* improved logging: add history record when creating WC order manually 
* fixed search on repricing page 
* fixed support for multiple images in BookLoader feed template 
* fixed wpla_mcf_enabled_order_statuses filter hook not working for automatic FBA submission (only manual) 
* fixed possible fatal error when processing FBA inventory report (Call to a member function set_stock_status() on a non-object) 
* fixed empty log records being created when checking for reports 
* added "Allow direct editing" developer option - hide Edit listing link by default 
* added experimental support for reserving / holding back FBA stock (MCF) - if order status is on-hold set FulfillmentAction to 'Hold' 
* added wpla_run_scheduled_tasks ajax action hook to trigger only the Amazon cron job (equal to wplister_run_scheduled_tasks) 

= 0.9.6.14 =
* fixed gift wrap row being added to WooCommerce orders even if gift wrap option was not selected 
* fixed issue where messages on the settings page would be invisible with certain 3rd party plugins installed 

= 0.9.6.13 =
* added option to leave email address empty when creating orders in WooCommerce 
* added FBA only mode settings option - to force FBA stock levels to be synced to WooCommerce 
* added filter hook wpla_mcf_enabled_order_statuses - allow to control which order statuses are allowed for FBA/MCF 
* added advanced options to make account settings, category settings and repricing tool available in main menu 
* store SKU as order line item meta when creating orders in WooCommerce 
* show warning if OpenSSL is older than 0.9.8o 
* show gift wrap option on orders page and details view 
* fixed creating orders with gift wrap option enabled - add gift wrap as separate shipping row 
* fixed tracking details not being sent to Amazon if orders are completed via ShipStation plugin 
* fixed fetching full listing description from amazon.es 
* fixed imported products not showing on frontend if sort by popularity is enabled (set total_sales meta field) 
* fixed stock status for imported variations 
* fixed issue where tracking details were not sent to eBay if autocomplete sales option was enabled 
* fixed issue where FBA items would not be updated from report if their SKU contains a "&" character 
* improved performance when updating products via CSV import (and show more debug data in log file) 
* improved error handling on storing report content in db (error: Got a packet bigger than 'max_allowed_packet' bytes) 
* improved error handling and logging if set_include_path() is disabled 
* escape HTML special chars in invalid SKU warning (force invisible HTML like soft hyphen / shy to become visible) 
* relabeled "Inventory Sync" option to "Synchronize sales" 

= 0.9.6.12 =
* improved storing taxes in created WooCommerce orders 
* fixed order line item product_id and variation_id for created WooCommerce orders containing variations 
* skip FBA orders from being auto completed on Amazon / Order Fulfillment Feed 
* fixed "The order id ... was not associated with your merchant" error for FBA orders
* repricing tool: restore listing view filters after using min/max price wizard 
* added option to filter order to import by marketplace - prevent duplicate orders if the same account is connected to multiple marketplaces 
* added feed template / category "Sport & Freizeit" on amazon.de 
* added Deutsche Post shipping provider 

= 0.9.6.11 =
* fixed missing variation_theme values for some templates like Jewellery and Clothing 
* fixed Error 99001: A value is required for the "feed_product_type" field (for parent variations) 
* fixed missing bullet points for (parent) variations 
* fixed FBA cron job not running more often than 24 hours 
* fixed duplicate description on imported products - leave product short description empty 
* fixed possible layout issue caused by 3rd party CSS 
* fixed empty external_product_id_type column for amazon.in 
* send SellerId instead of Merchant in SubmitFeed request header (SDK bug) 
* added DPD shipping service on edit order page 

= 0.9.6.10 =
* added experimental support for amazon.in 
* improved order processing for large numbers of orders 
* fixed error: A value is required for the brand_name / item_name field 

= 0.9.6.9 =
* added filter option to show listings with no profile assigned 
* fixed issue where orders were not imported / synced correctly if ListOrderItems requests are throttled 
* fixed issue where some orders were not imported if multiple accounts are used 
* fixed possible issue where lowest prices would not be updated from Amazon 

= 0.9.6.8 =
* fixed issue where variable items would be imported as simple products 
* fixed issue where parent attributes (e.g. brand) were missing for child variations (attribute_brand shortcode) 
* parent variations should only have three columns set: item_sku, parent_child, variation_theme 
* fixed possible php notice on inventory check (tools page) 
* added filter hook wpla_reason_for_not_creating_wc_order - allow other plugins to decide whether an order is created 

= 0.9.6.7 =
* fixed issue where activity indicator could show reports in progress when all reports were already processed 
* improved multiple offers indicator on repricing page - explain possible up-pricing issues in tooltip 
* feed generation: leave external_product_id_type empty if there is no external_product_id (parent variations) 
* skip invalid rows when processing inventory report - prevent inserting empty rows in amazon_listings 
* don't allow processing an inventory report that has localized column headers 
* added filter hook wpla_filter_imported_product_data and wpla_filter_imported_condition_html 

= 0.9.6.6.8 =
* fixed issue where sale dates were sent if sale price was intentionally left blank in listing profile 
* fixed inline price editor for large amounts - remove thousands separator from edit price field 
* fixed no change option in min/max price wizard  

= 0.9.6.6.7 =
* fixed sale start and end date not being set automatically 
* fixed repricing changelog showing integer prices when force decimal comma option was enabled  
* feed generation: leave external_product_id_type empty if there is no external_product_id (parent variations) 

= 0.9.6.6.6 =
* added warning note on import page about sale prices not being imported, but being removed when an imported product is updated 
* fixed issue where sale start and end date would be set for rows without a price (like parent variations in a listing data feed) 

= 0.9.6.6.5 =
* added warning on listing page if listings linked to missing products are found 
* added support for tracking details set by Shipment Tracking and Shipstation plugins (use their tracking number and provider in Order Fulfillment feed) 
* if no sale price is set send regular price with sale end date in the past (the only way to remove previously sent sale prices) 
* fixed stored number of pending feeds when multiple accounts are checked 

= 0.9.6.6.4 =
* include item condition note in imported product description 
* automatically create matched listing for simple products when ASIN is entered manually 
* trigger new Price&Quantity feed when updating min/max prices from WooCommerce (tools page) 
* updating reports checks pending ReportRequestIds only (make sure that each report is processed using the account it was requested by) 
* fixed issue where reports for different marketplaces would return the same results 
* fixed shipping date not being sent as UTC when order is manually marked as shipped 
* fixed importing books with multiple authors 
* added more feed templates for amazon.ca 

= 0.9.6.6.3 =
* added option to filter orders by Amazon account on WooCommerce Orders page 
* added prev/next buttons to import preview and fixed select all checkbox on search results 
* import book specific attributes - like author, publisher, binding and date published 
* extended option to set how often to request FBA Shipment reports to apply to FBA Inventory report as well 
* fixed importing item condition and condition note when report contains special characters 
* fixed possible error updating min/max prices 

= 0.9.6.6.2 =
* profile editor: do not require external_product_id if assigned account has the brand registry option enabled 
* update wp_amazon_listings.account_id when updating / applying listing profile 
* fixed issue where FBA enabled products would be marked as out of stock in WooCommerce if FBA stock is zero but still stock left in WC 
* fixed rare issue saving report processing options on import page 

= 0.9.6.6.1 =
* added option to import variations as simple products 
* fall back to import as simple product if there are no variation attributes on the parent listing (fix importing "variations without attributes") 
* fixed issue importing images for very long listing titles 
* improved error handling during importing process 

= 0.9.6.6 =
* added filter option to hide empty fields in profile editor
* added Industrial & Scientific feed templates for amazon.com
* added support for WooCommerce CSV importer 3.x

= 0.9.6.5.4 =
* added optional field for item condition and condition note on variation level
* added options to specify how long feeds, reports and order data should be kept in the database
* order details page: enter shipping time as local time instead of UTC
* view report: added search box to filter results / limit view to 1000 rows by default
* regard shipping discount when creating orders in WooCommerce (fix shipping total)
* fixed search box on import preview page - returned no results when searching for exact match ASIN or SKU

= 0.9.6.5.3 =
* fixed saving variations via AJAX on WooCommerce 2.4 beta
* show warning on edit product page if variations have no SKU set
* improved SKU mismatch warning on listings page in case the WooCommerce SKU is empty
* edit product: trim spaces from ASINs and UPCs automatically
* when duplicating a profile, jump to edit profile page

= 0.9.6.5.2 =
* shipping feed: make sure carrier-name is not empty if carrier-code is 'Other' (prevent Error 99021)
* edit order page: fixed field for custom service provider name not showing when tracking provider is set to "Other"
* fixed setup warnings not being shown (like missing cURL warning message)

= 0.9.6.5.1 =
* improved performance of generating import preview page
* fixed possible error code 200 when processing import queue

= 0.9.6.5 =
* added support for custom order statuses on settings page
* added gallery fallback option to use attached images if there is no WooCommerce Product Gallery (fixed issue with WooCommerce Dynamic Gallery plugin)
* added loading indicator on edit profile page
* added missing SDK file MarketplaceWebServiceProducts/Model/ErrorResponse.php
* added button to manually convert custom tables to utf8mb4 on WordPress 4.2+ (fix "Illegal mix of collations" sql error)
* improved Amazon column on Products page - show all listings for each product (but group variation listings)
* make sure the latest changes are submitted - even if a feed is "stuck" as submitted
* optimized memory footprint when processing import queue (fixed loading task list for 20k+ items on 192M RAM)
* improved processing of browse tree guide files - link db records to tpl_id to be able to clean incorrectly imported data automatically
* fixed php warning in ajax request when enabling all images on edit product page
* fixed issue with SWVG and Sports feed templates ok Amazon UK

= 0.9.6.4.2 =
* added option to request FBA shipment report every 3 hours
* added Clothing feed template for amazon.ca

= 0.9.6.4.1 =
* fixed possible php error during import 

= 0.9.6.4 =
* added option to set a default product category for products imported from Amazon (advanced settings page) 
* added option to automatically create matched listings for all products with ASINs (developer tools page) 
* improved profile editor for spanish feed templates 
* fixed some CE feed templates not being imported properly (amazon.es) 
* fixed possible fatal error during import 

= 0.9.6.3 =
* added option to process only selected rows when importing / updating products from merchant report 
* added option to enable Brand Registry / UPC exemption for account 
* brand registry: create listings for newly added child variations automatically, even if no UPC or ASIN is provided 
* fixed issue where items listed on multiple marketplaces using the same account would stay "submitted" 
* fixed matching product from edit product page - selected ASIN was removed if products was updated right after matching 
* fixed "View in WP-Lister" toolbar link on frontend 
* addedtooltips for report processing options on import page 
* import process: fixed creating additional (new / missing) variations for existing variable products in WooCommerce 
* regard "fall back to seller fulfilled" option when processing FBA inventory reports - skip zero qty rows entirely if fall back is enabled 

= 0.9.6.2 =
* added option to search / filter report rows in import preview 
* automatically fill in variation attribute columns like size_name and color_name  
* show number of offers considered next to lowest offer price in listings table 
* changed labeling from "imported" to "queued" - and updated text on import and settings pages 
* added developer tool buttons to clean the database - remove orphaned child variations and remove listings where the WooCommerce product has been deleted 
* fixed issue where selecting a category for Item Type Keyword column would insert browse node id instead of keyword (profile editor and edit product page) 
* make sure the customer state (address) is stored as two letter code in created WooCommerce orders (Amazon apparently returns either one or the other) 
* fixed search box on SKU generator page not showing products without listings 
* fixed formatting on ListMarketplaceParticipations response (log details) 
* fixes issue with attribute_ shortcodes on child variations inserting the same size/color value for all variations 

= 0.9.6.1 =
* added option to hide (exclude) specific variations from being listed on Amazon 
* added option to set WooCommerce order status for orders marked as shipped on Amazon 
* added "Sports & Outdoors" category for Amazon CA 
* regard WordPress timezone setting when creating orders 
* automatically update variation_theme for affected items when updating a listing profile 
* make sure sale_price is not higher than standard_price / price - Amazon might silently ignore price updates otherwise 
* fixed issue preparing listings when listing title is longer than 255 characters 
* fixed duplicate ASINs being skipped when importing products from merchant report 
* don't warn about duplicate ASINs if the SKU is unique 
* added action hook wpla_prepare_listing to create new listings from products 

= 0.9.6 =
* Initial release on wordpress.org

