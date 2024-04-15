<?php
/**
 * Themes management controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Themes;

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

$themes = buildThemes();

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	$L->g( 'Themes' ),
	$site->title()
);
