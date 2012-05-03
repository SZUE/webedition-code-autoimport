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
function we_isFieldNotEmpty($attribs){
	$type = weTag_getAttribute('type', $attribs);
	$match = we_tag_getPostName(weTag_getAttribute('match', $attribs));
	switch($type){
		case 'calendar' :
			if(isset($GLOBALS['lv']->calendar_struct)){
				if($GLOBALS['lv']->calendar_struct['date'] < 0 || count($GLOBALS['lv']->calendar_struct['storage']) < 1){
					return false;
				}
				switch($match){
					case 'day':
						$sd = mktime(
							0, 0, 0, $GLOBALS['lv']->calendar_struct['month_human'], $GLOBALS['lv']->calendar_struct['day_human'], $GLOBALS['lv']->calendar_struct['year_human']);
						$ed = mktime(
							23, 59, 59, $GLOBALS['lv']->calendar_struct['month_human'], $GLOBALS['lv']->calendar_struct['day_human'], $GLOBALS['lv']->calendar_struct['year_human']);
						break;
					case 'month':
						$sd = mktime(
							0, 0, 0, $GLOBALS['lv']->calendar_struct['month_human'], 1, $GLOBALS['lv']->calendar_struct['year_human']);
						$ed = mktime(
							23, 59, 59, $GLOBALS['lv']->calendar_struct['month_human'], $GLOBALS['lv']->calendar_struct['numofentries'], $GLOBALS['lv']->calendar_struct['year_human']);
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
		case 'multiobject' :
			if(isset($GLOBALS['lv'])){
				if(isset($GLOBALS['lv']->object)){
					$data = unserialize($GLOBALS['lv']->object->DB_WE->Record['we_' . $match]);
				} else{
					if($GLOBALS['lv']->ClassName == 'we_listview_shoppingCart'){//Bug #4827
						$data = unserialize($GLOBALS['lv']->f($match));
					} else{
						$data = unserialize($GLOBALS['lv']->DB_WE->Record['we_' . $match]);
					}
				}
			} else{
				$data = unserialize($GLOBALS['we_doc']->getElement($match));
			}
			if(isset($data['objects']) && is_array($data['objects']) && sizeof($data['objects']) > 0){
				$test = array_count_values($data['objects']);
				return (sizeof($test) > 1 || (sizeof($test) == 1 && !isset($test[''])));
			}
			return false;
		case 'object' : //Bug 3837: erstmal die Klasse rausfinden um auf den Eintrag we_we_object_X zu kommen
			$objectdb = new DB_WE();
			$objectid = f('SELECT ID FROM ' . OBJECT_TABLE . " WHERE Text='" . $match . "'", 'ID', $objectdb);
			return $GLOBALS['lv']->f('we_object_' . $objectid);
		case 'checkbox' :
		case 'binary' :
		case 'img' :
		case 'flashmovie' :
		case 'quicktime' :
			return $GLOBALS['lv']->f(we_tag_getPostName($match));
		case 'href' :
			$match = we_tag_getPostName($match);
			if($GLOBALS['lv']->ClassName == 'we_listview_object' || $GLOBALS['lv']->ClassName == 'we_objecttag'){
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
			$int = ($GLOBALS['lv']->f($match . '_we_jkhdsf_int') == '') ? 0 : $GLOBALS['lv']->f($match . '_we_jkhdsf_int');
			if($int){ // for type = href int
				$intID = $GLOBALS['lv']->f($match . '_we_jkhdsf_intID');
				return ($intID > 0 ? strlen(id_to_path($intID)) > 0 : false);
			} else{
				$hreftmp = $GLOBALS['lv']->f($match);
				if(substr($hreftmp, 0, 1) == '/' && (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp))){
					return false;
				}
			}
			break;//see return of function
		default :
			$match = we_tag_getPostName($match);
			$_tmp = @unserialize($GLOBALS['lv']->f($match));
			if(is_array($_tmp)){
				return count($_tmp) > 0;
			}
			//no break;
	}
	return $GLOBALS['lv']->f($match) != '';
}

function we_tag_ifFieldEmpty($attribs, $content){
	if(($foo = attributFehltError($attribs, 'match', __FUNCTION__))){
		print($foo);
		return false;
	}
	return !we_isFieldNotEmpty($attribs);
}
