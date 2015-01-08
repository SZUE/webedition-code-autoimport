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
class we_messaging_headerMsg{

	private static $messaging = 0;

	private static function start(){
		if(is_object(self::$messaging)){
			return;
		}
		self::$messaging = new we_messaging_messaging($_SESSION['weS']['we_data']["we_transaction"]);
		self::$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
		self::$messaging->add_msgobj('we_message', 1);
		self::$messaging->add_msgobj('we_todo', 1);
	}

	static function pCSS(){
		echo we_html_element::cssElement('
			table.msgheadertable {
				margin:2px 0px 1em auto;
				border-spacing:0px;
				border: none;
			}
			table.msgheadertable td {
				padding:0px;
	}
			table.msgheadertable tr {
				height: 12px;
			}
			');
	}

	static function pJS(){
		self::start();
		?>
		<script type="text/javascript"><!--

			function header_msg_update(newmsg_count, newtodo_count) {
				var msgTD = document.getElementById("msgCount");
				var todoTD = document.getElementById("todoCount");
				var changed = (newmsg_count > msgTD.firstChild.innerHTML) || (newtodo_count > todoTD.firstChild.innerHTML);
				var oldMsg = msgTD.firstChild.innerHTML;
				var oldTodo = todoTD.firstChild.innerHTML;
				msgTD.className = "middlefont" + ((newmsg_count > 0) ? "red" : "");
				todoTD.className = "middlefont" + ((newtodo_count > 0) ? "red" : "");
				msgTD.firstChild.innerHTML = newmsg_count;
				todoTD.firstChild.innerHTML = newtodo_count;
				if (changed) {
					new jsWindow("<?php echo WEBEDITION_DIR; ?>newMsg.php?msg=" + newmsg_count + "&todo=" + newtodo_count + "&omsg=" + oldMsg + "&otodo=" + oldTodo, "we_delinfo", -1, -1, 550, 200, true, true, true);
				}
			}
		<?php
		if(defined('MESSAGING_SYSTEM')){
			$newmsg_count = self::$messaging->used_msgobjs['we_message']->get_newmsg_count();
			$newtodo_count = self::$messaging->used_msgobjs['we_todo']->get_newmsg_count();
			?>

				if (top.weEditorFrameController && top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().quickstart && typeof (top.weEditorFrameController.getActiveDocumentReference().setMsgCount) == 'function' && typeof (top.weEditorFrameController.getActiveDocumentReference().setTaskCount) == 'function') {
					top.weEditorFrameController.getActiveDocumentReference().setMsgCount(<?php echo abs($newmsg_count); ?>);
					top.weEditorFrameController.getActiveDocumentReference().setTaskCount(<?php echo abs($newtodo_count); ?>);
				}
		<?php } ?>
		//-->
		</script>
		<?php
	}

	static function pbody(){
		self::start();
		//start with 0 to get popup with new count
		$msg_cmd = "we_cmd('messaging_start', " . we_messaging_frames::TYPE_MESSAGE . ");";
		$todo_cmd = "we_cmd('messaging_start', " . we_messaging_frames::TYPE_TODO . ");";
		?>
		<table class="msgheadertable">
			<?php echo '
<tr>
	<td id="msgCount" align="right" class="middlefont"><div onclick="' . $msg_cmd . '">0</div></td>
	<td>' . we_html_tools::getPixel(5, 1) . '</td>
	<td valign="bottom"><img src="' . IMAGE_DIR . 'modules/messaging/launch_messages.gif" style="width:16px;height:12px;" alt="" onclick="' . $msg_cmd . '"/></td>
</tr>
<tr>
	<td id="todoCount" align="right" class="middlefont"><div onclick="' . $todo_cmd . '">0</div></td>
	<td>' . we_html_tools::getPixel(5, 1) . '</td>
	<td valign="bottom"><img src="' . IMAGE_DIR . 'modules/messaging/launch_tasks.gif" style="width:16px;height:12px;" alt="" onclick="' . $todo_cmd . '"/></td>
</tr>'
			?>
		</table>
		<?php
	}

}
