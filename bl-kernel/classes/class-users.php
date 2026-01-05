<?php
/**
 * Users database
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

class Users extends dbJSON {

	/**
	 * Database fields
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $dbFields = [
		'firstName'     => '',
		'lastName'      => '',
		'nickname'      => '',
		'description'   => '',
		'role'          => 'author', // admin, editor, author
		'password'      => '',
		'salt'          => '!Taxation is theft!',
		'email'         => '',
		'registered'    => '1980-05-09 24:00',
		'tokenRemember' => '',
		'tokenAuth'     => '',
		'tokenAuthTTL'  => '2024-04-03 10:17',
		'website'       => ''
	];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		parent :: __construct( DB_USERS );
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
	 * Get user database
	 *
	 * Return an array with the database of the user,
	 * false otherwise.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $username
	 * @return mixed
	 */
	public function getUserDB( $username ) {
		if ( $this->exists( $username ) ) {
			return $this->db[$username];
		}
		return false;
	}

	/**
	 * User exists
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $username
	 * @return boolean
	 */
	public function exists( $username ) {
		return isset( $this->db[$username] );
	}

	/**
	 * Disable user
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $username
	 * @return void
	 */
	public function disableUser( $username ) {
		$this->db[$username]['password'] = '!';
		return $this->save();
	}

	/**
	 * Add user
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @return boolean
	 */
	public function add( $args ) {

		// The username is store as key and not as field.
		$username = $args['username'];

		// The password is hashed, the password doesn't need to be sanitize in the next step.
		$password = $args['password'];

		$row = [];
		foreach ( $this->dbFields as $field => $value ) {

			if ( isset( $args[$field] ) ) {
				$finalValue = $args[$field];

				// Remove HTML and PHP tags.
				$finalValue = strip_tags( $finalValue );

				// Sanitize if will be stored on database.
				$finalValue = \Sanitize :: html( $finalValue );
			} else {
				// Default value for the field if not defined.
				$finalValue = $value;
			}
			settype( $finalValue, gettype( $value ) );
			$row[$field] = $finalValue;
		}

		$row['registered'] = \Date::current( DB_DATE_FORMAT );
		$row['salt']       = $this->generateSalt();
		$row['password']   = $this->generatePasswordHash( $password, $row['salt'] );
		$row['tokenAuth']  = $this->generateAuthToken();

		// Save the database.
		$this->db[$username] = $row;
		return $this->save();
	}

	/**
	 * Edit user
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @return boolean
	 */
	public function set( $args ) {

		// The username is store as key and not as field.
		$username = $args['username'];

		// Current database of the user.
		$row = $this->db[$username];
		foreach ( $this->dbFields as $field => $value ) {

			if ( 'password' !== $field ) {

				if ( isset( $args[$field] ) ) {

					$finalValue = $args[$field];

					// Remove HTML and PHP tags.
					$finalValue = strip_tags( $finalValue );

					// Sanitize if will be stored on database.
					$finalValue = \Sanitize :: html( $finalValue );
				} else {
					// Default value is the current one.
					$finalValue = $row[$field];
				}
				settype( $finalValue, gettype( $value ) );
				$row[$field] = $finalValue;
			}
		}

		// Set a new password.
		if ( ! empty( $args['password'] ) ) {
			$row['salt']      = $this->generateSalt();
			$row['password']  = $this->generatePasswordHash( $args['password'], $row['salt'] );
			$row['tokenAuth'] = $this->generateAuthToken();
		}

		// Save the database.
		$this->db[$username] = $row;
		return $this->save();
	}

	/**
	 * Delete user
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $username
	 * @return boolean
	 */
	public function delete( $username ) {
		unset( $this->db[$username] );
		return $this->save();
	}

	/**
	 * Generate authentication token
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function generateAuthToken() {
		return md5( uniqid() . time() . DOMAIN );
	}

	/**
	 * Generate remember me token
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function generateRememberToken() {
		return $this->generateAuthToken();
	}

	/**
	 * Generate salt
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function generateSalt() {
		return \Text :: randomText( SALT_LENGTH );
	}

	/**
	 * Generate password hash
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function generatePasswordHash( $password, $salt ) {
		return sha1( $password . $salt );
	}

	/**
	 * Set remember me token
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function setRememberToken( $username, $token ) {
		$args['username']      = $username;
		$args['tokenRemember'] = $token;
		return $this->set( $args );
	}

	/**
	 * Set password
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function setPassword( $args ) {
		return $this->set( $args );
	}

	/**
	 * Get user by email
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $email
	 * @return mixed
	 */
	public function getByEmail( $email ) {

		foreach ( $this->db as $username => $values ) {
			if ( $values['email'] == $email ) {
				return $username;
			}
		}
		return false;
	}

	/**
	 * Get user by authentication token
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $token
	 * @return void
	 */
	public function getByAuthToken( $token ) {

		foreach ( $this->db as $username => $fields ) {
			if ( $fields['tokenAuth'] == $token ) {
				return $username;
			}
		}
		return false;
	}

	/**
	 * Get user by remember me token
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $token
	 * @return void
	 */
	public function getByRememberToken( $token ) {

		foreach ( $this->db as $username => $fields ) {

			if ( ! empty( $fields['tokenRemember'] ) ) {
				if  ($fields['tokenRemember'] == $token ) {
					return $username;
				}
			}
		}
		return false;
	}

	/**
	 * Scrub all remember me tokens
	 *
	 * This function is used if a hacker tries
	 * to use an invalid remember token.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function invalidateAllRememberTokens() {
		foreach ( $this->db as $username=>$values ) {
			$this->db[$username]['tokenRemember'] = '';
		}
		return $this->save();
	}

	/**
	 * Get user keys
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function keys() {
		return array_keys( $this->db );
	}
}
