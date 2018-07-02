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
define('DB_NAME', 'wordpressPM');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         '6z9Pg$/ekC@LVFFRI&9=}4-csG*FN7E5LJYP4:enRo{o5vn~nmS$=9]g}V;t5o#a');
define('SECURE_AUTH_KEY',  ' `_x1V]!X 2O5 9Hi`NiU5ZJtz1tU3)b%WB3Rj;yty<3uPrRCGcn^i|ouD{Js.8&');
define('LOGGED_IN_KEY',    'qja<2O LX3iBYQY&*)Z~N&|$m2*.=aN3}w>bEZ$v#`{BFi!k`6L6B`4V.|*L[ 3O');
define('NONCE_KEY',        '^2kN#DEbEPvrGDlJ82n14fa3PK&d H X)CFdW)WdPu<!:^Z|iR(4|aB~(t1:GCt|');
define('AUTH_SALT',        'o&.o.U;%rvcVi+JwY%RO|E_{](kxG}Kg.>86:bYJ_CADp,9Nh-D-4)G&I%5]q4%i');
define('SECURE_AUTH_SALT', 'MZ:_{W&{VqH:Ql$OmT0&1OdwV8(}$a&]s%P~H+<jde-skX9|TFx`Rg@_Op^fo%SZ');
define('LOGGED_IN_SALT',   '3;Xz/r^U[GC>V,CO^6@:a{k{VpkS)_^M`w{ PLag;?3XodYD&`HH{upQ-0_bpp/g');
define('NONCE_SALT',       '#`5U@6g1;39`jkXc|onn,uVj?::R6}1y5d ^8jVJ-D_2h)eBe9*fJ#V!ZP(+*};(');

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
