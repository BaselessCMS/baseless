<?php
/**
 * Configure plugin page
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

// Access global variables.
global $plugin;

?>
<form id="jsform" class="configure-plugin" method="post" action="" autocomplete="off">
	<header class="admin-page-header has-actions">
		<h1><?php echo $plugin->name(); ?></h1>

		<?php if ( $plugin->formButtons() ) : ?>
		<div class="form-actions admin-form-actions">
			<button type="submit" class="btn btn-primary" name="save"><?php lang()->p( 'Save' ); ?></button>
			<a class="btn btn-secondary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'plugins' ?>" role="button"><?php lang()->p( 'Plugins' ); ?></a>
		</div>
		<?php endif; ?>
	</header>

	<input type="hidden" id="jstokenCSRF" name="tokenCSRF" value="<?php echo security()->getTokenCSRF(); ?>">
	<?php if ( method_exists( $plugin->className(), 'form' ) ) {
		echo $plugin->form();
	} ?>
</form>

<script>
$(document).ready( function() {

	// Prevent the form submit when press enter key.
	$( 'form' ).keypress( function(e) {
		if ( ( e.which == 13 ) && ( e.target.type !== 'textarea' ) ) {
			return false;
		}
	});
});
</script>
