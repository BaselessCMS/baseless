<!DOCTYPE html>
<html>

<head>
	<title>Bludit</title>
	<meta charset="<?php echo CHARSET ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta name="robots" content="noindex,nofollow" />

	<link rel="shortcut icon" type="image/x-icon" href="<?php echo HTML_PATH_CORE_IMG . 'favicon.png?version=' . BLUDIT_VERSION; ?>">

	<?php
	echo \Theme :: cssBootstrap();
	echo \Theme :: css( [
		'style.css',
		'bootstrap-mods.css'
	], DOMAIN_ADMIN_THEME . 'assets/css/' );
	?>

	<?php
	echo \Theme :: jquery();
	echo \Theme :: jsBootstrap();
	?>

	<?php \Theme :: plugins( 'loginHead' ); ?>
</head>

<body class="login-screen">

	<?php \Theme :: plugins( 'loginBodyBegin' ); ?>

	<?php include( 'views/alert.php' ); ?>

	<?php if ( \Sanitize :: pathFile( PATH_ADMIN_VIEWS, $layout['view'] . '.php' ) ) {
		include( PATH_ADMIN_VIEWS . $layout['view'] . '.php' );
	} ?>

	<?php \Theme :: plugins( 'loginBodyEnd' ); ?>
</body>
</html>
