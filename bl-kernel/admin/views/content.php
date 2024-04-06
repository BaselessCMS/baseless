<?php
/**
 * Content lists page
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

// Import namespaced functions.
use function CMS\Help\{
	site,
	security,
	url,
	lang,
	users,
	plugins,
	page,
	pages,
	cats
};

/**
 * Content type table
 *
 * @since  1.0.0
 * @param  string $type The content type.
 * @return void
 */
function table( $type = 'published' ) {

	// Access global variables.
	global $autosave, $drafts, $published, $scheduled, $static, $sticky;

	if ( $type == 'published' ) {
		$list = $published;
		if ( empty( $list ) ) {
			printf(
				'<p class="content-table-empty-message">%s</p>',
				lang()->g( 'There are no pages at this moment.' )
			);
			return false;
		}
	} elseif ( $type == 'draft' ) {
		$list = $drafts;
		if ( empty( $list ) ) {
			printf(
				'<p class="content-table-empty-message">%s</p>',
				lang()->g( 'There are no draft pages at this moment.' )
			);
			return false;
		}
	} elseif ( $type == 'scheduled' ) {
		$list = $scheduled;
		if ( empty( $list ) ) {
			printf(
				'<p class="content-table-empty-message">%s</p>',
				lang()->g( 'There are no scheduled pages at this moment.' )
			);
			return false;
		}
	} elseif ( $type == 'static' ) {
		$list = $static;
		if ( empty( $list ) ) {
			printf(
				'<p class="content-table-empty-message">%s</p>',
				lang()->g( 'There are no static pages at this moment.' )
			);
			return false;
		}
	} elseif ( $type == 'sticky' ) {
		$list = $sticky;
		if ( empty( $list ) ) {
			printf(
				'<p class="content-table-empty-message">%s</p>',
				lang()->g( 'There are no sticky pages at this moment.' )
			);
			return false;
		}
	} elseif ( $type == 'autosave' ) {
		$list = $autosave;
	}

	// Table markup.
	printf(
		'<table class="table admin-table"><thead><tr><th scope="col">%s</th>',
		lang()->g( 'Title' )
	);
	if ( $type == 'published' || $type == 'static' || $type == 'sticky' ) {
		printf(
			'<th scope="col">%s</th>',
			lang()->g( 'URL' )
		);
	}
	printf(
		'<th scope="col">%s</th></tr></thead><tbody>',
		lang()->g( 'Actions' )
	);

	if ( ( ORDER_BY == 'position' ) || $type == 'static' ) {
		foreach ( $list as $pageKey ) {
			try {
				$page = new \Page( $pageKey );
				if ( ! $page->isChild() ) {
					echo '<tr><td>
						<div>
							<a href="' . HTML_PATH_ADMIN_ROOT . 'edit-content/' . $page->key() . '">'
							. ( $page->title() ? $page->title() : '<span class="label-empty-title">' . lang()->g( 'Empty title' ) . '</span> ' ) . '</a>
						</div>
						<div>
							<p>' . ( ( ( ORDER_BY == 'position' ) || ( $type != 'published' ) ) ? lang()->g( 'Position' ) . ': ' . $page->position() : $page->date( MANAGE_CONTENT_DATE_FORMAT ) ) . '</p>
						</div>
					</td>';

					if ( $type == 'published' || $type == 'static' || $type == 'sticky' ) {

						if ( \Text :: isEmpty( url()->filters( 'page' ) ) ) {
							$friendly_url = '/' . $page->key();
						} else {
							$friendly_url = '/' . url()->filters( 'page' ) . '/' . $page->key();
						}
						echo '<td><a target="_blank" href="' . $page->permalink() . '">' . $friendly_url . '</a></td>';
					}

					echo '<td class="contentTools">' . PHP_EOL;
					echo '<a target="_blank" href="' . $page->permalink() . '"><i class="fa fa-desktop"></i>' . lang()->g( 'View' ) . '</a>' . PHP_EOL;
					echo '<a href="' . HTML_PATH_ADMIN_ROOT . 'edit-content/' . $page->key() . '"><i class="fa fa-edit"></i>' . lang()->g( 'Edit' ) . '</a>' . PHP_EOL;

					if ( count( $page->children() ) == 0 ) {
						echo '<a href="#" class="text-danger deletePageButton" data-toggle="modal" data-target="#jsdeletePageModal" data-key="' . $page->key() . '"><i class="fa fa-trash"></i>' . lang()->g( 'Delete' ) . '</a>' . PHP_EOL;
					}
					echo '</td></tr>';

					foreach ( $page->children() as $child ) {

						//if ( $child->published() ) {
						echo '<tr><td class="child">
							<div>
								<a href="' . HTML_PATH_ADMIN_ROOT . 'edit-content/' . $child->key() . '">'
								 . ( $child->title() ? $child->title() : '<span class="label-empty-title">' . lang()->g( 'Empty title' ).'</span> ' ) . '</a>
							</div>
							<div>
								<p>' . ( ( ( ORDER_BY == 'position' ) || ( $type != 'published' ) ) ? lang()->g( 'Position' ) . ': ' . $child->position() : $child->date( MANAGE_CONTENT_DATE_FORMAT ) ) . '</p>
							</div>
						</td>';

						if ( $type == 'published' || $type == 'static' || $type == 'sticky' ) {

							if ( \Text :: isEmpty( url()->filters( 'page' ) ) ) {
								$friendly_url = '/' . $child->key();
							} else {
								$friendly_url = '/' . url()->filters( 'page' ) . '/'.$child->key();
							}
							echo '<td><a target="_blank" href="' . $child->permalink() . '">' . $friendly_url . '</a></td>';
						}
						echo '<td>'.PHP_EOL;

						if ( $type == 'published' || $type == 'static' || $type == 'sticky' ) {

							echo '<a target="_blank" href="' . $child->permalink() . '"><i class="fa fa-desktop"></i>' . lang()->g( 'View' ) . '</a>' . PHP_EOL;
						}
						echo '<a href="' . HTML_PATH_ADMIN_ROOT . 'edit-content/' . $child->key() . '"><i class="fa fa-edit"></i>' . lang()->g( 'Edit' ) . '</a>' . PHP_EOL;

						echo '<a class="text-danger deletePageButton" href="#" data-toggle="modal" data-target="#jsdeletePageModal" data-key="'.$child->key() . '"><i class="fa fa-trash"></i>' . lang()->g( 'Delete' ) . '</a>' . PHP_EOL;

						echo '</td></tr>';
						//}
					}
				}
			} catch ( Exception $e ) {
				// Continue.
			}
		}
	} else {
		foreach ( $list as $pageKey ) {
			try {
				$page = new \Page( $pageKey );
				echo '<tr><td>
					<div>
						<a href="' . HTML_PATH_ADMIN_ROOT . 'edit-content/' . $page->key() . '">' . ( $page->title() ? $page->title() : '<span class="label-empty-title">' . lang()->g( 'Empty title' ) . '</span> ' ) . '</a>
					</div>
					<div>
						<p>' . ( ( $type == 'scheduled' ) ? lang()->g( 'Scheduled' ) . ': ' . $page->date( SCHEDULED_DATE_FORMAT ) : $page->date( MANAGE_CONTENT_DATE_FORMAT ) ) . '</p>
					</div>
				</td>';

				if ( $type == 'published' || $type == 'static' || $type == 'sticky' ) {

					if ( \Text :: isEmpty( url()->filters( 'page' ) ) ) {
						$friendly_url = '/' . $page->key();
					} else {
						$friendly_url = '/' . url()->filters( 'page' ) . '/' . $page->key();
					}
					echo '<td><a target="_blank" href="' . $page->permalink() . '">' . $friendly_url . '</a></td>';
				}

				echo '<td class="contentTools">' . PHP_EOL;
				if ( $type == 'published' || $type == 'static' || $type == 'sticky' ) {
					echo '<a target="_blank" href="' . $page->permalink() . '"><i class="fa fa-desktop"></i>' . lang()->g( 'View' ) . '</a>' . PHP_EOL;
				}
				echo '<a href="' . HTML_PATH_ADMIN_ROOT . 'edit-content/' . $page->key() . '"><i class="fa fa-edit"></i>' . lang()->g( 'Edit' ).'</a>' . PHP_EOL;
				if ( count( $page->children() ) == 0 ) {
					echo '<a href="#" class="text-danger deletePageButton" data-toggle="modal" data-target="#jsdeletePageModal" data-key="' . $page->key() . '"><i class="fa fa-trash"></i>' . lang()->g( 'Delete' ) . '</a>' . PHP_EOL;
				}
				echo '</td></tr>';
			} catch ( Exception $e ) {
				// Continue.
			}
		}
	}
	echo '</tbody></table>';
}

