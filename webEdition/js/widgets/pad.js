/* global WE, top,weCombobox, prefs */

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
var _sInitTitle;
var _sInitBin;
var _sPadInc = 'pad/pad';
var _oSctDate;
var _aRdo = ['sort', 'display', 'date', 'prio'];
var _lastPreviewCsv = '';

function weEntity2char(weString) {
	weString = weString.replace('&lt;', '<');
	weString = weString.replace('&gt;', '>');
	return weString;
}

function weChar2entity(weString) {
	weString = weString.replace('<', '&lt;');
	weString = weString.replace('>', '&gt;');
	return weString;
}

function getCls() {
	return window.parent.document.getElementById(_sObjId + '_cls').value;
}
// displays the note dialog on click on a note
function selectNote(id) {
	var fo = document.forms[0];
	if (!isHotNote()) {
		cancelNote();
		setColor(document.getElementById(id + '_tr'), id, '#EDEDED');
		fo.elements.mark.value = id;
		populate(id);
	}
}
// displays the note dialog on click the add note button
function displayNote() {
	document.getElementById('view').style.display = 'none';
	document.getElementById('notices').style.height = '90px';
	document.getElementById('props').style.display = 'block';
	toggleTblValidity();
}
//close a open note
function cancelNote() {
	var fo = document.forms[0];
	document.getElementById('props').style.display = 'none';
	document.getElementById('notices').style.height = '250px';
	document.getElementById('view').style.display = 'block';
	var oMark = fo.elements.mark;
	var mark = oMark.value;
	if (mark !== '') {
		oMark.value = '';
		setColor(document.getElementById(mark + '_tr'), mark, '#FFFFFF');
	}
	unpopulate();
	WE().layout.button.switch_button_state(document, 'delete', 'disabled');
}
// deletes a note
function deleteNote() {
	var fo = document.forms[0];
	var mark = fo.elements.mark.value;
	var q_ID = document.getElementById(mark + '_ID').value;
	WE().layout.cockpitFrame.rpc(_ttlB64Esc.concat(',' + _sInitProps), q_ID, 'delete', '', _ttlB64Esc, _sObjId);
}

function isHotNote() {
	var fo = document.forms[0];
	var _id = fo.elements.mark.value;
	if (_id === '') {
		return false;
	}
	var q_init = getInitialQueryById(_id);

	var q_curr = getCurrentQuery();
	var idx = ['Title', 'Text', 'Priority', 'Validity', 'ValidFrom', 'ValidUntil'];
	var idx_len = idx.length;
	for (var i = 0; i < idx_len; i++) {
		if (q_init[idx[i]] != q_curr[idx[i]]) {
			return true;
		}
	}
	return false;
}

function getInitialQueryById(id) {
	return {
		Validity: document.getElementById(id + '_Valid').value,
		ValidFrom: document.getElementById(id + '_ValidFrom').value,
		ValidUntil: document.getElementById(id + '_ValidUntil').value,
		Priority: document.getElementById(id + '_Priority').value,
		Title: document.getElementById(id + '_Title').value,
		Text: document.getElementById(id + '_Text').value
	};
}

function getCurrentQuery() {
	var fo = document.forms[0];
	var oSctValid = fo.elements.sct_valid;
	var validSel = oSctValid.options[oSctValid.selectedIndex].value;
	var oRdoPrio = fo.elements.rdo_prio;
	var sValidFrom = fo.elements.f_ValidFrom.value;
	var sValidUntil = fo.elements.f_ValidUntil.value;
	return {
		Validity: (validSel === "0") ? 'always' : ((validSel === "1") ? 'date' : 'period'),
		ValidFrom: convertDate(sValidFrom, '%Y-%m-%d'),
		ValidUntil: convertDate(sValidUntil, '%Y-%m-%d'),
		Priority: (oRdoPrio[0].checked) ? 'high' : (oRdoPrio[1].checked) ? 'medium' : 'low',
		Title: fo.elements.props_title.value,
		Text: fo.elements.props_text.value
	};
}

function populate(r) {
	var fo = document.forms[0];
	var sValidity = document.getElementById(r + '_Valid').value;
	var sValidityIndex = sValidity === 'always' ? 0 : (sValidity === 'date' ? 1 : 2);
	var oSctValid = fo.elements.sct_valid;
	var iSctValidLen = oSctValid.length;
	for (var i = iSctValidLen - 1; i >= 0; i--) {
		if (oSctValid.options[i].value == sValidityIndex) {
			oSctValid.options[i].selected = true;
			break;
		}
	}
	toggleTblValidity();
	fo.elements.f_ValidFrom.value = convertDate(document.getElementById(r + '_ValidFrom').value, '%d.%m.%Y');
	fo.elements.f_ValidUntil.value = convertDate(document.getElementById(r + '_ValidUntil').value, '%d.%m.%Y');
	var prio = document.getElementById(r + '_Priority').value;
	fo.elements.rdo_prio[prio == 'high' ? 0 : prio == 'medium' ? 1 : 2].checked = true;
	fo.elements.props_title.value = document.getElementById(r + '_Title').value;
	fo.elements.props_text.value = document.getElementById(r + '_Text').value;
	WE().layout.button.switch_button_state(document, 'delete', 'enabled');
	displayNote();
}

