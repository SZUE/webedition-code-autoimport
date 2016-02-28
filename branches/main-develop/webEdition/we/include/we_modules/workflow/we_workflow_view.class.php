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
class we_workflow_view extends we_modules_view{

	const PAGE_PROPERTIES = 0;
	const PAGE_OVERVIEW = 1;
	const BUTTON_DECLINE = 'fat:decline,fa-lg fa-close fa-cancel';
	const BUTTON_FORWARD = 'fat:forward:fa-lg fa-mail-forward';

	// workflow array; format workflow[workflowID]=workflow_name
	var $workflows = array();
	//default workflow
	var $workflowDef;
	//default document
	var $documentDef;
	//what is current display 0-workflow(default);1-document;
	var $show = 0;
	//wat page is currentlly displed 0-properties(default);1-overview;
	var $page = self::PAGE_PROPERTIES;
	var $hiddens = array();
	var $uid;

	function __construct($framset){
		parent::__construct($framset);
		$this->uid = 'wf_' . md5(uniqid(__FILE__, true));
		$this->workflowDef = new we_workflow_workflow();
		$this->documentDef = new we_workflow_document();
		array_push($this->hiddens, 'ID', 'Status');
	}

	function getHiddens(){
		return we_html_element::htmlHiddens(array(
					'home' => '0',
					'wcmd' => 'new_workflow',
					'wid' => $this->workflowDef->ID,
					'pnt' => 'edit',
					'wname' => $this->uid,
					'page' => $this->page
		));
	}

	function getHiddensFormPropertyPage(){
		array_push($this->hiddens, 'Text', 'Type', 'Folders', 'ObjectFileFolders', 'Categories', 'ObjCategories', 'DocType', 'Objects', 'EmailPath', 'LastStepAutoPublish');

		return '';
	}

	function getHiddensFormOverviewPage(){
		//we need the following vars since fields expect this hidden fields & selectors don't generate a hidden field itself
		array_push($this->hiddens, 'Type', 'Folders', 'ObjectFileFolders', 'Categories', 'ObjCategories', 'DocType', 'Objects');

		$out = '';

		$counter = 0;
		$counter1 = 0;
		foreach($this->workflowDef->steps as $sv){
			$out.=we_html_element::htmlHiddens(array(
						$this->uid . '_step' . $counter . '_sid' => $sv->ID,
						$this->uid . '_step' . $counter . '_and' => $sv->stepCondition,
						$this->uid . '_step' . $counter . '_Worktime' => $sv->Worktime,
						$this->uid . '_step' . $counter . '_timeAction' => $sv->timeAction
			));
			$counter1 = 0;
			foreach($sv->tasks as $tv){
				$out.=we_html_element::htmlHiddens(array(
							$this->uid . '_task' . $counter . $counter1 . '_tid' => $tv->ID,
							$this->uid . '_task_' . $counter . '_' . $counter1 . '_userid' => $tv->userID,
							$this->uid . '_task_' . $counter . '_' . $counter1 . '_Edit' => ($tv->Edit ? 1 : 0),
							$this->uid . '_task_' . $counter . '_' . $counter1 . '_Mail' => ($tv->Mail ? 1 : 0)
				));
				++$counter1;
			}
			++$counter;
		}
		$out.=we_html_element::htmlHiddens(array(
					'wcat' => '0',
					'wocat' => '0',
					'wfolder' => '0',
					'woffolder' => '0',
					'wobject' => '0',
					'wsteps' => $counter,
					'wtasks' => $counter1
		));

		return $out;
	}

	function workflowHiddens(){
		$out = '';
		foreach($this->hiddens as $val){
			$dat = isset($this->workflowDef->persistents[$val]) ? $this->workflowDef->$val : $this->$val;
			$out.=we_html_element::htmlHidden($this->uid . '_' . $val, (is_array($dat) ? implode(',', $dat) : $dat));
		}
		return $out;
	}

	function getProperties(){
		$content = '<form name="we_form" onsubmit="return false">' .
				$this->getHiddens();
		if($this->show){
			$content .=$this->getDocumentInfo();
		} else {
			switch($this->page){
				case self::PAGE_PROPERTIES:
					$_space = 143;
					$parts = array(
						$this->getWorkflowHeaderMultiboxParts($_space),
						$parts[] = array(
					'headline' => g_l('modules_workflow', '[type]'),
					'space' => $_space - 25,
					'html' => $this->getWorkflowTypeHTML()),
						array(
							'headline' => g_l('modules_workflow', '[specials]'),
							'space' => $_space - 25,
							'html' => '<br/>' .
							we_html_forms::checkboxWithHidden($this->workflowDef->EmailPath, $this->uid . '_EmailPath', g_l('modules_workflow', '[EmailPath]'), false, 'defaultfont', '', false) .
							we_html_forms::checkboxWithHidden($this->workflowDef->LastStepAutoPublish, $this->uid . '_LastStepAutoPublish', g_l('modules_workflow', '[LastStepAutoPublish]'), false, 'defaultfont', '', false)
						),
					);
					//	Workflow-Type
					$content .= $this->getHiddensFormOverviewPage() .
							we_html_multiIconBox::getHTML('workflowProperties', $parts, 30);
					break;
				case self::PAGE_OVERVIEW:
					$content .= $this->getHiddensFormPropertyPage() .
							we_html_tools::htmlDialogLayout($this->getStepsHTML(), '');
			}
			$content .=$this->workflowHiddens();
		}
		$content .='</form>';
		return we_html_element::htmlBody(array('class' => 'weEditorBody', 'onload' => 'loaded=1;', 'onunload' => 'doUnload()'), $content);
	}

	/**
	 * @return array		can be used by class we_multiIconBox.class.inc.php as $content-array
	 * @desc Enter description here...
	 */
	function getWorkflowHeaderMultiboxParts($space){
		return array(
			'headline' => g_l('modules_workflow', '[name]'),
			'html' => we_html_tools::htmlTextInput($this->uid . '_Text', 37, stripslashes($this->workflowDef->Text), '', ' id="yuiAcInputPathName" onchange="top.content.setHot();" onblur=" parent.edheader.weTabs.setTitlePath(this.value);"', "text", 498),
			'space' => $space
		);
	}

	function getWorkflowSelectHTML(){
		$vals = we_workflow_workflow::getAllWorkflowsInfo();
		return we_html_tools::htmlSelect('wid', $vals, 4, $this->workflowDef->ID, false, array("onclick" => "we_cmd(\"workflow_edit\")"), "value", 200);
	}

	function getWorkflowTypeHTML(){
		return $this->getTypeTableHTML(we_html_forms::radiobutton(we_workflow_workflow::FOLDER, ($this->workflowDef->Type == we_workflow_workflow::FOLDER ? 1 : 0), $this->uid . '_Type', g_l('modules_workflow', '[type_dir]'), true, 'defaultfont', 'onclick=top.content.setHot();'), array(
					$this->getFoldersHTML(),
						), 25) .
				$this->getTypeTableHTML(we_html_forms::radiobutton(we_workflow_workflow::DOCTYPE_CATEGORY, ($this->workflowDef->Type == we_workflow_workflow::DOCTYPE_CATEGORY ? 1 : 0), $this->uid . '_Type', g_l('modules_workflow', '[type_doctype]'), true, 'defaultfont', 'onclick=top.content.setHot();'), array(
					$this->getDocTypeHTML(),
					$this->getCategoryHTML(),
						), 25) .
				(defined('OBJECT_TABLE') ?
						$this->getTypeTableHTML(we_html_forms::radiobutton(we_workflow_workflow::OBJECT, ($this->workflowDef->Type == we_workflow_workflow::OBJECT ? 1 : 0), $this->uid . '_Type', g_l('modules_workflow', '[type_object]'), true, 'defaultfont', 'onclick=top.content.setHot();'), array(
							$this->getObjectHTML(),
							$this->getObjCategoryHTML(),
							$this->getObjectFileFoldersHTML(),
								), 25) :
						'');
	}

