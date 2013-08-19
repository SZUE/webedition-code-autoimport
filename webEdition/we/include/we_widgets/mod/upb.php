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
// widget UNPUBLISHED
we_html_tools::protect();
$bTypeDoc = (bool) $aProps[3]{0};
$bTypeObj = (bool) $aProps[3]{1};
$_objectFilesTable = defined("OBJECT_FILES_TABLE") ? OBJECT_FILES_TABLE : "";
$numRows = 25;

$tbls = array();
if($bTypeDoc && $bTypeObj){
	if(defined("FILE_TABLE")){
		$tbls[] = FILE_TABLE;
	}
	if(defined("OBJECT_FILES_TABLE") && we_hasPerm("CAN_SEE_OBJECTFILES")){
		$tbls[] = OBJECT_FILES_TABLE;
	}
} else {
	if($bTypeDoc && defined("FILE_TABLE")){
		$tbls[] = FILE_TABLE;
	}
	if($bTypeObj && defined("OBJECT_FILES_TABLE")){
		$tbls[] = OBJECT_FILES_TABLE;
	}
}

$_cont = array();
foreach($tbls as $table){
	$wfDocsCSV = "";
	$myWfDocsCSV = "";
	if(defined("WORKFLOW_TABLE")){
		$myWfDocsArray = we_workflow_utility::getWorkflowDocsForUser(
				$_SESSION["user"]["ID"], $table, we_hasPerm("ADMINISTRATOR"), we_hasPerm("PUBLISH"), ($table == $_objectFilesTable) ? "" : get_ws($table));
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
		if(($ws = get_ws($table))){
			$wsArr = makeArrayFromCSV($ws);
			foreach($wsArr as $i){
				$parents[] = $i;
				$childs[] = $i;
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

	while($DB_WE->next_record()){
		$row = array();
		$_cont[$DB_WE->f("ModDate")] = $path = '<tr><td width="20" height="20" valign="middle" nowrap><img src="' . ICON_DIR . $DB_WE->f(
				"Icon") . '" width="16" height="18" />' . we_html_tools::getPixel(4, 1) . '</td><td valign="middle" class="middlefont"><nobr><a href="javascript:top.weEditorFrameController.openDocument(\'' . $table . '\',\'' . $DB_WE->f(
				"ID") . '\',\'' . $DB_WE->f("ContentType") . '\')" title="' . $DB_WE->f("Path") . '" style="color:' . ($DB_WE->f(
				"Published") ? "#3366CC" : "#FF0000") . ';text-decoration:none;">' . $DB_WE->f("Path") . '</a></nobr></td></tr>';
		$row[] = array(
			"dat" => $path
		);
		$usern = f("SELECT username FROM " . USER_TABLE . " WHERE ID=" . intval($DB_WE->f("CreatorID")), "username", $db2);
		$usern = $usern ? $usern : "-";
		$row[] = array(
			"dat" => $usern
		);

		$foo = $DB_WE->f("CreationDate") ? date(g_l('date', '[format][default]'), $DB_WE->f("CreationDate")) : "-";
		$row[] = array("dat" => $foo);
		$usern = f("
				SELECT username
				FROM " . USER_TABLE . "
				WHERE ID=" . intval($DB_WE->f("ModifierID")), "username", $db2);
		$usern = $usern ? $usern : "-";
		$row[] = array("dat" => $usern);

		$foo = $DB_WE->f("ModDate") ? date(g_l('date', '[format][default]'), $DB_WE->f("ModDate")) : "-";
		$row[] = array("dat" => $foo);
		$foo = $DB_WE->f("Published") ? date(g_l('date', '[format][default]'), $DB_WE->f("Published")) : "-";
		$row[] = array("dat" => $foo);
		if(defined("WORKFLOW_TABLE"))
			if($DB_WE->f("wforder")){
				$step = we_workflow_utility::findLastActiveStep($DB_WE->f("ID"), $table) + 1;
				$steps = count(we_workflow_utility::getNumberOfSteps($DB_WE->f("ID"), $table));
				$text = "$step&nbsp;" . g_l('resave', '[of]') . "&nbsp;$steps";
				$text .= '&nbsp;<img src="' . IMAGE_DIR . 'we_boebbel_' . ($DB_WE->f("mywforder") ? 'blau' : 'grau') . '.gif" align="absmiddle" />';
				$row[] = array("dat" => $text);
			} else {
				$row[] = array("dat" => "-");
			}
		$content[] = $row;
	}
}

$ct = "";
$ct .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
asort($_cont);
reset($_cont);
foreach($_cont as $k => $v){
	$ct .= $v . "\n";
}
$ct .= '</table>';
