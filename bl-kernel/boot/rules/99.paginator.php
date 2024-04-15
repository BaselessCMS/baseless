<?php
/**
 * Paginator
 *
 * @package    JSON CMS
 * @subpackage Boot
 * @category   Rules
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Current page number
$current = $url->pageNumber();
Paginator :: set( 'currentPage', $current );

if ( $url->whereAmI() == 'admin' ) {
	$items = ITEMS_PER_PAGE_ADMIN;
	$count = $pages->count( true );

} elseif ( $url->whereAmI() == 'tag' ) {
	$items   = $site->itemsPerPage();
	$tag_key = $url->slug();
	$count   = $tags->numberOfPages( $tag_key );

} elseif ( $url->whereAmI() == 'category' ) {
	$items   = $site->itemsPerPage();
	$cat_key = $url->slug();
	$count   = $categories->numberOfPages( $cat_key );

} else {
	$items = $site->itemsPerPage();
	$count = $pages->count( true );
}

// Execute hook from plugins
Theme :: plugins( 'paginator' );

// Items per page
Paginator :: set( 'itemsPerPage', $items );

// Amount of items
Paginator :: set( 'numberOfItems', $count );

// Amount of pages
$num_pages = (int) max( ceil( $count / $items ), 1 );
Paginator :: set( 'numberOfPages', $num_pages );

// TRUE if exists a next page to show
$show_next = $num_pages > $current;
Paginator :: set( 'showNext', $show_next );

// TRUE if exists a previous page to show
$show_prev = $current > Paginator :: firstPage();
Paginator :: set( 'showPrev', $show_prev );

// TRUE if exists a next and previous page to show
$show_next_prev = $show_next && $show_prev;
Paginator :: set( 'showNextPrev', $show_next_prev );

// Integer with the next page
$next_page = max( 0, $current + 1 );
Paginator :: set( 'nextPage', $next_page );

// Integer with the previous page
$prev_page = min( $num_pages, $current - 1 );
Paginator :: set( 'prevPage', $prev_page );
