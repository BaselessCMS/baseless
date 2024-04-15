<?php
/**
 * Edit category controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS_Admin\Controllers\Edit_Category;

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

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if ( 'delete' == $_POST['action'] ) {
		deleteCategory( $_POST );
	} elseif ( 'edit' == $_POST['action'] ) {
		editCategory( $_POST );
	}

	\Redirect :: page( 'categories' );
}

$categoryKey = $layout['parameters'];

if ( ! $categories->exists( $categoryKey ) ) {
	\Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to get the category: ' . $categoryKey );
	\Redirect :: page( 'categories' );
}

$cat_map = cats()->getMap( $categoryKey );

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	$L->g( 'Edit Category' ),
	$site->title()
);
