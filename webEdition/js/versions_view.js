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
'use strict';
WE().util.loadConsts(document, "g_l.versions");
var searchClass = WE().util.getDynamicVar(document, 'loadVarVersionView', 'data-searchClass');
var props = WE().util.getDynamicVar(document, 'loadVarVersionView', 'data-props');
var isAllChecked = false;

function init() {
	resizeVersionsTable();
	sizeScrollContent();
	window.addEventListener('resize', resizeVersionsTable);
}

function resizeVersionsTable(){
	var headCells = document.getElementById('contentTable_headTR').cells;
	var cntRow;

	if((cntRow = document.getElementById('contentTbody').firstChild)){
		for (var i = 0; i < headCells.length; i++) {
			headCells[i].style.width = (cntRow.cells[i].offsetWidth) + 'px';
		}
	}
}

function printScreen() {

	var scrollContent = document.getElementById("scrollContent");
	var hScrollContent = scrollContent.innerHeight ? scrollContent.innerHeight : scrollContent.offsetHeight;

	var contentTable = document.getElementById("contentTable");
	var hContentTable = contentTable.innerHeight ? contentTable.innerHeight : contentTable.offsetHeight;

	//hContentTable = hContentTable-500;

	scrollContent.style.height = hContentTable + "px";
	window.print();

	window.setTimeout(setCrollContent, 2000, hScrollContent);
}

function setCrollContent(hScrollContent) {
	var scrollContent = document.getElementById("scrollContent");
	scrollContent.style.height = hScrollContent + "px";
}

function search(newSearch) {
	if (newSearch) {
		document.we_form.searchstart.value = 0;
	}
	makeAjaxRequestDoclist();
}

function deleteVersionAjax() {
	var args = "";
	var check = "";
	var newString = "";
	var checkboxes = document.getElementsByName("deleteVersion");
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) {
			if (check !== "") {
				check += ",";
			}
			check += checkboxes[i].value;
			newString = checkboxes[i].name;
		}
	}
	args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(check);
	var scroll = document.getElementById("scrollContent");
	scroll.innerHTML = "<table width='100%' height='100%'><tr><td align='center'><i class=\"fa fa-2x fa-spinner fa-pulse\"></i></td></tr></table>";

	WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=DeleteVersion", "cns=versionlist&" + args);
}

function previewVersion(table, ID, version) {
	top.we_cmd("versions_preview", table, ID, version, 0);
}

var msBack = 0;
var diffBack = 0;
var msNext = 0;
var diffNext = 0;

function next(anzahl) {
	var zeit = new Date();
	if (msBack !== 0) {
		diffBack = zeit.getTime() - msBack;
	}
	msBack = zeit.getTime();
	if (diffBack > 1000 || diffBack === 0) {
		document.we_form.elements.searchstart.value = parseInt(document.we_form.elements.searchstart.value) + anzahl;

		search(false);
	}
}

function back(anzahl) {
	var zeit = new Date();
	if (msNext !== 0) {
		diffNext = zeit.getTime() - msNext;
	}
	msNext = zeit.getTime();
	if (diffNext > 1000 || diffNext === 0) {
		document.we_form.elements.searchstart.value = parseInt(document.we_form.elements.searchstart.value) - anzahl;
		search(false);
	}
}

function setOrder(order) {
	var columns = ["version", "modifierID", "timestamp"];
	for (var i = 0; i < columns.length; i++) {
		if (order != columns[i]) {
			var deleteArrow = document.getElementById("" + columns[i] + "");
			deleteArrow.innerHTML = "";
		}
	}
	var arrow = document.getElementById("" + order + "");
	var orderVal = document.we_form.elements.order.value;

	if (order + " DESC" == orderVal) {
		document.we_form.elements.order.value = order;
		arrow.innerHTML = "<i class=\"fa fa-sort-asc fa-lg\"></i>";
	} else {
		document.we_form.elements.order.value = order + " DESC";
		arrow.innerHTML = "<i class=\"fa fa-sort-desc fa-lg\"></i>";
	}
	search(false);
}

function delRow(id) {
	var scrollContent = document.getElementById("scrollContent");
	scrollContent.style.height = scrollContent.offsetHeight + 26 + "px";

	var elem = document.getElementById("filterTable");
	if (elem) {
		var trows = elem.rows;
		var rowID = "filterRow_" + id;

		for (var i = 0; i < trows.length; i++) {
			if (rowID == trows[i].id) {
				elem.deleteRow(i);
			}
		}
	}
	search(false);
}

