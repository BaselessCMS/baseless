<?php
/**
 * DOM helpers
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

class DOM {

	/**
	 * Get first image
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $content
	 * @return mixed
	 */
	public static function getFirstImage( $content ) {

		// Disable warning.
		libxml_use_internal_errors( true );
		$dom = new \DOMDocument();
		$dom->loadHTML( '<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $content );
		$finder = new \DomXPath( $dom );

		$images = $finder->query( '//img' );
		if ( $images->length > 0 ) {

			// First image from the list.
			$image = $images->item(0);

			// Get value from attribute src.
			$imgSrc = $image->getAttribute( 'src' );

			// Returns the image src.
			return $imgSrc;
		}
		return false;
	}
}
