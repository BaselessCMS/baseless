<?php
/**
 * Plugin form page controller
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

checkRole( [ 'admin' ] );

$plugin = false;
$pluginClassName = $layout['parameters'];

// Check if the plugin exists.
if ( isset( $plugins['all'][$pluginClassName] ) ) {
	$plugin = $plugins['all'][$pluginClassName];
} else {
	\Redirect :: page( 'plugins' );
}

// Check if the plugin has the method form().
if ( ! method_exists( $plugin, 'form' ) ) {
	\Redirect :: page( 'plugins' );
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	// Add to syslog.
	$syslog->add( [
		'dictionaryKey' => 'plugin-configured',
		'notes' => $plugin->name()
	] );

	// Call the method post of the plugin.
	$plugin->post();
	\Alert :: set( $L->g( 'The changes have been saved' ) );
	\Redirect :: page( 'configure-plugin/' . $plugin->className() );
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s - %s | %s',
	lang()->g( 'Plugin' ),
	$plugin->name(),
	site()->title()
);
