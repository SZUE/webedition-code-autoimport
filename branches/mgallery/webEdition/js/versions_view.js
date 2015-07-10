/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 9432 $
 * $Author: mokraemer $
 * $Date: 2015-02-28 13:43:00 +0100 (Sa, 28. Feb 2015) $
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
var ajaxURL = "/webEdition/rpc/rpc.php";

function init() {
	sizeScrollContent();
}

function printScreen() {

	var scrollContent = document.getElementById("scrollContent");
	var hScrollContent = scrollContent.innerHeight ? scrollContent.innerHeight : scrollContent.offsetHeight;

	var contentTable = document.getElementById("contentTable");
	var hContentTable = contentTable.innerHeight ? contentTable.innerHeight : contentTable.offsetHeight;

	//hContentTable = hContentTable-500;

	scrollContent.style.height = hContentTable + "px";
	window.print();

	setTimeout(function () {
		setCrollContent(hScrollContent);
	}, 2000);
}

function setCrollContent(hScrollContent) {
	var scrollContent = document.getElementById("scrollContent");
	scrollContent.style.height = hScrollContent + "px";
}


var ajaxCallbackResultList = {
	success: function (o) {
		if (o.responseText !== undefined && o.responseText != "") {
			document.getElementById("scrollContent").innerHTML = o.responseText;
			makeAjaxRequestParametersTop();
			makeAjaxRequestParametersBottom();
		}
	},
	failure: function (o) {
	}
}

var ajaxCallbackParametersTop = {
	success: function (o) {
		if (o.responseText !== undefined && o.responseText != "") {
			document.getElementById("parametersTop").innerHTML = o.responseText;
		}
	},
	failure: function (o) {
	}
}
var ajaxCallbackParametersBottom = {
	success: function (o) {
		if (o.responseText !== undefined && o.responseText != "") {
			document.getElementById("parametersBottom").innerHTML = o.responseText;
		}
	},
	failure: function (o) {
	}
}

function search(newSearch) {

	if (newSearch) {
		document.we_form.searchstart.value = 0;
	}
	makeAjaxRequestDoclist();

}

var ajaxCallbackDeleteVersion = {
	success: function (o) {
	},
	failure: function (o) {
	}
}

function deleteVersionAjax() {
	var args = "";
	var check = "";
	var newString = "";
	var checkboxes = document.getElementsByName("deleteVersion");
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) {
			if (check != "")
				check += ",";
			check += checkboxes[i].value;
			newString = checkboxes[i].name;
		}
	}
	args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(check);
	var scroll = document.getElementById("scrollContent");
	scroll.innerHTML = "<table border='0' width='100%' height='100%'><tr><td align='center'><i class=\"fa fa-2x fa-spinner fa-pulse\"></i></td></tr></table>";

	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackDeleteVersion, "protocol=json&cns=versionlist&cmd=DeleteVersion&" + args + "");
}

function previewVersion(ID) {
	top.we_cmd("versions_preview", ID, 0);
}

var msBack = 0;
var diffBack = 0;
var msNext = 0;
var diffNext = 0;

function next(anzahl) {
	var zeit = new Date();
	if (msBack != 0) {
		diffBack = zeit.getTime() - msBack;
	}
	msBack = zeit.getTime();
	if (diffBack > 1000 || diffBack == 0) {
		document.we_form.elements.searchstart.value = parseInt(document.we_form.elements.searchstart.value) + anzahl;

		search(false);
	}
}

function back(anzahl) {
	var zeit = new Date();
	if (msNext != 0) {
		diffNext = zeit.getTime() - msNext;
	}
	msNext = zeit.getTime();
	if (diffNext > 1000 || diffNext == 0) {
		document.we_form.elements.searchstart.value = parseInt(document.we_form.elements.searchstart.value) - anzahl;
		search(false);
	}
}

function setOrder(order) {
	columns = ["version", "modifierID", "timestamp"];
	for (var i = 0; i < columns.length; i++) {
		if (order != columns[i]) {
			deleteArrow = document.getElementById("" + columns[i] + "");
			deleteArrow.innerHTML = "";
		}
	}
	arrow = document.getElementById("" + order + "");
	orderVal = document.we_form.elements.order.value;

	if (order + " DESC" == orderVal) {
		document.we_form.elements.order.value = order;
		arrow.innerHTML = "<i class=\"fa fa-sort-asc fa-lg\"></i>";
	} else {
		document.we_form.elements.order.value = order + " DESC";
		arrow.innerHTML = "<i class=\"fa fa-sort-desc fa-lg\"></i>";
	}
	search(false);
}

function calendarSetup(x) {
	for (i = 0; i < x; i++) {
		if (document.getElementById("date_picker_from" + i + "") != null) {
			Calendar.setup({inputField: "search[" + i + "]", ifFormat: "%d.%m.%Y", button: "date_picker_from" + i + "", align: "Tl", singleClick: true});
		}
	}
}

