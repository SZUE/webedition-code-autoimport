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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');


/* * ************ fuction for orders  ************** */
$typeObj = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'object';
$typeDoc = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'document';
$actPage = isset($_REQUEST['actPage']) ? $_REQUEST['actPage'] : '0';

function orderBy($a, $b){
	return ($a[$_REQUEST['orderBy']] >= $b[$_REQUEST['orderBy']] ? (isset($_REQUEST['orderDesc']) ? false : true) : (isset($_REQUEST['orderDesc']) ? true : false));
}

function getTitleLinkObj($text, $orderKey){

	$_href = $_SERVER['SCRIPT_NAME'] .
			'?typ=' . $GLOBALS['typeObj'] .
			'&orderBy=' . $orderKey .
			'&ViewClass=' . $GLOBALS['classid'] .
			'&actPage=' . $GLOBALS['actPage'] .
			( ($GLOBALS['orderBy'] == $orderKey && !isset($_REQUEST['orderDesc'])) ? '&orderDesc=true' : '' );

	return '<a href="' . $_href . '">' . $text . '</a>' . ($GLOBALS['orderBy'] == $orderKey ? ' <img src="' . IMAGE_DIR . 'arrow_sort_' . (isset($_REQUEST['orderDesc']) ? 'desc' : 'asc') . '.gif" />' : '');
}

function getPagerLinkObj(){

	return $_SERVER['SCRIPT_NAME'] .
			'?typ=' . $GLOBALS['typeObj'] .
			'&orderBy=' . $GLOBALS['orderBy'] .
			'&ViewClass=' . $GLOBALS['classid'] .
			'&actPage=' . $GLOBALS['actPage'] .
			(isset($_REQUEST['orderdesc']) ? '&orderDesc=true' : '' );
}

function getTitleLinkDoc($text, $orderKey){

	$_href = $_SERVER['SCRIPT_NAME'] .
			'?typ=' . $GLOBALS['typeDoc'] .
			'&orderBy=' . $orderKey .
			'&actPage=' . $GLOBALS['actPage'] .
			( ($GLOBALS['orderBy'] == $orderKey && !isset($_REQUEST['orderDesc'])) ? '&orderDesc=true' : '' );

	$arrow = '';

	if($GLOBALS['orderBy'] == $orderKey){

		if(isset($_REQUEST['orderDesc'])){
			$arrow = ' <img src="' . IMAGE_DIR . 'arrow_sort_desc.gif" />';
		} else {
			$arrow = ' &darr; ';
			$arrow = ' <img src="' . IMAGE_DIR . 'arrow_sort_asc.gif" />';
		}
	}

	return '<a href="' . $_href . '">' . $text . '</a>' . $arrow;
}

function getPagerLinkDoc(){

	return $_SERVER['SCRIPT_NAME'] .
			'?typ=' . $GLOBALS['typeDoc'] .
			'&orderBy=' . $GLOBALS['orderBy'] .
			'&actPage=' . $GLOBALS['actPage'] .
			(isset($_REQUEST['orderdesc']) ? '&orderDesc=true' : '' );
}

/* * ************ fuction for orders  ************** */



we_html_tools::protect();

