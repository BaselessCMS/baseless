<?php
/**
 * User password page
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

$nickname = ucwords( str_replace( [ '-', '_' ], ' ', user()->username() ) );
if ( user()->nickname() ) {
	$nickname = user()->nickname();
}

?>
<form class="tab-content"  id="jsform" method="post" action=""  autocomplete="off">

	<header class="admin-page-header has-actions">

		<h1><?php lang()->p( 'Change Password:' ); ?> <?php echo $nickname; ?></h1>

		<div class="form-actions admin-form-actions">

			<button type="submit" class="btn btn-primary" name="save"><?php lang()->p( 'Save' ); ?></button>

			<a class="btn btn-secondary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'edit-user/' . user()->username() ?>" role="button"><?php lang()->p( 'Cancel' ); ?></a>
		</div>
	</header>

	<fieldset class="admin-fieldset">
		<legend class="screen-reader-text"><?php lang()->p( 'Password Settings' ); ?></legend>

		<input type="hidden" id="jstokenCSRF" name="tokenCSRF" value="<?php echo security()->getTokenCSRF(); ?>" />
		<input type="hidden" id="jsusername" name="username" value="<?php echo user()->username(); ?>" />

		<?php
		echo Bootstrap :: formInputText( [
			'name'        => 'usernameDisabled',
			'label'       => lang()->g( 'Username' ),
			'value'       => user()->username(),
			'class'       => '',
			'placeholder' => '',
			'disabled'    => true,
			'tip'         => ''
		] );

		echo Bootstrap :: formInputText( [
			'name'        => 'newPassword',
			'label'       => lang()->g( 'New Password' ),
			'type'        => 'password',
			'value'       => '',
			'class'       => '',
			'placeholder' => '',
			'tip'         => ''
		] );

		echo Bootstrap :: formInputText( [
			'name'        => 'confirmPassword',
			'label'       => lang()->g( 'Confirm Password' ),
			'type'        => 'password',
			'value'       => '',
			'class'       => '',
			'placeholder' => '',
			'tip'         => ''
		] );
		?>
	</fieldset>
</form>
