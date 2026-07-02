<?php
/**
 * Tags database
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

class Tags extends dbList {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		parent :: __construct( DB_TAGS );
	}

	/**
	 * Number of pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return integer
	 */
	public function numberOfPages( $key ) {
		return $this->countItems( $key );
	}

	/**
	 * Re-index tags
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $pages The Pages class.
	 * @return boolean
	 */
	public function reindex() {

		// Access global variables.
		global $pages;

		$db = $pages->getDB( $onlyKeys = false );
		$tagsIndex = [];

		foreach ( $db as $pageKey => $pageFields ) {

			if ( in_array( $pageFields['type'], $GLOBALS['DB_TAGS_TYPES'] ) ) {

				$tags = $pageFields['tags'];
				foreach ( $tags as $tagKey => $tagName ) {

					if ( isset( $tagsIndex[$tagKey] ) ) {
						array_push( $tagsIndex[$tagKey]['list'], $pageKey );
					} else {
						$tagsIndex[$tagKey]['name'] = $tagName;
						$tagsIndex[$tagKey]['list'] = [ $pageKey ];
					}
				}
			}
		}
		$this->db = $tagsIndex;
		$this->sortAlphanumeric();
		return $this->save();
	}
}
