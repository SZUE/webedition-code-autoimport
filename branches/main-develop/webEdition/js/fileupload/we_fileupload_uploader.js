/* global WE, top */

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

function weFileupload_uploader_abstract(win) {
	var self = this;

	self.win = win;
	self.doc = win.document;

	self.controller = null;
	self.sender = null;
	self.view = null;
	self.imageEdit = null;
	self.utils = null;

	self.fieldName = '';
	self.genericFilename = '';
	self.fileuploadType = 'base';
	self.uiType = 'base';
	self.debug = false;
	self.EDIT_IMAGES_CLIENTSIDE = false;


	self.init = function (conf) {
		if (typeof conf !== 'undefined') {
			self.fieldName = conf.fieldName ? conf.fieldName : self.fieldName;
			self.uiType = conf.uiType ? conf.uiType : self.uiType;
			self.genericFilename = conf.genericFilename ? conf.genericFilename : self.genericFilename;
			self.EDIT_IMAGES_CLIENTSIDE = conf.clientsideImageEditing ? true : false;

			self.controller.init(conf);
			self.sender.init(conf);
			self.view.init(conf);
			self.imageEdit.init(conf);
			self.utils.init(conf);

			self.init_sub(conf);

			if(!self.onload()){
				self.win.addEventListener('load', self.onload, true);
			}
		}
	};

	self.onload = function () {
		if(!self.doc.getElementById(self.fieldName)){
			return false;
		}

		self.sender.onload();
		self.view.onload();
		self.controller.onload();

		self.onload_sub();

		return true;
	};

	self.onload_sub = function () {
		// to be overridden
	};

	self.startUpload = function () {
		if (self.sender.prepareUpload()) {
			//setTimeout(sender.sendNextFile, 100); // FIXME: check why this does not work!!
			window.setTimeout(function () {
				self.sender.sendNextFile();
			}, 100);
		} else {
			self.sender.processError({from: 'gui', msg: self.utils.gl.errorNoFileSelected});
		}
	};

	self.cancelUpload = function () {
		self.sender.cancel();
	};

	self.reset = function () {
		self.view.elems.fileSelect.value = null;
		self.view.repaintGUI({what: 'resetGui'});
	};

	self.deleteRow = function (index, but) {
		self.view.deleteRow(index, but);
	};

	self.getType = function () {
		return self.fileuploadType;
	};

	self.doUploadIfReady = function (callback) {
		callback();
		return;
	};

	self.reeditImage = function (index, general) {
		self.imageEdit.reeditImage(index, general);
	};

	self.openImageEditor = function(pos){
		self.imageEdit.openImageEditor(pos);
	};
}

function weFileupload_uploader_base(win, conf) {
	weFileupload_uploader_abstract.call(self, win, conf);

	var self = this;
	self.fileuploadType = 'base';

	self.controller = new weFileupload_controller_base(self);
	self.sender = new weFileupload_sender_base(self);
	self.view = new weFileupload_view_base(self);
	self.imageEdit = new weFileupload_imageEdit_base(self);
	self.utils = new weFileupload_utils_base(self);

	self.init = function (conf) {
		self.init_abstract(conf);
		self.view.uploadBtnName = conf.uploadBtnName ? conf.uploadBtnName : self.view.uploadBtnName;
		self.view.isInternalBtnUpload = conf.isInternalBtnUpload ? conf.isInternalBtnUpload : self.view.isInternalBtnUpload;
		self.view.disableUploadBtnOnInit = conf.disableUploadBtnOnInit ? conf.disableUploadBtnOnInit : false;
	};

	self.onload = function () {
		if(!self.onload_abstract(self)){
			return false;
		}

		//self.view.onload();
		self.controller.checkIsPresetFiles();

		return true;
	};
}
weFileupload_uploader_base.prototype = Object.create(weFileupload_uploader_abstract.prototype);
weFileupload_uploader_base.prototype.constructor = weFileupload_uploader_base;

function weFileupload_uploader_bindoc(win) {
	var self = this;
	weFileupload_uploader_abstract.call(self, win);

	self.fileuploadType = 'binDoc';

	self.controller = new weFileupload_controller_bindoc(self);
	self.sender = new weFileupload_sender_bindoc(self);
	self.view = new weFileupload_view_bindoc(self);
	self.imageEdit = new weFileupload_imageEdit_bindoc(self);
	self.utils = new weFileupload_utils_bindoc(self);

	self.init_sub = function (conf) {
		var sender = self.sender;
		var view = self.view;

		self.fieldName = 'we_File';
		sender.form.action = conf.form.action ? conf.form.action : sender.form.action;

		view.uploadBtnName = conf.uploadBtnName ? conf.uploadBtnName : view.uploadBtnName;
		if (typeof conf.binDocProperties !== 'undefined') {
			view.icon = WE().util.getTreeIcon(conf.binDocProperties.ct);
			view.binDocType = conf.binDocProperties.type ? conf.binDocProperties.type : view.binDocType;
		} else {
			view.icon = top.WE().util.getTreeIcon('text/plain');
		}
	};

	self.onload_sub = function () {
		self.controller.checkIsPresetFiles(self);
	};
}
weFileupload_uploader_bindoc.prototype = Object.create(weFileupload_uploader_abstract.prototype);
weFileupload_uploader_bindoc.prototype.constructor = weFileupload_uploader_bindoc;

function weFileupload_uploader_import(win) {
	var self = this;
	weFileupload_uploader_abstract.call(self, win);
	self.fileuploadType = 'importer';

	self.controller = new weFileupload_controller_import(self);
	self.sender = new weFileupload_sender_import(self);
	self.view = new weFileupload_view_import(self);
	self.imageEdit = new weFileupload_imageEdit_import(self);
	self.utils = new weFileupload_utils_import(self);

	self.init_sub = function (conf) {
		if (typeof conf !== 'undefined') {
			self.sender.isGdOk = typeof conf.isGdOk !== 'undefined' ? conf.isGdOk : self.sender.isGdOk;
			self.view.htmlFileRow = conf.htmlFileRow ? conf.htmlFileRow : self.view.htmlFileRow;
			self.utils.fileTable = conf.fileTable ? conf.fileTable : self.view.fileTable;
		}
	};
}
weFileupload_uploader_import.prototype = Object.create(weFileupload_uploader_abstract.prototype);
weFileupload_uploader_import.prototype.constructor = weFileupload_uploader_import;

WE().layout.fileupload.getFileUpload = function(type, win) {

	switch(type){
		case 'base':
			return new weFileupload_uploader_base(win);
		case 'preview' :
		case 'wedoc' :
		case 'editor' :
			return new weFileupload_uploader_bindoc(win);
		case 'importer':
			return new weFileupload_uploader_import(win);
	}
};
