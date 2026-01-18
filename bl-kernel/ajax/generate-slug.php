<?php
/**
 * Generate slug
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
 * Generate an slug text for the URL
 *
 * @_POST['text'] string The text from where is generated the slug
 * @_POST['parentKey'] string The parent key if the page has one
 * @_POST['currentKey'] string The current page key
 */

// $_POST
$text 	= isset( $_POST['text'] ) ? $_POST['text'] : '';
$parent = isset( $_POST['parentKey'] ) ? $_POST['parentKey'] : '';
$oldKey = isset( $_POST['currentKey'] ) ? $_POST['currentKey'] : '';

// Slug.
$slug = $pages->generateKey( $text, $parent, $returnSlug = true, $oldKey );

ajax_response( 0, 'Slug generated.', [
	'slug' => $slug
] );
