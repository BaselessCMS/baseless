<?php
/**
 * JSON database
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

class dbJSON {

	/**
	 * Database file
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $file;

	/**
	 * Database content
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $db;

	/**
	 * Database backup
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $dbBackup;

	/**
	 * Security line
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $security;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $file The JSON file.
	 * @param  boolean $security TRUE if you want to remove the first line.
	 * @global object $categories The Categories class.
	 * @return self
	 */
	function __construct( $file, $security = true ) {

		$this->file      = $file;
		$this->db        = [];
		$this->dbBackup  = [];
		$this->security = $security;

		if ( file_exists( $file ) ) {

			// Read JSON file.
			$lines = file( $file );

			// Remove the first line, the first line is for security reasons.
			if ( $security ) {
				unset( $lines[0] );
			}

			// Regenerate the JSON file.
			$implode = implode( $lines );

			// Unserialize JSON to array.
			$array = $this->unserialize( $implode );
			if ( empty( $array ) ) {
				$this->db = [];
				$this->dbBackup = [];
			} else {
				$this->db = $array;
				$this->dbBackup = $array;
			}
		}
	}

	/**
	 * Restore the database
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function restoreDB() {
		$this->db = $this->dbBackup;
		return true;
	}

	/**
	 * Count rows
	 *
	 * Returns the number of rows in the database.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return integer
	 */
	public function count() {
		return count( $this->db );
	}

	/**
	 * Get field
	 *
	 * Returns the value from the field.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field Field name.
	 * @return mixed
	 */
	public function getField( $field ) {

		if ( isset( $this->db[$field] ) ) {
			return $this->db[$field];
		}
		return $this->dbFields[$field];
	}

	/**
	 * Save database
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function save() {

		$data = '';
		if ( $this->security ) {
			$data  = "<?php defined( 'Baseless' ) or die( 'You are not allowed to access this file directly.' ); ?>" . PHP_EOL;
		}

		// Serialize database.
		$data .= $this->serialize( $this->db );

		// Backup the new database.
		$this->dbBackup = $this->db;

		// LOCK_EX flag to prevent anyone else writing to the file at the same time.
		if ( file_put_contents( $this->file, $data, LOCK_EX ) ) {
			return true;
		} else {
			\Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to save the database file.', LOG_TYPE_ERROR );
			return false;
		}
	}

	/**
	 * Serialize
	 *
	 * Returns a JSON encoded string on success or FALSE on failure.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  array $data
	 * @return string
	 */
	private function serialize( $data ) {

		if ( DEBUG_MODE ) {
			return json_encode( $data, JSON_PRETTY_PRINT );
		}
		return json_encode( $data );
	}

	/**
	 * Unserialize
	 *
	 * Returns the value encoded in json in appropriate PHP type.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  array $data
	 * @return mixed
	 */
	private function unserialize( $data ) {

		// NULL is returned if the json cannot be decoded.
		$decode = json_decode( $data, true );
		if ( null == $decode ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'Error trying to read the JSON file: ' . $this->file, LOG_TYPE_ERROR );
			return false;
		}
		return $decode;
	}

	/**
	 * Get the database
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function getDB() {
		return $this->db;
	}

	/**
	 * Truncate database rows
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function truncate() {
		$this->db = [];
		return $this->save();
	}
}
