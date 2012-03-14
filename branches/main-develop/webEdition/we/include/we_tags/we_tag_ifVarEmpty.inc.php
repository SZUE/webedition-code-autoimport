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
include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_tags/we_tag_ifVarSet.inc.php");

function we_isVarNotEmpty($attribs){
	$docAttr = weTag_getAttribute('doc', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$match = we_tag_getPostName(weTag_getAttribute('match', $attribs));
	$name = weTag_getAttribute('name', $attribs);
	$type = weTag_getAttribute('type', $attribs, 'txt');
	$formname = weTag_getAttribute('formname', $attribs, 'we_global_form');
	$property = weTag_getAttribute('property', $attribs, false, true);

	if(!we_isVarSet($match, $type, $docAttr, $property, $formname))
		return false;

	switch($type){
		case 'request' :
			return (strlen($_REQUEST[$match]) > 0);
		case 'post' :
			return (strlen($_POST[$match]) > 0);
		case 'get' :
			return (strlen($_GET[$match]) > 0);
		case 'global' :
			return (strlen($GLOBALS[$match]) > 0);
		case 'session' :
			$foo = isset($_SESSION[$match]) ? $_SESSION[$match] : '';
			return (strlen($foo) > 0);
		case 'sessionfield' :
			return (strlen($_SESSION['webuser'][$match]) > 0);
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
					$retVal = isset($doc->$match) ? $doc->$match : '';
					return $retVal;
				} else{
					$name = $match;
					switch($type){
						case 'href' :
							$attribs['name'] = $match;
							$foo = $doc->getField($attribs, $type, true);
							break;
						case 'multiobject' :
							$attribs['name'] = $match;
							$data = unserialize($doc->getField($attribs, $type, true));
							if(!is_array($data['objects'])){
								$data['objects'] = array();
							}
							include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/object/we_listview_multiobject.class.php');
							$temp = new we_listview_multiobject($match);
							if(sizeof($temp->Record) > 0){
								return true;
							} else{
								return false;
							}
						default :
							$foo = $doc->getElement($match);
					}
					return (strlen($foo) > 0);
				}
			} else{
				return false;
			}
	}
}

function we_tag_ifVarEmpty($attribs){
	if(($foo = attributFehltError($attribs, 'match', __FUNCTION__))){
		print($foo);
		return false;
	}
	return !we_isVarNotEmpty($attribs);
}
