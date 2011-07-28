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
	$docAttr = we_getTagAttribute('doc', $attribs);
	$type = we_getTagAttribute('type', $attribs);
	$match = we_getTagAttribute('match', $attribs);
	$name = we_getTagAttribute('name', $attribs);
	$type = we_getTagAttribute('type', $attribs, 'txt');
	$formname = we_getTagAttribute('formname', $attribs, 'we_global_form');
	$property = we_getTagAttribute('property', $attribs, '', true);

	if (!we_isVarSet($match, $type, $docAttr, $property, $formname))
		return false;

	switch ($type) {
		case 'request' :
			return (strlen($_REQUEST[$match]) > 0);
			break;
		case 'post' :
			return (strlen($_POST[$match]) > 0);
			break;
		case 'get' :
			return (strlen($_GET[$match]) > 0);
			break;
		case 'global' :
			return (strlen($GLOBALS[$match]) > 0);
			break;
		case 'session' :
			$foo = isset($_SESSION[$match]) ? $_SESSION[$match] : '';
			return (strlen($foo) > 0);
			break;
		case 'sessionfield' :
			return (strlen($_SESSION['webuser'][$match]) > 0);
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
					eval('$retVal = isset($doc->' . $match . ') ? $doc->' . $match . ' : "";');
					return $retVal;
				} else {
					$name = $match;
					switch ($type) {
						case 'href' :
							$attribs['name'] = $match;
							$foo = $doc->getField($attribs, $type, true);
							break;
						case 'multiobject' :
							$attribs['name'] = $match;
							$data = unserialize($doc->getField($attribs, $type, true));
							if (!is_array($data['objects'])) {
								$data['objects'] = array();
							}
							include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/object/we_listview_multiobject.class.php');
							$temp = new we_listview_multiobject($match);
							if (sizeof($temp->Record) > 0) {
								return true;
							} else {
								return false;
							}
						default :
							$foo = $doc->getElement($match);
					}
					return (strlen($foo) > 0);
				}
			} else {
				return false;
			}
	}
}

function we_tag_ifVarEmpty($attribs, $content){
	if (($foo = attributFehltError($attribs, 'match', 'ifVarEmpty'))) {
		print($foo);
		return false;
	}
	return !we_isVarNotEmpty($attribs);
}
