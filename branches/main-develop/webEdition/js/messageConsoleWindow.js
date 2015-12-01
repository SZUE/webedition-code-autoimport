/**
 * webEdition CMS
 *
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function messageConsoleWindow(win) {

	this.win = win;
	this.doc = win.document;
	this.name = "messageConsoleWindow";

	this.notify = function (lastMessage) {
		this.addMessage(lastMessage);
	};

	/**
	 * registers this console to the messageConsole in mainWindow of webEdition
	 */
	this.register = function () {
		WE().layout.messageConsole.addObserver(this);
	};

	this.remove = function () {
		WE().layout.messageConsole.removeObserver(this);
	};

	this.addMessage = function (msg) {
		var _className,className;

		switch (msg.prio) {
			default:
				_className = "imgNoticeActive";
				className="info";
				break;

			case 2:
				_className = "imgWarningActive";
				className="lightbulb-o";
				break;
			case 4:
				_className = "imgErrorActive";
				className="exclamation-triangle";
				break;

		}

		var _li = this.doc.createElement("li");
		_li.className = "defaultfont " + _className;
		var i= this.doc.createElement("i");
		i.className="fa-li fa fa-lg active fa-"+className;
		_txt = this.doc.createTextNode(msg.message);
		_li.appendChild(i);
		_li.appendChild(_txt);

		var _pElem = this.doc.getElementById("jsMessageUl");
		if (_pElem.childNodes.length) {
			this.doc.getElementById("jsMessageUl").insertBefore(_li, _pElem.childNodes[0]);
		} else {
			this.doc.getElementById("jsMessageUl").appendChild(_li);
		}
	};

	this.init = function () {
		_messages = WE().layout.messageConsole.getMessages();
		for (i = 0; i < _messages.length; i++) {
			this.addMessage(_messages[i]);
		}
	};

	this.removeMessages = function () {
		WE().layout.messageConsole.removeMessages();
		this.doc.getElementById("jsMessageUl").innerHTML = "";
	};
}

messageConsoleWindow = new messageConsoleWindow(window);
messageConsoleWindow.register();