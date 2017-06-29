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

function Fileupload_sender_abstract(uploader) {
	this.uploader = uploader;

	this.doCommitFile = true;
	this.chunkSize = 256 * 1024;
	this.responseClass = 'we_fileupload_ui_base';
	this.typeCondition = [];
	this.maxUploadSize = 0;
	this.form = {
		form: null,
		name: '',
		action: ''
	};
	this.callback = function () {
		this.uploader.doc.forms[0].submit();
	};
	this.nextCmd = '';
	this.dialogCallback = null;
	this.isUploading = false;
	this.isCancelled = false;
	this.preparedFiles = [];
	this.uploadFiles = [];
	this.currentFile = -1;
	this.totalFiles = 0;
	this.totalWeight = 0;
	this.currentWeight = 0;
	this.currentWeightTag = 0;//FIXME: find better name
	this.isAutostartPermitted = false;
	this.moreFieldsToAppend = [];

	this.init = function (conf) {
		this.controller = this.uploader.controller; // on init all components are initialized
		this.view = this.uploader.view;
		this.imageEdit = this.uploader.imageEdit;
		this.utils = this.uploader.utils;

		this.typeCondition = conf.typeCondition ? conf.typeCondition : this.typeCondition;
		this.doCommitFile = conf.doCommitFile !== undefined ? conf.doCommitFile : this.doCommitFile;
		this.chunkSize = typeof conf.chunkSize !== 'undefined' ? (conf.chunkSize * 1024) : this.chunkSize;
		this.callback = conf.callback ? conf.callback : this.callback;
		this.nextCmd = conf.nextCmd ? conf.nextCmd : this.nextCmd;
		this.responseClass = conf.responseClass ? conf.responseClass : this.responseClass;
		this.dialogCallback = conf.dialogCallback ? conf.dialogCallback : this.dialogCallback;
		this.maxUploadSize = typeof conf.maxUploadSize !== 'undefined' ? conf.maxUploadSize : this.maxUploadSize;
		if (typeof conf.form !== 'undefined') {
			this.form.name = conf.form.name ? conf.form.name: this.form.name;
			this.form.action = conf.form.action ? conf.form.action : this.form.action;
		}
		this.moreFieldsToAppend = conf.moreFieldsToAppend ? conf.moreFieldsToAppend : [];
	};

	this.onload = function () {
		this.form.form = this.form.name ? this.uploader.doc.forms[this.form.name] : this.uploader.doc.forms[0];
		this.form.action = this.form.action ? this.form.action : (this.form.form.action ? this.form.form.action : this.uploader.win.location.href);

		this.onload_sub();
	};

	this.onload_sub = function () {
		// to be overridden
	};

	this.resetParams = function () {
	};

	this.prepareUpload = function () {
		return true;
	};

	this.getValidEditOptions = function () {
		// to be overridden
		return false;
	};

	this.sendNextFile = function () {
		var cur, fr = null, cnt, editOptsLast, editOpts;

		if (this.uploadFiles.length > 0) {
			cur = this.uploadFiles[0];

			if (uploader.EDIT_IMAGES_CLIENTSIDE && this.imageEdit.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1){
				editOptsLast = cur.img.processedOptions;//JSON.parse(JSON.stringify(cur.img.editOptions));
				this.imageEdit.setImageEditOptionsFile(cur);
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
						this.preparedFiles[cur.preparedFilesIndex].img.callback = 'sendNextFile';
						this.preparedFiles[cur.preparedFilesIndex].tmpSize = this.preparedFiles[cur.preparedFilesIndex].size;

						// we process this image and call sendNextFile again!
						this.imageEdit.processSingleImage(this.preparedFiles[cur.preparedFilesIndex]);
						return;
					}

					// dataUrl of edited file exists: make dataArray, reinsert meta if any and go on:
					cur.dataArray = this.utils.dataURLToUInt8Array(cur.dataUrl);
					if(cur.type === 'image/jpeg' && cur.img.jpgCustomSegments){
						cur.dataArray = this.imageEdit.jpgInsertSegment(cur.dataArray, cur.img.jpgCustomSegments);
					} else if(cur.type === 'image/png' && cur.img.pngTextChunks){
						cur.dataArray = this.imageEdit.pngReinsertTextchunks(cur.dataArray, cur.img.pngTextChunks);
					}
				}
				cur.dataUrl = null;
			}
			this.currentFile = cur = this.uploadFiles.shift();

			if (cur.uploadConditionsOk) {
				this.isUploading = true;
				cur.isUploadStarted = true;
				this.view.repaintGUI({what: 'startSendFile'});

				if (cur.size <= this.chunkSize && !cur.img.editOptions.doEdit) {
					this.sendNextChunk(false);
				} else {
					if (this.view.elems.fileSelect && this.view.elems.fileSelect.value) {
						this.view.elems.fileSelect.value = '';
					}

					if (uploader.EDIT_IMAGES_CLIENTSIDE && this.imageEdit.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1 && cur.dataArray && cur.dataArray.length){
						// we have an edited image width dataArray already prepared
						this.sendNextChunk(true);
					} else {
						// we have an image not to be edited or other filetype
						fr = new FileReader();
						fr.onload = function (e) {
							cur.dataArray = new Uint8Array(e.target.result);
							this.sendNextChunk(true);
						};
						fr.readAsArrayBuffer(cur.file);
					}
				}
			} else {
				this.processError({from: 'gui', msg: cur.error});
			}
		} else {
			//all uploads done
			this.currentFile = null;
			this.isUploading = false;
			this.postProcess();
		}
	};

	this.sendNextChunk = function (split) {
		var resp, oldPos, blob,
			cur = this.currentFile; // when using short syntax in line 1156 we must change some this to that = sender. FIXME: WHY?!

		if (this.isCancelled) {
			this.isCancelled = false;
			return;
		}

		if (split) {
			if (cur.partNum < cur.totalParts) {
				oldPos = cur.currentPos;
				cur.currentPos = oldPos + this.chunkSize;
				cur.partNum++;
				blob = new Blob([cur.dataArray.subarray(oldPos, cur.currentPos)]);

				this.sendChunk(
					blob,
					(cur.fileName ? cur.fileName : cur.file.name),
					(cur.mimePHP !== 'none' ? cur.mimePHP : cur.file.type),
					(cur.partNum === cur.totalParts ? cur.lastChunkSize : this.chunkSize),
					cur.partNum,
					cur.totalParts,
					cur.fileNameTemp,
					cur.size
				);
			}
		} else {
			this.sendChunk(cur.file, (cur.fileName ? cur.fileName : cur.file.name), cur.file.type, cur.size, 1, 1, '', cur.size);
		}
	};

	this.sendChunk = function (part, fileName, fileCt, partSize, partNum, totalParts, fileNameTemp, fileSize) {
		var xhr = new XMLHttpRequest();
		var fd = new FormData();
		var fsize = fileSize || 1;

		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4) {
				if (xhr.status === 200) {
					var resp = JSON.parse(xhr.responseText);
					resp = resp.DataArray && resp.DataArray.data ? resp.DataArray.data : resp;
					this.processResponse(resp, {partSize: partSize, partNum: partNum, totalParts: totalParts});
				} else {
					this.processError({type: 'request', msg: 'http request failed'});
				}
			}
		};

		fileCt = fileCt ? fileCt : 'text/plain';
		fd.append('fileinputName', uploader.fieldName);
		fd.append('doCommitFile', this.doCommitFile);
		fd.append('genericFilename', uploader.genericFilename);
		fd.append('weResponseClass', this.responseClass);
		fd.append('wePartNum', partNum);
		fd.append('wePartCount', totalParts);
		fd.append('weFileNameTemp', fileNameTemp);
		fd.append('weFileSize', fsize);
		fd.append('weFileName', fileName);
		fd.append('weFileCt', fileCt);
		fd.append(this.currentFile.field !== undefined ? this.currentFile.field : uploader.fieldName, part, fileName);//FIXME: take fieldname allways from cur!
		fd = this.appendMoreData(fd);
		xhr.open('POST', this.form.action, true);
		xhr.send(fd);
	};

	this.appendMoreData = function (fd) {
		var doc = this.uploader.doc;

		for (var i = 0; i < this.moreFieldsToAppend.length; i++) {
			if (doc.we_form.elements[this.moreFieldsToAppend[i][0]]) {
				switch (this.moreFieldsToAppend[i][1]) {
					case 'check':
						fd.append(this.moreFieldsToAppend[i][0], ((doc.we_form.elements[this.moreFieldsToAppend[i][0]].checked) ? 1 : 0));
						break;
					case 'multi_select':
						var sel = doc.we_form.elements[this.moreFieldsToAppend[i][0]],
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
						fd.append(this.moreFieldsToAppend[i][0], doc.we_form.elements[this.moreFieldsToAppend[i][0]].value);
				}
			}
		}

		return fd;
	};

	this.processResponse = function (resp, args) {
		if (!this.isCancelled) {
			var cur = this.currentFile;

			cur.fileNameTemp = resp.fileNameTemp;
			cur.mimePHP = resp.mimePhp;
			cur.currentWeightFile += args.partSize;
			this.currentWeight += args.partSize;

			switch (resp.status) {
				case 'continue':
					this.view.repaintGUI({what: 'chunkOK'});
					this.sendNextChunk(true);
					return;
				case 'success':
					this.currentWeightTag = this.currentWeight;
					cur.dataArray = null;
					cur.dataUrl = null;
					if(this.imageEdit.IS_MEMORY_MANAGMENT){
						this.imageEdit.memorymanagerUnregister(cur);
					}
					this.view.repaintGUI({what: 'chunkOK'});
					this.view.repaintGUI({what: 'fileOK'});
					this.doOnFileFinished(resp);//FIXME: make this part of postProcess(resp, fileonly=true)
					if (this.uploadFiles.length !== 0) {
						this.sendNextFile();
					} else {
						this.postProcess(resp);
					}
					return;
				case 'failure':
					this.currentWeight = this.currentWeightTag + cur.size;
					this.currentWeightTag = this.currentWeight;
					cur.dataArray = null;
					cur.dataUrl = null;
					if(this.imageEdit.IS_MEMORY_MANAGMENT){
						this.imageEdit.memorymanagerUnregister(cur);
					}
					this.view.repaintGUI({what: 'chunkNOK', message: resp.message});
					if (this.uploadFiles.length !== 0) {
						this.sendNextFile();
					} else {
						this.postProcess(resp);
					}
					return;
				default :
					return;
			}
		}
	};

	this.doOnFileFinished = function () {
		//to be overridden
	};

	this.postProcess = function (resp) {
		//to be overriden
	};

	this.cancel = function () {
		//to be overridden
	};
}

