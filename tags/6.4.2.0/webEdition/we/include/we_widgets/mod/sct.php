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

we_html_tools::protect();
$aCols = explode(';', isset($aProps) ? $aProps[3] : we_base_request::_(we_base_request::STRINGC, 'we_cmd', '', 0));
$_disableNew = true;
$_cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "');";
if(permissionhandler::hasPerm("NEW_WEBEDITIONSITE")){
	if(permissionhandler::hasPerm("NO_DOCTYPE")){
		$_disableNew = false;
	} else {
		$dtq = we_docTypes::getDoctypeQuery($GLOBALS['DB_WE']);
		$id = f('SELECT dt.ID FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where'] . ' LIMIT 1');
		if($id){
			$_disableNew = false;
			$_cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','" . $id . "')";
		} else {
			$_disableNew = true;
		}
	}
} else {
	$_disableNew = true;
}

$_disableObjects = false;
if(defined('OBJECT_TABLE')){
	$allClasses = we_users_util::getAllowedClasses();
	if(empty($allClasses)){
		$_disableObjects = true;
	}
}

$js = array();

if(defined('FILE_TABLE') && permissionhandler::hasPerm("CAN_SEE_DOCUMENTS")){
	$js["open_document"] = "top.we_cmd('open_document');";
}
if(defined('FILE_TABLE') && permissionhandler::hasPerm("CAN_SEE_DOCUMENTS") && permissionhandler::hasPerm("CAN_SEE_PROPERTIES") && !$_disableNew){
	$js["new_document"] = $_cmdNew;
}
if(defined('TEMPLATES_TABLE') && permissionhandler::hasPerm("NEW_TEMPLATE") && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
	$js["new_template"] = "top.we_cmd('new','" . TEMPLATES_TABLE . "','','" . we_base_ContentTypes::TEMPLATE . "');";
}
if(permissionhandler::hasPerm("NEW_DOC_FOLDER")){
	$js["new_directory"] = "top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::FOLDER . "')";
}
if(defined('FILE_TABLE') && permissionhandler::hasPerm("CAN_SEE_DOCUMENTS")){
	$js["unpublished_pages"] = "top.we_cmd('openUnpublishedPages');";
}
if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES") && !$_disableObjects){
	$js["unpublished_objects"] = "top.we_cmd('openUnpublishedObjects');";
}
if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("NEW_OBJECTFILE") && permissionhandler::hasPerm("CAN_SEE_PROPERTIES") && !$_disableObjects){
	$js["new_object"] = "top.we_cmd('new_objectFile');";
}
if(defined('OBJECT_TABLE') && permissionhandler::hasPerm("NEW_OBJECT") && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
	$js["new_class"] = "top.we_cmd('new_object');";
}
if(permissionhandler::hasPerm("EDIT_SETTINGS")){
	$js["preferences"] = "top.we_cmd('openPreferences');";
}
if(permissionhandler::hasPerm('NEW_GRAFIK')){
	$js['btn_add_image'] = "top.we_cmd('new','tblFile','','image/*')";
}


$shortcuts = array();
foreach($aCols as $sCol){
	$shortcuts[] = explode(',', $sCol);
}

$sSctOut = '';
$_col = 0;

foreach($shortcuts as $sctCol){
	$sSctOut .= '<div class="sct_row" style="display: block; width: 100%; float: left;"><table border="0" cellpadding="0" cellspacing="0" width="100%">';
	$iCurrSctRow = 0;
	foreach($sctCol as $_label){
		if(isset($js[$_label])){
			$sSctOut .= '<tr><td width="34" height="34">' . we_html_element::htmlA(
					array(
					"href" => "javascript:" . $js[$_label]
					), we_html_element::htmlImg(
						array(
							"src" => IMAGE_DIR . 'pd/sct/' . $_label . '.gif',
							"width" => 34,
							"height" => 34,
							"border" => 0
				))) . '</td>';
			$sSctOut .= '<td width="5">' . we_html_tools::getPixel(5, 1) . '</td>';
			$sSctOut .= '<td valign="middle">' . we_html_element::htmlA(
					array(
					"href" => "javascript:" . $js[$_label],
					"class" => "middlefont",
					"style" => "font-weight:bold;text-decoration:none;"
					), g_l('button', '[' . $_label . '][value]')) . '</td></tr>';
			$sSctOut .= '<tr><td height="3">' . we_html_tools::getPixel(1, 3) . '</td></tr>';
		}
		$iCurrSctRow++;
	}
	$sSctOut .= '</table></div>';
	$_col++;
}

$sc = new we_html_table(array("width" => "100%", "border" => 0, "cellpadding" => 0, "cellspacing" => 0), 1, 1);
$sc->setCol(0, 0, array("align" => "center", "valign" => "top"), $sSctOut);

if(!isset($aProps)){
	we_html_tools::protect();

	$sJsCode = "
	var _sObjId='" . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5) . "';
	var _sType='sct';
	var _sTb='" . g_l('cockpit', '[shortcuts]') . "';
	function init(){
		parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
	}";

	echo we_html_element::htmlDocType() .
	we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[shortcuts]')) .
			STYLESHEET . we_html_element::jsElement($sJsCode)) .
		we_html_element::htmlBody(
			array(
			"marginwidth" => 15,
			"marginheight" => 10,
			"leftmargin" => 15,
			"topmargin" => 10,
			"onload" => "if(parent!=self)init();"
			), we_html_element::htmlDiv(array(
				"id" => "sct"
				), $sc->getHtml())));
}