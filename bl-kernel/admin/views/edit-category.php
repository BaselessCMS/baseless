<?php
/**
 * Edit category page
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
<form id="jsform" method="post" action="" autocomplete="off">

	<header class="admin-page-header has-actions">

		<h1><?php lang()->p( 'Edit Category' ); ?></h1>

		<div class="form-actions admin-form-actions">

			<button type="submit" class="btn btn-primary" name="save"><?php lang()->p( 'Save' ); ?></button>

			<a class="btn btn-secondary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'dashboard'; ?>" role="button"><?php lang()->p( 'Cancel' ); ?></a>

			<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#jsdeleteModal"><?php lang()->p( 'Delete' ) ?></button>
		</div>
	</header>

	<input type="hidden" id="jstokenCSRF" name="tokenCSRF" value="<?php echo $security->getTokenCSRF(); ?>" />
	<input type="hidden" id="jsaction" name="action" value="edit" />
	<input type="hidden" id="jsoldKey" name="oldKey" value="<?php echo $categoryMap['key']; ?>" />

	<fieldset class="admin-fieldset">
		<legend class="screen-reader-text"><?php lang()->p( 'Category Settings' ); ?></legend>

		<?php
		echo Bootstrap :: formInputText( [
			'name'        => 'name',
			'label'       => lang()->g( 'Name' ),
			'value'       => $categoryMap['name'],
			'class'       => '',
			'placeholder' => '',
			'tip'         => ''
		] );

		echo Bootstrap :: formTextarea( [
			'name'        => 'description',
			'label'       => lang()->g( 'Description' ),
			'value'       => isset( $categoryMap['description'] ) ? $categoryMap['description'] : '',
			'class'       => '',
			'placeholder' => '',
			'tip'         => '',
			'rows'        => 3
		] );

		echo Bootstrap :: formInputText( [
			'name'        => 'template',
			'label'       => lang()->g( 'Template' ),
			'value'       => isset( $categoryMap['template'] ) ? $categoryMap['template'] : '',
			'class'       => '',
			'placeholder' => '',
			'tip'         => ''
		] );

		echo Bootstrap :: formInputText( [
			'name'        => 'newKey',
			'label'       => lang()->g( 'Friendly URL' ),
			'value'       => $categoryMap['key'],
			'class'       => '',
			'placeholder' => '',
			'tip'         => DOMAIN_CATEGORIES . $categoryMap['key']
		] );
		?>
	</fieldset>
</form>

<!-- Modal for delete category -->
<?php
	echo Bootstrap :: modal( [
		'buttonPrimary'        => lang()->g( 'Delete' ),
		'buttonPrimaryClass'   => 'btn-danger jsbuttonDeleteAccept',
		'buttonSecondary'      => lang()->g( 'Cancel' ),
		'buttonSecondaryClass' => 'btn-link',
		'modalTitle'           => lang()->g( 'Delete category' ),
		'modalText'            => lang()->g( 'Are you sure you want to delete this category?' ),
		'modalId'              => 'jsdeleteModal'
	] );
?>
<script>
$(document).ready( function() {

	// Delete content.
	$( '.jsbuttonDeleteAccept' ).on( 'click', function() {
		$( '#jsaction' ).val( 'delete' );
		$( '#jsform' ).submit();
	});
});
</script>
