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
function we_isVarSet($name, $orig, $type, $docAttr, $property = false, $formname = '', $shopname = ''){
	switch($type){
		case 'request' :
			return isset($_REQUEST[$orig]);
		case 'post' :
			return isset($_POST[$orig]);
		case 'get' :
			return isset($_GET[$orig]);
		case 'global' :
			return isset($GLOBALS[$name]) || isset($GLOBALS[$orig]);
		case 'session' :
			return isset($_SESSION[$orig]);
		case 'sessionfield' :
			return isset($_SESSION['webuser'][$orig]);
		case 'shopField' :
			return (isset($GLOBALS[$shopname]) ? $GLOBALS[$shopname]->hasCartField($orig) : false);
		case 'sum' :
			return (isset($GLOBALS['summe']) && isset($GLOBALS['summe'][$orig]));
		default :

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
			if(!$doc){
				return false;
			}
			if($property){
				return isset($doc->$name) || isset($doc->orig);
			}
			if($type == 'href' && isset($doc->elements[$name . we_base_link::MAGIC_INT_LINK]) && $doc->elements[$name . we_base_link::MAGIC_INT_LINK]['dat']){
				return isset($doc->elements[$name . we_base_link::MAGIC_INT_LINK_PATH]['dat']);
			}
			if(isset($doc->elements[$name])){
				switch(isset($doc->elements[$name]['type']) ? $doc->elements[$name]['type'] : ''){
					case 'checkbox_feld':
						return isset($doc->elements[$name]['dat']) && $doc->elements[$name]['dat'] != 0;
					case 'img':
						return isset($doc->elements[$name]['bdid']) && $doc->elements[$name]['bdid'] != 0;
					default:
						return isset($doc->elements[$name]['dat']);
				}
			}
	}
}

function we_tag_ifVarSet($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		print($foo);
		return false;
	}

	$type = weTag_getAttribute('var', $attribs, weTag_getAttribute('type', $attribs));
	$doc = weTag_getAttribute('doc', $attribs);
	$name = weTag_getAttribute('name', $attribs);
	$name_orig = weTag_getAttribute('_name_orig', $attribs);
	$formname = weTag_getAttribute('formname', $attribs, 'we_global_form');
	$property = weTag_getAttribute('property', $attribs, false, true);
	$shopname = weTag_getAttribute('shopname', $attribs);

	return we_isVarSet($name, $name_orig, $type, $doc, $property, $formname, $shopname);
}