echo we_html_tools::getHtmlTop() .
 STYLESHEET .
 we_html_element::jsElement('
	function we_submitDateform() {
		elem = document.forms[0];
		elem.submit();
	}') .
 we_html_element::cssElement('
	table.revenueTable {
		border-collapse: collapse;
	}
	table.revenueTable th,
	table.revenueTable td {
		padding: 8px;
		border: 1px solid #666666;
	}
') . '
</head>
<body class="weEditorBody" onload="self.focus();" onunload="">
<form>';




/* * ************ some config  ************** */
$feldnamen = explode("|", f("SELECT strFelder from " . ANZEIGE_PREFS_TABLE . " WHERE strDateiname = 'shop_pref'", "strFelder", $DB_WE));
$waehr = "&nbsp;" . oldHtmlspecialchars($feldnamen[0]);
$dbPreisname = "Preis";
$numberformat = $feldnamen[2];
$notInc = "tblTemplates";

if(isset($feldnamen[3])){
	$fe = explode(",", $feldnamen[3]); //determine more than just one class-ID
} else {
	$fe = array(0);
}

if(empty($classid)){
	$classid = $fe[0];
}
if(!isset($nrOfPage)){
	$nrOfPage = isset($feldnamen[4]) ? $feldnamen[4] : 20;
}
if($nrOfPage == "default"){
	$nrOfPage = 20;
}
if(!isset($val)){
	$val = "";
}
if(!isset($varies)){
	$varies = "";
}
if(isset($varies)){
	$varies = "variant_" . WE_SHOP_VARIANTS_ELEMENT_NAME;
}

/* * ************ some config  ************** */
$parts = array();
$daten = "";
if(isset($daten)){

	/*	 * ************ some initialisation  ************** */
	$mwst = (!empty($feldnamen[1])) ? (($feldnamen[1] / 100) + 1) : "";
	$da = ( $GLOBALS["WE_LANGUAGE"] == "Deutsch" ) ? "%d.%m.%y" : "%m/%d/%y";
	$dateform = ( $GLOBALS["WE_LANGUAGE"] == "Deutsch" ) ? "00.00.00" : "00/00/00";
	$datereg = ( $GLOBALS["WE_LANGUAGE"] == "Deutsch" ) ? "/\d\d\.\d\d\.\d\d/" : "/\d\d\\/\d\d\\/\d\d/";
	if(!isset($_REQUEST['sort'])){
		$_REQUEST['sort'] = "";
	}


	/*	 * ************ selectbox function ************** */

	function array_select($arr_value, $select_name, $label){ // function for a selectbox for the purpose of selecting a class..
		$fe = (isset($GLOBALS['feldnamen'][3]) ?
						explode(",", $GLOBALS['feldnamen'][3]) : //determine more than just one class-ID
						array(0));

		$menu = '<label for="' . $select_name . '">' . $label . '</label>
<select name="' . $select_name . "\" onChange=\"document.location.href='" . $_SERVER['SCRIPT_NAME'] . "?typ=object&ViewClass='+ this.options[this.selectedIndex].value\">\n";

		foreach($fe as $val){
			if($val != ''){
				$menu .= "  <option value=\"" . $val . "\"" .
						((isset($_REQUEST[$select_name]) && $val == $_REQUEST[$select_name]) ? " selected=\"selected\"" : "") . '>' .
						f('SELECT ' . OBJECT_TABLE . '.Text as ClassIDName FROM ' . OBJECT_TABLE . ' WHERE ' . OBJECT_TABLE . '.ID = ' . intval($val), 'ClassIDName', $GLOBALS['DB_WE']) .
						'</option>';
			}
		}
		$menu .= '</select><input type="hidden" name="typ" value="object" />';
		return $menu;
	}

	/*	 * ************ selectbox function ************** */

	$selClass = array_select($val, "ViewClass", g_l('modules_shop', '[classSel]')); // displays a selectbox for the purpose of selecting a class..



	/*	 * ******** START PROCESS THE OUTPUT IF OPTED FOR AN OBJECT *********** */

	switch($_REQUEST['typ']){
		case "object": //start output object
			$orderBy = isset($_REQUEST['orderBy']) ? $DB_WE->escape($_REQUEST['orderBy']) : 'obTitle';
			$entries = 0;
			$count_expression = "";
			$from_expression = "";
			$where_expression = "";
			if(!empty($fe)){
				$fe_count = 0;

				foreach($fe as $clId){
					if($fe_count > 0){
						$count_expression .= ' + ';
						$from_expression .= ', ';
						$where_expression .= ' AND ';
					}
					$count_expression .= 'COUNT(DISTINCT ' . OBJECT_X_TABLE . intval($clId) . '.OF_ID)';
					$from_expression .= OBJECT_X_TABLE . $clId;
					$where_expression .= OBJECT_X_TABLE . $clId . '.OF_ID !=0';
					$fe_count++;
				}
			} else {
				$classid = intval($classid);
				$count_expression = 'COUNT(' . OBJECT_X_TABLE . $classid . '.OF_ID)';
				$from_expression = OBJECT_X_TABLE . $classid;
				$where_expression = OBJECT_X_TABLE . "$classid.OF_ID !=0";
			}
			$DB_WE->query('SELECT ' . $count_expression . ' AS dbEntries FROM ' . $from_expression . ' WHERE ' . $where_expression);
			while($DB_WE->next_record()){ // Pager: determine the number of records;
				$entries += $DB_WE->f("dbEntries");
			}
			$active_page = !empty($_GET['page']) ? $_GET['page'] : 0; // Pager: determine the current page
			$docType2 = isset($docType2) ? $docType2 = "objectFile" : $docType2 = "objectFile"; // Pager: determine the current page
			$typeAlias = isset($typeAlias) ? $typeAlias = "object" : $typeAlias = "object"; // Pager: determine the current page
			if(!isset($classSelectTable)){
				$classSelectTable = "";
			}
			if($entries != 0){ // Pager: Number of records not empty?
				$topInfo = ($entries > 0) ? $entries : g_l('modules_shop', '[noRecord]');

				$classid = abs($_REQUEST["ViewClass"]); // gets the value from the selectbox;

				$classSelectTable .= '<table cellpadding="2" cellspacing="0" width="600" border="0">
    <tr>
        <td colspan="2" class="defaultfont">' . $selClass . '</td>
    </tr>
</table>';
				$parts[] = array(
					'html' => $classSelectTable,
					'space' => 0
				);

				// :: then do the query for objects
				$queryCondition = OBJECT_X_TABLE . "$classid.OF_ID = " . OBJECT_FILES_TABLE . ".ID AND " . OBJECT_X_TABLE . "$classid.ID = " . OBJECT_FILES_TABLE . ".ObjectID";
				$queryFrom = OBJECT_X_TABLE . $classid . ',' . OBJECT_FILES_TABLE . ' ';
				$DB_WE->query('SELECT ' . OBJECT_X_TABLE . $classid . '.input_' . WE_SHOP_TITLE_FIELD_NAME . ' AS obTitle,' . OBJECT_X_TABLE . $classid . '.OF_ID AS obID,' . OBJECT_FILES_TABLE . '.CreationDate AS cDate,' . OBJECT_FILES_TABLE . '.Published AS cPub,' . OBJECT_FILES_TABLE . '.ModDate AS cMob
                    FROM ' . $queryFrom . '
                    WHERE ' . $queryCondition . '
                    ORDER BY obID'); // get the shop-objects from DB;
				// build the table
				$orderRows = array();

				while($DB_WE->next_record()){
					// for the articlelist, we need also all these article, so sve them in array
					$orderRows[] = array(
						'articleArray' => unserialize($DB_WE->f('strSerial')),
						// save all data in array
						'obTitle' => $DB_WE->f('obTitle'), // also for ordering
						'obID' => $DB_WE->f('obID'), // also for ordering
						'cDate' => $DB_WE->f('cDate'), // also for ordering
						'cPub' => $DB_WE->f('cPub'), // also for ordering
						'cMob' => $DB_WE->f('cMob'), // also for ordering
						//'type' => "Objekt",       // also for ordering
						'orderArray' => array(),
					);
				}

				// build the headline
				$headline = array(
					array('dat' => getTitleLinkObj(g_l('modules_shop', '[ArtName]'), 'obTitle')),
					array('dat' => getTitleLinkObj(g_l('modules_shop', '[ArtID]'), 'obID')),
					array('dat' => getTitleLinkObj(g_l('modules_shop', '[artCreate]'), 'cDate')),
					array('dat' => getTitleLinkObj(g_l('modules_shop', '[artPub]'), 'cPub')),
					array('dat' => getTitleLinkObj(g_l('modules_shop', '[artMod]'), 'cMob')),
				);

				// we need functionalitty to order these

				if(isset($_REQUEST['orderBy']) && $_REQUEST['orderBy']){
					usort($orderRows, 'orderBy');
				}

				if(!isset($content)){
					$content = array();
				}

				for($nr = 0, $i = ($actPage * $nrOfPage); $i < count($orderRows) && $i < ($actPage * $nrOfPage + $nrOfPage); $i++, $nr++){
					$isPublished = $orderRows[$i]['cPub'] > 0 ? true : false;
					$publishedStylePre = $isPublished ? "" : '<span style="color: red">';
					$publishedStylePost = $isPublished ? "" : '</span>';
					$publishedLinkStyle = $isPublished ? "" : ' style="color: red"';

					$content[$nr][0]['dat'] = '<a href="javascript:top.opener.top.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\' ,\'' . $orderRows[$i]['obID'] . '\',\'' . $docType2 . '\');");"' . $publishedLinkStyle . '>' . substr($orderRows[$i]['obTitle'], 0, 25) . ".." . '</a>';
					$content[$nr][1]['dat'] = $publishedStylePre . $orderRows[$i]['obID'] . $publishedStylePost;
					//$content[$nr][2]['dat'] = $orderRows[$i]['type'];
					$content[$nr][2]['dat'] = $publishedStylePre . ($orderRows[$i]['cDate'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['cDate']) : "") . $publishedStylePost;
					$content[$nr][3]['dat'] = $orderRows[$i]['cPub'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['cPub']) : "";
					$content[$nr][4]['dat'] = $publishedStylePre . ($orderRows[$i]['cMob'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['cMob']) : "") . $publishedStylePost;
				}

				$parts[] = array(
					'html' => we_html_tools::htmlDialogBorder3(670, 100, $content, $headline),
					'space' => 0,
					'noline' => true
				);

				// now the pager class at last:
				// Pager: Zweite Linkliste zeigen

				$pager = blaettern::getStandardPagerHTML(getPagerLinkObj(), $actPage, $nrOfPage, count($orderRows));

				$parts[] = array(
					'html' => $pager,
					'space' => 0
				);


				print we_html_multiIconBox::getHTML("revenues", "100%", $parts, 30, "", -1, "", "", false, sprintf(g_l('tabs', '[module][artList]'), $topInfo));
			} else { // if there is an empty result form the object table
				$parts = array(
					array(
						'html' => '<table cellpadding="2" cellspacing="0" width="100%" border="0">' .
						'<tr><td class="defaultfont">' . g_l('modules_shop', '[noRecordAlert]') . '</td></tr>' .
						'<tr><td class="defaultfont">' . we_html_button::create_button("image:btn_shop_pref", "javascript:top.opener.top.we_cmd('pref_shop')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")) . '</td></tr>' .
						'</table>',
						'space' => 0
					)
				);


				print we_html_multiIconBox::getHTML("revenues", "100%", $parts, 30, "", -1, "", "", false, sprintf(g_l('tabs', '[module][artList]'), g_l('modules_shop', '[noRecord]')));
			}

			/*			 * ******** END PROCESS THE OUTPUT IF OPTED FOR AN OBJECT *********** */


			/*			 * ******** START PROCESS THE OUTPUT IF OPTED FOR A DOCUMENT *********** */
			break;
		case "document": //start output doc
			$orderBy = isset($_REQUEST['orderBy']) ? $_REQUEST['orderBy'] : 'sql';
			$entries = f('SELECT count(Name) AS Anzahl FROM ' . LINK_TABLE . ' WHERE Name ="' . $DB_WE->escape(WE_SHOP_TITLE_FIELD_NAME) . '"', 'Anzahl', $DB_WE); // Pager: determine the number of records;
			$active_page = !empty($_GET['page']) ? $_GET['page'] : 0; // Pager: determine the number of records;
			$docType = isset($docType) ? $docType = "text/webedition" : $docType = "text/webedition"; // Pager: determine the current page
			$typeAlias = isset($typeAlias) ? $typeAlias = "document" : $typeAlias = "document"; // Pager: determine the current page

			if($entries != 0){ // Pager: Number of records not empty?
				$topInfo = ($entries > 0) ? $entries : g_l('modules_shop', '[noRecord]');
				// :: then do the query for documents
				$queryCondition = FILE_TABLE . '.ID = ' . LINK_TABLE . '.DID AND ' . LINK_TABLE . '.CID = ' . CONTENT_TABLE . '.ID AND ' . LINK_TABLE . '.Name = "' . WE_SHOP_TITLE_FIELD_NAME . '" ';
				$queryFrom = CONTENT_TABLE . ', ' . LINK_TABLE . ',' . FILE_TABLE . ' ';
				$DB_WE->query('SELECT ' . CONTENT_TABLE . '.dat AS sqlDat, ' . LINK_TABLE . '.DID AS dd, ' . FILE_TABLE . '.CreationDate AS dDate,' . FILE_TABLE . '.Published AS dPub,' . FILE_TABLE . '.ModDate AS dMod
            FROM ' . $queryFrom . ' WHERE ' . $queryCondition . ' ORDER BY dd'); // get the shop-documents from DB;
				// build the table
				$orderRows = array();
				while($DB_WE->next_record()){
					// for the articlelist, we need also all these article, so sve them in array
					$orderRows[] = array(
						'articleArray' => unserialize($DB_WE->f('strSerial')),
						// save all data in array
						'sql' => $DB_WE->f('sqlDat'), // also for ordering
						'dd' => $DB_WE->f('dd'), // also for ordering
						'dDate' => $DB_WE->f('dDate'), // also for ordering
						'dPub' => $DB_WE->f('dPub'), // also for ordering
						'dMod' => $DB_WE->f('dMod'), // also for ordering
						//'type'] = "Doc";       // also for ordering
						'orderArray' => array()
					);
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

				// we need functionalitty to order these

				if(isset($_REQUEST['orderBy']) && $_REQUEST['orderBy']){
					usort($orderRows, 'orderBy');
				}

				for($nr = 0, $i = ($actPage * $nrOfPage); $i < count($orderRows) && $i < ($actPage * $nrOfPage + $nrOfPage); $i++, $nr++){
					$isPublished = $orderRows[$i]['dPub'] > 0 ? true : false;
					$publishedStylePre = $isPublished ? '' : '<span style="color: red">';
					$publishedStylePost = $isPublished ? '' : '</span>';
					$publishedLinkStyle = $isPublished ? '' : ' style="color: red"';
					$content[$nr] = array(
						array('dat' => $publishedStylePre . ('<a href="javascript:top.opener.top.weEditorFrameController.openDocument(\'' . FILE_TABLE . '\' ,\'' . $orderRows[$i]['dd'] . '\',\'' . $docType . '\');");"' . $publishedLinkStyle . '>' . substr($orderRows[$i]['sql'], 0, 25) . ".." . '</a>') . $publishedStylePost),
						array('dat' => $publishedStylePre . ($orderRows[$i]['dd'])),
						//$content[$nr][2]['dat'] = $orderRows[$i]['type'];
						array('dat' => $publishedStylePre . ($orderRows[$i]['dDate'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['dDate']) : '') . $publishedStylePost),
						array('dat' => $orderRows[$i]['dPub'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['dPub']) : ""),
						array('dat' => $publishedStylePre . ($orderRows[$i]['dMod'] > 0 ? date('d.m.Y - H:m:s', $orderRows[$i]['dMod']) : '') . $publishedStylePost),
					);
				}
				if(!isset($content)){
					$content = array();
				}
				$parts[] = array(
					'html' => we_html_tools::htmlDialogBorder3(670, 100, $content, $headline),
					'space' => 0,
					'noline' => true
				);

				$pager = blaettern::getStandardPagerHTML(getPagerLinkDoc(), $actPage, $nrOfPage, count($orderRows));


				$parts[] = array(
					'html' => $pager,
					'space' => 0
				);

				print we_html_multiIconBox::getHTML("revenues", "100%", $parts, 30, "", -1, "", "", false, sprintf(g_l('tabs', '[module][artList]'), $topInfo));
			}

			/*			 * ******** END PROCESS THE OUTPUT IF OPTED FOR A DOCUMENT *********** */
			break;
		default:
			print "Die von Ihnen gewünschte Seite kann nicht angezeigt werden!";
	}
}
?>
</body>
</html>