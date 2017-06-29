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

function Fileupload_utils_abstract(uploader) {
	this.uploader = uploader;

	this.init = function (conf) {
		this.controller = this.uploader.controller; // on init all components are initialized
		this.sender = this.uploader.sender;
		this.view = this.uploader.view;
		this.imageEdit = this.uploader.imageEdit;
		this.init_sub(conf);
	};

	this.init_sub = function () {
		// to be overridden
	};

	this.logTimeFromStart = function(text, resetStart, more){
		if(uploader.debug){
			var date = new Date();

			this.start = resetStart ? date.getTime() : this.start;
			this.uploader.win.console.log((text ? text : ''), (date.getTime() - this.start)/1000, more ? more : '');
		}
	};

	this.containsFiles = function (arr) {
		for (var i = 0; i < arr.length; i++) {
			if (typeof arr[i] === 'object' && arr[i] !== null) {
				return true;
			}
		}
		return false;
	};

	this.contains = function (a, obj) {
		var i = a.length;
		while (i--) {
			if (a[i] !== null && a[i].file.name === obj.name) {
				return true;
			}
		}

		return false;
	};

	this.checkFileType = function (type, name) {
		var n = name || '',
			ext = n.split('.').pop().toLowerCase(),
			tc = this.sender.typeCondition,
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

	this.computeSize = function (size) {
		return (size / 1024 > 1023 ? ((size / 1024) / 1024).toFixed(1) + ' MB' : (size / 1024).toFixed(1) + ' KB');
	};

	this.dataURLToUInt8Array = function (dataURL) {
		var BASE64_MARKER = ';base64,',
			parts = dataURL.split(BASE64_MARKER),
			//contentType = parts[0].split(':')[1],
			raw = this.uploader.win.atob(parts[1]),
			rawLength = raw.length,
			uInt8Array = new Uint8Array(rawLength);

		for (var i = 0; i < rawLength; ++i) {
			uInt8Array[i] = raw.charCodeAt(i);
		}

		return uInt8Array;
		//return new Blob([uInt8Array], {type: contentType});
	};

	this.concatTypedArrays = function (resultConstructor, arrays) {
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

function Fileupload_utils_base(uploader) {
	Fileupload_utils_abstract.call(this, uploader);
	this.uploader = uploader;
}
Fileupload_utils_base.prototype = Object.create(Fileupload_utils_abstract.prototype);
Fileupload_utils_base.prototype.constructor = Fileupload_utils_base;

function Fileupload_utils_bindoc(uploader) {
	Fileupload_utils_abstract.call(this, uploader);
	this.uploader = uploader;
}
Fileupload_utils_bindoc.prototype = Object.create(Fileupload_utils_abstract.prototype);
Fileupload_utils_bindoc.prototype.constructor = Fileupload_utils_bindoc;

function Fileupload_utils_import(uploader) {
	Fileupload_utils_abstract.call(this, uploader);
}
Fileupload_utils_import.prototype = Object.create(Fileupload_utils_abstract.prototype);
Fileupload_utils_import.prototype.constructor = Fileupload_utils_import;

