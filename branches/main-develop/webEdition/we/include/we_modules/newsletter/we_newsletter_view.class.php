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
/* the parent class of storagable webEdition classes */

class we_newsletter_view extends we_modules_view{
	const MAILS_ALL = 0;
	const MAILS_CUSTOMER = 1;
	const MAILS_EMAILS = 2;
	const MAILS_FILE = 3;

	// settings array; format settings[setting_name]=settings_value
	var $settings = [];
	//default newsletter
	var $newsletter;
	//wat page is currentlly displayed 0-properties(default);1-overview;
	var $page = 0;
	var $get_import = 0;
	var $hiddens = ['ID'];
	var $customers_fields;
	protected $show_import_box = -1;
	protected $show_export_box = -1;

	public function __construct($frameset){
		parent::__construct($frameset);

		$this->newsletter = new we_newsletter_newsletter();

		$this->settings = self::getSettings();
		//FIXME: add types for settings

		if(defined('CUSTOMER_TABLE')){
			$this->customers_fields = [];
			$this->db->query('SHOW FIELDS FROM ' . CUSTOMER_TABLE);
			while($this->db->next_record()){
				$this->customers_fields[] = $this->db->f('Field');
			}
		}
		$this->newsletter->Text = g_l('modules_newsletter', '[new_newsletter]');
		$this->newsletter->Sender = $this->settings['default_sender'];
		$this->newsletter->Reply = $this->settings['default_reply'];
		$this->newsletter->Test = $this->settings['test_account'];
		$this->newsletter->isEmbedImages = $this->settings['isEmbedImages'];
	}

	function getHiddens($predefs = []){
		return we_html_element::htmlHiddens(['mod' => 'newsletter',
				'ncmd' => (isset($predefs['ncmd']) ? $predefs['ncmd'] : 'new_newsletter'),
				'we_cmd[0]' => 'show_newsletter',
				'nid' => (isset($predefs['nid']) ? $predefs['nid'] : $this->newsletter->ID),
				'pnt' => (isset($predefs['pnt']) ? $predefs['pnt'] : we_base_request::_(we_base_request::STRING, 'pnt')),
				'page' => (isset($predefs['page']) ? $predefs['page'] : $this->page),
				'gview' => (isset($predefs['gview']) ? $predefs['gview'] : 0),
				'hm' => (isset($predefs['hm']) ? $predefs['hm'] : 0),
				'ask' => (isset($predefs['ask']) ? $predefs['ask'] : 1),
				'test' => (isset($predefs['test']) ? $predefs['test'] : 0)
		]);
	}

	function newsletterHiddens(){
		$out = '';
		foreach($this->hiddens as $val){
			$out .= we_html_element::htmlHidden($val, (isset($this->newsletter->persistents[$val]) ? $this->newsletter->$val : $this->$val)
			);
		}

		return $out;
	}

	function getHiddensProperty(){
		$out = '';
		$counter = 0;
		$val = '';

		foreach($this->newsletter->groups as $group){

			foreach(array_keys($group->persistents) as $per){
				$val = $group->$per;
				$out .= we_html_element::htmlHidden('group' . $counter . '_' . $per, $val);
			}

			$counter++;
		}

		$out .= we_html_element::htmlHiddens(['groups' => $counter,
				'Step' => $this->newsletter->Step,
				'Offset' => $this->newsletter->Offset,
				'IsFolder' => $this->newsletter->IsFolder]
		);
		return $out;
	}

	function getHiddensPropertyPage(){
		return we_html_element::htmlHiddens(['Text' => $this->newsletter->Text,
				'Subject' => $this->newsletter->Subject,
				'ParentID' => $this->newsletter->ParentID,
				'Sender' => $this->newsletter->Sender,
				'Reply' => $this->newsletter->Reply,
				'Test' => $this->newsletter->Test,
				'Charset' => $this->newsletter->Charset,
				'isEmbedImages' => $this->newsletter->isEmbedImages
		]);
	}

	function getHiddensMailingPage(){
		$out = '';

		$fields_names = ['fieldname', 'operator', 'fieldvalue', 'logic', 'hours', 'minutes'];
		foreach($this->newsletter->groups as $g => $group){
			$filter = $group->getFilter();
			if($filter){
				$out .= we_html_element::htmlHidden('filter_' . $g, count($filter));

				foreach($filter as $k => $v){
					foreach($fields_names as $field){
						if(isset($v[$field])){
							$out .= we_html_element::htmlHidden('filter_' . $field . '_' . $g . '_' . $k, $v[$field]);
						}
					}
				}
			}
		}

		return $out;
	}

	function getHiddensContentPage(){
		$out = '';
		$counter = 0;

		foreach($this->newsletter->blocks as $bk => $bv){

			foreach(array_keys($this->newsletter->blocks[$bk]->persistents) as $per){
				$out .= we_html_element::htmlHidden('block' . $counter . '_' . $per, $bv->$per);
			}

			$counter++;
		}

		$out .= we_html_element::htmlHidden('blocks', $counter);

		return $out;
	}

	function getFields($id, $table){
		$ClassName = f('SELECT ClassName FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($id), 'ClassName', $this->db);
		$foo = [];

		if($ClassName){
			$ent = new $ClassName();
			$ent->initByID($id, $table);
			$tmp = array_keys($ent->elements);

			foreach($tmp as $v){
				$foo[$v] = $v;
			}
		}

		return $foo;
	}

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		return we_html_element::jsElement('
parent.document.title = "' . $title . '";
var frameSet="' . $this->frameset . '";
') . we_html_element::jsScript(WE_JS_MODULES_DIR . 'newsletter/newsletter_top.js');
	}

	function getJSProperty($load = ''){
		return
			we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . 'newsletter/newsletter_property.js', $load, ['loadVarNewsletter_property', 'data-nlView' => setDynamicVar([
					'checkMail' => !empty($this->settings['reject_save_malformed']),
					'uid' => (isset($this->uid) ? $this->uid : ""),
				])]
		);
	}

