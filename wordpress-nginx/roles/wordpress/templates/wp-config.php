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
define('DB_NAME', '{{ wp_db_name }}');

/** MySQL database username */
define('DB_USER', '{{ wp_db_user }}');

/** MySQL database password */
define('DB_PASSWORD', '{{ wp_db_password }}');

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
define('AUTH_KEY',         'IX}WRK=%2ob#7fQM]ti(R}3r%csrn`UUAM$`^iP<M6$l3!W%MdZ).Wy$WH|WRPgv');
define('SECURE_AUTH_KEY',  'BRwa73U/FnWSRVx DV6YucR$&LieLz>w%uLhE*oI^##sDkLq=Y0ddH9axw|cc!L`');
define('LOGGED_IN_KEY',    'nT/IFrw/7f)x%+K.QA,h$}-Hb}{V|4n/)6#.?|G( >alEge+27iU4_qfmC:P%LF~');
define('NONCE_KEY',        '.`<Bm*)@2-}sf9#UN*F!W_c.$e`l8YfA.3[L4^$*He301RKGaa+-^N#pytwj{,8e');
define('AUTH_SALT',        '}mu.?&>mYW:$N},;A-T*aRRg*-L }@FRQtKb!3t>UC[{p%*fixeo8B-Jp~P*17fa');
define('SECURE_AUTH_SALT', '&%xilhmWPI#/6Cn]zA*{V()]~ (eN|gS=>tTr:$[B3RGDe3.mqY1M<#E#,|NOHt1');
define('LOGGED_IN_SALT',   '5s_yrVU.)Q=pfPCBxys.v0n3}G]SsOKVm)V{4ne;S;32K*Y-SW)EmPy(vQasfI]|');
define('NONCE_SALT',       'ZEv4U*e$wptd9GutKj?h{UkH+&+vOUO+KX^y_@ye=2{MwrMwM)s[-}#-H@=u4rVs');

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

/** Disable Automatic Updates Completely */
define( 'AUTOMATIC_UPDATER_DISABLED', {{auto_up_disable}} );

/** Define AUTOMATIC Updates for Components. */
define( 'WP_AUTO_UPDATE_CORE', {{core_update_level}} );
