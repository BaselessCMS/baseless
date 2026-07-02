<?php
/**
 * Categories database
 *
 * @package    Baseless
 * @subpackage Classes
 * @category   Databases
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Categories extends dbList {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		parent :: __construct( DB_CATEGORIES );
	}

	/**
	 * Count pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key Page key.
	 * @return string
	 */
	public function numberOfPages( $key ) {
		return $this->countItems( $key );
	}

	/**
	 * Re-index the list
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $pages The Pages class.
	 * @return boolean
	 */
	public function reindex() {

		// Access global variables.
		global $pages;

		foreach ( $this->db as $key => $value ) {
			$this->db[$key]['list'] = [];
		}

		// Get pages database.
		$db = $pages->getDB( false );
		foreach ( $db as $page_key => $page_fields ) {

			if ( ! empty( $page_fields['category'] ) ) {
				$cat_key = $page_fields['category'];

				if ( isset( $this->db[$cat_key]['list'] ) ) {
					if (
						( 'published' == $db[$page_key]['type'] ) ||
						( 'sticky'    == $db[$page_key]['type'] ) ||
						( 'static'    == $db[$page_key]['type'] )
					) {
						array_push( $this->db[$cat_key]['list'], $page_key );
					}
				}
			}
		}
		return $this->save();
	}
}
