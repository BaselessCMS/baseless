<?php
/**
 * Edit content controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Edit_Content;

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	login,
	site,
	syslog
};
use function CMS\Func\{
	check_role,
	delete_page,
	edit_page
};

if ( check_role( [ 'author' ], false ) ) {
	try {
		$key  = isset( $_POST['key'] ) ? $_POST['key'] : $layout['parameters'];
		$page = new Page( $key );
	} catch ( \Exception $e ) {
		\Alert :: set( lang()->g( 'You do not have sufficient permissions.' ) );
		\Redirect :: page( 'dashboard' );
	}

	if ( $page->username() !== login()->username() ) {

		// Add to syslog.
		syslog()->add( [
			'dictionaryKey' => 'access-denied',
			'notes'         => login()->username()
		] );

		\Alert :: set( lang()->g( 'You do not have sufficient permissions.' ) );
		\Redirect :: page( 'dashboard' );
	}
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	if ( 'delete' === $_POST['type'] ) {

		if ( delete_page( $_POST['key'] ) ) {
			\Alert :: set( lang()->g( 'The changes have been saved.' ) );
		}
	} else {
		$key = edit_page( $_POST );

		if ( false !== $key  ) {
			\Alert :: set( lang()->g( 'The changes have been saved.' ) );
			\Redirect :: page( 'edit-content/' . $key );
		}
	}
	\Redirect :: page( 'content' );
}

try {
	$key  = $layout['parameters'];
	$page = new \Page( $key );
} catch ( \Exception $e ) {
	\Log :: set( __METHOD__ . LOG_SEP.'Error occurred when trying to get the page ' . $key, LOG_TYPE_ERROR );
	\Redirect :: page( 'content' );
}

// Images prefix directory.
define( 'PAGE_IMAGES_KEY', $page->uuid() );

// Images and thumbnails directories.
if ( IMAGE_RESTRICT ) {
	define( 'PAGE_IMAGES_DIRECTORY', ( IMAGE_RELATIVE_TO_ABSOLUTE ? '' : HTML_PATH_UPLOADS_PAGES.PAGE_IMAGES_KEY . '/' ) );
	define( 'PAGE_IMAGES_URL', ( IMAGE_RELATIVE_TO_ABSOLUTE ? '' : DOMAIN_UPLOADS_PAGES.PAGE_IMAGES_KEY . '/' ) );
	define( 'PAGE_THUMBNAILS_DIRECTORY', PATH_UPLOADS_PAGES . PAGE_IMAGES_KEY . DS . 'thumbnails' . DS );
	define( 'PAGE_THUMBNAILS_HTML', HTML_PATH_UPLOADS_PAGES . PAGE_IMAGES_KEY . '/thumbnails/' );
	define( 'PAGE_THUMBNAILS_URL', DOMAIN_UPLOADS_PAGES.PAGE_IMAGES_KEY . '/thumbnails/' );
} else {
	define( 'PAGE_IMAGES_DIRECTORY', ( IMAGE_RELATIVE_TO_ABSOLUTE ? '' : HTML_PATH_UPLOADS ) );
	define( 'PAGE_IMAGES_URL', ( IMAGE_RELATIVE_TO_ABSOLUTE ? '' : DOMAIN_UPLOADS ) );
	define( 'PAGE_THUMBNAILS_DIRECTORY', PATH_UPLOADS_THUMBNAILS );
	define( 'PAGE_THUMBNAILS_HTML', HTML_PATH_UPLOADS_THUMBNAILS );
	define( 'PAGE_THUMBNAILS_URL', DOMAIN_UPLOADS_THUMBNAILS );
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'Edit Content' ),
	site()->title()
);
