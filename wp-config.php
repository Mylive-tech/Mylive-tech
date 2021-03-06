<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache




/** Enable W3 Total Cache Edge Mode */
define('W3TC_EDGE_MODE', true); // Added by W3 Total Cache

/** Enable Wordpress Debug Mode */
//define( 'WP_DEBUG', true );


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
/** The name of the database for WordPress */define( 'WP_MEMORY_LIMIT', '128M' );
define('DB_NAME', 'mylivete_wp1');

/** MySQL database username */
define('DB_USER', 'mylivete_wp1');

/** MySQL database password */
define('DB_PASSWORD', 'd?)TE3&Hpq&5');

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
define('AUTH_KEY',         ']{a4`3Fn!xD|X!TaF>J{9/f+UB&6_]LX#5vS0~Q5%nA PV6,-VYH#Aj/8AHMd*m;');
define('SECURE_AUTH_KEY',  'yTpB-<w^&UWI1}~- lfu`B`_VShiW6d-><xx8g)1r>:1q3Vw=C]Vp)97)r>/^@+9');
define('LOGGED_IN_KEY',    '?K.pasLZnHFDE#Z,ipL/x_M|kB+18-r7$d$q_&+>KwnR.j]VN|w@g/(pIT=?aiSS');
define('NONCE_KEY',        '?!R[c[M0Bn0q|nhyhoQm+!+t|Gv-vj-6<IE.*~Spe8-dSGfKa6RRU;onW|Em-{r`');
define('AUTH_SALT',        '3MNRL_ULwAK7`ZFSOW-)6=r+@~^H?Gp7O)-CRXk^Uq-x[+zFZ{rE5tL@umW,2<uK');
define('SECURE_AUTH_SALT', 'UPG% %@7vZA^24[A)&vNYG&j2/weS)u8W)>j5B,NfJLxHN.@.iqd^P2p{Jx:>? 6');
define('LOGGED_IN_SALT',   'r6vr-bXl3<Z+psJji)q|s1gavw}[QI!tNme}gQ*kt)v9<c:-~WVx{]?OYqG;-<U-');
define('NONCE_SALT',       '^8/iG]f}fPRip<2#_--^a>@o&J2,`5ZVB+!H<_)~QttIv7*8%_7CjTG)tI%tuV.t');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
@ini_set( 'display_errors', 0 );

define('WP_MEMORY_LIMIT', '512M');
define('FORCE_SSL_ADMIN', false);
//define('DISALLOW_FILE_EDIT', true);


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
//define('DOMAIN_CURRENT_SITE', 'www.mylive-tech.com');

# Disables all core updates. Added by SiteGround Autoupdate:
define( 'WP_AUTO_UPDATE_CORE', false );
