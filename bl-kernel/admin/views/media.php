<?php
/**
 * Media modal content
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

/**
 * Preload the first 10 files to not call via AJAX
 * when the user open the first time the media manager.
 */
$files_by_page = \Filesystem :: listFiles( PAGE_THUMBNAILS_DIRECTORY, '*', '*', MEDIA_MANAGER_SORT_BY_DATE, MEDIA_MANAGER_NUMBER_OF_FILES );

$preLoadFiles = [];

if ( ! empty( $files_by_page[0] ) ) {
	foreach ( $files_by_page[0] as $file ) {
		$filename = \Filesystem :: filename( $file );
		array_push( $preLoadFiles, $filename );
	}
}

// Amount of pages for the paginator.
$numberOfPages = count( $files_by_page );
?>

<div id="jsmediaManagerModal" class="modal" tabindex="-1" role="dialog">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="container-fluid">
	<div class="row">
		<div class="col p-3">
			<h3 class="mt-2 mb-3"><span class="fa fa-image"></span> <?php lang()->p( 'Images' ); ?></h3>

			<div id="jsalertMedia" class="alert alert-warning d-none" role="alert"></div>

			<form name="bluditFormUpload" id="jsbluditFormUpload" enctype="multipart/form-data">
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="jsimages" name="images[]" multiple>
					<label class="custom-file-label" for="jsimages"><?php lang()->p( 'Choose images to upload' ); ?></label>
				</div>
			</form>

			<div class="progress mt-3">
				<div id="jsbluditProgressBar" class="progress-bar bg-primary" role="progressbar" style="width:0%"></div>
			</div>

			<table id="jsbluditMediaTable" class="table mt-3">
				<tr>
					<td><?php lang()->p( 'There are no images' ); ?></td>
				</tr>
			</table>

			<nav id="jsbluditMediaTablePagination"></nav>

		</div>
	</div>
   </div>
  </div>
 </div>
</div>

<script>
<?php echo 'var preLoadFiles = ' . json_encode( $preLoadFiles ) . ';'; ?>

function openMediaManager() {
	$( '#jsmediaManagerModal' ).modal( 'show' );
}

function closeMediaManager() {
	$( '#jsmediaManagerModal' ).modal( 'hide' );
}

// Remove all files from the table.
function cleanTable() {
	$( '#jsbluditMediaTable' ).empty();
}

function showMediaAlert( message ) {
	$( '#jsalertMedia' ).html(message).removeClass( 'd-none' );
}

function hideMediaAlert() {
	$( '#jsalertMedia' ).addClass( 'd-none' );
}

// Show the files in the table
function displayFiles( files, numberOfPages = <?= $numberOfPages ?> ) {

	if ( ! Array.isArray( files ) ) {
		return false;
	}
	cleanTable();

	// Regenerate the table.
	if ( files.length > 0 ) {
		$.each( files, function( key, filename ) {
			var thumbnail = "<?php echo PAGE_THUMBNAILS_URL; ?>" + filename;
			var image = "<?php echo PAGE_IMAGES_URL; ?>" + filename;

			tableRow = '<tr id="js' + filename + '">' +
					'<td style="width:80px"><img class="img-thumbnail" alt="200x200" src="' + thumbnail + '" style="width: 50px; height: 50px;"><\/td>' +
					'<td class="information">' +
						'<div class="text-secondary pb-2">' + filename + '<\/div>' +
						'<div class="media-modal-actions">' +
							'<a href="#" onClick="editorInsertMedia(\'' + image + '\' ); closeMediaManager();"><span class="fa fa-plus-circle"></span><?php lang()->p( 'Insert' ) ?><\/a>' +
							'<a href="#" onClick="editorInsertMedia(\'' + thumbnail + '\' ); closeMediaManager();"><span class="fa fa-image"></span><?php lang()->p( 'Insert Thumbnail' ) ?><\/a>' +
							'<a href="#" onClick="editorInsertLinkedMedia(\'' + thumbnail + '\',\'' + image + '\' ); closeMediaManager();"><span class="fa fa-link"></span><?php lang()->p( 'Insert Linked Thumbnail' ) ?><\/a>' +
							'<a href="#" onClick="setCoverImage(\'' + filename + '\' ); closeMediaManager();"><span class="fa fa-desktop"></span><?php lang()->p( 'Set as Cover' ) ?><\/button>' +
							'<a href="#" class="text-danger" onClick="deleteMedia(\'' + filename + '\' )"><span class="fa fa-trash-o"></span><?php lang()->p( 'Delete' ) ?><\/a>' +
						'<\/div>' +
					'<\/td>' +
				'<\/tr>';
			$( '#jsbluditMediaTable' ).append( tableRow );
		});

		mediaPagination = '<ul class="pagination justify-content-center flex-wrap">';
		for ( var i = 1; i <= numberOfPages; i++ ) {
			mediaPagination += '<li class="page-item"><button type="button" class="btn btn-link page-link" onClick="getFiles( ' + i + ' )">' + i + '</button></li>';
		}
		mediaPagination += '</ul>';
		$( '#jsbluditMediaTablePagination' ).html( mediaPagination );

	}

	if ( files.length == 0 ) {
		$( '#jsbluditMediaTable' ).html("<p><?php ( IMAGE_RESTRICT ? lang()->p( 'There are no images for the page' ) : lang()->p( 'There are no images' ) ) ?></p>");
		$( '#jsbluditMediaTablePagination' ).html( '' );
	}
}

