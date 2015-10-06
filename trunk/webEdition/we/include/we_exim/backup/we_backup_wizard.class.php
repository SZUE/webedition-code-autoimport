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

	var $mode; //1-backup;2-recover
	var $frameset;
	var $db;
	private $fileUploader = null;

	function __construct($frameset = "", $mode = self::BACKUP){
		$this->setFrameset($frameset);
		$this->setMode($mode);
		$this->db = new DB_WE();
	}

	function setFrameset($frameset){
		$this->frameset = $frameset;
	}

	function setMode($mode){
		$this->mode = $mode;
	}

	public function setFileUploader($fileUploader){
		$this->fileUploader = $fileUploader;
	}

	public function getFileUploader(){
		return $this->fileUploader;
	}

	function getJSDep($mode, $docheck, $doclick, $douncheck = ''){
		return we_html_element::jsElement('
function we_submitForm(target,url) {
	var f = self.document.we_form;
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
}


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
	if (a.checked){
		switch(opt) {
			case 101:
				if(!document.we_form.handle_core.checked) {
					document.we_form.handle_core.value=1;
					document.we_form.handle_core.checked=true;
					' . we_message_reporting::getShowMessageCall(g_l('backup', '[' . $mode . '_temporary_dep]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}
			break;
			case 12:
				if(!document.we_form.handle_core.checked || !document.we_form.handle_object.checked) {
					document.we_form.handle_core.value=1;
					document.we_form.handle_core.checked=true;
					document.we_form.handle_object.value=1;
					document.we_form.handle_object.checked=true;
					' . we_message_reporting::getShowMessageCall(g_l('backup', '[' . $mode . '_versions_dep]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}
			break;
			case 13:
				if(!document.we_form.handle_core.checked || !document.we_form.handle_object.checked  || !document.we_form.handle_versions.checked) {
					document.we_form.handle_core.value=1;
					document.we_form.handle_core.checked=true;
					document.we_form.handle_object.value=1;
					document.we_form.handle_object.checked=true;
					document.we_form.handle_versions.value=1;
					document.we_form.handle_versions.checked=true;
					' . we_message_reporting::getShowMessageCall(g_l('backup', '[' . $mode . '_versions_binarys_dep]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}
			break;
			case 14:
				if(!document.we_form.handle_core.checked) {
					document.we_form.handle_core.value=1;
					document.we_form.handle_core.checked=true;
					' . we_message_reporting::getShowMessageCall(g_l('backup', '[' . $mode . '_binary_dep]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}
			break;
			case 55:
				if(!document.we_form.handle_core.checked || !document.we_form.handle_object.checked) {
					document.we_form.handle_core.value=1;
					document.we_form.handle_core.checked=true;
					document.we_form.handle_object.value=1;
					document.we_form.handle_object.checked=true;
					' . we_message_reporting::getShowMessageCall(g_l('backup', '[' . $mode . '_schedule_dep]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}
			break;
		' . ((defined('SHOP_TABLE')) ? ('
			case 30:
				' . ((defined('CUSTOMER_TABLE')) ? ('
				if(!document.we_form.handle_customer.checked) {
					document.we_form.handle_customer.value=1;
					document.we_form.handle_customer.checked=true;
					' . we_message_reporting::getShowMessageCall(g_l('backup', '[' . $mode . '_shop_dep]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}
			') : ('')) . '
				break;
		') : '') .
						((defined('WORKFLOW_TABLE')) ? ('
			case 35:
				if(!document.we_form.handle_user.checked || !document.we_form.handle_core.checked) {
					document.we_form.handle_core.value=1;
					document.we_form.handle_core.checked=true;
					document.we_form.handle_user.value=1;
					document.we_form.handle_user.checked=true;
					' . we_message_reporting::getShowMessageCall(g_l('backup', '[' . $mode . '_workflow_dep]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}
				break;
		') : '') .
						((defined('MESSAGING_SYSTEM')) ? ('
			case 40:
				if(!document.we_form.handle_user.checked) {
					document.we_form.handle_user.value=1;
					document.we_form.handle_user.checked=true;
					' . we_message_reporting::getShowMessageCall(g_l('backup', '[' . $mode . '_todo_dep]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}
				break;
		') : '') .
						((defined('NEWSLETTER_TABLE')) ? ('
			case 45:
				' . ((defined('CUSTOMER_TABLE')) ? ('
				if(!document.we_form.handle_customer.checked || !document.we_form.handle_core.checked || !document.we_form.handle_object.checked){
					document.we_form.handle_core.value=1;
					document.we_form.handle_core.checked=true;
					document.we_form.handle_object.value=1;
					document.we_form.handle_object.checked=true;
					document.we_form.handle_customer.value=1;
					document.we_form.handle_customer.checked=true;
					' . we_message_reporting::getShowMessageCall(g_l('backup', '[' . $mode . '_newsletter_dep]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}
			') : ('')) . '
				break;
		') : '') .
						((defined('BANNER_TABLE')) ? ('
			case 50:
				if(!document.we_form.handle_core.checked){
					document.we_form.handle_core.value=1;
					document.we_form.handle_core.checked=true;
					' . we_message_reporting::getShowMessageCall(g_l('backup', '[' . $mode . '_banner_dep]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}
				break;
		') : '') . '
		}
	}
	else{
		var mess="";
		switch(opt) {
			case 10:
			' . ((defined('WORKFLOW_TABLE')) ? ('
			if(document.forms["we_form"].elements["handle_workflow"].checked){
				document.forms["we_form"].elements["handle_workflow"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '_workflow_data]') . '";
			}
			') : ('')) . '
			' . ((defined('NEWSLETTER_TABLE')) ? ('
			if(document.forms["we_form"].elements["handle_newsletter"].checked){
				document.forms["we_form"].elements["handle_newsletter"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '_newsletter_data]') . '";
			}
			') : ('')) . '
			' . ((defined('BANNER_TABLE')) ? ('
			if(document.forms["we_form"].elements["handle_banner"].checked){
				document.forms["we_form"].elements["handle_banner"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '_banner_data]') . '";
			}
			') : ('')) . '
			' . (we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) ? ('
			if(document.forms["we_form"].elements["handle_schedule"].checked){
				document.forms["we_form"].elements["handle_schedule"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '_schedule_data]') . '";
			}
			') : ('')) . '
			if(document.forms["we_form"].elements["handle_versions"].checked){
				document.forms["we_form"].elements["handle_versions"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '_versions_data]') . '";
			}

			if(document.forms["we_form"].elements["handle_versions_binarys"].checked){
				document.forms["we_form"].elements["handle_versions_binarys"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '_versions_binarys_data]') . '";
			}
			if(document.forms["we_form"].elements["handle_temporary"].checked){
				document.forms["we_form"].elements["handle_temporary"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '][temporary_data]') . '";
			}
			if(document.forms["we_form"].elements["handle_history"].checked){
				document.forms["we_form"].elements["handle_history"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '][history_data]') . '";
			}
			if(mess!="") {
				tmpMess = "' . sprintf(g_l('backup', '[unselect_dep2]'), g_l('backup', '[' . $mode . '_core_data]')) . '"+mess+"\n' . g_l('backup', '[unselect_dep3]') . '";
				' . we_message_reporting::getShowMessageCall("tmpMess", we_message_reporting::WE_MESSAGE_NOTICE, true) . '
			}
			break;

			' . ((defined('OBJECT_TABLE')) ? ('
			case 11:
				' . (we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) ? ('
				if(document.forms["we_form"].elements["handle_schedule"].checked){
					document.forms["we_form"].elements["handle_schedule"].checked=false;
					mess+="\n-' . g_l('backup', '[' . $mode . '_schedule_data]') . '";
				}
			') : ('')) . '
			if(document.forms["we_form"].elements["handle_versions"].checked){
				document.forms["we_form"].elements["handle_versions"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '_versions_data]') . '";
			}
			if(document.forms["we_form"].elements["handle_versions_binarys"].checked){
				document.forms["we_form"].elements["handle_versions_binarys"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '_versions_binarys_data]') . '";
			}
			if(mess!="") {
				tmpMess = "' . sprintf(g_l('backup', '[unselect_dep2]'), g_l('backup', '[' . $mode . '_object_data]')) . '"+mess+"\n' . g_l('backup', '[unselect_dep3]') . '";
				' . we_message_reporting::getShowMessageCall("tmpMess", we_message_reporting::WE_MESSAGE_NOTICE, true) . '
			}
			break;
			case 12:

			if(document.forms["we_form"].elements["handle_versions_binarys"].checked){
				document.forms["we_form"].elements["handle_versions_binarys"].checked=false;
				mess+="\n-' . g_l('backup', '[' . $mode . '_versions_binarys_data]') . '";
			}
			if(mess!="") {
				tmpMess = "' . sprintf(g_l('backup', '[unselect_dep2]'), g_l('backup', '[' . $mode . '_versions_data]')) . '"+mess+"\n' . g_l('backup', '[unselect_dep3]') . '";
				' . we_message_reporting::getShowMessageCall("tmpMess", we_message_reporting::WE_MESSAGE_NOTICE, true) . '
			}
			break;
			') : ('')) . '

			case 14:
				if(mess!="") {
					tmpMess = "' . sprintf(g_l('backup', '[unselect_dep2]'), g_l('backup', '[' . $mode . '_binary_data]')) . '"+mess+"\n' . g_l('backup', '[unselect_dep3]') . '";
					' . we_message_reporting::getShowMessageCall("tmpMess", we_message_reporting::WE_MESSAGE_NOTICE, true) . '
				}
			break;
			case 20:
				' . ((defined('WORKFLOW_TABLE')) ? ('
				if(document.forms["we_form"].elements["handle_workflow"].checked){
					document.forms["we_form"].elements["handle_workflow"].checked=false;
					mess+="\n-' . g_l('backup', '[' . $mode . '_workflow_data]') . '";
				}
			' . ((defined('MESSAGING_SYSTEM')) ? ('
				if(document.forms["we_form"].elements["handle_todo"].checked){
					document.forms["we_form"].elements["handle_todo"].checked=false;
					mess+="\n-' . g_l('backup', '[' . $mode . '_todo_data]') . '";
				}
			') : ('')) . '
			if(mess!="") {
				tmpMess = "' . sprintf(g_l('backup', '[unselect_dep2]'), g_l('backup', '[' . $mode . '_user_data]')) . '"+mess+"\n' . g_l('backup', '[unselect_dep3]') . '";
				' . we_message_reporting::getShowMessageCall("tmpMess", we_message_reporting::WE_MESSAGE_NOTICE, true) . '
			}
			break;
			') : ('')) . '
			' . ((defined('CUSTOMER_TABLE')) ? ('
			case 25:
				' . ((defined('SHOP_TABLE')) ? ('
				if(document.forms["we_form"].elements["handle_shop"].checked){
					document.forms["we_form"].elements["handle_shop"].checked=false;
					mess+="\n-' . g_l('backup', '[' . $mode . '_shop_data]') . '";
				}
			') : ('')) . '
			' . ((defined('NEWSLETTER_TABLE')) ? ('
				if(document.forms["we_form"].elements["handle_newsletter"].checked){
					document.forms["we_form"].elements["handle_newsletter"].checked=false;
					mess+="\n-' . g_l('backup', '[' . $mode . '_newsletter_data]') . '";
				}
			') : ('')) . '
			if(mess!="") {
				tmpMess = "' . sprintf(g_l('backup', '[unselect_dep2]'), g_l('backup', '[' . $mode . '_customer_data]')) . '"+mess+"\n' . g_l('backup', '[unselect_dep3]') . '";
				' . we_message_reporting::getShowMessageCall("tmpMess", we_message_reporting::WE_MESSAGE_NOTICE, true) . '
			}
			break;
			') : ('')) . '
		}
	}
}');
	}

	function getHTMLFrameset(){
		$head = we_html_tools::getHtmlInnerHead(g_l('backup', '[wizard_' . ($this->mode == self::BACKUP ? 'backup' : 'recover') . '_title]')) . STYLESHEET;

		$body = we_html_element::htmlBody(array('style' => 'background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;')
						, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
								, we_html_element::htmlIFrame('body', $this->frameset . "?pnt=body", 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow: auto', 'border:0px;width:100%;height:100%;overflow: auto;') .
								we_html_element::htmlIFrame('busy', $this->frameset, 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden') .
								we_html_element::htmlIFrame('cmd', $this->frameset . "?pnt=cmd", 'position:absolute;height:0px;bottom:0px;left:0px;right:0px;overflow: hidden')
		));

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead($head) .
						$body
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
			array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[save_before]'), we_html_tools::TYPE_ALERT, 600), "space" => 0, "noline" => 1),
			array("headline" => "", "html" => g_l('backup', '[save_question]'), "space" => 0, "noline" => 1),
		);

		$js = we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
function startStep(){
	self.focus();
	top.busy.location="' . $this->frameset . '?pnt=busy&step=1";
}');

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "startStep()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_multiIconBox::getHTML("backup_options", "100%", $parts, 30, "", -1, "", "", false, g_l('backup', '[step1]'))
						)
		);
		$head = we_html_tools::getHtmlInnerHead(g_l('backup', '[wizard_title]')) . $js . STYLESHEET;

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead($head) .
						$body
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
	top.busy.location="' . $this->frameset . '?pnt=busy&step=2";
}

self.focus();
		');
		$parts = array(
			array("headline" => "", "html" => we_html_forms::radiobutton("import_server", true, "import_from", g_l('backup', '[import_from_server]')), "space" => 0, "noline" => 1),
			array("headline" => "", "html" => we_html_forms::radiobutton("import_upload", false, "import_from", g_l('backup', '[import_from_local]')), "space" => 0, "noline" => 1)
		);

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "startStep();"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden(array("name" => "pnt", "value" => "body")) .
								we_html_element::htmlHidden(array("name" => "step", "value" => 3)) .
								we_html_multiIconBox::getHTML("backup_options", "100%", $parts, 30, "", -1, "", "", false, g_l('backup', '[step2]'))
						)
		);

		$head = we_html_tools::getHtmlInnerHead(g_l('backup', '[wizard_title]')) . $js . STYLESHEET;

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead($head) .
						$body
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
		$parts = array();

		$js = "";

		//FIXME: delete condition when new uploader is stable
		if(!we_fileupload_include::USE_LEGACY_FOR_BACKUP){
			$maxsize = $this->fileUploader ? $this->fileUploader->getMaxUploadSize() : getUploadMaxFilesize();
		} else {
			$maxsize = getUploadMaxFilesize();
		}

		if(we_base_request::_(we_base_request::STRING, "import_from") === 'import_upload'){
			if($maxsize || $this->fileUploader){
				//FIXME: delete condition and else branch when new uploader is stable
				if(!we_fileupload_include::USE_LEGACY_FOR_BACKUP){
					if($this->fileUploader){
						$fileUploaderHead = $this->fileUploader->getCss() . $this->fileUploader->getJs();
						$inputTypeFile = $this->fileUploader->getHTML();
					} else {
						$alertMaxSize = sprintf(g_l('newFile', '[max_possible_size]'), we_base_file::getHumanFileSize($maxsize, we_base_file::SZ_MB));
						$inputTypeFile = we_html_element::htmlInput(array("name" => "we_upload_file", "type" => "file", "size" => 35));
					}

					$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[charset_warning]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 0, "noline" => 1);
					if(!(DEFAULT_CHARSET != '')){
						$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[defaultcharset_warning]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 0, "noline" => 1);
					}
					$parts[] = array("headline" => "", "html" => we_fileupload_base::getHtmlAlertBoxesStatic(600), "space" => 0, "noline" => 1);
					$parts[] = array("headline" => "", "html" => $inputTypeFile, "space" => 0, "noline" => 1);
					$parts[] = array("headline" => "", "html" => we_html_tools::getPixel(1, 1), "space" => 0, "noline" => 1);
				} else {
					$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[charset_warning]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 0, "noline" => 1);
					if(!(DEFAULT_CHARSET != '')){
						$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[defaultcharset_warning]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 0, "noline" => 1);
					}
					$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(sprintf(g_l('newFile', '[max_possible_size]'), we_base_file::getHumanFileSize($maxsize, we_base_file::SZ_MB)), we_html_tools::TYPE_ALERT, 600), "space" => 0, "noline" => 1);
					$parts[] = array("headline" => "", "html" => we_html_element::htmlInput(array("name" => "we_upload_file", "type" => "file", "size" => 35)), "space" => 0, "noline" => 1);
					$parts[] = array("headline" => "", "html" => we_html_tools::getPixel(1, 1), "space" => 0, "noline" => 1);
				}
			}
		} else {

			$js = '
function setLocation(loc){
	location.href=loc;
}
extra_files=new Array();
extra_files_desc=new Array();';
			$select = new we_html_select(array("name" => "backup_select", "size" => 7, "style" => "width: 600px;"));


			$files = array();
			$extra_files = array();
			$dateformat = g_l('date', '[format][default]');
			for($i = 0; $i <= 1; $i++){
				$adddatadir = ($i == 0 ? '' : 'data/');
				$dstr = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . $adddatadir;
				$d = dir($dstr);
				while(($entry = $d->read())){
					switch($entry){
						case '.':
						case '..':
						case 'CVS':
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

			$default = we_html_select::getNewOptionGroup(array('style' => 'font-weight: bold; font-style: normal; color: darkblue;', 'label' => g_l('backup', '[we_backups]')));
			$other = we_html_select::getNewOptionGroup(array('style' => 'font-weight: bold; font-style: normal; color: darkblue;', 'label' => g_l('backup', '[other_files]')));

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

			$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[charset_warning]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 0, "noline" => 1);
			if(!(DEFAULT_CHARSET != '')){
				$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[defaultcharset_warning]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 0, "noline" => 1);
			}
			$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[old_backups_warning]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 0, "noline" => 1);
			$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[select_server_file]'), we_html_tools::TYPE_INFO, 600, false), "space" => 0, "noline" => 1);
			$parts[] = array("headline" => "", "html" => $select->getHtml(), "space" => 0, "noline" => 1);
			//$parts[] =array("headline"=>"","html"=>we_html_forms::checkbox(1, false, "show_all", g_l('backup',"[show_all]"), false, "defaultfont", "showAll()"),"space"=>0,"noline"=>1);
			$parts[] = array("headline" => "", "html" => we_html_button::create_button("delete_backup", "javascript:delSelected();", true, 100, 22, '', '', false, false), "space" => 0);
		}

		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "rebuild", g_l('backup', '[rebuild]'), false), "space" => 0);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[delold_notice]'), we_html_tools::TYPE_QUESTION, 600, false), "space" => 0, "noline" => 1);
		$parts[] = array("headline" => "", "html" => we_html_button::create_button("delete", "javascript:delOldFiles();", true, 100, 22, '', '', false, false), "space" => 0);

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
			70 => defined('SPELLCHECKER') ? 'handle_spellchecker' : '',
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

		$docheck = '';
		$douncheck = '';
		$doclick = '';
		$doclickall1 = '';
		$doclickall2 = '';
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
				$parts[] = array('headline' => '', 'html' => we_html_forms::checkbox(1, true, $v, g_l('backup', '[' . str_replace('handle', 'import', $v) . '_data]'), false, 'defaultfont', "doClick($k);"), "space" => 70, "noline" => 1);
				$doclickall1.="doCheck($k);";
			} else {
				$doclickall2.="doCheck($k);";
			}
		}

		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "handle_temporary", g_l('backup', '[import][temporary_data]'), false, "defaultfont", "doClick(101);"), "space" => 70, "noline" => 1);

		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "handle_history", g_l('backup', '[import][history_data]'), false, "defaultfont", "doClick(102);"), "space" => 70, "noline" => 1);


		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[tools_import_desc]'), we_html_tools::TYPE_INFO, 600, false), "space" => 70, "noline" => 1);
		foreach($_tools as $_tool){
			$text = ($_tool === 'weSearch' ?
							g_l('searchtool', '[import_tool_' . $_tool . '_data]') :
							g_l('backup', '[import][weapp]') . ' ' . $_tool);

			$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, 'handle_tool[' . $_tool . ']', $text, false, "defaultfont", "doClick($k);"), "space" => 70, "noline" => 1);
		}

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[extern_exp]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 70, "noline" => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, false, "handle_extern", g_l('backup', '[import_extern_data]'), false, "defaultfont", "doClick(300);"), "space" => 70, "noline" => 1);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[convert_charset]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 70, "noline" => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, false, "convert_charset", g_l('backup', '[convert_charset_data]'), false, "defaultfont", "doClick(310);doUnCheck(101);doUnCheck(100);doUnCheck(70)"), "space" => 70, "noline" => 1);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[backup_log_exp]'), we_html_tools::TYPE_INFO, 600, false), "space" => 70, "noline" => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "backup_log", g_l('backup', '[export_backup_log]'), false, "defaultfont", "doClick(320);"), "space" => 70, "noline" => 1);


		$js = we_html_element::jsElement($js) .
				we_html_element::jsScript(JS_DIR . "windows.js") .
				(!we_fileupload_include::USE_LEGACY_FOR_BACKUP && isset($fileUploaderHead) ? $fileUploaderHead : '') .
				we_backup_wizard::getJSDep("import", $docheck, $doclick, $douncheck) .
				we_html_element::jsElement(we_html_button::create_state_changer(false) . '
function startBusy() {
	top.busy.location="' . $this->frameset . '?pnt=busy&operation_mode=busy&step=4";
}

function startImport(isFileReady) {
	var _usedEditors = top.opener.top.weEditorFrameController.getEditorsInUse(),
		isFileReady = isFileReady || false;
	for (frameId in _usedEditors) {
		_usedEditors[frameId].setEditorIsHot( false );

	}
	top.opener.top.weEditorFrameController.closeAllDocuments();

	' . ((we_base_request::_(we_base_request::STRING, "import_from") === "import_upload") ? ('
	if(isFileReady || document.we_form.we_upload_file.value) {
		startBusy();
		top.body.delete_enabled = top.body.switch_button_state("delete", "delete_enabled", "disabled");
		document.we_form.action = "' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php";
		setTimeout("document.we_form.submit()",100);
	}else
		' . we_message_reporting::getShowMessageCall(g_l('backup', '[nothing_selected]'), we_message_reporting::WE_MESSAGE_WARNING) . '
	') : ('
	if(document.we_form.backup_select.value) {
		startBusy();
		top.body.delete_backup_enabled = top.body.switch_button_state("delete_backup", "delete_backup_enabled", "disabled");
		top.body.delete_enabled = top.body.switch_button_state("delete", "delete_enabled", "disabled");
		document.we_form.action = "' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php";
		setTimeout("document.we_form.submit()",100);
	}
	else
		' . we_message_reporting::getShowMessageCall(g_l('backup', '[nothing_selected_fromlist]'), we_message_reporting::WE_MESSAGE_WARNING) . '
	')) . '
}

function showAll() {
	var a=document.we_form.backup_select.options;
	var b=document.we_form.show_all;

	if(b.checked){
		b.value=1;
		for(i=0;i<extra_files.length;i++)
			a[a.length]=new Option(extra_files_desc[i],extra_files[i]);
	}else {
		b.value=0;
		for(i=a.length-1;i>-1;i--){
			for(j=extra_files.length-1;j>-1;j--){
				if(a[i].value==extra_files[j]) {
					a[i]=null;
					break;
				}
			}
		}
	}
}

function delOldFiles(){
	if(confirm("' . g_l('backup', '[delold_confirm]') . '")) top.cmd.location="' . $this->frameset . '?pnt=cmd&operation_mode=deleteall";
}

function startStep(){
	top.busy.location="' . $this->frameset . '?pnt=busy&step=3";
}

function delSelected(){
	var sel = document.we_form.backup_select;
	if(sel.selectedIndex>-1){
		if(confirm("' . g_l('backup', '[del_backup_confirm]') . '")) top.cmd.location="' . $this->frameset . '?pnt=cmd&operation_mode=deletebackup&bfile="+sel.options[sel.selectedIndex].value;
	} else {
		' . we_message_reporting::getShowMessageCall(g_l('backup', '[nothing_selected_fromlist]'), we_message_reporting::WE_MESSAGE_WARNING) . '
	}
}

function delSelItem(){
	var sel = document.we_form.backup_select;
	if(sel.selectedIndex>-1){
		sel.remove(sel.selectedIndex);
	}
}

self.focus();');

		$form_attribs = (we_base_request::_(we_base_request::STRING, "import_from") === "import_upload" ?
						array("name" => "we_form", "method" => "post", "action" => $this->frameset, "target" => "cmd", "enctype" => "multipart/form-data") :
						array("name" => "we_form", "method" => "post", "action" => $this->frameset, "target" => "cmd")
				);

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "startStep();"), we_html_element::htmlForm($form_attribs, we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
								we_html_element::htmlHidden(array("name" => "cmd", "value" => "import")) .
								we_html_element::htmlHidden(array("name" => "step", "value" => 3)) .
								we_html_element::htmlHidden(array("name" => "MAX_FILE_SIZE", "value" => $maxsize)) .
								we_html_element::htmlInput(array("type" => "hidden", "name" => "operation_mode", "value" => "import")) .
								we_html_multiIconBox::getJS() .
								we_html_multiIconBox::getHTML("backup_options", "100%", $parts, 30, "", 7, g_l('backup', '[recover_option]'), "<b>" . g_l('backup', '[recover_option]') . "</b>", false, g_l('backup', '[step3]'))
						)
		);

		$head = we_html_tools::getHtmlInnerHead(g_l('backup', '[wizard_title]')) . $js . STYLESHEET;

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead($head) .
						$body
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
			array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[finished_success]'), we_html_tools::TYPE_INFO, 600), "space" => 0, "noline" => 1),
			array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[old_backups_warning]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 0, "noline" => 1)
		);

		$js = we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
