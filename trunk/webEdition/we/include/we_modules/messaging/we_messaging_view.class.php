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
class we_messaging_view extends we_modules_view{
	private $messaging = null;
	private $transaction;
	private $weTransaction;

	function __construct($frameset = "", $topframe = "top.content", $reqTransaction = 0, &$weTransaction = 0){
		parent::__construct($frameset, $topframe);
		$this->item_pattern = addslashes('<img style="vertical-align: bottom" src="' . ICON_DIR . 'user.gif" />&nbsp;');
		$this->group_pattern = addslashes('<img style="vertical-align: bottom" src="' . ICON_DIR . we_base_ContentTypes::FOLDER_ICON . '" />&nbsp;');

		$this->transaction = $reqTransaction;
		$this->weTransaction = &$weTransaction;
	}

	//-----------------Init -------------------------------


	function setTopFrame($frame){
		parent::setTopFrame($frame);
		$this->editorBodyFrame = $frame . '.editor.edbody';
		$this->editorBodyForm = $this->editorBodyFrame . '.document.we_form';
		$this->editorHeaderFrame = $frame . '.editor.edheader';
	}

	//------------------------------------------------


	function getCommonHiddens($cmds = array()){
		return
			parent::getCommonHiddens($cmds) .
			$this->htmlHidden("vernr", (isset($cmds["vernr"]) ? $cmds["vernr"] : 0)) .
			$this->htmlHidden("IsFolder", (isset($this->voting->IsFolder) ? $this->voting->IsFolder : 0));
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

	function processCommands(){
		$this->transaction = ($this->transaction == 'no_request' ?
				$this->weTransaction :
				(preg_match('|^([a-f0-9]){32}$|i', $this->transaction) ? $this->transaction : 0)
			);


		$this->messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$this->transaction]);
		$this->messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
		$this->messaging->init($_SESSION['weS']['we_data'][$this->transaction]);

