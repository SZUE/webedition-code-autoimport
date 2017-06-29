/* global WE, top,Fileupload_controller_base,Fileupload_sender_base,Fileupload_view_base,Fileupload_imageEdit_base,Fileupload_utils_base,Fileupload_controller_bindoc,Fileupload_sender_bindoc,Fileupload_view_bindoc,Fileupload_imageEdit_bindoc,Fileupload_utils_bindoc, Fileupload_controller_import,Fileupload_sender_import,Fileupload_view_import,Fileupload_imageEdit_import,Fileupload_utils_import */

/**
 webEdition CMS
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

WE().util.loadConsts(document, 'g_l.fileupload');

function Fileupload_uploader_abstract(win) {

	this.win = win;
	this.doc = win.document;

	this.controller = null;
	this.sender = null;
	this.view = null;
	this.imageEdit = null;
	this.utils = null;

	this.fieldName = '';
	this.genericFilename = '';
	this.fileuploadType = 'base';
	this.uiType = 'base';
	this.debug = false;
	this.EDIT_IMAGES_CLIENTSIDE = false;


	this.init = function (conf) {
		if (typeof conf !== 'undefined') {
			this.fieldName = conf.fieldName ? conf.fieldName : this.fieldName;
			this.uiType = conf.uiType ? conf.uiType : this.uiType;
			this.genericFilename = conf.genericFilename ? conf.genericFilename : this.genericFilename;
			this.EDIT_IMAGES_CLIENTSIDE = conf.clientsideImageEditing ? true : false;

			this.controller.init(conf);
			this.sender.init(conf);
			this.view.init(conf);
			this.imageEdit.init(conf);
			this.utils.init(conf);

			this.init_sub(conf);

			if(!this.onload()){
				this.win.addEventListener('load', this.onload, true);
			}

			WE().util.setIconOfDocClass(this.doc, 'filedrag_set_icon');
		}
	};

	this.onload = function () {
		if(!this.doc.getElementById(this.fieldName)){
			return false;
		}

		this.sender.onload();
		this.view.onload();
		this.controller.onload();

		this.onload_sub();

		return true;
	};

	this.onload_sub = function () {
		// to be overridden
	};

	this.startUpload = function () {
		if (this.sender.prepareUpload()) {
			//setTimeout(sender.sendNextFile, 100); // FIXME: check why this does not work!!
			window.setTimeout(function () {
				this.sender.sendNextFile();
			}, 100);
		} else {
			this.sender.processError({from: 'gui', msg: WE().consts.g_l.fileupload.errorNoFileSelected});
		}
	};

	this.cancelUpload = function () {
		this.sender.cancel();
	};

	this.isUploading = function () {
		return this.sender.isUploading;
	};

	this.reset = function () {
		this.view.elems.fileSelect.value = null;
		this.view.repaintGUI({what: 'resetGui'});
	};

	this.deleteRow = function (index, but) {
		this.view.deleteRow(index, but);
	};

	this.getType = function () {
		return this.fileuploadType;
	};

	this.doUploadIfReady = function (callback) {
		callback();
		return;
	};

	this.reeditImage = function (index, general) {
		this.imageEdit.reeditImage(index, general);
	};

	this.openImageEditor = function(pos){
		this.imageEdit.openImageEditor(pos);
	};
}

function Fileupload_uploader_base(win, conf) {
	Fileupload_uploader_abstract.call(this, win, conf);

	this.fileuploadType = 'base';

	this.controller = new Fileupload_controller_base(this);
	this.sender = new Fileupload_sender_base(this);
	this.view = new Fileupload_view_base(this);
	this.imageEdit = new Fileupload_imageEdit_base(this);
	this.utils = new Fileupload_utils_base(this);

	this.init = function (conf) {
		this.init_abstract(conf);
		this.view.uploadBtnName = conf.uploadBtnName ? conf.uploadBtnName : this.view.uploadBtnName;
		this.view.isInternalBtnUpload = conf.isInternalBtnUpload ? conf.isInternalBtnUpload : this.view.isInternalBtnUpload;
		this.view.disableUploadBtnOnInit = conf.disableUploadBtnOnInit ? conf.disableUploadBtnOnInit : false;
	};

	this.onload = function () {
		if(!this.onload_abstract(this)){
			return false;
		}

		//self.view.onload();
		this.controller.checkIsPresetFiles();

		return true;
	};
}
Fileupload_uploader_base.prototype = Object.create(Fileupload_uploader_abstract.prototype);
Fileupload_uploader_base.prototype.constructor = Fileupload_uploader_base;

function Fileupload_uploader_bindoc(win) {
	Fileupload_uploader_abstract.call(this, win);

	this.fileuploadType = 'binDoc';

	this.controller = new Fileupload_controller_bindoc(this);
	this.sender = new Fileupload_sender_bindoc(this);
	this.view = new Fileupload_view_bindoc(this);
	this.imageEdit = new Fileupload_imageEdit_bindoc(this);
	this.utils = new Fileupload_utils_bindoc(this);

	this.init_sub = function (conf) {
		var sender = this.sender;
		var view = this.view;

		this.fieldName = 'we_File';
		sender.form.action = conf.form.action ? conf.form.action : sender.form.action;

		view.uploadBtnName = conf.uploadBtnName ? conf.uploadBtnName : view.uploadBtnName;
		if (typeof conf.binDocProperties !== 'undefined') {
			view.icon = WE().util.getTreeIcon(conf.binDocProperties.ct);
			view.binDocType = conf.binDocProperties.type ? conf.binDocProperties.type : view.binDocType;
		} else {
			view.icon = top.WE().util.getTreeIcon('text/plain');
		}
	};

	this.onload_sub = function () {
		this.controller.checkIsPresetFiles(this);
	};
}
Fileupload_uploader_bindoc.prototype = Object.create(Fileupload_uploader_abstract.prototype);
Fileupload_uploader_bindoc.prototype.constructor = Fileupload_uploader_bindoc;

function Fileupload_uploader_import(win) {
	Fileupload_uploader_abstract.call(this, win);
	this.fileuploadType = 'importer';

	this.controller = new Fileupload_controller_import(this);
	this.sender = new Fileupload_sender_import(this);
	this.view = new Fileupload_view_import(this);
	this.imageEdit = new Fileupload_imageEdit_import(this);
	this.utils = new Fileupload_utils_import(this);

	this.init_sub = function (conf) {
		if (typeof conf !== 'undefined') {
			this.sender.isGdOk = typeof conf.isGdOk !== 'undefined' ? conf.isGdOk : this.sender.isGdOk;
			this.view.htmlFileRow = conf.htmlFileRow ? conf.htmlFileRow : this.view.htmlFileRow;
			this.utils.fileTable = conf.fileTable ? conf.fileTable : this.view.fileTable;
		}
	};
}
Fileupload_uploader_import.prototype = Object.create(Fileupload_uploader_abstract.prototype);
Fileupload_uploader_import.prototype.constructor = Fileupload_uploader_import;

WE().layout.fileupload.getFileUpload = function(type, win) {

	switch(type){
		case 'base':
			return new Fileupload_uploader_base(win);
		case 'preview' :
		case 'wedoc' :
		case 'editor' :
			return new Fileupload_uploader_bindoc(win);
		case 'importer':
			return new Fileupload_uploader_import(win);
	}
};
