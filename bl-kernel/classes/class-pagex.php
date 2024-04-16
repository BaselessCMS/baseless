<?php
/**
 * Page object
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Content
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Page {

	/**
	 * Variables
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $vars;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key The page key.
	 * @global object $pages The Pages class.
	 * @return self
	 */
	function __construct( $key ) {

		// Access global variables.
		global $pages;

		$this->vars['key'] = $key;

		/**
		 * If key is FALSE, the page is create with default values,
		 * like an empty page. Useful for Page Not Found.
		 */
		if ( false === $key ) {
			$row = $pages->getDefaultFields();
		} else {
			if ( \Text :: isEmpty( $key ) || ! $pages->exists( $key ) ) {
				$errorMessage = 'Page not found in database by key [' . $key . ']';
				\Log :: set( __METHOD__ . LOG_SEP . $errorMessage );
				throw new \Exception( $errorMessage );
			}
			$row = $pages->getPageDB( $key );
		}

		foreach ( $row as $field => $value ) {
			if ( 'date' == $field ) {
				$this->setField( 'dateRaw', $value );
			} else {
				$this->setField( $field, $value );
			}
		}
	}

	/**
	 * Get field value
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @return mixed
	 */
	public function getValue( $field ) {

		if ( isset( $this->vars[$field] ) ) {
			return $this->vars[$field];
		}
		return false;
	}

	/**
	 * Set field value
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @param  mixed $value
	 * @return boolean
	 */
	public function setField( $field, $value ) {
		$this->vars[$field] = $value;
		return true;
	}

	// Returns the raw content
	// This content is not markdown parser
	// (boolean) $sanitize, TRUE returns the content sanitized
	/**
	 * Raw page content
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $sanitize
	 * @return string
	 */
	public function contentRaw( $sanitize = false ) {

		$key = $this->key();
		$filePath   = PATH_PAGES.$key.DS.FILENAME;
		$contentRaw = file_get_contents( $filePath );

		if ( $sanitize ) {
			return \Sanitize :: html( $contentRaw );
		}
		return $contentRaw;
	}

	// Returns the full content
	// This content is markdown parser
	// (boolean) $sanitize, TRUE returns the content sanitized
	/**
	 * Page content
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $sanitize
	 * @return string
	 */
	public function content( $sanitize = false ) {

		// If already set the content, return it.
		$content = $this->getValue( 'content' );
		if ( ! empty( $content ) ) {
			return $content;
		}

		// Get the raw content.
		$content = $this->contentRaw();

		// Parse Markdown.
		if ( MARKDOWN_PARSER ) {
			$parsedown = new \Parsedown();
			$content = $parsedown->text( $content );
		}

		// Parse img src relative to absolute (with domain).
		if ( IMAGE_RELATIVE_TO_ABSOLUTE ) {
			$domain  = IMAGE_RESTRICT ? DOMAIN_UPLOADS_PAGES . $this->uuid() . '/' : DOMAIN_UPLOADS;
			$content = \Text :: imgRel2Abs( $content, $domain );
		}

		if ( $sanitize ) {
			return \Sanitize :: html( $content );
		}
		return $content;
	}

	// Returns the first part of the content if the content is splited, otherwise is returned the full content
	// This content is markdown parser
	// (boolean) $sanitize, TRUE returns the content sanitized
	/**
	 * Page content with break
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $sanitize
	 * @return void
	 */
	public function contentBreak( $sanitize = false ) {
		$content = $this->content( $sanitize );
		$explode = explode( PAGE_BREAK, $content );
		return $explode[0];
	}

	/**
	 * Page date
	 *
	 * Returns the date according to locale settings
	 * and the format defined in the system.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $format
	 * @global object $site The Site class.
	 * @return void
	 */
	public function date( $format = false ) {

		// Access global variables.
		global $site;

		$dateRaw = $this->dateRaw();
		if ( false === $format ) {
			$format = $site->dateFormat();
		}
		return \Date :: format( $dateRaw, DB_DATE_FORMAT, $format );
	}

	/**
	 * Raw page date
	 *
	 * Returns the date according to locale settings
	 * and format as database stored.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function dateRaw() {

		// This field is set in the constructor.
		return $this->getValue( 'dateRaw' );
	}

	/**
	 * Page modified date
	 *
	 * Returns the date according to locale settings
	 * and format settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $format
	 * @global object $site The Site class.
	 * @return void
	 */
	public function dateModified( $format = false ) {

		// Access global variables.
		global $site;

		$dateRaw = $this->getValue( 'dateModified' );
		if ( false === $format ) {
			$format = $site->dateFormat();
		}
		return \Date :: format( $dateRaw, DB_DATE_FORMAT, $format );
	}

	/**
	 * Page creator
	 *
	 * Returns the username who created the page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function username() {
		return $this->getValue( 'username' );
	}

	/**
	 * Page author
	 *
	 * Alias of `$this->username()`.
	 *
	 * @todo Look into separate author field.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function author() {
		return $this->username();
	}

	/**
	 * Get page database
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function getDB() {
		return $this->vars;
	}

	/**
	 * Page permalink
	 *
	 * True returns the page link with the domain,
	 * false without the domain.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $absolute
	 * @return string
	 */
	public function permalink( $absolute = true ) {

		// Get the key of the page.
		$key = $this->key();

		if ( $absolute ) {
			return DOMAIN_PAGES . $key;
		}
		return HTML_PATH_ROOT . PAGE_URI_FILTER . $key;
	}

	/**
	 * Previous page key
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $pages The Pages class.
	 * @return mixed
	 */
	public function previousKey() {

		// Access global variables.
		global $pages;

		return $pages->previousPageKey( $this->key() );
	}

	/**
	 * Next page key
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $pages The Pages class.
	 * @return mixed
	 */
	public function nextKey() {

		// Access global variables.
		global $pages;

		return $pages->nextPageKey( $this->key() );
	}

	/**
	 * Page category
	 *
	 * Returns the display name of the category.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function category() {
		return $this->categoryMap( 'name' );
	}

	/**
	 * Page category template
	 *
	 * Returns the display name of the category template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function categoryTemplate() {
		return $this->categoryMap( 'template' );
	}

	/**
	 * Page category description
	 *
	 * Returns the display name of the category description.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function categoryDescription() {
		return $this->categoryMap( 'description' );
	}

	/**
	 * Page category key
	 *
	 * Returns the display name of the category key.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function categoryKey() {
		return $this->getValue( 'category' );
	}

	/**
	 * Page category permalink
	 *
	 * Returns the display name of the category permalink.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function categoryPermalink() {
		return DOMAIN_CATEGORIES . $this->categoryKey();
	}

	/**
	 * Page category map
	 *
	 * Returns the field from the array.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @global object $categories The Categories class.
	 * @return mixed
	 */
	public function categoryMap ( $field ) {

		// Access global variables.
		global $categories;

		$categoryKey = $this->categoryKey();
		$map = $categories->getMap( $categoryKey );

		if ( 'key' == $field ) {
			return $this->categoryKey();
		} elseif ( isset( $map[$field] ) ) {
			return $map[$field];
		}
		return false;
	}

	/**
	 * Page user
	 *
	 * Returns the user object or passing the method
	 * returns the object User method.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $method
	 * @return mixed
	 */
	public function user( $method = false ) {

		$username = $this->username();
		try {
			$user = new \User( $username );
			if ( $method ) {
				return $user->{$method}();
			}
			return $user;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Page template
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function template() {
		return $this->getValue( 'template' );
	}

	/**
	 * Page description
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function description() {
		return $this->getValue( 'description' );
	}

	// Returns the tags separated by comma
	// (boolean) $returnsArray, TRUE to get the tags as an array, FALSE to get the tags separated by comma
	// The tags in array format returns array( tagKey => tagName )
	public function tags( $returnsArray = false ) {

		$tags = $this->getValue( 'tags' );
		if ( $returnsArray ) {
			if ( empty( $tags ) ) {
				return [];
			}
			return $tags;
		}

		if ( empty( $tags ) ) {
			return '';
		}
		return implode( ',', $tags );
	}

	public function json( $returnsArray = false ) {

		$tmp['key']         = $this->key();
		$tmp['title']       = $this->title();
		$tmp['content']     = $this->content(); // Markdown parsed
		$tmp['contentRaw']  = $this->contentRaw( true ); // No Markdown parsed
		$tmp['description'] = $this->description();
		$tmp['type']        = $this->type();
		$tmp['slug']        = $this->slug();
		$tmp['date']        = $this->date();
		$tmp['dateRaw']     = $this->dateRaw();
		$tmp['tags']        = $this->tags( false );
		$tmp['username']    = $this->username();
		$tmp['category']    = $this->category();
		$tmp['uuid']        = $this->uuid();
		$tmp['dateUTC']     = \Date :: convertToUTC( $this->dateRaw(), DB_DATE_FORMAT, DB_DATE_FORMAT );
		$tmp['permalink']   = $this->permalink( true );
		$tmp['coverImage']  = $this->coverImage( true );
		$tmp['coverImageFilename'] = $this->coverImage( false );

		if ( $returnsArray ) {
			return $tmp;
		}
		return json_encode( $tmp );
	}

	// Returns the endpoint of the coverimage, FALSE if the page doesn't have a cover image
	// (boolean) $absolute, TRUE returns the complete URL, FALSE returns the filename
	// If the user defined an external cover image the function returns it
	public function coverImage( $absolute = true ) {

		$filename = $this->getValue( 'coverImage' );
		if ( empty( $filename ) ) {
			return false;
		}

		// Check is external cover image.
		if ( filter_var( $filename, FILTER_VALIDATE_URL ) ) {
			return $filename;
		}

		if ( $absolute ) {
			if ( IMAGE_RESTRICT ) {
				return DOMAIN_UPLOADS_PAGES . $this->uuid() . '/' . $filename;
			}
			return DOMAIN_UPLOADS.$filename;
		}
		return $filename;
	}

	// Returns the endpoint of the thumbnail cover image, FALSE if the page doesn't have a cover image
	public function thumbCoverImage() {

		$filename = $this->coverImage( false );
		if ( false == $filename ) {
			return false;
		}

		// Check is external cover image.
		if ( filter_var( $filename, FILTER_VALIDATE_URL ) ) {
			return $filename;
		}

		if ( IMAGE_RESTRICT ) {
			return DOMAIN_UPLOADS_PAGES . $this->uuid() . '/thumbnails/' . $filename;
		}
		return DOMAIN_UPLOADS_THUMBNAILS . $filename;
	}

	/**
	 * Read more
	 *
	 * Returns true if the content has the text split.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function readMore() {
		$content = $this->contentRaw();
		return \Text :: stringContains( $content, PAGE_BREAK );
	}

	/**
	 * Page UUID
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function uuid() {
		return $this->getValue( 'uuid' );
	}

	/**
	 * Page key
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function key() {
		return $this->getValue( 'key' );
	}

	/**
	 * Page is published
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function published() {
		return ( 'published' === $this->getValue( 'type' ) );
	}

	/**
	 * Page is scheduled
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function scheduled() {
		return ( 'scheduled' === $this->getValue( 'type' ) );
	}

	/**
	 * Page is draft
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function draft() {
		return ( 'draft' === $this->getValue( 'type' ) );
	}

	/**
	 * Page is autosave
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function autosave() {
		return ( 'autosave' === $this->getValue( 'type' ) );
	}

	/**
	 * Page is sticky
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function sticky() {
		return ( 'sticky' === $this->getValue( 'type' ) );
	}

	/**
	 * Page is static
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function isStatic() {
		return ( 'static' === $this->getValue( 'type' ) );
	}

	/**
	 * Page type
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function type() {
		return $this->getValue( 'type' );
	}

	/**
	 * Page title
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function title() {
		return $this->getValue( 'title' );
	}

	/**
	 * Allow comments
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function allowComments() {
		return $this->getValue( 'allowComments' );
	}

	/**
	 * Page position
	 *
	 * @since  1.0.0
	 * @access public
	 * @return integer
	 */
	public function position() {
		return $this->getValue( 'position' );
	}

	/**
	 * Page meta: noindex
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function noindex() {
		return $this->getValue( 'noindex' );
	}

	/**
	 * Page meta: nofollow
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function nofollow() {
		return $this->getValue( 'nofollow' );
	}

	/**
	 * Page meta: noarchive
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function noarchive() {
		return $this->getValue( 'noarchive' );
	}

	/**
	 * Page slug
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function slug() {
		$explode = explode( '/', $this->key() );
		return end( $explode );
	}

	/**
	 * Page parent
	 *
	 * @since  1.0.0
	 * @access public
	 * @return mixed
	 */
	public function parent() {
		return $this->parentKey();
	}

	/**
	 * Page parent key
	 *
	 * @since  1.0.0
	 * @access public
	 * @return mixed
	 */
	public function parentKey() {
		$explode = explode( '/', $this->key() );
		if ( isset( $explode[1] ) ) {
			return $explode[0];
		}
		return false;
	}

	/**
	 * Page is parent
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function isParent() {
		return $this->parentKey() === false;
	}

	/**
	 * Parent method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $method
	 * @return mixed
	 */
	public function parentMethod( $method ) {

		$parentKey = $this->parentKey();
		if ( $parentKey ) {
			try {
				$page = new \Page( $parentKey );
				return $page->{$method}();
			} catch ( \Exception $e ) {
				// Continue.
			}
		}
		return false;
	}

	/**
	 * Page is child
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function isChild() {
		return $this->parentKey() !== false;
	}

	/**
	 * Page has children
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function hasChildren() {
		$childrenKeys = $this->childrenKeys();
		return ! empty( $childrenKeys );
	}

	/**
	 * Page children keys
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $pages The Pages class.
	 * @return array
	 */
	public function childrenKeys() {

		// Access global variables.
		global $pages;

		$key = $this->key();
		return $pages->getChildren( $key );
	}

	/**
	 * Page children
	 *
	 * Returns an array with all children as page object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $pages The Pages class.
	 * @return array
	 */
	public function children() {

		// Access global variables.
		global $pages;

		$list = [];
		$childrenKeys = $pages->getChildren( $this->key() );

		foreach ( $childrenKeys as $childKey ) {
			try {
				$child = new \Page( $childKey );
				array_push( $list, $child );
			} catch ( \Exception $e ) {
				// Continue.
			}
		}
		return $list;
	}

	/**
	 * Reading time
	 *
	 * Returns the amount of minutes takes to read the page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L The Language class.
	 * @return string
	 */
	public function readingTime() {

		// Access global variables.
		global $L;

		$words   = $this->content( true );
		$words   = strip_tags( $words );
		$words   = str_word_count( $words );
		$average = $words / 200;
		$minutes = round( $average );

		if ( $minutes > 1 ) {
			return $minutes . ' ' . $L->get( 'minutes' );
		}
		return '~1 ' . $L->get( 'minute' );
	}

	/**
	 * Relative time
	 *
	 * $complete = false : short version
	 * $complete = true  : full version
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $complete
	 * @return string
	 */
	public function relativeTime( $complete = false ) {

		$current = new \DateTime;
		$past    = new \DateTime( $this->getValue( 'dateRaw' ) );
		$elapsed = $current->diff( $past );

		$elapsed->w  = floor( $elapsed->d / 7 );
		$elapsed->d -= $elapsed->w * 7;

		$string = [
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		];

		foreach ( $string as $key => &$value ) {
			if ( $elapsed->$key) {
				$value = $elapsed->$key . ' ' . $value . ( $elapsed->$key > 1 ? 's' : ' ' );
			} else {
				unset( $string[$key] );
			}
		}

		if ( ! $complete ) {
			$string = array_slice( $string, 0 , 1 );
		}

		return $string ? implode( ', ', $string ) . ' ago' : 'Just now';
	}

	/**
	 * Custom field
	 *
	 * Returns the value from the field, false if the fields doesn't exists.
	 *
	 * If you set the $option as true, the function returns
	 * an array with all the values of the field.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @param  boolean $options
	 * @return mixed
	 */
	public function custom( $field, $options = false ) {

		if ( isset( $this->vars['custom'][$field] ) ) {
			if ( $options ) {
				return $this->vars['custom'][$field];
			}
			return $this->vars['custom'][$field]['value'];
		}
		return false;
	}

	/**
	 * Related pages
	 *
	 * Returns an array with all pages key related to the page.
	 * The relation is based on the tags.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object The tags class.
	 * @return array
	 */
	public function related() {

		// Access global variables.
		global $tags;

		$pageTags = $this->tags( true );
		$list     = [];

		// For each tag get the list of related pages.
		foreach ( $pageTags as $tagKey => $tagName ) {
			$pagesRelated = $tags->getList( $tagKey, 1, -1 );
			$list = array_merge( $list, $pagesRelated );
		}

		// Remove duplicates.
		$list = array_unique( $list );

		// Remove himself from the list.
		if ( ( $key = array_search( $this->key(), $list ) ) !== false ) {
			unset( $list[$key] );
		}
		return $list;
	}
}
