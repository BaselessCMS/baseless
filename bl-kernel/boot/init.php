<?php
/**
 * Initiate system
 *
 * @package    JSON CMS
 * @subpackage Boot
 * @category   Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

/**
 * System configuration
 *
 * @since 1.0.0
 * @return mixed Returns an array of config
 *               parameters or false.
 */
function config() {

	$config = false;
	if ( file_exists( PATH_ROOT . 'config.json' ) ) {
		$config = file_get_contents( PATH_BOOT . 'config.json' );
	}

	// False if no config file.
	if ( ! $config ) {
		return false;
	}

	// Decode config file.
	$flags = ENT_COMPAT;
	if ( defined( 'ENT_HTML5' ) ) {
		$flags = ENT_COMPAT | ENT_HTML5;
	}
	$decode = htmlspecialchars_decode( $config, $flags );

	// Return the JSON config as a PHP array.
	return json_decode( $decode, true );
}
$config = config();

// Die if no config file.
if ( ! $config ) {
	die( 'This website needs a config.json file in the CMS boot directory.' );
}

// CMS version.
define( 'BLUDIT_VERSION', '1.0.0' );
define( 'CMS_VERSION', '1.0.0' );

// Change to true for debugging.
define( 'DEBUG_MODE', $config['debug']['mode'] );
define( 'DEBUG_TYPE', $config['debug']['type'] ); // INFO or TRACE.

/**
 * Display debug errors
 *
 * This determines whether errors should be printed to
 * the screen as part of the output or if they should
 * be hidden from the user.
 */
ini_set( 'display_errors', $config['debug']['errors'] );

/**
 * Display startup errors
 *
 * Even when display_errors is on, errors that occur
 * during PHP's startup sequence are not displayed.
 *
 * It's strongly recommended to keep display_startup_errors off,
 * except for debugging.
 */
ini_set( 'display_startup_errors', $config['debug']['start'] );

/**
 * HTML errors
 *
 * If disabled, error message will be solely plain text instead HTML code.
 */
ini_set( 'html_errors', $config['debug']['html'] );

/**
 * Log errors
 *
 * Tells whether script error messages should be logged
 * to the server's error log or error_log.
 */
ini_set( 'log_errors', $config['debug']['log'] );

if ( DEBUG_MODE ) {
	error_reporting( E_ERROR | E_WARNING | E_PARSE | E_NOTICE );
} else {
	error_reporting( E_ERROR );
}

/**
 * PHP paths
 *
 * PATH_ROOT and PATH_BOOT are defined in index.php
 */
define( 'PATH_LANGUAGES', PATH_ROOT . 'bl-languages' . DS );
define( 'PATH_THEMES', PATH_ROOT . 'bl-themes' . DS );
define( 'PATH_PLUGINS', PATH_ROOT . 'bl-plugins' . DS );
define( 'PATH_KERNEL', PATH_ROOT . 'bl-kernel' . DS );
define( 'PATH_CONTENT', PATH_ROOT . 'bl-content' . DS );

define( 'PATH_RULES', PATH_KERNEL . 'boot' . DS . 'rules' . DS );
define( 'PATH_INCLUDES', PATH_KERNEL . 'includes' . DS );
define( 'PATH_CLASSES', PATH_KERNEL . 'classes' . DS );
define( 'PATH_HELPERS', PATH_CLASSES ); // Deprecated.
define( 'PATH_AJAX', PATH_KERNEL . 'ajax' . DS );
define( 'PATH_ASSETS', PATH_KERNEL . 'assets' . DS );
define( 'PATH_CORE_JS', PATH_ASSETS . 'js' . DS );

define( 'PATH_PAGES', PATH_CONTENT . 'pages' . DS );
define( 'PATH_DATABASES', PATH_CONTENT . 'databases' . DS );
define( 'PATH_PLUGINS_DATABASES', PATH_CONTENT . 'databases' . DS . 'plugins' . DS );
define( 'PATH_TMP', PATH_CONTENT . 'tmp' . DS );
define( 'PATH_UPLOADS', PATH_CONTENT . 'uploads' . DS );
define( 'PATH_WORKSPACES', PATH_CONTENT . 'workspaces' . DS );
define( 'PATH_DEBUG', PATH_CONTENT . $config['debug']['file'] );

