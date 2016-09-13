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
function we_tag_var(array $attribs){
	switch(weTag_getAttribute('type', $attribs)){ //Fix #9311
		case 'shopVat':
		case 'shopCategory': //shopVat and shopCategory need no attribute 'name'
			break;
		default:
			if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
				return $foo;
			}
	}
	$docAttr = weTag_getAttribute('doc', $attribs);
	$name = weTag_getAttribute('name', $attribs);
	$name_orig = weTag_getAttribute('_name_orig', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, true); // #3771
	$format = weTag_getAttribute('format', $attribs);
	$num_format = weTag_getAttribute('num_format', $attribs, '', we_base_request::STRING);
	$doc = we_getDocForTag($docAttr, false);
	$varType = weTag_getAttribute('varType', $attribs, we_base_request::STRING, we_base_request::STRING);
	$prepareSQL = weTag_getAttribute('prepareSQL', $attribs, false, true);
	$attribs = removeAttribs($attribs, ['varType', 'prepareSQL', 'htmlspecialchars',]);

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
		case 'global':
			$return = getArrayValue($GLOBALS, null, $name_orig);
			break;
		case 'multiobject' :
			$data = we_unserialize($doc->getField($attribs, $type, true));
			return is_array($data) ? (!empty($data['objects']) ? implode(',', $data['objects']) : implode(',', $data)) : '';
		case 'property' :
			$return = (isset($GLOBALS['we_obj']) ?
					$GLOBALS['we_obj']->$name_orig :
					$doc->$name_orig);
			break;
		case 'shopVat' :
			if(defined('SHOP_TABLE')){
				if(!we_shop_category::isCategoryMode()){
					$vatId = $doc->getElement(WE_SHOP_VAT_FIELD_NAME);
					$return = we_shop_vats::getVatRateForSite($vatId);
				} else {
					$shopVatAttribs = $attribs;
					$shopVatAttribs['shopcategoryid'] = $doc->getElement(WE_SHOP_CATEGORY_FIELD_NAME);
					$shopVatAttribs['wedoccategories'] = $doc->Category;
					unset($shopVatAttribs['type']);
					$return = we_tag('shopVat', $shopVatAttribs);
				}
				return $prepareSQL ? $GLOBALS['DB_WE']->escape($return) : $return;
			}
			return '';
		case 'shopCategory' :
			if(defined('SHOP_TABLE')){
				return $doc->getElement(WE_SHOP_CATEGORY_FIELD_NAME);
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
			// wenn die Abfrage im Aktuellen Objekt kein Ergebnis liefert
			// wird in den eingebundenen Objekten ueberprueft ob das Feld existiert
			$name = ($type === 'select' && $normVal == '' ? $name_orig : $name);
			$selectKey = weTag_getAttribute('key', $attribs, false, we_base_request::BOOL);
			if($type === 'select' && $selectKey){
				$return = $doc->getElement($name);
				break;
			}

			if(isset($doc->DefArray) && is_array($doc->DefArray)){
				$keys = array_keys($doc->DefArray);
				foreach($keys as $glob_key){
					if((strpos($glob_key, we_object::QUERY_PREFIX) === 0 && ($rest = substr($glob_key, strlen(we_object::QUERY_PREFIX)))) || (strpos($glob_key, 'we_object_') === 0 && ($rest = substr($glob_key, 10)))){
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
	if($num_format){
		return we_base_util::formatNumber($return, $num_format);
	}
	$return = $htmlspecialchars ? oldHtmlspecialchars($return) : $return;
	return $prepareSQL ? $GLOBALS['DB_WE']->escape($return) : $return;
}
