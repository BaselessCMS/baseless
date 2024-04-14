<?php
/**
 * Get content list
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
 * Search for pages that have in the title the string $query and returns the array of pages
 *
 * @_GET['published'] boolean True to search in published database
 * @_GET['static'] boolean True to search in static database
 * @_GET['sticky'] boolean True to search in sticky database
 * @_GET['scheduled'] boolean True to search in scheduled database
 * @_GET['draft'] boolean True to search in draft database
 * @_GET['query'] string Text to search in the title
 */

// $_GET
$published = empty( $_GET['published'] ) ? false : true;
$static    = empty( $_GET['static'] ) ? false : true;
$sticky    = empty( $_GET['sticky'] ) ? false : true;
$scheduled = empty( $_GET['scheduled'] ) ? false : true;
$draft     = empty( $_GET['draft'] ) ? false : true;
$query     = isset( $_GET['query'] ) ? \Text :: lowercase( $_GET['query'] ) : false;

if ( false === $query ) {
	ajaxResponse( 1, 'Invalid query.' );
}

$pageNumber    = 1;
$numberOfItems = -1;
$pagesKey      = $pages->getList( $pageNumber, $numberOfItems, $published, $static, $sticky, $draft, $scheduled );

$tmp = [];
foreach ( $pagesKey as $pageKey ) {
	try {
		$page  = new \Page( $pageKey );
		$title = \Text :: lowercase( $page->title() );
		if ( \Text :: stringContains( $title, $query ) ) {
			$tmp[ $page->key() ] = $page->json( true );
		}
	} catch ( Exception $e ) {
		// Continue.
	}
}

exit( json_encode( $tmp ) );
