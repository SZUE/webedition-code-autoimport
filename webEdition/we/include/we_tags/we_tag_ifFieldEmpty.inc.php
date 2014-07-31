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
function we_isFieldNotEmpty($attribs){
	$type = weTag_getAttribute('type', $attribs);
	$match = weTag_getAttribute('match', $attribs);
	if(!isset($GLOBALS['lv'])){
		echo parseError(g_l('parser', '[field_not_in_lv]'));
		return false;
	}

	$orig_match = $match;
	$match = ($GLOBALS['lv']->f($match) ? $match : we_tag_getPostName($match));

	switch($type){
		case 'calendar' :
			if(isset($GLOBALS['lv']->calendar_struct)){
				if($GLOBALS['lv']->calendar_struct['date'] < 0 || count($GLOBALS['lv']->calendar_struct['storage']) < 1){
					return false;
				}
				switch($orig_match){
					case 'day':
						$sd = mktime(0, 0, 0, $GLOBALS['lv']->calendar_struct['month_human'], $GLOBALS['lv']->calendar_struct['day_human'], $GLOBALS['lv']->calendar_struct['year_human']);
						$ed = mktime(23, 59, 59, $GLOBALS['lv']->calendar_struct['month_human'], $GLOBALS['lv']->calendar_struct['day_human'], $GLOBALS['lv']->calendar_struct['year_human']);
						break;
					case 'month':
						$sd = mktime(0, 0, 0, $GLOBALS['lv']->calendar_struct['month_human'], 1, $GLOBALS['lv']->calendar_struct['year_human']);
						$ed = mktime(23, 59, 59, $GLOBALS['lv']->calendar_struct['month_human'], $GLOBALS['lv']->calendar_struct['numofentries'], $GLOBALS['lv']->calendar_struct['year_human']);
						break;
					case 'year':
						$sd = mktime(0, 0, 0, 1, 1, $GLOBALS['lv']->calendar_struct['year_human']);
						$sd = mktime(23, 59, 59, 12, 31, $GLOBALS['lv']->calendar_struct['year_human']);
						break;
				}
				if(isset($sd) && isset($ed)){
					foreach($GLOBALS['lv']->calendar_struct['storage'] as $entry){
						if($sd < $entry && $ed > $entry)
							return true;
					}
				}
			}
			return false;
		case 'multiobject':
			$data = (isset($GLOBALS['lv']) ?
					(method_exists($GLOBALS['lv'], 'getObject') || ($GLOBALS['lv'] instanceof we_shop_shop) ?
						unserialize($GLOBALS['lv']->getObject()->f($orig_match)) :
						unserialize($GLOBALS['lv']->getDBf('we_' . $orig_match))) :
					unserialize($GLOBALS['we_doc']->getElement($orig_match)));

			if(isset($data['objects']) && is_array($data['objects']) && !empty($data['objects'])){
				$test = array_count_values($data['objects']);
				return (count($test) > 1 || (count($test) == 1 && !isset($test[''])));
			}
			return false;
		case 'object' : //Bug 3837: erstmal die Klasse rausfinden um auf den Eintrag we_we_object_X zu kommen
			if($GLOBALS['lv'] instanceof we_listview_document){ // listview/document with objects included using we:object
				return (bool) $GLOBALS['lv']->f($match);
			}
			$match = strpos($orig_match, '/') === false ? $orig_match : substr(strrchr($orig_match, '/'), 1);
			$objectid = f('SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Text="' . $GLOBALS['DB_WE']->escape($match) . '"', 'ID', $GLOBALS['DB_WE']);
			return (bool) $GLOBALS['lv']->f('we_object_' . $objectid);
		case 'checkbox' :
		case 'binary' :
		case 'img' :
		case 'flashmovie' :
		case 'quicktime' :
			return (bool) $GLOBALS['lv']->f($match);
		case 'float':
			return floatval($GLOBALS['lv']->f($match)) !== floatval(0);
		case 'int':
			return intval($GLOBALS['lv']->f($match)) !== 0;
		case 'href' :
			switch(get_class($GLOBALS['lv'])){
				case 'we_object_listview':
				case 'we_object_tag':
					$hrefArr = $GLOBALS['lv']->f($match) ? unserialize($GLOBALS['lv']->f($match)) : array();
					if(!is_array($hrefArr)){
						$hrefArr = array();
					}
					$hreftmp = trim(we_document::getHrefByArray($hrefArr));
					if(substr($hreftmp, 0, 1) == '/' && (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp))){
						return false;
					}
					return (bool) $hreftmp;
			}

			// we must check $match . we_base_link::MAGIC_INT_LINK for block-Postfix instead of $match (which exists only for href type = ext): #6422
			$isInBlock = ( $GLOBALS['lv']->f($orig_match . we_base_link::MAGIC_INT_LINK) || $GLOBALS['lv']->f($orig_match) ) ? false : true;
			$match = $isInBlock ? we_tag_getPostName($orig_match) : $orig_match;

			$int = ($GLOBALS['lv']->f($match . we_base_link::MAGIC_INT_LINK) == '') ? 0 : $GLOBALS['lv']->f($match . we_base_link::MAGIC_INT_LINK);
			if($int){ // for type = href int
				$intID = $GLOBALS['lv']->f($match . we_base_link::MAGIC_INT_LINK_ID);
				return ($intID > 0 ? strlen(id_to_path($intID)) > 0 : false);
			} else {
				$hreftmp = $GLOBALS['lv']->f($match);
				if(substr($hreftmp, 0, 1) == '/' && (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp))){
					return false;
				}
			}
			break; //see return of function
		default :
			$_tmp = @unserialize($GLOBALS['lv']->f($match));
			if(is_array($_tmp)){
				return count($_tmp) > 0;
			}
		//no break;
	}
	return $GLOBALS['lv']->f($match) != '';
}

function we_tag_ifFieldEmpty($attribs){
	if(($foo = attributFehltError($attribs, 'match', __FUNCTION__))){
		print($foo);
		return false;
	}
	return !we_isFieldNotEmpty($attribs);
}
