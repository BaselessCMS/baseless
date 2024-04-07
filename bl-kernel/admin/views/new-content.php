<?php
/**
 * New content page
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

// Access global variables.
global $uuid;

?>
<form class="d-flex flex-column h-100"  id="jsform" method="post" action=""  autocomplete="off">
	<input type="hidden" id="jstokenCSRF" name="tokenCSRF" value="<?php echo security()->getTokenCSRF(); ?>" />
	<input type="hidden" id="jsuuid" name="uuid" value="<?php echo $uuid; ?>" />
	<input type="hidden" id="jstype" name="type" value="published" />
	<input type="hidden" id="jscoverImage" name="coverImage" value="" />
	<input type="hidden" id="jscontent" name="content" value="" />

	<header class="admin-page-header">
		<h1><?php lang()->p( 'New Content' ); ?></h1>
	</header>

	<div id="jseditorToolbar" class="content-editor-toolbar">
		<div id="content-editor-toolbar-modals" class="content-editor-toolbar-actions" role="group">

			<button type="button" class="btn btn-secondary" id="jsmediaManagerOpenModal" data-toggle="modal" data-target="#jsmediaManagerModal"><?php lang()->p( 'Images' ); ?></button>

			<button type="button" class="btn btn-secondary" id="jsoptionsSidebar"><?php lang()->p( 'Options' ); ?></button>
		</div>

		<div id="content-editor-toolbar-publish" class="content-editor-toolbar-actions" role="group">

			<button type="button" class="btn btn-primary" id="jsbuttonSave"><?php echo lang()->g( 'Save' ); ?></button>

			<button id="jsbuttonPreview" type="button" class="btn btn-secondary"><?php lang()->p( 'Preview' ); ?></button>

			<span id="jsbuttonSwitch" data-switch="publish" class="btn btn-secondary"><?php lang()->p( 'Publish' ); ?></span>
		</div>
	</div>
	<script>
		$(document).ready( function() {
			$( '#jsoptionsSidebar' ).on( 'click', function() {
				$( '#jseditorSidebar' ).toggle();
				$( '#jsshadow' ).toggle();
			});
			$( '#jsshadow' ).on( 'click', function() {
				$( '#jseditorSidebar' ).toggle();
				$( '#jsshadow' ).toggle();
			});
		});
	</script>
	<div id="jseditorSidebar">
		<nav>
			<div class="nav nav-tabs" id="nav-tab" role="tablist">

				<a class="nav-link active show" id="nav-general-tab"  data-toggle="tab" href="#nav-general"  role="tab" aria-controls="general"><?php lang()->p( 'General' ); ?></a>

				<a class="nav-link" id="nav-advanced-tab" data-toggle="tab" href="#nav-advanced" role="tab" aria-controls="advanced"><?php lang()->p( 'Advanced' ); ?></a>

				<?php if ( ! empty( site()->customFields() ) ) : ?>
				<a class="nav-link" id="nav-custom-tab" data-toggle="tab" href="#nav-custom" role="tab" aria-controls="custom"><?php lang()->p( 'Custom' ); ?></a>
				<?php endif; ?>

				<a class="nav-link" id="nav-seo-tab" data-toggle="tab" href="#nav-seo" role="tab" aria-controls="seo"><?php lang()->p( 'SEO' ); ?></a>
			</div>
		</nav>

		<div class="tab-content pr-3 pl-3 pb-3">
			<div id="nav-general" class="tab-pane fade show active" role="tabpanel" aria-labelledby="general-tab">
				<?php
				echo Bootstrap :: formSelectBlock( [
					'name'        => 'category',
					'label'       => lang()->g( 'Category' ),
					'selected'    => '',
					'class'       => '',
					'emptyOption' => '- ' . lang()->g( 'Uncategorized' ) . ' -',
					'options'     => cats()->getKeyNameArray()
				] );

				echo Bootstrap :: formTextareaBlock( [
					'name'        => 'description',
					'label'       => lang()->g( 'Description' ),
					'selected'    => '',
					'class'       => '',
					'value'       => '',
					'rows'        => 5,
					'placeholder' => lang()->get( 'this-field-can-help-describe-the-content' )
				] );
				?>

				<label class="mt-4 mb-2 pb-2 w-100"><?php lang()->p( 'Cover Image' ); ?></label>
				<div>
					<img id="jscoverImagePreview" class="mx-auto d-block w-100" alt="<?php lang()->p( 'Cover Image Preview' ); ?>" src="<?php echo HTML_PATH_CORE_IMG; ?>default.svg" />
				</div>
				<div class="mt-2 text-center">
					<button type="button" id="jsbuttonSelectCoverImage" class="btn btn-primary btn-sm"><?php lang()->p( 'Select cover image' ); ?></button>
					<button type="button" id="jsbuttonRemoveCoverImage" class="btn btn-secondary btn-sm"><?php lang()->p( 'Remove cover image' ); ?></button>
				</div>
				<script>
					$(document).ready( function() {
						$( '#jscoverImagePreview' ).on( 'click', function() {
							openMediaManager();
						});
						$( '#jsbuttonSelectCoverImage' ).on( 'click', function() {
							openMediaManager();
						});
						$( '#jsbuttonRemoveCoverImage' ).on( 'click', function() {
							$( '#jscoverImage' ).val( '' );
							$( '#jscoverImagePreview' ).attr( 'src', HTML_PATH_CORE_IMG + 'default.svg' );
						});
					});
				</script>
			</div>
			<div id="nav-advanced" class="tab-pane fade" role="tabpanel" aria-labelledby="advanced-tab">
				<?php
				echo Bootstrap :: formInputTextBlock( [
					'name'        => 'date',
					'label'       => lang()->g( 'Date' ),
					'placeholder' => '',
					'value'       => \Date :: current( DB_DATE_FORMAT ),
					'tip'         => lang()->g( 'date-format-format' )
				] );

				echo Bootstrap :: formSelectBlock( [
					'name'     => 'typeSelector',
					'label'    => lang()->g( 'Type' ),
					'selected' => '',
					'options'  => [
						'published' => '- ' . lang()->g( 'Default' ) . ' -',
						'sticky'    => lang()->g( 'Sticky' ),
						'static'    => lang()->g( 'Static' )
					],
					'tip' => ''
				] );

				echo Bootstrap :: formInputTextBlock( [
					'name'  => 'position',
					'label' => lang()->g( 'Position' ),
					'tip'   => lang()->g( 'Field used when ordering content by position' ),
					'value' => pages()->nextPositionNumber()
				] );

				echo Bootstrap :: formInputTextBlock( [
					'name'        => 'tags',
					'label'       => lang()->g( 'Tags' ),
					'placeholder' => '',
					'tip'         => lang()->g( 'Write the tags separated by comma' )
				] );

				echo Bootstrap :: formSelectBlock( [
					'name'     => 'parent',
					'label'    => lang()->g( 'Parent' ),
					'options'  => [],
					'selected' => false,
					'class'    => '',
					'tip'      => lang()->g( 'Start typing a page title to see a list of suggestions.' ),
				] );
				?>
				<script>
				$(document).ready( function() {
					var parent = $( '#jsparent' ).select2( {
						placeholder : '',
						allowClear : true,
						theme : 'bootstrap4',
						minimumInputLength : 2,
						ajax : {
							url : HTML_PATH_ADMIN_ROOT + 'ajax/get-published',
							data : function (params) {
								var query = {
									checkIsParent : true,
									query : params.term
								}
								return query;
							},
							processResults: function (data) {
								return data;
							}
						},
						escapeMarkup: function(markup) {
							return markup;
						},
						templateResult: function(data) {
							var html = data.text;
							if ( data.type == 'static' ) {
								html += '<span class="badge badge-pill badge-light">' + data.type + '</span>';
							}
							return html;
						}
					});
				});
				</script>
				<?php
				echo Bootstrap :: formInputTextBlock( [
					'name'        => 'template',
					'label'       => lang()->g( 'Template' ),
					'placeholder' => '',
					'value'       => '',
					'tip'         => lang()->g( 'Write a template name to filter the page in the theme and change the style of the page.' )
				] );

				echo Bootstrap :: formInputTextBlock( [
					'name'        =>' externalCoverImage',
					'label'       => lang()->g( 'External cover image' ),
					'placeholder' => 'https://',
					'value'       => '',
					'tip'         => lang()->g( 'Set a cover image from external URL, such as a CDN or some server dedicated for images.' )
				] );

				echo Bootstrap :: formInputTextBlock( [
					'name'        => '',
					'label'       => lang()->g( 'Author' ),
					'placeholder' => '',
					'value'       => login()->username(),
					'tip'         => '',
					'disabled'    => true
				] );
				?>
				<script>
				$(document).ready( function() {

					// Changes in External cover image input.
					$( '#jsexternalCoverImage' ).change( function() {
						$( '#jscoverImage' ).val( $(this).val() );
					});

					// Generate slug when the user type the title.
					$( '#jstitle' ).keyup( function() {
						var text       = $(this).val();
						var parent     = $( '#jsparent' ).val();
						var currentKey = '';
						var ajax       = new bluditAjax();
						var callBack   = $( '#jsslug' );
						ajax.generateSlug( text, parent, currentKey, callBack );
					});

					// Datepicker.
					$( '#jsdate' ).datetimepicker( { format : DB_DATE_FORMAT } );


				});
				</script>
			</div>
			<?php if ( ! empty( site()->customFields() ) ) : ?>
			<div id="nav-custom" class="tab-pane fade" role="tabpanel" aria-labelledby="custom-tab">
			<?php
				$custom_fields = site()->customFields();
				foreach ( $custom_fields as $field => $options ) {
					if ( ! isset( $options['position'] ) ) {

						if ( 'string' == $options['type'] ) {
							echo Bootstrap :: formInputTextBlock( [
								'name'        => 'custom[' . $field . ']',
								'label'       => ( isset( $options['label'] ) ? $options['label'] : '' ),
								'value'       => ( isset( $options['default'] ) ? $options['default'] : '' ),
								'tip'         => ( isset( $options['tip'] ) ? $options['tip'] : '' ),
								'placeholder' => ( isset( $options['placeholder'] ) ? $options['placeholder'] : '' )
							] );

						} elseif ( 'bool' == $options['type'] ) {
							echo Bootstrap :: formCheckbox( [
								'name'             => 'custom[' . $field . ']',
								'label'            => ( isset( $options['label'] ) ? $options['label'] : '' ),
								'placeholder'      => ( isset( $options['placeholder'] ) ? $options['placeholder'] : '' ),
								'checked'          => ( isset( $options['checked'] ) ? true : false ),
								'labelForCheckbox' => ( isset( $options['tip'] ) ? $options['tip'] : '' )
							] );
						}
					}
				}
			?>
			</div>
			<?php endif; ?>
			<div id="nav-seo" class="tab-pane fade" role="tabpanel" aria-labelledby="seo-tab">
				<?php
				echo Bootstrap :: formInputTextBlock( [
					'name'        => 'slug',
					'tip'         => lang()->g( 'URL associated with the content' ),
					'label'       => lang()->g( 'Friendly URL' ),
					'placeholder' => lang()->g( 'Leave empty for autocomplete by Bludit.' )
				] );

				echo Bootstrap :: formCheckbox( [
					'name'             => 'noindex',
					'label'            => 'Robots',
					'labelForCheckbox' => lang()->g( 'apply-code-noindex-code-to-this-page' ),
					'placeholder'      => '',
					'checked'          => false,
					'tip'              => lang()->g( 'This tells search engines not to show this page in their search results.' )
				] );

				echo Bootstrap :: formCheckbox( [
					'name'             => 'nofollow',
					'label'            => '',
					'labelForCheckbox' => lang()->g( 'apply-code-nofollow-code-to-this-page' ),
					'placeholder'      => '',
					'checked'          => false,
					'tip'              => lang()->g( 'This tells search engines not to follow links on this page.' )
				] );

				echo Bootstrap :: formCheckbox( [
					'name'             => 'noarchive',
					'label'            => '',
					'labelForCheckbox' => lang()->g( 'apply-code-noarchive-code-to-this-page' ),
					'placeholder'      => '',
					'checked'          => false,
					'tip'              => lang()->g( 'This tells search engines not to save a cached copy of this page.' )
				] );
				?>
			</div>
		</div>
	</div>
	<?php
	$custom_fields = site()->customFields();
	foreach ( $custom_fields as $field => $options ) {
		if ( isset( $options['position'] ) && ( 'top' == $options['position'] ) ) {

			if ( 'string' == $options['type'] ) {
				echo Bootstrap :: formInputTextBlock( [
					'name'        => 'custom[' . $field . ']',
					'label       '=> ( isset( $options['label'] ) ? $options['label'] : '' ),
					'value'       => ( isset( $options['default'] ) ? $options['default'] : '' ),
					'tip'         => ( isset( $options['tip'] ) ? $options['tip'] : '' ),
					'placeholder' => ( isset( $options['placeholder'] ) ? $options['placeholder'] : '' ),
					'class'       => 'mb-2',
					'labelClass'  => 'mb-2 pb-2 border-bottom w-100'

				] );

			} elseif ( 'bool' == $options['type'] ) {
				echo Bootstrap :: formCheckbox( [
					'name'             => 'custom[' . $field . ']',
					'label'            => ( isset( $options['label'] ) ? $options['label'] : '' ),
					'placeholder'      => ( isset( $options['placeholder'] ) ? $options['placeholder'] : '' ),
					'checked'          => ( isset( $options['checked'] ) ? true : false ),
					'labelForCheckbox' => ( isset( $options['tip'] ) ? $options['tip'] : '' ),
					'class'            => 'mb-2',
					'labelClass'       => 'mb-2 pb-2 border-bottom w-100'
				] );
			}
		}
	}
	?>
	<div id="jseditorTitle" class="form-group mb-1">
		<input id="jstitle" name="title" type="text" class="form-control form-control-lg rounded-0" value="" placeholder="<?php lang()->p( 'Enter title' ); ?>">
	</div>

	<textarea id="jseditor" class="editable h-100 mb-1"></textarea>

	<?php
	$custom_fields = site()->customFields();
	foreach ( $custom_fields as $field => $options ) {
		if ( isset( $options['position'] ) && ( 'bottom' == $options['position'] ) ) {

			if ( 'string' == $options['type'] ) {

				echo Bootstrap :: formInputTextBlock( [
					'name'        => 'custom[' . $field . ']',
					'label'       => ( isset( $options['label'] ) ? $options['label'] : '' ),
					'value'       => ( isset( $options['default'] ) ? $options['default'] : '' ),
					'tip'         => ( isset( $options['tip'] ) ? $options['tip'] : '' ),
					'placeholder' => ( isset( $options['placeholder'] ) ? $options['placeholder'] : '' ),
					'class'       => 'mt-2',
					'labelClass'  => 'mb-2 pb-2 border-bottom w-100'

				] );

			} elseif ( 'bool' == $options['type'] ) {

				echo Bootstrap :: formCheckbox( [
					'name'             => 'custom[' . $field . ']',
					'label'            => ( isset( $options['label'] ) ? $options['label'] : '' ),
					'placeholder'      => ( isset( $options['placeholder'] ) ? $options['placeholder'] : '' ),
					'checked'          => ( isset( $options['checked'] ) ? true : false),
					'labelForCheckbox' => ( isset( $options['tip'] ) ? $options['tip'] : '' ),
					'class'            => 'mt-2',
					'labelClass'       => 'mb-2 pb-2 border-bottom w-100'
				] );
			}
		}
	}
	?>
</form>

<?php include( PATH_ADMIN . 'views/media.php' ); ?>

<script>
$(document).ready( function() {

	// Define function if they doesn't exist.
	// This helps if the user doesn't activate any plugin as editor.
	if ( typeof editorGetContent != 'function' ) {
		window.editorGetContent = function() {
			return $( '#jseditor' ).val();
		};
	}
	if ( typeof editorInsertMedia != 'function' ) {
		window.editorInsertMedia = function(filename) {
			$( '#jseditor' ).val( $( '#jseditor' ).val() + '<img src="' + filename + ' alt="">' );
		};
	}
	if ( typeof editorInsertLinkedMedia != 'function' ) {
		window.editorInsertLinkedMedia = function( filename, link ) {
			$( '#jseditor' ).val( $( '#jseditor' ).val() + '<a href="' + link + '"><img src="' + filename + '" alt=""></a>' );
		};
	}

	// Button switch.
	$( '#jsbuttonSwitch' ).on( 'click', function() {
		if ( $(this).data( 'switch' ) == 'publish' ) {
			$(this).html( '<?php lang()->p( 'Draft' ); ?>' );
			$(this).data( 'switch', 'draft' );
		} else {
			$(this).html( '<?php lang()->p( 'Publish' ); ?>' );
			$(this).data( 'switch', 'publish' );
		}
	});

	// Button preview.
	$( '#jsbuttonPreview' ).on( 'click', function() {
		var uuid    = $( '#jsuuid' ).val();
		var title   = $( '#jstitle' ).val();
		var content = editorGetContent();
		bluditAjax.saveAsDraft( uuid, title, content ).then( function(data) {
			var preview = window.open( "<?php echo DOMAIN_PAGES . 'autosave-' . $uuid . '?preview=' . md5( 'autosave-' . $uuid ); ?>", 'bludit-preview' );
			preview.focus();
		});
	});

	// Button Save.
	$( '#jsbuttonSave' ).on( 'click', function() {

		// If the switch is set to "published", get the value from the selector.
		if ( $( '#jsbuttonSwitch' ).data( 'switch' ) == 'publish' ) {
			var value = $( '#jstypeSelector option:selected' ).val();
			$( '#jstype' ).val(value);
		} else {
			$( '#jstype' ).val( 'draft' );
		}

		// Get the content.
		$( '#jscontent' ).val( editorGetContent() );

		// Submit the form.
		$( '#jsform' ).submit();
	});

	// Autosave
	var currentContent = editorGetContent();
	setInterval( function() {

			var uuid    = $( '#jsuuid' ).val();
			var title   = $( '#jstitle' ).val() + "[<?php lang()->p( 'Autosave' ); ?>]";
			var content = editorGetContent();

			// Autosave when content has at least 100 characters.
			if ( content.length < 100 ) {
				return false;
			}
			// Autosave only when the user change the content.
			if ( currentContent != content ) {
				currentContent = content;
				bluditAjax.saveAsDraft( uuid, title, content ).then( function(data) {
					if ( data.status == 0 ) {
						showAlert( "<?php lang()->p( 'Autosave' ); ?>" );
					}
				});
			}
	}, 1000 * 60 * AUTOSAVE_INTERVAL );
});
</script>
