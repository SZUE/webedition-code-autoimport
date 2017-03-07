/* global WE, top, prefs */

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
var _oCsv_;
var _sInitCsv_;
var _oSctDate;
var _bPrev = false;
var _sLastPreviewCsv = '';
var widget = WE().util.getDynamicVar(document, 'loadVarWidget', 'data-widget');

function init() {
	var _fo = document.forms[0];
	_oCsv_ = opener.document.getElementById(prefs._sObjId + '_csv');
	_sInitCsv_ = _oCsv_.value;
	_oSctDate = _fo.elements.sct_date;
	_fo.elements.revenueTarget.value = widget.sInitNum;
	top.initPrefs();
}

function getBinary(postfix) {
	var sBinary = '';
	var _fo = document.forms[0];
	var oChbx = _fo.elements['chbx_' + postfix];
	var iChbxLen = oChbx.length;
	for (var i = 0; i < iChbxLen; i++) {
		sBinary += (oChbx[i].checked) ? '1' : '0';
	}
	return sBinary;
}

function getCsv() {
	var _fo = document.forms[0];
	return getBinary('type') + ';' + _oSctDate.selectedIndex + ';' + _fo.elements.revenueTarget.value;
}

function refresh(bRender) {
	if (bRender) {
		_sLastPreviewCsv = getCsv();
	}
	WE().layout.cockpitFrame.rpc(getBinary('type'), _oSctDate.selectedIndex, document.forms[0].elements.revenueTarget.value, '', '', prefs._sObjId);
}

function exit_close() {
	if (_bPrev && _sInitCsv_ != _sLastPreviewCsv) {
		var aCsv = _sInitCsv_.split(';');
		WE().layout.cockpitFrame.rpc(aCsv[0], aCsv[1], aCsv[2], aCsv[3], aCsv[4], prefs._sObjId);
	}
	top.exitPrefs();
	window.close();
}

function save() {
	if (isNoError()) {
		var sCsv = getCsv();
		_oCsv_.value = sCsv;
		//top.savePrefs();
		window.opener.saveSettings();
		if ((!_bPrev && sCsv != _sInitCsv_) || (_bPrev && sCsv != _sLastPreviewCsv)) {
			refresh(false);
		}
		top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
		window.close();
	} else {
		WE().util.showMessage(WE().consts.g_l.cockpit.no_type_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function isNoError() {
	var chbx_type_checked = false;
	for (var chbx_type_i = 0; chbx_type_i < document.we_form.chbx_type.length; chbx_type_i++) {
		if (document.we_form.chbx_type[chbx_type_i].checked) {
			chbx_type_checked = true;
		}
	}
	return chbx_type_checked;
}

function preview() {
	if (isNoError()) {
		_bPrev = true;
		top.previewPrefs();
		refresh(true);
	} else {
		WE().util.showMessage(WE().consts.g_l.cockpit.no_type_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}
