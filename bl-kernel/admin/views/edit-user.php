<?php
/**
 * Edit user page
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

$nickname = ucwords( str_replace( [ '-', '_' ], ' ', $user->username() ) );
if ( $user->nickname() ) {
	$nickname = $user->nickname();
}

?>
<form class="tab-content" id="jsform" method="post" action="" autocomplete="off">

	<header class="admin-page-header has-actions">
		<h1><?php lang()->p( 'Edit User:' ); ?> <?php echo $nickname; ?></h1>
		<div class="form-actions admin-form-actions">
			<button type="submit" class="btn btn-primary" name="save"><?php lang()->p( 'Save' ); ?></button>
			<a class="btn btn-secondary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'users'; ?>" role="button"><?php lang()->p( 'Cancel' ); ?></a>
		</div>
	</header>

	<nav class="tabbed-tabs admin-tabbed-tabs">
		<div class="nav nav-tabs" id="nav-tab" role="tablist">

			<a class="nav-item nav-link active" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"><?php lang()->p( 'Profile' ) ?></a>

			<a class="nav-item nav-link" id="nav-avatar-tab" data-toggle="tab" href="#avatar" role="tab" aria-controls="nav-avatar" aria-selected="false"><?php lang()->p( 'Avatar' ) ?></a>

			<a class="nav-item nav-link" id="nav-links-tab" data-toggle="tab" href="#links" role="tab" aria-controls="nav-links" aria-selected="false"><?php lang()->p( 'Links' ) ?></a>

			<a class="nav-item nav-link" id="nav-security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="nav-security" aria-selected="false"><?php lang()->p( 'Security' ) ?></a>
		</div>
	</nav>

	<?php
	echo Bootstrap :: formInputHidden( [
		'name'  => 'tokenCSRF',
		'value' => $security->getTokenCSRF()
	] );

	echo Bootstrap :: formInputHidden( [
		'name'  => 'username',
		'value' => $user->username()
	] );
	?>

	<div class="tab-content" id="nav-tabContent">

		<div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="nav-profile-tab">
			<?php
			// Display username but disable the field.
			echo Bootstrap :: formInputText( [
				'name'        => 'usernameDisabled',
				'label'       => lang()->g( 'Username' ),
				'value'       => $user->username(),
				'class'       => '',
				'placeholder' => '',
				'disabled'    => true,
				'tip'         => ''
			] );

			if ( 'admin' === $login->role() ) {
				echo Bootstrap :: formSelect( [
					'name'     => 'role',
					'label'    => lang()->g( 'Role' ),
					'options'  => [ 'author'=>lang()->g( 'Author' ), 'editor'=>lang()->g( 'Editor' ), 'admin'=>lang()->g( 'Administrator' ) ],
					'selected' => $user->role(),
					'class'    => '',
					'tip'      => lang()->g( 'author-can-write-and-edit-their-own-content' )
				] );
			}

			echo Bootstrap :: formInputText( [
				'name'        => 'email',
				'label'       => lang()->g( 'Email' ),
				'value'       => $user->email(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'nickname',
				'label'       => lang()->g( 'Nickname' ),
				'value'       => $nickname,
				'class'       => '',
				'placeholder' => $nickname,
				'tip'         => lang()->g( 'The nickname is almost used in the themes to display the author of the content' )
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'firstName',
				'label'       => lang()->g( 'First Name' ),
				'value'       => $user->firstName(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'lastName',
				'label'       => lang()->g( 'Last Name' ),
				'value'       => $user->lastName(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'website',
				'label'       => lang()->g( 'Website' ),
				'value'       => $user->website(),
				'class'       => '',
				'placeholder' => 'https://',
				'tip'         => ''
			] );
			?>
		</div>

		<div class="tab-pane fade" id="avatar" role="tabpanel" aria-labelledby="nav-avatar-tab">
			<div class="container">
				<div class="row">
					<div class="col-lg-4 col-sm-12 p-0 pr-2">
						<div class="custom-file">
							<input type="file" class="custom-file-input" id="jsprofilePictureInputFile" name="profilePictureInputFile">
							<label class="custom-file-label" for="jsprofilePictureInputFile"><?php lang()->p( 'Upload Image' ); ?></label>
						</div>
						<!-- <button id="jsbuttonRemovePicture" type="button" class="btn btn-primary w-100 mt-4 mb-4"><i class="fa fa-trash"></i> Remove picture</button> -->
					</div>
					<div class="col-lg-8 col-sm-12 p-0 text-center">
						<img id="jsprofilePicturePreview" class="img-fluid img-thumbnail" alt="Profile picture preview" src="<?php echo ( \Sanitize :: pathFile( PATH_UPLOADS_PROFILES . $user->username() . '.png' ) ? DOMAIN_UPLOADS_PROFILES . $user->username() . '.png?version=' . time() : HTML_PATH_CORE_IMG . 'default.svg' ) ?>" />
					</div>
				</div>
			</div>
			<script>
			// $("#jsbuttonRemovePicture").on("click", function() {
			// 	var username = $("#jsusername").val();
			// 	bluditAjax.removeProfilePicture(username);
			// 	$("#jsprofilePicturePreview").attr("src", "<?php // echo HTML_PATH_CORE_IMG.'default.svg' ?>");
			// });

			$( '#jsprofilePictureInputFile' ).on( 'change', function() {

				var formData = new FormData();
				formData.append( 'tokenCSRF', tokenCSRF);
				formData.append( 'profilePictureInputFile', $(this)[0].files[0] );
				formData.append( 'username', $( '#jsusername' ).val() );
				$.ajax( {
					url         : HTML_PATH_ADMIN_ROOT + 'ajax/profile-picture-upload',
					type        : "POST",
					data        : formData,
					cache       : false,
					contentType : false,
					processData : false
				} ).done(function(data) {
					if ( data.status == 0 ) {
						$( '#jsprofilePicturePreview' ).attr( 'src',data.absoluteURL + '?time=' + Math.random() );
					} else {
						showAlert( data.message );
					}
				});
			});
			</script>
		</div>

		<div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="nav-security-tab">

			<div class="form-group row">
				<label for="change-user-password" class="col-sm-2 col-form-label"><?php lang()->p( 'Password' ); ?></label>
				<div class="col-sm-10">
					<a id="change-user-password" href="<?php echo HTML_PATH_ADMIN_ROOT . 'user-password/' . $user->username(); ?>" class="btn btn-primary mr-2"><?php lang()->p( 'Change Password' ); ?></a>
				</div>
			</div>

			<?php
			echo Bootstrap :: formInputText( [
				'name'  => 'tokenAuth',
				'label' => lang()->g( 'Token' ),
				'value' => $user->tokenAuth(),
				'class' => '',
				'tip'   => lang()->g( 'this-token-is-similar-to-a-password-it-should-not-be-shared' )
			] );

			$users_db = $users->db;
			if ( checkRole( [ 'admin' ], false ) && count( $users_db ) > 1 ) : ?>
			<h2><?php lang()->p( 'Status' ); ?></h2>

			<?php
			echo Bootstrap :: formInputText( [
				'name'     => 'status',
				'label'    => lang()->g( 'Current status' ),
				'value'    => $user->enabled() ? lang()->g( 'Enabled' ) : lang()->g( 'Disabled' ),
				'class'    => '',
				'disabled' => true,
				'tip'      => $user->enabled() ? '' : lang()->g( 'To enable the user you must set a new password' )
			] );

			if ( $user->enabled() ) : ?>
			<div class="form-group row">
				<div class="col-sm-2"></div>
				<div class="col-sm-10">
					<button type="submit" class="btn btn-warning mr-2" id="jsdisableUser" name="disableUser"><?php lang()->p( 'Disable Uuser' ); ?></button>
					<button type="submit" class="btn btn-danger mr-2" id="jsdeleteUserAndKeepContent" name="deleteUserAndKeepContent"><?php lang()->p( 'Delete User' ); ?></button>
					<button type="submit" class="btn btn-danger mr-2" id="jsdeleteUserAndDeleteContent" name="deleteUserAndDeleteContent"><?php lang()->p( 'Delete User & Content' ); ?></button>
				</div>
			</div>
			<?php endif; endif;
		?>
		</div>

		<div class="tab-pane fade" id="links" role="tabpanel" aria-labelledby="nav-links-tab">
			<?php
			echo Bootstrap :: formInputText( [
				'name'        => 'twitter',
				'label'       => 'Twitter',
				'value'       => $user->twitter(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'facebook',
				'label'       => 'Facebook',
				'value'       => $user->facebook(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'instagram',
				'label'       => 'Instagram',
				'value'       => $user->instagram(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'youtube',
				'label'       => 'YouTube',
				'value'       => $user->youtube(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'vimeo',
				'label'       => 'Vimeo',
				'value'       => $user->vimeo(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'codepen',
				'label'       => 'CodePen',
				'value'       => $user->codepen(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'gitlab',
				'label'       => 'GitLab',
				'value'       => $user->gitlab(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'github',
				'label'       => 'GitHub',
				'value'       => $user->github(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'linkedin',
				'label'       => 'LinkedIn',
				'value'       => $user->linkedin(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'xing',
				'label'       => 'Xing',
				'value'       => $user->xing(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'mastodon',
				'label'       => 'Mastodon',
				'value'       => $user->mastodon(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );

			echo Bootstrap :: formInputText( [
				'name'        => 'vk',
				'label'       => 'VK',
				'value'       => $user->vk(),
				'class'       => '',
				'placeholder' => '',
				'tip'         => ''
			] );
			?>
		</div>
	</div>
</form>
<script>
	// Open current tab after refresh page
	$( function() {
		$( 'a[data-toggle="tab"]' ).on( 'click', function(e) {
			window.localStorage.setItem( 'activeTab', $(e.target).attr( 'href' ) );
			console.log( $( e.target ).attr( 'href' ) );
		});
		var activeTab = window.localStorage.getItem( 'activeTab' );
		if ( activeTab ) {
			$( '#nav-tab a[href="' + activeTab + '"]' ).tab( 'show' );
			//window.localStorage.removeItem("activeTab");
		}
	});
</script>
