<?php
/**
 * Build plugins
 *
 * @package    JSON CMS
 * @subpackage Boot
 * @category   Rules
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

/**
 * Plugin hooks
 *
 * Hooks provided for plugins to extend
 * functionality or print markup.
 */
$plugins = [
	'beforeSiteLoad' => [],
	'afterSiteLoad'  => [],
	'siteHead'       => [],
	'siteBodyBegin'  => [],
	'siteBodyEnd'    => [],

	'pageBegin'    => [],
	'contentBegin' => [],
	'siteContent'  => [],
	'contentEnd'   => [],
	'sidebarBegin' => [],
	'siteSidebar'  => [],
	'sidebarEnd'   => [],
	'pageEnd'      => [],

	'beforeAdminLoad'     => [],
	'afterAdminLoad'      => [],
	'adminHead'           => [],
	'adminBodyBegin'      => [],
	'adminBodyEnd'        => [],
	'adminSidebar'        => [],
	'adminContentSidebar' => [],
	'dashboard'           => [],

	'beforeAll' => [],
	'afterAll'  => [],
	'paginator' => [],

	'afterPageCreate' => [],
	'afterPageModify' => [],
	'afterPageDelete' => [],

	'loginHead'      => [],
	'loginBodyBegin' => [],
	'loginBodyEnd'   => [],

	'all' => []
];

$plugins_installed = [];
$plugins_events    = $plugins;
unset( $plugins_events['all'] );

/**
 * Build plugins
 *
 * @since  1.0.0
 * @global object $L The Language class.
 * @global array  $plugins
 * @global array  $plugins_events
 * @global array  $plugins_installed The Language class.
 * @global object $site The Site class.
 * @return void
 */
function buildPlugins() {

	global $L, $plugins, $plugins_events, $plugins_installed, $site;

	// Get declared classes BEFORE load plugins classes.
	$current_declared = get_declared_classes();

	// List plugins directories.
	$list = Filesystem :: listDirectories( PATH_PLUGINS );

	// Load each plugin classes.
	foreach ( $list as $path ) {

		// Check if the directory has the plugin.php.
		if ( file_exists( $path . DS . 'plugin.php' ) ) {
			include_once( $path . DS . 'plugin.php' );
		}
	}

	// Get plugins classes loaded.
	$plugins_declared = array_diff( get_declared_classes(), $current_declared );

	foreach ( $plugins_declared as $plugin_class ) {

		$plugin = new $plugin_class;

		// Check if the plugin is translated.
		$lang_file = PATH_PLUGINS . $plugin->directoryName() . DS . 'languages' . DS . $site->language() . '.json';
		if ( ! Sanitize :: pathFile( $lang_file ) ) {
			$lang_file = PATH_PLUGINS . $plugin->directoryName() . DS . 'languages' . DS . DEFAULT_LANGUAGE_FILE;
		}

		$database = file_get_contents( $lang_file );
		$database = json_decode( $database, true );

		// Set name and description from the language file.
		$plugin->setMetadata( 'name', $database['plugin-data']['name'] );
		$plugin->setMetadata( 'description', $database['plugin-data']['description'] );

		/**
		 * Remove name and description from the language file
		 * loaded and add new words if there are.
		 *
		 * This function overwrite the key=>value.
		 */
		unset( $database['plugin-data'] );
		if ( ! empty( $database ) ) {
			$L->add( $database );
		}

		// $plugins['all'] Array with all plugins, installed and not installed.
		$plugins['all'][$plugin_class] = $plugin;

		// If the plugin is installed insert on the hooks.
		if ( $plugin->installed() ) {

			// Include custom hooks.
			if ( ! empty( $plugin->customHooks ) ) {
				foreach ( $plugin->customHooks as $customHook ) {
					if ( ! isset( $plugins[$customHook] ) ) {
						$plugins[$customHook]       = [];
						$plugins_events[$customHook] = [];
					}
				}
			}

			$plugins_installed[$plugin_class] = $plugin;
			foreach ( $plugins_events as $event=>$value ) {
				if ( method_exists( $plugin, $event ) ) {
					array_push( $plugins[$event], $plugin );
				}
			}
		}

		// Sort the plugins by the position for the site sidebar.
		uasort( $plugins['siteSidebar'], function ( $a, $b ) {
				return $a->position() > $b->position();
			}
		);
	}
}
buildPlugins();
