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
function we_parse_tag_listview($attribs, $content, array $arr){
	switch(weTag_getParserAttribute('type', $arr)){
		default:
		case 'document':
		case 'search':
		case 'languagelink':
		case 'customer':
		case 'onlinemonitor':
		case 'order':
		case 'orderitem':
		case 'shopVariant':
		case 'category':
			break;
		case 'object':
			if(defined('OBJECT_TABLE')){
				$predefinedSQL = weTag_getParserAttribute('predefinedSQL', $arr, '');

				if(($foo = attributFehltError($arr, 'classid', __FUNCTION__)) && !$predefinedSQL){
					return $foo;
				}
			}
			break;
		case 'orderitem':
			if(defined('SHOP_TABLE') && ($foo = attributFehltError($arr, 'orderid', __FUNCTION__))){
				return $foo;
			}
			break;
		case 'multiobject':
			if(defined('OBJECT_TABLE') && ($foo = attributFehltError($arr, 'name', __FUNCTION__))){
				return $foo;
			}
			break;
		case 'banner':
			if(defined('BANNER_TABLE') && ($foo = attributFehltError($arr, 'path', __FUNCTION__))){
				return $foo;
			}
			break;
	}
	//setting global $lv is for backward compatibility
	return '<?php global $lv;' . we_tag_tagParser::printTag('listview', $attribs) . ';?>' . $content . '<?php we_post_tag_listview();?>';
}

