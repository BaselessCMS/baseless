<?php
/**
 * Validation helpers
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Valid {

	/**
	 * Validate IP
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $ip
	 * @return mixed
	 */
	public static function ip( $ip ) {
		return filter_var( $ip, FILTER_VALIDATE_IP );
	}

	/**
	 * Validate email
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $email
	 * @return mixed
	 */
	public static function email( $email ) {
		return filter_var( $email, FILTER_VALIDATE_EMAIL );
	}

	/**
	 * Validate integer
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $int
	 * @return boolean
	 */
	public static function int( $int ) {

		if ( $int === 0 ) {
			return true;
		} elseif ( filter_var( $int, FILTER_VALIDATE_INT ) === false ) {
			return false;
		}
		return true;
	}

	/**
	 * Validate date
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $date
	 * @param  string $format
	 * @return mixed
	 */
	public static function date( $date, $format = 'Y-m-d H:i:s' ) {
		$tmp = \DateTime :: createFromFormat( $format, $date );
		return $tmp && $tmp->format( $format ) == $date;
	}
}
