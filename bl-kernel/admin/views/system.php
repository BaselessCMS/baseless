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
<header class="admin-page-header">
	<h1><?php lang()->p( 'System Info' ); ?></h1>
</header>

<?php

echo '<table class="table table-striped mt-3"><tbody>';

echo '<tr>';
echo "<td>{$L->g( 'PHP Version' )}</td>";
echo '<td>' . phpversion() . '</td>';
echo '</tr>';

echo '<tr>';
echo "<td>{$L->g( 'CMS Version' )}</td>";
echo '<td>' . BLUDIT_VERSION . '</td>';
echo '</tr>';

echo '<tr>';
echo "<td>{$L->g( 'Build Number' )}</td>";
echo '<td>' . BLUDIT_BUILD . '</td>';
echo '</tr>';

echo '<tr>';
echo "<td>{$L->g( 'Disk Usage' )}</td>";
echo '<td>' . Filesystem :: bytesToHumanFileSize( Filesystem :: getSize( PATH_ROOT ) ) . '</td>';
echo '</tr>';

echo '</tbody></table>';
?>

<?php if ( $config['dash_notify_qty'] > 0 ) : ?>

<h2 class="m-0"><?php $L->p( 'Notifications' ); ?></h2>

<table class="table table-striped mt-3">
	<tbody>
		<tr>
			<th><?php lang()->p( 'Action' ); ?></th>
			<th><?php lang()->p( 'Details' ); ?></th>
		</tr>

	<?php
	$logs = array_slice( $syslog->db, 0, NOTIFICATIONS_AMOUNT );
	foreach ( $logs as $log ) :
		$phrase = $L->g( $log['dictionaryKey'] );

		?>
		<tr>
			<td><?php echo $phrase; ?>
				<?php if ( ! empty( $log['notes'] ) ) {
				echo ' > ' . $log['notes'];
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
printTable( 'File Uploads', $uploadOptions );

// Loaded extensions.
printTable( 'Server Information ( $_SERVER )', $_SERVER );

// PHP Ini.
printTable( 'PHP Configuration Options ( ini_get_all() )', ini_get_all() );

// Loaded extensions.
printTable( 'Loaded Extensions', get_loaded_extensions() );

// Locales installed.
exec( 'locale -a', $locales );
printTable( 'Locales Installed', $locales );

// Defined constants.
$constants = get_defined_constants( true );
printTable( 'CMS Constants', $constants['user'] );

// Site object.
printTable( 'Site Object Database', $site->db );
