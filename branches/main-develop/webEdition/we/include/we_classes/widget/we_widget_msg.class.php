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
class we_widget_msg extends we_widget_base{
	private $count = [
		'msg' => 0,
		'todo' => 0
	];

	public function __construct($curID = '', $aProps = []){
		if(!defined('MESSAGING_SYSTEM')){
			return;
		}
		$transact = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
		$_SESSION['weS']['we_data'][$transact] = [];

		$we_messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transact]);
		$we_messaging->add_msgobj('we_message');
		$we_messaging->saveInSession($_SESSION['weS']['we_data'][$transact]);

		$messaging = new we_messaging_messaging($_SESSION['weS']['we_data']["we_transaction"]);
		$messaging->set_login_data($_SESSION['user']['ID'], $_SESSION['user']['Username']);
		$messaging->add_msgobj('we_message', 1);
		$messaging->add_msgobj('we_todo', 1);

		$this->count['msg'] = $messaging->used_msgobjs['we_message']->get_newmsg_count();
		$this->count['todo'] = $messaging->used_msgobjs['we_todo']->get_newmsg_count();
	}

	public function getInsertDiv($iCurrId, $iWidth){
		$msg_cmd = "javascript:top.we_cmd('messaging_start'," . we_messaging_frames::TYPE_MESSAGE . ");";
		$todo_cmd = "javascript:top.we_cmd('messaging_start'," . we_messaging_frames::TYPE_TODO . ");";
		$oTblCont = new we_html_table(["id" => "m_" . $iCurrId . "_inline", 'style' => "width:100%;",], 2, 2);
		$oTblCont->setCol(0, 0, ["width" => 34, 'style' => 'vertical-align:middle;', "class" => "middlefont"], we_html_element::htmlA(["href" => $msg_cmd], '<i class="fa fa-3x fa-envelope-o"></i>'));
		$oTblCont->setCol(0, 1, ['style' => 'vertical-align:middle;'], we_html_element::htmlA(["href" => $msg_cmd,
				"class" => "middlefont bold",
				'style' => "text-decoration:none;"
				], g_l('modules_messaging', '[new_messages]') . " (" . we_html_element::htmlSpan(["id" => "msg_count"
					], $this->count['msg']) . ")"));
		$oTblCont->setCol(1, 0, ["width" => 34, 'style' => 'vertical-align:middle;', "class" => "middlefont"], we_html_element::htmlA(["href" => $todo_cmd], '<i class="fa fa-3x fa-paperclip"></i>'));
		$oTblCont->setCol(1, 1, ['style' => 'vertical-align:middle;'], we_html_element::htmlA(["href" => $todo_cmd,
				"class" => "middlefont bold",
				'style' => "text-decoration:none;"
				], g_l('modules_messaging', '[new_tasks]') . " (" . we_html_element::htmlSpan(["id" => "task_count"
					], $this->count['todo']) . ")"));
		$aLang = [g_l('cockpit', '[messaging]'), ""];
		$oTblDiv = $oTblCont->getHtml();
		return [$oTblDiv, $aLang];
	}

	public static function getDefaultConfig(){
		return [
			'width' => self::WIDTH_SMALL,
			'height' => 100,
			'res' => 0,
			'cls' => 'lightCyan',
			'csv' => '',
			'dlgHeight' => 140,
			'isResizable' => 1
		];
	}

	public static function showDialog(){
		list($jsFile, $oSelCls) = self::getDialogPrefs();
		$parts = [
			["headline" => "", "html" => $oSelCls->getHTML(),]
		];

		$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();");
		$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();");
		$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
		$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

		$sTblWidget = we_html_multiIconBox::getHTML("rssProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[messaging]'));

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[messaging]'), '', '', $jsFile .
			we_html_element::jsScript(JS_DIR . 'widgets/msg.js'), we_html_element::htmlBody(
				["class" => "weDialogBody", "onload" => "init();"], we_html_element::htmlForm("", $sTblWidget)));
	}

	public function showPreview(){
//nothing to do
	}

}
