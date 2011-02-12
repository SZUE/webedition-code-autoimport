<?php
/**
 * webEdition CMS
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
	$name = we_getTagAttribute("name", $attribs);

	// quickfix 4192
	if (isset($GLOBALS["lv"]->BlockInside) && !$GLOBALS["lv"]->BlockInside  ){ // if due to bug 4635
		$nameA = explode("blk_",$name);
		$name = $nameA[0];
	}
	// quickfix 4192
	$href = we_getTagAttribute("href", $attribs);
	$type = we_getTagAttribute("type", $attribs);
	$alt = we_getTagAttribute("alt", $attribs);
	$value = we_getTagAttribute("value", $attribs);
	$max = we_getTagAttribute("max", $attribs);
	$format = we_getTagAttribute("format", $attribs);
	$target = we_getTagAttribute("target", $attribs);
	$tid = we_getTagAttribute("tid", $attribs);
	$class = we_getTagAttribute("class", $attribs);
	$classid = we_getTagAttribute("classid", $attribs);
	$style = we_getTagAttribute("style", $attribs);
	$hyperlink = we_getTagAttribute("hyperlink", $attribs, "", true);
	$src = we_getTagAttribute("src", $attribs);
	$winprops = we_getTagAttribute("winprops", $attribs);
	$id = we_getTagAttribute("id", $attribs);
	$xml = we_getTagAttribute("xml", $attribs, "");
	$striphtml = we_getTagAttribute("striphtml", $attribs, false, true);
	$only = we_getTagAttribute("only", $attribs);
	$triggerid = we_getTagAttribute("triggerid", $attribs);
	$nameTo = we_getTagAttribute("nameto", $attribs);
	$to = we_getTagAttribute("to", $attribs,'screen');

	$out = "";

	$seeMode = we_getTagAttribute("seeMode", $attribs, true, true, true);

	if (!isset($GLOBALS["lv"])) {
		return parseError($GLOBALS["l_parser"]["field_not_in_lv"]);
	}
	$lvname = isset($GLOBALS["lv"]->name) ? $GLOBALS["lv"]->name : "";

	if ($alt == "we_path")
		$alt = "WE_PATH";
	if ($alt == "we_text")
		$alt = "WE_TEXT";
	if ($name == "we_path")
		$name = "WE_PATH";
	if ($name == "we_text")
		$name = "WE_TEXT";

	if (isset($attribs["winprops"])) {
		unset($attribs["winprops"]);
	}

	$classid = $classid ? $classid : (isset($GLOBALS["lv"]) ? (isset($GLOBALS["lv"]->object->classID) ? $GLOBALS["lv"]->object->classID : (isset(
			$GLOBALS["lv"]->classID) ? $GLOBALS["lv"]->classID : "")) : (isset($GLOBALS["we_doc"]->TableID) ? $GLOBALS["we_doc"]->TableID : 0));
	$isImageDoc = false;
	if (isset($GLOBALS["lv"]->Record["wedoc_ContentType"]) && $GLOBALS["lv"]->Record["wedoc_ContentType"] == "image/*") {
		$isImageDoc = true;
	}

	$isCalendar = false;
	if (isset($GLOBALS["lv"]->calendar_struct["calendar"]) && $GLOBALS["lv"]->calendar_struct["calendar"] != "" && $GLOBALS["lv"]->isCalendarField(
			$type)) {
		$isCalendar = true;
	}

	if (!$GLOBALS["lv"]->f("WE_ID") && $GLOBALS["lv"]->calendar_struct["calendar"] == "") {
		return we_redirect_tagoutput("",$nameTo,$to);
	}

	switch ($type) {

		case "binary" :
			$t = we_document::getFieldByVal(
					$GLOBALS["lv"]->f($name),
					$type,
					$attribs,
					false,
					$GLOBALS["we_doc"]->ParentID,
					$GLOBALS["we_doc"]->Path,
					$GLOBALS["DB_WE"],
					$classid,
					'$GLOBALS["lv"]->f');
			if ($only ==''||$only =='name') {$out = $t[0];}
			if ($only =='path') {$out = $t[1];}
			if ($only =='parentpath') {$out = $t[2];}
			if ($only =='filename') {$out = $t[3];}
			if ($only =='extension') {$out = $t[4];}
			if ($only =='filesize') {$out = $t[5];}
			$href = (empty($href) ? $t[1] : $href);
			break;
		case "link" :
			if ($GLOBALS["lv"]->ClassName) {
				$out = we_document::getFieldByVal(
						$GLOBALS["lv"]->f($name),
						"link",
						$attribs,
						false,
						$GLOBALS["we_doc"]->ParentID,
						$GLOBALS["we_doc"]->Path,
						$GLOBALS["DB_WE"],
						$classid,
						'$GLOBALS["lv"]->f');
				$href = (empty($href) ? $out : $href);
				break;
			}


		case "date" :
		case "img" :
		case "int" :
		case "float" :
		case "checkbox" :

			if ($src && $type == "img") {

				$_imgAtts['alt'] = ''; //  alt must be set
				$_imgAtts['src'] = $src; //  src
				$_imgAtts['xml'] = $xml; //  xml

				$_imgAtts = array_merge(
						$_imgAtts,
						useAttribs(
								$attribs,
								array(
									'alt', 'width', 'height', 'border', 'hspace', 'align', 'vspace'
								))); //  use some atts form attribs array
				$_imgAtts = removeEmptyAttribs($_imgAtts, array(
					'alt'
				));

				$out = getHtmlTag('img', $_imgAtts);

			} else {
				$idd = ($isImageDoc && $type == "img" ) ? $GLOBALS["lv"]->Record["wedoc_ID"] : $GLOBALS["lv"]->f($name);
				if ($idd ==0) {
					$out = '';
				} else {
					$out = we_document::getFieldByVal(
						$idd,
						$type,
						$attribs,
						false,
						$GLOBALS["we_doc"]->ParentID,
						$GLOBALS["we_doc"]->Path,
						$GLOBALS["DB_WE"],
						$classid,
						'$GLOBALS["lv"]->f');
				}
			}
			break;
		case "day" :
		case "dayname" :
		case "dayname_long" :
		case "dayname_short" :
		case "month" :
		case "monthname" :
		case "monthname_long" :
		case "monthname_short" :
		case "year" :
		case "hour" :
		case "week" :
			$out = listviewBase::getCalendarField($GLOBALS["lv"]->calendar_struct["calendar"], $type);
			break;

		case "multiobject" :
			$temp = unserialize($GLOBALS["lv"]->DB_WE->Record['we_' . $name]);
			if (isset($temp['objects']) && sizeof($temp['objects']) > 0) {
				$out = implode(",", $temp['objects']);
			} else {
				$out = "";
			}
			break;
		case 'country' :
			$lang = we_getTagAttribute("outputlanguage", $attribs, "");
			if ($lang==''){
				$docAttr = we_getTagAttribute("doc", $attribs, "self");
				$doc = we_getDocForTag($docAttr);
				$lang=$doc->Language;
			}
			$langcode= substr($lang,0,2);
			if ($lang==''){
				$lang = explode('_',$GLOBALS["WE_LANGUAGE"]);
				$langcode = array_search ($lang[0],$GLOBALS['WE_LANGS']);
			}
			$out = CheckAndConvertISOfrontend(Zend_Locale::getTranslation($GLOBALS["lv"]->f($name),'territory',$langcode));
		break;
		case 'language' :
			$lang = we_getTagAttribute("outputlanguage", $attribs, "");
			if ($lang==''){
				$docAttr = we_getTagAttribute("doc", $attribs, "self");
				$doc = we_getDocForTag($docAttr);
				$lang=$doc->Language;
			}
			$langcode= substr($lang,0,2);
			if ($lang==''){
				$lang = explode('_',$GLOBALS["WE_LANGUAGE"]);
				$langcode = array_search ($lang[0],$GLOBALS['WE_LANGS']);
			}
			$out = CheckAndConvertISOfrontend(Zend_Locale::getTranslation($GLOBALS["lv"]->f($name),'language',$langcode));
		break;
		case 'shopVat' :

			if (defined('SHOP_TABLE')) {

				$normVal = we_document::getFieldByVal(
						$GLOBALS["lv"]->f(WE_SHOP_VAT_FIELD_NAME, 'txt'),
						$type,
						$attribs,
						false,
						$GLOBALS["we_doc"]->ParentID,
						$GLOBALS["we_doc"]->Path,
						$GLOBALS["DB_WE"],
						$classid,
						'$GLOBALS["lv"]->f'); // war '$GLOBALS["lv"]->getElement', getElemet gibt es aber nicht in LVs, gefunden bei #4648

				require_once (WE_SHOP_MODULE_DIR . 'weShopVats.class.php');
				$out = weShopVats::getVatRateForSite($normVal);
			}
			break;
		case "href" :
			if (isset($GLOBALS["lv"]) && ($GLOBALS["lv"]->ClassName == "we_listview_multiobject" || $GLOBALS["lv"]->ClassName == "we_listview_object" || $GLOBALS["lv"]->ClassName == "we_objecttag")) {
				$hrefArr = $GLOBALS["lv"]->f($name) ? unserialize($GLOBALS["lv"]->f($name)) : array();
				if (!is_array($hrefArr))
					$hrefArr = array();
				$out = sizeof($hrefArr) ? we_document::getHrefByArray($hrefArr) : "";
				break;
			}
		default :
			if($name=='WE_PATH' && $triggerid && isset($GLOBALS["lv"]->ClassName) && ($GLOBALS["lv"]->ClassName == "we_search_listview" || $GLOBALS["lv"]->ClassName == "we_listview_object" || $GLOBALS["lv"]->ClassName == "we_listview_multiobject" || $GLOBALS["lv"]->ClassName == "we_objecttag"  ) ){
				$triggerpath = id_to_path($triggerid); 
				$triggerpath_parts = pathinfo($triggerpath); 
				if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $GLOBALS["lv"]->hidedirindex && in_array($triggerpath_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
					$normVal = ($triggerpath_parts['dirname']!=DIRECTORY_SEPARATOR ? $triggerpath_parts['dirname']:'').DIRECTORY_SEPARATOR. $GLOBALS["lv"]->f("WE_URL");
				} else {
					$normVal = ($triggerpath_parts['dirname']!=DIRECTORY_SEPARATOR ? $triggerpath_parts['dirname']:'').DIRECTORY_SEPARATOR . $triggerpath_parts['filename'] . DIRECTORY_SEPARATOR . $GLOBALS["lv"]->f("WE_URL");
				}
				
			} else {
				$normVal = we_document::getFieldByVal(
					$GLOBALS["lv"]->f($name),
					$type,
					$attribs,
					false,
					$GLOBALS["we_doc"]->ParentID,
					$GLOBALS["we_doc"]->Path,
					$GLOBALS["DB_WE"],
					$classid,
					'$GLOBALS["lv"]->f'); // war '$GLOBALS["lv"]->getElement', getElemet gibt es aber nicht inLV, #4648
				if ($name=='WE_PATH'){	
					$path_parts = pathinfo($normVal); 
					if (!$GLOBALS['WE_MAIN_DOC']->InWebEdition && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $GLOBALS["lv"]->hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
						$normVal = ($path_parts['dirname']!=DIRECTORY_SEPARATOR ? $path_parts['dirname']:'').DIRECTORY_SEPARATOR;
					} 
				}	
			}
			// bugfix 7557
			// wenn die Abfrage im Aktuellen Objekt kein Erg?bnis liefert
			// wird in den eingebundenen Objekten ?berpr?ft ob das Feld existiert

			if ($type == "select" && $normVal == "") {

				foreach ($GLOBALS["lv"]->DB_WE->Record as $_glob_key => $_val) {

					if (substr($_glob_key, 0, 13) == "we_we_object_") {

						$normVal = we_document::getFieldByVal(
								$GLOBALS["lv"]->f($name),
								$type,
								$attribs,
								false,
								$GLOBALS["we_doc"]->ParentID,
								$GLOBALS["we_doc"]->Path,
								$GLOBALS["DB_WE"],
								substr($_glob_key, 13),
								'$GLOBALS["lv"]->f');// war '$GLOBALS["lv"]->getElement', getElemet gibt es aber nicht in LVs, gefunden bei #4648
					}

					if ($normVal != "")
						break;
				}
			}
			// EOF bugfix 7557


			if ($name && $name != 'we_href') {
				if ($normVal == "") {
					$altVal = we_document::getFieldByVal(
							$GLOBALS["lv"]->f($alt),
							$type,
							$attribs,
							false,
							$GLOBALS["we_doc"]->ParentID,
							$GLOBALS["we_doc"]->Path,
							$GLOBALS["DB_WE"],
							$classid,
							'$GLOBALS["lv"]->f');// war '$GLOBALS["lv"]->getElement', getElemet gibt es aber nicht in LVs, gefunden bei #4648
					if ($altVal == "")
						return "";
					
					if ($alt=='WE_PATH'){	
						$path_parts = pathinfo($altVal); 
						if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $GLOBALS["lv"]->hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
							$altVal = ($path_parts['dirname']!=DIRECTORY_SEPARATOR ? $path_parts['dirname']:'').DIRECTORY_SEPARATOR;
						} 
					}	
					
					$out = cutText($altVal, $max);
				} else {
					$out = cutText($normVal, $max);
				}

			} else
				if ($value) {
					$out = $value;
				}
			if ($striphtml) {
				$out = strip_tags($out);
			}
	}

	if ($hyperlink || $name == 'we_href') {

		$_linkAttribs['xml'] = $xml;
		if ($target && !$winprops) { //  save atts in array
			$_linkAttribs['target'] = $target;
		}
		if ($class) {
			$_linkAttribs['class'] = $class;
		}
		if ($style) {
			$_linkAttribs['style'] = $style;
		}

		if ($winprops) {

			if (!$GLOBALS['we_doc']->InWebEdition) { //	we are NOT in webEdition open new window


				$js = "";
				$newWinProps = "";
				$winpropsArray = array();
				$probsPairs = makeArrayFromCSV($winprops);

				foreach ($probsPairs as $pair) {
					$foo = explode("=", $pair);
					if (isset($foo[0]) && $foo[0]) {
						$winpropsArray[$foo[0]] = isset($foo[1]) ? $foo[1] : "";
					}
				}

				if (isset($winpropsArray["left"]) && ($winpropsArray["left"] == -1) && isset($winpropsArray["width"]) && $winpropsArray["width"]) {
					$js .= 'if (window.screen) {' . 'var screen_width = screen.availWidth;' . 'var w = Math.min(screen_width, ' . $winpropsArray["width"] . ');' . '}' . 'var x = Math.round((screen_width - w) / 2);';

					$newWinProps .= "width='+w+',left='+x+',";
					unset($winpropsArray["left"]);
					unset($winpropsArray["width"]);
				} else {
					if (isset($winpropsArray["left"])) {
						$newWinProps .= 'left=' . $winpropsArray["left"] . ',';
						unset($winpropsArray["left"]);
					}
					if (isset($winpropsArray["width"])) {
						$newWinProps .= 'width=' . $winpropsArray["width"] . ',';
						unset($winpropsArray["width"]);
					}
				}
				if (isset($winpropsArray["top"]) && ($winpropsArray["top"] == -1) && isset($winpropsArray["height"]) && $winpropsArray["height"]) {
					$js .= 'if (window.screen) {' . 'var screen_height = ((screen.height - 50) > screen.availHeight ) ? screen.height - 50 : screen.availHeight;screen_height = screen_height - 40;' . 'var h = Math.min(screen_height, ' . $winpropsArray["height"] . ');' . '}' . 'var y = Math.round((screen_height - h) / 2);';

					$newWinProps .= "height='+h+',top='+y+',";
					unset($winpropsArray["top"]);
					unset($winpropsArray["height"]);
				} else {
					if (isset($winpropsArray["top"])) {
						$newWinProps .= 'top=' . $winpropsArray["top"] . ',';
						unset($winpropsArray["top"]);
					}
					if (isset($winpropsArray["height"])) {
						$newWinProps .= 'height=' . $winpropsArray["height"] . ',';
						unset($winpropsArray["height"]);
					}
				}
				foreach ($winpropsArray as $k => $v) {
					if ($k && $v) {
						$newWinProps .= "$k=$v,";
					}
				}
				$newWinProps = ereg_replace('^(.+),$', '\1', $newWinProps);

				$_linkAttribs['onclick'] = $js . ';var we_win = window.open(\'\',\'win_' . $name . '\',\'' . $newWinProps . '\');';
				$_linkAttribs['target'] = 'win_' . $name;

			} else { // we are in webEdition
				if ($_SESSION['we_mode'] == 'seem') { //	we are in seeMode -> open in edit_include ?....


				}
			}
		}

		if ($href) {
			$_linkAttribs['href'] = $href;
			$out = getHtmlTag('a', $_linkAttribs, $out);
		} else {

			if ($id && $isCalendar) {
				if (isset($GLOBALS["lv"]->calendar_struct["storage"]) && count(
						$GLOBALS["lv"]->calendar_struct["storage"])) {
					$found = false;
					foreach ($GLOBALS["lv"]->calendar_struct["storage"] as $date) {
						if ((($GLOBALS["lv"]->calendar_struct["calendarCount"] > 0 || ($GLOBALS["lv"]->calendar_struct["calendar"] == "day" && $GLOBALS["lv"]->calendar_struct["calendarCount"] >= 0)) && $GLOBALS["lv"]->calendar_struct["calendarCount"] <= $GLOBALS["lv"]->calendar_struct["numofentries"]) && ((int)$date >= (int)$GLOBALS["lv"]->calendar_struct["start_date"] && (int)$date <= (int)$GLOBALS["lv"]->calendar_struct["end_date"])) {
							$found = true;
							break;
						}
					}
					if ($found) {
						if ($GLOBALS["lv"]->calendar_struct["calendar"] == "year")
							$show = "month";
						else
							$show = "day";
						$listviewname = we_getTagAttribute("listviewname", $attribs, $lvname);

						$_linkAttribs['href'] = id_to_path($id) . '?' . (isset($GLOBALS["lv"]->contentTypes) && $GLOBALS["lv"]->contentTypes ? ('we_lv_ct_' . $listviewname . '=' . rawurlencode(
								$GLOBALS["lv"]->contentTypes) . '&amp;') : '') . ($GLOBALS["lv"]->order ? ('we_lv_order_' . $listviewname . '=' . rawurlencode(
								$GLOBALS["lv"]->order) . '&amp;') : '') . ($GLOBALS["lv"]->desc ? ('we_lv_desc_' . $listviewname . '=' . rawurlencode(
								$GLOBALS["lv"]->desc) . '&amp;') : '') . ($GLOBALS["lv"]->cats ? ('we_lv_cats_' . $listviewname . '=' . rawurlencode(
								$GLOBALS["lv"]->cats) . '&amp;') : '') . ($GLOBALS["lv"]->catOr ? ('we_lv_catOr_' . $listviewname . '=' . rawurlencode(
								$GLOBALS["lv"]->catOr) . '&amp;') : '') . ($GLOBALS["lv"]->workspaceID ? ('we_lv_ws_' . $listviewname . '=' . rawurlencode(
								$GLOBALS["lv"]->workspaceID) . '&amp;') : '') . ((isset(
								$GLOBALS["lv"]->searchable) && !$GLOBALS["lv"]->searchable) ? ('we_lv_se_' . $listviewname . '=0&amp;') : '') . ('we_lv_calendar_' . $listviewname . '=' . rawurlencode(
								$show) . '&amp;') . ($GLOBALS["lv"]->calendar_struct["datefield"] != "" ? ('we_lv_datefield_' . $listviewname . '=' . rawurlencode(
								$GLOBALS["lv"]->calendar_struct["datefield"]) . '&amp;') : '') . ($GLOBALS["lv"]->calendar_struct["date"] >= 0 ? ('we_lv_date_' . $listviewname . '=' . rawurlencode(
								date("Y-m-d", $GLOBALS["lv"]->calendar_struct["date"]))) : '');

						$out = getHtmlTag('a', $_linkAttribs, $out);
					}
				}
			} else
				if ($id && $isImageDoc) {
					$_linkAttribs['href'] = id_to_path($id) . '?' . ($GLOBALS["lv"]->contentTypes ? ('we_lv_ct_' . $lvname . '=' . rawurlencode(
							$GLOBALS["lv"]->contentTypes) . '&amp;') : '') . ($GLOBALS["lv"]->order ? ('we_lv_order_' . $lvname . '=' . rawurlencode(
							$GLOBALS["lv"]->order) . '&amp;') : '') . ($GLOBALS["lv"]->desc ? ('we_lv_desc_' . $lvname . '=' . rawurlencode(
							$GLOBALS["lv"]->desc) . '&amp;') : '') . ($GLOBALS["lv"]->cats ? ('we_lv_cats_' . $lvname . '=' . rawurlencode(
							$GLOBALS["lv"]->cats) . '&amp;') : '') . ($GLOBALS["lv"]->catOr ? ('we_lv_catOr_' . $lvname . '=' . rawurlencode(
							$GLOBALS["lv"]->catOr) . '&amp;') : '') . ($GLOBALS["lv"]->workspaceID ? ('we_lv_ws_' . $lvname . '=' . rawurlencode(
							$GLOBALS["lv"]->workspaceID) . '&amp;') : '') . ((!$GLOBALS["lv"]->searchable) ? ('we_lv_se_' . $lvname . '=0&amp;') : '') . (isset(
							$GLOBALS["lv"]->condition) && $GLOBALS["lv"]->condition != "" ? ('we_lv_condition_' . $lvname . '=' . rawurlencode(
							$GLOBALS["lv"]->condition) . '&amp;') : '') . 'we_lv_start_' . $lvname . '=' . (($GLOBALS["lv"]->count + $GLOBALS["lv"]->start) - 1) . '&amp;we_lv_pend_' . $lvname . '=' . ($GLOBALS["lv"]->start + $GLOBALS["lv"]->anz) . '&amp;we_lv_pstart_' . $lvname . '=' . ($GLOBALS["lv"]->start);

					$out = getHtmlTag('a', $_linkAttribs, $out);

				} else {

					if ($tid) {
						$GLOBALS["lv"]->tid = $tid;
					}

					if (isset($GLOBALS["lv"]->ClassName) && $GLOBALS["lv"]->ClassName == "we_search_listview" && $GLOBALS["lv"]->f("OID")) {
						if ($tid) {
							$tail = "&amp;we_objectTID=" . $tid;
						} else {
							$tail = "";
						}
						$path_parts = pathinfo($_SERVER["PHP_SELF"]);
						if ($GLOBALS["lv"]->objectseourls){
							$db = new DB_WE();
							$objecturl=f("SELECT DISTINCT Url FROM ".OBJECT_FILES_TABLE." WHERE ID='" . abs($GLOBALS["lv"]->f("OID")) . "' LIMIT 1", "Url", $db);
							$objectdaten=getHash("SELECT  Url,TriggerID FROM ".OBJECT_FILES_TABLE." WHERE ID='" . abs($GLOBALS["lv"]->f("OID")) . "' LIMIT 1", $db);
							$objecturl=$objectdaten['Url'];$objecttriggerid= $objectdaten['TriggerID'];
							if ($objecttriggerid){$path_parts = pathinfo(id_to_path($objecttriggerid));}
						}
						if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $GLOBALS["lv"]->hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
							if($GLOBALS["lv"]->objectseourls && $objecturl!=''){
								$_linkAttribs['href'] = ($path_parts['dirname']!=DIRECTORY_SEPARATOR ? $path_parts['dirname']:'').DIRECTORY_SEPARATOR.$objecturl . '?pid=' . $GLOBALS["lv"]->f("WorkspaceID");
							} else {
								$_linkAttribs['href'] = ($path_parts['dirname']!=DIRECTORY_SEPARATOR ? $path_parts['dirname']:'').DIRECTORY_SEPARATOR.  '?we_objectID=' . $GLOBALS["lv"]->f("OID") . '&amp;pid=' . $GLOBALS["lv"]->f("WorkspaceID");
							 }
						} else {
							if($GLOBALS["lv"]->objectseourls && $objecturl!=''){
								$_linkAttribs['href'] = ($path_parts['dirname']!=DIRECTORY_SEPARATOR ? $path_parts['dirname']:'').DIRECTORY_SEPARATOR.$path_parts['filename'].DIRECTORY_SEPARATOR.$objecturl. '?pid=' . $GLOBALS["lv"]->f("WorkspaceID");
							} else {
								$_linkAttribs['href'] = $_SERVER["PHP_SELF"] . '?we_objectID=' . $GLOBALS["lv"]->f("OID") . '&amp;pid=' . $GLOBALS["lv"]->f("WorkspaceID");
							}
						}		
						$_linkAttribs['href'] = $_linkAttribs['href'] . $tail;

						if ($name == 'we_href') {
							$out = $_linkAttribs['href'];
						} else {
							$out = getHtmlTag('a', $_linkAttribs, $out); //  output of link-tag
						}
					} else
						if (isset($GLOBALS["lv"]->ClassName) && $GLOBALS["lv"]->ClassName == "we_catListview" && we_tag('ifHasChildren',
								array(),
								"")) {
							$parentidname = we_getTagAttribute('parentidname', $attribs, 'we_parentid');
							$_linkAttribs['href'] = $_SERVER["PHP_SELF"] . '?' . $parentidname . '=' . $GLOBALS["lv"]->f(
									"ID");

							if ($name == 'we_href') {
								$out = $_linkAttribs['href'];
							} else {
								$out = getHtmlTag('a', $_linkAttribs, $out); //  output of link-tag
							}
						} else {

							$showlink = (!isset($GLOBALS["lv"]->ClassName) || $GLOBALS["lv"]->ClassName == "" || $GLOBALS["lv"]->ClassName == "we_listview") || ($GLOBALS["lv"]->ClassName == "we_search_listview") || ($GLOBALS["lv"]->ClassName == "we_listview_shopVariants") || ($GLOBALS["lv"]->ClassName == "we_listview_shoppingCart") || ($GLOBALS["lv"]->ClassName == "we_objecttag" && $GLOBALS["lv"]->triggerID != "0") || ($GLOBALS["lv"]->ClassName == "we_customertag") || ($GLOBALS["lv"]->ClassName == "we_listview_customer") || ($GLOBALS["lv"]->ClassName == "we_listview_object" && $GLOBALS["lv"]->triggerID != "0") || ($tid && $GLOBALS["lv"]->ClassName == "we_listview_object") || ($GLOBALS["lv"]->ClassName == "we_listview_object" && ($GLOBALS["lv"]->DB_WE->f(
									"OF_Templates") || $GLOBALS["lv"]->docID)) || ($GLOBALS["lv"]->ClassName == "we_listview_multiobject" && ($GLOBALS["lv"]->DB_WE->f(
									"OF_Templates") || $GLOBALS["lv"]->docID));

							if ($showlink) {

								if ($tid && $GLOBALS["lv"]->ClassName == "we_listview_object") {
									$tail = "&amp;we_objectTID=$tid";
								} else {
									$tail = '';
								}

								if (($GLOBALS['we_doc']->ClassName == 'we_objectFile') && ($GLOBALS['we_doc']->InWebEdition)) {
									$_linkAttribs['href'] = $GLOBALS["lv"]->f("wedoc_lastPath") . $tail;
								} else {
									$path_parts = pathinfo($GLOBALS["lv"]->f("WE_PATH"));
									if ($triggerid) {
										$triggerpath = id_to_path($triggerid); 
										$triggerpath_parts = pathinfo($triggerpath); 
										if (!$GLOBALS['WE_MAIN_DOC']->InWebEdition && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $GLOBALS["lv"]->hidedirindex && in_array($triggerpath_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
											$_linkAttribs['href'] = ($triggerpath_parts['dirname']!=DIRECTORY_SEPARATOR ? $triggerpath_parts['dirname']:'').DIRECTORY_SEPARATOR. $GLOBALS["lv"]->f("WE_URL") . $tail;
										} else {
											$_linkAttribs['href'] = ($triggerpath_parts['dirname']!=DIRECTORY_SEPARATOR ? $triggerpath_parts['dirname']:'').DIRECTORY_SEPARATOR . $triggerpath_parts['filename'] . DIRECTORY_SEPARATOR . $GLOBALS["lv"]->f("WE_URL") . $tail;
										}
									} else {
										if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $GLOBALS["lv"]->hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
											$_linkAttribs['href'] = ($path_parts['dirname']!=DIRECTORY_SEPARATOR ? $path_parts['dirname']:'').DIRECTORY_SEPARATOR;
										} else {
											$_linkAttribs['href'] = $GLOBALS["lv"]->f("WE_PATH") . $tail;
										}
									}
								}

								if ($name == 'we_href') { //  return href for this object
									$out = $_linkAttribs['href'];
								} else {
									$out = getHtmlTag('a', $_linkAttribs, $out);
								}
							}
						}
				}
		}

	}

	if ($isImageDoc && isset($_SESSION["we_mode"]) && $_SESSION["we_mode"] == "seem" && $GLOBALS["we_doc"]->InWebEdition && $GLOBALS["we_doc"]->ContentType != "text/weTmpl") {
		$out .= '<a href="' . $GLOBALS['lv']->f('WE_ID') . '" seem="edit_image"></a>';
	}

	//	Add a anchor to tell seeMode that this is an object.
	if (isset($_SESSION["we_mode"]) && $_SESSION["we_mode"] == "seem" && (isset($GLOBALS["lv"]->ClassName) && $GLOBALS["lv"]->ClassName == "we_listview_object") && isset(
			$GLOBALS["_we_listview_object_flag"]) && $GLOBALS["_we_listview_object_flag"] && $GLOBALS["we_doc"]->InWebEdition && $GLOBALS["we_doc"]->ContentType != "text/weTmpl" && $GLOBALS["lv"]->seeMode && $seeMode) {

		$out = '<a href="' . $GLOBALS["lv"]->DB_WE->Record["OF_ID"] . '" seem="object"></a>
		<?php $GLOBALS["_we_listview_object_flag"] = false; ?>
		' . $out;

	}
	return we_redirect_tagoutput($out,$nameTo,$to);
}