// Get the list of files via AJAX, filter by the page number
function getFiles (pageNumber ) {

	$.post( HTML_PATH_ADMIN_ROOT + "ajax/list-images",
		{ 	tokenCSRF  : tokenCSRF,
			pageNumber : pageNumber,
			uuid : "<?php echo PAGE_IMAGES_KEY; ?>",
			path : "thumbnails" // The paths are defined in ajax/list-images.
		},
		function(data) { // Success function.
			if ( data.status==0 ) {
				displayFiles( data.files, data.numberOfPages );
			} else {
				console.log( data.message );
			}
		}
	);
}

// Delete the file and the thumbnail if exist.
function deleteMedia( filename ) {

	$.post( HTML_PATH_ADMIN_ROOT + 'ajax/delete-image',
		{ 	tokenCSRF : tokenCSRF,
			filename  : filename,
			uuid : '<?php echo PAGE_IMAGES_KEY; ?>'
		},
		function( data ) { // Success function.
			if ( data.status == 0 ) {
				getFiles(1);
			} else {
				console.log( data.message );
			}
		}
	);
}

function setCoverImage( filename ) {
	var image = "<?php echo PAGE_IMAGES_URL; ?>" + filename;
	$( '#jscoverImage' ).val( filename );
	$( '#jscoverImagePreview' ).attr( 'src', image );
}

function uploadImages() {

	// Remove current alerts.
	hideMediaAlert();

	var images = $( '#jsimages' )[0].files;
	for ( var i = 0; i < images.length; i++ ) {

		// Check file type/extension.
		const validImageTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'];
		if ( ! validImageTypes.includes( images[i].type ) ) {
			showMediaAlert( "<?php echo lang()->g( 'File type is not supported. Allowed types:' ) . ' ' . implode( ', ',$GLOBALS['ALLOWED_IMG_EXTENSION'] ); ?>" );
			return false;
		}

		// Check file size and compare with PHP upload_max_filesize
		if ( images[i].size > UPLOAD_MAX_FILESIZE ) {
			showMediaAlert( "<?php echo lang()->g( 'Maximum load file size allowed:' ) . ' ' . ini_get( 'upload_max_filesize' ); ?>" );
			return false;
		}
	};

	// Clean progress bar.
	$( '#jsbluditProgressBar' ).removeClass().addClass( 'progress-bar bg-primary' );
	$( '#jsbluditProgressBar' ).width( '0' );

	// Data to send via AJAX.
	var formData = new FormData( $( '#jsbluditFormUpload' )[0] );
	formData.append( 'uuid', '<?php echo PAGE_IMAGES_KEY; ?>' );
	formData.append( 'tokenCSRF', tokenCSRF );

	$.ajax({
		url: HTML_PATH_ADMIN_ROOT + 'ajax/upload-images',
		type  : "POST",
		data  : formData,
		cache : false,
		contentType : false,
		processData : false,
		xhr : function() {
			var xhr = $.ajaxSettings.xhr();
			if ( xhr.upload ) {
				xhr.upload.addEventListener( 'progress', function(e) {
					if ( e.lengthComputable ) {
						var percentComplete = ( e.loaded / e.total ) * 100;
						$( '#jsbluditProgressBar' ).width( percentComplete + '%' );
					}
				}, false );
			}
			return xhr;
		}
	}).done( function( data ) {

		if ( data.status == 0 ) {
			$( '#jsbluditProgressBar' ).removeClass( 'bg-primary' ).addClass( 'bg-success' );
			// Get the files for the first page, this include the files uploaded.
			getFiles(1);
		} else {
			$( '#jsbluditProgressBar' ).removeClass( 'bg-primary' ).addClass( 'bg-danger' );
			showMediaAlert( data.message );
		}
	});
}

$(document).ready( function() {

	// Display the files preloaded for the first time.
	displayFiles( preLoadFiles );

	// Select image event.
	$( '#jsimages' ).on( 'change', function(e) {
		uploadImages();
	});

	// Drag and drop image.
	$(window).on( 'dragover dragenter', function(e) {
		e.preventDefault();
		e.stopPropagation();
		openMediaManager();
	});

	// Drag and drop image.
	$(window).on( 'drop', function(e) {
		e.preventDefault();
		e.stopPropagation();
		$( '#jsimages' ).prop( 'files', e.originalEvent.dataTransfer.files );
		uploadImages();
	});
});
</script>
