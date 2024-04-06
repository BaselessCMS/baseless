<?php
/**
 * Template tags
 *
 * @package    JSON CMS
 * @subpackage Core
 * @category   Functions
 * @since      1.0.0
 */

namespace CMS\Tags;

function jquery_tag() {
	return sprintf(
		'<script src="%sjquery.min.js?version=%s"></script>',
		DOMAIN_CORE_JS,
		CMS_VERSION
	);
}

function jquery() {
	echo jquery_tag() . PHP_EOL;
}