		$out = '';
		switch(($mcmd = we_base_request::_(we_base_request::STRING, "mcmd", "goToDefaultCase"))){
			case 'search_messages':
			case 'show_folder_content':
				return $this->get_folder_content(we_base_request::_(we_base_request::INT, 'id', 0), we_base_request::_(we_base_request::STRING, 'sort', ""), we_base_request::_(we_base_request::RAW, 'entrsel', ""), we_base_request::_(we_base_request::RAW, 'searchterm', ""), 1) .
					$this->print_fc_html() .
					$this->update_treeview();
			case 'launch':
				$mode = we_base_request::_(we_base_request::STRING, 'mode');
				if($mode == 'todo'){
					$f = $this->messaging->get_inbox_folder('we_todo');
				} elseif($mode == 'message'){
					$f = $this->messaging->get_inbox_folder('we_message');
				} else {
					break;
				}

				return $this->get_folder_content($f['ID'], '', '', '', 0) .
					$this->print_fc_html() .
					$this->update_treeview() .
					we_html_element::jsElement('
					if (top.content.viewclass != "' . $mode . '") {
						top.content.set_frames("' . $mode . '");
					}
					');
			case 'refresh_mwork':
				$out .= $this->refresh_work(true);
			/* FALLTHROUGH */
			case 'show_message':
				if(isset($id)){
					$out .= we_html_element::jsElement('
					top.content.editor.edbody.msg_mfv.messaging_msg_view.location="' . (WE_MESSAGING_MODULE_DIR . 'messaging_message_view.php?we_transaction=' . $this->transaction . '&id= ' . $id) . '";
					');
				}
				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				return $out;
			case 'new_message':
				return we_html_element::jsScript(JS_DIR . 'windows.js') .
					we_html_element::jsElement('
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'messaging_newmessage.php?we_transaction=' . $this->transaction . '&mode=' . we_base_request::_(we_base_request::STRING, 'mode') . '", "messaging_new_message",-1,-1,670,530,true,false,true,false);
				');
			case 'new_todo':
				return we_html_element::jsScript(JS_DIR . 'windows.js') .
					we_html_element::jsElement('
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_edit_todo.php?we_transaction=' . $this->transaction . '&mode=new", "messaging_new_todo",-1,-1,690,520,true,false,true,false);					//-->
				');
			case 'forward_todo':
				return we_html_element::jsScript(JS_DIR . 'windows.js') .
					we_html_element::jsElement('
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_edit_todo.php?we_transaction=' . $this->transaction . '&mode=forward", "messaging_new_todo",-1,-1,690,600,true,false,true,false);
				');
			case 'rej_todo':
				return we_html_element::jsScript(JS_DIR . 'windows.js') .
					we_html_element::jsElement('
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_edit_todo.php?we_transaction=' . $this->transaction . '&mode=reject", "messaging_new_todo",-1,-1,690,600,true,false,true,false);
				');
			case 'reset_right_view':
				return we_html_element::jsElement('
				top.content.editor.edbody.entries_selected = new Array();
				top.content.editor.edbody.msg_mfv.messaging_messages_overview.location="' . we_class::url(WE_MESSAGING_MODULE_DIR . 'messaging_show_folder_content.php') . '";
				top.content.editor.edbody.msg_mfv.messaging_msg_view.location="' . HTML_DIR . 'white.html"
				');
			case 'update_todo':
				if($this->messaging->selected_message){
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
			case 'copy_msg':
				$this->messaging->set_clipboard(we_base_request::_(we_base_request::INTLIST, 'entrsel'), 'copy');
				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				break;
			case 'cut_msg':
				$this->messaging->set_clipboard(we_base_request::_(we_base_request::INTLIST, 'entrsel'), 'cut');
				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				break;
			case 'paste_msg':
				$errs = array();
				$this->messaging->clipboard_paste($errs);
				$this->messaging->reset_ids_selected();
				$this->messaging->get_fc_data($this->messaging->Folder_ID, '', '', 0);

				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);

				$js_out = '
				top.content.editor.edbody.entries_selected = new Array();
				top.content.editor.edbody.messaging_fv_headers.location="' . we_class::url($this->frameset) . '&pnt=msg_fv_headers&si=' . $this->messaging->get_sortitem() . '&so=' . $this->messaging->get_sortorder() . '&viewclass=" + top.content.viewclass;
				top.content.editor.edbody.msg_mfv.messaging_messages_overview.location="' . we_class::url(WE_MESSAGING_MODULE_DIR . 'messaging_show_folder_content.php') . '";
				top.content.editor.edbody.msg_mfv.messaging_msg_view.location="' . HTML_DIR . 'white.html";
				';

				$aid = $this->messaging->Folder_ID;
				$idx = we_messaging_messaging::array_ksearch('ID', $aid, $this->messaging->available_folders);
				if($idx > -1){
					$js_out .= 'aid = ' . $aid . ';
					top.content.updateEntry(aid, -1, "' . $this->messaging->available_folders[$idx]['Name'] . ' - (' . $this->messaging->get_message_count($aid, '') . ')", -1, 1);';
				}

				return we_html_element::jsElement($js_out) . $this->update_treeview();
			case 'delete_msg':
				$this->messaging->set_ids_selected(we_base_request::_(we_base_request::INTLIST, 'entrsel'));
				$this->messaging->delete_items();
				$this->messaging->reset_ids_selected();
				$this->messaging->get_fc_data(we_base_request::_(we_base_request::INT, 'id', 0), we_base_request::_(we_base_request::STRING, 'sort', ''), we_base_request::_(we_base_request::RAW, 'searchterm', ''), 1);

				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);

				$js_out = '
				top.content.editor.edbody.entries_selected = new Array();
				//we_class::2xok
				top.content.editor.edbody.messaging_fv_headers.location="' . we_class::url($this->frameset) . '&pnt=msg_fv_headers&si=' . $this->messaging->get_sortitem() . '&so=' . $this->messaging->get_sortorder() . '&viewclass=" + top.content.viewclass;
				top.content.editor.edbody.msg_mfv.messaging_messages_overview.location=" ' . we_class::url(WE_MESSAGING_MODULE_DIR . 'messaging_show_folder_content.php') . '";
				top.content.editor.edbody.msg_mfv.messaging_msg_view.location="' . HTML_DIR . 'white.html";
				';

				$aid = $this->messaging->Folder_ID;
				$js_out = '
					aid = ' . $aid . ';
					top.content.updateEntry(aid, -1, "' . $this->messaging->available_folders[we_messaging_messaging::array_ksearch('ID', $aid, $this->messaging->available_folders)]['Name'] . ' - (' . $this->messaging->get_message_count($aid, '') . ')", -1, 1));
				';
				return we_html_element::jsElement($js_out);
			case 'update_treeview':
				return $this->update_treeview();
			case 'update_msgs':
				$out .= $this->update_treeview();
				$blank = false;
			/* FALLTHROUGH */
			case 'update_fcview':
				$id = $this->messaging->Folder_ID;
				$blank = isset($blank) ? $blank : true;
				if(($this->messaging->cont_from_folder != 1) && ($id != -1)){
					if(($ids = we_base_request::_(we_base_request::INTLIST, 'entrsel'))){
						$this->messaging->set_ids_selected($ids);
					}

					$this->messaging->get_fc_data($id, we_base_request::_(we_base_request::STRING, 'sort', ''), '', 0);

					$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
					$out .= $this->print_fc_html($blank);
				}
				return $out;
			case 'edit_folder':
				return (($mode = we_base_request::_(we_base_request::STRING, 'mode')) == 'edit' ?
						we_html_element::jsElement('
					top.content.editor.location = "' . WE_MESSAGING_MODULE_DIR . 'messaging_edit_folder.php?we_transaction=' . $this->transaction . '&mode=' . $mode . '&fid=' . we_base_request::_(we_base_request::INT, 'fid', -1) . '";
					') :
						'');

			case 'save_folder_settings':
				if(($id = we_base_request::_(we_base_request::INT, 'id')) !== false){
					//$mcount = $_REQUEST['mode'] == 'new' ? 0 : $this->messaging->get_message_count($_REQUEST['id'], '');
					if(($mode = we_base_request::_(we_base_request::STRING, "mode")) == 'new'){
						$parent = we_base_request::_(we_base_request::INT, 'parent_id', 0);
						$type = we_base_request::_(we_base_request::STRING, 'type');
						$out .= we_html_element::jsElement('
top.content.folder_added(' . $parent . ');
top.content.menuDaten.add(new top.content.urlEntry("' . ($type == 'we_todo' ? 'todo_folder' : 'msg_folder') . '.gif", "' . $id . '", "' . $parent . '", "' . we_base_request::_(we_base_request::STRING, 'name') . ' - (0)", "leaf_Folder", "' . MESSAGES_TABLE . '", "' . ($type == 'we_todo' ? 'todo_folder' : 'msg_folder') . '"));' .
								we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[folder_created]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
top.content.drawEintraege();
						');
					} else {
						$js_out = '
top.content.menuDaten.clear();
top.content.startloc=0;
top.content.menuDaten.add(new top.content.self.rootEntry(0,"root","root"));';

						foreach($this->messaging->available_folders as $folder){
							if(($sf_cnt = $this->messaging->get_subfolder_count($folder['ID'], '')) >= 0){
								$js_out .= 'top.content.menuDaten.add(
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
);';
							} else {
								$js_out .= 'top.content.menuDaten.add(
	new top.content.urlEntry(
		"' . ($folder['ClassName'] == 'we_todo' ? 'todo_folder' : 'msg_folder') . '.gif",
		"' . $folder['ID'] . '",
		"' . $folder['ParentID'] . '",
		"' . $folder['Name'] . ' - (' . $this->messaging->get_message_count($folder['ID'], '') . ')",
			"leaf_Folder",
		"' . MESSAGES_TABLE . '",
		"' . ($folder['ClassName'] == 'we_todo' ? 'todo_folder' : 'msg_folder') . '"
	)
);';
							}
						}

						$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
						$js_out .= 'top.content.drawEintraege();';

						$out .= we_html_element::jsElement($js_out);
					}
				}
				return $out;
			case 'delete_folders':
				if(($folders = we_base_request::_(we_base_request::INTLISTA, 'folders'))){

					$out .= we_html_element::jsElement('
					top.content.delete_menu_entries(new Array(String(' . implode('), String(', $folders) . ')));
					top.content.folders_removed(new Array(String(' . implode('), String(', $folders) . ')));
					top.content.drawEintraege();
					');
				}
				return $out;
			case 'edit_settings':
				return we_html_element::jsScript(JS_DIR . 'windows.js') .
					we_html_element::jsElement('
					new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'messaging_settings.php?we_transaction=' . $this->transaction . '&mode=' . we_base_request::_(we_base_request::STRING, 'mode') . '", "messaging_settings",-1,-   1,280,200,true,false,true,false);
					');
			case 'save_settings':
				if($ui){
					if($this->messaging->save_settings(array('update_interval' => $ui))){
						$out .= we_html_element::jsScript(JS_DIR . 'messaging_std.js') .
							we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[saved]'), we_message_reporting::WE_MESSAGE_NOTICE) .
								'close_win("messaging_settings");'
						);
					}
				}
				return $out;
			case 'messaging_close':
				return we_html_element::jsElement('top.close();');
			default:
				return 'mcmd=' . $mcmd . '<br/>';
		}
	}

	//some additional methods called by getJSCmd(). TODO: elimiminate GLOBALS by passing objects in
	private function print_fc_html($blank = true){

		return we_html_element::jsElement('
top.content.editor.edbody.entries_selected = new Array(' . $this->messaging->get_ids_selected() . ');
top.content.editor.edbody.messaging_fv_headers.location="' . we_class::url($this->frameset) . '&pnt=msg_fv_headers&si=' . $this->messaging->get_sortitem() . '&so=' . $this->messaging->get_sortorder() . '&viewclass=" + top.content.viewclass;
if (top.content.editor.edbody.msg_mfv.messaging_messages_overview) {
	top.content.editor.edbody.msg_mfv.messaging_messages_overview.location="' . we_class::url(WE_MESSAGING_MODULE_DIR . "messaging_show_folder_content.php") . '";
}' .
				($blank ? 'top.content.editor.edbody.msg_mfv.messaging_msg_view.location="' . HTML_DIR . 'white.html";' : '')
		);
	}

	private function refresh_work($blank = false){
		if(($eSel = we_base_request::_(we_base_request::STRING, "entrsel"))){
			$this->messaging->set_ids_selected($eSel);
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
			$out = we_html_element::jsElement('top.content.editor.edbody.last_entry_selected = -1;');
		}

		$this->messaging->get_fc_data(isset($id) ? $id : '', empty($sort) ? '' : $sort, $searchterm, $usecache);
		$this->weTransaction = (preg_match('|^([a-f0-9]){32}$|i', $this->transaction) ? $this->transaction : 0); //$this->weTransaction is reference to $we_transaction
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
