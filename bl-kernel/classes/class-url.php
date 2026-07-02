<?php
/**
 * Content URL
 *
 * @package    Baseless
 * @subpackage Classes
 * @category   Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Url {

	/**
	 * URI
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $uri;

	/**
	 * URI length
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    integer
	 */
	protected $uriStrlen;

	/**
	 * Current content type
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $whereAmI;

	/**
	 * URL slug
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $slug;

	/**
	 * URI filters
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $filters;

	/**
	 * URI
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    boolean
	 */
	protected $notFound;

	/**
	 * URL parameters
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $parameters;

	/**
	 * Active filter
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $activeFilter;

	/**
	 * HTTP code
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $httpCode;

	/**
	 * HTTP message
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $httpMessage;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		/**
		 * Decodes any %## encoding in the given string.
		 * Plus symbols ('+') are decoded to a space character.
		 */
		$decode = urldecode( $_SERVER['REQUEST_URI'] );

		/**
		 * Remove parameters GET, I don't use parse_url
		 * because has problem with utf-8.
		 */
		$explode = explode( '?', $decode );
		$this->uri          = $explode[0];
		$this->parameters   = $_GET;
		$this->uriStrlen    = \Text :: length( $this->uri );
		$this->whereAmI     = 'home';
		$this->notFound     = false;
		$this->slug         = '';
		$this->filters      = [];
		$this->activeFilter = '';
		$this->httpCode     = 200;
		$this->httpMessage  = 'OK';
	}

	/**
	 * Check filters
	 *
	 * Filters change for different languages.
	 *
	 * @example (English): [ 'post' => '/post/', 'tag' => '/tag/' ]
	 * @example (Spanish): [ 'post' => '/publicacion/', 'tag' => '/etiqueta/' ]
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $filters
	 * @return boolean
	 */
	public function checkFilters( $filters ) {

		// Put the "admin" filter first.
		$adminFilter['admin'] = $filters['admin'];
		unset( $filters['admin'] );
		uasort( $filters, [ $this, 'sortByLength' ] );
		$this->filters = $adminFilter + $filters;

		foreach ( $this->filters as $filterName => $filterURI ) {

			$filterURIwoSlash = rtrim( $filterURI, '/' );
			$filterFull = ltrim( $filterURI, '/' );
			$filterFull = HTML_PATH_ROOT . $filterFull;
			$filterFullLength = \Text :: length( $filterFull );

			$subString = mb_substr( $this->uri, 0, $filterFullLength, CHARSET );

			/**
			 * Check coincidence without the last slash at the end,
			 * this case is not found.
			 */
			if ( $subString == $filterURIwoSlash ) {
				$this->setNotFound();
				return false;
			}

			// Check coincidence with complete filterURI.
			if ( $subString==$filterFull ) {

				$this->slug = mb_substr( $this->uri, $filterFullLength );
				$this->setWhereAmI( $filterName );
				$this->activeFilter = $filterURI;

				if ( empty( $this->slug ) && ( 'blog' == $filterName ) ) {
					$this->setWhereAmI( 'blog' );

				} elseif ( ! empty( $this->slug ) && ( 'blog' == $filterName ) ) {
					$this->setNotFound();
					return false;

				} elseif ( empty( $this->slug ) && ( '/' == $filterURI ) ) {
					$this->setWhereAmI( 'home' );

				} elseif ( ! empty( $this->slug ) && ( '/' == $filterURI ) ) {
					$this->setWhereAmI( 'page' );

				} elseif ( 'admin' == $filterName ) {
					$this->slug = ltrim( $this->slug, '/' );
				}
				return true;
			}
		}
		$this->setNotFound();
		return false;
	}

	/**
	 * URL slug
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function slug() {
		return $this->slug;
	}

	/**
	 * Set URL slug
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function setSlug( $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Active filters
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function activeFilter() {
		return $this->activeFilter;
	}

	/**
	 * Explode URL slug
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function explodeSlug( $delimiter = '/' ) {
		return explode( $delimiter, $this->slug );
	}

	/**
	 * URI
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function uri() {
		return $this->uri;
	}

	/**
	 * URL filters
	 *
	 * Return the filter by type.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function filters( $type, $trim = true ) {

		$filter = $this->filters[$type];
		if ( $trim ) {
			$filter = trim( $filter, '/' );
		}
		return $filter;
	}

	/**
	 * Where am I?
	 *
	 * Returns current content type: home, pages, categories, tags.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function whereAmI() {
		return $this->whereAmI;
	}

	/**
	 * Set where am I
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $where
	 * @return string
	 */
	public function setWhereAmI( $where ) {
		$GLOBALS['WHERE_AM_I'] = $where;
		$this->whereAmI = $where;
	}

	/**
	 * 404 error
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function notFound() {
		return $this->notFound;
	}

	/**
	 * age number
	 *
	 * Used for paginated content ( blog, category, tag ).
	 *
	 * @since  1.0.0
	 * @access public
	 * @return integer
	 */
	public function pageNumber() {
		if ( isset( $this->parameters['page'] ) ) {
			return (int)$this->parameters['page'];
		}
		return 1;
	}

	/**
	 * URL parameter
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @return mixed
	 */
	public function parameter( $field ) {
		if ( isset( $this->parameters[$field] ) ) {
			return $this->parameters[$field];
		}
		return false;
	}

	/**
	 * Set 404 error properties
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function setNotFound() {
		$this->setWhereAmI( 'page' );
		$this->notFound = true;
		$this->httpCode = 404;
		$this->httpMessage = 'Not Found';
	}

	/**
	 * HTTP code
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function httpCode() {
		return $this->httpCode;
	}

	/**
	 * Set HTTP code
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $code
	 * @return void
	 */
	public function setHttpCode( $code = 200 ) {
		$this->httpCode = $code;
	}

	/**
	 * HTTP message
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function httpMessage() {
		return $this->httpMessage;
	}

	/**
	 * Set HTTP message
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $msg
	 * @return void
	 */
	public function setHttpMessage( $msg = 'OK' ) {
		$this->httpMessage = $msg;
	}

	/**
	 * Sort URLs by length
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  integer $a
	 * @param  integer $b
	 * @return array
	 */
	private function sortByLength( $a, $b ) {
		return strlen( $b ) - strlen( $a );
	}
}
