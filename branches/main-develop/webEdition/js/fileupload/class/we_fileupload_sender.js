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

function weFileupload_sender_abstract(uploader) {
	var self = this;
	self.uploader = uploader;

	self.doCommitFile = true;
	self.chunkSize = 256 * 1024;
	self.responseClass = 'we_fileupload_ui_base';
	self.typeCondition = [];
	self.maxUploadSize = 0;
	self.form = {
		form: null,
		name: '',
		action: ''
	};
	self.callback = function () {
		self.uploader.doc.forms[0].submit();
	};
	self.nextCmd = '';
	self.dialogCallback = null;
	self.isUploading = false;
	self.isCancelled = false;
	self.preparedFiles = [];
	self.uploadFiles = [];
	self.currentFile = -1;
	self.totalFiles = 0;
	self.totalWeight = 0;
	self.currentWeight = 0;
	self.currentWeightTag = 0;//FIXME: find better name
	self.isAutostartPermitted = false;
	self.moreFieldsToAppend = [];

	self.init = function (conf) {
		self.controller = self.uploader.controller; // on init all components are initialized
		self.view = self.uploader.view;
		self.imageEdit = self.uploader.imageEdit;
		self.utils = self.uploader.utils;

		self.typeCondition = conf.typeCondition ? conf.typeCondition : self.typeCondition;
		self.doCommitFile = conf.doCommitFile !== undefined ? conf.doCommitFile : self.doCommitFile;
		self.chunkSize = typeof conf.chunkSize !== 'undefined' ? (conf.chunkSize * 1024) : self.chunkSize;
		self.callback = conf.callback ? conf.callback : self.callback;
		self.nextCmd = conf.nextCmd ? conf.nextCmd : self.nextCmd;
		self.responseClass = conf.responseClass ? conf.responseClass : self.responseClass;
		self.dialogCallback = conf.dialogCallback ? conf.dialogCallback : self.dialogCallback;
		self.maxUploadSize = typeof conf.maxUploadSize !== 'undefined' ? conf.maxUploadSize : self.maxUploadSize;
		if (typeof conf.form !== 'undefined') {
			self.form.name = conf.form.name ? conf.form.name: self.form.name;
			self.form.action = conf.form.action ? conf.form.action : self.form.action;
		}
		self.moreFieldsToAppend = conf.moreFieldsToAppend ? conf.moreFieldsToAppend : [];
	};

	self.onload = function () {
		self.form.form = self.form.name ? self.uploader.doc.forms[self.form.name] : self.uploader.doc.forms[0];
		self.form.action = self.form.action ? self.form.action : (self.form.form.action ? self.form.form.action : self.uploader.win.location.href);

		self.onload_sub();
	};

	self.onload_sub = function () {
		// to be overridden
	};

	self.resetParams = function () {
	};

	self.prepareUpload = function () {
		return true;
	};

	self.getValidEditOptions = function () {
		// to be overridden
		return false;
	};

	self.sendNextFile = function () {
		var cur, fr = null, cnt, editOptsLast, editOpts;

		if (self.uploadFiles.length > 0) {
			cur = self.uploadFiles[0];

			if (uploader.EDIT_IMAGES_CLIENTSIDE && self.imageEdit.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1){
				editOptsLast = cur.img.processedOptions;//JSON.parse(JSON.stringify(cur.img.editOptions));
				self.imageEdit.setImageEditOptionsFile(cur);
				editOpts = cur.img.editOptions;

				if(editOpts.doEdit){
					if(!cur.dataUrl || !cur.dataUrl.length ||
								editOpts.doEdit != editOptsLast.doEdit || // IMPORTANT: make config string: "1,w,1280,270,90" to compare!!
								editOpts.scaleWhat != editOptsLast.scaleWhat ||
								editOpts.scale != editOptsLast.scale ||
								editOpts.rotate != editOptsLast.rotate ||
								editOpts.quality != editOptsLast.quality) {

						// image is not yet edited or edited using options others than actually valid ones!
						cur.isEdited = false;
						self.preparedFiles[cur.preparedFilesIndex].img.callback = 'sendNextFile';
						self.preparedFiles[cur.preparedFilesIndex].tmpSize = self.preparedFiles[cur.preparedFilesIndex].size;

						// we process this image and call sendNextFile again!
						self.imageEdit.processSingleImage(self.preparedFiles[cur.preparedFilesIndex]);
						return;
					}

					// dataUrl of edited file exists: make dataArray, reinsert meta if any and go on:
					cur.dataArray = self.utils.dataURLToUInt8Array(cur.dataUrl);
					if(cur.type === 'image/jpeg' && cur.img.jpgCustomSegments){
						cur.dataArray = self.imageEdit.jpgInsertSegment(cur.dataArray, cur.img.jpgCustomSegments);
					} else if(cur.type === 'image/png' && cur.img.pngTextChunks){
						cur.dataArray = self.imageEdit.pngReinsertTextchunks(cur.dataArray, cur.img.pngTextChunks);
					}
				}
				cur.dataUrl = null;
			}
			self.currentFile = cur = this.uploadFiles.shift();

			if (cur.uploadConditionsOk) {
				self.isUploading = true;
				cur.isUploadStarted = true;
				self.view.repaintGUI({what: 'startSendFile'});

				if (cur.size <= self.chunkSize && !cur.img.editOptions.doEdit) {
					self.sendNextChunk(false);
				} else {
					if (self.view.elems.fileSelect && self.view.elems.fileSelect.value) {
						self.view.elems.fileSelect.value = '';
					}

					if (uploader.EDIT_IMAGES_CLIENTSIDE && self.imageEdit.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1 && cur.dataArray && cur.dataArray.length){
						// we have an edited image width dataArray already prepared
						self.sendNextChunk(true);
					} else {
						// we have an image not to be edited or other filetype
						fr = new FileReader();
						fr.onload = function (e) {
							cur.dataArray = new Uint8Array(e.target.result);
							self.sendNextChunk(true);
						};
						fr.readAsArrayBuffer(cur.file);
					}
				}
			} else {
				self.processError({from: 'gui', msg: cur.error});
			}
		} else {
			//all uploads done
			self.currentFile = null;
			self.isUploading = false;
			self.postProcess();
		}
	};

	self.sendNextChunk = function (split) {
		var resp, oldPos, blob,
			cur = self.currentFile; // when using short syntax in line 1156 we must change some this to that = sender. FIXME: WHY?!

		if (self.isCancelled) {
			self.isCancelled = false;
			return;
		}

		if (split) {
			if (cur.partNum < cur.totalParts) {
				oldPos = cur.currentPos;
				cur.currentPos = oldPos + self.chunkSize;
				cur.partNum++;
				blob = new Blob([cur.dataArray.subarray(oldPos, cur.currentPos)]);

				self.sendChunk(
					blob,
					(cur.fileName ? cur.fileName : cur.file.name),
					(cur.mimePHP !== 'none' ? cur.mimePHP : cur.file.type),
					(cur.partNum === cur.totalParts ? cur.lastChunkSize : self.chunkSize),
					cur.partNum,
					cur.totalParts,
					cur.fileNameTemp,
					cur.size
				);
			}
		} else {
			self.sendChunk(cur.file, (cur.fileName ? cur.fileName : cur.file.name), cur.file.type, cur.size, 1, 1, '', cur.size);
		}
	};

	self.sendChunk = function (part, fileName, fileCt, partSize, partNum, totalParts, fileNameTemp, fileSize) {
		var xhr = new XMLHttpRequest();
		var fd = new FormData();
		var fsize = fileSize || 1;

		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4) {
				if (xhr.status === 200) {
					var resp = JSON.parse(xhr.responseText);
					resp = resp.DataArray && resp.DataArray.data ? resp.DataArray.data : resp;
					self.processResponse(resp, {partSize: partSize, partNum: partNum, totalParts: totalParts});
				} else {
					self.processError({type: 'request', msg: 'http request failed'});
				}
			}
		};

		fileCt = fileCt ? fileCt : 'text/plain';
		fd.append('fileinputName', uploader.fieldName);
		fd.append('doCommitFile', self.doCommitFile);
		fd.append('genericFilename', uploader.genericFilename);
		fd.append('weResponseClass', self.responseClass);
		fd.append('wePartNum', partNum);
		fd.append('wePartCount', totalParts);
		fd.append('weFileNameTemp', fileNameTemp);
		fd.append('weFileSize', fsize);
		fd.append('weFileName', fileName);
		fd.append('weFileCt', fileCt);
		fd.append(self.currentFile.field !== undefined ? self.currentFile.field : uploader.fieldName, part, fileName);//FIXME: take fieldname allways from cur!
		fd = self.appendMoreData(fd);
		xhr.open('POST', self.form.action, true);
		xhr.send(fd);
	};

	self.appendMoreData = function (fd) {
		var doc = self.uploader.doc;

		for (var i = 0; i < self.moreFieldsToAppend.length; i++) {
			if (doc.we_form.elements[self.moreFieldsToAppend[i][0]]) {
				switch (this.moreFieldsToAppend[i][1]) {
					case 'check':
						fd.append(self.moreFieldsToAppend[i][0], ((doc.we_form.elements[self.moreFieldsToAppend[i][0]].checked) ? 1 : 0));
						break;
					case 'multi_select':
						var sel = doc.we_form.elements[self.moreFieldsToAppend[i][0]],
							opts = [], opt;

						for (var j = 0, len = sel.options.length; j < len; j++) {
							opt = sel.options[j];
							if (opt.selected) {
								opts.push(opt.value);
							}
						}
						fd.append(sel.id, opts);
						break;
					default:
						fd.append(self.moreFieldsToAppend[i][0], doc.we_form.elements[self.moreFieldsToAppend[i][0]].value);
				}
			}
		}

		return fd;
	};

	self.processResponse = function (resp, args) {
		if (!this.isCancelled) {
			var cur = this.currentFile;

			cur.fileNameTemp = resp.fileNameTemp;
			cur.mimePHP = resp.mimePhp;
			cur.currentWeightFile += args.partSize;
			self.currentWeight += args.partSize;

			switch (resp.status) {
				case 'continue':
					self.view.repaintGUI({what: 'chunkOK'});
					self.sendNextChunk(true);
					return;
				case 'success':
					self.currentWeightTag = self.currentWeight;
					cur.dataArray = null;
					cur.dataUrl = null;
					if(self.imageEdit.IS_MEMORY_MANAGMENT){
						self.imageEdit.memorymanagerUnregister(cur);
					}
					self.view.repaintGUI({what: 'chunkOK'});
					self.view.repaintGUI({what: 'fileOK'});
					self.doOnFileFinished(resp);//FIXME: make this part of postProcess(resp, fileonly=true)
					if (self.uploadFiles.length !== 0) {
						self.sendNextFile();
					} else {
						self.postProcess(resp);
					}
					return;
				case 'failure':
					self.currentWeight = self.currentWeightTag + cur.size;
					self.currentWeightTag = this.currentWeight;
					cur.dataArray = null;
					cur.dataUrl = null;
					if(self.imageEdit.IS_MEMORY_MANAGMENT){
						self.imageEdit.memorymanagerUnregister(cur);
					}
					self.view.repaintGUI({what: 'chunkNOK', message: resp.message});
					if (self.uploadFiles.length !== 0) {
						self.sendNextFile();
					} else {
						self.postProcess(resp);
					}
					return;
				default :
					return;
			}
		}
	};

	self.doOnFileFinished = function () {
		//to be overridden
	};

	self.postProcess = function (resp) {
		//to be overriden
	};

	self.cancel = function () {
		//to be overridden
	};
}

