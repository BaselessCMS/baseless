<?php
/**
 * Page markup
 *
 * @package    Baseless
 * @subpackage Frontend
 * @category   Themes
 * @since      1.0.0
 */

?>
<article class="page">

	<h1 class="page-title"><?php echo $page->title(); ?></h1>

	<?php if ( $page->description() ) : ?>
		<p class="page-description"><?php echo $page->description(); ?></p>
	<?php endif ?>

	<?php if ( $page->coverImage() ) : ?>
		<figure class="page-cover">
			<img src="<?php echo $page->coverImage(); ?>" alt="" />
		</figure>
	<?php endif ?>

	<div class="page-content">
		<?php echo $page->content(); ?>
	</div>
</article>
