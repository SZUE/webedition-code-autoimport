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

	static function pJS(){
		if(defined('MESSAGING_SYSTEM')){
			$messaging = new we_messaging_messaging($_SESSION['weS']['we_data']['we_transaction']);
			$messaging->set_login_data($_SESSION['user']['ID'], $_SESSION['user']['Username']);
			$messaging->add_msgobj('we_message', 1);
			$messaging->add_msgobj('we_todo', 1);

			$newmsg_count = $messaging->used_msgobjs['we_message']->get_newmsg_count();
			$newtodo_count = $messaging->used_msgobjs['we_todo']->get_newmsg_count();
			$load = 'header_msg_update(' . $newmsg_count . ', ' . $newtodo_count . ');';
		} else {
			$load = '';
		}

		echo we_html_element::jsScript(JS_DIR . 'header_msg.js', $load);
	}

	static function pbody(){
		//start with 0 to get popup with new count
		$msg_cmd = "we_cmd('messaging_start', " . we_messaging_frames::TYPE_MESSAGE . ");";
		$todo_cmd = "we_cmd('messaging_start', " . we_messaging_frames::TYPE_TODO . ");";
		?>
		<table>
			<tr>
				<?php echo '
	<td id="msgCount" align="right" class="middlefont"><div onclick="' . $msg_cmd . '">0</div></td>
	<td>' . we_html_tools::getPixel(5, 1) . '</td>
	<td valign="bottom"><i class="fa fa-envelope-o" onclick="' . $msg_cmd . '"/></td>
</tr>
<tr>
	<td id="todoCount" align="right" class="middlefont"><div onclick="' . $todo_cmd . '">0</div></td>
	<td>' . we_html_tools::getPixel(5, 1) . '</td>
	<td valign="bottom"><i class="fa fa-tasks" alt="" onclick="' . $todo_cmd . '"/></td>'
				?>
			</tr>
		</table>
		<?php
	}

}
