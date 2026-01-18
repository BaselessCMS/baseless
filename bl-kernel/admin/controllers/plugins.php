<?php
/**
 * Plugins management controller
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\Plugins;

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	site
};
use function CMS\Func\{
	check_role
};

check_role( [ 'admin' ] );

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'Plugins' ),
	site()->title()
);
