<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'hotel' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'X.p~b(a||;s4Ebk~Oj`_@OLCi9YEkG,aaE]MCYPL`e%BVQV>*N>y8p>Vt[wYxrEg' );
define( 'SECURE_AUTH_KEY',  'Y;<uXZLZky53ZkmV1oA;DA2G_XxTd`Aou;Z:Tn`HQ|n!!cG,t$,W+ZWIdH%-?WGw' );
define( 'LOGGED_IN_KEY',    '{7Ti!}9fLv+3#Zbo84q(dD24@pr-k_~Iu1j )^s#A),4Fa&) Jt3f~B#ayy<1l8r' );
define( 'NONCE_KEY',        'gEpbSS 0Vk-+5I7s!Q:]]=-D%y) 9z7)v*S3(-=,%ZXC_3`8Ce4t5~$ybc:3Z/5n' );
define( 'AUTH_SALT',        'Xq).O,+y-!|p?9Ly4VTDSGNh`]uN)2 .VO%&m@.V Ju@h[,JVG6CHaw}ek{SVdAu' );
define( 'SECURE_AUTH_SALT', 'nfTxv]ztC(gX#frj@aYf>=y4JvJN_&2(.fMr7Wt(tsHtF(+UUwF.Zt$*TOy>%?|;' );
define( 'LOGGED_IN_SALT',   ' {qy=;jEM[0<3ym[LSypa6GAX%IvhCsFP6A~d<%PlUK_$O,6W0+rPI>O(-+vzLM#' );
define( 'NONCE_SALT',       '-?zh?1rTohe~%@u^/gT%GP+.j7WTnVUNS7aq3,:|8!Xk m4x(E@=L9BVXLPE-1kE' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
