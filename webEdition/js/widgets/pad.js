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
var _sInitTitle;
var _sInitBin;
var _sPadInc = 'pad/pad';
var _oSctDate;
var _aRdo = ['sort', 'display', 'date', 'prio'];
var _lastPreviewCsv = '';

function gel(id_) {
	return document.getElementById ? document.getElementById(id_) : null;
}

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

function calendarSetup() {
	Calendar.setup({inputField: 'f_ValidFrom', ifFormat: '%d.%m.%Y', button: 'date_picker_from', align: 'Tl', singleClick: true});
	Calendar.setup({inputField: 'f_ValidUntil', ifFormat: '%d.%m.%Y', button: 'date_picker_until', align: 'Tl', singleClick: true});
}

function getCls() {
	return parent.gel(_sObjId + '_cls').value;
}
// displays the note dialog on click on a note
function selectNote(id) {
	var fo = document.forms[0];
	if (!isHotNote()) {
		cancelNote();
		setColor(gel(id + '_tr'), id, '#EDEDED');
		fo.elements.mark.value = id;
		populate(id);
	}
}
// displays the note dialog on click the add note button
function displayNote() {
	gel('view').style.display = 'none';
	gel('notices').style.height = '90px';
	gel('props').style.display = 'block';
	toggleTblValidity();
}
//close a open note
function cancelNote() {
	fo = document.forms[0];
	gel('props').style.display = 'none';
	gel('notices').style.height = '250px';
	gel('view').style.display = 'block';
	var oMark = fo.elements.mark;
	var mark = oMark.value;
	if (mark !== '') {
		oMark.value = '';
		setColor(gel(mark + '_tr'), mark, '#FFFFFF');
	}
	unpopulate();
	WE().layout.button.switch_button_state(document, 'delete', 'disabled');
}
// deletes a note
function deleteNote() {
	var fo = document.forms[0];
	var mark = fo.elements.mark.value;
	var q_ID = gel(mark + '_ID').value;
	parent.rpc(_ttlB64Esc.concat(',' + _sInitProps), q_ID, 'delete', '', _ttlB64Esc, _sObjId, 'pad/pad');
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
	return (asoc = {
		'Validity': gel(id + '_Valid').value,
		'ValidFrom': gel(id + '_ValidFrom').value,
		'ValidUntil': gel(id + '_ValidUntil').value,
		'Priority': gel(id + '_Priority').value,
		'Title': gel(id + '_Title').value,
		'Text': gel(id + '_Text').value
	});
}

function getCurrentQuery() {
	var fo = document.forms[0];
	var oSctValid = fo.elements.sct_valid;
	var validSel = oSctValid.options[oSctValid.selectedIndex].value;
	var oRdoPrio = fo.elements.rdo_prio;
	var sValidFrom = fo.elements.f_ValidFrom.value;
	var sValidUntil = fo.elements.f_ValidUntil.value;
	return (asoc = {
		'Validity': (validSel == 0) ? 'always' : ((validSel == 1) ? 'date' : 'period'),
		'ValidFrom': convertDate(sValidFrom, '%Y-%m-%d'),
		'ValidUntil': convertDate(sValidUntil, '%Y-%m-%d'),
		'Priority': (oRdoPrio[0].checked) ? 'high' : (oRdoPrio[1].checked) ? 'medium' : 'low',
		'Title': fo.elements.props_title.value,
		'Text': fo.elements.props_text.value
	});
}

function populate(r) {
	fo = document.forms[0];
	var sValidity = gel(r + '_Valid').value;
	var sValidityIndex = sValidity == 'always' ? 0 : (sValidity == 'date' ? 1 : 2);
	var oSctValid = fo.elements.sct_valid;
	var iSctValidLen = oSctValid.length;
	for (var i = iSctValidLen - 1; i >= 0; i--) {
		if (oSctValid.options[i].value == sValidityIndex) {
			oSctValid.options[i].selected = true;
			break;
		}
	}
	toggleTblValidity();
	fo.elements.f_ValidFrom.value = convertDate(gel(r + '_ValidFrom').value, '%d.%m.%Y');
	fo.elements.f_ValidUntil.value = convertDate(gel(r + '_ValidUntil').value, '%d.%m.%Y');
	var prio = gel(r + '_Priority').value;
	fo.elements.rdo_prio[prio == 'high' ? 0 : prio == 'medium' ? 1 : 2].checked = true;
	fo.elements.props_title.value = gel(r + '_Title').value;
	fo.elements.props_text.value = gel(r + '_Text').value;
	WE().layout.button.switch_button_state(document, 'delete', 'enabled');
	displayNote();
}