function unpopulate() {
	var fo = document.forms[0];
	var oSctValid = fo.elements.sct_valid;
	oSctValid.options[0].selected = true;
	fo.elements.f_ValidFrom.value = '';
	fo.elements.f_ValidUntil.value = '';
	fo.elements.rdo_prio[2].checked = true;
	fo.elements.props_title.value = '';
	fo.elements.props_text.value = '';
}

function setColor(theRow, theRowNum, newColor) {
	var fo = document.forms[0];
	var theCells = null;
	if (fo.elements.mark.value !== '' || theRow.style === undefined) {
		return false;
	}
	theCells = theRow.getElementsByTagName('td');
	var rowCellsCnt = theCells.length;
	var domDetect = (window.opera === undefined && theCells[0].getAttribute !== undefined);
	var c = null;
	if (domDetect) {
		for (c = 0; c < rowCellsCnt; c++) {
			theCells[c].setAttribute('bgcolor', newColor, 0);
		}
	} else {
		for (c = 0; c < rowCellsCnt; c++) {
			theCells[c].style.backgroundColor = newColor;
		}
	}
	return true;
}

function convertDate(sDate, sFormat) {
	var fixedImplode = '';
	var arr = sDate.split((sFormat == '%Y-%m-%d') ? '.' : '-');
	var separator = (sFormat == '%Y-%m-%d') ? '-' : '.';
	for (var x = arr.length - 1; x >= 0; x--) {
		fixedImplode += (separator + arr[x].toString());
	}
	fixedImplode = fixedImplode.substring(separator.length, fixedImplode.length);
	return fixedImplode;
}

function toggleTblValidity() {
	switch (getCurrentQuery().Validity) {
		case "always":
			document.getElementById("f_ValidFrom_cell").style.visibility = "hidden";
			document.getElementById("f_ValidUntil_cell").style.visibility = "hidden";
			break;
		case "date":
			document.getElementById("f_ValidFrom_cell").style.visibility = "visible";
			document.getElementById("f_ValidUntil_cell").style.visibility = "hidden";
			break;
		default:
			document.getElementById("f_ValidFrom_cell").style.visibility = "visible";
			document.getElementById("f_ValidUntil_cell").style.visibility = "visible";
	}
}

function init() {
	window.parent.rpcHandleResponse(_sType, _sObjId, document.getElementById(_sType), _sTb);
}

