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

var resizeDummy = 1;
var titlePathName = "";
var titlePathGroup = "";
var hasPathName = false;
var hasPathGroup = false;

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
		if (els[i].className == "tabActive") {
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

function setTitlePath() {
	if ((titleElem = document.getElementById('titlePath'))) {
		titlePathName = titlePathName.replace(/</g, "&lt;");
		titlePathName = titlePathName.replace(/>/g, "&gt;");
		titlePathGroup = titlePathGroup.replace(/</g, "&lt;");
		titlePathGroup = titlePathGroup.replace(/>/g, "&gt;");
		titleElem.innerHTML = titlePathGroup + ((titlePathGroup == "/" || titlePathName === "") ? "" : "/") + titlePathName;
	}
}

function setPathName(pathName) {
	if (hasPathName)
		titlePathName = pathName;
}

function setPathGroup(pathGroup) {
	if (hasPathGroup)
		titlePathGroup = pathGroup;
}


var loop = 0;
function getPathInfos() {
	try {
		var contentEditor = WE().layout.weEditorFrameController.getVisibleEditorFrame();

		if (contentEditor === null && parent.frames) {
			contentEditor = parent.frames[1];
		}

		if (contentEditor.loaded) {
			if ((pathNameElem = contentEditor.document.getElementById('yuiAcInputPathName'))) {
				hasPathName = true;
				titlePathName = pathNameElem.value;
			}
			if ((pathGroupElem = contentEditor.document.getElementById('yuiAcInputPathGroup'))) {
				hasPathGroup = true;
				titlePathGroup = pathGroupElem.value;
			}
			loop = 0;
		} else if (loop < 10) {
			loop++;
			setTimeout(getPathInfos, 250);
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
		} else if (parent.document.getElementsByTagName("FRAMESET").length) {
			//FIXME: remove this if frames are obsolete
			var fs = parent.document.getElementsByTagName("FRAMESET")[0];
			//document.getElementById('main').style.overflow = "hidden";
			tabsHeight = document.getElementById('main').offsetHeight;
			var fsRows = fs.rows.split(',');
			fsRows[0] = tabsHeight;
			fs.rows = fsRows.join(",");
		}
	} else {
		setTimeout(setFrameSize, 100);
	}
}
