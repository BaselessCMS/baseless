<?php
/**
 * Install plugin controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Install_Plugin;

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

$pluginClassName = $layout['parameters'];
if ( ! activate_plugin( $pluginClassName ) ) {
	\Log :: set( 'Fail when try to activate the plugin.', LOG_TYPE_ERROR );
}

if ( isset( $plugins['all'][$pluginClassName] ) ) {
	$plugin = $plugins['all'][$pluginClassName];
} else {
	\Redirect :: page( 'plugins' );
}

if ( method_exists( $plugin, 'form' ) ) {
	\Redirect :: page( 'configure-plugin/' . $pluginClassName );
}

\Redirect :: page( 'plugins#' . $pluginClassName );
