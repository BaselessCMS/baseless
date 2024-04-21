<?php
/**
 * Site settings & methods
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Site extends dbJSON {

	/**
	 * Field defaults
	 *
	 * @since  1.0.0
	 * @access public
	 * @var array
	 */
	public $dbFields = [
		'title'        => 'Website Title',
		'slogan'       => '',
		'description'  => '',
		'footer'       => '',
		'itemsPerPage' => 6,
		'language'     => 'en',
		'locale'       => 'en, en_US, en_AU, en_CA, en_GB, en_IE, en_NZ',
		'timezone'     => 'America/Argentina/Buenos_Aires',
		'theme'        => 'default',
		'adminTheme'   => 'default',
		'homepage'     => '',
		'pageNotFound' => '',
		'uriPage'      => '/',
		'uriTag'       => '/tag/',
		'uriCategory'  => '/category/',
		'uriBlog'      => '/blog/',
		'url'          => '',
		'emailFrom'    => '',
		'dateFormat'   => 'F j, Y',
		'timeFormat'   => 'g:i a',
		'currentBuild' => 0,
		'twitter'      => '',
		'facebook'     => '',
		'instagram'    => '',
		'youtube'      => '',
		'vimeo'        => '',
		'codepen'      => '',
		'github'       => '',
		'gitlab'       => '',
		'linkedin'     => '',
		'xing'         => '',
		'mastodon'     => '',
		'dribbble'     => '',
		'vk'           => '',
		'orderBy'      => 'date', // date or position
		'extremeFriendly'         => true,
		'autosaveInterval'        => 2, // minutes
		'titleFormatHomepage'     => '{{site-slogan}} | {{site-title}}',
		'titleFormatPages'        => '{{page-title}} | {{site-title}}',
		'titleFormatCategory'     => '{{category-name}} | {{site-title}}',
		'titleFormatTag'          => '{{tag-name}} | {{site-title}}',
		'imageRestrict'           => true,
		'imageRelativeToAbsolute' => false,
		'thumbnailWidth'          => 400, // px
		'thumbnailHeight'         => 400, // px
		'thumbnailQuality'        => 100,
		'logo'                    => '',
		'markdownParser'          => true,
		'customFields'            => '{}'
	];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Run parent constructor.
		parent :: __construct( DB_SITE );

		// Set timezone.
		$this->setTimezone( $this->timezone() );

		// Set locale.
		$this->setLocale( $this->locale() );
	}

	/**
	 * Get database
	 *
	 * Returns an array with site configuration.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get() {
		return $this->db;
	}

	/**
	 * Set fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @return method
	 */
	public function set( $args ) {

		// Check values on args or set default values.
		foreach ( $this->dbFields as $field => $value ) {
			if ( isset( $args[$field] ) ) {
				$finalValue = \Sanitize :: html( $args[$field] );
				if ( $finalValue === 'false' ) {
					$finalValue = false;
				} elseif ( $finalValue === 'true' ) {
					$finalValue = true;
				}
				settype( $finalValue, gettype( $value ) );
				$this->db[$field] = $finalValue;
			}
		}
		return $this->save();
	}

	/**
	 * URI filters
	 *
	 * Returns an array with the URL filters.
	 * Also get the a particular filter.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $filter
	 * @return mixed
	 */
	public function uriFilters( $filter = '' ) {

		$filters['admin']    = '/' . ADMIN_URI_FILTER . '/';
		$filters['page']     = $this->getField( 'uriPage' );
		$filters['tag']      = $this->getField( 'uriTag' );
		$filters['category'] = $this->getField( 'uriCategory' );

		if ( $this->getField( 'uriBlog' ) ) {
			$filters['blog'] = $this->getField( 'uriBlog' );
		}

		if ( empty( $filter) ) {
			return $filters;
		}

		if ( isset( $filters[$filter] ) ) {
			return $filters[$filter];
		}
		return false;
	}

	/**
	 * Thumbnail width
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function thumbnailWidth() {
		return $this->getField( 'thumbnailWidth' );
	}

	/**
	 * Thumbnail height
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function thumbnailHeight() {
		return $this->getField( 'thumbnailHeight' );
	}

	/**
	 * Thumbnail quality
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function thumbnailQuality() {
		return $this->getField( 'thumbnailQuality' );
	}

	/**
	 * Autosave interval
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function autosaveInterval() {
		return $this->getField( 'autosaveInterval' );
	}

	/**
	 * Allow Unicode characters in the URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function extremeFriendly() {
		return $this->getField( 'extremeFriendly' );
	}

	/**
	 * Markdown parser
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function markdownParser() {
		return $this->getField( 'markdownParser' );
	}

	/**
	 * X/Twitter URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function twitter() {
		return $this->getField( 'twitter' );
	}

	/**
	 * Facebook URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function facebook() {
		return $this->getField( 'facebook' );
	}

	/**
	 * Instagram URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function instagram() {
		return $this->getField( 'instagram' );
	}

	/**
	 * YouTube URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function youtube() {
		return $this->getField( 'youtube' );
	}

	/**
	 * Vimeo URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function vimeo() {
		return $this->getField( 'vimeo' );
	}

	/**
	 * CodePen URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function codepen() {
		return $this->getField( 'codepen' );
	}

	/**
	 * GitHub URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function github() {
		return $this->getField( 'github' );
	}

	/**
	 * GitLab URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function gitlab() {
		return $this->getField( 'gitlab' );
	}

	/**
	 * LinkedIn URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function linkedin() {
		return $this->getField( 'linkedin' );
	}

	/**
	 * Xing URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function xing() {
		return $this->getField( 'xing' );
	}

	/**
	 * Mastodon URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function mastodon() {
		return $this->getField( 'mastodon' );
	}

	/**
	 * Dribble URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function dribbble() {
		return $this->getField( 'dribbble' );
	}

	/**
	 * VK URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function vk() {
		return $this->getField( 'vk' );
	}

	/**
	 * Order posts by
	 *
	 * Date or position.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function orderBy() {
		return $this->getField( 'orderBy' );
	}

	/**
	 * Restrict image
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function imageRestrict() {
		return $this->getField( 'imageRestrict' );
	}

	/**
	 * Image relative path
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function imageRelativeToAbsolute() {
		return $this->getField( 'imageRelativeToAbsolute' );
	}

	/**
	 * Site title
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function title() {
		return $this->getField( 'title' );
	}

	/**
	 * Site slogan
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function slogan() {
		return $this->getField( 'slogan' );
	}

	/**
	 * Site description
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function description() {
		return $this->getField( 'description' );
	}

	/**
	 * From email
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function emailFrom() {
		return $this->getField( 'emailFrom' );
	}

	/**
	 * Date format
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function dateFormat() {
		return $this->getField( 'dateFormat' );
	}

	/**
	 * Time format
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function timeFormat() {
		return $this->getField( 'timeFormat' );
	}

	/**
	 * Active frontend theme
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function theme() {
		return $this->getField( 'theme' );
	}

	/**
	 * Active backend theme
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function adminTheme() {
		return $this->getField( 'adminTheme' );
	}

	/**
	 * Site footer text.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function footer() {
		return $this->getField( 'footer' );
	}

	/**
	 * Title format: pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function titleFormatPages() {
		return $this->getField( 'titleFormatPages' );
	}

	/**
	 * Title format: home page
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function titleFormatHomepage() {
		return $this->getField( 'titleFormatHomepage' );
	}

	/**
	 * Title format: categories
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function titleFormatCategory() {
		return $this->getField( 'titleFormatCategory' );
	}

	/**
	 * Title format: tags
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function titleFormatTag() {
		return $this->getField( 'titleFormatTag' );
	}

	/**
	 * Site logo
	 *
	 * Returns the absolute URL of the site logo.
	 * If $absolute is false, only the filename is returned.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $absolute
	 * @return string
	 */
	public function logo( $absolute = true ) {

		$logo = $this->getField( 'logo' );
		if ( $absolute && $logo ) {
			return DOMAIN_UPLOADS . $logo;
		}
		return $logo;
	}

	/**
	 * CMS URL
	 *
	 * Returns the full domain and base url.
	 *
	 * @example https://www.domain.com
	 * @example https://www.domain.com/blog
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function url() {
		return $this->getField( 'url' );
	}

	/**
	 * Site Domain
	 *
	 * Returns the protocol and the domain without the base url.
	 *
	 * @example https://www.domain.com
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function domain() {

		// If the URL field is not set, try detect the domain.
		if ( \Text :: isEmpty( $this->url() ) ) {
			if ( ! empty( $_SERVER['HTTPS'] ) ) {
				$protocol = 'https://';
			} else {
				$protocol = 'http://';
			}

			$domain = trim( $_SERVER['HTTP_HOST'], '/' );
			return $protocol . $domain;
		}

		// Parse the domain from the field url (Settings->Advanced)
		$parse  = parse_url( $this->url() );
		$domain = rtrim( $parse['host'], '/' );
		$port   = ! empty( $parse['port'] ) ? ':' . $parse['port'] : '';
		$scheme = ! empty( $parse['scheme'] ) ? $parse['scheme'] . '://' : 'http://';

		return $scheme . $domain . $port;
	}

	/**
	 * Site time zone
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function timezone() {
		return $this->getField( 'timezone' );
	}

	/**
	 * URL path
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function urlPath() {
		$url = $this->getField( 'url' );
		return parse_url( $url, PHP_URL_PATH );
	}

	/**
	 * URL scheme
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function isHTTPS() {
		$url = $this->getField( 'url' );
		return parse_url( $url, PHP_URL_SCHEME ) === 'https';
	}

	/**
	 * Build version
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function currentBuild() {
		return $this->getField( 'currentBuild' );
	}

	/**
	 * Items per page
	 *
	 * For paginated content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return integer
	 */
	public function itemsPerPage() {
		$value = $this->getField( 'itemsPerPage' );
		if ( ( $value > 0 ) or ( $value == -1 ) ) {
			return $value;
		}
		return 6;
	}

	/**
	 * CMS/site language
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function language() {
		return $this->getField( 'language' );
	}

	/**
	 * CMS/site language, short version
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function languageShortVersion() {
		$current = $this->language();
		$explode = explode( '_', $current );
		return $explode[0];
	}

	/**
	 * CMS/site locale
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function locale() {
		return $this->getField( 'locale' );
	}

	/**
	 * Home page content
	 *
	 * Sequential posts or a static page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return mixed Returns a page key to use as front page or false.
	 */
	public function homepage() {
		$homepage = $this->getField( 'homepage' );
		if ( empty( $homepage ) ) {
			return false;
		}
		return $homepage;
	}

	/**
	 * $)$ error page
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function pageNotFound() {
		$pageNotFound = $this->getField( 'pageNotFound' );
		return $pageNotFound;
	}

	// Set the locale, returns TRUE is success, FALSE otherwise
	/**
	 * Set locale
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $locale
	 * @return void
	 */
	public function setLocale( $locale ) {

		$localeList = explode( ',', $locale );
		foreach ( $localeList as $locale ) {

			$locale = trim( $locale );
			if ( setlocale( LC_ALL, $locale . '.UTF-8' ) !== false ) {
				return true;
			} elseif ( setlocale( LC_ALL, $locale ) !== false ) {
				return true;
			}
		}

		// Not was possible to set a locale, using default locale
		return false;
	}

	/**
	 * Set the timezone.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $timezone
	 * @return string
	 */
	public function setTimezone( $timezone ) {
		return date_default_timezone_set( $timezone );
	}

	/**
	 * Custom fields
	 *
	 * Returns the custom fields as array.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function customFields() {
		$customFields = \Sanitize :: htmlDecode( $this->getField( 'customFields' ) );
		return json_decode( $customFields, true );
	}
}
