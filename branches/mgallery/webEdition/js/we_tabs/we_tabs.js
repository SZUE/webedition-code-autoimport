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

var titlePathName = "";
var titlePathGroup = "";

function setActiveTab(tab) {
	var tabCon = document.getElementById('tabContainer');
	docTabs = tabCon.getElementsByTagName('DIV');
	for (i = 0; i < docTabs.length; i++) {
		docTabs[i].className = "tabNormal";
	}
	document.getElementById(tab).className = "tabActive";
}


function setTabClass(elem) {
	var arr = [];
	var els = document.getElementsByTagName("*");
	for (var i = 0; i < els.length; i++) {
		if (els[i].className === "tabActive") {
			els[i].className = "tabNormal";
		}
	}
	elem.className = "tabActive";
}

function allowed_change_edit_page() {
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
}

function setTitlePath(path, group) {
	if (group) {
		titlePathGroup = group;
	}
	if (path) {
		titlePathName = path;
	}
	if ((titleElem = document.getElementById('titlePath'))) {
		titlePathName = titlePathName.replace(/</g, "&lt;").replace(/>/g, "&gt;");
		titlePathGroup = titlePathGroup.replace(/</g, "&lt;").replace(/>/g, "&gt;");
		titleElem.innerHTML = titlePathGroup + ((titlePathGroup == "/" || titlePathName === "") ? "" : "/") + titlePathName;
	}
}

function getPathInfos() {
	try {
		var contentEditor = WE().layout.weEditorFrameController.getVisibleEditorFrame();

		if (contentEditor === null && parent.frames) {
			contentEditor = parent.frames[1];
		}

		if (!contentEditor.loaded) {
			setTimeout(getPathInfos, 250);
			return;
		}

		var elem = contentEditor.document.getElementById('yuiAcInputPathName');
		if (elem) {
			titlePathName = elem.value;
		}
		elem = contentEditor.document.getElementById('yuiAcInputPathGroup');
		if (elem) {
			titlePathGroup = elem.value;
		}
	}
	catch (e) {
		// Nothing
	}
}

function setFrameSize() {
	var tabsHeight;
	if (document.getElementById('tabContainer').offsetWidth > 0) {
		if (document.getElementById('naviDiv')) {
			tabsHeight = document.getElementById('main').offsetHeight;
			document.getElementById('naviDiv').style.height = tabsHeight + "px";
			document.getElementById('contentDiv').style.top = tabsHeight + "px";
		} else if (parent.document.getElementById("edheaderDiv")) {
			tabsHeight = document.getElementById('main').offsetHeight;
			parent.document.getElementById('edheaderDiv').style.height = tabsHeight + "px";
			parent.document.getElementById('edbodyDiv').style.top = tabsHeight + "px";
		} else if (parent.document.getElementsByName('editHeaderDiv').length > 0) {
			tabsHeight = document.getElementById('main').offsetHeight;
			var tmp = parent.document.getElementsByName("editHeaderDiv");
			var nList = tmp[0].parentNode.getElementsByTagName("div");
			nList[0].style.height = tabsHeight + "px";
			nList[1].style.top = tabsHeight + "px";
			nList[2].style.top = tabsHeight + "px";
		} else if (parent.document.getElementById('updatetabsDiv')) {
			//no need to resize
		}
	} else {
		setTimeout(setFrameSize, 100);
	}
}
