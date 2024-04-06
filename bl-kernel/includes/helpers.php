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
	global $L;
	return $L;
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
