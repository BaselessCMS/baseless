<?php
/**
 * About page controller
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Controllers
 * @since      1.0.0
 */

namespace CMS\Admin\Controllers\About;

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	lang,
	site
};

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	lang()->g( 'About' ),
	site()->title()
);
