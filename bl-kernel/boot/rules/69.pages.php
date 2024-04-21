<?php
/**
 * Pages
 *
 * @package    JSON CMS
 * @subpackage Boot
 * @category   Rules
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

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
$staticContent = $staticPages = buildStaticPages();

// Execute the scheduler.
if ( $pages->scheduler() ) {

	// Execute plugins with the hook afterPageCreate.
	Theme :: plugins( 'afterPageCreate' );

	reindexTags();
    reindexCategories();

	// Add to syslog.
	$syslog->add( [
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
	$content[0] = $page = buildThePage();

// Build content by tag.
} elseif ( $url->whereAmI() === 'tag' ) {
	$content = buildPagesByTag();

// Build content by category.
} elseif ( $url->whereAmI() === 'category' ) {
	$content = buildPagesByCategory();

// Build content for the homepage.
} elseif ( ( $url->whereAmI() === 'home' ) || ( $url->whereAmI() === 'blog' ) ) {
        $content = buildPagesForHome();
}

if ( isset( $content[0] ) ) {
	$page = $content[0];
}

// If set notFound, create the page 404.
if ( $url->notFound() ) {
	$content[0] = $page = buildErrorPage();
}