function weFileupload_sender_base(uploader) {
	var self = this;
	weFileupload_sender_abstract.call(self, uploader);

	self.uploader = uploader;

	self.totalWeight = 0;

	/* use parent (abstract)
	 this.appendMoreData = function (fd) {
	 for(var i = 0; i < this.moreFieldsToAppend.length; i++){
	 if(self.uploader.doc.we_form.elements[this.moreFieldsToAppend[i]]){
	 fd.append(this.moreFieldsToAppend[i], self.uploader.doc.we_form.elements[this.moreFieldsToAppend[i]].value);
	 }
	 }

	 return fd;
	 };
	 */

	self.postProcess = function (resp) {
		var cur = self.currentFile;
		var win = self.uploader.win;

		self.form.form.elements.weFileNameTemp.value = cur.fileNameTemp;
		self.form.form.elements.weFileCt.value = cur.mimePHP;
		self.form.form.elements.weFileName.value = cur.file.name;
		//this.form.form.elements.weIsUploadComplete.value = 1;

		if(self.nextCmd){
			window.setTimeout(function () {
				var tmp = self.nextCmd.split(',');
				tmp.splice(1, 0, self.resp);
				if(win.we_cmd){
					win.we_cmd.apply(win, tmp);
				} else { // FIXME: make sure we have a function we_cmd on every opener!
					win.top.we_cmd.apply(win, tmp);
				}
				win.close();
			}, 100);
		}
	};

	self.processError = function (arg) {
		switch (arg.from) {
			case 'gui' :
				WE().util.showMessage(arg.msg, 4, self.uploader.win);
				return;
			case 'request' :
				self.view.repaintGUI({what: 'fileNOK'});
				self.view.repaintGUI({what: 'resetGui'});
				return;
			default :
				return;
		}
	};

	self.resetParams = function () {
		self.preparedFiles = [];
		self.totalWeight = 0;
		self.isCancelled = false;
		self.view.repaintGUI({what: 'resetGui'});
	};

	self.prepareUpload = function () {
		if (self.preparedFiles.length < 1) {
			return false;
		}
		self.preparedFiles[0].preparedFilesIndex = 0;
		self.uploadFiles = [self.preparedFiles[0]];
		self.totalFiles = 1;
		self.totalWeight = self.preparedFiles[0].size;//size?
		self.currentWeight = 0;
		return true;
	};

	self.cancel = function () {
		if (!this.isUploading) {
			top.close();
		}
		self.isCancelled = true;
		self.isUploading = false;
		self.view.repaintGUI({what: 'cancelUpload'});
	};
}
weFileupload_sender_base.prototype = Object.create(weFileupload_sender_abstract.prototype);
weFileupload_sender_base.prototype.constructor = weFileupload_sender_base;

