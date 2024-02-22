<?php

echo Bootstrap :: pageTitle( [ 'title' => $L->g( 'Content Management System' ) ] );

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
