<?php

/**
 * webEdition CMS
 *
 * $Rev: 5556 $
 * $Author: mokraemer $
 * $Date: 2013-01-11 22:17:18 +0100 (Fr, 11 Jan 2013) $
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
/* the parent class of storagable webEdition classes */
class weMessagingView extends weModuleView {

	var $db;
	var $frameset;
	var $topFrame;
	//var $voting;
	//var $editorBodyFrame;
	//var $editorBodyForm;
	//var $editorHeaderFrame;
	//var $icon_pattern = "";
	//var $item_pattern = "";
	//var $group_pattern = "";

	private $messaging = null;
	private $transaction;
	private $weTransaction;

	function __construct($frameset = "", $topframe = "top.content", $reqTransaction = 0, &$weTransaction = 0){
		$this->db = new DB_WE();
		$this->setFramesetName($frameset);
		$this->setTopFrame($topframe);
		$this->item_pattern = addslashes('<img style="vertical-align: bottom" src="' . ICON_DIR . 'user.gif" />&nbsp;');
		$this->group_pattern = addslashes('<img style="vertical-align: bottom" src="' . ICON_DIR . we_base_ContentTypes::FOLDER_ICON . '" />&nbsp;');

		$this->transaction = $reqTransaction;
		$this->weTransaction = &$weTransaction;
	}

	//----------- Utility functions ------------------

	function htmlHidden($name, $value = ""){
		return we_html_element::htmlHidden(array("name" => trim($name), "value" => oldHtmlspecialchars($value)));
	}

	//-----------------Init -------------------------------

	function setFramesetName($frameset){
		$this->frameset = $frameset;
	}

	function setTopFrame($frame){
		$this->topFrame = $frame;
		$this->editorBodyFrame = $frame . '.right.editor.edbody';
		$this->editorBodyForm = $this->editorBodyFrame . '.document.we_form';
		$this->editorHeaderFrame = $frame . '.right.editor.edheader';
	}

	//------------------------------------------------


	function getCommonHiddens($cmds = array()){
		$out = $this->htmlHidden("cmd", (isset($cmds["cmd"]) ? $cmds["cmd"] : ""));
		$out.=$this->htmlHidden("cmdid", (isset($cmds["cmdid"]) ? $cmds["cmdid"] : ""));
		$out.=$this->htmlHidden("pnt", (isset($cmds["pnt"]) ? $cmds["pnt"] : ""));
		$out.=$this->htmlHidden("tabnr", (isset($cmds["tabnr"]) ? $cmds["tabnr"] : ""));
		$out.=$this->htmlHidden("vernr", (isset($cmds["vernr"]) ? $cmds["vernr"] : 0));
		$out.=$this->htmlHidden("IsFolder", (isset($this->voting->IsFolder) ? $this->voting->IsFolder : '0'));
		return $out;
	}

	function getJSTop(){
		//
	}

