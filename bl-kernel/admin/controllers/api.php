<?php
/**
 * Plugin API
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	site,
	security,
	url,
	lang,
	users,
	plugins,
	page,
	pages,
	cats
};

check_role( [ 'admin' ] );

activate_plugin( 'pluginAPI' );

$apiURL    = DOMAIN_BASE . 'api/';
$pluginAPI = get_plugin ( 'pluginAPI' );
$apiToken  = $pluginAPI->getToken();
$username  = $login->username();
$admin     = new \User( $username );
$authToken = $admin->tokenAuth();
$output    = [
	'apiURL'    => $apiURL,
	'username'  => $username,
	'apiToken'  => $apiToken,
	'authToken' => $authToken
];
exit( json_encode( $output ) );
