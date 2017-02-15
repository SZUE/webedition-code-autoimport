/*
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
/* global WE */
'use strict';

var weNavigationHistory = function () {

	this.documentHistory = [];
	this.currentIndex = -1;
	this.saveInHistory = true;
	this.addDocToHistory = function (table, id, ct, editcmd, url, parameters) {

		if (this.saveInHistory) {

			if (this.currentIndex != (this.documentHistory.length - 1)) { // reset navigation History when needed

				do {
					this.documentHistory.pop();
				} while (this.currentIndex < (this.documentHistory.length - 1));
// resave document array
				this.documentHistory = [];
			}

			this.documentHistory.push(new NavigationHistoryEntry(table, id, ct, editcmd, url, parameters));
			while (this.documentHistory.length > 50) {
				this.documentHistory.shift();
			}

			this.currentIndex = (this.documentHistory.length - 1);
		}
		this.saveInHistory = true;
	};

	this.navigateBack = function () {
		if (this.documentHistory.length) {
			if (this.currentIndex > 0) {
				this.saveInHistory = false;
				this.currentIndex--;
				if (!this.documentHistory[this.currentIndex].executeHistoryEntry()) {
					this.navigateBack();
				}
			} else {
				top.we_showMessage(WE().consts.g_l.main.nav_first_document, WE().consts.message.WE_MESSAGE_NOTICE, window);
			}
		} else {
			this.getNoDocumentMessage();
		}
	};

	this.navigateNext = function () {
		if (this.documentHistory.length) {
			if (this.currentIndex < (this.documentHistory.length - 1)) {
				this.currentIndex++;
				this.saveInHistory = false;
				if (!this.documentHistory[this.currentIndex].executeHistoryEntry()) {
					this.navigateNext();
				}
			} else {
				top.we_showMessage(WE().consts.g_l.main.nav_last_document, WE().consts.message.WE_MESSAGE_NOTICE, window);
			}
		} else {
			this.getNoDocumentMessage();
		}
	};

	this.navigateReload = function () {
		if (this.documentHistory.length) {
			var _currentEditor;
			if ((_currentEditor = WE().layout.weEditorFrameController.getActiveEditorFrame())) { // reload current Editor
				_currentEditor.setEditorReloadAllNeeded(true);
				_currentEditor.setEditorIsActive(true);
			} else { // reopen current Editor
				top.we_showMessage(WE().consts.g_l.main.nav_no_open_document, WE().consts.message.WE_MESSAGE_NOTICE, window);
// this.saveInHistory = false;
// this.documentHistory[this.currentIndex].executeHistoryEntry();

			}
		} else {
			this.getNoDocumentMessage();
		}
	};

	this.getNoDocumentMessage = function () {
		top.we_showMessage(WE().consts.g_l.main.nav_no_entry, WE().consts.message.WE_MESSAGE_NOTICE, window);
	};
};

var NavigationHistoryEntry = function (table, id, ct, editcmd, url, parameters) {

	this.table = table;
	this.id = id;
	this.ct = ct;
	this.editcmd = editcmd;
	this.url = url;
	this.parameters = parameters;
	this.executeHistoryEntry = function () {

		if (this.editcmd || (this.id && this.id != "0")) {
			WE().layout.weEditorFrameController.openDocument(
				this.table,
				this.id,
				this.ct,
				this.editcmd,
				'',
				this.url,
				'',
				'',
				this.parameters
				);
			return true;
		}
		return false;
	};
};