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

var _oCsv_;
var _sInitCsv_;
var _sMfdInc = 'mfd/mfd';
var _oSctDate;
var _oSctNumEntries;
var _bPrev = false;
var _sLastPreviewCsv = '';


function exit_close() {
	if (_bPrev && _sInitCsv_ != _sLastPreviewCsv) {
		var aCsv = _sInitCsv_.split(';');
		opener.rpc(aCsv[0], aCsv[1], aCsv[2], aCsv[3], aCsv[4], _sObjId, _sMfdInc);
	}
	exitPrefs();
	self.close();
}

function isNoError() {
	elem = document.getElementsByName('chbx_type');
	for (var i = 0; i < elem.length; i++) {
		if (elem[i].checked) {
			return true;
		}
	}
	return false;
}

function delUser(iUsrId) {
	var sUsers = '';
	var i;
	if (iUsrId != -1) {
		var aUsers = _sUsers.split(',');
		var iUsersLen = aUsers.length;
		for (i = 0; i < iUsersLen; i++) {
			if (aUsers[i] == iUsrId) {
				aUsers.splice(i, 1);
				iUsersLen--;
				break;
			}
		}
		for (i = 0; i < iUsersLen; i++) {
			sUsers += aUsers[i];
			if (i != iUsersLen - 1)
				sUsers += ',';
		}
	}
	_fo.action = '/webEdition/we_widgets/dlg/mfd.php?we_cmd[0]=' +
					_sObjId + '&we_cmd[1]=' + getBinary('type') + ';' + _oSctDate.selectedIndex + ';' + _oSctNumEntries.selectedIndex +
					';' + getBinary('display_opt') + ';' + sUsers;
	_fo.method = 'post';
	_fo.submit();
}

function getCsv() {
	return getBinary('type') + ';' + _oSctDate.selectedIndex + ';' + _oSctNumEntries.value +
					';' + getBinary('display_opt') + ';' + _sUsers;
}

function refresh(bRender) {
	if (bRender)
		_sLastPreviewCsv = getCsv();
	opener.rpc(getBinary('type'), _oSctDate.selectedIndex, _oSctNumEntries.selectedIndex, getBinary('display_opt'), _sUsers, _sObjId, _sMfdInc);
}

function init() {
	_fo = document.forms[0];
	_oCsv_ = opener.document.getElementById(_sObjId + '_csv');
	_sInitCsv_ = _oCsv_.value;
	_oSctDate = _fo.elements.sct_date;
	_oSctNumEntries = _fo.elements.sct_amount_entries;
	initPrefs();
}

function getBinary(postfix) {
	var sBinary = '';
	var oChbx = _fo.elements['chbx_' + postfix];
	var iChbxLen = oChbx.length;
	for (var i = 0; i < iChbxLen; i++) {
		sBinary += (oChbx[i].checked) ? '1' : '0';
	}
	return sBinary;
}

function addUserToField() {
	var iNewUsrId = _fo.elements.UserIDTmp.value;
	var aUsers = _sUsers.split(',');
	var iUsersLen = aUsers.length;
	var bUsrExists = false;
	for (var i = 0; i < iUsersLen; i++) {
		if (aUsers[i] == iNewUsrId) {
			bUsrExists = true;
			break;
		}
	}
	if (!bUsrExists) {//FIXME change this path!
		_fo.action = '/webEdition/we/include/we_widgets/dlg/mfd.php?we_cmd[0]=' +
						_sObjId + '&we_cmd[1]=' + getBinary('type') + ';' + _oSctDate.selectedIndex + ';' +
						_oSctNumEntries.selectedIndex + ';' + getBinary('display_opt') + ';' + _sUsers + ',' + iNewUsrId;
		_fo.method = 'post';
		_fo.submit();
	}
}

function save() {
	if (isNoError()) {
		var sCsv = getCsv();
		_oCsv_.value = sCsv;
		savePrefs();
		opener.saveSettings();
		if ((!_bPrev && sCsv != _sInitCsv_) || (_bPrev && sCsv != _sLastPreviewCsv)) {
			refresh(false);
		}
		top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
		self.close();
	} else {
		top.we_showMessage(g_l.no_type_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function preview() {
	if (isNoError()) {
		_bPrev = true;
		previewPrefs();
		refresh(true);
	} else {
		top.we_showMessage(g_l.no_type_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}
