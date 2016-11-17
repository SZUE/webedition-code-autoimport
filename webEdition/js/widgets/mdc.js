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
var widget = WE().util.getDynamicVar(document, 'loadVarWidget', 'data-widget');

var _oCsv_, _fo, _sCsv, _sInitCsv_, table, categories_edit, SelectedItems, _sInitTitle_;

function toggle(id) {
	var elem = document.getElementById(id);
	if (elem) {
		if (elem.style.display == 'none') {
			elem.style.display = 'block';
		} else {
			elem.style.display = 'none';
		}
	}
}

function setVisible(id, visible) {
	var elem = document.getElementById(id);
	if (elem) {
		elem.style.display = (visible ? 'block' : 'none');
	}
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
		sCats += WE().util.Base64.encode(categories_edit.form.elements[categories_edit.name + '_variant0_' + categories_edit.name + '_item' + j].value);
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
	opener.rpc(sSel + sSwitch, (sCsv) ? sCsv : '', '', '', sTitle, prefs._sObjId);
}

function exit_close() {
	var sTitle = _fo.elements.title.value;
	var sSel = (_fo.Selection.selectedIndex) ? '1' : '0';
	var sSwitch = (_fo.headerSwitch.selectedIndex) ? '1' : '0';
	var sCsv = (parseInt(sSel)) ? getTreeSelected() : getCsv(parseInt(sSwitch));
	var aInitCsv = _sInitCsv_.split(';');
	var sInitTitle = WE().util.Base64.decode(aInitCsv[0]);
	if ((sInitTitle !== '' && sInitTitle != sTitle) || aInitCsv[1] != sSel + sSwitch || aInitCsv[2] != sCsv) {
		opener.rpc(aInitCsv[1], aInitCsv[2], '', '', sInitTitle, prefs._sObjId);
	}
	exitPrefs();
	window.close();
}


function we_submit() {
	var bSelection = _fo.Selection.selectedIndex;
	var bSelType = _fo.headerSwitch.selectedIndex;
	_fo.action = WE().consts.dirs.WE_INCLUDES_DIR + 'we_widgets/dlg/mdc.php?we_cmd[0]=' + prefs._sObjId + '&we_cmd[1]=' + WE().util.Base64.encode(_fo.title.value) + ';' +
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
	var found = false;
	var j = 0;
	for (var i = 0; i < paths.length; i++) {
		if (paths[i] !== '') {
			found = false;
			for (j = 0; j < categories_edit.itemCount; j++) {
				if (categories_edit.form.elements[categories_edit.name + '_variant0_' + categories_edit.name + '_item' + j].value == paths[i]) {
					found = true;
				}
			}
			if (!found) {
				categories_edit.addItem();
				categories_edit.setItem(0, (categories_edit.itemCount - 1), paths[i]);
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
	opener.rpc(sSel + sSwitch, sCsv, '', '', sTitle, prefs._sObjId);
	_oCsv_.value = WE().util.Base64.encode(sTitle) + ';' + sSel + sSwitch + ';' + sCsv;
	WE().util.showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, top.window);
	window.close();
}



function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case 'we_selector_directory':
			new (WE().util.jsWindow)(this, url, 'we_fileselector', -1, -1, WE().consts.size.windowDirSelect.width, WE().consts.size.windowDirSelect.height, true, true, true, true);
			break;
		case 'we_selector_category':
			new (WE().util.jsWindow)(this, url, 'we_catselector', -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.height, true, true, true, true);
			break;
		case 'add_cat':
			this.addCat(args[1].allPaths);
		default:
			parent.we_cmd.apply(this, Array.prototype.slice.call(arguments));

	}
}

function init(tab, title, sBinary, _sCsv) {
	table = tab;
	_sInitTitle_ = title;
	_fo = document.forms[0];
	initPrefs();
	categories_edit = new (WE().util.multi_edit)("categories", window, 0, widget.cats.del, 390, false);
	categories_edit.addVariant();
	document.we_form.CategoriesControl.value = categories_edit.name;
	categories_edit.showVariant(0);

	_oCsv_ = opener.document.getElementById(prefs._sObjId + '_csv');
	_sInitCsv_ = _oCsv_.value;
	_sInitTitle_ = opener.document.getElementById(prefs._sObjId + '_prefix').value;
	_fo.elements.title.value = _sInitTitle_;
	var aInitCsv = _sInitCsv_.split(';');
	var dir = aInitCsv[2].split(',');
	if (parseInt(sBinary.substr(0, 1)) == parseInt(aInitCsv[1].substr(0, 1))) {
		if (parseInt(sBinary.substr(1)) == parseInt(aInitCsv[1].substr(1))) {
			_fo.FolderID.value = dir[0];
			_fo.FolderPath.value = dir[1];
			if (aInitCsv[3] !== undefined && aInitCsv[3] !== '') {
				var obj = parseInt(sBinary.substr(1)) ? _fo.classID : _fo.DocTypeID;
				obj.value = aInitCsv[3];
			}
			if (aInitCsv[4] !== undefined && aInitCsv[4] !== '') {
				addCat(WE().util.Base64.decode(aInitCsv[4]));
			}
		}
	}
	startTree();
	var aCsv = _sCsv.split(',');
	var aCsvLen = aCsv.length;
	for (var i = 0; i < aCsvLen; i++) {
		SelectedItems[tab][i] = aCsv[i];
	}
}
