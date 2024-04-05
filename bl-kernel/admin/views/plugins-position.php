<?php
/**
 * Plugins positions page
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

?>

<form class="tab-content"  id="jsform" method="post" action=""  autocomplete="off">

	<header class="admin-page-header has-actions">

		<h1><?php lang()->p( 'Plugins Positions' ); ?></h1>

		<div class="form-actions admin-form-actions">

			<button type="button" class="btn btn-primary jsbuttonSave" name="save"><?php lang()->p( 'Save' ); ?></button>

			<a class="btn btn-secondary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'plugins'; ?>" role="button"><?php lang()->p( 'Cancel' ); ?></a>
		</div>
	</header>

	<div class="alert alert-primary"><?php lang()->p( 'Drag and drop to sort the plugins' ); ?></div>

	<input type="hidden" id="jstokenCSRF" name="tokenCSRF" value="<?php echo $security->getTokenCSRF(); ?>" />
	<input type="hidden" id="jsplugin-list" name="plugin-list" value="" />

	<ul class="list-group list-group-sortable">
	<?php
	foreach ( $plugins['siteSidebar'] as $sortable ) {
		printf(
			'<li class="list-group-item" data-plugin="%s"><span class="fa fa-arrows-v"></span> %s</li>',
			$sortable->className(),
			$sortable->name()
		);
	} ?>
	</ul>
</form>

<script>
$(document).ready( function() {
	$( '.list-group-sortable' ).sortable( {
		placeholderClass : 'list-group-item'
	});

	$( '.jsbuttonSave' ).on( 'click', function() {
		var tmp = [];
		$( 'li.list-group-item' ).each( function() {
			tmp.push( $(this).attr( 'data-plugin' ) );
		});
		$( '#jsplugin-list' ).attr( 'value', tmp.join( ',' ) );
		$( '#jsform' ).submit();
	});
});
</script>
