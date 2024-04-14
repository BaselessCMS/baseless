<?php
/**
 * Get published pages
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

header('Content-Type: application/json');

/**
 * Returns a list of pages and the title contains the query string.
 * The returned list have published, sticky and statics pages.
 *
 * @_POST['query'] string The string to search in the title of the pages.
 */

// $_GET
$query = isset( $_GET['query'] ) ? \Text :: lowercase( $_GET['query'] ) : false;
$checkIsParent = empty( $_GET['checkIsParent'] ) ? false : true;

if ( false === $query ) {
	ajaxResponse( 1, 'Invalid query.' );
}

$result   = [];
$pagesKey = $pages->getDB();
foreach ( $pagesKey as $pageKey ) {
	try {
		$page = new \Page( $pageKey );
		if ( $page->isParent() || ! $checkIsParent ) {

			// Check page status.
			if ( $page->published() || $page->sticky() || $page->isStatic() ) {

				// Check if the query contains in the title.
				$title = \Text :: lowercase( $page->title() );
				if ( \Text :: stringContains( $title, $query) ) {
					$tmp = [
						'disabled' => false
					];
					$tmp['id']   = $page->key();
					$tmp['text'] = $page->title();
					$tmp['type'] = $page->type();

					array_push( $result, $tmp );
				}
			}
		}
	} catch (Exception $e) {
		// continue
	}
}

exit( json_encode( [ 'results' => $result ] ) );
