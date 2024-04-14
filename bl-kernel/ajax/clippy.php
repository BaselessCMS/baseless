<?php
/**
 * Clippy
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

// $_GET
$query = isset( $_GET['query'] ) ? \Text :: lowercase( $_GET['query'] ) : false;

if ( false === $query ) {
	ajaxResponse( 1, 'Invalid query.' );
}

$result = [];

// MENU
if ( \Text :: stringContains( \Text :: lowercase( $L->g( 'New content' ) ), $query ) ) {
	$tmp = [
		'disabled' => true,
		'icon'     => 'plus-circle',
		'type'     => 'menu'
	];
	$tmp['text'] = $L->g( 'New Content' );
	$tmp['url']  = HTML_PATH_ADMIN_ROOT . 'new-content';

	array_push( $result, $tmp );
}
if ( \Text :: stringContains( \Text :: lowercase( $L->g( 'New category' ) ), $query ) ) {
	$tmp = [
		'disabled' => true,
		'icon'     => 'tag',
		'type'     => 'menu'
	];
	$tmp['text'] = $L->g( 'New Category' );
	$tmp['url']  = HTML_PATH_ADMIN_ROOT . 'new-category';

	array_push( $result, $tmp );
}
if ( \Text :: stringContains( \Text :: lowercase( $L->g( 'New user' ) ), $query ) ) {
	$tmp = [
		'disabled' => true,
		'icon'     => 'user',
		'type'     => 'menu'
	];
	$tmp['text'] = $L->g( 'New User' );
	$tmp['url']  = HTML_PATH_ADMIN_ROOT . 'new-user';

	array_push( $result, $tmp );
}
if ( \Text :: stringContains( \Text :: lowercase( $L->g( 'Categories' ) ), $query ) ) {
	$tmp = [
		'disabled' => true,
		'icon'     => 'tags',
		'type'     => 'menu'
	];
	$tmp['text'] = $L->g( 'Categories' );
	$tmp['url']  = HTML_PATH_ADMIN_ROOT . 'categories';

	array_push( $result, $tmp );
}
if ( \Text :: stringContains( \Text :: lowercase( $L->g( 'Users' ) ), $query ) ) {
	$tmp = [
		'disabled' => true,
		'icon'     => 'users',
		'type'     => 'menu'
	];
	$tmp['text'] = $L->g( 'Users' );
	$tmp['url']  = HTML_PATH_ADMIN_ROOT . 'users';

	array_push( $result, $tmp );
}


// PAGES
$pagesKey = $pages->getDB();
foreach ( $pagesKey as $pageKey ) {
	try {
		$page  = new \Page( $pageKey );
		$title = \Text :: lowercase( $page->title() );

		if ( \Text :: stringContains( $title, $query ) ) {

			$tmp = [ 'disabled' => true ];
			$tmp['id']   = $page->key();
			$tmp['text'] = $page->title();
			$tmp['type'] = $page->type();

			array_push( $result, $tmp );
		}
	} catch ( Exception $e ) {
		// Continue.
	}
}

exit ( json_encode( [ 'results' => $result ] ) );
