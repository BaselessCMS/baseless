<?php
/**
 * Settings screen controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS_Admin\Controllers\Settings;

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	site,
	security,
	url,
	lang,
	users,
	plugins,
	page,
	pages,
	cats
};

checkRole( [ 'admin' ] );

if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	editSettings( $_POST );
	\Redirect :: page( 'settings' );
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	$L->g( 'Settings' ),
	$site->title()
);
