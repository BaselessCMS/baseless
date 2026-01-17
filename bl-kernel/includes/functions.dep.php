<?php
/**
 * Functions
 *
 * @package  JSON CMS
 * @category Core
 * @since    1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	cats,
	lang,
	login,
	page,
	pages,
	plugins,
	site,
	syslog,
	tags,
	url,
	users,
};

/**
 * Re-index database of categories
 *
 * If you create/edit/remove a page is necessary
 * regenerate the database of categories.
 *
 * @since  1.0.0
 * @return method
 */
function reindex_categories() {
	return cats()->reindex();
}

/**
 * Re-index database of tags
 *
 * If you create/edit/remove a page is necessary
 * regenerate the database of tags.
 *
 * @since  1.0.0
 * @return method
 */
function reindex_tags() {
	return tags()->reindex();
}

/**
 * Build 404 page
 *
 * @since  1.0.0
 * @return object Instance of the Page class.
 */
function build_error_page() {

	try {
		$key  = site()->pageNotFound();
		$page = new Page( $key );
	} catch ( Exception $e ) {
		$page = new Page( false );
		$page->setField( 'title', lang()->get( 'page-not-found' ) );
		$page->setField( 'content', lang()->get( 'page-not-found-content' ) );
		$page->setField( 'username', 'admin' );
	}
	return $page;
}

/**
 * Build the page
 *
 * Generates a particular page from the current slug of the URL.
 *
 * @since  1.0.0
 * @return mixed Returns the page object or false.
 */