function resetVersion(id, documentID, version, table) {
	var Check = window.confirm(WE().consts.g_l.versions.resetVersions);
	if (Check === true) {
		if (document.getElementById("publishVersion_" + id) !== null) {
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

	var scrollheight = 37;

	var elem = document.getElementById("filterTable");
	var newID = elem.rows.length - 1;

	for (var i = 0; i < newID; i++) {
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

	search(false);
}

function checkAll() {
	isAllChecked = !isAllChecked;
	document.getElementById('selectAll').title = isAllChecked ? WE().consts.g_l.versions.notMark : WE().consts.g_l.versions.mark;

	var checkboxes = document.getElementsByName("deleteVersion");
	for (var i = 0; i < checkboxes.length; i++) {
		checkboxes[i].checked = isAllChecked;
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
	scroll.innerHTML = '<table style="width:100%;height:100%"><tr><td style="text-align:center"><i class="fa fa-2x fa-spinner fa-pulse"></i></td></tr></table>';

	WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=GetSearchResult", "cns=versionlist&classname=" + window.doc.docClass + "&id=" + window.doc.docId + "&table=" + window.doc.docTable + "&we_transaction=" + props.transaction + args, function (responseText) {
		scroll.innerHTML = '<table id="contentTable" class="contentTable" cellpadding="5">' + ((responseText && responseText.DataArray && responseText.DataArray.data) ?
				responseText.DataArray.data : '') + '</table>';

		resizeVersionsTable();
		makeAjaxRequestParametersTop();
		makeAjaxRequestParametersBottom();
	}, "html");
}

function makeAjaxRequestParametersTop() {
	var args = "";
	var newString = "";
	for (var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(document.we_form.elements[i].value);
	}
	WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=GetSearchParameters", "position=top&cns=versionlist&path=" + window.doc.docPath + "&text=" + window.doc.docText + "&classname=" + window.doc.docClass + "&id=" + window.doc.docId + "&we_transaction=" + props.transaction + args, function (responseText) {
		if (responseText && responseText.DataArray && responseText.DataArray.data) {
			document.getElementById("parametersTop").innerHTML = responseText.DataArray.data;
		}
	}, "html");
}

function makeAjaxRequestParametersBottom() {
	var args = "";
	var newString = "";
	for (var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(document.we_form.elements[i].value);
	}
	WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=GetSearchParameters", "position=bottom&cns=versionlist&classname=" + window.doc.docClass + "&id=" + window.doc.docId + "&we_transaction=" + props.transaction + args, function (responseText) {
		if (responseText && responseText.DataArray && responseText.DataArray.data) {
			document.getElementById("parametersBottom").innerHTML = responseText.DataArray.data;
		}
	}, "html");
}

function ajaxCallbackResetVersion(response) {
	if (response !== undefined) {
		//top.we_cmd("save_document",props.transaction,"0","1","0", "","");
		window.setTimeout(search, 500, false);
		// reload current document => reload all open Editors on demand

		//reset content of editor
		WE().layout.weEditorFrameController.getActiveDocumentReference().frames[2].location = "about:blank";

		var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
		for (var frameId in _usedEditors) {

			if (_usedEditors[frameId].getEditorIsActive()) { // reload active editor

				_usedEditors[frameId].setEditorReloadAllNeeded(true);
				_usedEditors[frameId].setEditorIsActive(true);
			} else {
//					_usedEditors[frameId].setEditorReloadAllNeeded(true);
			}
		}

		//reload tree
		top.we_cmd("load", window.doc.docTable, 0);

	}
}

function resetVersionAjax(id, documentID, version, table) {
	WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=ResetVersion", "cns=versionlist&id=" + id + "&documentID=" + documentID + "&version=" + version + "&documentTable=" + table + "&we_transaction=" + props.transaction, ajaxCallbackResetVersion);
}


function sizeScrollContent() {
	var elem = document.getElementById("filterTable");
	if (elem) {
		var newID = elem.rows.length - 1;
		var scrollheight = searchClass.scrollHeight;

		var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
		var scrollContent = document.getElementById("scrollContent");

		var height = 240;
		if ((h - height) > 0) {
			scrollContent.style.height = (h - height) + "px";
		}
		if ((scrollContent.offsetHeight - scrollheight) > 0) {
			scrollContent.style.height = (scrollContent.offsetHeight - scrollheight) + "px";
		}
	}
}

function deleteVers() {
	var checkboxes = document.getElementsByName("deleteVersion");
	var check = false;
	var i;
	for (i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) {
			check = true;
			break;
		}
	}

	if (checkboxes.length === 0) {
		check = false;
	}

	if (check === false) {
		WE().util.showMessage(WE().consts.g_l.versions.notChecked, WE().consts.message.WE_MESSAGE_NOTICE, window);
		return;
	}
	var Check = window.confirm(WE().consts.g_l.versions.deleteVersions);
	if (Check === true) {
		if (isAllChecked) {
			isAllChecked = false;
			document.getElementById('selectAll').title = WE().consts.g_l.versions.mark;
			if (parseInt(document.we_form.searchstart.value) !== 0) {
				document.we_form.searchstart.value = document.we_form.searchstart.value - searchClass.anzahl;
			}
		} else {
			var allChecked = true;
			checkboxes = document.getElementsByName("deleteVersion");
			for (i = 0; i < checkboxes.length; i++) {
				if (checkboxes[i].checked === false) {
					allChecked = false;
				}
			}
			if (allChecked) {
				if (parseInt(document.we_form.searchstart.value) !== 0) {
					document.we_form.searchstart.value = document.we_form.searchstart.value - searchClass.anzahl;
				}
			}
		}

		deleteVersionAjax();
		window.setTimeout(search, 800, false);
	}
}

