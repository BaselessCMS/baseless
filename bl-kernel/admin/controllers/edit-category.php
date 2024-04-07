<?php defined('BLUDIT') or die('Bludit CMS.');

// Import namespaced functions.
use function CMS\Help\{
	site,
	security,
	url,
	lang,
	users,
	plugins,
	page,
	pages,
	cats
};

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin'));

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($_POST['action']=='delete') {
		deleteCategory($_POST);
	} elseif ($_POST['action']=='edit') {
		editCategory($_POST);
	}

	Redirect::page('categories');
}

// ============================================================================
// Main after POST
// ============================================================================
$categoryKey = $layout['parameters'];

if (!$categories->exists($categoryKey)) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the category: '.$categoryKey);
	Redirect::page('categories');
}

$cat_map = cats()->getMap($categoryKey);

// Title of the page.
$layout['title'] .= sprintf(
	'%s | %s',
	$L->g( 'Edit Category' ),
	$site->title()
);
