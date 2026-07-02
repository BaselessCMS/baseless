<?php
/**
 * Plugin parent class
 *
 * Extend this class to develop a plugin for this CMS.
 *
 * @package    Baseless
 * @subpackage Classes
 * @category   Extend
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Plugin {

	/**
	 * Directory name
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $directoryName;

	// (string) Absolute database filename and path
	// Ex: /www/bludit/bl-content/plugins/sitemap/db.php
	/**
	 * Database filename
	 *
	 * Absolute database filename and path.
	 * @example /www/bludit/bl-content/plugins/sitemap/db.php
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $filenameDb;

	/**
	 * Metadata filename
	 *
	 * Absolute metadata filename and path.
	 * @example /www/bludit/bl-plugins/sitemap/metadata.json
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $filenameMetadata;

	/**
	 * Plugin metadata
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $metadata;

	/**
	 * Class name
	 *
	 * The name of the plugin;s core PHP class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $className;

	/**
	 * Database unserialized
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $db;

	/**
	 * Database fields, only for initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $dbFields;

	/**
	 * Form buttons
	 *
	 * Enable or disable default Save and Cancel
	 * button on plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $formButtons;

	/**
	 * Custom hooks
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $customHooks;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		$this->dbFields    = [];
		$this->customHooks = [];

		$reflector = new \ReflectionClass( get_class( $this ) );

		$this->directoryName = basename( dirname( $reflector->getFileName() ) );
		$this->className     = $reflector->getName();
		$this->formButtons   = true;

		// Call the method init() from the children.
		$this->init();

		// Init empty database with default values.
		$this->db = $this->dbFields;
		$this->filenameDb = PATH_PLUGINS_DATABASES . $this->directoryName . DS . 'db.php';

		// Plugin metadata.
		$this->filenameMetadata = PATH_PLUGINS . $this->directoryName() . DS . 'metadata.json';
		$metadataString = file_get_contents( $this->filenameMetadata );
		$this->metadata = json_decode( $metadataString, true );

		// If the plugin is installed then get the database.
		if ( $this->installed() ) {
			$Tmp = new \dbJSON( $this->filenameDb );
			$this->db = $Tmp->db;
			$this->prepare();
		}
	}

	/**
	 * Plugin compatibility
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function isCompatible() {

		$cms_root   = explode( '.', CMS_VERSION );
		$compatible = explode( ',', $this->getMetadata( 'compatible' ) );
		foreach ( $compatible as $version ) {
			$root = explode( '.', $version );
			if ( $root[0] == $cms_root[0] && $root[1] == $cms_root[1] ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Include PHP files
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_files() {

		// Plugin path.
		$path = PATH_PLUGINS . __CLASS__ . DS;

		// Get plugin functions.
		foreach ( glob( $path . 'includes/*.php' ) as $filename ) {
			require_once $filename;
		}
	}

	/**
	 * Initialize plugin
	 *
	 * This method is used in child classes.
	 * One primary use is to define database fields.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function init() {}

	/**
	 * Prepare for installation
	 *
	 * This method is used in child classes.
	 * One primary use may be to include files.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function prepare() {
		$this->get_files();
	}

	/**
	 * Plugin position
	 *
	 * Starting the position of plugin output in
	 * frontend sidebars; position in relation
	 * to other plugins.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  integer $position
	 * @return void
	 */
	public function setPosition( $position ) {
		return $this->setField( 'position', $position );
	}

	/**
	 * Install plugin
	 *
	 * Return true if the installation is successful.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $position
	 * @return boolean
	 */
	public function install( $position = 1 ) {

		if ( $this->installed() ) {
			return false;
		}

		// Create workspace.
		$workspace = $this->workspace();
		mkdir( $workspace, DIR_PERMISSIONS, true );

		// Create plugin directory for the database.
		mkdir( PATH_PLUGINS_DATABASES . $this->directoryName, DIR_PERMISSIONS, true );

		$this->dbFields['position'] = $position;

		// Sanitize default values to store in the file.
		foreach ( $this->dbFields as $key => $value ) {

			if ( is_array( $value ) ) {
				$array_fields = [];
				foreach ( $value as $array_field ) {
					if ( is_int( $array_field ) ) {
						$array_field = Sanitize :: int( $array_field );
					} elseif ( is_string( $array_field ) ) {
						$array_field = Sanitize :: html( $array_field );
					}
					$array_fields[] = $array_field;
				}
				$value = $array_fields;
			} elseif ( is_int( $value ) ) {
				$value = Sanitize :: int( $value );
			} else {
				$value = Sanitize :: html( $value );
			}

			settype( $value, gettype( $this->dbFields[$key] ) );
			$this->db[$key] = $value;
		}

		// Create the database.
		return $this->save();
	}

	/**
	 * Plugin is installed
	 *
	 * Returns true if the plugin is installed.
	 * This function checks if the database
	 * of the plugin is created.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function installed() {
		return file_exists( $this->filenameDb );
	}

	/**
	 * Save plugin database
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function save() {
		$tmp = new \dbJSON( $this->filenameDb );
		$tmp->db = $this->db;
		return $tmp->save();
	}

	/**
	 * Post form method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function post() {

		$args = $_POST;

		foreach ( $this->dbFields as $field => $value ) {

			if ( isset( $args[$field] ) ) {

				if ( is_array( $args[$field] ) ) {
					$array_fields = [];
					foreach ( $args[$field] as $array_field ) {
						if ( is_int( $array_field ) ) {
							$array_field = Sanitize :: int( $array_field );
						} elseif ( is_string( $array_field ) ) {
							$array_field = Sanitize :: html( $array_field );
						}
						$array_fields[] = $array_field;
					}
					$value = $array_fields;
				} elseif ( is_int( $args[$field] ) ) {
					$value = Sanitize :: int( $args[$field] );
				} else {
					$value = Sanitize :: html( $args[$field] );
				}

				if ( 'false' === $value ) {
					$value = false;
				} elseif ( 'true' === $value ) {
					$value = true;
				}

				settype( $value, gettype( $value ) );
				$this->db[$field] = $value;
			}
		}
		return $this->save();
	}

	/**
	 * Set field
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $field
	 * @param  mixed $value
	 * @return void
	 */
	public function setField( $field, $value ) {
		$this->db[$field] = \Sanitize :: html( $value );
		return $this->save();
	}

	/**
	 * Plugin URL
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string
	 */
	protected function plugin_url() {
		return HTML_PATH_ADMIN_ROOT . 'configure-plugin/' . __CLASS__;
	}

	/**
	 * URL slug
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string
	 */
	protected function plugin_slug() {
		return 'configure-plugin/' . __CLASS__;
	}

	/**
	 * Directory name
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string
	 */
	public function directoryName() {
		return $this->directoryName;
	}

	/**
	 * Domain path
	 *
	 * Returns absolute URL and path of the plugin directory.
	 * This function helps to include CSS or Javascript
	 * files with absolute URL.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string
	 */
	public function domainPath() {
		return DOMAIN_PLUGINS . $this->directoryName . '/';
	}

	/**
	 * HTML path
	 *
	 * Returns relative path of the plugin directory.
	 * This function helps to include CSS or Javascript
	 * files with relative URL.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string
	 */
	public function htmlPath() {
		return HTML_PATH_PLUGINS . $this->directoryName . '/';
	}

	/**
	 * PHP path
	 *
	 * Returns absolute path of the plugin directory.
	 * This function helps to include PHP libraries
	 * or some file at server level.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string
	 */
	public function phpPath() {
		return PATH_PLUGINS . $this->directoryName . DS;
	}

	/**
	 * Database path
	 *
	 * Returns absolute path of the plugin database directory.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string
	 */
	public function phpPathDB() {
		return PATH_PLUGINS_DATABASES . $this->directoryName . DS;
	}

	/**
	 * Workspace path
	 *
	 * Returns absolute path of the plugin workspace directory.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string
	 */
	public function workspace() {
		return PATH_WORKSPACES . $this->directoryName . DS;
	}

	/**
	 * Get metadata
	 *
	 * Returns the value of the key from the metadata of the plugin,
	 * false if the key doesn't exist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return mixed
	 */
	public function getMetadata( $key ) {

		if ( isset( $this->metadata[$key] ) ) {
			return $this->metadata[$key];
		}
		return false;
	}

	/**
	 * Set metadata
	 *
	 * Set a key/value on the metadata of the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @param  mixed $value
	 * @return boolean
	 */
	public function setMetadata( $key, $value ) {
		$this->metadata[$key] = $value;
		return true;
	}

	/**
	 * Get field value
	 *
	 * Returns the value of the field from the database.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @param  boolean $html True returns the value sanitized,
	 *                       false not sanitized.
	 * @return mixed
	 */
	public function getValue( $field, $html = true ) {

		if ( isset( $this->db[$field] ) ) {
			if ( $html ) {
				return $this->db[$field];
			} else {
				return \Sanitize :: htmlDecode( $this->db[$field] );
			}
		}
		return $this->dbFields[$field];
	}

	/**
	 * Plugin position
	 *
	 * @since  1.0.0
	 * @access public
	 * @return integer
	 */
	public function position() {
		return $this->getValue( 'position' );
	}

	/**
	 * Plugin label
	 *
	 * From the plugin's JSON metadata file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function label() {
		return $this->getMetadata( 'label' );
	}

	/**
	 * Plugin type
	 *
	 * From the plugin's JSON metadata file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function type() {
		return $this->getMetadata( 'type' );
	}

	/**
	 * Plugin name
	 *
	 * From the plugin's JSON metadata file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function name() {
		return $this->getMetadata( 'name' );
	}

	/**
	 * Plugin description
	 *
	 * From the plugin's JSON metadata file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function description() {
		return $this->getMetadata( 'description' );
	}

	/**
	 * Plugin author
	 *
	 * From the plugin's JSON metadata file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function author() {
		return $this->getMetadata( 'author' );
	}

	/**
	 * Plugin email
	 *
	 * From the plugin's JSON metadata file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function email() {
		return $this->getMetadata( 'email' );
	}

	/**
	 * Plugin website
	 *
	 * From the plugin's JSON metadata file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function website() {
		return $this->getMetadata( 'website' );
	}

	/**
	 * Plugin version
	 *
	 * From the plugin's JSON metadata file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function version() {
		return $this->getMetadata( 'version' );
	}

	/**
	 * Plugin release date
	 *
	 * From the plugin's JSON metadata file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function releaseDate() {
		return $this->getMetadata( 'releaseDate' );
	}

	/**
	 * Plugin class name
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function className() {
		return $this->className;
	}

	/**
	 * Form buttons
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function formButtons() {
		return $this->formButtons;
	}

	/**
	 * Get stylesheet
	 *
	 * Returns a link meta tag for a CSS stylesheet or null
	 * if not found. Looks for `/assets/css/filename`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $filename The stylesheet filename (with extension).
	 * @param  boolean $echo Whether to echo or return the tag markup.
	 * @return mixed
	 */
	public function get_css( $filename, $echo = true ) {

		// If the stylesheet is not found.
		if ( ! file_exists( $this->htmlPath() . 'assets/css/' . $filename ) ) {
			return null;
		}

		$tag = '<link rel="stylesheet" type="text/css" href="' . $this->domainPath() . 'assets/css/' . $filename . '?version=' . CMS_VERSION . '">' . PHP_EOL;

		if ( ! $echo ) {
			return $tag;
		} else {
			echo $tag;
		}
	}

	/**
	 * Get JavaScript
	 *
	 * Returns a link meta tag for a JavaScript file or null
	 * if not found. Looks for `/assets/js/filename`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $filename The script filename (with extension).
	 * @param  boolean $echo Whether to echo or return the tag markup.
	 * @return mixed
	 */
	public function get_js( $filename, $echo = true ) {

		// If the script is not found.
		if ( ! file_exists( $this->htmlPath() . 'assets/js/' . $filename ) ) {
			return null;
		}

		$tag = '<script charset="utf-8" src="' . $this->domainPath() . 'assets/js/' . $filename . '?version=' . CMS_VERSION . '"></script>' . PHP_EOL;

		if ( ! $echo ) {
			return $tag;
		} else {
			echo $tag;
		}
	}

	/**
	 * Deprecated: include CSS
	 *
	 * Returns a link meta tag for a CSS stylesheet.
	 * Looks for `/css/filename`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $filename The stylesheet filename (with extension).
	 * @return void
	 */
	public function includeCSS( $filename ) {
		return '<link rel="stylesheet" type="text/css" href="' . $this->domainPath() . 'css/' . $filename . '?version=' . CMS_VERSION . '">' . PHP_EOL;
	}

	/**
	 * Deprecated: include JavaScript
	 *
	 * Returns a link meta tag for a JavaScript file.
	 * Looks for `/js/filename`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $filename The script filename (with extension).
	 * @return void
	 */
	public function includeJS( $filename ) {
		return '<script charset="utf-8" src="' . $this->domainPath() . 'js/' . $filename . '?version=' . CMS_VERSION . '"></script>' . PHP_EOL;
	}

	/**
	 * Custom URL
	 *
	 * Returns the parameters after the URI, false if
	 * the URI doesn't match with the webhook.
	 *
	 * @example https://www.mybludit.com/api/foo/bar
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $uri
	 * @param  boolean $return_after
	 * @param  boolean $fixed
	 * @return boolean
	 */
	public function webhook( $uri = false, $return_after = false, $fixed = true ) {

		// Access global variables.
		global $url;

		if ( empty( $uri ) ) {
			return false;
		}

		// Check URI start with the webhook.
		$start  = HTML_PATH_ROOT . $uri;
		$uri    = $url->uri();
		$length = mb_strlen( $start, CHARSET );
		if ( mb_substr( $uri, 0, $length ) != $start ) {
			return false;
		}

		$after_uri = mb_substr( $uri, $length );
		if ( ! empty( $after_uri ) ) {
			if ( $fixed ) {
				return false;
			}
			if ( $after_uri[0] != '/' ) {
				return false;
			}
		}

		if ( $return_after ) {
			return $after_uri;
		}

		\Log :: set( __METHOD__ . LOG_SEP . 'Webhook requested.' );
		return true;
	}

	/**
	 * Uninstall plugin
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function uninstall() {

		// Delete database.
		$path = PATH_PLUGINS_DATABASES . $this->directoryName;
		\Filesystem :: deleteRecursive( $path );

		// Delete workspace.
		$workspace = $this->workspace();
		\Filesystem :: deleteRecursive( $workspace );

		return true;
	}
}
