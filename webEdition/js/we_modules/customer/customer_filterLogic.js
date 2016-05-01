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
function $(id) {
	return document.getElementById(id);
}

function wecf_logic_changed(s) {
	wecf_hot();
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
			prev.childNodes[i].style.paddingBottom = (val == "OR") ? "10px" : "0";
		}
	}
	for (i = 0; i < l; i++) {
		if (row.childNodes[i].nodeName.toLowerCase() === "td") {
			row.childNodes[i].style.paddingTop = (val == "OR") ? "10px" : "0";
			row.childNodes[i].style.borderTop = (val == "OR") ? "1px solid grey" : "0";
		}
	}
}

function removeFromMultiEdit(_multEdit) {
	wecf_hot();
	if (_multEdit.itemCount > 0) {
		while (_multEdit.itemCount > 0) {
			_multEdit.delItem(_multEdit.itemCount);
		}
	}
}

function addToMultiEdit(_multEdit, paths, ids) {
	wecf_hot();
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
