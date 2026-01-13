<!-- Use .flex-column to set a vertical direction -->
<ul id="admin-menu">

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'dashboard' ?>"><span class="fa fa-dashboard"></span><?php $L->p( 'Dashboard' ) ?></a>
	</li>

	<?php if ( ! checkRole( [ 'admin' ], false) ): ?>
	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'content' ?>"><span class="fa fa-archive"></span><?php $L->p( 'Content' ) ?></a>
	</li>
	<?php endif; ?>

	<?php if ( ! checkRole( [ 'admin' ], false) ): ?>
	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'edit-user/'.$login->username() ?>"><span class="fa fa-user"></span><?php $L->p( 'Profile' ) ?></a>
	</li>
	<?php endif; ?>

	<?php if ( checkRole( [ 'admin' ], false) ): ?>

	<li class="nav-item">
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'content' ?>"><span class="fa fa-folder"></span><?php $L->p( 'Content' ) ?></a>
	</li>
	<?php endif; ?>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'new-content' ?>"><span class="fa fa-plus-circle"></span><?php $L->p( 'New content' ) ?></a>
	</li>

	<?php if ( checkRole( [ 'admin' ], false) ): ?>
	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'categories' ?>"><span class="fa fa-bookmark"></span><?php $L->p( 'Categories' ) ?></a>
	</li>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'users' ?>"><span class="fa fa-users"></span><?php $L->p( 'Users' ) ?></a>
	</li>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'themes' ?>"><span class="fa fa-desktop"></span><?php $L->p( 'Themes' ) ?></a>
	</li>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'plugins' ?>"><span class="fa fa-plug"></span><?php $L->p( 'Plugins' ) ?></a>
	</li>

	<li class="nav-item">
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'settings' ?>"><span class="fa fa-gear"></span><?php $L->p( 'Settings' ) ?></a>
	</li>

	<?php endif; ?>

	<?php if ( checkRole( [ 'admin', 'editor' ], false ) ): ?>
		<?php
			if ( ! empty( $plugins['adminSidebar'] ) ) {
				echo '<li><hr></li>';
				foreach ( $plugins['adminSidebar'] as $pluginSidebar ) {
					echo '<li>';
					echo $pluginSidebar->adminSidebar();
					echo '</li>';
				}
			}
		?>
	<?php endif; ?>
	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ROOT ?>"><span class="fa fa-home"></span><?php $L->p( 'Website' ) ?></a>
	</li>

	<li>
		<a class="top-level-link" href="<?php echo HTML_PATH_ADMIN_ROOT . 'logout' ?>"><span class="fa fa-arrow-circle-right"></span><?php $L->p( 'Logout' ) ?></a>
	</li>
</ul>
