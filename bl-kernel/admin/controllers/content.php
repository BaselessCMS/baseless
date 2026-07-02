<?php
/**
 * Content page controller
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Content;

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	login,
	pages,
	site,
	url
};
use function CMS\Func\{
	check_role
};

check_role( [ 'admin', 'editor', 'author' ] );

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
function _content_owner( $list ) {

	$tmp = [];
	foreach ( $list as $key ) {
		if ( login()->username() == pages()->db[$key]['username'] ) {
			array_push( $tmp, $key );
		}
	}
	return $tmp;
}

$published = pages()->getList( url()->pageNumber(), ITEMS_PER_PAGE_ADMIN );
$drafts    = pages()->getDraftDB( true );
$scheduled = pages()->getScheduledDB( true );
$static    = pages()->getStaticDB( true );
$sticky    = pages()->getStickyDB( true );
$autosave  = pages()->getAutosaveDB( true );

// If the user is an Author filter the content he/she can edit.
if ( check_role( [ 'author' ], false ) ) {
	$published = _content_owner( $published );
	$drafts    = _content_owner( $drafts );
	$scheduled = _content_owner( $scheduled );
	$static    = _content_owner( $static );
	$sticky    = _content_owner( $sticky );
}

// Check if out of range the pageNumber.
if ( empty( $published ) && url()->pageNumber() > 1 ) {
	\Redirect :: page( 'content' );
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'Manage Content' ),
	site()->title()
);
