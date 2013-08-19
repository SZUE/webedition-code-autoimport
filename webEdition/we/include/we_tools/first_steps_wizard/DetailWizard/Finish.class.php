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
class Finish extends leWizardStepBase{

	var $EnabledButtons = array();

	function execute(&$Template){

		// copy new Sidebar file to correct position
		$NewSidebarFile = $_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . "sidebar/first_steps_wizard.php";
		$OldSidebarFile = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/SideBar.php";

		if(file_exists($NewSidebarFile) && is_file($NewSidebarFile)){

			if(!unlink($NewSidebarFile)){
				return LE_WIZARDSTEP_ERROR;
			}
		}

		if(file_exists($OldSidebarFile) && is_file($OldSidebarFile)){

			if(!copy($OldSidebarFile, $NewSidebarFile)){
				return LE_WIZARDSTEP_ERROR;
			}
		}

		// now change paths in sidebar document(!)
		$content = implode('', file($NewSidebarFile));
		$content = weSidebarDocumentParser::parseCode($content, $_SESSION['weS']['we_fsw_document_path']);

		// change mastertemplate in installed templates
		$TemplateIds = array();
		foreach($_SESSION['weS']['fsw_importRefTable'] as $index => $entry){
			if($entry['Table'] == TEMPLATES_TABLE){
				$TemplateIds[] = $entry['ID'];
			}
		}
		we_loadDefaultMasterTemplateConfig();
		$query = "UPDATE " . TEMPLATES_TABLE . " SET MasterTemplateID = " . FSW_DEFAULT_MASTER_TEMPLATE . " WHERE ID IN (" . implode(",", $TemplateIds) . ")"; // AND MasterTemplateID != 0";
		$DB_WE = new DB_WE();
		$DB_WE->query($query);

		// write new content
		if(!weFile::save($NewSidebarFile, $content, "w+")){
			return LE_WIZARDSTEP_ERROR;
		}

		// oputput of the wizard step

		$RebuildBtnWidth = ($GLOBALS["WE_LANGUAGE"] == "Finnish") ? "130" : "100";
		$Rebuild = $weButton->create_button('rebuild', 'javascript:top.openRebuild();', true, $RebuildBtnWidth, 22);

		$SidebarFile = WEBEDITION_DIR . "sidebar/first_steps_wizard.php";
		$webEditionDir = WEBEDITION_DIR;

		$NextStep = <<<EOF
top.weButton.enable("next");
top.leWizardForm.next = function() {
	top.opener.top.weSidebar.open('{$SidebarFile}', 300);
	top.close();
}
top.openRebuild = function() {
	top.opener.top.openWindow("{$webEditionDir}we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_all","resave",-1,-1,600,130,0,true);
}
EOF;

		$Template->addJavascript($NextStep);

		$Content = <<<EOF
		{$Rebuild}<br /><br />
		{$this->Language['content_2']}

EOF;

		$this->setContent($Content);

		return LE_WIZARDSTEP_NEXT;
	}

}