function stopBusy() {
	top.busy.location="' . $this->frameset . '?pnt=busy&step=5";
	/*if(top.opener.top.header)
		top.opener.top.header.document.location.reload();*/
}
top.cmd.location ="about:blank";
self.focus();');

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "stopBusy()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "enctype" => "multipart/form-data"), we_html_multiIconBox::getHTML("backup_options", "100%", $parts, 34, "", -1, "", "", false, g_l('backup', '[step3]'))
						)
		);

		$head = we_html_tools::getHtmlInnerHead(g_l('backup', '[wizard_title]')) . $js . STYLESHEET;

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead($head) .
						$body
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
		if(defined('SPELLCHECKER')){
			$form_properties[70] = "handle_spellchecker";
		}
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
			array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', ($compression ? '[filename_compression]' : '[filename_info]')), we_html_tools::TYPE_INFO, 600, false), "space" => 0, "noline" => 1),
			array("headline" => g_l('backup', '[filename]') . ":&nbsp;&nbsp;", "html" => we_html_tools::htmlTextInput("filename", 60, 'weBackup_' . str_replace('.', '-', $_SERVER['SERVER_NAME']) . '_' . date("Y_m_d__H_i", time()) . '_' . str_replace('.', '-', WE_VERSION) . ".xml", "", "", "text"), "space" => 100, "noline" => 1)
		);

		if($compression){
			$switchbut = 9;
			$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(we_backup_base::COMPRESSION, true, "compress", g_l('backup', '[compress]'), false, "defaultfont", "", false, g_l('backup', '[ftp_hint]')), "space" => 100);
		} else {
			$switchbut = 7;
		}


		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[protect_txt]'), we_html_tools::TYPE_INFO, 600, false), "space" => 0, "noline" => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, false, "protect", g_l('backup', '[protect]'), false, "defaultfont", ""), "space" => 70);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[export_location]'), we_html_tools::TYPE_INFO, 600, false), "space" => 0, "noline" => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "export_server", g_l('backup', '[export_location_server]'), false, "defaultfont", "doClick(1)"), "space" => 70, "noline" => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, false, "export_send", g_l('backup', '[export_location_send]'), false, "defaultfont", "doClick(2)", (!permissionhandler::hasPerm("EXPORT"))), "space" => 70);
		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[export_options]'), we_html_tools::TYPE_INFO, 600, false), "space" => 0, "noline" => 1);

		$docheck = '';
		$doclick = '';
		$doclickall1 = '';
		$doclickall2 = '';
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
					"space" => 70,
					"noline" => 1
				);
				$doclickall1.="doCheck($k);";
			} else {
				$doclickall2.="doCheck($k);";
			}
		}

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[tools_export_desc]'), we_html_tools::TYPE_INFO, 600, false), "space" => 70, "noline" => 1);
		$k = 700;
		foreach($_tools as $_tool){
			$text = ($_tool === 'weSearch' ?
							g_l('searchtool', '[import_tool_' . $_tool . '_data]') :
							g_l('backup', '[export][weapp]') . ' ' . $_tool);

			$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, 'handle_tool[' . $_tool . ']', $text, false, "defaultfont", "doClick($k);"), "space" => 70, "noline" => 1);
			$k++;
		}

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[temporary_info]'), we_html_tools::TYPE_INFO, 600, false) . we_html_forms::checkbox(1, true, "handle_temporary", g_l('backup', '[export][temporary_data]'), false, "defaultfont", "doClick(101);"), "space" => 70);
		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[history_info]'), we_html_tools::TYPE_INFO, 600, false) . we_html_forms::checkbox(1, true, "handle_history", g_l('backup', '[export][history_data]'), false, "defaultfont", "doClick(102);"), "space" => 70);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[extern_exp]'), we_html_tools::TYPE_ALERT, 600, false), "space" => 70, "noline" => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, false, "handle_extern", g_l('backup', '[export_extern_data]'), false, "defaultfont", "doClick(300);"), "space" => 70, "noline" => 1);

		$parts[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('backup', '[backup_log_exp]'), we_html_tools::TYPE_INFO, 600, false), "space" => 70, "noline" => 1);
		$parts[] = array("headline" => "", "html" => we_html_forms::checkbox(1, true, "backup_log", g_l('backup', '[export_backup_log]'), false, "defaultfont", "doClick(320);"), "space" => 70, "noline" => 1);


		$mode = "export";
		$js = we_html_element::jsScript(JS_DIR . "windows.js") .
				we_backup_wizard::getJSDep("export", $docheck, $doclick) .
				we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
