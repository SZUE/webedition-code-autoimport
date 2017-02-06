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

if(defined('MESSAGING_SYSTEM')){
	$transact = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
	$_SESSION['weS']['we_data'][$transact] = array();

	$we_messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transact]);
	$we_messaging->add_msgobj('we_message');
	$we_messaging->saveInSession($_SESSION['weS']['we_data'][$transact]);
	$messaging_text = g_l('javaMenu_moduleInformation', '[messaging][text]') . ":";
	$new_messages = g_l('modules_messaging', '[new_messages]');
	$new_tasks = g_l('modules_messaging', '[new_tasks]');

	$messaging = new we_messaging_messaging($_SESSION['weS']['we_data']["we_transaction"]);
	$messaging->set_login_data($_SESSION['user']['ID'], $_SESSION['user']['Username']);
	$messaging->add_msgobj('we_message', 1);
	$messaging->add_msgobj('we_todo', 1);

	$newmsg_count = $messaging->used_msgobjs['we_message']->get_newmsg_count();
	$newtodo_count = $messaging->used_msgobjs['we_todo']->get_newmsg_count();

	$msg_cmd = "javascript:top.we_cmd('messaging_start'," . we_messaging_frames::TYPE_MESSAGE . ");";
	$todo_cmd = "javascript:top.we_cmd('messaging_start'," . we_messaging_frames::TYPE_TODO . ");";
	$msg_button = we_html_element::htmlA(array("href" => $msg_cmd), '<i class="fa fa-3x fa-envelope-o"></i>');
	$todo_button = we_html_element::htmlA(array("href" => $todo_cmd), '<i class="fa fa-3x fa-paperclip"></i>');
}
