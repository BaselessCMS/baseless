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
	lang
};
use function CMS\Func\{
	activate_plugin,
	check_role
};

check_role( [ 'admin' ] );

$class = $layout['parameters'];
if ( ! activate_plugin( $class ) ) {
	\Log :: set( lang()->g( 'Failed to activate the plugin.' ), LOG_TYPE_ERROR );
}

if ( isset( $plugins['all'][$class] ) ) {
	$plugin = $plugins['all'][$class];
} else {
	\Redirect :: page( 'plugins' );
}

if ( method_exists( $plugin, 'form' ) ) {
	\Redirect :: page( 'configure-plugin/' . $class );
}

\Redirect :: page( 'plugins#' . $class );
