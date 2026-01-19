<?php
/**
 * System log helpers
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Log {

	/**
	 * Print to log
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $text
	 * @param  string $type
	 * @return void
	 */
	public static function set( $text, $type = LOG_TYPE_INFO ) {

		if ( ! DEBUG_MODE ) {
			return false;
		}

		$messageType = 0;
		if ( is_array( $text ) ) {
			error_log( '------------------------', $messageType );
			error_log( 'Array', $messageType);
			error_log( '------------------------', $messageType );
			foreach ( $text as $key => $value) {
				error_log( $key . '=>' . $value, $messageType );
			}
			error_log( '------------------------', $messageType );
		}
		error_log( $type . ' [' . CMS_VERSION . '] [' . $_SERVER['REQUEST_URI'] . '] ' . $text, $messageType );

		if ( 'TRACE' == DEBUG_TYPE ) {
			error_log( print_r( debug_backtrace(), true ) );
		}
	}
}
