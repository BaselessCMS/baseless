
<h1><?php $L->p( 'System' ); ?></h1>

<?php

echo '<table class="table table-striped mt-3"><tbody>';

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

echo '<tr>';
echo '<td><a href="' . HTML_PATH_ADMIN_ROOT . 'developers' . '">' . $L->g( 'Developers' ) . '</a></td>';
echo '<td></td>';
echo '</tr>';

echo '</tbody></table>';

?>
<?php if ( $config['dash_notify_qty'] > 0 ) : ?>

<h2 class="m-0"><?php $L->p('Notifications') ?></h2>

<ul class="list-group list-group-striped b-0">
	<?php
	$logs = array_slice($syslog->db, 0, NOTIFICATIONS_AMOUNT);
	foreach ($logs as $log) {
		$phrase = $L->g($log['dictionaryKey']);
		echo '<li class="list-group-item">';
		echo $phrase;
		if (!empty($log['notes'])) {
			echo ' « <b>' . $log['notes'] . '</b> »';
		}
		echo '<br><span class="notification-date"><small>';
		echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT);
		echo ' [ ' . $log['username'] . ' ]';
		echo '</small></span>';
		echo '</li>';
	}
	?>
</ul>
<?php endif; ?>
