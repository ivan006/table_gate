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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'owlbluegpyuty_db5' );

/** MySQL database username */
define( 'DB_USER', 'bluegpyuty_8' );

/** MySQL database password */
define( 'DB_PASSWORD', 'S8G7UQM4951rn8Jb73t8' );

/** MySQL hostname */
define( 'DB_HOST', 'sql39.jnb1.host-h.net' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '8g%o$)ka{$HIo .Bjdk6pD5cGy%wPP-PTK9s3&p8AeMu{,j:= 5^~1r^KK7/^27i' );
define( 'SECURE_AUTH_KEY',  '//w$=:NHHY,>{NI>8P8t^jP)}CXn,Ri1%@rm=rcw!P1,$TeKQ#$g4|uVHR6ZT={8' );
define( 'LOGGED_IN_KEY',    '_Md?8TNl_)SBPW(@D56l6;cyBV17HDP1~] /WB,09MupHy4WIkl!wt}1}^^[xn.^' );
define( 'NONCE_KEY',        '.9@fVc#0Eml|7ufSEIgAEBLFp+z2I{*WqWC)/w;1RD?+!-Xc,&gMO_P1_bfGrC56' );
define( 'AUTH_SALT',        '1M)/fApTo?nY&eM-a>HA~T&q3{pp_wc<kqVlcO-?pM-Uz mYL)Mt$`!$}C%xZVj0' );
define( 'SECURE_AUTH_SALT', '[uAymI},0>oI#ylPuMz%gD#2P16Gz^ZcHL#>S+FnkfY-+O[xz$n+=IivWr#R5wT[' );
define( 'LOGGED_IN_SALT',   'Kwa3>/$xM}ru^+e@QIuF0lrYS+_Fqu(V^i)l/+;~;mlug27^_P:zLrp(b^J%-WB3' );
define( 'NONCE_SALT',       'gm4QeLc!rS^O16m#22n5.mNt0FS*}7K_yCJNn9@Ar=T[Ih~l?h^z Evd]=u3QcqC' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
