<?php
/**
 * Install CMS
 *
 * @package  JSON CMS
 * @category Core
 * @since    1.0.0
 */

// Check PHP version.
if ( version_compare( phpversion(), '5.6', '<' ) ) {
	$errorText = 'Current PHP version ' . phpversion() . ', you need > 5.6.';
	error_log( '[ERROR] ' . $errorText, 0 );
	exit( $errorText );
}

// Check PHP modules.
$modulesRequired        = [ 'mbstring', 'json', 'gd', 'dom' ];
$modulesRequiredExit    = false;
$modulesRequiredMissing = '';

foreach ( $modulesRequired as $module ) {

	if ( ! extension_loaded( $module ) ) {
		$errorText = 'PHP module <b>' . $module . '</b> is not installed.';
		error_log( '[ERROR] ' . $errorText, 0 );

		$modulesRequiredExit     = true;
		$modulesRequiredMissing .= $errorText . PHP_EOL;
	}
}

if ( $modulesRequiredExit ) {
	echo 'PHP modules missing:';
	echo $modulesRequiredMissing;
	echo '';
	$L->p( 'Please read system requirements.' );

	exit(0);
}

// Security constant.
define( 'BLUDIT', true );

// Directory separator.
define( 'DS', DIRECTORY_SEPARATOR );

// PHP paths.
define( 'PATH_ROOT', __DIR__ . DS );
define( 'PATH_CONTENT', PATH_ROOT . 'bl-content' . DS );
define( 'PATH_KERNEL', PATH_ROOT . 'bl-kernel' . DS );
define( 'PATH_LANGUAGES', PATH_ROOT . 'bl-languages' . DS );
define( 'PATH_UPLOADS', PATH_CONTENT . 'uploads' . DS );
define( 'PATH_TMP', PATH_CONTENT . 'tmp' . DS );
define( 'PATH_PAGES', PATH_CONTENT . 'pages' . DS );
define( 'PATH_WORKSPACES', PATH_CONTENT . 'workspaces' . DS );
define( 'PATH_DATABASES', PATH_CONTENT . 'databases' . DS );
define( 'PATH_PLUGINS_DATABASES', PATH_CONTENT . 'databases' . DS . 'plugins' . DS );
define( 'PATH_UPLOADS_PROFILES', PATH_UPLOADS . 'profiles' . DS );
define( 'PATH_UPLOADS_THUMBNAILS', PATH_UPLOADS . 'thumbnails' . DS );
define( 'PATH_UPLOADS_PAGES', PATH_UPLOADS . 'pages' . DS );
define( 'PATH_HELPERS', PATH_KERNEL . 'helpers' . DS );
define( 'PATH_ABSTRACT', PATH_KERNEL . 'abstract' . DS );

// Protecting against Symlink attacks.
define( 'CHECK_SYMBOLIC_LINKS', true );

// Filename for pages.
define( 'FILENAME', 'index.txt' );

// Domain and protocol.
define( 'DOMAIN', $_SERVER['HTTP_HOST']);

if ( ! empty( $_SERVER['HTTPS'] ) ) {
	define( 'PROTOCOL', 'https://' );
} else {
	define( 'PROTOCOL', 'http://' );
}

