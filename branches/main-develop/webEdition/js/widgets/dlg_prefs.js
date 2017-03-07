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
var prefs = WE().util.getDynamicVar(document, 'loadVarDlg_prefs', 'data-prefs');
var _sCls_ = opener.document.getElementById(prefs._sObjId + '_cls').value;

var _sInitCls, _oSctCls;
var _iInitCls = 0;

function initPrefs() {
	var _fo = document.forms[0];
	_oSctCls = _fo.elements.sct_cls;
	var iSctClsLen = _oSctCls.length;
	_sInitCls = _sCls_;
	for (var i = iSctClsLen - 1; i >= 0; i--) {
		if (_oSctCls.options[i].value == _sCls_) {
			_oSctCls.options[i].selected = true;
			_iInitCls = i;
		}
	}
}

function clip(unique) {
	var oText = document.getElementById("clip_" + unique);
	var oDiv = document.getElementById("div_" + unique);
	var oBtn = document.getElementById("btn_" + unique).getElementsByTagName("i")[0];

	if (prefs.clip[unique].state) {
		oText.innerHTML = prefs.clip[unique].textsmall;
		oDiv.style.display = "none";
		oBtn.classList.remove("fa-caret-down");
		oBtn.classList.add("fa-caret-right");
	} else {
		oText.innerHTML = prefs.clip[unique].text;
		oDiv.style.display = "block";
		oBtn.classList.remove("fa-caret-right");
		oBtn.classList.add("fa-caret-down");
	}
	prefs.clip[unique].state = !prefs.clip[unique].state;
}

function savePrefs() {
	window.opener.setTheme(prefs._sObjId, _oSctCls[_oSctCls.selectedIndex].value);
}

function previewPrefs() {
	window.opener.setTheme(prefs._sObjId, _oSctCls[_oSctCls.selectedIndex].value);
}

function exitPrefs() {
	var sTheme = _oSctCls[_oSctCls.selectedIndex].value;
	if (_sCls_ != sTheme) {
		sTheme = _oSctCls[_iInitCls].value;
		window.opener.setTheme(prefs._sObjId, sTheme);
	}
}

function we_cmd() {
	/*jshint validthis:true */
	//var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//	var url = WE().util.getWe_cmdArgsUrl(args);
	switch (args[0]) {
		case 'addUserToField':
			top.addUserToField();
			break;
	}
}