	function getFoldersHTML(){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:top.content.setHot();we_cmd('del_all_folders');");
		$wecmdenc3 = we_base_request::encCmd("fillIDs();opener.we_cmd('add_folder',top.allIDs);");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:top.content.setHot();we_cmd('we_selector_directory','','" . FILE_TABLE . "','','','" . $wecmdenc3 . "','','','',true)");

		$dirs = new we_chooser_multiDir(495, $this->workflowDef->Folders, 'del_folder', $delallbut . $addbut, '', 'ContentType', FILE_TABLE, 'defaultfont', '', "top.content.setHot();");

		return we_html_tools::htmlFormElementTable($dirs->get(), g_l('modules_workflow', '[dirs]'));
	}

	function getCategoryHTML(){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:top.content.setHot();we_cmd('del_all_cats')", false, 100, 22, "", "", (!permissionhandler::hasPerm("EDIT_KATEGORIE")));
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:top.content.setHot();we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','fillIDs();opener.we_cmd(\\'add_cat\\',top.allIDs);')", false, 100, 22, "", "", (!permissionhandler::hasPerm("EDIT_KATEGORIE")));

		$cats = new we_chooser_multiDir(495, $this->workflowDef->Categories, 'del_cat', $delallbut . $addbut, "", '"we/category"', CATEGORY_TABLE, "defaultfont", "", "top.content.setHot();");

		return we_html_tools::htmlFormElementTable($cats->get(), g_l('modules_workflow', '[categories]'));
	}

	function getObjCategoryHTML(){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:top.content.setHot();we_cmd('del_all_objcats')", false, 100, 22, "", "", (!permissionhandler::hasPerm("EDIT_KATEGORIE")));
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:top.content.setHot();we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','fillIDs();opener.we_cmd(\\'add_objcat\\',top.allIDs);')", false, 100, 22, "", "", (!permissionhandler::hasPerm("EDIT_KATEGORIE")));

		$cats = new we_chooser_multiDir(495, $this->workflowDef->ObjCategories, "del_objcat", $delallbut . $addbut, "", '"we/category"', CATEGORY_TABLE, "defaultfont", "", "top.content.setHot();");

		return we_html_tools::htmlFormElementTable($cats->get(), g_l('modules_workflow', '[categories]'));
	}

	function getObjectHTML(){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:top.content.setHot();we_cmd('del_all_objects')", false, 100, 22, "", "", (!permissionhandler::hasPerm("EDIT_KATEGORIE")));
		$wecmdenc3 = we_base_request::encCmd("opener.we_cmd('add_object',top.currentID);");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:top.content.setHot();we_cmd('openObjselector','','" . OBJECT_TABLE . "','','','" . $wecmdenc3 . "')", false, 100, 22, "", "", (!permissionhandler::hasPerm("EDIT_KATEGORIE")));

		$cats = new we_chooser_multiDir(495, $this->workflowDef->Objects, "del_object", $delallbut . $addbut, "", "ContentType", OBJECT_TABLE, "defaultfont", "", "top.content.setHot();");

		return we_html_tools::htmlFormElementTable($cats->get(), g_l('modules_workflow', '[classes]'));
	}

	function getObjectFileFoldersHTML(){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:top.content.setHot();we_cmd('del_all_object_file_folders');");
		//avascript:top.content.setHot();we_cmd('we_selector_directory','','".OBJECT_FILES_TABLE."','','','fillIDs();opener.we_cmd(\\'add_object_file_folder\\',top.allIDs);','','','',true)
		$wecmdenc3 = we_base_request::encCmd("fillIDs();opener.we_cmd('add_object_file_folder',top.allIDs);");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:top.content.setHot();we_cmd('we_selector_directory','','" . OBJECT_FILES_TABLE . "','','','" . $wecmdenc3 . "','','','',true)");

		$dirs = new we_chooser_multiDir(495, $this->workflowDef->ObjectFileFolders, "del_object_file_folder", $delallbut . $addbut, "", "ContentType", OBJECT_FILES_TABLE, "defaultfont", "", "top.content.setHot();");

		return we_html_tools::htmlFormElementTable($dirs->get(), g_l('modules_workflow', '[dirs]'));
	}

	function getStatusHTML(){
		return we_html_forms::checkboxWithHidden(1, 'status_workflow', g_l('modules_workflow', '[active]'), false, 'defaultfont', 'top.content.setHot();');
	}

