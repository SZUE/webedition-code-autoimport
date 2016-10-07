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
	private $json = [];

	function __construct($mode = self::BACKUP){
		$this->frameset = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . ($mode == self::BACKUP ? 'make_backup' : 'recover_backup');
		$this->mode = $mode;
		$this->json['mode'] = $mode;
		$this->json['modeCmd'] = ($mode == self::BACKUP ? 'make_backup' : 'recover_backup');
	}

	private function getJSDep(){
		$this->json['import_from'] = we_base_request::_(we_base_request::STRING, "import_from");

		return we_html_element::jsScript(JS_DIR . 'backup_wizard.js', '', ['id' => 'loadVarBackup_wizard', 'data-backup' => setDynamicVar($this->json)]);
	}

	function getHTMLFrameset(){
		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_' . ($this->mode == self::BACKUP ? 'backup' : 'recover') . '_title]'), '', '', '', we_html_element::htmlBody(array('id' => 'weMainBody')
					, we_html_element::htmlIFrame('body', $this->frameset . '&pnt=body', 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;', 'border:0px;width:100%;height:100%;') .
					we_html_element::htmlIFrame('busy', $this->frameset, 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden', '', '', false) .
					we_html_element::htmlIFrame('cmd', $this->frameset . '&pnt=cmd')
				)
		);
	}

	function getHTMLStep($step){
		switch($this->mode){
			case self::BACKUP:
				switch($step){
					default:
					case 1:
						return $this->getHTMLBackupStep1();
					case 2:
						return $this->getHTMLBackupStep2();
					case 3:
						return $this->getHTMLBackupStep3();
				}
			case self::RECOVER:
				switch($step){
					default:
					case 1:
						return $this->getHTMLRecoverStep1();
					case 2:
						return $this->getHTMLRecoverStep2();
					case 3:
						return $this->getHTMLRecoverStep3();
					case 4:
						return $this->getHTMLRecoverStep4();
				}
		}
	}

	function getHTMLRecoverStep1(){
		$parts = [['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[save_before]'), we_html_tools::TYPE_ALERT, 600), 'noline' => 1],
			['headline' => '', 'html' => g_l('backup', '[save_question]'), 'noline' => 1],
		];


		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js', '', ['id' => 'loadVarBackup_wizard', 'data-backup' => setDynamicVar($this->json)]), we_html_element::htmlBody(['class' => "weDialogBody", "onload" => "startStep(1)"], we_html_element::htmlForm(['name' => 'we_form', "method" => "post"], we_html_multiIconBox::getHTML("backup_options", $parts, 30, "", -1, "", "", false, g_l('backup', '[step1]'))
					)
				)
		);
	}

	function getHTMLRecoverStep2(){
		$parts = [
			['headline' => '', 'html' => we_html_forms::radiobutton("import_server", true, "import_from", g_l('backup', '[import_from_server]')), 'noline' => 1],
			['headline' => '', 'html' => we_html_forms::radiobutton("import_upload", false, "import_from", g_l('backup', '[import_from_local]')), 'noline' => 1]
		];

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js', '', ['id' => 'loadVarBackup_wizard', 'data-backup' => setDynamicVar($this->json)]), we_html_element::htmlBody(array('class' => "weDialogBody", "onload" => "startStep(2);"), we_html_element::htmlForm(array('name' => 'we_form', "method" => "post"), we_html_element::htmlHiddens(array("pnt" => "body", "step" => 3)) .
						we_html_multiIconBox::getHTML("backup_options", $parts, 30, "", -1, "", "", false, g_l('backup', '[step2]'))
					)
				)
		);
	}

	function getHTMLRecoverStep3(){
		if(isset($_SESSION['weS']['weBackupVars'])){
			// workaround for php bug #18071
			// bug: session has been restarted
			$_SESSION['weS']['weBackupVars'] = [];
			// workaround end
			unset($_SESSION['weS']['weBackupVars']);
		}

		$this->fileUploader = new we_fileupload_ui_base('we_upload_file');
		$this->fileUploader->setTypeCondition('accepted', [we_base_ContentTypes::XML], ['.gz', '.tgz']);
		$this->fileUploader->setCallback('top.body.startImport(true)');
		$this->fileUploader->setNextCmd('uploaderCallback_startImport');
		$this->fileUploader->setInternalProgress(['isInternalProgress' => true, 'width' => 300]);
		$this->fileUploader->setDimensions(['width' => 500, 'alertBoxWidth' => 600, 'dragWidth' => 594, 'dragHeight' => 70, 'marginTop' => 5]);
		$this->fileUploader->setGenericFileName(TEMP_DIR . we_fileupload::REPLACE_BY_FILENAME);

		$maxsize = $this->fileUploader->getMaxUploadSize();

		if(we_base_request::_(we_base_request::STRING, "import_from") === 'import_upload'){
			if($maxsize || $this->fileUploader){
				//FIXME:
				$fileUploaderHead = $this->fileUploader->getCss() . $this->fileUploader->getJs();
				$inputTypeFile = $this->fileUploader->getHTML();

				$parts = [['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[charset_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1],
					(DEFAULT_CHARSET ? null : ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[defaultcharset_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1]),
					['headline' => '', 'html' => $this->fileUploader->getHtmlAlertBoxes(), 'noline' => 1],
					['headline' => '', 'html' => $inputTypeFile, 'noline' => 1]
				];
			}
		} else {
			$select = new we_html_select(['name' => "backup_select", "size" => 7, "style" => "width: 600px;"]);
			$files = [];
			$extra_files = [];
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
					$ts = str_replace(['.php', '.xml', '.gz', '.bz', '.zip'], '', preg_replace('|^weBackup_|', '', $entry));

					if(is_numeric($ts) && !($ts < 1004569200)){//old Backup
						$comp = we_base_file::getCompression($entry);
						$files[$adddatadir . $entry] = /* g_l('backup', '[backup_form]') . ' ' . */ date($dateformat, $ts) . ($comp && $comp != "none" ? " ($comp)" : "") . " " . $filesize;
						continue;
					}

					if(substr_count($ts, '_') > 5){
						$matches = [];
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

			$this->json['extra_files'] = [];
			$this->json['extra_files_desc'] = [];
			foreach($extra_files as $fk => $fv){
				$this->json['extra_files'][] = $fk;
				$this->json['extra_files_desc'][] = $fv;
			}

			$parts = [['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[charset_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1],
				(DEFAULT_CHARSET ? null : ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[defaultcharset_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1] ),
				['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[old_backups_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1],
				['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[select_server_file]'), we_html_tools::TYPE_INFO, 600, false), 'noline' => 1],
				['headline' => '', 'html' => $select->getHtml(), 'noline' => 1],
				//array("headline"=>"","html"=>we_html_forms::checkbox(1, false, "show_all", g_l('backup',"[show_all]"), false, "defaultfont", "showAll()"),"space"=>0,"noline"=>1);
				['headline' => '', 'html' => we_html_button::create_button('delete_backup', "javascript:delSelected();", true, 100, 22, '', '', false, false),]
			];
		}

		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, true, "rebuild", g_l('backup', '[rebuild]'), false),];

		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[delold_notice]'), we_html_tools::TYPE_QUESTION, 600, false) .
			we_html_button::create_button('delold', "javascript:delOldFiles();", true, 100, 22, '', '', false, false), 'noline' => 1];

		$form_properties = [
			10 => 'handle_core',
			11 => defined('OBJECT_TABLE') ? 'handle_object' : '',
			12 => 'handle_versions',
			13 => 'handle_versions_binarys',
			14 => 'handle_binary',
			20 => 'handle_user',
			25 => defined('CUSTOMER_TABLE') ? 'handle_customer' : '',
			30 => defined('SHOP_ORDER_TABLE') ? 'handle_shop' : '',
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
		];

		$i = 0;
		$tools = we_tool_lookup::getToolsForBackup();
		foreach($tools as $tool){
			$form_properties[700 + $i] = 'handle_tool[' . $tool . ']';
			$i++;
		}

		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[import_options]'), we_html_tools::TYPE_INFO, 600, false), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];

		$this->json['form_properties'] = $form_properties;


		foreach($form_properties as $k => $v){
			if(!$v){
				continue;
			}
			if($k > 2 && $k < 101){
				$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, true, $v, g_l('backup', '[' . str_replace('handle', 'import', $v) . '_data]'), false, 'defaultfont', "doClick($k);"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
			}
		}

		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, true, "handle_temporary", g_l('backup', '[import][temporary_data]'), false, "defaultfont", "doClick(101);"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];

		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, true, "handle_history", g_l('backup', '[import][history_data]'), false, "defaultfont", "doClick(102);"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];


		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[tools_import_desc]'), we_html_tools::TYPE_INFO, 600, false), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
		foreach($tools as $tool){
			$text = ($tool === 'weSearch' ?
					g_l('searchtool', '[import_tool_' . $tool . '_data]') :
					g_l('backup', '[import][weapp]') . ' ' . $tool);

			$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, true, 'handle_tool[' . $tool . ']', $text, false, "defaultfont", "doClick($k);"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
		}

		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[extern_exp]'), we_html_tools::TYPE_ALERT, 600, false), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, false, "handle_extern", g_l('backup', '[import_extern_data]'), false, "defaultfont", "doClick(300);"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];

		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[convert_charset]'), we_html_tools::TYPE_ALERT, 600, false), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, false, "convert_charset", g_l('backup', '[convert_charset_data]'), false, "defaultfont", "doClick(310);doUnCheck(101);doUnCheck(100);doUnCheck(70)"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];

		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[backup_log_exp]'), we_html_tools::TYPE_INFO, 600, false), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, true, "backup_log", g_l('backup', '[export_backup_log]'), false, "defaultfont", "doClick(320);"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];


		$form_attribs = (we_base_request::_(we_base_request::STRING, "import_from") === "import_upload" ?
				['name' => 'we_form', "method" => "post", "action" => $this->frameset, "target" => "cmd", "enctype" => "multipart/form-data"] :
				['name' => 'we_form', "method" => "post", "action" => $this->frameset, "target" => "cmd"]
			);

		$body = we_html_element::htmlBody(['class' => "weDialogBody", "onload" => "startStep(3);self.focus();"], we_html_element::htmlForm($form_attribs, we_html_element::htmlHiddens(["pnt" => "cmd",
						"cmd" => "import",
						"step" => 3,
						"MAX_FILE_SIZE" => $maxsize]) .
					we_html_element::htmlInput(["type" => "hidden", "name" => "operation_mode", "value" => "import"]) .
					we_html_multiIconBox::getJS() .
					we_html_multiIconBox::getHTML("backup_options", $parts, 30, "", 7, g_l('backup', '[recover_option]'), "<b>" . g_l('backup', '[recover_option]') . "</b>", false, g_l('backup', '[step3]'))
				)
		);

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js', '', ['id' => 'loadVarBackup_wizard', 'data-backup' => setDynamicVar($this->json)]) . (isset($fileUploaderHead) ? $fileUploaderHead : '') .
				$this->getJSDep(), $body);
	}

	function getHTMLRecoverStep4(){

		if(isset($_SESSION['weS']['weBackupVars'])){
			// workaround for php bug #18071
			// bug: session has been restarted
			$_SESSION['weS']['weBackupVars'] = [];
			// workaround end
			unset($_SESSION['weS']['weBackupVars']);
		}

		$parts = [
			['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[finished_success]'), we_html_tools::TYPE_INFO, 600), 'noline' => 1],
			['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[old_backups_warning]'), we_html_tools::TYPE_ALERT, 600, false), 'noline' => 1]
		];

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js', '', ['id' => 'loadVarBackup_wizard', 'data-backup' => setDynamicVar($this->json)]), we_html_element::htmlBody(['class' => "weDialogBody", "onload" => "stopBusy()"], we_html_element::htmlForm(['name' => 'we_form', "method" => "post", "enctype" => "multipart/form-data"], we_html_multiIconBox::getHTML("backup_options", $parts, 30, "", -1, "", "", false, g_l('backup', '[step3]'))
					)
				)
		);
	}

	function getHTMLBackupStep1(){
		if(isset($_SESSION['weS']['weBackupVars'])){
			// workaround for php bug #18071
			// bug: session has been restarted
			$_SESSION['weS']['weBackupVars'] = [];
			// workaround end
			unset($_SESSION['weS']['weBackupVars']);
		}

		$form_properties = [
			1 => "export_server",
			2 => "export_send",
			10 => "handle_core",
			11 => defined('OBJECT_TABLE') ? "handle_object" : '',
			12 => "handle_versions",
			13 => "handle_versions_binarys",
			14 => "handle_binary",
			20 => 'handle_user',
			25 => defined('CUSTOMER_TABLE') ? "handle_customer" : '',
			30 => defined('SHOP_ORDER_TABLE') ? "handle_shop" : '',
			35 => defined('WORKFLOW_TABLE') ? "handle_workflow" : '',
			40 => defined('MESSAGING_SYSTEM') ? "handle_todo" : '',
			45 => defined('NEWSLETTER_TABLE') ? "handle_newsletter" : '',
			50 => defined('BANNER_TABLE') ? "handle_banner" : '',
			55 => we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) ? "handle_schedule" : '',
			60 => we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT) ? "handle_export" : '',
			65 => defined('VOTING_TABLE') ? "handle_voting" : '',
			//70 => defined('SPELLCHECKER') ? "handle_spellchecker" : '',
			75 => defined('GLOSSARY_TABLE') ? "handle_glossary" : '',
			98 => "handle_hooks",
			99 => "handle_customtags",
			100 => "handle_settings",
			101 => "handle_temporary",
			102 => "handle_history",
			300 => "handle_extern",
			320 => "backup_log"
		];

		$i = 0;
		$tools = we_tool_lookup::getToolsForBackup();
		foreach($tools as $tool){
			$form_properties[700 + $i] = "handle_tool[" . $tool . ']';
			$i++;
		}

		$compression = we_base_file::hasCompression("gzip");

		$parts = [
			['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', ($compression ? '[filename_compression]' : '[filename_info]')), we_html_tools::TYPE_INFO, 600, false), 'noline' => 1],
			["headline" => g_l('backup', '[filename]') . ":&nbsp;&nbsp;", "html" => we_html_tools::htmlTextInput("filename", 0, 'weBackup_' . str_replace('.', '-', $_SERVER['SERVER_NAME']) . '_' . date("Y_m_d__H_i", time()) . '_' . str_replace('.', '-', WE_VERSION) . ".xml", "", "", "text", '30em'), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1]
		];

		if($compression){
			$switchbut = 9;
			$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(we_backup_util::COMPRESSION, true, "compress", g_l('backup', '[compress]'), false, "defaultfont", "", false, g_l('backup', '[ftp_hint]'), we_html_tools::TYPE_ALERT, 600), 'space' => we_html_multiIconBox::SPACE_MED];
		} else {
			$switchbut = 7;
		}


		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[protect_txt]'), we_html_tools::TYPE_INFO, 600, false), 'noline' => 1];
		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, false, "protect", g_l('backup', '[protect]'), false, "defaultfont", ""), 'space' => we_html_multiIconBox::SPACE_MED];

		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[export_location]'), we_html_tools::TYPE_INFO, 600, false), 'noline' => 1];
		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, true, "export_server", g_l('backup', '[export_location_server]'), false, "defaultfont", "doClick(1)"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, false, "export_send", g_l('backup', '[export_location_send]'), false, "defaultfont", "doClick(2)", (!permissionhandler::hasPerm("EXPORT"))), 'space' => we_html_multiIconBox::SPACE_MED];
		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[export_options]'), we_html_tools::TYPE_INFO, 600, false), 'noline' => 1];

		$this->json['form_properties'] = $form_properties;
		foreach($form_properties as $k => $v){
			if(!$v){
				continue;
			}

			if($k > 2 && $k < 101){
				if($v === "handle_versions_binarys"){
					$boxNr = 1;
					$checked = false;
				} else {
					$boxNr = 2;
					$checked = true;
				}
				$parts[] = ["headline" => '',
					"html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[' . str_replace('handle_', '', $v) . "_info]"), $boxNr, 600, false) .
					we_html_forms::checkbox(1, $checked, $v, g_l('backup', '[' . str_replace('handle', 'export', $v) . "_data]"), false, "defaultfont", "doClick($k);"),
					'space' => we_html_multiIconBox::SPACE_MED,
					'noline' => 1
				];
			}
		}

		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[tools_export_desc]'), we_html_tools::TYPE_INFO, 600, false), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
		$k = 700;
		foreach($tools as $tool){
			$text = ($tool === 'weSearch' ?
					g_l('searchtool', '[import_tool_' . $tool . '_data]') :
					g_l('backup', '[export][weapp]') . ' ' . $tool);

			$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, true, 'handle_tool[' . $tool . ']', $text, false, "defaultfont", "doClick($k);"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
			$k++;
		}

		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[temporary_info]'), we_html_tools::TYPE_INFO, 600, false) . we_html_forms::checkbox(1, true, "handle_temporary", g_l('backup', '[export][temporary_data]'), false, "defaultfont", "doClick(101);"), 'space' => we_html_multiIconBox::SPACE_MED];
		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[history_info]'), we_html_tools::TYPE_INFO, 600, false) . we_html_forms::checkbox(1, true, "handle_history", g_l('backup', '[export][history_data]'), false, "defaultfont", "doClick(102);"), 'space' => we_html_multiIconBox::SPACE_MED];

		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[extern_exp]'), we_html_tools::TYPE_ALERT, 600, false), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, false, "handle_extern", g_l('backup', '[export_extern_data]'), false, "defaultfont", "doClick(300);"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];

		$parts[] = ['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[backup_log_exp]'), we_html_tools::TYPE_INFO, 600, false), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];
		$parts[] = ['headline' => '', 'html' => we_html_forms::checkbox(1, true, "backup_log", g_l('backup', '[export_backup_log]'), false, "defaultfont", "doClick(320);"), 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1];


		$js = $this->getJSDep();

		$edit_cookie = weGetCookieVariable("but_edit_image");

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title_export]'), '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js', '', ['id' => 'loadVarBackup_wizard', 'data-backup' => setDynamicVar($this->json)]) . $js, we_html_element::htmlBody(['class' => "weDialogBody", "onload" => "startStep(1)"], we_html_element::htmlForm(['name' => 'we_form', "method" => "post", 'onsubmit' => 'return false;'], we_html_element::htmlHiddens(["pnt" => "cmd",
							"cmd" => "export",
							"operation_mode" => "backup",
							"do_import_after_backup" => we_base_request::_(we_base_request::BOOL, "do_import_after_backup")]) .
						we_html_multiIconBox::getJS() .
						we_html_multiIconBox::getHTML("backup_options1", $parts, 30, "", $switchbut, g_l('backup', '[option]'), "<b>" . g_l('backup', '[option]') . "</b>", $edit_cookie != false ? ($edit_cookie === "down") : $edit_cookie, g_l('backup', '[export_step1]'))
					)
				)
		);
	}

	function getHTMLBackupStep2(){
		$content = we_html_element::htmlDiv(['style' => 'padding-bottom:20px;'], g_l('backup', '[finish]'));

		if($_SESSION['weS']['weBackupVars']['options']['export2send']){
			$down = $_SESSION['weS']['weBackupVars']['backup_file'];
			if(is_file($_SESSION['weS']['weBackupVars']['backup_file'])){
//Note: we show a link for external download - do we need this?

				$link = WEBEDITION_DIR . 'showTempFile.php?' . http_build_query([
						'file' => str_replace(WEBEDITION_PATH, '', $down),
						'binary' => 1
				]);

				$content.=we_html_element::htmlDiv(['class' => 'defaultfont'], self::getDownloadLinkText() . '<br/><br/>' .
						we_html_element::htmlA(['href' => $link, 'download' => $_SESSION['weS']['weBackupVars']['filename']], g_l('backup', '[download_file]'))
				);
			} else {
				$content.=we_html_element::htmlDiv([], g_l('backup', '[download_failed]'));
			}
		}

		$do_import_after_backup = (!empty($_SESSION['weS']['weBackupVars']['options']['do_import_after_backup'])) ? 1 : 0;

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title_export]'), '', '', we_html_element::jsScript(JS_DIR . 'backup_wizard.js', '', ['id' => 'loadVarBackup_wizard', 'data-backup' => setDynamicVar($this->json)]), we_html_element::htmlBody(['class' => 'weDialogBody', 'onload' => 'startStep(3,' . $do_import_after_backup . ');'], we_html_element::htmlForm(['name' => 'we_form', 'method' => 'post'], we_html_tools::htmlDialogLayout($content, g_l('backup', '[export_step2]'))
					)
				)
		);
	}

	function getHTMLBackupStep3(){
		update_time_limit(0);
		if(we_base_request::_(we_base_request::BOOL, "backupfile")){
			$filename = urldecode(we_base_request::_(we_base_request::RAW, "backupfile"));

			if(file_exists($filename) && stripos($filename, BACKUP_PATH) !== false){ // Does file exist and does it saved in backup dir?
				$size = filesize($filename);

				header("Pragma: public");
				header("Expires: 0");
				header("Cache-control: private, max-age=0, must-revalidate");
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . trim(htmlentities(basename($filename))) . '"');
				header("Content-Description: " . trim(htmlentities(basename($filename))));
				header("Content-Length: " . $size);

				if(($filehandler = fopen($filename, 'rb'))){
					while(!feof($filehandler)){
						echo fread($filehandler, 8192);
						flush();
					}
					fclose($filehandler);
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
			$_SESSION['weS']['weBackupVars'] = [];
			// workaround end
			unset($_SESSION['weS']['weBackupVars']);
		}
	}

	function build_error_message(){
		$error_message = new we_html_table(['class' => "default defaultfont"], 1, 1);
		$error_message->setCol(0, 0, null, g_l('backup', '[download_failed]'));

		return we_html_tools::getHtmlTop('', '', '', '', '<body class="weDialogBody">' . we_html_tools::htmlDialogLayout($error_message->getHtml(), g_l('backup', '[export_step2]')));
	}

	function getHTMLExtern(){
		$txt = g_l('backup', '[extern_backup_question_' . we_base_request::_(we_base_request::STRING, "w", "exp") . ']');

		$yesCmd = "self.close();";
		$noCmd = "top.opener.top.body.clearExtern();" . $yesCmd;

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', '', we_html_element::htmlBody(array('class' => 'weEditorBody', "onblur" => "self.focus()", "onload" => "self.focus();"), we_html_element::htmlForm(array('name' => 'we_form'), we_html_tools::htmlYesNoCancelDialog($txt, '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', "ja", "nein", "", $yesCmd, $noCmd))
				)
		);
	}

	function getHTMLBusy(){
		$head = $body = '';

		$table = new we_html_table(['class' => 'default', "style" => "width:100%;text-align:right"], 1, 3);

		if(we_base_request::_(we_base_request::STRING, "operation_mode") === "busy"){
			$text = we_base_request::_(we_base_request::BOOL, "current_description", g_l('backup', '[working]'));
			$progress = new we_progressBar(we_base_request::_(we_base_request::INT, "percent", 0), 200);
			$progress->addText($text, 0, "current_description");
			$head .= we_progressBar::getJSCode();
			$table->setCol(0, 0, ['style' => 'text-align:left;'], $progress->getHtml('', 'margin-left:15px'));
		}


		$step = we_base_request::_(we_base_request::INT, "step", 0);

		switch($this->mode){
			case self::BACKUP:
				switch($step){
					case 1:
						$head.=we_html_element::jsElement('
function doExport() {
	if((!top.body.document.we_form.export_send.checked) && (!top.body.document.we_form.export_server.checked)) {
		' . we_message_reporting::getShowMessageCall(g_l('backup', '[save_not_checked]'), we_message_reporting::WE_MESSAGE_WARNING) . '
	}else {
		top.busy.location="' . $this->frameset . '&pnt=busy&operation_mode=busy&step=2";
		top.body.we_submitForm("cmd","' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php");
	}
}');
						$table->setCol(0, 1, null, we_html_button::position_yes_no_cancel(we_html_button::create_button('make_backup', "javascript:doExport();"), null, we_html_button::create_button(we_html_button::CANCEL, 'javascript:top.close();')));
						break;
					case 2:
						$table->setCol(0, 1, null, we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();"));
						break;
					case 3:
						if(we_base_request::_(we_base_request::BOOL, 'do_import_after_backup')){
							$body = we_html_button::create_button(we_html_button::NEXT, "javascript:top.body.location='" . $this->frameset . "&pnt=body&step=2';top.busy.location='" . $this->frameset . "&pnt=cmd';top.cmd.location='" . $this->frameset . "&pnt=busy';");
						} else if(!empty($_SESSION['weS']['inbackup'])){
							$body = we_html_button::create_button(we_html_button::NEXT, "javascript:top.opener.weiter();top.close();");
							unset($_SESSION['weS']['inbackup']);
						} else {
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

						$nextbuts = we_html_button::create_button(we_html_button::BACK, "javascript:top.body.location='" . $this->frameset . "&pnt=body&step=1'") .
							we_html_button::create_button(we_html_button::NEXT, "javascript:top.body.we_submitForm('body','" . $this->frameset . "');");

						$buttons = we_html_button::position_yes_no_cancel($nextbuts, null, we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();"));

						$table->setCol(0, 1, null, $buttons);
						break;
					case 3:
						$startImportCall = 'top.body.weFileUpload_instance.startUpload()';
						$cancelCall = 'top.body.weFileUpload_instance.cancelUpload()';

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

		return we_html_tools::getHtmlTop(g_l('backup', '[wizard_title]'), '', '', $head, we_html_element::htmlBody(array('class' => "weDialogButtonsBody"), $table->getHtml())
		);
	}

	function getHTMLCmd(){
		switch(we_base_request::_(we_base_request::STRING, "operation_mode", '-1')){
			case '-1':
				return;
			case "rebuild":
				return we_html_element::jsElement('
top.opener.top.openWindow(WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_all&responseText=' . g_l('backup', '[finished_success]') . '","rebuildwin",-1,-1,600,130,0,true);
top.close();');
			case "deleteall":
				$_SESSION['weS']['backup_delete'] = 1;
				$_SESSION['weS']['delete_files_nok'] = [];
				$_SESSION['weS']["delete_files_info"] = g_l('backup', '[files_not_deleted]');
				return we_html_element::jsElement('new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR+"delFrag.php?currentID=-1", "we_del", -1, -1, 600, 130, true, true, true);');
			case "deletebackup":
				$bfile = we_base_request::_(we_base_request::FILE, "bfile");
				if(strpos($bfile, '..') === 0){
					return we_message_reporting::jsMessagePush(g_l('backup', '[name_notok]'), we_message_reporting::WE_MESSAGE_ERROR);
				}
				if(!is_writable(BACKUP_PATH . $bfile)){
					return we_message_reporting::jsMessagePush(g_l('backup', '[error_delete]'), we_message_reporting::WE_MESSAGE_ERROR);
				}
				return we_html_element::jsElement((unlink(BACKUP_PATH . $bfile) ?
							'if(top.body.delSelItem){
	top.body.delSelItem();
}' :
							we_message_reporting::getShowMessageCall(g_l('backup', '[error_delete]'), we_message_reporting::WE_MESSAGE_ERROR))
				);
			default:
				return we_message_reporting::jsMessagePush(g_l('backup', '[error]'), we_message_reporting::WE_MESSAGE_ERROR);
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
		$execute = (min($_SESSION['weS']['weBackupVars']['limits']['exec'], 32) * 1000) + 5000; //wait extra 5 secs

		$retry = 3;

		return we_html_element::jsElement('

function reloadFrame(){
	var reload = ' . (we_base_request::_(we_base_request::INT, 'reload', 0) + 1) . ';
	if(reload < ' . $retry . '){
		top.cmd.location="' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php?cmd=' . ($mode == self::RECOVER ? 'import' : 'export') . '&reload="+reload;
	} else{' .
				we_message_reporting::getShowMessageCall(g_l('backup', '[error_timeout]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	}
}

top.cmd.reloadTimer=setTimeout(reloadFrame, ' . $execute . ');');
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

	public static function showLog(){
		if(permissionhandler::hasPerm("BACKUPLOG")){
			$parts = [
				[
					'headline' => g_l('backup', '[view_log]'),
					'html' => '',
					'space' => we_html_multiIconBox::SPACE_SMALL
				],
				[
					'headline' => '',
					'html' => (file_exists(BACKUP_PATH . we_backup_util::logFile) ?
						'<pre>' . file_get_contents(BACKUP_PATH . we_backup_util::logFile) . '</pre>' :
						'<p>' . g_l('backup', '[view_log_not_found]') . '</p>'),
					'space' => we_html_multiIconBox::SPACE_SMALL
				]
			];
		} else {
			$parts = [
				[
					'headline' => '',
					'html' => '<p>' . g_l('backup', '[view_log_no_perm]') . '</p>',
					'space' => we_html_multiIconBox::SPACE_SMALL
				]
			];
		}
		$buttons = we_html_button::formatButtons(we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close()"));
		echo we_html_tools::getHtmlTop(g_l('backup', '[view_log]'), '', '', we_html_element::jsScript(JS_DIR . 'closeEscape.js')
			, we_html_element::htmlBody(['class' => "weDialogBody", 'style' => "overflow:hidden;", 'onload' => "self.focus();"], '
	<div id="info">' .
				we_html_multiIconBox::getJS() .
				we_html_multiIconBox::getHTML('', $parts, 30, $buttons) .
				'</div>')
		);
	}

	public static function showBackupFrameset(){
		$weBackupWizard = new self(we_backup_wizard::BACKUP);

		switch($what = we_base_request::_(we_base_request::STRING, "pnt", 'frameset')){
			case "frameset":
				echo $weBackupWizard->getHTMLFrameset();
				break;
			case "body":
				echo $weBackupWizard->getHTMLStep(we_base_request::_(we_base_request::INT, "step", 1));
				break;
			case "cmd":
				echo we_html_tools::getHtmlTop('', '', '', $weBackupWizard->getHTMLCmd(), we_html_element::htmlBody());
				flush();
				break;
			case "busy":
				echo $weBackupWizard->getHTMLBusy();
				break;
			case "extern":
				echo $weBackupWizard->getHTMLExtern();
				break;
			default:
				t_e(__FILE__ . ' unknown reference: ' . $what);
		}
	}

	public static function showRecoverFrameset(){
		$what = we_base_request::_(we_base_request::STRING, 'pnt', 'frameset');
		$step = we_base_request::_(we_base_request::INT, 'step', 1);
		$weBackupWizard = new self(we_backup_wizard::RECOVER);

		switch($what){
			case 'frameset':
				echo $weBackupWizard->getHTMLFrameset();
				break;
			case 'body':
				echo $weBackupWizard->getHTMLStep($step);
				break;
			case 'cmd':
				if(($ret = $weBackupWizard->getHTMLCmd())){
					echo we_html_tools::getHtmlTop('webEdition', '', '', $ret, we_html_element::htmlBody());
					break;
				}
			case 'busy':
				echo $weBackupWizard->getHTMLBusy();
				break;
			case 'extern':
				echo $weBackupWizard->getHTMLExtern();
				break;
			default:
				t_e(__FILE__ . ' unknown reference: ' . $what);
		}
	}

	public static function getJSLangConsts(){
		return 'WE().consts.g_l.backupWizard={
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
	delold_confirm:"' . g_l('backup', '[delold_confirm]') . '",
	nothing_selected:"' . g_l('backup', '[nothing_selected]') . '",
	nothing_selected_fromlist:"' . g_l('backup', '[nothing_selected_fromlist]') . '",
	del_backup_confirm:"' . g_l('backup', '[del_backup_confirm]') . '",
};
';
	}

}
