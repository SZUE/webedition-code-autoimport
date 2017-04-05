/* global WE, top, prefs, treeData */

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
var widget = WE().util.getDynamicVar(document, 'loadVarWidget', 'data-widget');

var _oCsv_, _fo, _sCsv, _sInitCsv_, table, categories_edit, _sInitTitle_;

function toggle(id) {
	var elem = document.getElementById(id);
	if (elem) {
		if (elem.style.display === 'none') {
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
		sCats += window.btoa(categories_edit.form.elements[categories_edit.name + '_variant0_' + categories_edit.name + '_item' + j].value);
		if (j < categories_edit.itemCount - 1){
			sCats += ',';
		}
	}
	var sCsv = iFolderID + ',' + sFolderPath + ';' + iDtOrCls + ';' + sCats;
	return sCsv;
}

function getTreeSelected() {
	var sCsvIds = '';
	var iTemsLen = treeData.SelectedItems[table].length;
	for (var i = 0; i < iTemsLen; i++) {
		sCsvIds += treeData.SelectedItems[table][i];
		if (i < iTemsLen - 1 && treeData.SelectedItems[table][i] !== undefined && treeData.SelectedItems[table][i] !== ''){
			sCsvIds += ',';
		}
	}
	return sCsvIds;
}

function preview() {
	var sTitle = _fo.title.value;
	var sSel = (_fo.Selection.selectedIndex) ? '1' : '0';
	var sSwitch = (_fo.headerSwitch.selectedIndex) ? '1' : '0';
	var sCsv = (parseInt(sSel)) ? getTreeSelected() : getCsv(parseInt(sSwitch));
	top.previewPrefs();
	WE().layout.cockpitFrame.rpc(sSel + sSwitch, (sCsv) ? sCsv : '', '', '', sTitle, prefs._sObjId);
}

function exit_close() {
	var sTitle = _fo.elements.title.value;
	var sSel = (_fo.Selection.selectedIndex) ? '1' : '0';
	var sSwitch = (_fo.headerSwitch.selectedIndex) ? '1' : '0';
	var sCsv = (parseInt(sSel)) ? getTreeSelected() : getCsv(parseInt(sSwitch));
	var aInitCsv = _sInitCsv_.split(';');
	var sInitTitle = window.atob(aInitCsv[0]);
	if ((sInitTitle !== '' && sInitTitle !== sTitle) || aInitCsv[1] !== sSel + sSwitch || aInitCsv[2] !== sCsv) {
		WE().layout.cockpitFrame.rpc(aInitCsv[1], aInitCsv[2], '', '', sInitTitle, prefs._sObjId);
	}
	top.exitPrefs();
	window.close();
}


function we_submit() {
	var bSelection = _fo.Selection.selectedIndex;
	var bSelType = _fo.headerSwitch.selectedIndex;
	_fo.action = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=widget_cmd&we_cmd[1]=dialog&we_cmd[2]=we_widget_mdc&we_cmd[]=' + prefs._sObjId + '&we_cmd[]=' + window.btoa(_fo.title.value) + ';' +
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
	WE().layout.cockpitFrame.rpc(sSel + sSwitch, sCsv, '', '', sTitle, prefs._sObjId);
	_oCsv_.value = window.btoa(sTitle) + ';' + sSel + sSwitch + ';' + sCsv;
	WE().util.showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, top.window);
	window.close();
}



function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case 'we_selector_directory':
			new (WE().util.jsWindow)(caller, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case 'we_selector_category':
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case 'add_cat':
			addCat(args[1].allPaths);
			break;
		default:
			window.parent.we_cmd.apply(caller, Array.prototype.slice.call(arguments));

	}
}

function init(tab, title, sBinary, _sCsv) {
	table = tab;
	_sInitTitle_ = title;
	_fo = document.forms[0];
	top.initPrefs();
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
				addCat(window.atob(aInitCsv[4]));
			}
		}
	}
	startTree();
	var aCsv = _sCsv.split(',');
	var aCsvLen = aCsv.length;
	for (var i = 0; i < aCsvLen; i++) {
		treeData.SelectedItems[tab][i] = aCsv[i];
	}
}