	function getStepsHTML(){
		$content = array();

		$ids = '';

		$headline = array(
			array('dat' => '<div class="middlefont">' . g_l('modules_workflow', '[step]') . '</div>'),
			array('dat' => '<div class="middlefont">' . g_l('modules_workflow', '[and_or]') . '</div>'),
			array('dat' => '<div class="middlefont">' . g_l('modules_workflow', '[worktime]') . '</div>')
		);

		$counter = 0;
		$counter1 = 0;

		$yuiSuggest = & weSuggest::getInstance();

		/*		 * *** WORKFLOWSTEPS **** */
		foreach($this->workflowDef->steps as $sv){
			$ids.=we_html_element::htmlHidden($this->uid . '_step' . $counter . '_sid', $sv->ID);
			$content[$counter] = array(
				array(
					'dat' => $counter + 1,
					'height' => '',
					'align' => 'center',
				),
				array(
					'dat' => '<table><tr style="vertical-align:top"><td>' . we_html_forms::radiobutton(1, $sv->stepCondition ? 1 : 0, $this->uid . "_step" . $counter . "_and", "", false, "defaultfont", "top.content.setHot();") . '</td><td style="padding-left:5px;">' . we_html_forms::radiobutton(0, $sv->stepCondition ? 0 : 1, $this->uid . "_step" . $counter . "_and", "", false, "defaultfont", "top.content.setHot();") . '</td></tr></table>',
					'height' => '',
					'align' => '',
				),
				array(
					'dat' => '<table class="default" style="margin-top:5px;">
	<tr style="vertical-align:middle"><td class="middlefont" style="padding-bottom:1em;">' . we_html_tools::htmlTextInput($this->uid . "_step" . $counter . "_Worktime", 15, $sv->Worktime, '', 'min="0" step="0.016" onchange="top.content.setHot();"', 'number') . '</td></tr>
	<tr style="vertical-align:top"><td class="middlefont">' . we_html_forms::checkboxWithHidden($sv->timeAction == 1, $this->uid . "_step" . $counter . "_timeAction", g_l('modules_workflow', '[go_next]'), false, "middlefont", "top.content.setHot();") . '</td></tr>
</table>',
					'height' => '',
					'align' => '',
				)
			);


			$counter1 = 0;
			foreach($sv->tasks as $tv){
				$ids.=we_html_element::htmlHidden($this->uid . '_task' . $counter . '_' . $counter1 . '_tid', $tv->ID);
				$headline[$counter1 + 3] = array('dat' => g_l('modules_workflow', '[user]') . (string) ($counter1 + 1));

				$foo = f('SELECT Path FROM ' . USER_TABLE . ' WHERE ID=' . intval($tv->userID), '', $this->db);
				$wecmdenc2 = we_base_request::encCmd("document.we_form." . $this->uid . "_task_" . $counter . "_" . $counter1 . "_usertext.value");
				$button = we_html_button::create_button(we_html_button::SELECT, "javascript:top.content.setHot();we_cmd('we_users_selector','document.we_form." . $this->uid . "_task_" . $counter . '_' . $counter1 . '_userid.value', '' . $wecmdenc2 . "','',document.we_form." . $this->uid . "_task_" . $counter . "_" . $counter1 . "_userid.value);");

				$yuiSuggest->setAcId('User_' . $counter . '_' . $counter1);
				$yuiSuggest->setContentType(we_users_user::TYPE_USER . ',' . we_users_user::TYPE_USER_GROUP);
				$yuiSuggest->setInput($this->uid . '_task_' . $counter . '_' . $counter1 . '_usertext', $foo, array('onchange' => 'top.content.setHot();'));
				$yuiSuggest->setMaxResults(10);
				$yuiSuggest->setMayBeEmpty(false);
				$yuiSuggest->setResult($this->uid . '_task_' . $counter . '_' . $counter1 . '_userid', $tv->userID);
				$yuiSuggest->setSelector(weSuggest::DocSelector);
				$yuiSuggest->setTable(USER_TABLE);
				$yuiSuggest->setWidth(200);
				$yuiSuggest->setContainerWidth(305);
				$yuiSuggest->setSelectButton($button, 6);

				$content[$counter][$counter1 + 3] = array(
					'dat' => '<table class="default" style="margin-top:1ex;">
						<tr style="vertical-align:middle"><td>' . $yuiSuggest->getHTML() . '</td>
						</tr></table>
						<table class="default">
						<tr style="vertical-align:top">
						<td class="middlefont" style="text-align:right">' . we_html_forms::checkboxWithHidden($tv->Mail, $this->uid . "_task_" . $counter . "_" . $counter1 . "_Mail", g_l('modules_workflow', '[send_mail]'), false, "middlefont", "top.content.setHot();") . '</td>
						<td class="middlefont" style="padding-left:20px;">' . we_html_forms::checkboxWithHidden($tv->Edit, $this->uid . "_task_" . $counter . "_" . $counter1 . "_Edit", g_l('modules_workflow', '[edit]'), false, "middlefont", "top.content.setHot();") . '</td>
						</tr></table>',
					'height' => '',
					'align' => ''
				);
				$counter1++;
			}
			++$counter;
		}
		return $ids .
				weSuggest::getYuiFiles() . '
<table style="margin-right:30px;">
	<tr style="vertical-align:top">
		<td>' . we_html_tools::htmlDialogBorder3(400, 300, $content, $headline) . '</td>
		<td><table class="default" style="margin-top:3px;">
			<tr><td>' . we_html_button::create_button(we_html_button::PLUS, "javascript:top.content.setHot();addTask()", true, 30) . we_html_button::create_button(we_html_button::TRASH, "javascript:top.content.setHot();delTask()", true, 30) . '</td>
			</tr>
			</table></td>
	</tr>
	<tr style="vertical-align:top">
		<td colspan="2">' . we_html_button::create_button(we_html_button::PLUS, "javascript:top.content.setHot();addStep()", true, 30) . we_html_button::create_button(we_html_button::TRASH, "javascript:top.content.setHot();delStep()", true, 30) . '</td></tr>
</table>' .
				$yuiSuggest->getYuiJs() .
				we_html_element::htmlHiddens(array(
					'wsteps' => $counter,
					'wtasks' => $counter1
		));
	}

	function getTypeTableHTML($head, $values, $ident = 0, $textalign = "left", $textclass = "defaultfont"){
		$out = '<table class="default">' . ($head ? '<tr><td class="' . trim($textclass) . '" style="text-align:' . trim($textalign) . '" colspan="2">' . $head . '</td></tr>' : '');
		foreach($values as $val){
			$out.='<tr><td class="' . trim($textclass) . '" style="padding-left:' . $ident . 'px;">' . $val . '</td></tr>';
		}
		$out.='</table>';
		return $out;
	}

	function getBoxHTML($w, $h, $content, $headline = "", $width = 120){
		$headline = str_replace(' ', '&nbsp;', $headline);
		if($headline){
			return '<table class="default" style="margin:15px 0px 15px 24px;">
			<tr>
				<td style="vertical-align:top" class="defaultfont lowContrast">' . $headline . '</td>
				<td>' . $content . '</td>
			</tr>
</table>';
		} else {
			return '<table class="default" style="margin:15px 0px 15px 24px;">
			<tr>
				<td>' . $content . '</td>
			</tr>
</table>';
		}
	}

	function getDocTypeHTML($width = 498){
		$vals = array();
		$dtq = we_docTypes::getDoctypeQuery($this->db);
		$this->db->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
		while($this->db->next_record()){
			$v = $this->db->f('ID');
			$t = $this->db->f('DocType');
			$vals[$v] = $t;
		}
		return we_html_tools::htmlFormElementTable(
						we_html_tools::htmlSelect($this->uid . '_DocType[]', $vals, 6, $this->workflowDef->DocType, true, array('onchange' => "top.content.setHot();"), "value", $width, "defaultfont"), g_l('modules_workflow', '[doctype]'));
	}