function unpopulate() {
	fo = document.forms[0];
	var oSctValid = fo.elements.sct_valid;
	oSctValid.options[0].selected = true;
	fo.elements.f_ValidFrom.value = '';
	fo.elements.f_ValidUntil.value = '';
	fo.elements.rdo_prio[2].checked = true;
	fo.elements.props_title.value = '';
	fo.elements.props_text.value = '';
}

function setColor(theRow, theRowNum, newColor) {
	fo = document.forms[0];
	var theCells = null;
	if (fo.elements.mark.value !== '' || theRow.style === undefined) {
		return false;
	}
	if (document.getElementsByTagName !== undefined) {
		theCells = theRow.getElementsByTagName('td');
	} else if (theRow.cells !== undefined) {
		theCells = theRow.cells;
	} else {
		return false;
	}
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
	separator = (sFormat == '%Y-%m-%d') ? '-' : '.';
	for (var x = arr.length - 1; x >= 0; x--) {
		fixedImplode += (separator + String(arr[x]));
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
	parent.rpcHandleResponse(_sType, _sObjId, document.getElementById(_sType), _sTb);
}

// saves a note, using the function rpc() in home.inc.php (750)
function saveNote() {
	var fo = document.forms[0];
	var _id = fo.elements.mark.value;
	var q_init = (_id !== '' ?
					getInitialQueryById(_id) :
					{'Validity': 'always', 'ValidFrom': '', 'ValidUntil': '', 'Priority': 'low', 'Title': '', 'Text': ''});
	var q_curr = getCurrentQuery();
	var hot = false;
	var idx = ['Title', 'Text', 'Priority', 'Validity', 'ValidFrom', 'ValidUntil'];
	var csv = '';
	var idx_len = idx.length;
	for (var i = 0; i < idx_len; i++) {
		if (q_init[idx[i]] != q_curr[idx[i]]) {
			hot = true;
		}
		csv += (idx[i] === 'Title' || idx[i] === 'Text') ? parent.base64_encode(q_curr[idx[i]]) : q_curr[idx[i]];
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
					top.we_showMessage(WE.consts.g_l.cockpit.pad.until_befor_from, WE().consts.message.WE_MESSAGE_NOTICE, window);
					return false;
				}
			}
			if (q_curr.Title === '') {
				top.we_showMessage(WE.consts.g_l.cockpit.pad.title_empty, WE().consts.message.WE_MESSAGE_NOTICE, window);
				return false;
			}
			var q_ID = gel(_id + '_ID').value;
			parent.rpc(_ttlB64Esc.concat(',' + _sInitProps), (q_ID + ';' + encodeURI(csv)), 'update', '', _ttlB64Esc, _sObjId, 'pad/pad', escape(q_curr['Title']), escape(q_curr.Text));
		} else {
			top.we_showMessage(WE.consts.g_l.cockpit.pad.note_not_modified, WE().consts.message.WE_MESSAGE_NOTICE, window);
		}
		return;
	}
	if (hot) {
		// insert note
		if (q_curr.Validity == 'period') {
			weValidFrom = q_curr.ValidFrom.replace(/-/g, '');
			weValidUntil = q_curr.ValidUntil.replace(/-/g, '');
			if (weValidFrom > weValidUntil) {
				top.we_showMessage(WE.consts.g_l.cockpit.pad.until_befor_from, WE().consts.message.WE_MESSAGE_NOTICE, window);
				return false;
			} else if (!weValidFrom || !weValidUntil) {
				top.we_showMessage(WE.consts.g_l.cockpit.pad.date_empty, WE().consts.message.WE_MESSAGE_NOTICE, window);
				return false;
			}
		} else if (q_curr.Validity == 'date' && !q_curr.ValidFrom) {
			top.we_showMessage(WE.consts.g_l.cockpit.pad.date_empty, WE().consts.message.WE_MESSAGE_NOTICE, window);
			return false;
		}
		if (q_curr.Title === '') {
			top.we_showMessage(WE.consts.g_l.cockpit.pad.title_empty, WE().consts.message.WE_MESSAGE_NOTICE, window);
			return false;
		}
		parent.rpc(_ttlB64Esc.concat(',' + _sInitProps), escape(csv), 'insert', '', _ttlB64Esc, _sObjId, 'pad/pad', escape(q_curr['Title']), escape(q_curr['Text']));
	} else {
		top.we_showMessage(WE.consts.g_l.cockpit.pad.title_empty, WE().consts.message.WE_MESSAGE_NOTICE, window);
	}
}

