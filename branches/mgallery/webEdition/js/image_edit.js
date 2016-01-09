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

document.onkeyup = function(e) {
	e = (event !== undefined) ? event : e;
	if (e.keyCode == 13) {
		doOK();
	} else if(e.keyCode == 27) {
		top.close();
	}
};

function isSpecialKey(key) {
	return (key >= 63232 && key <= 63235) || key == 8 || key == 63272 || key == 0 || key == 13;
}

function we_switchPixelPercent(inp, sel) {

	if (sel.options[sel.selectedIndex].value == "pixel") {
		if (inp.name == "width") {
			inp.value = Math.round((width / 100) * inp.value);
		} else {
			inp.value = Math.round((height / 100) * inp.value);
		}
	} else {
		if (inp.name == "width") {
			inp.value = Math.round(100 * (100 / width) * inp.value) / 100.0;
		} else {
			inp.value = Math.round(100 * (100 / height) * inp.value) / 100.0;
		}
	}

}

function we_keep_ratio(inp, sel) {
	var _newVal;

	if (inp.value) {
		if (sel.options[sel.selectedIndex].value == "pixel") {
			_newVal = Math.round(inp.value);
		} else {
			_newVal = Math.round(100 * inp.value) / 100.0;
		}
		if (_newVal != inp.value) {
			inp.value = _newVal;
		}
	}

	if (inp.form.ratio.checked) {
		var inp_change = null;
		var sel_change = null;
		var ratio = null;
		var org = null;

		if (inp.name == "width") {
			ratio = ratio_hw;
			inp_change = inp.form.height;
			sel_change = inp.form.heightSelect;
			org = width;
		} else {
			ratio = ratio_wh;
			inp_change = inp.form.width;
			sel_change = inp.form.widthSelect;
			org = height;
		}
		if (sel_change.options[sel_change.selectedIndex].value == "pixel") {
			if (sel.options[sel.selectedIndex].value == "pixel") {
				_newVal = Math.round(inp.value * ratio);
			} else {
				_newVal = Math.round((org / 100) * inp.value * ratio);
			}
		} else {
			if (sel.options[sel.selectedIndex].value == "percent") {
				_newVal = inp.value;
			} else {
				_newVal = Math.round(100 * (100 / org) * inp.value * ratio) / 100.0;
			}
		}
		if (inp_change.value != _newVal) {
			inp_change.value = _newVal;
		}
	}
}
