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
class we_backup_wizard{
	const BACKUP = 1;
	const RECOVER = 2;

	private $mode; //1-backup;2-recover
	private $frameset;
	private $fileUploader = null;

	function __construct($mode = self::BACKUP){
		$this->frameset = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . ($mode == self::BACKUP ? 'make_backup' : 'recover_backup');
		$this->mode = $mode;
	}

	private static function getJSDep($mode, $docheck, $doclick, $douncheck = ''){
		return
			we_html_element::jsScript(JS_DIR . 'backup_wizard.js') .
			we_html_element::jsElement('
WE().consts.g_l.backupWizard={
	temporary_dep:"' . we_message_reporting::prepareMsgForJS(g_l('backup', '[' . $mode . '_temporary_dep]')) . '",
	versions_dep:"' . we_message_reporting::prepareMsgForJS(g_l('backup', '[' . $mode . '_versions_dep]')) . '",
	versions_binarys_dep:"' . we_message_reporting::prepareMsgForJS(g_l('backup', '[' . $mode . '_versions_binarys_dep]')) . '",
	binary_dep:"' . we_message_reporting::prepareMsgForJS(g_l('backup', '[' . $mode . '_binary_dep]')) . '",
	schedule_dep:"' . we_message_reporting::prepareMsgForJS(g_l('backup', '[' . $mode . '_schedule_dep]')) . '",
	shop_dep:"' . we_message_reporting::prepareMsgForJS(g_l('backup', '[' . $mode . '_shop_dep]')) . '",
	workflow_dep:"' . we_message_reporting::prepareMsgForJS(g_l('backup', '[' . $mode . '_workflow_dep]')) . '",
	todo_dep:"' . we_message_reporting::prepareMsgForJS(g_l('backup', '[' . $mode . '_todo_dep]')) . '",
  newsletter_dep:"' . we_message_reporting::prepareMsgForJS(g_l('backup', '[' . $mode . '_newsletter_dep]')) . '",
	banner_dep:"' . we_message_reporting::prepareMsgForJS(g_l('backup', '[' . $mode . '_banner_dep]')) . '",
	workflow_data:"' . g_l('backup', '[' . $mode . '_workflow_data]') . '",
	newsletter_data:"' . g_l('backup', '[' . $mode . '_newsletter_data]') . '",
	schedule_data:"' . g_l('backup', '[' . $mode . '_schedule_data]') . '",
	versions_data:"' . g_l('backup', '[' . $mode . '_versions_data]') . '",
	versions_binarys_data:"' . g_l('backup', '[' . $mode . '_versions_binarys_data]') . '",
	temporary_data:"' . g_l('backup', '[' . $mode . '][temporary_data]') . '",
	history_data:"' . g_l('backup', '[' . $mode . '][history_data]') . '",
	todo_data:"' . g_l('backup', '[' . $mode . '_todo_data]') . '",
	shop_data:"' . g_l('backup', '[' . $mode . '_shop_data]') . '",
	unselect_dep2:"' . g_l('backup', '[unselect_dep2]') . '",
	unselect_dep3:"' . g_l('backup', '[unselect_dep3]') . '",
	core_data:"' . g_l('backup', '[' . $mode . '_core_data]') . '",
	object_data:"' . g_l('backup', '[' . $mode . '_object_data]') . '",
	versions_data:"' . g_l('backup', '[' . $mode . '_versions_data]') . '",
	binary_data:"' . g_l('backup', '[' . $mode . '_binary_data]') . '",
	user_data:"' . g_l('backup', '[' . $mode . '_user_data]') . '",
	customer_data:"' . g_l('backup', '[' . $mode . '_customer_data]') . '",
};

function doCheck(opt){
	switch (opt) {
		' . $docheck . '
	}
}

function doUnCheck(opt){
	switch (opt) {
		' . $douncheck . '
	}
}

function doClick(opt) {
	switch (opt) {
		' . $doclick . '
	}
	doClicked(a.checked,opt);
	if (!a.checked){

	}
}');
	}

	function getHTMLFrameset(){
		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_' . ($this->mode == self::BACKUP ? 'backup' : 'recover') . '_title]'), '', '', STYLESHEET, we_html_element::htmlBody(array('id' => 'weMainBody')
					, we_html_element::htmlIFrame('body', $this->frameset . '&pnt=body', 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;', 'border:0px;width:100%;height:100%;') .
					we_html_element::htmlIFrame('busy', $this->frameset, 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden', '', '', false) .
					we_html_element::htmlIFrame('cmd', $this->frameset . '&pnt=cmd')
				)
		);
	}

	function getHTMLStep($step){
		switch($this->mode){
			case self::BACKUP:
				$step = 'getHTMLBackupStep' . $step;
				return $this->{$step}();
			case self::RECOVER:
				$step = 'getHTMLRecoverStep' . $step;
				return $this->{$step}();
		}
	}

