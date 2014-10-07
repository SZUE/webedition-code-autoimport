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
//TODO: make weModuleIconbar.class for all Iconbars and let weMessagingIconbar inherit from it

class we_messaging_iconbar{
	private $parentFrameset;
	private $weTransaction;
	private $viewclass;
	private $buttonsMsg = array(
		array("image:btn_messages_create", "javascript:new_message('new')", false),
		array("image:btn_messages_reply", "javascript:new_message('re')", true),
		array("image:btn_messages_copy", "javascript:copy_messages()", false),
		array("image:btn_messages_cut", "javascript:cut_messages()", false),
		array("image:btn_messages_paste", "javascript:paste_messages()", false),
		array("image:btn_messages_trash", "javascript:delete_messages()", false),
		array("image:btn_messages_update", "javascript:refresh()", false),
		array("image:btn_messages_tasks", "javascript:launch_todo()", false)
	);
	private $buttonsTodo = array(
		array("image:btn_task_create", "javascript:new_todo()", false),
		array("image:btn_task_forward", "javascript:forward_todo()", false),
		array("image:btn_task_reject", "javascript:reject_todo()", false),
		array("image:btn_task_status", "javascript:update_todo()", true),
		array("image:btn_task_copy", "javascript:copy_messages()", false),
		array("image:btn_task_cut", "javascript:cut_messages()", false),
		array("image:btn_task_paste", "javascript:paste_messages()", false),
		array("image:btn_task_trash", "javascript:delete_messages()", false),
		array("image:btn_task_update", "javascript:refresh()", false),
		array("image:btn_task_messages", "javascript:launch_msg()", false)
	);

	public function __construct($parentFrameset){
		$this->parentFrameset = $parentFrameset;
		$this->weTransaction = $this->parentFrameset->weTransaction;
		$this->viewclass = $this->parentFrameset->viewclass;
	}

	public function getHTML(){
		return $this->parentFrameset->getHTMLDocument($this->getHTMLBody(), $this->getJSCode());
	}

	private function getJSCode(){
		return we_html_element::jsScript(JS_DIR . 'windows.js') . ($this->viewclass === 'todo' ? $this->getJSCodeTodo() : $this->getJSCodeMsg());
	}

	private function getJSCodeTodo(){
		return we_html_element::jsElement('
			function new_todo() {
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_edit_todo.php?we_transaction=' . $this->weTransaction . '&mode=new", "messaging_new_todo",-1,-1,690,520,true,false,true,false);
			}

			function forward_todo() {
				if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
					new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_edit_todo.php?we_transaction=' . $this->weTransaction . '&mode=forward", "messaging_new_todo",-1,-1,705,600,true,true,true,false);
				}
			}

			function reject_todo() {
				if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
					new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_edit_todo.php?we_transaction=' . $this->weTransaction . '&mode=reject", "messaging_new_todo",-1,-1,690,600,true,false,true,false);
				}
			}

			function update_todo() {
				if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
					new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'todo_update_todo.php?we_transaction=' . $this->weTransaction . '&mode=reject", "messaging_new_todo",-1,-1,705,600,true,true,true,false);
				}
			}

			function copy_messages() {
				if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
					top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&we_transaction=' . $this->weTransaction . '&mcmd=copy_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
				}
			}

			function cut_messages() {
				if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
					top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&we_transaction=' . $this->weTransaction . '&mcmd=cut_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
				}
			}

			function paste_messages() {
				if (top.content.editor.edbody.entries_selected) {
					top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&we_transaction=' . $this->weTransaction . '&mcmd=paste_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
				}
			}

			function delete_messages() {
				if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
					c = confirm("' . g_l('modules_messaging', '[q_rm_todos]') . '");
					if (c == false) {
						return;
					}
					top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&we_transaction=' . $this->weTransaction . '&mcmd=delete_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
				}
			}

			function refresh() {
				top.content.update_messaging();
			}

			function launch_msg() {
				if (top.content.editor.edbody.entries_selected) {
					top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&mcmd=launch&mode=message&we_transaction=' . $this->weTransaction . '";
				}
			}
		');
	}

	private function getJSCodeMsg(){

		return we_html_element::jsElement('
			function new_message(mode) {
				if (mode == "re" && (top.content.editor.edbody.last_entry_selected == -1)) {
					return;
				}
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'messaging_newmessage.php?we_transaction=' . $this->weTransaction . '&mode=" + mode, "messaging_new_message",-1,-1,670,530,true,false,true,false);
			}

			function copy_messages() {
				if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
					top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&we_transaction=' . $this->weTransaction . '&mcmd=copy_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
				}
			}

			function cut_messages() {
				if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
					top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&we_transaction=' . $this->weTransaction . '&mcmd=cut_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
				}
			}

			function paste_messages() {
				if (top.content.editor.edbody.entries_selected) {
					top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&we_transaction=' . $this->weTransaction . '&mcmd=paste_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
				}
			}

			function delete_messages() {
				if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
					c = confirm("' . g_l('modules_messaging', '[q_rm_messages]') . '");
					if (c == false) {
						return;
					}
					top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&we_transaction=' . $this->weTransaction . '&mcmd=delete_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
				}
			}

			function refresh() {
				top.content.update_messaging();
			}

			function launch_todo() {
				if (top.content.editor.edbody.entries_selected) {
					top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&mcmd=launch&mode=todo&we_transaction=' . $this->weTransaction . '";
				}
			}
		');
	}

	private function getHTMLBody(){
		$buttons = $this->viewclass === 'todo' ? $this->buttonsTodo : $this->buttonsMsg;

		$j = 0;
		$table = new we_html_table(array('border' => 0, 'cellpadding' => 8, 'cellspacing' => 0, 'width' => 'auto', 'style' => 'margin-top: 5px'), 1, count($buttons));
		foreach($buttons as $button){
			$table->setCol(0, $j++, array('width' => 36), we_html_button::create_button($button[0], $button[1], true));
			if($button[2]){
				$table->addCol();
				$table->setCol(0, $j++, array('width' => 36), '');
			}
		}

		return we_html_element::htmlBody($attribs = array('background' => IMAGE_DIR . 'backgrounds/iconbarBack.gif'), $table->getHTML());
	}

}
