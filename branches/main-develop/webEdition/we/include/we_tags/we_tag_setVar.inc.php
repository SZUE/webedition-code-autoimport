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
function we_tag_setVar($attribs){
	if(($foo = attributFehltError($attribs, array('nameto' => false, 'to' => false), __FUNCTION__))){
		return $foo;
	}

	$nameFrom = weTag_getAttribute('namefrom', $attribs);
	$nameTo = weTag_getAttribute('nameto', $attribs);
	$typeFrom = weTag_getAttribute('typefrom', $attribs, 'text');
	$to = weTag_getAttribute('to', $attribs);
	$from = weTag_getAttribute('from', $attribs);
	$propertyTo = weTag_getAttribute('propertyto', $attribs, false, true);
	$propertyFrom = weTag_getAttribute('propertyfrom', $attribs, false, true);
	$striptags = weTag_getAttribute('striptags', $attribs, false, true);
	$formnameTo = weTag_getAttribute('formnameto', $attribs, 'we_global_form');
	$formnameFrom = weTag_getAttribute('formnamefrom', $attribs, 'we_global_form');
	$varType = $typeFrom === 'href' ? we_base_request::URL : weTag_getAttribute('varType', $attribs, we_base_request::STRING);
	$prepareSQL = weTag_getAttribute('prepareSQL', $attribs, false, true);

	if(isset($attribs['value'])){
		$valueFrom = weTag_getAttribute('value', $attribs);
	} else {
		switch($from){
			case 'request' :
			case 'post' :
			case 'get' :
			case 'global' :
			case 'session' :
				$valueFrom = we_tag_var(array('type' => $from, '_name_orig' => $nameFrom, 'name' => $nameFrom, 'varType' => $varType));
				break;
			case 'top' :
				if($propertyFrom){
					$valueFrom = isset($GLOBALS['WE_MAIN_DOC']->$nameFrom) ? $GLOBALS['WE_MAIN_DOC']->$nameFrom : '';
				} else {
					$valueFrom = $GLOBALS['WE_MAIN_DOC']->issetElement($nameFrom . ($typeFrom === 'href' ? we_base_link::MAGIC_INT_LINK : '')) ?
							$GLOBALS['WE_MAIN_DOC']->getField(array('name' => $nameFrom), $typeFrom, true) :
							'';
				}
				break;
			case 'self' :
				if($propertyFrom){
					$valueFrom = isset($GLOBALS['we_doc']->$nameFrom) ? $GLOBALS['we_doc']->$nameFrom : '';
				} else {
					$valueFrom = $GLOBALS['we_doc']->issetElement($nameFrom . ($typeFrom === 'href' ? we_base_link::MAGIC_INT_LINK : '')) ?
							$GLOBALS['we_doc']->getField(array('name' => $nameFrom), $typeFrom, true) :
							'';
				}
				break;
			case 'object' :
			case 'document' :
				$valueFrom = ($propertyFrom ?
								(isset($GLOBALS['we_' . $from][$formnameFrom]->$nameFrom) ? $GLOBALS['we_' . $from][$formnameFrom]->$nameFrom : '') :
								($GLOBALS['we_' . $from][$formnameFrom]->issetElement($nameFrom) ? $GLOBALS['we_' . $from][$formnameFrom]->getElement($nameFrom) : ''));

				break;
			case 'sessionfield' :
				$valueFrom = isset($_SESSION['webuser'][$nameFrom]) ? $_SESSION['webuser'][$nameFrom] : '';
				break;
			case 'calendar' :
				$valueFrom = we_listview_base::getCalendarFieldValue($GLOBALS['lv']->calendar_struct, $nameFrom);
				break;
			case 'listview' :
				if(!isset($GLOBALS['lv'])){
					return parseError(g_l('parser', '[setVar_lv_not_in_lv]'));
				}
				$valueFrom = we_tag('field', array('name' => $nameFrom, 'type' => $typeFrom));
				break;
			case 'block' :
				$nameFrom = we_tag_getPostName($nameFrom);
				if($typeFrom === 'href'){

					if($GLOBALS['we_doc']->issetElement($nameFrom . we_base_link::MAGIC_INT_LINK)){
						$nameFrom .= we_base_link::MAGIC_INT_LINK_PATH;
					}
				}
				$valueFrom = $GLOBALS['WE_MAIN_DOC']->issetElement($nameFrom) ? $GLOBALS['WE_MAIN_DOC']->getField(
								array(
							'name' => $nameFrom
								), $typeFrom, true) : '';
				break;
			case 'listdir' :
				$valueFrom = isset($GLOBALS['we_position']['listdir'][$nameFrom]) ? $GLOBALS['we_position']['listdir'][$nameFrom] : '';
				break;
			default:
				$valueFrom = '';
		}
	}

	$valueFrom = ($striptags ? strip_tags($valueFrom) : $valueFrom);
	$valueFrom = ($prepareSQL ? $GLOBALS['DB_WE']->escape($valueFrom) : $valueFrom);

	switch($to){
		case 'object' :
		case 'document' :
			if($propertyTo){
				if(isset($GLOBALS['we_' . $to][$formnameTo])){
					$GLOBALS['we_' . $to][$formnameTo]->$nameTo = $valueFrom;
				}
			} else {
				if(isset($GLOBALS['we_' . $to][$formnameTo])){
					$GLOBALS['we_' . $to][$formnameTo]->setElement($nameTo, $valueFrom);
				}
			}
			break;
		case 'top' :
		case 'self' :
			if($propertyTo){
				$GLOBALS[($to === 'top' ? 'WE_MAIN_DOC_REF' : 'we_doc')]->$nameTo = $valueFrom;
				break;
			}
		default:
			we_redirect_tagoutput($valueFrom, $nameTo, $to);
	}
}
