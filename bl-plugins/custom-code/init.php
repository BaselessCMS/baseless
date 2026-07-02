<?php
/**
 * Custom code plugin
 *
 * @package    Custom Code
 * @subpackage Plugins
 * @version    1.0.0
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Custom_Code extends Plugin {

	/**
	 * Initialize plugin
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function init() {
		$this->dbFields = [
			'head'        => '',
			'header'      => '',
			'footer'      => '',
			'adminHead'   => '',
			'adminHeader' => '',
			'adminFooter' => ''
		];
	}

	/**
	 * Admin settings form
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L Language class.
	 * @return string Returns the markup of the form.
	 */
	public function form() {

		// Access global variables.
		global $L;

		$html = '';
		ob_start();
		include( $this->phpPath() . '/views/page-form.php' );
		$html .= ob_get_clean();

		return $html;
	}

	/**
	 * Hook: siteHead
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function siteHead() {
		return html_entity_decode( $this->getValue( 'head' ) );
	}

	/**
	 * Hook: siteBodyBegin
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function siteBodyBegin() {
		return html_entity_decode( $this->getValue( 'header' ) );
	}

	/**
	 * Hook: siteBodyEnd
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function siteBodyEnd() {
		return html_entity_decode( $this->getValue( 'footer' ) );
	}

	/**
	 * Hook: adminHead
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function adminHead() {
		return html_entity_decode( $this->getValue( 'adminHead' ) );
	}

	/**
	 * Hook: adminBodyBegin
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function adminBodyBegin() {
		return html_entity_decode( $this->getValue( 'adminHeader' ) );
	}

	/**
	 * Hook: adminBodyEnd
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function adminBodyEnd() {
		return html_entity_decode( $this->getValue( 'adminFooter' ) );
	}
}