function startStep(){
	self.focus();
	top.busy.location="' . $this->frameset . '?pnt=busy&step=1";
}
function setLocation(loc){
	location.href=loc;
}');

		$_edit_cookie = weGetCookieVariable("but_edit_image");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "startStep()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", 'onsubmit' => 'return false;'), we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
								we_html_element::htmlHidden(array("name" => "cmd", "value" => "export")) .
								we_html_element::htmlHidden(array("name" => "operation_mode", "value" => "backup")) .
								we_html_element::htmlHidden(array("name" => "do_import_after_backup", "value" => we_base_request::_(we_base_request::BOOL, "do_import_after_backup"))) .
								we_html_multiIconBox::getJS() .
								we_html_multiIconBox::getHTML("backup_options1", 580, $parts, 30, "", $switchbut, g_l('backup', '[option]'), "<b>" . g_l('backup', '[option]') . "</b>", $_edit_cookie != false ? ($_edit_cookie === "down") : $_edit_cookie, g_l('backup', '[export_step1]'))
						)
		);

		$head = we_html_tools::getHtmlInnerHead(g_l('backup', '[wizard_title_export]')) . STYLESHEET . $js;

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead($head) .
						$body
		);
	}

	function getHTMLBackupStep2(){
		$content = '';

		$table = new we_html_table(array('cellpadding' => 0, 'cellspacing' => 0, 'border' => 0, 'class' => 'defaultfont'), 4, 1);

		$table->setCol(0, 0, null, g_l('backup', '[finish]'));
		$table->setCol(1, 0, null, we_html_tools::getPixel(5, 20));

		if($_SESSION['weS']['weBackupVars']['options']['export2send']){
			$_down = $_SESSION['weS']['weBackupVars']['backup_file'];
			if(is_file($_SESSION['weS']['weBackupVars']['backup_file'])){
//Note: we show a link for external download - do we need this?

				$_link = WEBEDITION_DIR . 'showTempFile.php?' . http_build_query(array(
						'file' => str_replace(WEBEDITION_PATH, '', $_down),
						'binary' => 1
				));

				$table->setCol(2, 0, array('class' => 'defaultfont'), self::getDownloadLinkText() . '<br/><br/>' .
						we_html_element::htmlA(array('href' => $_link), g_l('backup', '[download_file]'))
				);
			} else {
				$table->setCol(2, 0, null, g_l('backup', '[download_failed]'));
			}
		}


		$table->setCol(3, 0, null, we_html_tools::getPixel(5, 5));

		$content.=$table->getHtml();

		$do_import_after_backup = (isset($_SESSION['weS']['weBackupVars']['options']['do_import_after_backup']) && $_SESSION['weS']['weBackupVars']['options']['do_import_after_backup']) ? 1 : 0;
		$js = we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
function startStep(){
	self.focus();
	top.busy.location="' . $this->frameset . '?pnt=busy&do_import_after_backup=' . $do_import_after_backup . '&step=3";
}');

		$head = we_html_tools::getHtmlInnerHead(g_l('backup', '[wizard_title_export]')) . $js . STYLESHEET;
		$body = we_html_element::htmlBody(array('class' => 'weDialogBody', 'onload' => 'startStep();'), we_html_element::htmlForm(array('name' => 'we_form', 'method' => 'post'), we_html_tools::htmlDialogLayout($content, g_l('backup', '[export_step2]'))
						)
		);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead($head) .
						$body
		);
	}

	function getHTMLBackupStep3(){
		update_time_limit(0);
		if(we_base_request::_(we_base_request::BOOL, "backupfile")){
			$_filename = urldecode(we_base_request::_(we_base_request::RAW, "backupfile"));

			if(file_exists($_filename) && stripos($_filename, $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR) !== false){ // Does file exist and does it saved in backup dir?
				$_size = filesize($_filename);

				header("Pragma: public");
				header("Expires: 0");
				header("Cache-control: private, max-age=0, must-revalidate");
				header('Content-Type: application/octet-stream');
				header("Content-Disposition: attachment; filename=\"" . trim(htmlentities(basename($_filename))) . "\"");
				header("Content-Description: " . trim(htmlentities(basename($_filename))));
				header("Content-Length: " . $_size);

				if(($_filehandler = fopen($_filename, 'rb'))){
					while(!feof($_filehandler)){
						print(fread($_filehandler, 8192));
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

			we_base_file::insertIntoCleanUp($_SESSION['weS']['weBackupVars']['backup_file'], time());
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
		$_header = we_html_tools::getHtmlTop() . STYLESHEET;

		$_error_message = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0, "class" => "defaultfont"), 1, 1);
		$_error_message->setCol(0, 0, null, g_l('backup', '[download_failed]'));

		return $_header . '<body class="weDialogBody">' . we_html_tools::htmlDialogLayout($_error_message->getHtml(), g_l('backup', '[export_step2]'));
	}

	function getHTMLExtern(){
		$txt = g_l('backup', '[extern_backup_question_' . we_base_request::_(we_base_request::STRING, "w", "exp") . ']');

		$yesCmd = "self.close();";
		$noCmd = "top.opener.top.body.clearExtern();" . $yesCmd;

		$js = we_html_element::jsElement('self.focus();');

		$body = we_html_element::htmlBody(array("class" => "weEditorBody", "onBlur" => "self.focus()", "onload" => "self.focus();"), we_html_element::htmlForm(
								array("name" => "we_form"), we_html_tools::htmlYesNoCancelDialog($txt, IMAGE_DIR . "alert.gif", "ja", "nein", "", $yesCmd, $noCmd)
						)
		);

		$head = we_html_tools::getHtmlInnerHead(g_l('backup', '[wizard_title]')) . $js . STYLESHEET;

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead($head) .
						$body
		);
	}

	function getHTMLBusy(){
		$head = we_html_tools::getHtmlInnerHead(g_l('backup', '[wizard_title]')) . STYLESHEET;
		$body = '';

		$table = new we_html_table(array("border" => 0, "align" => "right", "cellpadding" => 0, "cellspacing" => 0), 2, 4);
		$table->setCol(0, 0, null, we_html_tools::getPixel(15, 5));

		if(we_base_request::_(we_base_request::STRING, "operation_mode") === "busy"){
			$text = we_base_request::_(we_base_request::BOOL, "current_description", g_l('backup', '[working]'));

			$progress = new we_progressBar(we_base_request::_(we_base_request::INT, "percent", 0));
			$progress->setStudLen(200);
			$progress->addText($text, 0, "current_description");
			$head.=$progress->getJSCode('top.busy');
			$body.=$progress->getHtml();
			$table->setCol(0, 1, null, $body);
			$table->setCol(1, 1, null, we_html_tools::getPixel(250, 1));
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
		top.busy.location="' . $this->frameset . '?pnt=busy&operation_mode=busy&step=2";
		top.body.we_submitForm("cmd","' . WE_INCLUDES_DIR . 'we_editors/we_backup_cmd.php");
	}
}');
						$table->setCol(0, 2, null, we_html_tools::getPixel(355, 5));
						$table->setCol(0, 3, null, we_html_button::position_yes_no_cancel(we_html_button::create_button("make_backup", "javascript:doExport();"), null, we_html_button::create_button("cancel", "javascript:top.close();")));
						break;
					case 2:
						$table->setCol(0, 2, null, we_html_tools::getPixel(265, 5));
						$table->setCol(0, 3, null, we_html_button::create_button("cancel", "javascript:top.close();"));
						break;
					case 3:
						if(we_base_request::_(we_base_request::BOOL, "do_import_after_backup")){
							$body = we_html_button::create_button("next", "javascript:top.body.location='" . WE_INCLUDES_DIR . "we_editors/we_recover_backup.php?pnt=body&step=2';top.busy.location='" . WE_INCLUDES_DIR . "we_editors/we_recover_backup.php?pnt=cmd';top.cmd.location='" . WE_INCLUDES_DIR . "we_editors/we_recover_backup.php?pnt=busy';");
						} else if(isset($_SESSION['weS']['inbackup']) && $_SESSION['weS']['inbackup']){
							$body = we_html_button::create_button("next", "javascript:top.opener.weiter();top.close();");
							unset($_SESSION['weS']['inbackup']);
						} else {
							$head.=we_html_element::jsElement("top.opener.top.afterBackup=true;");
							$body = we_html_button::create_button("close", "javascript:top.close();");
						}
						$table->setCol(0, 2, null, we_html_tools::getPixel(495, 5));
						$table->setCol(0, 3, null, $body);
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

	var _usedEditors = top.opener.top.weEditorFrameController.getEditorsInUse();
	var _unsavedChanges = false;
	for (frameId in _usedEditors) {
		if ( _usedEditors[frameId].getEditorIsHot() ) {
			_unsavedChanges = true;
		}
	}

	if (_unsavedChanges) {
		' . we_message_reporting::getShowMessageCall(g_l('backup', '[recover_backup_unsaved_changes]'), we_message_reporting::WE_MESSAGE_WARNING) . "
	} else {
		top.body.location='" . WE_INCLUDES_DIR . "we_editors/we_make_backup.php?pnt=body&do_import_after_backup=1';
		top.busy.location='" . WE_INCLUDES_DIR . "we_editors/we_make_backup.php?pnt=busy';
		top.cmd.location='" . WE_INCLUDES_DIR . "we_editors/we_make_backup.php?pnt=cmd';
	}

}");
						$buttons = we_html_button::position_yes_no_cancel(
										we_html_button::create_button("yes", "javascript:press_yes();"), we_html_button::create_button("no", "javascript:top.body.location='" . $this->frameset . "?pnt=body&step=2';"), we_html_button::create_button("cancel", "javascript:top.close();")
						);
						$table->setCol(0, 2, null, we_html_tools::getPixel(290, 5));
						$table->setCol(0, 3, null, $buttons);
						break;
					case 2:

						$nextbuts = we_html_button::create_button_table(array(
									we_html_button::create_button("back", "javascript:top.body.location='" . $this->frameset . "?pnt=body&step=1'", true),
									we_html_button::create_button("next", "javascript:top.body.we_submitForm('body','" . $this->frameset . "');")));

						$buttons = we_html_button::position_yes_no_cancel($nextbuts, null, we_html_button::create_button("cancel", "javascript:top.close();"));

						$table->setCol(0, 2, null, we_html_tools::getPixel(290, 5));
						$table->setCol(0, 3, null, $buttons);
						break;
					case 3:
						//FIXME: delete condition when new uploader is stable
						if(!we_fileupload_include::USE_LEGACY_FOR_BACKUP){
							//$startImportCall = $this->fileUploader ? $this->fileUploader->getJsBtnCmd('upload') : "top.body.startImport();";
							if(!(we_fileupload_include::isFallback() || we_fileupload_base::isLegacyMode())){
								$startImportCall = we_fileupload_include::getJsBtnCmdStatic('upload', 'body', 'top.body.startImport()');
								$cancelCall = we_fileupload_include::getJsBtnCmdStatic('cancel', 'body');
							} else {
								$startImportCall = 'top.body.startImport()';
								$cancelCall = 'top.close()';
							}

							if(defined('WORKFLOW_TABLE')){
								$db = new DB_WE();
								$nextbut = (we_workflow_utility::getAllWorkflowDocs(FILE_TABLE, $db) || (defined('OBJECT_FILES_TABLE') && we_workflow_utility::getAllWorkflowDocs(OBJECT_FILES_TABLE, $db)) ?
												we_html_button::create_button("restore_backup", "javascript:if(confirm('" . g_l('modules_workflow', '[ask_before_recover]') . "')) " . $startImportCall . ";") :
												we_html_button::create_button("restore_backup", "javascript:" . $startImportCall));
							} else {
								$nextbut = we_html_button::create_button("restore_backup", "javascript:" . $startImportCall);
							}
						} else {
							if(defined('WORKFLOW_TABLE')){
								$db = new DB_WE();
								$nextbut = (we_workflow_utility::getAllWorkflowDocs(FILE_TABLE, $db) || (defined('OBJECT_FILES_TABLE') && we_workflow_utility::getAllWorkflowDocs(OBJECT_FILES_TABLE, $db)) ?
												we_html_button::create_button("restore_backup", "javascript:if(confirm('" . g_l('modules_workflow', '[ask_before_recover]') . "')) top.body.startImport();") :
												we_html_button::create_button("restore_backup", "javascript:top.body.startImport();"));
							} else {
								$nextbut = we_html_button::create_button("restore_backup", "javascript:top.body.startImport();");
							}
						}

						$nextprevbuts = we_html_button::create_button_table(array(
									we_html_button::create_button("back", "javascript:top.body.location='" . $this->frameset . "?pnt=body&step=2';"),
									$nextbut));
						$buttons = we_html_button::position_yes_no_cancel($nextprevbuts, null, we_html_button::create_button("cancel", "javascript:" . $cancelCall));

						$table->setCol(0, 2, null, we_html_tools::getPixel(240, 5));
						$table->setCol(0, 3, null, $buttons);
						break;
					case 4:
						$table->setCol(0, 2, null, we_html_tools::getPixel(260, 5));
						$table->setCol(0, 3, null, we_html_button::create_button("cancel", "javascript:top.close();"));
						break;
					case 5:
						$table->setCol(0, 2, null, we_html_tools::getPixel(490, 5));
						$table->setCol(0, 3, null, we_html_button::create_button("close", "javascript:top.close();"));
						break;
					default:
				}
				break;
		}

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead($head) .
						we_html_element::htmlBody(array("class" => "weDialogButtonsBody"), $table->getHtml()
						)
		);
	}

	function getHTMLCmd(){
		switch(we_base_request::_(we_base_request::STRING, "operation_mode", '-1')){
			case '-1':
				return;
			case "backup":
				if(!is_writable($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . "tmp")){
					echo we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
top.busy.location="' . $this->frameset . '?pnt=busy";' .
							we_message_reporting::getShowMessageCall(sprintf(g_l('backup', '[cannot_save_tmpfile]'), BACKUP_DIR), we_message_reporting::WE_MESSAGE_ERROR)
					);
					return "";
				}

				$handle_options = array(
					"user" => we_base_request::_(we_base_request::BOOL, "handle_user"),
					"customer" => we_base_request::_(we_base_request::BOOL, "handle_customer"),
					"shop" => we_base_request::_(we_base_request::BOOL, "handle_shop"),
					"workflow" => we_base_request::_(we_base_request::BOOL, "handle_workflow"),
					"todo" => we_base_request::_(we_base_request::BOOL, "handle_todo"),
					"newsletter" => we_base_request::_(we_base_request::BOOL, "handle_newsletter"),
					"temporary" => we_base_request::_(we_base_request::BOOL, "handle_temporary"),
					"history" => we_base_request::_(we_base_request::BOOL, "handle_history"),
					"banner" => we_base_request::_(we_base_request::BOOL, "handle_banner"),
					"core" => we_base_request::_(we_base_request::BOOL, "handle_core"),
					"object" => we_base_request::_(we_base_request::BOOL, "handle_object"),
					"schedule" => we_base_request::_(we_base_request::BOOL, "handle_schedule"),
					"hooks" => we_base_request::_(we_base_request::BOOL, "handle_hooks"),
					"customTags" => we_base_request::_(we_base_request::BOOL, "handle_customtags"),
					"settings" => we_base_request::_(we_base_request::BOOL, "handle_settings"),
					"export" => we_base_request::_(we_base_request::BOOL, "handle_export"),
					"voting" => we_base_request::_(we_base_request::BOOL, "handle_voting"),
				);
				$we_backup_obj = new we_backup_backup($handle_options);
				$temp_filename = we_base_request::_(we_base_request::FILE, "temp_filename", '');

				if(!$temp_filename){
					$we_backup_obj->backup_extern = we_base_request::_(we_base_request::BOOL, "handle_extern");
					$we_backup_obj->convert_charset = we_base_request::_(we_base_request::BOOL, "convert_charset");
					$we_backup_obj->export2server = we_base_request::_(we_base_request::BOOL, "export_server");
					$we_backup_obj->export2send = we_base_request::_(we_base_request::BOOL, "export_send");
					$we_backup_obj->filename = we_base_request::_(we_base_request::FILE, "filename");
					$we_backup_obj->compress = we_base_request::_(we_base_request::STRING, 'compress', we_backup_base::NO_COMPRESSION);
					$we_backup_obj->backup_binary = we_base_request::_(we_base_request::BOOL, "handle_binary");

					//create file list
					if($we_backup_obj->backup_extern){
						$we_backup_obj->getFileList();
					}
					//create table list
					//$we_backup_obj->getTableList();
				} else {
					$temp_filename = $we_backup_obj->restoreState($temp_filename);
					$we_backup_obj->setDescriptions();
				}

				$ret = $we_backup_obj->makeBackup();
				$temp_filename = $we_backup_obj->saveState($temp_filename);

				$do_import_after_backup = we_base_request::_(we_base_request::BOOL, 'do_import_after_backup');

				switch($ret){
					case 1:
						$percent = $we_backup_obj->getExportPercent();
						echo we_html_element::jsElement('
if(top.busy.setProgressText) top.busy.setProgressText("current_description","' . $we_backup_obj->current_description . '");
if(top.busy.setProgress) top.busy.setProgress(' . $percent . ');
top.cmd.location="' . $this->frameset . '?pnt=cmd&operation_mode=backup&do_import_after_backup=' . $do_import_after_backup . '&temp_filename=' . $temp_filename . '";');
						break;
					case -1:
						echo we_html_element::jsElement('
if(top.busy.setProgressText) top.busy.setProgressText("current_description","' . g_l('backup', '[finished]') . '");
if(top.busy.setProgress) top.busy.setProgress(100);
top.body.location="' . $this->frameset . '?pnt=body&step=2&ok=false&do_import_after_backup=' . $do_import_after_backup . '&temp_filename=' . $temp_filename . '";');
						break;
					default:
						$we_backup_obj->writeFooter();
						$we_backup_obj->printDump2BackupDir();
						$temp_filename = $we_backup_obj->saveState($temp_filename);

						echo we_html_element::jsElement('
if(top.busy.setProgressText) top.busy.setProgressText("current_description","' . g_l('backup', '[finished]') . '");
if(top.busy.setProgress) top.busy.setProgress(100);
top.body.location="' . $this->frameset . '?pnt=body&step=2&ok=false&do_import_after_backup=' . $do_import_after_backup . '&temp_filename=' . $temp_filename . '";
									');

						break;
				}
				flush();
				unset($we_backup_obj);
				break;
			case "rebuild":
				echo we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
top.opener.top.openWindow("' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_all&responseText=' . g_l('backup', '[finished_success]') . '","rebuildwin",-1,-1,600,130,0,true);
setTimeout("top.close();",300);'
				);
				break;
			case "import":
				$continue = true;
				if(!is_writable($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . "tmp")){
					echo we_html_element::jsElement('
function setLocation(loc){
	location.href=loc;
}
top.busy.location="' . $this->frameset . '?pnt=busy";' .
							we_message_reporting::getShowMessageCall(sprintf(g_l('backup', '[cannot_save_tmpfile]'), BACKUP_DIR), we_message_reporting::WE_MESSAGE_ERROR)
					);
					return '';
				}

				$handle_options = array(
					"user" => we_base_request::_(we_base_request::BOOL, "handle_user"),
					"customer" => we_base_request::_(we_base_request::BOOL, "handle_customer"),
					"shop" => we_base_request::_(we_base_request::BOOL, "handle_shop"),
					"workflow" => we_base_request::_(we_base_request::BOOL, "handle_workflow"),
					"todo" => we_base_request::_(we_base_request::BOOL, "handle_todo"),
					"newsletter" => we_base_request::_(we_base_request::BOOL, "handle_newsletter"),
					"temporary" => we_base_request::_(we_base_request::BOOL, "handle_temporary"),
					"history" => we_base_request::_(we_base_request::BOOL, "handle_history"),
					"banner" => we_base_request::_(we_base_request::BOOL, "handle_banner"),
					"core" => we_base_request::_(we_base_request::BOOL, "handle_core"),
					"object" => we_base_request::_(we_base_request::BOOL, "handle_object"),
					"schedule" => we_base_request::_(we_base_request::BOOL, "handle_schedule"),
					"hooks" => we_base_request::_(we_base_request::BOOL, "handle_hooks"),
					"customTags" => we_base_request::_(we_base_request::BOOL, "handle_customtags"),
					"settings" => we_base_request::_(we_base_request::BOOL, "handle_settings"),
					"export" => we_base_request::_(we_base_request::BOOL, "handle_export"),
					"voting" => we_base_request::_(we_base_request::BOOL, "handle_voting"),
				);
				$we_backup_obj = new we_backup_backup($handle_options);
				$temp_filename = we_base_request::_(we_base_request::FILE, "temp_filename");

				if(!$temp_filename){
					$we_backup_obj->backup_extern = we_base_request::_(we_base_request::BOOL, "handle_extern");
					$we_backup_obj->convert_charset = we_base_request::_(we_base_request::BOOL, "convert_charset");
					$we_backup_obj->compress = we_base_request::_(we_base_request::STRING, "compress");
					$we_backup_obj->backup_binary = we_base_request::_(we_base_request::BOOL, "handle_binary");
					$we_backup_obj->rebuild = we_base_request::_(we_base_request::BOOL, "rebuild");

					$backup_select = we_base_request::_(we_base_request::STRING, "backup_select", '');
					$we_upload_file = (isset($_FILES["we_upload_file"]) && $_FILES["we_upload_file"]) ? $_FILES["we_upload_file"] : "";
					$ok = false;

					if($backup_select){
						$we_backup_obj->filename = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . $backup_select;
						$ok = true;
					} else if($we_upload_file && ($we_upload_file != "none")){
						//FIXME: delete condition when new uploader is stable
						if(!we_fileupload_include::USE_LEGACY_FOR_BACKUP){
							if($this->fileUploader){
								$continue = $this->fileUploader->processFileRequest();
							} else {
								$we_backup_obj->filename = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $_FILES['we_upload_file']['name'];
								if(!move_uploaded_file($_FILES["we_upload_file"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . "tmp/" . $_FILES["we_upload_file"]["name"])){
									echo we_html_element::jsElement('top.busy.location="' . $this->frameset . '?pnt=busy";' .
											we_message_reporting::getShowMessageCall(sprintf(g_l('backup', '[cannot_save_tmpfile]'), BACKUP_DIR), we_message_reporting::WE_MESSAGE_ERROR));
									return '';
								}
								we_base_file::insertIntoCleanUp($we_backup_obj->filename, time());
								$ok = true;
							}
						} else {
							$we_backup_obj->filename = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $_FILES['we_upload_file']['name'];
							if(!move_uploaded_file($_FILES["we_upload_file"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . "tmp/" . $_FILES["we_upload_file"]["name"])){
								echo we_html_element::jsElement('top.busy.location="' . $this->frameset . '?pnt=busy";' .
										we_message_reporting::getShowMessageCall(sprintf(g_l('backup', '[cannot_save_tmpfile]'), BACKUP_DIR), we_message_reporting::WE_MESSAGE_ERROR));
								return '';
							}
							we_base_file::insertIntoCleanUp($we_backup_obj->filename, time());
							$ok = true;
						}
					} else {
						$we_alerttext = sprintf(g_l('alert', '[we_backup_import_upload_err]'), ini_get("upload_max_filesize"));
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall($we_alerttext, we_message_reporting::WE_MESSAGE_ERROR));
						$ok = false;
					}

					if($continue){
						if($handle_options["core"]){
							$we_backup_obj->getSiteFiles();
							$we_backup_obj->getFileList(TEMPLATES_PATH, true, false);
						}

						$we_backup_obj->getVersion($we_backup_obj->filename);
						$we_backup_obj->file_end = $we_backup_obj->splitFile2();
						if($we_backup_obj->file_end < 0){
							echo we_html_element::jsElement('top.busy.location = "' . $this->frameset . '?pnt=busy";' .
									we_message_reporting::getShowMessageCall(sprintf(g_l('backup', '[cannot_split_file]'), basename($we_backup_obj->filename)) . ($we_backup_obj->file_end == -10 ? g_l('backup', '[cannot_split_file_ziped]') : ''), we_message_reporting::WE_MESSAGE_ERROR));
							return '';
						}
						if($handle_options["core"]){
							$we_backup_obj->clearTemporaryData("tblFile");
						}
						if($handle_options["object"]){
							$we_backup_obj->clearTemporaryData("tblObjectFiles");
						}
					}
				} else {
					$temp_filename = $we_backup_obj->restoreState($temp_filename);
					$we_backup_obj->setDescriptions();
				}

				if($continue){
					if(!empty($we_backup_obj->file_list)){
						for($i = 0; $i < $we_backup_obj->backup_steps; $i++){
							if(empty($we_backup_obj->file_list)){
								break;
							}
							we_base_file::delete(array_pop($we_backup_obj->file_list));
						}
						$temp_filename = $we_backup_obj->saveState($temp_filename);
						$percent = $we_backup_obj->getImportPercent();
						echo we_html_element::jsElement('
	if(top.busy.setProgressText) top.busy.setProgressText("current_description", "' . g_l('backup', '[delete_old_files]') . '");
	if(top.busy.setProgress) top.busy.setProgress(' . $percent . ');
	top.cmd.location = "' . $this->frameset . '?pnt=cmd&operation_mode=import&temp_filename=' . $temp_filename . '";
								');
						flush();
					} else if($we_backup_obj->file_counter < $we_backup_obj->file_end){
						$filename_tmp = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . "tmp/" . basename($we_backup_obj->filename) . "_" . $we_backup_obj->file_counter;
						$we_backup_obj->file_counter++;
						$ok = $we_backup_obj->restoreChunk($filename_tmp);
						$temp_filename = $we_backup_obj->saveState($temp_filename);
						we_base_file::delete($filename_tmp);

						if($ok){
							$percent = $we_backup_obj->getImportPercent();
							if($percent == 100){
								$we_backup_obj->current_description = g_l('backup', '[finished]');
							}
							if(!$we_backup_obj->current_description){
								$we_backup_obj->current_description = g_l('backup', '[working]');
							}

							echo we_html_element::jsElement('
	if(top.busy.setProgressText) top.busy.setProgressText("current_description", "' . $we_backup_obj->current_description . '");
	if(top.busy.setProgress) top.busy.setProgress(' . $percent . ');
	top.cmd.location = "' . $this->frameset . '?pnt=cmd&operation_mode=import&temp_filename=' . $temp_filename . '";'
							);
						} else {
							echo we_html_element::jsElement('
	top.busy.location = "' . $this->frameset . '?pnt=busy";
	top.body.location = "' . $this->frameset . '?pnt=body&step=4&temp_filename=' . $temp_filename . '";'
							);
						}
						flush();
					} else {
						$we_backup_obj->doUpdate();
						if(is_file($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $temp_filename) && $we_backup_obj->rebuild && empty($we_backup_obj->errors)){
							unlink($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/' . $temp_filename);
						}
						echo we_html_element::jsElement('
	top.opener.top.we_cmd("load", "' . FILE_TABLE . '");
	top.opener.top.we_cmd("exit_delete");
	top.busy.location = "' . $this->frameset . '?pnt=busy&operation_mode=busy&current_description=' . g_l('backup', '[finished]') . '&percent=100";' .
								($we_backup_obj->rebuild && empty($we_backup_obj->errors) ?
										'top.cmd.location = "' . $this->frameset . '?pnt=cmd&operation_mode=rebuild";' :
										'top.body.location = "' . $this->frameset . '?pnt=body&step=4&temp_filename=' . $temp_filename . '";')
						);
						flush();
					}
				}
				break;
			case "deleteall":
				$_SESSION['weS']['backup_delete'] = 1;
				$_SESSION['weS']['delete_files_nok'] = array();
				$_SESSION['weS']["delete_files_info"] = g_l('backup', '[files_not_deleted]');
				echo we_html_element::jsScript(JS_DIR . "windows.js") .
				we_html_element::jsElement('new jsWindow("' . WEBEDITION_DIR . 'delFrag.php?currentID=-1", "we_del", -1, -1, 600, 130, true, true, true);');
				break;
			case "deletebackup":
				$bfile = we_base_request::_(we_base_request::FILE, "bfile");
				if(strpos($bfile, '..') === 0){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[name_notok]'), we_message_reporting::WE_MESSAGE_ERROR));
				} else {
					if(!is_writable($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . $bfile)){
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[error_delete]'), we_message_reporting::WE_MESSAGE_ERROR));
					} else {
						echo we_html_element::jsElement((unlink($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . $bfile) ?
										'if(top.body.delSelItem) top.body.delSelItem();' :
										we_message_reporting::getShowMessageCall(g_l('backup', '[error_delete]'), we_message_reporting::WE_MESSAGE_ERROR))
						);
					}
				}
				break;
			default:
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[error]'), we_message_reporting::WE_MESSAGE_ERROR)
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

	/**
	 * Function: printErrors
	 *
	 * Description: This function tells the user, if and which error(s) took
	 * place.
	 */
	function printErrors(&$we_backup_obj){
		$errors = $we_backup_obj->getErrors();

		$text = "";
		if(!empty($errors)){
			foreach($errors as $k => $v){
				$text .= g_l('backup', '[error]') . ' [' . ++$k . ']: ' . $v . "\n";
			}
		} else {
			$text.=g_l('backup', '[unspecified_error]');
		}

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0, "class" => "defaultfont"), 3, 1);
		$table->setCol(0, 0, null, g_l('backup', '[finish_error]'));
		$table->setCol(1, 0, null, we_html_element::htmlTextArea(array("name" => "text_errors", "cols" => 45, "rows" => 7), $text));
		$table->setCol(2, 0, null, we_html_tools::getPixel(400, 5));
		return $table->getHtml();
	}

	/**
	 * Function: printWarnings
	 *
	 * Description: This function tells the user, if and which warning(s)
	 * took place.
	 */
	function printWarnings(&$we_backup_obj){
		$warnings = $we_backup_obj->getWarnings();

		if(!empty($warnings)){

			foreach($warnings as $k => $v){
				$text .= g_l('backup', '[warning]') . ' [' . ++$k . ']: ' . $v . "\n";
			}

			$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0, "class" => "defaultfont"), 3, 1);
			$table->setCol(0, 0, null, g_l('backup', '[finish_warning]'));
			$table->setCol(1, 0, null, we_html_element::htmlTextArea(array("name" => "text_errors", "cols" => 45, "rows" => 7), $text));
			$table->setCol(2, 0, null, we_html_tools::getPixel(400, 5));
			return $table->getHtml();
		}
		return "";
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

setTimeout("reloadFrame()", ' . $_execute . ');');
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
