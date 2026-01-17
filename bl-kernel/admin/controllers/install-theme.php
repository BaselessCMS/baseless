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

$themeDirectory = $layout['parameters'];

// Activate theme.
activateTheme( $themeDirectory );

// Redirect.
\Redirect :: page( 'themes' );
