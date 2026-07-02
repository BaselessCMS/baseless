<?php
/**
 * User login screen
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
<h1 class="login-heading"><a href="<?php echo site()->url(); ?>"><?php echo site()->title(); ?></a></h1>

<form method="post" action="" autocomplete="off">
	<fieldset class="admin-fieldset">
		<input type="hidden" id="jstokenCSRF" name="tokenCSRF" value="<?php echo security()->getTokenCSRF(); ?>" />

		<div id="login-password">
			<label for="jsusername"><?php lang()->p( 'Username' ); ?></label>
			<input type="text" value="<?php echo ( isset( $_POST['username'] ) ? \Sanitize :: html( $_POST['username'] ) : '' ); ?>" id="jsusername" name="username" placeholder="" autofocus>

			<label for="jspassword"><?php lang()->p( 'Password' ); ?></label>
			<input type="password" id="jspassword" name="password" placeholder="">
		</div>

		<div id="login-remember">
			<input type="checkbox" value="true" id="jsremember" name="remember" />
			<label for="jsremember"><?php lang()->p( 'Remember Me' ); ?></label>
		</div>

		<div id="login-submit" class="login-submit">
			<button type="submit" class="button button-primary" name="save"><?php lang()->p( 'Login' ); ?></button>
		</div>
	</fieldset>
</form>
