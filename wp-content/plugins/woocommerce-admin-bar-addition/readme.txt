=== WooCommerce Admin Bar Addition ===
Contributors: daveshine, deckerweb
Donate link: http://genesisthemes.de/en/donate/
Tags: toolbar, tool bar, adminbar, admin bar, woocommerce, woo commerce, woothemes, administration, resources, links, shop, shop manager, deckerweb, ddwtoolbar
Requires at least: 3.6 plus WooCommerce 2.x
Tested up to: 3.8.1
Stable tag: 2.7.0
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php

This plugin adds useful admin links and massive resources for the WooCommerce Shop Plugin to the WordPress Toolbar / Admin Bar.

== Description ==

> #### Have Quicker Access to Your WooCommerce Shop Resources - Time Saver!
> This **small and lightweight plugin** just adds a lot WooCommerce related resources to your WordPress Toolbar / Admin Bar.
>
> Also links to all setting/ tab pages of the plugin are added making life for shop administrators/ webmasters a lot easier. So you might just switch from the frontend of your site to 'Payment Settings' page or check recent 'Orders' etc.

= General Features =
* The plugin is **primarily intended towards shop managers, site admins and webmasters**.
* All WooCommerce shop settings, products and orders management as well as extensions are hooked in!
* Supporting all official and most third-party plugins with own settings pages!
* Access WooCommerce resources from your dashboard with one click - or just search the documentation.
* 4 action hooks included for hooking custom menu items in -- for all main sections plus the resource group section ([see Other Notes section here for more info on that](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)).
* 7 additional icon colors included :) (changeable via filters)
* Plus 7 filters included to change wording/tooltip and icon of the main item - for more info [see Other Notes section here](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
* For custom "branding" or special needs a few sections like "Extensions" and "Resource links group" could be hidden from displaying via your active theme/child theme - for more info [see Other Notes section here](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
* Fully internationalized! Real-life tested and developed with international users in mind!
* Fully WPML compatible!
* Fully Multisite compatible, you can also network-enable it if ever needed (per site use is recommended).
* Developed, tested and fully maintained since the WooCommerce 1.2.x branch up to now! Plugin also supported by WooCommerce main developer!
* As of this plugin version 2.7.0 or higher: Only WooCommerce v2.1.0 or higher, and legacy branch 2.0.x are supported! So please update your installs!
* Tested with WordPress branch 3.8 also in debug mode (no stuff there, ok? :) (Note: Plugin should also run with WP 3.6+ but please always update to latest version, ok?!)

= Special Features =
* Not only supporting official WooCommerce sites ALSO third-party and user links - so just the whole WooCommerce ecosystem :)
* Link to German language/translation plugin - only displayed when German locales are active (de_DE, de_AT, de_CH, de_LU, gsw)
* Link to downloadable German language packs - only displayed when German locales are active (de_DE, de_AT, de_CH, de_LU, gsw)
* Link to Dutch language/translation plugin - only displayed when Dutch locales are active (nl_NL or nl)
* Link to Spanish language/translation plugin - only displayed when Spanish locales are active (es_ES or es_PE)
* Link to Czech language/translation plugin - only displayed when Czech locale is active (cs_CZ)
* *NOTE:* I would be happy to add more language/locale specific resources and more useful third-party links - just contact me!

= Requiremets =
* Technically speaking this plugin runs also without WooCommerce 2.x being active
* BUT: it really makes no sense to use it without WooCommerce 2.x!
* WordPress 3.6 or higher, but latest version always recommended!

As the name suggests this plugin is **intended towards admins/ webmasters/ shop managers**. The new admin bar entries will only be displayed if the current user has the WooCommerce capability of `manage_woocommerce` (a.k.a. user role "Shop Manager"). (Note: I am open for suggestions here if the default value should maybe changed to a more suitable capability. However, you can now tweak this also yourself using our new filter :)

**Currently supporting:** 27 official premium extensions // 15 free third-party extensions // 13 third-party premium themes/frameworks // 1 free third-party theme // plus all official free/premium WooCommerce themes by WooThemes!

= Plugin Extensions Support =
*At this time the plugin out of the box supports links to settings pages of these WooCommerce-specific extensions/plugins:*

* **Official plugin extensions from WooThemes:** for a full (and growing) list [see here under "Other Notes" ...](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
* Plugin extension: ["WooCommerce Print Invoices & Delivery Notes" (free, by Steve Clark, Trigvvy Gunderson, David Decker)](http://wordpress.org/plugins/woocommerce-delivery-notes/)
* Plugin extension: ["WooCommerce Compare Products Lite" (free, by A3 Revolution Software Development)](http://wordpress.org/plugins/woocommerce-compare-products/)
* Plugin extension: ["WooCommerce Custom Status" (free, by Garman Technical Services)](http://wordpress.org/plugins/woocommerce-custom-statuses/)
* Plugin extension: ["WooCommerce Pay to Upload" (free, by Patrick Garman)](http://wordpress.org/plugins/woocommerce-pay-to-upload/)
* Plugin extension: ["WooCommerce Cloud Zoom Image" (free, by mRova)](http://wordpress.org/plugins/cloud-zoom-for-woocommerce/)
* Plugin extension: ["WooCommerce - Exporter" (free, by Visser Labs)](http://www.visser.com.au/woocommerce/plugins/exporter/)
* Plugin extension: ["WooCommerce - Store Toolkit" (free, by Visser Labs)](http://www.visser.com.au/woocommerce/plugins/store-toolkit/)
* Plugin extension: ["Pronamic iDEAL" Payment Gateway (free, by Pronamic, NL)](http://wordpress.org/plugins/pronamic-ideal/)
* Plugin extension: ["Mediaburst: Ecommerce SMS Notifications" (free, by Mediaburst + Simon Wheatley)](http://wordpress.org/plugins/mediaburst-ecommerce-sms-notifications/)
* Plugin extension: ["KISS Metrics for WooCommerce" (free, by Max Rice)](http://wordpress.org/plugins/kiss-metrics-for-woocommerce/)
* Plugin extension: ["Affiliates WooCommerce Integration Light" (free, by Karim Rahimpur)](http://wordpress.org/plugins/affiliates-woocommerce-light/)
* Plugin: upcoming "WooCommerce German Extension" (premium?, by Inpsyde GmbH) - only for German locales
* Plugin: ["WP e-Commerce to WooCommerce Converter" (free, by ralcus at GitHub)](https://github.com/ralcus/wp-e-commerce-to-woocommerce-converter)
* Plugin: ["Smart Manager for WP e-Commerce & WooCommerce" (free lite version, by Store Apps)](http://wordpress.org/plugins/smart-manager-for-wp-e-commerce/)
* *More support is planned as there are new releases of extensions but I only could include what I have or get from plugin authors to test.*
* *Your free or premium WooCommerce-specific plugin extension? - [Just contact me with specific data](http://genesisthemes.de/en/contact/)*

= Theme/Framework Support =
*At this time the plugin out of the box supports links to settings pages of these WooCommerce-specific themes:*

* Themes/ Framework: all themes from "WooThemes" (free & premium)
* Themes: "Abundance", "Propulsion", "Flashlight" (all premium, [by Kriesi](http://www.kriesi.at/) at ThemeForest)
* Themes: "Clean Cut eCommerce", "Handmade eCommerce", "mag.press eCommerce", "Kiosk", "Gigawatt eCommerce" (all premium, [by Obox Design](http://www.obox-design.com/) & at ThemeForest)
* Themes: "WP Sharp", "Flexishop 2" (both premium, [by PrimaThemes](http://www.primathemes.com/) at ThemeForest)
* Themes: "Wardrobe", "Sneakers Addict", "Eureka" (all premium, by [ColorLabs & Company](http://colorlabsproject.com/))
* Theme: "Sommerce" (premium, by Sara_p at ThemeForest)
* Theme: "ShopDock" (premium, by Themify)
* Theme: "TeeShirt Lite" (free, by WooCommerceThemes.me)
* Theme Framework plus child themes: "Genesis" (premium, by StudioPress) via free plugin ["Genesis Connect for WooCommerce"](http://wordpress.org/plugins/genesis-connect-woocommerce/)
* *More support is planned as there are new releases of WooCommerce-specific themes but I only could include what I have or get from theme authors to test.*
* *Your free or premium WooCommerce-specific theme/framework? - [Just contact me with specific data](http://genesisthemes.de/en/contact/)*

= Translations: Internationalization (i18n) / Localization (L10n) =
* English (default) - always included
* German (de_DE) - always included
* French (fr_FR) - user-submitted - 72% complete for v2.7.0
* Italian (it_IT) - user-submitted - 72% complete for v2.7.0
* Danish (da_DK) - user-submitted - 72% complete for v2.7.0
* Spanish (es_ES) - user-submitted - 45% complete for v2.7.0
* Polish (pl_PL) - user-submitted - 19% complete for v2.7.0
* .pot file (`woocommerce-admin-bar-addition.pot`) for translators is also always included :)
* Easy plugin translation platform with GlotPress tool: [Translate "WooCommerce Admin Bar Addition"...](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/woocommerce-admin-bar-addition)
* *Your translation? - [Just send it in](http://genesisthemes.de/en/contact/)*

Credit where credit is due: This plugin here is inspired and based on the work of Remkus de Vries @defries and his original "WooThemes Admin Bar Addition" plugin.

[A plugin from deckerweb.de and GenesisThemes](http://genesisthemes.de/en/)

= Feedback =
* I am open for your suggestions and feedback - Thank you for using or trying out one of my plugins!
* Drop me a line [@deckerweb](https://twitter.com/#!/deckerweb) on Twitter
* Follow me on [my Facebook page](https://www.facebook.com/deckerweb.service)
* Or follow me on [+David Decker](https://plus.google.com/+DavidDecker/posts) on Google Plus ;-)

= This Plugin... =
* ...is *Quality Made in Germany*
* ...was created with love (and some coffee) on an [Ubuntu Linux](http://www.ubuntu.com/desktop) powered machine :)

= More =
* [Also see my other plugins](http://genesisthemes.de/en/wp-plugins/) or see [my WordPress.org profile page](http://profiles.wordpress.org/daveshine/)
* Tip: [*GenesisFinder* - Find then create. Your Genesis Framework Search Engine.](http://genesisfinder.com/)

== Installation ==

= Installation Steps =
1. Installing alternatives:
 * *via Admin Dashboard:* Go to 'Plugins > Add New', search for "WooCommerce Admin Bar Addition", click "install"
 * *OR via direct ZIP upload:* Upload the ZIP package via 'Plugins > Add New > Upload' in your WP Admin
 * *OR via FTP upload:* Upload `woocommerce-admin-bar-addition` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Look at your toolbar / admin bar and enjoy using the new links there :)
4. Go and manage your shop - good luck with sales :)

= Requirements =
* Technically speaking this plugin runs also without WooCommerce 2.x being active
* BUT: it really makes no sense to use it without WooCommerce 2.x!
* WordPress 3.6 or higher, but latest version always recommended!
* WooCommerce branch compat: As of this plugin version 2.7.0 or higher: Only WooCommerce v2.1.0 or higher, and legacy branch 2.0.x are supported! So please update your installs!

**Note:** This plugin has NO settings page because I believe it's just not neccessarry. All customizing could be done via action hooks, filters, constants and regular WordPress user roles & capabilities (see ["Other Notes"](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/) section here). As the plugin is indended for a shop manager/ admin/ webmaster use that's the way to go. This way we can save the overhaul of an options panel/settings page, additional database requests, uninstall routines and such. End result: a lightweight system that just works and saves clicks & time :-).

**Multisite install:** Yes, it's fully compatible but have a look in the [FAQ section here](http://wordpress.org/plugins/woocommerce-admin-bar-addition/faq/) for more info :)

**Own translation/wording:** For custom and update-secure language files please upload them to `/wp-content/languages/wcaba/` (just create this folder) - This enables you to use fully custom translations that won't be overridden on plugin updates. Also, complete custom English wording is possible with that, just use a language file like `wcaba-en_US.mo/.po` to achieve that (for creating one see the tools on "Other Notes").

== Frequently Asked Questions ==

= How are new resources being added to the admin bar? =
Just drop me a note on [my Twitter @deckerweb](https://twitter.com/deckerweb) or via my contact page and I'll add the link if it is useful for admins/ webmasters and the WooCommerce community.

= How could my plugin/extension or theme options page be added to the admin bar links? =
This is possible of course and highly welcomed! Just drop me a note on [my Twitter @deckerweb](https://twitter.com/deckerweb) or via my contact page and we sort out the details!

Particularly, I need the admin url for the primary options page (like so `wp-admin/admin.php?page=foo`) - this is relevant for both, plugins and themes. For themes then I also need the correct name defined in the stylesheet (like so `Footheme`).

= There are still some other plugins/ extensions/ add-ons for WooCommerce out there why aren't these included by default? =
Simple answer: The settings of these add-ons are added directly to the various WooCommerce settings pages or tabs and have no anchor to link to. So linking/ adding is just impossible.

= How can I customize & brand the admin bar / toolbar items? =
All this could be done via our filters and constants. Really lightweight and simple. [Just step up the "Other Notes" area here](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/) and scroll down to the proper section.

= Is this plugin Multisite compatible? =
Yes, it is! :) Works really fine in Multisite invironment - here I just recommend to activate on a per site basis so to load things only where and when needed.

= In Multisite, could I "network enable" this plugin? =
Yes, you could. -- However, it doesn't make much sense. The plugin is intented for a per site use as the admin links refer to the special settings, plugin-support and theme-support for that certain site/blog. So if you have a Multisite install with 5 sites but only 3 would run "WooCommerce" then the other 2 sites will only see support links in the Toolbar / Admin Bar... I guess, you got it? :)

Though intended for a per site use it could make some sense in such an edge case: if all of the sites in Multisite use WooCommerce and have lots of WooCommerce-specific plugins in common and use the same theme/framework. This might be the case if you use Multisite for multilingual projects, especially via that awesome plugin: http://wordpress.org/plugins/multilingual-press/

= Why is there no admin settings page to this plugin? =
This plugin has NO settings page because I believe it's just not neccessarry. All customizing could be done via CSS styling, filters, constants and regular WordPress user roles & capabilities. As the plugin is indended for a shop manager/ admin/ webmaster use that's the way to go. This way we can save the overhaul of an options panel/ settings page, additional database storing & requests, uninstall routines and such. End result: a lightweight system that just works and saves clicks & time :-).

== Screenshots ==

1. WooCommerce Toolbar / Admin Bar Addition in action - default status. ([Click here for larger version of screenshot](https://www.dropbox.com/s/v6kj5kcbfo22y1a/screenshot-1.png))

2. WooCommerce Toolbar / Admin Bar Addition in action - shop settings, plus sub items. ([Click here for larger version of screenshot](https://www.dropbox.com/s/vaf8ntu2jtz5ig1/screenshot-2.png))

3. WooCommerce Toolbar / Admin Bar Addition in action - support & resources links. ([Click here for larger version of screenshot](https://www.dropbox.com/s/bz7ili5a0xyday1/screenshot-3.png))

4. WooCommerce Toolbar / Admin Bar Addition in action - more WooCommerce related resources. ([Click here for larger version of screenshot](https://www.dropbox.com/s/n9da4a0o8x98bit/screenshot-4.png))

5. WooCommerce Toolbar / Admin Bar Addition in action - with German translation active, and [German language plugin](http://wordpress.org/plugins/woocommerce-de/) support. ([Click here for larger version of screenshot](https://www.dropbox.com/s/1cooj8uh52liei8/screenshot-5.png))

6. WooCommerce Toolbar / Admin Bar Addition in action - mobile support: view your orders on the go :). ([Click here for larger version of screenshot](https://www.dropbox.com/s/layrznf1no1ynok/screenshot-6.png))

7. WooCommerce Toolbar / Admin Bar Addition in action - plugin help tab. ([Click here for larger version of screenshot](https://www.dropbox.com/s/aplkguj45ye1bfi/screenshot-7.png))

== Changelog ==

= 2.7.0 (2014-02-02) =
* NEW: Added full WooCommerce 2.1.0 compatibility -- by keeping backwards compat for former 2.0.x branch (that will be for a certain time, not always!).
* UPDATE: Changed main item link (top level directly in Toolbar) to point to *Orders* page -- this is more expected behavior and was requested by some users.
* UPDATE: Changed Order of main items: now beginning with *Orders*, then *Reports*, *Products*, *Coupons*, *Settings* --- this way it's more handy for daily shop maintenance work :).
* UDAPTE: Updated German translations and also the .pot file for all translators!
* UPDATE: Updated all user-submitted translations.

= 2.6.0 (2014-01-23) =
* *New features:*
 * NEW: Added some new settings and resources links.
 * NEW: Use WordPress 3.8 Dashicons for Toolbar (font) icon! :) (Old image icon approach only for backwards compat!)
 * NEW: Added essential mobile (!) support - that means: on mobile devices the main item link url switches from "settings" to "orders", plus, only most important sub items are displayed (see [this screenshot](https://www.dropbox.com/s/layrznf1no1ynok/screenshot-6.png) for example).
 * NEW: Added plugin help tab to WooCommerce settings pages.
* *Extended theme support:*
 * NEW: Added support for "Kiosk" and "Gigawatt eCommerce" (both premium, by Obox Design)
* *Extended plugin extension support:*
 * NEW: Added language plugin support for "WooCommerce čeština" (free, by Expres-Web.cz)
 * NEW: Added language plugin support for "WooCommerce (es)" (free, by Closemarketing)
 * NEW: Added plugin support for "WooCommerce Pay to Upload" (free, by Patrick Garman)
* *Other stuff & maintenance:*
 * UPDATE: Updated/ fixed all settings and resources links.
 * CODE: Code & documentation improvements, partly refactoring.
 * UPDATE: **Changed plugin's textdomain** from `wcaba` to `woocommerce-admin-bar-addition` to match the plugin's slug!
 * UDAPTE: Updated German translations and also the .pot file for all translators!
 * NEW: Added new - partly - translations for: French (fr_FR), Italian (it_IT), Danish (da_DK), Spanish (es_ES), Polish (pl_PL). All were user-submitted - thank you!
 * UPDATE: Initiated new three digits versioning, starting with this version.
 * UPDATE: All screenshots; readme.txt file; tested with WordPress branch 3.8.

= 2.5.0 (2012) =
* *private beta, unreleased*

= 2.4.0 (2012-05-18) =
* *Maintenance release.*
* NEW: Added plugin extension support for new "WooCommerce Software Add-On" -- one more official extension available through WooThemes - [see "Other Notes" here for the full listing!](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
* NEW: Added plugin extension support for "KISS Metrics for WooCommerce" (free, by Max Rice).
* NEW: Added sub-level links for the "Integration" settings link, this way I can provide support for third-party extensions here.
* NEW: Added new constant for customizing, 'WCABA_REPORTS_DISPLAY' lets you remove the reports/statistics items - comes in handy if you have lots of shop managers and want to customize your wp-admin more user-specific.
* UPDATE: Improved behavior of constants for removing sections (or all), so that setting to "FALSE" removes stuff, and setting to "TRUE" displays stuff. (This does not affect existing behavior as explained in the FAQ but introduces ability to use the boolean "TRUE" to bring stuff back in favor of removing the code lines - great for testing purposes etc.)
* FIX: Bugfix for "Pronamic iDEAL" plugin support (syntax error).
* CODE: Minor code & documentation improvements.
* UDAPTE: Updated German translations and also the .pot file for all translators!

= 2.3.1 (2012-05-14) =
* *Bugfix release.*
* BUGFIX: Fixed a link within the "WooCommerce Product CSV Import Suite" plugin extension support.
* UDAPTE: Updated German translations and also the .pot file for all translators!

= 2.3.0 (2012-05-11) =
* *New features:*
 * NEW: Added new "WooCommerce Debug" settings link.
 * NEW: Added 4 action hooks for hooking custom menu items in -- for all main sections plus the resource group section ([see FAQ section here](http://wordpress.org/plugins/woocommerce-admin-bar-addition/faq/) for more info on that).
* *Extended extensions/plugin support:*
 * NEW: Added plugin extension support for 4+ more official extensions available through WooThemes - [see "Other Notes" here for the full listing!](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
 * NEW: Added plugin support for "WooCommerce Custom Status" (free, by Garman Technical Services)
 * NEW: Added plugin support for "WooCommerce Cloud Zoom Image" (free, by mRova)
 * UPDATE: Updated plugin support for "WooCommerce Print Invoices & Delivery Notes" to v1.2+ of that extension.
 * UPDATE: Updated plugin support for "WooCommerce Compare Products Lite" to v1.0.3 of that extension.
* *Extended theme support:*
 * NEW: Added theme support for "WP Flexishop 2" by Primathemes (premium, at ThemeForest).
 * NEW: Added theme support for "mag.Press eCommerce" (premium, by Obox Design)
* *More stuff:*
 * UPDATE: Tweaked reports settings links; removed ugly patch from last update due to update in WooCommerce itself.
 * CODE: Minor code & documentation tweaks
 * UDAPTE: Updated German translations and also the .pot file for all translators!
 * UPDATE: Extended GPL License info in readme.txt as well as main plugin file.
 * NEW: Easy plugin translation platform with GlotPress tool: [Translate "WooCommerce Admin Bar Addition"...](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/woocommerce-admin-bar-addition)

= 2.2.0 (2012-03-23) =
* Woot, major updates on almost all fronts! :)
* *New features:*
 * COOL: Plugin can now be branded and customized a lot more!
 * NEW: Added 7 filters to change icon graphic, main item name, main capability and more! For these cases there are now the new built-in filters and helper functions available! [(See "Other Notes" here)](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
 * NEW: Plugin now goes hand in hand with the official "WooCommerce Branding" extension (only if that one is activated, of course) -- using custom name, icon and color scheme together! (See "Other Notes" here)
 * NEW: Almost all sections can now be removed for special needs, capabilities etc. -- all done via `constants` in your active theme/child theme -- this way you can customize for your staff members if you need some more users with extended or restricted admin bar/toolbar access (See "Other Notes" here)
 * NEW: Via `add_theme_support` you can now add all WooCommerce order status with counters to the "Orders" item -- I decided to make it optional because getting the counts will need a database call, so not every admin will want this! (See "Other Notes" here)
 * NEW: Via `add_theme_support` you can now add all frontend shop links (all special shop, cart etc. pages) to the main level "Orders" -- not every shop manager needs that so I decided to make it optional (See "Other Notes" here)
 * NEW: Added search form for official "WooComerce Docs" site directly to the main level (in Resources section) - note: search results will open in new window/tab!
* *More updates, additions and improvements:*
 * NEW: Added new "Tutorials" and "Community Videos" links to "Docs" item.
 * UPDATE: Added proper capability checks to all plugin and theme menu items - the links are now following the same capabilities like in the original plugins/themes. So, if a user don't sees any left-hand admin menu items the same user won't see anything of this in the toolbar!
 * UPDATE: Added an interim patch to get somewhat locale-independent reports admin urls - now for ALL locales - getting the slug strings directly from the language files of WooCommerce itself -- Therefore the 3 strings ("sales", "customers", "stock") should NOT be translated in my plugin language files! -- Waiting for "WooCommerce" to switch to IDs for the tabs slugs...
 * UPDATE: Minor code and documentation improvements/tweaks.
 * UPDATE: Updated readme.txt file with new and updated documentation/instructions etc.
 * UPDATE: Updated some of the screenshots (more might follow soon...)
* *Extended extensions/plugin support:*
 * NEW: Added plugin extension support for 2+ more official extensions available through WooThemes - [see "Other Notes" here for the full listing!](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
 * NEW: Added plugin support for "WooCommerce Compare Products Lite" (free, by A3 Revolution Software Development)
 * Updated: Further extended plugin support for upcoming "WooCommerce German Extension" (by Inpsyde GmbH; only for German locale!)
* *Theme support related stuff:*
 * Optimized links display with added capability check and also removed overhaul regarding translation strings.
* *Translation related stuff:*
 * Optimized language files a bit by unifying even more strings and therefore removing some overhaul!
 * UDAPTE: Updated German translations and also the .pot file for all translators!

= 2.1.0 (2012-03-04) =
* IMPROVEMENT: For WordPress 3.3+ changed display of resource links group: now at the bottom, below settings links and in WP 3.3 group style :)
* NEW: Added link to WordPress menus in "Settings" section.
* UPDATE: Removed old codex resource links.
* *Extended extensions/plugin support:*
 * NEW: Added plugin support for upcoming "WooCommerce German Extension" (by Inpsyde GmbH; only for German locale!)
 * NEW: Added plugin extension support for 6 more official extensions available through WooThemes - [see "Other Notes" here for the full listing!](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
* *Extended theme support:*
 * NEW: Added theme support for "TeeShirt Lite" (free, by WooCommerceThemes.me)
 * NEW: Added theme framework support for "Genesis Framework" (premium, by StudioPress) - in light of now official WooCommerce integration plugin :)
 * NEW: Added theme support for "Sneakers Addict" and "Eureka" (both premium, by ColorLabs & Company)
 * NEW: Added theme support for "ShopDock" (premium, by Themify)
 * NEW: Added theme support for "Sommerce" (premium, by Sara_p at ThemeForest)
* UPDATE: Minor code tweaks and improvements - also removed unneeded variable and depending PHP notice.
* UPDATE: Updated screenshots for new improved styling - and also readme.txt file.
* NEW: Added info for Multisite installs to "Installation" and "FAQ" section of readme.txt file
* NEW: Added possibility for custom and update-secure language files for this plugin - just upload them to `/wp-content/languages/wcaba/` (just create this folder) - this enables you to use fully custom wording or translations
* UPDATE: Updated all screenshots and added some new ones.
* UDAPTE: Updated German translations and also the .pot file for all translators!

= 2.0.0 (2012-02-19) =
* *The "Woot, we're already 2.0" release ;-)*
* NEW: Finally added more report links items (on sub-level) - due to language specific URLs in WooCommerce this currently works for all English and German locales
* NEW: Added *WooCommerce News Planet* feed link to resource links - you can also access this from here: http://friendfeed.com/woocommerce-news
* NEW: Added plugin extension support for 4 more official extensions available through WooThemes - [see "Other Notes" here for the full listing!](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
* CODE: No longer loading css styles or menu items for not logged-in users when plugins like "GD Press Tools" are active (which have options to show toolbar / admin bar also for visitors...)
* CODE: Minor code/ code documenation tweaks
* UDAPTE: Updated German translations and also the .pot file for all translators!

= 1.9.0 (2012-02-14) =
* Maintenance release.
* NEW: Added plugin extension support for 1 more official extension available through WooThemes - [see "Other Notes" here for the full listing!](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
* NEW: Added link to more third-party premium extensions
* BUGFIX: Fixed conditional checks for German and Dutch language plugins
* BUGFIX: Fixed "Professor Cloud Zoom" plugin check
* CODE: Minor code/ code documenation tweaks
* UDAPTE: Updated German translations and also the .pot file for all translators!

= 1.8.0 (2012-02-04) =
* NEW/UPDATE: Added link to new official Public Community Forum; further changed Documentation and User Guide links to reflect changes on official WooCommerce/WooThemes sites
* NEW: Added link to User Dashboard and User Profile at WooThemes.com
* *Extended plugin & theme support even further:*
 * NEW: Added plugin extension support for 9 (!) official extensions available through WooThemes - [see "Other Notes" here for the full listing!](http://wordpress.org/plugins/woocommerce-admin-bar-addition/other_notes/)
 * NEW: Added theme support for "Handmade eCommerce" (premium, by Obox Design)
 * NEW: Added language plugin support for my own ["WooCommerce German (de_DE)" (free, by David Decker)](http://wordpress.org/plugins/woocommerce-de/)
 * NEW: Added plugin extension support for ["Affiliates WooCommerce Integration Light" (free, by Karim Rahimpur)](http://wordpress.org/plugins/affiliates-woocommerce-light/)
* *Code improvements and tweaks:*
 * NEW: Splitted code into more files for better maintenance - plugin and theme support now have their own file.
 * UPDATE: Added WooCommerce capabilities to Products, Orders, Coupons and Reports sections to display these only for users with these caps.
 * UPDATE: Improved conditional check for Dutch language plugin (only display link if plugin is not activated).
* UPDATE: Extended readme.txt file further with more information/ documentation.
* UDAPTE: Updated German translations and also the .pot file for all translators!

= 1.7.0 (2012-01-07) =
* Added plugin extension support for ["Pronamic iDEAL" Payment Gateway (free, by Pronamic, NL)](http://wordpress.org/plugins/pronamic-ideal/)
* Added plugin extension support for ["Mediaburst: Ecommerce SMS Notifications" (free, by Mediaburst + Simon Wheatley)](http://wordpress.org/plugins/mediaburst-ecommerce-sms-notifications/)
* Added theme support for "Wardrobe" (premium, by ColorLabs & Company)
* Updated German translations and also the .pot file for all translators!

= 1.6.0 (2012-01-03) =
* Added compatibility for (upcoming) WooCommerce version 1.4+ (Added new "Integration" settings link / Edited "Shipping" setting links)
* Added checks for enhanced backwards compatibily for WooCommerce versions prior 1.4 (Please note: this might be removed over time as the older versions become outdated...)
* Added plugin support for ["Smart Manager for WP e-Commerce & WooCommerce" (free lite version, by Store Apps)](http://wordpress.org/plugins/smart-manager-for-wp-e-commerce/)
* Added theme support for "Clean Cut" (premium, by Obox Design & at ThemeForest)
* Updated German translations and also the .pot file for all translators!
* Added a few update instructions to readme.txt file

= 1.5.0 (2011-12-30) =
* Added plugin support for ["WooCommerce Delivery Notes" (free, by David Decker)](http://wordpress.org/plugins/woocommerce-delivery-notes/)
* Added theme support for "WP Sharp" (premium, by PrimaThemes at ThemeForest)
* Added theme support for "Propulsion" (premium, by Kriesi at ThemeForest)
* Added theme support for "Flashlight" (premium, by Kriesi at ThemeForest)
* Updated readme.txt file - added new "Toolbar" wording introduced with WordPress 3.3 (formerly known as the Admin Bar)
* Updated German translations and also the .pot file for all translators!
* Added banner image on WordPress.org for better plugin branding :)

= 1.4.0 (2011-12-09) =
* Added new link for Products "Shipping Classes" - new setting in WooCommerce since v1.3.1 or higher
* Added plugin support for "WP e-Commerce to WooCommerce Converter" (free, by ralcus at GitHub)
* Minor code/ code documenation tweaks
* Updated German translations and also the .pot file for all translators!

= 1.3.0 (2011-12-04) =
* Fixed display of first level icon on mouse-hover with WordPress 3.3 - props to [Dominik Schilling](http://wpgrafie.de/) [@ocean90](http://twitter.com/#!/ocean90) for great help with the CSS!
* Updated German translations and also the .pot file for all translators!
* Now I'd call this some fully optimized release - enjoy :-)

= 1.2.0 (2011-12-03) =
* Added tooltips to all Admin Bar elements
* Fixed some ugly typos (Ah, it still happens sometimes...)
* Improved readme.txt file

= 1.1.0 (2011-12-01) =
* Added theme support for "Abundance" (premium, by Kriesi at ThemeForest)
* Optimized conditional check for themes so that also child themes of these parent themes will work
* Updated readme.txt file here with new sections "Special Features" and "Credits"
* Updated German translations and also the .pot file for all translators!

= 1.0.1 (2011-12-01) =
* Just fixed the Extensions typo (Yes, it happens sometimes...)

= 1.0.0 (2011-11-30) =
* Initial release

== Upgrade Notice ==

= 2.7.0 =
Several additions & improvements: Fully WooCommerce 2.1.0 compat! Tweaked main level item order. Also, updated .pot file for translators plus all translations.

= 2.6.0 =
Several additions & improvements: Updated all links, lots of code and partly refactored. Further, updated .pot file for translators together with German translations.

= 2.4.0 =
Several additions & improvements: Extended plugin support. Code & documentation improvements. Also updated .pot file for translators together with German translations.

= 2.3.1 =
Bugfix release: Fixed a link within the "WooCommerce Product CSV Import Suite" plugin extension support.

= 2.3.0 =
Several additions & improvements: Added hooks for customizing; extended plugin & theme support. Optimized and improved code, documentation and language files. Also updated .pot file for translators together with German translations.

= 2.2.0 =
Major changes & improvements - Added filters & constants for customizing; extended plugin support. Optimized and improved code, documentation and language files. Also updated .pot file for translators together with German translations.

= 2.1.0 =
Several changes & improvements - Improved styling and support for WP 3.3+. Added 1 more settings link. Further, added more theme and plugin support. Also, code/ documentation tweaks and updated .pot file for translators together with German translations.

= 2.0.0 =
Several improvements - Added more "Reports" sub-links as well as resource links. Further, added support for 4 more official extensions. A few minor code tweaks and also updated .pot file for translators together with German translations.

= 1.9.0 =
Maintenance release - Added one more third-party resource link. Fixed conditionals for language plugins. Also, updated .pot file for translators together with German translations.

= 1.8.0 =
Major additions and improvements - Added new official resource links, changed few existing ones. Further, added support for 9 official extensions, 2 more free extensions and 1 more premium theme. Also, updated .pot file for translators together with German translations.

= 1.7.0 =
Added theme support for 1 more premium theme, also added support for 2 more plugins. Updated .pot file for translators together with German translations.

= 1.6.0 =
Added enhanced support for (upcoming) WooCommerce version 1.4 and full backwards compatibility to existing version! Added/updated/edited some setting links. Further added theme support for 1 more free plugin and 1 more premium theme. Updated .pot file for translators together with German translations.

= 1.5.0 =
Added theme support for 1 more fee plugin and 3 premium themes. Updated readme.txt file and also .pot file for translators together with German translations.

= 1.4.0 =
Added settings link to new "Shipping Classes" (since WooCommerce 1.3.1 or higher). Added plugin support for third-party plugin. A few minor code tweaks and also updated .pot file for translators together with German translations.

= 1.3.0 =
Fixed first-level icon display in WP 3.3. Updated readme.txt file, screenshots and also .pot file for translators together with German translations.

= 1.2.0 =
Added tooltips to all Admin Bar elements. Fixed some ugly typos and improved readme.txt file.

= 1.1.0 =
Added theme support for 1 premium theme. Also updated conditional code for theme checks to include possible child themes. Updated .pot file for translators and German translations.

= 1.0.1 =
Just fixed the Extensions typo (Yes, it happens sometimes...)

= 1.0.0 =
Just released into the wild.

== Plugin Links ==
* [Translations (GlotPress)](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/woocommerce-admin-bar-addition)
* [User support forums](http://wordpress.org/support/plugin/woocommerce-admin-bar-addition)
* [Code snippets archive for customizing, GitHub Gist](https://gist.github.com/deckerweb/2173193)

== Donate ==
Enjoy using *WooCommerce Admin Bar Addition*? Please consider [making a small donation](http://genesisthemes.de/en/donate/) to support the project's continued development.

== Translations ==

* English - default, always included
* German (de_DE): Deutsch - immer dabei! [Download auch via deckerweb.de](http://deckerweb.de/sprachdateien/woocommerce-und-extensions/#woocommerce-admin-bar-addition)
* French (fr_FR): Français - user-submitted
* Italian (it_IT): Italiano - user-submitted
* Danish (da_DK): Dansk - user-submitted
* Spanish (es_ES): Español - user-submitted
* Polish (pl_PL): Polski - user-submitted
* For custom and update-secure language files please upload them to `/wp-content/languages/wcaba/` (just create this folder) - This enables you to use fully custom translations that won't be overridden on plugin updates. Also, complete custom English wording is possible with that, just use a language file like `wcaba-en_US.mo/.po` to achieve that (for creating one see the following tools).

**Easy plugin translation platform with GlotPress tool:** [**Translate "WooCommerce Admin Bar Addition"...**](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/woocommerce-admin-bar-addition)

*Note:* All my plugins are internationalized/ translateable by default. This is very important for all users worldwide. So please contribute your language to the plugin to make it even more useful. For translating I recommend the awesome ["Codestyling Localization" plugin](http://wordpress.org/plugins/codestyling-localization/) and for validating the ["Poedit Editor"](http://www.poedit.net/), which works fine on Windows, Mac and Linux.

== Additional Info ==
**Idea Behind / Philosophy:** Just a little leightweight plugin for all the WooCommerce shop managers out there to make their daily shop admin life a bit easier. Integration of extensions and third-party plugins & themes was also an important goal. -- I'll try to add even more plugin/theme support if it makes some sense. So stay tuned :).

**WooCommerce News Planet** I also have started a little news/feed service via "FriendFeed" that you can subscribe to: http://friendfeed.com/woocommerce-news -- Please contact me via my Twitter for new resources (that have an RSS feed and are WooCommerce-related!)

== Credits ==
* Thanks to WooThemes company and WooCommerce team for promoting this plugin on their official homepage as well as on the download page here on wordpress.org! ;-)
* Thanks to WooCommerce lead developer Mike Jolley for supporting this plugin - and mentioning this in his personal blog :)
* Thanks to the team of [ColorLabs & Company](http://colorlabsproject.com/) for supporting the plugin with their premium theme for WooCommerce
* Thanks to [PrimaThemes](http://www.primathemes.com/) [@PrimaThemes](http://twitter.com/#!/primathemes) for supporting the plugin with their premium theme "WP Sharp" for WooCommerce
* Thanks to [Kriesi](http://www.kriesi.at/) [@Kriesi](http://twitter.com/#!/Kriesi) for supporting the plugin with his premium themes "Abundance", "Propulsion" and "Flashlight" for WooCommerce
* Thanks to [Dominik Schilling](http://wpgrafie.de/) [@ocean90](http://twitter.com/#!/ocean90) for great help with the CSS for the first level icon in WordPress 3.3!
* **Thanks to all users and especially all translators who submitted translations! You guys really rock! :)**

== Last but not least ==
**Special Thanks go out to my family for allowing me to do such spare time projects (aka free plugins) and supporting me in every possible way!**


== Support for OFFICIAL Plugin Extensions Released by WooThemes ==
**General Extensions**

* "WooCommerce Software Add-On" (by WooThemes)
* "WooCommerce Product CSV Import Suite" (by Mike Jolley/WooThemes)
* "WooCommerce Product Image Watermark" (by David Baker)
* "WooCommerce Compare Produts Pro" (by A3 Revolution Software Development) (Note: upcoming release!)
* "WooCommerce Bulk Stock Management" (by Mike Jolley/WooThemes)
* "WooCommerce Branding" (by WooThemes)
* "WooCommerce Print Invoice/Packing List" (by Ilari Mäkelä)
* "WooCommerce E-Mail Attachments" (by Guenter Schoenmann - InoPlugs)
* "WooCommerce Amazon S3 Storage" (by Gerhard Potgieter)
* "WooCommerce jPlayer Product Sampler" (by WooThemes)
* "WooCommerce Wishlist Member Integration" (by Radomir van Dalen)
* "WooCommerce Catalog Visibility Options" (by Lucas Stark)
* "WooCommerce CSV Export" (by Ilari Mäkelä)
* "WooCommerce Facebook Tab" (by David Baker)
* "WooCommerce AWeber Newsletter Subscription" (by Gerhard Potgieter)
* "WooCommerce Commission King" (by Winning Media)
* "WooCommerce Dynamic Pricing" (by Lucas Stark)
* "WooCommerce Substribe to Newsletter" (by WooThemes)
* "WooCommerce Professor Cloud Zoom" (by Andrew Benbow)
* "WooCommerce SMS Notifications" (by Mediaburst & Simon Wheatley)
* "WooCommerce Clickatell SMS Notifications" (by WooThemes)
* "WooCommerce FreshBooks" (by Patrick Garman)
* "SOD QuickBooks Connector" (by Sixty One Designs)

**Converters/ Importers**

* "WooCommerce - Product Importer Deluxe" (by Visser Labs)
* "Jigoshop to WooCommerce Converter" (by Agus MU)
* "MarketPress to WooCommerce Converter" (by Agus MU)
* "WP e-Commerce to WooCommerce Converter" (by Agus MU)

Of course, only extensions with own settings pages (which are linkable!) could be integrated. More extensions might be added as they become available and I could get my hands on the internal parameters... :-).


== Tweaking, Branding, Custom Snippets ==

All the following custom & branding stuff code can also be found as a Gist on Github: https://gist.github.com/deckerweb/2173193 (you can also add your questions/ feedback there :)

== Add Theme Support for "Order Status" with Counters ==
Add the following code to your active theme/child theme `functions.php` file:
`
/** WooCommerce Admin Bar Addition: Add Order Status Links */
add_theme_support( 'wcaba-order-status' );
`
--> Links will appear under "Orders" item as sub-level links

== Add Theme Support for All the Frontend "Shop Pages" Links ==
Add the following code to your active theme/child theme `functions.php` file:
`
/** WooCommerce Admin Bar Addition: Add Shop Pages Links */
add_theme_support( 'wcaba-shop-links' );
`
--> Links will appear between "Reports" and "Extensions" -- also the WordPress capability `edit_pages` is required - because you'll want to edit your stuff right away :)


== Add new custom menu items via theme or other plugins ==
This is possible since version 2.3 of the plugin! There are 4 action hooks available for hooking custom menu items in -- `wcaba_custom_main_items` for the main section, `wcaba_custom_extension_items` for the exentensions section, `wcaba_custom_theme_items` for the theme section plus `wcaba_custom_group_items` for the resource group section. Here's an example code:
`
add_action( 'wcaba_custom_group_items', 'wcaba_custom_additional_group_item' );
/**
 * WooCommerce Admin Bar Addition: Custom Resource Group Items
 *
 * @global mixed $wp_admin_bar
 */
function wcaba_custom_additional_group_item() {

	global $wp_admin_bar;

	$wp_admin_bar->add_menu( array(
		'parent' => 'ddw-woocommerce-wcgroup',
		'id'     => 'your-unique-item-id',
		'title'  => __( 'Custom Menu Item Name', 'your-textdomain' ),
		'href'   => 'http://deckerweb.de/',
		'meta'   => array( 'title' => __( 'Custom Menu Item Name Tooltip', 'your-textdomain' ) )
	) );
}
`

== Remove Certain Sections from the Toolbar Items ==
To achieve this add one, some or all of the following constants to your active theme/child theme's `functions.php` file:
`
/** WooCommerce Admin Bar Addition: Remove ALL Items! */
define( 'WCABA_DISPLAY', FALSE );

/** WooCommerce Admin Bar Addition: Remove Extensions Items */
define( 'WCABA_EXTENSIONS_DISPLAY', FALSE );

/** WooCommerce Admin Bar Addition: Remove Themes Items */
define( 'WCABA_THEMES_DISPLAY', FALSE );

/** WooCommerce Admin Bar Addition: Remove Resource Items */
define( 'WCABA_RESOURCES_DISPLAY', FALSE );

/** WooCommerce Admin Bar Addition: Remove German Language Items */
define( 'WCABA_DE_DISPLAY', FALSE );

/** WooCommerce Admin Bar Addition: Remove Dutch Language Items */
define( 'WCABA_NL_DISPLAY', FALSE );

/** WooCommerce Admin Bar Addition: Remove Spanish Language Items */
define( 'WCABA_ES_DISPLAY', FALSE );

/** WooCommerce Admin Bar Addition: Remove Czech Language Items */
define( 'WCABA_CZ_DISPLAY', FALSE );

/** WooCommerce Admin Bar Addition: Remove WC Debug Link */
define( 'WCABA_DEBUG_DISPLAY', FALSE );

/** WooCommerce Admin Bar Addition: Remove Reports/Statistics */
define( 'WCABA_REPORTS_DISPLAY', FALSE );
`
--> You can also combine these with conditional `if` statements to customize this even more, for example:

If you want to disable the display of any "WooCommerce Admin Bar Addition" items for all user roles of "Editor" please use this code:
`
/** WooCommerce Admin Bar Addition: Remove all items for "Editor" user role */
if ( current_user_can( 'editor' ) ) {
	define( 'WCABA_DISPLAY', FALSE );
}
`

To hide the "Extensions" section only from a user with the user ID of "2", just use this code:
`
/** WooCommerce Admin Bar Addition: Remove all items for user ID 2 */
if ( 2 == get_current_user_id() ) {
	define( 'WCABA_EXTENSIONS_DISPLAY', FALSE );
}
`

== Available Filters to Customize More Stuff ==
All filters are listed with the filter name in bold and the below additional info, helper functions (if available) as well as usage examples.

**wcaba_filter_capability_all**

* Default value: `manage_woocommerce` (set by "WooCommerce" plugin itself, the main capability for shop managers!)
* 5 Predefined helper functions:
 * `__wcaba_admin_only` -- returns `'administrator'` role -- usage:
`
add_filter( 'wcaba_filter_capability_all', '__wcaba_admin_only' );
`
 * `__wcaba_role_shop_manager` -- returns `'shop_manager'` role -- usage:
`
add_filter( 'wcaba_filter_capability_all', '__wcaba_role_shop_manager' );
`
 * `__wcaba_role_editor` -- returns `'editor'` role -- usage:
`
add_filter( 'wcaba_filter_capability_all', '__wcaba_role_editor' );
`
 * `__wcaba_cap_manage_options` -- returns `'manage_options'` capability -- usage:
`
add_filter( 'wcaba_filter_capability_all', '__wcaba_cap_manage_options' );
`
 * `__wcaba_cap_install_plugins` -- returns `'install_plugins'` capability -- usage:
`
add_filter( 'wcaba_filter_capability_all', '__wcaba_cap_install_plugins' );
`
* Another example:
`
add_filter( 'wcaba_filter_capability_all', 'custom_wcaba_capability_all' );
/**
 * WooCommerce Admin Bar Addition: Change Main Capability
 */
function custom_wcaba_capability_all() {
	return 'edit_theme_options';
}
`
--> Changes the capability to `edit_theme_options`

**wcaba_filter_main_icon**

* Default value: WooCommerce cart icon in default color
* 7 Predefined helper functions for the 6 included colored icons, returning special colored icon values - the helper function always has this name: `__wcaba_colornamehere_icon()` this results in the following filters ready for usage:
`
add_filter( 'wcaba_filter_main_icon', '__wcaba_blue_icon' );

add_filter( 'wcaba_filter_main_icon', '__wcaba_grey_icon' );

add_filter( 'wcaba_filter_main_icon', '__wcaba_orange_icon' );

add_filter( 'wcaba_filter_main_icon', '__wcaba_pink_icon' );

add_filter( 'wcaba_filter_main_icon', '__wcaba_red_icon' );

add_filter( 'wcaba_filter_main_icon', '__wcaba_teal_icon' );

add_filter( 'wcaba_filter_main_icon', '__bptb_theme_images_icon' );
`
--> Where the last helper function returns the icon file (`icon-wcaba.png`) found in your current theme's/child theme's `/images/` subfolder

* Another example:
`
add_filter( 'wcaba_filter_main_icon', 'custom_wcaba_main_icon' );
/**
 * WooCommerce Admin Bar Addition: Change Main Icon
 */
function custom_wcaba_main_icon() {
	return get_stylesheet_directory_uri() . '/images/custom-icon.png';
}
`
--> Uses a custom image from your active theme's `/images/` folder

--> Recommended dimensions are 16px x 16px

**wcaba_filter_main_icon_display**

* Returning the CSS class for the main item icon
* Default value: `icon-woocommerce` (class is: `.icon-woocommerce`)
* 1 Predefined helper function:
 * `__wcaba_no_icon_display()` -- usage:
`
add_filter( 'wcaba_filter_main_icon_display', '__wcaba_no_icon_display' );
`
 * This way you can REMOVE the icon!
* Another example:
`
add_filter( 'wcaba_filter_main_icon_display', 'custom_wcaba_main_icon_display_class' );
/**
 * WooCommerce Admin Bar Addition: Change Main Icon CSS Class
 */
function custom_wcaba_main_icon_display_class() {
	return 'your-custom-icon-class';
}
`
--> You then have to define CSS rules in your theme/child theme stylesheet for your own custom class `.your-custom-icon-class`

**wcaba_filter_main_item**

* Default value: "WooCommerce"
* NOTE: Interlinked with "WooCommerce Branding" extension: if you're changing the Name there, then the new value will be applied ALSO to this filter!
* Example code for your theme's `functions.php` file:
`
add_filter( 'wcaba_filter_main_item', 'custom_wcaba_main_item' );
/**
 * WooCommerce Admin Bar Addition: Change Main Item Name
 */
function custom_wcaba_main_item() {
	return __( 'Your custom main item', 'your-theme-textdomain' );
}
`

**wcaba_filter_main_item_tooltip**

* Default value: "WooCommerce Shop Plugin"
* NOTE: Interlinked with the "WooCommerce Branding" extension: if you're changing the Name there, then the new value will be applied ALSO to this filter!
* Example code for your theme's `functions.php` file:
`
add_filter( 'wcaba_filter_main_item_tooltip', 'custom_wcaba_main_item_tooltip' );
/**
 * WooCommerce Admin Bar Addition: Change Main Item Name's Tooltip
 */
function custom_wcaba_main_item_tooltip() {
	return __( 'Your custom main item tooltip', 'your-theme-textdomain' );
}
`

**wcaba_filter_woocommerce_name** and **wcaba_filter_woocommerce_name_tooltip**

* Default value for both: "WooCommerce"
* Used for some items within toolbar links to enable proper branding
* Change things like in the other examples/principles shown above
* NOTE: Interlinked with the "WooCommerce Branding" extension: if you're changing the Name there, then it will be applied ALSO to both of these filters!

**Final note:** If you don't like to add your customizations to your theme's `functions.php` file you can also add them to a functionality plugin or an mu-plugin. This way you can also use this better for Multisite environments. In general you are then more independent from theme changes etc.

All the custom & branding stuff code above can also be found as a Gist on Github: https://gist.github.com/deckerweb/2173193 (you can also add your questions/ feedback there :)