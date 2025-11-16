<?php
/**
 * User object
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class User {

	/**
	 * User variables
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
	 * @param  string $username
	 * @global object $users The Users class.
	 * @return self
	 */
	public function __construct( $username ) {

		// Access global variables.
		global $users;

		$this->vars['username'] = $username;

		if ( false === $username ) {
			$row = $users->getDefaultFields();
		} else {
			if ( \Text :: isEmpty( $username ) || ! $users->exists( $username ) ) {
				$errorMessage = 'User not found in the database by username [' . $username . ']';
				\Log :: set( __METHOD__ . LOG_SEP . $errorMessage );
				throw new \Exception( $errorMessage );
			}
			$row = $users->getUserDB( $username );
		}

		foreach ( $row as $field => $value ) {
			$this->setField( $field, $value );
		}
	}

	public function getValue( $field ) {
		if ( isset( $this->vars[$field] ) ) {
			return $this->vars[$field];
		}
		return false;
	}

	public function setField( $field, $value ) {
		$this->vars[$field] = $value;
		return true;
	}

	public function getDB() {
		return $this->vars;
	}

	public function username() {
		return $this->getValue('username');
	}

	public function description() {
		return $this->getValue('description');
	}

	public function nickname() {
		return $this->getValue('nickname');
	}

	public function firstName() {
		return $this->getValue('firstName');
	}

	public function lastName() {
		return $this->getValue('lastName');
	}

	public function tokenAuth() {
		return $this->getValue('tokenAuth');
	}

	public function role() {
		return $this->getValue('role');
	}

	public function password() {
		return $this->getValue('password');
	}

	public function enabled() {
		$password = $this->getValue('password');
		return $password != '!';
	}

	public function salt() {
		return $this->getValue('salt');
	}

	public function email() {
		return $this->getValue('email');
	}

	public function registered() {
		return $this->getValue('registered');
	}

	public function website() {
		return $this->getValue('website');
	}

	/**
	 * Avatar/Profile picture
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function profilePicture() {

		$filename = $this->getValue( 'username' ) . '.png';
		if ( ! file_exists( PATH_UPLOADS_PROFILES . $filename ) ) {
			return HTML_PATH_CORE_IMG . 'avatars/user.png';
		}
		return DOMAIN_UPLOADS_PROFILES . $filename;
	}

	/**
	 * User data in JSON
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $returnsArray
	 * @return array
	 */
	public function json( $returnsArray = false ) {

		$tmp['username']       = $this->username();
		$tmp['firstName']      = $this->firstName();
		$tmp['lastName']       = $this->lastName();
		$tmp['nickname']       = $this->nickname();
		$tmp['description']    = $this->description();
		$tmp['website']        = $this->website();
		$tmp['profilePicture'] = $this->profilePicture();

		if ( $returnsArray ) {
			return $tmp;
		}
		return json_encode( $tmp );
	}
}