function initDlg() {
	_fo = document.forms[0];
	_oCsv_ = opener.gel(_sObjId + '_csv');
	_sInitCsv_ = _oCsv_.value;
	var aCsv = _sInitCsv_.split(',');
	_sInitTitle = opener.base64_decode(aCsv[0]);
	_sInitBin = aCsv[1];
	for (var i = 0; i < _aRdo.length; i++) {
		_fo.elements['rdo_' + _aRdo[i]][_sInitBin.charAt(i)].checked = true;
	}
	_fo.elements.sct_valid.options[_sInitBin.charAt(4)].selected = true;
	var oSctTitle = _fo.elements.sct_title;
	for (var i = oSctTitle.length - 1; i >= 0; i--) {
		oSctTitle.options[i].selected = (oSctTitle.options[i].text == _sInitTitle) ? true : false;
	}
	initPrefs();
	ComboBox = new weCombobox();
	ComboBox.init('title');
}

function getRdoChecked(sType) {
	var oRdo = _fo.elements['rdo_' + sType];
	var iRdoLen = oRdo.length;
	for (var i = 0; iRdoLen > i; i++) {
		if (oRdo[i].checked == true)
			return i;
	}
}

function getBitString() {
	var sBit = '';
	for (var i = 0; i < _aRdo.length; i++) {
		var iCurr = getRdoChecked(_aRdo[i]);
		sBit += (iCurr !== undefined) ? iCurr : '0';
	}
	sBit += _fo.elements.sct_valid.selectedIndex;
	return sBit;
}

function getTitle() {
	var oSctTitle = _fo.elements.sct_title;
	return oSctTitle[oSctTitle.selectedIndex].value;
}

function save() {
	var oCsv_ = opener.gel(_sObjId + '_csv');
	var sTitleEnc = opener.base64_encode(getTitle());
	var sBit = getBitString();
	oCsv_.value = sTitleEnc.concat(',' + sBit);
	if ((_lastPreviewCsv != '' && sTitleEnc.concat(',' + sBit) != _lastPreviewCsv) ||
					(_lastPreviewCsv == '' && (_sInitTitle != getTitle() || _sInitBin != getBitString()))) {
		var sTitleEsc = escape(sTitleEnc);
		opener.rpc(sTitleEsc.concat(',' + sBit), '', '', '', sTitleEsc, _sObjId, _sPadInc);
	}
	opener.setPrefs(_sObjId, sBit, sTitleEnc);
	top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
	opener.top.weNavigationHistory.navigateReload();
	self.close();
}

function preview() {
	var sTitleEnc = opener.base64_encode(getTitle());
	var sTitleEsc = escape(sTitleEnc);
	var sBit = getBitString();
	opener.rpc(sTitleEsc.concat(',' + sBit), '', '', '', sTitleEsc, _sObjId, _sPadInc);
	previewPrefs();
	_lastPreviewCsv = sTitleEnc.concat(',' + sBit);
}

function exit_close() {
	if (_lastPreviewCsv != '' && (_sInitTitle != getTitle() || _sInitBin != getBitString())) {
		opener.rpc(_sInitCsv_, '', '', '', escape(opener.base64_encode(_sInitTitle)), _sObjId, _sPadInc);
	}
	exitPrefs();
	self.close();
}
