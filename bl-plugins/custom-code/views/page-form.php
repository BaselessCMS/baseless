<?php
/**
 * Custom code form page
 *
 * @package    Custom Code
 * @subpackage Views
 * @since      1.0.0
 */

?>
<style>
.code-section { margin-top: 2rem; }
textarea { display: block; }
</style>
<p><?php echo $this->description(); ?></p>

<fieldset>
	<legend><?php $L->p( 'Frontend Hooks' ); ?></legend>

	<div class="code-section">
		<label><?php $L->p( 'Frontend Head' ); ?></label>
		<textarea name="head" id="jshead" rows="6" cols="60"><?php echo $this->getValue( 'head' ); ?></textarea>
		<small class="tip"><?php echo $L->get( 'insert-code-in-the-theme-inside-the-tag-head' ); ?></small>
	</div>

	<div class="code-section">
		<label><?php $L->p( 'Frontend Header' ); ?></label>
		<textarea name="header" id="jsheader" rows="6" cols="60"><?php echo $this->getValue( 'header' ); ?></textarea>
		<small class="tip"><?php echo $L->get( 'insert-code-in-the-theme-at-the-top' ); ?></small>
	</div>

	<div class="code-section">
		<label><?php $L->p( 'Frontend Footer' ); ?></label>
		<textarea name="footer" id="jsfooter" rows="6" cols="60"><?php echo $this->getValue( 'footer' ); ?></textarea>
		<small class="tip"><?php echo $L->get( 'insert-code-in-the-theme-at-the-bottom' ); ?></small>
	</div>
</fieldset>

<fieldset>
	<legend><?php $L->p( 'Admin Hooks' ); ?></legend>

	<div class="code-section">
		<label><?php $L->p( 'Admin Head' ); ?></label>
		<textarea name="adminHead" rows="6" cols="60"><?php echo $this->getValue( 'adminHead' ); ?></textarea>
		<small class="tip"><?php echo $L->get( 'insert-code-in-the-theme-inside-the-tag-head' ); ?></small>
	</div>

	<div class="code-section">
		<label><?php $L->p( 'Admin Header' ); ?></label>
		<textarea name="adminHeader" rows="6" cols="60"><?php echo $this->getValue( 'adminHeader' ); ?></textarea>
		<small class="tip"><?php echo $L->get( 'insert-code-in-the-theme-at-the-top' ); ?></small>
	</div>

	<div class="code-section">
		<label><?php $L->p( 'Admin Footer' ); ?></label>
		<textarea name="adminFooter" rows="6" cols="60"><?php echo $this->getValue( 'adminFooter' ); ?></textarea>
		<small class="tip"><?php echo $L->get( 'insert-code-in-the-theme-at-the-bottom' ); ?></small>
	</div>
</fieldset>
