<?php
/**
 * General functions
 *
 * @package    JSON CMS
 * @subpackage Core
 * @category   Functions
 * @since      1.0.0
 */

namespace CMS\Func;

// Import namespaced functions.
use function CMS\Help\{
	cats,
	lang,
	login,
	page,
	pages,
	plugins,
	security,
	site,
	url,
	user,
	users,
};

/**
 * Current language
 *
 * The language from site settings.
 *
 * @since  1.0.0
 * @return string
 */
function current_lang() {
	return lang()->currentLanguageShortVersion();
}

/**
 * Is RTL language
 *
 * @since  1.0.0
 * @param  mixed $langs Arguments to be passed.
 * @param  array $rtl Default arguments.
 * @global object $L The Language class.
 * @return boolean Returns true if site is in RTL language.
 */
function is_rtl( $langs = null, $rtl = [] ) {

	// Access global variables.
	global $L;

	$rtl = [
		'ar',
		'fa',
		'he',
		'ks',
		'ku',
		'pa',
		'ps',
		'sd',
		'ug',
		'ur'
	];

	// Maybe override defaults.
	if ( is_array( $langs ) && $langs ) {
		$langs = array_merge( $rtl, $langs );
	} else {
		$langs = $rtl;
	}

	$current = current_lang();

	if ( is_array( $rtl ) && in_array( $current, $rtl ) ) {
		return true;
	}
	return false;
}

/**
 * Asset file suffix
 *
 * Gets minified file if not in debug mode.
 * Third party (e.g. jQuery) may be exempted.
 *
 * @since  1.0.0
 * @return string Returns an empty string or
 *                `.min` string.
 */
function asset_min() {

	// Get non-minified file if in debug mode.
	if ( defined( 'DEBUG_MODE' ) && DEBUG_MODE ) {
		return '';
	}
	return '.min';
}

/**
 * Get SVG files
 *
 * @since  1.0.0
 * @param  string $filename Name of the SVG file.
 * @param  string $type Directory of SVG icons.
 * @return mixed Returns the contents of the SVG file or
 *               returns null if the filename is not found.
 */
function get_svg_icon( $filename, $type = 'regular' ) {

	// Access global variables.
	global $site;

	$path = PATH_ASSETS . 'images' . DS . 'svg' . DS . 'icons' . DS . $type . DS . $filename . '.svg';
	$args = [
		'svg',
		'g',
		'path'
	];

	if ( is_file( $path ) && is_readable( $path ) ) {

		$file = strip_tags( $path, $args );
		return file_get_contents( $file );

	} else {
		return $path;
	}
}

/**
 * SVG icon
 *
 * Prints the contents of a given SVG file.
 *
 * @since  1.0.0
 * @param  string $filename Name of the SVG file.
 * @param  array $args
 * @return string Returns the icon markup.
 */
function svg_icon( $filename, $args = [] ) {

	$default = [
		'type'  => 'regular',
		'wrap'  => true,
		'echo'  => true,
		'class' => 'svg-icon'
	];
	$args = array_merge( $default, $args );

	if ( true == $args['wrap'] ) {
		$icon = sprintf(
			'<span class="%s">%s</span>',
			$args['class'],
			get_svg_icon( $filename, $args['type'] )
		);
	} else {
		$icon = get_svg_icon( $filename, $args['type'] );
	}

	if ( $args['echo'] ) {
		echo $icon;
	} else {
		return $icon;
	}
}