	function getHTMLRecoverStep1(){
		$parts = array(
			array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[save_before]'), we_html_tools::TYPE_ALERT, 600), 'noline' => 1),
			array("headline" => "", "html" => g_l('backup', '[save_question]'), 'noline' => 1),
		);

		$js = we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
function startStep(){
	self.focus();
	top.busy.location="' . $this->frameset . '&pnt=busy&step=1";
}');

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', $js . STYLESHEET, we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "startStep()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_multiIconBox::getHTML("backup_options", $parts, 30, "", -1, "", "", false, g_l('backup', '[step1]'))
					)
				)
		);
	}

	function getHTMLRecoverStep2(){
		$js = we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
function we_submitForm(target,url) {
	var f = self.document.we_form;
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
}

function startStep(){
	top.busy.location="' . $this->frameset . '&pnt=busy&step=2";
}

self.focus();
		');
		$parts = array(
			array("headline" => "", "html" => we_html_forms::radiobutton("import_server", true, "import_from", g_l('backup', '[import_from_server]')), 'noline' => 1),
			array("headline" => "", "html" => we_html_forms::radiobutton("import_upload", false, "import_from", g_l('backup', '[import_from_local]')), 'noline' => 1)
		);

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', $js . STYLESHEET, we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "startStep();"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHiddens(array("pnt" => "body", "step" => 3)) .
						we_html_multiIconBox::getHTML("backup_options", $parts, 30, "", -1, "", "", false, g_l('backup', '[step2]'))
					)
				)
		);
	}

	function getHTMLRecoverStep3(){
		if(isset($_SESSION['weS']['weBackupVars'])){
			// workaround for php bug #18071
			// bug: session has been restarted
			$_SESSION['weS']['weBackupVars'] = array();
			// workaround end
			unset($_SESSION['weS']['weBackupVars']);
		}

		$this->fileUploader = new we_fileupload_ui_base('we_upload_file');
		$this->fileUploader->setTypeCondition('accepted', array(we_base_ContentTypes::XML), array('.gz', '.tgz'));
		$this->fileUploader->setCallback('top.body.startImport(true)');
		$this->fileUploader->setInternalProgress(array('isInternalProgress' => true, 'width' => 300));
		$this->fileUploader->setDimensions(array('width' => 500, 'alertBoxWidth' => 600, 'dragWidth' => 594, 'dragHeight' => 70, 'marginTop' => 5));
		$this->fileUploader->setGenericFileName(TEMP_DIR . we_fileupload::REPLACE_BY_FILENAME);

		$js = "";

		$maxsize = $this->fileUploader->getMaxUploadSize();

		if(we_base_request::_(we_base_request::STRING, "import_from") === 'import_upload'){
			if($maxsize || $this->fileUploader){
				//FIXME:
				$fileUploaderHead = $this->fileUploader->getCss() . $this->fileUploader->getJs();
				$inputTypeFile = $this->fileUploader->getHTML();

				$parts = array(
					array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[charset_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1),
					(DEFAULT_CHARSET ? null : array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[defaultcharset_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1)),
					array("headline" => "", "html" => $this->fileUploader->getHtmlAlertBoxes(), 'noline' => 1),
					array("headline" => "", "html" => $inputTypeFile, 'noline' => 1)
				);
			}
		} else {
			$js = '
function setLocation(loc){
	location.href=loc;
}
extra_files=[];
extra_files_desc=[];';
			$select = new we_html_select(array("name" => "backup_select", "size" => 7, "style" => "width: 600px;"));
			$files = array();
			$extra_files = array();
			$dateformat = g_l('date', '[format][default]');
			for($i = 0; $i <= 1; $i++){
				$adddatadir = ($i == 0 ? '' : 'data/');
				$dstr = BACKUP_PATH . $adddatadir;
				$d = dir($dstr);
				while(($entry = $d->read())){
					switch($entry){
						case '.':
						case '..':
						case 'download':
						case 'tmp':
						case 'lastlog.php':
						case '.htaccess':
							continue 2;
						default:
							if(@is_dir($dstr . $entry)){
								continue 2;
							}
					}

					$filename = $dstr . $entry;
					$filesize = we_base_file::getHumanFileSize(filesize($filename));
					$filedate = date($dateformat, filemtime($filename));
					if(strpos($entry, 'weBackup_') !== 0){
						$extra_files[$adddatadir . $entry] = $entry . " $filedate $filesize";
						continue;
					}
					$ts = str_replace(array('.php', '.xml', '.gz', '.bz', '.zip'), '', preg_replace('|^weBackup_|', '', $entry));

					if(is_numeric($ts) && !($ts < 1004569200)){//old Backup
						$comp = we_base_file::getCompression($entry);
						$files[$adddatadir . $entry] = /* g_l('backup', '[backup_form]') . ' ' . */ date($dateformat, $ts) . ($comp && $comp != "none" ? " ($comp)" : "") . " " . $filesize;
						continue;
					}

					if(substr_count($ts, '_') > 5){
						$matches = array();
						if(preg_match('|([^_]*)_(\d{4})_(\d{1,2})_(\d{1,2})__(\d{1,2})_(\d{1,2})_?([\d-]*)|', $ts, $matches)){
							list(, $url, $year, $month, $day, $hour, $min, $wever) = $matches;
							$filedate = date($dateformat, mktime($hour, $min, 0, $month, $day, $year));
						} else {
							$url = $wever = '';
						}
						$comp = we_base_file::getCompression($entry);
						$files[$adddatadir . $entry] = /* g_l('backup', '[backup_form]') . ' ' . */ $filedate . ($url ? ' - ' . $url : '') . ($wever ? ' (WE: ' . str_replace('-', '.', $wever) . ')' : '') . ($comp && $comp != 'none' ? ' (' . $comp . ')' : '') . " " . $filesize;
						continue;
					}

					$extra_files[$adddatadir . $entry] = $entry . " $filedate $filesize";
				}
			}
			$d->close();

			krsort($files);
			asort($extra_files);
			$i = 0;

			/* foreach($files as $fk=>$fv)	$select->addOption($fk,$fv); */

			$default = we_html_select::getNewOptionGroup(array('class' => 'bold', 'style' => 'font-style: normal; color: darkblue;', 'label' => g_l('backup', '[we_backups]')));
			$other = we_html_select::getNewOptionGroup(array('class' => 'bold', 'style' => 'font-style: normal; color: darkblue;', 'label' => g_l('backup', '[other_files]')));

			foreach($files as $fk => $fv){
				if(strlen($fv) > 75){
					$fv = addslashes(substr($fv, 0, 65) . '...' . substr($fv, -10));
				}
				$default->addChild(we_html_select::getNewOption($fk, $fv));
			}
			foreach($extra_files as $fk => $fv){
				if(strlen($fv) > 75){
					$fv = addslashes(substr($fv, 0, 65) . '...' . substr($fv, -10));
				}
				$other->addChild(we_html_select::getNewOption($fk, $fv));
			}

			$select->addChild($default);
			$select->addChild($other);

			foreach($extra_files as $fk => $fv){
				$js.='extra_files["' . $i . '"]="' . $fk . '";
						extra_files_desc["' . $i . '"]="' . $fv . '"
				';
				$i++;
			}

			$parts = array(
				array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[charset_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1),
				(DEFAULT_CHARSET ? null : array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[defaultcharset_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1) ),
				array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[old_backups_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1),
				array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[select_server_file]'), we_html_tools::TYPE_INFO, 600, false), 'noline' => 1),
				array("headline" => "", "html" => $select->getHtml(), 'noline' => 1),
				//array("headline"=>"","html"=>we_html_forms::checkbox(1, false, "show_all", g_l('backup',"[show_all]"), false, "defaultfont", "showAll()"),"space"=>0,"noline"=>1);
				array("headline" => "", "html" => we_html_button::create_button('delete_backup', "javascript:delSelected();", true, 100, 22, '', '', false, false),)
			);
		}

		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "rebuild", g_l('backup', '[rebuild]'), false),);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[delold_notice]'), we_html_tools::TYPE_QUESTION, 600, false) .
			we_html_button::create_button('delold', "javascript:delOldFiles();", true, 100, 22, '', '', false, false), 'noline' => 1);

		$form_properties = array(
			10 => 'handle_core',
			11 => defined('OBJECT_TABLE') ? 'handle_object' : '',
			12 => 'handle_versions',
			13 => 'handle_versions_binarys',
			14 => 'handle_binary',
			20 => 'handle_user',
			25 => defined('CUSTOMER_TABLE') ? 'handle_customer' : '',
			30 => defined('SHOP_TABLE') ? 'handle_shop' : '',
			35 => defined('WORKFLOW_TABLE') ? 'handle_workflow' : '',
			40 => defined('MESSAGING_SYSTEM') ? 'handle_todo' : '',
			45 => defined('NEWSLETTER_TABLE') ? 'handle_newsletter' : '',
			50 => defined('BANNER_TABLE') ? 'handle_banner' : '',
			55 => we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) ? 'handle_schedule' : '',
			60 => we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT) ? 'handle_export' : '',
			65 => defined('VOTING_TABLE') ? 'handle_voting' : '',
			//70 => defined('SPELLCHECKER') ? 'handle_spellchecker' : '',
			75 => defined('GLOSSARY_TABLE') ? 'handle_glossary' : '',
			98 => 'handle_hooks',
			99 => 'handle_customtags',
			100 => 'handle_settings',
			101 => 'handle_temporary',
			102 => 'handle_history',
			300 => 'handle_extern',
			310 => 'convert_charset',
			320 => 'backup_log'
		);

		$i = 0;
		$_tools = we_tool_lookup::getToolsForBackup();
		foreach($_tools as $_tool){
			$form_properties[700 + $i] = 'handle_tool[' . $_tool . ']';
			$i++;
		}

		ksort($form_properties);

		$parts[] = array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[import_options]'), we_html_tools::TYPE_INFO, 600, false), 'space' => 70, 'noline' => 1);

		$docheck = $douncheck = $doclick = '';
		foreach($form_properties as $k => $v){
			if(!$v){
				continue;
			}
			$docheck.='
				case ' . $k . ':
					document.we_form.' . $v . '.checked=true;
					doClick(' . $k . ');
				break;
			';
			$douncheck.='
				case ' . $k . ':
					document.we_form.' . $v . '.checked=false;
					doClick(' . $k . ');
				break;
			';

			$doclick.='
				case ' . $k . ':
					var a=document.we_form.' . $v . ';
				break;
			';
			if($k > 2 && $k < 101){
				$parts[] = array('headline' => '', 'html' => we_html_forms::checkbox(1, true, $v, g_l('backup', '[' . str_replace('handle', 'import', $v) . '_data]'), false, 'defaultfont', "doClick($k);"), 'space' => 70, 'noline' => 1);
			}
		}

		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "handle_temporary", g_l('backup', '[import][temporary_data]'), false, "defaultfont", "doClick(101);"), 'space' => 70, 'noline' => 1);

		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "handle_history", g_l('backup', '[import][history_data]'), false, "defaultfont", "doClick(102);"), 'space' => 70, 'noline' => 1);


		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[tools_import_desc]'), we_html_tools::TYPE_INFO, 600, false), 'space' => 70, 'noline' => 1);
		foreach($_tools as $_tool){
			$text = ($_tool === 'weSearch' ?
					g_l('searchtool', '[import_tool_' . $_tool . '_data]') :
					g_l('backup', '[import][weapp]') . ' ' . $_tool);

			$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, 'handle_tool[' . $_tool . ']', $text, false, "defaultfont", "doClick($k);"), 'space' => 70, 'noline' => 1);
		}

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[extern_exp]'), we_html_tools::TYPE_ALERT, 600, false), 'space' => 70, 'noline' => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, false, "handle_extern", g_l('backup', '[import_extern_data]'), false, "defaultfont", "doClick(300);"), 'space' => 70, 'noline' => 1);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[convert_charset]'), we_html_tools::TYPE_ALERT, 600, false), 'space' => 70, 'noline' => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, false, "convert_charset", g_l('backup', '[convert_charset_data]'), false, "defaultfont", "doClick(310);doUnCheck(101);doUnCheck(100);doUnCheck(70)"), 'space' => 70, 'noline' => 1);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[backup_log_exp]'), we_html_tools::TYPE_INFO, 600, false), 'space' => 70, 'noline' => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "backup_log", g_l('backup', '[export_backup_log]'), false, "defaultfont", "doClick(320);"), 'space' => 70, 'noline' => 1);


		$js = we_html_element::jsElement($js) .
			(isset($fileUploaderHead) ? $fileUploaderHead : '') .
			self::getJSDep("import", $docheck, $doclick, $douncheck) .
			we_html_element::jsElement('
function startBusy() {
	top.busy.location="' . $this->frameset . '&pnt=busy&operation_mode=busy&step=4";
}

function startImport(isFileReady) {
	var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse(),
		isFileReady = isFileReady || false;
	for (frameId in _usedEditors) {
		_usedEditors[frameId].setEditorIsHot( false );

	}
	WE().layout.weEditorFrameController.closeAllDocuments();

	' . ((we_base_request::_(we_base_request::STRING, "import_from") === "import_upload") ? ('
	if(isFileReady || document.we_form.we_upload_file.value) {
		startBusy();
		top.body.delete_enabled = WE().layout.button.switch_button_state(top.body.document, "delete", "disabled");
		document.we_form.action = WE().consts.dirs.WE_INCLUDES_DIR+"we_editors/we_backup_cmd.php";
		document.we_form.submit();
	}else{
		' . we_message_reporting::getShowMessageCall(g_l('backup', '[nothing_selected]'), we_message_reporting::WE_MESSAGE_WARNING) . '
	}') : ('
	if(document.we_form.backup_select.value) {
		startBusy();
		top.body.delete_backup_enabled = WE().layout.button.switch_button_state(top.body.document, "delete_backup", "disabled");
		top.body.delete_enabled = WE().layout.button.switch_button_state(top.body.document, "delete", "disabled");
		document.we_form.action = WE().consts.dirs.WE_INCLUDES_DIR+"we_editors/we_backup_cmd.php";
		document.we_form.submit();
	}else{
		' . we_message_reporting::getShowMessageCall(g_l('backup', '[nothing_selected_fromlist]'), we_message_reporting::WE_MESSAGE_WARNING) . '
	}')) . '
}

function delOldFiles(){
	if(confirm("' . g_l('backup', '[delold_confirm]') . '")){
     top.cmd.location="' . $this->frameset . '&pnt=cmd&operation_mode=deleteall";
		}
}

function startStep(){
	top.busy.location="' . $this->frameset . '&pnt=busy&step=3";
}

function delSelected(){
	var sel = document.we_form.backup_select;
	if(sel.selectedIndex>-1){
		if(confirm("' . g_l('backup', '[del_backup_confirm]') . '")) top.cmd.location="' . $this->frameset . '&pnt=cmd&operation_mode=deletebackup&bfile="+sel.options[sel.selectedIndex].value;
	} else {
		' . we_message_reporting::getShowMessageCall(g_l('backup', '[nothing_selected_fromlist]'), we_message_reporting::WE_MESSAGE_WARNING) . '
	}
}
');

		$form_attribs = (we_base_request::_(we_base_request::STRING, "import_from") === "import_upload" ?
				array("name" => "we_form", "method" => "post", "action" => $this->frameset, "target" => "cmd", "enctype" => "multipart/form-data") :
				array("name" => "we_form", "method" => "post", "action" => $this->frameset, "target" => "cmd")
			);

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "startStep();self.focus();"), we_html_element::htmlForm($form_attribs, we_html_element::htmlHiddens(array(
						"pnt" => "cmd",
						"cmd" => "import",
						"step" => 3,
						"MAX_FILE_SIZE" => $maxsize)) .
					we_html_element::htmlInput(array("type" => "hidden", "name" => "operation_mode", "value" => "import")) .
					we_html_multiIconBox::getJS() .
					we_html_multiIconBox::getHTML("backup_options", $parts, 30, "", 7, g_l('backup', '[recover_option]'), "<b>" . g_l('backup', '[recover_option]') . "</b>", false, g_l('backup', '[step3]'))
				)
		);

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', $js . STYLESHEET, $body
		);
	}

	function getHTMLRecoverStep4(){

		if(isset($_SESSION['weS']['weBackupVars'])){
			// workaround for php bug #18071
			// bug: session has been restarted
			$_SESSION['weS']['weBackupVars'] = array();
			// workaround end
			unset($_SESSION['weS']['weBackupVars']);
		}

		$parts = array(
			array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[finished_success]'), we_html_tools::TYPE_INFO, 600), 'noline' => 1),
			array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[old_backups_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1)
		);

		$js = we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
function stopBusy() {
	top.busy.location="' . $this->frameset . '&pnt=busy&step=5";
	/*if(top.opener.top.header)
		top.opener.top.header.document.location.reload();*/
}
top.cmd.location ="about:blank";
self.focus();');

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', $js . STYLESHEET, we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "stopBusy()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "enctype" => "multipart/form-data"), we_html_multiIconBox::getHTML("backup_options", $parts, 34, "", -1, "", "", false, g_l('backup', '[step3]'))
					)
				)
		);
	}

	function getHTMLBackupStep1(){
		if(isset($_SESSION['weS']['weBackupVars'])){
			// workaround for php bug #18071
			// bug: session has been restarted
			$_SESSION['weS']['weBackupVars'] = array();
			// workaround end
			unset($_SESSION['weS']['weBackupVars']);
		}

		$form_properties = array(
			1 => "export_server",
			2 => "export_send",
			10 => "handle_core",
			14 => "handle_binary",
			98 => "handle_hooks",
			99 => "handle_customtags",
			100 => "handle_settings",
			101 => "handle_temporary",
			102 => "handle_history",
			300 => "handle_extern",
			320 => "backup_log"
		);

		if(defined('OBJECT_TABLE')){
			$form_properties[11] = "handle_object";
		}
		$form_properties[20] = "handle_user";
		if(defined('CUSTOMER_TABLE')){
			$form_properties[25] = "handle_customer";
		}
		if(defined('SHOP_TABLE')){
			$form_properties[30] = "handle_shop";
		}
		if(defined('WORKFLOW_TABLE')){
			$form_properties[35] = "handle_workflow";
		}
		if(defined('MESSAGING_SYSTEM')){
			$form_properties[40] = "handle_todo";
		}
		if(defined('NEWSLETTER_TABLE')){
			$form_properties[45] = "handle_newsletter";
		}
		if(defined('BANNER_TABLE')){
			$form_properties[50] = "handle_banner";
		}
		if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
			$form_properties[55] = "handle_schedule";
		}
		if(we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT)){
			$form_properties[60] = "handle_export";
		}
		if(defined('VOTING_TABLE')){
			$form_properties[65] = "handle_voting";
		}
		/* if(defined('SPELLCHECKER')){
		  $form_properties[70] = "handle_spellchecker";
		  } */
		if(defined('GLOSSARY_TABLE')){
			$form_properties[75] = "handle_glossary";
		}
		$form_properties[12] = "handle_versions";
		$form_properties[13] = "handle_versions_binarys";

		$i = 0;
		$_tools = we_tool_lookup::getToolsForBackup();
		foreach($_tools as $_tool){
			$form_properties[700 + $i] = "handle_tool[" . $_tool . ']';
			$i++;
		}

		ksort($form_properties);

		$compression = we_base_file::hasCompression("gzip");

		$parts = array(
			array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', ($compression ? '[filename_compression]' : '[filename_info]')), we_html_tools::TYPE_INFO, 600, false), 'noline' => 1),
			array("headline" => g_l('backup', '[filename]') . ":&nbsp;&nbsp;", "html" => we_html_tools::htmlTextInput("filename", 0, 'weBackup_' . str_replace('.', '-', $_SERVER['SERVER_NAME']) . '_' . date("Y_m_d__H_i", time()) . '_' . str_replace('.', '-', WE_VERSION) . ".xml", "", "", "text", '30em'), 'space' => 100, 'noline' => 1)
		);

		if($compression){
			$switchbut = 9;
			$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(we_backup_util::COMPRESSION, true, "compress", g_l('backup', '[compress]'), false, "defaultfont", "", false, g_l('backup', '[ftp_hint]')), 'space' => 100);
		} else {
			$switchbut = 7;
		}


		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[protect_txt]'), we_html_tools::TYPE_INFO, 600, false), 'noline' => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, false, "protect", g_l('backup', '[protect]'), false, "defaultfont", ""), 'space' => 70);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[export_location]'), we_html_tools::TYPE_INFO, 600, false), 'noline' => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "export_server", g_l('backup', '[export_location_server]'), false, "defaultfont", "doClick(1)"), 'space' => 70, 'noline' => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, false, "export_send", g_l('backup', '[export_location_send]'), false, "defaultfont", "doClick(2)", (!permissionhandler::hasPerm("EXPORT"))), 'space' => 70);
		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[export_options]'), we_html_tools::TYPE_INFO, 600, false), 'noline' => 1);

		$docheck = $doclick = '';
		foreach($form_properties as $k => $v){
			$docheck.='
				case ' . $k . ':
					document.we_form.' . $v . '.checked=true;
					doClick(' . $k . ');
				break;
			';

			$doclick.='
				case ' . $k . ':
					var a=document.we_form.' . $v . ';
				break;
			';
			if($k > 2 && $k < 101){
				if($v === "handle_versions_binarys"){
					$boxNr = 1;
					$checked = false;
				} else {
					$boxNr = 2;
					$checked = true;
				}
				$parts[] = array(
					"headline" => '',
					"html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[' . str_replace('handle_', '', $v) . "_info]"), $boxNr, 600, false) .
					we_html_forms::checkbox(1, $checked, $v, g_l('backup', '[' . str_replace('handle', 'export', $v) . "_data]"), false, "defaultfont", "doClick($k);"),
					'space' => 70,
					'noline' => 1
				);
			}
		}

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[tools_export_desc]'), we_html_tools::TYPE_INFO, 600, false), 'space' => 70, 'noline' => 1);
		$k = 700;
		foreach($_tools as $_tool){
			$text = ($_tool === 'weSearch' ?
					g_l('searchtool', '[import_tool_' . $_tool . '_data]') :
					g_l('backup', '[export][weapp]') . ' ' . $_tool);

			$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, 'handle_tool[' . $_tool . ']', $text, false, "defaultfont", "doClick($k);"), 'space' => 70, 'noline' => 1);
			$k++;
		}

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[temporary_info]'), we_html_tools::TYPE_INFO, 600, false) . we_html_forms::checkbox(1, true, "handle_temporary", g_l('backup', '[export][temporary_data]'), false, "defaultfont", "doClick(101);"), 'space' => 70);
		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[history_info]'), we_html_tools::TYPE_INFO, 600, false) . we_html_forms::checkbox(1, true, "handle_history", g_l('backup', '[export][history_data]'), false, "defaultfont", "doClick(102);"), 'space' => 70);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[extern_exp]'), we_html_tools::TYPE_ALERT, 600, false), 'space' => 70, 'noline' => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, false, "handle_extern", g_l('backup', '[export_extern_data]'), false, "defaultfont", "doClick(300);"), 'space' => 70, 'noline' => 1);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[backup_log_exp]'), we_html_tools::TYPE_INFO, 600, false), 'space' => 70, 'noline' => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "backup_log", g_l('backup', '[export_backup_log]'), false, "defaultfont", "doClick(320);"), 'space' => 70, 'noline' => 1);


		$mode = "export";
		$js = self::getJSDep("export", $docheck, $doclick) .
			we_html_element::jsElement('
function startStep(){
	self.focus();
	top.busy.location="' . $this->frameset . '&pnt=busy&step=1";
}
function setLocation(loc){
	location.href=loc;
}');

		$_edit_cookie = weGetCookieVariable("but_edit_image");

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title_export]'), '', '', STYLESHEET . $js, we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "startStep()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", 'onsubmit' => 'return false;'), we_html_element::htmlHiddens(array(
							"pnt" => "cmd",
							"cmd" => "export",
							"operation_mode" => "backup",
							"do_import_after_backup" => we_base_request::_(we_base_request::BOOL, "do_import_after_backup"))) .
						we_html_multiIconBox::getJS() .
						we_html_multiIconBox::getHTML("backup_options1", $parts, 30, "", $switchbut, g_l('backup', '[option]'), "<b>" . g_l('backup', '[option]') . "</b>", $_edit_cookie != false ? ($_edit_cookie === "down") : $_edit_cookie, g_l('backup', '[export_step1]'))
					)
				)
		);
	}

	function getHTMLBackupStep2(){
		$content = we_html_element::htmlDiv(array('style' => 'padding-bottom:20px;'), g_l('backup', '[finish]'));

		if($_SESSION['weS']['weBackupVars']['options']['export2send']){
			$_down = $_SESSION['weS']['weBackupVars']['backup_file'];
			if(is_file($_SESSION['weS']['weBackupVars']['backup_file'])){
//Note: we show a link for external download - do we need this?

				$_link = WEBEDITION_DIR . 'showTempFile.php?' . http_build_query(array(
						'file' => str_replace(WEBEDITION_PATH, '', $_down),
						'binary' => 1
				));

				$content.=we_html_element::htmlDiv(array('class' => 'defaultfont'), self::getDownloadLinkText() . '<br/><br/>' .
						we_html_element::htmlA(array('href' => $_link, 'download' => basename($_down)), g_l('backup', '[download_file]'))
				);
			} else {
				$content.=we_html_element::htmlDiv(array(), g_l('backup', '[download_failed]'));
			}
		}


		$do_import_after_backup = (!empty($_SESSION['weS']['weBackupVars']['options']['do_import_after_backup'])) ? 1 : 0;
		$js = we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
function startStep(){
	self.focus();
	top.busy.location="' . $this->frameset . '&pnt=busy&do_import_after_backup=' . $do_import_after_backup . '&step=3";
}');

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title_export]'), '', '', $js . STYLESHEET, we_html_element::htmlBody(array('class' => 'weDialogBody', 'onload' => 'startStep();'), we_html_element::htmlForm(array('name' => 'we_form', 'method' => 'post'), we_html_tools::htmlDialogLayout($content, g_l('backup', '[export_step2]'))
					)
				)
		);
	}

	function getHTMLBackupStep3(){
		update_time_limit(0);
		if(we_base_request::_(we_base_request::BOOL, "backupfile")){
			$_filename = urldecode(we_base_request::_(we_base_request::RAW, "backupfile"));

			if(file_exists($_filename) && stripos($_filename, BACKUP_PATH) !== false){ // Does file exist and does it saved in backup dir?
				$_size = filesize($_filename);

				header("Pragma: public");
				header("Expires: 0");
				header("Cache-control: private, max-age=0, must-revalidate");
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . trim(htmlentities(basename($_filename))) . '"');
				header("Content-Description: " . trim(htmlentities(basename($_filename))));
				header("Content-Length: " . $_size);

				if(($_filehandler = fopen($_filename, 'rb'))){
					while(!feof($_filehandler)){
						echo fread($_filehandler, 8192);
						flush();
					}
					fclose($_filehandler);
				} else {
					echo $this->build_error_message();
				}
			} else {
				echo $this->build_error_message();
			}
		} else {
			echo $this->build_error_message();
		}

		if(isset($_SESSION['weS']['weBackupVars']['backup_file']) && isset($_SESSION['weS']['weBackupVars']['options']['export2server']) &&
			is_file($_SESSION['weS']['weBackupVars']['backup_file']) && $_SESSION['weS']['weBackupVars']['options']['export2server'] != 1){

			we_base_file::insertIntoCleanUp($_SESSION['weS']['weBackupVars']['backup_file'], 0);
		}

		if(isset($_SESSION['weS']['weBackupVars'])){
			// workaround for php bug #18071
			// bug: session has been restarted
			$_SESSION['weS']['weBackupVars'] = array();
			// workaround end
			unset($_SESSION['weS']['weBackupVars']);
		}
	}

	function build_error_message(){
		$_error_message = new we_html_table(array("class" => "default defaultfont"), 1, 1);
		$_error_message->setCol(0, 0, null, g_l('backup', '[download_failed]'));

		return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET, '<body class="weDialogBody">' . we_html_tools::htmlDialogLayout($_error_message->getHtml(), g_l('backup', '[export_step2]')));
	}

	function getHTMLExtern(){
		$txt = g_l('backup', '[extern_backup_question_' . we_base_request::_(we_base_request::STRING, "w", "exp") . ']');

		$yesCmd = "self.close();";
		$noCmd = "top.opener.top.body.clearExtern();" . $yesCmd;

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', STYLESHEET, we_html_element::htmlBody(array("class" => "weEditorBody", "onblur" => "self.focus()", "onload" => "self.focus();"), we_html_element::htmlForm(array("name" => "we_form"), we_html_tools::htmlYesNoCancelDialog($txt, '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', "ja", "nein", "", $yesCmd, $noCmd))
				)
		);
	}

	function getHTMLBusy(){
		$head = STYLESHEET;
		$body = '';

		$table = new we_html_table(array('class' => 'default', "style" => "width:100%;text-align:right"), 1, 3);

		if(we_base_request::_(we_base_request::STRING, "operation_mode") === "busy"){
			$text = we_base_request::_(we_base_request::BOOL, "current_description", g_l('backup', '[working]'));
			$progress = new we_progressBar(we_base_request::_(we_base_request::INT, "percent", 0));
			$progress->setStudLen(200);
			$progress->addText($text, 0, "current_description");
			$head.=$progress->getJSCode('top.busy');
			$pg = $progress->getHtml('', 'margin-left:15px');
		} else {
			$pg = '';
		}


		$step = we_base_request::_(we_base_request::INT, "step", 0);

		switch($this->mode){
			case self::BACKUP:
				switch($step){
					case 1:
						$head.=we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
function doExport() {
	if((!top.body.document.we_form.export_send.checked) && (!top.body.document.we_form.export_server.checked)) {
		' . we_message_reporting::getShowMessageCall(g_l('backup', '[save_not_checked]'), we_message_reporting::WE_MESSAGE_WARNING) . '
	}else {
		top.busy.location="' . $this->frameset . '&pnt=busy&operation_mode=busy&step=2";
		top.body.we_submitForm("cmd","' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php");
	}
}');
						$table->setCol(0, 1, null, we_html_button::position_yes_no_cancel(we_html_button::create_button('make_backup', "javascript:doExport();"), null, we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")));
						break;
					case 2:
						$table->setCol(0, 1, null, we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();"));
						break;
					case 3:
						if(we_base_request::_(we_base_request::BOOL, "do_import_after_backup")){
							$body = we_html_button::create_button(we_html_button::NEXT, "javascript:top.body.location='" . $this->frameset . "&pnt=body&step=2';top.busy.location='" . $this->frameset . "&pnt=cmd';top.cmd.location='" . $this->frameset . "&pnt=busy';");
						} else if(!empty($_SESSION['weS']['inbackup'])){
							$body = we_html_button::create_button(we_html_button::NEXT, "javascript:top.opener.weiter();top.close();");
							unset($_SESSION['weS']['inbackup']);
						} else {
							$head.=we_html_element::jsElement("top.opener.top.afterBackup=true;");
							$body = we_html_button::create_button(we_html_button::CLOSE, "javascript:top.close();");
						}
						$table->setCol(0, 1, null, $body);
						break;
					default:
				}
				break;
			case self::RECOVER:
				switch($step){
					case 1:
						$head .= we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
function press_yes() {
	var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
	var _unsavedChanges = false;
	for (frameId in _usedEditors) {
		if ( _usedEditors[frameId].getEditorIsHot() ) {
			_unsavedChanges = true;
		}
	}

	if (_unsavedChanges) {
		' . we_message_reporting::getShowMessageCall(g_l('backup', '[recover_backup_unsaved_changes]'), we_message_reporting::WE_MESSAGE_WARNING) . "
	} else {
		top.body.location='" . $this->frameset . "&pnt=body&do_import_after_backup=1';
		top.busy.location='" . $this->frameset . "&pnt=busy';
		top.cmd.location='" . $this->frameset . "&pnt=cmd';
	}

}");
						$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::YES, "javascript:press_yes();"), we_html_button::create_button(we_html_button::NO, "javascript:top.body.location='" . $this->frameset . "&pnt=body&step=2';"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();"));
						$table->setCol(0, 1, null, $buttons);
						break;
					case 2:

						$nextbuts = we_html_button::create_button(we_html_button::BACK, "javascript:top.body.location='" . $this->frameset . "&pnt=body&step=1'", true) .
							we_html_button::create_button(we_html_button::NEXT, "javascript:top.body.we_submitForm('body','" . $this->frameset . "');");

						$buttons = we_html_button::position_yes_no_cancel($nextbuts, null, we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();"));

						$table->setCol(0, 1, null, $buttons);
						break;
					case 3:
						//FIXME: delete condition when new uploader is stable
						//$startImportCall = $this->fileUploader ? $this->fileUploader->getJsBtnCmd('upload') : "top.body.startImport();";
						$startImportCall = we_fileupload_ui_base::getJsBtnCmdStatic('upload', 'body', 'top.body.startImport()');
						$cancelCall = we_fileupload_ui_base::getJsBtnCmdStatic('cancel', 'body');

						if(defined('WORKFLOW_TABLE')){
							$db = new DB_WE();
							$nextbut = (we_workflow_utility::getAllWorkflowDocs(FILE_TABLE, $db) || (defined('OBJECT_FILES_TABLE') && we_workflow_utility::getAllWorkflowDocs(OBJECT_FILES_TABLE, $db)) ?
									we_html_button::create_button('restore_backup', "javascript:if(confirm('" . g_l('modules_workflow', '[ask_before_recover]') . "')) " . $startImportCall . ";") :
									we_html_button::create_button('restore_backup', "javascript:" . $startImportCall));
						} else {
							$nextbut = we_html_button::create_button('restore_backup', "javascript:" . $startImportCall);
						}

						$nextprevbuts = we_html_button::create_button(we_html_button::BACK, "javascript:top.body.location='" . $this->frameset . "&pnt=body&step=2';") . $nextbut;
						$buttons = we_html_button::position_yes_no_cancel($nextprevbuts, null, we_html_button::create_button(we_html_button::CANCEL, "javascript:" . $cancelCall));

						$table->setCol(0, 1, null, $buttons);
						break;
					case 4:
						$table->setCol(0, 1, null, we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();"));
						break;
					case 5:
						$table->setCol(0, 1, null, we_html_button::create_button(we_html_button::CLOSE, "javascript:top.close();"));
						break;
					default:
				}
				break;
		}

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', $head, we_html_element::htmlBody(array("class" => "weDialogButtonsBody"), $pg . $table->getHtml())
		);
	}

	function getHTMLCmd(){
		switch(we_base_request::_(we_base_request::STRING, "operation_mode", '-1')){
			case '-1':
				return;
			case "rebuild":
				return we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
top.opener.top.openWindow(WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_all&responseText=' . g_l('backup', '[finished_success]') . '","rebuildwin",-1,-1,600,130,0,true);
top.close();');
			case "deleteall":
				$_SESSION['weS']['backup_delete'] = 1;
				$_SESSION['weS']['delete_files_nok'] = array();
				$_SESSION['weS']["delete_files_info"] = g_l('backup', '[files_not_deleted]');
				return we_html_element::jsElement('new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR+"delFrag.php?currentID=-1", "we_del", -1, -1, 600, 130, true, true, true);');
			case "deletebackup":
				$bfile = we_base_request::_(we_base_request::FILE, "bfile");
				if(strpos($bfile, '..') === 0){
					return we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[name_notok]'), we_message_reporting::WE_MESSAGE_ERROR));
				}
				if(!is_writable(BACKUP_PATH . $bfile)){
					return we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[error_delete]'), we_message_reporting::WE_MESSAGE_ERROR));
				}
				return we_html_element::jsElement((unlink(BACKUP_PATH . $bfile) ?
							'if(top.body.delSelItem) top.body.delSelItem();' :
							we_message_reporting::getShowMessageCall(g_l('backup', '[error_delete]'), we_message_reporting::WE_MESSAGE_ERROR))
				);
			default:
				return we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[error]'), we_message_reporting::WE_MESSAGE_ERROR)
				);
		}
	}

	function getParam($params){
		$out = '';
		foreach($params as $k => $v){
			$out.='&' . $k . '=' . $v;
		}
		return $out;
	}

	public static function getHTMLChecker($mode){
		$_execute = (min($_SESSION['weS']['weBackupVars']['limits']['exec'], 32) * 1000) + 5000; //wait extra 5 secs

		$_retry = 3;

		return we_html_element::jsElement('
function setLocation(loc){
	location.href = loc;
}

function reloadFrame(){
	var reload = ' . (we_base_request::_(we_base_request::INT, 'reload', 0) + 1) . ';
	if(reload < ' . $_retry . '){
		top.cmd.location="' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php?cmd=' . ($mode == self::RECOVER ? 'import' : 'export') . '&reload="+reload;
	} else{' .
				we_message_reporting::getShowMessageCall(g_l('backup', '[error_timeout]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	}
}

setTimeout(reloadFrame, ' . $_execute . ');');
	}

	static function getDownloadLinkText(){

		switch(we_base_browserDetect::inst()->getBrowser()){
			case we_base_browserDetect::SAFARI:
			case we_base_browserDetect::APPLE:
				$out = g_l('browser', '[save_link_as_SAFARI]');
				break;
			case we_base_browserDetect::IE:
				$out = g_l('browser', '[save_link_as_IE]');
				break;
			case we_base_browserDetect::FF:
				$out = g_l('browser', '[save_link_as_FF]');
				break;
			case we_base_browserDetect::OPERA:
			default:
				$out = g_l('browser', '[save_link_as_DEFAULT]');
		}

		return nl2br(oldHtmlspecialchars(preg_replace('#<br\s*/?\s*>#i', "\n", $out)));
	}

}
