<?php
/**
 * Upload images
 *
 * @package    JSON CMS
 * @subpackage AJAX
 * @category   Controllers
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

header( 'Content-Type: application/json' );

// $_POST
$uuid = empty( $_POST['uuid'] ) ? false : $_POST['uuid'];

// Check path traversal on $uuid.
if ( $uuid ) {
	if ( \Text::stringContains( $uuid, DS, false ) ) {
		$message = 'Path traversal detected.';
		\Log :: set( $message, LOG_TYPE_ERROR );
		ajax_response(1, $message );
	}
}

// Set upload directory.
if ( $uuid && IMAGE_RESTRICT ) {
	$imageDirectory     = PATH_UPLOADS_PAGES . $uuid.DS;
	$thumbnailDirectory = $imageDirectory . 'thumbnails' . DS;
	if ( ! \Filesystem :: directoryExists( $thumbnailDirectory ) ) {
		\Filesystem :: mkdir( $thumbnailDirectory, true );
	}
} else {
	$imageDirectory     = PATH_UPLOADS;
	$thumbnailDirectory = PATH_UPLOADS_THUMBNAILS;
}

$images = [];
foreach ( $_FILES['images']['name'] as $uuid => $filename ) {

	// Check for errors.
	if ( $_FILES['images']['error'][$uuid] != 0 ) {
		$message = $L->g( 'Maximum load file size allowed:' ) . ' ' . ini_get( 'upload_max_filesize' );
		\Log :: set( $message, LOG_TYPE_ERROR );
		ajax_response( 1, $message );
	}

	// Convert URL characters such as spaces or quotes to characters.
	$filename = urldecode( $filename );

	// Check path traversal on $filename.
	if ( \Text :: stringContains( $filename, DS, false ) ) {
		$message = 'Path traversal detected.';
		\Log :: set( $message, LOG_TYPE_ERROR );
		ajax_response( 1, $message );
	}

	// Check file extension.
	$fileExtension = \Filesystem :: extension( $filename );
	$fileExtension = \Text :: lowercase( $fileExtension );
	if ( ! in_array( $fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSION'] ) ) {
		$message = $L->g( 'File type is not supported. Allowed types:' ) . ' ' . implode( ', ', $GLOBALS['ALLOWED_IMG_EXTENSION'] );
		\Log :: set( $message, LOG_TYPE_ERROR );
		ajax_response( 1, $message );
	}

	// Check file MIME Type.
	$fileMimeType = \Filesystem :: mimeType( $_FILES['images']['tmp_name'][$uuid] );
	if ( $fileMimeType !== false ) {
		if ( ! in_array( $fileMimeType, $GLOBALS['ALLOWED_IMG_MIMETYPES'] ) ) {
			$message = $L->g( 'File mime type is not supported. Allowed types:' ) . ' ' . implode( ', ',$GLOBALS['ALLOWED_IMG_MIMETYPES'] );
			\Log :: set( $message, LOG_TYPE_ERROR );
			ajax_response( 1, $message );
		}
	}

	// Move from PHP tmp file to Bludit tmp directory.
	\Filesystem :: mv( $_FILES['images']['tmp_name'][$uuid], PATH_TMP . $filename );

	// Transform the image and generate the thumbnail.
	$image = transform_image( PATH_TMP . $filename, $imageDirectory, $thumbnailDirectory );

	if ( $image ) {
		chmod( $image, 0644 );
		$filename = \Filesystem :: filename( $image );
		array_push( $images, $filename );
	} else {
		$message = 'Error after transform_image() function.';
		\Log :: set( $message, LOG_TYPE_ERROR );
		ajax_response( 1, $message );
	}
}

ajax_response( 0, 'Images uploaded.', [
	'images' => $images
] );
