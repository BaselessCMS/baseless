<?php
/**
 * Categories page controller
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

checkRole( [ 'admin' ] );

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'Categories' ),
	site()->title()
);
