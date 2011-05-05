<?php

/**
 * webEdition CMS
 *
 * $Rev: 2814 $
 * $Author: mokraemer $
 * $Date: 2011-04-24 22:23:28 +0200 (So, 24. Apr 2011) $
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
function we_parse_tag_listview($attribs, $content) {
	eval('$arr = ' . $attribs . ';');
	switch (we_getTagAttributeTagParser('type', $arr)) {
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
			if (defined('OBJECT_TABLE')) {
				$predefinedSQL = we_getTagAttributeTagParser('predefinedSQL', $arr, '');

				if (($foo = attributFehltError($arr, 'classid', 'listview')) && $predefinedSQL == '')
					return $foo;
			}
			break;
		case 'orderitem':
			if (defined('SHOP_TABLE')) {
				if (($foo = attributFehltError($arr, 'orderid', 'listview')))
					return $foo;
			}
			break;
		case 'multiobject':
			if (defined('OBJECT_TABLE')) {
				if (($foo = attributFehltError($arr, 'name', 'listview')))
					return $foo;
			}
			break;
		case 'banner':
			if (defined('BANNER_TABLE')) {
				if (($foo = attributFehltError($arr, 'path', 'listview')))
					return $foo;
			}
			break;
		default:
			return parseError(sprintf(g_l('parser', '[wrong_type]'), 'listview'));
	}
	//setting global $lv is for backward compatibility
	return '<?php global $lv;print we_tag(\'listview\',' . $attribs . ');?>' . $content . '<?php we_post_tag_listview();?>';
}

function we_tag_listview($attribs, $content) {
	$name = we_getTagAttribute('name', $attribs, '0');
	$doctype = we_getTagAttribute('doctype', $attribs);
	$class = we_getTagAttribute('classid', $attribs, '0');
	$we_lv_cats = isset($_REQUEST['we_lv_cats_' . $name]) ? $_REQUEST['we_lv_cats_' . $name] : we_getTagAttribute('categories', $attribs);
	$categoryids = we_getTagAttribute('categoryids', $attribs);
	$we_lv_categoryids = isset($_REQUEST['we_lv_categoryids_' . $name]) ? $_REQUEST['we_lv_categoryids_' . $name] : $categoryids;
	$we_lv_catOr = (isset($_REQUEST['we_lv_catOr_' . $name]) ? $_REQUEST['we_lv_catOr_' . $name] : we_getTagAttribute('catOr', $attribs, '', true) ) ? true : false;

	$rows = we_getTagAttribute('rows', $attribs, '100000000');
	$order = we_getTagAttribute('order', $attribs);
	$we_lv_order = isset($_REQUEST['we_lv_order_' . $name]) ? $_REQUEST['we_lv_order_' . $name] : $order;

	$we_lv_numorder = (isset($_REQUEST['we_lv_numorder_' . $name]) ? $_REQUEST['we_lv_numorder_' . $name] : we_getTagAttribute('numorder', $attribs, '', true) ) ? true : false;

	$id = we_getTagAttribute('id', $attribs);
	$cond = we_getTagAttribute('condition', $attribs);
	$type = we_getTagAttribute('type', $attribs, 'document');
	$desc = we_getTagAttribute('desc', $attribs, '', true);
	$we_lv_desc = (isset($_REQUEST['we_lv_desc_' . $name]) ? $_REQUEST['we_lv_desc_' . $name] : $desc ) ? true : false;

	$predefinedSQL = we_getTagAttribute('predefinedSQL', $attribs, '');
	$offset = we_getTagAttribute('offset', $attribs);
	$workspaceID = we_getTagAttribute('workspaceID', $attribs);
	$workspaceID = $workspaceID ? $workspaceID : we_getTagAttribute('workspaceid', $attribs, '');
	$we_lv_ws = isset($_REQUEST['we_lv_ws_' . $name]) ? $_REQUEST['we_lv_ws_' . $name] : $workspaceID;

	$orderid = we_getTagAttribute('orderid', $attribs, '0');

	$we_lv_languages = isset($_REQUEST['we_lv_languages_' . $name]) ? $_REQUEST['we_lv_languages_' . $name] : we_getTagAttribute('languages', $attribs, '');
	$we_lv_pagelanguage = isset($_REQUEST['we_lv_pagelanguage_' . $name]) ? $_REQUEST['we_lv_pagelanguage_' . $name] : we_getTagAttribute('pagelanguage', $attribs, '');

	$triggerid = we_getTagAttribute('triggerid', $attribs, '0');
	$docid = we_getTagAttribute('docid', $attribs, '0');
	$customers = we_getTagAttribute('customers', $attribs); // csv value of Ids
	$casesensitive = we_getTagAttribute('casesensitive', $attribs, '', true);
	$customer = we_getTagAttribute('customer', $attribs, '', true);
	$we_lv_ct = isset($_REQUEST['we_lv_ct_' . $name]) ? $_REQUEST['we_lv_ct_' . $name] : we_getTagAttribute('contenttypes', $attribs);

	$cols = we_getTagAttribute('cols', $attribs);
	$we_lv_se = (isset($_REQUEST['we_lv_se_' . $name]) ? $_REQUEST['we_lv_se_' . $name] : we_getTagAttribute('searchable', $attribs, '', true, true)) ? true : false;

	if (isset($attribs['seem'])) {
		$seeMode = we_getTagAttribute('seem', $attribs, '', true, true); //	backwards compatibility
	} else {
		$seeMode = we_getTagAttribute('seeMode', $attribs, '', true, true);
	}
	$calendar = we_getTagAttribute('calendar', $attribs, '');
	$datefield = we_getTagAttribute('datefield', $attribs, '');
	$date = we_getTagAttribute('date', $attribs, '');
	$weekstart = we_getTagAttribute('weekstart', $attribs, 'monday');
	$lastaccesslimit = we_getTagAttribute('lastaccesslimit', $attribs, '300');
	$lastloginlimit = we_getTagAttribute('lastloginlimit', $attribs, '');
	if (isset($attribs['recursive'])) {
		$subfolders = we_getTagAttribute('recursive', $attribs, 'true');
	} else {
		// deprecated, because subfolders acts the other way arround as it should
		$subfolders = !we_getTagAttribute('subfolders', $attribs, '', true, false);
	}
	$we_lv_subfolders = isset($_REQUEST['we_lv_subfolders_' . $name]) ? $_REQUEST['we_lv_subfolders_' . $name] : $subfolders;
	if ($we_lv_subfolders == 'false') {
		$we_lv_subfolders = false;
	}

	$cfilter = we_getTagAttribute('cfilter', $attribs, 'off');
	$hidedirindex = we_getTagAttribute('hidedirindex', $attribs, (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE), false);
	$objectseourls = we_getTagAttribute('objectseourls', $attribs, (defined('TAGLINKS_OBJECTSEOURLS') && TAGLINKS_OBJECTSEOURLS), false);
	$docAttr = we_getTagAttribute('doc', $attribs, 'self');

	if (!isset($GLOBALS['we_lv_array'])) {
		$GLOBALS['we_lv_array'] = array();
	}

	if ($we_lv_languages == 'self' || $we_lv_languages == 'top') {
		$we_lv_langguagesdoc = we_getDocForTag($we_lv_languages);
		$we_lv_languages = $we_lv_langguagesdoc->Language;
		unset($we_lv_langguagesdoc);
	}
	if ($we_lv_pagelanguage == 'self' || $we_lv_pagelanguage == 'top') {
		$we_lv_langguagesdoc = we_getDocForTag($we_lv_pagelanguage);
		if (isset($we_lv_langguagesdoc->TableID) && $we_lv_langguagesdoc->TableID) {
			$we_lv_pagelanguage = $we_lv_langguagesdoc->Language;
			$we_lv_pageID = $we_lv_langguagesdoc->OF_ID;
			$we_lv_linktype = 'tblObjectFile';
		} else {
			$we_lv_pagelanguage = $we_lv_langguagesdoc->Language;
			$we_lv_pageID = $we_lv_langguagesdoc->ID;
			$we_lv_linktype = 'tblFile';
		}
		unset($we_lv_langguagesdoc);
	} else {
		$we_lv_DocAttr = $docAttr;
		$we_lv_langguagesdoc = we_getDocForTag($we_lv_DocAttr);
		if (isset($we_lv_langguagesdoc->TableID) && $we_lv_langguagesdoc->TableID) {
			$we_lv_pagelanguage = $we_lv_langguagesdoc->Language;
			$we_lv_pageID = $we_lv_langguagesdoc->OF_ID;
			$we_lv_linktype = 'objectfile';
		} else {
			$we_lv_pagelanguage = $we_lv_langguagesdoc->Language;
			$we_lv_pageID = $we_lv_langguagesdoc->ID;
			$we_lv_linktype = 'file';
		}
		unset($we_lv_langguagesdoc);
	}
	$we_lv_calendar = isset($_REQUEST['we_lv_calendar_' . $name]) ? $_REQUEST['we_lv_calendar_' . $name] : $calendar;
	$we_lv_datefield = isset($_REQUEST['we_lv_datefield_' . $name]) ? $_REQUEST['we_lv_datefield_' . $name] : $datefield;
	$we_lv_date = isset($_REQUEST['we_lv_date_' . $name]) ? $_REQUEST['we_lv_date_' . $name] : ($date != '' ? $date : date('Y-m-d'));
	$we_lv_weekstart = isset($_REQUEST['we_lv_weekstart_' . $name]) ? $_REQUEST['we_lv_weekstart_' . $name] : $weekstart;

	if ($we_lv_cats == 'we_doc') {
		$we_lv_cats = we_getCatsFromDoc($we_doc, ',', true, $DB_WE);
	}
	$we_predefinedSQL = $predefinedSQL;
	$we_offset = $offset;
	$we_offset = $we_offset ? abs($we_offset) : 0;
	$we_rows = abs($rows);


	if ($type == 'document' || $type == 'search') {
		$we_lv_doctype = $doctype;
		if ($we_lv_doctype == 'we_doc') {
			if ($GLOBALS['we_doc']->DocType) {
				$we_lv_doctype = f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID="' . $GLOBALS['we_doc']->DocType . '"', 'DocType', $GLOBALS['DB_WE']);
			}
		}
	}

	switch ($type) {
		case 'document':
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/listview/we_listview.class.php');
			$GLOBALS['lv'] = new we_listview($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $doctype, $we_lv_cats, $we_lv_catOr, $casesensitive, $we_lv_ws, $we_lv_ct, $cols, $we_lv_se, $cond, $we_lv_calendar, $we_lv_datefield, $we_lv_date, $we_lv_weekstart, $we_lv_categoryids, $cfilter, $we_lv_subfolders, $customers, $id, $we_lv_languages, $we_lv_numorder, $hidedirindex);
			break;
		case 'search':
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/listview/we_search_listview.class.php');
			$GLOBALS['lv'] = new we_search_listview($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $doctype, $class, $we_lv_cats, $we_lv_catOr, $casesensitive, $we_lv_ws, $cols, $cfilter, $we_lv_languages, $hidedirindex, $objectseourls);
			if (!isset($GLOBALS['weEconda'])) {
				$GLOBALS['weEconda'] = '';
			}
			if (!isset($GLOBALS['weEconda']['HTML'])) {
				$GLOBALS['weEconda']['HTML'] = '';
			}

			$GLOBALS['weEconda']['HTML'] .= '<a name="emos_name" title="search" rel="' . $GLOBALS["lv"]->search . '" rev="' . $GLOBALS["lv"]->anz_all . '" >';
			break;
		case 'object':
			if (!defined('OBJECT_TABLE')) {
				return modulFehltError('Object/DB', 'listview type="object"');
			}
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/object/we_listview_object.class.php');
			$GLOBALS['lv'] = new we_listview_object($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $class, $we_lv_cats, $we_lv_catOr, $cond, $triggerid, $cols, $seeMode, $we_lv_se, $we_lv_calendar, $we_lv_datefield, $we_lv_date, $we_lv_weekstart, $we_lv_categoryids, $we_lv_ws, $cfilter, $docid, $customers, $id, $we_predefinedSQL, $we_lv_languages, $hidedirindex, $objectseourls);
			break;
		case 'languagelink':
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/listview/we_langlink_listview.class.php');
			$GLOBALS['lv'] = new we_langlink_listview($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $we_lv_linktype, $cols, $seeMode, $we_lv_se, $cfilter, $we_lv_pageID, $we_lv_pagelanguage, $hidedirindex, $objectseourls);
			break;
		case 'customer':
			if (!defined('CUSTOMER_TABLE')) {
				return modulFehltError('Customer', 'listview type="customer"');
			}
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/customer/we_listview_customer.class.php');
			$GLOBALS['lv'] = new we_listview_customer($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $hidedirindex);
			break;
		case 'onlinemonitor':
			if (!defined('CUSTOMER_SESSION_TABLE')) {
				return modulFehltError('Customer', 'listview type="onlinemonitor"');
			}
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/customer/we_listview_onlinemonitor.class.php');
			$GLOBALS['lv'] = new we_listview_onlinemonitor($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $lastaccesslimit, $lastloginlimit, $hidedirindex);
			break;
		case 'order':
			if (!defined('SHOP_TABLE')) {
				return modulFehltError('Shop', 'listview type="order"');
			}
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/shop/we_listview_order.class.php');
			$GLOBALS['lv'] = new we_listview_order($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $hidedirindex);
			break;
		case 'orderitem':
			if (!defined('SHOP_TABLE')) {
				return modulFehltError('Shop', 'listview type="orderitem"');
			}
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/shop/we_listview_orderitem.class.php');
			$GLOBALS['lv'] = new we_listview_orderitem($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $orderid, $hidedirindex);
			break;
		case 'multiobject':
			if (!defined('OBJECT_TABLE')) {
				return modulFehltError('Object/DB', 'listview type="multiobject"');
			}
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/object/we_listview_multiobject.class.php');
			$GLOBALS['lv'] = new we_listview_multiobject($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $we_lv_cats, $we_lv_catOr, $cond, $triggerid, $cols, $seeMode, $we_lv_se, $we_lv_calendar, $we_lv_datefield, $we_lv_date, $we_lv_weekstart, $we_lv_categoryids, $cfilter, $docid, $we_lv_languages, $hidedirindex, $objectseourls);
			break;
		case 'banner':
			if (!defined('BANNER_TABLE')) {
				return modulFehltError('Banner', 'listview type="banner"');
			}
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/banner/we_listview_banner.inc.php');
			$usefilter = we_getTagAttribute('usefilter', $attribs);
			$path = we_getTagAttribute('path', $attribs);
			$filterdatestart = we_getTagAttribute('filterdatestart', $attribs, '-1');
			$filterdateend = we_getTagAttribute('filterdateend', $attribs, '-1');
			$bannerid = f('SELECT ID FROM ' . BANNER_TABLE . ' WHERE PATH="' . $GLOBALS[DB_WE]->escape($path) . '"', 'ID', new DB_WE());
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/banner/weBanner.php');
			if ($customer && defined('CUSTOMER_TABLE') && (!weBanner::customerOwnsBanner($_SESSION['webuser']['ID'], $bannerid))) {
				$bannerid = 0;
			}
			$GLOBALS['lv'] = new we_listview_banner($name, $we_rows, $order, $bannerid, $usefilter, $filterdatestart, $filterdateend);
			break;
		case 'shopVariant':
			if (!defined('SHOP_TABLE')) {
				return modulFehltError('Shop', 'listview type="shopVariant"');
			}
			$defaultname = we_getTagAttribute('defaultname', $attribs, '');
			$docId = we_getTagAttribute('documentid', $attribs, '');
			$objectId = we_getTagAttribute('objectid', $attribs, '');
			if ($objectId == '') {
				if (isset($GLOBALS['lv']->ClassName) && $GLOBALS['lv']->ClassName == 'we_objecttag') {
					$objectId = $GLOBALS['lv']->object->DB_WE->f('OF_ID');
				}
				if (isset($GLOBALS['lv']->ClassName) && $GLOBALS['lv']->ClassName == 'we_listview_object') {
					$objectId = $GLOBALS['lv']->DB_WE->f('OF_ID');
				}
			}
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/shop/we_listview_shopVariants.class.php');
			$GLOBALS['lv'] = new we_listview_shopVariants($name, $we_rows, $defaultname, $docId, $objectId, $we_offset, $hidedirindex, $objectseourls);
			break;
		case 'category':
			$parentid = we_getTagAttribute('parentid', $attribs, 0);
			$parentidname = we_getTagAttribute('parentidname', $attribs);
//$categoryids="' . $categoryids . '";
//$parentid="' . $parentid . '";
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/listview/we_catListview.class.php');
			$GLOBALS['lv'] = new we_catListview($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $parentid, $categoryids, 'default', $cols, ($parentidname ? $parentidname : ''), $hidedirindex);
			break;
		default:
	}
//prevent error if $GLOBALS["we_lv_array"] is no array
	if (!isset($GLOBALS['we_lv_array']) || !is_array($GLOBALS['we_lv_array'])) {
		$GLOBALS['we_lv_array'] = array();
	}

	array_push($GLOBALS['we_lv_array'], clone($GLOBALS['lv']));
}
