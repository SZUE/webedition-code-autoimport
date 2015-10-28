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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
$protect = we_base_moduleInfo::isActive('shop') && we_users_util::canEditModule('shop') ? null : array(false);
we_html_tools::protect($protect);

/* * ************ fuction for orders  ************** */
$typeObj = we_base_request::_(we_base_request::STRING, 'type', 'object');
$typeDoc = we_base_request::_(we_base_request::STRING, 'type', 'document');
$actPage = we_base_request::_(we_base_request::INT, 'actPage', 0);

function orderBy($a, $b){
	static $ob = false;
	if($ob === false){
		$ob = we_base_request::_(we_base_request::STRING, 'orderBy');
	}
	return ($a[$ob] >= $b[$ob] ? !we_base_request::_(we_base_request::BOOL, 'orderDesc') : we_base_request::_(we_base_request::BOOL, 'orderDesc'));
}

function getTitleLinkObj($text, $orderKey){
	$_href = $_SERVER['SCRIPT_NAME'] .
		'?typ=' . $GLOBALS['typeObj'] .
		'&orderBy=' . $orderKey .
		'&ViewClass=' . $GLOBALS['classid'] .
		'&actPage=' . $GLOBALS['actPage'] .
		( ($GLOBALS['orderBy'] == $orderKey && !we_base_request::_(we_base_request::BOOL, 'orderDesc')) ? '&orderDesc=1' : '' );

	return '<a href="' . $_href . '">' . $text . '</a>' . ($GLOBALS['orderBy'] == $orderKey ? ' <i class="fa fa-sort-' . (we_base_request::_(we_base_request::BOOL, 'orderDesc') ? 'desc' : 'asc') . ' fa-lg"></i>' : '<i class="fa fa-sort fa-lg"></i>');
}

function getPagerLinkObj(){

	return $_SERVER['SCRIPT_NAME'] .
		'?typ=' . $GLOBALS['typeObj'] .
		'&orderBy=' . $GLOBALS['orderBy'] .
		'&ViewClass=' . $GLOBALS['classid'] .
		'&actPage=' . $GLOBALS['actPage'] .
		(we_base_request::_(we_base_request::BOOL, 'orderdesc') ? '&orderDesc=1' : '' );
}

function getTitleLinkDoc($text, $orderKey){

	$_href = $_SERVER['SCRIPT_NAME'] .
		'?typ=' . $GLOBALS['typeDoc'] .
		'&orderBy=' . $orderKey .
		'&actPage=' . $GLOBALS['actPage'] .
		( ($GLOBALS['orderBy'] == $orderKey && !we_base_request::_(we_base_request::BOOL, 'orderDesc')) ? '&orderDesc=1' : '' );

	$arrow = '<i class="fa fa-sort fa-lg"></i>';

	if($GLOBALS['orderBy'] == $orderKey){
		$arrow = ' <i class="fa fa-lg fa-sort-' . (we_base_request::_(we_base_request::BOOL, 'orderDesc') ?
				'desc' :
				'asc') .
			'"></i>';
	}

	return '<a href="' . $_href . '">' . $text . '</a>' . $arrow;
}

function getPagerLinkDoc(){
	return $_SERVER['SCRIPT_NAME'] .
		'?typ=' . $GLOBALS['typeDoc'] .
		'&orderBy=' . $GLOBALS['orderBy'] .
		'&actPage=' . $GLOBALS['actPage'] .
		(we_base_request::_(we_base_request::BOOL, 'orderdesc') ? '&orderDesc=1' : '' );
}

/* * ************ fuction for orders  ************** */

we_html_tools::protect();

echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET .
	we_html_element::jsElement('
	function we_submitDateform() {
		elem = document.forms[0];
		elem.submit();
	}')) . '
<body class="weEditorBody" onload="self.focus();" onunload="">
<form>';

/* * ************ some config  ************** */
$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"'));
$waehr = '&nbsp;' . oldHtmlspecialchars($feldnamen[0]);
$numberformat = $feldnamen[2];

//determine more than just one class-ID
$fe = isset($feldnamen[3]) ? explode(',', $feldnamen[3]) : array(0);

if(empty($classid)){
	$classid = $fe[0];
}
if(!isset($nrOfPage)){
	$nrOfPage = isset($feldnamen[4]) ? $feldnamen[4] : 20;
}
if($nrOfPage === "default"){
	$nrOfPage = 20;
}
if(!isset($val)){
	$val = "";
}
if(!isset($varies)){
	$varies = "";
}
if(isset($varies)){
	$varies = "variant_" . we_base_constants::WE_VARIANTS_ELEMENT_NAME;
}

/* * ************ some config  ************** */
$parts = array();
$daten = "";
if(isset($daten)){

	/*	 * ************ some initialisation  ************** */
	$mwst = (!empty($feldnamen[1])) ? (($feldnamen[1] / 100) + 1) : "";
	$da = ( $GLOBALS["WE_LANGUAGE"] === "Deutsch" ) ? "%d.%m.%y" : "%m/%d/%y";
	$dateform = ( $GLOBALS["WE_LANGUAGE"] === "Deutsch" ) ? "00.00.00" : "00/00/00";
	$datereg = ( $GLOBALS["WE_LANGUAGE"] === "Deutsch" ) ? "/\d\d\.\d\d\.\d\d/" : "/\d\d\\/\d\d\\/\d\d/";
	if(!isset($_REQUEST['sort'])){
		$_REQUEST['sort'] = "";
	}


	/*	 * ************ selectbox function ************** */

	function array_select($arr_value, $select_name, $label){ // function for a selectbox for the purpose of selecting a class
		$shopConfig = !empty($GLOBALS['feldnamen']) ?
			$GLOBALS['feldnamen'] :
			explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"'));

		$fe = (isset($shopConfig[3]) ?
				array_map('intval', array_filter(explode(',', $shopConfig[3]))) : //determine more than just one class-ID
				array());

		$selVal = we_base_request::_(we_base_request::STRING, $select_name);

		$menu = '<label for="' . $select_name . '">' . $label . '</label><select name="' . $select_name . '" onchange="document.location.href=\'' . $_SERVER['SCRIPT_NAME'] . '?typ=object&ViewClass=\' + this.options[this.selectedIndex].value ">';

		if($fe){
			$GLOBALS['DB_WE']->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' WHERE ID IN (' . implode(',', $fe) . ')');
			foreach($GLOBALS['DB_WE']->getAllFirst(false) as $id => $val){
				$menu .= '<option value="' . $id . '"' . (($id == $selVal) ? ' selected="selected"' : '') . '>' . $val . '</option>';
			}
		}
		$menu .= '</select><input type="hidden" name="typ" value="object" />';

		return $menu;
	}

	switch(we_base_request::_(we_base_request::STRING, 'typ')){
		case 'object': //start output object
			$orderBy = $DB_WE->escape(we_base_request::_(we_base_request::STRING, 'orderBy', 'obTitle'));
			$entries = 0;
			$count_expression = $from_expression = $where_expression = "";
			if(!empty($fe)){
				$fe_count = 0;

				foreach($fe as $clId){
					$clId = intval($clId);
					if(!$clId){
						continue;
					}
					if($fe_count > 0){
						$count_expression .= ' + ';
						$from_expression .= ', ';
						$where_expression .= ' AND ';
					}
					$count_expression .= 'COUNT(DISTINCT ' . OBJECT_X_TABLE . $clId . '.OF_ID)';
					$from_expression .= OBJECT_X_TABLE . $clId;
					$where_expression .= OBJECT_X_TABLE . $clId . '.OF_ID!=0';
					$fe_count++;
				}
			} else {
				$classid = intval($classid);
				if($classid){
					continue;
				}
				$count_expression = 'COUNT(' . OBJECT_X_TABLE . $classid . '.OF_ID)';
				$from_expression = OBJECT_X_TABLE . $classid;
				$where_expression = OBJECT_X_TABLE . $classid . '.OF_ID!=0';
			}
			$DB_WE->query('SELECT ' . $count_expression . ' FROM ' . $from_expression . ' WHERE ' . $where_expression);
			$entries += array_sum($DB_WE->getAll(true)); // Pager: determine the number of records;
			$active_page = we_base_request::_(we_base_request::RAW, 'page', 0); // Pager: determine the current page
			$docType2 = we_base_ContentTypes::OBJECT_FILE; // Pager: determine the current page
			$typeAlias = we_base_ContentTypes::OBJECT; // Pager: determine the current page
			$classSelectTable = (isset($classSelectTable) ? $classSelectTable : '');
			if($entries != 0){ // Pager: Number of records not empty?
				$topInfo = ($entries > 0 ? $entries : g_l('modules_shop', '[noRecord]'));
				$classid = abs(we_base_request::_(we_base_request::INT, 'ViewClass')); // gets the value from the selectbox;

				$classSelectTable .= '<table width="600">
    <tr>
        <td colspan="2" class="defaultfont">' .
					// displays a selectbox for the purpose of selecting a class..
					array_select($val, 'ViewClass', g_l('modules_shop', '[classSel]')) . '</td>
    </tr>
</table>';
				$parts[] = array(
					'html' => $classSelectTable,
					'space' => 0
				);

				// :: then do the query for objects
				$DB_WE->query('SELECT o.input_' . WE_SHOP_TITLE_FIELD_NAME . ' AS obTitle,o.OF_ID AS obID,of.CreationDate AS cDate,of.Published AS cPub,of.ModDate AS cMob
FROM ' . OBJECT_X_TABLE . $classid . ' o JOIN ' . OBJECT_FILES_TABLE . ' of ON o.OF_ID=of.ID
WHERE IsFolder=0
ORDER BY o.OF_ID'); // get the shop-objects from DB;
				// build the table
				$orderRows = $DB_WE->getAll();
				// we need functionalitty to order these
				if(we_base_request::_(we_base_request::BOOL, 'orderBy')){
					usort($orderRows, 'orderBy');
				}

				// build the headline
				$headline = array(
					array('dat' => getTitleLinkObj(g_l('modules_shop', '[ArtName]'), 'obTitle')),
					array('dat' => getTitleLinkObj(g_l('modules_shop', '[ArtID]'), 'obID')),
					array('dat' => getTitleLinkObj(g_l('modules_shop', '[artCreate]'), 'cDate')),
					array('dat' => getTitleLinkObj(g_l('modules_shop', '[artPub]'), 'cPub')),
					array('dat' => getTitleLinkObj(g_l('modules_shop', '[artMod]'), 'cMob')),
				);

				$content = array();

				for($nr = 0, $i = ($actPage * $nrOfPage); $i < count($orderRows) && $i < ($actPage * $nrOfPage + $nrOfPage); $i++, $nr++){
					$isPublished = $orderRows[$i]['cPub'] > 0 ? true : false;
					$publishedStylePre = $isPublished ? '<span>' : '<span style="color: red">';

					$content[] = array(
						array('dat' => '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\' ,\'' . $orderRows[$i]['obID'] . '\',\'' . $docType2 . '\');");">' . $publishedStylePre . substr($orderRows[$i]['obTitle'], 0, 25) . '..</span></a>'),
						array('dat' => $publishedStylePre . $orderRows[$i]['obID'] . '</span>'),
						//$content[$nr][2]['dat'] = $orderRows[$i]['type'];
						array('dat' => $publishedStylePre . ($orderRows[$i]['cDate'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['cDate']) : "") . '</span>'),
						array('dat' => $orderRows[$i]['cPub'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['cPub']) : ''),
						array('dat' => $publishedStylePre . ($orderRows[$i]['cMob'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['cMob']) : "") . '</span>')
					);
				}

				$parts[] = array(
					'html' => we_html_tools::htmlDialogBorder3(670, 100, $content, $headline),
					'space' => 0,
					'noline' => true
				);

				// now the pager class at last:
				// Pager: Zweite Linkliste zeigen
				$parts[] = array(
					'html' => we_shop_pager::getStandardPagerHTML(getPagerLinkObj(), $actPage, $nrOfPage, count($orderRows)),
					'space' => 0
				);

				echo we_html_multiIconBox::getHTML("revenues", $parts, 30, "", -1, "", "", false, sprintf(g_l('tabs', '[module][artList]'), $topInfo));
			} else { // if there is an empty result form the object table
				$parts = array(
					array(
						'html' => '<table width="100%">' .
						'<tr><td class="defaultfont">' . g_l('modules_shop', '[noRecordAlert]') . '</td></tr>' .
						'<tr><td class="defaultfont">' . we_html_button::create_button("fa:btn_shop_pref,fa-lg fa-pencil,fa-lg fa-list-alt", "javascript:top.opener.top.we_cmd('pref_shop')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")) . '</td></tr>' .
						'</table>',
						'space' => 0
					)
				);


				echo we_html_multiIconBox::getHTML("revenues", $parts, 30, "", -1, "", "", false, sprintf(g_l('tabs', '[module][artList]'), g_l('modules_shop', '[noRecord]')));
			}

			break;
		case 'document': //start output doc
			$orderBy = we_base_request::_(we_base_request::RAW, 'orderBy', 'sql');
			$entries = f('SELECT COUNT(Name) FROM ' . LINK_TABLE . ' WHERE Name="' . $DB_WE->escape(WE_SHOP_TITLE_FIELD_NAME) . '"'); // Pager: determine the number of records;
			$active_page = we_base_request::_(we_base_request::RAW, 'page', 0); // Pager: determine the number of records;
			$docType = we_base_ContentTypes::WEDOCUMENT; // Pager: determine the current page
			$typeAlias = isset($typeAlias) ? "document" : "document"; // Pager: determine the current page

			if($entries){ // Pager: Number of records not empty?
				$topInfo = ($entries ? : g_l('modules_shop', '[noRecord]'));
				// :: then do the query for documents
				$DB_WE->query('SELECT c.dat AS sql,l.DID AS dd,f.CreationDate AS dDate,f.Published AS dPub,f.ModDate AS dMod FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON l.CID=c.ID JOIN ' . FILE_TABLE . ' f ON f.ID=l.DID WHERE l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND l.Name="' . WE_SHOP_TITLE_FIELD_NAME . '" ORDER BY dd'); // get the shop-documents from DB;
				// for the articlelist, we need also all these article, so save them in array
				$orderRows = $DB_WE->getAll();
				// we need functionalitty to order these
				if(we_base_request::_(we_base_request::BOOL, 'orderBy')){
					usort($orderRows, 'orderBy');
				}
				$typeAlias = "document";
				// build the headline
				$headline = array(
					array('dat' => getTitleLinkDoc(g_l('modules_shop', '[ArtName]'), 'sql')),
					array('dat' => getTitleLinkDoc(g_l('modules_shop', '[ArtID]'), 'dd')),
					//$headline[2]['dat'] = getTitleLinkDoc(g_l('modules_shop','[docType]'), $typeAlias);
					array('dat' => getTitleLinkDoc(g_l('modules_shop', '[artCreate]'), 'dDate')),
					array('dat' => getTitleLinkDoc(g_l('modules_shop', '[artPub]'), 'dPub')),
					array('dat' => getTitleLinkDoc(g_l('modules_shop', '[artMod]'), 'dMod')),
				);

				$content = array();
				for($nr = 0, $i = ($actPage * $nrOfPage); $i < count($orderRows) && $i < ($actPage * $nrOfPage + $nrOfPage); $i++, $nr++){
					$isPublished = $orderRows[$i]['dPub'] > 0 ? true : false;
					$publishedStylePre = $isPublished ? '<span>' : '<span style="color: red">';
					$content[$nr] = array(
						array('dat' => '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . FILE_TABLE . '\' ,\'' . $orderRows[$i]['dd'] . '\',\'' . $docType . '\');");">' . $publishedStylePre . substr($orderRows[$i]['sql'], 0, 25) . '..</span></a>'),
						array('dat' => $publishedStylePre . ($orderRows[$i]['dd']) . '</span>'),
						//$content[$nr][2]['dat'] = $orderRows[$i]['type'];
						array('dat' => $publishedStylePre . ($orderRows[$i]['dDate'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['dDate']) : '') . '</span>'),
						array('dat' => $orderRows[$i]['dPub'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['dPub']) : ""),
						array('dat' => $publishedStylePre . ($orderRows[$i]['dMod'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['dMod']) : '') . '</span>'),
					);
				}
				$parts[] = array(
					'html' => we_html_tools::htmlDialogBorder3(670, 100, $content, $headline),
					'space' => 0,
					'noline' => true
				);

				$parts[] = array(
					'html' => we_shop_pager::getStandardPagerHTML(getPagerLinkDoc(), $actPage, $nrOfPage, count($orderRows)),
					'space' => 0
				);

				echo we_html_multiIconBox::getHTML("revenues", $parts, 30, "", -1, "", "", false, sprintf(g_l('tabs', '[module][artList]'), $topInfo));
			}

			/*			 * ******** END PROCESS THE OUTPUT IF OPTED FOR A DOCUMENT *********** */
			break;
		default:
			echo 'Die von Ihnen gewünschte Seite kann nicht angezeigt werden!';
	}
}
?>
</body>
</html>