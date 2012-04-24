<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
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
function we_tag_hidden($attribs){

	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute("name", $attribs);
	$type = weTag_getAttribute("type", $attribs);
	$xml = weTag_getAttribute("xml", $attribs);

	$value = '';
	switch($type){
		case 'session' :
			$value = $_SESSION[$name];
			break;
		case 'request' :
			$value = we_util::rmPhp(isset($_REQUEST[$name]) ? $_REQUEST[$name] : "");
			break;
		default :
			$value = $GLOBALS[$name];
			break;
	}

	return getHtmlTag('input', array('type' => 'hidden', 'name' => $name, 'value' => $value, 'xml' => $xml));
}