	function processCommands(we_base_jsCmd $jscmd){
		$ncmd = we_base_request::_(we_base_request::STRING, "ncmd");
		switch($ncmd){
			case "new_newsletter":
				$this->newsletter = new we_newsletter_newsletter();
				$this->newsletter->Text = g_l('modules_newsletter', '[new_newsletter]');
				$this->newsletter->Sender = $this->settings["default_sender"];
				$this->newsletter->Reply = $this->settings["default_reply"];
				$this->newsletter->Test = $this->settings["test_account"];
				$this->newsletter->isEmbedImages = $this->settings['isEmbedImages'];

				echo we_html_element::jsElement('
top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=edheader' . (($page = we_base_request::_(we_base_request::INT, "page")) !== false ? "&page=" . $page : "") . '";
top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=edfooter";
');
				break;
			case "new_newsletter_group":
				$this->page = 0;
				$this->newsletter = new we_newsletter_newsletter();
				$this->newsletter->IsFolder = "1";
				$this->newsletter->Text = g_l('modules_newsletter', '[new_newsletter_group]');
				echo we_html_element::jsElement('
top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=edheader&group=1";
top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=edfooter&group=1";
');
				break;
			case "add_customer":
				$ngroup = we_base_request::_(we_base_request::STRING, 'ngroup');
				if($ngroup !== false){
					$arr = makeArrayFromCSV($this->newsletter->groups[$ngroup]->Customers);
					if(($ncust = we_base_request::_(we_base_request::STRING, "ncustomer") ) !== false){

						$ids = makeArrayFromCSV($ncust);
						foreach($ids as $id){
							if($id && (!in_array($id, $arr))){
								$arr[] = $id;
							}
						}

						$this->newsletter->groups[$ngroup]->Customers = implode(',', $arr);
					}
				}
				break;

			case "del_customer":
				$arr = [];
				$ngroup = we_base_request::_(we_base_request::STRING, 'ngroup');
				if($ngroup !== false){
					$arr = makeArrayFromCSV($this->newsletter->groups[$ngroup]->Customers);

					if(($ncust = we_base_request::_(we_base_request::STRING, "ncustomer") ) !== false){
						foreach($arr as $k => $v){
							if($v == $ncust){
								unset($arr[$k]);
							}
						}
						$this->newsletter->groups[$ngroup]->Customers = implode(',', $arr);
					}
				}
				break;

			case "add_file":
				$arr = [];
				if(($ngroup = we_base_request::_(we_base_request::STRING, 'ngroup')) !== false){
					$arr = explode(',', $this->newsletter->groups[$ngroup]->Extern);
					if(($nfile = we_base_request::_(we_base_request::FILE, "nfile")) !== false){
						$sd = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
						$arr[] = str_replace($sd, (substr($sd, -1) === '/' ? '/' : ''), $nfile);
						$this->newsletter->groups[$ngroup]->Extern = implode(',', $arr);
					}
				}
				break;

			case "del_file":
				$arr = [];
				if(($ngroup = we_base_request::_(we_base_request::STRING, 'ngroup')) !== false){
					$arr = explode(',', $this->newsletter->groups[$ngroup]->Extern);
					if(($nfile = we_base_request::_(we_base_request::FILE, "nfile")) !== false){
						if(($pos = array_search($nfile, $arr, false)) !== false){
							unset($arr[$pos]);
						}
						$this->newsletter->groups[$ngroup]->Extern = implode(',', $arr);
					}
				}
				break;
			case "del_all_files":
				if(($ngroup = we_base_request::_(we_base_request::STRING, 'ngroup')) !== false){
					$this->newsletter->groups[$ngroup]->Extern = '';
				}
				break;
			case "del_all_customers":
				if(($ngroup = we_base_request::_(we_base_request::STRING, 'ngroup')) !== false){
					$this->newsletter->groups[$ngroup]->Customers = '';
				}
				break;

			case "reload":
				echo we_html_element::jsElement('
top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=edheader&page=' . $this->page . '&txt=' . urlencode($this->newsletter->Text) . ($this->newsletter->IsFolder ? '&group=1' : '') . '";
top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=edfooter' . ($this->newsletter->IsFolder ? '&group=1' : '') . '";
');
				break;

			case "newsletter_edit":
				if(($nid = we_base_request::_(we_base_request::INT, 'nid'))){
					$this->newsletter = new we_newsletter_newsletter($nid);
				}
				if($this->newsletter->IsFolder){
					$this->page = 0;
				}
				$_REQUEST["ncmd"] = "reload";
				$this->processCommands($jscmd);
				break;

			case 'switchPage':
				$this->page = we_base_request::_(we_base_request::INT, "page", $this->page);
				break;

			case 'save_newsletter':
				$nid = we_base_request::_(we_base_request::INT, 'nid');
				if($nid === false){
					break;
				}
				$weAcQuery = new we_selector_query();
				$newone = false;

				if($this->newsletter->filenameNotValid()){
					$jscmd->addMsg(g_l('modules_newsletter', '[we_filename_notValid]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}

				if($this->newsletter->ParentID > 0){
					$weAcResult = $weAcQuery->getItemById($this->newsletter->ParentID, NEWSLETTER_TABLE, ["IsFolder"], false);
					if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
						$jscmd->addMsg(g_l('modules_newsletter', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR);
						return;
					}
				}

				if(isset($_REQUEST['blocks'])){
					for($i = 0; $i < $_REQUEST['blocks']; $i++){
						switch(we_base_request::_(we_base_request::INT, 'block' . $i . '_Type')){
							case we_newsletter_block::DOCUMENT:
							case we_newsletter_block::DOCUMENT_FIELD:
								$acTable = FILE_TABLE;
								$acErrorField = g_l('modules_newsletter', '[block_document]');
								break;
							case we_newsletter_block::OBJECT:
							case we_newsletter_block::OBJECT_FIELD:
								$acTable = OBJECT_FILES_TABLE;
								$acErrorField = g_l('modules_newsletter', '[block_object]');
								break;
							default:
								$acTable = '';
								$acErrorField = '';
						}
						if($acTable){
							$weAcResult = $weAcQuery->getItemById(we_base_request::_(we_base_request::INT, 'block' . $i . '_LinkID'), $acTable, ['IsFolder']);

							if(!is_array($weAcResult) || empty($weAcResult) || $weAcResult[0]['IsFolder'] == 1){
								$jscmd->addMsg(sprintf(g_l('modules_newsletter', '[blockFieldError]'), ($i + 1), $acErrorField), we_message_reporting::WE_MESSAGE_ERROR);
								return;
							}
							if(($field = we_base_request::_(we_base_request::INT, 'block' . $i . '_Field'))){
								$weAcResult = $weAcQuery->getItemById($field, TEMPLATES_TABLE, ["IsFolder"]);
								if(!is_array($weAcResult) || !$weAcResult || $weAcResult[0]['IsFolder'] == 1){
									$jscmd->addMsg(sprintf(g_l('modules_newsletter', '[blockFieldError]'), $i, g_l('modules_newsletter', '[block_template]')), we_message_reporting::WE_MESSAGE_ERROR);
									return;
								}
							}
						}
					}
				}

				if(!$this->newsletter->ID){
					$newone = true;
				}

				if(!$newone && we_base_request::_(we_base_request::BOOL, 'ask')){
					$h = getHash('SELECT Step,Offset FROM ' . NEWSLETTER_TABLE . ' WHERE ID=' . intval($this->newsletter->ID), $this->db);

					if($h['Step'] != 0 || $h['Offset'] != 0){
						$jscmd->addCmd("save_newsletter_question");
						break;
					}
				}

				if(!$this->newsletter->Sender){
					$this->newsletter->Sender = $this->settings["default_sender"];
				}

				if(!$this->newsletter->Reply){
					$this->newsletter->Reply = $this->settings["default_reply"];
				}

				if(!$this->newsletter->Test){
					$this->newsletter->Test = $this->settings["test_account"];
				}
				if(!$this->newsletter->isEmbedImages){
					$this->newsletter->isEmbedImages = $this->settings["isEmbedImages"];
				}

				$double = intval(f('SELECT COUNT(1) FROM ' . NEWSLETTER_TABLE . ' WHERE Path="' . $this->db->escape($this->newsletter->Path) . '"' . ($newone ? '' : ' AND ID<>' . $this->newsletter->ID), '', $this->db));

				if(!permissionhandler::hasPerm("EDIT_NEWSLETTER") && !permissionhandler::hasPerm("NEW_NEWSLETTER")){
					$jscmd->addMsg(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}
				if($newone && !permissionhandler::hasPerm("NEW_NEWSLETTER")){
					$jscmd->addMsg(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}
				if(!$newone && !permissionhandler::hasPerm("EDIT_NEWSLETTER")){
					$jscmd->addMsg(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}

				if($double){
					$jscmd->addMsg(g_l('modules_newsletter', '[double_name]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}

				$message = "";

				$ret = $this->newsletter->saveNewsletter($message, (isset($this->settings["reject_save_malformed"]) ? $this->settings["reject_save_malformed"] : true));
				switch($ret){
					default:
						$jscmd->addMsg(sprintf(g_l('modules_newsletter', '[malformed_mail_group]'), $ret, $message), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case we_newsletter_newsletter::MALFORMED_SENDER:
						$jscmd->addMsg(sprintf(g_l('modules_newsletter', '[malformed_mail_sender]'), $message), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case we_newsletter_newsletter::MALFORMED_REPLY:
						$jscmd->addMsg(sprintf(g_l('modules_newsletter', '[malformed_mail_reply]'), $message), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case we_newsletter_newsletter::MALFORMED_TEST:
						$jscmd->addMsg(sprintf(g_l('modules_newsletter', '[malformed_mail_test]'), $message), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case we_newsletter_newsletter::SAVE_PATH_NOK:
						$jscmd->addMsg($message, we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case 0:
						if($newone){
							$jscmd->addCmd('makeTreeEntry', [
								'id' => $this->newsletter->ID,
								'parentid' => $this->newsletter->ParentID,
								'text' => $this->newsletter->Text,
								'open' => false,
								'contenttype' => ($this->newsletter->IsFolder ? we_base_ContentTypes::FOLDER : 'we/newsletter'),
								'table' => NEWSLETTER_TABLE
							]);
						} else {
							$jscmd->addCmd('updateTreeEntry', [
								'id' => $this->newsletter->ID,
								'parentid' => $this->newsletter->ParentID,
								'text' => $this->newsletter->Text,
							]);
						}
						$jscmd->addCmd('drawTree');

						echo we_html_element::jsElement('top.content.hot=false;');
						$jscmd->addMsg(g_l('modules_newsletter', ($this->newsletter->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE);
						break;
				}

				break;

			case "delete_newsletter":
				$nid = we_base_request::_(we_base_request::INT, "nid");
				if($nid !== false){
					if(!$nid){
						$jscmd->addMsg(g_l('modules_newsletter', '[delete_nok]'), we_message_reporting::WE_MESSAGE_ERROR);
						return;
					}
					if(!permissionhandler::hasPerm("DELETE_NEWSLETTER")){
						$jscmd->addMsg(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
						return;
					} else {
						$this->newsletter = new we_newsletter_newsletter($nid);

						if($this->newsletter->delete()){
							$this->newsletter = new we_newsletter_newsletter();
							$jscmd->addCmd('deleteTreeEntry', [$nid, "file"]);
							$jscmd->addMsg(g_l('modules_newsletter', (we_base_request::_(we_base_request::BOOL, "IsFolder") ? '[delete_group_ok]' : '[delete_ok]')), we_message_reporting::WE_MESSAGE_NOTICE);
							$_REQUEST['home'] = 1;
							$_REQUEST['pnt'] = 'edbody';
						} else {
							$jscmd->addMsg(g_l('modules_newsletter', (we_base_request::_(we_base_request::BOOL, "IsFolder") ? '[delete_group_nok]' : '[delete_nok]')), we_message_reporting::WE_MESSAGE_ERROR);
						}
					}
				} else {
					$jscmd->addMsg(g_l('modules_newsletter', (we_base_request::_(we_base_request::BOOL, "IsFolder") ? '[delete_group_nok]' : '[delete_nok]')), we_message_reporting::WE_MESSAGE_ERROR);
				}
				break;

			case "reload_table":
				$this->page = 1;
				break;

			case "set_import":
				$this->show_import_box = we_base_request::_(we_base_request::STRING, "ngroup");
				break;

			case "set_export":
				$this->show_export_box = we_base_request::_(we_base_request::STRING, "ngroup");
				break;

			case "reset_import":
				$this->show_import_box = -1;
				break;

			case "reset_export":
				$this->show_export_box = -1;
				break;

			case "addBlock":
				if(($bid = we_base_request::_(we_base_request::INT, "blockid")) !== false){
					$this->newsletter->addBlock($bid + 1);
				}
				break;

			case "delBlock":
				if(($bid = we_base_request::_(we_base_request::INT, "blockid")) !== false){
					$this->newsletter->removeBlock($bid);
				}
				break;

			case "addGroup":
				$this->newsletter->addGroup();
				echo we_html_element::jsElement('
var edf=top.content.editor.edfooter;
edf.document.we_form.gview.length = 0;
edf.populateGroups();');
				break;

			case "delGroup":
				if(($ngroup = we_base_request::_(we_base_request::STRING, "ngroup")) !== false){
					$this->newsletter->removeGroup($ngroup);
					echo we_html_element::jsElement('
var edf=top.content.editor.edfooter;
edf.document.we_form.gview.length = 0;
edf.populateGroups();');
				}
				break;

			case "send_test":
				if(!permissionhandler::hasPerm("SEND_TEST_EMAIL")){
					$jscmd->addMsg(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}
				$this->sendTestMail(we_base_request::_(we_base_request::INT, "gview", 0), we_base_request::_(we_base_request::BOOL, "hm"));
				$jscmd->addMsg(sprintf(g_l('modules_newsletter', '[test_mail_sent]'), $this->newsletter->Test), we_message_reporting::WE_MESSAGE_NOTICE);
				break;

			case "add_filter":
				$this->newsletter->groups[we_base_request::_(we_base_request::STRING, "ngroup")]->addFilter($this->customers_fields[0]);
				break;

			case "del_filter":
				$this->newsletter->groups[we_base_request::_(we_base_request::STRING, "ngroup")]->delFilter();
				break;

			case "del_all_filters":
				$this->newsletter->groups[we_base_request::_(we_base_request::STRING, "ngroup")]->delallFilter();
				break;

			case "copy_newsletter":
				if(($cid = we_base_request::_(we_base_request::INT, "copyid"))){
					$id = $this->newsletter->ID;
					$this->newsletter = new we_newsletter_newsletter($cid);
					$this->newsletter->ID = $id;
					$this->newsletter->Text .= "_" . g_l('modules_newsletter', '[copy]');
				}
				break;

			case 'save_settings':
				foreach($this->settings as $k => $v){
					$this->settings[$k] = we_base_request::_(we_base_request::RAW, $k, 0);
				}
				$this->saveSettings();
				break;

			case 'import_csv':
				if(($importno = we_base_request::_(we_base_request::INT, 'csv_import')) !== false){
					$filepath = we_base_request::_(we_base_request::FILE, 'csv_file' . $importno);
					$delimiter = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_delimiter' . $importno);
					$col = max(0, we_base_request::_(we_base_request::INT, 'csv_col' . $importno, 1) - 1);

					$imports = ['hmcol' => [],
						'salutationcol' => [],
						'titlecol' => [],
						'firstnamecol' => [],
						'lastnamecol' => [],
					];
					foreach($imports as $key => &$vals){
						$vals['val'] = we_base_request::_(we_base_request::INT, 'csv_' . $key . $importno, 0);
						$vals['import'] = ($vals['val'] > 0);
						if($vals['val']){
							$vals['val'] --;
						}
					}

					if(strpos($filepath, '..') !== false){
						$jscmd->addMsg(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR);
					} else {
						$row = [];
						$control = [];
						$fh = @fopen($_SERVER['DOCUMENT_ROOT'] . $filepath, 'rb');
						$mailListArray = [];
						if($fh){
							$mailListArray = explode("\n", $this->newsletter->groups[$importno]->Emails);
							foreach($mailListArray as $line){
								$mailListArray[] = substr($line, 0, strpos($line, ','));
							}
							unset($mailListArray);
							while($dat = fgetcsv($fh, 1000, $delimiter)){
								if(!isset($control[$dat[$col]])){
									$alldat = implode('', $dat);
									if(str_replace(' ', '', $alldat) === ""){
										continue;
									}
									$mailrecip = (str_replace(' ', '', $dat[$col]) === '') ? '--- ' . g_l('modules_newsletter', '[email_missing]') . ' ---' : $dat[$col];
									if(!empty($mailrecip) && !in_array($mailrecip, $mailListArray)){
										$row[] = $mailrecip . ',' .
											( ($imports['hmcol']['import'] && isset($dat[$imports['hmcol']['val']])) ? $dat[$imports['hmcol']['val']] : '') . "," .
											( ($imports['salutationcol']['import'] && isset($dat[$imports['salutationcol']['val']])) ? $dat[$imports['salutationcol']['val']] : "") . "," .
											( ($imports['titlecol']['import'] && isset($dat[$imports['titlecol']['val']])) ? $dat[$imports['titlecol']['val']] : "") . "," .
											( ($imports['firstnamecol']['import'] && isset($dat[$imports['firstnamecol']['val']])) ? $dat[$imports['firstnamecol']['val']] : "") . "," .
											( ($imports['lastnamecol']['import'] && isset($dat[$imports['lastnamecol']['val']])) ? $dat[$imports['lastnamecol']['val']] : "");
										$control[$dat[$col]] = 1;
									}
								}
							}
							fclose($fh);
							$this->newsletter->groups[$importno]->Emails .= ($this->newsletter->groups[$importno]->Emails ? "\n" : '') . implode("\n", $row);
						} else {
							$jscmd->addMsg(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR);
						}
					}
				}
				break;

			case "export_csv":
				if(($exportno = we_base_request::_(we_base_request::INT, "csv_export")) !== false){
					$fname = rtrim(we_base_request::_(we_base_request::FILE, "csv_dir" . $exportno), '/') . "/emails_export_" . time() . ".csv";

					we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $fname, $this->newsletter->groups[$exportno]->Emails);
					echo we_html_element::jsElement('new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=export_csv_mes&lnk=' . $fname . '","edit_email",-1,-1,440,250,true,true,true,true);');
				}
				break;

			case "save_black":
				$this->saveSetting("black_list", $this->settings["black_list"]);
				$jscmd->addCmd('close');
				break;

			case "do_upload_csv":
			case "do_upload_black":
				$group = we_base_request::_(we_base_request::INT, "group", 0);

				//set header we avoided when sending JSON only
				we_html_tools::headerCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']);
				echo we_html_tools::getHtmlTop('newsletter');

				$tempName = '';
				$fileUploader = new we_fileupload_resp_base();
				//$fileUploader->setTypeCondition();
				if(!($tempName = $fileUploader->commitUploadedFile())){
					//some reaction on upload failure
				}

				//print next command
				echo we_html_element::jsElement($ncmd === 'do_upload_csv' ? '
opener.document.we_form.csv_file' . $group . '.value="' . $tempName . '";
opener.we_cmd("import_csv");
self.close();' : '
opener.document.we_form.csv_file.value="' . $tempName . '";
opener.document.we_form.sib.value=0;
opener.we_cmd("import_black");
self.close();');
				break;

			case "save_email_file":
				$csv_file = we_base_request::_(we_base_request::FILE, "csv_file", '');
				$nrid = we_base_request::_(we_base_request::INT, "nrid", '');
				$email = we_base_request::_(we_base_request::EMAIL, "email", '');
				$htmlmail = we_base_request::_(we_base_request::BOOL, "htmlmail", '');
				$salutation = we_base_request::_(we_base_request::STRING, "salutation", '');
				$title = we_base_request::_(we_base_request::STRING, "title", '');
				$firstname = we_base_request::_(we_base_request::STRING, "firstname", '');
				$lastname = we_base_request::_(we_base_request::STRING, "lastname", '');

				$emails = ($csv_file ? we_newsletter_newsletter::getEmailsFromExtern($csv_file) : []);

				$emails[$nrid] = [$email, $htmlmail, $salutation, $title, $firstname, $lastname];
				$emails_out = "";
				foreach($emails as $email){
					$emails_out .= implode(',', array_slice($email, 0, 6)) . "\n";
				}

				if($csv_file){
					we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $csv_file, $emails_out);
				}

				break;

			case "delete_email_file":
				$nrid = we_base_request::_(we_base_request::INT, "nrid", false);
				$csv_file = we_base_request::_(we_base_request::FILE, "csv_file", '');
				$emails = ($csv_file ? we_newsletter_newsletter::getEmailsFromExtern($csv_file, 2) : []);

				if($nrid !== false){
					unset($emails[$nrid]);
					$emails_out = '';
					foreach($emails as $email){
						$emails_out .= implode(',', $email) . "\n";
					}

					if($csv_file){
						we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $csv_file, $emails_out);
					}
				}
				break;
			case "popSend":
				$jscmd->addCmd("popSend_do_cont", [$this->newsletter->ID, empty(trim($this->newsletter->Subject)), we_base_request::_(we_base_request::BOOL, "test")]);
				break;
			default:
		}
	}

	private function processVariablesGroups($groups){
		$this->newsletter->groups = [];

		if($groups == 0){
			$this->newsletter->addGroup();
		}

		for($i = 0; $i < $groups; $i++){
			$this->newsletter->addGroup();
		}

		$fields_names = ['fieldname', 'operator', 'fieldvalue', 'logic', 'hours', 'minutes'];

		foreach($this->newsletter->groups as $gkey => &$gval){
			// persistens
			$gval->NewsletterID = $this->newsletter->ID;

			foreach($gval->persistents as $per => $type){
				$varname = 'group' . $gkey . '_' . $per;
				$gval->$per = we_base_request::_($type, $varname, $gval->$per);
			}

			// Filter
			$count = (isset($_REQUEST['filter_' . $gkey]) ? $_REQUEST['filter_' . $gkey] ++ : 0);
			if($count){
				for($i = 0; $i < $count; $i++){
					$new = [];

					foreach($fields_names as $field){
						$varname = 'filter_' . $field . '_' . $gkey . '_' . $i;

						if(($tmp = we_base_request::_(we_base_request::RAW_CHECKED, $varname)) !== false){
							$new[$field] = $tmp;
						}
					}

					if($new){
						$gval->appendFilter($new);
						$gval->preserveFilter();
					}
				}
			}
		}
		unset($gval);
	}

	private function processVariablesBlocks($blocks){
		$this->newsletter->blocks = [];

		if($blocks == 0){
			$this->newsletter->addBlock();
		}

		for($i = 0; $i < $blocks; $i++){
			$this->newsletter->addBlock();
		}

		foreach($this->newsletter->blocks as $skey => &$sval){
			$sval->NewsletterID = $this->newsletter->ID;

			foreach($sval->persistents as $per => $type){
				$varname = 'block' . $skey . '_' . $per;
				$sval->$per = we_base_request::_($type, $varname, $sval->$per);
			}
		}
	}

	function processVariables(){
		if(($uid = we_base_request::_(we_base_request::STRING, 'wname'))){
			$this->uid = $uid;
		}

		foreach($this->newsletter->persistents as $val => $type){
			$this->newsletter->$val = we_base_request::_($type, $val, $this->newsletter->$val);
		}

		if($this->newsletter->ParentID){
			$this->newsletter->Path = f('SELECT Path FROM ' . NEWSLETTER_TABLE . ' WHERE ID=' . $this->newsletter->ParentID, '', $this->db) . '/' . $this->newsletter->Text;
		} elseif(!$this->newsletter->filenameNotValid($this->newsletter->Text)){
			$this->newsletter->Path = '/' . $this->newsletter->Text;
		}

		$this->page = we_base_request::_(we_base_request::INT, 'page', $this->page);
		if(($groups = we_base_request::_(we_base_request::INT, 'groups')) !== false){
			$this->processVariablesGroups($groups);
		}
		if(($blocks = we_base_request::_(we_base_request::INT, 'blocks')) !== false){
			$this->processVariablesBlocks($blocks);
		}
	}

	function getTime($seconds){
		$min = floor($seconds / 60);
		$ret = ["hour" => floor($min / 60),
			"min" => $min,
			"sec" => $seconds - ($min * 60)
		];
		$ret["min"] -= ($ret["hour"] * 60);
		return $ret;
	}

	/**
	 * Newsletter printing functions
	 */
	private function initDocByObject($we_objectID){
		$we_obj = new we_objectFile();
		$we_obj->initByID($we_objectID, OBJECT_FILES_TABLE);

		$we_doc = $this->initDoc();
		$we_doc->initByObj($we_obj);
		return $we_doc;
	}

	private function initDoc($id = 0){
		$we_doc = new we_webEditionDocument();

		if($id){
			$we_doc->initByID($id);
		}
		return $we_doc;
	}

	function we_includeEntity(&$we_doc, $tmpid){//FIXME: unused
		if($tmpid != "" && $tmpid != 0){
			$path = id_to_path($tmpid, TEMPLATES_TABLE);
		}

		$path = ($path ? TEMPLATES_PATH . $path : $we_doc->TemplatePath);

		if(file_exists($path)){
			include($path);
		} else {
			echo STYLESHEET .
			'<div class="defaultfont lowContrast" style="text-align:center">' . g_l('modules_newsletter', '[cannot_preview]') . '</div>';
		}
	}

	function getContent($pblk = 0, $gview = 0, $hm = 0, $salutation = '', $title = '', $firstname = '', $lastname = '', $customerid = 0){
		if(!isset($this->newsletter->blocks[$pblk])){
			return '';
		}
		$block = $this->newsletter->blocks[$pblk];
		$groups = makeArrayFromCSV($block->Groups);
		if(!(in_array($gview, $groups) || $gview == 0)){
			return '';
		}

		$content = $GLOBALS['we_doc'] = '';

		$GLOBALS['WE_MAIL'] = we_newsletter_base::EMAIL_REPLACE_TEXT;
		$GLOBALS['WE_HTMLMAIL'] = $hm;
		$GLOBALS['WE_TITLE'] = $title;
		$GLOBALS['WE_SALUTATION'] = $salutation;
		$GLOBALS['WE_FIRSTNAME'] = $firstname;
		$GLOBALS['WE_LASTNAME'] = $lastname;
		$GLOBALS['WE_CUSTOMERID'] = $customerid;
		$patterns = [];

		switch($block->Type){
			case we_newsletter_block::DOCUMENT:
				if($block->Field != "" && $block->Field != 0){
					$path = TEMPLATES_PATH . preg_replace('/\.tmpl$/i', '.php', id_to_path($block->Field, TEMPLATES_TABLE));
				} else if($block->LinkID){
					$p = f('SELECT t.Path FROM ' . FILE_TABLE . ' f LEFT JOIN ' . TEMPLATES_TABLE . ' t ON f.TemplateID=t.ID WHERE f.ID=' . intval($block->LinkID), "", $this->db);
					$path = TEMPLATES_PATH . preg_replace('/\.tmpl$/i', '.php', $p);
				} else {
					$path = "";
				}
				if($block->LinkID && $path){
					$content = ($block->LinkID > 0) && we_base_file::isWeFile($block->LinkID, FILE_TABLE, $this->db) ? we_getDocumentByID($block->LinkID, $path, $this->db) : 'No such File';
				}
				break;
			case we_newsletter_block::DOCUMENT_FIELD:
				if($block->LinkID){
					$we_doc = $this->initDoc($block->LinkID);
					$content = $we_doc->getElement($block->Field);
				}
				break;
			case we_newsletter_block::OBJECT:
				$path = ($block->Field != "" && $block->Field ?
					TEMPLATES_PATH . preg_replace('/\.tmpl$/i', '.php', id_to_path($block->Field, TEMPLATES_TABLE)) : '');

				if($block->LinkID && $path){
					$content = self::we_getObjectFileByID($block->LinkID, $path);
				}

				break;
			case we_newsletter_block::OBJECT_FIELD:
				if($block->LinkID){
					$we_doc = $this->initDocByObject($block->LinkID);
					$content = $we_doc->getElement($block->Field);
				}
				break;
			case we_newsletter_block::TEXT:
				$blockHtml = $block->Html ? preg_replace(['/(href=")(\\\\*&quot;)*(.+?)(\\\\*&quot;)*(")/',
						'/(src=")(\\\\*&quot;)*(.+?)(\\\\*&quot;)*(")/'], '${1}${3}${5}', stripslashes($block->Html)) : '';

				if($hm){
					$content = $blockHtml ?
						$blockHtml :
						strtr($block->Source, ["\r\n" => '<br/>',
							"\r" => '<br/>',
							'&' => '&amp;',
							'<' => '&lt;',
							'>' => '&gt;',
							"\n" => '<br/>',
							"\t" => '&nbsp;&nbsp;&nbsp;',
					]);
					break;
				}
				$content = ($block->Source ?
					$block->Source :
					str_ireplace(['&nbsp;', '&lt;', "&gt;", "&quot;", "&amp;",], [' ', "<", ">", '"', "&",], preg_replace("|&nbsp;(&nbsp;)+|i", "\t", trim(strip_tags(preg_replace("|<br\s*/?\s*>|i", "\n", $blockHtml))))));
				//TODO: we should preserve img- and link-pathes: "text text linktext (path) text"

				break;
			case we_newsletter_block::FILE:
				$content = we_base_file::load($_SERVER['DOCUMENT_ROOT'] . $block->Field);
				if(!$content){
					echo g_l('modules_newsletter', '[cannot_open]') . ": " . $_SERVER['DOCUMENT_ROOT'] . $block->Field;
				}
				break;
			case we_newsletter_block::URL:
				if($block->Field){
					if(substr(trim($block->Field), 0, 4) != "http"){
						$block->Field = "http://" . $block->Field;
					}

					$url = parse_url($block->Field);
					$st = '';
					$content = getHTTP($url["host"], (isset($url["path"]) ? $url["path"] : '/'), $st, "", defined('HTTP_USERNAME') ? HTTP_USERNAME : "", defined('HTTP_PASSWORD') ? HTTP_PASSWORD : "");

					$trenner = '\s*';
					$patterns[] = "/<(img" . $trenner . "[^>]+src" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ? \\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie";
					$patterns[] = "/<(link" . $trenner . "[^>]+href" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ? \\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie";
					$match = [];

					foreach($patterns as $pattern){
						if(preg_match_all($pattern, $content, $match)){
							$unique = array_unique($match[2]);
							foreach($unique as $name){
								$src = parse_url($name);

								if(!isset($src["host"])){

									if(isset($src["path"])){
										$path = (dirname($src["path"]) ?
											dirname($src["path"]) . "/" :
											(isset($url["path"]) ?
											dirname($url["path"]) . "/" :
											''));
									}
									$newname = $url["scheme"] . "://" . preg_replace("|/+|", "/", $url["host"] . "/" . $path . basename($name));
									$content = str_replace($name, $newname, $content);
								}
							}
						}
					}
				}
				break;
			case we_newsletter_block::ATTACHMENT:
				break;
		}


		$port = (!empty($this->settings["use_port"])) ? ':' . $this->settings["use_port"] : '';
		$protocol = (!empty($this->settings["use_https_refer"]) ? 'https://' : 'http://');

		if($hm){
			if($block->Type != we_newsletter_block::URL){
				$spacer = '\s*';

				we_document::parseInternalLinks($content, 0);

				$urlReplace = we_folder::getUrlReplacements($this->db, false, true);
				if($urlReplace){
					$content = preg_replace('-(["\'])//-', '${1}' . $protocol, preg_replace($urlReplace, array_keys($urlReplace), $content));
				}
				$content = preg_replace(['-(<[^>]+src' . $spacer . '=' . $spacer . '[\'"]?)(/)-i',
					'-(<[^>]+href' . $spacer . '=' . $spacer . '[\'"]?)(/)-i',
					'-(<[^>]+background' . $spacer . '=' . $spacer . '[\'"]?)(/)-i',
					'-(background' . $spacer . ':' . $spacer . '[^url]*url' . $spacer . '\\([\'"]?)(/)-i',
					'+(background-image' . $spacer . ':' . $spacer . '[^url]*url' . $spacer . '\\([\'"]?)(/)+i',
					], ['${1}' . $protocol . $_SERVER['SERVER_NAME'] . $port . '${2}',
					'${1}' . $protocol . $_SERVER['SERVER_NAME'] . $port . '${2}',
					'${1}' . $protocol . $_SERVER['SERVER_NAME'] . $port . '${2}',
					'${1}' . $protocol . $_SERVER['SERVER_NAME'] . $port . '${2}',
					'${1}' . $protocol . $_SERVER['SERVER_NAME'] . $port . '${2}',
					], $content);
			}
		} else {
			$urlReplace = we_folder::getUrlReplacements($this->db, true, true);
			if($urlReplace){
				$content = str_replace('//', $protocol, preg_replace($urlReplace, array_keys($urlReplace), $content));
			}
			$newplain = preg_replace(['|<br */? *>|', '|<title>.*</title>|i',], "\n", $content);
			if($block->Type != we_newsletter_block::TEXT){
				$newplain = strip_tags($newplain);
			}
			$newplain = preg_replace("|&nbsp;(&nbsp;)+|i", "\t", $newplain);
			$content = $newplain = str_ireplace(['&nbsp;', '&lt;', '&gt;', '&quot;', '&amp;',], [' ', '<', '>', '"', '&'], $newplain);
		}

		return $content;
	}

	function getBlockContents(){
		$content = [];
		$keys = array_keys($this->newsletter->blocks);
		foreach($keys as $kblock){
			$blockid = $kblock + 1;

			$content[] = ['plain' => ['defaultC' => $this->getContent($blockid, 0, 0, '', '', '', '', '###CUSTOMERID###'),
					'femaleC' => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'maleC' => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'title_firstname_lastnameC' => $this->getContent($blockid, 0, 0, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'title_lastnameC' => $this->getContent($blockid, 0, 0, '', '###TITLE###', '', '###LASTNAME###', '###CUSTOMERID###'),
					'firstname_lastnameC' => $this->getContent($blockid, 0, 0, '', '', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'firstnameC' => $this->getContent($blockid, 0, 0, '', '', '###FIRSTNAME###', '', '###CUSTOMERID###'),
					'lastnameC' => $this->getContent($blockid, 0, 0, '', '', '', '###LASTNAME###', '###CUSTOMERID###'),
					'default' => $this->getContent($blockid, 0, 0, '', '', '', '', ''),
					'female' => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'male' => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'title_firstname_lastname' => $this->getContent($blockid, 0, 0, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'title_lastname' => $this->getContent($blockid, 0, 0, '', '###TITLE###', '', '###LASTNAME###', ''),
					'firstname_lastname' => $this->getContent($blockid, 0, 0, '', '', '###FIRSTNAME###', '###LASTNAME###', ''),
					'firstname' => $this->getContent($blockid, 0, 0, '', '', '###FIRSTNAME###', '', ''),
					'lastname' => $this->getContent($blockid, 0, 0, '', '', '', '###LASTNAME###', ''),
				],
				'html' => ['defaultC' => $this->getContent($blockid, 0, 1, '', '', '', '', '###CUSTOMERID###'),
					'femaleC' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'maleC' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'title_firstname_lastnameC' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '', '###LASTNAME###', '###CUSTOMERID###'),
					'title_lastnameC' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'firstname_lastnameC' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'firstnameC' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '', '###CUSTOMERID###'),
					'lastnameC' => $this->getContent($blockid, 0, 1, '', '', '', '###LASTNAME###', '###CUSTOMERID###'),
					'default' => $this->getContent($blockid, 0, 1, '', '', '', '', ''),
					'female' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'male' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'title_firstname_lastname' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '', '###LASTNAME###', ''),
					'title_lastname' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'firstname_lastname' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '###LASTNAME###', ''),
					'firstname' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '', ''),
					'lastname' => $this->getContent($blockid, 0, 1, '', '', '', '###LASTNAME###', ''),
			]];
		}
		return $content;
	}

	function getGroupBlocks($group){
		$content = [];
		$count = count($this->newsletter->blocks);
		if($group == 0){
			for($i = 0; $i < $count; $i++){
				$content[] = $i;
			}
		} else {
			foreach($this->newsletter->blocks as $kblock => $block){
				if(in_array($group, $block->GroupsA)){
					$content[] = $kblock;
				}
			}
		}
		return $content;
	}

	function getGroupsForEmail($email){
		$ret = [];

		if(is_array($this->newsletter->groups)){
			$keys = array_keys($this->newsletter->groups);
			foreach($keys as $gk){
				$emails = $this->getEmails($gk + 1, self::MAILS_ALL, 1);

				if(in_array($email, $emails)){
					$ret[] = $gk + 1;
				}
			}
		}

		return $ret;
	}

	function getAttachments($group){
		$atts = [];
		$dbtmp = new DB_WE();
		$this->db->query('SELECT LinkID FROM ' . NEWSLETTER_BLOCK_TABLE . ' WHERE NewsletterID=' . $this->newsletter->ID . ' AND Type=' . we_newsletter_block::ATTACHMENT . ($group ? ' AND FIND_IN_SET("' . $this->db->escape($group) . '",Groups)' : ''));

		while($this->db->next_record()){
			if($this->db->f('LinkID')){
				$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . $this->db->f('LinkID'), '', $dbtmp);

				if($path){
					$atts[] = $_SERVER['DOCUMENT_ROOT'] . $path;
				}
			}
		}
		return $atts;
	}

	function sendTestMail($group, $hm){
		$plain = "";
		$content = "";
		$inlines = [];

		$ret = $this->cacheNewsletter($this->newsletter->ID, false);
		$blocks = $this->getGroupBlocks($group);
		foreach($blocks as $i){
			if($hm){
				$block = we_newsletter_newsletter::getFromCache($ret["blockcache"] . "_h_" . $i);
				$inlines = array_merge($inlines, $block["inlines"]);
				$content .= $block["default"];
				$block = we_newsletter_newsletter::getFromCache($ret["blockcache"] . "_p_" . $i);
				$plain .= $block["default"];
			} else {
				$block = we_newsletter_newsletter::getFromCache($ret["blockcache"] . "_p_" . $i);
				$content .= $block["default"];
				$plain .= $block["default"];
			}
		}

		$atts = $this->getAttachments($group);
		//$clean = $this->getCleanMail($this->newsletter->Reply);
		$phpmail = new we_mail_mail($this->newsletter->Test, $this->newsletter->Subject, $this->newsletter->Sender, $this->newsletter->Reply, $this->newsletter->isEmbedImages);
		if(!$this->settings["use_base_href"]){
			$phpmail->setIsUseBaseHref($this->settings["use_base_href"]);
		}
		$phpmail->setCharSet($this->newsletter->Charset ?: $GLOBALS['WE_BACKENDCHARSET']);
		if($hm){
			$phpmail->addHTMLPart($content);
		}
		$phpmail->addTextPart(trim($plain));
		foreach($atts as $att){
			$phpmail->doaddAttachment($att);
		}
		$phpmail->buildMessage();
		$phpmail->Send();

		$cc = 0;
		while(true){
			if(file_exists(WE_CACHE_DIR . 'nl_' . $ret["blockcache"] . "_p_" . $cc)){
				we_base_file::delete(WE_CACHE_DIR . 'nl_' . $ret["blockcache"] . "_p_" . $cc);
			} else {
				break;
			}

			if(file_exists(WE_CACHE_DIR . 'nl_' . $ret["blockcache"] . "_h_" . $cc)){
				$buffer = we_newsletter_newsletter::getFromCache(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_h_" . $cc);
				if(isset($buffer['inlines'])){
					foreach($buffer['inlines'] as $fn){
						if(file_exists($fn)){
							we_base_file::delete($fn);
						}
					}
				}
				we_base_file::delete(WE_CACHE_DIR . 'nl_' . $ret["blockcache"] . "_h_" . $cc);
			} else {
				break;
			}
			$cc++;
		}
		foreach($inlines as $ins){
			we_base_file::delete($ins);
		}
	}

	function getFilterSQL($filter){
		$filterSQL = $filter["fieldname"];
		switch($filter["fieldname"]){
			case 'MemberSince':
			case 'LastLogin':
			case 'LastAccess':
				if(stristr($filter['fieldvalue'], '.')){
					$date = explode(".", $filter['fieldvalue']);
					$day = $date[0];
					$month = $date[1];
					$year = $date[2];
					$hour = $filter['hours'];
					$minute = $filter['minutes'];
					$filter['fieldvalue'] = mktime($hour, $minute, 0, $month, $day, $year);
				}
		}

		switch($filter["operator"]){
			case we_newsletter_newsletter::OP_EQ:
				return $filterSQL . ' = "' . $filter["fieldvalue"] . '"';
			case we_newsletter_newsletter::OP_NEQ:
				return $filterSQL . ' != "' . $filter["fieldvalue"] . '"';
			case we_newsletter_newsletter::OP_LE:
				return $filterSQL . ' < "' . $filter["fieldvalue"] . '"';
			case we_newsletter_newsletter::OP_LEQ:
				return $filterSQL . ' <= "' . $filter["fieldvalue"] . '"';
			case we_newsletter_newsletter::OP_GE:
				return $filterSQL . ' > "' . $filter["fieldvalue"] . '"';
			case we_newsletter_newsletter::OP_GEQ:
				return $filterSQL . ' >= "' . $filter["fieldvalue"] . '"';
			case we_newsletter_newsletter::OP_LIKE:
				return $filterSQL . ' LIKE "' . $filter["fieldvalue"] . '"';
			case we_newsletter_newsletter::OP_CONTAINS:
				return $filterSQL . ' LIKE "%' . $filter["fieldvalue"] . '%"';
			case we_newsletter_newsletter::OP_STARTS:
				return $filterSQL . ' LIKE "' . $filter["fieldvalue"] . '%"';
			case we_newsletter_newsletter::OP_ENDS:
				return $filterSQL . ' LIKE "%' . $filter["fieldvalue"] . '"';
			default:
				return $filterSQL;
		}
	}

	function getEmails($group, $select = self::MAILS_ALL, $emails_only = 0){

		update_time_limit(0);
		update_mem_limit(128);

		$extern = ($select == self::MAILS_ALL || $select == self::MAILS_FILE) ? we_newsletter_base::getEmailsFromExtern($this->newsletter->groups[$group - 1]->Extern, $emails_only, $group, $this->getGroupBlocks($group)) : [];

		if($select == self::MAILS_FILE){
			return $extern;
		}

		$list = ($select == self::MAILS_ALL || $select == self::MAILS_EMAILS) ? we_newsletter_base::getEmailsFromList($this->newsletter->groups[$group - 1]->Emails, $emails_only, $group, $this->getGroupBlocks($group)) : [];
		if($select == self::MAILS_EMAILS){
			return $list;
		}

		$customer_mail = $customers = [];

		if(defined('CUSTOMER_TABLE')){
			$filterarr = [];
			$filtera = $this->newsletter->groups[$group - 1]->getFilter();
			if($filtera){
				foreach($filtera as $k => $filter){
					$filterarr[] = ($k ? (' ' . $filter['logic'] . ' ') : ' ') . $this->getFilterSQL($filter);
				}
			}

			$filtersql = implode(' ', $filterarr);

			$customers = ($this->newsletter->groups[$group - 1]->SendAll ?
				'SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE ' . ($filtersql !== '' ? $filtersql : 1) :
				implode(',', array_map('intval', explode(',', $this->newsletter->groups[$group - 1]->Customers))));

			if(!empty($customers) || !empty($filtersql)){ //Fix #10898
				$default_html = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="newsletter" AND pref_name="default_htmlmail"', '', $this->db);
				$selectX = $this->settings['customer_email_field'] .
					($emails_only ? '' :
					',' . $this->settings['customer_html_field'] . ',' .
					$this->settings['customer_salutation_field'] . ',' .
					$this->settings['customer_title_field'] . ',' .
					$this->settings['customer_firstname_field'] . ',' .
					$this->settings['customer_lastname_field']
					);
				$this->db->query('SELECT ID,' . $selectX . ' FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . $customers . ')' . ($filtersql ? ' AND (' . $filtersql . ')' : ''));
				while($this->db->next_record()){
					if($this->db->f($this->settings["customer_email_field"])){
						$email = trim($this->db->f($this->settings["customer_email_field"]));
						if($emails_only){
							$customer_mail[] = $email;
						} else {
							$htmlmail = ($this->settings["customer_html_field"] != 'ID' && trim($this->db->f($this->settings["customer_html_field"])) != '') ? trim($this->db->f($this->settings["customer_html_field"])) : $default_html;
							$salutation = $this->settings["customer_salutation_field"] != 'ID' ? $this->db->f($this->settings["customer_salutation_field"]) : '';
							$title = $this->settings["customer_title_field"] != 'ID' ? $this->db->f($this->settings["customer_title_field"]) : '';
							$firstname = $this->db->f($this->settings["customer_firstname_field"]);
							$lastname = $this->db->f($this->settings["customer_lastname_field"]);

							// damd: Parmeter $customer (Kunden ID in der Kundenverwaltung) und Flag dass es sich um Daten aus der Kundenverwaltung handelt angehängt
							$customer = $this->db->f('ID');
							$customer_mail[] = [$email, $htmlmail, $salutation, $title, $firstname, $lastname, $group, $this->getGroupBlocks($group), $customer, 'customer'];
						}
					}
				}
			}

			if($select == self::MAILS_CUSTOMER){
				return $customer_mail;
			}
		}
		return array_merge($customer_mail, $list, $extern);
	}

	function getEmailsNum(){
		$out = 0;
		$count = count($this->newsletter->groups);
		for($i = 0; $i < $count; $i++){
			$out += count($this->getEmails($i + 1, self::MAILS_ALL, 1));
		}
		return $out;
	}

	/**
	 * Static function - Settings
	 */
	static function getSettings(){
		$db = new DB_WE();
		$domainName = str_replace("www.", "", $_SERVER['SERVER_NAME']);
		$ret = ['black_list' => '',
			'customer_email_field' => 'Kontakt_Email',
			'customer_firstname_field' => 'Forename',
			'customer_html_field' => 'ID',
			'customer_lastname_field' => 'Surname',
			'customer_salutation_field' => '',
			'customer_title_field' => '',
			'default_htmlmail' => 0,
			'isEmbedImages' => 0,
			'default_reply' => 'replay@' . $domainName,
			'default_sender' => 'mailer@' . $domainName,
			we_newsletter_newsletter::FEMALE_SALUTATION_FIELD => g_l('modules_newsletter', '[default][female]'),
			'global_mailing_list' => '',
			'log_sending' => 1,
			we_newsletter_newsletter::MALE_SALUTATION_FIELD => g_l('modules_newsletter', '[default][male]'),
			'reject_malformed' => 1,
			'reject_not_verified' => 1,
			'send_step' => 20,
			'send_wait' => 0,
			'test_account' => 'test@' . $domainName,
			'title_or_salutation' => 0,
			'use_port' => 0,
			'use_https_refer' => 0,
			'use_base_href' => 1
		];

		$db->query('SELECT pref_name,pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="newsletter"');
		while($db->next_record()){
			$ret[$db->f("pref_name")] = $db->f("pref_value");
		}
		//make sure blacklist is correct
		$ret['black_list'] = implode(',', array_map('trim', explode(',', $ret['black_list'])));

		return $ret;
	}

	function putSetting($name, $value){
		$db = new DB_WE();
		$db->query('INSERT IGNORE INTO ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter(['tool' => 'newsletter', 'pref_name' => $name, pref_value => $value]));
	}

	function saveSettings(){
		$db = new DB_WE();
		// WORKARROUND BUG NR 7450
		foreach($this->settings as $key => $value){
			if($key != 'black_list'){
				$db->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter(['tool' => 'newsletter', 'pref_name' => $key, 'pref_value' => $value]));
			}
		}
	}

	function saveSetting($name, $value){
		$db = new DB_WE();
		$db->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter(['tool' => 'newsletter', 'pref_name' => $name, 'pref_value' => $value]));
	}

	function getBlackList(){
		return [];
	}

	function isBlack($email){
		static $black = 0;
		if(!$black){
			//remove whitespaces
			$black = explode(',', strtolower($this->settings['black_list']));
			foreach($black as &$b){
				$b = trim($b, " \t\n\r\n"); //intentionally duplicate \n!
			}
		}
		return in_array(trim(strtolower($email), " \t\n\r\n"), $black);
	}

	/**
	 * Write newsletter and mailing lists temp files
	 *
	 * @param Integer $nid
	 * @param Boolean $cachemails
	 * @return Array
	 */
	function cacheNewsletter($nid = 0, $cachemails = true){
		$ret = [];
		if($nid){
			$this->newsletter = new we_newsletter_newsletter($nid);
		}

		if($cachemails){
			// BEGIN cache emails groups
			$emailcache = we_base_file::getUniqueId();
			$groupcount = count($this->newsletter->groups) + 1;

			$ret["emailcache"] = $emailcache;
			$buffer = [];

			for($groupid = 1; $groupid < $groupcount; $groupid++){
				$tmp = $this->getEmails($groupid);
				foreach($tmp as $curEntry){
					if(isset($curEntry[0]) && !empty($curEntry[7])){
						$index = strtolower($curEntry[0]);
						if(isset($buffer[$index])){
							if(!in_array($curEntry[6], explode(",", $buffer[$index][6]))){
								$buffer[$index][6] .= "," . $curEntry[6];
							}
							$buffer[$index][7] = array_merge($buffer[$index][7], $curEntry[7]);
						} else {
							$buffer[$index] = $curEntry;
						}
					}
				}
			}

			$cc = 0;
			foreach($buffer as $k => $one){
				$buffer[$cc] = $one;
				unset($buffer[$k]);
				$cc++;
			}

			$ret["ecount"] = count($buffer);

			$groups = 0;
			$tmp = [];
			$go = true;
			$offset = 0;


			while($go){
				$tmp = array_slice($buffer, $offset, $this->settings["send_step"]);
				if(!empty($tmp)){
					$offset += $this->settings['send_step'];
					$groups++;
					we_newsletter_newsletter::saveToCache($tmp, $emailcache . "_$groups");
				} else {
					$go = false;
				}
			}

			$ret["gcount"] = $groups + 1;
		}

		// END cache emails groups
		// BEGIN cache newlsetter blocks
		$blockcache = we_base_file::getUniqueId();
		$blockcount = count($this->newsletter->blocks);

		$ret["blockcache"] = $blockcache;

		for($blockid = 0; $blockid < $blockcount; $blockid++){

			we_newsletter_newsletter::saveToCache(["defaultC" => $this->getContent($blockid, 0, 0, "", "", "", "", "###CUSTOMERID###"),
				"femaleC" => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###"),
				"maleC" => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###"),
				"title_firstname_lastnameC" => $this->getContent($blockid, 0, 0, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###"),
				"title_lastnameC" => $this->getContent($blockid, 0, 0, "", "###TITLE###", "", "###LASTNAME###", "###CUSTOMERID###"),
				"firstname_lastnameC" => $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###"),
				"firstnameC" => $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "", "###CUSTOMERID###"),
				"lastnameC" => $this->getContent($blockid, 0, 0, "", "", "", "###LASTNAME###", "###CUSTOMERID###"),
				"default" => $this->getContent($blockid, 0, 0, "", "", "", "", ""),
				"female" => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", ""),
				"male" => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", ""),
				"title_firstname_lastname" => $this->getContent($blockid, 0, 0, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", ""),
				"title_lastname" => $this->getContent($blockid, 0, 0, "", "###TITLE###", "", "###LASTNAME###", ""),
				"firstname_lastname" => $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "###LASTNAME###", ""),
				"firstname" => $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "", ""),
				"lastname" => $this->getContent($blockid, 0, 0, "", "", "", "###LASTNAME###", ""),
				], $blockcache . "_p_" . $blockid);

			we_newsletter_newsletter::saveToCache(['defaultC' => $this->getContent($blockid, 0, 1, '', '', '', '', '###CUSTOMERID###'),
				'femaleC' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
				'maleC' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
				'title_firstname_lastnameC' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
				'title_lastnameC' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '', '###LASTNAME###', '###CUSTOMERID###'),
				'firstname_lastnameC' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
				'firstnameC' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '', '###CUSTOMERID###'),
				'lastnameC' => $this->getContent($blockid, 0, 1, '', '', '', '###LASTNAME###', '###CUSTOMERID###'),
				'default' => $this->getContent($blockid, 0, 1, '', '', '', '', ''),
				'female' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
				'male' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
				'title_firstname_lastname' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
				'title_lastname' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '', '###LASTNAME###', ''),
				'firstname_lastname' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '###LASTNAME###', ''),
				'firstname' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '', ''),
				'lastname' => $this->getContent($blockid, 0, 1, '', '', '', '###LASTNAME###', ''),
				'inlines' => ($this->newsletter->blocks[$blockid]->Pack ? $this->cacheInlines($buffer) : []),
				], $blockcache . '_h_' . $blockid);
		}
		// END cache newlsetter blocks

		return $ret;
	}

	function cacheInlines(&$buffer){
		$trenner = '\s*';
		$patterns = ["/<(img" . $trenner . "[^>]+src" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ?\\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie",
			"/<(body" . $trenner . "[^>]+background" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ?\\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie",
			"/<(table" . $trenner . "[^>]+background" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ?\\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie",
			"/<(td" . $trenner . "[^>]+background" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ?\\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie",
			"/background" . $trenner . ":" . $trenner . "([^url]*url" . $trenner . "\([\"|\'|\\\\])?(.[^\)|^\"|^\'|^\\\\]+)([\"|\'|\\\\])?/sie",
			"/background-image" . $trenner . ":" . $trenner . "([^url]*url" . $trenner . "\([\"|\'|\\\\])?(.[^\)|^\"|^\'|^\\\\]+)([\"|\'|\\\\])?/sie",
		];

		$match = $inlines = [];

		foreach($buffer as $v){
			foreach($patterns as $pattern){
				if(preg_match_all($pattern, $v, $match)){
					foreach($match[2] as $name){
						if(!in_array($name, array_keys($inlines))){
							$newname = WE_CACHE_DIR . 'nl_' . we_base_file::getUniqueID();
							$inlines[$name] = $newname;
							we_base_file::insertIntoCleanUp($newname, 86400);
							we_base_file::save($newname, chunk_split(base64_encode(we_base_file::load($name)), 76, "\n"));
						}
					}
				}
			}
		}
		return $inlines;
	}

	function getCleanMail($mail){
		$match = [];
		$pattern = '|[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,6}|i';
		if(preg_match($pattern, $mail, $match)){
			return ($match[0]);
		}
		return '';
	}

	public function getShowImportBox(){
		return $this->show_import_box;
	}

	public function getShowExportBox(){
		return $this->show_export_box;
	}

	public function getHomeScreen(){
		$content = we_html_button::create_button('new_newsletter', "javascript:top.we_cmd('new_newsletter');", '', 0, 0, "", "", !permissionhandler::hasPerm("NEW_NEWSLETTER")) .
			'<br/>' .
			we_html_button::create_button('new_newsletter_group', "javascript:top.we_cmd('new_newsletter_group');", '', 0, 0, "", "", !permissionhandler::hasPerm("NEW_NEWSLETTER"));

		return parent::getActualHomeScreen('newsletter', "newsletter.gif", $content, we_html_element::htmlForm(['name' => 'we_form'], $this->getHiddens(['ncmd' => 'home']) . we_html_element::htmlHidden('home', 0)));
	}

	private static function we_getObjectFileByID($id, $includepath = ''){
		$mydoc = new we_objectFile();
		$mydoc->initByID($id, OBJECT_FILES_TABLE, we_class::LOAD_MAID_DB);
		return $mydoc->i_getDocument($includepath);
	}

}
