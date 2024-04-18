<?php
/**
 * Tag object
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Content
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Tag {

	/**
	 * Tag variables
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $vars;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return self
	 */
	public function __construct( $key ) {

		// Access global variables.
		global $tags;

		if ( isset( $tags->db[$key] ) ) {
			$this->vars['name']      = $tags->db[$key]['name'];
			$this->vars['key']       = $key;
			$this->vars['permalink'] = DOMAIN_TAGS . $key;
			$this->vars['list']      = $tags->db[$key]['list'];
		} else {
			$errorMessage = 'Tag not found in database by key [' . $key . ']';
			\Log :: set( __METHOD__ . LOG_SEP.$errorMessage );
			throw new \Exception( $errorMessage );
		}
	}

	/**
	 * Get field value
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @return mixed
	 */
	public function getValue( $field ) {
		if ( isset( $this->vars[$field] ) ) {
			return $this->vars[$field];
		}
		return false;
	}

	/**
	 * Tag key
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function key() {
		return $this->getValue( 'key' );
	}

	/**
	 * Tag name
	 *
	 * The display name of the tag.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function name() {
		return $this->getValue( 'name' );
	}

	/**
	 * Tag permalink
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function permalink() {
		return $this->getValue( 'permalink' );
	}

	/**
	 * Pages tagged
	 *
	 * Returns an array with the pages keys linked to the tag.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function pages() {
		return $this->getValue( 'list' );
	}

	/**
	 * Tag data
	 *
	 * Returns an array in json format with all the data of the tag.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function json( $returnsArray = false ) {
		$tmp['key']       = $this->key();
		$tmp['name']      = $this->name();
		$tmp['permalink'] = $this->permalink();
		$tmp['pages']     = $this->pages();

		if ( $returnsArray ) {
			return $tmp;
		}
		return json_encode( $tmp );
	}
}
