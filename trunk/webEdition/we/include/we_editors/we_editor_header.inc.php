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
echo we_html_tools::getHtmlTop() . STYLESHEET;

// init document
$we_dt = $_SESSION['weS']['we_data'][$GLOBALS['we_transaction']];
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

$z = 0;

switch($_SESSION['weS']['we_mode']){
	default:
	case we_base_constants::MODE_NORMAL:
		$we_tabs = new we_tabs();
		// user has no access to file - only preview mode.
		if($we_doc->userHasAccess() != we_root::USER_HASACCESS && $we_doc->userHasAccess() != we_root::USER_NO_SAVE){
			if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs)){
				$jscmd = "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');";
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-eye"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW) ? we_tab::ACTIVE : we_tab::NORMAL), ($we_doc->isBinary() ? we_fileupload_ui_preview::getJsOnLeave($jscmd) : $jscmd), array("id" => "tab_" . we_base_constants::WE_EDITPAGE_PREVIEW, 'title' => g_l('weClass', '[preview]'))));
			}
		} else { //	show tabs according to permissions
			if(in_array(we_base_constants::WE_EDITPAGE_PROPERTIES, $we_doc->EditPageNrs) && permissionhandler::hasPerm("CAN_SEE_PROPERTIES")){
				$jscmd = "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PROPERTIES . ",'" . $we_transaction . "');";
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-cog"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES) ? we_tab::ACTIVE : we_tab::NORMAL), ($we_doc->isBinary() ? we_fileupload_ui_preview::getJsOnLeave($jscmd) : $jscmd), array("id" => "tab_" . we_base_constants::WE_EDITPAGE_PROPERTIES, 'title' => g_l('weClass', '[tab_properties]'))));
			}
			if(in_array(we_base_constants::WE_EDITPAGE_CONTENT, $we_doc->EditPageNrs)){
				$we_tabs->addTab(new we_tab(($we_doc->isBinary() ? '<i class="fa fa-lg fa-upload"></i>' : '<i class="fa fa-lg fa-edit"></i>'), (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT) ? we_tab::ACTIVE : we_tab::NORMAL), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_CONTENT . ",'" . $we_transaction . "');", array("id" => "tab_" . we_base_constants::WE_EDITPAGE_CONTENT, 'title' =>
					($we_doc->isBinary() ? g_l('weClass', '[upload]') :
						g_l('weClass', '[edit]')
					)
				)));
			}

			if(in_array(we_base_constants::WE_EDITPAGE_IMAGEEDIT, $we_doc->EditPageNrs)){
				$jscmd = "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_IMAGEEDIT . ",'" . $we_transaction . "');";
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-edit"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_IMAGEEDIT) ? we_tab::ACTIVE : we_tab::NORMAL), ($we_doc->isBinary() ? we_fileupload_ui_preview::getJsOnLeave($jscmd) : $jscmd), array("id" => "tab_" . we_base_constants::WE_EDITPAGE_IMAGEEDIT, 'title' => g_l('weClass', '[edit_image]'))));
			}

			if(in_array(we_base_constants::WE_EDITPAGE_THUMBNAILS, $we_doc->EditPageNrs)){
				$jscmd = "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_THUMBNAILS . ",'" . $we_transaction . "');";
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-image"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_THUMBNAILS) ? we_tab::ACTIVE : we_tab::NORMAL), ($we_doc->isBinary() ? we_fileupload_ui_preview::getJsOnLeave($jscmd) : $jscmd), array("id" => "tab_" . we_base_constants::WE_EDITPAGE_THUMBNAILS, 'title' => g_l('weClass', '[thumbnails]'))));
			}

			if(in_array(we_base_constants::WE_EDITPAGE_WORKSPACE, $we_doc->EditPageNrs)){
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-desktop"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_WORKSPACE) ? we_tab::ACTIVE : we_tab::NORMAL), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_WORKSPACE . ",'" . $we_transaction . "');", array("id" => "tab_" . we_base_constants::WE_EDITPAGE_WORKSPACE, 'title' => g_l('weClass', '[workspace]'))));
			}

			if(in_array(we_base_constants::WE_EDITPAGE_INFO, $we_doc->EditPageNrs) && permissionhandler::hasPerm("CAN_SEE_INFO")){
				$jscmd = "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_INFO . ",'" . $we_transaction . "');";
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-info-circle"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO) ? we_tab::ACTIVE : we_tab::NORMAL), ($we_doc->isBinary() ? we_fileupload_ui_preview::getJsOnLeave($jscmd) : $jscmd), array("id" => "tab_" . we_base_constants::WE_EDITPAGE_INFO, 'title' => g_l('weClass', '[information]'))));
			}

			if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs)){

				$jscmd = "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');";
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-eye"></i>' . ($we_doc->ContentType == we_base_ContentTypes::TEMPLATE ? '<i class="fa fa-lg fa-edit"></i>' : ''), (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW) ? we_tab::ACTIVE : we_tab::NORMAL), ($we_doc->isBinary() ? we_fileupload_ui_preview::getJsOnLeave($jscmd) : $jscmd), array("id" => "tab_" . we_base_constants::WE_EDITPAGE_PREVIEW, 'title' => g_l('weClass', ($we_doc->ContentType == we_base_ContentTypes::TEMPLATE ? '[previeweditmode]' : '[preview]')))));
			}

			if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE, $we_doc->EditPageNrs)){
				//preview of real page in templates
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-eye"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE) ? we_tab::ACTIVE : we_tab::NORMAL), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE . ",'" . $we_transaction . "');", array("id" => "tab_" . we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE, 'title' => g_l('weClass', '[preview]'))));
			}
			if(in_array(we_base_constants::WE_EDITPAGE_FIELDS, $we_doc->EditPageNrs)){
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-search"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_FIELDS) ? we_tab::ACTIVE : we_tab::NORMAL), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_FIELDS . ",'" . $we_transaction . "');", array("id" => "tab_" . we_base_constants::WE_EDITPAGE_FIELDS, 'title' => g_l('weClass', '[docList]'))));
			}
			if(in_array(we_base_constants::WE_EDITPAGE_DOCLIST, $we_doc->EditPageNrs)){
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-search"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_DOCLIST) ? we_tab::ACTIVE : we_tab::NORMAL), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_DOCLIST . ",'" . $we_transaction . "');", array("id" => "tab_" . we_base_constants::WE_EDITPAGE_DOCLIST, 'title' => g_l('weClass', '[docList]'))));
			}

			/* 			if(in_array(we_base_constants::WE_EDITPAGE_SEARCH, $we_doc->EditPageNrs)){
			  $we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-search"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_SEARCH) ? we_tab::ACTIVE : we_tab::NORMAL), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_SEARCH . ",'" . $we_transaction . "');", array("id" => "tab_" . we_base_constants::WE_EDITPAGE_SEARCH, 'title' => g_l('weClass', '[search]'))));
			  }
			 */
			if(permissionhandler::hasPerm("CAN_SEE_SCHEDULER") && we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && in_array(we_base_constants::WE_EDITPAGE_SCHEDULER, $we_doc->EditPageNrs) && $we_doc->ContentType != "folder"){
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-clock-o"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_SCHEDULER) ? we_tab::ACTIVE : we_tab::NORMAL), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_SCHEDULER . ",'" . $we_transaction . "');", array("id" => "tab_" . we_base_constants::WE_EDITPAGE_SCHEDULER, 'title' => g_l('weClass', '[scheduler]'))));
			}
			if((in_array(we_base_constants::WE_EDITPAGE_VALIDATION, $we_doc->EditPageNrs) && ($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $we_doc->ContentType == we_base_ContentTypes::CSS || $we_doc->ContentType == we_base_ContentTypes::HTML )) && permissionhandler::hasPerm("CAN_SEE_VALIDATION")){
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-bug"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_VALIDATION) ? we_tab::ACTIVE : we_tab::NORMAL), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_VALIDATION . ",'" . $we_transaction . "');", array("id" => "tab_" . we_base_constants::WE_EDITPAGE_VALIDATION, 'title' => g_l('weClass', '[validation]'))));
			}

			if(in_array(we_base_constants::WE_EDITPAGE_WEBUSER, $we_doc->EditPageNrs) && (permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER') || permissionhandler::hasPerm('CAN_CHANGE_DOCS_CUSTOMER'))){
				$jscmd = "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_WEBUSER . ",'" . $we_transaction . "');";
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-group"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_WEBUSER) ? we_tab::ACTIVE : we_tab::NORMAL), ($we_doc->isBinary() ? we_fileupload_ui_preview::getJsOnLeave($jscmd) : $jscmd), array("id" => "tab_" . we_base_constants::WE_EDITPAGE_WEBUSER, 'title' => g_l('weClass', '[webUser]'))));
			}

			if(permissionhandler::hasPerm("SEE_VERSIONS") && in_array(we_base_constants::WE_EDITPAGE_VERSIONS, $we_doc->EditPageNrs)){
				$jscmd = "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_VERSIONS . ",'" . $we_transaction . "');";
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-history"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_VERSIONS) ? we_tab::ACTIVE : we_tab::NORMAL), ($we_doc->isBinary() ? we_fileupload_ui_preview::getJsOnLeave($jscmd) : $jscmd), array("id" => "tab_" . we_base_constants::WE_EDITPAGE_VERSIONS, 'title' => g_l('weClass', '[version]'))));
			}

			$we_doc->we_initSessDat($we_dt);

			if((in_array(we_base_constants::WE_EDITPAGE_VARIANTS, $we_doc->EditPageNrs) && ($we_doc->canHaveVariants(($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $we_doc->ContentType === we_base_ContentTypes::OBJECT_FILE)) )) && permissionhandler::hasPerm("CAN_EDIT_VARIANTS")){
				$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-sitemap"></i>', (($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_VARIANTS) ? we_tab::ACTIVE : we_tab::NORMAL), "we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_VARIANTS . ",'" . $we_transaction . "');", array("id" => "tab_" . we_base_constants::WE_EDITPAGE_VARIANTS, 'title' => g_l('weClass', '[variants]'))));
			}
		}
		echo we_tabs::getHeader();
		break;
	case we_base_constants::MODE_SEE://	No tabs in Super-Easy-Edit_mode
		echo we_html_element::jsElement('var weTabs; function setFrameSize(){}');
}

