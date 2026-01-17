<?php
/**
 * Admin theme bootstrap
 *
 * Taken from the default Bludit admin theme
 * and improved.
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Admin
 * @since      1.0.0
 *
 * @todo Deprecate this class and rid the CMS admin
 *       of admin theme dependencies.
 */

class Bootstrap {

	/**
	 * Modal window
	 *
	 * Markup for modal content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  array $args
	 * @return string Returns modal window markup.
	 */
	public static function modal( $args ) {

		$buttonPrimary = $args['buttonPrimary'];
		$buttonPrimaryClass = $args['buttonPrimaryClass'];

		$buttonSecondary = $args['buttonSecondary'];
		$buttonSecondaryClass = $args['buttonSecondaryClass'];

		$modalText  = $args['modalText'];
		$modalTitle = $args['modalTitle'];
		$modalId    = $args['modalId'];

return <<<EOF
<div id="$modalId" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<h3>$modalTitle</h3>
				<p>$modalText</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn $buttonSecondaryClass" data-dismiss="modal">$buttonSecondary</button>
				<button type="button" class="btn $buttonPrimaryClass">$buttonPrimary</button>
			</div>
		</div>
	</div>
</div>
EOF;
	}

	/**
	 * Print link
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  mixed $args
	 * @param  array $defaults
	 * @return string Returns the link markup.
	 */
	public static function link( $args = null, $defaults = [] ) {

		// Default arguments.
		$defaults = [
			'href'   => '',
			'target' => false,
			'icon'   => '',
			'text'   => '',
			'class'  => ''
		];

		// Maybe override defaults.
		if ( is_array( $args ) && $args ) {
			$args = array_merge( $defaults, $args );
		} else {
			$args = $defaults;
		}

		$text = $args['text'];
		// If deprecated key `title`.
		if ( isset( $args['title'] ) ) {
			$text = $args['title'];
		}

		$target = '';
		if ( $args['target'] && ( '_blank' == $args['target'] || 'blank' == $args['target'] ) ) {
			$target = ' target="_blank" rel="noopener noreferrer" ';
		}

		$class = '';
		if ( ! empty( $args['class'] ) ) {
			$class = ' class="' . $args['class'] . '" ';
		}

		$icon = '';
		if ( ! empty( $args['icon'] ) ) {
			$icon = '<span class="fa fa-' . $args['icon'] . '"></span> ';
		}

		$html = '';
		if ( ! empty( $args['href'] ) && ! empty( $text ) ) {
			$html = sprintf(
				'<a href="%s"%s%s>%s%s</a>',
				$args['href'],
				$target,
				$class,
				$icon,
				$text
			);
		}
		return $html;
	}

	/**
	 * Page title
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  mixed $args
	 * @param  array $defaults
	 * @global object $L The Language class.
	 * @return string
	 */
	public static function pageTitle( $args = null, $defaults = [] ) {

		// Access global variables.
		global $L;

		// Default arguments.
		$defaults = [
			'text'  => $L->get( 'Page Title' ),
			'icon'  => '',
			'class' => 'admin-page-heading'
		];

		// Maybe override defaults.
		if ( is_array( $args ) && $args ) {
			$args = array_merge( $defaults, $args );
		} else {
			$args = $defaults;
		}

		$text = $args['text'];
		// If deprecated key `title`.
		if ( isset( $args['title'] ) ) {
			$text = $args['title'];
		}

		if ( ! empty( $args['icon'] ) ) {
			$html = sprintf(
				'<h1 class="%s"><span class="fa fa-%s"></span> %s</h1>',
				$args['class'],
				$args['icon'],
				$text
			);
		} else {
			$html = sprintf(
				'<h1 class="%s">%s</h1>',
				$args['class'],
				$text
			);
		}
		return $html;
	}

