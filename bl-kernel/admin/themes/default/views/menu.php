<?php
/**
 * Admin menu
 *
 * @package    JSON CMS
 * @subpackage Admin
 * @category   Themes
 * @since      1.0.0
 */

// Import namespaced functions.
use function CMS\Func\{
	check_role,
	svg_icon
};

?>
<ul id="admin-menu-list">
	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'dashboard'; ?>"><?php svg_icon( 'tachometer' ); ?> <span class="admin-menu-text"><?php $L->p( 'Dashboard' ); ?></span></a>
	</li>

	<?php if ( ! check_role( [ 'admin' ], false ) ) : ?>
	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'content'; ?>"><?php svg_icon( 'file' ); ?> <span class="admin-menu-text"><?php $L->p( 'Content' ); ?></span></a>
	</li>
	<?php endif; ?>

	<?php if ( ! check_role( [ 'admin' ], false ) ) : ?>
	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'edit-user/' . $login->username(); ?>"><?php svg_icon( 'user' ); ?> <span class="admin-menu-text"><?php $L->p( 'Profile' ); ?></span></a>
	</li>
	<?php endif; ?>

	<?php if ( check_role( [ 'admin' ], false ) ) : ?>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'content'; ?>"><?php svg_icon( 'file' ); ?> <span class="admin-menu-text"><?php $L->p( 'Content' ); ?></span></a>
	</li>
	<?php endif; ?>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'new-content'; ?>"><?php svg_icon( 'pencil' ); ?> <span class="admin-menu-text"><?php $L->p( 'Compose' ); ?></span></a>
	</li>

	<?php if ( check_role( [ 'admin' ], false ) ) : ?>
	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'categories'; ?>"><?php svg_icon( 'folder' ); ?> <span class="admin-menu-text"><?php $L->p( 'Categories' ); ?></span></a>
	</li>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'users'; ?>"><?php svg_icon( 'users' ); ?> <span class="admin-menu-text"><?php $L->p( 'Users' ); ?></span></a>
	</li>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'themes'; ?>"><?php svg_icon( 'paint-brush' ); ?> <span class="admin-menu-text"><?php $L->p( 'Themes' ); ?></span></a>
	</li>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'plugins'; ?>"><?php svg_icon( 'plug' ); ?> <span class="admin-menu-text"><?php $L->p( 'Plugins' ); ?></span></a>
	</li>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'settings'; ?>"><?php svg_icon( 'cog' ); ?> <span class="admin-menu-text"><?php $L->p( 'Settings' ); ?></span></a>
	</li>

	<?php endif; ?>

	<?php if ( check_role( [ 'admin', 'editor' ], false ) ) : ?>
		<?php
			if ( ! empty( $plugins['adminSidebar'] ) ) {
				echo '<li><hr /></li>';
				foreach ( $plugins['adminSidebar'] as $pluginSidebar ) {
					echo '<li>';
					echo $pluginSidebar->adminSidebar();
					echo '</li>';
				}
			}
		?>
	<?php endif; ?>
	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ROOT; ?>" target="_blank" rel="noopener noreferrer"><?php svg_icon( 'house' ); ?> <span class="admin-menu-text"><?php $L->p( 'Website' ); ?></span></a>
	</li>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'logout'; ?>"><?php svg_icon( 'arrow-alt-from-left' ); ?> <span class="admin-menu-text"><?php $L->p( 'Logout' ); ?></span></a>
	</li>
</ul>
