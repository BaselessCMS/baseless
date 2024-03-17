<?php
/**
 * Functions
 *
 * @package  JSON CMS
 * @category Core
 * @since    1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

/**
 * Re-index database of categories
 *
 * If you create/edit/remove a page is necessary
 * regenerate the database of categories.
 *
 * @since  1.0.0
 * @global object $categories The Categories class.
 * @return method
 */
function reindexCategories() {

	// Access global variables.
	global $categories;
	return $categories->reindex();
}

/**
 * Re-index database of tags
 *
 * If you create/edit/remove a page is necessary
 * regenerate the database of tags.
 *
 * @since  1.0.0
 * @global object $tags The Tags class.
 * @return method
 */
function reindexTags() {

	// Access global variables.
	global $tags;
	return $tags->reindex();
}

/**
 * Build 404 page
 *
 * @since  1.0.0
 * @global object $L The TLanguage class.
 * @global object $site The Site class.
 * @return object Instance of the Page class.
 */
function buildErrorPage() {

	// Access global variables.
	global $L, $site;

	try {
		$pageNotFoundKey = $site->pageNotFound();
		$pageNotFound = new Page( $pageNotFoundKey );
	} catch ( Exception $e ) {
		$pageNotFound = new Page( false );
		$pageNotFound->setField( 'title',   $L->get( 'page-not-found' ) );
		$pageNotFound->setField( 'content',   $L->get( 'page-not-found-content' ) );
		$pageNotFound->setField( 'username',   'admin' );
	}
	return $pageNotFound;
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
// This function generate a particular page from the current slug of the url
// If the slug has not a page associated returns FALSE and set not-found as true
function buildThePage() {

	// Access global variables.
	global $url;

	try {
		$pageKey = $url->slug();
		$page = new Page($pageKey);
	} catch ( Exception $e ) {
		$url->setNotFound();
		return false;
	}

	if ( $page->draft() || $page->scheduled() || $page->autosave() ) {
		if ( $url->parameter( 'preview' ) !== md5( $page->uuid() ) ) {
			$url->setNotFound();
			return false;
		}
	}
	return $page;
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function buildPagesForHome() {
	return buildPagesFor( 'home' );
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function buildPagesByCategory() {

	// Access global variables.
	global $url;

	$categoryKey = $url->slug();
	return buildPagesFor( 'category', $categoryKey, false );
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
function buildPagesByTag() {

	// Access global variables.
	global $url;

	$tagKey = $url->slug();
	return buildPagesFor( 'tag', false, $tagKey );
}

// This function is only used from the rule 69.pages.php, DO NOT use this function!
// Generate the global variables $content / $content, defined on 69.pages.php
// This function is use for buildPagesForHome(), buildPagesByCategory(), buildPagesByTag()
function buildPagesFor( $for, $categoryKey = false, $tagKey = false ) {

	// Access global variables.
	global $categories, $pages, $site, $tags, $url;

	// Get the page number from URL
	$pageNumber = $url->pageNumber();

	if ( $for == 'home' ) {

		$onlyPublished = true;
		$numberOfItems = $site->itemsPerPage();
		$list = $pages->getList( $pageNumber, $numberOfItems, $onlyPublished );

		// Include sticky pages only in the first page.
		if ( $pageNumber == 1 ) {
			$sticky = $pages->getStickyDB();
			$list   = array_merge( $sticky, $list );
		}
	} elseif ( $for == 'category' ) {
		$numberOfItems = $site->itemsPerPage();
		$list = $categories->getList( $categoryKey, $pageNumber, $numberOfItems );
	} elseif ( $for == 'tag' ) {
		$numberOfItems = $site->itemsPerPage();
		$list = $tags->getList( $tagKey, $pageNumber, $numberOfItems );
	}

	// There are not items, invalid tag, invalid category, out of range, etc.
	if ( $list === false ) {
		$url->setNotFound();
		return false;
	}

	$content = [];
	foreach ( $list as $pageKey ) {
		try {
			$page = new Page( $pageKey );
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

// Returns an array with all the static pages as Page-Object
// The static pages are order by position all the time
function buildStaticPages() {

	// Access global variables.
	global $pages;

	$list     = [];
	$pagesKey = $pages->getStaticDB();
	foreach ( $pagesKey as $pageKey ) {
		try {
			$page = new Page( $pageKey );
			array_push( $list, $page );
		} catch ( Exception $e ) {
			// Continue.
		}
	}
	return $list;
}

// Returns the Page-Object if exists, FALSE otherwise
function buildPage( $pageKey ) {

	try {
		$page = new Page( $pageKey );
		return $page;
	} catch ( Exception $e ) {
		return false;
	}
}

// Returns an array with all the parent pages as Page-Object
// The pages are order by the settings on the system
function buildParentPages() {

	// Access global variables.
	global $pages;

	$list     = [];
	$pagesKey = $pages->getPublishedDB();
	foreach ( $pagesKey as $pageKey ) {
		try {
			$page = new Page( $pageKey );
			if ( $page->isParent() ) {
			array_push( $list, $page );
			}
		} catch ( Exception $e ) {
			// Continue.
		}
	}
	return $list;
}

// Returns the Plugin-Object if is enabled and installed, FALSE otherwise
function getPlugin( $pluginClassName ) {

	// Access global variables.
	global $plugins;

	if ( pluginActivated( $pluginClassName ) ) {
		return $plugins['all'][$pluginClassName];
	}
	return false;
}

// Returns TRUE if the plugin is activaed / installed, FALSE otherwise
function pluginActivated( $pluginClassName ) {

	// Access global variables.
	global $plugins;

	if ( isset( $plugins['all'][$pluginClassName] ) ) {
		return $plugins['all'][$pluginClassName]->installed();
	}
	return false;
}

function activatePlugin( $pluginClassName ) {

	// Access global variables.
	global $L, $plugins, $syslog;

	// Check if the plugin exists.
	if ( isset( $plugins['all'][$pluginClassName] ) ) {

		$plugin = $plugins['all'][$pluginClassName];
		if ( $plugin->install() ) {

			// Add to syslog.
			$syslog->add( [
				'dictionaryKey' => 'plugin-activated',
				'notes' => $plugin->name()
			] );

			// Create an alert.
			Alert :: set( $L->g( 'plugin-activated' ) );
			return true;
		}
	}
	return false;
}

function deactivatePlugin( $pluginClassName ) {

	// Access global variables.
	global $L, $plugins, $syslog;

	// Check if the plugin exists
	if ( isset( $plugins['all'][$pluginClassName] ) ) {

		$plugin = $plugins['all'][$pluginClassName];

		if ( $plugin->uninstall() ) {

			// Add to syslog.
			$syslog->add( [
				'dictionaryKey' => 'plugin-deactivated',
				'notes' => $plugin->name()
			] );

			// Create an alert.
			Alert :: set( $L->g( 'plugin-deactivated' ) );
			return true;
		}
	}
	return false;
}

function deactivateAllPlugin() {

	// Access global variables.
	global $L, $plugins, $syslog;

	// Check if the plugin exists.
	foreach ( $plugins['all'] as $plugin ) {
		if ( $plugin->uninstall() ) {

			// Add to syslog.
			$syslog->add( [
				'dictionaryKey' => 'plugin-deactivated',
				'notes' => $plugin->name()
			] );
		}
	}
	return false;
}

function changePluginsPosition( $pluginClassList ) {

	// Access global variables.
	global $L, $plugins, $syslog;

	foreach ( $pluginClassList as $position => $pluginClassName ) {
		if ( isset( $plugins['all'][$pluginClassName] ) ) {
			$plugin = $plugins['all'][$pluginClassName];
			$plugin->setPosition( ++$position );
		}
	}

	// Add to syslog.
	$syslog->add( [
		'dictionaryKey' => 'plugins-sorted',
		'notes' => ''
	] );

	Alert :: set( $L->g( 'The changes have been saved' ) );
	return true;
}

/*
	Create a new page

	The array $args support all the keys from variable $dbFields of the class pages.class.php
	If you don't pass all the keys, the default values are used, the default values are from $dbFields in the class pages.class.php
*/
function createPage( $args ) {

	// Access global variables.
	global $L, $pages, $plugins, $syslog;

	// Check if the autosave page exists for this new page and delete it.
	if ( isset( $args['uuid'] ) ) {

		$autosaveKey = $pages->getByUUID( 'autosave-' . $args['uuid'] );

		if ( ! empty( $autosaveKey ) ) {
			Log :: set( 'Function createPage()' . LOG_SEP . 'Autosave deleted for ' . $args['title'], LOG_TYPE_INFO );
			deletePage( $autosaveKey );
		}
	}

	// The user is always the one logged.
	$args['username'] = Session :: get( 'username' );

	if ( empty( $args['username'] ) ) {
		Log :: set( 'Function createPage()' . LOG_SEP . 'Empty username.', LOG_TYPE_ERROR );
		return false;
	}

	$key = $pages->add( $args );
	if ( $key ) {

		// Call the plugins after page created.
		Theme :: plugins( 'afterPageCreate', [ $key ] );

		reindexCategories();
		reindexTags();

		// Add to syslog.
		$syslog->add( [
			'dictionaryKey' => 'new-content-created',
			'notes' => ( empty($args['title'] ) ? $key : $args['title'] )
		] );

		return $key;
	}

	Log :: set( 'Function createNewPage()' . LOG_SEP . 'Error occurred when trying to create the page', LOG_TYPE_ERROR );
	Log :: set( 'Function createNewPage()' . LOG_SEP . 'Cleaning database...', LOG_TYPE_ERROR );
	deletePage( $key );
	Log :: set( 'Function createNewPage()' . LOG_SEP . 'Cleaning finished...', LOG_TYPE_ERROR );

	return false;
}

function editPage( $args ) {

	// Access global variables.
	global $pages, $syslog;

	// Check if the autosave/preview page exists for this new page and delete it.
	if ( isset( $args['uuid'] ) ) {
		$autosaveKey = $pages->getByUUID( 'autosave-' . $args['uuid'] );
		if ( $autosaveKey ) {
			Log :: set( 'Function editPage()' . LOG_SEP . 'Autosave/Preview deleted for ' . $autosaveKey, LOG_TYPE_INFO );
			deletePage( $autosaveKey );
		}
	}

	// Check if the key is not empty.
	if ( empty( $args['key'] ) ) {
		Log :: set( 'Function editPage()' . LOG_SEP . 'Empty key.', LOG_TYPE_ERROR );
		return false;
	}

	// Check if the page key exist.
	if ( ! $pages->exists( $args['key'] ) ) {
		Log :: set( 'Function editPage()' . LOG_SEP . 'Page key does not exist, ' . $args['key'], LOG_TYPE_ERROR );
		return false;
	}

	$key = $pages->edit( $args );
	if ( $key ) {

		// Call the plugins after page modified.
		Theme :: plugins( 'afterPageModify', [ $key ] );

		reindexCategories();
		reindexTags();

		// Add to syslog.
		$syslog->add( [
			'dictionaryKey' => 'content-edited',
			'notes' => empty( $args['title'] ) ? $key : $args['title']
		] );
		return $key;
	}

	Log :: set( 'Function editPage()' . LOG_SEP . 'Something happen when try to edit the page.', LOG_TYPE_ERROR );

	return false;
}

function deletePage( $key ) {

	// Access global variables.
	global $pages, $syslog;

	if ( $pages->delete( $key ) ) {

		// Call the plugins after page deleted.
		Theme :: plugins( 'afterPageDelete', [ $key ] );

		reindexCategories();
		reindexTags();

		// Add to syslog.
		$syslog->add( [
			'dictionaryKey' => 'content-deleted',
			'notes' => $key
		] );
		return true;
	}
	return false;
}

function editUser( $args ) {

	// Access global variables.
	global $syslog, $users;

	if ( $users->set( $args ) ) {

		// Add to syslog.
		$syslog->add( [
			'dictionaryKey' => 'user-edited',
			'notes' => $args['username']
		] );
		return true;
	}
	return false;
}

function disableUser( $args ) {

	// Access global variables.
	global $login, $syslog, $users;

	// Arguments.
	$username = $args['username'];

	// Only administrators can disable users.
	if ( $login->role() !== 'admin' ) {
		return false;
	}

	// Check if the username exists.
	if ( ! $users->exists( $username ) ) {
		return false;
	}

	// Disable the user.
	if ( $users->disableUser( $username ) ) {

		// Add to syslog.
		$syslog->add( [
			'dictionaryKey' => 'user-disabled',
			'notes' => $username
		] );
		return true;
	}
	return false;
}

function deleteUser( $args ) {

	// Access global variables.
	global $login, $pages, $syslog, $users;

	// Arguments
	$username = $args['username'];
	$deleteContent = isset( $args['deleteContent']) ? $args['deleteContent'] : false;

	// Only administrators can delete users.
	if ( $login->role() !== 'admin' ) {
		return false;
	}

	// The user admin cannot be deleted.
	if ( $username == 'admin' ) {
		return false;
	}

	// Check if the username exists.
	if ( ! $users->exists( $username ) ) {
		return false;
	}

	if ( $deleteContent ) {
		$pages->deletePagesByUser( [ 'username' => $username ] );
	} else {
		$pages->transferPages( [ 'oldUsername' => $username ] );
	}

	if ( $users->delete( $username ) ) {

		// Add to syslog.
		$syslog->add( [
			'dictionaryKey' => 'user-deleted',
			'notes' => $username
		] );
		return true;
	}
	return false;
}

function createUser( $args ) {

	// Access global variables.
	global $L, $syslog, $users;

	$args['new_username'] = Text :: removeSpecialCharacters( $args['new_username'] );

	// Check empty username.
	if ( Text :: isEmpty( $args['new_username'] ) ) {
		Alert :: set( $L->g( 'username-field-is-empty' ), ALERT_STATUS_FAIL );
		return false;
	}

	// Check already exist username.
	if ( $users->exists( $args['new_username'] ) ) {
		Alert :: set( $L->g( 'username-already-exists' ), ALERT_STATUS_FAIL );
		return false;
	}

	// Password length.
	if ( Text::length( $args['new_password'] ) < PASSWORD_LENGTH ) {
		Alert :: set( $L->g( 'Password must be at least ' . PASSWORD_LENGTH . ' characters long' ), ALERT_STATUS_FAIL );
		return false;
	}

	// Check new password and confirm password are equal.
	if ( $args['new_password'] != $args['confirm_password'] ) {
		Alert :: set( $L->g( 'The password and confirmation password do not match' ), ALERT_STATUS_FAIL );
		return false;
	}

	// Filter form fields.
	$tmp = [];
	$tmp['username'] = $args['new_username'];
	$tmp['password'] = $args['new_password'];
	$tmp['role']     = $args['role'];
	$tmp['email']    = $args['email'];

	// Add the user to the database.
	if ( $users->add( $tmp ) ) {

		// Add to syslog.
		$syslog->add( [
			'dictionaryKey' => 'new-user-created',
			'notes' => $tmp['username']
		] );
		return true;
	}
	return false;
}

function editSettings( $args ) {

	// Access global variables.
	global $L, $pages, $site, $syslog;

	if ( isset( $args['language'] ) ) {

		if ( $args['language'] != $site->language() ) {

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
		$pages->setCustomFields( $args['customFields'] );
	}

	if ( $site->set( $args ) ) {

		// Check current order-by if changed it reorder the content.
		if ( $site->orderBy() != ORDER_BY ) {
			if ( $site->orderBy() == 'date' ) {
				$pages->sortByDate();
			} else {
				$pages->sortByPosition();
			}
			$pages->save();
		}

		// Add syslog
		$syslog->add( [
			'dictionaryKey' => 'settings-changes',
			'notes' => ''
		] );

		// Create alert.
		Alert :: set( $L->g( 'The changes have been saved' ) );
		return true;
	}
	return false;
}

function changeUserPassword( $args ) {

	// Access global variables.
	global $L, $syslog, $users;

	// Arguments.
	$username        = $args['username'];
	$newPassword     = $args['newPassword'];
	$confirmPassword = $args['confirmPassword'];

	// Password length.
	if ( Text :: length( $newPassword ) < 6 ) {
		Alert :: set( $L->g( 'Password must be at least 6 characters long' ), ALERT_STATUS_FAIL );
		return false;
	}

	if ( $newPassword != $confirmPassword ) {
		Alert :: set( $L->g( 'The password and confirmation password do not match' ), ALERT_STATUS_FAIL );
		return false;
	}

	if ( $users->setPassword( [ 'username' => $username, 'password' => $newPassword ] ) ) {

	// Add to syslog.
	$syslog->add( [
		'dictionaryKey' => 'user-password-changed',
		'notes' => $username
	] );

		Alert :: set( $L->g( 'The changes have been saved' ), ALERT_STATUS_OK );
		return true;
	}
	return false;
}

// Returns true if the user is allowed to proceed
function checkRole( $allowRoles, $redirect = true ) {

	// Access global variables.
	global $L, $login, $syslog;

	$userRole = $login->role();
	if ( in_array( $userRole, $allowRoles ) ) {
		return true;
	}

	if ( $redirect ) {

		// Add to syslog.
		$syslog->add( [
			'dictionaryKey' => 'access-denied',
			'notes' => $login->username()
		] );

		Alert :: set( $L->g( 'You do not have sufficient permissions' ) );
		Redirect :: page( 'dashboard' );
	}
	return false;
}

// Add a new category to the system
// Returns TRUE is successfully added, FALSE otherwise
function createCategory( $args ) {

	// Access global variables.
	global $categories, $L, $syslog;

	if ( Text :: isEmpty( $args['name'] ) ) {
		Alert :: set( $L->g( 'Category name is empty' ), ALERT_STATUS_FAIL );
		return false;
	}

	if ( $categories->add( [ 'name' => $args['name'], 'description' => $args['description'] ] ) ) {

		// Add to syslog.
		$syslog->add( [
			'dictionaryKey' => 'new-category-created',
			'notes' => $args['name']
		] );

		Alert :: set( $L->g( 'Category added' ), ALERT_STATUS_OK );
		return true;
	}
	Alert :: set( $L->g( 'The category already exists' ), ALERT_STATUS_FAIL );
	return false;
}

function editCategory( $args ) {

	// Access global variables.
	global $categories, $L, $pages, $syslog;

	if ( Text :: isEmpty( $args['name'] ) || Text :: isEmpty( $args['newKey'] ) ) {
		Alert :: set( $L->g( 'Empty fields' ) );
		return false;
	}

	$newCategoryKey = $categories->edit( $args );

	if ( $newCategoryKey == false ) {
		Alert :: set( $L->g( 'The category already exists' ) );
		return false;
	}

	// Change the category key in the pages database.
	$pages->changeCategory($args['oldKey'], $newCategoryKey);

	// Add to syslog.
	$syslog->add( [
		'dictionaryKey' => 'category-edited',
		'notes' => $newCategoryKey
	] );

	Alert :: set( $L->g( 'The changes have been saved' ) );
	return true;
}

function deleteCategory( $args ) {

	// Access global variables.
	global $categories, $L, $syslog;

	// Remove the category by key.
	$categories->remove( $args['oldKey'] );

	// Remove the category from the pages ? or keep it if the user want to recovery the category?

	// Add to syslog.
	$syslog->add( [
		'dictionaryKey' => 'category-deleted',
		'notes' => $args['oldKey']
	] );

	Alert :: set( $L->g( 'The changes have been saved' ) );
	return true;
}

// Returns an array with all the categories.
// By default, the database of categories is alphanumeric sorted.
function getCategories() {

	// Access global variables.
	global $categories;

	$list = [];
	foreach ( $categories->keys() as $key ) {
		$category = new Category( $key );
		array_push( $list, $category );
	}
	return $list;
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

	// Access global variables.
	global $tags;

	$list = [];
	foreach ( $tags->db as $key => $fields ) {
		$tag = new Tag( $key );
		array_push( $list, $tag );
	}
	return $list;
}

// Returns the object tag if the tag exists, FALSE otherwise.
function getTag( $key ) {

	try {
		$tag = new Tag( $key );
		return $tag;
	} catch ( Exception $e ) {
		return false;
	}
}

// Activate a theme.
function activateTheme( $themeDirectory ) {

	// Access global variables.
	global $L, $language, $site, $syslog;

	if ( Sanitize :: pathFile( PATH_THEMES . $themeDirectory ) ) {

		// Disable current theme.
		$currentTheme = $site->theme();
		deactivatePlugin( $currentTheme );

		// Install new theme.
		if ( Filesystem :: fileExists( PATH_THEMES . $themeDirectory . DS . 'install.php' ) ) {
			include_once( PATH_THEMES . $themeDirectory . DS . 'install.php' );
		}

		// Install theme's plugin.
		activatePlugin( $themeDirectory );

		$site->set( [ 'theme' => $themeDirectory ] );

		$syslog->add( [
			'dictionaryKey' => 'new-theme-configured',
			'notes' => $themeDirectory
		] );

		Alert :: set( $L->g( 'The changes have been saved' ) );
		return true;
	}
	return false;
}

function ajaxResponse( $status = 0, $message = "", $data = [] ) {
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
function transformImage( $file, $imageDir, $thumbnailDir = false ) {

	// Access global variables.
	global $site;

	// Check image extension
	$fileExtension = Filesystem :: extension( $file );
	$fileExtension = Text :: lowercase( $fileExtension );

	if ( ! in_array( $fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSION'] ) ) {
		return false;
	}

	// Generate a filename to not overwrite current image if exists.
	$filename     = Filesystem :: filename( $file );
	$nextFilename = Filesystem :: nextFilename( $filename, $imageDir );

	// Move the image to a proper place and rename.
	$image = $imageDir . $nextFilename;
	Filesystem :: mv( $file, $image );
	chmod( $image, 0644 );

	// Generate Thumbnail.
	if ( ! empty( $thumbnailDir ) ) {
		if ( ( $fileExtension == 'svg' ) || ( $fileExtension == 'webp' ) ) {
			Filesystem :: symlink( $image, $thumbnailDir . $nextFilename );
		} else {
			$Image = new Image();
			$Image->setImage( $image, $site->thumbnailWidth(), $site->thumbnailHeight(), 'crop' );
			$Image->saveImage( $thumbnailDir . $nextFilename, $site->thumbnailQuality(), true );
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
