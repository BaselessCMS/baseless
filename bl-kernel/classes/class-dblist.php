<?php
/**
 * Database list
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

/*
Database structure:
```
{
	"videos": {
		"name": "Videos",
		"template: "",
		"description: "",
		"list": [ "my-page", "second-page" ]
	},
	"pets": {
		"name": "Pets",
		"template: "",
		"description: "",
		"list": [ "cats-and-dogs" ]
	}
}
```
*/

class dbList extends dbJSON {

	/**
	 * Database content
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $db = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $file The filename.
	 * @global object $categories The Categories class.
	 * @return self
	 */
	public function __construct( $file ) {
		parent :: __construct( $file );
	}

	/**
	 * Database keys
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function keys() {
		return array_keys( $this->db );
	}

	/**
	 * Get database list
	 *
	 * Returns the list of keys filter by pageNumber.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @param  integer $pageNumber Start in 1.
	 * @param  integer $numberOfItems
	 * @return mixed
	 */
	public function getList( $key, $pageNumber, $numberOfItems ) {

		if ( ! isset( $this->db[$key] ) ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'Error key does not exist ' . $key );
			return false;
		}

		// List of keys.
		$list = $this->db[$key]['list'];

		// Returns all the items from the list.
		if ( 1 == $numberOfItems ) {
			return $list;
		}

		// The first page number is 1, so the real is 0.
		$realPageNumber = $pageNumber - 1;
		$chunks = array_chunk( $list, $numberOfItems );
		if ( isset( $chunks[$realPageNumber] ) ) {
			return $chunks[$realPageNumber];
		}

		// Out of index.
		return false;
	}

	/**
	 * Generate key
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @global object $L The Language class.
	 * @return void
	 */
	public function generateKey( $name ) {

		// Access global variables.
		global $L;

		$key = \Text :: cleanUrl( $name );
		if ( \Text :: isEmpty( $key ) ) {
			$key = $L->g( 'empty' );
		}
		while ( isset( $this->db[$key] ) ) {
			$key++;
		}
		return $key;
	}

	/**
	 * Add DB item
	 *
	 * `$args => 'name', 'template', 'description', list'`
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @return string
	 */
	public function add( $args ) {

		$key = $this->generateKey( $args['name'] );

		$this->db[$key]['name'] = strip_tags( $args['name'] );

		$this->db[$key]['template'] = '';
		if ( isset( $args['template'] ) ) {
			$this->db[$key]['template'] = strip_tags( $args['template'] );
		}

		$this->db[$key]['description'] = '';
		if ( isset( $args['description'] ) ) {
			$this->db[$key]['description'] = strip_tags( $args['description'] );
		}

		$this->db[$key]['list'] = [];
		if ( isset( $args['list'] ) ) {
			$this->db[$key]['list'] = $args['list'];
		}

		$this->sortAlphanumeric();
		$this->save();
		return $key;
	}

	/**
	 * Remove DB item
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return string
	 */
	public function remove( $key ) {

		if ( ! isset( $this->db[$key] ) ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'The key does not exist, key: ' . $key );
			return false;
		}
		unset( $this->db[$key] );
		return $this->save();
	}

	// Edit an item to the dblist
	// $args => 'name', 'oldkey', 'newKey', 'template', 'description'

	/**
	 * Edit DB item
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @return mixed
	 */
	public function edit( $args ) {

		if ( isset( $this->db[$args['newKey']] ) && ( $args['newKey'] !== $args['oldKey'] ) ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'The new key already exists. Key: ' . $args['newKey'], LOG_TYPE_WARN );
			return false;
		}

		$this->db[$args['newKey']]['name'] = strip_tags( $args['name'] );

		$this->db[$args['newKey']]['template'] = '';
		if ( isset( $args['template'] ) ) {
			$this->db[$args['newKey']]['template'] = strip_tags( $args['template'] );
		}

		$this->db[$args['newKey']]['description'] = '';
		if ( isset( $args['description'] ) ) {
			$this->db[$args['newKey']]['description'] = strip_tags( $args['description'] );
		}

		$this->db[$args['newKey']]['list'] = $this->db[$args['oldKey']]['list'];

		// Remove the old category.
		if ( $args['oldKey'] !== $args['newKey'] ) {
			unset( $this->db[$args['oldKey']] );
		}

		$this->sortAlphanumeric();
		$this->save();
		return $args['newKey'];
	}

	/**
	 * Sort alphanumerically
	 *
	 * Sort the categories by "Natural order":
	 * a01, a10, b10, c02.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function sortAlphanumeric() {
		return ksort( $this->db );
	}

	/**
	 * Get list name
	 *
	 * Returns the name associated to the key,
	 * false if the key doesn't exist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return mixed
	 */
	public function getName( $key ) {

		if ( isset( $this->db[$key] ) ) {
			return $this->db[$key]['name'];
		}
		return false;
	}

	/**
	 * Get key names
	 *
	 * Returns an array with `'key' => 'name'` of the list.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function getKeyNameArray() {

		$tmp = [];
		foreach( $this->db as $key => $fields ) {
			$tmp[$key] = $fields['name'];
		}
		return $tmp;
	}

	/**
	 * Count items
	 *
	 * Returns the number of items in the list.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return integer
	 */
	public function countItems( $key ) {

		if ( isset( $this->db[$key] ) ) {
			return count( $this->db[$key]['list'] );
		}
		return 0;
	}

	/**
	 * Key exists
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return void
	 */
	public function exists( $key ) {
		return isset( $this->db[$key] );
	}

	/**
	 * Key name exists
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @return boolean
	 */
	public function existsName( $name ) {

		foreach ( $this->db as $key => $fields ) {
			if ( $name == $fields['name'] ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Database array map
	 *
	 * Returns an array with a portion of the database filtered by key.
	 * `[ 'key'=>'', 'name'=>'', 'template'=>'', 'description'=>'', list'=>array() ]`
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return mixed
	 */
	public function getMap( $key ) {

		if ( isset( $this->db[$key] ) ) {
			$tmp = $this->db[$key];
			$tmp['key'] = $key;
			return $tmp;
		}
		return false;
	}
}
