<?php
/**
 * System info controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\System;

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	site,
	security,
	url,
	lang,
	users,
	plugins,
	page,
	pages,
	cats
};

checkRole( [ 'admin' ] );

/**
 * System info table
 *
 * This function is used on the VIEW to show the tables.
 *
 * @since  1.0.0
 * @param  string $title
 * @param  arraY $array
 * @return void
 */
function table( $title, $array, $heading_el = 'h2' ) {

	printf(
		'<%s class="system-info-heading">%s</%s>',
		$heading_el,
		$title,
		$heading_el
	);
	echo '<table class="table table-striped system-info-table"><tbody>';

	foreach ( $array as $key => $value ) {

		if ( false === $value ) {
			$value = 'false';
		} elseif ( true === $value ) {
			$value = 'true';
		}
		echo '<tr>';
		printf(
			'<td>%s</td>',
			$key
		);

		if ( is_array( $value ) ) {
			printf(
				'<td></td>',
				json_encode( $value )
			);
		} else {
			printf(
				'<td>%s</td>',
				\Sanitize :: html( $value )
			);
		}
		echo '</tr>';
	}
	echo '</tbody></table>';
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	$L->g( 'System' ),
	$site->title()
);
