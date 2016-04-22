<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'mylivete_intranet');

/** MySQL database username */
define('DB_USER', 'mylivete_intrane');

/** MySQL database password */
define('DB_PASSWORD', 'zjv4349jm22fRgg');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '84=YSVc|1VY%jOJSSuWQA3?6242}X|0T 8Tq+b~u$FH5 6rC+[DTnjA]Yl(wAPxE');
define('SECURE_AUTH_KEY',  'c}-oRcPJ1IBJsu*s1HtMF5@1J)yP7/^lE(50_GxDAF_/3uF|!QG&o- r`OMW%n|K');
define('LOGGED_IN_KEY',    'M@(u**5]#7*%~H--@IUz/vT7q6a2KgySm,cL`eo3vKTC$AU8co]Xl3-FzEk85Y_$');
define('NONCE_KEY',        '`k>-M;}g,i[;+[.:V5w+`;a3/Y[S=$4vWAPz YS_h=~97_FS|Nk=Q}{,8c.a.uSu');
define('AUTH_SALT',        '~6$nZM!,qdp</b%-[7uZB`Qs:`/5va~2MyFL%;<k_vu[fUXg8E|%)gQ^DHal Ku]');
define('SECURE_AUTH_SALT', 'Mu16cZvvIi%*5RV>c3bd>A-1||)f8d)5R5!aDq$5fPdiJ$0b0lJQk.s<aQRaeg3B');
define('LOGGED_IN_SALT',   ')O{pcF-Fvifc|r>-]3Jurnz:, f-r(e`:TWb3!A~@?[p[-*JZbRNaiSZlKlU|wxH');
define('NONCE_SALT',       'Q}28jP*#O_o|N5-,x@Bqx>-tYUUx65D&_|SAaeJQM_1kn->=EXN. <d{df!>}Hwt');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'imlt_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
