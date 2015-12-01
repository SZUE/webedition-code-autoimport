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

function we_tag_ifVarEmpty($attribs){
	if(($foo = attributFehltError($attribs, 'match', __FUNCTION__))){
		echo $foo;
		return false;
	}
	$match_orig = weTag_getAttribute('match', $attribs, '', we_base_request::RAW);
	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);

	switch($type){
		case 'request' :
			$ret = getArrayValue($_REQUEST, null, $match_orig);
			return empty($ret);
		case 'post' :
			$ret = getArrayValue($_POST, null, $match_orig);
			return empty($ret);
		case 'get' :
			$ret = getArrayValue($_GET, null, $match_orig);
			return empty($ret);
		case 'global' :
			$ret = getArrayValue($GLOBALS, null, $match_orig);
			return empty($ret);
		case 'session' :
			$ret = getArrayValue($_SESSION, null, $match_orig);
			return empty($ret);
		case 'sessionfield' :
			return empty($_SESSION['webuser'][$match_orig]);
	}
	$docAttr = weTag_getAttribute('doc', $attribs, '', we_base_request::STRING);
	$match = we_tag_getPostName($match_orig); //#6367

	switch($docAttr){
		case 'object' :
		case 'document' :
			$formname = weTag_getAttribute('formname', $attribs, 'we_global_form', we_base_request::STRING);
			$doc = isset($GLOBALS['we_' . $docAttr][$formname]) ? $GLOBALS['we_' . $docAttr][$formname] : false;
			break;
		case 'top' :
			$doc = isset($GLOBALS['WE_MAIN_DOC']) ? $GLOBALS['WE_MAIN_DOC'] : false;
			break;
		default :
			$doc = isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc'] : false;
	}
	if(!$doc){
		return true;
	}

	if(weTag_getAttribute('property', $attribs, false, we_base_request::BOOL)){
		return empty($doc->$match_orig);
	}

	$type = $type ? : $doc->getElement($match_orig, 'type');
	switch($type){
		case 'href' :
			$attribs['name'] = $match;
			$attribs['_name_orig'] = $match_orig;
			$foo = $doc->getField($attribs, $type, true);
			return empty($foo);
		case 'multiobject':
			//FIXME: this makes no sense
			$attribs['name'] = $match;
			$attribs['_name_orig'] = $match_orig;
			$data = we_unserialize($doc->getField($attribs, $type, true));
			$objects = isset($data['objects']) ? $data['objects'] : $data;
			return empty($objects);
		default :
			$elemType = $doc->getElement($match, 'type');
			$foo = $doc->getElement($match, $elemType === 'img' ? 'bdid' : 'dat');

			if(!$foo){
				$elemType = $doc->getElement($match_orig, 'type');
				$foo = $doc->getElement($match_orig, $elemType === 'img' ? 'bdid' : 'dat');
			}
			return empty($foo);
	}
}
