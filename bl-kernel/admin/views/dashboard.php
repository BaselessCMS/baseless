<?php
/**
 * Dashboard/Index page
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Views
 * @since      1.0.0
 */

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

?>
<header class="admin-page-header">
	<h1><?php lang()->p( 'Dashboard' ); ?></h1>
</header>
<div id="dashboard">
	<?php Theme :: plugins( 'dashboard' ); ?>
</div>
