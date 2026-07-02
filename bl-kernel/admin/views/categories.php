<?php
/**
 * Categories page
 *
 * @package    Baseless
 * @subpackage Admin
 * @category   Views
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

// Import namespaced functions.
use function CMS\Help\{
	site,
	security,
	login,
	url,
	lang,
	user,
	users,
	plugins,
	page,
	pages,
	cats
};

?>
<style>
.admin-table-heading {
	margin: var( --cms-admin-page--content--table--heading--margin, 0 );
	font-size: var( --cms-admin-page--content--table--heading--font-size, 1.125rem );
}
</style>
<header class="admin-page-header has-actions">

	<h1><?php lang()->p( 'Categories' ); ?></h1>

	<div class="form-actions admin-form-actions">
		<a class="btn btn-primary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'new-category' ?>" role="button"><?php lang()->p( 'Add Category' ); ?></a>
	</div>
</header>

<table class="table table-striped">
	<thead>
		<tr>
			<th scope="col"><?php lang()->p( 'Name' ); ?></th>
			<th scope="col"><?php lang()->p( 'URL' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ( cats()->keys() as $key ) :

		$cat = new \Category( $key );
		?>
		<tr>
			<td>
				<h2 class="admin-table-heading"><a href="<?php echo HTML_PATH_ADMIN_ROOT . 'edit-category/' . $key; ?>"><?php echo $cat->name(); ?></a></h2>
			</td>
			<td><a href="<?php echo $cat->permalink(); ?>"><?php echo url()->filters( 'category', false ) . $key; ?></a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
