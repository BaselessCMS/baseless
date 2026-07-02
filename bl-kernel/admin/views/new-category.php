<?php
/**
 * New category page
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Views
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	site,
	security,
	login,
	url,
	lang,
	user,
	users,
	plugins,
	page,
	pages,
	cats
};

?>

<form class="tab-content"  id="jsform" method="post" action=""  autocomplete="off">

	<header class="admin-page-header has-actions">

		<h1><?php lang()->p( 'New Category' ); ?></h1>

		<div class="form-actions admin-form-actions">

			<button type="submit" class="btn btn-primary" name="save"><?php lang()->p( 'Save' ); ?></button>

			<a class="btn btn-secondary" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>" role="button"><?php lang()->p( 'Cancel' ); ?></a>
		</div>
	</header>

	<fieldset class="admin-fieldset">
		<legend class="screen-reader-text"><?php lang()->p( 'Category Fields' ); ?></legend>

		<?php
		echo Bootstrap :: formInputHidden( [
			'name'  => 'tokenCSRF',
			'value' => security()->getTokenCSRF()
		] );

		echo Bootstrap :: formInputText( [
			'name'        => 'name',
			'label'       => lang()->g( 'Name' ),
			'value'       => isset( $_POST['category'] ) ? $_POST['category'] : '',
			'class'       => '',
			'placeholder' => '',
			'tip'         => ''
		] );

		echo Bootstrap :: formTextarea( [
			'name'        => 'description',
			'label'       => lang()->g( 'Description' ),
			'value'       => isset( $_POST['description'] ) ? $_POST['description'] : '',
			'class'       => '',
			'placeholder' => '',
			'tip'         => '',
			'rows'        => 3
		] );
		?>
	</fieldset>
</form>
