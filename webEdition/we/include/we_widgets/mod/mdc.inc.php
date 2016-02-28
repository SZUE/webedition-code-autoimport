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
// widget MY DOCUMENTS

if(!isset($aProps)){//preview requested
	$all = explode(';', we_base_request::_(we_base_request::RAW_CHECKED, 'we_cmd', '', 1));
	list($dir, $dt_tid, $cats) = (count($all) > 1 ?
			$all :
			array($all[0], '', ''));

	$aCsv = array(
		0, //unused - compatibility
		we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
		$dir,
		$dt_tid,
		$cats
	);
}

$mdc = "";
//$ct["image"] = true;
if(!isset($aCsv)){
	$aCsv = explode(';', $aProps[3]);
}
if($aCsv && count($aCsv) == 3){
	$_binary = $aCsv[1];
	$_csv = $aCsv[2];
	$_table = ($_binary{1}) ? OBJECT_FILES_TABLE : FILE_TABLE;
} else {
	$_csv = '';
}

if($_csv){
	if($_binary{0}){
		$_ids = explode(',', $_csv);
		$_paths = makeArrayFromCSV(id_to_path($_ids, $_table));
		$_where = array();
		foreach($_paths as $_path){
			$_where[] = 'Path LIKE "' . $_path . '%" ';
		}
		$_query = ($_where ?
				'SELECT ID,Path,Text,ContentType FROM ' . $GLOBALS['DB_WE']->escape($_table) . ' WHERE (' . implode(' OR ', $_where) . ') AND IsFolder=0' :
				false);
	} else {
		list($folderID, $folderPath) = explode(",", $_csv);
		$q_path = 'Path LIKE "' . $folderPath . '%"';
		$q_dtTid = ($aCsv[3] != 0) ? (!$_binary{1} ? 'DocType' : 'TableID') . '="' . $aCsv[3] . '"' : '';
		if($aCsv[4] != ""){
			$_cats = explode(",", $aCsv[4]);
			$_categories = array();
			foreach($_cats as $_myCat){
				$_id = f('SELECT ID FROM ' . CATEGORY_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape(base64_decode($_myCat)) . '"', 'ID', $GLOBALS['DB_WE']);
				$_categories[] = 'Category LIKE ",' . intval($_id) . ',"';
			}
		}
		$_query = 'SELECT ID,Path,Text,ContentType FROM ' . $GLOBALS['DB_WE']->escape($_table) . ' WHERE ' . $q_path . (($q_dtTid) ? ' AND ' . $q_dtTid : '') . ((isset(
				$_categories)) ? ' AND (' . implode(' OR ', $_categories) . ')' : '') . ' AND IsFolder=0;';
	}

	if($_query && $DB_WE->query($_query)){
		$mdc .= '<table class="default">';
		while($DB_WE->next_record()){
			$mdc .= '<tr><td class="mdcIcon" data-contenttype="' . $DB_WE->f('ContentType') . '"></td><td style="vertical-align:middle" class="middlefont">' . we_html_element::htmlA(
					array(
					"href" => 'javascript:WE().layout.weEditorFrameController.openDocument(\'' . $_table . '\',\'' . $DB_WE->f('ID') . '\',\'' . $DB_WE->f('ContentType') . '\');',
					"title" => $DB_WE->f("Path"),
					"style" => "color:#000000;text-decoration:none;"
					), $DB_WE->f("Path")) . '</td></tr>';
		}
		$mdc .= '</table>';
	}
}

if(!isset($aProps)){//preview requested
	$cmd4 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 4);

	$js = "
var _sObjId='" . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5) . "';
var _sType='mdc';
var _sTb='" . ($cmd4 ? : g_l('cockpit', (($_binary{1} ? '[my_objects]' : '[my_documents]')))) . "';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}";

	echo we_html_tools::getHtmlTop(g_l('cockpit', '[my_documents]'), '', '', STYLESHEET .
		we_html_element::jsElement($js), we_html_element::htmlBody(
			array(
			'style' => 'margin:10px 15px;',
			"onload" => "if(parent!=self){init();}WE().util.setIconOfDocClass(document,'mdcIcon');"
			), we_html_element::htmlDiv(array(
				"id" => "mdc"
				), $mdc)));
}