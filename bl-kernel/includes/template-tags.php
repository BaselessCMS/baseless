<?php
/**
 * Template tags
 *
 * @package    JSON CMS
 * @subpackage Core
 * @category   Functions
 * @since      1.0.0
 */

namespace CMS\Tags;

// Access namespaced functions.
use function CMS\Help\{
	site,
	url,
	lang
};

/**
 * Site URL
 *
 * Returns the absolute URL of the site.
 *
 * @since  1.0.0
 * @return string
 */
function site_url() {
	return DOMAIN_BASE;
}

/**
 * Admin URL
 *
 * Returns the absolute URL of admin panel.
 *
 * @since  1.0.0
 * @return string
 */
function admin_url() {
	return DOMAIN_ADMIN;
}

/**
 * Site RSS URL
 *
 * @since  1.0.0
 * @return mixed
 */
function rss_url() {
	if ( plugin_activated( 'pluginRSS' ) ) {
		return DOMAIN_BASE . 'rss.xml';
	}
	return false;
}

/**
 * Site map URL
 *
 * @since  1.0.0
 * @return mixed
 */
function sitemap_url() {
	if ( plugin_activated( 'pluginSitemap' ) ) {
		return DOMAIN_BASE . 'sitemap.xml';
	}
	return false;
}

/**
 * Site title
 *
 * @since  1.0.0
 * @return string
 */
function site_title() {
	return site()->title();
}

/**
	* Site description
	*
	* @since  1.0.0
	* @return string
	*/
function site_description() {
	return site()->description();
}

/**
 * Site slogan
 *
 * @since  1.0.0
 * @return string
 */
function slogan() {
	return site()->slogan();
}

/**
 * jQuery tag
 *
 * @since  1.0.0
 * @return string
 */
function jquery_tag() {
	return sprintf(
		'<script src="%sjquery.min.js?version=%s"></script>',
		DOMAIN_CORE_JS,
		CMS_VERSION
	);
}

/**
 * Print jQuery tag
 *
 * @since  1.0.0
 * @return void
 */
function jquery() {
	echo jquery_tag() . PHP_EOL;
}

/**
 * Core CSS tags
 *
 * @since  1.0.0
 * @param  array $files
 * @param  string $base
 * @return string
 */
function core_css( $files, $base = DOMAIN_CORE_ASSETS ) {

	if ( ! is_array( $files ) ) {
		$files = [ $files ];
	}

	$links = '';
	foreach ( $files as $file ) {
		$links .= '<link rel="stylesheet" type="text/css" href="' . $base . 'css/' . $file . '?version=' . CMS_VERSION . '">' . PHP_EOL;
	}
	return $links;
}

/**
 * Core JavaScript tags
 *
 * @since  1.0.0
 * @param  array $files
 * @param  string $base
 * @param  string $attributes
 * @return string
 */
function core_js( $files, $base = DOMAIN_CORE_ASSETS, $attributes = '' ) {

	if ( ! is_array( $files ) ) {
		$files = [ $files ];
	}

	$scripts = '';
	foreach ( $files as $file ) {
		$scripts .= '<script ' . $attributes . ' src="' . $base . 'js/' . $file . '?version=' . CMS_VERSION . '"></script>' . PHP_EOL;
	}
	return $scripts;
}
