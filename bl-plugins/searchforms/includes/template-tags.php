<?php
/**
 * Template tags
 *
 * @package    Search Forms
 * @subpackage Core
 * @category   Frontend
 * @since      1.0.0
 */

namespace SearchForms;

if ( ! defined( 'BLUDIT' ) ) {
	die( 'The Search Forms plugin can not be accessed.' );
}

/**
 * Search form
 *
 * Use in theme files as `SearchForms\form()`
 *
 * @example Override defaults:
 * ```
 * $searchform = [
 *     'label_wrap'  => 'h3',
 *     'placeholder' => $L->get( "Find this…" ),
 *     'button_text' => $L->get( 'Go' )
 * ];
 * echo SearchForms\form( $searchform );
 * ```
 *
 * @since  1.0.0
 * @param  mixed $args Arguments to be passed.
 * @param  array $defaults Default arguments.
 * @global object $L Language class.
 * @return string Returns the form markup.
 */
function form( $args = null, $defaults = [] ) {

	if ( ! class_exists( 'Search_Forms' ) ) {
		return null;
	}

	// Access global variables.
	global $L;

	// Get the plugin object.
	$plugin = new \Search_Forms;

	// Create a unique form ID.
	$form_id = uniqid( 'search_' );

	// Get minimum search characters.
	$min_chars = $plugin->min_chars();

	// Default arguments.
	$defaults = [
		'wrap'         => true,
		'wrap_class'   => 'form-wrap search-form-wrap',
		'form_class'   => 'form search-form',
		'label'        => $L->get( 'Search' ),
		'label_wrap'   => 'h2',
		'placeholder'  => $L->get( "Enter at least {$min_chars} characters." ),
		'button'       => true,
		'button_text'  => $L->get( 'Submit' ),
		'button_class' => 'button search-submit-button'
	];

	// Maybe override defaults.
	if ( is_array( $args ) && $args ) {
		$args = array_merge( $defaults, $args );
	} else {
		$args = $defaults;
	}

	// Heading element.
	$label_wrap_open  = '';
	$label_wrap_close = '';
	if ( $args['label_wrap'] ) {

		// Allow for nested tags.
		$get_open  = str_replace( ',', '><', $args['label_wrap'] );
		$get_close = str_replace( ',', '></', $args['label_wrap'] );

		$label_wrap_open  = "<{$get_open}>";
		$label_wrap_close = "</{$get_close}>";
	}

	// List markup.
	$html = '';
	if ( $args['wrap'] ) {
		$html .= "<div class='{$args['wrap_class']}'>";
	}

	if ( ! empty( $args['label'] ) ) {
		$html .= sprintf(
			'%s<label for="%s">%s</label>%s',
			strtolower( $label_wrap_open ),
			$form_id,
			$args['label'],
			strtolower( $label_wrap_close )
		);
	}
	$html .= "<form class='{$args['form_class']}' role='search'>";
	$html .= sprintf(
		'<input type="search" id="%s" name="%s" placeholder="%s" />',
		$form_id,
		$form_id,
		$args['placeholder']
	);

	if ( $args['button'] ) {
		$html .= sprintf(
			'<input type="button" id="%s" class="%s" value="%s" onClick="%s" />',
			$form_id . '_submit',
			$args['button_class'],
			$args['button_text'],
			"{$form_id}_action()"
		);
	}

	$html .= '</form>';
	if ( $args['wrap'] ) {
		$html .= '</div>';
	}

	// Form script.
	$base = DOMAIN_BASE;
	$html .= "<script>function {$form_id}_action(){var text=document.getElementById('{$form_id}').value;window.open('{$base}'+'search/'+text, '_self');return false;}document.getElementById('{$form_id}').onkeypress=function(e){if(!e)e=window.event;var keyCode=e.keyCode||e.which;if(keyCode=='13'){{$form_id}_action();return false;}}</script>";

	return $html;
}

/**
 * Sidebar search
 *
 * @since  1.0.0
 * @return string Returns the form markup.
 */
function sidebar_search() {

	// Get the plugin object.
	$plugin = new \Search_Forms;

	// Override default function arguments.
	$args = [
		'wrap_class' => 'form-wrap search-form-wrap plugin plugin-search sidebar-search',
		'form_class' => 'form search-form plugin-content'
	];

	if ( ! $plugin->wrap() ) {
		$args = array_merge( $args, [ 'wrap' => false ] );
	}

	if ( ! $plugin->label() ) {
		$args = array_merge( $args, [ 'label' => false ] );
	} elseif ( $plugin->label() ) {
		$args = array_merge( $args, [ 'label' => $plugin->label() ] );
	}

	if ( ! $plugin->label_wrap() ) {
		$args = array_merge( $args, [ 'label_wrap' => false ] );
	} elseif ( $plugin->label_wrap() ) {
		$args = array_merge( $args, [ 'label_wrap' => $plugin->label_wrap() ] );
	}

	if ( ! $plugin->placeholder() ) {
		$args = array_merge( $args, [ 'placeholder' => false ] );
	} elseif ( $plugin->placeholder() ) {
		$args = array_merge( $args, [ 'placeholder' => $plugin->placeholder() ] );
	}

	if ( ! $plugin->button() ) {
		$args = array_merge( $args, [ 'button' => false ] );
	}

	if ( ! $plugin->button_text() ) {
		$args = array_merge( $args, [ 'button_text' => false ] );
	} elseif ( $plugin->button_text() ) {
		$args = array_merge( $args, [ 'button_text' => $plugin->button_text() ] );
	}

	if ( ! $plugin->button_class() ) {
		$args = array_merge( $args, [ 'button_class' => '' ] );
	} elseif ( $plugin->button_class() ) {
		$args = array_merge( $args, [ 'button_class' => $plugin->button_class() ] );
	}

	// Return a modified search form.
	return form( $args );
}