function Fileupload_sender_base(uploader) {
	Fileupload_sender_abstract.call(this, uploader);

	this.uploader = uploader;

	this.totalWeight = 0;

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

	this.postProcess = function (resp) {
		var cur = this.currentFile;
		var win = this.uploader.win;

		this.form.form.elements.weFileNameTemp.value = cur.fileNameTemp;
		this.form.form.elements.weFileCt.value = cur.mimePHP;
		this.form.form.elements.weFileName.value = cur.file.name;
		//this.form.form.elements.weIsUploadComplete.value = 1;

		if(this.nextCmd){
			window.setTimeout(function () {
				var tmp = this.nextCmd.split(',');
				tmp.splice(1, 0, this.resp);
				if(win.we_cmd){
					win.we_cmd.apply(win, tmp);
				} else { // FIXME: make sure we have a function we_cmd on every opener!
					win.top.we_cmd.apply(win, tmp);
				}
				win.close();
			}, 100);
		}
	};

	this.processError = function (arg) {
		switch (arg.from) {
			case 'gui' :
				WE().util.showMessage(arg.msg, 4, this.uploader.win);
				return;
			case 'request' :
				this.view.repaintGUI({what: 'fileNOK'});
				this.view.repaintGUI({what: 'resetGui'});
				return;
			default :
				return;
		}
	};

	this.resetParams = function () {
		this.preparedFiles = [];
		this.totalWeight = 0;
		this.isCancelled = false;
		this.view.repaintGUI({what: 'resetGui'});
	};

	this.prepareUpload = function () {
		if (this.preparedFiles.length < 1) {
			return false;
		}
		this.preparedFiles[0].preparedFilesIndex = 0;
		this.uploadFiles = [this.preparedFiles[0]];
		this.totalFiles = 1;
		this.totalWeight = this.preparedFiles[0].size;//size?
		this.currentWeight = 0;
		return true;
	};

	this.cancel = function () {
		if (!this.isUploading) {
			top.close();
		}
		this.isCancelled = true;
		this.isUploading = false;
		this.view.repaintGUI({what: 'cancelUpload'});
	};
}
Fileupload_sender_base.prototype = Object.create(Fileupload_sender_abstract.prototype);
Fileupload_sender_base.prototype.constructor = Fileupload_sender_base;

