<?php
/**
 * Default JSON CMS theme
 *
 * Begin markup for frontend display.
 *
 * @package    JSON CMS
 * @subpackage Frontend
 * @category   Themes
 * @since      1.0.0
 */

?>
<head data-site-head>

	<meta name="robots" content="max-image-preview:large" />
	<meta charset="<?php echo CHARSET; ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

	<title><?php echo $site->title(); ?></title>

	<?php
	// Change `<html>` 'no-js' class to 'js' if JavaScript is enabled.
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\r";
	echo Theme :: jquery();
	?>

	<?php echo Theme :: css(
		[ 'assets/css/style.css' ],
		DOMAIN_THEME
	); ?>

</head>
