/* global WE, top */

/**
 webEdition CMS
 *
 * $Rev: 13229 $
 * $Author: lukasimhof $
 * $Date: 2017-01-11 14:49:48 +0100 (Mi, 11 Jan 2017) $
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

function weFileupload_utils_abstract(uploader) {
	var self = this;
	self.uploader = uploader;

	self.gl = {
		errorNoFileSelected: 'no file selected',
		errorFileSize: 'file size',
		errorFileType: 'file type',
		errorFileSizeType: 'file size and type',
		dropText: '',
		sizeTextOk: '',
		sizeTextNok: '',
		typeTextOk: '',
		typeTextNok: '',
		uploadCancelled: '',
		cancelled: '',
		doImport: '',
		file: '',
		btnClose: '',
		btnCancel: ''
	};

	self.init = function (conf) {
		self.controller = self.uploader.controller; // on init all components are initialized
		self.sender = self.uploader.sender;
		self.view = self.uploader.view;
		self.imageEdit = self.uploader.imageEdit;

		self.gl = WE().consts.g_l.fileupload; // FIXME: call translations on WE().consts.g_l.fileupload directly

		self.init_sub(conf);
	};

	self.init_sub = function () {
		// to be overridden
	};

	self.logTimeFromStart = function(text, resetStart, more){
		if(uploader.debug){
			var date = new Date();

			self.start = resetStart ? date.getTime() : self.start;
			self.uploader.win.console.log((text ? text : ''), (date.getTime() - self.start)/1000, more ? more : '');
		}
	};

	self.containsFiles = function (arr) {
		for (var i = 0; i < arr.length; i++) {
			if (typeof arr[i] === 'object' && arr[i] !== null) {
				return true;
			}
		}
		return false;
	};

	self.contains = function (a, obj) {
		var i = a.length;
		while (i--) {
			if (a[i] !== null && a[i].file.name === obj.name) {
				return true;
			}
		}

		return false;
	};

	self.checkFileType = function (type, name) {
		var n = name || '',
			ext = n.split('.').pop().toLowerCase(),
			tc = self.sender.typeCondition,
			typeGroup = type.split('/').shift() + '/*';

		ext = ext ? '.' + ext : '';

		// no restrictions
		if (!tc.accepted.cts && !tc.accepted.exts && !tc.forbidden.cts && !tc.forbidden.exts) {
			return 1; // 4: no restrictions
		}

		// check forbidden mimes and extensions
		if ((tc.forbidden.cts && type && (tc.forbidden.cts.indexOf(',' + type + ',') !== -1 || tc.forbidden.cts.indexOf(',' + typeGroup + ',') !== -1)) ||
						(tc.forbidden.exts && tc.forbidden.exts.indexOf(',' + ext + ',') !== -1) ||
						(tc.forbidden.exts4cts && tc.forbidden.exts4cts.indexOf(',' + ext + ',') !== -1)) {
			return 0;
		}

		// explicitly aloud
		if (tc.accepted.cts || tc.accepted.exts) {
			if (tc.accepted.cts && type && (tc.accepted.cts.indexOf(',' + type + ',') !== -1 || tc.accepted.cts.indexOf(',' + typeGroup + ',') !== -1)) {
				return 1; // 1: mime ok
			}
			if (tc.accepted.exts && tc.accepted.exts.indexOf(',' + ext + ',') !== -1) {
				return 2; // 2: ext ok
			}
			if (tc.accepted.exts4cts && tc.accepted.exts4cts.indexOf(',' + ext + ',') !== -1) {
				return 3; // 3: mime not ok but ext belongs to aloud mime
			}

			return 0; // it's not forbidden but does not match explicitly aloud mine/extensions
		}

		return 1; // it's not forbidden and there are no explicitly aloud mine/extensions
	};

	self.computeSize = function (size) {
		return (size / 1024 > 1023 ? ((size / 1024) / 1024).toFixed(1) + ' MB' : (size / 1024).toFixed(1) + ' KB');
	};

	self.dataURLToUInt8Array = function (dataURL) {
		var BASE64_MARKER = ';base64,',
			parts = dataURL.split(BASE64_MARKER),
			//contentType = parts[0].split(':')[1],
			raw = self.uploader.win.atob(parts[1]),
			rawLength = raw.length,
			uInt8Array = new Uint8Array(rawLength);

		for (var i = 0; i < rawLength; ++i) {
			uInt8Array[i] = raw.charCodeAt(i);
		}

		return uInt8Array;
		//return new Blob([uInt8Array], {type: contentType});
	};

	self.concatTypedArrays = function (resultConstructor, arrays) {
		var size = 0, i,
			pos = 0;

		for (i = 0; i < arrays.length; i++) {
			size += arrays[i].length;
		}

		var result = new resultConstructor(size);

		for (i = 0; i < arrays.length; i++) {
			result.set(arrays[i], pos);
			pos += arrays[i].length;
		}

		return result;
	};
}

function weFileupload_utils_base(uploader) {
	var self = this;
	weFileupload_utils_abstract.call(self, uploader);

	self.uploader = uploader;
}
weFileupload_utils_base.prototype = Object.create(weFileupload_utils_abstract.prototype);
weFileupload_utils_base.prototype.constructor = weFileupload_utils_base;

function weFileupload_utils_bindoc(uploader) {
	var self = this;
	weFileupload_utils_abstract.call(self, uploader);

	self.uploader = uploader;
}
weFileupload_utils_bindoc.prototype = Object.create(weFileupload_utils_abstract.prototype);
weFileupload_utils_bindoc.prototype.constructor = weFileupload_utils_bindoc;

function weFileupload_utils_import(uploader) {
	var self = this;
	weFileupload_utils_abstract.call(self, uploader);
}
weFileupload_utils_import.prototype = Object.create(weFileupload_utils_abstract.prototype);
weFileupload_utils_import.prototype.constructor = weFileupload_utils_import;

