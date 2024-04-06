<?php
/**
 * Content page controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

checkRole( [ 'admin', 'editor', 'author' ] );

/**
 * Content filter owner
 *
 * Returns the content belongs to the current user
 * if the user has the role Editor.
 *
 * @since  1.0.0
 * @param  array $list
 * @return array
 */
function filterContentOwner( $list ) {

	// Access global variables.
	global $login, $pages;

	$tmp = [];
	foreach ( $list as $key ) {
		if ( $login->username() == $pages->db[$key]['username'] ) {
			array_push( $tmp, $key );
		}
	}
	return $tmp;
}

$published = $pages->getList( $url->pageNumber(), ITEMS_PER_PAGE_ADMIN );
$drafts    = $pages->getDraftDB( true );
$scheduled = $pages->getScheduledDB( true );
$static    = $pages->getStaticDB( true );
$sticky    = $pages->getStickyDB( true );
$autosave  = $pages->getAutosaveDB( true );

// If the user is an Author filter the content he/she can edit
if ( checkRole( [ 'author' ], false ) ) {
	$published = filterContentOwner( $published );
	$drafts    = filterContentOwner( $drafts );
	$scheduled = filterContentOwner( $scheduled );
	$static    = filterContentOwner( $static );
	$sticky    = filterContentOwner( $sticky );
}

// Check if out of range the pageNumber.
if ( empty( $published ) && $url->pageNumber() > 1 ) {
	\Redirect :: page( 'content' );
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'Manage Content' ),
	site()->title()
);