?>
<header class="admin-page-header has-actions">

	<h1><?php lang()->p( 'Content' ); ?></h1>

	<div class="form-actions admin-form-actions">
		<a class="btn btn-primary" href="<?php echo HTML_PATH_ADMIN_ROOT . 'new-content' ?>" role="button"><?php lang()->p( 'Add Content' ); ?></a>
	</div>
</header>

<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="pages-tab" data-toggle="tab" href="#pages" role="tab"><?php lang()->p( 'Pages' ); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="static-tab" data-toggle="tab" href="#static" role="tab"><?php lang()->p( 'Static' ); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="sticky-tab" data-toggle="tab" href="#sticky" role="tab"><?php lang()->p( 'Sticky' ); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="scheduled-tab" data-toggle="tab" href="#scheduled" role="tab"><?php lang()->p( 'Scheduled' );?> <?php if ( count( $scheduled ) > 0 ) { echo '<span class="badge badge-danger">' . count( $scheduled ) . '</span>'; } ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="draft-tab" data-toggle="tab" href="#draft" role="tab"><?php lang()->p( 'Draft' ); ?></a>
	</li>
	<?php if ( ! empty( $autosave ) ) : ?>
	<li class="nav-item">
		<a class="nav-link" id="autosave-tab" data-toggle="tab" href="#autosave" role="tab"><?php lang()->p( 'Autosave' ); ?></a>
	</li>
	<?php endif; ?>
