<?php
/**
 * Header
 *
 * @package    JSON CMS
 * @subpackage Boot
 * @category   Rules
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

header( 'HTTP/1.0 ' . $url->httpCode() . ' ' . $url->httpMessage() );
header( 'X-Powered-By: JSON CMS' );
