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
$aCols = explode(';', isset($aProps) ? $aProps[3] : we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0));
$disableNew = true;
$cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "');";
if(we_base_permission::hasPerm("NEW_WEBEDITIONSITE")){
	if(we_base_permission::hasPerm("NO_DOCTYPE")){
		$disableNew = false;
	} else {
		$dtq = we_docTypes::getDoctypeQuery($GLOBALS['DB_WE']);
		$id = f('SELECT dt.ID FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where'] . ' LIMIT 1');
		if($id){
			$disableNew = false;
			$cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','" . $id . "')";
		} else {
			$disableNew = true;
		}
	}
} else {
	$disableNew = true;
}

$disableObjects = false;
if(defined('OBJECT_TABLE')){
	$allClasses = we_users_util::getAllowedClasses();
	if(empty($allClasses)){
		$disableObjects = true;
	}
}

$js = [];

if(defined('FILE_TABLE') && we_base_permission::hasPerm("CAN_SEE_DOCUMENTS")){
	$js["open_document"] = "top.we_cmd('open_document');";
}
if(defined('FILE_TABLE') && we_base_permission::hasPerm("CAN_SEE_DOCUMENTS") && we_base_permission::hasPerm("CAN_SEE_PROPERTIES") && !$disableNew){
	$js["new_document"] = $cmdNew;
}
if(defined('TEMPLATES_TABLE') && we_base_permission::hasPerm("NEW_TEMPLATE") && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
	$js["new_template"] = "top.we_cmd('new','" . TEMPLATES_TABLE . "','','" . we_base_ContentTypes::TEMPLATE . "');";
}
if(we_base_permission::hasPerm("NEW_DOC_FOLDER")){
	$js["new_directory"] = "top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::FOLDER . "')";
}
if(defined('FILE_TABLE') && we_base_permission::hasPerm("CAN_SEE_DOCUMENTS")){
	$js["unpublished_pages"] = "top.we_cmd('openUnpublishedPages');";
}
if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm("CAN_SEE_OBJECTFILES") && !$disableObjects){
	$js["unpublished_objects"] = "top.we_cmd('openUnpublishedObjects');";
}
if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm("NEW_OBJECTFILE") && we_base_permission::hasPerm("CAN_SEE_PROPERTIES") && !$disableObjects){
	$js["new_object"] = "top.we_cmd('new_objectFile');";
}
if(defined('OBJECT_TABLE') && we_base_permission::hasPerm("NEW_OBJECT") && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
	$js["new_class"] = "top.we_cmd('new_object');";
}
if(we_base_permission::hasPerm("EDIT_SETTINGS")){
	$js["preferences"] = "top.we_cmd('openPreferences');";
}
if(we_base_permission::hasPerm('NEW_GRAFIK')){
	$js['btn_add_image'] = "top.we_cmd('new','tblFile','','image/*')";
}


$shortcuts = [];
foreach($aCols as $sCol){
	$shortcuts[] = explode(',', $sCol);
}

$sSctOut = '';
$col = 0;

foreach($shortcuts as $sctCol){
	$sSctOut .= '<div class="sct_row" style="display: block; margin-right: 1em; float: left;"><table class="default" style="width:100%;">';
	$iCurrSctRow = 0;
	foreach($sctCol as $label){
		if(isset($js[$label])){
			$icon = '';
			switch($label){
				case 'new_directory':
					$icon = we_base_ContentTypes::FOLDER;
					break;
				case 'unpublished_pages':
				case 'open_document':
				case 'new_document':
					$icon = we_base_ContentTypes::WEDOCUMENT;
					break;
				case 'unpublished_objects':
				case 'new_object':
					$icon = we_base_ContentTypes::OBJECT_FILE;
					break;
				case 'new_template':
					$icon = we_base_ContentTypes::TEMPLATE;
					break;
				case 'new_class':
					$icon = we_base_ContentTypes::OBJECT;
					break;
				case 'btn_add_image':
					$icon = we_base_ContentTypes::IMAGE;
					break;
				case 'preferences':
					$icon = 'settings';
					break;
			}

			$sSctOut .= '<tr onclick="' . $js[$label] . '"><td class="sctFileIcon" data-contenttype="' . $icon . '"></td><td class="middlefont sctText">' . g_l('button', '[' . $label . '][value]') . '</tr>';
		}
		$iCurrSctRow++;
	}
	$sSctOut .= '</table></div>';
	$col++;
}

$sc = $sSctOut . we_html_element::jsElement('WE().util.setIconOfDocClass(document,"sctFileIcon");');

if(!isset($aProps)){
	echo we_html_tools::getHtmlTop(g_l('cockpit', '[shortcuts]'), '', '', we_html_element::jsScript(JS_DIR . 'widgets/preview.js', '', [
			'id' => 'loadVarPreview',
			'data-preview' => setDynamicVar([
				'id' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5),
				'type' => 'sct',
				'tb' => g_l('cockpit', '[shortcuts]'),
				//'iconClass' =>
		])]), we_html_element::htmlBody(
			['style' => 'margin:10px 15px;',
			"onload" => "if(parent!=self)init();"
			], we_html_element::htmlDiv(["id" => "sct"
				], $sc)));
}