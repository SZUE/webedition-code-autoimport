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
	switch($type){
		case 'request' :
			return isset($_REQUEST[$name]);
		case 'post' :
			return isset($_POST[$name]);
		case 'get' :
			return isset($_GET[$name]);
		case 'global' :
			return isset($GLOBALS[$name]);
		case 'session' :
			return isset($_SESSION[$name]);
		case 'sessionfield' :
			return isset($_SESSION['webuser'][$name]);
		case 'shopField' :
			if(isset($GLOBALS[$shopname])){
				return $GLOBALS[$shopname]->hasCartField($name);
			}
			break;
		case 'sum' :
			return (isset($GLOBALS['summe']) && isset($GLOBALS['summe'][$name]));
		default :
			$doc = false;
			switch($docAttr){
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
			if($doc){
				if($property){
					$retval = isset($doc->$name);
					return $retval;
				} else{
					if($type == 'href'){
						if($doc->elements[$name . '_we_jkhdsf_int']['dat']){
							return isset($doc->elements[$name . '_we_jkhdsf_intPath']['dat']);
						}
					}
					$fieldType = isset($doc->elements[$name]['type']) ? $doc->elements[$name]['type'] : '';
					$issetElemNameDat = isset($doc->elements[$name]['dat']);
					if($fieldType == 'checkbox_feld' && $issetElemNameDat && $doc->elements[$name]['dat'] == 0)
						return false;
					return $issetElemNameDat;
				}
			} else{
				return false;
			}
	}
}

function we_tag_ifVarSet($attribs, $content){
	if(($foo = attributFehltError($attribs, "name", "ifVarSet"))){
		print($foo);
		return "";
	}

	$type = weTag_getAttribute("var", $attribs);
	$type = $type ? $type : weTag_getAttribute("type", $attribs);
	$doc = weTag_getAttribute("doc", $attribs);
	$name = weTag_getAttribute("name", $attribs);
	$formname = weTag_getAttribute("formname", $attribs, "we_global_form");
	$property = weTag_getAttribute("property", $attribs, false, true);
	$shopname = weTag_getAttribute('shopname', $attribs);

	return we_isVarSet($name, $type, $doc, $property, $formname, $shopname);
}
