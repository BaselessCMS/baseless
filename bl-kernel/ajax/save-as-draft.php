<?php
/**
 * Save as draft
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
 * $_POST
 *
 * Create/edit a page and save as draft.
 * If the UUID already exists the page is updated.
 */
$title   = isset( $_POST['title'] ) ? $_POST['title'] : false;
$content = isset( $_POST['content'] ) ? $_POST['content'] : false;
$cover   = isset( $_POST['coverImage'] ) ? $_POST['coverImage'] : false;
$uuid    = isset( $_POST['uuid'] ) ? $_POST['uuid'] : false;
$type    = isset( $_POST['type'] ) ? $_POST['type'] : 'draft';

// Check UUID.
if ( empty( $uuid ) ) {
	ajaxResponse( 1, 'Draft save failed. UUID is not defined.' );
}

$page = [
	'uuid'    => $uuid,
	'key'     => $uuid,
	'slug'    => $uuid,
	'title'   => $title,
	'content' => $content,
	'cover'   => $cover,
	'type'    => $type
];

// Get the page key by the UUID.
$pageKey = $pages->getByUUID( $uuid );

// If pageKey is empty means the page doesn't exist.
if ( empty( $pageKey ) ) {
	create_page( $page );
} else {
	edit_page( $page );
}

ajaxResponse( 0, 'Draft saved successfully.', [
	'uuid' => $uuid
] );
