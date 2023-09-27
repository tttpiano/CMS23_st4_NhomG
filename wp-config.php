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
define( 'DB_NAME', 'wp_cms' );

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
define( 'AUTH_KEY',         'iS~Cf!|w5vSz%?hQ.ODJ6DClzJ8a*qX?hZsm2&XYE4kl/apZ6 ^c0!bELLkOa3bf' );
define( 'SECURE_AUTH_KEY',  'qV=<,(X-|KJaeWavl9kX=aUhbfYEYU{$LG8H63zS:6#a(q6hoQCS S 58 yCpl[$' );
define( 'LOGGED_IN_KEY',    'j)02djcy8nCUYUJ7bxflpGHvD^oG4[Hzm|eMYY}$JO#(r.h0aD5.x0iz>-TLY$y2' );
define( 'NONCE_KEY',        '6d+a8D/: [OV$T0)YdKET2~CtUZ|=(V0W-BS)A2^e(q`.MLnc+yqs8hqL5q5{.(:' );
define( 'AUTH_SALT',        'I$t3t(2iUO+Qn+k~ *J+Y(Y@g{6dJFA)F1(NIiOQMyZ8]xVas3JlzHT+@59wpc4~' );
define( 'SECURE_AUTH_SALT', '!6UL68xyvC]5?:UU=xtN=UZ_0gsg8/&0,5</54x(S9V69NQ[>;`WN}0X2[vqRFZ,' );
define( 'LOGGED_IN_SALT',   'R$k8M6*(8ONq+U ]wn$v49S|8pO}@8Br>kv:0G01/r+DV79OcUzaNW=sylu~/.y4' );
define( 'NONCE_SALT',       'R1:5,t*A3@$(+j:KdENs`ts0mS;nA,)dU_9*#Vctjh)f8KApCj35IwZ%J5(L~Feh' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_v631';

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
