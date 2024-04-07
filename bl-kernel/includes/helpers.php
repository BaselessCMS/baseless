<?php
/**
 * Helper functions
 *
 * @package    JSON CMS
 * @subpackage Core
 * @category   Functions
 * @since      1.0.0
 */

namespace CMS\Help;

/**
 * Site class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $site Site class
 * @return object
 */
function site() {
	global $site;
	return $site;
}

/**
 * Security class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $security Security class
 * @return object
 */
function security() {
	global $security;
	return $security;
}

/**
 * Login class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $login Login class
 * @return object
 */
function login() {
	global $login;
	return $login;
}

/**
 * Url class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $url Url class
 * @return object
 */
function url() {
	global $url;
	return $url;
}

/**
 * Language class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $L Language class
 * @return object
 */
function lang() {
	global $language;
	return $language;
}

/**
 * User class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $users User class
 * @return object
 */
function user() {
	global $user;
	return $user;
}

/**
 * Users class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $users Users class
 * @return object
 */
function users() {
	global $users;
	return $users;
}

/**
 * Plugins class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $plugins Plugins class
 * @return object
 */
function plugins() {
	global $plugins;
	return $plugins;
}

/**
 * Page class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $page Page class
 * @return object
 */
function page() {
	global $page;
	return $page;
}

/**
 * Pages class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $pages Pages class
 * @return object
 */
function pages() {
	global $pages;
	return $pages;
}

/**
 * Categories class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $categories Categories class
 * @return object
 */
function cats() {
	global $categories;
	return $categories;
}

/**
 * Website domain
 *
 * Returns the site URL setting or
 * the DOMAIN_BASE constant.
 *
 * @since  1.0.0
 * @return string
 */
function site_domain() {

	if ( site()->url() ) {
		return site()->url();
	}
	return DOMAIN_BASE;
}

/**
 * Helper class instance
 *
 * Theme helper class is changed tp
 * HTML in Bludit version 4.0.
 *
 * @since  1.0.0
 * @return object
 */
function helper() {

	if ( bludit_min( 4 ) ) {
		$helper_class = new \HTML;
	} else {
		$helper_class = new \Theme;
	}
	return $helper_class;
}

/**
 * Plugins hook
 *
 * @since  1.0.0
 * @param  string $name The hook name.
 * @return mixed
 */
function plugins_hook( $name = '' ) {

	if ( bludit_min( 4 ) ) {
		$hook = execPluginsByHook( $name );
	} else {
		$hook = helper()->plugins( $name );
	}

	if ( $hook ) {
		echo $hook;
	} else {
		return false;
	}
}

/**
 * Text replace
 *
 * Replaces the `%replace%` variable in
 * a language file string.
 *
 * @since  1.0.0
 * @param  string $get The language string to get.
 * @param  string $string The string to replace the variable.
 * @return string Returns the modified string or the string
 *                as is if the variable is not found.
 */
function text_replace( $get = '', $string = '' ) {

	if ( strstr( lang()->get( $get ), '%replace%' ) ) {
		return str_replace( '%replace%', $string, lang()->get( $get ) );
	}
	return lang()->get( $get );
}
