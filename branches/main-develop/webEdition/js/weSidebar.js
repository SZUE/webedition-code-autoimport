/* global WE */

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
'use strict';

WE().layout.sidebar = {
//
// ----> Functions to load documents in webEdition
//
	widthBeforeDeleteMode: 0,
	load: function (url, params) {
		var cmd = [
			'loadSidebarDocument',
			url,
			params
		];
		top.we_cmd(cmd[0], cmd[1], cmd[2]);
	},
//
// ----> Functions to open, close and resize sidebar
//
	open: function (what, width) {
// load document if needed
		if (what !== undefined) {
			this.load(what);
		} else if (what === "default") {
			this.load('');
		}

// get width of sidebar frame
		if (width !== undefined) {
			width = parseInt(width);
		} else {
			width = WE().consts.size.sidebar.defaultWidth;
		}
		if (isNaN(width) || width < 100) {
			width = 100;
		}

		window.setTimeout(WE().layout.sidebar.resize, 200, width);
	},
	close: function () {
		top.document.getElementById("bm_content_frameDiv").style.right = "0px";
		top.document.getElementById("sidebarDiv").style.width = "0px";
	},
	resize: function (width) {
		top.document.getElementById("bm_content_frameDiv").style.right = width + "px";
		top.document.getElementById("sidebarDiv").style.width = width + "px";
	},
	reload: function () {
		top.weSidebarContent.location.reload();
	},
//
// ----> Functions to open tabs in webEdition
//
	openUrl: function (url) {
//	build command for this location
		top.we_cmd("open_url_in_editor", url);
	},
	openDocument: function (obj) {
		obj.table = WE().consts.tables.FILE_TABLE;
		obj.ct = (obj.ct === undefined ? WE().consts.contentTypes.WEDOCUMENT : obj.ct);
		this._open(obj);
	},
	openDocumentById: function (id, ct) {
		this._open({
			id: (id === undefined ? 0 : id),
			ct: (ct === undefined ? WE().consts.contentTypes.WEDOCUMENT : ct)
		});
	},
	openTemplate: function (obj) {
		this._open({
			table: WE().consts.tables.TEMPLATES_TABLE,
			ct: WE().consts.contentTypes.TEMPLATE
		});
	},
	openTemplateById: function (id) {
		this._open({
			id: (id === undefined ? 0 : id)
		});
	},
	openObject: function (obj) {
		if (WE().consts.tables.OBJECT_FILES_TABLE) {
			this._open({
				table: WE().consts.tables.OBJECT_FILES_TABLE,
				ct: "objectFile"
			});
		}
	},
	openObjectById: function (id) {
		this._open({
			id: (id === undefined ? 0 : id)
		});
	},
	openClass: function (obj) {
		if (WE().consts.tables.OBJECT_TABLE) {
			this._open({
				table: WE().consts.tables.OBJECT_TABLE,
				ct: "object"
			});
		}
	},
	openClassById: function (id) {
		this._open({
			id: (id === undefined ? 0 : id)
		});
	},
	openCockpit: function () {
		this._open({
			ct: "cockpit",
			editcmd: "open_cockpit"
		});
	},
//
// ----> Function to open navigation tool
//

	openNavigation: function () {
		top.we_cmd('navigation_edit');
	},
//
// ----> Function to open doctypes
//

	openDoctypes: function () {
		top.we_cmd('doctypes');
	},
//
// ----> Internal function
//
	_open: function (obj) {
		var table = (obj.table === undefined ? "" : obj.table),
			id = (obj.id === undefined ? "" : obj.id),
			ct = (obj.ct === undefined ? "" : obj.ct),
			editcmd = (obj.editcmd === undefined ? "" : obj.editcmd),
			dt = (obj.dt === undefined ? "" : obj.dt),
			url = (obj.url === undefined ? "" : obj.url),
			code = (obj.code === undefined ? "" : obj.code),
			mode = (obj.mode === undefined ? "" : obj.mode),
			parameters = (obj.parameters === undefined ? "" : obj.parameters);
		WE().layout.weEditorFrameController.openDocument(table, id, ct, editcmd, dt, url, code, mode, parameters);
	},
	getWidth: function () {
		var obj = document.getElementById("sidebarDiv");
		if (obj === undefined || obj === null) {
			return 0;
		}
		var w = obj.style.left;
		return w.substr(0, w.length - 2);
	},
	setWidth: function () {
		var obj = document.getElementById("sidebarDiv");
		if (obj !== undefined && obj !== null) {
			obj.style.left = top.w + "px";
		}
	}
};