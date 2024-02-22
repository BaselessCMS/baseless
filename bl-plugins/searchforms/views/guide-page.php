<?php
/**
 * Search Forms options
 *
 * @package    Search Forms
 * @subpackage Views
 * @category   Docs
 * @since      1.0.0
 */

 // Settings page URL.
$settings_page = DOMAIN_ADMIN . 'configure-plugin/Search_Forms';

?>
<style>
	pre, code {
		user-select: all;
		cursor: pointer;
	}
	pre {
		max-width: 720px;
		margin: 1rem 0;
		white-space: pre-wrap;
	}
	<?php if ( ! $site->adminTheme() || ( 'booty' == $site->adminTheme() && 'configureight' != $site->theme() ) ) : ?>
	pre {
		padding: 1em 2em;
		background: #eaeaea;
		background: rgba( 0,0,0,0.07 );
		border: solid 1px #cccccc;
		color: #444444;
	}
	<?php endif; ?>
</style>

<?php echo Bootstrap :: pageTitle( [ 'title' => $L->g( 'Search Forms Guide' ), 'icon' => 'book' ] ); ?>

<div class="alert alert-primary alert-search-forms" role="alert">
	<p class="m-0"><?php $L->p( "Go to the <a href='{$settings_page}'>search form settings</a> page." ); ?></p>
</div>

<div id="search-forms-guide">

	<?php echo Bootstrap :: formTitle( [ 'element' => 'h3', 'title' => $L->g( 'Sidebar Search Form' ) ] ); ?>

	<p><?php $L->p( "The sidebar search form requires no coding and is enabled by default. It can be disabled on the settings page. The HTML markup and the CSS classes for the form are nearly identical to the original Bludit search plugin for those who have already written custom CSS for the sidebar form." ); ?></p>

	<p><?php $L->p( 'When enabled, the sidebar search form has several options for customizing to your needs.' ); ?></p>

	<?php echo Bootstrap :: formTitle( [ 'element' => 'h3', 'title' => $L->g( 'Default Settings' ) ] ); ?>

	<p><?php $L->p( 'The array below is the complete array of arguments used to construct a search form. Any of these can be overridden with an array of arguments passed to a function call. These are also used by the sidebar search form but array values are overridden by the plugin with settings values.' ); ?></p>

<pre lang="PHP">
&lt;?php
$defaults = [
	'wrap'        => true,
	'wrap_class'  => 'form-wrap search-form-wrap',
	'form_class'  => 'form search-form',
	'label'       => $L->get( 'Search' ),
	'label_wrap'  => 'h2',
	'placeholder' => $L->get( "Enter at least 3 characters." ),
	'button'      => true,
	'button_text' => $L->get( 'Submit' )
];
?&gt;
</pre>

	<?php echo Bootstrap :: formTitle( [ 'element' => 'h3', 'title' => $L->g( 'Template Tags' ) ] ); ?>

	<p><?php $L->p( 'The search form function generates a unique ID for each instance. Thus more than one form may be displayed on a page without conflict in the accompanying JavaScript. The function accepts an array of arguments to override the function defaults. It is also namespaced so the function must be preceded by the namespace or aliased.' ); ?></p>

	<p><?php $L->p( 'Following is an example of displaying a default form in a theme template.<br />Note the SearchForms namespace and backslash before the function call.' ); ?></p>

	<pre lang="PHP">&lt;?php SearchForms\form(); ?&gt;</pre>

	<p><?php $L->p( 'The following example demonstrates a simple override of the form label.' ); ?></p>

	<pre lang="PHP">&lt;?php SearchForms\form( [ 'label' => $L->get( 'Search Content' ) ] ); ?&gt;</pre>

	<p><?php $L->p( 'The following example modifies the heading element, the text placeholder, and the button text.' ); ?></p>

<pre lang="PHP">
&lt;?php
$searchform = [
	'label_wrap'  => 'h3',
	'placeholder' => $L->get( "Find this…" ),
	'button_text' => $L->get( 'Go' )
];
echo SearchForms\form( $searchform );
?&gt;
</pre>

	<p><?php $L->p( 'Please raise issues and make suggestions on the plugin\'s GitHub page: <a href="https://github.com/Bludiot/searchforms">https://github.com/Bludiot/searchforms</a>' ); ?></p>
</div>
