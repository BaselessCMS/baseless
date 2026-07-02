<?php
/**
 * Themes management controller
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Themes;

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	site
};
use function CMS\Func\{
	check_role
};

check_role( [ 'admin' ] );

$themes = buildThemes();

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'Themes' ),
	site()->title()
);