function we_tag_listview($attribs){
	$name = weTag_getAttribute('name', $attribs, 0, we_base_request::STRING);
	$doctype = weTag_getAttribute('doctype', $attribs, '', we_base_request::STRING);
	$class = weTag_getAttribute('classid', $attribs, 0, we_base_request::INT);
	$we_lv_cats = we_base_request::_(we_base_request::WEFILELIST, 'we_lv_cats_' . $name, weTag_getAttribute('categories', $attribs, '', we_base_request::WEFILELIST));
	$categoryids = weTag_getAttribute('categoryids', $attribs, '', we_base_request::INTLIST);
	$we_lv_categoryids = we_base_request::_(we_base_request::INTLIST, 'we_lv_categoryids_' . $name, $categoryids);
	$we_lv_catOr = we_base_request::_(we_base_request::BOOL, 'we_lv_catOr_' . $name, weTag_getAttribute('catOr', $attribs, false, we_base_request::BOOL));

	$we_rows = intval(weTag_getAttribute('rows', $attribs, 100000000, we_base_request::INT));
	$order = weTag_getAttribute('order', $attribs, '', we_base_request::STRING);
	$we_lv_order = we_base_request::_(we_base_request::STRING, 'we_lv_order_' . $name, $order);

	$we_lv_numorder = we_base_request::_(we_base_request::BOOL, 'we_lv_numorder_' . $name, weTag_getAttribute('numorder', $attribs, false, we_base_request::BOOL));
	$id = weTag_getAttribute('id', $attribs, '', we_base_request::STRING);
	$cond = weTag_getAttribute('condition', $attribs, '', we_base_request::RAW);
	if($cond && $cond{0} != '$' && isset($GLOBALS[$cond])){
		$cond = $GLOBALS[$cond];
	}
	$type = weTag_getAttribute('type', $attribs, 'document', we_base_request::STRING);
	$desc = weTag_getAttribute('desc', $attribs, false, we_base_request::BOOL);
	$we_lv_desc = we_base_request::_(we_base_request::BOOL, 'we_lv_desc_' . $name, $desc);

	$predefinedSQL = weTag_getAttribute('predefinedSQL', $attribs, '', we_base_request::RAW);
	$we_offset = intval(weTag_getAttribute('offset', $attribs, 0, we_base_request::INT));
	$workspaceID = weTag_getAttribute('workspaceID', $attribs, weTag_getAttribute('workspaceid', $attribs, 0, we_base_request::INTLIST), we_base_request::INTLIST);
	$we_lv_ws = we_base_request::_(we_base_request::INTLIST, 'we_lv_ws_' . $name, $workspaceID);

	$orderid = weTag_getAttribute('orderid', $attribs, 0, we_base_request::INT);

	$we_lv_languages = we_base_request::_(we_base_request::RAW, 'we_lv_languages_' . $name, weTag_getAttribute('languages', $attribs, '', we_base_request::STRING));
	$we_lv_pagelanguage = we_base_request::_(we_base_request::RAW, 'we_lv_pagelanguage_' . $name, weTag_getAttribute('pagelanguage', $attribs, '', we_base_request::STRING));
	$showself = weTag_getAttribute('showself', $attribs, false, we_base_request::BOOL);

	$triggerid = weTag_getAttribute('triggerid', $attribs, 0, we_base_request::INT);
	$docid = weTag_getAttribute('docid', $attribs, 0, we_base_request::INT);
	$customers = weTag_getAttribute('customers', $attribs); // csv value of Ids
	$casesensitive = weTag_getAttribute('casesensitive', $attribs, false, we_base_request::BOOL);
	$customer = weTag_getAttribute('customer', $attribs, false, we_base_request::BOOL);
	$we_lv_ct = we_base_request::_(we_base_request::RAW, 'we_lv_ct_' . $name, weTag_getAttribute('contenttypes', $attribs, '', we_base_request::STRING));

	$cols = weTag_getAttribute('cols', $attribs, '', we_base_request::INT);
	$we_lv_se = we_base_request::_(we_base_request::BOOL, 'we_lv_se_' . $name, weTag_getAttribute('searchable', $attribs, true, we_base_request::BOOL));

	$seeMode = weTag_getAttribute('seem', $attribs, weTag_getAttribute('seeMode', $attribs, true, we_base_request::BOOL), we_base_request::BOOL); //	backwards compatibility


	$calendar = weTag_getAttribute('calendar', $attribs, '', we_base_request::STRING);
	$datefield = weTag_getAttribute('datefield', $attribs, '', we_base_request::STRING);
	$date = weTag_getAttribute('date', $attribs, '', we_base_request::STRING);
	$weekstart = weTag_getAttribute('weekstart', $attribs, 'monday', we_base_request::STRING);

	// deprecated, because subfolders acts the other way arround as it should
	$subfolders = (isset($attribs['subfolders'])) ?
		!weTag_getAttribute('subfolders', $attribs, false, we_base_request::BOOL) :
		weTag_getAttribute('recursive', $attribs, true, we_base_request::BOOL);

	$we_lv_subfolders = isset($_REQUEST['we_lv_subfolders_' . $name]) ? (bool) $_REQUEST['we_lv_subfolders_' . $name] : $subfolders;

	$cfilter = weTag_getAttribute('cfilter', $attribs, false, we_base_request::BOOL);
	$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, we_base_request::BOOL);
	$objectseourls = weTag_getAttribute('objectseourls', $attribs, TAGLINKS_OBJECTSEOURLS, we_base_request::BOOL);
	$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);

	switch($we_lv_languages){
		case 'self':
		case 'top':
			$we_lv_langguagesdoc = we_getDocForTag($we_lv_languages);
			$we_lv_languages = $we_lv_langguagesdoc->Language;
			unset($we_lv_langguagesdoc);
	}

	$we_lv_calendar = we_base_request::_(we_base_request::STRING, 'we_lv_calendar_' . $name, $calendar);
	$we_lv_datefield = we_base_request::_(we_base_request::STRING, 'we_lv_datefield_' . $name, $datefield);
	$we_lv_date = we_base_request::_(we_base_request::STRING, 'we_lv_date_' . $name, ($date ? : date('Y-m-d')));
	$we_lv_weekstart = we_base_request::_(we_base_request::STRING, 'we_lv_weekstart_' . $name, $weekstart);

	$we_lv_cats = ($we_lv_cats === 'we_doc' ? we_category::we_getCatsFromDoc($GLOBALS['we_doc'], ',', true, $GLOBALS['DB_WE']) : $we_lv_cats);

	switch($type){
		case 'document':
			$GLOBALS['lv'] = new we_listview_document($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $doctype, $we_lv_cats, $we_lv_catOr, $casesensitive, $we_lv_ws, $we_lv_ct, $cols, $we_lv_se, $cond, $we_lv_calendar, $we_lv_datefield, $we_lv_date, $we_lv_weekstart, $we_lv_categoryids, $cfilter, $we_lv_subfolders, $customers, $id, $we_lv_languages, $we_lv_numorder, $hidedirindex, $triggerid);
			break;
		case 'search':
			$GLOBALS['lv'] = new we_listview_search($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $doctype, $class, $we_lv_cats, $we_lv_catOr, $casesensitive, $we_lv_ws, $triggerid, $cols, $cfilter, $we_lv_languages, $hidedirindex, $objectseourls);
			break;
		case 'object':
			if(!defined('OBJECT_TABLE')){
				echo modulFehltError('Object/DB', __FUNCTION__ . ' type="object"');
				unset($GLOBALS['lv']);
				return false;
			}
			if(f('SELECT 1 FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($class))){
				$GLOBALS['lv'] = new we_object_listview($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $class, $we_lv_cats, $we_lv_catOr, $cond, $triggerid, $cols, $seeMode, $we_lv_se, $we_lv_calendar, $we_lv_datefield, $we_lv_date, $we_lv_weekstart, $we_lv_categoryids, $we_lv_ws, $cfilter, $docid, $customers, $id, $predefinedSQL, $we_lv_languages, $hidedirindex, $objectseourls);
			} else {
				t_e('warning', 'Class with id=' . intval($class) . ' does not exist');
				unset($GLOBALS['lv']);
				return false;
			}
			break;
		case 'languagelink':
			$we_lv_langguagesdoc = we_getDocForTag($we_lv_pagelanguage);
			$we_lv_ownlanguage = $we_lv_langguagesdoc->Language;

			switch(isset($GLOBALS['lv']) ? get_class($GLOBALS['lv']) : ''){
				case 'we_object_listview':
				case 'we_object_tag':
					$we_lv_pageID = $GLOBALS['lv']->getDBf('OF_ID');
					$we_lv_linktype = 'tblObjectFile';
					$we_lv_pagelanguage = $we_lv_pagelanguage === 'self' ? $GLOBALS['lv']->getDBf('OF_Language') : ($we_lv_pagelanguage === 'top' ? $we_lv_ownlanguage : $we_lv_pagelanguage);
					$we_lv_ownlanguage = $GLOBALS['lv']->getDBf('OF_Language');
					break;
				default:
					$we_lv_pagelanguage = $we_lv_pagelanguage === 'self' || $we_lv_pagelanguage === 'top' ? $we_lv_ownlanguage : we_getDocForTag($docAttr)->Language;

					if($we_lv_langguagesdoc instanceof we_objectFile){
						$we_lv_pageID = $we_lv_langguagesdoc->OF_ID;
						$we_lv_linktype = 'tblObjectFile';
					} else {
						$we_lv_pageID = $we_lv_langguagesdoc->ID;
						$we_lv_linktype = 'tblFile';
					}
			}
			unset($we_lv_langguagesdoc);

			$GLOBALS['lv'] = new we_listview_langlink($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $we_lv_linktype, $cols, $showself, $we_lv_pageID, $we_lv_pagelanguage, $we_lv_ownlanguage, $hidedirindex, $objectseourls, $we_lv_subfolders);
			break;
		case 'customer':
			if(!defined('CUSTOMER_TABLE')){
				echo modulFehltError('Customer', __FUNCTION__ . ' type="customer"');
				return;
			}
			$GLOBALS['lv'] = new we_customer_listview($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $hidedirindex);
			break;
		case 'onlinemonitor':
			if(defined('CUSTOMER_SESSION_TABLE')){
				$id = we_base_request::_(we_base_request::INT, 'we_omid', 0);
				$lastaccesslimit = weTag_getAttribute('lastaccesslimit', $attribs, 300, we_base_request::INT);
				$lastloginlimit = weTag_getAttribute('lastloginlimit', $attribs, 0, we_base_request::INT);
				$GLOBALS['lv'] = new we_customer_onlinemonitor($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $lastaccesslimit, $lastloginlimit, $hidedirindex);
				break;
			}
			echo modulFehltError('Customer', __FUNCTION__ . ' type="onlinemonitor"');
			return;
		case 'order':
			if(!defined('SHOP_TABLE')){
				echo modulFehltError('Shop', __FUNCTION__ . ' type="order"');
				return;
			}
			$GLOBALS['lv'] = new we_shop_listviewOrder($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $hidedirindex);
			break;
		case 'orderitem':
			if(!defined('SHOP_TABLE')){
				echo modulFehltError('Shop', __FUNCTION__ . ' type="orderitem"');
				return;
			}
			$GLOBALS['lv'] = new we_shop_listviewOrderitem($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $orderid, $hidedirindex);
			break;
		case 'multiobject':
			if(!defined('OBJECT_TABLE')){
				echo modulFehltError('Object/DB', __FUNCTION__ . ' type="multiobject"');
				return;
			}
			$name = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);
			$GLOBALS['lv'] = new we_object_listviewMultiobject($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $we_lv_cats, $we_lv_catOr, $cond, $triggerid, $cols, $seeMode, $we_lv_se, $we_lv_calendar, $we_lv_datefield, $we_lv_date, $we_lv_weekstart, $we_lv_categoryids, $cfilter, $docid, $we_lv_languages, $hidedirindex, $objectseourls);
			break;
		case 'banner':
			if(!defined('BANNER_TABLE')){
				echo modulFehltError('Banner', __FUNCTION__ . ' type="banner"');
				return;
			}
			$usefilter = weTag_getAttribute('usefilter', $attribs, false, we_base_request::BOOL);
			$path = weTag_getAttribute('path', $attribs, '', we_base_request::FILE);
			$filterdatestart = weTag_getAttribute('filterdatestart', $attribs, -1, we_base_request::INT);
			$filterdateend = weTag_getAttribute('filterdateend', $attribs, -1, we_base_request::INT);
			$bannerid = f('SELECT ID FROM ' . BANNER_TABLE . ' WHERE PATH="' . $GLOBALS[DB_WE]->escape($path) . '"');
			if($customer && defined('CUSTOMER_TABLE') && isset($_SESSION['webuser']['registered']) && $_SESSION['webuser']['registered'] && (!we_banner_banner::customerOwnsBanner($_SESSION['webuser']['ID'], $bannerid, $GLOBALS['DB_WE']))){
				$bannerid = 0;
			}
			$GLOBALS['lv'] = new we_banner_listview($name, $we_rows, $order, $bannerid, $usefilter, $filterdatestart, $filterdateend);
			break;
		case 'shopVariant':
			if(!defined('SHOP_TABLE')){
				echo modulFehltError('Shop', __FUNCTION__ . ' type="shopVariant"');
				return;
			}
			$defaultname = weTag_getAttribute('defaultname', $attribs, '', we_base_request::STRING);
			$docId = weTag_getAttribute('documentid', $attribs, 0, we_base_request::INT);
			$objectId = weTag_getAttribute('objectid', $attribs, 0, we_base_request::INT);
			if($objectId == 0){
				switch(isset($GLOBALS['lv']) ? get_class($GLOBALS['lv']) : ''){
					case 'we_object_tag':
					case 'we_object_listview':
						$objectId = $GLOBALS['lv']->getDBf('OF_ID');
				}
			}
			$GLOBALS['lv'] = new we_shop_listviewShopVariants($name, $we_rows, $defaultname, $docId, $objectId, $we_offset, $hidedirindex, $objectseourls, $triggerid);
			break;
		case 'category':
			$parentid = weTag_getAttribute('parentid', $attribs, 0, we_base_request::INT);
			$parentidname = weTag_getAttribute('parentidname', $attribs, '', we_base_request::STRING);
//$categoryids="' . $categoryids . '";
//$parentid="' . $parentid . '";
			$GLOBALS['lv'] = new we_listview_category($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $parentid, $categoryids, $cols, ($parentidname ? $parentidname : ''), $hidedirindex);
			break;
		default:
	}
//prevent error if $GLOBALS["we_lv_array"] is no array
	if(!isset($GLOBALS['we_lv_array']) || !is_array($GLOBALS['we_lv_array'])){
		$GLOBALS['we_lv_array'] = array();
	}

	$GLOBALS['we_lv_array'][] = clone($GLOBALS['lv']);
}