function newinput() {
	var elem = document.getElementById("filterTable");
	var newID = elem.rows.length - 1;
	props.rows++;

	var scrollContent = document.getElementById("scrollContent");
	scrollContent.style.height = scrollContent.offsetHeight - 26 + "px";

	if (elem) {
		var newRow = document.createElement("TR");
		newRow.setAttribute("id", "filterRow_" + props.rows);

		var cell = document.createElement("TD");
		cell.innerHTML = searchClass.searchFields.replace(/__we_new_id__/g, props.rows);
		newRow.appendChild(cell);

		cell = document.createElement("TD");
		cell.setAttribute("id", "td_location[" + props.rows + "]");
		cell.innerHTML = searchClass.locationFields.replace(/__we_new_id__/g, props.rows);
		newRow.appendChild(cell);

		cell = document.createElement("TD");
		cell.setAttribute("id", "td_search[" + props.rows + "]");
		cell.innerHTML = searchClass.search.replace(/__we_new_id__/g, props.rows);
		newRow.appendChild(cell);

		cell = document.createElement("TD");
		cell.setAttribute("id", "td_delButton[" + props.rows + "]");
		cell.innerHTML = searchClass.trash.replace(/__we_row__/g, props.rows);
		newRow.appendChild(cell);

		cell = document.createElement("TD");
		cell.setAttribute("id", "td_hiddenLocation[" + props.rows + "]");
		cell.innerHTML = '<input type="hidden" name="location[' + props.rows + ']" value="IS">';
		newRow.appendChild(cell);

		elem.appendChild(newRow);
	}
}

function changeit(value, rowNr) {
	var row = document.getElementById("filterRow_" + rowNr);
	var locationTD = document.getElementById("td_location[" + rowNr + "]");
	var searchTD = document.getElementById("td_search[" + rowNr + "]");
	var delButtonTD = document.getElementById("td_delButton[" + rowNr + "]");
	var location = document.getElementById("location[" + rowNr + "]");
	var hiddenLocTD = document.getElementById("td_hiddenLocation[" + rowNr + "]");
	var cell;
	switch (value) {
		case "allModsIn":
			if (locationTD !== null) {
				location.value = 'IS';
				location.disabled = true;
			}
			row.removeChild(searchTD);
			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}
			if (hiddenLocTD !== null) {
				row.removeChild(hiddenLocTD);
			}

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = searchClass.search.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = searchClass.trash.replace(/__we_row__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_hiddenLocation[" + rowNr + "]");
			cell.innerHTML = '<input type="hidden" name="location[' + rowNr + ']" value="IS">';
			row.appendChild(cell);
			break;
		case "timestamp":
			row.removeChild(locationTD);
			row.removeChild(searchTD);
			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}
			if (hiddenLocTD !== null) {
				row.removeChild(hiddenLocTD);
			}

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_location[" + rowNr + "]");
			cell.innerHTML = searchClass.locationFieldsDate.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);
			location = document.getElementById("location[" + rowNr + "]");
			location.disabled = false;

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = '<span id="search[' + rowNr + ']_cell" class="default">' +
				'<input class="wetextinput datepicker" name="search[' + rowNr + ']" value="" id="selectorDate' + rowNr + '" readonly="readonly" style="width: 100px;" type="text" />' +
				'<button class="weBtn" onclick="$(\'#selectorDate' + rowNr + '\').datepicker(\'show\')"><i class="fa fa-lg fa-calendar"></i>' +
				'</button></span>';
			row.appendChild(cell);
			$('.datepicker').datepicker();

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = searchClass.trash.replace(/__we_row__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_hiddenLocation[" + rowNr + "]");
			row.appendChild(cell);
			break;
		case "modifierID":
			if (locationTD !== null) {
				location.valaue = 'IS';
				location.disabled = true;
			}
			row.removeChild(searchTD);
			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}
			if (hiddenLocTD !== null) {
				row.removeChild(hiddenLocTD);
			}

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = searchClass.searchUsers.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = searchClass.trash.replace(/__we_row__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_hiddenLocation[" + rowNr + "]");
			cell.innerHTML = '<input type="hidden" name="location[' + rowNr + ']" value="IS">';
			row.appendChild(cell);
			break;
		case "status":
			if (locationTD !== null) {
				location.value = 'IS';
				location.disabled = true;
			}
			row.removeChild(searchTD);
			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}
			if (hiddenLocTD !== null) {
				row.removeChild(hiddenLocTD);
			}

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = searchClass.searchStats.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = searchClass.trash.replace(/__we_row__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_hiddenLocation[" + rowNr + "]");
			cell.innerHTML = '<input type="hidden" name="location[' + rowNr + ']" value="IS">';
			row.appendChild(cell);
	}
}