	/**
	 * Form open
	 *
	 * Prints the opening tag of a form element.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  mixed $args
	 * @param  array $defaults
	 * @return string
	 */
	public static function formOpen( $args = null, $defaults = [] ) {

		// Default arguments.
		$defaults = [
			'id'      => '',
			'class'   => '',
			'enctype' => '',
			'action'  => '',
			'method'  => 'post',
			'autoc'   => 'off',
			'style'   => ''
		];

		// Maybe override defaults.
		if ( is_array( $args ) && $args ) {
			$args = array_merge( $defaults, $args );
		} else {
			$args = $defaults;
		}

		$id = '';
		if ( ! empty( $args['id'] ) ) {
			$id = 'id="' . $args['id'] . '"';
		}

		$class = '';
		if ( ! empty( $args['class'] ) ) {
			$class = 'class="' . $args['class'] . '"';
		}

		$enctype = '';
		if ( ! empty( $args['enctype'] ) ) {
			$enctype = 'enctype="' . $args['enctype'] . '"';
		}

		$action = '';
		if ( ! empty( $args['action'] ) ) {
			$action = 'action="' . $args['action'] . '"';
		}

		$method = '';
		if ( ! empty( $args['method'] ) ) {
			$method = 'method="' . $args['method'] . '"';
		}

		$autocomplete = 'autocomplete="' . $args['autoc'] . '"';

		$style = '';
		if ( ! empty( $args['style'] ) ) {
			$style = 'style="' . $args['style'] . '"';
		}

		return "<form {$id} {$class} {$enctype} {$method} {$action} {$autocomplete} {$style} >";
	}

	/**
	 * Form close
	 *
	 * Prints the closing tag of a form element
	 * plus some JavaScript.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @return string
	 */
	public static function formClose() {

		return <<<EOF
		</form>
		<script>
		$(document).ready( function() {
			// Prevent the form submit when press enter key.
			$( 'form' ).keypress( function(e) {
				if ( ( e.which == 13 ) && ( e.target.type !== 'textarea' ) ) {
					return false;
				}
			});
		});
		</script>
		EOF;
	}

	/**
	 * Form title
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  mixed $args
	 * @param  array $defaults
	 * @return string Returns the heading markup.
	 */
	public static function formTitle( $args = null, $defaults = [] ) {

		// Default arguments.
		$defaults = [
			'elem'   => 'h2',
			'text'   => '',
			'class'  => 'form-section-heading admin-form-section-heading'
		];

		// Maybe override defaults.
		if ( is_array( $args ) && $args ) {
			$args = array_merge( $defaults, $args );
		} else {
			$args = $defaults;
		}

		$text = $args['text'];
		// If deprecated key `title`.
		if ( isset( $args['title'] ) ) {
			$text = $args['title'];
		}

		return sprintf(
			'<%s class="%s">%s</%s>',
			$args['elem'],
			$args['class'],
			$text,
			$args['elem']
		);
	}

	/**
	 * Form block, text input
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  mixed $args
	 * @param  array $defaults
	 * @return mixed
	 */
	public static function formInputTextBlock( $args = null, $defaults = [] ) {

		// Default arguments.
		$defaults = [
			'name'        => '',
			'id'          => '',
			'value'       => '',
			'placeholder' => '',
			'disabled'    => false,
			'class'       => 'form-group',
			'label'       => '',
			'labelClass'  => 'form-input-label',
			'tip'         => ''
		];

		// Maybe override defaults.
		if ( is_array( $args ) && $args ) {
			$args = array_merge( $defaults, $args );
		} else {
			$args = $defaults;
		}

		$disabled    = empty($args['disabled'])?'':'disabled';
		$placeholder = isset($args['placeholder'])?$args['placeholder']:'';

		$id = 'js' . $args['name'];
		if ( ! empty( $args['id'] ) ) {
			$id = $args['id'];
		}

		$tip = '';
		if ( ! empty( $args['tip'] ) ) {
			$tip = '<small class="form-tip">' . $args['tip'] . '</small>';
		}

		$html = sprintf(
			'<div class="%s">',
			$args['class']
		);

		if ( ! empty( $args['label'] ) ) {
			$html .= sprintf(
				'<label for="%s">%s</label>',
				$args['name'],
				$args['label']
			);
		}

		$html .= sprintf(
			'<input type="text" id="%s" name="%s" value="%s" class="form-control"%s%s />',
			$id,
			$args['name'],
			$args['value'],
			( ! empty( $args['placeholder'] ) ? ' placeholder="' . $args['placeholder'] . '"' : '' ),
			( true == $args['disabled'] ? ' disabled="disabled"' : '' )
		);
		$html .= '</div>';

		if ( ! empty( $args['name'] ) ) {
			return $html;
		}
		return null;
	}