/**
 * Base URL
 *
 * Change the base URL or leave it empty if
 * you want the system to detect the base URL.
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

// Workaround for Windows Web Servers.
} else {
	$base = '/';
}

define( 'HTML_PATH_ROOT', $base );

// Log separator.
define( 'LOG_SEP', ' | ' );

// JSON.
if ( ! defined( 'JSON_PRETTY_PRINT' ) ) {
	define( 'JSON_PRETTY_PRINT', 128 );
}

// Database format date.
define( 'DB_DATE_FORMAT', 'Y-m-d H:i:s' );

// Charset, default UTF-8.
define( 'CHARSET', 'UTF-8' );

// Default language file.
define( 'DEFAULT_LANGUAGE_FILE', 'en.json' );

// Set internal character encoding.
mb_internal_encoding( CHARSET );

// Set HTTP output character encoding.
mb_http_output( CHARSET );

// Directory permissions.
define( 'DIR_PERMISSIONS', 0755 );

// PHP classes.
include( PATH_ABSTRACT . 'dbjson.class.php' );
include( PATH_HELPERS . 'sanitize.class.php' );
include( PATH_HELPERS . 'valid.class.php' );
include( PATH_HELPERS . 'text.class.php' );
include( PATH_HELPERS . 'log.class.php' );
include( PATH_HELPERS . 'date.class.php' );
include( PATH_KERNEL . 'language.class.php' );

// Try to detect the language from browser or headers.
$languageFromHTTP = 'en';
$localeFromHTTP   = 'en_US';

if ( isset( $_GET['language'] ) ) {
	$languageFromHTTP = Sanitize :: html( $_GET['language'] );

// Try to detect the language browser.
} else {
	$languageFromHTTP = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );

	// Try to detect the locale.
	if ( function_exists( 'locale_accept_from_http' ) ) {
		$localeFromHTTP = locale_accept_from_http( $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
	}
}

$finalLanguage = 'en';
$languageFiles = getLanguageList();
foreach ( $languageFiles as $fname => $native ) {
	if ( ( $languageFromHTTP == $fname ) || ( $localeFromHTTP == $fname ) ) {
		$finalLanguage = $fname;
	}
}

$L = $language = new Language( $finalLanguage);

// Set locale.
setlocale( LC_ALL, $localeFromHTTP );

// --- TIMEZONE ---

// Check if timezone is defined in php.ini.
$iniDate = ini_get( 'date.timezone' );

// Timezone not defined in php.ini, then set UTC as default.
if ( empty( $iniDate ) ) {
	date_default_timezone_set( 'UTC' );
}

// Returns an array with all languages.
function getLanguageList() {

	$files = glob( PATH_LANGUAGES . '*.json' );
	$tmp   = [];

	foreach ( $files as $file ) {

		$t = new dbJSON( $file, false );
		$native = $t->db['language-data']['native'];
		$locale = basename( $file, '.json' );
		$tmp[$locale] = $native;
	}
	return $tmp;
}

// Check if the system is installed.
function alreadyInstalled() {
	return file_exists( PATH_DATABASES . 'site.php' );
}

// Check write permissions and .htaccess file.
function checkSystem() {

	$output = [];

	// Try to create .htaccess.
	$htaccessContent = 'AddDefaultCharset UTF-8

<IfModule mod_rewrite.c>

# Enable rewrite rules
RewriteEngine on

# Base directory
RewriteBase ' . HTML_PATH_ROOT . '

# Deny direct access to the next directories
RewriteRule ^bl-content/(databases|workspaces|pages|tmp)/.*$ - [R=404,L]

# All URL process by index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*) index.php [PT,L]

</IfModule>';

	if ( ! file_put_contents( PATH_ROOT . '.htaccess', $htaccessContent ) ) {

		if ( ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) {

			$webserver = Text::lowercase( $_SERVER['SERVER_SOFTWARE'] );

			if ( Text :: stringContains( $webserver, 'apache' ) || Text :: stringContains( $webserver, 'litespeed' ) ) {
				$errorText = 'Missing file, upload the file .htaccess';
				error_log( '[ERROR] ' . $errorText, 0 );
				array_push( $output, $errorText );
			}
		}
	}

	// Check mod_rewrite module.
	if ( function_exists( 'apache_get_modules' ) ) {
		if ( ! in_array( 'mod_rewrite', apache_get_modules() ) ) {

			$errorText = 'Module mod_rewrite is not installed or loaded.';
			error_log( '[ERROR] ' . $errorText, 0 );
			array_push( $output, $errorText );
		}
	}

	// Try to create the directory content.
	@mkdir( PATH_CONTENT, DIR_PERMISSIONS, true );

	// Check if the directory content is writeable.
	if ( ! is_writable( PATH_CONTENT ) ) {
		$errorText = 'Writing test failure, check directory "bl-content" permissions.';
		error_log( '[ERROR] ' . $errorText, 0 );
		array_push( $output, $errorText );
	}
	return $output;
}

// Install the CMS.
function install( $adminPassword, $timezone ) {

	// Access global variables.
	global $L;

	if ( ! date_default_timezone_set( $timezone ) ) {
		date_default_timezone_set( 'UTC' );
	}
	$currentDate = Date :: current( DB_DATE_FORMAT );

	// Directories for initial pages.
	$pagesToInstall = [
		'example-page-1-slug',
		'example-page-2-slug',
		'example-page-3-slug',
		'example-page-4-slug'
	];

	foreach ( $pagesToInstall as $page ) {

		if ( ! mkdir( PATH_PAGES . $L->get( $page ), DIR_PERMISSIONS, true ) ) {
			$errorText = 'Error when trying to created the directory=>' . PATH_PAGES . $L->get( $page );
			error_log( '[ERROR] ' . $errorText, 0 );
		}
	}

	// Directories for initial plugins.
	$pluginsToInstall = [
		'tinymce',
		'about',
		'visits-stats',
		'robots',
		'canonical',
		// 'frontend'
	];

	foreach ( $pluginsToInstall as $plugin ) {

		if ( ! mkdir( PATH_PLUGINS_DATABASES . $plugin, DIR_PERMISSIONS, true ) ) {
			$errorText = 'Error when trying to created the directory=>' . PATH_PLUGINS_DATABASES . $plugin;
			error_log( '[ERROR] ' . $errorText, 0 );
		}
	}

	// Directories for upload files.
	if ( ! mkdir( PATH_UPLOADS_PROFILES, DIR_PERMISSIONS, true ) ) {
		$errorText = 'Error when trying to created the directory=>' . PATH_UPLOADS_PROFILES;
		error_log( '[ERROR] ' . $errorText, 0 );
	}

	if ( ! mkdir( PATH_UPLOADS_THUMBNAILS, DIR_PERMISSIONS, true ) ) {
		$errorText = 'Error when trying to created the directory=>' . PATH_UPLOADS_THUMBNAILS;
		error_log( '[ERROR] ' . $errorText, 0 );
	}

	if ( ! mkdir( PATH_TMP, DIR_PERMISSIONS, true ) ) {
		$errorText = 'Error when trying to created the directory=>' . PATH_TMP;
		error_log( '[ERROR] ' . $errorText, 0 );
	}

	if ( ! mkdir( PATH_WORKSPACES, DIR_PERMISSIONS, true ) ) {
		$errorText = 'Error when trying to created the directory=>' . PATH_WORKSPACES;
		error_log( '[ERROR] ' . $errorText, 0 );
	}

	if ( ! mkdir( PATH_UPLOADS_PAGES, DIR_PERMISSIONS, true ) ) {
		$errorText = 'Error when trying to created the directory=>' . PATH_UPLOADS_PAGES;
		error_log( '[ERROR] ' . $errorText, 0 );
	}

	$dataHead = "<?php defined( 'BLUDIT' ) or die( 'Bludit CMS.' ); ?>" . PHP_EOL;

	$data  = [];
	$slugs = [];
	$nextDate = $currentDate;

	foreach ( $pagesToInstall as $page ) {

		$slug     = $page;
		$title    = Text :: replace( 'slug', 'title', $slug );
		$content  = Text :: replace( 'slug', 'content', $slug );
		$nextDate = Date :: offset( $nextDate, DB_DATE_FORMAT, '-1 minute' );

		$data[$L->get( $slug)] = [
			'title'         => $L->get( $title ),
			'description'   => '',
			'username'      => 'admin',
			'tags'          => [],
			'type'          => ( ( $slug == 'example-page-4-slug' ) ? 'static' : 'published' ),
			'date'          => $nextDate,
			'dateModified'  => '',
			'allowComments' => true,
			'position'      => 1,
			'coverImage'    => '',
			'md5file'       => '',
			'category'      => 'general',
			'uuid'          => md5(uniqid() ),
			'parent'        => '',
			'template'      => '',
			'noindex'       => false,
			'nofollow'      => false,
			'noarchive'     => false
		];
		array_push( $slugs, $slug );

		file_put_contents( PATH_PAGES . $L->get( $slug ) . DS . FILENAME, $L->get( $content ), LOCK_EX );
	}

	file_put_contents( PATH_DATABASES . 'pages.php', $dataHead . json_encode( $data, JSON_PRETTY_PRINT ), LOCK_EX );

	/**
	 * File site.php
	 *
	 * If the system is not installed inside a folder the URL doesn't need finish with `/`
	 * @example (root): https://domain.com
	 * @example (inside a folder): https://domain.com/folder/
	 */
	if ( HTML_PATH_ROOT == '/' ) {
		$siteUrl = PROTOCOL . DOMAIN;
	} else {
		$siteUrl = PROTOCOL . DOMAIN . HTML_PATH_ROOT;
	}

	$data = [
		'title'        => 'JSON CMS',
		'slogan'       => '',
		'description'  => '',
		'footer'       => 'Copyright ©' . Date :: current( 'Y' ),
		'itemsPerPage' => 6,
		'language'     => $L->currentLanguage(),
		'locale'       => $L->locale(),
		'timezone'     => $timezone,
		'theme'        => 'frontend',
		'adminTheme'   => 'default',
		'homepage'     => '',
		'pageNotFound' => '',
		'uriPage'      => '/',
		'uriTag'       => '/tag/',
		'uriCategory'  => '/category/',
		'uriBlog'      => '',
		'url'          => $siteUrl,
		'emailFrom'    => 'no-reply@' . DOMAIN,
		'orderBy'      => 'date',
		'currentBuild' => '0',
		'twitter'      => '',
		'facebook'     => '',
		'codepen'      => '',
		'github'       => '',
		'instagram'    => '',
		'gitlab'       => '',
		'linkedin'     => '',
		'xing'         => '',
		'dateFormat'   => 'F j, Y',
		'extremeFriendly'         => true,
		'autosaveInterval'        => 2,
		'titleFormatHomepage'     => '{{site-slogan}} | {{site-title}}',
		'titleFormatPages'        => '{{page-title}} | {{site-title}}',
		'titleFormatCategory'     => '{{category-name}} | {{site-title}}',
		'titleFormatTag'          => '{{tag-name}} | {{site-title}}',
		'imageRestrict'           => true,
		'imageRelativeToAbsolute' => false
	];
	file_put_contents( PATH_DATABASES . 'site.php', $dataHead . json_encode( $data, JSON_PRETTY_PRINT ), LOCK_EX );

	// File users.php
	$salt = uniqid();
	$passwordHash = sha1( $adminPassword . $salt );
	$tokenAuth    = md5( uniqid() . time() . DOMAIN );

	$data = [
		'admin' => [
			'nickname'      => 'Admin',
			'firstName'     => $L->get( 'Administrator' ),
			'lastName'      => '',
			'role'          => 'admin',
			'password'      => $passwordHash,
			'salt'          => $salt,
			'email'         => '',
			'registered'    => $currentDate,
			'tokenRemember' => '',
			'tokenAuth'     => $tokenAuth,
			'tokenAuthTTL'  => '2009-03-15 14:00',
			'twitter'       => '',
			'facebook'      => '',
			'instagram'     => '',
			'codepen'       => '',
			'linkedin'      => '',
			'xing'          => '',
			'github'        => '',
			'gitlab'        => ''
		]
	];
	file_put_contents( PATH_DATABASES . 'users.php', $dataHead . json_encode( $data, JSON_PRETTY_PRINT), LOCK_EX);

	// File syslog.php
	$data = [
		[
			'date'          => $currentDate,
			'dictionaryKey' => 'welcome-to-bludit',
			'notes'         => '',
			'idExecution'   => uniqid(),
			'method'        => 'POST',
			'username'      => 'admin'
		]
	];
	file_put_contents( PATH_DATABASES . 'syslog.php', $dataHead . json_encode( $data, JSON_PRETTY_PRINT ), LOCK_EX );

	// File security.php
	$data = [
		'minutesBlocked'        => 5,
		'numberFailuresAllowed' => 10,
		'blackList'             => []
	];
	file_put_contents( PATH_DATABASES . 'security.php', $dataHead . json_encode( $data, JSON_PRETTY_PRINT ), LOCK_EX );

	// File categories.php
	$data = [
		'general' => [
			'name'        => 'General',
			'description' => '',
			'template'    => '',
			'list'        => $slugs
		],
		'music' => [
			'name'        => 'Music',
			'description' => '',
			'template'    => '',
			'list'        => []
		],
		'videos' => [
			'name'        => 'Videos',
			'description' => '',
			'template'    => '',
			'list'        => []
		]
	];
	file_put_contents( PATH_DATABASES . 'categories.php', $dataHead . json_encode( $data, JSON_PRETTY_PRINT ), LOCK_EX );

	// File tags.php
	$data = [];
	file_put_contents( PATH_DATABASES . 'tags.php', $dataHead . json_encode( $data, JSON_PRETTY_PRINT ), LOCK_EX );

	// File plugins/about/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'about' . DS . 'db.php',
		$dataHead . json_encode(
			[
				'position' => 1,
				'label'    => $L->get( 'About' ),
				'text'     => $L->get( 'this-is-a-brief-description-of-yourself-our-your-site' )
			],
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/visits-stats/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'visits-stats' . DS . 'db.php',
		$dataHead . json_encode(
			[
				'numberOfDays'  => 7,
				'label'         => $L->get( 'Visits' ),
				'excludeAdmins' => false,
				'position'      => 1
			],
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);
	mkdir( PATH_WORKSPACES . 'visits-stats', DIR_PERMISSIONS, true );

	// File plugins/tinymce/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'tinymce' . DS . 'db.php',
		$dataHead . json_encode(
			[
				'position' => 1,
				'toolbar1' => 'formatselect bold italic forecolor backcolor removeformat | bullist numlist table | blockquote alignleft aligncenter alignright | link unlink pagebreak image code',
				'toolbar2' => '',
				'plugins'  => 'code autolink image link pagebreak advlist lists textpattern table'
			],
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/canonical/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'canonical' . DS . 'db.php',
		$dataHead . json_encode(
			[
				'position' => 1
			],
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/frontend/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'frontend' . DS . 'db.php',
		$dataHead . json_encode(
			[
				'googleFonts'         => false,
				'showPostInformation' => false,
				'dateFormat'          => 'relative',
				'position'            => 1
			],
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);

	// File plugins/robots/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES . 'robots' . DS . 'db.php',
		$dataHead . json_encode(
			[
				'position'  => 1,
				'robotstxt' => 'User-agent: *' . PHP_EOL . 'Allow: /'
			],
			JSON_PRETTY_PRINT
		),
		LOCK_EX
	);
	return true;
}

function redirect( $url ) {

	if ( ! headers_sent() ) {
		header( "Location:" . $url, TRUE, 302 );
		exit;
	}

	exit( '<meta http-equiv="refresh" content="0; url="' . $url . '" />' );
}

if ( alreadyInstalled() ) {
	$errorText = 'System is already installed.';
	error_log( '[ERROR] ' . $errorText, 0 );
	exit( $errorText );
}

// Install a demo, just call the install.php?demo=true.
if ( isset( $_GET['demo'] ) ) {
	install( 'demo123', 'UTC' );
	redirect( HTML_PATH_ROOT );
}

// Install by POST method.
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if ( Text :: length( $_POST['password'] ) < 6 ) {
		$errorText = $L->g( 'password-must-be-at-least-6-characters-long' );
		error_log( '[ERROR] ' . $errorText, 0 );
	} else {
		install( $_POST['password'], $_POST['timezone'] );
		redirect( HTML_PATH_ROOT );
	}
}

