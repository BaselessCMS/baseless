<?php
/**
 * Plugin form page controller
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Plugin_Settings;

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	site,
	syslog
};
use function CMS\Func\{
	check_role
};

check_role( [ 'admin' ] );

$plugin = false;
$class  = $layout['parameters'];

// Check if the plugin exists.
if ( isset( $plugins['all'][$class] ) ) {
	$plugin = $plugins['all'][$class];
} else {
	\Redirect :: page( 'plugins' );
}

// Check if the plugin has the method form().
if ( ! method_exists( $plugin, 'form' ) ) {
	\Redirect :: page( 'plugins' );
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	// Add to syslog.
	syslog()->add( [
		'dictionaryKey' => 'plugin-configured',
		'notes' => $plugin->name()
	] );

	// Call the method post of the plugin.
	$plugin->post();
	\Alert :: set( lang()->g( 'The changes have been saved' ) );
	\Redirect :: page( 'configure-plugin/' . $plugin->className() );
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s - %s | %s',
	lang()->g( 'Plugin' ),
	$plugin->name(),
	site()->title()
);
