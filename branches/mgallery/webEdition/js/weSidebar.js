/**
 * webEdition CMS
 *
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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function weSidebar() {
}

//
// ----> Functions to load documents in webEdition
//

weSidebar.load = function (url, params) {
	var cmd = [
		'loadSidebarDocument',
		url,
		params
	];
	top.we_cmd(cmd[0], cmd[1], cmd[2]);
};

//
// ----> Functions to open, close and resize sidebar
//
weSidebar.open = function () {
	var cmd = Array();
// load document if needed
	if (arguments[0] !== undefined) {
		weSidebar.load(arguments[0]);
	} else if (arguments[0] == "default") {
		weSidebar.load('');
	}

// get width of sidebar frame
	if (arguments[1] !== undefined) {
		width = parseInt(arguments[1]);
	} else {
		width = top.WE().consts.size.sidebar.defaultWidth;
	}
	if (isNaN(width) || width < 100) {
		width = 100;
	}

	window.setTimeout("weSidebar.resize(" + width + ");", 200);
};

weSidebar.close = function () {
	top.document.getElementById("bm_content_frameDiv").style.right = "0px";
	top.document.getElementById("sidebarDiv").style.width = "0px";
};


weSidebar.resize = function (width) {
	top.document.getElementById("bm_content_frameDiv").style.right = width + "px";
	top.document.getElementById("sidebarDiv").style.width = width + "px";
};

weSidebar.reload = function () {
	top.weSidebarContent.location.reload();
};

//
// ----> Functions to open tabs in webEdition
//
weSidebar.openUrl = function (url) {
//	build command for this location
	top.we_cmd("open_url_in_editor", url);
};

weSidebar.openDocument = function (obj) {
	obj.table = top.WE().consts.tables.FILE_TABLE;
	obj.ct = (obj.ct === undefined ? top.WE().consts.contentTypes.WEDOCUMENT : obj.ct);
	weSidebar._open(obj);
};

weSidebar.openDocumentById = function () {
	obj.id = (arguments[0] === undefined ? 0 : arguments[0]);
	obj.ct = (arguments[1] === undefined ? top.WE().consts.contentTypes.WEDOCUMENT : arguments[1]);
	weSidebar._open(obj);
};

weSidebar.openTemplate = function (obj) {
	obj.table = top.WE().consts.tables.TEMPLATES_TABLE;
	obj.ct = top.WE().consts.contentTypes.TEMPLATE;
	weSidebar._open(obj);
};

weSidebar.openTemplateById = function () {
	obj.id = (arguments[0] === undefined ? 0 : arguments[0]);
	weSidebar._open(obj);
};

weSidebar.openObject = function (obj) {
	if (top.WE().consts.tables.OBJECT_FILES_TABLE) {
		obj.table = top.WE().consts.tables.OBJECT_FILES_TABLE;
		obj.ct = "objectFile";
		weSidebar._open(obj);
	}
};

weSidebar.openObjectById = function () {
	obj.id = (arguments[0] === undefined ? 0 : arguments[0]);
	weSidebar._open(obj);
};

weSidebar.openClass = function (obj) {
	if (top.WE().consts.tables.OBJECT_TABLE) {
		obj.table = top.WE().consts.tables.OBJECT_TABLE;
		obj.ct = "object";
		weSidebar._open(obj);
	}
};

weSidebar.openClassById = function () {
	obj.id = (arguments[0] === undefined ? 0 : arguments[0]);
	weSidebar._open(obj);
};

weSidebar.openCockpit = function () {
	obj.ct = "cockpit";
	obj.editcmd = "open_cockpit";
	weSidebar._open(obj);
};

//
// ----> Function to open navigation tool
//

weSidebar.openNavigation = function () {
	var cmd = Array();
	cmd[0] = 'navigation_edit';
	top.we_cmd(cmd[0]);
};

//
// ----> Function to open doctypes
//

weSidebar.openDoctypes = function () {
	var cmd = Array();
	cmd[0] = 'doctypes';
	top.we_cmd(cmd[0]);
};

//
// ----> Internal function
//
weSidebar._open = function (obj) {
	table = (obj.table === undefined ? "" : obj.table);
	id = (obj.id === undefined ? "" : obj.id);
	ct = (obj.ct === undefined ? "" : obj.ct);
	editcmd = (obj.editcmd === undefined ? "" : obj.editcmd);
	dt = (obj.dt === undefined ? "" : obj.dt);
	url = (obj.url === undefined ? "" : obj.url);
	code = (obj.code === undefined ? "" : obj.code);
	mode = (obj.mode === undefined ? "" : obj.mode);
	parameters = (obj.parameters === undefined ? "" : obj.parameters);
	top.weEditorFrameController.openDocument(table, id, ct, editcmd, dt, url, code, mode, parameters);
};