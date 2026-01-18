<?php
/**
 * Remove logo
 *
 * @package    JSON CMS
 * @subpackage AJAX
 * @category   Controllers
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

header( 'Content-Type: application/json' );

/**
 * Delete the site logo.
 *
 * This script delete the file and set and empty string in the database.
 */
$logoFilename = $site->logo( false );
if ( $logoFilename ) {
	\Filesystem :: rmfile( PATH_UPLOADS . $logoFilename );
}

// Remove the logo from the database.
$site->set( [ 'logo' => '' ] );

ajax_response( 0, 'Logo Removed.' );
