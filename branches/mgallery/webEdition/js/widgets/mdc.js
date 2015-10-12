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
function toggle(id) {
	var elem = document.getElementById(id);
	if (elem) {
		if (elem.style.display == 'none')
			elem.style.display = 'block';
		else
			elem.style.display = 'none';
	}
}

function setVisible(id, visible) {
	var elem = document.getElementById(id);
	if (elem) {
		elem.style.display = (visible ? 'block' : 'none');
	}
}

function setPresentation(type) {

}

function closeAllSelection() {
	setVisible('dynamic', false);
	setVisible('static', false);
}

function getCsv(bTbl) {
	var iFolderID = _fo.FolderID.value;
	var sFolderPath = _fo.FolderPath.value;
	var iDtOrCls = (bTbl) ? _fo.classID.value : _fo.DocTypeID.value;
	var sCats = '';
	for (var j = 0; j < categories_edit.itemCount; j++) {
		sCats += opener.base64_encode(categories_edit.form.elements[categories_edit.name + '_variant0_' + categories_edit.name + '_item' + j].value);
		if (j < categories_edit.itemCount - 1)
			sCats += ',';
	}
	var sCsv = iFolderID + ',' + sFolderPath + ';' + iDtOrCls + ';' + sCats;
	return sCsv;
}

function getTreeSelected() {
	var sCsvIds = '';
	var iTemsLen = SelectedItems[table].length;
	for (var i = 0; i < iTemsLen; i++) {
		sCsvIds += SelectedItems[table][i];
		if (i < iTemsLen - 1 && SelectedItems[table][i] !== undefined && SelectedItems[table][i] !== '')
			sCsvIds += ',';
	}
	return sCsvIds;
}

function preview() {
	var sTitle = _fo.title.value;
	var sSel = (_fo.Selection.selectedIndex) ? '1' : '0';
	var sSwitch = (_fo.headerSwitch.selectedIndex) ? '1' : '0';
	var sCsv = (parseInt(sSel)) ? getTreeSelected() : getCsv(parseInt(sSwitch));
	previewPrefs();
	opener.rpc(sSel + sSwitch, (sCsv) ? sCsv : '', '', '', sTitle, _sObjId, _sMdcInc);
}

function exit_close() {
	var sTitle = _fo.elements.title.value;
	var sSel = (_fo.Selection.selectedIndex) ? '1' : '0';
	var sSwitch = (_fo.headerSwitch.selectedIndex) ? '1' : '0';
	var sCsv = (parseInt(sSel)) ? getTreeSelected() : getCsv(parseInt(sSwitch));
	var aInitCsv = _sInitCsv_.split(';');
	var sInitTitle = opener.base64_decode(aInitCsv[0]);
	if ((sInitTitle !== '' && sInitTitle != sTitle) || aInitCsv[1] != sSel + sSwitch || aInitCsv[2] != sCsv) {
		opener.rpc(aInitCsv[1], aInitCsv[2], '', '', sInitTitle, _sObjId, _sMdcInc);
	}
	exitPrefs();
	self.close();
}


function we_submit() {
	var bSelection = _fo.Selection.selectedIndex;
	var bSelType = _fo.headerSwitch.selectedIndex;
	_fo.action = WE().consts.dirs.WE_INCLUDES_DIR + 'we_widgets/dlg/mdc.php?we_cmd[0]=' + _sObjId + '&we_cmd[1]=' + opener.base64_encode(_fo.title.value) + ';' +
					(bSelection ? '1' : '0') + (bSelType ? '1' : '0') + ';' + (bSelection ? getTreeSelected() : '');
	_fo.method = 'post';
	_fo.submit();
}


function removeAllCats() {
	if (categories_edit.itemCount > 0) {
		while (categories_edit.itemCount > 0) {
			categories_edit.delItem(categories_edit.itemCount);
		}
	}
}

function addCat(paths) {
	var path = paths.split(',');
	var found = false;
	var j = 0;
	for (var i = 0; i < path.length; i++) {
		if (path[i] != '') {
			found = false;
			for (j = 0; j < categories_edit.itemCount; j++) {
				if (categories_edit.form.elements[categories_edit.name + '_variant0_' + categories_edit.name + '_item' + j].value == path[i]) {
					found = true;
				}
			}
			if (!found) {
				categories_edit.addItem();
				categories_edit.setItem(0, (categories_edit.itemCount - 1), path[i]);
			}
		}
	}
	categories_edit.showVariant(0);
}

function save() {
	var sTitle = _fo.title.value;
	var sSel = (_fo.Selection.selectedIndex) ? '1' : '0';
	var sSwitch = (_fo.headerSwitch.selectedIndex) ? '1' : '0';
	var sCsv = (parseInt(sSel)) ? getTreeSelected() : getCsv(parseInt(sSwitch));
	opener.rpc(sSel + sSwitch, sCsv, '', '', sTitle, _sObjId, _sMdcInc);
	_oCsv_.value = opener.base64_encode(sTitle) + ';' + sSel + sSwitch + ';' + sCsv;
	WE().util.showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, top.window);
	self.close();
}
