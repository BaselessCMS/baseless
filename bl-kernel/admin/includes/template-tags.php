<?php
/**
 * Admin template tags
 *
 * Functions for use in admin page and partial templates.
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Core
 * @since      1.0.0
 */

namespace CMS\Admin_Tags;

/**
 * Admin page content
 *
 * Gets the relevant page template.
 *
 * @since  1.0.0
 * @global object $L The Language class.
 * @return void
 */
function admin_content() {

	// Access global variables.
	global $layout, $scheduled, $syslog, $themes;

	if ( \Sanitize :: pathFile( PATH_ADMIN_VIEWS, $layout['view'] . '.php' ) ) {
		include( PATH_ADMIN_VIEWS . $layout['view'] . '.php' );
	} elseif ( $layout['plugin'] && method_exists( $layout['plugin'], 'adminView' ) ) {
		echo $layout['plugin']->adminView();
	} else {
		printf(
			'<h1>%s</h1>',
			$L->get( '404 Error: Page Not Found' )
		);
		printf(
			'<p>%s</p>',
			$L->get( 'Try looking for a link in the admin menu.' )
		);
	}
}
