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

	if (top.weEditorFrameController &&
					top.weEditorFrameController.getActiveDocumentReference() &&
					top.weEditorFrameController.getActiveDocumentReference().quickstart &&
					typeof (top.weEditorFrameController.getActiveDocumentReference().setMsgCount) == 'function' &&
					typeof (top.weEditorFrameController.getActiveDocumentReference().setTaskCount) == 'function') {
		top.weEditorFrameController.getActiveDocumentReference().setMsgCount(newmsg_count);
		top.weEditorFrameController.getActiveDocumentReference().setTaskCount(newtodo_count);
	}
	if (changed) {
		new jsWindow(dirs.WEBEDITION_DIR + "newMsg.php?msg=" + newmsg_count + "&todo=" + newtodo_count + "&omsg=" + oldMsg + "&otodo=" + oldTodo, "we_delinfo", -1, -1, 550, 200, true, true, true);
	}
}
