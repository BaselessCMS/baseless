<?php
/**
 * New content controller
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\New_Content;

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	pages,
	site
};
use function CMS\Func\{
	check_role,
	create_page
};

check_role( [ 'admin', 'editor', 'author' ] );

if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	create_page( $_POST );
	\Redirect :: page( 'content' );
}

// UUID of the page is need it for autosave and media manager.
$uuid = pages()->generateUUID();

// Images prefix directory.
define( 'PAGE_IMAGES_KEY', $uuid );

// Images and thumbnails directories.
if ( IMAGE_RESTRICT ) {
	define( 'PAGE_IMAGES_DIRECTORY', ( IMAGE_RELATIVE_TO_ABSOLUTE ? '' : HTML_PATH_UPLOADS_PAGES . PAGE_IMAGES_KEY . '/' ) );
	define( 'PAGE_IMAGES_URL', ( IMAGE_RELATIVE_TO_ABSOLUTE ? '' : DOMAIN_UPLOADS_PAGES.PAGE_IMAGES_KEY . '/' ) );
	define( 'PAGE_THUMBNAILS_DIRECTORY', PATH_UPLOADS_PAGES . PAGE_IMAGES_KEY . DS . 'thumbnails' . DS );
	define( 'PAGE_THUMBNAILS_HTML', HTML_PATH_UPLOADS_PAGES . PAGE_IMAGES_KEY . '/thumbnails/' );
	define( 'PAGE_THUMBNAILS_URL', DOMAIN_UPLOADS_PAGES . PAGE_IMAGES_KEY . '/thumbnails/' );

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
	lang()->g( 'New Content' ),
	site()->title()
);
