<?php
/**
 * Plugins positions controller
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Plugins_Pos;

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	site
};
use function CMS\Func\{
	change_plugin_order,
	check_role
};

check_role( [ 'admin' ] );

if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	change_plugin_order( explode( ',', $_POST['plugin-list'] ) );
	\Redirect :: page( 'plugins-position' );
}

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'Plugins Order' ),
	site()->title()
);
