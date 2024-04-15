<?php
/**
 * System info page
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

// Alias namespaces.
use CMS_Admin\Controllers\System as Ctrl;

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
global $config;

?>
<header class="admin-page-header">
	<h1><?php lang()->p( 'System Info' ); ?></h1>
</header>

<table class="table table-striped system-info-table">
	<tbody>
		<tr>
			<td><?php lang()->p( 'PHP Version' ); ?></td>
			<td><?php echo phpversion(); ?></td>
		</tr>

		<tr>
			<td><?php lang()->p( 'CMS Version' ); ?></td>
			<td><?php echo CMS_VERSION; ?></td>
		</tr>

		<tr>
			<td><?php lang()->p( 'Build Number' ); ?></td>
			<td><?php echo BLUDIT_BUILD; ?></td>
		</tr>

		<tr>
			<td><?php lang()->p( 'Disk Usage' ); ?></td>
			<td><?php echo \Filesystem :: bytesToHumanFileSize( \Filesystem :: getSize( PATH_ROOT ) ); ?></td>
		</tr>
	</tbody>
</table>

<?php if ( $config['dash_notify_qty'] > 0 ) : ?>

<h2 class="system-info-heading"><?php lang()->p( 'Notifications' ); ?></h2>

<table class="table table-striped system-info-table">
	<tbody>
		<tr>
			<th><?php lang()->p( 'Action' ); ?></th>
			<th><?php lang()->p( 'Details' ); ?></th>
		</tr>

	<?php
	$logs = array_slice( $syslog->db, 0, NOTIFICATIONS_AMOUNT );
	foreach ( $logs as $log ) :
		$phrase = lang()->g( $log['dictionaryKey'] );

		?>
		<tr>
			<td><?php echo $phrase; ?>
				<?php if ( ! empty( $log['notes'] ) ) {
				echo $log['notes'];
				} ?>
			</td>
			<td><?php echo \Date :: format( $log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT ); ?> <?php lang()->p( 'by' ); ?> <?php echo $log['username']; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>

<?php

// PHP Ini.
$uploadOptions = [
	'upload_max_filesize' => ini_get( 'upload_max_filesize' ),
	'post_max_size'       => ini_get( 'post_max_size' ),
	'upload_tmp_dir'      => ini_get( 'upload_tmp_dir' )
];
Ctrl\table( 'File Uploads', $uploadOptions );

// Loaded extensions.
Ctrl\table( 'Server Information ( $_SERVER )', $_SERVER );

// PHP Ini.
Ctrl\table( 'PHP Configuration Options ( ini_get_all() )', ini_get_all() );

// Loaded extensions.
Ctrl\table( 'Loaded Extensions', get_loaded_extensions() );

// Locales installed.
exec( 'locale -a', $locales );
Ctrl\table( 'Locales Installed', $locales );
if ( ! $locales ) {
	printf(
		'<p>%s</p>',
		lang()->get( 'No locales installed.' )
	);
}

// Defined constants.
$constants = get_defined_constants( true );
Ctrl\table( 'CMS Constants', $constants['user'] );

// Site object.
Ctrl\table( 'Site Object Database', site()->db );
