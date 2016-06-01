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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_ifVarSet(array $attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		echo $foo;
		return false;
	}

	$type = weTag_getAttribute('var', $attribs, weTag_getAttribute('type', $attribs, '', we_base_request::STRING), we_base_request::STRING);
	$docAttr = weTag_getAttribute('doc', $attribs, '', we_base_request::STRING);
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$orig = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);
	$formname = weTag_getAttribute('formname', $attribs, 'we_global_form', we_base_request::STRING);
	$property = weTag_getAttribute('property', $attribs, false, we_base_request::BOOL);
	$shopname = weTag_getAttribute('shopname', $attribs, '', we_base_request::STRING);

	switch($type){
		case 'request' :
			return getArrayValue($_REQUEST, null, $orig, true);
		case 'post' :
			return getArrayValue($_POST, null, $orig, true);
		case 'get' :
			return getArrayValue($_GET, null, $orig, true);
		case 'global' :
			return getArrayValue($GLOBALS, null, $name, true)|| getArrayValue($GLOBALS, null, $orig, true);
		case 'session' :
			return getArrayValue($_SESSION, null, $orig, true);
		case 'sessionfield' :
			return isset($_SESSION['webuser'][$orig]);
		case 'shopField' :
			return (isset($GLOBALS[$shopname]) ? $GLOBALS[$shopname]->hasCartField($orig) : false);
		case 'sum' :
			return (isset($GLOBALS['summe']) && isset($GLOBALS['summe'][$orig]));
	}

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
		return isset($doc->$name) || isset($doc->$orig);
	}
	if($type === 'href' && $doc->getElement($name . we_base_link::MAGIC_INT_LINK, 'dat', -1) == 0){
		//internal link is empty, check if external link is present
		return $doc->issetElement($name . we_base_link::MAGIC_INT_LINK_EXTPATH);
	}
	if($doc->issetElement($name)){
		switch(isset($doc->elements[$name]['type']) ? $doc->elements[$name]['type'] : ''){
			case 'checkbox_feld':
				return intval($doc->getElement($name)) > 0;
			case 'img':
				return intval($doc->getElement($name, 'bdid')) > 0;
			case 'href'://can be serialized
				return intval($doc->getElement($name, 'bdid')) > 0 || $doc->getElement($name);
			default:
				return isset($doc->elements[$name]['dat']);
		}
	}
	return false;
}
