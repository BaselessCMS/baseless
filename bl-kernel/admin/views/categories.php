<?php
/**
 * Categories page
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Views
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

?>
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
			<td><a href="<?php echo HTML_PATH_ADMIN_ROOT . 'edit-category/' . $key; ?>"><?php echo $cat->name(); ?></a></td>
			<td><a href="<?php echo $cat->permalink(); ?>"><?php echo url()->filters( 'category', false ) . $key; ?></a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
