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
	$docAttr = weTag_getAttribute('doc', $attribs);
	$name = weTag_getAttribute('name', $attribs);
	$name_orig = weTag_getAttribute('_name_orig', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, true); // #3771
	$format = weTag_getAttribute('format', $attribs);
	$doc = we_getDocForTag($docAttr, false);

	switch($type){
		case 'session' :
			$return = getArrayValue($_SESSION, null, $name_orig);
			return $htmlspecialchars ? oldHtmlspecialchars($return) : ($format ? date($format, intval($return)) : $return);
		case 'request' :
			$return = filterXss(we_base_util::rmPhp(getArrayValue($_REQUEST, null, $name_orig)));
			return $htmlspecialchars ? oldHtmlspecialchars($return) : ($format ? date($format, intval($return)) : $return);
		case 'post' :
			$return = we_base_util::rmPhp(getArrayValue($_POST, null, $name_orig));
			return $htmlspecialchars ? oldHtmlspecialchars($return) : ($format ? date($format, intval($return)) : $return);
		case 'get' :
			$return = we_base_util::rmPhp(getArrayValue($_GET, null, $name_orig));
			return $htmlspecialchars ? oldHtmlspecialchars($return) : ($format ? date($format, intval($return)) : $return);
		case 'global' :
			$return = getArrayValue($GLOBALS, null, $name_orig);
			return $htmlspecialchars ? oldHtmlspecialchars($return) : ($format ? date($format, intval($return)) : $return);
		case 'multiobject' :
			$data = unserialize($doc->getField($attribs, $type, true));
			return (isset($data['objects']) && $data['objects'] ? implode(',', $data['objects']) : '');

		case 'property' :
			$return = (isset($GLOBALS['we_obj']) ?
					$GLOBALS['we_obj']->$name_orig :
					$doc->$name_orig);
			return ($format ? date($format, intval($return)) : $return);

		case 'shopVat' :
			if(defined('SHOP_TABLE')){
				$vatId = $doc->getElement(WE_SHOP_VAT_FIELD_NAME);
				return we_shop_vats::getVatRateForSite($vatId);
			}
			return '';
		case 'link' :
			return $doc->getField($attribs, $type, false);
		// bugfix #3634
		default :
			$normVal = $doc->getField($attribs, $type, true);
			if($type == 'date'){//already formated
				$format = '';
			}
			// bugfix 7557
			// wenn die Abfrage im Aktuellen Objekt kein Erg?bnis liefert
			// wird in den eingebundenen Objekten ?berpr?ft ob das Feld existiert
			$name = ($type == 'select' && $normVal == '' ? $name_orig : $name);
			$selectKey = weTag_getAttribute('key', $attribs, false, true);
			if($type == 'select' && $selectKey){
				return $htmlspecialchars ? oldHtmlspecialchars($doc->getElement($name)) :
					($format ? date($format, intval($doc->getElement($name))) : $doc->getElement($name));
			}

			if(isset($doc->DefArray) && is_array($doc->DefArray)){
				$keys = array_keys($doc->DefArray);
				foreach($keys as $_glob_key){
					if((strpos($_glob_key, we_object::QUERY_PREFIX) === 0 && ($rest = substr($_glob_key, strlen(we_object::QUERY_PREFIX)))) || (strpos($_glob_key, 'we_object_') === 0 && ($rest = substr($_glob_key, 10)))){
						$normVal = $doc->getFieldByVal($doc->getElement($name), $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $rest);
					}

					if($normVal != ''){
						return $htmlspecialchars ? oldHtmlspecialchars($normVal) :
							($format ? date($format, intval($normVal)) : $normVal);
					}
				}
			}
			// EOF bugfix 7557


			return ($format ? date($format, intval($normVal)) : $normVal);
	}
}
