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
		array("fa:btn_messages_create,fa-lg fa-pencil", "javascript:new_message('new')", false),
		array("fa:btn_messages_reply,fa-lg fa-mail-reply", "javascript:new_message('re')", true),
		array("fa:btn_messages_copy,fa-lg fa-copy", "javascript:copy_messages()", false),
		array("fa:btn_messages_cut,fa-lg fa-scissors", "javascript:cut_messages()", false),
		array("fa:btn_messages_paste,fa-lg fa-paste", "javascript:paste_messages()", false),
		array("fa:btn_messages_trash,fa-lg fa-trash-o", "javascript:delete_messages(false)", false),
		array("fa:btn_messages_update,fa-lg fa-refresh", "javascript:refresh()", false),
		array("fa:btn_messages_tasks,fa-lg fa-long-arrow-right,fa-lg fa-tasks", "javascript:launch_todo()", false)
	);
	private $buttonsTodo = array(
		array("fa:btn_task_create,fa-lg fa-pencil", "javascript:new_todo()", false),
		array("fa:btn_task_forward,fa-lg fa-mail-forward", "javascript:forward_todo()", false),
		array("fa:btn_task_reject,fa-lg fa-mail-reply,fa-lg fa-tasks", "javascript:reject_todo()", false),
		array("fa:btn_task_status,fa-lg fa-question,,fa-lg fa-tasks", "javascript:update_todo()", true),
		array("fa:btn_task_copy,fa-lg fa-copy", "javascript:copy_messages()", false),
		array("fa:btn_task_cut,fa-lg fa-scissors", "javascript:cut_messages()", false),
		array("fa:btn_task_paste,fa-lg fa-paste", "javascript:paste_messages()", false),
		array("fa:btn_task_trash,fa-lg fa-trash-o", "javascript:delete_messages(true)", false),
		array("fa:btn_task_update,fa-lg fa-refresh", "javascript:refresh()", false),
		array("fa:btn_task_messages,fa-lg fa-long-arrow-right,fa-lg fa-envelope-o", "javascript:launch_msg()", false)
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
		return we_html_element::jsElement('
var transaction="' . $this->weTransaction . '";
') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . 'messaging/messaging_iconbar.js');
	}

	private function getHTMLBody(){
		$buttons = $this->viewclass === 'todo' ? $this->buttonsTodo : $this->buttonsMsg;

		$j = 0;
		$table = new we_html_table(array('class' => 'iconBar'), 1, count($buttons));
		foreach($buttons as $button){
			$table->setCol(0, $j++, [], we_html_button::create_button($button[0], $button[1], true));
			if($button[2]){
				$table->addCol();
				$table->setCol(0, $j++, [], '');
			}
		}

		return we_html_element::htmlBody($attribs = array('id' => 'iconBar'), $table->getHTML());
	}

}
