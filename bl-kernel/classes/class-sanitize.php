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
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Sanitize {

	/**
	 * Remove tags
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $text
	 * @return string
	 */
	public static function removeTags( $text ) {
		return strip_tags( $text );
	}

	/**
	 * HTML entities
	 *
	 * Converts special characters to HTML entities.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $text
	 * @return string
	 */
	public static function html( $text ) {

		$flags = ENT_COMPAT;

		if ( defined( 'ENT_HTML5' ) ) {
			$flags = ENT_COMPAT|ENT_HTML5;
		}
		return htmlspecialchars( $text, $flags, CHARSET );
	}

	/**
	 * Decode HTML entities
	 *
	 * Converts special HTML entities back to characters.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $text
	 * @return string
	 */
	public static function htmlDecode( $text ) {

		$flags = ENT_COMPAT;

		if ( defined( 'ENT_HTML5' ) ) {
			$flags = ENT_COMPAT|ENT_HTML5;
		}
		return htmlspecialchars_decode( $text, $flags );
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
	 * @param  string $email
	 * @return string
	 */
	public static function email( $email ) {
		return ( filter_var( $email, FILTER_SANITIZE_EMAIL ) );
	}

	/**
	 * URL
	 *
	 * Returns the URL without illegal characters.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $url
	 * @return string
	 */
	public static function url( $url ) {
		return ( filter_var( $url, FILTER_SANITIZE_URL ) );
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
		} else {
			return 0;
		}
	}
}
