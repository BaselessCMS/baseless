<?php
/**
 * Security
 *
 * @package    JSON CMS
 * @subpackage Boot
 * @category   Rules
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	if ( isset( $_POST['tokenCSRF'] ) ) {
		$token = Sanitize :: html( $_POST['tokenCSRF'] );
	} else {
		$token = false;
	}

	if ( ! $security->validateTokenCSRF( $token ) ) {

		Log :: set( __FILE__ . LOG_SEP . 'Error occurred when trying to validate the tokenCSRF.', ALERT_STATUS_FAIL );
		Log :: set( __FILE__ . LOG_SEP . 'Token via POST [' . $token . ']', ALERT_STATUS_FAIL );

		Session :: destroy();
		Redirect :: page( 'login' );
	} else {
		unset( $_POST['tokenCSRF'] );
	}
}
