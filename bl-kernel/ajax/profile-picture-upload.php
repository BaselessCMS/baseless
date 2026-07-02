<?php
/**
 * Avatar upload
 *
 * @package    Baseless
 * @subpackage AJAX
 * @category   Controllers
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

header( 'Content-Type: application/json' );

// $_POST
$username = empty( $_POST['username'] ) ? false : $_POST['username'];

if ( false === $username ) {
	ajax_response( 1, 'Error in username.' );
}

if ( ( $login->role() != 'admin' ) && ( $login->username() != $username ) ) {
	ajax_response( 1, 'Error in username.' );
}

if ( ! isset( $_FILES['profilePictureInputFile'] ) ) {
	ajax_response( 1, 'Error trying to upload the profile picture.' );
}

// Check path traversal.
if ( \Text :: stringContains( $username, DS, false ) ) {
	$message = 'Path traversal detected.';
	\Log :: set( $message, LOG_TYPE_ERROR );
	ajax_response( 1, $message );
}

// Check file extension.
$fileExtension = \Filesystem :: extension( $_FILES['profilePictureInputFile']['name'] );
$fileExtension = \Text :: lowercase( $fileExtension );
if ( ! in_array( $fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSION'] ) ) {
	$message = $L->g( 'File type is not supported. Allowed types:' ) . ' ' . implode( ', ',$GLOBALS['ALLOWED_IMG_EXTENSION'] );
	\Log :: set( $message, LOG_TYPE_ERROR );
	ajax_response( 1, $message );
}

// Check file MIME type.
$fileMimeType = \Filesystem :: mimeType( $_FILES['profilePictureInputFile']['tmp_name'] );
if ( $fileMimeType !== false ) {
	if ( ! in_array( $fileMimeType, $GLOBALS['ALLOWED_IMG_MIMETYPES'] ) ) {
		$message = $L->g( 'File mime type is not supported. Allowed types:' ) . ' ' . implode( ', ',$GLOBALS['ALLOWED_IMG_MIMETYPES'] );
		\Log :: set( $message, LOG_TYPE_ERROR );
		ajax_response( 1, $message );
	}
}

// Tmp filename.
$tmpFilename = $username . '.' . $fileExtension;

// Final filename.
$filename = $username . '.png';

// Move from temporary directory to uploads folder.
rename( $_FILES['profilePictureInputFile']['tmp_name'], PATH_TMP . $tmpFilename );

// Resize and convert to png.
$image = new \Image();
$image->setImage( PATH_TMP . $tmpFilename, PROFILE_IMG_WIDTH, PROFILE_IMG_HEIGHT, 'crop' );
$image->saveImage( PATH_UPLOADS_PROFILES . $filename, PROFILE_IMG_QUALITY, false, true );

// Delete temporary file.
\Filesystem :: rmfile( PATH_TMP . $tmpFilename );

// Permissions.
chmod( PATH_UPLOADS_PROFILES.$filename, 0644 );

ajax_response( 0, 'Image uploaded.', [
	'filename'     => $filename,
	'absoluteURL'  => DOMAIN_UPLOADS_PROFILES.$filename,
	'absolutePath' => PATH_UPLOADS_PROFILES.$filename
] );
