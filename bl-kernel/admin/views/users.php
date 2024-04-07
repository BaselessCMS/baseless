<?php
/**
 * Users page
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

	<h1><?php lang()->p( 'Registered Users' ); ?></h1>

	<div class="form-actions admin-form-actions">
		<a class="btn btn-primary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'new-user' ?>" role="button"><?php lang()->p( 'Add User' ); ?></a>
	</div>
</header>

<table class="table table-striped">
	<thead>
		<tr>
			<th scope="col"><?php lang()->p( 'Avatar' ); ?></th>
			<th scope="col"><?php lang()->p( 'Username' ); ?></th>
			<th scope="col"><?php lang()->p( 'Nickname' ); ?></th>
			<th scope="col"><?php lang()->p( 'Email' ); ?></th>
			<th scope="col"><?php lang()->p( 'Status' ); ?></th>
			<th scope="col"><?php lang()->p( 'Role' ); ?></th>
			<th scope="col"><?php lang()->p( 'Registered' ); ?></th>
		</tr>
	</thead>

	<tbody>
	<?php
	$list = users()->keys();
	foreach ( $list as $username ) :
	try {
		$user = new \User( $username );

		$role = lang()->g( 'Reader' );
		if ( 'dev' == $user->role() ) {
			$role = lang()->g( 'Developer' );
		} elseif ( 'admin' == $user->role() ) {
			$role = lang()->g( 'Administrator' );
		} elseif ( 'editor' == $user->role() ) {
			$role = lang()->g( 'Editor' );
		} elseif ( 'author' == $user->role() ) {
			$role = lang()->g( 'Author' );
		} elseif ( 'member' == $user->role() ) {
			$role = lang()->g( 'Member' );
		} ?>
		<tr>
			<td>
				<img class="profilePicture mr-1" alt="" src="<?php echo ( \Sanitize :: pathFile( PATH_UPLOADS_PROFILES . $user->username() . '.png' ) ? DOMAIN_UPLOADS_PROFILES . $user->username() . '.png' : HTML_PATH_CORE_IMG . 'default.svg' ); ?>" />
			</td>
			<td>
				<h2 class="admin-table-heading"><a href="<?php echo HTML_PATH_ADMIN_ROOT . 'edit-user/' . $username; ?>"><?php echo $username; ?></a></h2>
			</td>

			<td><?php echo $user->nickname(); ?></td>

			<td><?php echo $user->email(); ?></td>

			<td><?php echo ( $user->enabled() ? lang()->g( 'Enabled' ) : lang()->g( 'Disabled' ) ); ?></td>

			<td><?php echo $role; ?></td>

			<td><?php echo \Date :: format( $user->registered(), DB_DATE_FORMAT, 'M j, Y' ); ?></td>
		</tr>
	<?php
	} catch ( Exception $e ) {
		// Continue.
	}
	endforeach; ?>
	</tbody>
</table>
