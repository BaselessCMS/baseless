<?php
/**
 * Posts markup
 *
 * @package    JSON CMS
 * @subpackage Frontend
 * @category   Themes
 * @since      1.0.0
 */

if ( empty( $content ) ) : ?>
	<h2><?php $language->p( 'No Posts Found' ); ?></h2>
<?php
return; endif;

foreach ( $content as $page ) : ?>

	<article>
		<h2 class="page-title"><a href="<?php echo $page->permalink(); ?>"><?php echo $page->title(); ?></a></h2>

		<?php if ( $page->description() ) : ?>
			<p class="page-description"><?php echo $page->description(); ?></p>
		<?php endif ?>

		<div>
			<?php echo $page->content(); ?>
		</div>
	</article>
<?php endforeach ?>

<?php if ( Paginator :: numberOfPages() > 1 ) : ?>
	<nav>
		<ul>
			<?php if ( Paginator :: showPrev() ) : ?>
				<li>
					<a href="<?php echo Paginator :: previousPageUrl(); ?>" tabindex="-1"><?php echo $L->get( 'Previous' ); ?></a>
				</li>
			<?php endif; ?>

			<?php if ( Paginator :: showNext() ) : ?>
				<li>
					<a href="<?php echo Paginator::nextPageUrl() ?>"><?php echo $L->get( 'Next' ); ?></a>
				</li>
			<?php endif; ?>
		</ul>
	</nav>
<?php endif ?>
