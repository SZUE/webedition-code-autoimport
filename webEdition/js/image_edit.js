/* global top,WE, doc */

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
'use strict';
var imgEdit = WE().util.getDynamicVar(document, 'loadImage_edit', 'data-imgEdit');

document.onkeyup = function (e) {
	if (e.keyCode === 13) {
		doOK();
	} else if (e.keyCode === 27) {
		top.close();
	}
};

function isSpecialKey(key) {
	return (key >= 63232 && key <= 63235) || key === 8 || key === 63272 || key === 0 || key === 13;
}

function we_switchPixelPercent(inp, sel) {

	if (sel.options[sel.selectedIndex].value === "pixel") {
		inp.value = (inp.name === "width" ?
						Math.round((imgEdit.width / 100) * inp.value) :
						Math.round((imgEdit.height / 100) * inp.value)
						);
	} else {
		inp.value = (inp.name === "width" ?
						Math.round(100 * (100 / imgEdit.width) * inp.value) / 100.0 :
						Math.round(100 * (100 / imgEdit.height) * inp.value) / 100.0
						);
	}
}

function we_keep_ratio(inp, sel) {
	var _newVal;

	if (inp.value) {
		if (sel.options[sel.selectedIndex].value === "pixel") {
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

		if (inp.name === "width") {
			ratio = imgEdit.ratio_hw;
			inp_change = inp.form.height;
			sel_change = inp.form.heightSelect;
			org = imgEdit.width;
		} else {
			ratio = imgEdit.ratio_wh;
			inp_change = inp.form.width;
			sel_change = inp.form.widthSelect;
			org = imgEdit.height;
		}
		if (sel_change.options[sel_change.selectedIndex].value === "pixel") {
			if (sel.options[sel.selectedIndex].value == "pixel") {
				_newVal = Math.round(inp.value * ratio);
			} else {
				_newVal = Math.round((org / 100) * inp.value * ratio);
			}
		} else {
			if (sel.options[sel.selectedIndex].value === "percent") {
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

function doOK() {
	switch (imgEdit.okCmd) {
		case 'ImageConvert':
			doOKImageConvert();
			break;
		case 'ResizeDialog':
			doOKResizeDialog();
			break;
		case 'ImageRotate':
			doOKImageRotate();
			break;
	}
}

function doOKImageConvert() {
	var f = document.we_form;
	var qual = f.quality.options[f.quality.selectedIndex].value;
	top.opener._EditorFrame.setEditorIsHot(true);
	top.opener.top.we_cmd("doImage_convertJPEG", qual);
	top.close();
}

function doOKResizeDialog() {
	var f = document.we_form;
	var qual = 8;

	if (f.width.value === "0" || f.height.value === "0" || f.width.value == "0%" || f.height.value == "0%") {
		WE().util.showMessage(WE().consts.g_l.alert.image_edit_null_not_allowed, WE().consts.message.WE_MESSAGE_ERROR, window);
		return;
	}
	var newWidth = (f.widthSelect.options[f.widthSelect.selectedIndex].value === "pixel") ? f.width.value : Math.round((imgEdit.width / 100) * f.width.value);
	var newHeight = (f.heightSelect.options[f.heightSelect.selectedIndex].value === "pixel") ? f.height.value : Math.round((imgEdit.height / 100) * f.height.value);
	if (doc.gdType === "jpg") {
		qual = f.quality.options[f.quality.selectedIndex].value;
	}
	top.opener._EditorFrame.setEditorIsHot(true);
	top.opener.we_cmd("resizeImage", newWidth, newHeight, qual);
	top.close();
}


function doOKImageRotate() {
	var f = document.we_form;
	var qual = 8;
	var degrees = 0;

	for (var i = 0; i < f.degrees.length; i++) {
		if (f.degrees[i].checked) {
			degrees = f.degrees[i].value;
			break;
		}
	}
	switch (parseInt(degrees)) {
		case 90:
		case 270:
			var tmp = imgEdit.width;
			imgEdit.width = imgEdit.height;
			imgEdit.height = tmp;
			break;
	}
	if (doc.gdType === "jpg") {
		qual = f.quality.options[f.quality.selectedIndex].value;
	}
	top.opener._EditorFrame.setEditorIsHot(true);
	top.opener.top.we_cmd("rotateImage", imgEdit.width, imgEdit.height, degrees, qual);
	top.close();
}
