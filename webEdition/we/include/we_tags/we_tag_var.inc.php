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
function we_tag_var($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$name_orig = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);
	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);
	$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, we_base_request::BOOL); // #3771
	$format = weTag_getAttribute('format', $attribs, '', we_base_request::STRING);
	$doc = we_getDocForTag(weTag_getAttribute('doc', $attribs, '', we_base_request::STRING), false);
	$varType = weTag_getAttribute('varType', $attribs, we_base_request::STRING, we_base_request::STRING);
	$prepareSQL = weTag_getAttribute('prepareSQL', $attribs, false, we_base_request::BOOL);

	switch($type){
		case 'session' :
			$return = getArrayValue($_SESSION, null, $name_orig);
			break;
		case 'request' :
			$return = we_base_util::rmPhp(we_base_request::filterVar(getArrayValue($_REQUEST, null, $name_orig), $varType));
			break;
		case 'post' :
			$return = we_base_util::rmPhp(we_base_request::filterVar(getArrayValue($_POST, null, $name_orig), $varType));
			break;
		case 'get' :
			$return = we_base_util::rmPhp(we_base_request::filterVar(getArrayValue($_GET, null, $name_orig), $varType));
			break;
		case 'global' :
			$return = getArrayValue($GLOBALS, null, $name_orig);
			break;
		case 'multiobject' :
			$data = unserialize($doc->getField($attribs, $type, true));
			return (isset($data['objects']) && $data['objects'] ? implode(',', $data['objects']) : '');

		case 'property' :
			$return = (isset($GLOBALS['we_obj']) ?
							$GLOBALS['we_obj']->$name_orig :
							$doc->$name_orig);
			break;

		case 'shopVat' :
			if(defined('SHOP_TABLE')){
				$vatId = $doc->getElement(WE_SHOP_VAT_FIELD_NAME);
				$return = we_shop_vats::getVatRateForSite($vatId);
				return $prepareSQL ? $GLOBALS['DB_WE']->escape($return) : $return;
			}
			return '';
		case 'link' :
			return $doc->getField($attribs, $type, false);
		// bugfix #3634
		default :
			$normVal = $doc->getField($attribs, $type, true);
			if($type === 'date'){//already formated
				$format = '';
			}
			// bugfix 7557
			// wenn die Abfrage im Aktuellen Objekt kein Erg?bnis liefert
			// wird in den eingebundenen Objekten ?berpr?ft ob das Feld existiert
			$name = ($type === 'select' && $normVal == '' ? $name_orig : $name);
			$selectKey = weTag_getAttribute('key', $attribs, false, we_base_request::BOOL);
			if($type === 'select' && $selectKey){
				$return = $doc->getElement($name);
				break;
			}

			if(isset($doc->DefArray) && is_array($doc->DefArray)){
				$keys = array_keys($doc->DefArray);
				foreach($keys as $_glob_key){
					if((strpos($_glob_key, we_object::QUERY_PREFIX) === 0 && ($rest = substr($_glob_key, strlen(we_object::QUERY_PREFIX)))) || (strpos($_glob_key, 'we_object_') === 0 && ($rest = substr($_glob_key, 10)))){
						$normVal = $doc->getFieldByVal($doc->getElement($name), $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $rest);
					}

					if($normVal != ''){
						break;
					}
				}
			}
			// EOF bugfix 7557
			$return = $normVal;
	}

	if($format){//date
		return date($format, intval($return));
	}
	$return = $htmlspecialchars ? oldHtmlspecialchars($return) : $return;
	$return = $prepareSQL ? $GLOBALS['DB_WE']->escape($return) : $return;

	return $return;
}
