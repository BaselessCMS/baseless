<?php
/**
 * Uninstall plugin controller
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Uninstall_Plugin;

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Func\{
	check_role,
	deactivate_plugin
};

check_role( [ 'admin' ] );

$pluginClassName = $layout['parameters'];
deactivate_plugin( $pluginClassName );

\Redirect :: page( 'plugins' );
