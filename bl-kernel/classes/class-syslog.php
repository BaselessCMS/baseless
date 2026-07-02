<?php
/**
 * System log
 *
 * @package    Baseless
 * @subpackage Classes
 * @category   Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Syslog extends dbJSON {

	/**
	 * Database fields
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $dbFields = [
		'date' => [
			'inFile' => false,
			'value'  => ''
		],
		'dictionaryKey' => [
			'inFile' => false,
			'value'  => ''
		],
		'notes' => [
			'inFile' => false,
			'value'  => ''
		],
		'username' => [
			'inFile'=>false,
			'value'=>''
		],
		'idExecution' => [
			'inFile' => false,
			'value'  => ''
		],
		'method' => [
			'inFile' => false,
			'value'  => ''
		]
	];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		parent :: __construct( DB_SYSLOG );
	}

	/**
	 * Execution exists
	 *
	 * Returns true if the ID of execution exists,
	 * false otherwise.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $idExecution
	 * @return boolean
	 */
	public function exists( $idExecution ) {

		foreach ( $this->db as $field ) {
			if ( $field['idExecution'] == $idExecution ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get field
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $idExecution
	 * @return mixed
	 */
	public function get( $idExecution ) {
		foreach ( $this->db as $field ) {
			if ( $field['idExecution'] == $idExecution ) {
				return $field;
			}
		}
		return false;
	}

	/**
	 * Add to log
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @global object $L The Language class.
	 * @return boolean
	 */
	public function add( $args) {

		// Access global variables.
		global $L;

		$data = [];
		$data['date'] = \Date :: current( DB_DATE_FORMAT );
		$data['dictionaryKey'] = $args['dictionaryKey'];
		$data['notes'] = \Sanitize :: html( $args['notes'] );

		// Unique ID for each execution, defined in boot/init.php.
		$data['idExecution'] = $GLOBALS['ID_EXECUTION'];
		$data['method']      = $_SERVER['REQUEST_METHOD'];

		// Username
		$data['username'] = \Session :: get( 'username' );
		if ( \Text :: isEmpty( $data['username'] ) ) {
			return false;
		}

		// Insert at beginning of the database.
		array_unshift( $this->db, $data );

		// Keep just NOTIFICATIONS_AMOUNT notifications.
		$this->db = array_slice( $this->db, 0, NOTIFICATIONS_AMOUNT );

		return $this->save();
	}
}
