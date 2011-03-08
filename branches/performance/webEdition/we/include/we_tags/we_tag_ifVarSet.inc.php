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

function we_isVarSet($name, $type, $docAttr, $property = false, $formname = '', $shopname = ''){
	switch ($type) {
		case 'request' :
			return isset($_REQUEST[$name]);
			break;
		case 'post' :
			return isset($_POST[$name]);
			break;
		case 'get' :
			return isset($_GET[$name]);
			break;
		case 'global' :
			return isset($GLOBALS[$name]);
			break;
		case 'session' :
			return isset($_SESSION[$name]);
			break;
		case 'sessionfield' :
			return isset($_SESSION['webuser'][$name]);
			break;
		case 'shopField' :
			if (isset($GLOBALS[$shopname])) {
				return isset($GLOBALS[$shopname]->CartFields[$name]);
			}
			break;
		case 'sum' :
			return (isset($GLOBALS['summe']) && isset($GLOBALS['summe'][$name]));
			break;
		default :
			$doc = false;
			switch ($docAttr) {
				case 'object' :
				case 'document' :
					$doc = isset($GLOBALS['we_' . $docAttr][$formname]) ? $GLOBALS['we_' . $docAttr][$formname] : false;
					break;
				case 'top' :
					$doc = isset($GLOBALS['WE_MAIN_DOC']) ? $GLOBALS['WE_MAIN_DOC'] : false;
					break;
				default :
					$doc = isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc'] : false;
			}
			if ($doc) {
				if ($property) {
					eval('$retval = isset($doc->' . $name . ');');
					return $retval;
				} else {
					if ($type == 'href') {
						if ($doc->elements[$name . '_we_jkhdsf_int']['dat']) {
							return isset($doc->elements[$name . '_we_jkhdsf_intPath']['dat']);
						}
					}
					$fieldType = isset($doc->elements[$name]['type']) ? $doc->elements[$name]['type'] : '';
					$issetElemNameDat = isset($doc->elements[$name]['dat']);
					if ($fieldType == 'checkbox_feld' && $issetElemNameDat && $doc->elements[$name]['dat'] == 0)
						return false;
					return $issetElemNameDat;
				}
			} else {
				return false;
			}
	}
}

function we_tag_ifVarSet($attribs, $content){
	$foo = attributFehltError($attribs, "name", "ifVarSet");
	if ($foo) {
		print($foo);
		return "";
	}

	$type = we_getTagAttribute("var", $attribs);
	$type = $type ? $type : we_getTagAttribute("type", $attribs);
	$doc = we_getTagAttribute("doc", $attribs);
	$name = we_getTagAttribute("name", $attribs);
	$formname = we_getTagAttribute("formname", $attribs, "we_global_form");
	$property = we_getTagAttribute("property", $attribs, "", true);
	$shopname = we_getTagAttribute('shopname', $attribs, '');

	return we_isVarSet($name, $type, $doc, $property, $formname, $shopname);
}
