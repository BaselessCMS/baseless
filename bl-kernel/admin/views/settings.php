<?php
/**
 * Settings page
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Views
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
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

?>
<header class="admin-page-header has-actions">
	<h1><?php lang()->p( 'Settings' ); ?></h1>
</header>

<form id="jsform" method="post" action=""  autocomplete="off">

	<div class="form-wrap admin-form-wrap">
		<div class="form-fields admin-form-fields tab-content">

			<nav class="tabbed-tabs admin-tabbed-tabs form-tabs admin-form-tabs">
				<div class="nav nav-tabs" id="nav-tab" role="tablist">

					<a class="nav-item nav-link active" id="nav-general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="nav-general" aria-selected="false"><?php lang()->p( 'General' ); ?></a>

					<a class="nav-item nav-link" id="nav-content-tab" data-toggle="tab" href="#content" role="tab" aria-controls="nav-content" aria-selected="false"><?php lang()->p( 'Content' ); ?></a>

					<a class="nav-item nav-link" id="nav-images-tab" data-toggle="tab" href="#images" role="tab" aria-controls="nav-images" aria-selected="false"><?php lang()->p( 'Images' ); ?></a>

					<a class="nav-item nav-link" id="nav-meta-tab" data-toggle="tab" href="#meta" role="tab" aria-controls="nav-meta" aria-selected="false"><?php lang()->p( 'Meta' ); ?></a>

					<a class="nav-item nav-link" id="nav-custom-fields-tab" data-toggle="tab" href="#custom-fields" role="tab" aria-controls="nav-custom-fields" aria-selected="false"><?php lang()->p( 'Custom Fields' ); ?></a>

					<a class="nav-item nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'system' ?>" role="link" aria-selected="false"><?php lang()->p( 'System' ); ?></a>
				</div>
			</nav>

			<input type="hidden" id="jstokenCSRF" name="tokenCSRF" value="<?php echo security()->getTokenCSRF(); ?>" />

			<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">

				<h2><?php lang()->p( 'General Settings' ); ?></h2>

				<fieldset class="admin-fieldset">
					<?php
					echo Bootstrap :: formInputText( [
						'name'        => 'url',
						'label'       => 'Site URL',
						'value'       => site()->url(),
						'class'       => '',
						'placeholder' => 'https://',
						'tip'         => lang()->g( 'full-url-of-your-site' )
					] );

					echo Bootstrap :: formInputText( [
						'name'        => 'title',
						'label'       => lang()->g( 'Site Title' ),
						'value'       => site()->title(),
						'class'       => '',
						'placeholder' => '',
						'tip'         => lang()->g( 'use-this-field-to-name-your-site' )
					] );

					echo Bootstrap :: formInputText( [
						'name'        => 'slogan',
						'label'       => lang()->g( 'Site Slogan' ),
						'value'       => site()->slogan(),
						'class'       => '',
						'placeholder' => '',
						'tip'         => lang()->g( 'use-this-field-to-add-a-catchy-phrase' )
					] );

					echo Bootstrap :: formInputText( [
						'name'        => 'description',
						'label'       => lang()->g( 'Site Description' ),
						'value'       => site()->description(),
						'class'       => '',
						'placeholder' => '',
						'tip'         => lang()->g( 'you-can-add-a-site-description-to-provide' )
					] );

					echo Bootstrap :: formInputText( [
						'name'        => 'emailFrom',
						'label'       => lang()->g( 'Site Email' ),
						'value'       => site()->emailFrom(),
						'class'       => '',
						'placeholder' => '',
						'tip'         => lang()->g( 'Emails will be sent from this address.' )
					] );
					?>
				</fieldset>

				<h2><?php lang()->p( 'Language Settings' ); ?></h2>

				<fieldset class="admin-fieldset">
					<?php
					echo Bootstrap :: formSelect( [
						'name'     => 'language',
						'label'    => lang()->g( 'Language' ),
						'options'  => lang()->getLanguageList(),
						'selected' => site()->language(),
						'class'    => '',
						'tip'      => lang()->g( 'select-your-sites-language' )
					] );

					echo Bootstrap :: formInputText( [
						'name'        => 'locale',
						'label'       => lang()->g( 'Locale' ),
						'value'       => site()->locale(),
						'class'       => '',
						'placeholder' => '',
						'tip'         => lang()->g( 'with-the-locales-you-can-set-the-regional-user-interface' )
					] );
					?>
				</fieldset>

				<h2><?php lang()->p( 'Time & Date Settings' ); ?></h2>

				<fieldset class="admin-fieldset">

					<?php
					echo Bootstrap :: formSelect( [
						'name'     => 'timezone',
						'label'    => lang()->g( 'Timezone' ),
						'options'  => \Date :: timezoneList(),
						'selected' => site()->timezone(),
						'class'    => '',
						'tip'      => lang()->g( 'select-a-timezone-for-a-correct' )
					] );

					echo Bootstrap :: formInputText( [
						'name'        => 'dateFormat',
						'label'       => lang()->g( 'Date Format' ),
						'value'       => site()->dateFormat(),
						'class'       => '',
						'placeholder' => '',
						'tip'         => lang()->g( 'Current format' ) . ': ' . \Date :: current(site()->dateFormat() )
					] );

					/*
					echo Bootstrap :: formInputText( [
						'name'        => 'footer',
						'label'       => lang()->g( 'Footer text' ),
						'value'       => site()->footer(),
						'class'       => '',
						'placeholder' => '',
						'tip'         => lang()->g( 'you-can-add-a-small-text-on-the-bottom' )
					] );
					*/
					?>
				</fieldset>
			</div>

			<div class="tab-pane fade" id="content" role="tabpanel" aria-labelledby="content-tab">

				<h2><?php lang()->p( 'Content Settings' ); ?></h2>

				<?php
				echo Bootstrap :: formInputText( [
					'name'        => 'itemsPerPage',
					'label'       => lang()->g( 'Items Per Page' ),
					'value'       => site()->itemsPerPage(),
					'class'       => '',
					'placeholder' => '',
					'tip'         => lang()->g( 'Number of items to show per page' )
				] );

				echo Bootstrap :: formSelect( [
					'name'     => 'orderBy',
					'label'    => lang()->g( 'Order Content By' ),
					'options'  => [
						'date'     => lang()->g( 'Date' ),
						'position' => lang()->g( 'Position' )
					],
					'selected' => site()->orderBy(),
					'class'    => '',
					'tip'      => lang()->g( 'order-the-content-by-date-to-build-a-blog' )
				] );

				echo Bootstrap :: formInputText( [
					'name'        => 'autosaveInterval',
					'label'       => lang()->g( 'Autosave Interval' ),
					'value'       => site()->autosaveInterval(),
					'class'       => '',
					'placeholder' => '',
					'tip'         => lang()->g( 'Number in minutes for every execution of autosave' )
				] );

				echo Bootstrap :: formSelect( [
					'name'     => 'markdownParser',
					'label'    => lang()->g( 'Markdown Parser' ),
					'options'  => [
						'true'  => lang()->g( 'Enabled' ),
						'false' => lang()->g( 'Disabled' )
					],
					'selected' => ( site()->markdownParser() ? 'true' : 'false' ),
					'class'    => '',
					'tip'      => lang()->g( 'Enable the markdown parser for the content of the page.' )
				] );
				?>

				<h2><?php lang()->p( 'URL Settings' ); ?></h2>

				<?php
				// Home page.
				try {
					$options = [];
					$homeKey = site()->homepage();
					if ( ! empty( $homeKey ) ) {
						$home    = new \Page( $homeKey );
						$options = [ $homeKey => $home->title() ];
					}
				} catch ( Exception $e) {
					// Continue.
				}
				echo Bootstrap :: formSelect( [
					'name'     => 'homepage',
					'label'    => lang()->g( 'Home Page' ),
					'options'  => $options,
					'selected' => false,
					'class'    => '',
					'tip'      => lang()->g( 'Set a static page for the home page rather than the posts index.' )
				] );
				?>
				<script>
					$(document).ready( function() {
						var homepage = $( '#jshomepage' ).select2( {
							placeholder : "<?php lang()->p( 'Start typing to see a list of suggestions.' ) ?>",
							allowClear : true,
							theme : 'bootstrap4',
							minimumInputLength : 2,
							ajax : {
								url : HTML_PATH_ADMIN_ROOT + 'ajax/get-published',
								data: function(params) {
									var query = {
										query : params.term
									}
									return query;
								},
								processResults: function(data) {
									return data;
								}
							},
							escapeMarkup: function(markup) {
								return markup;
							}
						});
					});
				</script>

				<?php
				// Page not found 404.
				try {
					$options = [];
					$pageNotFoundKey = site()->pageNotFound();
					if ( ! empty( $pageNotFoundKey ) ) {
						$pageNotFound = new \Page( $pageNotFoundKey );
						$options      = [ $pageNotFoundKey => $pageNotFound->title() ];
					}
				} catch ( Exception $e) {
					// Continue.
				}
				echo Bootstrap :: formSelect( [
					'name'     => 'pageNotFound',
					'label'    => lang()->g( 'Page Not Found' ),
					'options'  => $options,
					'selected' => false,
					'class'    => '',
					'tip'      => lang()->g( 'Page to display on 404 error, URL not found.' )
				] );
				?>

				<script>
				$(document).ready( function() {
					var homepage = $( '#jspageNotFound' ).select2( {
						placeholder : "<?php lang()->p( 'Start typing to see a list of suggestions.' ) ?>",
						allowClear : true,
						theme : 'bootstrap4',
						minimumInputLength : 2,
						ajax : {
							url : HTML_PATH_ADMIN_ROOT + 'ajax/get-published',
							data: function(params) {
								var query = {
									query : params.term
								}
								return query;
							},
							processResults: function(data) {
								return data;
							}
						},
						escapeMarkup: function(markup) {
							return markup;
						}
					});
				});
				</script>

				<?php
				echo Bootstrap :: formInputText( [
					'name'        => 'uriPage',
					'label'       => lang()->g( 'Pages' ),
					'value'       => site()->uriFilters( 'page' ),
					'class'       => '',
					'placeholder' => '',
					'tip'         => DOMAIN_PAGES
				] );

				echo Bootstrap :: formInputText( [
					'name'        => 'uriTag',
					'label'       => lang()->g( 'Tags' ),
					'value'       => site()->uriFilters( 'tag' ),
					'class'       => '',
					'placeholder' => '',
					'tip'         => DOMAIN_TAGS
				] );

				echo Bootstrap :: formInputText( [
					'name'        => 'uriCategory',
					'label'       => lang()->g( 'Category' ),
					'value'       => site()->uriFilters( 'category' ),
					'class'       => '',
					'placeholder' => '',
					'tip'         => DOMAIN_CATEGORIES
				] );

				echo Bootstrap :: formInputText( [
					'name'        => 'uriBlog',
					'label'       => lang()->g( 'Blog' ),
					'value'       => site()->uriFilters( 'blog' ),
					'class'       => '',
					'placeholder' => '',
					'tip'         => DOMAIN . site()->uriFilters( 'blog' ),
					'disabled'    => \Text :: isEmpty( site()->uriFilters( 'blog' ) )
				] );

				echo Bootstrap :: formSelect( [
					'name'     => 'extremeFriendly',
					'label'    => lang()->g( 'Unicode' ),
					'options'  => [
						'true'  => lang()->g( 'Enabled' ),
						'false' => lang()->g( 'Disabled' )
					],
					'selected' => ( site()->extremeFriendly() ? 'true' : 'false' ),
					'class'    => '',
					'tip'      => lang()->g( 'Allow unicode characters in the URL and some part of the system.' )
				] );
				?>
			</div>

			<div class="tab-pane fade" id="images" role="tabpanel" aria-labelledby="images-tab">

				<h2><?php lang()->p( 'Image Settings' ); ?></h2>

				<?php
				echo Bootstrap :: formInputText( [
					'name'        => 'thumbnailWidth',
					'label'       => lang()->g( 'Width' ),
					'value'       => site()->thumbnailWidth(),
					'class'       => '',
					'placeholder' => '',
					'tip'         => lang()->g( 'Thumbnail width in pixels' )
				] );

				echo Bootstrap :: formInputText( [
					'name'        => 'thumbnailHeight',
					'label'       => lang()->g( 'Height' ),
					'value'       => site()->thumbnailHeight(),
					'class'       => '',
					'placeholder' => '',
					'tip'         => lang()->g( 'Thumbnail height in pixels' )
				] );

				echo Bootstrap :: formInputText( [
					'name'        => 'thumbnailQuality',
					'label'       => lang()->g( 'Quality' ),
					'value'       => site()->thumbnailQuality(),
					'class'       => '',
					'placeholder' => '',
					'tip'         => lang()->g( 'Thumbnail quality in percentage' )
				] );
				?>

				<h2><?php lang()->p( 'Site Logo' ); ?></h2>

				<div class="container">
					<div class="row">
						<div class="col-lg-4 col-sm-12 p-0 pr-2">
							<div class="custom-file">

								<input id="jssiteLogoInputFile" class="custom-file-input" type="file" name="inputFile" />

								<label for="jssiteLogoInputFile" class="custom-file-label"><?php lang()->p( 'Upload Image' ); ?></label>
							</div>
							<button id="jsbuttonRemoveLogo" type="button" class="btn btn-primary w-100 mt-4 mb-4"><?php lang()->p( 'Remove logo' ); ?></button>
						</div>
						<div class="col-lg-8 col-sm-12 p-0 text-center">
							<img id="jssiteLogoPreview" class="img-fluid img-thumbnail" alt="Site logo preview" src="<?php echo ( site()->logo() ? DOMAIN_UPLOADS . site()->logo( false ) . '?version=' . time() : HTML_PATH_CORE_IMG . 'default.svg' ); ?>" />
						</div>
					</div>
				</div>
				<script>
					$( '#jsbuttonRemoveLogo' ).on( 'click', function() {
						bluditAjax.removeLogo();
						$( '#jssiteLogoPreview' ).attr( 'src', "<?php echo HTML_PATH_CORE_IMG . 'default.svg'; ?>" );
					});

					$( '#jssiteLogoInputFile' ).on( 'change', function() {
						var formData = new FormData();
						formData.append( 'tokenCSRF', tokenCSRF );
						formData.append( 'inputFile', $(this)[0].files[0] );
						$.ajax({
							url : HTML_PATH_ADMIN_ROOT + 'ajax/logo-upload',
							type : "POST",
							data : formData,
							cache : false,
							contentType : false,
							processData : false
						}).done( function(data) {
							if ( data.status == 0 ) {
								$( '#jssiteLogoPreview' ).attr( 'src', data.absoluteURL + "?time=" + Math.random() );
							} else {
								showAlert( data.message );
							}
						});
					});
				</script>
			</div>

			<div class="tab-pane fade" id="meta" role="tabpanel" aria-labelledby="meta-tab">

				<h2><?php lang()->p( 'Title Formats' ); ?></h2>

				<?php
				echo Bootstrap :: formInputText( [
					'name'        => 'titleFormatHomepage',
					'label'       => lang()->g( 'Home Page' ),
					'value'       => site()->titleFormatHomepage(),
					'class'       => '',
					'placeholder' => '',
					'tip'         => lang()->g( 'Variables allowed' ) . ' <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>'
				] );

				echo Bootstrap :: formInputText( [
					'name'        => 'titleFormatPages',
					'label'       => lang()->g( 'Pages' ),
					'value'       => site()->titleFormatPages(),
					'class'       => '',
					'placeholder' => '',
					'tip'         => lang()->g( 'Variables allowed' ) . ' <code>{{page-title}}</code> <code>{{page-description}}</code> <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>'
				] );

				echo Bootstrap :: formInputText( [
					'name'        => 'titleFormatCategory',
					'label'       => lang()->g( 'Category' ),
					'value'       => site()->titleFormatCategory(),
					'class'       => '',
					'placeholder' => '',
					'tip'         => lang()->g( 'Variables allowed' ) . ' <code>{{category-name}}</code> <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>'
				] );

				echo Bootstrap :: formInputText( [
					'name'        => 'titleFormatTag',
					'label'       => lang()->g( 'Tag' ),
					'value'       => site()->titleFormatTag(),
					'class'       => '',
					'placeholder' => '',
					'tip'         => lang()->g( 'Variables Allowed' ) . ' <code>{{tag-name}}</code> <code>{{site-title}}</code> <code>{{site-slogan}}</code> <code>{{site-description}}</code>'
				] );
				?>
			</div>

			<div class="tab-pane fade" id="custom-fields" role="tabpanel" aria-labelledby="custom-fields-tab">

				<h2><?php lang()->p( 'Custom Fields' ); ?></h2>

				<?php
				echo Bootstrap :: formTextarea( [
					'name'        => 'customFields',
					'label'       => 'JSON Format',
					'value'       => json_encode( site()->customFields(), JSON_PRETTY_PRINT ),
					'class'       => '',
					'placeholder' => '',
					'tip'         => lang()->g( 'define-custom-fields-for-the-content' ),
					'rows'        => 15
				] );
				?>
			</div>
		</div>

		<div class="form-actions admin-form-actions">
			<button type="submit" class="btn btn-primary" name="save"><?php lang()->p( 'Save' ); ?></button>

			<a class="btn btn-secondary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'dashboard' ?>" role="button"><?php lang()->p( 'Cancel' ); ?></a>
		</div>
	</div>
</form>

<script>
	// Open current tab after refresh page.
	$( function() {
		$( 'a[data-toggle="tab"]' ).on( 'click', function(e) {
			window.localStorage.setItem( 'activeTab', $( e.target ).attr( 'href' ) );
		});
		var activeTab = window.localStorage.getItem( 'activeTab' );
		if ( activeTab) {
			$( '#nav-tab a[href="' + activeTab + '"]' ).tab( 'show' );
			//window.localStorage.removeItem("activeTab");
		}
	});
</script>
