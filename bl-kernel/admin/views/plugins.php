<?php
/**
 * Plugins page
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
<header class="admin-page-header has-actions">

	<h1><?php lang()->p( 'Plugins' ); ?></h1>

	<div class="form-actions admin-form-actions">
		<a class="btn btn-primary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'plugins-position' ?>" role="button"><?php lang()->p( 'Sort Sidebar' ); ?></a>
	</div>
</header>

<fieldset>
	<label for="search" class="form-label"><?php lang()->p( 'Search Plugins' ); ?></label>
	<input type="text" class="form-control" id="search" placeholder="<?php lang()->p( 'Search plugins by name or keywords in plugin description&hellip;' ); ?>" />

	<script>
		$(document).ready( function() {
			$( '#search' ).on( 'keyup', function() {
				var textToSearch = $(this).val().toLowerCase();
				$( '.searchItem' ).each( function() {
					var item = $(this);
					item.hide();
					item.find( '.searchText' ).each( function() {
						var element = $(this).text().toLowerCase();
						if ( element.indexOf( textToSearch) != -1 ) {
							item.show();
						}
					});
				});
			});
		});
	</script>
</fieldset>

<h2><?php lang()->p( 'Enabled Plugins' ); ?></h2>

<table class="table">
	<tbody
	<?php
	foreach ( $plugins_installed as $plugin ) :

		// Do not display theme's plugins.
		if ( 'theme' == $plugin->type() ) {
			continue;
		}

		?>
		<tr id="<?php echo $plugin->className(); ?>" class="bg-light searchItem">
			<td class="align-middle pt-3 pb-3 w-25">
				<div class="searchText"><?php echo $plugin->name(); ?></div>
				<div class="mt-1">

					<?php if ( method_exists( $plugin, 'form' ) ) : ?>
					<a class="mr-3" href="<?php echo HTML_PATH_ADMIN_ROOT . 'configure-plugin/' . $plugin->className(); ?>"><?php lang()->p( 'Settings' ); ?></a>
					<?php endif; ?>

					<a href="<?php echo HTML_PATH_ADMIN_ROOT . 'uninstall-plugin/' . $plugin->className(); ?>"><span class="text-danger"><?php lang()->p( 'Deactivate' ); ?></span></a>
				</div>
			</td>
			<td class="searchText align-middle d-none d-sm-table-cell">
				<?php echo $plugin->description(); ?>
			</td>
			<td class="text-center align-middle d-none d-lg-table-cell">
				<span><?php echo $plugin->version(); ?></span>
			</td>
			<td class="text-center align-middle d-none d-lg-table-cell">
				<a target="_blank" rel="noopener noreferrer" href="<?php echo $plugin->website(); ?>"><?php echo $plugin->author(); ?></a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<h2><?php lang()->p( 'Disabled Plugins' ); ?></h2>

<table class="table">
	<tbody>
	<?php
	$pluginsNotInstalled = array_diff_key( $plugins['all'], $plugins_installed );
	foreach ( $pluginsNotInstalled as $plugin ) :

		// Do not display theme's plugins.
		if ( 'theme' == $plugin->type() ) {
			continue;
		}

		?>
		<tr id="<?php echo $plugin->className(); ?>" class="searchItem">
			<td class="align-middle pt-3 pb-3 w-25">
				<div class="searchText"><?php echo $plugin->name(); ?></div>
				<div class="mt-1">
					<a href="<?php echo HTML_PATH_ADMIN_ROOT . 'install-plugin/' . $plugin->className(); ?>"><span class="text-success"><?php lang()->p( 'Activate' ); ?></span></a>
				</div>
			</td>
			<td class="searchText align-middle d-none d-sm-table-cell">
				<?php echo $plugin->description(); ?>
			</td>
			<td class="text-center align-middle d-none d-lg-table-cell">
				<span><?php echo $plugin->version(); ?></span>
			</td>
			<td class="text-center align-middle d-none d-lg-table-cell">
				<a target="_blank" rel="noopener noreferrer" href="<?php echo $plugin->website(); ?>"><?php echo $plugin->author(); ?></a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
