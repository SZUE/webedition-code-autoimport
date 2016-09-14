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
var _sCsvInit_;
var _bPrev = false;

function addEntry(sText, sValue) {
	var oSctPool = _fo.elements.sct_pool;
	oSctPool.options[0].text = '';
	oSctPool.options[oSctPool.options.length] = new Option(sText, sValue, false, false);
}

function addBtn(obj, text, value, selected) {
	if (obj !== null && obj.options !== null) {
		obj.options[obj.options.length] = new Option(text, value, false, selected);
		deleteEntry(value);
	}
}

function hasOptions(obj) {
	if (obj !== null && obj.options !== null) {
		return true;
	}
	return false;
}

function selectUnselectMatchingOptions(obj, regex, which, only) {
	var bSel1, bSel2;
	if (window.RegExp) {
		switch (which) {
			case 'select':
				bSel1 = true;
				bSel2 = false;
				break;
			case 'unselect':
				bSel1 = false;
				bSel2 = true;
				break;
			default:
				return;
		}
		var re = new RegExp(regex);
		if (!hasOptions(obj)) {
			return;
		}
		for (var i = 0; i < obj.options.length; i++) {
			if (re.test(obj.options[i].text)) {
				obj.options[i].selected = bSel1;
			} else {
				if (only === true) {
					obj.options[i].selected = bSel2;
				}
			}
		}
	}
}

function selectMatchingOptions(obj, regex) {
	selectUnselectMatchingOptions(obj, regex, 'select', false);
}

function selectOnlyMatchingOptions(obj, regex) {
	selectUnselectMatchingOptions(obj, regex, 'select', true);
}

function unSelectMatchingOptions(obj, regex) {
	selectUnselectMatchingOptions(obj, regex, 'unselect', false);
}

function sortSelect(obj) {
	var o = [];
	if (!hasOptions(obj)) {
		return;
	}
	var i;
	for (i = 0; i < obj.options.length; i++) {
		o[o.length] = new Option(obj.options[i].text, obj.options[i].value, obj.options[i].defaultSelected, obj.options[i].selected);
	}
	if (o.length === 0) {
		return;
	}
	o = o.sort(
					function (a, b) {
						if ((a.text + '') < (b.text + '')) {
							return -1;
						}
						if ((a.text + '') > (b.text + '')) {
							return 1;
						}
						return 0;
					}
	);
	for (i = 0; i < o.length; i++) {
		obj.options[i] = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
	}
}

function selectAllOptions(obj) {
	if (!hasOptions(obj)) {
		return;
	}
	for (var i = 0; i < obj.options.length; i++) {
		obj.options[i].selected = true;
	}
}

function moveSelectedOptions(from, to, select, regex) {
	if (regex) {
		unSelectMatchingOptions(from, regex);
	}
	if (!hasOptions(from)) {
		return;
	}
	var i, index, o;
	for (i = 0; i < from.options.length; i++) {
		o = from.options[i];
		if (o.selected) {
			if (!hasOptions(to)) {
				index = 0;
			} else {
				index = to.options.length;
			}
			to.options[index] = new Option(o.text, o.value, false, false);
		}
	}
	for (i = (from.options.length - 1); i >= 0; i--) {
		o = from.options[i];
		if (o.selected) {
			from.options[i] = null;
		}
	}
	if ((select === undefined) || (select === true)) {
		sortSelect(from);
		sortSelect(to);
	}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
}

function copySelectedOptions(from, to, select) {
	var options = {};
	var i, index, o;
	if (hasOptions(to)) {
		for (i = 0; i < to.options.length; i++) {
			options[to.options[i].value] = to.options[i].text;
		}
	}
	if (!hasOptions(from)) {
		return;
	}
	for (i = 0; i < from.options.length; i++) {
		o = from.options[i];
		if (o.selected) {
			if (options[o.value] === null || options[o.value] === undefined || options[o.value] != o.text) {
				if (!hasOptions(to)) {
					index = 0;
				} else {
					index = to.options.length;
				}
				to.options[index] = new Option(o.text, o.value, false, false);
			}
		}
	}
	if ((select === undefined) || (select === true)) {
		sortSelect(to);
	}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
}

function moveAllOptions(from, to, select, regex) {
	selectAllOptions(from);
	if (arguments.length > 1) {
		moveSelectedOptions.apply(Array.prototype.slice.call(arguments));
	}
}

function copyAllOptions(from, to, select) {
	selectAllOptions(from);
	if (arguments.length > 1) {
		copySelectedOptions.apply(this, Array.prototype.slice.call(arguments));
	}
}

