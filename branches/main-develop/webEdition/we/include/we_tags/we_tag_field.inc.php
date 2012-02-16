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
function we_tag_field($attribs, $content){
	$orgName = weTag_getAttribute('_name_orig', $attribs);
	$name = weTag_getAttribute('name', $attribs);

	$href = weTag_getAttribute('href', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$orgAlt = weTag_getAttribute('alt', $attribs);
	$alt = we_tag_getPostName($orgAlt);
	$value = weTag_getAttribute('value', $attribs);
	$max = weTag_getAttribute('max', $attribs);
	$format = weTag_getAttribute('format', $attribs);
	$target = weTag_getAttribute('target', $attribs);
	$tid = weTag_getAttribute('tid', $attribs);
	$class = weTag_getAttribute('class', $attribs);
	$classid = weTag_getAttribute('classid', $attribs);
	$style = weTag_getAttribute('style', $attribs);
	$hyperlink = weTag_getAttribute('hyperlink', $attribs, false, true);
	$src = weTag_getAttribute('src', $attribs);
	$winprops = weTag_getAttribute('winprops', $attribs);
	$id = weTag_getAttribute('id', $attribs);
	$xml = weTag_getAttribute('xml', $attribs);
	$striphtml = weTag_getAttribute('striphtml', $attribs, false, true);
	$only = weTag_getAttribute('only', $attribs);
	$usekey = weTag_getAttribute('usekey', $attribs, false, true);
	$triggerid = weTag_getAttribute('triggerid', $attribs);
	$seeMode = weTag_getAttribute('seeMode', $attribs, true, true);

	$out = '';


	if(!isset($GLOBALS['lv'])){
		return parseError(g_l('parser', '[field_not_in_lv]'));
	}
	$lvname = isset($GLOBALS['lv']->name) ? $GLOBALS['lv']->name : '';

	if($orgAlt == 'we_path')
		$alt = 'WE_PATH';
	else if($orgAlt == 'we_text')
		$alt = 'WE_TEXT';

	if($orgName == 'we_path')
		$name = 'WE_PATH';
	else if($orgName == 'we_text')
		$name = 'WE_TEXT';

	if(isset($attribs['winprops'])){
		unset($attribs['winprops']);
	}

	$classid = $classid ? $classid : (isset($GLOBALS['lv']) ? (isset($GLOBALS['lv']->object->classID) ? $GLOBALS['lv']->object->classID : (isset(
					$GLOBALS['lv']->classID) ? $GLOBALS['lv']->classID : '')) : (isset($GLOBALS['we_doc']->TableID) ? $GLOBALS['we_doc']->TableID : 0));
	$isImageDoc = false;
	if(isset($GLOBALS['lv']->Record['wedoc_ContentType']) && $GLOBALS['lv']->Record['wedoc_ContentType'] == 'image/*'){
		$isImageDoc = true;
	}

	$isCalendar = false;
	if(isset($GLOBALS['lv']->calendar_struct['calendar']) && $GLOBALS['lv']->calendar_struct['calendar'] != '' && $GLOBALS['lv']->isCalendarField(
			$type)){
		$isCalendar = true;
	}

	if(!$GLOBALS['lv']->f('WE_ID') && $GLOBALS['lv']->calendar_struct['calendar'] == ''){
		return '';
	}

	//listview of documents, document with a block. Try to access by blockname.
	$name = ($GLOBALS['lv']->f($name) ? $name : $orgName);
	$alt = ($GLOBALS['lv']->f($alt) ? $alt : $orgAlt);

	switch($type){
		case 'binary' :
			$t = we_document::getFieldByVal($GLOBALS['lv']->f($name), $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview');
			if($only == '' || $only == 'name'){
				$out = $t[0];
			}
			if($only == 'path'){
				$out = $t[1];
			}
			if($only == 'parentpath'){
				$out = $t[2];
			}
			if($only == 'filename'){
				$out = $t[3];
			}
			if($only == 'extension'){
				$out = $t[4];
			}
			if($only == 'filesize'){
				$out = $t[5];
			}
			$href = (empty($href) ? $t[1] : $href);
			break;
		case 'link' :
			if($GLOBALS['lv']->ClassName){
				$out = we_document::getFieldByVal($GLOBALS['lv']->f($name), 'link', $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview');
				$href = (empty($href) ? $out : $href);
				break;
			}
		case 'img' :
			if($src){
				$_imgAtts = array(
					'alt' => '', //  alt must be set
					'src' => $src,
					'xml' => $xml,
				);

				$_imgAtts = array_merge(
					$_imgAtts, useAttribs(
						$attribs, array(
						'alt', 'width', 'height', 'border', 'hspace', 'align', 'vspace'
					))); //  use some atts form attribs array
				$_imgAtts = removeEmptyAttribs($_imgAtts, array(
					'alt'
					));

				$out = getHtmlTag('img', $_imgAtts);
				break;
			}
		//intentionally no break
		case 'int' :
		case 'date' :
		case 'float' :
		case 'checkbox' :
			$idd = ($isImageDoc && $type == 'img' ) ? $GLOBALS['lv']->Record['wedoc_ID'] : $GLOBALS['lv']->f($name);
			if($idd == 0){
				$out = '';
			} else{
				$out = we_document::getFieldByVal($idd, $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview');
			}
			break;
		case 'day' :
		case 'dayname' :
		case 'dayname_long' :
		case 'dayname_short' :
		case 'month' :
		case 'monthname' :
		case 'monthname_long' :
		case 'monthname_short' :
		case 'year' :
		case 'hour' :
		case 'week' :
			$out = listviewBase::getCalendarField($GLOBALS['lv']->calendar_struct['calendar'], $type);
			break;

		case 'multiobject' :
			$temp = unserialize($GLOBALS['lv']->DB_WE->Record['we_' . $name]);
			if(isset($temp['objects']) && sizeof($temp['objects']) > 0){
				$out = implode(',', $temp['objects']);
			} else{
				$out = '';
			}
			break;
		case 'country' :
			$lang = weTag_getAttribute('outputlanguage', $attribs);
			if($lang == ''){
				$docAttr = weTag_getAttribute('doc', $attribs, 'self');
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
			}
			$langcode = substr($lang, 0, 2);
			if($lang == ''){
				$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
				$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
			}
			if(defined('WE_COUNTRIES_DEFAULT') && WE_COUNTRIES_DEFAULT != '' && $GLOBALS['lv']->f($name) == '--'){
				$out = WE_COUNTRIES_DEFAULT;
			} else{
				if(!Zend_Locale::hasCache()){
					Zend_Locale::setCache(getWEZendCache());
				}
				$out = CheckAndConvertISOfrontend(Zend_Locale::getTranslation($GLOBALS['lv']->f($name), 'territory', $langcode));
			}
			break;
		case 'language' :
			$lang = weTag_getAttribute('outputlanguage', $attribs);
			if($lang == ''){
				$docAttr = weTag_getAttribute('doc', $attribs, 'self');
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
			}
			$langcode = substr($lang, 0, 2);
			if($lang == ''){
				$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
				$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
			}
			if(!Zend_Locale::hasCache()){
				Zend_Locale::setCache(getWEZendCache());
			}
			$out = CheckAndConvertISOfrontend(Zend_Locale::getTranslation($GLOBALS['lv']->f($name), 'language', $langcode));
			break;
		case 'shopVat' :

			if(defined('SHOP_TABLE')){

				$normVal = we_document::getFieldByVal($GLOBALS['lv']->f(WE_SHOP_VAT_FIELD_NAME, 'txt'), $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview'); // war '$GLOBALS['lv']->getElement', getElemet gibt es aber nicht in LVs, gefunden bei #4648

				$out = weShopVats::getVatRateForSite($normVal);
			}
			break;
		case 'href' :
			if(isset($GLOBALS['lv']) && ($GLOBALS['lv']->ClassName == 'we_listview_multiobject' || $GLOBALS['lv']->ClassName == 'we_listview_object' || $GLOBALS['lv']->ClassName == 'we_objecttag')){
				$hrefArr = $GLOBALS['lv']->f($name) ? unserialize($GLOBALS['lv']->f($name)) : array();
				if(!is_array($hrefArr))
					$hrefArr = array();
				$out = sizeof($hrefArr) ? we_document::getHrefByArray($hrefArr) : '';
				break;
			}
		default :
			$normVal = '';
			if($name == 'WE_PATH' && $triggerid && isset($GLOBALS['lv']->ClassName) && ($GLOBALS['lv']->ClassName == 'we_search_listview' || $GLOBALS['lv']->ClassName == 'we_listview_object' || $GLOBALS['lv']->ClassName == 'we_listview_multiobject' || $GLOBALS['lv']->ClassName == 'we_objecttag' )){
				$triggerpath = id_to_path($triggerid);
				$triggerpath_parts = pathinfo($triggerpath);
				if(show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $GLOBALS['lv']->hidedirindex && in_array($triggerpath_parts['basename'], explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))){
					$normVal = ($triggerpath_parts['dirname'] != '/' ? $triggerpath_parts['dirname'] : '') . '/' . $GLOBALS['lv']->f('WE_URL');
				} else{
					$normVal = ($triggerpath_parts['dirname'] != '/' ? $triggerpath_parts['dirname'] : '') . '/' . $triggerpath_parts['filename'] . '/' . $GLOBALS['lv']->f('WE_URL');
				}
			} else{
				$testtype = $type;
				if($type == 'select' && $usekey){
					$testtype = 'text';
				}
				$normVal = we_document::getFieldByVal($GLOBALS['lv']->f($name), $testtype, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview'); // war '$GLOBALS['lv']->getElement', getElemet gibt es aber nicht inLV, #4648
				if($name == 'WE_PATH'){
					$path_parts = pathinfo($normVal);
					if(!$GLOBALS['WE_MAIN_DOC']->InWebEdition && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $GLOBALS['lv']->hidedirindex && in_array($path_parts['basename'], explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))){
						$normVal = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
					}
				}
			}
			// bugfix 7557
			// wenn die Abfrage im Aktuellen Objekt kein Erg?bnis liefert
			// wird in den eingebundenen Objekten ?berpr?ft ob das Feld existiert

			if($type == 'select' && $normVal == ''){

				foreach($GLOBALS['lv']->DB_WE->Record as $_glob_key => $_val){

					if(substr($_glob_key, 0, 13) == 'we_we_object_'){
						$testtype = $type;
						if($type == 'select' && $usekey){
							$testtype = 'text';
						}
						$normVal = we_document::getFieldByVal($GLOBALS['lv']->f($name), $testtype, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], substr($_glob_key, 13), 'listview'); // war '$GLOBALS['lv']->getElement', getElemet gibt es aber nicht in LVs, gefunden bei #4648
					}

					if($normVal != ''){
						break;
					}
				}
			}
			// EOF bugfix 7557


			if($name && $name != 'we_href'){
				if($normVal == ''){
					$altVal = we_document::getFieldByVal($GLOBALS['lv']->f($alt), $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview'); // war '$GLOBALS['lv']->getElement', getElemet gibt es aber nicht in LVs, gefunden bei #4648
					if($altVal == '')
						return '';

					if($alt == 'WE_PATH'){
						$path_parts = pathinfo($altVal);
						if(show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $GLOBALS['lv']->hidedirindex && in_array($path_parts['basename'], explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))){
							$altVal = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
						}
					}

					$out = cutText($altVal, $max);
				} else{
					$out = cutText($normVal, $max);
				}
			} else
			if($value){
				$out = $value;
			}
			if($striphtml){
				$out = strip_tags($out);
			}
	}

	if($hyperlink || $name == 'we_href'){

		$_linkAttribs = array('xml' => $xml);
		if($target && !$winprops){ //  save atts in array
			$_linkAttribs['target'] = $target;
		}
		if($class){
			$_linkAttribs['class'] = $class;
		}
		if($style){
			$_linkAttribs['style'] = $style;
		}

		if($winprops){

			if(!$GLOBALS['we_doc']->InWebEdition){ //	we are NOT in webEdition open new window
				$js = '';
				$newWinProps = '';
				$winpropsArray = array();
				$probsPairs = makeArrayFromCSV($winprops);

				foreach($probsPairs as $pair){
					$foo = explode('=', $pair);
					if(isset($foo[0]) && $foo[0]){
						$winpropsArray[$foo[0]] = isset($foo[1]) ? $foo[1] : '';
					}
				}

				if(isset($winpropsArray['left']) && ($winpropsArray['left'] == -1) && isset($winpropsArray['width']) && $winpropsArray['width']){
					$js .= 'if (window.screen) {var screen_width = screen.availWidth;var w = Math.min(screen_width, ' . $winpropsArray['width'] . ');} var x = Math.round((screen_width - w) / 2);';

					$newWinProps .= 'width=\'+w+\',left=\'+x+\',';
					unset($winpropsArray['left']);
					unset($winpropsArray['width']);
				} else{
					if(isset($winpropsArray['left'])){
						$newWinProps .= 'left=' . $winpropsArray['left'] . ',';
						unset($winpropsArray['left']);
					}
					if(isset($winpropsArray['width'])){
						$newWinProps .= 'width=' . $winpropsArray['width'] . ',';
						unset($winpropsArray['width']);
					}
				}
				if(isset($winpropsArray['top']) && ($winpropsArray['top'] == -1) && isset($winpropsArray['height']) && $winpropsArray['height']){
					$js .= 'if (window.screen) {var screen_height = ((screen.height - 50) > screen.availHeight ) ? screen.height - 50 : screen.availHeight;screen_height = screen_height - 40; var h = Math.min(screen_height, ' . $winpropsArray['height'] . ');} var y = Math.round((screen_height - h) / 2);';

					$newWinProps .= 'height=\'+h+\',top=\'+y+\',';
					unset($winpropsArray['top']);
					unset($winpropsArray['height']);
				} else{
					if(isset($winpropsArray['top'])){
						$newWinProps .= 'top=' . $winpropsArray['top'] . ',';
						unset($winpropsArray['top']);
					}
					if(isset($winpropsArray['height'])){
						$newWinProps .= 'height=' . $winpropsArray['height'] . ',';
						unset($winpropsArray['height']);
					}
				}
				foreach($winpropsArray as $k => $v){
					if($k && $v){
						$newWinProps .= $k . '=' . $v . ',';
					}
				}
				$newWinProps = rtrim($newWinProps, ',');

				$_linkAttribs['onclick'] = $js . ';var we_win = window.open(\'\',\'win_' . $name . '\',\'' . $newWinProps . '\');';
				$_linkAttribs['target'] = 'win_' . $name;
			} else{ // we are in webEdition
				if($_SESSION['we_mode'] == 'seem'){ //	we are in seeMode -> open in edit_include ?....
				}
			}
		}

		if($href){
			$_linkAttribs['href'] = $href;
			$out = getHtmlTag('a', $_linkAttribs, $out);
		} else{

			if($id && $isCalendar){
				if(isset($GLOBALS['lv']->calendar_struct['storage']) && count(
						$GLOBALS['lv']->calendar_struct['storage'])){
					$found = false;
					foreach($GLOBALS['lv']->calendar_struct['storage'] as $date){
						if((($GLOBALS['lv']->calendar_struct['calendarCount'] > 0 || ($GLOBALS['lv']->calendar_struct['calendar'] == 'day' && $GLOBALS['lv']->calendar_struct['calendarCount'] >= 0)) && $GLOBALS['lv']->calendar_struct['calendarCount'] <= $GLOBALS['lv']->calendar_struct['numofentries']) && ((int) $date >= (int) $GLOBALS['lv']->calendar_struct['start_date'] && (int) $date <= (int) $GLOBALS['lv']->calendar_struct['end_date'])){
							$found = true;
							break;
						}
					}
					if($found){
						if($GLOBALS['lv']->calendar_struct['calendar'] == 'year')
							$show = 'month';
						else
							$show = 'day';
						$listviewname = weTag_getAttribute('listviewname', $attribs, $lvname);

						$_linkAttribs['href'] = id_to_path($id) . '?' . (isset($GLOBALS['lv']->contentTypes) && $GLOBALS['lv']->contentTypes ? ('we_lv_ct_' . $listviewname . '=' . rawurlencode(
									$GLOBALS['lv']->contentTypes) . '&amp;') : '') . ($GLOBALS['lv']->order ? ('we_lv_order_' . $listviewname . '=' . rawurlencode(
									$GLOBALS['lv']->order) . '&amp;') : '') . ($GLOBALS['lv']->desc ? ('we_lv_desc_' . $listviewname . '=' . rawurlencode(
									$GLOBALS['lv']->desc) . '&amp;') : '') . ($GLOBALS['lv']->cats ? ('we_lv_cats_' . $listviewname . '=' . rawurlencode(
									$GLOBALS['lv']->cats) . '&amp;') : '') . ($GLOBALS['lv']->catOr ? ('we_lv_catOr_' . $listviewname . '=' . rawurlencode(
									$GLOBALS['lv']->catOr) . '&amp;') : '') . ($GLOBALS['lv']->workspaceID ? ('we_lv_ws_' . $listviewname . '=' . rawurlencode(
									$GLOBALS['lv']->workspaceID) . '&amp;') : '') . ((isset(
								$GLOBALS['lv']->searchable) && !$GLOBALS['lv']->searchable) ? ('we_lv_se_' . $listviewname . '=0&amp;') : '') . ('we_lv_calendar_' . $listviewname . '=' . rawurlencode(
								$show) . '&amp;') . ($GLOBALS['lv']->calendar_struct['datefield'] != '' ? ('we_lv_datefield_' . $listviewname . '=' . rawurlencode(
									$GLOBALS['lv']->calendar_struct['datefield']) . '&amp;') : '') . ($GLOBALS['lv']->calendar_struct['date'] >= 0 ? ('we_lv_date_' . $listviewname . '=' . rawurlencode(
									date('Y-m-d', $GLOBALS['lv']->calendar_struct['date']))) : '');

						$out = getHtmlTag('a', $_linkAttribs, $out);
					}
				}
			} else
			if($id && $isImageDoc){
				$_linkAttribs['href'] = id_to_path($id) . '?' . ($GLOBALS['lv']->contentTypes ? ('we_lv_ct_' . $lvname . '=' . rawurlencode(
							$GLOBALS['lv']->contentTypes) . '&amp;') : '') . ($GLOBALS['lv']->order ? ('we_lv_order_' . $lvname . '=' . rawurlencode(
							$GLOBALS['lv']->order) . '&amp;') : '') . ($GLOBALS['lv']->desc ? ('we_lv_desc_' . $lvname . '=' . rawurlencode(
							$GLOBALS['lv']->desc) . '&amp;') : '') . ($GLOBALS['lv']->cats ? ('we_lv_cats_' . $lvname . '=' . rawurlencode(
							$GLOBALS['lv']->cats) . '&amp;') : '') . ($GLOBALS['lv']->catOr ? ('we_lv_catOr_' . $lvname . '=' . rawurlencode(
							$GLOBALS['lv']->catOr) . '&amp;') : '') . ($GLOBALS['lv']->workspaceID ? ('we_lv_ws_' . $lvname . '=' . rawurlencode(
							$GLOBALS['lv']->workspaceID) . '&amp;') : '') . ((!$GLOBALS['lv']->searchable) ? ('we_lv_se_' . $lvname . '=0&amp;') : '') . (isset(
						$GLOBALS['lv']->condition) && $GLOBALS['lv']->condition != '' ? ('we_lv_condition_' . $lvname . '=' . rawurlencode(
							$GLOBALS['lv']->condition) . '&amp;') : '') . 'we_lv_start_' . $lvname . '=' . (($GLOBALS['lv']->count + $GLOBALS['lv']->start) - 1) . '&amp;we_lv_pend_' . $lvname . '=' . ($GLOBALS['lv']->start + $GLOBALS['lv']->anz) . '&amp;we_lv_pstart_' . $lvname . '=' . ($GLOBALS['lv']->start);

				$out = getHtmlTag('a', $_linkAttribs, $out);
			} else{

				if($tid){
					$GLOBALS['lv']->tid = $tid;
				}

				if(isset($GLOBALS['lv']->ClassName) && $GLOBALS['lv']->ClassName == 'we_search_listview' && $GLOBALS['lv']->f('OID')){
					if($tid){
						$tail = '&amp;we_objectTID=' . $tid;
					} else{
						$tail = '';
					}
					$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
					if($GLOBALS['lv']->objectseourls){
						$db = new DB_WE();
						$objecturl = f('SELECT DISTINCT Url FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($GLOBALS['lv']->f('OID')) . ' LIMIT 1', 'Url', $db);
						$objectdaten = getHash('SELECT  Url,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($GLOBALS['lv']->f('OID')) . ' LIMIT 1', $db);
						$objecturl = $objectdaten['Url'];
						$objecttriggerid = $objectdaten['TriggerID'];
						if($objecttriggerid){
							$path_parts = pathinfo(id_to_path($objecttriggerid));
						}
					}
					$pidstr = '';
					if($GLOBALS['lv']->f('WorkspaceID')){
						$pidstr = '?pid=' . intval($GLOBALS['lv']->f('WorkspaceID'));
					}
					$pidstr = '?pid=' . intval($GLOBALS['lv']->f('WorkspaceID'));
					if(show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $GLOBALS['lv']->hidedirindex && in_array($path_parts['basename'], explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))){
						if($GLOBALS['lv']->objectseourls && $objecturl != ''){

							$_linkAttribs['href'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $objecturl . $pidstr;
						} else{
							$_linkAttribs['href'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/?we_objectID=' . $GLOBALS['lv']->f('OID') . str_replace('?', '&amp;', $pidstr);
						}
					} else{
						if($GLOBALS['lv']->objectseourls && $objecturl != ''){
							$_linkAttribs['href'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $path_parts['filename'] . '/' . $objecturl . $pidstr;
						} else{
							$_linkAttribs['href'] = $_SERVER['SCRIPT_NAME'] . '?we_objectID=' . $GLOBALS['lv']->f('OID') . str_replace('?', '&amp;', $pidstr);
						}
					}
					$_linkAttribs['href'] = $_linkAttribs['href'] . $tail;

					if($name == 'we_href'){
						$out = $_linkAttribs['href'];
					} else{
						$out = getHtmlTag('a', $_linkAttribs, $out); //  output of link-tag
					}
				} else
				if(isset($GLOBALS['lv']->ClassName) && $GLOBALS['lv']->ClassName == 'we_catListview' && we_tag('ifHasChildren', array(), '')){
					$parentidname = weTag_getAttribute('parentidname', $attribs, 'we_parentid');
					$_linkAttribs['href'] = $_SERVER['SCRIPT_NAME'] . '?' . $parentidname . '=' . $GLOBALS['lv']->f(
							'ID');

					if($name == 'we_href'){
						$out = $_linkAttribs['href'];
					} else{
						$out = getHtmlTag('a', $_linkAttribs, $out); //  output of link-tag
					}
				} else{

					$showlink = (!isset($GLOBALS['lv']->ClassName) || $GLOBALS['lv']->ClassName == '' || $GLOBALS['lv']->ClassName == 'we_listview') || ($GLOBALS['lv']->ClassName == 'we_search_listview') || ($GLOBALS['lv']->ClassName == 'we_shop_listviewShopVariants') || ($GLOBALS['lv']->ClassName == 'we_listview_shoppingCart') || ($GLOBALS['lv']->ClassName == 'we_objecttag' && $GLOBALS['lv']->triggerID != '0') || ($GLOBALS['lv']->ClassName == 'we_customertag') || ($GLOBALS['lv']->ClassName == 'we_listview_customer') || ($GLOBALS['lv']->ClassName == 'we_listview_object' && $GLOBALS['lv']->triggerID != '0') || ($tid && $GLOBALS['lv']->ClassName == 'we_listview_object') || ($GLOBALS['lv']->ClassName == 'we_listview_object' && ($GLOBALS['lv']->DB_WE->f(
							'OF_Templates') || $GLOBALS['lv']->docID)) || ($GLOBALS['lv']->ClassName == 'we_listview_multiobject' && ($GLOBALS['lv']->DB_WE->f(
							'OF_Templates') || $GLOBALS['lv']->docID));

					if($showlink){

						if($tid && $GLOBALS['lv']->ClassName == 'we_listview_object'){
							$tail = '&amp;we_objectTID=' . $tid;
						} else{
							$tail = '';
						}

						if(($GLOBALS['we_doc']->ClassName == 'we_objectFile') && ($GLOBALS['we_doc']->InWebEdition)){
							$_linkAttribs['href'] = $GLOBALS['lv']->f('wedoc_lastPath') . $tail;
						} else{
							$path_parts = pathinfo($GLOBALS['lv']->f('WE_PATH'));
							if($triggerid){
								$triggerpath = id_to_path($triggerid);
								$triggerpath_parts = pathinfo($triggerpath);
								if(!$GLOBALS['WE_MAIN_DOC']->InWebEdition && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $GLOBALS['lv']->hidedirindex && in_array($triggerpath_parts['basename'], explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))){
									$_linkAttribs['href'] = ($triggerpath_parts['dirname'] != '/' ? $triggerpath_parts['dirname'] : '') . '/' . $GLOBALS['lv']->f('WE_URL') . $tail;
								} else{
									$_linkAttribs['href'] = ($triggerpath_parts['dirname'] != '/' ? $triggerpath_parts['dirname'] : '') . '/' . $triggerpath_parts['filename'] . '/' . $GLOBALS['lv']->f('WE_URL') . $tail;
								}
							} else{
								if(show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $GLOBALS['lv']->hidedirindex && in_array($path_parts['basename'], explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))){
									$_linkAttribs['href'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
								} else{
									$_linkAttribs['href'] = $GLOBALS['lv']->f('WE_PATH') . $tail;
								}
							}
						}

						if($name == 'we_href'){ //  return href for this object
							$out = $_linkAttribs['href'];
						} else{
							$out = getHtmlTag('a', $_linkAttribs, $out);
						}
					}
				}
			}
		}
	}

	if($isImageDoc && isset($_SESSION['we_mode']) && $_SESSION['we_mode'] == 'seem' && $GLOBALS['we_doc']->InWebEdition && $GLOBALS['we_doc']->ContentType != 'text/weTmpl'){
		$out .= '<a href="' . $GLOBALS['lv']->f('WE_ID') . '" seem="edit_image"></a>';
	}

	//	Add a anchor to tell seeMode that this is an object.
	if(isset($_SESSION['we_mode']) && $_SESSION['we_mode'] == 'seem' && (isset($GLOBALS['lv']->ClassName) && $GLOBALS['lv']->ClassName == 'we_listview_object') && isset(
			$GLOBALS['_we_listview_object_flag']) && $GLOBALS['_we_listview_object_flag'] && $GLOBALS['we_doc']->InWebEdition && $GLOBALS['we_doc']->ContentType != 'text/weTmpl' && $GLOBALS['lv']->seeMode && $seeMode){

		$out = '<a href="' . $GLOBALS['lv']->DB_WE->Record['OF_ID'] . '" seem="object"></a>
		<?php $GLOBALS[\'_we_listview_object_flag\'] = false; ?>
		' . $out;
	}
	return $out;
}
