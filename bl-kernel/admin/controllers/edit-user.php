<?php
/**
 * Edit user controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Edit_User;

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

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	// Prevent non-administrators to change other users.
	if ( 'admin' !== $login->role() ) {
		$_POST['username'] = $login->username();
		unset( $_POST['role'] );
	}

	if ( isset( $_POST['delete_userAndDeleteContent'] ) && ( 'admin' === $login->role() ) ) {
		$_POST['deleteContent'] = true;
		delete_user( $_POST );

	} elseif ( isset( $_POST['delete_userAndKeepContent'] ) && ( 'admin' === $login->role() ) ) {
		$_POST['deleteContent'] = false;
		delete_user( $_POST );

	} elseif ( isset( $_POST['disable_user'] ) && ( 'admin' === $login->role() )) {
		disable_user( [ 'username' => $_POST['username'] ] );

	} else {
		edit_user( $_POST );
	}

	\Alert :: set( $L->g( 'The changes have been saved' ) );

	if ( 'admin' === $login->role() ) {
		// @todo Make setting for redirect.
		\Redirect :: page( 'users' );
	}
	\Redirect :: page( 'edit-user/' . $login->username() );
}

$username = $layout['parameters'];

// Prevent non-administrators to change other users.
if ( 'admin' !== $login->role() ) {
	$username = $login->username();
}

try {
	$user = new \User( $username );
} catch ( \Exception $e ) {
	\Redirect :: page( 'users' );
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	$L->g( 'Edit User' ),
	$site->title()
);