define( 'PATH_UPLOADS_PAGES', PATH_UPLOADS . 'pages' . DS );
define( 'PATH_UPLOADS_PROFILES', PATH_UPLOADS . 'profiles' . DS );
define( 'PATH_UPLOADS_THUMBNAILS', PATH_UPLOADS . 'thumbnails' . DS );

define( 'PATH_ADMIN', PATH_KERNEL . 'admin' . DS );
define( 'PATH_ADMIN_THEMES', PATH_ADMIN . 'themes' . DS );
define( 'PATH_ADMIN_CONTROLLERS', PATH_ADMIN . 'controllers' . DS );
define( 'PATH_ADMIN_VIEWS', PATH_ADMIN . 'views' . DS );

// Databases.
define( 'DB_PAGES', PATH_DATABASES . 'pages.php' );
define( 'DB_SITE', PATH_DATABASES . 'site.php' );
define( 'DB_CATEGORIES', PATH_DATABASES . 'categories.php' );
define( 'DB_TAGS', PATH_DATABASES . 'tags.php' );
define( 'DB_SYSLOG', PATH_DATABASES . 'syslog.php' );
define( 'DB_USERS', PATH_DATABASES . 'users.php' );
define( 'DB_SECURITY', PATH_DATABASES . 'security.php' );

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

// Allowed image extensions.
$GLOBALS['ALLOWED_IMG_EXTENSION'] = $config['media']['img_ext'];

// Allowed image mime types.
$GLOBALS['ALLOWED_IMG_MIMETYPES'] = $config['media']['img_mime'];

// Number of images to show in the media manager per page.
define( 'MEDIA_MANAGER_NUMBER_OF_FILES', $config['media']['per_page'] );

// Sort the image by date.
define( 'MEDIA_MANAGER_SORT_BY_DATE', $config['media']['sort_date'] );

// Profile image size.
define( 'PROFILE_IMG_WIDTH', $config['media']['avatar_width'] );
define( 'PROFILE_IMG_HEIGHT', $config['media']['avatar_height'] );
define( 'PROFILE_IMG_QUALITY', $config['media']['avatar_quality'] ); // 100%

// Content filename.
define( 'FILENAME', $config['content']['db_file'] );

// Page brake string.
define( 'PAGE_BREAK', $config['content']['page_break'] );

// Constant arrays using define are not allowed in PHP 5.6 or earlier.

// Type of pages included in the tag database.
$GLOBALS['DB_TAGS_TYPES'] = $config['content']['tags_types'];

// Set internal character encoding.
mb_internal_encoding( CHARSET );

// Set HTTP output character encoding.
mb_http_output( CHARSET );

// Include core classes.
include( PATH_CLASSES . 'class-dbjson.php' );
include( PATH_CLASSES . 'class-dblist.php' );
include( PATH_CLASSES . 'class-plugin.php' );
include( PATH_CLASSES . 'class-pages.php' );
include( PATH_CLASSES . 'class-users.php' );
include( PATH_CLASSES . 'class-tags.php' );
include( PATH_CLASSES . 'class-language.php' );
include( PATH_CLASSES . 'class-site.php' );
include( PATH_CLASSES . 'class-categories.php' );
include( PATH_CLASSES . 'class-syslog.php' );
include( PATH_CLASSES . 'class-pagex.php' );
include( PATH_CLASSES . 'class-category.php' );
include( PATH_CLASSES . 'class-tag.php' );
include( PATH_CLASSES . 'class-user.php' );
include( PATH_CLASSES . 'class-url.php' );
include( PATH_CLASSES . 'class-login.php' );
include( PATH_CLASSES . 'class-parsedown.php' );
include( PATH_CLASSES . 'class-security.php' );
include( PATH_CLASSES . 'class-admin-bootstrap.php' );

// Include functions.
include( PATH_INCLUDES . 'functions.dep.php' ); // Old.
include( PATH_INCLUDES . 'helpers.php' );
include( PATH_INCLUDES . 'functions.php' );
include( PATH_INCLUDES . 'template-tags.php' );

// Include helper classes.
include( PATH_CLASSES . 'class-text.php' );
include( PATH_CLASSES . 'class-log.php' );
include( PATH_CLASSES . 'class-date.php' );
include( PATH_CLASSES . 'class-theme.php' );
include( PATH_CLASSES . 'class-session.php' );
include( PATH_CLASSES . 'class-redirect.php' );
include( PATH_CLASSES . 'class-sanitize.php' );
include( PATH_CLASSES . 'class-valid.php' );
include( PATH_CLASSES . 'class-email.php' );
include( PATH_CLASSES . 'class-filesystem.php' );
include( PATH_CLASSES . 'class-alert.php' );
include( PATH_CLASSES . 'class-paginator.php' );
include( PATH_CLASSES . 'class-image.php' );
include( PATH_CLASSES . 'class-tcp.php' );
include( PATH_CLASSES . 'class-dom.php' );
include( PATH_CLASSES . 'class-cookie.php' );

