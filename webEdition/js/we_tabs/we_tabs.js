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
'use strict';

WE().layout.we_tabs = function (doc, win) {
	this.doc = doc;
	this.win = win;
	this.titlePathName = "";
	this.titlePathGroup = "";
};

WE().layout.we_tabs.prototype = {
	setActiveTab: function (tab) {
		var tabCon = this.doc.getElementById('tabContainer');
		var docTabs = tabCon.getElementsByTagName('DIV');
		for (var i = 0; i < docTabs.length; i++) {
			docTabs[i].className = "tabNormal";
		}
		var obj = this.doc.getElementById(tab);
		if (obj) {
			obj.className = "tabActive";
		}
	},
	setTabClass: function (elem) {
		var els = this.doc.getElementsByTagName("*");
		for (var i = 0; i < els.length; i++) {
			if (els[i].className === "tabActive") {
				els[i].className = "tabNormal";
			}
		}
		elem.className = "tabActive";
	},
	allowed_change_edit_page: function () {
		try {
			var contentEditor = WE().layout.weEditorFrameController.getVisibleEditorFrame();
			if (contentEditor && contentEditor.fields_are_valid) {
				return contentEditor.fields_are_valid();
			}
		} catch (e) {
			// Nothing
		}
		return true;
	},
	setTitlePath: function (path, group) {
		if (group) {
			this.titlePathGroup = group;
		}
		if (path) {
			this.titlePathName = path;
		}
		var titleElem = this.doc.getElementById('titlePath');
		if (titleElem) {
			this.titlePathName = this.titlePathName.replace(/</g, "&lt;").replace(/>/g, "&gt;");
			this.titlePathGroup = this.titlePathGroup.replace(/</g, "&lt;").replace(/>/g, "&gt;");
			titleElem.innerHTML = this.titlePathGroup + ((this.titlePathGroup == "/" || this.titlePathName === "") ? "" : "/") + this.titlePathName;
		}
	},
	setFrameSize: function () {
		var tabsHeight;
		if (this.doc.getElementById('tabContainer').offsetWidth > 0) {
			if (this.doc.getElementById('naviDiv')) {
				tabsHeight = this.doc.getElementById('main').offsetHeight;
				this.doc.getElementById('naviDiv').style.height = tabsHeight + "px";
				this.doc.getElementById('contentDiv').style.top = tabsHeight + "px";
			} //no else since in modules both can exist
			if (this.win.parent) {
				if (this.win.parent.document.getElementById("edheaderDiv")) {
					tabsHeight = this.doc.getElementById('main').offsetHeight;
					this.win.parent.document.getElementById('edheaderDiv').style.height = tabsHeight + "px";
					this.win.parent.document.getElementById('edbodyDiv').style.top = tabsHeight + "px";
				} else if (this.win.parent.document.getElementsByName('editHeaderDiv').length > 0) {
					tabsHeight = this.doc.getElementById('main').offsetHeight;
					var tmp = this.win.parent.document.getElementsByName("editHeaderDiv");
					var nList = tmp[0].parentNode.getElementsByTagName("div");
					nList[0].style.height = tabsHeight + "px";
					nList[1].style.top = tabsHeight + "px";
					nList[2].style.top = tabsHeight + "px";
				} else if (this.win.parent.document.getElementById('updatetabsDiv')) {
					//no need to resize
				}
			} else {
				WE().t_e('no parent', this.doc);
			}
		}
	},
	clickHandler: function (win, elem, cmd) {
		if (this.allowed_change_edit_page()) {
			this.setTabClass(elem);
			win.setTab(cmd);
		} else {
			WE().util.showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	}
};