<?php
/**
 * Search Forms options
 *
 * @package    Search Forms
 * @subpackage Views
 * @category   Forms
 * @since      1.0.0
 */

// Add class class to 'js' to `<body>` if JavaScript is enabled.
echo "<script>var bodyClass = document.body;bodyClass.classList ? bodyClass.classList.add('js') : bodyClass.className += ' js';</script>\n";

// Guide page URL.
$guide_page = DOMAIN_ADMIN . 'plugin/Search_Forms';

?>
<style>
.form-control-has-button {
	display: flex;
	align-items: center;
	flex-wrap: nowrap;
	gap: 0.25em;
	width: 100%;
	margin: 0;
	padding: 0;
}
</style>
<div class="alert alert-primary alert-search-forms" role="alert">
	<p class="m-0"><?php $L->p( "Go to the <a href='{$guide_page}'>search forms guide</a> page." ); ?></p>
</div>
<div id="search-form-options">

	<fieldset>
		<legend class="screen-reader-text"><?php $L->p( 'Search Form Options' ); ?></legend>

		<?php echo Bootstrap :: formTitle( [ 'element' => 'h3', 'title' => $L->g( 'General Search Settings' ) ] ); ?>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="min_chars"><?php $L->p( 'Minimum Characters' ); ?></label>
			<div class="col-sm-10 row form-range-row">
				<div class="form-range-controls">
					<span class="form-range-value ch-range-value"><span id="min_chars_value"><?php echo $this->getValue( 'min_chars' ); ?></span></span>
					<input type="range" class="form-control-range" onInput="$('#min_chars_value').html($(this).val())" id="min_chars" name="min_chars" value="<?php echo $this->getValue( 'min_chars' ); ?>" min="0" max="10" step="1" />
					<span class="btn btn-secondary btn-md form-range-button hide-if-no-js" onClick="$('#min_chars_value').text('<?php echo $this->dbFields['min_chars']; ?>');$('#min_chars').val('<?php echo $this->dbFields['min_chars']; ?>');"><?php $L->p( 'Default' ); ?></span>
				</div>
				<small class="form-text text-muted form-range-small"><?php $L->p( 'Minimum number of characters for search results.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="cache_words"><?php $L->p( 'Cached Words' ); ?></label>
			<div class="col-sm-10 row form-range-row">
				<div class="form-range-controls">
					<span class="form-range-value ch-range-value"><span id="cache_words_value"><?php echo $this->getValue( 'cache_words' ); ?></span></span>
					<input type="range" class="form-control-range" onInput="$('#cache_words_value').html($(this).val())" id="cache_words" name="cache_words" value="<?php echo $this->getValue( 'cache_words' ); ?>" min="100" max="2000" step="100" />
					<span class="btn btn-secondary btn-md form-range-button hide-if-no-js" onClick="$('#cache_words_value').text('<?php echo $this->dbFields['cache_words']; ?>');$('#cache_words').val('<?php echo $this->dbFields['cache_words']; ?>');"><?php $L->p( 'Default' ); ?></span>
				</div>
				<small class="form-text text-muted form-range-small"><?php $L->p( 'Number of words per result to cache.' ); ?></small>
			</div>
		</div>

		<?php echo Bootstrap :: formTitle( [ 'element' => 'h3', 'title' => $L->g( 'Sidebar Search Settings' ) ] ); ?>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="in_sidebar"><?php $L->p( 'Sidebar Form' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="in_sidebar" name="in_sidebar">
					<option value="true" <?php echo ( $this->getValue( 'in_sidebar' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>
					<option value="false" <?php echo ( $this->getValue( 'in_sidebar' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>
				</select>
				<small class="form-text text-muted"><?php $L->p( 'Display a search form in the standard frontend sidebar area, if the active theme supports it.' ); ?></small>
			</div>
		</div>

		<div id="sidebar_options" style="display: <?php echo ( $this->in_sidebar() ? 'block' : 'none' ); ?>;">

			<div class="form-field form-group row">
				<label class="form-label col-sm-2 col-form-label" for="wrap"><?php $L->p( 'Form Wrap' ); ?></label>
				<div class="col-sm-10">
					<select class="form-select" id="wrap" name="wrap">
						<option value="true" <?php echo ( $this->getValue( 'wrap' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>
						<option value="false" <?php echo ( $this->getValue( 'wrap' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>
					</select>
					<small class="form-text text-muted"><?php $L->p( 'Use the standard HTML for sidebar plugins.' ); ?></small>
				</div>
			</div>

			<div class="form-field form-group row">
				<label class="form-label col-sm-2 col-form-label" for="label"><?php $L->p( 'Form Label' ); ?></label>
				<div class="col-sm-10">
					<div class="form-control-has-button">
						<input type="text" id="label" name="label" value="<?php echo $this->getValue( 'label' ); ?>" placeholder="<?php echo $this->dbFields['label']; ?>" />
						<span class="btn btn-secondary btn-md button hide-if-no-js" onClick="$('#label').val('<?php echo $this->dbFields['label']; ?>');"><?php $L->p( 'Default' ); ?></span>
					</div>
					<small class="form-text text-muted"><?php $L->p( 'Text of the form label. Save as blank to hide the label.' ); ?></small>
				</div>
			</div>

			<div class="form-field form-group row">
				<label class="form-label col-sm-2 col-form-label" for="label_wrap"><?php $L->p( 'Label Wrap' ); ?></label>
				<div class="col-sm-10">
					<div class="form-control-has-button">
						<input type="text" id="label_wrap" name="label_wrap" value="<?php echo $this->getValue( 'label_wrap' ); ?>" placeholder="<?php $L->p( 'h2' ); ?>" />
						<span class="btn btn-secondary btn-md button hide-if-no-js" onClick="$('#label_wrap').val('<?php echo $this->dbFields['label_wrap']; ?>');"><?php $L->p( 'Default' ); ?></span>
					</div>
					<small class="form-text text-muted"><?php $L->p( 'Wrap the label in an element, such as a heading. Accepts HTML tags without brackets (e.g. h3), and comma-separated tags (e.g. span,strong,em). Save as blank for no wrapping element.' ); ?></small>
				</div>
			</div>

			<div class="form-field form-group row">
				<label class="form-label col-sm-2 col-form-label" for="placeholder"><?php $L->p( 'Placeholder' ); ?></label>
				<div class="col-sm-10">
					<div class="form-control-has-button">
						<input type="text" id="placeholder" name="placeholder" value="<?php echo $this->getValue( 'placeholder' ); ?>" placeholder="<?php $L->p( 'Submit' ); ?>" />
						<span class="btn btn-secondary btn-md button hide-if-no-js" onClick="$('#placeholder').val('<?php echo $this->dbFields['placeholder']; ?>');"><?php $L->p( 'Default' ); ?></span>
					</div>
					<small class="form-text text-muted"><?php $L->p( 'The placeholder text of the search query input.' ); ?></small>
				</div>
			</div>

			<div class="form-field form-group row">
				<label class="form-label col-sm-2 col-form-label" for="button"><?php $L->p( 'Form Button' ); ?></label>
				<div class="col-sm-10">
					<select class="form-select" id="button" name="button">
						<option value="true" <?php echo ( $this->getValue( 'button' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>
						<option value="false" <?php echo ( $this->getValue( 'button' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>
					</select>
					<small class="form-text text-muted"><?php $L->p( 'Hide the submit button, submit only on Enter key.' ); ?></small>
				</div>
			</div>

			<div id="button_fields_wrap" style="display: <?php echo ( $this->button() ? 'block' : 'none' ); ?>;">

				<div class="form-field form-group row">
					<label class="form-label col-sm-2 col-form-label" for="button_text"><?php $L->p( 'Button Text' ); ?></label>
					<div class="col-sm-10">
						<div class="form-control-has-button">
							<input type="text" id="button_text" name="button_text" value="<?php echo $this->getValue( 'button_text' ); ?>" placeholder="<?php $L->p( 'Submit' ); ?>" />
							<span class="btn btn-secondary btn-md button hide-if-no-js" onClick="$('#button_text').val('<?php echo $this->dbFields['button_text']; ?>');"><?php $L->p( 'Default' ); ?></span>
						</div>
						<small class="form-text text-muted"><?php $L->p( 'The text of the form submit button.' ); ?></small>
					</div>
				</div>

				<div class="form-field form-group row">
					<label class="form-label col-sm-2 col-form-label" for="button_class"><?php $L->p( 'Button Classes' ); ?></label>
					<div class="col-sm-10">
						<div class="form-control-has-button">
							<input type="text" id="button_class" name="button_class" value="<?php echo $this->getValue( 'button_class' ); ?>" placeholder="<?php echo $this->dbFields['button_class']; ?>" />
							<span class="btn btn-secondary btn-md button hide-if-no-js" onClick="$('#button_class').val('<?php echo $this->dbFields['button_class']; ?>');"><?php $L->p( 'Default' ); ?></span>
						</div>
						<small class="form-text text-muted"><?php $L->p( 'The CSS classes of the form submit button.' ); ?></small>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</div>
<script>

// Sidebar form options.
jQuery(document).ready( function($) {
	$( '#in_sidebar' ).on( 'change', function() {
		var show = $(this).val();
		if ( show == 'true' ) {
			$( "#sidebar_options" ).fadeIn( 250 );
			$( 'html, body' ).animate( {
				scrollTop: $( '#sidebar_options' ).offset().top
			}, 1000 );
		} else if ( show == 'false' ) {
			$( "#sidebar_options" ).fadeOut( 250 );
		}
	});
	$( '#button' ).on( 'change', function() {
		var show = $(this).val();
		if ( show == 'true' ) {
			$( "#button_fields_wrap" ).fadeIn( 250 );
			$( 'html, body' ).animate( {
				scrollTop: $( '#button_fields_wrap' ).offset().top
			}, 1000 );
		} else if ( show == 'false' ) {
			$( "#button_fields_wrap" ).fadeOut( 250 );
		}
	});
});
</script>