</ul>
<div class="tab-content">
	<div class="tab-pane show active" id="pages" role="tabpanel">

		<?php table( 'published' ); ?>

		<?php if ( \Paginator :: numberOfPages() > 1 ) : ?>
		<nav class="paginator">
			<ul class="pagination flex-wrap justify-content-center">

				<li class="page-item <?php if ( ! \Paginator :: showPrev() ) echo 'disabled'; ?>">
					<a class="page-link" href="<?php echo \Paginator :: firstPageUrl() ?>"><span class="align-middle fa fa-media-skip-backward"></span> <?php echo lang()->get( 'First' ); ?></a>
				</li>

				<li class="page-item <?php if ( ! \Paginator :: showPrev() ) echo 'disabled'; ?>">
					<a class="page-link" href="<?php echo \Paginator :: previousPageUrl() ?>"><?php echo lang()->get( 'Previous' ); ?></a>
				</li>

				<li class="page-item <?php if ( ! \Paginator :: showNext() ) echo 'disabled'; ?>">
					<a class="page-link" href="<?php echo \Paginator :: nextPageUrl() ?>"><?php echo lang()->get( 'Next' ); ?></a>
				</li>

				<li class="page-item <?php if ( ! \Paginator :: showNext() ) echo 'disabled'; ?>">
					<a class="page-link" href="<?php echo \Paginator :: lastPageUrl() ?>"><?php echo lang()->get( 'Last' ); ?> <span class="align-middle fa fa-media-skip-forward"></span></a>
				</li>
			</ul>
		</nav>
		<?php endif; ?>
	</div>

	<div class="tab-pane" id="static" role="tabpanel">
		<?php table( 'static' ); ?>
	</div>

	<div class="tab-pane" id="sticky" role="tabpanel">
		<?php table( 'sticky' ); ?>
	</div>

	<div class="tab-pane" id="scheduled" role="tabpanel">
		<?php table( 'scheduled' ); ?>
	</div>

	<div class="tab-pane" id="draft" role="tabpanel">
		<?php table( 'draft' ); ?>
	</div>

	<?php if ( ! empty( $autosave ) ) : ?>
	<div class="tab-pane" id="autosave" role="tabpanel">
		<?php table( 'autosave' ); ?>
	</div>
	<?php endif; ?>
</div>

<?php
	echo \Bootstrap :: modal( [
		'buttonPrimary'        => lang()->g( 'Delete' ),
		'buttonPrimaryClass'   => 'btn-danger deletePageModalAcceptButton',
		'buttonSecondary'      => lang()->g( 'Cancel' ),
		'buttonSecondaryClass' => 'btn-link',
		'modalTitle'           => lang()->g( 'Delete content' ),
		'modalText'            => lang()->g( 'Are you sure you want to delete this page' ),
		'modalId'              => 'jsdeletePageModal'
	] );
?>
<script>
$(document ).ready( function() {

	var key = false;

	// Button for delete a page in the table.
	$( '.deletePageButton' ).on( 'click', function() {
		key = $(this).data( 'key' );
	});

	// Event from button accept from the modal.
	$( '.deletePageModalAcceptButton' ).on( 'click', function() {

		var form = jQuery( '<form>', {
			'action' : HTML_PATH_ADMIN_ROOT + 'edit-content/' + key,
			'method' : 'post',
			'target' : '_top'
		}).append( jQuery( '<input>', {
			'type'  : 'hidden',
			'name'  : 'tokenCSRF',
			'value' : tokenCSRF
		}).append( jQuery( '<input>', {
			'type'  : 'hidden',
			'name'  : 'key',
			'value' : key
		}).append( jQuery( '<input>', {
			'type'  : 'hidden',
			'name'  : 'type',
			'value' : 'delete'
		}) )) );

		form.hide().appendTo( 'body' ).submit();
	});
});
</script>
<script>
	// Open the tab defined in the URL.
	const anchor = window.location.hash;
	$(`a[href="${anchor}"]`).tab( 'show' );
</script>
