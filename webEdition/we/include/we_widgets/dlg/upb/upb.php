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
we_html_tools::protect();

$bTypeDoc = (bool) $_REQUEST['we_cmd'][0]{0};
$bTypeObj = (bool) $_REQUEST['we_cmd'][0]{1};
$sTb = ($bTypeDoc && $bTypeObj) ? g_l('cockpit', "[upb_docs_and_objs]") : (($bTypeDoc) ? g_l('cockpit', "[upb_docs]") : (($bTypeObj) ? g_l('cockpit', "[upb_objs]") : g_l('cockpit', "[upb_docs_and_objs]")));

$jsCode = "
var _sObjId='" . $_REQUEST['we_cmd'][5] . "';
var _sType='upb';
var _sTb='" . $sTb . "';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}
";

$_objectFilesTable = defined("OBJECT_FILES_TABLE") ? OBJECT_FILES_TABLE : "";
$numRows = 25;

$tbls = array();
if($bTypeDoc && $bTypeObj){
	$tbls[] = FILE_TABLE;
	$tbls[] = OBJECT_FILES_TABLE;
} else{
	$tbls[] = ($bTypeDoc) ? FILE_TABLE : (($bTypeObj) ? OBJECT_FILES_TABLE : "");
}

$_cont = array();
foreach($tbls as $table){
	$wfDocsCSV = "";
	$myWfDocsCSV = "";
	if(defined("WORKFLOW_TABLE")){
		$myWfDocsArray = we_workflow_utility::getWorkflowDocsForUser(
				$_SESSION["user"]["ID"], $table, $_SESSION["perms"]["ADMINISTRATOR"], $_SESSION["perms"]["PUBLISH"], ($table == $_objectFilesTable) ? "" : get_ws($table));
		$myWfDocsCSV = makeCSVFromArray($myWfDocsArray);
		$wfDocsArray = we_workflow_utility::getAllWorkflowDocs($table);
		$wfDocsCSV = makeCSVFromArray($wfDocsArray);
	}

	$offset = isset($_REQUEST["offset"]) ? $_REQUEST["offset"] : 0;
	$order = isset($_REQUEST["order"]) ? $_REQUEST["order"] : "ModDate DESC";

	#### get workspace query ###


	$parents = array();
	$childs = array();
	$parentlist = "";
	$childlist = "";
	$wsQuery = "";

	if($table == FILE_TABLE)
		if($ws = get_ws($table)){
			$wsArr = makeArrayFromCSV($ws);
			foreach($wsArr as $i){
				array_push($parents, $i);
				array_push($childs, $i);
				we_readParents($i, $parents, $table);
				we_readChilds($i, $childs, $table);
			}
			$childlist = makeCSVFromArray($childs);
			$parentlist = makeCSVFromArray($parents);
			if($parentlist)
				$wsQuery = " ID IN(" . $parentlist . ") ";
			if($parentlist && $childlist)
				$wsQuery .= " OR ";
			if($childlist)
				$wsQuery .= " ParentID IN(" . $childlist . ") ";
			if($wsQuery)
				$wsQuery = " AND (" . $wsQuery . ") ";
		}

	#####


	$q = "
			SELECT " . ($wfDocsCSV ? "(ID IN($wfDocsCSV)) AS wforder," : "") . " " . ($myWfDocsCSV ? "(ID IN($myWfDocsCSV)) AS mywforder," : "") . " ContentType,ID,Text,ParentID,Path,Published,ModDate,CreationDate,ModifierID,CreatorID,Icon
			FROM " . $DB_WE->escape($table) . "
			WHERE (((Published=0 OR Published < ModDate) AND (ContentType='text/webedition' OR ContentType='text/html' OR ContentType='objectFile'))" . ($myWfDocsCSV ? " OR (ID IN($myWfDocsCSV)) " : "") . ") $wsQuery
			ORDER BY " . ($myWfDocsCSV ? "mywforder DESC," : "") . " $order";

	$DB_WE->query($q);
	$anz = $DB_WE->num_rows();
	$DB_WE->query($q . " LIMIT " . intval($offset) . "," . intval($numRows));
	$db2 = new DB_WE();
	$content = array();

	while($DB_WE->next_record()) {
		$row = array();
		$_cont[$DB_WE->f("ModDate")] = $path = '<tr><td width="20" height="20" valign="middle" nowrap><img src="' . ICON_DIR . $DB_WE->f(
				"Icon") . '" />' . we_html_tools::getPixel(4, 1) . '</td><td valign="middle" class="middlefont"><nobr><a href="javascript:top.weEditorFrameController.openDocument(\'' . $table . '\',\'' . $DB_WE->f(
				"ID") . '\',\'' . $DB_WE->f("ContentType") . '\')" title="' . $DB_WE->f("Path") . '" style="color:' . ($DB_WE->f(
				"Published") ? "#3366CC" : "#FF0000") . ';text-decoration:none;">' . $DB_WE->f("Path") . '</a></nobr></td></tr>';
		array_push($row, array(
			"dat" => $path
		));
		$usern = f("
				SELECT username
				FROM " . USER_TABLE . "
				WHERE ID=" . intval($DB_WE->f("CreatorID")), "username", $db2);
		$usern = $usern ? $usern : "-";
		array_push($row, array(
			"dat" => $usern
		));

		$foo = $DB_WE->f("CreationDate") ? date(g_l('date', '[format][default]'), $DB_WE->f("CreationDate")) : "-";
		array_push($row, array(
			"dat" => $foo
		));
		$usern = f("
				SELECT username
				FROM " . USER_TABLE . "
				WHERE ID=" . intval($DB_WE->f("ModifierID")), "username", $db2);
		$usern = $usern ? $usern : "-";
		array_push($row, array(
			"dat" => $usern
		));

		$foo = $DB_WE->f("ModDate") ? date(g_l('date', '[format][default]'), $DB_WE->f("ModDate")) : "-";
		array_push($row, array(
			"dat" => $foo
		));
		$foo = $DB_WE->f("Published") ? date(g_l('date', '[format][default]'), $DB_WE->f("Published")) : "-";
		array_push($row, array(
			"dat" => $foo
		));
		if(defined("WORKFLOW_TABLE"))
			if($DB_WE->f("wforder")){
				$step = we_workflow_utility::findLastActiveStep($DB_WE->f("ID"), $table) + 1;
				$steps = count(we_workflow_utility::getNumberOfSteps($DB_WE->f("ID"), $table));
				$text = "$step&nbsp;" . g_l('resave', '[of]') . "&nbsp;$steps";
				if($DB_WE->f("mywforder"))
					$text .= '&nbsp;<img src="' . IMAGE_DIR . 'we_boebbel_blau.gif" align="absmiddle" />';
				else
					$text .= '&nbsp;<img src="' . IMAGE_DIR . 'we_boebbel_grau.gif" align="absmiddle" />';
				array_push($row, array(
					"dat" => $text
				));
			} else
				array_push($row, array(
					"dat" => "-"
				));
		array_push($content, $row);
	}
}

$ct = "";
$ct .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
asort($_cont);
reset($_cont);
foreach($_cont as $k => $v){
	$ct .= $v . "\n";
}
$ct .= "</table>\n";

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[unpublished]')) . STYLESHEET . we_html_element::jsElement(
				$jsCode)) . we_html_element::htmlBody(
			array(
			"marginwidth" => "15",
			"marginheight" => "10",
			"leftmargin" => "15",
			"topmargin" => "10",
			"onload" => "if(parent!=self)init();"
			), we_html_element::htmlDiv(array(
				"id" => "upb"
				), $ct)));