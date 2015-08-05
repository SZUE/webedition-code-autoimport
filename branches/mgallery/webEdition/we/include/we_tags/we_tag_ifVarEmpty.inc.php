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
require_once (WE_INCLUDES_PATH . 'we_tags/we_tag_ifVarSet.inc.php');

function we_isVarNotEmpty($attribs){
	$docAttr = weTag_getAttribute('doc', $attribs, '', we_base_request::STRING);
	$match_orig = weTag_getAttribute('match', $attribs, '', we_base_request::RAW);
	$match = we_tag_getPostName($match_orig); //#6367

	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);

	/* 	if(!we_isVarSet($match, $match_orig, $type, $docAttr, $property, $formname)){
	  return false;
	  } */

	switch($type){
		case 'request' :
			return isset($_REQUEST[$match_orig]) && (strlen($_REQUEST[$match_orig]) > 0);
		case 'post' :
			return isset($_POST[$match_orig]) && (strlen($_POST[$match_orig]) > 0);
		case 'get' :
			return isset($_GET[$match_orig]) && (strlen($_GET[$match_orig]) > 0);
		case 'global' :
			return isset($GLOBALS[$match_orig]) && (strlen($GLOBALS[$match_orig]) > 0);
		case 'session' :
			return isset($_SESSION[$match_orig]) && (strlen($_SESSION[$match_orig]) > 0);
		case 'sessionfield' :
			return isset($_SESSION['webuser'][$match_orig]) && (strlen($_SESSION['webuser'][$match_orig]) > 0);
		default :
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
				return false;
			}
			if(weTag_getAttribute('property', $attribs, false, we_base_request::BOOL)){
				return isset($doc->$match_orig) ? !empty($doc->$match_orig) : false;
			}

			$type = $type ? : $doc->getElement($match_orig, 'type');
			switch($type){
				case 'href' :
					$attribs['name'] = $match;
					$attribs['_name_orig'] = $match_orig;
					$foo = $doc->getField($attribs, $type, true);
					break;
				case 'multiobject':
					//FIXME: this makes no sense
					$attribs['name'] = $match;
					$attribs['_name_orig'] = $match_orig;
					$data = we_unserialize($doc->getField($attribs, $type, true));
					$objects = isset($data['objects']) ? $data['objects'] : $data;
					return (!empty($objects));
				default :
					$type = $doc->getElement($match, 'type');
					$foo = $doc->getElement($match, $type === 'img' ? 'bdid' : 'dat');

					if(!$foo){
						$type = $doc->getElement($match_orig, 'type');
						$foo = $doc->getElement($match_orig, $type === 'img' ? 'bdid' : 'dat');
					}
			}
			return (!empty($foo));
	}
}

function we_tag_ifVarEmpty($attribs){
	if(($foo = attributFehltError($attribs, 'match', __FUNCTION__))){
		echo $foo;
		return false;
	}
	return !we_isVarNotEmpty($attribs);
}
