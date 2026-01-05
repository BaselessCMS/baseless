<?php
/**
 * Sanitization helpers
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

class Sanitize {

	/**
	 * HTML entities
	 *
	 * Converts special characters to HTML entities.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $value
	 * @return string
	 */
	public static function html( $value ) {

		if ( ! is_string( $value ) ) {
			return $value;
		}

		$flags = ENT_COMPAT;

		if ( defined( 'ENT_HTML5' ) ) {
			$flags = ENT_COMPAT|ENT_HTML5;
		}
		return htmlspecialchars( $value, $flags, CHARSET );
	}

	/**
	 * Decode HTML entities
	 *
	 * Converts special HTML entities back to characters.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $value
	 * @return string
	 */
	public static function htmlDecode( $value ) {

		if ( ! is_string( $value ) ) {
			return $value;
		}

		$flags = ENT_COMPAT;

		if ( defined( 'ENT_HTML5' ) ) {
			$flags = ENT_COMPAT|ENT_HTML5;
		}
		return htmlspecialchars_decode( $value, $flags );
	}

	/**
	 * Real file path
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $path
	 * @param  boolean $file
	 * @return boolean
	 */
	public static function pathFile( $path, $file = false ) {

		if ( false !== $file ) {
			$fullPath = $path . $file;
		} else {
			$fullPath = $path;
		}

		// Fix for Windows on paths. eg: $path = c:\diego/page/subpage convert to c:\diego\page\subpages.
		$fullPath = str_replace( '/', DS, $fullPath );

		if ( CHECK_SYMBOLIC_LINKS ) {
			$real = realpath( $fullPath );
		} else {
			$real = file_exists( $fullPath ) ? $fullPath : false;
		}

		// If $real is false the file does not exist.
		if ( false === $real ) {
			return false;
		}

		// If the $real path does not start with the systemPath then this is path traversal.
		if ( strpos( $fullPath, $real ) !== 0 ) {
			return false;
		}
		return true;
	}

	/**
	 * Email address
	 *
	 * Returns the email without illegal characters.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $value
	 * @return string
	 */
	public static function email( $value ) {

		if ( ! is_string( $value ) ) {
			return $value;
		}
		return ( filter_var( $value, FILTER_SANITIZE_EMAIL ) );
	}

	/**
	 * URL
	 *
	 * Returns the URL without illegal characters.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $value
	 * @return string
	 */
	public static function url( $value ) {

		if ( ! is_string( $value ) ) {
			return $value;
		}
		return ( filter_var( $value, FILTER_SANITIZE_URL ) );
	}

	/**
	 * Integer
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  integer $value
	 * @return integer
	 */
	public static function int( $value ) {

		$value = (int)$value;
		if ( $value >= 0 ) {
			return $value;
		}
		return 0;
	}
}
