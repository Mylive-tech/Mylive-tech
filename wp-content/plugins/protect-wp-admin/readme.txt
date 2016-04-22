=== Protect Your Admin ===
Contributors:india-web-developer
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZEMSYQUZRUK6A
Tags: Protect WP-Admin,wp-admin,Protect wordpress admin,Secure Admin,Admin,Scure Wordpress Admin,Rename Admin URL, Rename Wordpress Admin URL,Change wp-admin url,Change Admin URL,Change Admin Path,Restrict wp-admin
Requires at least: 3.3
Tested up to: 4.5
Stable tag: 2.0

1.) Protect Your Website Admin Against Hackers
2.) Modify Login Page Design

== Description ==

If you run a WordPress website, you should absolutely use "protect-wp-admin" to secure it against hackers.

Protect WP-Admin fixes a glaring security hole in the WordPress community: the well-known problem of the admin panel URL.
Everyone knows where the admin panel, and this includes hackers as well.

Protect WP-Admin helps solve this problem by allowing webmasters to customize their admin panel URL and blocking the default links.

After you setup Protect WP-Admin, webmasters will be able to change the "sitename.com/wp-admin" link into something like "sitename.com/custom-string".
All queries for the classic "/wp-admin/" and "wp-login.php" files will be redirected to the homepage, while access to the WP backend will be allowed only for the custom URL.

The plugin also comes with some access filters, allowing webmasters to restrict guest and registered users access to wp-admin, just in case you want some of your editors to log in the classic way.

**NOTE :Back up your database before beginning the activate plugin.**
It is extremely important to back up your database before beginning the activate plugin. If, for some reason, you find it necessary to restore your database from these backups.



 **[ Upgrade to Pro Version ](https://raghunathgurjar.wordpress.com/wp/pwa-pro/)**


= Features =

 * Define custom wp-admin url(i.e http://yourdomain.com/myadmin)
 * Define custom Logo OR change default logo on login page
 * Define body background color on login page 
 * SEO friendly URL for "Register" page (i.e http://yourdomain.com/myadmin/register)
 * SEO friendly URL for "Lost Password" page (i.e http://yourdomain.com/myadmin/lostpassword)
 * Restrict guest users for access to wp-admin
 * Restrict registered non-admin users from wp-admin
 * Allow admin access to non-admin users by define comma seprate multiple ids

= Go Pro =

We have also released an add-on for Protect-WP-Admin which not only demonstrates the flexibility of Protect-WP-Admin, but also adds some important features

 * Login Attempt Counter
 * An option for manage login page CSS from admin
 * An option for change to username of any user from admin
 * An option for define custom redirect url for defalut wp-admin url
 * Faster support



 **[ Upgrade to Pro Version](https://rgaddons.wordpress.com/protect-wp-admin-pro/)**


== Installation ==
In most cases you can install automatically from WordPress.

However, if you install this manually, follow these steps:

 * Step 1. Upload "protect-wp-admin-pro" folder to the `/wp-content/plugins/` directory
 * Step 2. Activate the plugin through the Plugins menu in WordPress
 * Step 3. Go to Settings "Protect WP-Admin Pro" and configure the plugin settings.

== Frequently Asked Questions ==

* 1.) Nothing happen after enable and add the new wordpress admin url? 

   Don't worry, Just update the site permalink ("Settings" >> "Permalinks") and re-check,Now this time it will be work fine

* 2.) Not able to login into admin after enable plugin? 

 May be issue can come when you not give proper writable permission on htaccess file OR you have not update permalink settings to SEO friendly url from admin. You can access login page url with default wp-admin slug after disable my plugin, you can disable plugin through FTP by rename protect-wp-admin folder to any other one. 

* 3.) Am i not able to login after installation

Basicaly issues can come only in case when you will use default permalink settings. 
If your permalink will be update to any other option except default then it will be work fine. Anyway Dont' worry, manualy you can add code into your site .htaccess file.

# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteRule ^newadmin/?$ /wp-login.php [QSA,L]
RewriteRule ^newadmin/register/?$ /wp-login.php?action=register [QSA,L]
RewriteRule ^newadmin/lostpassword/?$ /wp-login.php?action=lostpassword [QSA,L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress

Don not forgot to update the "newadmin" slug with your new admin slug (that you were added during update the plugin settings) :-)

4.) Is there any option for set number of login attempt?
* Yes, this option is available in pro addon not in free version. please browse https://rgaddons.wordpress.com/protect-wp-admin-pro/ this url for purchase to pro addon.

== Screenshots ==

1. screenshot-1.png

2. screenshot-2.png

3. screenshot-3.png

4. screenshot-4.png

5. screenshot-5.png



== Changelog == 
= 1.9 = 
 * Tested with new wordpress version 2.5 
 * Removed localhost permission related conditions.

= 1.9 = 
 * Fixed htaccess writable notice popup related issues on localhost 
 * Add an new confirmation alert before enable plugin 

= 1.8 = 
 * Fixed Login Failure issue
 * Released Pro Addon

= 1.7 = 
 * Fixed forget password email issue
 * Fixed forgot password link issue on login error page

= 1.6 = 
 *  Fixed wp-login.php issue for www url
 
= 1.5 = 
 * Fixed wp-login url issue
 * Fixed wp-admin url issue

= 1.4 = 
 * Fixed links issue on "Register", "Login" & "Lost Password" As Per New Admin Url
 * Fixed the "Register", "Login" & "Lost Password" Form Action URL As Per New Admin Url
 * Add validation to check SEO firendly url enable or not.
 * Add validation to check .htaccess file is writable or not.

= 1.3 = 
 * Added an option for define to admin login page logo
 * Added an option for define to wp-login page background-color
 * Fixed some minor css issues

= 1.2 = 
 * Added new option for allow admin access to non-admin users
 * Added condition for check permalink is updated or not
 * Fixed a minor issues (logout issues after add/update admin new url)
 
= 1.1 = 
 * Add new option for restrict registered users from wp-admin
 * Fixed permalink update issue after add/update admin new url. Now no need to update your permalink
 * Add option for redirect user to new admin url after update the new admin url

= 1.0 = 
 * First stable release
