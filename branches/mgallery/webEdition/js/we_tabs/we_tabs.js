/* global WE */

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

WE().layout.we_tabs = function (doc) {
	this.doc = doc;
	this.titlePathName = "";
	this.titlePathGroup = "";
};

WE().layout.we_tabs.prototype = {
	setActiveTab: function (tab) {
		var tabCon = this.doc.getElementById('tabContainer');
		docTabs = tabCon.getElementsByTagName('DIV');
		for (i = 0; i < docTabs.length; i++) {
			docTabs[i].className = "tabNormal";
		}
		this.doc.getElementById(tab).className = "tabActive";
	},
	setTabClass: function (elem) {
		var arr = [];
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
			if (contentEditor && contentEditor.contentWindow.fields_are_valid) {
				return contentEditor.contentWindow.fields_are_valid();
			}
		}
		catch (e) {
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
			} else if (this.doc.parent.document.getElementById("edheaderDiv")) {
				tabsHeight = this.doc.getElementById('main').offsetHeight;
				this.doc.parent.document.getElementById('edheaderDiv').style.height = tabsHeight + "px";
				this.doc.parent.document.getElementById('edbodyDiv').style.top = tabsHeight + "px";
			} else if (this.doc.parent.document.getElementsByName('editHeaderDiv').length > 0) {
				tabsHeight = this.doc.getElementById('main').offsetHeight;
				var tmp = this.doc.parent.document.getElementsByName("editHeaderDiv");
				var nList = tmp[0].parentNode.getElementsByTagName("div");
				nList[0].style.height = tabsHeight + "px";
				nList[1].style.top = tabsHeight + "px";
				nList[2].style.top = tabsHeight + "px";
			} else if (this.doc.parent.document.getElementById('updatetabsDiv')) {
				//no need to resize
			}
		}
	}
};