function Fileupload_sender_bindoc(uploader) {
	Fileupload_sender_abstract.call(this, uploader);

	this.uploader = uploader;
	this.totalWeight = 0;

	this.doOnFileFinished = function (resp) {};

	this.postProcess = function (resp) {
		var win = this.uploader.win;
		this.preparedFiles = [];
		if (uploader.uiType !== 'wedoc') {
			var cur = this.currentFile;

			if (resp.status === 'failure') {
				this.resetParams();
			} else {
				this.form.form.elements.weFileNameTemp.value = cur.fileNameTemp;
				this.form.form.elements.weFileCt.value = cur.mimePHP;
				this.form.form.elements.weFileName.value = cur.file.name;
				this.currentFile = null;

				for(var k in resp.weDoc){
					resp[k]=resp.weDoc[k];
				}

				window.setTimeout(function () {
					var tmp = this.nextCmd.split(',');
					tmp.splice(1, 0, resp);
					win.opener.we_cmd.apply(win.opener, tmp);
					win.close();
				}, 100);

				//reload main tree and close!
				//or weFileUpload_instance.reset(); and let nextCmd close uploader!
			}
		} else if (resp.status === 'success') {
			this.currentFile = null;
			if (WE(true)) {
				win.we_cmd('update_file');
				WE().layout.we_setPath(null, resp.weDoc.path, resp.weDoc.text, 0, "published");
			}
		}
	};

	this.processError = function (arg) {
		switch (arg.from) {
			case 'gui' :
				WE().util.showMessage(arg.msg, 4, this.uploader.win);
				return;
			case 'request' :
				this.view.repaintGUI({what: 'fileNOK'});
				this.view.repaintGUI({what: 'resetGui'});
				return;
			default :
				return;
		}
	};

	this.resetParams = function () {
		this.preparedFiles = [];
		this.totalWeight = 0;
		this.isCancelled = false;
		this.view.repaintGUI({what: 'resetGui'});
	};

	this.prepareUpload = function () {
		if (this.preparedFiles.length < 1) {
			return false;
		}

		if (typeof this.preparedFiles[0] === 'object' && this.preparedFiles[0] !== null && this.preparedFiles[0].isUploadable) {
			this.preparedFiles[0].fileNum = 0;
			this.preparedFiles[0].preparedFilesIndex = 0;
			this.uploadFiles = [this.preparedFiles[0]];
			this.totalWeight = this.preparedFiles[0].size;
		}

		this.totalFiles = this.uploadFiles.length;

		if (this.totalFiles > 0) {
			this.currentWeight = 0;
			this.totalChunks = this.totalWeight / this.chunkSize;
			this.currentWeight = 0;
			this.currentWeightTag = 0;

			return true;
		}
		return false;
	};

	this.cancel = function () {
		this.currentFile = -1;
		this.isCancelled = true;
		this.isUploading = false;
		this.view.repaintGUI({what: 'resetGui'});
	};

}
Fileupload_sender_bindoc.prototype = Object.create(Fileupload_sender_abstract.prototype);
Fileupload_sender_bindoc.prototype.constructor = Fileupload_sender_bindoc;

