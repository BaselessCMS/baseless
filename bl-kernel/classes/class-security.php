<?php
/**
 * Database security
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

class Security extends dbJSON {

	/**
	 * Database fields
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $dbFields = [
		'minutesBlocked'        => 5,
		'numberFailuresAllowed' => 10,
		'blackList'             => []
	];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	function __construct() {
		parent :: __construct( DB_SECURITY );
	}

	/**
	 * Generate token
	 *
	 * Saves the token in Session.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function generateTokenCSRF() {

		$token = sha1( uniqid() . time() );
		\Session :: set( 'tokenCSRF', $token );
		\Log :: set( __METHOD__ . LOG_SEP . 'New Token CSRF [' . $token . ']' );
	}

	/**
	 * Validate the token
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $token
	 * @return boolean
	 */
	public function validateTokenCSRF($token) {
		$sessionToken = $this->getTokenCSRF();
		return ( ! empty( $sessionToken ) && ( $sessionToken === $token ) );
	}

	/**
	 * Get the token
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function getTokenCSRF() {
		return \Session :: get( 'tokenCSRF' );
	}

	/**
	 * Token blocked
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function isBlocked() {

		$ip = $this->getUserIp();

		if ( ! isset( $this->db['blackList'][$ip] ) ) {
			return false;
		}

		$currentTime    = time();
		$userBlack      = $this->db['blackList'][$ip];
		$numberFailures = $userBlack['numberFailures'];
		$lastFailure    = $userBlack['lastFailure'];

		// Check if the IP is expired, then is not blocked.
		if ( $currentTime > $lastFailure + ( $this->db['minutesBlocked'] * 60 ) ) {
			return false;
		}

		// The IP has more failures than number of failures, then the IP is blocked.
		if ( $numberFailures >= $this->db['numberFailuresAllowed'] ) {
			\Log :: set( __METHOD__ . LOG_SEP . 'IP Blocked:' . $ip );
			return true;
		}

		// Otherwise the IP is not blocked.
		return false;
	}

	/**
	 * Add to blacklist
	 *
	 * Add or update the current client IP on the blacklist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function addToBlacklist() {

		$ip = $this->getUserIp();
		$currentTime    = time();
		$numberFailures = 1;

		if ( isset( $this->db['blackList'][$ip] ) ) {
			$userBlack   = $this->db['blackList'][$ip];
			$lastFailure = $userBlack['lastFailure'];

			// Check if the IP is expired, then renew the number of failures.
			if ( $currentTime <= $lastFailure + ( $this->db['minutesBlocked'] * 60 ) ) {
				$numberFailures = $userBlack['numberFailures'];
				$numberFailures = $numberFailures + 1;
			}
		}

		$this->db['blackList'][$ip] = [
			'lastFailure'    => $currentTime,
			'numberFailures' => $numberFailures
		];
		\Log :: set( __METHOD__ . LOG_SEP . 'Blacklist, IP:' . $ip . ', Number of failures:' . $numberFailures );
		return $this->save();
	}

	/**
	 * Number of failures
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $ip
	 * @return integer
	 */
	public function getNumberFailures( $ip = null ) {

		if ( empty( $ip ) ) {
			$ip = $this->getUserIp();
		}

		if ( isset( $this->db['blackList'][$ip] ) ) {
			$userBlack = $this->db['blackList'][$ip];
			return $userBlack['numberFailures'];
		}
	}

	/**
	 * Get user IP address
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function getUserIp() {
		return getenv( 'REMOTE_ADDR' );
	}
}
