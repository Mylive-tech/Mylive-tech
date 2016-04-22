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
define('DB_NAME', 'mylive5_exchng');

/** MySQL database username */
define('DB_USER', 'mylive5_lteadmin');

/** MySQL database password */
define('DB_PASSWORD', 'M@zt3r@ll');

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
define('AUTH_KEY',         'U7t.GrqJLm YeH?0%3Rg~fT-rv9#Dq|]PLQ]fOSNUOU@+a6h0}`QZ1=j LmM2j>j');
define('SECURE_AUTH_KEY',  'bg!-,$8q2Dvek~?!46gtfq-z=|9jYAkL`SY@U~RzY<Sh,/f!|dUv<6hV|PSMqHU>');
define('LOGGED_IN_KEY',    '`DWpaUOKX#+`@E{`t#H4[>WnbED)E_-[wF+KJ(043r+Fegb T]I}5!`;Hqt}luUc');
define('NONCE_KEY',        '|+3BG|Fu+fL6F%dm]`m0r.QK!sclw{||--4q +m@)<uT9O=+^:n:>`4K0m3)OC|-');
define('AUTH_SALT',        '?s>~Kd8yX$KnmxScOmh [VU-z~#1ycLmvl&X6s*%YNB@jx8,=IMmI(~{Ly|bo1W]');
define('SECURE_AUTH_SALT', 'y3Ha5AX-R9|wUnD0(U[h;-,s-v<-[XlSEACZ8:w$V(1*A/_T#5*uu (N^?7_+f-0');
define('LOGGED_IN_SALT',   'O.(ieIqK)}0=3(|=f*y$T[|s4,6&;S(h|G.766M(O~H4I*k-7. 4;BxqGn0GuyS;');
define('NONCE_SALT',       'c(zi{CPK$,#gcfN *pqkrR&X-s.o+[_~$EmE|<*hP+aXL<rg!<YC{Pj*I&]N2P@?');

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
