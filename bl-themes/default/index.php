<?php
/**
 * Default Baseless theme
 *
 * Begin markup for frontend display.
 *
 * @package    Baseless
 * @subpackage Frontend
 * @category   Themes
 * @since      1.0.0
 */

// Site direction.
$dir = 'ltr';
if ( CMS\Func\is_rtl() ) {
	$dir = 'rtl';
}

// Get UUID
$uuid = '';
if ( 'page' == $url->whereAmI() ) {
	$uuid = $page->uuid();
}

?>
<!DOCTYPE html>
<html dir="<?php echo $dir; ?>" class="no-js" lang="<?php // echo current_lang(); ?>" xmlns:og="http://opengraphprotocol.org/schema/" data-web-page>

<?php include( THEME_DIR . 'views/utility/head.php' ); ?>

<body class="<?php // echo body_classes(); ?>" itemid="<?php echo $uuid; ?>">

	<?php include( THEME_DIR . 'views/header/header.php' ); ?>

	<div class="page-wrap wrapper-general">
		<?php if ( 'page' == $WHERE_AM_I ) {
			include( THEME_DIR . 'views/content/page.php' );
		} else {
			include( THEME_DIR . 'views/content/home.php' );
		}
		?>
		<aside><?php echo Theme :: plugins( 'siteSidebar' ); ?></aside>
	</div>

</body>
</html>
