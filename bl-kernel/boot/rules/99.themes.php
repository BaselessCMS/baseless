<?php
/**
 * Build themes
 *
 * @package    JSON CMS
 * @subpackage Boot
 * @category   Rules
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Func\{
	get_plugin
};

$themePlugin = get_plugin( $site->theme() );

/**
 * Build themes
 *
 * @since  1.0.0
 * @global object $site The Site class.
 * @return void
 */
function buildThemes() {

	global $site;

	$themes = [];
	$themesPaths = Filesystem :: listDirectories( PATH_THEMES );

	foreach ( $themesPaths as $themePath ) {

		// Check if the theme is translated.
		$languageFilename = $themePath . DS . 'languages' . DS . $site->language() . '.json';

		if ( ! Sanitize :: pathFile( $languageFilename ) ) {
			$languageFilename = $themePath . DS . 'languages' . DS . DEFAULT_LANGUAGE_FILE;
		}

		if ( Sanitize :: pathFile($languageFilename ) ) {

			$database = file_get_contents( $languageFilename );
			$database = json_decode( $database, true );

			if ( empty( $database ) ) {
				Log :: set( '99.themes.php' . LOG_SEP . 'Language file error on theme ' . $themePath );
				break;
			}

			$database = $database['theme-data'];
			$database['dirname'] = basename( $themePath );
			$filenameMetadata = $themePath . DS . 'metadata.json';

			if ( Sanitize :: pathFile( $filenameMetadata ) ) {

				$metadataString = file_get_contents( $filenameMetadata );
				$metadata = json_decode( $metadataString, true );

				$database['compatible'] = false;
				if ( ! empty( $metadata['compatible'] ) ) {

					$cms_root = explode( '.', CMS_VERSION );
					$compatible = explode( ',', $metadata['compatible'] );

					foreach ( $compatible as $version ) {
						$root = explode( '.', $version );
						if ( $root[0] == $cms_root[0] && $root[1] == $cms_root[1] ) {
							$database['compatible'] = true;
						}
					}
				}

				$database = $database + $metadata;
				array_push( $themes, $database );
			}
		}
	}
	return $themes;
}

// Load the language file.
$languageFilename = THEME_DIR . 'languages' . DS . $site->language() . '.json';
if ( ! Sanitize :: pathFile( $languageFilename ) ) {
	$languageFilename = THEME_DIR . 'languages' . DS . DEFAULT_LANGUAGE_FILE;
}

if ( Sanitize :: pathFile( $languageFilename ) ) {
	$database = file_get_contents( $languageFilename );
	$database = json_decode( $database, true );

	// Remote the name and description.
	unset( $database['theme-data'] );

	// Load words from the theme language.
	if ( ! empty( $database ) ) {
		$L->add( $database );
	}
}
