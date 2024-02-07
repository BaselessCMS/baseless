<?php

echo Bootstrap :: pageTitle( [ 'title'=>$L->g( 'Developers' ), 'icon'=>'gears' ] );

echo '<h2 class="mb-4 mt-4"><b>PHP version: ' . phpversion() . '</b></h2>';

// PHP Ini.
$uploadOptions = [
	'upload_max_filesize' => ini_get( 'upload_max_filesize' ),
	'post_max_size'       => ini_get( 'post_max_size' ),
	'upload_tmp_dir'      => ini_get( 'upload_tmp_dir' )
];
printTable( 'File Uploads', $uploadOptions );

// Loaded extensions.
printTable( 'Server information ( $_SERVER )', $_SERVER );

// PHP Ini.
printTable( 'PHP Configuration options ( ini_get_all() )', ini_get_all() );

// Loaded extensions.
printTable( 'Loaded extensions', get_loaded_extensions() );

// Locales installed.
exec( 'locale -a', $locales );
printTable( 'Locales installed', $locales );

// Defined constants.
$constants = get_defined_constants( true );
printTable( 'CMS Constants', $constants['user'] );

// Site object.
printTable( 'site object database', $site->db );
