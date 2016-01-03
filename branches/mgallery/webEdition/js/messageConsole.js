/* global WE, top */

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

/**
 * Controller of the messageconsole in the menu frame of the mainwindow and in
 * module and tools window
 *
 * implements observer pattern
 * - the console is the subject
 * - the consoleViews in the different windows are observers
 */
WE().layout.messageConsole = {
	observers: [],
	maxAmount: 35,
	messages: [],
	addMessage: function (prio, message) {
		if (this.messages.length > 35) { // remove one message
			this.messages.shift();
		}
		this.messages.push({"prio": prio, "message": message});
		this.notifyObservers();
	},
	removeMessages: function () {
		this.messages = [];
	},
	getMessages: function (type) {
		return this.messages;
	},
	getLastMessage: function (type) {
		if (this.messages.length) {
			return this.messages[(this.messages.length - 1)];
		}
		return null;
	},
	notifyObservers: function () {
		for (i = 0; i < this.observers.length; i++) {
			try { // must try this - perhaps a frame of an observer is reloaded
				this.observers[i].notify(this.getLastMessage());
			} catch (exc) {
			}
		}
	},
	addObserver: function (observer) {
		this.removeObserver(observer); // debug reasons, remove before adding
		this.observers.push(observer);
	},
	removeObserver: function (observer) {
		_newObservers = [];
		for (i = 0; i < this.observers.length; i++) {
			if (this.observers[i].name !== observer.name) {
				_newObservers.push(this.observers[i]);
			}
		}
		this.observers = _newObservers;
	},
	openMessageConsole: function () {
		top.we_cmd("show_message_console");
	}
};

WE().layout.messageConsoleView = function (conName, win) {
	this.name = conName;
	this.win = win;

	// for disabling/hiding the messages the boxes
	this.calls = [];
	this.currentPrio = null;
};
WE().layout.messageConsoleView.prototype = {
	notify: function (_lastMessage) {
		try {
			if (this.win && this.win.document) {

				if (_lastMessage) { // if there is a lastMessage show it in the console window
					this.currentPrio = _lastMessage.prio;

					/*
					 1 => see Notices
					 2 => see Warnings
					 4 => see Errors
					 */
					switch (_lastMessage.prio) {

						case 1:
							this.win.document.getElementById("messageConsoleMessage" + this.name).innerHTML = WE().consts.g_l.message_reporting.msgNotice;
							break;
						case 2:
							this.win.document.getElementById("messageConsoleMessage" + this.name).innerHTML = WE().consts.g_l.message_reporting.msgWarning;
							break;
						case 4:
							this.win.document.getElementById("messageConsoleMessage" + this.name).innerHTML = WE().consts.g_l.message_reporting.msgError;
							break;
						default:
							this.win.document.getElementById("messageConsoleMessage" + this.name).innerHTML = WE().consts.g_l.message_reporting.msgNotice;
							break;
					}
					this.win.document.getElementById("messageConsoleMessage" + this.name).style.display = "block";
					this.switchImage(_lastMessage.prio, true);
					this.calls.push(null);

					this.win.setTimeout(this.hideMessage, 5000);
				}
			}
		} catch (e) {
			//FF raises error (can't access win)
		}
	},
	/**
	 * switches image depending on the prio of the message
	 *
	 * @param {integer} prio
	 * @param {boolean} active
	 */
	switchImage: function (prio, active) {
		var _img;
		switch (prio) {
			case 2://warning
				_img = "lightbulb-o";
				break;
			case 4://error
				_img = "exclamation-triangle";
				break;
			default://notice
				_img = "info";
				break;
		}
		this.win.document.getElementById("messageConsoleImage" + this.name).className = "fa fa-lg fa-" + _img + (active ? " active" : "");
	},
	/**
	 * Disabled the message after a certain time
	 */
	hideMessage: function () {
		this.calls.pop();

		if (this.calls.length === 0) {
			this.win.document.getElementById("messageConsoleMessage" + this.name).style.display = "none";
			this.switchImage(this.currentPrio);
		}
	},
	/**
	 * registers this console to the messageConsole in mainWindow of webEdition
	 */
	register: function () {
		WE().layout.messageConsole.addObserver(this);
	},
	unregister: function () {
		WE().layout.messageConsole.removeObserver(this);
	},
	/**
	 * opens the message console in a new window
	 */
	openMessageConsole: function () {
		WE().layout.messageConsole.openMessageConsole();
	}
};