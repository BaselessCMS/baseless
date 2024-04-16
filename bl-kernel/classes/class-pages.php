<?php
/**
 * Pages database
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Databases
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Pages extends dbJSON {

	/**
	 * Parent keys
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $parentKeyList = [];

	/**
	 * Database fields
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $dbFields = [
		'title'         => '',
		'description'   => '',
		'username'      => '',
		'tags'          => [],
		'type'          => 'published',
		'date'          => '',
		'dateModified'  => '',
		'position'      => 0,
		'coverImage'    => '',
		'category'      => '',
		'md5file'       => '',
		'uuid'          => '',
		'allowComments' => true,
		'template'      => '',
		'noindex'       => false,
		'nofollow'      => false,
		'noarchive'     => false,
		'custom'        => []
	];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	function __construct() {
		parent :: __construct( DB_PAGES );
	}

	/**
	 * Get default fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function getDefaultFields() {
		return $this->dbFields;
	}

	/**
	 * Get page database
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key The page key.
	 * @return mixed
	 */
	public function getPageDB( $key ) {

		if ( $this->exists( $key ) ) {
			return $this->db[$key];
		}
		return false;
	}

	/**
	 * Page exists
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key The page key.
	 * @return boolean
	 */
	public function exists( $key ) {
		return isset( $this->db[$key] );
	}

	/**
	 * Add new page
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @global object $site The Site class.
	 * @return mixed
	 */
	public function add( $args ) {

		// Access global variables.
		global $site;

		$row = [];

		// Predefined values.
		foreach ( $this->dbFields as $field => $value ) {

			if ( 'tags' == $field ) {
				$tags = '';

				if ( isset( $args['tags'] ) ) {
					$tags = $args['tags'];
				}
				$finalValue = $this->generateTags( $tags );

			} elseif ( 'custom' == $field ) {

				if ( isset( $args['custom'] ) ) {

					$customFields = $site->customFields();
					foreach ( $args['custom'] as $customField => $customValue ) {
						$html = \Sanitize :: html( $customValue );

						// Store the custom field as defined type.
						settype( $html, $customFields[$customField]['type'] );
						$row['custom'][$customField]['value'] = $html;
					}
					unset( $args['custom'] );
					continue;
				}

			// Sanitize if will be stored on database.
			} elseif ( isset( $args[$field] ) ) {
				$finalValue = Sanitize :: html( $args[$field] );

			// Default value for the field if not defined.
			} else {
				$finalValue = $value;
			}
			// Store the value as defined type.
			settype( $finalValue, gettype( $value ) );
			$row[$field] = $finalValue;
		}

		// This variable is not belong to the database so is not defined in $row.
		$contentRaw = ( empty( $args['content'] ) ? '' : $args['content'] );

		// This variable is not belong to the database so is not defined in $row.
		$parent = '';
		if ( ! empty( $args['parent'] ) ) {
			$parent = $args['parent'];
			$row['type'] = $this->db[$parent]['type'];
		}

		// This variable is not belong to the database so is not defined in $row.
		if ( empty( $args['slug'] ) ) {
			if ( ! empty($row['title'] ) ) {
				$slug = $this->generateSlug( $row['title'] );
			} else {
				$slug = $this->generateSlug( $contentRaw );
			}
		} else {
			$slug = $args['slug'];
		}

		// This variable is not belong to the database so is not defined in $row.
		$key = $this->generateKey( $slug, $parent );

		// Generate UUID.
		if ( empty( $row['uuid'] ) ) {
			$row['uuid'] = $this->generateUUID();
		}

		// Validate date.
		if ( ! \Valid :: date( $row['date'], DB_DATE_FORMAT ) ) {
			$row['date'] = \Date :: current( DB_DATE_FORMAT );
		}

		// Schedule page.
		if ( ( $row['date'] > \Date :: current( DB_DATE_FORMAT ) ) && ( 'published' == $row['type'] ) ) {
			$row['type'] = 'scheduled';
		}

		// Create the directory.
		if ( \Filesystem :: mkdir( PATH_PAGES . $key, true ) === false ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to create the directory [' . PATH_PAGES . $key . ']', LOG_TYPE_ERROR );
			return false;
		}

		// Create the index.txt and save the file.
		if ( file_put_contents( PATH_PAGES . $key . DS . FILENAME, $contentRaw ) === false ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to create the content in the file [' . FILENAME . ']', LOG_TYPE_ERROR );
			return false;
		}

		// Checksum MD5
		$row['md5file'] = md5_file( PATH_PAGES . $key . DS . FILENAME );

		// Insert in database.
		$this->db[$key] = $row;

		// Sort database.
		$this->sortBy();

		// Save database.
		$this->save();

		// Create symlink for images directory
		if ( \Filesystem :: mkdir( PATH_UPLOADS_PAGES . $row['uuid'] ) ) {
			\Filesystem :: symlink( PATH_UPLOADS_PAGES . $row['uuid'], PATH_UPLOADS_PAGES . $key );
		}
		return $key;
	}

	/**
	 * Edit page
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @global object $site The Site class.
	 * @return mixed
	 */
	public function edit( $args ) {

		// Access global variables.
		global $site;

		// This is the new row for the table and is going to replace the old row
		$row = [];

		// Current key
		// This variable is not belong to the database so is not defined in $row
		$key = $args['key'];

		// Check values from the arguments ($args)
		// If some field is missing the current value is taken
		foreach ( $this->dbFields as $field => $value ) {

			if ( ( 'tags' == $field ) && isset( $args['tags'] ) ) {
				$finalValue = $this->generateTags( $args['tags'] );

			} elseif ( 'custom' == $field ) {
				if ( isset( $args['custom'] ) ) {

					$customFields = $site->customFields();
					foreach ( $args['custom'] as $customField => $customValue ) {

						$html = Sanitize :: html( $customValue );

						// Store the custom field as defined type.
						settype( $html, $customFields[$customField]['type'] );
						$row['custom'][$customField]['value'] = $html;
					}
					unset( $args['custom'] );
					continue;
				}

			// Sanitize if will be stored on database.
			} elseif ( isset( $args[$field] ) ) {
				$finalValue = \Sanitize :: html( $args[$field] );

			// Default value from the current row.
			} else {
				$finalValue = $this->db[$key][$field];
			}
			settype( $finalValue, gettype( $value ) );
			$row[$field] = $finalValue;
		}

		// This variable is not belong to the database so is not defined in $row.
		$parent = '';
		if ( ! empty( $args['parent'] ) ) {
			$parent = $args['parent'];
			$row['type'] = $this->db[$parent]['type'];
		}

		/**
		 * If the user change the slug the page key changes.
		 * If the user send an empty slug the page key doesn't change.
		 * This variable is not belong to the database so is not defined in $row.
		 */
		if ( empty( $args['slug'] ) ) {
			$explode = explode( '/', $key );
			$slug = end( $explode );
		} else {
			$slug = $args['slug'];
		}

		/**
		 * New key
		 *
		 * The key of the page can change if the user change the slug or the parent
		 * if the user doesn't change the slug or the parent the key is going to be
		 * the same as the current key.
		 *
		 * This variable is not belong to the database so is not defined in $row.
		 */
		$newKey = $this->generateKey( $slug, $parent, false, $key );

		// if the date in the arguments is not valid, take the value from the old row.
		if ( ! \Valid :: date( $row['date'], DB_DATE_FORMAT ) ) {
			$row['date'] = $this->db[$key]['date'];
		}

		// Modified date.
		$row['dateModified'] = \Date :: current( DB_DATE_FORMAT );

		// Schedule page.
		if ( ( $row['date'] > \Date :: current( DB_DATE_FORMAT ) ) && ( 'published' == $row['type'] ) ) {
			$row['type'] = 'scheduled';
		}

		// Move the directory from old key to new key only if the keys are different.
		if ( $newKey !== $key ) {
			if ( \Filesystem :: mv( PATH_PAGES.$key, PATH_PAGES.$newKey ) === false ) {
				\Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to move the directory to ' . PATH_PAGES . $newKey );
				return false;
			}

			// Regenerate the symlink to a proper directory.
			unlink( PATH_UPLOADS_PAGES . $key );
			Filesystem :: symlink( PATH_UPLOADS_PAGES . $row['uuid'], PATH_UPLOADS_PAGES . $newKey );
		}

		// If the content was passed via arguments replace the content.
		if ( isset( $args['content'] ) ) {

			// Make the index.txt and save the file.
			if ( file_put_contents( PATH_PAGES . $newKey . DS . FILENAME, $args['content'] ) === false ) {
				\Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to put the content in the file ' . FILENAME );
				return false;
			}
		}

		// Remove the old row.
		unset( $this->db[$key] );

		// Reindex Orphan Children.
		$this->reindexChildren( $key, $newKey );

		// Checksum MD5.
		$row['md5file'] = md5_file( PATH_PAGES.$newKey.DS.FILENAME );

		// Insert in database the new row.
		$this->db[$newKey] = $row;

		// Sort database.
		$this->sortBy();

		// Save database.
		$this->save();

		return $newKey;
	}

	// This function reindex the orphan children with the new parent key
	// If a page has subpages and the page change his key is necessary check the children key
	/**
	 * Re-index child pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $oldParentKey
	 * @param  string $newParentKey
	 * @return mixed
	 */
	public function reindexChildren( $oldParentKey, $newParentKey ) {

		if ( $oldParentKey == $newParentKey ) {
			return false;
		}
		$tmp = $this->db;
		foreach ( $tmp as $key => $fields ) {

			if ( \Text :: startsWith( $key, $oldParentKey . '/' ) ) {
				$newKey = \Text :: replace( $oldParentKey . '/', $newParentKey . '/', $key );
				$this->db[$newKey] = $this->db[$key];
				unset( $this->db[$key] );
			}
		}
	}

	/**
	 * Delete page
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return boolean
	 */
	public function delete( $key ) {

		/**
		 * This is need it, because if the key is empty the
		 * Filesystem::deleteRecursive is going to delete PATH_PAGES.
		 */
		if ( empty( $key ) ) {
			return false;
		}

		// Page doesn't exist in database.
		if ( ! $this->exists( $key ) ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'The page does not exist. Key: ' . $key );
			return false;
		}

		// Delete directory and files.
		if ( \Filesystem :: deleteRecursive( PATH_PAGES . $key ) === false ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to delete the directory ' . PATH_PAGES . $key, LOG_TYPE_ERROR );
		}

		// Delete page images directory; The function already check if exists the directory.
		if ( \Filesystem :: deleteRecursive( PATH_UPLOADS_PAGES . $key ) === false ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'Directory with images not found ' . PATH_UPLOADS_PAGES . $key );
		}

		// Remove from database.
		unset( $this->db[$key] );

		// Save the database.
		if ( false === $this->save() ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to save the database file.' );
		}
		return true;
	}

	/**
	 * Delete pages by user
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $args
	 * @return boolean
	 */
	public function deletePagesByUser( $args ) {

		$username = $args['username'];

		foreach ( $this->db as $key => $fields ) {
			if ( $fields['username'] === $username ) {
				$this->delete( $key );
			}
		}
		return true;
	}

	/**
	 * Transfer pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $args
	 * @return boolean
	 */
	public function transferPages( $args ) {

		$oldUsername = $args['oldUsername'];
		$newUsername = isset( $args['newUsername'] ) ? $args['newUsername'] : 'admin';

		foreach ( $this->db as $key => $fields ) {
			if ( $fields['username'] === $oldUsername ) {
				$this->db[$key]['username'] = $newUsername;
			}
		}
		return $this->save();
	}

	/**
	 * Set field
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @param  string $field
	 * @param  mixed $value
	 * @return mixed
	 */
	public function setField( $key, $field, $value ) {

		if ( $this->exists( $key ) ) {
			settype( $value, gettype( $this->dbFields[$field] ) );
			$this->db[$key][$field] = $value;
			return $this->save();
		}
		return false;
	}

	/**
	 * Get all pages database
	 *
	 * $onlyKeys = true; Returns only the pages keys.
	 * $onlyKeys = false; Returns part of the database.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $onlyKeys
	 * @return array
	 */
	public function getDB( $onlyKeys = true ) {

		$tmp = $this->db;
		if ( $onlyKeys ) {
			return array_keys( $tmp );
		}
		return $tmp;
	}

	/**
	 * Get published pages database
	 *
	 * $onlyKeys = true; Returns only the pages keys.
	 * $onlyKeys = false; Returns part of the database.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $onlyKeys
	 * @return array
	 */
	public function getPublishedDB( $onlyKeys = true ) {

		$tmp = $this->db;
		foreach ( $tmp as $key => $fields ) {
			if ( $fields['type'] != 'published' ) {
				unset( $tmp[$key] );
			}
		}
		if ( $onlyKeys ) {
			return array_keys( $tmp );
		}
		return $tmp;
	}

	/**
	 * Get static pages database
	 *
	 * By default the static pages are sorted by position.
	 *
	 * $onlyKeys = true; Returns only the pages keys.
	 * $onlyKeys = false; Returns part of the database.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $onlyKeys
	 * @return array
	 */
	public function getStaticDB( $onlyKeys = true ) {

		$tmp = $this->db;
		foreach ( $tmp as $key => $fields ) {
			if ( $fields['type'] != 'static' ) {
				unset( $tmp[$key] );
			}
		}
		uasort( $tmp, [ $this, 'sortByPositionLowToHigh' ] );
		if ( $onlyKeys ) {
			return array_keys( $tmp );
		}
		return $tmp;
	}

	/**
	 * Get draft pages database
	 *
	 * $onlyKeys = true; Returns only the pages keys.
	 * $onlyKeys = false; Returns part of the database.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $onlyKeys
	 * @return array
	 */
	public function getDraftDB( $onlyKeys = true ) {

		$tmp = $this->db;
		foreach ( $tmp as $key => $fields ) {
			if ( $fields['type'] !='draft' ) {
				unset($tmp[$key]);
			}
		}
		if ( $onlyKeys ) {
			return array_keys( $tmp );
		}
		return $tmp;
	}

	/**
	 * Get autosave pages database
	 *
	 * $onlyKeys = true; Returns only the pages keys.
	 * $onlyKeys = false; Returns part of the database.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $onlyKeys
	 * @return array
	 */
	public function getAutosaveDB( $onlyKeys = true ) {

		$tmp = $this->db;
		foreach ( $tmp as $key => $fields ) {
			if ( $fields['type'] != 'autosave' ) {
				unset( $tmp[$key] );
			}
		}
		if ( $onlyKeys ) {
			return array_keys( $tmp );
		}
		return $tmp;
	}

	/**
	 * Get scheduled pages database
	 *
	 * $onlyKeys = true; Returns only the pages keys.
	 * $onlyKeys = false; Returns part of the database.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $onlyKeys
	 * @return array
	 */
	public function getScheduledDB( $onlyKeys = true ) {

		$tmp = $this->db;
		foreach ( $tmp as $key => $fields ) {
			if ( $fields['type'] != 'scheduled' ) {
				unset( $tmp[$key] );
			}
		}
		if ( $onlyKeys ) {
			return array_keys( $tmp );
		}
		return $tmp;
	}

	/**
	 * Get sticky pages database
	 *
	 * $onlyKeys = true; Returns only the pages keys.
	 * $onlyKeys = false; Returns part of the database.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $onlyKeys
	 * @return array
	 */
	public function getStickyDB ($onlyKeys = true ) {

		$tmp = $this->db;
		foreach ( $tmp as $key => $fields ) {
			if ( $fields['type'] != 'sticky' ) {
				unset( $tmp[$key] );
			}
		}
		if ( $onlyKeys ) {
			return array_keys( $tmp );
		}
		return $tmp;
	}

	/**
	 * Get next position
	 *
	 * @since  1.0.0
	 * @access public
	 * @return integer
	 */
	public function nextPositionNumber() {

		$tmp = 1;
		foreach ( $this->db as $key => $fields ) {
			if ( $fields['position'] > $tmp ) {
				$tmp = $fields['position'];
			}
		}
		return ++$tmp;
	}

	/**
	 * Next page key
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $currentKey
	 * @return mixed
	 */
	public function nextPageKey( $currentKey ) {

		if ( 'published' == $this->db[$currentKey]['type'] ) {

			$keys     = array_keys( $this->db );
			$position = array_search( $currentKey, $keys ) - 1;
			if ( isset( $keys[$position] ) ) {
				$nextKey = $keys[$position];
				if ( 'published' == $this->db[$nextKey]['type'] ) {
					return $nextKey;
				}
			}
		}
		return false;
	}

	/**
	 * Previous page key
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $currentKey
	 * @return mixed
	 */
	public function previousPageKey( $currentKey ) {

		if ( 'published' == $this->db[$currentKey]['type'] ) {

			$keys     = array_keys( $this->db );
			$position = array_search( $currentKey, $keys ) + 1;
			if ( isset( $keys[$position] ) ) {
				$prevKey = $keys[$position];
				if ( 'published' == $this->db[$prevKey]['type'] ) {
					return $prevKey;
				}
			}
		}
		return false;
	}

	/**
	 * Get pages list
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $pageNumber
	 * @param  integer $numberOfItems
	 * @param  boolean $published
	 * @param  boolean $static
	 * @param  boolean $sticky
	 * @param  boolean $draft
	 * @param  boolean $scheduled
	 * @return mixed
	 */
	public function getList( $pageNumber, $numberOfItems, $published = true, $static = false, $sticky = false, $draft = false, $scheduled = false ) {

		$list = [];
		foreach ( $this->db as $key => $fields ) {

			if ( $published && 'published' == $fields['type'] ) {
				array_push( $list, $key );
			} elseif ( $static && 'static' == $fields['type'] ) {
				array_push( $list, $key );
			} elseif ( $sticky && 'sticky' == $fields['type'] ) {
				array_push( $list, $key );
			} elseif ( $draft && 'draft' == $fields['type'] ) {
				array_push( $list, $key );
			} elseif ( $scheduled && 'scheduled' == $fields['type'] ) {
				array_push( $list, $key );
			}
		}

		if ( $numberOfItems == -1 ) {
			return $list;
		}

		// The first page number is 1, so the real is 0.
		$realPageNumber = $pageNumber - 1;

		$total = count( $list );
		$init = (int) $numberOfItems * $realPageNumber;
		$end  = (int) min( ( $init + $numberOfItems - 1 ), $total );
		$outrange = $init < 0 ? true : $init > $end;
		if ( ! $outrange ) {
			return array_slice( $list, $init, $numberOfItems, true );
		}
		return false;
	}

	/**
	 * Count pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $onlyPublished
	 * @return integer
	 */
	public function count( $onlyPublished = true ) {

		if ( $onlyPublished ) {
			$db = $this->getPublishedDB( false );
			return count( $db );
		}
		return count( $this->db );
	}

	/**
	 * Get parent pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function getParents() {

		$db = $this->getPublishedDB();
		foreach  ( $db as $key => $pageKey ) {

			// if the key has slash then is a child.
			if ( \Text :: stringContains( $pageKey, '/' ) ) {
				unset( $db[$key] );
			}
		}
		return $db;
	}

	/**
	 * Get page children
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $parentKey
	 * @return array
	 */
	public function getChildren( $parentKey ) {

		$tmp  = $this->db;
		$list = [];
		foreach ( $tmp as $key => $fields ) {
			if ( \Text :: startsWith( $key, $parentKey . '/' ) ) {
				array_push( $list, $key );
			}
		}
		return $list;
	}

	/**
	 * Sort pages by
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function sortBy() {

		if ( 'date' == ORDER_BY ) {
			return $this->sortByDate( true );
		}
		return $this->sortByPosition( false );
	}

	/**
	 * Sort by position
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $HighToLow
	 * @return void
	 */
	public function sortByPosition( $HighToLow = false ) {

		if ( $HighToLow ) {
			uasort( $this->db, [ $this, 'sortByPositionHighToLow' ] );
		} else {
			uasort( $this->db, [ $this, 'sortByPositionLowToHigh' ] );
		}
		return true;
	}

	/**
	 * Sort position low to high
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  null $a
	 * @param  null $b
	 * @return void
	 */
	private function sortByPositionLowToHigh( $a, $b ) {
		return $a['position'] > $b['position'];
	}

	/**
	 * Sort position high to low
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  null $a
	 * @param  null $b
	 * @return void
	 */
	private function sortByPositionHighToLow( $a, $b ) {
		return $a['position'] < $b['position'];
	}

	/**
	 * Sort by date
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $HighToLow
	 * @return boolean
	 */
	public function sortByDate( $HighToLow = true ) {

		if ( $HighToLow ) {
			uasort( $this->db, [ $this, 'sortByDateHighToLow' ] );
		} else {
			uasort( $this->db, [ $this, 'sortByDateLowToHigh' ] );
		}
		return true;
	}

	/**
	 * Sort date low to high
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  null $a
	 * @param  null $b
	 * @return void
	 */
	private function sortByDateLowToHigh( $a, $b ) {
		return $a['date'] > $b['date'];
	}

	/**
	 * Sort date high to low
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  null $a
	 * @param  null $b
	 * @return void
	 */
	private function sortByDateHighToLow( $a, $b ) {
		return $a['date'] < $b['date'];
	}

	/**
	 * Generate UUID
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function generateUUID() {
		return md5( uniqid() . time() );
	}

	/**
	 * Get page UUID
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return mixed
	 */
	public function getUUID( $key ) {

		if ( $this->exists( $key ) ) {
			return $this->db[$key]['uuid'];
		}
		return false;
	}

	/**
	 * Get page by UUID
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $uuid
	 * @return mixed
	 */
	public function getByUUID( $uuid ) {

		foreach ( $this->db as $key => $value ) {
			if ( $value['uuid'] == $uuid ) {
				return $key;
			}
		}
		return false;
	}

	/**
	 * Generate page slug
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $text
	 * @param  integer $truncateLength
	 * @return void
	 */
	private function generateSlug( $text, $truncateLength = 60 ) {

		$tmpslug = \Text :: removeHTMLTags( $text );
		$tmpslug = \Text :: removeLineBreaks( $tmpslug );
		$tmpslug = \Text :: truncate( $tmpslug, $truncateLength, '' );

		return string;
	}

	/**
	 * Page scheduler
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function scheduler() {

		// Get current date.
		$currentDate  = \Date :: current( DB_DATE_FORMAT );
		$saveDatabase = false;

		// The database need to be sorted by date
		foreach ( $this->db as $pageKey => $fields ) {

			if ( 'scheduled' == $fields['type'] ) {
				if ( $fields['date'] <= $currentDate ) {
					$this->db[$pageKey]['type'] = 'published';
					$saveDatabase = true;
				}
			} elseif ( ( 'published' == $fields['type'] ) && ( 'date' == ORDER_BY ) ) {
				break;
			}
		}

		if ( $saveDatabase ) {
			if ( false === $this->save() ) {
				\Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to save the database file.' );
				return false;
			}

			\Log :: set( __METHOD__ . LOG_SEP . 'New pages published from the scheduler.' );
			return true;
		}
		return false;
	}

	/**
	 * Generate page key
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $text
	 * @param  boolean $parent
	 * @param  boolean $returnSlug
	 * @param  string $oldKey
	 * @return string
	 */
	public function generateKey( $text, $parent = false, $returnSlug = false, $oldKey = '' ) {

		// Access global variables.
		global $L, $site;

		if ( \Text :: isEmpty( $text ) ) {
			$text = $L->g( 'empty' );
		}

		if ( \Text :: isEmpty( $parent ) ) {
			$newKey = \Text :: cleanUrl( $text );
		} else {
			$newKey = \Text :: cleanUrl( $parent ) . '/' . \Text :: cleanUrl( $text );
		}

		// cleanURL can return empty string
		if ( \Text :: isEmpty( $newKey ) ) {
			$newKey = $L->g( 'empty' );
		}

		if ( $newKey !== $oldKey ) {

			// Verify if the key is already been used.
			if ( isset( $this->db[$newKey] ) ) {
				$i = 0;
				while ( isset( $this->db[$newKey . '-' . $i] ) ) {
					$i++;
				}
				$newKey = $newKey . '-' . $i;
			}
		}

		if ( $returnSlug ) {

			$explode = explode( '/', $newKey );
			if ( isset( $explode[1] ) ) {
				return $explode[1];
			}
			return $explode[0];
		}
		return $newKey;
	}

	/**
	 * Generate tags
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $tags
	 * @return array
	 */
	public function generateTags( $tags ) {

		$tmp  = [];
		$tags = trim( $tags );
		if ( empty( $tags ) ) {
			return $tmp;
		}

		$tags = explode( ',', $tags );
		foreach ( $tags as $tag ) {

			$tag    = trim( $tag );
			$tagKey = \Text :: cleanUrl( $tag );
			$tmp[$tagKey] = $tag;
		}
		return $tmp;
	}

	/**
	 * Change category
	 *
	 * Changes all pages with the old category key
	 * to the new category key.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $oldCategoryKey
	 * @param  string $newCategoryKey
	 * @return boolean
	 */
	public function changeCategory( $oldCategoryKey, $newCategoryKey ) {

		foreach ( $this->db as $key => $value ) {
			if ( $value['category'] === $oldCategoryKey ) {
				$this->db[$key]['category'] = $newCategoryKey;
			}
		}
		return $this->save();
	}

	/**
	 * Set custom fields
	 *
	 * Insert custom fields to all the pages in the database.
	 *
	 * The structure for the custom fields need to be a valid JSON format.
	 * The custom fields are incremental, this means the custom fields
	 * are never deleted.
	 * The pages only store the value of the custom field,
	 * the structure of the custom fields are in the database site.php.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $fields
	 * @return void
	 */
	public function setCustomFields( $fields ) {

		$customFields = json_decode( $fields, true );
		if ( json_last_error() != JSON_ERROR_NONE ) {
			return false;
		}
		foreach ( $this->db as $pageKey => $pageFields ) {

			foreach ( $customFields as $customField => $customValues ) {

				if ( ! isset( $pageFields['custom'][$customField] ) ) {
					$defaultValue = '';
					if ( isset( $customValues['default'] ) ) {
						$defaultValue = $customValues['default'];
					}
					$this->db[$pageKey]['custom'][$customField]['value'] = $defaultValue;
				}
			}
		}
		return $this->save();
	}
}
