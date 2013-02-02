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
we_html_tools::protect();

if(isset($_REQUEST['we_cmd'][5])){
	$_SESSION["prefs"]["FileFilter"] = $_REQUEST['we_cmd'][5];
}

$table = $_REQUEST['we_cmd'][1];
$parentFolder = isset($_REQUEST['we_cmd'][2]) ? $_REQUEST['we_cmd'][2] : 0;
$offset = isset($_REQUEST['we_cmd'][6]) ? $_REQUEST['we_cmd'][6] : 0;

//p_r($_REQUEST);


if(isset($_REQUEST['we_cmd'][0]) && $_REQUEST['we_cmd'][0] == "closeFolder"){
	$table = $_REQUEST['we_cmd'][1];
	$parentFolder = isset($_REQUEST['we_cmd'][2]) ? $_REQUEST['we_cmd'][2] : 0;
	$openDirs = array_flip(makeArrayFromCSV($_SESSION["prefs"]["openFolders_" . stripTblPrefix($table)]));
	new_array_splice($openDirs, $parentFolder, 1);
	$openDirs = array_keys($openDirs);
	$_SESSION["prefs"]["openFolders_" . stripTblPrefix($table)] = makeCSVFromArray($openDirs);
} else{
	$GLOBALS["OBJECT_FILES_TREE_COUNT"] = defined("OBJECT_FILES_TREE_COUNT") ? OBJECT_FILES_TREE_COUNT : 20;

	$counts = array();
	$parents = array();
	$childs = array();
	$parentlist = "";
	$childlist = "";
	$wsQuery = "";

	$parentpaths = array();

	function getQueryParents($path){
		$out = "";
		while($path != "/" && $path != "\\" && $path) {
			$out .= "Path='$path' OR ";
			$path = dirname($path);
		}
		if($out){
			return substr($out, 0, strlen($out) - 3);
		} else{
			return "";
		}
	}

	function getItems($ParentID, $offset = 0, $segment = 0){
		global $table, $openFolders, $parentpaths, $wsQuery, $treeItems;

		if(($table == TEMPLATES_TABLE && !we_hasPerm("CAN_SEE_TEMPLATES")) || ($table == FILE_TABLE && !we_hasPerm("CAN_SEE_DOCUMENTS"))){
			return 0;
		}
		$prevoffset = $offset - $segment;
		$prevoffset = ($prevoffset < 0) ? 0 : $prevoffset;
		if($offset && $segment){
			$treeItems[] = array(
				"icon" => "arrowup.gif",
				"id" => "prev_" . $ParentID,
				"parentid" => $ParentID,
				"text" => "display (" . $prevoffset . "-" . $offset . ")",
				"contenttype" => "arrowup",
				"isclassfolder" => 0,
				"isnoteditable" => 0,
				"table" => $table,
				"checked" => 0,
				"typ" => "threedots",
				"open" => 0,
				"published" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $prevoffset
			);
		}
		$DB_WE = new DB_WE();
		$tmp = array_filter($openFolders);
		$tmp[] = $ParentID;
		$where = ' WHERE  ID!=' . intval($ParentID) . ' AND ParentID IN(' . implode(',', $tmp) . ') AND ((1' . makeOwnersSql() . ') ' . $wsQuery . ')';

		$elem = "ID,ParentID,Path,Text,IsFolder,Icon,ModDate" . (($table == FILE_TABLE || (defined(
				"OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE)) ? ",Published" : "") . ((defined(
				"OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE) ? ",IsClassFolder,IsNotEditable" : "");

		if($table == FILE_TABLE || $table == TEMPLATES_TABLE){
			$elem .= ",Extension";
		}

		$tree_count = 0;
		if($table == FILE_TABLE || $table == TEMPLATES_TABLE || (defined("OBJECT_TABLE") && $table == OBJECT_TABLE) || (defined(
				"OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE)){
			$elem .= ",ContentType";
		}

		$DB_WE->query('SELECT ' . $elem . ', LOWER(Text) AS lowtext, ABS(REPLACE(Text,"info","")) AS Nr, (Text REGEXP "^[0-9]") AS isNr FROM ' . $table . ' ' . $where . ' ORDER BY IsFolder DESC,isNr DESC,Nr,lowtext' . ($segment != 0 ? ' LIMIT ' . $offset . ',' . $segment : ''));
		$ct = we_base_ContentTypes::inst();

		while($DB_WE->next_record()) {
			$tree_count++;
			$ID = $DB_WE->f("ID");
			$ParentID = $DB_WE->f("ParentID");
			$Text = $DB_WE->f("Text");
			$Path = $DB_WE->f("Path");
			$IsFolder = $DB_WE->f("IsFolder");
			$ContentType = $DB_WE->f("ContentType");
			$Icon = $ct->getIcon($ContentType, we_base_ContentTypes::LINK_ICON, $DB_WE->f("Extension"));
			$published = ($table == FILE_TABLE || (defined("OBJECT_FILES_TABLE") && ($table == OBJECT_FILES_TABLE))) ? ((($DB_WE->f(
					"Published") != 0) && ($DB_WE->f("Published") < $DB_WE->f("ModDate"))) ? -1 : $DB_WE->f(
						"Published")) : 1;
			$IsClassFolder = $DB_WE->f("IsClassFolder");
			$IsNotEditable = $DB_WE->f("IsNotEditable");

			$OpenCloseStatus = (in_array($ID, $openFolders) ? 1 : 0);
			$disabled = in_array($Path, $parentpaths) ? 1 : 0;

			$typ = $IsFolder ? "group" : "item";

			$treeItems[] = array(
				"icon" => $Icon,
				"id" => "$ID",
				"parentid" => $ParentID,
				"text" => "$Text",
				"contenttype" => $ContentType,
				"isclassfolder" => $IsClassFolder,
				"isnoteditable" => $IsNotEditable,
				"table" => $table,
				"checked" => 0,
				"typ" => $typ,
				"open" => $OpenCloseStatus,
				"published" => $published,
				"disabled" => $disabled,
				"tooltip" => $ID,
				"offset" => $offset
			);

			/* if($typ == "group" && $OpenCloseStatus == 1){
			  getItems($ID, 0, $segment);
			  } */
		}
		$total = f('SELECT COUNT(1) as total FROM ' . $table . ' ' . $where, 'total', $DB_WE);
		$nextoffset = $offset + $segment;
		if($segment && $total > $nextoffset){
			$treeItems[] = array(
				"icon" => "arrowdown.gif",
				"id" => "next_" . $ParentID,
				"parentid" => $ParentID,
				"text" => "display (" . $nextoffset . "-" . ($nextoffset + $segment) . ")",
				"contenttype" => "arrowdown",
				"isclassfolder" => 0,
				"isnoteditable" => 0,
				"table" => $table,
				"checked" => 0,
				"typ" => "threedots",
				"open" => 0,
				"published" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $nextoffset
			);
		}
	}

	if(($ws = get_ws($table))){
		$wsPathArray = id_to_path($ws, $table, $DB_WE, false, true);

		foreach($wsPathArray as $path){
			$wsQuery .= " Path LIKE '" . $DB_WE->escape($path) . "/%' OR " . getQueryParents($path) . ' OR ';
			while($path != '/' && $path != '\\' && $path) {
				$parentpaths[] = $path;
				$path = dirname($path);
			}
		}
	} elseif(defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE && (!$_SESSION["perms"]["ADMINISTRATOR"])){
		$ac = getAllowedClasses($DB_WE);
		foreach($ac as $cid){
			$path = id_to_path($cid, OBJECT_TABLE);
			$wsQuery .= " Path LIKE '" . $DB_WE->escape($path) . "/%' OR Path='" . $DB_WE->escape($path) . "' OR ";
		}
	}

	$wsQuery = ($wsQuery ?
			' OR (' . substr($wsQuery, 0, strlen($wsQuery) - 3) . ') ' :
			' OR RestrictOwners=0 ');

	if(isset($_REQUEST['we_cmd'][3])){
		$openFolders = explode(',', $_REQUEST['we_cmd'][3]);
		$_SESSION["prefs"]["openFolders_" . stripTblPrefix($_REQUEST['we_cmd'][4])] = $_REQUEST['we_cmd'][3];
	}

	$openFolders = (isset($_SESSION["prefs"]["openFolders_" . stripTblPrefix($table)]) ?
			explode(',', $_SESSION["prefs"]["openFolders_" . stripTblPrefix($table)]) :
			array());


	if($parentFolder){
		if(!in_array($parentFolder, $openFolders)){
			$openFolders[] = $parentFolder;
			$_SESSION["prefs"]["openFolders_" . stripTblPrefix($table)] = implode(",", $openFolders);
		}
	}

	$js = '';
	if($_SESSION['weS']['we_mode'] != "seem"){
		$Tree = new weMainTree("webEdition.php", "top", "top.resize.left.tree", "top.load");

		$treeItems = array();

		getItems($parentFolder, $offset, $Tree->default_segment);

		$js = we_html_element::jsElement('
function loadTreeData(){
	if(!' . $Tree->topFrame . '.treeData) {
		window.setTimeout("loadTreeData()",500);
		return;
	}' .
				($parentFolder ? '' :
					$Tree->topFrame . '.treeData.clear();' .
					$Tree->topFrame . '.treeData.add(new ' . $Tree->topFrame . '.rootEntry(\'' . $parentFolder . '\',\'root\',\'root\',\'' . $offset . '\'));'
				) .
				$Tree->getJSLoadTree($treeItems) .'
	first=' . $Tree->topFrame . '.firstLoad;
	if(top.firstLoad){
		' . $Tree->topFrame . '.toggleBusy(0);
	}else{
		' . $Tree->topFrame . '.firstLoad = true;
	}
}
loadTreeData();');
	}

	print we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead(
				we_html_tools::getHtmlInnerHead('File-Tree') .
				$js
			) . we_html_element::htmlBody(array("bgcolor" => "white"))
		);
}
we_user::writePrefs($_SESSION["prefs"]["userID"], $GLOBALS['DB_WE']);