function weFileupload_sender_bindoc(uploader) {
	var self = this;
	weFileupload_sender_abstract.call(self, uploader);

	self.uploader = uploader;
	self.totalWeight = 0;

	self.doOnFileFinished = function (resp) {};

	self.postProcess = function (resp) {
		var win = self.uploader.win;
		self.preparedFiles = [];
		if (uploader.uiType !== 'wedoc') {
			var cur = this.currentFile;

			if (resp.status === 'failure') {
				self.resetParams();
			} else {
				self.form.form.elements.weFileNameTemp.value = cur.fileNameTemp;
				self.form.form.elements.weFileCt.value = cur.mimePHP;
				self.form.form.elements.weFileName.value = cur.file.name;
				self.currentFile = null;

				for(var k in resp.weDoc){
					resp[k]=resp.weDoc[k];
				}

				window.setTimeout(function () {
					var tmp = self.nextCmd.split(',');
					tmp.splice(1, 0, resp);
					win.opener.we_cmd.apply(win.opener, tmp);
					win.close();
				}, 100);

				//reload main tree and close!
				//or weFileUpload_instance.reset(); and let nextCmd close uploader!
			}
		} else if (resp.status === 'success') {
			self.currentFile = null;
			if (WE(true)) {
				win.we_cmd('update_file');
				WE().layout.we_setPath(null, resp.weDoc.path, resp.weDoc.text, 0, "published");
			}
		}
	};

	self.processError = function (arg) {
		switch (arg.from) {
			case 'gui' :
				WE().util.showMessage(arg.msg, 4, self.uploader.win);
				return;
			case 'request' :
				self.view.repaintGUI({what: 'fileNOK'});
				self.view.repaintGUI({what: 'resetGui'});
				return;
			default :
				return;
		}
	};

	self.resetParams = function () {
		self.preparedFiles = [];
		self.totalWeight = 0;
		self.isCancelled = false;
		self.view.repaintGUI({what: 'resetGui'});
	};

	self.prepareUpload = function () {
		if (self.preparedFiles.length < 1) {
			return false;
		}

		if (typeof self.preparedFiles[0] === 'object' && self.preparedFiles[0] !== null && self.preparedFiles[0].isUploadable) {
			self.preparedFiles[0].fileNum = 0;
			self.preparedFiles[0].preparedFilesIndex = 0;
			self.uploadFiles = [self.preparedFiles[0]];
			self.totalWeight = self.preparedFiles[0].size;
		}

		self.totalFiles = self.uploadFiles.length;

		if (self.totalFiles > 0) {
			self.currentWeight = 0;
			self.totalChunks = self.totalWeight / self.chunkSize;
			self.currentWeight = 0;
			self.currentWeightTag = 0;

			return true;
		}
		return false;
	};

	self.cancel = function () {
		self.currentFile = -1;
		self.isCancelled = true;
		self.isUploading = false;
		self.view.repaintGUI({what: 'resetGui'});
	};

}
weFileupload_sender_bindoc.prototype = Object.create(weFileupload_sender_abstract.prototype);
weFileupload_sender_bindoc.prototype.constructor = weFileupload_sender_bindoc;

