<?php
/**
 * TCP helpers
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

class TCP {

	/**
	 * HTTP
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $url
	 * @param  string $method
	 * @param  boolean $verifySSL
	 * @param  integer $timeOut
	 * @param  boolean $followRedirections
	 * @param  boolean $binary
	 * @param  boolean $headers
	 * @return string
	 */
	public static function http( $url, $method = 'GET', $verifySSL = true, $timeOut = 10, $followRedirections = true, $binary = true, $headers = false ) {

		if ( function_exists( 'curl_version' ) ) {

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_HEADER, $headers );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, $followRedirections );
			curl_setopt( $ch, CURLOPT_BINARYTRANSFER, $binary );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $verifySSL );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeOut );
			curl_setopt( $ch, CURLOPT_TIMEOUT, $timeOut );

			if ( 'POST' == $method ) {
				curl_setopt( $ch, CURLOPT_POST, true );
			}
			$output = curl_exec( $ch );
			if ( false == $output ) {
				\Log :: set( 'Curl error: ' . curl_error( $ch ) );
			}
			curl_close( $ch );
		} else {
			$options = [
				'http' => [
					'method'          => $method,
					'timeout'         => $timeOut,
					'follow_location' => $followRedirections
				],
				'ssl' => [
					'verify_peer'      => false,
					'verify_peer_name' => false
				]
			];
			$stream = stream_context_create( $options );
			$output = file_get_contents( $url, false, $stream );
		}
		return $output;
	}

	/**
	 * Download
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $url
	 * @param  string $destination
	 * @return void
	 */
	public static function download( $url, $destination ) {

		$data = self :: http( $url, $method = 'GET', $verifySSL = true, $timeOut = 30, $followRedirections = true, $binary = true, $headers = false );

		return file_put_contents( $destination, $data );
	}

	/**
	 * Get IP
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return void
	 */
	public static function getIP() {

		if ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$ip = getenv( 'HTTP_CLIENT_IP' );
		} elseif ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$ip = getenv( 'HTTP_X_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_X_FORWARDED' ) ) {
			$ip = getenv( 'HTTP_X_FORWARDED' );
		} elseif ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
			$ip = getenv( 'HTTP_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_FORWARDED' ) ) {
			$ip = getenv( 'HTTP_FORWARDED' );
		} elseif ( getenv( 'REMOTE_ADDR' ) ) {
			$ip = getenv( 'REMOTE_ADDR' );
		} else {
			return false;
		}
		return $ip;
	}
}