// saves a note, using the function rpc() in home.inc.php (750)
function saveNote() {
	var fo = document.forms[0],
		_id = fo.elements.mark.value,
		weValidFrom, weValidUntil;
	var q_init = (_id !== '' ?
		getInitialQueryById(_id) :
		{Validity: 'always', ValidFrom: '', ValidUntil: '', Priority: 'low', Title: '', Text: ''});
	var q_curr = getCurrentQuery();
	var hot = false;
	var idx = ['Title', 'Text', 'Priority', 'Validity', 'ValidFrom', 'ValidUntil'];
	var csv = '';
	var idx_len = idx.length;
	for (var i = 0; i < idx_len; i++) {
		if (q_init[idx[i]] != q_curr[idx[i]]) {
			hot = true;
		}
		csv += (idx[i] === 'Title' || idx[i] === 'Text') ? window.btoa(q_curr[idx[i]]) : q_curr[idx[i]];
		if (i < idx_len - 1) {
			csv += ';';
		}
	}

	if (_id !== '') {
		if (hot) {
			// update note

			if (q_curr.Validity == 'period') {
				weValidFrom = q_curr.ValidFrom.replace(/-/g, '');
				weValidUntil = q_curr.ValidUntil.replace(/-/g, '');
				if (weValidFrom > weValidUntil) {
					top.we_showMessage(WE().consts.g_l.cockpit.pad.until_befor_from, WE().consts.message.WE_MESSAGE_NOTICE, window);
					return false;
				}
			}
			if (q_curr.Title === '') {
				top.we_showMessage(WE().consts.g_l.cockpit.pad.title_empty, WE().consts.message.WE_MESSAGE_NOTICE, window);
				return false;
			}
			var q_ID = document.getElementById(_id + '_ID').value;
			WE().layout.cockpitFrame.rpc(_ttlB64Esc.concat(',' + _sInitProps), (q_ID + ';' + encodeURI(csv)), 'update', '', _ttlB64Esc, _sObjId, 'pad/pad', q_curr.Title, q_curr.Text);
		} else {
			top.we_showMessage(WE().consts.g_l.cockpit.pad.note_not_modified, WE().consts.message.WE_MESSAGE_NOTICE, window);
		}
		return;
	}
	if (hot) {
		// insert note
		if (q_curr.Validity == 'period') {
			weValidFrom = q_curr.ValidFrom.replace(/-/g, '');
			weValidUntil = q_curr.ValidUntil.replace(/-/g, '');
			if (weValidFrom > weValidUntil) {
				top.we_showMessage(WE().consts.g_l.cockpit.pad.until_befor_from, WE().consts.message.WE_MESSAGE_NOTICE, window);
				return false;
			} else if (!weValidFrom || !weValidUntil) {
				top.we_showMessage(WE().consts.g_l.cockpit.pad.date_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
				return false;
			}
		} else if (q_curr.Validity == 'date' && !q_curr.ValidFrom) {
			top.we_showMessage(WE().consts.g_l.cockpit.pad.date_empty, WE().consts.message.WE_MESSAGE_NOTICE, window);
			return false;
		}
		if (q_curr.Title === '') {
			top.we_showMessage(WE().consts.g_l.cockpit.pad.title_empty, WE().consts.message.WE_MESSAGE_NOTICE, window);
			return false;
		}
		WE().layout.cockpitFrame.rpc(_ttlB64Esc.concat(',' + _sInitProps), csv, 'insert', '', _ttlB64Esc, _sObjId, 'pad/pad', q_curr.Title, q_curr.Text);
	} else {
		top.we_showMessage(WE().consts.g_l.cockpit.pad.title_empty, WE().consts.message.WE_MESSAGE_NOTICE, window);
	}
}

function initDlg() {
	var _fo = document.forms[0];
	_oCsv_ = opener.document.getElementById(prefs._sObjId + '_csv');
	_sInitCsv_ = _oCsv_.value;
	var aCsv = _sInitCsv_.split(',');
	_sInitTitle = window.atob(aCsv[0]);
	_sInitBin = aCsv[1];
	var i;
	for (i = 0; i < _aRdo.length; i++) {
		_fo.elements['rdo_' + _aRdo[i]][_sInitBin.charAt(i)].checked = true;
	}
	_fo.elements.sct_valid.options[_sInitBin.charAt(4)].selected = true;
	var oSctTitle = _fo.elements.sct_title;
	for (i = oSctTitle.length - 1; i >= 0; i--) {
		oSctTitle.options[i].selected = (oSctTitle.options[i].text == _sInitTitle) ? true : false;
	}
	initPrefs();
	var ComboBox = new weCombobox();
	ComboBox.init('title');
}

function getRdoChecked(sType) {
	var _fo = document.forms[0];
	var oRdo = _fo.elements['rdo_' + sType];
	var iRdoLen = oRdo.length;
	for (var i = 0; iRdoLen > i; i++) {
		if (oRdo[i].checked === true) {
			return i;
		}
	}
}

function getBitString() {
	var _fo = document.forms[0];
	var sBit = '';
	for (var i = 0; i < _aRdo.length; i++) {
		var iCurr = getRdoChecked(_aRdo[i]);
		sBit += (iCurr !== undefined) ? iCurr : '0';
	}
	sBit += _fo.elements.sct_valid.selectedIndex;
	return sBit;
}

function getTitle() {
	var _fo = document.forms[0];
	var oSctTitle = _fo.elements.sct_title;
	return oSctTitle[oSctTitle.selectedIndex].value;
}

function save() {
	var oCsv_ = opener.document.getElementById(_sObjId + '_csv');
	var sTitleEnc = window.btoa(getTitle());
	var sBit = getBitString();
	oCsv_.value = sTitleEnc.concat(',' + sBit);
	if ((_lastPreviewCsv !== '' && sTitleEnc.concat(',' + sBit) !== _lastPreviewCsv) ||
		(_lastPreviewCsv === '' && (_sInitTitle != getTitle() || _sInitBin != getBitString()))) {
		WE().layout.cockpitFrame.rpc(sTitleEnc.concat(',' + sBit), '', '', '', sTitleEnc, _sObjId);
	}
	window.opener.setPrefs(_sObjId, sBit, sTitleEnc);
	top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
	WE().layout.weNavigationHistory.navigateReload();
	window.close();
}

function preview() {
	var sTitleEnc = window.btoa(getTitle());
	var sBit = getBitString();
	WE().layout.cockpitFrame.rpc(sTitleEnc.concat(',' + sBit), '', '', '', sTitleEnc, prefs._sObjId);
	previewPrefs();
	_lastPreviewCsv = sTitleEnc.concat(',' + sBit);
}

function exit_close() {
	if (_lastPreviewCsv !== '' && (_sInitTitle != getTitle() || _sInitBin != getBitString())) {
		WE().layout.cockpitFrame.rpc(_sInitCsv_, '', '', '', window.btoa(_sInitTitle), prefs._sObjId);
	}
	exitPrefs();
	window.close();
}
