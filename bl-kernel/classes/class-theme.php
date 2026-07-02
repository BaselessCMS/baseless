<?php
/**
 * Template helpers
 *
 * @package    Baseless
 * @subpackage Classes
 * @category   Templates
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Theme {

	/**
	 * Site title
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @global object $site The Site class.
	 * @return string
	 */
	public static function title() {
		global $site;
		return $site->title();
	}

	/**
	 * Site description
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @global object $site The Site class.
	 * @return string
	 */
	public static function description() {
		global $site;
		return $site->description();
	}

	/**
	 * Site slogan
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @global object $site The Site class.
	 * @return string
	 */
	public static function slogan() {
		global $site;
		return $site->slogan();
	}

	/**
	 * Site footer text
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @global object $site The Site class.
	 * @return string
	 */
	public static function footer() {
		global $site;
		return $site->footer();
	}

	/**
	 * Site language
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @global object $L The Language class.
	 * @return string
	 */
	public static function lang() {
		global $L;
		return $L->currentLanguageShortVersion();
	}

	/**
	 * Site RSS URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return mixed
	 */
	public static function rssUrl() {
		if ( plugin_activated( 'pluginRSS' ) ) {
			return DOMAIN_BASE . 'rss.xml';
		}
		return false;
	}

	/**
	 * Site map URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return mixed
	 */
	public static function sitemapUrl() {
		if ( plugin_activated( 'pluginSitemap' ) ) {
			return DOMAIN_BASE . 'sitemap.xml';
		}
		return false;
	}

	/**
	 * Site URL
	 *
	 * Returns the absolute URL of the site.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return string
	 */
	public static function siteUrl() {
		return DOMAIN_BASE;
	}

	/**
	 * Admin URL
	 *
	 * Returns the absolute URL of admin panel.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return string
	 */
	public static function adminUrl() {
		return DOMAIN_ADMIN;
	}

	/**
	 * Meta tags
	 *
	 * Title or description
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $tag
	 * @return string
	 */
	public static function metaTags( $tag ) {

		if ( 'title' == $tag ) {
			return self :: metaTagTitle();
		} elseif ( 'description' == $tag ) {
			return self :: metaTagDescription();
		}
	}

	/**
	 * Title meta tag
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @global object $categories The Categories class.
	 * @global object $page The Page class.
	 * @global object $site The Site class.
	 * @global object $tags The Tags class.
	 * @global object $url The Url class.
	 * @global string $WHERE_AM_I
	 * @return string
	 */
	public static function metaTagTitle() {

		// Access global variables.
		global $categories, $page, $site, $tags, $url, $WHERE_AM_I;

		if ( 'page' == $WHERE_AM_I ) {
			$format = $site->titleFormatPages();
			$format = \Text :: replace( '{{page-title}}', $page->title(), $format );
			$format = \Text :: replace( '{{page-description}}', $page->description(), $format );
		} elseif ( 'tag' == $WHERE_AM_I ) {
			try {
				$tagKey = $url->slug();
				$tag    = new \Tag( $tagKey );
				$format = $site->titleFormatTag();
				$format = \Text :: replace( '{{tag-name}}', $tag->name(), $format );
			} catch ( \Exception $e ) {
				// Tag doesn't exist.
			}

		} elseif ( 'category' == $WHERE_AM_I ) {
			try {
				$categoryKey = $url->slug();
				$category    = new \Category( $categoryKey );
				$format      = $site->titleFormatCategory();
				$format      = \Text :: replace( '{{category-name}}', $category->name(), $format );
			} catch ( \Exception $e ) {
				// Category doesn't exist.
			}
		} else {
			$format = $site->titleFormatHomepage();
		}

		$format = \Text :: replace( '{{site-title}}', $site->title(), $format );
		$format = \Text :: replace( '{{site-slogan}}', $site->slogan(), $format );
		$format = \Text :: replace( '{{site-description}}', $site->description(), $format );

		return '<title>' . $format . '</title>' . PHP_EOL;
	}

	/**
	 * Description meta tag
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @global object $page The Page class.
	 * @global object $site The Site class.
	 * @global object $url The Url class.
	 * @global string $WHERE_AM_I
	 * @return string
	 */
	public static function metaTagDescription() {

		// Access global variables.
		global $page, $site, $url, $WHERE_AM_I;

		$description = $site->description();

		if ( 'page' == $WHERE_AM_I ) {
			$description = $page->description();
		} elseif ( 'category' == $WHERE_AM_I ) {
			try {
				$categoryKey = $url->slug();
				$category    = new \Category( $categoryKey );
				$description = $category->description();
			} catch ( \Exception $e ) {
				// Description from the site.
			}
		}
		return '<meta name="description" content="' . $description . '">' . PHP_EOL;
	}

	/**
	 * Title tag
	 *
	 * Returns the metatag <title> with a predefined structure.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return string
	 */
	public static function headTitle() {
		return self :: metaTagTitle();
	}

	/**
	 * Description tag: deprecated
	 *
	 * Returns the metatag <description> with a predefined structure.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return string
	 */
	public static function headDescription() {
		return self :: metaTagDescription();
	}

	/**
	 * Charset meta tag
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $charset
	 * @return string
	 */
	public static function charset( $charset ) {
		return '<meta charset="' . $charset . '">' . PHP_EOL;
	}

	/**
	 * Viewport meta tag
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $content
	 * @return string
	 */
	public static function viewport( $content ) {
		return '<meta name="viewport" content="' . $content . '">' . PHP_EOL;
	}

	/**
	 * SRC relative to theme base
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $file
	 * @param  string $base
	 * @return string
	 */
	public static function src( $file, $base = DOMAIN_THEME ) {
		return $base . $file;
	}

	/**
	 * CSS tags
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  array $files
	 * @param  string $base
	 * @return string
	 */
	public static function css( $files, $base = DOMAIN_THEME ) {

		if ( ! is_array( $files ) ) {
			$files = [ $files ];
		}

		$links = '';
		foreach ( $files as $file ) {
			$links .= '<link rel="stylesheet" type="text/css" href="' . $base . $file . '?version=' . CMS_VERSION . '">' . PHP_EOL;
		}
		return $links;
	}

	/**
	 * JavaScript tags
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  array $files
	 * @param  string $base
	 * @param  string $attributes
	 * @return string
	 */
	public static function javascript( $files, $base = DOMAIN_THEME, $attributes = '' ) {

		if ( ! is_array( $files ) ) {
			$files = [ $files ];
		}

		$scripts = '';
		foreach ( $files as $file ) {
			$scripts .= '<script ' . $attributes . ' src="' . $base . $file . '?version=' . CMS_VERSION . '"></script>' . PHP_EOL;
		}
		return $scripts;
	}

	/**
	 * Alias of self :: javascript()
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  array $files
	 * @param  string $base
	 * @param  string $attributes
	 * @return string
	 */
	public static function js( $files, $base = DOMAIN_THEME, $attributes = '' ) {
		return self :: javascript( $files, $base, $attributes );
	}

	/**
	 * Get plugins
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $type
	 * @param  array $args
	 * @return void
	 */
	public static function plugins( $type, $args = [] ) {

		// Access global variables.
		global $plugins;

		foreach ( $plugins[$type] as $plugin ) {
			echo call_user_func_array( [ $plugin, $type ], $args );
		}
	}

	/**
	 * Site favicon
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $file
	 * @param  string $typeIcon
	 * @return string
	 */
	public static function favicon( $file = 'favicon.png', $typeIcon = 'image/png' ) {
		return '<link rel="icon" href="' . DOMAIN_THEME . $file . '" type="' . $typeIcon . '">' . PHP_EOL;
	}

	/**
	 * Keywords meta tag
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  array $keywords
	 * @return string
	 */
	public static function keywords( $keywords ) {
		if ( is_array( $keywords ) ) {
			$keywords = implode( ',', $keywords );
		}
		return '<meta name="keywords" content="' . $keywords . '">' . PHP_EOL;
	}

	/**
	 * Enqueue jQuery
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return string
	 */
	public static function jquery() {
		return '<script src="' . DOMAIN_CORE_JS . 'jquery.min.js?version=' . CMS_VERSION . '"></script>' . PHP_EOL;
	}

	/**
	 * Bootstrap scripts
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $attributes
	 * @return string
	 */
	public static function jsBootstrap( $attributes = '' ) {
		return '<script ' . $attributes . ' src="' . DOMAIN_CORE_JS . 'bootstrap.bundle.min.js?version=' . CMS_VERSION . '"></script>' . PHP_EOL;
	}

	/**
	 * Bootstrap stylesheet
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return string
	 */
	public static function cssBootstrap() {
		return '<link rel="stylesheet" type="text/css" href="' . DOMAIN_CORE_CSS . 'bootstrap.min.css?version=' . CMS_VERSION . '">' . PHP_EOL;
	}

	/**
	 * Enqueue jQuery sortable script
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $attributes
	 * @return string
	 */
	public static function jsSortable( $attributes = '' ) {
		return '<script ' . $attributes . ' src="' . DOMAIN_CORE_JS . 'jquery.sortable.min.js?version=' . CMS_VERSION . '"></script>' . PHP_EOL;
	}
}
