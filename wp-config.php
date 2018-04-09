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
define('DB_NAME', 'studt');

/** MySQL database username */
define('DB_USER', 'studt');

/** MySQL database password */
define('DB_PASSWORD', 'studt@123');

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
define('AUTH_KEY',         ' O>:8Jjv#:~o$XXcEm+JJMHRxdyzbt9lnq5x6{J fW0zZ]ZwAJ^#OK92muwf6R*Q');
define('SECURE_AUTH_KEY',  ' 9i1aqw[TQl+(4Hrf69L}3p2z;zWq[Mu~%pU2iW|J$4OL3ZOw-EkJS%lf_Ab1=Zk');
define('LOGGED_IN_KEY',    'Epxz8?Ho4b;#*fA,E*g4M-ehi{@UGHvRA?!2<V:Kj[<YX1Cu173*5mb%8<t(^p=t');
define('NONCE_KEY',        'I?_MU<<@{=L?j=rrkPd<J&?`}]`&OYHU!Z7-.2L;,5?Q-w}Q^px<Y9`/+rc])0md');
define('AUTH_SALT',        'kt.@^Z!I0!FM@Rparo1(%;3]jC^Q/rGzrx4~&L|dN>`Ow%Gv3=!a{DZ?6JXl+pMS');
define('SECURE_AUTH_SALT', 'd7uPr8}Y<kF#W#xnM#F*,Ju,D&ll2nw*NC(=|voInFfGvJYu3TG~&N#MA|q SiE+');
define('LOGGED_IN_SALT',   'uM%0mG.2z.^~Y7w_L{;Q7OY 7SVHh?e ]:Q(Wh12Z;aVPwjTlXSr:y.2ZR!<DD8f');
define('NONCE_SALT',       '2<5XEJ.>2wGjN;eum);P.74pOPF$Pqp}})${/fLa6+5Xp^xLc?pP|lChYKhx6!2)');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('FS_METHOD', 'direct');
