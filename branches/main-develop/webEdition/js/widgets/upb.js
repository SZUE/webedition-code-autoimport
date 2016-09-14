/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software, you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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
var _oCsv_;
var _sInitCsv_;
var _bPrev = false;
var _sLastPrevCsv = '';

function init() {
	_fo = document.forms[0];
	_oCsv_ = opener.document.getElementById(prefs._sObjId + '_csv');
	var sCsv = _oCsv_.value;
	_sInitCsv_ = sCsv;
	var oChbxType = _fo.elements.chbx_type;
	var iChbxTypeLen = oChbxType.length;
	if (iChbxTypeLen !== undefined) {
		for (var i = iChbxTypeLen - 1; i >= 0; i--) {
			oChbxType[i].checked = (parseInt(sCsv.charAt(i))) ? true : false;
		}
	} else {
		oChbxType.checked = (parseInt(sCsv.charAt(0))) ? true : false;
	}
	initPrefs();
}

function getBinary() {
	var oChbx = _fo.elements.chbx_type;
	if (WE().consts.tables.FILE_TABLE && WE().consts.tables.OBJECT_FILES_TABLE !== 'OBJECT_FILES_TABLE' && WE().util.hasPerm('CAN_SEE_OBJECTFILES')) {
		var iChbxLen = oChbx.length;
		var sBinary = '';
		for (var i = 0; i < iChbxLen; i++) {
			sBinary += (oChbx[i].checked) ? '1' : '0';
		}
		return sBinary;

	}
	return (oChbx.checked) ? '10' : '00';
}

function save() {
	var sCsv = getBinary();
	_oCsv_.value = sCsv;
	if ((!_bPrev && _sInitCsv_ != sCsv) || (_bPrev && _sLastPrevCsv != sCsv)) {
		opener.rpc(sCsv, '', '', '', '', prefs._sObjId);
	}
	previewPrefs();
	top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
	self.close();
}

function preview() {
	_bPrev = true;
	var sCsv = getBinary();
	_sLastPrevCsv = sCsv;
	previewPrefs();
	opener.rpc(sCsv, '', '', '', '', prefs._sObjId);
}

function exit_close() {
	if (_sInitCsv_ != getBinary() && _bPrev) {
		opener.rpc(_sInitCsv_, '', '', '', '', prefs._sObjId);
	}
	exitPrefs();
	self.close();
}