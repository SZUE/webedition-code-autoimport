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

	public function __construct($frameset, $reqTransaction = 0, &$weTransaction = 0){
		parent::__construct($frameset);

		$this->transaction = $reqTransaction;
		$this->weTransaction = &$weTransaction;
	}

	function getCommonHiddens($cmds = []){
		return
			parent::getCommonHiddens($cmds) .
			we_html_element::htmlHiddens(array(
				"vernr" => (isset($cmds["vernr"]) ? $cmds["vernr"] : 0),
				"IsFolder" => (isset($this->voting->IsFolder) ? $this->voting->IsFolder : 0)
		));
	}

	function processVariables(){
		//
	}

	function processCommands(){
		$this->transaction = ($this->transaction === 'no_request' ?
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
				return $this->get_folder_content(we_base_request::_(we_base_request::INT, 'id', 0), we_base_request::_(we_base_request::STRING, 'sort', ""), we_base_request::_(we_base_request::INTLISTA, 'entrsel', []), we_base_request::_(we_base_request::RAW, 'searchterm', ""), 1) .
					$this->print_fc_html() .
					$this->update_treeview();
			case 'launch':
				$mode = we_base_request::_(we_base_request::STRING, 'mode');
				switch($mode){
					case 'todo':
						$f = $this->messaging->get_inbox_folder('we_todo');
						break;
					case 'message':
						$f = $this->messaging->get_inbox_folder('we_message');
						break;
					default:
						break 2;
				}

				return $this->get_folder_content($f['ID'], '', [], '', 0) .
					$this->print_fc_html() .
					$this->update_treeview() .
					we_html_element::jsElement('
if (top.content.viewclass != "' . $mode . '") {
	top.content.set_frames("' . $mode . '");
}');
			case 'refresh_mwork':
				$out .= $this->refresh_work(true);
			/* FALLTHROUGH */
			case 'show_message':
				if(isset($id)){
					$out .= we_html_element::jsElement('top.content.editor.edbody.messaging_msg_view.location=WE().consts.dirs.WE_MESSAGING_MODULE_DIR+"messaging_message_view.php?we_transaction=' . $this->transaction . '&id= ' . $id . '";');
				}
				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				return $out;
			case 'new_message':
				return we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
					we_html_element::jsElement('new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR+"messaging_newmessage.php?we_transaction=' . $this->transaction . '&mode=' . we_base_request::_(we_base_request::STRING, 'mode') . '", "messaging_new_message",-1,-1,670,530,true,false,true,false);');
			case 'new_todo':
				return we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
					we_html_element::jsElement('new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR+"todo_edit_todo.php?we_transaction=' . $this->transaction . '&mode=new", "messaging_new_todo",-1,-1,690,520,true,false,true,false);');
			case 'forward_todo':
				return we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
					we_html_element::jsElement('new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR+"todo_edit_todo.php?we_transaction=' . $this->transaction . '&mode=forward", "messaging_new_todo",-1,-1,690,600,true,false,true,false);');
			case 'rej_todo':
				return we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
					we_html_element::jsElement('new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR+"todo_edit_todo.php?we_transaction=' . $this->transaction . '&mode=reject", "messaging_new_todo",-1,-1,690,600,true,false,true,false);');
			case 'reset_right_view':
				return we_html_element::jsElement('
top.content.editor.edbody.entries_selected = [];
top.content.editor.edbody.messaging_messages_overview.location="' . we_class::url(WE_MESSAGING_MODULE_DIR . 'messaging_show_folder_content.php') . '";
top.content.editor.edbody.messaging_msg_view.location="about:blank"
				');
			case 'update_todo':
				if($this->messaging->selected_message){
					echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
					we_html_element::jsElement('
					new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR+"todo_update_todo.php?we_transaction=' . $this->transaction . '&mode=reject", "messaging_new_todo",-1,-1,690,600,true,false,true,false);
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
				$this->messaging->set_clipboard(we_base_request::_(we_base_request::INTLISTA, 'entrsel', []), 'copy');
				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				break;
			case 'cut_msg':
				$this->messaging->set_clipboard(we_base_request::_(we_base_request::INTLISTA, 'entrsel', []), 'cut');
				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				break;
			case 'paste_msg':
				$errs = [];
				$this->messaging->clipboard_paste($errs);
				$this->messaging->reset_ids_selected();
				$this->messaging->get_fc_data($this->messaging->Folder_ID, '', '', 0);

				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);

				$js_out = '
top.content.editor.edbody.entries_selected = [];
top.content.editor.edbody.messaging_fv_headers.location="' . we_class::url($this->frameset) . '&pnt=msg_fv_headers&si=' . $this->messaging->get_sortitem() . '&so=' . $this->messaging->get_sortorder() . '&viewclass=" + top.content.viewclass;
top.content.editor.edbody.messaging_messages_overview.location="' . we_class::url(WE_MESSAGING_MODULE_DIR . 'messaging_show_folder_content.php') . '";
top.content.editor.edbody.messaging_msg_view.location="about:blank";
				';

				$aid = $this->messaging->Folder_ID;
				$idx = isset($this->messaging->available_folders[$aid]) ? $aid : -1;
				if($idx > -1){
					$js_out .= '
					top.content.treeData.updateEntry({id:' . $aid . ',parentid: -1, text:"' . $this->messaging->available_folders[$idx]['Name'] . ' - (' . $this->messaging->get_message_count($aid) . ')", published:1});';
				}

				return $this->update_treeview($js_out);
			case 'delete_msg':
				$this->messaging->set_ids_selected(we_base_request::_(we_base_request::INTLISTA, 'entrsel', []));
				$this->messaging->delete_items();
				$this->messaging->reset_ids_selected();
				$this->messaging->get_fc_data(we_base_request::_(we_base_request::INT, 'id', 0), we_base_request::_(we_base_request::STRING, 'sort', ''), we_base_request::_(we_base_request::RAW, 'searchterm', ''), 1);

				$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
				$aid = $this->messaging->Folder_ID;

				return we_html_element::jsElement('
top.content.editor.edbody.entries_selected = [];
top.content.editor.edbody.messaging_fv_headers.location="' . we_class::url($this->frameset) . '&pnt=msg_fv_headers&si=' . $this->messaging->get_sortitem() . '&so=' . $this->messaging->get_sortorder() . '&viewclass=" + top.content.viewclass;
top.content.editor.edbody.messaging_messages_overview.location=" ' . we_class::url(WE_MESSAGING_MODULE_DIR . 'messaging_show_folder_content.php') . '";
top.content.editor.edbody.messaging_msg_view.location="about:blank";
top.content.treeData.updateEntry({id:' . $aid . ',parentid: -1, text:"' . $this->messaging->available_folders[we_messaging_messaging::array_ksearch('ID', $aid, $this->messaging->available_folders)]['Name'] . ' - (' . $this->messaging->get_message_count($aid) . ')", published:1});');

			case 'update_treeview':
				return $this->update_treeview();
			case 'update_msgs':
				$out .= $this->update_treeview();
				$blank = false;
			/* no break */
			case 'update_fcview':
				$id = $this->messaging->Folder_ID;
				$blank = isset($blank) ? $blank : true;
				if(($this->messaging->cont_from_folder != 1) && ($id != -1)){
					if(($ids = we_base_request::_(we_base_request::INTLISTA, 'entrsel', []))){
						$this->messaging->set_ids_selected($ids);
					}

					$this->messaging->get_fc_data($id, we_base_request::_(we_base_request::STRING, 'sort', ''), '', 0);

					$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
					$out .= $this->print_fc_html($blank);
				}
				return $out;
			case 'edit_folder':
				return (($mode = we_base_request::_(we_base_request::STRING, 'mode')) === 'edit' ?
						we_html_element::jsElement('
					top.content.editor.location = WE().consts.dirs.WE_MESSAGING_MODULE_DIR+"messaging_edit_folder.php?we_transaction=' . $this->transaction . '&mode=' . $mode . '&fid=' . we_base_request::_(we_base_request::INT, 'fid', -1) . '";
					') :
						'');

			case 'save_folder_settings':
				if(($id = we_base_request::_(we_base_request::INT, 'id')) !== false){
					if(($mode = we_base_request::_(we_base_request::STRING, "mode")) === 'new'){
						$parent = we_base_request::_(we_base_request::INT, 'parent_id', 0);
						$type = we_base_request::_(we_base_request::STRING, 'type');
						$out .= we_html_element::jsElement('
top.content.folder_added(' . $parent . ');
top.content.treeData.add({
	id : ' . $id . ',
	parentid : ' . $parent . ',
	text : "' . we_base_request::_(we_base_request::STRING, 'name') . ' - (0)",
	typ : "item",
	checked : false,
	contenttype : "leaf_Folder",
	table :  "' . MESSAGES_TABLE . '",
	viewclass : "' . ($type === 'we_todo' ? 'todo_folder' : 'msg_folder') . '",
	}"));' .
								we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[folder_created]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
top.content.drawTree();
');
					} else {
						$js_out = '
top.content.treeData.clear();
top.content.treeData.startloc=0;
top.content.treeData.add(top.content.node.prototype.rootEntry(0,"root","root"));';

						foreach($this->messaging->available_folders as $folder){
							if(($sf_cnt = $this->messaging->get_subfolder_count($folder['ID'])) >= 0){
								$js_out .= 'top.content.treeData.add({
	id : ' . $folder['ID'] . ',
	parentid : ' . $folder['ParentID'] . ',
	text :"' . $folder['Name'] . ' - (' . $this->messaging->get_message_count($folder['ID']) . ')",
	typ : "group",
	open :0,
	contenttype : "folder",
	leaf_count : ' . $sf_cnt . ',
	table : "' . MESSAGES_TABLE . '",
	loaded : 0,
	checked : false,
	viewclass : "' . ($folder['ClassName'] === 'we_todo' ? 'todo_folder' : 'msg_folder') . '",
});';
							} else {
								$js_out .= 'top.content.treeData.add({
	id : "' . $folder['ID'] . '",
	parentid :"' . $folder['ParentID'] . '",
	text : "' . $folder['Name'] . ' - (' . $this->messaging->get_message_count($folder['ID']) . ')",
	typ : "item",
	checked : false,
	contenttype : "folder",
	table : "' . MESSAGES_TABLE . '",
	viewclass : "' . ($folder['ClassName'] === 'we_todo' ? 'todo_folder' : 'msg_folder') . '",
	});';
							}
						}

						$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
						$js_out .= 'top.content.drawTree();';

						$out .= we_html_element::jsElement($js_out);
					}
				}
				return $out;
			case 'delete_folders':
				if(($folders = we_base_request::_(we_base_request::INTLIST, 'folders'))){
					return we_html_element::jsElement('
top.content.delete_menu_entries([' . implode(',', $folders) . ']);
top.content.folders_removed([' . implode(',', $folders) . ']);
top.content.drawTree();');
				}
				return '';
			case 'save_settings':
				if($ui){
					if($this->messaging->save_settings(array('update_interval' => $ui))){
						$out .= we_html_element::jsScript(JS_DIR . 'we_modules/messaging/messaging_std.js') .
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
				return '';
		}
	}

	//some additional methods called by .
	private function print_fc_html($blank = true){

		return we_html_element::jsElement('
top.content.editor.edbody.entries_selected = [' . $this->messaging->get_ids_selected() . '];
top.content.editor.edbody.messaging_fv_headers.location="' . we_class::url($this->frameset) . '&pnt=msg_fv_headers&si=' . $this->messaging->get_sortitem() . '&so=' . $this->messaging->get_sortorder() . '&viewclass=" + top.content.viewclass;
if (top.content.editor.edbody.messaging_messages_overview) {
	top.content.editor.edbody.messaging_messages_overview.location="' . we_class::url(WE_MESSAGING_MODULE_DIR . "messaging_show_folder_content.php") . '";
}' .
				($blank ? 'top.content.editor.edbody.messaging_msg_view.location="about:blank";' : '')
		);
	}

	private function refresh_work($blank = false){
		if(($eSel = we_base_request::_(we_base_request::INTLISTA, "entrsel", []))){
			$this->messaging->set_ids_selected($eSel);
		}

		$this->messaging->get_fc_data($this->messaging->Folder_ID, '', '', 0);
		return $this->print_fc_html($blank) . $this->update_treeview();
	}

	private function get_folder_content($id, $sort = '', array $entrsel = [], $searchterm = '', $usecache = 1){

		if($entrsel){
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

	private function update_treeview($js = ''){
		foreach($this->messaging->available_folders as $f){
			$js.='top.content.treeData.updateEntry({id:' . $f['ID'] . ', parentid:' . $f['ParentID'] . ', text:"' . $f['Name'] . ' - (' . $this->messaging->get_message_count($f['ID']) . ')", published:1});';
		}
		$js.='top.content.drawTree();';
		return we_html_element::jsElement($js);
	}

	function getMessaging(){
		return $this->messaging;
	}

}
