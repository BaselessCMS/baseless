<?php
/**
 * Install theme controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Install_Theme;

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Func\{
	activate_theme,
	check_role
};

check_role( [ 'admin' ] );

$themeDirectory = $layout['parameters'];

// Activate theme.
activate_theme( $themeDirectory );

// Redirect.
\Redirect :: page( 'themes' );