function build_the_page() {

	try {
		$key  = url()->slug();
		$page = page( $key );
	} catch ( Exception $e ) {
		url()->setNotFound();
		return false;
	}

	if ( $page->draft() || $page->scheduled() || $page->autosave() ) {
		if ( url()->parameter( 'preview' ) !== md5( $page->uuid() ) ) {
			url()->setNotFound();
			return false;
		}
	}
	return $page;
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
// Generate the global variables $content / $content, defined on 69.pages.php
// This function is use for build_pages_for_home(), build_pages_by_category(), build_pages_by_tag()
function build_pages_for( $for, $cat = false, $tag = false ) {

	// Get the page number from URL.
	$paged = url()->pageNumber();

	if ( 'home' == $for ) {

		$published = true;
		$per_page  = site()->itemsPerPage();
		$page_list = pages()->getList( $paged, $per_page, $published );

		// Include sticky pages only in the first page.
		if ( 1 == $paged ) {
			$sticky    = pages()->getStickyDB();
			$page_list = array_merge( $sticky, $page_list );
		}
	} elseif ( $for == 'category' ) {
		$per_page = site()->itemsPerPage();
		$page_list = cats()->getList( $cat, $paged, $per_page );
	} elseif ( $for == 'tag' ) {
		$per_page = site()->itemsPerPage();
		$page_list = tags()->getList( $tag, $paged, $per_page );
	}

	// There are not items, invalid tag, invalid category, out of range, etc.
	if ( $page_list === false ) {
		url()->setNotFound();
		return false;
	}

	$content = [];
	foreach ( $page_list as $key ) {
		try {
			$page = new Page( $key );
			if (
				( $page->type() == 'published' ) ||
				( $page->type() == 'sticky' ) ||
				( $page->type() == 'static' )
			) {
			array_push( $content, $page );
			}
		} catch ( Exception $e ) {
			// Continue.
		}
	}
	return $content;
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function build_pages_for_home() {
	return build_pages_for( 'home' );
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function build_pages_by_category() {
	$key = url()->slug();
	return build_pages_for( 'category', $key, false );
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function build_pages_by_tag() {
	$key = url()->slug();
	return build_pages_for( 'tag', false, $key );
}

// Returns an array with all the static pages as Page-Object
// The static pages are order by position all the time
function build_static_pages() {

	$list = [];
	$keys = pages()->getStaticDB();
	foreach ( $keys as $key ) {
		try {
			$page = page( $key );
			array_push( $list, $page );
		} catch ( Exception $e ) {
			// Continue.
		}
	}
	return $list;
}

// Returns the Page-Object if exists, FALSE otherwise
function build_page( $key ) {

	try {
		$page = new Page( $key );
		return $page;
	} catch ( Exception $e ) {
		return false;
	}
}

// Returns an array with all the parent pages as Page-Object
// The pages are order by the settings on the system
function build_parent_pages() {

	$list = [];
	$keys = pages()->getPublishedDB();
	foreach ( $keys as $key ) {
		try {
			$page = new Page( $key );
			if ( $page->isParent() ) {
			array_push( $list, $page );
			}
		} catch ( Exception $e ) {
			// Continue.
		}
	}
	return $list;
}

/**
 * Get plugin
 *
 * @param  string $plugin The plugin class name from plugin.php
 * @return mixed
 */
function get_plugin( $plugin ) {

	$plugins = plugins();
	if ( plugin_activated( $plugin ) ) {
		return $plugins['all'][$plugin];
	}
	return false;
}

// Returns TRUE if the plugin is activated / installed, FALSE otherwise
function plugin_activated( $plugin ) {

	$plugins = plugins();
	if ( isset( $plugins['all'][$plugin] ) ) {
		return $plugins['all'][$plugin]->installed();
	}
	return false;
}

function activate_plugin( $plugin ) {

	$plugins = plugins();
	if ( isset( $plugins['all'][$plugin] ) ) {

		$plugin = $plugins['all'][$plugin];
		if ( $plugin->install() ) {

			// Add to syslog.
			syslog()->add( [
				'dictionaryKey' => 'plugin-activated',
				'notes' => $plugin->name()
			] );

			// Create an alert.
			Alert :: set( lang()->g( 'plugin-activated' ) );
			return true;
		}
	}
	return false;
}

function deactivate_plugin( $plugin ) {

	$plugins = plugins();
	if ( isset( $plugins['all'][$plugin] ) ) {

		$plugin = $plugins['all'][$plugin];
		if ( $plugin->uninstall() ) {

			// Add to syslog.
			syslog()->add( [
				'dictionaryKey' => 'plugin-deactivated',
				'notes' => $plugin->name()
			] );

			// Create an alert.
			Alert :: set( lang()->g( 'plugin-deactivated' ) );
			return true;
		}
	}
	return false;
}

function deactivate_all_plugins() {

	$plugins = plugins();
	foreach ( $plugins['all'] as $plugin ) {
		if ( $plugin->uninstall() ) {

			// Add to syslog.
			syslog()->add( [
				'dictionaryKey' => 'plugin-deactivated',
				'notes' => $plugin->name()
			] );
		}
	}
	return false;
}

function change_plugin_order( $list ) {

	$plugins = plugins();
	foreach ( $list as $position => $class_name ) {
		if ( isset( $plugins['all'][$class_name] ) ) {
			$plugin = $plugins['all'][$class_name];
			$plugin->setPosition( ++$position );
		}
	}

	// Add to syslog.
	syslog()->add( [
		'dictionaryKey' => 'plugins-sorted',
		'notes' => ''
	] );

	Alert :: set( lang()->g( 'The changes have been saved' ) );
	return true;
}

/*
	Create a new page

	The array $args support all the keys from variable $dbFields of the class pages.class.php
	If you don't pass all the keys, the default values are used, the default values are from $dbFields in the class pages.class.php
*/
function create_page( $args ) {

	// Check if the autosave page exists for this new page and delete it.
	if ( isset( $args['uuid'] ) ) {

		// Auto save page key.
		$key = pages()->getByUUID( 'autosave-' . $args['uuid'] );

		if ( ! empty( $key ) ) {
			Log :: set( 'Function create_page()' . LOG_SEP . 'Autosave deleted for ' . $args['title'], LOG_TYPE_INFO );
			delete_page( $key );
		}
	}

	// The user is always the one logged.
	$args['username'] = Session :: get( 'username' );

	if ( empty( $args['username'] ) ) {
		Log :: set( 'Function create_page()' . LOG_SEP . 'Empty username.', LOG_TYPE_ERROR );
		return false;
	}

	$key = pages()->add( $args );
	if ( $key ) {

		// Call the plugins after page created.
		Theme :: plugins( 'afterPageCreate', [ $key ] );

		reindex_categories();
		reindex_tags();

		// Add to syslog.
		syslog()->add( [
			'dictionaryKey' => 'new-content-created',
			'notes' => ( empty( $args['title'] ) ? $key : $args['title'] )
		] );

		return $key;
	}

	Log :: set( 'Function createNewPage()' . LOG_SEP . 'Error occurred when trying to create the page', LOG_TYPE_ERROR );
	Log :: set( 'Function createNewPage()' . LOG_SEP . 'Cleaning database&hellip;', LOG_TYPE_ERROR );
	delete_page( $key );
	Log :: set( 'Function createNewPage()' . LOG_SEP . 'Cleaning finished&hellip;', LOG_TYPE_ERROR );

	return false;
}

function edit_page( $args ) {

	// Check if the autosave/preview page exists for this new page and delete it.
	if ( isset( $args['uuid'] ) ) {
		$autosaveKey = pages()->getByUUID( 'autosave-' . $args['uuid'] );
		if ( $autosaveKey ) {
			Log :: set( 'Function edit_page()' . LOG_SEP . 'Autosave/Preview deleted for ' . $autosaveKey, LOG_TYPE_INFO );
			delete_page( $autosaveKey );
		}
	}

	// Check if the key is not empty.
	if ( empty( $args['key'] ) ) {
		Log :: set( 'Function edit_page()' . LOG_SEP . 'Empty key.', LOG_TYPE_ERROR );
		return false;
	}

	// Check if the page key exist.
	if ( ! pages()->exists( $args['key'] ) ) {
		Log :: set( 'Function edit_page()' . LOG_SEP . 'Page key does not exist, ' . $args['key'], LOG_TYPE_ERROR );
		return false;
	}

	$key = pages()->edit( $args );
	if ( $key ) {

		// Call the plugins after page modified.
		Theme :: plugins( 'afterPageModify', [ $key ] );

		reindex_categories();
		reindex_tags();

		// Add to syslog.
		syslog()->add( [
			'dictionaryKey' => 'content-edited',
			'notes' => empty( $args['title'] ) ? $key : $args['title']
		] );
		return $key;
	}

	Log :: set( 'Function edit_page()' . LOG_SEP . 'Something happen when try to edit the page.', LOG_TYPE_ERROR );

	return false;
}

function delete_page( $key ) {

	if ( pages()->delete( $key ) ) {

		// Call the plugins after page deleted.
		Theme :: plugins( 'afterPageDelete', [ $key ] );

		reindex_categories();
		reindex_tags();

		// Add to syslog.
		syslog()->add( [
			'dictionaryKey' => 'content-deleted',
			'notes' => $key
		] );
		return true;
	}
	return false;
}

// Add a new category to the system
// Returns TRUE is successfully added, FALSE otherwise
function create_category( $args ) {

	if ( Text :: isEmpty( $args['name'] ) ) {
		Alert :: set( lang()->g( 'Category name is empty' ), ALERT_STATUS_FAIL );
		return false;
	}

	if ( cats()->add( [ 'name' => $args['name'], 'description' => $args['description'] ] ) ) {

		// Add to syslog.
		syslog()->add( [
			'dictionaryKey' => 'new-category-created',
			'notes' => $args['name']
		] );

		Alert :: set( lang()->g( 'Category added' ), ALERT_STATUS_OK );
		return true;
	}
	Alert :: set( lang()->g( 'The category already exists' ), ALERT_STATUS_FAIL );
	return false;
}

function edit_category( $args ) {

	if ( Text :: isEmpty( $args['name'] ) || Text :: isEmpty( $args['newKey'] ) ) {
		Alert :: set( lang()->g( 'Empty fields' ) );
		return false;
	}

	$key = cats()->edit( $args );
	if ( $key == false ) {
		Alert :: set( lang()->g( 'The category already exists' ) );
		return false;
	}

	// Change the category key in the pages database.
	pages()->changeCategory( $args['oldKey'], $key );

	// Add to syslog.
	syslog()->add( [
		'dictionaryKey' => 'category-edited',
		'notes' => $key
	] );

	Alert :: set( lang()->g( 'The changes have been saved' ) );
	return true;
}

function delete_category( $args ) {

	// Remove the category by key.
	cats()->remove( $args['oldKey'] );

	// Remove the category from the pages ? or keep it if the user want to recovery the category?

	// Add to syslog.
	syslog()->add( [
		'dictionaryKey' => 'category-deleted',
		'notes' => $args['oldKey']
	] );

	Alert :: set( lang()->g( 'The changes have been saved' ) );
	return true;
}

// Returns an array with all the categories.
// By default, the database of categories is alphanumeric sorted.
function get_categories() {

	$list = [];
	foreach ( cats()->keys() as $key ) {
		$category = new Category( $key );
		array_push( $list, $category );
	}
	return $list;
}

function create_user( $args ) {

	$args['new_username'] = Text :: removeSpecialCharacters( $args['new_username'] );

	// Check empty username.
	if ( Text :: isEmpty( $args['new_username'] ) ) {
		Alert :: set( lang()->g( 'username-field-is-empty' ), ALERT_STATUS_FAIL );
		return false;
	}

	// Check already exist username.
	if ( users()->exists( $args['new_username'] ) ) {
		Alert :: set( lang()->g( 'username-already-exists' ), ALERT_STATUS_FAIL );
		return false;
	}

	// Password length.
	if ( Text::length( $args['new_password'] ) < PASSWORD_LENGTH ) {
		Alert :: set( lang()->g( 'Password must be at least ' . PASSWORD_LENGTH . ' characters long' ), ALERT_STATUS_FAIL );
		return false;
	}

	// Check new password and confirm password are equal.
	if ( $args['new_password'] != $args['confirm_password'] ) {
		Alert :: set( lang()->g( 'The password and confirmation password do not match' ), ALERT_STATUS_FAIL );
		return false;
	}

	// Filter form fields.
	$tmp = [];
	$tmp['username'] = $args['new_username'];
	$tmp['password'] = $args['new_password'];
	$tmp['role']     = $args['role'];
	$tmp['email']    = $args['email'];

	// Add the user to the database.
	if ( users()->add( $tmp ) ) {

		// Add to syslog.
		syslog()->add( [
			'dictionaryKey' => 'new-user-created',
			'notes' => $tmp['username']
		] );
		return true;
	}
	return false;
}

function edit_user( $args ) {

	if ( users()->set( $args ) ) {

		// Add to syslog.
		syslog()->add( [
			'dictionaryKey' => 'user-edited',
			'notes' => $args['username']
		] );
		return true;
	}
	return false;
}

function change_user_password( $args ) {

	// Arguments.
	$username = $args['username'];
	$password = $args['newPassword'];
	$confirm  = $args['confirmPassword'];

	// Password length.
	if ( Text :: length( $password ) < 6 ) {
		Alert :: set( lang()->g( 'Password must be at least 6 characters long' ), ALERT_STATUS_FAIL );
		return false;
	}

	if ( $password != $confirm ) {
		Alert :: set( lang()->g( 'The password and confirmation password do not match' ), ALERT_STATUS_FAIL );
		return false;
	}

	if ( users()->setPassword( [ 'username' => $username, 'password' => $password ] ) ) {

	// Add to syslog.
	syslog()->add( [
		'dictionaryKey' => 'user-password-changed',
		'notes' => $username
	] );

		Alert :: set( lang()->g( 'The changes have been saved' ), ALERT_STATUS_OK );
		return true;
	}
	return false;
}

function disable_user( $args ) {

	// Access global variables.
	global $login;

	// Arguments.
	$username = $args['username'];

	// Only administrators can disable users.
	if ( $login->role() !== 'admin' ) {
		return false;
	}

	// Check if the username exists.
	if ( ! users()->exists( $username ) ) {
		return false;
	}

	// Disable the user.
	if ( users()->disable_user( $username ) ) {

		// Add to syslog.
		syslog()->add( [
			'dictionaryKey' => 'user-disabled',
			'notes' => $username
		] );
		return true;
	}
	return false;
}

function delete_user( $args ) {

	// Arguments
	$username = $args['username'];
	$deleteContent = isset( $args['deleteContent']) ? $args['deleteContent'] : false;

	// Only administrators can delete users.
	if ( login()->role() !== 'admin' ) {
		return false;
	}

	// The user admin cannot be deleted.
	if ( $username == 'admin' ) {
		return false;
	}

	// Check if the username exists.
	if ( ! users()->exists( $username ) ) {
		return false;
	}

	if ( $deleteContent ) {
		pages()->delete_pagesByUser( [ 'username' => $username ] );
	} else {
		pages()->transferPages( [ 'oldUsername' => $username ] );
	}

	if ( users()->delete( $username ) ) {

		// Add to syslog.
		syslog()->add( [
			'dictionaryKey' => 'user-deleted',
			'notes' => $username
		] );
		return true;
	}
	return false;
}

// Returns true if the user is allowed to proceed
function checkRole( $allowRoles, $redirect = true ) {

	$userRole = login()->role();
	if ( in_array( $userRole, $allowRoles ) ) {
		return true;
	}

	if ( $redirect ) {

		// Add to syslog.
		syslog()->add( [
			'dictionaryKey' => 'access-denied',
			'notes' => login()->username()
		] );

		Alert :: set( lang()->g( 'You do not have sufficient permissions' ) );
		Redirect :: page( 'dashboard' );
	}
	return false;
}

// Returns the object category if the category exists, FALSE otherwise.
function getCategory( $key ) {

	try {
		$category = new Category( $key );
		return $category;
	} catch ( Exception $e ) {
		return false;
	}
}

// Returns an array with all the tags.
// By default, the database of tags is alphanumeric sorted.
function getTags() {

	$list = [];
	foreach ( tags()->db as $key => $fields ) {
		$tag = new Tag( $key );
		array_push( $list, $tag );
	}
	return $list;
}

// Returns the object tag if the tag exists, false otherwise.
function getTag( $key ) {

	try {
		$tag = new Tag( $key );
		return $tag;
	} catch ( Exception $e ) {
		return false;
	}
}

// Activate a theme.
function activateTheme( $directory ) {

	if ( Sanitize :: pathFile( PATH_THEMES . $directory ) ) {

		// Disable current theme.
		$current = site()->theme();
		deactivate_plugin( $current );

		// Install new theme.
		if ( Filesystem :: fileExists( PATH_THEMES . $directory . DS . 'install.php' ) ) {
			include_once( PATH_THEMES . $directory . DS . 'install.php' );
		}

		// Install theme's plugin.
		activate_plugin( $directory );

		$site->set( [ 'theme' => $directory ] );

		syslog()->add( [
			'dictionaryKey' => 'new-theme-configured',
			'notes' => $directory
		] );

		Alert :: set( lang()->g( 'The changes have been saved' ) );
		return true;
	}
	return false;
}

function ajaxResponse( $status = 0, $message = '', $data = [] ) {
	$default = [ 'status' => $status, 'message' => $message ];
	$output  = array_merge( $default, $data );
	exit( json_encode( $output ) );
}

/*
| This function checks the image extension,
| generate a new filename to not overwrite the exists,
| generate the thumbnail,
| and move the image to a proper place
|
| @file		string	Path and filename of the image
| @imageDir	string	Path where the image is going to be stored
| @thumbnailDir	string	Path where the thumbnail is going to be stored, if you don't set the variable is not going to create the thumbnail
|
| @return	string/boolean	Path and filename of the new image or FALSE if there were some error
*/
function transformImage( $file, $images, $thumbs = false ) {

	// Check image extension.
	$ext = Filesystem :: extension( $file );
	$ext = Text :: lowercase( $ext );

	if ( ! in_array( $ext, $GLOBALS['ALLOWED_IMG_EXTENSION'] ) ) {
		return false;
	}

	// Generate a filename to not overwrite current image if exists.
	$name = Filesystem :: filename( $file );
	$next = Filesystem :: nextFilename( $name, $images );

	// Move the image to a proper place and rename.
	$image = $images . $next;
	Filesystem :: mv( $file, $image );
	chmod( $image, 0644 );

	// Generate thumbnail.
	if ( ! empty( $thumbs ) ) {
		if ( ( $ext == 'svg' ) || ( $ext == 'webp' ) ) {
			Filesystem :: symlink( $image, $thumbs . $next );
		} else {
			$Image = new Image();
			$Image->setImage( $image, site()->thumbnailWidth(), site()->thumbnailHeight(), 'crop' );
			$Image->saveImage( $thumbs . $next, site()->thumbnailQuality(), true );
		}
	}
	return $image;
}

function downloadRestrictedFile( $file ) {

	if ( is_file( $file ) ) {
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename="' . basename( $file ) . '"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $file ) );
		readfile( $file );
		exit(0);
	}
}

function editSettings( $args ) {

	if ( isset( $args['language'] ) ) {

		if ( $args['language'] != site()->language() ) {

			$tmp = new dbJSON( PATH_LANGUAGES . $args['language'] . '.json', false );
			if ( isset( $tmp->db['language-data']['locale'] ) ) {
				$args['locale'] = $tmp->db['language-data']['locale'];
			} else {
				$args['locale'] = $args['language'];
			}
		}
	}

	if ( empty( $args['homepage'] ) ) {
		$args['homepage'] = '';
		$args['uriBlog']  = '';
	}

	if ( empty( $args['pageNotFound'] ) ) {
		$args['pageNotFound'] = '';
	}

	if ( isset( $args['uriPage'] ) ) {
		$args['uriPage'] = Text :: addSlashes( $args['uriPage'] );
	}

	if ( isset( $args['uriTag'] ) ) {
		$args['uriTag'] = Text :: addSlashes( $args['uriTag'] );
	}

	if ( isset( $args['uriCategory'] ) ) {
		$args['uriCategory'] = Text :: addSlashes( $args['uriCategory'] );
	}

	if ( ! empty( $args['uriBlog'] ) ) {
		$args['uriBlog'] = Text :: addSlashes( $args['uriBlog'] );
	} else {
		if ( ! empty( $args['homepage'] ) && empty( $args['uriBlog'] ) ) {
			$args['uriBlog'] = '/blog/';
		} else {
			$args['uriBlog'] = '';
		}
	}

	if ( isset($args['extremeFriendly'] ) ) {
		$args['extremeFriendly'] = ( ( $args['extremeFriendly'] == 'true' ) ? true : false );
	}

	if ( isset( $args['customFields'] ) ) {

		// Custom fields need to be JSON format valid, also the empty JSON need to be "{}".
		json_decode( $args['customFields'] );
		if ( json_last_error() != JSON_ERROR_NONE ) {
			return false;
		}
		pages()->setCustomFields( $args['customFields'] );
	}

	if ( site()->set( $args ) ) {

		// Check current order-by if changed it reorder the content.
		if ( site()->orderBy() != ORDER_BY ) {
			if ( site()->orderBy() == 'date' ) {
				pages()->sortByDate();
			} else {
				pages()->sortByPosition();
			}
			pages()->save();
		}

		// Add syslog
		syslog()->add( [
			'dictionaryKey' => 'settings-changes',
			'notes' => ''
		] );

		// Create alert.
		Alert :: set( lang()->g( 'The changes have been saved' ) );
		return true;
	}
	return false;
}
