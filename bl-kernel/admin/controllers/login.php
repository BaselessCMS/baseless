<?php
/**
 * Login screen controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Login;

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	login,
	security,
	site
};

/**
 * Check login
 *
 * @since  1.0.0
 * @param  array $args
 * @return boolean
 */
function checkLogin( $args ) {

	if ( security()->isBlocked() ) {
		\Alert :: set( lang()->g( 'IP address has been blocked' ) . '<br>' . lang()->g( 'Try again in a few minutes.' ), ALERT_STATUS_FAIL );
		return false;
	}

	if ( login()->verifyUser( $_POST['username'], $_POST['password'] ) ) {

		if ( isset( $_POST['remember'] ) ) {
			login()->setRememberMe( $_POST['username'] );
		}

		/**
		 * Renew the token
		 *
		 * This token will be the same inside
		 * the session for multiple forms.
		 */
		security()->generateTokenCSRF();

		if ( isset( $_GET['enableAPI'] ) ) {
			\Redirect :: page( 'api' );
		}
		\Redirect :: page( 'dashboard' );
		return true;
	}

	// Brute force protection, add IP to the blacklist.
	security()->addToBlacklist();

	// Create alert.
	\Alert :: set( lang()->g( 'Username or password is incorrect.' ), ALERT_STATUS_FAIL );
	return false;
}

/**
 * Remember Me checkbox
 *
 * @since  1.0.0
 * @return boolean
 */
function checkRememberMe() {

	if ( security()->isBlocked() ) {
		return false;
	}

	if ( login()->verifyUserByRemember() ) {
		security()->generateTokenCSRF();
		\Redirect :: page( 'dashboard' );
		return true;
	}
	return false;
}

if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
	checkRememberMe();
}

if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	checkLogin( $_POST );
}