function Fileupload_sender_import(uploader) {
	Fileupload_sender_abstract.call(this, uploader);

	this.isGdOk = false;
	this.isCancelled = false;
	this.totalChunks = 0; //FIXME: apply consistent terminology to differ between currentfile and all files: and make it part of abstract
	this.mapFiles = [];

	this.prepareUpload = function (rePrepare) {
		if(rePrepare){ // first file has been reedited: we must recalculat totalWeight and set external progress
			this.totalWeight = this.totalWeight - this.uploadFiles[0].tmpSize + this.uploadFiles[0].size;
			this.totalChunks = this.totalWeight / this.chunkSize;
			this.view.repaintGUI({what: 'chunkOK'});
			return true;
		}

		if (this.currentFile === -1) {
			this.uploadFiles = [];
			this.mapFiles = [];
			for (var i = 0, c = 0; i < this.preparedFiles.length; i++) {
				if (typeof this.preparedFiles[i] === 'object' && this.preparedFiles[i] !== null && this.preparedFiles[i].isUploadable) {
					this.preparedFiles[i].fileNum = c++;
					this.preparedFiles[i].preparedFilesIndex = i;
					this.uploadFiles.push(this.preparedFiles[i]);
					this.mapFiles.push(i);
					this.totalWeight += this.preparedFiles[i].size;
					this.uploader.doc.getElementById('div_rowButtons_' + i).style.display = 'none';
					this.uploader.doc.getElementById('div_rowProgress_' + i).style.display = 'block';
				}
			}
			this.totalFiles = this.uploadFiles.length;

			if (this.totalFiles > 0) {
				this.currentWeight = 0;
				this.totalChunks = this.totalWeight / this.chunkSize;
				this.currentWeight = 0;
				this.currentWeightTag = 0;
				this.view.repaintGUI({what: 'startUpload'});

				return true;
			}
		}
		return false;
	};

	this.cancel = function () {
		this.isCancelled = true;
		this.isUploading = false;
		this.view.repaintGUI({what: 'cancelUpload'});
		this.postProcess('', true);
		//WE().util.showMessage(WE().consts.g_l.fileupload.uploadCancelled, 1, win);
	};

	this.appendMoreData = function (fd) {
		var sf = this.uploader.doc.we_form,
			cur = this.currentFile;

		fd.append('weFormNum', cur.fileNum + 1);
		fd.append('weFormCount', this.totalFiles);
		fd.append('we_cmd[0]', 'import_files');
		fd.append('step', 1);

		//if(!uploader.EDIT_IMAGES_CLIENTSIDE){
			fd.append('fu_file_sameName', sf.fu_file_sameName.value);
			fd.append('fu_file_parentID', sf.fu_file_parentID.value);
			fd.append('fu_doc_categories', sf.fu_doc_categories.value);
			fd.append('fu_doc_importMetadata', sf.fu_doc_importMetadata.value);
			fd.append('fu_doc_isSearchable', sf.fu_doc_isSearchable.value);
		//}

		if (this.imageEdit.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1) {
			fd.append('fu_doc_focusX', cur.img.focusX);
			fd.append('fu_doc_focusX', cur.img.focusY);
			fd.append('fu_doc_thumbs', sf.fu_doc_thumbs.value);
		}

		return fd;
	};

	this.postProcess = function (resp) {
		this.resp = resp;

		if (!this.isCancelled) {
			this.view.elems.footer.setProgress('', 100);
			this.view.elems.footer.setProgressText('progress_title', '');
			WE().util.showMessage(resp.completed.message, WE().consts.message.WE_MESSAGE_INFO, this.uploader.win);

			if(this.nextCmd){
				window.setTimeout(function () {
					var tmp = this.nextCmd.split(',');
					tmp.splice(1, 0, this.resp);
					top.we_cmd.apply(top, tmp);
				}, 100);
				this.uploader.reset();
				window.setTimeout(function(){this.uploader.win.top.close();}, 1000);
			}
			window.setTimeout(this.uploader.reset, 1000);
		}
		this.view.reloadOpener();

		//reinitialize some vars to add and upload more files
		this.isUploading = false;
		this.resetSender();

		WE().layout.button.display(this.view.elems.footer.document, 'cancel', false);
		WE().layout.button.display(this.view.elems.footer.document, 'close', true);
	};

	this.processError = function (arg) {
		switch (arg.from) {
			case 'gui' :
				WE().util.showMessage(arg.msg, top.WE().consts.message.WE_MESSAGE_ERROR, this.uploader.win);
				return;
			case 'request' :
				//view.repaintGUI({what : 'fileNOK'});
				//this.resetSender();
				return;
			default :
				return;
		}
	};

	this.resetSender = function () {
		for (var i = 0; i < this.preparedFiles.length; i++) {
			if (!this.isCancelled && this.preparedFiles[i]) {
				this.preparedFiles[i].isUploadable = false;
			} else {
				this.preparedFiles[i] = null;
			}
		}
		this.imageEdit.memorymanagerReregisterAll();
		this.uploadFiles = [];
		this.currentFile = -1;
		this.mapFiles = [];
		this.totalFiles = this.totalWeight = this.currentWeight = this.currentWeightTag = 0;
		this.view.elems.footer.setProgress("", 0);
		this.view.elems.extProgressDiv.style.display = 'none';
		WE().layout.button.disable(this.uploader.doc, 'reset_btn', false);
		WE().layout.button.disable(this.uploader.doc, 'browse_harddisk_btn', false);
	};

}
Fileupload_sender_import.prototype = Object.create(Fileupload_sender_abstract.prototype);
Fileupload_sender_import.prototype.constructor = Fileupload_sender_import;
