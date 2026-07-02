<?php
/**
 * Cookie helpers
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

class Cookie {

	/**
	 * Get cookie
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $key
	 * @return mixed
	 */
	public static function get( $key ) {
		if ( isset( $_COOKIE[$key] ) ) {
			return $_COOKIE[$key];
		}
		return false;
	}

	/**
	 * Set cookie
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $key
	 * @param  mixed $value
	 * @param  integer $daysToExpire
	 * @return void
	 */
	public static function set( $key, $value, $daysToExpire = 30 ) {

		/**
		 * The time the cookie expires.
		 *
		 * This is a Unix timestamp so is in number of seconds since the epoch.
		 * In other words, you'll most likely set this with the time() function
		 * plus the number of seconds before you want it to expire.
		 *
		 * Or you might use mktime(). time()+60*60*24*30 will set the
		 * cookie to expire in 30 days.
		 *
		 * If set to 0, or omitted, the cookie will expire at the end
		 * of the session (when the browser closes).
		 */
		$expire = time() + 60 * 60 * 24 * $daysToExpire;
		setcookie( $key, $value, $expire );
	}

	/**
	 * Remove cookie
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $key
	 * @return void
	 */
	public static function remove( $key ) {
		unset( $_COOKIE[$key] );
		setcookie( $key, null, time() - 3600 );
	}

	/**
	 * Cookie is empty
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $key
	 * @return boolean
	 */
	public static function isEmpty( $key ) {
		return empty( $_COOKIE[$key] );
	}
}
