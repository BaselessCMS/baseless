<!doctype html>
<html dir="auto" lang="<?php echo $L->currentLanguageShortVersion(); ?>" class="no-js">

<head>
	<title><?php $L->p( 'User Login' ); ?> | <?php echo $site->title(); ?></title>
	<meta charset="<?php echo CHARSET ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta name="robots" content="noindex, nofollow" />
	<?php

	// Change `<html>` 'no-js' class to 'js' if JavaScript is enabled.
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n"; ?>

	<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN_CORE_CSS; ?>style.min.css?version=<?php echo time(); ?>" />

	<script charset="utf-8" src="<?php echo DOMAIN_CORE_JS; ?>jquery.min.js?version=<?php echo time(); ?>"></script>
	<script charset="utf-8" src="<?php echo DOMAIN_CORE_JS; ?>jstz.min.js?version=<?php echo time(); ?>"></script>

	<?php \Theme :: plugins( 'loginHead' ); ?>
</head>

<body class="login-screen">
	<div class="admin-content">

		<?php \Theme :: plugins( 'loginBodyBegin' ); ?>

		<?php include( 'views/alert.php' ); ?>

		<?php if ( \Sanitize :: pathFile( PATH_ADMIN_VIEWS, $layout['view'] . '.php' ) ) {
			include( PATH_ADMIN_VIEWS . $layout['view'] . '.php' );
		} ?>

		<?php \Theme :: plugins( 'loginBodyEnd' ); ?>
	</div>
</body>
</html>
