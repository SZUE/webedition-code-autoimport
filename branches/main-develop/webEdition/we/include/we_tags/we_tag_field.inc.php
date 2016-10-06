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
function we_tag_field(array $attribs){
	if(!isset($GLOBALS['lv'])){
		return parseError(g_l('parser', '[field_not_in_lv]'));
	}
	//show field in case of e.g. listdir
	if(isset($GLOBALS['lv']) && $GLOBALS['lv'] instanceof stdClass){
		return $GLOBALS['lv']->field;
	}

	$orgName = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);

	$href = weTag_getAttribute('href', $attribs, '', we_base_request::URL);
	if(isset($attribs['href'])){
		$attribs['href'] = $href;
	}
	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);
	if(isset($attribs['type'])){
		$attribs['type'] = $type;
	}
	$alt = weTag_getAttribute('alt', $attribs, '', we_base_request::STRING);
	if(isset($attribs['alt'])){
		$attribs['alt'] = $alt;
	}

	if(isset($attribs['title'])){
		$attribs['title'] = weTag_getAttribute('title', $attribs, '', we_base_request::STRING);
	}
	if(isset($attribs['value'])){
		$attribs['value'] = weTag_getAttribute('value', $attribs, '', we_base_request::RAW);
	}
	if(isset($attribs['format'])){
		$attribs['format'] = weTag_getAttribute('format', $attribs);
	}
	if(isset($attribs['target'])){
		$attribs['target'] = weTag_getAttribute('target', $attribs);
	}
	$tid = weTag_getAttribute('tid', $attribs);
	if(isset($attribs['tid'])){
		$attribs['tid'] = $tid;
	}
	if(isset($attribs['class'])){
		$attribs['class'] = weTag_getAttribute('class', $attribs);
	}
	$classid = weTag_getAttribute('classid', $attribs);
	if(isset($attribs['classid'])){
		$attribs['classid'] = $classid;
	}

	if(isset($attribs['style'])){
		$attribs['style'] = weTag_getAttribute('style', $attribs);
	}
	$hyperlink = weTag_getAttribute('hyperlink', $attribs, false, we_base_request::BOOL);

	if(isset($attribs['src'])){
		$attribs['src'] = weTag_getAttribute('src', $attribs);
	}
	$winprops = weTag_getAttribute('winprops', $attribs);
	if(isset($attribs['winprops'])){
		$attribs['winprops'] = $winprops;
	}
	$id = weTag_getAttribute('id', $attribs);
	if(isset($attribs['id'])){
		$attribs['id'] = $id;
	}
	$xml = weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, we_base_request::BOOL);
	if(isset($attribs['xml'])){
		$attribs['xml'] = $xml;
	}
	$striphtml = weTag_getAttribute('striphtml', $attribs, false, we_base_request::BOOL);
	$only = weTag_getAttribute('only', $attribs);
	if(isset($attribs['only'])){
		$attribs['only'] = $only;
	}
	$usekey = weTag_getAttribute('usekey', $attribs, false, we_base_request::BOOL);
	if(isset($attribs['usekey'])){
		$attribs['usekey'] = $usekey;
	}
	$triggerid = weTag_getAttribute('triggerid', $attribs);
	if(isset($attribs['triggerid'])){
		$attribs['triggerid'] = $triggerid;
	}
	if(isset($attribs['seeMode'])){
		$attribs['seeMode'] = weTag_getAttribute('seeMode', $attribs, true, we_base_request::BOOL);
	}

	//zusatz typen für verchiedene field-Typen
	if(isset($attribs['thumbnail'])){
		$attribs['thumbnail'] = weTag_getAttribute('thumbnail', $attribs);
	}// Image
	if(isset($attribs['classes'])){
		$attribs['classes'] = weTag_getAttribute('classes', $attribs);
	}//Wwysiwyg
	if(isset($attribs['commands'])){
		$attribs['commands'] = weTag_getAttribute('commands', $attribs);
	}//Wwysiwyg
	if(isset($attribs['fontnames'])){
		$attribs['fontnames'] = weTag_getAttribute('fontnames', $attribs);
	}//Wwysiwyg

	$showpath = weTag_getAttribute('showpath', $attribs);
	if(isset($attribs['showpath'])){
		$attribs['showpath'] = $showpath;
	}
	$rootdir = weTag_getAttribute('rootdir', $attribs, '', we_base_request::FILE);
	if(isset($attribs['rootdir'])){
		$attribs['rootdir'] = $rootdir;
	}
	if(isset($attribs['field'])){
		$attribs['field'] = weTag_getAttribute('field', $attribs);
	}
	if(isset($attribs['customerid'])){
		$attribs['customerid'] = weTag_getAttribute('customerid', $attribs);
	}
	if(isset($attribs['country'])){
		$attribs['country'] = weTag_getAttribute('country', $attribs);
	}
	$out = '';

	$lvname = isset($GLOBALS['lv']->name) ? $GLOBALS['lv']->name : '';
	$alt = ($alt === 'we_path' ? 'WE_PATH' : ($alt === 'we_text' ? 'WE_TEXT' : $alt));
	$name = ($orgName === 'we_path' ? 'WE_PATH' : ($orgName === 'we_text' ? 'WE_TEXT' : $name));

	//listview of documents, document with a block. Try to access by blockname.
	$name = ($GLOBALS['lv']->f($name) ? $name : $orgName);

	unset($attribs['winprops']);

	$classid = ($classid ?:
			(isset($GLOBALS['lv']) ? (
			isset($GLOBALS['lv']->classID) ?
			$GLOBALS['lv']->classID :
			($GLOBALS['lv'] instanceof we_shop_shop ?
			$GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'TABLEID') :
			''
			)
			) : //Fix #9223
			(isset($GLOBALS['we_doc']->OF_ID) ?
			$GLOBALS['we_doc']->TableID :
			0
			)
			)

			);


	$isImageDoc = (isset($GLOBALS['lv']->Record[we_listview_base::PROPPREFIX . 'CONTENTTYPE']) && $GLOBALS['lv']->Record[we_listview_base::PROPPREFIX . 'CONTENTTYPE'] == we_base_ContentTypes::IMAGE);
	$isCalendar = (!empty($GLOBALS['lv']->calendar_struct['calendar']) && $GLOBALS['lv']->isCalendarField($type));

	if((!$GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'ID') && property_exists($GLOBALS['lv'], 'calendar_struct') && $GLOBALS['lv']->calendar_struct['calendar'] === '')){
		return '';
	}


	switch($type){
		case 'binary' :
			$t = $GLOBALS['we_doc']->getFieldByVal($GLOBALS['lv']->f($name), $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview');
			switch($only){
				case '':
				case 'name':
					$out = $t[0];
					break;
				case 'path':
					$out = $t[1];
					break;
				case 'parentpath':
					$out = $t[2];
					break;
				case 'filename':
					$out = $t[3];
					break;
				case 'extension':
					$out = $t[4];
					break;
				case 'filesize':
					$out = $t[5];
					break;
			}
			$href = ($href ?: $t[1]);
			break;
		case 'link' :
			if(is_object($GLOBALS['lv'])){
				$out = $GLOBALS['we_doc']->getFieldByVal($GLOBALS['lv']->f($name), 'link', $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview');
				$href = ($href ?: $out);
				break;
			}
		case 'img' :
			if(!empty($attribs['src'])){
				$imgAtts = ['alt' => '', //  alt must be set
					'src' => $attribs['src'],
					'xml' => $xml,
				];

				$imgAtts = array_merge($imgAtts, useAttribs($attribs, ['alt', 'width', 'height', 'border', 'hspace', 'align', 'vspace'])); //  use some atts form attribs array
				$imgAtts = removeEmptyAttribs($imgAtts, ['alt']);

				$out = getHtmlTag('img', $imgAtts);
				if(!$out){
					//we have no image, so we don't generate an link
					return '';
				}
				break;
			}
		//intentionally no break
		case 'int' :
		case 'date' :
		case 'float' :
		case 'checkbox' :
		case 'collection':
			$idd = ($isImageDoc && $type === 'img' ) ? $GLOBALS['lv']->Record[we_listview_base::PROPPREFIX . 'ID'] : $GLOBALS['lv']->f($name);
			$out = ($idd == 0 ? '' : $GLOBALS['we_doc']->getFieldByVal($idd, $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview'));
			if($type === 'img' && empty($out)){
				return '';
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
			$out = we_listview_base::getCalendarField($GLOBALS['lv']->calendar_struct['calendar'], $type, getFieldOutLang($attribs));
			break;

		case 'multiobject':
			$temp = we_unserialize($GLOBALS['lv']->f($name));
			$out = is_array($temp) ? (!empty($temp['objects']) ? implode(',', $temp['objects']) : implode(',', $temp)) : '';
			break;
		case 'country' :
			$langcode = getFieldOutLang($attribs);
			$out = (WE_COUNTRIES_DEFAULT != '' && $GLOBALS['lv']->f($name) === '--' ?
					WE_COUNTRIES_DEFAULT :
					CheckAndConvertISOfrontend(we_base_country::getTranslation($GLOBALS['lv']->f($name), we_base_country::TERRITORY, $langcode))
					);
			break;
		case 'language' :
			$langcode = getFieldOutLang($attribs);
			$out = CheckAndConvertISOfrontend(we_base_country::getTranslation($GLOBALS['lv']->f($name), we_base_country::LANGUAGE, $langcode));
			break;
		case 'shopVat' :
			if(defined('SHOP_ORDER_TABLE')){
				if(!we_shop_category::isCategoryMode()){
					$normVal = $GLOBALS['we_doc']->getFieldByVal($GLOBALS['lv']->f(WE_SHOP_VAT_FIELD_NAME, 'txt'), $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview'); // war '$GLOBALS['lv']->getElement', getElemet gibt es aber nicht in LVs, gefunden bei #4648
					$out = we_shop_vats::getVatRateForSite($normVal);
				} else {
					$shopVatAttribs = $attribs;
					$shopVatAttribs['shopcategoryid'] = $GLOBALS['lv']->f(WE_SHOP_CATEGORY_FIELD_NAME);
					$shopVatAttribs['wedoccategories'] = $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'CATEGORY');
					unset($shopVatAttribs['type']);

					$out = we_tag('shopVat', $shopVatAttribs);
				}
			}
			break;
		case 'shopCategory' :
			if(defined('SHOP_ORDER_TABLE') && is_object($GLOBALS['lv'])){
				$id = $GLOBALS['lv']->f(WE_SHOP_CATEGORY_FIELD_NAME);
				$wedocCategory = $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'CATEGORY');

				$out = we_shop_category::getShopCatFieldByID($id, $wedocCategory, weTag_getAttribute('field', $attribs), $showpath, $rootdir, true);
			}
			break;
		case 'href' ://#6329: fixed for lv type=document. check later for other types! #6421: field type=href in we:block
			if(isset($GLOBALS['lv'])){
				switch(get_class($GLOBALS['lv'])){
					case 'we_listview_document':
						$hrefArr = [
							'int' => $GLOBALS['lv']->f($name . we_base_link::MAGIC_INT_LINK) ?: $GLOBALS['lv']->f(we_tag_getPostName($name) . we_base_link::MAGIC_INT_LINK),
							'intID' => $GLOBALS['lv']->f($name . we_base_link::MAGIC_INT_LINK_ID) ?: $GLOBALS['lv']->f(we_tag_getPostName($name) . we_base_link::MAGIC_INT_LINK_ID),
							'extPath' => $GLOBALS['lv']->f($name)
						];
						break;
					case 'we_listview_multiobject':
					case 'we_listview_object':
					case 'we_shop_shop':
					case 'we_listview_shopOrderitem': //Fix #7816
						$hrefArr = we_unserialize($GLOBALS['lv']->f($name));
						if(!is_array($hrefArr)){
							$hrefArr = [];
						}
						break;
				}
				$out = $hrefArr ? we_document::getHrefByArray($hrefArr) : '';
				$path_parts = pathinfo($out);
				if(!empty($GLOBALS['lv']->hidedirindex) && seoIndexHide($path_parts['basename'])){
					$out = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
				}

				break;
			}
		default : // FIXME: treat type="select" as separate case, and clean up the mess with all this little fixes

			if($orgName === 'WE_PATH' && $triggerid && in_array(get_class($GLOBALS['lv']), ['we_listview_search', 'we_listview_object', 'we_listview_multiobject'])){
				$triggerpath = id_to_path($triggerid);
				$triggerpath_parts = pathinfo($triggerpath);
				$normVal = ($triggerpath_parts['dirname'] != '/' ? $triggerpath_parts['dirname'] : '') . '/' .
						(!empty($GLOBALS['lv']->hidedirindex) && seoIndexHide($triggerpath_parts['basename']) ?
						'' : $triggerpath_parts['filename'] . '/' ) .
						$GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'URL');
			} else {
				$testtype = ($type === 'select' && $usekey) ? 'text' : $type;
				switch(get_class($GLOBALS['lv'])){
					case 'we_shop_shop':
					case 'we_listview_object':
						if($type === 'select'){// bugfix #6399
							$attribs['name'] = $attribs['_name_orig'];
						}
					default:
						$normVal = $GLOBALS['we_doc']->getFieldByVal($GLOBALS['lv']->f($name), $testtype, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview');
						if($orgName === 'WE_PATH'){
							$path_parts = pathinfo($normVal);
							if(!$GLOBALS['WE_MAIN_DOC']->InWebEdition && !empty($GLOBALS['lv']->hidedirindex) && seoIndexHide($path_parts['basename'])){
								$normVal = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
							}
						}
				}
			}
			// bugfix 7557
			// wenn die Abfrage im Aktuellen Objekt kein Ergebnis liefert
			// wird in den eingebundenen Objekten ueberprueft ob das Feld existiert

			if($type === 'select' && $normVal === ''){
				//FIXME: remove getDBRecord
				$dbRecord = array_keys($GLOBALS['lv']->getDBRecord()); // bugfix #6399
				foreach($dbRecord as $glob_key){
					if(substr($glob_key, 0, 7) === 'object_'){
						$normVal = $GLOBALS['we_doc']->getFieldByVal($GLOBALS['lv']->f($name), ($usekey ? 'text' : 'select'), $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], substr($glob_key, 7), 'listview'); // war '$GLOBALS['lv']->getElement', getElemet gibt es aber nicht in LVs, gefunden bei #4648
					}

					if($normVal != ''){
						break;
					}
				}
			}
			// EOF bugfix 7557


			if($name && $name != 'we_href'){
				if($normVal === ''){
					$altVal = $GLOBALS['we_doc']->getFieldByVal($GLOBALS['lv']->f($alt), $type, $attribs, false, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $classid, 'listview');
					if($altVal === ''){
						return '';
					}

					if($alt === 'WE_PATH'){
						$path_parts = pathinfo($altVal);
						if(!empty($GLOBALS['lv']->hidedirindex) && seoIndexHide($path_parts['basename'])){
							$altVal = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
						}
					}
					$normVal = $altVal;
				}
				$max = weTag_getAttribute('max', $attribs, 0, we_base_request::INT);
				$out = cutText(($striphtml ? strip_tags($normVal) : $normVal), $max, $striphtml);
			} elseif(($value = weTag_getAttribute('value', $attribs, '', we_base_request::RAW))){
				$out = ($striphtml ? strip_tags($value) : $value);
			} else if($striphtml){
				$out = strip_tags($out);
			}
	}

	if($hyperlink || $name === 'we_href'){
		$linkAttribs = ['xml' => $xml];
		$target = weTag_getAttribute('target', $attribs);
		if($target && !$winprops){ //  save atts in array
			$linkAttribs['target'] = $target;
		}
		if(isset($attribs['class'])){
			$linkAttribs['class'] = $attribs['class'];
		}
		if(isset($attribs['style'])){
			$linkAttribs['style'] = $attribs['style'];
		}
		foreach($attribs as $key => $val){
			if(strpos($key, 'pass_') === 0){
				$linkAttribs[$key] = $val;
			}
		}


		if($winprops){

			if(!$GLOBALS['we_doc']->InWebEdition){ //	we are NOT in webEdition open new window
				$js = '';
				$newWinProps = $winpropsArray = [];
				$probsPairs = makeArrayFromCSV($winprops);

				foreach($probsPairs as $pair){
					$foo = explode('=', $pair);
					if(!empty($foo[0])){
						$winpropsArray[$foo[0]] = isset($foo[1]) ? $foo[1] : '';
					}
				}

				if(isset($winpropsArray['left']) && ($winpropsArray['left'] == -1) && !empty($winpropsArray['width'])){
					$js .= 'if (window.screen) {var screen_width = screen.availWidth;var w = Math.min(screen_width, ' . $winpropsArray['width'] . ');} var x = Math.round((screen_width - w) / 2);';
					$newWinProps [] = 'width=\'+w+\',left=\'+x+\'';
				} else {
					if(isset($winpropsArray['left'])){
						$newWinProps [] = 'left=' . $winpropsArray['left'];
					}
					if(isset($winpropsArray['width'])){
						$newWinProps [] = 'width=' . $winpropsArray['width'];
					}
				}
				if(isset($winpropsArray['top']) && ($winpropsArray['top'] == -1) && !empty($winpropsArray['height'])){
					$js .= 'if (window.screen) {var screen_height = ((screen.height - 50) > screen.availHeight ) ? screen.height - 50 : screen.availHeight;screen_height = screen_height - 40; var h = Math.min(screen_height, ' . $winpropsArray['height'] . ');} var y = Math.round((screen_height - h) / 2);';
					$newWinProps [] = 'height=\'+h+\',top=\'+y+\'';
				} else {
					if(isset($winpropsArray['top'])){
						$newWinProps [] = 'top=' . $winpropsArray['top'];
					}
					if(isset($winpropsArray['height'])){
						$newWinProps [] = 'height=' . $winpropsArray['height'];
					}
				}
				foreach($winpropsArray as $k => $v){
					switch($k){
						case '':
						case 'top':
						case 'left':
						case 'width':
						case 'height':
							continue;
						default:
							if($v){
								$newWinProps [] = $k . '=' . $v;
							}
					}
				}

				$linkAttribs['onclick'] = $js . ';var we_win = window.open(\'\',\'win_' . $name . '\',\'' . implode(',', $newWinProps) . '\');';
				$linkAttribs['target'] = 'win_' . $name;
			} else { // we are in webEdition
				if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){ //	we are in seeMode -> open in edit_include ?....
				}
			}
		}

		if($href){
			$linkAttribs['href'] = $href;
			return getHtmlTag('a', $linkAttribs, $out, true);
		}

		if($id && $isCalendar){
			if(isset($GLOBALS['lv']->calendar_struct['storage']) && !empty($GLOBALS['lv']->calendar_struct['storage'])){

				foreach($GLOBALS['lv']->calendar_struct['storage'] as $date){
					if((($GLOBALS['lv']->calendar_struct['calendarCount'] > 0 || ($GLOBALS['lv']->calendar_struct['calendar'] === 'day' && $GLOBALS['lv']->calendar_struct['calendarCount'] >= 0)) && $GLOBALS['lv']->calendar_struct['calendarCount'] <= $GLOBALS['lv']->calendar_struct['numofentries']) && ((int) $date >= (int) $GLOBALS['lv']->calendar_struct['start_date'] && (int) $date <= (int) $GLOBALS['lv']->calendar_struct['end_date'])){
						$show = ($GLOBALS['lv']->calendar_struct['calendar'] === 'year' ? 'month' : 'day');
						$listviewname = weTag_getAttribute('listviewname', $attribs, $lvname, we_base_request::STRING);

						$linkAttribs['href'] = id_to_path($id) . '?' .
								$GLOBALS['lv']->getListviewRequest($listviewname) .
								('we_lv_calendar_' . $listviewname . '=' . rawurlencode($show) . '&amp;') .
								($GLOBALS['lv']->calendar_struct['datefield'] ? ('we_lv_datefield_' . $listviewname . '=' . rawurlencode($GLOBALS['lv']->calendar_struct['datefield']) . '&amp;') : '') .
								($GLOBALS['lv']->calendar_struct['date'] >= 0 ? ('we_lv_date_' . $listviewname . '=' . rawurlencode(date('Y-m-d', $GLOBALS['lv']->calendar_struct['date']))) : '');

						return getHtmlTag('a', $linkAttribs, $out, true);
					}
				}
			}
		} elseif($id && $isImageDoc){
			$linkAttribs['href'] = id_to_path($id) . '?' .
					$GLOBALS['lv']->getListviewRequest($lvname) .
					'we_lv_start_' . $lvname . '=' . (($GLOBALS['lv']->count + $GLOBALS['lv']->start) - 1) .
					'&amp;we_lv_pend_' . $lvname . '=' . ($GLOBALS['lv']->start + $GLOBALS['lv']->anz) .
					'&amp;we_lv_pstart_' . $lvname . '=' . ($GLOBALS['lv']->start);

			return getHtmlTag('a', $linkAttribs, $out, true);
		}

		if($tid){
			$GLOBALS['lv']->tid = $tid;
		}

		if(($GLOBALS['lv'] instanceof we_listview_search) && $GLOBALS['lv']->f('ClassID')){
			$tail = ($tid ? '&amp;we_objectTID=' . $tid : '');

			$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
			$isSeo = $GLOBALS['lv']->objectseourls;
			if($isSeo){
				$h = getHash('SELECT  Url,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($GLOBALS['lv']->f('OID')));
				$objecttriggerid = $h['TriggerID'];
				$objecturl = $h['Url'] . '?';
				if($objecttriggerid){
					$path_parts = pathinfo(id_to_path($objecttriggerid));
				}
			} else {
				$objecturl = '?we_objectID=' . $GLOBALS['lv']->f('OID') . '&';
			}

			$pidstr = '?pid=' . intval($GLOBALS['lv']->f('WorkspaceID'));
			$linkAttribs['href'] = (!empty($GLOBALS['lv']->hidedirindex) && seoIndexHide($path_parts['basename']) ?
					($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' .
					$objecturl .
					$pidstr :
					($isSeo && $objecturl ?
					($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $path_parts['filename'] . '/' . $objecturl :
					$_SERVER['SCRIPT_NAME'] . $objecturl
					) . $pidstr);

			$linkAttribs['href'] .= $tail;

			return ($name === 'we_href' ?
					$linkAttribs['href'] :
					getHtmlTag('a', $linkAttribs, $out, true) //  output of link-tag
					);
		}
		if(($GLOBALS['lv'] instanceof we_listview_category) && we_tag('ifHasChildren')){
			$parentidname = weTag_getAttribute('parentidname', $attribs, 'we_parentid', we_base_request::STRING);
			$linkAttribs['href'] = $_SERVER['SCRIPT_NAME'] . '?' . $parentidname . '=' . $GLOBALS['lv']->f('ID');

			return ($name === 'we_href' ?
					$linkAttribs['href'] :
					getHtmlTag('a', $linkAttribs, $out, true) //  output of link-tag
					);
		}
		$showlink = false;
		switch(get_class($GLOBALS['lv'])){
			case 'we_listview_document':
				$triggerid = $triggerid ?: $GLOBALS['lv']->triggerID;
				$tailOwnId = '?we_documentID=' . $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'ID');
			case '':
			case 'we_listview_search':
			case 'we_listview_variants':
			case 'we_shop_shop':
			case 'we_customertag':
			case 'we_listview_customer':
				$showlink = true;
				break;
			case 'we_listview_object':
				$triggerid = $triggerid ?: $GLOBALS['lv']->triggerID;
				$showlink = $tid || $triggerid || $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'TEMPLATES') || $GLOBALS['lv']->docID;
				$tailOwnId = '?we_objectID=' . $GLOBALS['lv']->f('OF_ID');
				break;
			case 'we_listview_multiobject':
				$showlink = $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'TEMPLATES') || $GLOBALS['lv']->docID;
				break;
			case 'we_listview_shopOrder': //listview type="order"
				$showlink = !empty($triggerid);
				break;
			default:
				$showlink = false;
				break;
		}

		if(!$showlink){
			return $out;
		}

		$tail = ($tid && ($GLOBALS['lv'] instanceof we_listview_object) ? '&amp;we_objectTID=' . $tid : '');

		if(isset($GLOBALS['we_doc']->OF_ID) && ($GLOBALS['we_doc']->InWebEdition)){
			$linkAttribs['href'] = $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'LASTPATH') . $tail;
		} else {
			$path_parts = pathinfo($GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'PATH'));
			if($triggerid){
				$triggerpath = id_to_path($triggerid);
				$triggerpath_parts = pathinfo($triggerpath);

				$linkAttribs['href'] = (!empty($GLOBALS['lv']->objectseourls)) ? // objectseourls=true
						rtrim($triggerpath_parts['dirname'], '/') . '/' .
						((!$GLOBALS['WE_MAIN_DOC']->InWebEdition && !empty($GLOBALS['lv']->hidedirindex) && seoIndexHide($triggerpath_parts['basename'])) ? //hidedirindex of triggerID
						$GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'URL') . $tail : //Fix #8708 do not hidedirindex of triggerID
						$triggerpath_parts['filename'] . '/' . $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'URL') . $tail
						) : //objectseourls=false or not set
						$triggerpath . $tailOwnId . $tail;

				/* End Fix '7771 */
			} else {
				$linkAttribs['href'] = (!empty($GLOBALS['lv']->hidedirindex) && seoIndexHide($path_parts['basename']) ?
						($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' :
						$GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'PATH') . $tail
						);
			}
		}

		return ($name === 'we_href' ? //  return href for this object
				$linkAttribs['href'] :
				$out = getHtmlTag('a', $linkAttribs, $out, true));
	}

	return $out;
}

function getFieldOutLang(array $attribs){
	$lang = weTag_getAttribute('outputlanguage', $attribs, '', we_base_request::STRING);
	if(!$lang){
		$doc = we_getDocForTag(weTag_getAttribute('doc', $attribs, 'self', '', we_base_request::STRING));
		$lang = $doc->Language;
	}
	$langcode = substr($lang, 0, 2);
	if(!$lang){
		$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
		$langcode = array_search($lang[0], getWELangs());
	}
	return $langcode;
}
