<?php
/**
 * User login screen
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
<div id="user-login">

	<h1 class="login-heading"><a href="<?php echo site()->url(); ?>"><?php echo site()->title(); ?></a></h1>

	<form method="post" action="" autocomplete="off">
		<input type="hidden" id="jstokenCSRF" name="tokenCSRF" value="<?php echo $security->getTokenCSRF(); ?>" />

		<div id="login-username" class="form-group">
			<input type="text" value="<?php echo ( isset( $_POST['username'] ) ? \Sanitize :: html( $_POST['username'] ) : '' ); ?>" class="form-control form-control-lg" id="jsusername" name="username" placeholder="<?php lang()->p( 'Username' ); ?>" autofocus>
		</div>

		<div id="login-password" class="form-group">
			<input type="password" class="form-control form-control-lg" id="jspassword" name="password" placeholder="<?php lang()->p( 'Password' ); ?>">
		</div>

		<div id="login-remember" class="form-check">
			<label class="form-check-label" for="jsremember"><input class="form-check-input" type="checkbox" value="true" id="jsremember" name="remember" /> <?php lang()->p( 'Remember Me' ); ?></label>
		</div>

		<div id="login-submit" class="form-group login-submit">
			<button type="submit" class="btn btn-primary btn-lg" name="save"><?php lang()->p( 'Login' ); ?></button>
		</div>
	</form>
</div>
