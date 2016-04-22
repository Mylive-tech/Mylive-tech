<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'mylivete_ltit_blog');

/** MySQL database username */
define('DB_USER', 'mylivete_ltit');

/** MySQL database password */
define('DB_PASSWORD', '}8rE&{wC(?m~');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'A9ML;bqn1VH&~+-]H1pgG}8CgsX$bh2mB>Y/P#+;lKJ0c-]JeK@f4+~:A6fM3=w<');
define('SECURE_AUTH_KEY',  'Sd&zkX^@UE*?a>+5!qgd[Yr4BUy07KK<xmz%(~S&$@p:AhJR|MchbMM0Q#Q5+@wY');
define('LOGGED_IN_KEY',    '+J/CrJ-U0;6ppofXSwx1ek*O-|~N$7IO5kFMZU#]rVT#,~-@8D9gz@RnD-FbObzA');
define('NONCE_KEY',        'q/>H}vZh}yp%Y+O/5*gU<=>i+B=zF9<YcYj^qoNe) W{GGzgA4^+/d31kq&=HA4i');
define('AUTH_SALT',        '%p=Wv%.}kG.q>U~%JxgsYp7-H83Q:`EeQB0#@@6).&~x#~+x1mE5Rskw!1G2rK+U');
define('SECURE_AUTH_SALT', 'N)#:;7a#~vb5s(x;z~1&gUOdT%}C2e?;DKY)s:fS~`9j@>upu|I&Xb&R],Ku0g7V');
define('LOGGED_IN_SALT',   'P_K}:lXSs&fSnV$+xTwfJP;]]&P_&ZTtG@;ExI+d3PA>>bX+|/z@RdR<].6U~*7j');
define('NONCE_SALT',       'ko~G c,m E^gbR?)L,s8uKQU?.j>s{@.vfoN?9a_r7a?aKZ&[:y)3@I?JWl|xL8)');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'it_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
