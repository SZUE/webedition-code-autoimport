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
	msgTD.className = "middlefont " + ((newmsg_count > 0) ? "highlightElementChanged" : "");
	todoTD.className = "middlefont " + ((newtodo_count > 0) ? "highlightElementChanged" : "");
	msgTD.firstChild.innerHTML = newmsg_count;
	todoTD.firstChild.innerHTML = newtodo_count;
	var control = WE().layout.weEditorFrameController;
	if (control && control.getActiveDocumentReference() &&
					control.getActiveDocumentReference().quickstart &&
					typeof (control.getActiveDocumentReference().setMsgCount) == 'function' &&
					typeof (control.getActiveDocumentReference().setTaskCount) == 'function') {
		control.getActiveDocumentReference().setMsgCount(newmsg_count);
		control.getActiveDocumentReference().setTaskCount(newtodo_count);
	}
	if (changed) {
		new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=newMsg&msg=" + newmsg_count + "&todo=" + newtodo_count + "&omsg=" + oldMsg + "&otodo=" + oldTodo, "we_delinfo", -1, -1, 550, 200, true, true, true);
	}
}
