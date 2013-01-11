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

	$true = true;
	$false = false;

	if(isset($_REQUEST['orderDesc'])){ // turn order!
		$true = false;
		$false = true;
	}

	if($a[$_REQUEST['orderBy']] >= $b[$_REQUEST['orderBy']]){
		return $true;
	} else{
		return $false;
	}
}

function getTitleLinkObj($text, $orderKey){

	$_href = $_SERVER['SCRIPT_NAME'] .
		'?typ=' . $GLOBALS['typeObj'] .
		'&orderBy=' . $orderKey .
		'&ViewClass=' . $GLOBALS['classid'] .
		'&actPage=' . $GLOBALS['actPage'] .
		( ($GLOBALS['orderBy'] == $orderKey && !isset($_REQUEST['orderDesc'])) ? '&orderDesc=true' : '' );

	$arrow = '';

	if($GLOBALS['orderBy'] == $orderKey){

		if(isset($_REQUEST['orderDesc'])){
			$arrow = ' <img src="' . IMAGE_DIR . 'arrow_sort_desc.gif" />';
		} else{
			$arrow = ' &darr; ';
			$arrow = ' <img src="' . IMAGE_DIR . 'arrow_sort_asc.gif" />';
		}
	}

	return '<a href="' . $_href . '">' . $text . '</a>' . $arrow;
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
		} else{
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

we_html_tools::htmlTop();


print STYLESHEET;

print we_html_element::jsElement('
	function we_submitDateform() {
		elem = document.forms[0];
		elem.submit();
	}').
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
$DB_WE->query("SELECT strFelder from " . ANZEIGE_PREFS_TABLE . " WHERE strDateiname = 'shop_pref'");
$DB_WE->next_record();
$feldnamen = explode("|", $DB_WE->f("strFelder"));
$waehr = "&nbsp;" . oldHtmlspecialchars($feldnamen[0]);
$dbTitlename = "shoptitle";
$dbPreisname = "Preis";
$numberformat = $feldnamen[2];
$notInc = "tblTemplates";

if(isset($feldnamen[3])){
	$fe = explode(",", $feldnamen[3]); //determine more than just one class-ID
} else{
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
	/*	 * ************ some initialisation  ************** */

	/*	 * ************ number format ************** */

	function numfom($result){
		$result = we_util::std_numberformat($result);
		switch($GLOBALS['numberformat']){
			case 'german':
				return number_format($result, 2, ",", ".");
			case 'french':
				return number_format($result, 2, ",", "&nbsp;");
			case 'english':
				return number_format($result, 2, ".", "");
			case 'swiss':
				return number_format($result, 2, ",", "'");
		}
		return $result;
	}

	/*	 * ************ number format ************** */


	/*	 * ************ selectbox function ************** */

	function array_select($arr_value, $select_name, $label){ // function for a selectbox for the purpose of selecting a class..
		if(isset($GLOBALS['feldnamen'][3])){
			$fe = explode(",", $GLOBALS['feldnamen'][3]); //determine more than just one class-ID
		} else{
			$fe = array(0);
		}
		$menu = "<label for=\"" . $select_name . "\">" . $label . "</label>\n";
		$menu .="<select name=\"" . $select_name . "\" onChange=\"document.location.href='" . $_SERVER['SCRIPT_NAME'] . "?typ=object&ViewClass='+ this.options[this.selectedIndex].value\">\n";

		foreach($fe as $key => $val){
			if($val != ""){
				$menu .= "  <option value=\"" . $val . "\"";
				$menu .= (isset($_REQUEST[$select_name]) && $val == $_REQUEST[$select_name]) ? " selected=\"selected\"" : "";
				$sql_merge = "SELECT " . OBJECT_TABLE . ".Text as ClassIDName, " . OBJECT_TABLE . ".ID as SerID FROM " . OBJECT_TABLE . " WHERE " . OBJECT_TABLE . ".ID = " . abs($val);
				$GLOBALS['DB_WE']->query($sql_merge);
				$GLOBALS['DB_WE']->next_record();
				$menu .= ">" . $GLOBALS['DB_WE']->f("ClassIDName") . "\n";
			}
		}
		$menu .= "</select>\n";
		$menu .= '<input type="hidden" name="typ" value="object" />';
		return $menu;
	}

	/*	 * ************ selectbox function ************** */

	$selClass = array_select("$val", "ViewClass", g_l('modules_shop', '[classSel]')); // displays a selectbox for the purpose of selecting a class..



	/*	 * ******** START PROCESS THE OUTPUT IF OPTED FOR AN OBJECT *********** */

	if($_REQUEST['typ'] == "object"){ //start output object
		$orderBy = isset($_REQUEST['orderBy']) ? $DB_WE->escape($_REQUEST['orderBy']) : 'obTitle';
		$entries = 0;
		$count_expression = "";
		$from_expression = "";
		$where_expression = "";
		if(count($fe) > 0){
			$fe_count = 0;

			foreach($fe as $clId){
				if($fe_count > 0){
					$count_expression .= " + ";
					$from_expression .= ", ";
					$where_expression .= " AND ";
				}
				$count_expression .= "COUNT(DISTINCT " . OBJECT_X_TABLE . intval($clId) . ".OF_ID)";
				$from_expression .= OBJECT_X_TABLE . $clId;
				$where_expression .= OBJECT_X_TABLE . "$clId.OF_ID !=0";
				$fe_count++;
			}
		} else{
			$classid = intval($classid);
			$count_expression = "COUNT(" . OBJECT_X_TABLE . "$classid.OF_ID)";
			$from_expression = OBJECT_X_TABLE . $classid;
			$where_expression = OBJECT_X_TABLE . "$classid.OF_ID !=0";
		}
		$DB_WE->query("SELECT $count_expression as dbEntries FROM $from_expression WHERE $where_expression");
		while($DB_WE->next_record()) {	 // Pager: determine the number of records;
			$entries += $DB_WE->f("dbEntries");
		}
		$active_page = !empty($_GET['page']) ? $_GET['page'] : 0; // Pager: determine the current page
		$docType2 = isset($docType2) ? $docType2 = "objectFile" : $docType2 = "objectFile"; // Pager: determine the current page
		$typeAlias = isset($typeAlias) ? $typeAlias = "object" : $typeAlias = "object"; // Pager: determine the current page
		if(!isset($classSelectTable)){
			$classSelectTable = "";
		}
		if($entries != 0){	// Pager: Number of records not empty?
			$topInfo = ($entries > 0) ? $entries : g_l('modules_shop', '[noRecord]');

			$classid = abs($_REQUEST["ViewClass"]); // gets the value from the selectbox;

			$classSelectTable .= '<table cellpadding="2" cellspacing="0" width="600" border="0">
    <tr>
        <td colspan="2" class="defaultfont">' . $selClass . '</td>
    </tr>
</table>
';
			array_push($parts, array(
				'html' => $classSelectTable,
				'space' => 0
				)
			);

			// :: then do the query for objects
			$queryCondition = OBJECT_X_TABLE . "$classid.OF_ID = " . OBJECT_FILES_TABLE . ".ID AND " . OBJECT_X_TABLE . "$classid.ID = " . OBJECT_FILES_TABLE . ".ObjectID";
			$queryFrom = OBJECT_X_TABLE . "$classid," . OBJECT_FILES_TABLE . " ";
			$queryObjects = "SELECT " . OBJECT_X_TABLE . "$classid.input_shoptitle as obTitle," . OBJECT_X_TABLE . "$classid.OF_ID as obID," . OBJECT_FILES_TABLE . ".CreationDate as cDate," . OBJECT_FILES_TABLE . ".Published as cPub," . OBJECT_FILES_TABLE . ".ModDate  as cMob
                    FROM " . $queryFrom . "
                    WHERE " . $queryCondition . "
                    ORDER BY obID";
			$DB_WE->query($queryObjects);	// get the shop-objects from DB;
			// build the table
			$nr = 0;
			$orderRows = array();

			while($DB_WE->next_record()) {

				// for the articlelist, we need also all these article, so sve them in array

				$orderRows[$nr]['articleArray'] = unserialize($DB_WE->f('strSerial'));

				// initialize all data saved for an article
				$shopArticleObject = $orderRows[$nr]['articleArray'];



				// save all data in array
				$orderRows[$nr]['obTitle'] = $DB_WE->f('obTitle'); // also for ordering
				$orderRows[$nr]['obID'] = $DB_WE->f('obID');		// also for ordering
				$orderRows[$nr]['cDate'] = $DB_WE->f('cDate');	 // also for ordering
				$orderRows[$nr]['cPub'] = $DB_WE->f('cPub');		// also for ordering
				$orderRows[$nr]['cMob'] = $DB_WE->f('cMob');		// also for ordering
				//$orderRows[$nr]['type'] = "Objekt";       // also for ordering

				$orderRows[$nr]['orderArray'] = array();
				$nr++;
			}

			// build the headline
			$headline[0]["dat"] = getTitleLinkObj(g_l('modules_shop', '[ArtName]'), 'obTitle');
			$headline[1]["dat"] = getTitleLinkObj(g_l('modules_shop', '[ArtID]'), 'obID');
			//$headline[2]["dat"] = getTitleLinkObj(g_l('modules_shop','[docType]'), $typeAlias);
			$headline[2]["dat"] = getTitleLinkObj(g_l('modules_shop', '[artCreate]'), 'cDate');
			$headline[3]["dat"] = getTitleLinkObj(g_l('modules_shop', '[artPub]'), 'cPub');
			$headline[4]["dat"] = getTitleLinkObj(g_l('modules_shop', '[artMod]'), 'cMob');

			// we need functionalitty to order these

			if(isset($_REQUEST['orderBy']) && $_REQUEST['orderBy']){
				usort($orderRows, 'orderBy');
			}

			if(!isset($content)){
				$content = array();
			}

			for($nr = 0, $i = ($actPage * $nrOfPage); $i < sizeof($orderRows) && $i < ($actPage * $nrOfPage + $nrOfPage); $i++, $nr++){
				$isPublished = $orderRows[$i]['cPub'] > 0 ? true : false;
				$publishedStylePre = $isPublished ? "" : '<span style="color: red">';
				$publishedStylePost = $isPublished ? "" : "</span>";
				$publishedLinkStyle = $isPublished ? "" : ' style="color: red"';

				$content[$nr][0]['dat'] = '<a href="javascript:top.opener.top.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\' ,\'' . $orderRows[$i]['obID'] . '\',\'' . $docType2 . '\');");"' . $publishedLinkStyle . '>' . substr($orderRows[$i]['obTitle'], 0, 25) . ".." . '</a>';
				$content[$nr][1]['dat'] = $publishedStylePre . $orderRows[$i]['obID'] . $publishedStylePost;
				//$content[$nr][2]['dat'] = $orderRows[$i]['type'];
				$content[$nr][2]['dat'] = $publishedStylePre . ($orderRows[$i]['cDate'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['cDate']) : "") . $publishedStylePost;
				$content[$nr][3]['dat'] = $orderRows[$i]['cPub'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['cPub']) : "";
				$content[$nr][4]['dat'] = $publishedStylePre . ($orderRows[$i]['cMob'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['cMob']) : "") . $publishedStylePost;
			}

			array_push($parts, array(
				'html' => we_html_tools::htmlDialogBorder3(670, 100, $content, $headline),
				'space' => 0,
				'noline' => true
				)
			);

			// now the pager class at last:
			// Pager: Zweite Linkliste zeigen

			$pager = blaettern::getStandardPagerHTML(getPagerLinkObj(), $actPage, $nrOfPage, count($orderRows));

			array_push($parts, array(
				'html' => $pager,
				'space' => 0
				)
			);


			print we_multiIconBox::getHTML("revenues", "100%", $parts, 30, "", -1, "", "", false, sprintf(g_l('tabs', '[module][artList]'), $topInfo));
		} else{ // if there is an empty result form the object table
			$parts = array();

			$out = '<table cellpadding="2" cellspacing="0" width="100%" border="0">'
				. '<tr><td class="defaultfont">' . g_l('modules_shop', '[noRecordAlert]') . '</td></tr>'
				. '<tr><td class="defaultfont">' . we_button::create_button("image:btn_shop_pref", "javascript:top.opener.top.we_cmd('pref_shop')", true, -1, -1, "", "", !we_hasPerm("NEW_USER")) . '</td></tr>'
				. '</table>';

			array_push($parts, array(
				'html' => $out,
				'space' => 0
				)
			);


			print we_multiIconBox::getHTML("revenues", "100%", $parts, 30, "", -1, "", "", false, sprintf(g_l('tabs', '[module][artList]'), g_l('modules_shop', '[noRecord]')));
		}

		/*		 * ******** END PROCESS THE OUTPUT IF OPTED FOR AN OBJECT *********** */


		/*		 * ******** START PROCESS THE OUTPUT IF OPTED FOR A DOCUMENT *********** */
	} elseif($_REQUEST['typ'] == "document"){ //start output doc
		$orderBy = isset($_REQUEST['orderBy']) ? $_REQUEST['orderBy'] : 'sql';
		$DB_WE->query("SELECT count(Name) as Anzahl FROM " . LINK_TABLE . " WHERE Name ='" . $DB_WE->escape($dbTitlename) . "'");
		while($DB_WE->next_record()) {			 // Pager: determine the number of records;
			$entries = $DB_WE->f("Anzahl");
		}
		$active_page = !empty($_GET['page']) ? $_GET['page'] : 0; // Pager: determine the number of records;
		$docType = isset($docType) ? $docType = "text/webedition" : $docType = "text/webedition"; // Pager: determine the current page
		$typeAlias = isset($typeAlias) ? $typeAlias = "document" : $typeAlias = "document"; // Pager: determine the current page

		if($entries != 0){ // Pager: Number of records not empty?
			$topInfo = ($entries > 0) ? $entries : g_l('modules_shop', '[noRecord]');
			// :: then do the query for documents
			$queryCondition = FILE_TABLE . ".ID = " . LINK_TABLE . ".DID AND " . LINK_TABLE . ".CID = " . CONTENT_TABLE . ".ID AND " . LINK_TABLE . ".Name = \"" . $dbTitlename . "\" ";
			$queryFrom = CONTENT_TABLE . ", " . LINK_TABLE . "," . FILE_TABLE . " ";
			$queryDocuments = "SELECT " . CONTENT_TABLE . ".dat as sqlDat, " . LINK_TABLE . ".DID as dd, " . FILE_TABLE . ".CreationDate as dDate," . FILE_TABLE . ".Published as dPub," . FILE_TABLE . ".ModDate as dMod
            FROM " . $queryFrom . "
            WHERE " . $queryCondition . "
            ORDER BY dd";

			$DB_WE->query($queryDocuments);	// get the shop-documents from DB;
			//print $queryDocuments;
			// build the table
			$nr = 0;
			$orderRows = array();
			while($DB_WE->next_record()) {

				// for the articlelist, we need also all these article, so sve them in array

				$orderRows[$nr]['articleArray'] = unserialize($DB_WE->f('strSerial'));

				// initialize all data saved for an article
				$shopArticleObject = $orderRows[$nr]['articleArray'];


				// save all data in array
				$orderRows[$nr]['sql'] = $DB_WE->f('sqlDat'); // also for ordering
				$orderRows[$nr]['dd'] = $DB_WE->f('dd');		// also for ordering
				$orderRows[$nr]['dDate'] = $DB_WE->f('dDate');	 // also for ordering
				$orderRows[$nr]['dPub'] = $DB_WE->f('dPub');		// also for ordering
				$orderRows[$nr]['dMod'] = $DB_WE->f('dMod');		// also for ordering
				//$orderRows[$nr]['type'] = "Doc";       // also for ordering

				$orderRows[$nr]['orderArray'] = array();
				$nr++;
			}
			$typeAlias = "document";
			// build the headline
			$headline[0]["dat"] = getTitleLinkDoc(g_l('modules_shop', '[ArtName]'), 'sql');
			$headline[1]["dat"] = getTitleLinkDoc(g_l('modules_shop', '[ArtID]'), 'dd');
			//$headline[2]["dat"] = getTitleLinkDoc(g_l('modules_shop','[docType]'), $typeAlias);
			$headline[2]["dat"] = getTitleLinkDoc(g_l('modules_shop', '[artCreate]'), 'dDate');
			$headline[3]["dat"] = getTitleLinkDoc(g_l('modules_shop', '[artPub]'), 'dPub');
			$headline[4]["dat"] = getTitleLinkDoc(g_l('modules_shop', '[artMod]'), 'dMod');

			// we need functionalitty to order these

			if(isset($_REQUEST['orderBy']) && $_REQUEST['orderBy']){
				usort($orderRows, 'orderBy');
			}

			for($nr = 0, $i = ($actPage * $nrOfPage); $i < sizeof($orderRows) && $i < ($actPage * $nrOfPage + $nrOfPage); $i++, $nr++){

				$isPublished = $orderRows[$i]['dPub'] > 0 ? true : false;
				$publishedStylePre = $isPublished ? "" : '<span style="color: red">';
				$publishedStylePost = $isPublished ? "" : "</span>";
				$publishedLinkStyle = $isPublished ? "" : ' style="color: red"';
				$content[$nr][0]['dat'] = $publishedStylePre . ('<a href="javascript:top.opener.top.weEditorFrameController.openDocument(\'' . FILE_TABLE . '\' ,\'' . $orderRows[$i]['dd'] . '\',\'' . $docType . '\');");"' . $publishedLinkStyle . '>' . substr($orderRows[$i]['sql'], 0, 25) . ".." . '</a>') . $publishedStylePost;
				$content[$nr][1]['dat'] = $publishedStylePre . ($orderRows[$i]['dd']);
				//$content[$nr][2]['dat'] = $orderRows[$i]['type'];
				$content[$nr][2]['dat'] = $publishedStylePre . ($orderRows[$i]['dDate'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['dDate']) : "") . $publishedStylePost;
				$content[$nr][3]['dat'] = $orderRows[$i]['dPub'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['dPub']) : "";
				$content[$nr][4]['dat'] = $publishedStylePre . ($orderRows[$i]['dMod'] > 0 ? date("d.m.Y - H:m:s", $orderRows[$i]['dMod']) : "") . $publishedStylePost;
			}
			if(!isset($content))
				$content = array();
			array_push($parts, array(
				'html' => we_html_tools::htmlDialogBorder3(670, 100, $content, $headline),
				'space' => 0,
				'noline' => true
				)
			);

			$pager = blaettern::getStandardPagerHTML(getPagerLinkDoc(), $actPage, $nrOfPage, count($orderRows));


			array_push($parts, array(
				'html' => $pager,
				'space' => 0
				)
			);

			print we_multiIconBox::getHTML("revenues", "100%", $parts, 30, "", -1, "", "", false, sprintf(g_l('tabs', '[module][artList]'), $topInfo));
		}

		/*		 * ******** END PROCESS THE OUTPUT IF OPTED FOR A DOCUMENT *********** */
	}else{

		print"	Die von Ihnen gew�nschte Seite kann nicht angezeigt werden!"; //if ($_REQUEST['typ'] == "doc")
	}
}
?>
</body>
</html>