	/* creates the DirectoryChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formDirChooser($width = '', $rootDirID = 0, $table = FILE_TABLE, $Pathname = 'ParentPath', $Pathvalue = '', $IDName = 'ParentID', $IDValue = '', $cmd = ''){
		$cmd1 = "document.we_form.elements['" . $IDName . "'].value";
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory'," . $cmd1 . ",'" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, '', 'onchange="top.content.setHot();" readonly', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden($IDName, $IDValue), $button);
	}

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';
		return we_html_element::jsElement('
parent.document.title="' . $title . '";
WE().consts.g_l.workflow.view={
	save_changed_workflow:"' . g_l('modules_workflow', '[save_changed_workflow]') . '",
	save_question:"' . g_l('modules_workflow', '[save_question]') . '",
	delete_question:"' . g_l('modules_workflow', '[delete_question]') . '",
	nothing_to_delete: "' . we_message_reporting::prepareMsgForJS(g_l('modules_workflow', '[nothing_to_delete]')) . '",
	nothing_to_save: "' . we_message_reporting::prepareMsgForJS(g_l('modules_workflow', '[nothing_to_save]')) . '"
};
WE().consts.g_l.workflow.prop={
	worktime_empty: "' . addslashes(g_l('modules_workflow', '[worktime_empty]')) . '",
	user_empty:"' . addslashes(g_l('modules_workflow', '[user_empty]')) . '",
	del_last_step:"' . we_message_reporting::prepareMsgForJS(g_l('modules_workflow', '[del_last_step]')) . '",
	del_last_task:"' . we_message_reporting::prepareMsgForJS(g_l('modules_workflow', '[del_last_task]')) . '",
	name_empty:"' . we_message_reporting::prepareMsgForJS(g_l('modules_workflow', '[name_empty]')) . '",
	folders_empty:"' . we_message_reporting::prepareMsgForJS(g_l('modules_workflow', '[folders_empty]')) . '",
	doctype_empty:"' . we_message_reporting::prepareMsgForJS(g_l('modules_workflow', '[doctype_empty]')) . '",
	objects_empty:"' . we_message_reporting::prepareMsgForJS(g_l('modules_workflow', '[objects_empty]')) . '"
};
') .
				we_html_element::jsScript(WE_JS_MODULES_DIR . 'workflow/workflow_top.js');
	}

	function getCmdJS(){
		return we_html_element::jsElement('
function submitForm(){
	var f = self.document.we_form;
	f.target = "cmd";
	f.method = "post";
	f.submit();
}
');
	}

	function getJSProperty(){
		return we_html_element::jsElement('
var loaded;
var uid="' . $this->uid . '";
' . (!$this->show ? '

function getNumOfDocs(){
	return ' . $this->workflowDef->loadDocuments() . count($this->workflowDef->documents) . ';
}
' : '')) .
				we_html_element::jsScript(WE_JS_MODULES_DIR . 'workflow/workflow_property.js');
	}

	function processCommands(){
		switch(we_base_request::_(we_base_request::STRING, 'wcmd', '')){
			case 'new_workflow':
				$this->workflowDef = new we_workflow_workflow();
				$this->page = self::PAGE_PROPERTIES;
				echo we_html_element::jsElement('
top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=workflow&pnt=edheader";
top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=workflow&pnt=edfooter";
					');
				break;
			case 'add_cat':
				$arr = $this->workflowDef->Categories;
				if(($ids = we_base_request::_(we_base_request::INTLISTA, 'wcat', array()))){
					foreach($ids as $id){
						if(strlen($id) && (!in_array($id, $arr))){
							array_push($arr, $id);
						}
					}
					$this->workflowDef->Categories = $arr;
				}
				break;
			case 'del_cat':
				$arr = $this->workflowDef->Categories;
				if(($cat = we_base_request::_(we_base_request::INT, 'wcat'))){
					if(($pos = array_search($cat, $arr)) === false){
						break;
					}
					unset($arr[$pos]);
					$this->workflowDef->Categories = $arr;
				}
				break;
			case 'del_all_cats':
				$this->workflowDef->Categories = array();
				break;
			case 'add_objcat':
				$arr = $this->workflowDef->ObjCategories;
				if(($ids = we_base_request::_(we_base_request::INTLISTA, 'wocat', array()))){
					foreach($ids as $id){
						if((!in_array($id, $arr))){
							$arr[] = $id;
						}
					}
					$this->workflowDef->ObjCategories = $arr;
				}
				break;
			case 'del_objcat':
				$arr = $this->workflowDef->ObjCategories;
				if(($cat = we_base_request::_(we_base_request::INT, 'wcat'))){
					if(($pos = array_search($cat, $arr)) === false){
						break;
					}
					unset($arr[$pos]);
					$this->workflowDef->ObjCategories = $arr;
				}
				break;
			case 'del_all_objcats':
				$this->workflowDef->ObjCategories = array();
				break;
			case 'add_folder':
				$arr = $this->workflowDef->Folders;
				if(($ids = we_base_request::_(we_base_request::INTLISTA, 'wfolder')) !== false){
					foreach($ids as $id){
						if((!in_array($id, $arr))){
							$arr[] = $id;
						}
					}
					$this->workflowDef->Folders = $arr;
				}
				break;
			case 'del_folder':
				$arr = $this->workflowDef->Folders;
				if(($id = we_base_request::_(we_base_request::INT, 'wfolder')) !== false){
					if(($pos = array_search($id, $arr)) === false){
						break;
					}
					unset($arr[$pos]);
					$this->workflowDef->Folders = $arr;
				}
				break;
			case 'del_all_folders':
				$this->workflowDef->Folders = array();
				break;
			case 'add_object_file_folder':
				$arr = $this->workflowDef->ObjectFileFolders;
				if(($ids = we_base_request::_(we_base_request::INTLISTA, 'woffolder')) !== false){
					foreach($ids as $id){
						if((!in_array($id, $arr))){
							$arr[] = $id;
						}
					}
					$this->workflowDef->ObjectFileFolders = $arr;
				}
				break;
			case 'del_object_file_folder':
				$arr = $this->workflowDef->ObjectFileFolders;
				if(($id = we_base_request::_(we_base_request::INT, 'woffolder')) !== false){
					if(($pos = array_search($id, $arr)) === false){
						break;
					}
					unset($arr[$pos]);
					$this->workflowDef->ObjectFileFolders = $arr;
				}
				break;
			case 'del_all_object_file_folders':
				$this->workflowDef->ObjectFileFolders = array();
				break;
			case 'add_object':
				$arr = $this->workflowDef->Objects;
				if(($id = we_base_request::_(we_base_request::INT, 'wobject'))){
					$arr[] = $id;
					$this->workflowDef->Objects = $arr;
				}
				break;
			case 'del_object':
				$arr = $this->workflowDef->Objects;
				if(($id = we_base_request::_(we_base_request::INT, 'wobject'))){
					if(($pos = array_search($id, $arr)) === false){
						break;
					}
					unset($arr[$pos]);
					$this->workflowDef->Objects = $arr;
				}
				break;
			case 'del_all_objects':
				$this->workflowDef->Objects = array();
				break;
			case 'reload':
				echo we_html_element::jsElement('
					top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=workflow&pnt=edheader&page=' . $this->page . '&txt=' . $this->workflowDef->Text . '";
					top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=workflow&pnt=edfooter";
					');
				break;
			case 'workflow_edit':
				$this->show = 0;
				if(($id = we_base_request::_(we_base_request::INT, 'wid'))){
					$this->workflowDef = new we_workflow_workflow($id);
				}

				$_REQUEST['wcmd'] = 'reload';
				$this->processCommands();
				break;
			case 'switchPage':
				if(($page = we_base_request::_(we_base_request::INT, 'page')) !== false){
					$this->page = $page;
				}
				break;
			case 'save_workflow':
				if(we_base_request::_(we_base_request::INT, 'wid') !== false){
					$newone = (!$this->workflowDef->ID);
					$double = intval(f('SELECT COUNT(1) FROM ' . WORKFLOW_TABLE . ' WHERE Text="' . $this->db->escape($this->workflowDef->Text) . '"' . ($newone ? '' : ' AND ID!=' . intval($this->workflowDef->ID)), '', $this->db));

					if(!permissionhandler::hasPerm('EDIT_WORKFLOW') && !permissionhandler::hasPerm('NEW_WORKFLOW')){
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
						return;
					}
					if($newone && !permissionhandler::hasPerm('NEW_WORKFLOW')){
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
						return;
					}
					if($double){
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[double_name]'), we_message_reporting::WE_MESSAGE_ERROR));
						return;
					}
					$childs = '';
					$this->workflowDef->loadDocuments();
					foreach($this->workflowDef->documents as $v){
						$childs.="top.content.treeData.deleteEntry(" . $v["ID"] . ",'file');";
					}
					if(($dts = we_base_request::_(we_base_request::INTLISTA, $this->uid . '_DocType')) !== false){
						$this->workflowDef->DocType = $dts;
					}

					$this->workflowDef->save();
					echo we_html_element::jsElement(
							($newone ?
									'top.content.treeData.makeNewEntry({id:' . $this->workflowDef->ID . ',parentid:0,text:"' . $this->workflowDef->Text . '",open:1,contenttype:"folder",table:"we_workflow_workflowDef",published:"' . $this->workflowDef->Status . '"});' :
									'top.content.treeData.updateEntry({id:' . $this->workflowDef->ID . ',text:"' . $this->workflowDef->Text . '",published:"' . $this->workflowDef->Status . '"});'
							) . $childs .
							'top.content.editor.edheader.document.getElementById("headrow").innerHTML="' . addcslashes(we_html_element::htmlB(g_l('modules_workflow', '[workflow]') . ': ' . oldHtmlspecialchars($this->workflowDef->Text)), '"') . '";' .
							we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[save_ok]'), we_message_reporting::WE_MESSAGE_NOTICE)
					);
				}
				break;
			case 'show_document':
				if(($id = we_base_request::_(we_base_request::INT, 'wid'))){
					$this->show = 1;
					$this->page = self::PAGE_PROPERTIES;
					$this->documentDef->load($id);
					echo we_html_element::jsElement('
					top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=workflow&pnt=edheader&art=1&txt=' . $this->documentDef->document->Text . '";
					top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=workflow&pnt=edfooter&art=1";
					');
				}
				break;
			case 'delete_workflow':
				if(($id = we_base_request::_(we_base_request::INT, 'wid'))){
					if(!permissionhandler::hasPerm('DELETE_WORKFLOW')){
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
						return;
					}

					$this->workflowDef = new we_workflow_workflow($id);
					if($this->workflowDef->delete()){
						$this->workflowDef = new we_workflow_workflow();
						echo we_html_element::jsElement('top.content.treeData.deleteEntry(' . $id . ',"folder");' .
								we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[delete_ok]'), we_message_reporting::WE_MESSAGE_NOTICE));
					} else {
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[delete_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
					}
				}
				break;
			case 'reload_table':
				$this->page = self::PAGE_OVERVIEW;
				break;
			case 'empty_log':
				$stamp = 0;
				if(($t = we_base_request::_(we_base_request::INTLISTA, 'wopt', array()))){
					$stamp = mktime($t[3], $t[4], 0, $t[1], $t[0], $t[2]);
				}
				$this->Log->clearLog($stamp);
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[empty_log_ok]'), we_message_reporting::WE_MESSAGE_NOTICE));
				break;
			default:
		}
	}

	function processVariables(){
		$this->uid = we_base_request::_(we_base_request::STRING, 'wname', $this->uid);
		foreach($this->workflowDef->persistents as $val => $type){
			$varname = $this->uid . '_' . $val;
			if(($tmp = we_base_request::_($type, $varname, '_noval_')) !== '_noval_'){
				$this->workflowDef->$val = $tmp;
			}
		}

		$wsteps = we_base_request::_(we_base_request::INT, 'wsteps', 0);
		$wtasks = we_base_request::_(we_base_request::INT, 'wtasks', 0);
		$this->page = we_base_request::_(we_base_request::INT, 'page', self::PAGE_PROPERTIES);


		$this->workflowDef->steps = array();
		if($wsteps == 0){
			$this->workflowDef->addNewStep();
			$this->workflowDef->addNewTask();
		}

		for($i = 0; $i < $wsteps; $i++){
			$this->workflowDef->addNewStep();
		}
		for($j = 0; $j < $wtasks; $j++){
			$this->workflowDef->addNewTask();
		}

		foreach($this->workflowDef->steps as $skey => &$step){
			$step->workflowID = $this->workflowDef->ID;

			if(($tmp = we_base_request::_(we_base_request::INT, $this->uid . '_step' . $skey . '_sid'))){
				$step->ID = $tmp;
			}

			$step->stepCondition = we_base_request::_(we_base_request::BOOL, $this->uid . '_step' . $skey . '_and', $step->stepCondition);
			$step->Worktime = we_base_request::_(we_base_request::FLOAT, $this->uid . '_step' . $skey . '_Worktime', $step->Worktime);
			$step->timeAction = we_base_request::_(we_base_request::BOOL, $this->uid . '_step' . $skey . '_timeAction', $step->timeAction);

			foreach($step->tasks as $tkey => &$task){
				$task->stepID = $this->workflowDef->steps[$skey]->ID;
				if(($id = we_base_request::_(we_base_request::INT, $this->uid . '_task_' . $skey . '_' . $tkey . '_tid'))){
					$task->ID = $id;
				}

				$task->userID = we_base_request::_(we_base_request::INT, $this->uid . '_task_' . $skey . '_' . $tkey . '_userid', $task->userID);
				$task->username = we_base_request::_(we_base_request::STRING, $this->uid . '_task_' . $skey . '_' . $tkey . '_usertext', $task->username); //FIXME: this is a path to a user
				$task->Edit = we_base_request::_(we_base_request::BOOL, $this->uid . '_task_' . $skey . '_' . $tkey . '_Edit', $task->Edit);
				$task->Mail = we_base_request::_(we_base_request::BOOL, $this->uid . '_task_' . $skey . '_' . $tkey . '_Mail', $task->Mail);
			}
		}
	}

	function getDocumentInfo(){
		if($this->documentDef->workflow->Type == we_workflow_workflow::OBJECT){
			return $this->getObjectInfo();
		}

		//	Part - file-information
		$_parts = array(
			array(
				'headline' => g_l('weEditorInfo', '[content_type]'),
				'html' => g_l('weEditorInfo', '[' . $this->documentDef->document->ContentType . ']'),
				'space' => 100,
				'noline' => (($this->documentDef->document->ContentType != we_base_ContentTypes::FOLDER && $this->documentDef->workflow->Type != we_workflow_workflow::OBJECT) ? 1 : 0)
			)
		);
		if($this->documentDef->document->ContentType != we_base_ContentTypes::FOLDER && $this->documentDef->workflow->Type != we_workflow_workflow::OBJECT){
			$GLOBALS['we_doc'] = $this->documentDef->document;
			$fs = $this->documentDef->document->getFilesize($this->documentDef->document->Path);
			$_parts[] = array(
				'headline' => g_l('weEditorInfo', '[file_size]'),
				'html' => we_base_file::getHumanFileSize($fs) . '&nbsp;KB&nbsp;(' . we_base_file::getHumanFileSize($fs, we_base_file::SZ_BYTE) . ')',
				'space' => 100
			);
		}

		//	Part - publish-information
		$_parts[] = array(
			'headline' => g_l('weEditorInfo', '[creation_date]'),
			'html' => date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->CreationDate),
			'space' => 100,
			'noline' => 1
		);

		if($this->documentDef->document->CreatorID){
			$this->db->query('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . $this->documentDef->document->CreatorID);
			if($this->db->next_record()){
				$_parts[] = array(
					'headline' => g_l('modules_users', '[created_by]'),
					'html' => $this->db->f('First') . ' ' . $this->db->f('Second') . ' (' . $this->db->f('username') . ')',
					'space' => 100,
					'noline' => 1
				);
			}
		}

		$_parts[] = array(
			'headline' => g_l('weEditorInfo', '[changed_date]'),
			'html' => date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->ModDate),
			'space' => 100,
			'noline' => 1
		);

		if($this->documentDef->document->ModifierID){
			$this->db->query('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . $this->documentDef->document->ModifierID);
			if($this->db->next_record()){
				$_parts[] = array(
					'headline' => g_l('modules_users', '[changed_by]'),
					'html' => $this->db->f('First') . ' ' . $this->db->f('Second') . ' (' . $this->db->f('username') . ')',
					'space' => 100,
					'noline' => 1
				);
			}
		}

		if($this->documentDef->document->ContentType == we_base_ContentTypes::HTML || $this->documentDef->document->ContentType == we_base_ContentTypes::WEDOCUMENT){
			$_parts[] = array(
				'headline' => g_l('weEditorInfo', '[lastLive]'),
				'html' => ($this->documentDef->document->Published ? date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->Published) : '-'),
				'space' => 100
			);
		}

		//	Part - Path-information

		if($this->documentDef->document->Table != TEMPLATES_TABLE && $this->documentDef->workflow->Type != we_workflow_workflow::OBJECT){
			$rp = realpath($this->documentDef->document->getRealPath());
			$http = $this->documentDef->document->getHttpPath();

			switch($this->documentDef->document->ContentType){
				default:
					$showlink = false;
					break;
				case we_base_ContentTypes::HTML:
				case we_base_ContentTypes::WEDOCUMENT:
				case we_base_ContentTypes::IMAGE:
				case we_base_ContentTypes::FLASH:
				case we_base_ContentTypes::VIDEO:
				case we_base_ContentTypes::AUDIO:
					$showlink = true;
			}

			$_parts[] = array(
				'headline' => g_l('weEditorInfo', '[local_path]'),
				'html' => '<a href="#" style="text-decoration:none;cursor:text" class="defaultfont" onMouseOver="showtip(this,event,\'' . $rp . '\')" onMouseOut="hidetip()"  onclick="openToEdit(\'' . $this->documentDef->document->Table . '\',\'' . $this->documentDef->document->ID . '\',\'' . $this->documentDef->document->ContentType . '\')" >' . we_base_util::shortenPath($rp, 74) . '</a>',
				'space' => 100,
				'noline' => 1
			);

			$_parts[] = array(
				'headline' => g_l('weEditorInfo', '[http_path]'),
				'html' => ($showlink ? '<a href="' . $http . '" target="_blank" onMouseOver="showtip(this,event,\'' . $http . '\')" onMouseOut="hidetip()">' : '') . we_base_util::shortenPath($http, 74) . ($showlink ? '</a>' : ''),
				'space' => 100
			);
			$_parts[] = array(
				'headline' => '',
				'html' => '<a href="#" onclick="openToEdit(\'' . $this->documentDef->document->Table . '\',\'' . $this->documentDef->document->ID . '\',\'' . $this->documentDef->document->ContentType . '\')" >' . g_l('weEditorInfo', '[openDocument]') . '</a>',
				'space' => 100
			);
		}

		//	Logbook
		$_parts[] = array(
			'headline' => '',
			'html' => self::getDocumentStatus($this->documentDef->ID),
		);

		return we_html_element::jsScript(JS_DIR . 'tooltip.js') .
				we_html_element::jsElement('function openToEdit(tab,id,contentType){
		WE().layout.weEditorFrameController.openDocument(tab,id,contentType);
	}') .
				we_html_multiIconBox::getHTML('', $_parts, 30);
	}

	function getObjectInfo(){
		//	Dokument properties
		$_parts = array(
			array(
				'headline' => 'ID',
				'html' => $this->documentDef->document->ID,
				'space' => 150,
				'noline' => 1
			),
			array(
				'headline' => g_l('weEditorInfo', '[content_type]'),
				'html' => g_l('weEditorInfo', '[' . $this->documentDef->document->ContentType . ']'),
				'space' => 150,
			),
			// publish information
			array(
				'headline' => g_l('weEditorInfo', '[creation_date]'),
				'html' => date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->CreationDate),
				'space' => 150,
				'noline' => 1
			)
		);

		$this->db->query('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . $this->documentDef->document->CreatorID);
		if($this->db->next_record()){
			$_parts[] = array(
				'headline' => g_l('modules_users', '[created_by]'),
				'html' => $this->db->f('First') . ' ' . $this->db->f('Second') . ' (' . $this->db->f('username') . ')',
				'space' => 150,
				'noline' => 1
			);
		}

		$_parts[] = array(
			'headline' => g_l('weEditorInfo', '[changed_date]'),
			'html' => date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->ModDate),
			'space' => 150,
			'noline' => 1
		);

		$this->db->query('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . $this->documentDef->document->ModifierID);
		if($this->db->next_record()){
			$_parts[] = array(
				'headline' => g_l('modules_users', '[changed_by]'),
				'html' => $this->db->f('First') . ' ' . $this->db->f('Second') . ' (' . $this->db->f('username') . ')',
				'space' => 150,
				'noline' => 1
			);
		}

		$_parts[] = array(
			'headline' => g_l('weEditorInfo', '[lastLive]'),
			'html' => ($this->documentDef->document->Published ? date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->Published) : '-'),
			'space' => 150,
		);

		$_parts[] = array(
			'headline' => '',
			'html' => '<a href="#" onclick="openToEdit(\'' . $this->documentDef->document->Table . '\',\'' . $this->documentDef->document->ID . '\',\'' . $this->documentDef->document->ContentType . '\')" >' . g_l('weEditorInfo', '[openDocument]') . '</a>',
			'space' => 150
		);

		$_parts[] = array(
			'headline' => '',
			'html' => self::getDocumentStatus($this->documentDef->ID),
		);

		ob_start();
		require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

		return ob_end_clean() .
				we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				we_html_element::jsElement('
		function openToEdit(tab,id,contentType){
		WE().layout.weEditorFrameController.openDocument(tab,id,contentType);
	}') .
				'</head>
		<body class="weEditorBody" onunload="doUnload()">
				<form name="we_form">' . we_class::hiddenTrans() . '<table cellpadding="6">' .
				we_html_multiIconBox::getHTML('', $_parts, 30) .
				'</form></body></html>';
	}

	function getTime($seconds){
		$min = floor($seconds / 60);
		$ret = array(
			'hour' => floor($min / 60),
			'min' => $min,
			'sec' => $seconds - ($min * 60));
		$ret['min'] -= ($ret['hour'] * 60);
		return $ret;
	}

	static function getDocumentStatus($workflowDocID){
		$db = new DB_WE();
		$headline = array(array('dat' => '<div class="middlefont">' . g_l('modules_workflow', '[step]') . '</div>'));

		$workflowDocument = new we_workflow_document($workflowDocID);

		$counter = 0;
		$counter1 = 0;
		$current = $workflowDocument->findLastActiveStep();
		if($current < 0){
			return g_l('modules_workflow', '[cannot_find_active_step]');
		}
		foreach($workflowDocument->steps as $sk => $sv){

			$workflowStep = new we_workflow_step($sv->workflowStepID);

			/* $now = date(g_l('weEditorInfo', '[date_format]'), time());
			  $start = date(g_l('weEditorInfo', '[date_format]'), $sv->startDate);
			 */
			$elapsed = self::getTime(time() - $sv->startDate);
			$remained = self::getTime(($sv->startDate + round($workflowStep->Worktime * 3600)) - time());