// Instantiate classes.
$pages 		= new Pages();
$users 		= new Users();
$tags 		= new Tags();
$categories = new Categories();
$site  		= new Site();
$url		= new Url();
$security	= new Security();
$syslog 	= new Syslog();

/**
 * Base URL
 *
 * The user can define the base URL. Leave empty if
 * you want the system to try to detect the base URL.
 */
$base = '';
if ( ! empty( $_SERVER['DOCUMENT_ROOT'] ) && ! empty( $_SERVER['SCRIPT_NAME'] ) && empty( $base ) ) {
	$base = str_replace( $_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_NAME'] );
	$base = dirname( $base );
} elseif ( empty( $base ) ) {
	$base = empty( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$base = dirname( $base );
}

if ( strpos( $_SERVER['REQUEST_URI'], $base ) !== 0 ) {
	$base = '/';
} elseif ( $base != DS ) {
	$base = trim( $base, '/' );
	$base = '/' . $base . '/';
} else {
	// Workaround for Windows web servers.
	$base = '/';
}

// These paths are relative to the user/browser.
define( 'HTML_PATH_ROOT', $base );
define( 'HTML_PATH_THEMES', HTML_PATH_ROOT . 'bl-themes/' );
define( 'HTML_PATH_THEME', HTML_PATH_THEMES . $site->theme() . '/' );
define( 'HTML_PATH_THEME_ASSET', HTML_PATH_THEME . 'assets/' );
define( 'HTML_PATH_THEME_CSS', HTML_PATH_THEME . 'css/' );
define( 'HTML_PATH_THEME_JS', HTML_PATH_THEME . 'js/' );
define( 'HTML_PATH_THEME_IMG', HTML_PATH_THEME . 'img/' );
define( 'HTML_PATH_ADMIN_ROOT', HTML_PATH_ROOT . ADMIN_URI_FILTER . '/' );
define( 'HTML_PATH_ADMIN_THEME', HTML_PATH_ROOT . 'bl-kernel/admin/themes/' . $site->adminTheme() . '/' );
define( 'HTML_PATH_ADMIN_THEME_JS',	HTML_PATH_ADMIN_THEME . 'js/' );
define( 'HTML_PATH_ADMIN_THEME_CSS', HTML_PATH_ADMIN_THEME . 'css/' );
define( 'HTML_PATH_CORE_ASSETS', HTML_PATH_ROOT . 'bl-kernel/assets/' );
define( 'HTML_PATH_CORE_JS', HTML_PATH_ROOT . 'bl-kernel/assets/js/' );
define( 'HTML_PATH_CORE_CSS', HTML_PATH_ROOT . 'bl-kernel/css/' );
define( 'HTML_PATH_CORE_IMG', HTML_PATH_ROOT . 'bl-kernel/assets/images/' );
define( 'HTML_PATH_CONTENT', HTML_PATH_ROOT . 'bl-content/' );
define( 'HTML_PATH_UPLOADS', HTML_PATH_ROOT . 'bl-content/uploads/' );
define( 'HTML_PATH_UPLOADS_PAGES', HTML_PATH_UPLOADS . 'pages/' );
define( 'HTML_PATH_UPLOADS_PROFILES', HTML_PATH_UPLOADS . 'profiles/' );
define( 'HTML_PATH_UPLOADS_THUMBNAILS',	HTML_PATH_UPLOADS . 'thumbnails/' );
define( 'HTML_PATH_PLUGINS', HTML_PATH_ROOT . 'bl-plugins/' );

$language = new Language( $site->language() );
$L = $language;
$url->checkFilters( $site->uriFilters() );

// Tag URI filter.
define( 'TAG_URI_FILTER', $url->filters( 'tag' ) );

// Category URI filter.
define( 'CATEGORY_URI_FILTER', $url->filters( 'category' ) );

// Page URI filter.
define( 'PAGE_URI_FILTER', $url->filters( 'page' ) );

// Content order by: date / position.
define( 'ORDER_BY', $site->orderBy() );

// Allow unicode characters in the URL.
define( 'EXTREME_FRIENDLY_URL', $site->extremeFriendly() );

// Minutes to execute the autosave function.
define( 'AUTOSAVE_INTERVAL', $site->autosaveInterval() );

// TRUE for upload images restrict to a pages, FALSE to upload images in common.
define( 'IMAGE_RESTRICT', $site->imageRestrict() );

// TRUE to convert relatives images to absolutes, FALSE No changes apply.
define( 'IMAGE_RELATIVE_TO_ABSOLUTE', $site->imageRelativeToAbsolute() );

// TRUE if the markdown parser is enabled.
define( 'MARKDOWN_PARSER', $site->markdownParser() );

// These paths are absolutes for the OS.
define( 'THEME_DIR', PATH_ROOT . 'bl-themes' . DS . $site->theme() . DS );
define( 'THEME_DIR_INC', THEME_DIR . 'includes' . DS );
define( 'THEME_DIR_CLASS', THEME_DIR . 'includes' . DS . 'classes' . DS );
define( 'THEME_DIR_PHP', THEME_DIR . 'php' . DS );
define( 'THEME_DIR_ASSET', THEME_DIR . 'assets' . DS );
define( 'THEME_DIR_CSS', THEME_DIR . 'css' . DS );
define( 'THEME_DIR_JS', THEME_DIR . 'js' . DS );
define( 'THEME_DIR_IMG', THEME_DIR . 'img' . DS );
define( 'THEME_DIR_LANG', THEME_DIR . 'languages' . DS );

// These paths are absolutes for the user/browser.
define( 'DOMAIN', $site->domain() );
define( 'DOMAIN_BASE', DOMAIN . HTML_PATH_ROOT );
define( 'DOMAIN_CORE_ASSETS', DOMAIN . HTML_PATH_CORE_ASSETS );
define( 'DOMAIN_CORE_JS', DOMAIN . HTML_PATH_CORE_JS );
define( 'DOMAIN_CORE_CSS', DOMAIN . HTML_PATH_CORE_CSS );
define( 'DOMAIN_THEME', DOMAIN . HTML_PATH_THEME );
define( 'DOMAIN_THEME_ASSET', DOMAIN . HTML_PATH_THEME_ASSET );
define( 'DOMAIN_THEME_CSS', DOMAIN . HTML_PATH_THEME_CSS );
define( 'DOMAIN_THEME_JS', DOMAIN . HTML_PATH_THEME_JS );
define( 'DOMAIN_THEME_IMG', DOMAIN . HTML_PATH_THEME_IMG );
define( 'DOMAIN_ADMIN_THEME', DOMAIN . HTML_PATH_ADMIN_THEME );
define( 'DOMAIN_ADMIN_THEME_CSS', DOMAIN . HTML_PATH_ADMIN_THEME_CSS );
define( 'DOMAIN_ADMIN_THEME_JS', DOMAIN . HTML_PATH_ADMIN_THEME_JS );
define( 'DOMAIN_UPLOADS', DOMAIN . HTML_PATH_UPLOADS );
define( 'DOMAIN_UPLOADS_PAGES', DOMAIN . HTML_PATH_UPLOADS_PAGES );
define( 'DOMAIN_UPLOADS_PROFILES', DOMAIN . HTML_PATH_UPLOADS_PROFILES );
define( 'DOMAIN_UPLOADS_THUMBNAILS', DOMAIN . HTML_PATH_UPLOADS_THUMBNAILS );
define( 'DOMAIN_PLUGINS', DOMAIN . HTML_PATH_PLUGINS );
define( 'DOMAIN_CONTENT', DOMAIN . HTML_PATH_CONTENT );

define( 'DOMAIN_ADMIN', DOMAIN_BASE . ADMIN_URI_FILTER . '/' );

define( 'DOMAIN_TAGS', Text :: addSlashes( DOMAIN_BASE . TAG_URI_FILTER, false, true ) );
define( 'DOMAIN_CATEGORIES', Text :: addSlashes( DOMAIN_BASE . CATEGORY_URI_FILTER, false, true ) );
define( 'DOMAIN_PAGES', Text :: addSlashes( DOMAIN_BASE . PAGE_URI_FILTER, false, true ) );

$ADMIN_CONTROLLER = '';
$ADMIN_VIEW   = '';
$ID_EXECUTION = uniqid(); // String 13 characters long.
$WHERE_AM_I   = $url->whereAmI();
