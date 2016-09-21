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
we_html_tools::protect();
// init document
$we_dt = $_SESSION['weS']['we_data'][$GLOBALS['we_transaction']];
$we_doc = we_document::initDoc($we_dt);

$z = 0;

switch($_SESSION['weS']['we_mode']){
	default:
	case we_base_constants::MODE_NORMAL:
		$we_tabs = new we_tabs();
		// user has no access to file - only preview mode.
		$access = $we_doc->userHasAccess();
		if($access != we_root::USER_HASACCESS && $access != we_root::USER_NO_SAVE){
			if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs)){
				$we_tabs->addTab(we_base_constants::WE_ICON_PREVIEW, (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW)), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');", ["id" => "tab_" . we_base_constants::WE_EDITPAGE_PREVIEW, 'title' => g_l('weClass', '[preview]')]);
			}
		} else { //	show tabs according to permissions
			if(in_array(we_base_constants::WE_EDITPAGE_PROPERTIES, $we_doc->EditPageNrs) && permissionhandler::hasPerm("CAN_SEE_PROPERTIES")){
				$jscmd = "var we_cmd_args = ['switch_edit_page'," . we_base_constants::WE_EDITPAGE_PROPERTIES . ",'" . $we_transaction . "'];";
				$we_tabs->addTab(we_base_constants::WE_ICON_PROPERTIES, (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES)), ($we_doc->isBinary() ? $jscmd . 'WE().layout.checkFileUpload(we_cmd_args)' : $jscmd . 'we_cmd.apply(this, we_cmd_args);'), ['id' => 'tab_' . we_base_constants::WE_EDITPAGE_PROPERTIES, 'title' => g_l('weClass', '[tab_properties]')]);
			}
			if(in_array(we_base_constants::WE_EDITPAGE_CONTENT, $we_doc->EditPageNrs)){
				$we_tabs->addTab(($we_doc->isBinary() ? '<i class="fa fa-lg fa-upload"></i>' : '<i class="fa fa-lg fa-edit"></i>'), (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT)), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_CONTENT . ",'" . $we_transaction . "');", ['id' => 'tab_' . we_base_constants::WE_EDITPAGE_CONTENT, 'title' => ($we_doc->isBinary() ?
						g_l('weClass', '[upload]') :
						g_l('weClass', '[edit]')
					)
				]);
			}

			if(in_array(we_base_constants::WE_EDITPAGE_IMAGEEDIT, $we_doc->EditPageNrs)){
				$jscmd = "var we_cmd_args = ['switch_edit_page'," . we_base_constants::WE_EDITPAGE_IMAGEEDIT . ",'" . $we_transaction . "'];";
				$we_tabs->addTab(we_base_constants::WE_ICON_EDIT, (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_IMAGEEDIT)), ($we_doc->isBinary() ? $jscmd . 'WE().layout.checkFileUpload(we_cmd_args)' : $jscmd . 'we_cmd.apply(this, we_cmd_args);'), ["id" => "tab_" . we_base_constants::WE_EDITPAGE_IMAGEEDIT, 'title' => g_l('weClass', '[edit_image]')]);
			}

			if(in_array(we_base_constants::WE_EDITPAGE_THUMBNAILS, $we_doc->EditPageNrs)){
				$jscmd = "var we_cmd_args = ['switch_edit_page'," . we_base_constants::WE_EDITPAGE_THUMBNAILS . ",'" . $we_transaction . "'];";
				$we_tabs->addTab('<i class="fa fa-lg fa-image"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_THUMBNAILS)), ($we_doc->isBinary() ? $jscmd . 'WE().layout.checkFileUpload(we_cmd_args)' : $jscmd . 'we_cmd.apply(this, we_cmd_args);'), ["id" => "tab_" . we_base_constants::WE_EDITPAGE_THUMBNAILS, 'title' => g_l('weClass', '[thumbnails]')]);
			}

			if(in_array(we_base_constants::WE_EDITPAGE_WORKSPACE, $we_doc->EditPageNrs)){
				$we_tabs->addTab('<i class="fa fa-lg fa-desktop"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_WORKSPACE)), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_WORKSPACE . ",'" . $we_transaction . "');", ["id" => "tab_" . we_base_constants::WE_EDITPAGE_WORKSPACE, 'title' => g_l('weClass', '[workspace]')]);
			}

			if(in_array(we_base_constants::WE_EDITPAGE_INFO, $we_doc->EditPageNrs) && permissionhandler::hasPerm("CAN_SEE_INFO")){
				$jscmd = "var we_cmd_args = ['switch_edit_page'," . we_base_constants::WE_EDITPAGE_INFO . ",'" . $we_transaction . "'];";
				$we_tabs->addTab('<i class="fa fa-lg fa-info-circle"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO)), ($we_doc->isBinary() ? $jscmd . 'WE().layout.checkFileUpload(we_cmd_args)' : $jscmd . 'we_cmd.apply(this, we_cmd_args);'), ["id" => "tab_" . we_base_constants::WE_EDITPAGE_INFO, 'title' => g_l('weClass', '[information]')]);
			}

			if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs)){

				$jscmd = "var we_cmd_args = ['switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "'];";
				$we_tabs->addTab('<i class="fa fa-lg fa-eye"></i>' . ($we_doc->ContentType == we_base_ContentTypes::TEMPLATE ? '<i class="fa fa-lg fa-edit"></i>' : ''), (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW)), ($we_doc->isBinary() ? $jscmd . 'WE().layout.checkFileUpload(we_cmd_args)' : $jscmd . 'we_cmd.apply(this, we_cmd_args);'), ['id' => 'tab_' . we_base_constants::WE_EDITPAGE_PREVIEW, 'title' => g_l('weClass', ($we_doc->ContentType == we_base_ContentTypes::TEMPLATE ? '[previeweditmode]' : '[preview]'))]);
			}

			if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE, $we_doc->EditPageNrs)){
				//preview of real page in templates
				$we_tabs->addTab('<i class="fa fa-lg fa-eye"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE)), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE . ",'" . $we_transaction . "');", ["id" => "tab_" . we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE, 'title' => g_l('weClass', '[preview]')]);
			}
			if(in_array(we_base_constants::WE_EDITPAGE_TEMPLATE_UNUSEDELEMENTS, $we_doc->EditPageNrs)){
				//show unused elements on pages not available in template
				$we_tabs->addTab('<i class="fa fa-lg fa-balance-scale"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_TEMPLATE_UNUSEDELEMENTS)), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_TEMPLATE_UNUSEDELEMENTS . ",'" . $we_transaction . "');", ["id" => "tab_" . we_base_constants::WE_EDITPAGE_TEMPLATE_UNUSEDELEMENTS, 'title' => g_l('weClass', '[unusedElementsTab]')]);
			}
			if(in_array(we_base_constants::WE_EDITPAGE_FIELDS, $we_doc->EditPageNrs)){
				$we_tabs->addTab(we_base_constants::WE_ICON_CONTENT, (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_FIELDS)), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_FIELDS . ",'" . $we_transaction . "');", ["id" => "tab_" . we_base_constants::WE_EDITPAGE_FIELDS, 'title' => g_l('weClass', '[docList]')]);
			}
			if(in_array(we_base_constants::WE_EDITPAGE_DOCLIST, $we_doc->EditPageNrs)){
				$we_tabs->addTab('<i class="fa fa-lg fa-search"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_DOCLIST)), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_DOCLIST . ",'" . $we_transaction . "');", ["id" => "tab_" . we_base_constants::WE_EDITPAGE_DOCLIST, 'title' => g_l('weClass', '[docList]')]);
			}

			if(permissionhandler::hasPerm('CAN_SEE_SCHEDULER') && we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && in_array(we_base_constants::WE_EDITPAGE_SCHEDULER, $we_doc->EditPageNrs) && $we_doc->ContentType != 'folder'){
				$we_tabs->addTab('<i class="fa fa-lg fa-clock-o"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_SCHEDULER)), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_SCHEDULER . ",'" . $we_transaction . "');", ["id" => "tab_" . we_base_constants::WE_EDITPAGE_SCHEDULER, 'title' => g_l('weClass', '[scheduler]')]);
			}
			if((in_array(we_base_constants::WE_EDITPAGE_VALIDATION, $we_doc->EditPageNrs) && ($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $we_doc->ContentType == we_base_ContentTypes::CSS || $we_doc->ContentType == we_base_ContentTypes::HTML )) && permissionhandler::hasPerm("CAN_SEE_VALIDATION")){
				$we_tabs->addTab('<i class="fa fa-lg fa-bug"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_VALIDATION)), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_VALIDATION . ",'" . $we_transaction . "');", ["id" => "tab_" . we_base_constants::WE_EDITPAGE_VALIDATION, 'title' => g_l('weClass', '[validation]')]);
			}

			if(in_array(we_base_constants::WE_EDITPAGE_WEBUSER, $we_doc->EditPageNrs) && (permissionhandler::hasPerm(['CAN_EDIT_CUSTOMERFILTER', 'CAN_CHANGE_DOCS_CUSTOMER']))){
				$jscmd = "var we_cmd_args = ['switch_edit_page'," . we_base_constants::WE_EDITPAGE_WEBUSER . ",'" . $we_transaction . "'];";
				$we_tabs->addTab(we_base_constants::WE_ICON_CUSTOMER_FILTER, (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_WEBUSER)), ($we_doc->isBinary() ? $jscmd . 'WE().layout.checkFileUpload(we_cmd_args)' : $jscmd . 'we_cmd.apply(this, we_cmd_args);'), ['id' => 'tab_' . we_base_constants::WE_EDITPAGE_WEBUSER, 'title' => g_l('weClass', '[webUser]')]);
			}

			if(permissionhandler::hasPerm("SEE_VERSIONS") && in_array(we_base_constants::WE_EDITPAGE_VERSIONS, $we_doc->EditPageNrs)){
				$jscmd = "var we_cmd_args = ['switch_edit_page'," . we_base_constants::WE_EDITPAGE_VERSIONS . ",'" . $we_transaction . "'];";
				$we_tabs->addTab('<i class="fa fa-lg fa-history"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_VERSIONS)), ($we_doc->isBinary() ? $jscmd . 'WE().layout.checkFileUpload(we_cmd_args)' : $jscmd . 'we_cmd.apply(this, we_cmd_args);'), ["id" => "tab_" . we_base_constants::WE_EDITPAGE_VERSIONS, 'title' => g_l('weClass', '[version]')]);
			}

			$we_doc->we_initSessDat($we_dt);

			if((in_array(we_base_constants::WE_EDITPAGE_VARIANTS, $we_doc->EditPageNrs) && ($we_doc->canHaveVariants(($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $we_doc->ContentType === we_base_ContentTypes::OBJECT_FILE)) )) && permissionhandler::hasPerm("CAN_EDIT_VARIANTS")){
				$we_tabs->addTab('<i class="fa fa-lg fa-sitemap"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_VARIANTS)), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_VARIANTS . ",'" . $we_transaction . "');", ["id" => "tab_" . we_base_constants::WE_EDITPAGE_VARIANTS, 'title' => g_l('weClass', '[variants]')]);
			}
		}
		break;
	case we_base_constants::MODE_SEE://	No tabs in Super-Easy-Edit_mode
}

$extraHead = '';
switch($we_doc->ContentType){
	case we_base_ContentTypes::WEDOCUMENT:
		if($we_doc->TemplateID && permissionhandler::hasPerm('CAN_SEE_TEMPLATES')){
			$extraHead = ' - <a class="bold" style="color:#006699" href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . TEMPLATES_TABLE . '\',' . $we_doc->TemplateID . ',\'' . we_base_ContentTypes::TEMPLATE . '\');">' . g_l('weClass', '[openTemplate]') . '</a>';
		}
		break;
	case we_base_ContentTypes::TEMPLATE:
		if($we_doc->MasterTemplateID){
			$extraHead = ' - <a class="bold" style="color:#006699" href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . TEMPLATES_TABLE . '\',' . $we_doc->MasterTemplateID . ',\'' . we_base_ContentTypes::TEMPLATE . '\');">' . g_l('weClass', '[openMasterTemplate]') . '</a>';
		}
	default:
}

echo we_html_tools::getHtmlTop('', '', '',  we_html_element::cssLink(CSS_DIR . 'we_tab.css').we_html_element::jsScript(JS_DIR . 'we_editor_header.js'), we_html_element::htmlBody(array_merge($we_doc->getEditorBodyAttributes(we_root::EDITOR_HEADER), [
		'id' => "eHeaderBody",
		'onresize' => "weTabs.setFrameSize()",
		'onload' => '_EditorFrame.setEditorEditPageNr(' . $we_doc->EditPageNr . ');' .
		($GLOBALS['we_doc']->ContentType != we_base_ContentTypes::TEMPLATE ? 'parent.openedWithWE=true;' : '') .
		"WE().layout.we_setPath(_EditorFrame,'" . $we_doc->Path . "','" . ($we_doc->Filename ? $we_doc->Filename . (isset($we_doc->Extension) ? $we_doc->Extension : '') : $we_doc->Text) . "', " . intval($we_doc->ID) . ",'" . ($we_doc->Published == 0 ? 'notpublished' : ($we_doc->Table !== TEMPLATES_TABLE && $we_doc->Table !== VFILE_TABLE && $we_doc->ModDate > $we_doc->Published ? 'changed' : 'published')) . "');weTabs.setFrameSize();"
		]), we_html_element::htmlDiv(['id' => 'main'], we_html_element::htmlDiv(['id' => 'headrow'], ($we_doc->ContentType ? we_html_element::htmlB(str_replace(' ', '&nbsp;', g_l('contentTypes', '[' . $we_doc->ContentType . ']'))) : '') . ': ' .
				($we_doc->Table == FILE_TABLE && $we_doc->ID ? '<a href="' . WEBEDITION_DIR . 'openBrowser.php?url=' . $we_doc->ID . '" target="browser">' : '') .
				'<span id="h_path" class="bold cutText"></span>' . ($we_doc->Table == FILE_TABLE && $we_doc->ID ? '</a>' : '') . ' (ID: <span id="h_id"></span>)' .
				$extraHead
			) .
			($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE ?
				$we_tabs->getHTML() : ''))
	)
);
