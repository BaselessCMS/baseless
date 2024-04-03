<?php
/**
 * CMS core file
 *
 * @package  JSON CMS
 * @category Core
 * @since    1.0.0
 * @link     https://github.com/Bludiot/bludiot
 */

// Check if the CHS is installed.
if ( ! file_exists( 'bl-content/databases/site.php' ) ) {
	$base = dirname( $_SERVER['SCRIPT_NAME'] );
	$base = rtrim( $base, '/' );

	// Work around for Windows servers.
	$base = rtrim( $base, '\\' );

	header( 'Location:' . $base . '/install.php' );
	exit( '<a href="./install.php">Install the CMS first.</a>' );
}

// Load time.
$loadTime = microtime( true );

// Security constants.
define( 'BLUDIT', true );
define( 'JSON_CMS', true );

// Directory separator.
define( 'DS', DIRECTORY_SEPARATOR );

// PHP paths for init.
define( 'PATH_ROOT', __DIR__ . DS );
define( 'PATH_BOOT', PATH_ROOT . 'bl-kernel' . DS . 'boot' . DS );

// Initialize the system.
require( PATH_BOOT . 'init.php' );

// Back end or front end.
if ( $url->whereAmI() === 'admin' ) {
	require( PATH_BOOT . 'admin.php' );
} else {
	require( PATH_BOOT . 'site.php' );
}
