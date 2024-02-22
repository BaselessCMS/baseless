<?php defined('BLUDIT') or die('Bludit CMS.');

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	$L->g( 'About' ),
	$site->title()
);