	function getJSTreeHeader(){

		return '

			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd(){
				var args = "";
				var url = "' . $this->frameset . '?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				switch (arguments[0]) {
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
						}
						eval(\'top.content.we_cmd(\'+args+\')\');
				}
			}
		' . $this->getJSSubmitFunction("cmd");
	}

	function getJSSubmitFunction($def_target = "edbody", $def_method = "post"){
		return '
			function submitForm() {
				var f = self.document.we_form;

				if (arguments[0]) {
					f.target = arguments[0];
				} else {
					f.target = "' . $def_target . '";
				}

				if (arguments[1]) {
					f.action = arguments[1];
				} else {
					f.action = "' . $this->frameset . '";
				}

				if (arguments[2]) {
					f.method = arguments[2];
				} else {
					f.method = "' . $def_method . '";
				}

				f.submit();
			}

	';
	}

	function processVariables(){
		//
	}

	function new_array_splice(&$a, $start, $len = 1){
		$ks = array_keys($a);
		$k = array_search($start, $ks);
		if($k !== false){
			$ks = array_splice($ks, $k, $len);
			foreach($ks as $k)
				unset($a[$k]);
		}
	}

	function processCommands(){

		if($this->transaction == 'no_request'){
			$this->transaction = $this->weTransaction;
		} else{
			$this->transaction = (preg_match('|^([a-f0-9]){32}$|i', $this->transaction) ? $this->transaction : 0);
		}

		$this->messaging = new we_messaging($_SESSION['weS']['we_data'][$this->transaction]);
		$this->messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
		$this->messaging->init($_SESSION['weS']['we_data'][$this->transaction]);

		if(!isset($_REQUEST["mcmd"])){
			$_REQUEST["mcmd"] = "goToDefaultCase";
		}

		$out = '';
		switch($_REQUEST["mcmd"]){
			case 'search_messages':
			case 'show_folder_content':
				return $this->get_folder_content(isset($_REQUEST['id']) ? $_REQUEST['id'] : "", isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "", isset($_REQUEST['entrsel']) ? $_REQUEST['entrsel'] : "", isset($_REQUEST['searchterm']) ? $_REQUEST['searchterm'] : "", 1) .
					$this->print_fc_html() .
					$this->update_treeview();
				break;
			case 'launch':
				if($_REQUEST['mode'] == 'todo'){
					$f = $this->messaging->get_inbox_folder('we_todo');
				} elseif($_REQUEST['mode'] == 'message'){
					$f = $this->messaging->get_inbox_folder('we_message');
				} else{
					break;
				}

				return $this->get_folder_content($f['ID'], '', '', '', 0) .
					$this->print_fc_html() .
					$this->update_treeview() .
					we_html_element::jsElement('
					if (top.content.viewclass != "' . $_REQUEST['mode'] . '") {
						top.content.set_frames("' . $_REQUEST['mode'] . '");
					}
					');
				break;
			case 'refresh_mwork':
				$out .= $this->refresh_work(true);
			/* FALLTHROUGH */
			case 'show_message':
				if(isset($id)){
					$out .= we_html_element::jsElement('
					top.content.right.editor.edbody.msg_mfv.messaging_msg_view.location="' . (WE_MESSAGING_MODULE_DIR . 'messaging_message_view.php?we_transaction=' . $this->transaction . '&id= ' . $id) . '";
					');
				}
				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				return $out;
				break;
			case 'new_message':
				return we_html_element::jsScript(JS_DIR . 'windows.js') .
				we_html_element::jsElement('
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'messaging_newmessage.php?we_transaction=' . $this->transaction . '&mode=' . $_REQUEST['mode'] . '", "messaging_new_message",-1,-1,670,530,true,false,true,false);
				');
				break;
			case 'new_todo':
				return we_html_element::jsScript(JS_DIR . 'windows.js') .
				we_html_element::jsElement('
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_edit_todo.php?we_transaction=' . $this->transaction . '&mode=new", "messaging_new_todo",-1,-1,690,520,true,false,true,false);					//-->
				');
				break;
			case 'forward_todo':
				return we_html_element::jsScript(JS_DIR . 'windows.js') .
				we_html_element::jsElement('
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_edit_todo.php?we_transaction=' . $this->transaction . '&mode=forward", "messaging_new_todo",-1,-1,690,600,true,false,true,false);
				');					//-->
				break;
			case 'rej_todo':
				return we_html_element::jsScript(JS_DIR . 'windows.js') .
				we_html_element::jsElement('
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_edit_todo.php?we_transaction=' . $this->transaction . '&mode=reject", "messaging_new_todo",-1,-1,690,600,true,false,true,false);
				');					//-->
				break;
			case 'reset_right_view':
				return we_html_element::jsElement('
				top.content.right.editor.edbody.entries_selected = new Array();
				top.content.right.editor.edbody.msg_mfv.messaging_messages_overview.location="' . we_class::url(WE_MESSAGING_MODULE_DIR . 'messaging_show_folder_content.php') . '";
				top.content.right.editor.edbody.msg_mfv.messaging_msg_view.location="' . HTML_DIR . 'white.html"
				');
				break;
			case 'update_todo':
				if(!empty($this->messaging->selected_message)){
					echo we_html_element::jsScript(JS_DIR . 'windows.js') .
					we_html_element::jsElement('
					new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_update_todo.php?we_transaction=' . $this->transaction . '&mode=reject", "messaging_new_todo",-1,-1,690,600,true,false,true,false);
					');
				}
				break;
			case 'todo_markdone':
				$arr = array('todo_status' => 100);
				$this->messaging->used_msgobjs['we_todo']->update_status($arr, $this->messaging->selected_message['int_hdrs']);
				$out .= $this->refresh_work(true);
				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				return $out;
				break;
			case 'copy_msg':
				$this->messaging->set_clipboard($_REQUEST['entrsel'], 'copy');
				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				break;
			case 'cut_msg':
				$this->messaging->set_clipboard($_REQUEST['entrsel'], 'cut');
				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				break;
			case 'paste_msg':
				$errs = array();
				$this->messaging->clipboard_paste($errs);
				$this->messaging->reset_ids_selected();
				$this->messaging->get_fc_data($this->messaging->Folder_ID, '', '', 0);

				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);

				$js_out = '
				top.content.right.editor.edbody.entries_selected = new Array();
				top.content.right.editor.edbody.messaging_fv_headers.location="' . we_class::url($this->frameset) . '&pnt=msg_fv_headers&si=' . $this->messaging->get_sortitem() . '&so=' . $this->messaging->get_sortorder() . '&viewclass=" + top.content.viewclass;
				top.content.right.editor.edbody.msg_mfv.messaging_messages_overview.location="' . we_class::url(WE_MESSAGING_MODULE_DIR . 'messaging_show_folder_content.php') . '";
				top.content.right.editor.edbody.msg_mfv.messaging_msg_view.location="' . HTML_DIR . 'white.html";
				';

				$aid = $this->messaging->Folder_ID;
				$idx = array_ksearch('ID', $aid, $this->messaging->available_folders);
				if($idx > -1){
					$js_out .= '
					aid = ' . $aid . ';
					top.content.updateEntry(aid, -1, "' . $this->messaging->available_folders[$idx]['Name'] . ' - (' . $this->messaging->get_message_count($aid, '') . ')", -1, 1);
					';
				}

				return we_html_element::jsElement($js_out) . $this->update_treeview();
				break;
			case 'delete_msg':
				$this->messaging->set_ids_selected($_REQUEST['entrsel']);
				$this->messaging->delete_items();
				$this->messaging->reset_ids_selected();
				$this->messaging->get_fc_data(isset($_REQUEST['id']) ? $_REQUEST['id'] : '', empty($_REQUEST['sort']) ? '' : $_REQUEST['sort'], isset($_REQUEST['searchterm']) ? $_REQUEST['searchterm'] : '', 1);

				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);

				$js_out = '
				top.content.right.editor.edbody.entries_selected = new Array();
				//we_class::2xok
				top.content.right.editor.edbody.messaging_fv_headers.location="' . we_class::url($this->frameset) . '&pnt=msg_fv_headers&si=' . $this->messaging->get_sortitem() . '&so=' . $this->messaging->get_sortorder() . '&viewclass=" + top.content.viewclass;
				top.content.right.editor.edbody.msg_mfv.messaging_messages_overview.location=" ' . we_class::url(WE_MESSAGING_MODULE_DIR . 'messaging_show_folder_content.php') . '";
				top.content.right.editor.edbody.msg_mfv.messaging_msg_view.location="' . HTML_DIR . 'white.html";
				';

				$aid = $this->messaging->Folder_ID;
				$js_out = '
					aid = ' . $aid . ';
					top.content.updateEntry(aid, -1, "' . $this->messaging->available_folders[array_ksearch('ID', $aid, $this->messaging->available_folders)]['Name'] . ' - (' . $this->messaging->get_message_count($aid, '') . ')", -1, 1));
				';
				return we_html_element::jsElement($js_out);
				break;
			case 'update_treeview':
				return $this->update_treeview();
				break;
			case 'update_msgs':
				$out .= $this->update_treeview();
				$blank = false;
			/* FALLTHROUGH */
			case 'update_fcview':
				$id = $this->messaging->Folder_ID;
				$blank = isset($blank) ? $blank : true;
				if(($this->messaging->cont_from_folder != 1) && ($id != -1)){
					if(isset($_REQUEST['entrsel']) && $_REQUEST['entrsel'] != ''){
						$this->messaging->set_ids_selected($_REQUEST['entrsel']);
					}

					$this->messaging->get_fc_data($id, empty($_REQUEST['sort']) ? '' : $_REQUEST['sort'], '', 0);

					$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
					$out .= $this->print_fc_html($blank);
				}
				return $out;
				break;
			case 'edit_folder':
				if($_REQUEST['mode'] == 'new' || ($_REQUEST['mode'] == 'edit')){
					$out .= we_html_element::jsElement('
					top.content.right.editor.location = "' . WE_MESSAGING_MODULE_DIR . 'messaging_edit_folder.php?we_transaction=' . $this->transaction . '&mode=' . $_REQUEST['mode'] . '&fid=' . (isset($_REQUEST['fid']) ? $_REQUEST['fid'] : -1) . '";
					');
				}
				return $out;
				break;
			case 'save_folder_settings':
				if(isset($_REQUEST['id'])){
					$mcount = $_REQUEST['mode'] == 'new' ? 0 : $this->messaging->get_message_count($_REQUEST['id'], '');
					if($_REQUEST["mode"] == 'new'){
						$out .= we_html_element::jsElement('
top.content.folder_added(' . $_REQUEST['parent_id'] . ');
top.content.menuDaten.add(new top.content.urlEntry("' . ($_REQUEST['type'] == 'we_todo' ? 'todo_folder' : 'msg_folder') . '.gif", "' . $_REQUEST['id'] . '", "' . $_REQUEST['parent_id'] . '", "' . $_REQUEST['name'] . ' - (0)", "leaf_Folder", "' . MESSAGES_TABLE . '", "' . ($_REQUEST['type'] == 'we_todo' ? 'todo_folder' : 'msg_folder') . '"));' .
we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[folder_created]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
top.content.drawEintraege();
						');
					} else{
						$js_out = '
top.content.menuDaten.clear();
top.content.startloc=0;
top.content.menuDaten.add(new top.content.self.rootEntry("0","root","root"));
						';

						$entries = array();
						foreach($this->messaging->available_folders as $folder)
							if(($sf_cnt = $this->messaging->get_subfolder_count($folder['ID'], '')) >= 0){
								$js_out = '
top.content.menuDaten.add(
	new top.content.dirEntry(
		"' . ($folder['ClassName'] == 'we_todo' ? 'todo_folder' : 'msg_folder') . '.gif",
		"' . $folder['ID'] . '","' . $folder['ParentID'] . '",
		"' . $folder['Name'] . ' - (' . $this->messaging->get_message_count($folder['ID'], '') . ')",
		false,
		"parent_Folder",
		"' . MESSAGES_TABLE . '",
		' . $sf_cnt . ',
		"' . ($folder['ClassName'] == 'we_todo' ? 'todo_folder' : 'msg_folder') . '"
	)
);
								';
							} else{
								$js_out = '
top.content.menuDaten.add(
	new top.content.urlEntry(
		"' . ($folder['ClassName'] == 'we_todo' ? 'todo_folder' : 'msg_folder') . '.gif",
		"' . $folder['ID'] . '",
		"' . $folder['ParentID'] . '",
		"' . $folder['Name'] . ' - (' . $this->messaging->get_message_count($folder['ID'], '') . ')",
			"leaf_Folder",
		"' . MESSAGES_TABLE . '",
		"' . ($folder['ClassName'] == 'we_todo' ? 'todo_folder' : 'msg_folder') . '"
	)
);
								';
							}

						$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
						$js_out = '
top.content.drawEintraege();
						';

						$out .= we_html_element::jsElement($js_out);
					}
				}
				return $out;
				break;
			case 'delete_folders':
				if(!empty($_REQUEST['folders'])){
					$folders = explode(',', $_REQUEST['folders']);

					$out .= we_html_element::jsElement('
					top.content.delete_menu_entries(new Array(String(' . join('), String(', $folders) . ')));
					top.content.folders_removed(new Array(String(' . join('), String(', $folders) . ')));
					top.content.drawEintraege();
					');
				}
				return $out;
				break;
			case 'edit_settings':
				return we_html_element::jsScript(JS_DIR . 'windows.js') .
					we_html_element::jsElement('
					new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'messaging_settings.php?we_transaction=' . $this->transaction . '&mode=' . $_REQUEST['mode'] . '", "messaging_settings",-1,-   1,280,200,true,false,true,false);
					');
				break;
			case 'save_settings':
				if($ui){
					if($this->messaging->save_settings(array('update_interval' => $ui))){
						$out .= we_html_element::jsScript(JS_DIR . 'messaging_std.js') .
						we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[saved]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
						close_win("messaging_settings");
						');
					}
				}
				return $out;
				break;
			case 'messaging_close':
				return we_html_element::jsElement('
				top.close();
				');
				break;
			default:
				return 'mcmd=' . $_REQUEST['mcmd'] . '<br>';
		}

	}

	//some additional methods called by getJSCmd(). TODO: elimiminate GLOBALS by passing objects in
	private function print_fc_html($blank = true){

		return we_html_element::jsElement('
top.content.right.editor.edbody.entries_selected = new Array(' . $this->messaging->get_ids_selected() .');
top.content.right.editor.edbody.messaging_fv_headers.location="' . we_class::url($this->frameset) . '&pnt=msg_fv_headers&si=' . $this->messaging->get_sortitem() . '&so=' . $this->messaging->get_sortorder() . '&viewclass=" + top.content.viewclass;
if (top.content.right.editor.edbody.msg_mfv.messaging_messages_overview) {
	top.content.right.editor.edbody.msg_mfv.messaging_messages_overview.location="' . we_class::url(WE_MESSAGING_MODULE_DIR . "messaging_show_folder_content.php") . '";
}' .
($blank ? 'top.content.right.editor.edbody.msg_mfv.messaging_msg_view.location="' . HTML_DIR . 'white.html";' : '')
		);
	}

	private function refresh_work($blank = false){
		if(isset($_REQUEST["entrsel"]) && $_REQUEST["entrsel"] != ''){
			$this->messaging->set_ids_selected($_REQUEST["entrsel"]);
		}

		$this->messaging->get_fc_data($this->messaging->Folder_ID, '', '', 0);
		//print $this->print_fc_html($blank);
		return $this->print_fc_html($blank) . $this->update_treeview();
	}

	private function get_folder_content($id, $sort = '', $entrsel = '', $searchterm = '', $usecache = 1){

		if($entrsel != ''){
			$this->messaging->set_ids_selected($entrsel);
		}

		$out = '';
		if($id != $this->messaging->Folder_ID){
			$this->messaging->reset_ids_selected();
			$out = we_html_element::jsElement('top.content.right.editor.edbody.last_entry_selected = -1;');
		}

		$this->messaging->get_fc_data(isset($id) ? $id : '', empty($sort) ? '' : $sort, $searchterm, $usecache);
		$this->weTransaction = (preg_match('|^([a-f0-9]){32}$|i', $this->transaction) ? $this->transaction : 0);//$this->weTransaction is reference to $we_transaction
		$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);

		return $out;
	}

	private function update_treeview(){
		$tmp = '';
		foreach($this->messaging->available_folders as $f){
			$tmp.='top.content.updateEntry(' . $f['ID'] . ', ' . $f['ParentID'] . ', "' . $f['Name'] . ' - (' . $this->messaging->get_message_count($f['ID'], '') . ')", -1, 1);';
		}
		$tmp.='top.content.drawEintraege();';
		return we_html_element::jsElement($tmp);
	}

}