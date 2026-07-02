<?php
/**
 * User password controller
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Users;

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

if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	site()->set( $_POST );
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'Manage Users' ),
	site()->title()
);