?>
<!DOCTYPE html>
<html dir="auto" lang="en" class="no-js">

<head>
	<title><?php $L->p( 'CMS Installer' ); ?></title>

	<meta charset="<?php echo CHARSET; ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover" />
	<meta name="robots" content="noindex,nofollow" />

	<?php // Preconnect and preload files. ?>
	<link rel="preconnect" href="//fonts.adobe.com" />
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

	<?php

	// Change `<html>` 'no-js' class to 'js' if JavaScript is enabled.
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n"; ?>

	<link rel="stylesheet" type="text/css" href="bl-kernel/css/bootstrap.min.css?version=<?php echo time(); ?>" />

	<script charset="utf-8" src="bl-kernel/js/jquery.min.js?version=<?php echo time(); ?>"></script>
	<script charset="utf-8" src="bl-kernel/js/bootstrap.bundle.min.js?version=<?php echo time(); ?>"></script>
	<script charset="utf-8" src="bl-kernel/js/jstz.min.js?version=<?php echo time(); ?>"></script>
</head>

<body class="login">
	<div class="container">

		<h1><?php $L->p( 'CMS Installer' ); ?></h1>

		<?php
		$system = checkSystem();
		if ( ! empty( $system ) ) {
			foreach ( $system as $error ) {
				echo '
				<table class="table">
					<tbody>
						<tr>
							<th>' . $error . '</th>
						</tr>
					</tbody>
				</table>
				';
			}
		} elseif ( isset( $_GET['language'] ) ) {

		?>
			<p><?php $L->p( 'choose-a-password-for-the-user-admin' ); ?></p>

			<?php if ( ! empty( $errorText ) ) : ?>
				<div class="alert alert-danger"><?php echo $errorText; ?></div>
			<?php endif ?>

			<form id="jsformInstaller" method="post" action="" autocomplete="off">

				<input type="hidden" name="timezone" id="jstimezone" value="UTC" />

				<div class="form-group">
					<input type="text" value="admin" class="form-control form-control-lg" id="jsusername" name="username" placeholder="<?php $L->p( 'Username' ); ?>" disabled />
				</div>

				<div class="form-group">
					<input type="password" class="form-control form-control-lg" id="jspassword" name="password" placeholder="<?php $L->p( 'Password' ); ?>" />
				</div>

				<div class="form-check">
					<input role="button" class="form-check-input" type="checkbox" value="" id="jsshowPassword" />
					<label class="form-check-label" for="jsshowPassword"><?php $L->p( 'Show password' ); ?></label>
				</div>

				<div class="form-group">
					<button type="submit" class="button button-primary" name="install"><?php $L->p( 'Install' ); ?></button>
				</div>
			</form>
		<?php

		} else {

		?>
			<form id="jsformLanguage" method="get" action="" autocomplete="off">

				<label for="jslanguage"><?php echo $L->get( 'System Language' ); ?></label>
				<select id="jslanguage" name="language" class="form-control">
					<?php
					$htmlOptions = getLanguageList();
					foreach ( $htmlOptions as $fname => $native ) {

						printf(
							'<option value="%s" %s>%s</option>',
							$fname,
							( ( $finalLanguage === $fname ) ? ' selected="selected"' : '' ),
							$native
						);
					} ?>
				</select>

				<div class="form-group">
					<button id="system-install-next" type="submit" class="button"><?php $L->p( 'Next' ); ?></button>
				</div>
			</form>
		<?php } ?>
	</div>

	<script>
		$(document).ready( function() {

			// Timezone.
			var timezone = jstz.determine();
			$( '#jstimezone' ).val( timezone.name() );

			// Show password.
			$( '#jsshowPassword' ).on( 'click', function() {
				var input = document.getElementById( 'jspassword' );

				if ( ! $(this).is( ':checked' ) ) {
					input.setAttribute( 'type', 'password' );
				} else {
					input.setAttribute( 'type', 'text' );
				}
			});

		});
	</script>
</body>
</html>
