=== WP Store Locator ===
Plugin URI: http://wpstorelocator.co
Contributors: tijmensmit
Donate link: https://www.paypal.me/tijmensmit
Tags: google maps, store locator, business locations, geocoding, stores, geo, zipcode locator, dealer locater, geocode, gmaps, google map, google map plugin, location finder, map tools, shop locator, wp google map
Requires at least: 3.7
Tested up to: 4.3
Stable tag: 2.0.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

An easy to use location management system that enables users to search for nearby physical stores.

== Description ==

WP Store Locator is a powerful and easy to use location management system. 
You can customize the appearance of the map, and provide custom labels for entry fields. 
Users can filter the results by radius, and see driving directions to the nearby stores in 
the language that is set in the admin panel. 

= Features include: =

* Manage an unlimited numbers of stores.
* Provide extra details for stores like the phone, fax, email, url, description and opening hours. There are filters available that allow you add [custom](http://wpstorelocator.co/document/add-custom-meta-data-to-store-locations/) meta data.
* Support for custom [map styles](http://www.mapstylr.com/).
* Choose from nine retina ready marker icons.
* Show the driving distances in either km or miles.
* Shortcodes that enable you to add individual opening hours, addresses or just a map with a single marker to any page.
* Compatible with multilingual plugins like [WPML](https://wpml.org/plugin/wp-store-locator/) and qTranslate X.
* You can drag the marker in the editor to the exact location on the map.
* Show the search results either underneath the map, or next to it.
* Show Google Maps in different languages, this also influences the language for the driving directions.
* Show the driving directions to the stores.
* Customize the max results and search radius values that users can select.
* Users can filter the returned results by radius, max results or category.
* Supports [marker clusters](https://developers.google.com/maps/articles/toomanymarkers?hl=en#markerclusterer).
* Customize map settings like the terrain type, location of the map controls and the default zoom level.
* Use the Geolocation API to find the current location of the user and show nearby stores.
* Developer friendly code. It uses custom post types and includes almost 30 different [filters](http://wpstorelocator.co/documentation/filters/) that help you change the look and feel of the store locator.

= Documentation =

Please take a look at the store locator [documentation](http://wpstorelocator.co/documentation/) before making a support request.

* [Getting Started](http://wpstorelocator.co/documentation/getting-started/)
* [Troubleshooting](http://wpstorelocator.co/documentation/troubleshooting/)
* [Customisations](http://wpstorelocator.co/documentation/customisations/)
* [Filters](http://wpstorelocator.co/documentation/filters/)

= Premium Add-ons =

The following store locator add-ons will be available soon.
 
= CSV Import / Export = 
* Bulk import locations by importing a .CSV file.

= Statistics = 
* Keep track where users are searching, and see where there is demand for a possible store.

= Search Widget = 
* Enable users to search from a sidebar widget for nearby store locations.

= Store Directory =
* Generate a directory based on the store locations.

== Installation ==

1. Upload the `wp-store-locator` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add your stores under 'Store Locator' -> Add Store
1. Add the map to a page with this shortcode: [wpsl]

== Frequently Asked Questions ==

= How do I add the store locator to a page? =

Add this shortcode [wpsl] to the page where you want to display the store locator.

= The map doesn't display properly. It's either broken in half or doesn't load at all. =

Make sure you have defined a start point for the map under settings -> Map Settings.

= The map doesn't work anymore after installing the latest update =

If you use a caching plugin, or a service like Cloudflare, then make sure to flush the cache.

= Why does it show the location I searched for in the wrong country? =

Some location names exist in more then one country, and Google will guess which one you mean. This can be fixed by setting the correct 'Map Region' on the settings page -> API Settings.

= The store locator doesn't load, it only shows the number 1? =

This is most likely caused by your theme using ajax navigation ( the loading of content without reloading the page ), or a conflict with another plugin. Try to disable the ajax navigation in the theme settings, or deactivate the plugin that enables it to see if that solves the problem.

If you don't use ajax navigation, but do see the number 1 it's probably a conflict with another plugin. Try to disable the plugins one by one to see if one of them is causing a conflict.

If you find a plugin or theme that causes a conflict, please report it on the [support page](http://wordpress.org/support/plugin/wp-store-locator).

> You can find the full documentation [here](http://wpstorelocator.co/documentation/).

== Screenshots ==

1. Front-end of the plugin
2. The driving directions from the user location to the selected store
3. The 'Store Details' section
4. The plugin settings

== Changelog ==

= 2.0.4, November 23, 2015 =
* Fixed: HTML entity encoding issue in the marker tooltip, via [momo-fr](https://wordpress.org/support/profile/momo-fr) and [js-enigma](https://wordpress.org/support/profile/js-enigma).
* Fixed: Missing tooltip text for the start marker, and the info window for the start marker breaking when the Geolocation API successfully determined the users location.
* Fixed: Multiple shortcode attributes ignoring the "false" value, via [dynamitepets](https://wordpress.org/support/profile/dynamitepets) and [drfoxg](https://profiles.wordpress.org/drfoxg/).
* Changed: If a WPML compatible plugin is detected, a notice is shown above the label section explaining that the "String Translations" section in the used multilingual plugin should be used to change the labels.
* Changed: Removed the "sensor" parameter from the Google Maps JavaScript API. It triggered a 'SensorNotRequired' [warning](https://developers.google.com/maps/documentation/javascript/error-messages).
* Changed: Updated translation files.

= 2.0.3, October 27, 2015 =
* Fixed: The default search radius is no longer ignored if the Geolocation API is used. Via [xeyefex](https://wordpress.org/support/profile/xeyefex).
* Changed: Replaced get_page ( deprecated ) with get_post.
* Changed: Adjusted the position, and size of the reset map / current location icon to make them match with the new [control styles](http://googlegeodevelopers.blogspot.com/2015/09/new-controls-style-for-google-maps.html) introduced in v3.22 of the Google Maps API.
* Changed: Made it harder for themes to overwrite the icon font that is used to show the reset map / current location icon.
* Changed: Removed support for the map's pan control and zoom control style from the settings page and [wpsl_map] shortcode attributes. They are both [deprecated](https://developers.google.com/maps/articles/v322-controls-diff) in v3.22 of the Google Maps API.

= 2.0.2, September 19, 2015 =
* Fixed: Not all users always seeing the notice to convert the 1.x locations to custom post types.
* Fixed: Prevented empty search results from ending up in the autoload transient.
* Fixed: The autoload transient not being cleared after changing the start location on the settings page.
* Changed: Added extra CSS to make it harder for themes to turn the map completely grey, and set the default opening hours alignment to left.
* Changed: If you use the store locator in a tab, then it no longer requires the tab anchor to be 'wpsl-map-tab'. You can use whatever you want with the 'wpsl_map_tab_anchor' filter.

= 2.0.1, September 10, 2015 =
* Fixed: Prevented other plugins that use [underscore](http://underscorejs.org/) or [backbone](http://backbonejs.org/) from breaking the JavaScript templates, via [fatman49](https://profiles.wordpress.org/fatman49/) and [zurf](https://profiles.wordpress.org/zurf/).
* Fixed: Street view not showing the correct location after using it more then once, via [marijke_25](https://profiles.wordpress.org/marijke_25/).

= 2.0, September 7, 2015 =
* New: Moved away from a custom db table, the store locations are now registered as custom post types. 
* Note: The upgrade procedure will ask you to convert the current store locations to custom post types. This takes around 1 minute for every 1000 store locations.
* New: The option to enable/disable permalinks for the stores, and set a custom slug from the settings page.
* New: Three new [shortcodes](http://wpstorelocator.co/document/shortcodes/): [wpsl_map], [wpsl_hours] and [wpsl_address].
* New: A template attribute for the [wpsl](http://wpstorelocator.co/document/shortcodes/#store-locator) shortcode, via [Damien Carbery](http://www.damiencarbery.com).
* New: Supports [WPML](https://wpml.org/) and [qTranslate X](https://wordpress.org/plugins/qtranslate-x/).
* New: A textarea on the settings page where you can paste JSON code to create a [custom map style](https://developers.google.com/maps/documentation/javascript/styling).
* New: The option to hide the search radius dropdown on the frontend.
* New: A [wpsl_geolocation_timeout](http://wpstorelocator.co/document/wpsl_geolocation_timeout/) filter.
* New: The option to choose between different address formats, and a [filter](http://wpstorelocator.co/document/wpsl_address_formats/) to add custom ones.
* New: The option to use the [InfoBox](http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/docs/reference.html) library to style the info window.
* New: The option to choose between two different effects when a user hovers over the result list.
* New: Set the opening hours through dropdowns instead of a textarea.
* New: Filters that make it possible to add custom store data, and change the HTML structure of the info window and store listing template.
* New: The option to define a max location load if the auto loading of locations is enabled.
* New: The option to enable/disable scroll wheel zooming and the map type control on the map.
* New: Added 'Email' and 'Url' to the labels on the settings page.
* New: Added a general settings and documentation link to the plugin action links.
* New: The option to set a max auto zoom level to prevent the auto zoom from zooming to far.
* New: The option to set a different map type for the location preview.
* New: A check to see if the [SCRIPT_DEBUG](https://codex.wordpress.org/Debugging_in_WordPress#SCRIPT_DEBUG) constant is set, if this is the case the full scripts are loaded, otherwise the minified scripts are used.
* New: A [wpsl_thumb_size](http://wpstorelocator.co/document/wpsl_thumb_size/) filter that enables you to set the thumb size on the frontend without editing CSS files.
* New: The option to hide the distance in the store listing.
* New: Added JS code that prevents a grey map when the store locator is placed in a tab. This does require the use of a #wpsl-map-tab anchor.
* New: Portuguese translation via [Rúben Martins](http://www.rubenmartins.pt/).
* Changed: Better error handling for the Geolocation API.
* Changed: Regardless of the selected template, the store map is always placed before the store list on smaller screens.
* Changed: The wp-content/languages folder is checked for translations before using the translations in the plugin folder.
* Changed: The 'reset map' button now uses an icon font, and is placed in right bottom corner together with a new 'current location' icon.
* Changed: The cluster marker image will use HTTPS when available.
* Changed: Increased the default Geolocation timeout from 3000 to 5000 ms.
* Changed: The geocode requests to the Google Maps API will always use HTTPS.
* Changed: Instead of curl or file_get_contents the Google Maps API request will now use [wp_remote_get](https://codex.wordpress.org/Function_Reference/wp_remote_get).
* Changed: Replaced the 'wpsl_capability' filter with a 'Store Locator Manager' [role](http://wpstorelocator.co/document/roles/).
* Changed: Added an extra check in JS to prevent the search radius or max results value being set to NaN.
* Changed: The [wpsl_templates](http://wpstorelocator.co/document/wpsl_templates/) filter now expects an id field to be present in the array.
* Changed: Renamed the 'wpsl_gmap_api_attributes' filter to [wpsl_gmap_api_params](http://wpstorelocator.co/document/wpsl_gmap_api_params/).
* Changed: Added the 'enableHighAccuracy' parameter to the Geolocation request to make it more accurate on mobile devices.
* Fixed: An issue that prevented the settings page from saving the changes on servers that used the mod_security module.
* Fixed: The pan control option not working on the frontend if it was enabled on the settings page.
* Fixed: Prevented an empty comma from appearing in the direction URL if the zip code didn't exist.
* Fixed: Modified the CSS to prevent themes hiding the map images.
* Fixed: Dragging the store location marker in the store editor would sometimes return the incorrect coordinates.
* Fixed: The 'Back' button appeared multiple times after the user clicked on the 'Directions' link from different info windows.
* Fixed: The dropdown fields not being restored to the default values after the 'reset map' button was clicked.
* Note: Requires at least WP 3.7 instead of WP 3.5.

= 1.2.25 =
* Fixed: The store search breaking after the reset button was clicked, via [Drew75](https://wordpress.org/support/profile/drew75)
* Fixed: Two PHP notices.

= 1.2.24 =
* Fixed: Clicking the marker would no longer open the info window after a Google Maps API update. This only happened if street view was enabled.
* Fixed: A fatal error on some installations caused by the usage of mysql_real_escape_string, it is replaced with esc_sql.
* Fixed: A problem where some themes would just show "1" instead of the shortcode output.
* Fixed: The "dismiss" link not working in the notice that reminds users to define a start point.
* Fixed: A missing html tag that broken the store listing in IE7/8.
* Changed: Replaced the non-GPL compatible dropdown script.

= 1.2.23 =
* Fixed the geocoding request for the map preview on the add/edit page not including the zipcode when it's present, which can misplace the marker

= 1.2.22 =
* Fixed compatibility issues with the Google Maps field in the Advanced Custom Fields plugin
* Fixed the store urls in the store listings sometimes breaking
* Removed the requirement for a zipcode on the add/edit store page
* Improved the documentation in the js files

= 1.2.21 =
* Fixed an js error breaking the store locator

= 1.2.20 =
* Fixed the directions url sometimes showing an incomplete address due to an encoding issue
* Fixed the 'items' count on the store overview page showing the incorrect number after deleting a store
* Fixed the autocomplete for the 'start point' field sometimes not working on the settings page
* Fixed php notices breaking the store search when wp_debug is set to true
* Fixed the bulk actions when set to 'Bulk Actions' showing the full store list without paging
* Fixed small css alignment issues in the admin area
* Fixed the js script still trying to load store data when autoload was disabled
* Fixed the clickable area around the marker being to big
* Improved: After a user clicks on 'directions' and then clicks 'back', the map view is returned to the original location
* Removed: the 'Preview location on the map' button no longer updates the zip code value it receives from the Google Maps API
* Changed the way the dropdown filters are handled on mobile devices. They are now styled and behave according to the default UI of the device
* Added support for WP Multisite
* Added 'Screen Options' for the 'Current Stores' page, so you can define the amount of stores that are visible on a single page
* Added the option to make phone numbers clickable on mobile devices by adding a link around them with 'tel:'
* Added the option to make store names automatically clickable if the store url exists
* Added the option to show a 'zoom here' and 'street view' (when available) into the infowindow
* Added a second address field to the store fields
* Added the option to enable marker clusters
* Added the option to set a default country for the "Add Store" page
* Added Dutch (nl_NL) translations
* Added a .pot file to the languages folder for translators
* Added error handling for the driving directions
* Added several filters for developers: 
'wpsl_templates' for loading a custom template from another directory
'wpsl_menu_position' for adjusting the position of the store locator menu in the admin panel
'wpsl_capability' to manually set the required user capability for adding/editing stores
'wpsl_gmap_api_attributes' to modify the Google maps parameters ( change the map language dynamically )

= 1.2.13 =
* Fixed the store search not returning any results when the limit results dropdown is hidden

= 1.2.12 =
* Added an option to choose where the 'More info' details is shown, either in the store listings or on the map
* Added the 'back' and 'reset' text to the label fields on the settings page
* Added the option to remove the scrollbar when the store listings are shown below the map
* Improved the position of the reset button when the map controls are right aligned
* Fixed the 'More info' translation not working
* Fixed the start position marker disappearing when dragged

= 1.2.11 =
* Fixed the distance format always using km when you click the 'directions' text in the marker
* Fixed an issue where a CSS rule in some themes would place a background image on the active item in the dropdown list
* Added an option to disable the mouse cursor on pageload focusing on the location input field 
* Added an option to add a 'More info' link to the store listings, which when clicked will open the info window in the marker on the map

= 1.2.1 =
* Added an option to show the store listings below the map instead of next to it
* Added an option to open the directions in a new window on maps.google.com itself
* Fixed a 'too much recursion' js error that showed up when no start location was defined
* Fixed the auto loading of stores not being ordered by distance
* Fixed a problem with the input fields not always aligning in Chrome
* Improved the handling of thumbnails. If the thumbnail format is disabled in the theme, it will look for the medium or full format instead
* Several other small code improvements

= 1.1 =
* Added the option to open a link in a new window
* Added the option to show a reset button that will reset the map back to how it was on page load
* Added the option to load all stores on page load
* Fixed a problem with the shortcode output

= 1.0.1 =
* Fixed the styling for the store locator dropdowns being applied site wide
* Fixed a problem with slashes in store titles

= 1.0 =
* Initial release