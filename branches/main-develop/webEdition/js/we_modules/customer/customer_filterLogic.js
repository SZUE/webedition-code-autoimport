/* global top, WE */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
'use strict';
var cFilter = WE().util.getDynamicVar(document, 'loadcfilter', 'data-cfilter');

function getById(id) {
	return document.getElementById(id);
}

function wecf_logic_changed(s) {
	window.we_cmd('setHot');
	var val = s.options[s.selectedIndex].value;
	var cell = s.parentNode;
	var row = cell.parentNode;
	var prev = row.previousSibling;
	while (prev.nodeName.toLowerCase() !== "tr") {
		prev = prev.previousSibling;
	}

	var l = row.childNodes.length;
	var l2 = prev.childNodes.length;

	for (var i = 0; i < l2; i++) {
		if (prev.childNodes[i].nodeName.toLowerCase() === "td") {
			prev.childNodes[i].style.paddingBottom = (val === "OR") ? "10px" : "0";
		}
	}
	for (i = 0; i < l; i++) {
		if (row.childNodes[i].nodeName.toLowerCase() === "td") {
			row.childNodes[i].style.paddingTop = (val === "OR") ? "10px" : "0";
			row.childNodes[i].style.borderTop = (val === "OR") ? "1px solid grey" : "0";
		}
	}
}

// FIXME:  move this to multiedit!
function removeFromMultiEdit(_multEdit) {
	window.we_cmd('setHot');
	if (_multEdit.itemCount > 0) {
		while (_multEdit.itemCount > 0) {
			_multEdit.delItem(_multEdit.itemCount);
		}
	}
}

// FIXME:  move this to multiedit!
function addToMultiEdit(_multEdit, paths, ids) {
	window.we_cmd('setHot');
	var found = false;
	var j = 0;
	for (var i = 0; i < ids.length; i++) {
		if (ids[i] !== "") {
			found = false;
			for (j = 0; j < _multEdit.itemCount; j++) {
				if (_multEdit.form.elements[_multEdit.name + "_variant1_" + _multEdit.name + "_item" + j].value == ids[i]) {
					found = true;
				}
			}
			if (!found) {
				_multEdit.addItem();
				_multEdit.setItem(0, (_multEdit.itemCount - 1), paths[i]);
				_multEdit.setItem(1, (_multEdit.itemCount - 1), ids[i]);
			}
		}
	}
	_multEdit.showVariant(0);
}

function updateView() {
	switch (cFilter.type) {
		case "document":
			updateView_document();
			break;
		case "navigation":
			updateView_navigation();
			break;
		default:
			updateView_base();
			break;
	}
}

function updateView_base() {
	var f = document.forms[0];
	var r = f.wecf_mode;
	//var modeRadioOff = r[0];
	//var modeRadioAll = r[1];
	var modeRadioSpecific = r[2];
	var modeRadioFilter = r[3];
	//var modeRadioNone = r[4];

	getById('specificCustomersEditDiv').style.display = modeRadioSpecific.checked ? "block" : "none";
	getById('blackListEditDiv').style.display = modeRadioFilter.checked ? "block" : "none";
	getById('whiteListEditDiv').style.display = modeRadioFilter.checked ? "block" : "none";
	getById('filterCustomerDiv').style.display = modeRadioFilter.checked ? "block" : "none";
}

function updateView_document() {
	updateView_base();
	var f = document.forms[0];
	var r = f.wecf_mode;
	var modeRadioOff = r[0];
	var r2 = f.wecf_accessControlOnTemplate;
	//var wecf_onTemplateRadio = r2[0];
	var wecf_errorDocRadio = r2[1];

	getById('accessControlSelectorDiv').style.display = wecf_errorDocRadio.checked ? "block" : "none";
	getById('accessControlDiv').style.display = modeRadioOff.checked ? "none" : "block";
}

function updateView_navigation() {
	updateView_base();
	var f = document.forms[0];
	var wecf_useDocumentFilterCheckbox = f.check_wecf_useDocumentFilter;  // with underscore (_) its the checkbox, otherwise the hidden field
	getById('MainFilterDiv').style.display = wecf_useDocumentFilterCheckbox.checked ? 'none' : 'block';
}

function initFilters() {
	var curFilter, i;
	for (var filter in cFilter.filters) {
		curFilter = cFilter.filters[filter];
		window[filter] = new (WE().util.multi_edit)(filter + "MultiEdit", window, 0, cFilter.delButton, cFilter.filterWidth, false);
		window[filter].addVariant();
		window[filter].addVariant();
		document.we_form[filter + "Control"].value = window[filter].name;
		for (i = 0; i < curFilter.length; i++) {
			window[filter].addItem();
			window[filter].setItem(0, i, curFilter[i].Text);
			window[filter].setItem(1, i, curFilter[i].ID);
		}
		window[filter].showVariant(0);
	}
}