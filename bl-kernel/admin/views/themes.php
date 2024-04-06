<?php
/**
 * Themes page
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

$settings_url = '';
foreach ( buildThemes() as $theme ) {
	if (
		$theme['dirname'] == $site->theme() &&
		isset( $theme['plugin'] ) &&
		getPlugin( $theme['plugin'] )
	) {
		$settings_url = HTML_PATH_ADMIN_ROOT . 'configure-plugin/' . $theme['plugin'];
		break;
	}
}

?>
<header class="admin-page-header has-actions">

	<h1><?php lang()->p( 'Themes' ); ?></h1>

	<?php if ( ! empty( $settings_url ) ) : ?>
	<div class="form-actions admin-form-actions">
		<a class="btn btn-primary" href="<?php echo $settings_url; ?>" role="button"><?php lang()->p( 'Theme Options' ); ?></a>
	</div>
	<?php endif; ?>
</header>

<table class="table mt-3">
	<thead>
		<tr>
			<th class="border-bottom-0 w-25" scope="col"><?php $L->p( 'Name' ); ?></th>

			<th class="border-bottom-0 d-none d-sm-table-cell" scope="col"><?php $L->p( 'Description' ); ?></th>

			<th class="text-center border-bottom-0 d-none d-lg-table-cell" scope="col"><?php $L->p( 'Version' ); ?></th>

			<th class="text-center border-bottom-0 d-none d-lg-table-cell" scope="col"><?php $L->p( 'Author' ); ?></th>
		</tr>
	</thead>
	<tbody>

	<?php
	foreach ( $themes as $theme ) : ?>
		<tr>
			<td class="align-middle pt-3 pb-3">
				<div><?php echo $theme['name'] . ( $theme['dirname'] == $site->theme() ? '<span class="badge badge-primary ml-2">' . $L->g( 'Active' ) . '</span>' : '' ); ?></div>
				<div class="mt-1">
					<?php
					if ( $theme['dirname'] != $site->theme() ) {
						echo '<a href="' . HTML_PATH_ADMIN_ROOT . 'install-theme/' . $theme['dirname'] . '">' . $L->g( 'Activate' ) . '</a>';
					} else {
						if ( isset( $theme['plugin'] ) ) {
						echo '<a href="' . HTML_PATH_ADMIN_ROOT . 'configure-plugin/' . $theme['plugin'] . '">' . $L->g( 'Settings' ) . '</a>';
						}
					} ?>
				</div>
			</td>
			<td class="align-middle d-none d-sm-table-cell">
				<?php echo $theme['description']; ?>
			</td>
			<td class="text-center align-middle d-none d-lg-table-cell">
				<span><?php echo $theme['version']; ?></span>
			</td>
			<td class="text-center align-middle d-none d-lg-table-cell">
				<a target="_blank" href="<?php echo $theme['website']; ?>"><?php echo $theme['author']; ?></a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