function weFileupload_sender_import(uploader) {
	var self = this;
	weFileupload_sender_abstract.call(self, uploader);

	self.isGdOk = false;
	self.isCancelled = false;
	self.totalChunks = 0; //FIXME: apply consistent terminology to differ between currentfile and all files: and make it part of abstract
	self.mapFiles = [];

	self.prepareUpload = function (rePrepare) {
		if(rePrepare){ // first file has been reedited: we must recalculat totalWeight and set external progress
			self.totalWeight = self.totalWeight - self.uploadFiles[0].tmpSize + self.uploadFiles[0].size;
			self.totalChunks = self.totalWeight / self.chunkSize;
			self.view.repaintGUI({what: 'chunkOK'});
			return true;
		}

		if (self.currentFile === -1) {
			self.uploadFiles = [];
			this.mapFiles = [];
			for (var i = 0, c = 0; i < self.preparedFiles.length; i++) {
				if (typeof self.preparedFiles[i] === 'object' && self.preparedFiles[i] !== null && self.preparedFiles[i].isUploadable) {
					self.preparedFiles[i].fileNum = c++;
					self.preparedFiles[i].preparedFilesIndex = i;
					self.uploadFiles.push(self.preparedFiles[i]);
					self.mapFiles.push(i);
					self.totalWeight += self.preparedFiles[i].size;
					self.uploader.doc.getElementById('div_rowButtons_' + i).style.display = 'none';
					self.uploader.doc.getElementById('div_rowProgress_' + i).style.display = 'block';
				}
			}
			self.totalFiles = self.uploadFiles.length;

			if (self.totalFiles > 0) {
				self.currentWeight = 0;
				self.totalChunks = self.totalWeight / self.chunkSize;
				self.currentWeight = 0;
				self.currentWeightTag = 0;
				self.view.repaintGUI({what: 'startUpload'});

				return true;
			}
		}
		return false;
	};

	self.cancel = function () {
		if (!self.isUploading) {
			top.close();
		}
		self.isCancelled = true;
		self.isUploading = false;
		self.view.repaintGUI({what: 'cancelUpload'});
		self.postProcess('', true);
		//WE().util.showMessage(WE().consts.g_l.fileupload.uploadCancelled, 1, win);
	};

	self.appendMoreData = function (fd) {
		var sf = self.uploader.doc.we_form,
			cur = self.currentFile;

		fd.append('weFormNum', cur.fileNum + 1);
		fd.append('weFormCount', self.totalFiles);
		fd.append('we_cmd[0]', 'import_files');
		fd.append('step', 1);

		//if(!uploader.EDIT_IMAGES_CLIENTSIDE){
			fd.append('fu_file_sameName', sf.fu_file_sameName.value);
			fd.append('fu_file_parentID', sf.fu_file_parentID.value);
			fd.append('fu_doc_categories', sf.fu_doc_categories.value);
			fd.append('fu_doc_importMetadata', sf.fu_doc_importMetadata.value);
			fd.append('fu_doc_isSearchable', sf.fu_doc_isSearchable.value);
		//}

		if (self.imageEdit.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1) {
			fd.append('fu_doc_focusX', cur.img.focusX);
			fd.append('fu_doc_focusX', cur.img.focusY);
			fd.append('fu_doc_thumbs', sf.fu_doc_thumbs.value);
		}

		return fd;
	};

	self.postProcess = function (resp) {
		self.resp = resp;

		if (!self.isCancelled) {
			self.view.elems.footer.setProgress('', 100);
			self.view.elems.footer.setProgressText('progress_title', '');
			WE().util.showMessage(resp.completed.message, WE().consts.message.WE_MESSAGE_INFO, self.uploader.win);

			if(self.nextCmd){
				window.setTimeout(function () {
					var tmp = self.nextCmd.split(',');
					tmp.splice(1, 0, self.resp);
					top.we_cmd.apply(top, tmp);
				}, 100);
				//window.setTimeout(self.uploader.reset, 500);
				//window.setTimeout(function(){top.we_cmd('closeDialog');}, 1000);
			}
			window.setTimeout(self.uploader.reset, 1000);
		}
		self.view.reloadOpener();

		//reinitialize some vars to add and upload more files
		self.isUploading = false;
		self.resetSender();
		self.controller.setWeButtonText('cancel', 'close');
	};

	self.processError = function (arg) {
		switch (arg.from) {
			case 'gui' :
				WE().util.showMessage(arg.msg, top.WE().consts.message.WE_MESSAGE_ERROR, self.uploader.win);
				return;
			case 'request' :
				//view.repaintGUI({what : 'fileNOK'});
				//this.resetSender();
				return;
			default :
				return;
		}
	};

	self.resetSender = function () {
		for (var i = 0; i < self.preparedFiles.length; i++) {
			if (!self.isCancelled && self.preparedFiles[i]) {
				self.preparedFiles[i].isUploadable = false;
			} else {
				self.preparedFiles[i] = null;
			}
		}
		self.imageEdit.memorymanagerReregisterAll();
		self.uploadFiles = [];
		self.currentFile = -1;
		self.mapFiles = [];
		self.totalFiles = self.totalWeight = self.currentWeight = self.currentWeightTag = 0;
		self.view.elems.footer.setProgress("", 0);
		self.view.elems.extProgressDiv.style.display = 'none';
		self.controller.setWeButtonState('reset_btn', true);
		self.controller.setWeButtonState('browse_harddisk_btn', true);
	};

}
weFileupload_sender_import.prototype = Object.create(weFileupload_sender_abstract.prototype);
weFileupload_sender_import.prototype.constructor = weFileupload_sender_import;
