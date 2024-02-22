# Search Forms

Improved search form plugin for Bludit CMS.

![Tested on Bludit version 3.15.0](https://img.shields.io/badge/Bludit-3.15.0-42a5f5.svg?style=flat-square "Tested on Bludit version 3.15.0")
![Minimum PHP version is 7.4](https://img.shields.io/badge/PHP_Min-7.4-8892bf.svg?style=flat-square "Minimum PHP version is 7.4")
![Tested on PHP version 8.2.4](https://img.shields.io/badge/PHP_Test-8.2.4-8892bf.svg?style=flat-square "Tested on PHP version 8.2.4")

## Sidebar Search Form

The sidebar search form requires no coding and is enabled by default. It can be disabled on the settings page. The HTML markup and the CSS classes for the form are nearly identical to the original Bludit search plugin for those who have already written custom CSS for the sidebar form.

When enabled, the sidebar search form has several options for customizing to your needs.
Default Settings

The array below is the complete array of arguments used to construct a search form. Any of these can be overridden with an array of arguments passed to a function call. These are also used by the sidebar search form but array values are overridden by the plugin with settings values.

```php
<?php
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
?>
```

## Template Tags

The search form function generates a unique ID for each instance. Thus more than one form may be displayed on a page without conflict in the accompanying JavaScript. The function accepts an array of arguments to override the function defaults. It is also namespaced so the function must be preceded by the namespace or aliased.

Following is an example of displaying a default form in a theme template.
Note the SearchForms namespace and backslash before the function call.

```php
<?php SearchForms\form(); ?>
```

The following example demonstrates a simple override of the form label.

```php
<?php SearchForms\form( [ 'label' => $L->get( 'Search Content' ) ] ); ?>
```

The following example modifies the heading element, the text placeholder, and the button text.

```php
<?php
$searchform = [
	'label_wrap'  => 'h3',
	'placeholder' => $L->get( "Find this…" ),
	'button_text' => $L->get( 'Go' )
];
echo SearchForms\form( $searchform );
?>
```
