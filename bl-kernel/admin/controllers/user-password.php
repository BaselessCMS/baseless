<?php
/**
 * User password controller
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\User_PW;

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	login,
	site
};
use function CMS\Func\{
	change_user_password
};

if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {

	// Prevent non-administrators to change other users.
	$username = $_POST['username'];
	if ( 'admin' !== login()->role() ) {
	    $username = login()->username();
	}

	if ( change_user_password( [
		'username'        => $username,
		'newPassword'     => $_POST['newPassword'],
		'confirmPassword' => $_POST['confirmPassword']
	] )
	) {
		if ( 'admin' === login()->role() ) {
			\Redirect :: page( 'users' );
		}
		\Redirect :: page( 'edit-user/' . login()->username() );
	}
}

// Prevent non-administrators to change other users.
if ( 'admin' !== login()->role() ) {
	$layout['parameters'] = login()->username();
}

try {
	$username = $layout['parameters'];
	$user     = new \User( $username );
} catch ( \Exception $e ) {
	\Redirect :: page( 'users' );
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'Change Password' ),
	site()->title()
);
