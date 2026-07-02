<?php
/**
 * URL redirect helpers
 *
 * @package    Baseless
 * @subpackage Classes
 * @category   Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Redirect {

	/**
	 * URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $url
	 * @param  integer $httpCode
	 * @return void
	 */
	public static function url( $url, $httpCode = 301 ) {

		if ( ! headers_sent() ) {
			header( 'Location:' . $url, TRUE, $httpCode );
			exit( 0 );
		}
		exit( '<meta http-equiv="refresh" content="0; url=' . $url . '"/>' );
	}

	/**
	 * Redirect to page
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $page
	 * @return void
	 */
	public static function page( $page ) {
		self :: url( HTML_PATH_ADMIN_ROOT . $page );
	}

	/**
	 * Redirect to home
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $page
	 * @return void
	 */
	public static function home() {
		self :: url( HTML_PATH_ROOT );
	}

	/**
	 * Redirect to admin
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $page
	 * @return void
	 */
	public static function admin() {
		self :: url( HTML_PATH_ADMIN_ROOT );
	}
}
