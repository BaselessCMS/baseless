<?php
/**
 * Category object
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

class Category {

	/**
	 * Variables
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
	 * @param  string $key The category key.
	 * @global object $categories The Categories class.
	 * @return self
	 */
	public function __construct( $key ) {

		// Access global variables.
		global $categories;

		if ( isset( $categories->db[$key] ) ) {
			$this->vars['name']        = $categories->db[$key]['name'];
			$this->vars['template']    = $categories->db[$key]['template'];
			$this->vars['description'] = $categories->db[$key]['description'];
			$this->vars['key']         = $key;
			$this->vars['permalink']   = DOMAIN_CATEGORIES . $key;
			$this->vars['list']        = $categories->db[$key]['list'];
		} else {
			$errorMessage = 'Category not found in database by key [' . $key . ']';
			\Log :: set( __METHOD__ . LOG_SEP . $errorMessage );
			throw new Exception( $errorMessage );
		}
	}

	/**
	 * Get value
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field Field name.
	 * @return mixed
	 */
	public function getValue( $field ) {
		if ( isset( $this->vars[$field] ) ) {
			return $this->vars[$field];
		}
		return false;
	}

	/**
	 * Category key
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function key() {
		return $this->getValue( 'key' );
	}

	/**
	 * Category name
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function name() {
		return $this->getValue( 'name' );
	}

	/**
	 * Category permalink
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function permalink() {
		return $this->getValue( 'permalink' );
	}

	/**
	 * Category template
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function template() {
		return $this->getValue( 'template' );
	}

	/**
	 * Category description
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function description() {
		return $this->getValue( 'description' );
	}

	/**
	 * Category in pages
	 *
	 * Returns an array with the keys of pages linked to the category.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function pages() {
		return $this->getValue( 'list' );
	}

	/**
	 * JSON data
	 *
	 * Returns an array in json format with all the data of the tag.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $returnsArray
	 * @return mixed
	 */
	public function json( $returnsArray = false ) {
		$tmp['key']         = $this->key();
		$tmp['name']        = $this->name();
		$tmp['description'] = $this->description();
		$tmp['permalink']   = $this->permalink();
		$tmp['pages']       = $this->pages();

		if ( $returnsArray ) {
			return $tmp;
		}
		return json_encode( $tmp );
	}
}
