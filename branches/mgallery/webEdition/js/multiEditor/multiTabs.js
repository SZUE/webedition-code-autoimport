/*
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

// fits the frame height on resize, add or remove tabs if the tabs wrap
function setFrameSize() {
	WE().layout.multiTabs.setFrameSize();
}

/**
 * class declaration
 * the class TabView controls the behaviort of the tabs
 * onload a instance of this class is created
 */
TabView = function (myDoc) {
	this.myDoc = myDoc;
	this.init();
};
/**
 * class TabView methods and properties
 */
TabView.prototype = {
	/**
	 * if a tab for the given frameId exists, it will be selected
	 * if not if will be added
	 */
	openTab: function (frameId, text, title) {
		if (this.myDoc.getElementById("tab_" + frameId) === undefined) {
			this.addTab(frameId, text, title);
		} else {
			this.selectTab(frameId);
		}
	},
	/**
	 * adds an new tab to the tab view
	 */
	addTab: function (frameId, text, title, pos) {
		newtab = this.tabDummy.cloneNode(true);
		newtab.innerHTML = newtab.innerHTML.replace(/###tabTextId###/g, "text_" + frameId).replace(/###modId###/g, "mod_" + frameId).replace(/###loadId###/g, "load_" + frameId).replace(/###closeId###/g, "close_" + frameId);
		newtab.id = "tab_" + frameId;
		newtab.name = "tab";
		newtab.title = title;
		newtab.className = "tabActive";
		if (pos !== undefined) {
			if (this.tabContainer.childNodes.length > pos) {
				this.tabContainer.insertBefore(newtab, this.tabContainer.childNodes[pos]);
			} else {
				pos = undefined;
			}
		}
		if (pos === undefined) {
			this.tabContainer.appendChild(newtab);
		}
		this.setText(frameId, text);
		this.setTitle(frameId, title);
		this.selectTab(frameId);
	},
	/**
	 * controls the click on the close button
	 */
	onCloseTab: function (val) {
		frameId = (typeof val) == "object" ? val.id.replace(/close_/g, "") : val;
		WE().layout.weEditorFrameController.closeDocument(frameId);
	},
	/**
	 * removes a tab from the tab view
	 */
	closeTab: function (frameId) {
		this.tabContainer.removeChild(this.myDoc.getElementById('tab_' + frameId));
		if (this.activeTab == frameId) {
			this.activeTab = null;
		}
		this.setFrameSize();
		this.contentType[frameId] = "";
	},
	/**
	 * selects a tab (set style for selected tabs)
	 */
	selectTab: function (frameId) {
		this.deselectAll();
		if (this.activeTab !== null) {
			this.deselectTab(this.activeTab);
		}
		if (this.myDoc.getElementById('tab_' + frameId) && typeof (this.myDoc.getElementById('tab_' + frameId)) == "object") {
			this.myDoc.getElementById('tab_' + frameId).className = 'tabActive';
		}
		this.activeTab = frameId;
	},
	/**
	 * deselects a tab (set style for deselected tabs)
	 */
	deselectTab: function (frameId) {
		if (this.myDoc.getElementById('tab_' + frameId)) {
			this.myDoc.getElementById('tab_' + frameId).className = "tab";
		}
	},
	/**
	 * deselects all tab (set style for deselected tabs to all tabs)
	 */
	deselectAll: function () {
		tabs = this.myDoc.getElementsByName("tab");
		for (i = 0; tabs.length; i++) {
			tabs[i].className = "tab";
		}
	},
	/**
	 * sets the tab label
	 */
	setText: function (frameId, val) {
		text = this.myDoc.getElementById('text_' + frameId);
		if (text) {
			text.innerHTML = val;
			this.setFrameSize();
		}
	},
	setTextClass: function (frameId, classname) {
		text = this.myDoc.getElementById('text_' + frameId);
		if (classname) {
			text.className = "text " + classname;
		}
	},
	/**
	 * sets the tab title
	 */
	setTitle: function (frameId, val) {
		title = this.myDoc.getElementById('tab_' + frameId);
		if (title) {
			title.title = val;
		}
	},
	/**
	 * sets the id to the icon
	 */
	setId: function (frameId, val) {
		this.myDoc.getElementById('load_' + frameId).title = val;
	},
	/**
	 * marks a tab as modified an not safed
	 */
	setModified: function (frameId, modified) {
		this.myDoc.getElementById('mod_' + frameId).style.visibility = (modified ?
						"visible" :
						"hidden");
	},
	/**
	 * displays the loading loading icon
	 */
	setLoading: function (frameId, loading) {
		if (loading) {
			this.myDoc.getElementById('load_' + frameId).innerHTML = '<span class="fa-stack fa-lg fileicon"><i class="fa fa-2x fa-spinner fa-pulse"></i></span>';
		} else {
			var _text = this.myDoc.getElementById('text_' + frameId).innerHTML;
			var _ext = _text ? _text.replace(/^.*\./, ".") : "";
			this.myDoc.getElementById('load_' + frameId).innerHTML = WE().util.getTreeIcon(this.contentType[frameId], false, _ext);
		}
	},
	/**
	 * displays the content type icon
	 */
	setContentType: function (frameId, contentType) {
		this.contentType[frameId] = contentType;
		this.setLoading(frameId, false);
	},
	/**
	 * controls the click on a tab
	 */
	selectFrame: function (val) {
		frameId = (typeof val) == "object" ? val.id.replace(/tab_/g, "") : val;
		WE().layout.weEditorFrameController.showEditor(frameId);
		//this.selectTab(frameId);
	},
	setFrameSize: function () {
		tabsHeight = (this.myDoc.getElementById('tabContainer').clientHeight ? (this.myDoc.getElementById('tabContainer').clientHeight) : (this.myDoc.body.clientHeight));
		tabsHeight = tabsHeight < 24 ? 24 : tabsHeight;
		this.myDoc.getElementById('multiEditorDocumentTabsFrameDiv').style.height = tabsHeight + "px";
		this.myDoc.getElementById('multiEditorEditorFramesetsDiv').style.top = tabsHeight + "px";
	},
	/**
	 * inits some vars
	 */
	init: function () {
		this.tabs = [];
		this.frames = [];
		this.activeTab = null;
		this.tabContainer = this.myDoc.getElementById('tabContainer');
		this.tabDummy = this.myDoc.getElementById('tabDummy');
		this.contentType = [];
	}
};