echo we_html_element::jsScript(JS_DIR . 'we_editor_header.js', '
var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrame(parent.name);
_EditorFrame.setEditorEditPageNr(' . $we_doc->EditPageNr . ');' .
	($GLOBALS['we_doc']->ContentType != we_base_ContentTypes::TEMPLATE ? 'parent.openedWithWE=true;' : ''));
$_text = ($we_doc->Filename ? $we_doc->Filename . (isset($we_doc->Extension) ? $we_doc->Extension : '') : $we_doc->Text);
?>
</head>
<body id="eHeaderBody" onload="WE().layout.we_setPath(<?php echo "'" . $we_doc->Path . "','" . $_text . "', " . intval($we_doc->ID) . ",'" . ($we_doc->Published == 0 ? 'notpublished' : ($we_doc->Table !== TEMPLATES_TABLE && $we_doc->Table !== VFILE_TABLE && $we_doc->ModDate > $we_doc->Published ? 'changed' : 'published')) . "'"; ?>);weTabs.setFrameSize();" onresize="weTabs.setFrameSize()"
			<?php echo $we_doc->getEditorBodyAttributes(we_root::EDITOR_HEADER); ?>>
	<div id="main" ><?php
			echo '<div id="headrow">&nbsp;' . ($we_doc->ContentType ? we_html_element::htmlB(str_replace(' ', '&nbsp;', g_l('contentTypes', '[' . $we_doc->ContentType . ']'))) : '') . ': ' .
			($we_doc->Table == FILE_TABLE && $we_doc->ID ? '<a href="' . WEBEDITION_DIR . 'openBrowser.php?url=' . $we_doc->ID . '" target="browser">' : '') .
			'<span id="h_path" class="bold"></span>' . ($we_doc->Table == FILE_TABLE && $we_doc->ID ? '</a>' : '') . ' (ID: <span id="h_id"></span>)';
			switch($we_doc->ContentType){
				case we_base_ContentTypes::WEDOCUMENT:
					if($we_doc->TemplateID && permissionhandler::hasPerm('CAN_SEE_TEMPLATES')){
						echo ' - <a class="bold" style="color:#006699" href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . TEMPLATES_TABLE . '\',' . $we_doc->TemplateID . ',\'' . we_base_ContentTypes::TEMPLATE . '\');">' . g_l('weClass', '[openTemplate]') . '</a>';
					}
					break;
				case we_base_ContentTypes::TEMPLATE:
					if($we_doc->MasterTemplateID){
						echo ' - <a class="bold" style="color:#006699" href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . TEMPLATES_TABLE . '\',' . $we_doc->MasterTemplateID . ',\'' . we_base_ContentTypes::TEMPLATE . '\');">' . g_l('weClass', '[openMasterTemplate]') . '</a>';
					}
				default:
			}
			echo '</div>' . ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE ?
				$we_tabs->getHTML() : '');
			?></div>
</body>
</html>