<?php
/**
 * webEdition CMS
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function we_tag_hidden($attribs, $content){

	$foo = attributFehltError($attribs, "name", "hidden");
	if ($foo)
		return $foo;

	$name = we_getTagAttribute("name", $attribs);
	$type = we_getTagAttribute("type", $attribs, '');
	$xml = we_getTagAttribute("xml", $attribs);

	$value = '';
	switch ($type) {
		case 'session' :

			$value = $_SESSION[$name];
			break;
		case 'request' :
			$value = removePHP(isset($_REQUEST[$name]) ? $_REQUEST[$name] : "");
			break;
		default :
			$value = $GLOBALS[$name];
			break;
	}

	return getHtmlTag('input', array(
		'type' => 'hidden', 'name' => $name, 'value' => $value, 'xml' => $xml
	));
}