			if($remained['hour'] < 0){
				if($sk > $current){
					$finished_font = 'middlefont';
					$notfinished_font = 'middlefont lowContrast';
				} else {
					$finished_font = 'middlefont highlightElementChanged';
					$notfinished_font = 'middlefont highlightElementChanged';
				}
			} else {
				$finished_font = 'middlefont';
				$notfinished_font = 'middlefont lowContrast';
			}

			$end = date(g_l('weEditorInfo', '[date_format]'), $sv->startDate + round($workflowStep->Worktime * 3600));

			$content[$counter] = array(
				array(
					'dat' => ($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . ($counter + 1) . "</div>",
					'height' => '',
					'align' => 'center'
				)
			);

			$counter1 = 0;
			foreach($sv->tasks as $tk => $tv){

				$headline[++$counter1]['dat'] = g_l('modules_workflow', '[user]') . (string) ($counter1);

				$workflowTask = new we_workflow_task($tv->workflowTaskID);

				$foo = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($workflowTask->userID), '', $db);

				if($sk == $current){
					$out = ($tv->Status == we_workflow_documentTask::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . $foo . "</div>";
				} else if($sk < $current){
					$out = '<div class="' . $finished_font . '">' . $foo . '</div>';
				} else {
					$out = '<div class="' . $notfinished_font . '">' . $foo . '</div>';
				}

				$content[$counter][$counter1] = array(
					'dat' => $out,
					'height' => '',
					'align' => '',
				);
			}


			$headline[++$counter1] = array('dat' => g_l('modules_workflow', '[worktime]'));

			$content[$counter][$counter1] = array(
				'dat' => ($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . $workflowStep->Worktime . '</div>',
				'height' => '',
				'align' => 'right');

			if($sk <= $current){
				$headline[++$counter1]['dat'] = g_l('modules_workflow', '[time_elapsed]');


				$content[$counter][$counter1] = array(
					'dat' => ($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . $elapsed["hour"] . ":" . $elapsed["min"] . ":" . $elapsed["sec"] . "</div>",
					'height' => '',
					'align' => 'right',
				);

				$headline[++$counter1]['dat'] = g_l('modules_workflow', '[time_remained]');

				$content[$counter][$counter1] = array(
					'dat' => ($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . $remained["hour"] . ":" . $remained["min"] . ":" . $remained["sec"] . "</div>",
					'height' => '',
					'align' => 'right',
				);

				$headline[++$counter1]['dat'] = g_l('modules_workflow', '[step_plan]');

				$content[$counter][$counter1] = array(
					'dat' => ($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . $end . "</div>",
					'height' => '',
					'align' => 'right',
				);
			}
			++$counter;
		}

		$wfType = f('SELECT ' . WORKFLOW_TABLE . '.Type as Type FROM ' . WORKFLOW_TABLE . ',' . WORKFLOW_DOC_TABLE . ' WHERE ' . WORKFLOW_DOC_TABLE . '.workflowID=' . WORKFLOW_TABLE . '.ID AND ' . WORKFLOW_DOC_TABLE . '.ID=' . intval($workflowDocument->ID), 'Type', $db);
		return '<table class="default" style="margin-right:15px;">
		<tr><td>' . we_html_tools::htmlDialogBorder3(730, 300, $content, $headline) . '</td></tr>
		<tr><td style="padding-top:10px;">' . we_html_button::create_button('logbook', "javascript:new (WE().util.jsWindow)(window, '" . WEBEDITION_DIR . 'we_showMod.php?mod=wrokflow&pnt=log&art=' . $workflowDocument->document->ID . "&type=" . $wfType . "','workflow_history',-1,-1,640,480,true,false,true);") . '</td></tr>		</table>';
	}

	static function getLogForDocument($docID, $type = 0){//type is an string-array
		$db = new DB_WE();

		$content = array();

		$headlines = array(
			array('dat' => g_l('modules_workflow', '[action]')),
			array('dat' => g_l('modules_workflow', '[description]')),
			array('dat' => g_l('modules_workflow', '[time]')),
			array('dat' => g_l('modules_workflow', '[user]')),
		);

		$logs = we_workflow_log::getLogForDocument($docID, 'DESC', $type);
		$offset = we_base_request::_(we_base_request::INT, 'offset', 0);
		$art = we_base_request::_(we_base_request::INT, 'art', '');
		$numRows = we_workflow_log::NUMBER_LOGS;
		$anz = $GLOBALS['ANZ_LOGS'];

		foreach($logs as $v){
			$foo = getHash('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . intval($v['userID']), $db);
			$content[] = array(
				array(
					'dat' => '<div class="middlefont">' . $v['Type'] . '</div>',
					'height' => '',
					'align' => '',
				),
				array(
					'dat' => '<div class="middlefont">' . $v['Description'] . '</div>',
					'height' => '',
					'align' => '',
				),
				array(
					'dat' => '<div class="middlefont"><nobr>' . date(g_l('weEditorInfo', '[date_format]'), $v['logDate']) . '</nobr></div>',
					'height' => '',
					'align' => 'right',
				),
				array(
					'dat' => '<div class="middlefont">' . ((!empty($foo['First'])) ? $foo['First'] : '-') . ' ' . ((!empty($foo['Second'])) ? $foo['Second'] : '-') . ((!empty($foo['username'])) ? ' (' . $foo['username'] . ')' : '') . '</div>',
					'height' => '',
					'align' => 'left',
				),
			);
		}

		$nextprev = '<table class="default"><tr><td>' .
				($offset ?
						we_html_button::create_button(we_html_button::BACK, WEBEDITION_DIR . 'we_showMod.php?mod=workflow&pnt=log&art=' . $art . '&type=' . $type . '&offset=' . ($offset - $numRows)) :
						we_html_button::create_button(we_html_button::BACK, '', false, 100, 22, '', '', true)
				) .
				'</td><td class="defaultfont" style="padding: 0 10px 0 10px;"><b>' . (($anz) ? $offset + 1 : 0) . '-' .
				(($anz - $offset) < $numRows ? $anz : $offset + $numRows) . ' ' . g_l('global', '[from]') . ' ' . $anz . '</b></td><td>' .
				((($offset + $numRows) < $anz) ?
						we_html_button::create_button(we_html_button::NEXT, WEBEDITION_DIR . 'we_showMod.php?mod=workflow&pnt=log&art=' . $art . '&type=' . $type . '&offset=' . ($offset + $numRows)/* . "&order=$order" */) :
						we_html_button::create_button(we_html_button::NEXT, '', '', 100, 22, '', '', true)
				) .
				'</td><td></tr></table>';

		$buttonsTable = '<table style="width:100%" class="default"><tr><td>' . $nextprev . '</td><td style="text-align:right">' . we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();") . '</td></tr></table>';


		return ($logs ?
						we_html_tools::htmlDialogLayout(we_html_tools::htmlDialogBorder3(580, 300, $content, $headlines), '', $buttonsTable) :
						we_html_tools::htmlDialogLayout('<div style="width:500px;text-align:center" class="middlefont">-- ' . g_l('modules_workflow', '[log_is_empty]') . ' --</div>', '', we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();")));
	}

	function getLogQuestion(){
		$vals = array('<table class="default" style="margin-left:22px;"><tr><td>' . we_html_tools::getDateInput("log_time%s", (time() - (336 * 3600))) . '</td></tr></table>');

		return we_html_element::jsElement('
			function clear(){
				opener.top.content.cmd.document.we_form.wcmd.value="empty_log";
				if(document.we_form.clear_opt.value==1){
					var day=document.we_form.log_time_day.options[document.we_form.log_time_day.selectedIndex].text;
					var month=document.we_form.log_time_month.options[document.we_form.log_time_month.selectedIndex].text;
					var year=document.we_form.log_time_year.options[document.we_form.log_time_year.selectedIndex].text;
					var hour=document.we_form.log_time_hour.options[document.we_form.log_time_hour.selectedIndex].text;
					var min=document.we_form.log_time_minute.options[document.we_form.log_time_minute.selectedIndex].text;

					var timearr=[day,month,year,hour,min];
					opener.top.content.cmd.document.we_form.wopt.value=timearr.join();
				}
				else{
					if(!confirm("' . g_l('modules_workflow', '[emty_log_question]') . '")) return;
				}
				opener.top.content.cmd.submitForm();
				close();
			}
			self.focus();
		') .
				we_html_tools::htmlDialogLayout(
						we_html_element::htmlHidden('clear_opt', 1) .
						'<form name="we_form">' .
						'<table class="default">' .
						'<tr><td class="defaultfont" style="padding-bottom:10px;">' . g_l('modules_workflow', '[log_question_text]') . '</td></tr>' .
						'<tr><td>' . $this->getTypeTableHTML(we_html_forms::radiobutton(1, true, 'clear_time', g_l('modules_workflow', '[log_question_time]'), true, 'defaultfont', "javascript:document.we_form.clear_opt.value=1;"), $vals) . '</td></tr>' .
						'<tr><td style="padding-top:1em;">' . we_html_forms::radiobutton(0, false, 'clear_time', g_l('modules_workflow', '[log_question_all]'), true, 'defaultfont', "javascript:document.we_form.clear_opt.value=0;") . '</td></tr>' .
						'</table>'
						, g_l('modules_workflow', '[empty_log]'), we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, 'javascript:self.clear();'), '', we_html_button::create_button(we_html_button::CANCEL, 'javascript:self.close();')
						)
				) . '</form>';
	}

	static function getFooter(){

	}

	static function showFooterForNormalMode($we_doc, $showPubl){
		$_col = 0;

		$_footerTable = new we_html_table(array('class' => 'default'), 1, 0);

		$_publishbutton = '';
		//	decline
		$_footerTable->addCol(2);
		$_footerTable->setColContent(0, $_col++, we_html_button::create_button(self::BUTTON_DECLINE, "javascript:decline_workflow();"));

		if(we_workflow_utility::isWorkflowFinished($we_doc->ID, $we_doc->Table) || ((1 + we_workflow_utility::findLastActiveStep($we_doc->ID, $we_doc->Table)) == count(we_workflow_utility::getNumberOfSteps($we_doc->ID, $we_doc->Table)) && permissionhandler::hasPerm("PUBLISH"))){
			$_publishbutton = we_html_button::create_button(we_html_button::PUBLISH, "javascript:workflow_finish();");
		} else {
			$_footerTable->addCol(2);
			$_footerTable->setColContent(0, $_col++, we_html_button::create_button(self::BUTTON_FORWARD, "javascript:pass_workflow();"));
		}

		if(we_workflow_utility::canUserEditDoc($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"]) && $we_doc->userCanSave()){
			$_footerTable->addCol(2);
			$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::SAVE, "javascript:_EditorFrame.setEditorPublishWhenSave(false);we_save_document();"));
		}

		if($_publishbutton){
			$_footerTable->addCol(2);
			$_footerTable->setColContent(0, $_col++, $_publishbutton);
		} elseif(we_workflow_utility::canUserEditDoc($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"]) && $we_doc->userCanSave()){

			if($showPubl && (!isset($we_doc->IsClassFolder) || !$we_doc->IsClassFolder)){
				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::PUBLISH, "javascript:_EditorFrame.setEditorPublishWhenSave(true);we_save_document();"));
			}
		}

		return $_footerTable->getHtml();
	}

	static function showFooterForSEEMMode($we_doc, $showPubl){
		$_col = 0;
		$_gap = 16;
		$_footerTable = new we_html_table(array('class' => 'default'), 1, 0);

		switch($we_doc->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PREVIEW:
				//	Edit-Button
				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::EDIT, "javascript:parent.editHeader.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_CONTENT . ",'" . $GLOBALS["we_transaction"] . "');"));

				//	Decline Workflow
				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, we_html_button::create_button(self::BUTTON_DECLINE, "javascript:decline_workflow();"));

				$_footerTable->addCol(2);
				if(we_workflow_utility::isWorkflowFinished($we_doc->ID, $we_doc->Table) || ((1 + we_workflow_utility::findLastActiveStep($we_doc->ID, $we_doc->Table)) == count(we_workflow_utility::getNumberOfSteps($we_doc->ID, $we_doc->Table)) && permissionhandler::hasPerm("PUBLISH"))){
					$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::PUBLISH, "javascript:workflow_finish();"));
				} else {
					$_footerTable->setColContent(0, $_col++, we_html_button::create_button(self::BUTTON_FORWARD, "javascript:pass_workflow();"));
				}
				break;
			case we_base_constants::WE_EDITPAGE_CONTENT:

				//	Preview Button
				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::PREVIEW, "javascript:parent.editHeader.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $GLOBALS["we_transaction"] . "');"));

				//	Propertie-button
				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, we_html_button::create_button('properties', "javascript:parent.editHeader.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PROPERTIES . ",'" . $GLOBALS["we_transaction"] . "');"));

				//	Decline Workflow
				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, we_html_button::create_button(self::BUTTON_DECLINE, "javascript:decline_workflow();"));

				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, (we_workflow_utility::isWorkflowFinished($we_doc->ID, $we_doc->Table) || ((1 + we_workflow_utility::findLastActiveStep($we_doc->ID, $we_doc->Table)) == count(we_workflow_utility::getNumberOfSteps($we_doc->ID, $we_doc->Table)) && permissionhandler::hasPerm("PUBLISH")) ?
								we_html_button::create_button(we_html_button::PUBLISH, "javascript:workflow_finish();") :
								we_html_button::create_button(self::BUTTON_FORWARD, "javascript:pass_workflow();"))
				);

				if(we_workflow_utility::canUserEditDoc($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"]) && $we_doc->userCanSave()){
					$_footerTable->addCol(2);
					$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::SAVE, "javascript:_EditorFrame.setEditorPublishWhenSave(false);we_save_document();"));
					if($showPubl && (!isset($we_doc->IsClassFolder) || !$we_doc->IsClassFolder)){
						$_footerTable->addCol(2);
						$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::PUBLISH, "javascript:_EditorFrame.setEditorPublishWhenSave(true);we_save_document();"));
					}
				}
				break;

			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::PREVIEW, "javascript:parent.editHeader.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $GLOBALS["we_transaction"] . "');"));

				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::PREVIEW, "javascript:parent.editHeader.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $GLOBALS["we_transaction"] . "');"));

				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, we_html_button::create_button(self::BUTTON_DECLINE, "javascript:decline_workflow();"));

				$_footerTable->addCol(2);
				$_footerTable->setColContent(0, $_col++, (we_workflow_utility::isWorkflowFinished($we_doc->ID, $we_doc->Table) || ((1 + we_workflow_utility::findLastActiveStep($we_doc->ID, $we_doc->Table)) == count(we_workflow_utility::getNumberOfSteps($we_doc->ID, $we_doc->Table)) && permissionhandler::hasPerm("PUBLISH")) ?
								we_html_button::create_button(we_html_button::PUBLISH, "javascript:workflow_finish();") :
								we_html_button::create_button(self::BUTTON_FORWARD, "javascript:pass_workflow();"))
				);

				if(we_workflow_utility::canUserEditDoc($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"]) && $we_doc->userCanSave()){
					$_footerTable->addCol(2);
					$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::SAVE, "javascript:_EditorFrame.setEditorPublishWhenSave(false);we_save_document();"));

					if($showPubl && (!isset($we_doc->IsClassFolder) || !$we_doc->IsClassFolder)){
						$_footerTable->addCol(2);
						$_footerTable->setColContent(0, $_col++, we_html_button::create_button(we_html_button::PUBLISH, "javascript:_EditorFrame.setEditorPublishWhenSave(true);we_save_document();"));
					}
				}
		}
		return $_footerTable->getHtml();
	}

	public function getHomeScreen(){
		$content = we_html_button::create_button('fat:new_workflow,fa-lg fa-gears', "javascript:top.opener.top.we_cmd('new_workflow');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_WORKFLOW"));

		return parent::getActualHomeScreen('workflow', "workflow.gif", $content, '<form name="we_form">' . $this->getHiddens() . '</form>');
	}

}
