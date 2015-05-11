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
		array("fa:fa-2x fa-pencil,fa-2x fa-envelope-o", "javascript:new_message('new')", false),
		array("fa:fa-2x fa-mail-reply", "javascript:new_message('re')", true),
		array("fa:fa-2x fa-copy", "javascript:copy_messages()", false),
		array("fa:fa-2x fa-scissors", "javascript:cut_messages()", false),
		array("fa:fa-2x fa-paste", "javascript:paste_messages()", false),
		array("fa:fa-2x fa-trash", "javascript:delete_messages()", false),
		array("fa:fa-2x fa-refresh", "javascript:refresh()", false),
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
		return we_html_element::jsScript(JS_DIR . 'windows.js') .
				we_html_element::jsElement('
var dirs={
	"WE_MESSAGING_MODULE_DIR":"' . WE_MESSAGING_MODULE_DIR . '",
};
var transaction="' . $this->weTransaction . '";
var g_l={
	"q_rm_todos":"' . g_l('modules_messaging', '[q_rm_todos]') . '",
	"q_rm_messages":"' . g_l('modules_messaging', '[q_rm_messages]') . '"
};
') .
				we_html_element::jsScript(WE_JS_MESSAGING_MODULE_DIR . 'messaging_iconbar.js');
	}

	private function getHTMLBody(){
		$buttons = $this->viewclass === 'todo' ? $this->buttonsTodo : $this->buttonsMsg;

		$j = 0;
		$table = new we_html_table(array('border' => 0, 'cellpadding' => 8, 'cellspacing' => 0), 1, count($buttons));
		foreach($buttons as $button){
			$table->setCol(0, $j++, array(), we_html_button::create_button($button[0], $button[1], true));
			if($button[2]){
				$table->addCol();
				$table->setCol(0, $j++, array(), '');
			}
		}

		return we_html_element::htmlBody($attribs = array('id' => 'iconBar'), $table->getHTML());
	}

}
