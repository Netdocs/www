<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache


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
define('DB_NAME', 'netdocstgu14');

/** MySQL database username */
define('DB_USER', 'netdocstgu14');

/** MySQL database password */
define('DB_PASSWORD', 'Netdocs17');

/** MySQL hostname */
define('DB_HOST', 'mysql358.sql001:3306');

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
define('AUTH_KEY',         'LuwyZqgbi03oe2sDIb00dXEL3PEnOtcyfrMY98fLR9qbs7nW+QS6JbPyIMjX');
define('SECURE_AUTH_KEY',  'JnqVFNYPND91pW98n0tj6SLTXNxYER2wUovA3D76wkuwumM5IumXlkK5216q');
define('LOGGED_IN_KEY',    'rudGuCpXoKBXawI4zHl8aRys5H8eESLvv+h3LLllHFfhLvWkS+6ezdtjXgCI');
define('NONCE_KEY',        'BmdmCpo3G8sEhpcW6xuMV3TQ5NZBw1TI5ymInVX8Gwa6K6y0MaePDmBDiQne');
define('AUTH_SALT',        'r5hIpOgFtBIklM3ycYI/XAyKPp5v2pmvg9K4MOvuQQiduUogmpz7+Cd5K8a+');
define('SECURE_AUTH_SALT', 'umDkS+pnJAP8BYFUQ7iRkgDTkLyAxzlogTnTYlOgSY/6lcwnm8exoiwmPYtc');
define('LOGGED_IN_SALT',   'xo77X/tP6VaV4L0+AK4VOyZrv+n5PXfr+dVSLVemIz8NFMS2sID5u7ajE4pa');
define('NONCE_SALT',       'NDE4AsTlx/gVWSK1e0ZQR1v9fqZkFEp8KmXD4p9nikKNXU7gTN0c0oMvOHGK');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'mod175_';

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

/* Fixes "Add media button not working", see http://www.carnfieldwebdesign.co.uk/blog/wordpress-fix-add-media-button-not-working/ */
define('CONCATENATE_SCRIPTS', false );

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