	/**
	 * File input field.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  array $args
	 * @return void
	 */
	public static function formInputFile( $args ) {

		$id = 'js' . $args['name'];
		if ( isset( $args['id'] ) ) {
			$id = $args['id'];
		}

		$class = 'custom-file';
		if ( isset( $args['class'] ) ) {
			$class = $class . ' ' . $args['class'];
		}

		$html  = '<div class="' . $class . '">';
		$html .= '<input type="file" class="custom-file-input" id="' . $id . '">';
		$html .= '<label class="custom-file-label" for="' . $id . '">' . $args['label'] . '</label>';
		$html .= '</div>';

		return $html;
	}

	public static function formTextarea( $args ) {

		$id = 'js' . $args['name'];
		if ( isset( $args['id'] ) ) {
			$id = $args['id'];
		}

		$class = 'form-control';
		if ( isset( $args['class'] ) ) {
			$class = $class . ' ' . $args['class'];
		}

		$html = '<div class="form-group row">';

		if ( ! empty( $args['label'] ) ) {
			$html .= '<label for="' . $id . '" class="col-sm-2 col-form-label">' . $args['label'] . '</label>';
		}

		$html .= '<div class="col-sm-10">';
		$html .= '<textarea class="' . $class . '" id="' . $id . '" name="' . $args['name'] . '" rows="' . $args['rows'] . '" placeholder="' . $args['placeholder'] . '">' . $args['value'] . '</textarea>';
		if ( isset( $args['tip'] ) ) {
			$html .= '<small class="form-text text-muted">' . $args['tip'] . '</small>';
		}
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public static function formTextareaBlock( $args ) {

		$id = 'js' . $args['name'];
		if ( isset( $args['id'] ) ) {
			$id = $args['id'];
		}

		$class = 'form-control';
		if ( ! empty( $args['class'] ) ) {
			$class = $class . ' ' . $args['class'];
		}

		$html = '<div class="form-group m-0">';
		if ( ! empty( $args['label'] ) ) {
			$html .= '<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="' . $id . '">' . $args['label'] . '</label>';
		}

		$html .= '<textarea class="' . $class . '" id="' . $id . '" name="' . $args['name'] . '" rows="' . $args['rows'] . '" placeholder="' . $args['placeholder'] . '">' . $args['value'] . '</textarea>';
		if ( ! empty( $args['tip'] ) ) {
			$html .= '<small class="form-text text-muted">' . $args['tip'] . '</small>';
		}
		$html .= '</div>';

		return $html;
	}

	public static function formInputText( $args ) {
		$name = $args['name'];
		$disabled = empty($args['disabled'])?'':'disabled';
		$readonly = empty($args['readonly'])?'':'readonly';
		$placeholder = isset($args['placeholder'])?$args['placeholder']:'';
		$value = isset($args['value'])?$args['value']:'';

		$id = 'js'.$name;
		if (isset($args['id'])) {
			$id = $args['id'];
		}

		$tip = '';
		if (isset($args['tip'])) {
			$tip = '<small class="form-text text-muted">'.$args['tip'].'</small>';
		}

		$label = '';
		if (isset($args['label'])) {
			$label = '<label for="'.$id.'" class="col-sm-2 col-form-label">'.$args['label'].'</label>';
		}

		$class = 'form-control';
		if (isset($args['class'])) {
			$class = $class.' '.$args['class'];
		}

		$type = 'text';
		if (isset($args['type'])) {
			$type = $args['type'];
		}

return <<<EOF
<div class="form-group row">
	$label
	<div class="col-sm-10">
		<input class="$class" id="$id" name="$name" value="$value" placeholder="$placeholder" type="$type" $disabled $readonly>
		$tip
	</div>
</div>
EOF;
	}

	public static function formCheckbox($args)
	{
		$labelForCheckbox = isset($args['labelForCheckbox'])?$args['labelForCheckbox']:'';
		$placeholder = isset($args['placeholder'])?$args['placeholder']:'';
		$tip = isset($args['tip'])?'<small class="form-text text-muted">'.$args['tip'].'</small>':'';
		$value = isset($args['value'])?$args['value']:'';
		$name = $args['name'];
		$id = 'js'.$name;
		if (isset($args['id'])) {
			$id = $args['id'];
		}
		$disabled = isset($args['disabled'])?'disabled':'';

		$class = 'form-group m-0';
		if (isset($args['class'])) {
			$class = $args['class'];
		}

		$labelClass = 'mt-4 mb-2 pb-2 border-bottom text-uppercase w-100';
		if (isset($args['labelClass'])) {
			$labelClass = $args['labelClass'];
		}

		$type = 'text';
		if (isset($args['type'])) {
			$type = $args['type'];
		}

		$label = '';
		if (!empty($args['label'])) {
			$label = '<label class="'.$labelClass.'">'.$args['label'].'</label>';
		}

		$checked = $args['checked']?'checked':'';
		$value = $checked?'1':'0';

return <<<EOF
<div class="$class">
	$label
	<div class="form-check">
		<input type="hidden" name="$name" value="$value"><input id="$id" type="checkbox" class="form-check-input" onclick="this.previousSibling.value=1-this.previousSibling.value" $checked>
		<label class="form-check-label" for="$id">$labelForCheckbox</label>
		$tip
	</div>
</div>
EOF;
	}

	public static function formSelect($args)
	{
		$id = 'js'.$args['name'];
		if (isset($args['id'])) {
			$id = $args['id'];
		}

		$class = 'custom-select';
		if (isset($args['class'])) {
			$class = $class.' '.$args['class'];
		}

		$html = '<div class="form-group row">';

		if (isset($args['label'])) {
			$html .= '<label for="'.$id.'" class="col-sm-2 col-form-label">'.$args['label'].'</label>';
		}

		$html .= '<div class="col-sm-10">';
		$html .= '<select id="'.$id.'" name="'.$args['name'].'" class="'.$class.'">';
		foreach ($args['options'] as $key=>$value) {
			$html .= '<option '.(($key==$args['selected'])?'selected':'').' value="'.$key.'">'.$value.'</option>';
		}
		$html .= '</select>';
		if (isset($args['tip'])) {
			$html .= '<small class="form-text text-muted">'.$args['tip'].'</small>';
		}
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public static function formSelectBlock($args)
	{
		$id = 'js'.$args['name'];
		if (isset($args['id'])) {
			$id = $args['id'];
		}

		$class = 'custom-select';
		if (!empty($args['class'])) {
			$class = $class.' '.$args['class'];
		}

		$html = '<div class="form-group m-0">';

		if (!empty($args['label'])) {
			$html .= '<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="'.$id.'">'.$args['label'].'</label>';
		}

		$html .= '<select id="'.$id.'" name="'.$args['name'].'" class="'.$class.'">';
		if (!empty($args['emptyOption'])) {
			$html .= '<option value="">'.$args['emptyOption'].'</option>';
		}
		foreach ($args['options'] as $key=>$value) {
			$html .= '<option '.(($key==$args['selected'])?'selected':'').' value="'.$key.'">'.$value.'</option>';
		}
		$html .= '</select>';
		if (!empty($args['tip'])) {
			$html .= '<small class="form-text text-muted">'.$args['tip'].'</small>';
		}
		$html .= '</div>';

		return $html;
	}

	public static function formInputHidden($args)
	{
		return '<input type="hidden" id="js'.$args['name'].'" name="'.$args['name'].'" value="'.$args['value'].'">';
	}

	public static function alert($args)
	{
		$class = 'alert';
		if (!empty($args['class'])) {
			$class = $class.' '.$args['class'];
		}

		$text = $args['text'];

return <<<EOF
<div class="$class" role="alert">$text</div>
EOF;
	}
}
