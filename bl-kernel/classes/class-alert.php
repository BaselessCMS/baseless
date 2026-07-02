<?php
/**
 * Alert helpers
 *
 * @package    Baseless
 * @subpackage Classes
 * @category   UI
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Alert {

	/**
	 * Set status
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  mixed $value
	 * @param  string $status
	 * @param  string $key
	 * @return void
	 */
	public static function set( $value, $status = ALERT_STATUS_OK, $key = 'alert' ) {
		\Session :: set( 'defined', true );
		\Session :: set( 'alertStatus', $status );
		\Session :: set( $key, $value );
	}

	/**
	 * Get
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $key
	 * @return string
	 */
	public static function get( $key = 'alert' ) {
		\Session :: set( 'defined', false );
		return \Session :: get( $key );
	}

	/**
	 * Status
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $key
	 * @return string
	 */
	public static function status() {
		return \Session :: get( 'alertStatus' );
	}

	/**
	 * Print
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $key
	 * @return string
	 */
	public static function p( $key = 'alert' ) {
		echo self :: get( $key );
	}

	/**
	 * Defined
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return string
	 */
	public static function defined() {
		return \Session :: get( 'defined' );
	}
}
