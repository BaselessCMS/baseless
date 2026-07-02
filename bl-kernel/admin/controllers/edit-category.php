<?php
/**
 * Edit category controller
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Edit_Category;

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	cats,
	lang,
	site
};
use function CMS\Func\{
	check_role,
	delete_category,
	edit_category
};

check_role( [ 'admin' ] );

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if ( 'delete' == $_POST['action'] ) {
		delete_category( $_POST );
	} elseif ( 'edit' == $_POST['action'] ) {
		edit_category( $_POST );
	}

	\Redirect :: page( 'categories' );
}

$key = $layout['parameters'];
if ( ! $categories->exists( $key ) ) {
	\Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to get the category ' . $key );
	\Redirect :: page( 'categories' );
}

$cat_map = cats()->getMap( $key );

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'Edit Category' ),
	site()->title()
);