function delRow(id) {
	var scrollContent = document.getElementById("scrollContent");
	scrollContent.style.height = scrollContent.offsetHeight + 26 + "px";

	var elem = document.getElementById("filterTable");
	if (elem) {
		trows = elem.rows;
		rowID = "filterRow_" + id;

		for (i = 0; i < trows.length; i++) {
			if (rowID == trows[i].id) {
				elem.deleteRow(i);
			}
		}
	}
}

function resetVersion(id, documentID, version, table) {
	Check = confirm(g_l.resetVersions);
	if (Check == true) {
		if (document.getElementById("publishVersion_" + id) != null) {
			if (document.getElementById("publishVersion_" + id).checked) {
				id += "___1";
			} else {
				id += "___0";
			}
		}
		resetVersionAjax(id, documentID, version, table);
	}
}

function switchSearch(mode) {
	document.we_form.mode.value = mode;
	var defSearch = document.getElementById("defSearch");
	var advSearch = document.getElementById("advSearch");
	var advSearch2 = document.getElementById("advSearch2");
	var advSearch3 = document.getElementById("advSearch3");
	var scrollContent = document.getElementById("scrollContent");

	scrollheight = 37;

	var elem = document.getElementById("filterTable");
	newID = elem.rows.length - 1;

	for (i = 0; i < newID; i++) {
		scrollheight += 26;
	}

	if (mode == 1) {
		scrollContent.style.height = (scrollContent.offsetHeight - scrollheight) + "px";
		defSearch.style.display = "none";
		advSearch.style.display = "block";
		advSearch2.style.display = "block";
		advSearch3.style.display = "block";
	} else {
		scrollContent.style.height = (scrollContent.offsetHeight + scrollheight) + "px";
		defSearch.style.display = "block";
		advSearch.style.display = "none";
		advSearch2.style.display = "none";
		advSearch3.style.display = "none";
	}
}

function checkAll() {
	var checkAll = document.getElementsByName("deleteAllVersions");
	var checkboxes = document.getElementsByName("deleteVersion");
	var check = false;
	var label = document.getElementById("label_deleteAllVersions");
	label.innerHTML = g_l.mark;
	if (checkAll[0].checked) {
		check = true;
		label.innerHTML = g_l.notMark;
	}
	for (var i = 0; i < checkboxes.length; i++) {
		checkboxes[i].checked = check;
	}
}

function makeAjaxRequestDoclist() {
	var args = "";
	var newString = "";
	for (var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(document.we_form.elements[i].value);
	}
	var scroll = document.getElementById("scrollContent");
	scroll.innerHTML = '<table border="0" width="100%" height="100%"><tr><td align="center"><i class="fa fa-2x fa-spinner fa-pulse"></i></td></tr></table>';
	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackResultList, "protocol=json&cns=versionlist&cmd=GetSearchResult&classname=" + doc.ClassName + "&id=" + doc.ID + "&table=" + doc.Table + "&we_transaction=" + transaction + args);
}

function makeAjaxRequestParametersTop() {
	var args = "";
	var newString = "";
	for (var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(document.we_form.elements[i].value);
	}
	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackParametersTop, "protocol=json&position=top&cns=versionlist&cmd=GetSearchParameters&path=" + doc.Path + "&text=" + doc.Text + "&classname=" + doc.ClassName + "&id=" + doc.ID + "&we_transaction=" + transaction + args);
}

function makeAjaxRequestParametersBottom() {
	var args = "";
	var newString = "";
	for (var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(document.we_form.elements[i].value);
	}
	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackParametersBottom, "protocol=json&position=bottom&cns=versionlist&cmd=GetSearchParameters&classname=" + doc.ClassName + "&id=" + doc.ID + "&we_transaction=" + transaction + args);
}


var ajaxCallbackResetVersion = {
	success: function (o) {
		if (o.responseText !== undefined) {
			//top.we_cmd("save_document",transaction,"0","1","0", "","");
			setTimeout('search(false);', 500);
			// reload current document => reload all open Editors on demand

			var _usedEditors = top.weEditorFrameController.getEditorsInUse();
			for (frameId in _usedEditors) {

				if (_usedEditors[frameId].getEditorIsActive()) { // reload active editor
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
//					_usedEditors[frameId].setEditorReloadAllNeeded(true);
				}
			}
			_multiEditorreload = true;

			//reload tree
			top.we_cmd("load", doc.Table, 0);

		}
	},
	failure: function (o) {
	}
}

function resetVersionAjax(id, documentID, version, table) {
	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackResetVersion, "protocol=json&cns=versionlist&cmd=ResetVersion&id=" + id + "&documentID=" + documentID + "&version=" + version + "&documentTable=" + table + "&we_transaction=" + transaction);
}
