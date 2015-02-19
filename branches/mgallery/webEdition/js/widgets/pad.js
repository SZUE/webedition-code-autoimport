/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 9339 $
 * $Author: mokraemer $
 * $Date: 2015-02-19 00:34:49 +0100 (Do, 19. Feb 2015) $
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
	if (mark != '') {
		oMark.value = '';
		setColor(gel(mark + '_tr'), mark, '#FFFFFF');
	}
	unpopulate();
	switch_button_state('delete', 'delete_enabled', 'disabled');
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
	var q_init = (_id != '' ?
					getInitialQueryById(_id) :
					{'Validity': 'always', 'ValidFrom': '', 'ValidUntil': '', 'Priority': 'low', 'Title': '', 'Text': ''}
	);

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
	return asoc = {
		'Validity': gel(id + '_Valid').value,
		'ValidFrom': gel(id + '_ValidFrom').value,
		'ValidUntil': gel(id + '_ValidUntil').value,
		'Priority': gel(id + '_Priority').value,
		'Title': gel(id + '_Title').value,
		'Text': gel(id + '_Text').value
	};
}

function getCurrentQuery() {
	var fo = document.forms[0];
	var oSctValid = fo.elements.sct_valid;
	var validSel = oSctValid.options[oSctValid.selectedIndex].value;
	var oRdoPrio = fo.elements.rdo_prio;
	var sValidFrom = fo.elements.f_ValidFrom.value;
	var sValidUntil = fo.elements.f_ValidUntil.value;
	return asoc = {
		'Validity': (validSel == 0) ? 'always' : ((validSel == 1) ? 'date' : 'period'),
		'ValidFrom': convertDate(sValidFrom, '%Y-%m-%d'),
		'ValidUntil': convertDate(sValidUntil, '%Y-%m-%d'),
		'Priority': (oRdoPrio[0].checked) ? 'high' : (oRdoPrio[1].checked) ? 'medium' : 'low',
		'Title': fo.elements.props_title.value,
		'Text': fo.elements.props_text.value
	};
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
	switch_button_state('delete', 'delete_enabled', 'enabled');
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
	if (fo.elements.mark.value != '' || theRow.style === undefined) {
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
	var domDetect = null;
	if (window.opera === undefined && theCells[0].getAttribute !== undefined) {
		domDetect = true;
	} else {
		domDetect = false;
	}
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
	var arr = sDate.split((sFormat == '%Y-%m-%d') ? '.' : '-')
	separator = (sFormat == '%Y-%m-%d') ? '-' : '.';
	for (var x = arr.length - 1; x >= 0; x--) {
		fixedImplode += (separator + String(arr[x]));
	}
	fixedImplode = fixedImplode.substring(separator.length, fixedImplode.length);
	return fixedImplode;
}