<?php
/**
 * Pages
 *
 * @package    Baseless
 * @subpackage Boot
 * @category   Rules
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	syslog
};
use function CMS\Func\{
	build_pages_for_home,
	build_static_pages,
	build_the_page,
	build_pages_by_category,
	build_pages_by_tag,
	build_error_page,
	reindex_categories,
	reindex_tags
};

/**
 * Array with pages, each page is a page object.
 *
 * Filtered by page number, number of items per page
 * and sorted by date/position.
 *
 * ```
 * [
 *		0 => page object,
 *		1 => page object,
 *		...
 *		N => page object
 * ]```
*/
$content = [];

// Page filtered by the user is a page object.
$page = false;

/**
 * Array with static content, each item is a
 * page object, ordered by position.
 *
 * ```
 * [
 *		0 => page object,
 *		1 => page object,
 *		...
 *		N => page object
 * ]```
*/
$staticContent = $staticPages = build_static_pages();

// Execute the scheduler.
if ( $pages->scheduler() ) {

	// Execute plugins with the hook afterPageCreate.
	Theme :: plugins( 'afterPageCreate' );

	reindex_categories();
	reindex_tags();

	// Add to syslog.
	syslog()->add( [
		'dictionaryKey' => 'content-published-from-scheduler',
		'notes'         => ''
	] );
}

// Set home page if the user defined one.
if ( $site->homepage() && $url->whereAmI() === 'home' ) {

	$key = $site->homepage();
	if ( $pages->exists( $key ) ) {
		$url->setSlug( $key );
		$url->setWhereAmI( 'page' );
	}
}

// Build specific page.
if ( $url->whereAmI() === 'page' ) {
	$content[0] = $page = build_the_page();

// Build content by category.
} elseif ( $url->whereAmI() === 'category' ) {
	$content = build_pages_by_category();

// Build content by tag.
} elseif ( $url->whereAmI() === 'tag' ) {
	$content = build_pages_by_tag();

// Build content for the homepage.
} elseif ( ( $url->whereAmI() === 'home' ) || ( $url->whereAmI() === 'blog' ) ) {
        $content = build_pages_for_home();
}

if ( isset( $content[0] ) ) {
	$page = $content[0];
}

// If set notFound, create the page 404.
if ( $url->notFound() ) {
	$content[0] = $page = build_error_page();
}