function swapOptions(obj, i, j) {
	var o = obj.options;
	var i_selected = o[i].selected;
	var j_selected = o[j].selected;
	var temp = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
	var temp2 = new Option(o[j].text, o[j].value, o[j].defaultSelected, o[j].selected);
	o[i] = temp2;
	o[j] = temp;
	o[i].selected = j_selected;
	o[j].selected = i_selected;
}

function moveOptionUp(obj) {
	if (!hasOptions(obj)) {
		return;
	}
	for (i = 0; i < obj.options.length; i++) {
		if (obj.options[i].selected) {
			if (i !== 0 && !obj.options[i - 1].selected) {
				swapOptions(obj, i, i - 1);
				obj.options[i - 1].selected = true;
			}
		}
	}
}

function moveOptionDown(obj) {
	if (!hasOptions(obj)) {
		return;
	}
	for (i = obj.options.length - 1; i >= 0; i--) {
		if (obj.options[i].selected) {
			if (i != (obj.options.length - 1) && !obj.options[i + 1].selected) {
				swapOptions(obj, i, i + 1);
				obj.options[i + 1].selected = true;
			}
		}
	}
}

function removeSelectedOptions(from) {
	if (!hasOptions(from)) {
		return;
	}
	if (from.type == 'select-one') {
		from.options[from.selectedIndex] = null;
	} else {
		for (var i = (from.options.length - 1); i >= 0; i--) {
			var o = from.options[i];
			if (o.selected) {
				from.options[i] = null;
			}
		}
	}
	from.selectedIndex = -1;
}

function removeAllOptions(from) {
	if (!hasOptions(from)) {
		return;
	}
	for (var i = (from.options.length - 1); i >= 0; i--) {
		from.options[i] = null;
	}
	from.selectedIndex = -1;
}

function addOption(obj, text, value, selected) {
	if (obj !== null && obj.options !== null) {
		obj.options[obj.options.length] = new Option(text, value, false, selected);
	}
}

function removeOption(obj) {
	var selIndex = obj.selectedIndex;
	if (selIndex != -1) {
		for (i = obj.length - 1; i >= 0; i--) {
			if (obj.options[i].selected) {
				addEntry(obj.options[i].text, obj.options[i].value);
				obj.options[i] = null;
			}
		}
		if (obj.length > 0) {
			obj.selectedIndex = selIndex === 0 ? 0 : selIndex - 1;
		}
	}
}

function getCsv() {
	aSct = [];
	aSctLen = [];
	aSct[0] = _fo.list11;
	aSctLen[0] = aSct[0].length;
	aSct[1] = _fo.list21;
	aSctLen[1] = aSct[1].length;
	aValue = [];
	aValue[0] = aValue[1] = '';
	for (var i = 0; i < 2; i++) {
		for (var k = 0; k < aSctLen[i]; k++) {
			aValue[i] += aSct[i].options[k].value;
			if (k != aSctLen[i] - 1)
				aValue[i] += ',';
		}
	}
	return aValue[0] + ';' + aValue[1];
}

function preview() {
	_bPrev = true;
	previewPrefs();
	opener.rpc(getCsv(), '', '', '', '', prefs._sObjId);
}

function exit_close() {
	if (_sCsvInit_ != getCsv() && _bPrev) {
		opener.rpc(_sCsvInit_, '', '', '', '', prefs._sObjId);
	}
	exitPrefs();
	self.close();
}

function init() {
	_fo = document.forms[0];
	_sCsvInit_ = opener.document.getElementById(prefs._sObjId + '_csv').value;
	var aCsv = _sCsvInit_.split(';');
	for (var i = 0; i < aCsv.length; i++) {
		var aVals = aCsv[i].split(',');
		var iOpt = 0;
		while (iOpt < aVals.length) {
			if (_aLang[aVals[iOpt]] !== undefined) {
				deleteEntry(aVals[iOpt]);
				addOption(_fo['list' + (i + 1) + '1'], _aLang[aVals[iOpt]], aVals[iOpt], false);
			}
			iOpt++;
		}
	}
	initPrefs();
}

function deleteEntry(sValue) {
	var oSctPool = _fo.elements.sct_pool;
	for (var i = 1; i < oSctPool.length; i++) {
		if (oSctPool.options[i].value == sValue) {
			oSctPool.options[i] = null;
			if (oSctPool.length == 1) {
				oSctPool.options[0].text = WE().consts.g_l.cockpit.all_selected;
			}
			oSctPool.selectedIndex = 0;
			break;
		}
	}
}

function save() {
	var sCsv = getCsv();
	var oCsv_ = opener.document.getElementById(prefs._sObjId + '_csv');
	oCsv_.value = sCsv;
	//savePrefs();
	if (_sCsvInit_ != sCsv) {
		opener.rpc(sCsv, '', '', '', '', prefs._sObjId);
	}
	top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
	self.close();
}
