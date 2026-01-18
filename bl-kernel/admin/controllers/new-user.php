<?php
/**
 * New user controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\New_User;

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	site
};
use function CMS\Func\{
	check_role,
	create_user
};

check_role( [ 'admin' ] );

if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	if ( create_user( $_POST ) ) {
		\Redirect :: page( 'users' );
	}
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'New User' ),
	site()->title()
);
