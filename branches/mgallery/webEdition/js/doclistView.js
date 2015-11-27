/**
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

var elem = null;

var ajaxCallbackResultList = {
	success: function (o) {
		if (o.responseText !== undefined && o.responseText !== "") {
			document.getElementById("scrollContent_doclist").innerHTML = o.responseText;
			WE().util.setIconOfDocClass(document, 'resultIcon');
			makeAjaxRequestParametersTop();
			makeAjaxRequestParametersBottom();
		}
	},
	failure: function (o) {
		alert("Failure");
	}
};

var ajaxCallbackParametersTop = {
	success: function (o) {
		if (o.responseText !== undefined && o.responseText !== "") {
			document.getElementById("parametersTop").innerHTML = o.responseText;
		}
	},
	failure: function (o) {
		alert("Failure");
	}
};

var ajaxCallbackParametersBottom = {
	success: function (o) {
		if (o.responseText !== undefined && o.responseText !== "") {
			document.getElementById("parametersBottom").innerHTML = o.responseText;
		}
	},
	failure: function (o) {
		alert("Failure");
	}
};

var ajaxCallbackgetMouseOverDivs = {
	success: function (o) {
		if (o.responseText !== undefined && o.responseText !== "") {
			document.getElementById("mouseOverDivs_doclist").innerHTML = o.responseText;
		}
	},
	failure: function (o) {
		alert("Failure");
	}
};

function calendarSetup(x) {
	for (i = 0; i < x; i++) {
		if (document.getElementById("date_picker_from" + i + "") !== null) {
			Calendar.setup({inputField: "search[" + i + "]", ifFormat: "%d.%m.%Y", button: "date_picker_from" + i + "", align: "Tl", singleClick: true});
		}
	}
}

function delRow(id) {
	var scrollContent = document.getElementById("scrollContent_doclist");
	scrollContent.style.height = scrollContent.offsetHeight + 26 + "px";

	var elem = document.getElementById("filterTable");
	if (elem) {
		var trows = elem.rows;
		var rowID = "filterRow_" + id;

		for (i = 0; i < trows.length; i++) {
			if (rowID == trows[i].id) {
				elem.deleteRow(i);
			}
		}
	}
}

function init() {
	WE().util.setIconOfDocClass(document, 'resultIcon');
	sizeScrollContent();
}

function reload() {
	top.we_cmd("reload_editpage");
}

function next(anzahl) {
	var scrollActive = document.getElementById("scrollActive");
	if (scrollActive === null) {
		document.we_form.elements.searchstart.value = parseInt(document.we_form.elements.searchstart.value) + anzahl;

		search(false);
	}
}

function back(anzahl) {
	var scrollActive = document.getElementById("scrollActive");
	if (scrollActive === null) {
		document.we_form.elements.searchstart.value = parseInt(document.we_form.elements.searchstart.value) - anzahl;
		search(false);
	}

}

function showImageDetails(picID) {
	elem = document.getElementById(picID);
	if(elem){
		elem.style.visibility = "visible";
	}

}

function hideImageDetails(picID) {
	elem = document.getElementById(picID);
	if(elem){
		elem.style.visibility = "hidden";
		elem.style.left = "-9999px";
	}
}

// FIXME: take the same fn from search_view.js
function updateElem(e) {
	if (elem !== null && elem.style.visibility == "visible") {
		var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
		var w = window.innerWidth ? window.innerWidth : document.body.offsetWidth;
		var x = (document.all) ? window.event.x + document.body.scrollLeft : e.pageX;
		var y = (document.all) ? window.event.y + document.body.scrollTop : e.pageY;

		elemWidth = elem.offsetWidth;
		elemHeight = elem.offsetHeight;
		elem.style.left = (x + 10) + "px";
		elem.style.top = (y - 120) + "px";

		if ((w - x) < 400 && (h - y) < 250) {
			elem.style.left = (x - elemWidth - 10) + "px";
			elem.style.top = (y - elemHeight - 10) + "px";
		} else if ((w - x) < 400) {
			elem.style.left = (x - elemWidth - 10) + "px";
		} else if ((h - y) < 250) {
			elem.style.top = (y - elemHeight - 10) + "px";
		}
	}
}

function absLeft(el) {
	return (el.offsetParent) ?
					el.offsetLeft + absLeft(el.offsetParent) : el.offsetLeft;
}

function absTop(el) {
	return (el.offsetParent) ?
					el.offsetTop + absTop(el.offsetParent) : el.offsetTop;
}

function openToEdit(tab, id, contentType) {
	WE().layout.weEditorFrameController.openDocument(tab, id, contentType);
}

function switchSearch(mode) {
	document.we_form.mode.value = mode;
	var defSearch = document.getElementById("defSearch");
	var advSearch = document.getElementById("advSearch");
	var advSearch2 = document.getElementById("advSearch2");
	var advSearch3 = document.getElementById("advSearch3");
	var scrollContent = document.getElementById("scrollContent_doclist");

	scrollheight = 30;

	var elem = document.getElementById("filterTable");
	newID = elem.rows.length - 1;

	for (i = 0; i < newID; i++) {
		scrollheight = scrollheight + 26;
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

function makeAjaxRequestDoclist() {
	getMouseOverDivs();
	var args = "";
	var newString = "";
	for (var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(document.we_form.elements[i].value);
	}
	var scroll = document.getElementById("scrollContent_doclist");
	scroll.innerHTML = '<table border="0" width="100%" height="100%"><tr><td align="center"><i class="fa fa-2x fa-spinner fa-pulse"></i><div id="scrollActive"></div></td></tr></table>';
	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackResultList, "protocol=json&cns=doclist&cmd=GetSearchResult&classname=we_folder&id=" + docID + "&table=" + docTable + "&we_transaction=" + transaction + args + "");
}

function makeAjaxRequestParametersTop() {
	var args = "";
	var newString = "";
	for (var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(document.we_form.elements[i].value);
	}
	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackParametersTop, "protocol=json&cns=doclist&cmd=GetSearchParameters&position=top&classname=we_folder&id=" + docID + "&table=" + docTable + "&we_transaction=" + transaction + args + "");
}

function makeAjaxRequestParametersBottom() {
	var args = "";
	var newString = "";
	for (var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(document.we_form.elements[i].value);
	}
	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackParametersBottom, "protocol=json&cns=doclist&cmd=GetSearchParameters&position=bottom&classname=we_folder&id=" + docID + "&table=" + docTable + "&we_transaction=" + transaction + args + "");
}

function getMouseOverDivs() {
	var args = "";
	var newString = "";
	for (var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd[" + encodeURI(newString) + "]=" + encodeURI(document.we_form.elements[i].value);
	}
	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackgetMouseOverDivs, "protocol=json&cns=doclist&cmd=GetMouseOverDivs&whichsearch=doclist&classname=we_folder&id=" + docID + "&table=" + docTable + "&we_transaction=" + transaction + args);
}

function setOrder(order) {
	columns = ["Text", "SiteTitle", "CreationDate", "ModDate"];
	for (var i = 0; i < columns.length; i++) {
		if (order != columns[i]) {
			deleteArrow = document.getElementById(columns[i]);
			deleteArrow.innerHTML = "";
		}
	}
	arrow = document.getElementById(order);
	foo = document.we_form.elements.order.value;

	if (order + " DESC" == foo) {
		document.we_form.elements.order.value = order;
		arrow.innerHTML = '<i class="fa fa-sort-asc fa-lg"></i>';
	} else {
		document.we_form.elements.order.value = order + " DESC";
		arrow.innerHTML = '<i class="fa fa-sort-desc fa-lg"></i>';
	}
	search(false);
}

function setview(setView) {
	document.we_form.setView.value = setView;
	search(false);
}

function checkAllPubChecks() {
	var checkAll = document.getElementsByName("publish_all");
	var checkboxes = document.getElementsByName("publish_docs_doclist");
	var check = false;

	if (checkAll[0].checked) {
		check = true;
	}
	for (var i = 0; i < checkboxes.length; i++) {
		checkboxes[i].checked = check;
	}

}

function publishDocsAjax() {
	var args = "";
	var check = "";
	var checkboxes = document.getElementsByName("publish_docs_doclist");
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) {
			if (check !== "") {
				check += ",";
			}
			check += checkboxes[i].value;
		}
	}
	args += "&we_cmd[0]=" + encodeURI(check);
	var scroll = document.getElementById("resetBusy");
	scroll.innerHTML = "<table border=\'0\' width=\'100%\' height=\'100%\'><tr><td align=\'center\'><i class=\"fa fa-2x fa-spinner fa-pulse\"></i></td></tr></table>";

	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackPublishDocs, "protocol=json&cns=tools/weSearch&cmd=PublishDocs&" + args + "");
}


function sizeScrollContent() {
	var elem = document.getElementById("filterTable");
	newID = elem.rows.length - 1;

	scrollheight = (searchclassFolderMode ? 30 : 0);
	if (searchclassFolderMode) {
		for (i = 0; i < newID; i++) {
			scrollheight = scrollheight + 26;
		}
	}

	var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
	var scrollContent = document.getElementById("scrollContent_doclist");

	var height = 180; // maybe IE needs 200?
	if ((h - height) > 0) {
		scrollContent.style.height = (h - height) + "px";
	}
	if ((scrollContent.offsetHeight - scrollheight) > 0) {
		scrollContent.style.height = (scrollContent.offsetHeight - scrollheight) + "px";
	}
}

function publishDocs() {
	//var checkAll = document.getElementsByName("publish_all");
	var checkboxes = document.getElementsByName("publish_docs_doclist");
	var check = false;

	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) {
			check = true;
			break;
		}
	}

	if (checkboxes.length === 0) {
		check = false;
	}

	if (check === false) {
		top.we_showMessage(g_l.notChecked, WE().consts.message.WE_MESSAGE_NOTICE, window);
	} else {
		Check = confirm(g_l.publish_docs);
		if (Check === true) {
			publishDocsAjax();
		}
	}
}

var ajaxCallbackPublishDocs = {
	success: function (o) {
		top.we_showMessage(g_l.publishOK, WE().consts.message.WE_MESSAGE_NOTICE, window);

		// reload current document => reload all open Editors on demand

		var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
		for (var frameId in _usedEditors) {

			if (_usedEditors[frameId].getEditorIsActive()) { // reload active editor
				_usedEditors[frameId].setEditorReloadAllNeeded(true);
				_usedEditors[frameId].setEditorIsActive(true);

			} else {
				_usedEditors[frameId].setEditorReloadAllNeeded(true);
			}
		}
		_multiEditorreload = true;

		//reload tree
		top.we_cmd("load", top.treeData.table, 0);

		document.getElementById("resetBusy").innerHTML = "";

	},
	failure: function (o) {
		alert("Failure");
	}
};

function search(newSearch) {
	if (canNotMakeTemp) {
		top.we_showMessage(g_l.noTempTableRightsDoclist, WE().consts.message.WE_MESSAGE_NOTICE, window);
	} else {
		if (newSearch) {
			document.we_form.searchstart.value = 0;
		}
		makeAjaxRequestDoclist();
	}
}

function newinput() {
	var elem = document.getElementById("filterTable");
	newID = elem.rows.length - 1;
	rows++;

	var scrollContent = document.getElementById("scrollContent_doclist");
	scrollContent.style.height = scrollContent.offsetHeight - 26 + "px";


	if (elem) {
		var newRow = document.createElement("TR");
		newRow.setAttribute("id", "filterRow_" + rows);

		var cell = document.createElement("TD");
		cell.innerHTML = searchFields.replace(/__we_new_id__/g, rows) + '<input type="hidden" value="" name="hidden_searchFields[' + rows + ']"';
		newRow.appendChild(cell);

		cell = document.createElement("TD");
		cell.setAttribute("id", "td_location[" + rows + "]");
		cell.innerHTML = locationFields.replace(/__we_new_id__/g, rows);
		newRow.appendChild(cell);

		cell = document.createElement("TD");
		cell.setAttribute("id", "td_search[" + rows + "]");
		cell.innerHTML = searchFields.replace(/__we_new_id__/g, rows);
		newRow.appendChild(cell);

		cell = document.createElement("TD");
		cell.setAttribute("id", "td_delButton[" + rows + "]");
		cell.innerHTML = trashButton.replace(/__we_new_id__/g, rows);
		newRow.appendChild(cell);

		elem.appendChild(newRow);
	}
}

function changeit(value, rowNr) {
	var setValue = document.getElementsByName("search[" + rowNr + "]")[0].value;
	var from = document.getElementsByName("hidden_searchFields[" + rowNr + "]")[0].value;
	var row = document.getElementById("filterRow_" + rowNr);
	var locationTD = document.getElementById("td_location[" + rowNr + "]");
	var searchTD = document.getElementById("td_search[" + rowNr + "]");
	var delButtonTD = document.getElementById("td_delButton[" + rowNr + "]");
	var location = document.getElementById("location[" + rowNr + "]");
	var innerhtml,cell;

	switch (value) {
		case "Content":
			if (locationTD !== null) {
				location.disabled = true;
			}
			row.removeChild(searchTD);

			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}
			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = searchFields.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = trashButton.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);
			break;
		case "temp_category":
			if (locationTD !== null) {
				location.disabled = true;
			}
			row.removeChild(searchTD);

			innerhtml = "<table class=\"default\"><tbody><tr><td>" +
							"<input class=\"wetextinput\" name=\"search[" + rowNr + "]\" size=\"58\" value=\"\"  id=\"search[" + rowNr + "]\" readonly=\"1\" style=\"width: 190px;\" type=\"text\" />" +
							"</td><td><input value=\"\" name=\"searchParentID[" + rowNr + "]\" type=\"hidden\" /></td><td></td><td>" +
							"<button title=\"" + g_l.select_value + "\" class=\"weBtn\" style=\"width: 70px\" onclick=\"we_cmd(\'we_selector_category\',document.we_form.elements[\'searchParentID[" + rowNr + "]\'].value,\'" + WE().consts.tables.CATEGORY_TABLE + "\',\'document.we_form.elements[\\\\\'searchParentID[" + rowNr + "]\\\\\'].value\',\'document.we_form.elements[\\\\\'search[" + rowNr + "]\\\\\'].value\',\'\',\'\',\'0\',\'\',\'\');\">" +
							g_l.select_value +
							"</button></td></tr></tbody></table>";

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = innerhtml;
			row.appendChild(cell);

			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = trashButton.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);
			break;
		case "temp_template_id":
		case "MasterTemplateID":
			if (locationTD !== null) {
				location.disabled = true;
			}
			row.removeChild(searchTD);

			innerhtml = "<table class=\"default\"><tbody><tr><td>" +
							"<input class=\"wetextinput\" name=\"search[" + rowNr + "]\" size=\"58\" value=\"\"  id=\"search[" + rowNr + "]\" readonly=\"1\" style=\"width: 190px;\" type=\"text\" />" +
							"</td><td><input value=\"\" name=\"searchParentID[" + rowNr + "]\" type=\"hidden\" /></td><td></td><td>" +
							"<button title=\"" + g_l.select_value + "\" class=\"weBtn\" style=\"width: 70px\" onclick=\"we_cmd(\'we_selector_document\',document.we_form.elements[\'searchParentID[" + rowNr + "]\'].value,\'" + WE().consts.tables.TEMPLATES_TABLE + "\',\'document.we_form.elements[\\\\\'searchParentID[" + rowNr + "]\\\\\'].value\',\'document.we_form.elements[\\\\\'search[" + rowNr + "]\\\\\'].value\',\'\',\'\',\'0\',\'\',\'\');\" >" +
							g_l.select_value +
							"</button></td></tr></tbody></table>";

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = innerhtml;
			row.appendChild(cell);

			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = trashButton.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);
			break;
		case "Status":
			if (locationTD !== null) {
				location.disabled = true;
			}
			row.removeChild(searchTD);
			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = searchClassFolder.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = trashButton.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);
			break;
		case "Speicherart":
			if (locationTD !== null) {
				location.disabled = true;
			}
			row.removeChild(searchTD);
			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = searchSpeicherat.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = trashButton.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);
			break;
		case "Published":
		case "CreationDate":
		case "ModDate":

			row.removeChild(locationTD);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_location[" + rowNr + "]");
			cell.innerHTML = locationDateFields.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);

			row.removeChild(searchTD);

			innerhtml = "<table id=\"search[" + rowNr + "]_cell\" class=\"default\"><tbody><tr><td></td><td></td><td>" +
							"<input class=\"wetextinput\" name=\"search[" + rowNr + "]\" size=\"55\" value=\"\" maxlength=\"10\" id=\"search[" + rowNr + "]\" readonly=\"1\" style=\"width: 100px; \" type=\"text\" />" +
							"</td><td>&nbsp;</td><td><a href=\"#\">" +
							"<button id=\"date_picker_from" + rowNr + "\" class=\"weBtn\"><i class='fa fa-lg fa-calendar'></i>" +
							"</button></a></td></tr></tbody></table>";


			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = innerhtml;
			row.appendChild(cell);

			Calendar.setup({inputField: "search[" + rowNr + "]", ifFormat: "%d.%m.%Y", button: "date_picker_from" + rowNr + "", align: "Tl", singleClick: true});

			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = trashButton.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);
			break;
		default:
			row.removeChild(searchTD);

			if (locationTD !== null) {
				row.removeChild(locationTD);
			}
			if (delButtonTD !== null) {
				row.removeChild(delButtonTD);
			}

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_location[" + rowNr + "]");
			cell.innerHTML = locationDateFields.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_search[" + rowNr + "]");
			cell.innerHTML = searchFields.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton[" + rowNr + "]");
			cell.innerHTML = trashButton.replace(/__we_new_id__/g, rowNr);
			row.appendChild(cell);
	}
	switch (from) {
		case "temp_template_id":
		case "ContentType":
		case "temp_category":
		case "Status":
		case "Speicherart":
		case "Published":
		case "CreationDate":
		case "ModDate":
			setValue = "";
	}
	switch (value) {
		case "temp_template_id":
		case "ContentType":
		case "temp_category":
		case "Status":
		case "Speicherart":
		case "Published":
		case "CreationDate":
		case "ModDate":
			setValue = "";
	}

	document.getElementById("search[" + rowNr + "]").value = setValue;
	document.getElementsByName("hidden_searchFields[" + rowNr + "]")[0].value = value;
}

document.onmousemove = updateElem;
