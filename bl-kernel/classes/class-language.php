<?php
/**
 * Language databases
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Languages
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Language extends dbJSON {

	/**
	 * Data
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $data;

	/**
	 * Database content
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $db;

	/**
	 * Dates
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $dates;

	/**
	 * Current language
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $currentLanguage;

	/**
	 * Unicode characters
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $unicodeChars;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $currentLanguage The current language from setting.
	 * @return self
	 */
	function __construct( $currentLanguage ) {

		$this->data  = [];
		$this->db    = [];
		$this->dates = [];
		$this->currentLanguage = $currentLanguage;
		$this->unicodeChars    = [];

		// Load default language.
		$filename = PATH_LANGUAGES.DEFAULT_LANGUAGE_FILE;
		if ( \Sanitize :: pathFile( $filename ) ) {
			$Tmp = new \dbJSON($filename, false );
			$this->db = array_merge( $this->db, $Tmp->db );
		}

		// If the user defined a new language replace the content of the default language.
		// If the new dictionary has missing keys this are going to take from the default language.
		$filename = PATH_LANGUAGES.$currentLanguage.'.json';
		if ( \Sanitize :: pathFile( $filename ) && ( DEFAULT_LANGUAGE_FILE !== $currentLanguage . '.json' ) ) {
			$Tmp = new \dbJSON( $filename, false );
			$this->db = array_merge( $this->db, $Tmp->db );
		}

		// Language-data.
		$this->data = $this->db['language-data'];
		unset( $this->db['language-data'] );

		// Dates.
		if ( isset( $this->db['dates'] ) ) {
			$this->dates = $this->db['dates'];
			unset( $this->db['dates'] );
		}

		// Unicode chars.
		if ( isset( $this->db['unicode-chars'] ) ) {
			$this->unicodeChars = $this->db['unicode-chars'];
			unset( $this->db['unicode-chars'] );
		}
	}

	/**
	 * Locale
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function locale() {

		if ( isset( $this->data['locale'] ) ) {
			return $this->data['locale'];
		}
		return $this->currentLanguage;
	}

	/**
	 * Current language
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function currentLanguage() {
		return $this->currentLanguage;
	}

	/**
	 * Current language, short version
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function currentLanguageShortVersion() {
		$current = $this->currentLanguage;
		$explode = explode( '_', $current );
		return $explode[0];
	}

	/**
	 * Get translation
	 *
	 * Gets the translation to be printed or echoed later.
	 *
	 * If the translation doesn't exist returns English.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get( $string ) {

		$key = Text :: lowercase( $string );
		$key = Text :: replace( ' ', '-', $key );
		$key = Text :: replace( '.', '', $key );

		// file_put_contents(PATH_DEBUG, $key.PHP_EOL, FILE_APPEND);

		if ( isset( $this->db[$key] ) ) {
			return $this->db[$key];
		}

		// $line = '"'.$key.'": "'.$string.'",';
		// file_put_contents(PATH_DEBUG, $line.PHP_EOL, FILE_APPEND);
		return $string;
	}

	/**
	 * Alias of $this->get()
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $string
	 * @return string
	 */
	public function g( $string ) {
		return $this->get( $string );
	}

	/**
	 * Print translation
	 *
	 * Echoes the string of $this->get()
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function printMe( $string ) {
		echo $this->get( $string );
	}

	/**
	 * Alias of $this->printMe()
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $string
	 * @return string
	 */
	public function p( $string ) {
		echo $this->get( $string );
	}

	/**
	 * Add translation
	 *
	 * Add `'keys' => 'values'` to the current dictionary.
	 *
	 * This method does not overwrite the current value.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $array
	 * @return array
	 */
	public function add( $array ) {
		$this->db = array_merge( $this->db, $array );
	}

	/**
	 * Get language list
	 *
	 * Returns an array with all dictionaries.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function getLanguageList() {

		$files = \Filesystem :: listFiles( PATH_LANGUAGES, '*', 'json' );
		$tmp   = [];

		foreach ( $files as $file ) {

			$t = new \dbJSON( $file, false );
			if ( isset( $t->db['language-data']['native'] ) ) {
				$native = $t->db['language-data']['native'];
				$locale = basename( $file, '.json' );
				$tmp[$locale] = $native;
			}
		}
		return $tmp;
	}

	/**
	 * Get dates
	 *
	 * Returns array with all the dates and months.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function getDates() {
		return $this->dates;
	}

	/**
	 * Get Unicode characters
	 *
	 * Returns array with all the special characters from this language.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function getunicodeChars() {
		return $this->unicodeChars;
	}
}
