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
			[$all[0], '', '']);

	$aCsv = [
		0, //unused - compatibility
		we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
		$dir,
		$dt_tid,
		$cats
	];
}

$mdc = "";
//$ct["image"] = true;
if(!isset($aCsv)){
	$aCsv = explode(';', $aProps[3]);
}
if($aCsv && count($aCsv) == 3){
	$binary = $aCsv[1];
	$csv = $aCsv[2];
	$table = ($binary{1}) ? OBJECT_FILES_TABLE : FILE_TABLE;
} else {
	$csv = '';
}

if($csv){
	if($binary{0}){
		$ids = explode(',', $csv);
		$paths = makeArrayFromCSV(id_to_path($ids, $table));
		$where = [];
		foreach($paths as $path){
			$where[] = 'Path LIKE "' . $path . '%" ';
		}
		$query = ($where ?
				'SELECT ID,Path,Text,ContentType FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE (' . implode(' OR ', $where) . ') AND IsFolder=0' :
				false);
	} else {
		list($folderID, $folderPath) = explode(",", $csv);
		$q_path = 'Path LIKE "' . $folderPath . '%"';
		$q_dtTid = ($aCsv[3] != 0) ? (!$binary{1} ? 'DocType' : 'TableID') . '="' . $aCsv[3] . '"' : '';
		if($aCsv[4] != ""){
			$cats = explode(",", $aCsv[4]);
			$categories = [];
			foreach($cats as $myCat){
				$id = f('SELECT ID FROM ' . CATEGORY_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape(base64_decode($myCat)) . '"', 'ID', $GLOBALS['DB_WE']);
				$categories[] = 'Category LIKE ",' . intval($id) . ',"';
			}
		}
		$query = 'SELECT ID,Path,Text,ContentType FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE ' . $q_path . (($q_dtTid) ? ' AND ' . $q_dtTid : '') . ((isset(
				$categories)) ? ' AND (' . implode(' OR ', $categories) . ')' : '') . ' AND IsFolder=0;';
	}

	if($query && $DB_WE->query($query)){
		$mdc .= '<table class="default">';
		while($DB_WE->next_record()){
			$mdc .= '<tr><td class="mdcIcon" data-contenttype="' . $DB_WE->f('ContentType') . '"></td><td style="vertical-align:middle" class="middlefont">' . we_html_element::htmlA([
					"href" => "javascript:WE().layout.weEditorFrameController.openDocument('" . $table . "','" . $DB_WE->f('ID') . "','" . $DB_WE->f('ContentType') . "');",
					"title" => $DB_WE->f("Path"),
					'style' => "color:#000000;text-decoration:none;"
					], $DB_WE->f("Path")) . '</td></tr>';
		}
		$mdc .= '</table>';
	}
}

if(!isset($aProps)){//preview requested
	$cmd4 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 4);

	$js = "
var _sObjId='" . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5) . "';
var _sType='mdc';
var _sTb='" . ($cmd4 ? : g_l('cockpit', (($binary{1} ? '[my_objects]' : '[my_documents]')))) . "';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}";

	echo we_html_tools::getHtmlTop(g_l('cockpit', '[my_documents]'), '', '', we_html_element::jsElement($js), we_html_element::htmlBody(
			[
			'style' => 'margin:10px 15px;',
			"onload" => "if(parent!=self){init();}WE().util.setIconOfDocClass(document,'mdcIcon');"
			], we_html_element::htmlDiv([
				"id" => "mdc"
				], $mdc)));
}