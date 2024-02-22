<?php
/**
 * System config
 *
 * @package  JSON CMS
 * @category Core
 * @since    1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Log.
define( 'LOG_SEP', $config['log']['sep'] );
define( 'LOG_TYPE_INFO', $config['log']['info'] );
define( 'LOG_TYPE_WARN', $config['log']['warn'] );
define( 'LOG_TYPE_ERROR', $config['log']['error'] );

// Protecting against Symlink attacks.
define( 'CHECK_SYMBOLIC_LINKS', $config['symlink'] );

// Alert status OK.
define( 'ALERT_STATUS_OK', $config['alert']['ok'] );

// Alert status fail.
define( 'ALERT_STATUS_FAIL', $config['alert']['fail'] );

// Alert notification disappear in X seconds.
define( 'ALERT_DISAPPEAR_IN', $config['alert']['seconds'] );

// Items per page for admin area.
define( 'ITEMS_PER_PAGE_ADMIN', $config['admin']['per_page'] );

// Password length.
define( 'PASSWORD_LENGTH', $config['login']['pw_length'] );

// Password salt length.
define( 'SALT_LENGTH', $config['login']['salt_length'] );

// Remember me.
define( 'REMEMBER_COOKIE_USERNAME', $config['login']['cookie_user'] );
define( 'REMEMBER_COOKIE_TOKEN', $config['login']['cookie_token'] );
define( 'REMEMBER_COOKIE_EXPIRE_IN_DAYS', $config['login']['cookie_expire'] );

// Token time to live for login via email. The offset is defined by http://php.net/manual/en/datetime.modify.php.
define( 'TOKEN_EMAIL_TTL', $config['login']['email_token'] );

// Session lifetime of the cookie in seconds which is sent to the browser
// The value 0 means until the browser is closed.
define( 'SESSION_COOKIE_LIFE_TIME', $config['login']['session_life'] );

// Session timeout server side, gc_maxlifetime.
// 3600 = 1hour
define( 'SESSION_GC_MAXLIFETIME', $config['login']['session_max'] );

// Database date format.
define( 'DB_DATE_FORMAT', $config['dates']['db'] );

// Database date format.
define( 'BACKUP_DATE_FORMAT', $config['dates']['backup'] );

// Sitemap date format.
define( 'SITEMAP_DATE_FORMAT', $config['dates']['db'] );

// Date format for Manage Content, Manage Users.
define( 'ADMIN_PANEL_DATE_FORMAT', $config['dates']['admin'] );

// Date format for Dashboard schedule posts.
define( 'SCHEDULED_DATE_FORMAT', $config['dates']['schedule'] );

// Notifications date format.
define( 'NOTIFICATIONS_DATE_FORMAT', $config['dates']['notify'] );

// Manage content date format.
define( 'MANAGE_CONTENT_DATE_FORMAT', $config['dates']['manage'] );

// Amount of items to show on notification panel.
define( 'NOTIFICATIONS_AMOUNT', $config['dash_notify_qty'] );

// Charset, default UTF-8.
define( 'CHARSET', $config['charset'] );

// Permissions for new directories.
define( 'DIR_PERMISSIONS', (int)$config['dir_permission'] );

// Admin URI filter to access to the admin panel.
define( 'ADMIN_URI_FILTER', $config['admin']['uri'] );

// Default language file, in this case is English.
define( 'DEFAULT_LANGUAGE_FILE', $config['default_lang'] );

// Number of images to show in the media manager per page.
define( 'MEDIA_MANAGER_NUMBER_OF_FILES', 5 );

// Sort the image by date.
define( 'MEDIA_MANAGER_SORT_BY_DATE', true );

// Profile image size.
define( 'PROFILE_IMG_WIDTH', 400 );
define( 'PROFILE_IMG_HEIGHT', 400 );
define( 'PROFILE_IMG_QUALITY', 100 ); // 100%

// Content filename.
define( 'FILENAME', $config['content']['db_file'] );

// Page brake string.
define( 'PAGE_BREAK', '<!-- pagebreak -->' );

// Constant arrays using define are not allowed in PHP 5.6 or earlier.

// Type of pages included in the tag database.
$GLOBALS['DB_TAGS_TYPES'] = [
	'published',
	'static',
	'sticky'
];

// Allowed image extensions.
$GLOBALS['ALLOWED_IMG_EXTENSION'] = [
	'gif',
	'png',
	'jpg',
	'jpeg',
	'svg',
	'webp'
];

// Allowed image mime types.
$GLOBALS['ALLOWED_IMG_MIMETYPES'] = [
	'image/gif',
	'image/png',
	'image/jpeg',
	'image/svg+xml',
	'image/webp'
];
