<?php
/**
 * Default header
 *
 * @package    Baseless
 * @subpackage Frontend
 * @category   Themes
 * @since      1.0.0
 */

?>
<header class="site-header">
	<h1 class="site-title"><a href="<?php echo $site->url(); ?>"><?php echo $site->title(); ?></a></h1>
	<p class="site-description"><?php echo $site->slogan(); ?></p>
</header>
