<?php
/**
 * New user page
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

		<h1><?php lang()->p( 'New User' ); ?></h1>

		<div class="form-actions admin-form-actions">

			<button type="submit" class="btn btn-primary" name="save"><?php lang()->p( 'Save' ); ?></button>

			<a class="btn btn-secondary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'users'; ?>" role="button"><?php lang()->p( 'Cancel' ); ?></a>
		</div>
	</header>

	<fieldset class="admin-fieldset">
		<legend class="screen-reader-text"><?php lang()->p( 'User Fields' ); ?></legend>

		<input type="hidden" id="jstokenCSRF" name="tokenCSRF" value="<?php echo security()->getTokenCSRF(); ?>" />

		<?php
		echo Bootstrap :: formInputText( [
			'name'        => 'new_username',
			'label'       => lang()->g( 'Username' ),
			'value'       => ( isset( $_POST['new_username'] ) ? $_POST['new_username'] : '' ),
			'class'       => '',
			'placeholder' => '',
			'tip'         => ''
		] );

		echo Bootstrap :: formInputText( [
			'name'        => 'new_password',
			'type'        => 'password',
			'label'       => lang()->g( 'Password' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => '',
			'tip'         => ''
		] );

		echo Bootstrap :: formInputText( [
			'name'        => 'confirm_password',
			'type'        => 'password',
			'label'       => lang()->g( 'Confirm Password' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => '',
			'tip'         => ''
		] );

		echo Bootstrap :: formSelect( [
			'name'     => 'role',
			'label'    => lang()->g( 'Role' ),
			'options'  => [
				'author' => lang()->g( 'Author' ),
				'editor' => lang()->g( 'Editor' ),
				'admin'  => lang()->g( 'Administrator' )
			],
			'selected' => 'Author',
			'class'    => '',
			'tip'      => lang()->g( 'author-can-write-and-edit-their-own-content' )
		] );

		echo Bootstrap :: formInputText( [
			'name'        => 'email',
			'label'       => lang()->g( 'Email' ),
			'value'       => ( isset( $_POST['email'] ) ? $_POST['email'] : '' ),
			'class'       => '',
			'placeholder' => '',
			'tip'         => ''
		] );
	?>
	</fieldset>
</form>
