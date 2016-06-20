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

(function (win) {
	win.we_FileUpload_addListeners = false;
	win.addEventListener('load', function () {
		win.we_FileUpload_addListeners = true;
	}, false);

	/*
	 if (win.console) {
	 console = {
	 log: function () {
	 }
	 };
	 }
	 */
})(window);

var weFileUpload = (function () {
	var _ = {};

	function Fabric(type) {
		if (_.self) {
			//singleton: but we do not return the object, when constructor is called more than once!
			return false;
		}
		switch (type) {
			case 'base' :
				weFileUpload_base.prototype = new weFileUpload_abstract();
				weFileUpload_base.prototype.constructor = weFileUpload_base;
				return new weFileUpload_base();
			case 'importer' :
				weFileUpload_importer.prototype = new weFileUpload_abstract();
				weFileUpload_importer.prototype.constructor = weFileUpload_importer;
				return new weFileUpload_importer();
			case 'preview' :
			case 'wedoc' :
			case 'editor' :
				weFileUpload_binDoc.prototype = new weFileUpload_abstract();
				weFileUpload_binDoc.prototype.constructor = weFileUpload_binDoc;
				return new weFileUpload_binDoc();
		}
	}

	function weFileUpload_abstract() {
		//declare "protected" members: they are accessible from weFileUpload_include/importer too!
		_.fieldName = '';
		_.genericFilename = '';
		_.fileuploadType = 'abstract';
		_.uiType = 'base';

		_.debug = true;

		_.EDIT_IMAGES_CLIENTSIDE = true;
		_.picaOptions = {
			quality: 3, // [0,3]
			unsharpAmount: 0, // [0, 200]
			unsharpRadius: 0.5, // [0.5, 2]
			unsharpThreshold: 0, // [0, 255]
			alpha: false
		};

		_.init_abstract = function (conf) {
			var that = _.self, c = _.controller, s = _.sender, v = _.view, u = _.utils;

			//if site loaded allready we must call onload() manually
			if (we_FileUpload_addListeners) {
				_.onload(that);
			} else {
				//if not, we add listener now
				window.addEventListener('load', function (e) {
					_.onload(that);
				}, true);
			}

			//initialize private var outer to be used inside modules instead of unaccessible this
			c.outer = s.outer = v.outer = u.outer = that;

			//initialize properties only when conf is defined: dispatch them to modules
			if (typeof conf !== 'undefined') {
				s.typeCondition = conf.typeCondition || s.typeCondition;
				_.fieldName = conf.fieldName || _.fieldName;
				_.uiType = conf.uiType || _.uiType;
				_.genericFilename = conf.genericFilename || _.genericFilename;
				c.fileselectOnclick = conf.fileselectOnclick || _.controller.fileselectOnclick;
				c.isPreset = conf.isPreset || c.isPreset;
				s.doCommitFile = conf.doCommitFile !== undefined ? conf.doCommitFile : s.doCommitFile;
				s.chunkSize = typeof conf.chunkSize !== 'undefined' ? (conf.chunkSize * 1024) : s.chunkSize;
				s.callback = conf.callback || s.callback;
				s.responseClass = conf.responseClass || s.responseClass;
				s.dialogCallback = conf.callback || s.dialogCallback;
				s.maxUploadSize = typeof conf.maxUploadSize !== 'undefined' ? conf.maxUploadSize : s.maxUploadSize;
				if (typeof conf.form !== 'undefined') {
					s.form.name = conf.form.name || s.form.name;
					s.form.action = conf.form.action || s.form.action;
				}
				s.moreFieldsToAppend = conf.moreFieldsToAppend || [];
				u.gl = conf.gl || u.gl;
				v.isDragAndDrop = typeof conf.isDragAndDrop !== 'undefined' ? conf.isDragAndDrop : v.isDragAndDrop;
				v.footerName = conf.footerName || v.footerName;
				if (typeof conf.intProgress === 'object') {
					v.intProgress.isIntProgress = conf.intProgress.isInternalProgress || v.intProgress.isIntProgress;
					v.intProgress.width = conf.intProgress.width || v.intProgress.width;
				}
				if (typeof conf.extProgress === 'object') {
					v.extProgress.isExtProgress = conf.extProgress.isExternalProgress || v.extProgress.isExtProgress;
					v.extProgress.width = conf.extProgress.width || v.extProgress.width;
					v.extProgress.parentElemId = conf.extProgress.parentElemId || v.extProgress.parentElemId;
					v.extProgress.create = typeof conf.extProgress.create !== 'undefined' ? conf.extProgress.create : v.extProgress.create;
					v.extProgress.html = conf.extProgress.html || v.extProgress.html;
				}
			}
		};

		_.onload_abstract = function (scope) {
			var that = scope, s = _.sender, v = _.view;

			s.form.form = s.form.name ? document.forms[s.form.name] : document.forms[0];
			s.form.action = s.form.action ? s.form.action : (s.form.form.action ? s.form.form.action : window.location.href);

			//set references to some elements
			v.elems.fileSelect = document.getElementById(_.fieldName);
			v.elems.fileDrag = document.getElementById('div_' + _.fieldName + '_fileDrag');//FIXME: change to div_fileDrag
			v.elems.fileInputWrapper = document.getElementById('div_' + _.fieldName + '_fileInputWrapper');//FIXME: change to div_fileInputWrapper
			v.elems.footer = v.footerName ? top.frames[v.footerName] : window;
			v.elems.extProgressDiv = v.elems.footer.document.getElementById(v.extProgress.parentElemId);

			if (v.extProgress.isExtProgress && v.elems.extProgressDiv) {
				if (v.extProgress.create) {
					v.extProgress.isExtProgress = v.extProgress.html ? true : false;
					v.elems.extProgressDiv.innerHTML = v.extProgress.html;
				}
			} else {
				v.extProgress.isExtProgress = false;
			}

			//add eventhandlers for some html elements
			if (v.elems.fileSelect) {
				v.elems.fileSelect.addEventListener('change', _.controller.fileSelectHandler, false);
				v.elems.fileInputWrapper.addEventListener('click', _.controller.fileselectOnclick, false);
			}
			if (v.elems.fileDrag && _.view.isDragAndDrop) {
				v.elems.fileDrag.addEventListener('dragover', _.controller.fileDragHover, false);
				v.elems.fileDrag.addEventListener('dragleave', _.controller.fileDragHover, false);
				v.elems.fileDrag.addEventListener('drop', _.controller.fileSelectHandler, false);
				v.elems.fileDrag.style.display = 'block';
			} else {
				v.isDragAndDrop = false;
				if (v.elems.fileDrag) {
					v.elems.fileDrag.style.display = 'none';
				}
			}
		};

		function AbstractController() {
			this.elemFileDragClasses = 'we_file_drag';
			this.outer = null;
			this.isPreset = false;

			this.DELETE_IMG_AFTER_READING = false;
			this.IMG_NEXT = 0;
			this.IMG_LOAD_CANVAS = 1;
			this.IMG_EXTRACT_METADATA = 2;
			this.IMG_SCALE = 3;
			this.IMG_ROTATE = 4;
			this.IMG_APPLY_FILTERS = 5;
			this.IMG_WRITE_IMAGE = 6;
			this.IMG_INSERT_METADATA = 7;
			this.IMG_MAKE_PREVIEW = 8;
			this.IMG_POSTPROCESS = 9;

			this.fileselectOnclick = function () {
			};

			this.checkIsPresetFiles = function () {
				if (_.controller.isPreset && WE().layout.weEditorFrameController.getVisibleEditorFrame().document.presetFileupload) {
					_.controller.fileSelectHandler(null, true, WE().layout.weEditorFrameController.getVisibleEditorFrame().document.presetFileupload);
				} else if (_.controller.isPreset && top.opener.document.presetFileupload) {
					_.controller.fileSelectHandler(null, true, top.opener.document.presetFileupload);
				}
			};

			this.fileSelectHandler = function (e, isPreset, presetFileupload) {
				var files = [];

				files = _.controller.selectedFiles = isPreset ? (presetFileupload.length ? presetFileupload : files) : (e.target.files || e.dataTransfer.files);

				if (files.length) {
					if (!isPreset) {
						_.sender.resetParams();
						if (e.type === 'drop') {
							e.stopPropagation();
							e.preventDefault();
							_.sender.resetParams();
							_.controller.fileDragHover(e);
						}
					}
					_.controller.fileselectOnclick();

					_.sender.imageFilesToProcess = [];
					for (var f, i = 0; i < files.length; i++) {
						if (!_.utils.contains(_.sender.preparedFiles, _.controller.selectedFiles[i])) {
							f = _.controller.prepareFile(_.controller.selectedFiles[i]);
							_.sender.preparedFiles.push(f);
							_.view.addFile(f, _.sender.preparedFiles.length);
							
						}
					}

					if (_.sender.imageFilesToProcess.length){
						_.controller.processImages();
					}
				}
			};

			this.prepareFile = function (f, isUploadable) {
				var fileObj = {
					file: f,
					fileNum: 0,
					dataArray: null,
					canvas: null, // maybe call it workingcanvas
					img: {
						workingCanvas: null, // maybe call it workingcanvas
						previewCanvas: null,
						previewImg: null, // created only for edited images: unedited images can be very large...
						editOptions: null,
						originalPreviewCanvas: null,
						originalSize: 0,
						jpgCustomSegments: null,
						pngTextChunks: null,
						focusX: 0,
						focusY: 0
					},
					size: 0,
					currentPos: 0,
					partNum: 0,
					currentWeightFile: 0,
					mimePHP: 'none',
					fileNameTemp: ''
				},
				transformables = ['image/jpeg', 'image/gif', 'image/png'], //TODO: add all transformable types and make "global" (_.editables)
								//TODO: make this OK-stuff more concise
				type = f.type ? f.type : 'text/plain',
				u = isUploadable || true,
				isTypeOk = _.utils.checkFileType(type, f.name),
				isSizeOk = (f.size <= _.sender.maxUploadSize || !_.sender.maxUploadSize) ? true : false,
				errorMsg = [
					_.utils.gl.errorNoFileSelected,
					_.utils.gl.errorFileSize,
					_.utils.gl.errorFileType,
					_.utils.gl.errorFileSizeType
				];

				fileObj.type = type;
				fileObj.isUploadable = isTypeOk && isSizeOk && u; //maybe replace uploadConditionOk by this
				fileObj.isTypeOk = isTypeOk;
				fileObj.isSizeOk = isSizeOk;
				fileObj.uploadConditionsOk = isTypeOk && isSizeOk;
				fileObj.error = errorMsg[isSizeOk && isTypeOk ? 0 : (!isSizeOk && isTypeOk ? 1 : (isSizeOk && !isTypeOk ? 2 : 3))];
				fileObj.size = f.size;
				fileObj.totalParts = Math.ceil(f.size / _.sender.chunkSize);
				fileObj.lastChunkSize = f.size % _.sender.chunkSize;

				if (transformables.indexOf(f.type) !== -1) {
					_.sender.imageFilesToProcess.push(fileObj);
				}

				return fileObj;
			};

			this.processImages = function() {
				if (_.sender.imageFilesToProcess && _.sender.imageFilesToProcess.length) {
					if(_.EDIT_IMAGES_CLIENTSIDE){
						_.utils.setImageEditOptionsGeneral();
					}
					_.view.setImageEditMessage();
					_.controller.processNextImage();
				} else {
					_.view.unsetImageEditMessage();
				}
			};

			this.processNextImage = function() {
				if (_.sender.imageFilesToProcess.length) {
					var fileobj = _.sender.imageFilesToProcess.shift();
					_.utils.logTimeFromStart('start edit image', true);

					_.utils.setImageEditOptionsFile(fileobj);

					_.controller.processImage(fileobj, this.IMG_LOAD_CANVAS);
				} else {
					_.controller.processImages();
				}
			};

			this.processImage = function(fileobj, task) {
				if(!fileobj){
					_.controller.processNextImage();
					return;
				}

				switch(task) {
					case _.controller.IMG_LOAD_CANVAS: // TODO: make IMG_START
						if(!fileobj.img.editOptions.doEdit && fileobj.dataArray && fileobj.dataArray.length){ // we reset an image edited before!
							_.utils.processimageReset(fileobj, _.controller.IMG_POSTPROCESS);
							return;
						}
						if(!fileobj.img.editOptions.doEdit && fileobj.size > 10485760){ // nothing to edit and image is too big for preview (>10MB)
							_.controller.processImage(fileobj, _.controller.IMG_NEXT);
							return;
						}
						_.utils.processimageLoadCanvas(fileobj, fileobj.img.editOptions.doEdit ? _.controller.IMG_EXTRACT_METADATA : _.controller.IMG_MAKE_PREVIEW);
						break;
					case _.controller.IMG_EXTRACT_METADATA:
						_.utils.processimageExtractMetadata(fileobj, _.controller.IMG_SCALE);
						break;
					case _.controller.IMG_SCALE:
						_.utils.processimageScale(fileobj, _.controller.IMG_ROTATE);
						break;
					case _.controller.IMG_ROTATE:
						_.utils.processimageRotate(fileobj, _.controller.IMG_APPLY_FILTERS);
						break;							
					case _.controller.IMG_APPLY_FILTERS:
						_.utils.processimageApplyFilters(fileobj, _.controller.IMG_WRITE_IMAGE);
						break;
					case _.controller.IMG_WRITE_IMAGE:
						_.utils.processimageWriteImage(fileobj, _.controller.IMG_INSERT_METADATA);
						break;
					case _.controller.IMG_INSERT_METADATA:
						_.utils.processimagInsertMetadata(fileobj, _.controller.IMG_MAKE_PREVIEW);
						break;
					case _.controller.IMG_MAKE_PREVIEW:
						_.utils.processimageMakePreview(fileobj, _.controller.IMG_POSTPROCESS);
						break;
					case _.controller.IMG_POSTPROCESS:
						_.utils.processimagePostProcess(fileobj, _.controller.IMG_NEXT);
						break;
					case _.controller.IMG_NEXT:
					default:
						_.controller.processNextImage();
				}
			};

			this.fileDragHover = function (e) {
				e.preventDefault();
				e.target.className = (e.type === 'dragover' ? _.controller.elemFileDragClasses + ' we_file_drag_hover' : _.controller.elemFileDragClasses);
			};

			this.setWeButtonState = function (btn, enable, isFooter) {
				isFooter = isFooter || false;

				if (isFooter) {
					top.WE().layout.button[enable ? 'enable' : 'disable'](_.view.elems.footer.document, btn);
				} else {
					top.WE().layout.button[enable ? 'enable' : 'disable'](document, btn);
					if (btn === 'browse_harddisk_btn') {
						top.WE().layout.button[enable ? 'enable' : 'disable'](document, 'browse_btn');
					}
				}
			};

			this.reeditImage = function (fileobj, pos, all) {
				var files = all ? _.sender.preparedFiles : (fileobj ? [fileobj] : (pos === undefined ? [] : [_.sender.preparedFiles[pos]]));

				var transformables = ['image/jpeg', 'image/gif', 'image/png'];
				for(var i = 0; i < files.length; i++){
					if(files[i] && transformables.indexOf(files[i].type) !== -1){
						_.sender.imageFilesToProcess.push(files[i]);
					}
				}

				_.controller.processImages();
			};

			this.openImageEditor = function(pos){
				// to be overridden
			};

			this.editOptionsOnChange = function(target){
				var resizeName = 'fu_doc_resizeValue',
					rotateName = 'fu_doc_rotate',
					qualityName = 'fu_doc_quality';

				var altNames = ['resizeValue', 'rotateSelect', 'quality']; // TODO: unifiy names or classes
				if(altNames.indexOf(target.name) !== -1){
					resizeName = 'resizeValue';
					rotateName = 'rotateSelect';
					qualityName = 'quality';
				}
				
				var resizeValue = target.form.elements[resizeName].value;
				var rotateValue = target.form.elements[rotateName].value;
				var qualityValue = target.form.elements[qualityName].value;
				var btnRefresh = target.form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];

				if(target.name == qualityName){
					btnRefresh.disabled = !parseInt(qualityValue);
					return;
				}

				if(resizeValue === '' && !parseInt(rotateValue)){
					target.form.elements[qualityName].value = 0;
					target.form.qualityOutput.value = 0;
					btnRefresh.disabled = true;
				} else if(!parseInt(qualityValue)) {
					target.form.elements[qualityName].value = 90;
					target.form.qualityOutput.value = 90;
					btnRefresh.disabled = false;
				}
			};

		}

		function AbstractSender() {
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
				document.forms[0].submit();
			};
			this.dialogCallback = null;
			this.isUploading = false;
			this.isCancelled = false;
			this.preparedFiles = [];
			this.imageFilesToProcess = [];
			this.uploadFiles = [];
			this.currentFile = -1;
			this.totalFiles = 0;
			this.totalWeight = 0;
			this.currentWeight = 0;
			this.currentWeightTag = 0;//FIXME: find better name
			this.isAutostartPermitted = false;
			this.imageEditOptions = {
				doEdit: false,
				from: 'general',
				scaleUnit: 'pixel_l', // percent|pixel_w|pixel_h
				scaleValue: 0,
				rotateValue: 0,
				quality: 90
			};
			this.moreFieldsToAppend = [];

			this.resetParams = function () {
			};

			this.prepareUpload = function () {
				return true;
			};

			this.sendNextFile = function () {
				var cur, fr = null, cnt,
					that = _.sender;//IMPORTANT: if we use that = this, then that is of type AbstractSender not knowing members of Sender!

				/* when using short syntax in line 1156 we must change some this to that = _.sender. FIXME: WHY?!
				 if (that.uploadFiles.length > 0) {
				 that.currentFile = cur = that.uploadFiles.shift();
				 */
				if (this.uploadFiles.length > 0) {
					this.currentFile = cur = this.uploadFiles.shift();
					if (cur.uploadConditionsOk) {
						this.isUploading = true;
						_.view.repaintGUI({what: 'startSendFile'});

						if (cur.size <= this.chunkSize && !this.imageEditOptions.doEdit) {// IMPORTANT !cur.doEdit!
							this.sendNextChunk(false);
						} else {
							if (_.view.elems.fileSelect && _.view.elems.fileSelect.value) {
								_.view.elems.fileSelect.value = '';
							}
							var transformables = ['image/jpeg', 'image/gif', 'image/png'];//TODO: add all transformable types
							if (_.EDIT_IMAGES_CLIENTSIDE && transformables.indexOf(cur.type) !== -1 && cur.dataArray && cur.dataArray.length) {
								that.sendNextChunk(true);
							} else {
								fr = new FileReader();
								fr.onload = function (e) {
									cur.dataArray = new Uint8Array(e.target.result);
									that.sendNextChunk(true);
								};
								fr.readAsArrayBuffer(cur.file);
							}
						}
					} else {
						this.processError({'from': 'gui', 'msg': cur.error});
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
					cur = this.currentFile; // when using short syntax in line 1156 we must change some this to that = _.sender. FIXME: WHY?!

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
							cur.file.name,
							(cur.mimePHP !== 'none' ? cur.mimePHP : cur.file.type),
							(cur.partNum === cur.totalParts ? cur.lastChunkSize : this.chunkSize),
							cur.partNum,
							cur.totalParts,
							cur.fileNameTemp,
							cur.size
						);
					}
				} else {
					this.sendChunk(cur.file, cur.file.name, cur.file.type, cur.size, 1, 1, '', cur.size);
				}
			};

			this.sendChunk = function (part, fileName, fileCt, partSize, partNum, totalParts, fileNameTemp, fileSize) {
				var xhr = new XMLHttpRequest(),
					fd = new FormData(),
					fsize = fileSize || 1,
					that = this;

				xhr.onreadystatechange = function () {
					if (xhr.readyState === 4) {
						if (xhr.status === 200) {
							var resp = JSON.parse(xhr.responseText);
							resp = resp.DataArray && resp.DataArray.data ? resp.DataArray.data : resp;
							that.processResponse(resp, {partSize: partSize, partNum: partNum, totalParts: totalParts});
						} else {
							that.processError({type: 'request', msg: 'http request failed'});
						}
					}
				};

				fileCt = fileCt ? fileCt : 'text/plain';
				fd.append('fileinputName', _.fieldName);
				fd.append('doCommitFile', this.doCommitFile);
				fd.append('genericFilename', _.genericFilename);
				fd.append('weResponseClass', this.responseClass);
				fd.append('wePartNum', partNum);
				fd.append('wePartCount', totalParts);
				fd.append('weFileNameTemp', fileNameTemp);
				fd.append('weFileSize', fsize);
				fd.append('weFileName', fileName);
				fd.append('weFileCt', fileCt);
				fd.append(this.currentFile.field !== undefined ? this.currentFile.field : _.fieldName, part, fileName);//FIXME: take fieldname allways from cur!
				fd = this.appendMoreData(fd);
				xhr.open('POST', this.form.action, true);
				xhr.send(fd);
			};

			this.appendMoreData = function (fd) {
				for (var i = 0; i < this.moreFieldsToAppend.length; i++) {
					if (document.we_form.elements[this.moreFieldsToAppend[i][0]]) {
						switch (this.moreFieldsToAppend[i][1]) {
							case 'check':
								fd.append(this.moreFieldsToAppend[i][0], ((document.we_form.elements[this.moreFieldsToAppend[i][0]].checked) ? 1 : 0));
								break;
							case 'multi_select':
								var sel = document.we_form.elements[this.moreFieldsToAppend[i][0]],
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
								fd.append(this.moreFieldsToAppend[i][0], document.we_form.elements[this.moreFieldsToAppend[i][0]].value);
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
							_.view.repaintGUI({what: 'chunkOK'});
							this.sendNextChunk(true);
							return;
						case 'success':
							this.currentWeightTag = this.currentWeight;
							_.view.repaintGUI({what: 'chunkOK'});
							_.view.repaintGUI({what: 'fileOK'});
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
							_.view.repaintGUI({what: 'chunkNOK', message: resp.message});
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

		function AbstractView() {
			this.isDragAndDrop = true;
			this.footerName = '';
			this.elems = {};
			this.intProgress = {
				isExtProgress: false,
				width: 0
			};
			this.extProgress = {
				isExtProgress: true,
				name: '',
				width: 0,
				parentElemId: 'progressbar',
				create: false,
				html: ''
			};
			this.previewSize = 116;
			this.useOriginalAsPreviewIfNotEdited = false;
			this.lastLoupIndex = -1;

			this.setImageEditMessage = function (){
			};

			this.unsetImageEditMessage = function (){
			};

			this.repaintImageEditMessage = function (){
			};

			this.repaintGUI = function (arg){
			};

			this.repaintEntry = function (obj){
			};

			this.setPreviewLoupe = function(fileobj, pt){
				pt = pt ? pt : 0;
				if(pt === 1){
					fileobj.loupInner.appendChild(fileobj.img.fullPrev);
					document.getElementById('we_fileUpload_spinner').style.display = 'none';
					document.getElementsByClassName('editorCrosshairH')[0].style.display = 'block';
					document.getElementsByClassName('editorCrosshairV')[0].style.display = 'block';
					fileobj.focusPoint = document.getElementById('we_fileUpload_focusPoint');
					fileobj.focusPointFixed = document.getElementById('editorFocuspointFixed');
					fileobj.focusPoint.style.display = 'block';
					return;
				}

				if(!_.view.useOriginalAsPreviewIfNotEdited && _.view.lastLoupIndex !== -1 && _.view.lastLoupIndex !== fileobj.index){
					// in importer we delete fullPreview when moving to an other file
					_.sender.preparedFiles[fileobj.index].img.fullPrev = null;
				}

				fileobj.loupInner = document.getElementById('we_fileUpload_loupeInner');
				fileobj.loupInner.style.display = 'block';
				document.getElementById('we_fileUpload_loupe').style.display = 'block';
				document.getElementById('we_fileUpload_spinner').style.display = 'block';

				var mask;
				if((mask = document.getElementById('we_fileUploadImporter_mask'))){
					mask.style.display = 'block';
				}

				if(fileobj.img.fullPrev){
					_.view.setPreviewLoupe(fileobj, 1);
				} else if(fileobj.dataUrl || (fileobj.img.previewImg && fileobj.img.previewImg.src)){
					fileobj.img.fullPrev = new Image();
					fileobj.img.fullPrev.onload = function(){
						_.view.setPreviewLoupe(fileobj, 1);
					};
					setTimeout(function () {
						fileobj.img.fullPrev.src = fileobj.dataUrl ? fileobj.dataUrl : fileobj.img.previewImg.src;
					}, 0);
				} else { // in importer we do actually not load images for preview when not edited!
					var reader = new FileReader();
					reader.onload = function() {
						fileobj.img.fullPrev = new Image();
						fileobj.img.fullPrev.onload = function(){
							_.view.lastLoupIndex = fileobj.index;
							_.view.setPreviewLoupe(fileobj, 1);
						};
						fileobj.img.fullPrev.src = reader.result;
					};
					reader.readAsDataURL(fileobj.file);
				}
			};

			this.movePreviewLoupe = function(e, fileobj){
				if(e.timeStamp - _.view.lastklick < 10){
					// in Chrome onclick fires mosemove too: this causes the nely set focuspoint to be slightly wrong...
					return;
				}
				
				if(fileobj.loupInner.firstChild){
					var offsetLeft = (-fileobj.loupInner.firstChild.width / fileobj.img.previewWidth * e.offsetX) + (fileobj.loupInner.parentNode.offsetWidth / 2);
					var offsetTop = (-fileobj.loupInner.firstChild.height / fileobj.img.previewHeight * e.offsetY) + (fileobj.loupInner.parentNode.offsetHeight / 2);
					
					_.view.offesetLeft = offsetLeft;
					_.view.offsetTop = offsetTop;

					fileobj.loupInner.style.left = Math.round(offsetLeft) + 'px';
					fileobj.loupInner.style.top = Math.round(offsetTop) + 'px';

					fileobj.focusPoint.style.left = Math.round(offsetLeft + ((parseFloat(fileobj.img.focusX) + 1) / 2) * fileobj.img.fullPrev.width) + 'px';
					fileobj.focusPoint.style.top = Math.round(offsetTop + ((parseFloat(fileobj.img.focusY) + 1) / 2) * fileobj.img.fullPrev.height) + 'px';
				}
			};

			this.unsetPreviewLoupe = function(fileobj){
				fileobj.loupInner.style.display = 'none';
				fileobj.loupInner.parentNode.style.display = 'none';
				document.getElementsByClassName('editorCrosshairH')[0].style.display = 'none';
				document.getElementsByClassName('editorCrosshairV')[0].style.display = 'none';
				fileobj.focusPoint.style.display = 'none';
				fileobj.focusPointFixed.style.display = 'none';
				fileobj.loupInner.innerHTML = null;
//				fileobj.loupInner = null;
				var mask;
				if((mask = document.getElementById('we_fileUploadImporter_mask'))){
					mask.style.display = 'none';
				}
			};

			this.grabFocusPoint = function(e, fileobj){
				_.view.lastklick = e.timeStamp;
				if(fileobj.img.previewWidth && fileobj.img.previewHeight){
					fileobj.focusPoint.style.display = 'none';
					fileobj.focusPointFixed.style.display = 'block';
					var focusX = ((e.offsetX / fileobj.img.previewWidth) * 2) - 1;
					var focusY = ((e.offsetY / fileobj.img.previewHeight) * 2) - 1;
					fileobj.img.focusX = focusX.toFixed(2);
					fileobj.img.focusY = focusY.toFixed(2);
					_.view.writeFocusToForm(fileobj);
					setTimeout(function () {
						fileobj.focusPoint.style.top = (fileobj.loupInner.parentNode.offsetHeight / 2) + 'px';
						fileobj.focusPoint.style.left = (fileobj.loupInner.parentNode.offsetWidth / 2) + 'px';
						fileobj.focusPoint.style.display = 'block';
						fileobj.focusPointFixed.style.display = 'none';
					}, 400);
				}
			};

			this.writeFocusToForm = function(filobj){
				// to be overridden
			};

			//TODO: adapt these progress fns to standard progressbars
			this.setInternalProgressText = function (name, text, index) {
				var p = typeof index === 'undefined' || index === false ? '' : '_' + index;
				document.getElementById('span_' + _.fieldName + '_' + name + p).innerHTML = text;
			};

			this.setInternalProgress = function (progress, index) {
				var coef = this.intProgress.width / 100,
					i = typeof index !== 'undefined' || index === false ? index : false,
					p = i === false ? '' : '_' + i;

				document.getElementById(_.fieldName + '_progress_image_bg' + p).style.width = ((coef * 100) - (coef * progress)) + "px";
				document.getElementById(_.fieldName + '_progress_image' + p).style.width = coef * progress + "px";

				this.setInternalProgressText('progress_text', progress + '%', index);
			};

			this.setInternalProgressCompleted = function (success, index, txt) {
				var s = success || false,
								i = index || false,
								p = !i ? '' : '_' + i;

				if (s) {
					this.setInternalProgress(100, i);
					document.getElementById(_.fieldName + '_progress_image').className = 'progress_finished';
				} else {
					document.getElementById(_.fieldName + '_progress_image' + p).className = 'progress_failed';
				}
			};

			this.setDoEditGlobal = function (fileobj, checkox) {
				// from inline
			};

			this.setUseGeneralOpts = function (fileobj, checkox) {
				// to be overridden
			};

			this.setCustomEditOpts = function (fileobj, radio) {
				// to be overridden
			};
		}

		function AbstractUtils() {
			this.gl = {
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

			this.logTimeFromStart = function(text, resetStart){
				if(_.debug){
					var date = new Date();

					this.start = resetStart ? date.getTime() : this.start;
					top.console.log((text ? text : ''), (date.getTime() - this.start)/1000);
				}
			};

			this.abstractSetImageEditOptionsGeneral = function (formname) {
				var form = document.forms[(formname ? formname : 'we_form')],
					resizeValue = form.fu_doc_resizeValue.value,
					deg = form.fu_doc_rotate.value,
					quality = form.fu_doc_quality;
					opts = _.sender.imageEditOptions;

				opts.doEdit = false;

				if(parseInt(form.fu_doc_doResize.value) === 1 && ((resizeValue == '' && deg == 0 && quality == 0) ? false : true)){
					opts.doEdit = true;
					opts.keepRatio = true;

					opts.scaleUnit = form.fu_doc_unitSelect.value;
					opts.scaleValue = resizeValue;
					opts.rotateValue = deg;
					opts.quality = form.fu_doc_quality.value;
				}
			};

			this.setImageEditOptionsGeneral = function () {
				_.utils.abstractSetImageEditOptionsGeneral();
			};

			this.setImageEditOptionsFile = function (fileobj) {
				var type = 'general';

				switch(type){
					case 'general':
						fileobj.img.editOptions = JSON.parse(JSON.stringify(_.sender.imageEditOptions));
						break;
					case 'expert':
						// not implemented yet
						break;
				}
				fileobj.img.editOptions.from = type;
			};

			this.processimageLoadCanvas = function(fileobj, nexttask) {
				if(!fileobj.isEdited){
					//top.console.log('loadcanvas: must load img');
					fileobj.img.image = null;
				}

				fileobj.img.workingCanvas = document.createElement('canvas');

				// we have loaded original image before and have it still stored in fileobj.img.image
				if(fileobj.img.image && fileobj.img.image.src){
					fileobj.img.workingCanvas.width = fileobj.img.image.width;
					fileobj.img.workingCanvas.height = fileobj.img.image.height;
					fileobj.img.workingCanvas.getContext("2d").drawImage(fileobj.img.image, 0, 0);

					_.utils.logTimeFromStart('canvas loaded');
					_.controller.processImage(fileobj, nexttask);
					return;
				}

				// we are here for the first time or have cleared earlier fileobj.img.image to free memory
				var reader = new FileReader();
				fileobj.img.image = new Image();
				reader.onload = function() {
					fileobj.img.image.onload = function() {
						fileobj.img.workingCanvas.width = fileobj.img.image.width;
						fileobj.img.workingCanvas.height = fileobj.img.image.height;
						fileobj.img.workingCanvas.getContext("2d").drawImage(fileobj.img.image, 0, 0);
						if(_.controller.DELETE_IMG_AFTER_READING){
							fileobj.img.image = null;
						}

						_.utils.processimageMakePreview(fileobj, nexttask, 'originalPreviewCanvas');
					};

					_.utils.logTimeFromStart('canvas loaded');
					_.view.repaintImageEditMessage(true);
					//fileobj.img.dataUrl = 
					fileobj.img.image.src = reader.result;
				};
				reader.readAsDataURL(fileobj.file);
			};

			this.processimageExtractMetadata = function(fileobj, nexttask) {
				switch(fileobj.type){
					case 'image/jpeg':
						_.utils.processimageExtractMetadataJPG(fileobj, nexttask);
						break;
					case 'image/png':
						_.utils.processimageExtractMetadataPNG(fileobj, nexttask);
						break;
					default:
						_.controller.processImage(fileobj, nexttask);
				}
			};

			this.processimageExtractMetadataJPG = function(fileobj, nexttask) {
					var reader = new FileReader();

					reader.onloadend = function () {
						fileobj.img.jpgCustomSegments = _.utils.jpgGetSegmentsIfExist(new Uint8Array(reader.result), [225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239], true);
						fileobj.img.exifParsed = _.utils.jpgGetExifParsed();

						_.utils.logTimeFromStart('meta jpg exttracted');
						_.view.repaintImageEditMessage(true);
						_.controller.processImage(fileobj, nexttask);
					};
					reader.readAsArrayBuffer(fileobj.file.slice(0, 128 * 1024));
			};

			this.processimageExtractMetadataPNG = function(fileobj, nexttask) {
				var reader = new FileReader();

				reader.onloadend = function () {
					fileobj.pngTextChunks = [];
					try{
						var chunks = extractChunks(new Uint8Array(reader.result));
						for(var i = 0; i < chunks.length; i++){
							if(chunks[i].name === 'iTXt' || chunks[i].name === 'tEXt' || chunks[i].name === 'zTXt'){
								fileobj.pngTextChunks.push(chunks[i]);
								/*
								var decodedChunk = chunks[i].name !== 'iTXt' ? decodeChunk(chunks[i]) :
										decodeURIComponent(escape(String.fromCharCode.apply(null, chunks[i].data)));
								*/
							}
						}
					} catch(e){
						top.console.log('extracting png metadata failed');
					}

					_.utils.logTimeFromStart('meta png exttracted');
					_.view.repaintImageEditMessage(true);
					_.controller.processImage(fileobj, nexttask);
				};
				reader.readAsArrayBuffer(fileobj.file);
			};

			this.processimageScale = function(fileobj, nexttask){
				if(!fileobj.img.editOptions.scaleValue){
					_.utils.logTimeFromStart('scaling skipped');
					_.controller.processImage(fileobj, nexttask);
					return; // IMPORTANT! 
				}

				var scaleUnit = fileobj.img.editOptions.scaleUnit !== 'pixel_l' ? fileobj.img.editOptions.scaleUnit : 
						(fileobj.img.workingCanvas.width >= fileobj.img.workingCanvas.height ? 'pixel_w' : 'pixel_h');
				var ratio = scaleUnit === 'pixel_w' ? fileobj.img.editOptions.scaleValue/fileobj.img.workingCanvas.width : 
							fileobj.img.editOptions.scaleValue/fileobj.img.workingCanvas.height;
				if(ratio >= 1){
					_.controller.processImage(nexttask); // we do not upscale!
					return; // IMPORTANT! 
				}

				var targetCanvas = document.createElement('canvas');
				targetCanvas.width = fileobj.img.workingCanvas.width * ratio;
				targetCanvas.height = fileobj.img.workingCanvas.height * ratio;

				window.pica.resizeCanvas(fileobj.img.workingCanvas, targetCanvas, _.picaOptions, function (err) {
					if(err){
						top.console.log('scaling image failed');
					} else {
						fileobj.img.workingCanvas = targetCanvas;
						targetCanvas = null;
					}

					_.utils.logTimeFromStart('scaling done');
					_.view.repaintImageEditMessage(true);
					_.controller.processImage(fileobj, nexttask);
				});
			};

			this.processimageRotate = function(fileobj, nexttask){
				var deg = fileobj.img.editOptions.rotateValue;

				// correct landscape using exif data
				if(fileobj.img.exifParsed && fileobj.img.exifParsed.Orientation && fileobj.img.exifParsed.Orientation.value !== 1){
					switch(fileobj.img.exifParsed.Orientation.value) {
						case 3:
							deg += 180;
							break;
						case 6:
							deg += 270;
							break;
						case 8:
							deg += 90;
							break;
					}
				}
				deg = parseInt(deg)%360;

				var cw = fileobj.img.workingCanvas.width, ch = fileobj.img.workingCanvas.height, cx = 0, cy = 0;
				switch(deg) {
					case 90 :
						cw = fileobj.img.workingCanvas.height;
						ch = fileobj.img.workingCanvas.width;
						cy = -fileobj.img.workingCanvas.height;
						break;
					case 180 :
						cx = -fileobj.img.workingCanvas.width;
						cy = -fileobj.img.workingCanvas.height;
						break;
					case 270 :
					case -90 :
						cw = fileobj.img.workingCanvas.height;
						ch = fileobj.img.workingCanvas.width;
						cx = -fileobj.img.workingCanvas.width;
						break;
					default:
						_.utils.logTimeFromStart('rotation skipped');
						_.controller.processImage(fileobj, nexttask);
						return;
				};

				var targetCanvas = document.createElement('canvas'),
					ctxTargetCanvas = targetCanvas.getContext("2d");
					targetCanvas.width = cw;
					targetCanvas.height = ch;

				ctxTargetCanvas.rotate(deg * Math.PI / 180);
				ctxTargetCanvas.drawImage(fileobj.img.workingCanvas, cx, cy);

				fileobj.img.workingCanvas = targetCanvas;
				targetCanvas = null;

				_.utils.logTimeFromStart('rotation done');
				_.view.repaintImageEditMessage(true);
				_.controller.processImage(fileobj, nexttask);
			};

			this.processimageApplyFilters = function(fileobj, nexttask){
				_.controller.processImage(fileobj, nexttask);
			};

			this.processimageWriteImage_2 = function(fileobj, nexttask){
				fileobj.img.workingCanvas.toBlob(function (blob) { // DO WE NEED toBlob TO GET UINT8ARRAY?
																	// THIS FN CAUASES PROBLEMS WITH PNG!
					var reader = new FileReader();
					reader.onload = function() {
						fileobj.dataArray = new Uint8Array(reader.result);
						_.utils.logTimeFromStart('image written');
						_.controller.processImage(fileobj, nexttask);
					};
					reader.readAsArrayBuffer(blob);
				}, fileobj.type, (fileobj.img.editOptions.quality/100));
			};

			this.processimageWriteImage = function(fileobj, nexttask){
				if(fileobj.type !== 'image/png'){
					_.utils.processimageWriteImage_2(fileobj, nexttask);
				}
				fileobj.dataUrl = fileobj.img.workingCanvas.toDataURL(fileobj.type, (fileobj.img.editOptions.quality/100));
				fileobj.dataArray = _.utils.dataURLToUInt8Array(fileobj.dataUrl);
				_.utils.logTimeFromStart('image written fn 2');
				_.controller.processImage(fileobj, nexttask);
			};

			this.processimagInsertMetadata = function(fileobj, nexttask) {
				switch(fileobj.type){
					case 'image/jpeg':
						_.utils.processimagInsertMetadataJPG(fileobj, nexttask);
						break;
					case 'image/png':
						_.utils.processimagInsertMetadataPNG(fileobj, nexttask);
						break;
					default:
						_.utils.logTimeFromStart('no metadata to reinsert');
						_.controller.processImage(fileobj, nexttask);
				}
			};

			this.processimagInsertMetadataJPG = function(fileobj, nexttask) {
				if(fileobj.img.jpgCustomSegments) {
					fileobj.dataArray = _.utils.jpgInsertSegment(fileobj.dataArray, fileobj.img.jpgCustomSegments);
					fileobj.size = fileobj.dataArray.length;
					_.view.repaintImageEditMessage(true);
				}

				_.utils.logTimeFromStart('metadata reinserted');
				_.controller.processImage(fileobj, nexttask);
			};

			this.processimagInsertMetadataPNG = function(fileobj, nexttask) {
				_.utils.logTimeFromStart('metadata reinsert skipped');
				_.controller.processImage(fileobj, nexttask);

				if(fileobj.img.pngTextChunks && fileobj.img.pngTextChunks.length){
					var combinedChuks = [];
					try{
						var chunks = extractChunks(fileobj.dataArray),
							combinedChuks = [];

						combinedChuks.push(chunks.shift()); // new IHDR
						while(fileobj.pngTextChunks.length){
							combinedChuks.push(fileobj.pngTextChunks.shift()); // all extracted text chunks
						}
						while(chunks.length){
							combinedChuks.push(chunks.shift()); // all extracted text chunks
						}
					} catch(e){
						combinedChuks = false;
					}

					var newUInt8Array = false;
					if(combinedChuks){
						try{
							newUInt8Array = encodeChunks(combinedChuks);
						} catch (e) {
							newUInt8Array = false;
						}
					}

					fileobj.dataArray = newUInt8Array ? newUInt8Array : fileobj.dataArray;
					_.view.repaintImageEditMessage(true);
				}

				_.utils.logTimeFromStart('metadata reinserted');
				_.controller.processImage(fileobj, nexttask);
			};

			this.processimageMakePreview = function(fileobj, nexttask, target){ // no synchronous action in here so we can normally go back
				target = target ? target : 'previewCanvas';

				if(fileobj && fileobj.img.workingCanvas){
					var previewCanvas = document.createElement("canvas"),
						previewMaxSize = _.view.previewSize,
						previewWidth = 0,
						previewHeight = 0;
				
						

					if(fileobj.img.workingCanvas.width > fileobj.img.workingCanvas.height){
						previewWidth = previewMaxSize;
						previewHeight = previewMaxSize / fileobj.img.workingCanvas.width * fileobj.img.workingCanvas.height;
						
					} else {
						previewHeight = previewMaxSize;
						previewWidth = previewMaxSize / fileobj.img.workingCanvas.height * fileobj.img.workingCanvas.width;
					}
					fileobj.img.previewWidth = Math.round(previewWidth);
					fileobj.img.previewHeight = Math.round(previewHeight);
					previewCanvas.width = fileobj.img.previewWidth;
					previewCanvas.height = fileobj.img.previewHeight;

					if(target === 'previewCanvas' && ((_.view.useOriginalAsPreviewIfNotEdited && fileobj.img.image && fileobj.img.image.src) || fileobj.dataUrl)){
						// preview of edited image (or original if not edited)
						if(fileobj.dataUrl){ // image is edited and saved to dataArray
							fileobj.img.previewImg = document.createElement('img');
							fileobj.img.previewImg.src = fileobj.dataUrl;
							//top.console.log('we use dataUrl');
						} else {
							fileobj.img.previewImg = fileobj.img.image;
							//top.console.log('we use img.image');
						}
						fileobj.img.previewImg.width = fileobj.img.previewWidth;
						fileobj.img.previewImg.height = fileobj.img.previewHeight;
					} else { // preview of original image => we downscale it the fast way, so we can use it for fast resetting GUI
						var ctxPreviewCanvas = previewCanvas.getContext("2d");
						ctxPreviewCanvas.drawImage(
							fileobj.img.workingCanvas,
							0,
							0,
							fileobj.img.workingCanvas.width,
							fileobj.img.workingCanvas.height,
							0,
							0,
							fileobj.img.previewWidth,
							fileobj.img.previewHeight
						);
						fileobj.img[target] = previewCanvas;
					}
					_.view.repaintImageEditMessage(true);
				}
				_.utils.logTimeFromStart('preview done');
				_.controller.processImage(fileobj, nexttask);

				return;
			};

			this.processimageReset = function(fileobj, nexttask){
				fileobj.dataArray = null;
				fileobj.dataUrl = null;
				fileobj.size = fileobj.img.originalSize;
				fileobj.img.previewImg = null;
				fileobj.img.fullPrev = null;
				fileobj.img.previewCanvas = fileobj.img.originalPreviewCanvas;// always use poorly scaled canvas for preview of NOT edited images!
				fileobj.isEdited = false;

				_.controller.processImage(fileobj, nexttask);
			};

			this.processimagePostProcess = function(fileobj, nexttask){
				if(fileobj.dataArray && fileobj.dataArray.length){
					fileobj.img.originalSize = fileobj.file.size;
					fileobj.size = fileobj.dataArray.length;
					fileobj.isEdited = true;
				}
				fileobj.img.fullPrev = null;
				fileobj.totalParts = Math.ceil(fileobj.size / _.sender.chunkSize);
				fileobj.lastChunkSize = fileobj.size % _.sender.chunkSize;
				_.view.repaintEntry(fileobj);

				_.utils.logTimeFromStart('processing finished');
				_.view.repaintImageEditMessage();
				_.controller.processImage(fileobj, nexttask);
			};

			this.checkBrowserCompatibility = function () {
				var xhrTestObj = new XMLHttpRequest(),
								xhrTest = xhrTestObj && xhrTestObj.upload ? true : false;

				return (xhrTest && window.File && window.FileReader && window.FileList && window.Blob) ? true : false;
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
					tc = _.sender.typeCondition,
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

			// obsolete!
			this.dataURLToUInt8Array = function (dataURL) {
				var BASE64_MARKER = ';base64,',
					parts = dataURL.split(BASE64_MARKER),
					//contentType = parts[0].split(':')[1],
					raw = window.atob(parts[1]),
					rawLength = raw.length,
					uInt8Array = new Uint8Array(rawLength);

				for (var i = 0; i < rawLength; ++i) {
					uInt8Array[i] = raw.charCodeAt(i);
				}

				return uInt8Array;
				//return new Blob([uInt8Array], {type: contentType});
			};

			this.jpgInsertSegment = function (uint8array, exifSegment) {
				if(uint8array[0] == 255 && uint8array[1] == 216 && uint8array[2] == 255 && uint8array[3] == 224){
					var pos = uint8array.indexOf(255, 4), 
						head = uint8array.slice(0, pos),
						segments = uint8array.slice(pos);

					return this.concatTypedArrays(Uint8Array, [head, exifSegment, segments]);
				}
			};

			this.jpgGetExifSegment = function (uint8array){
				return this.jpgGetSegment(uint8array, 225);
			};

			// use this to extract a certain segment: not intended to be used several times in sequence
			this.jpgGetSegment = function (uint8array, marker) {
				var head = 0;

				while (head < uint8array.length) {
					if (uint8array[head] == 255 & uint8array[head + 1] == 218){ // SOI = Scan of Image = image data (eg. canvas works on)!
						break;
					}

					if (uint8array[head] == 255 & uint8array[head + 1] == 216){ // omit 216 (D8): SOI = Start of Image (is empty so length = 2 bytes)
						head += 2;
					} else {
						var length = uint8array[head + 2] * 256 + uint8array[head + 3],
							endPoint = head + length + 2;

						if(uint8array[head + 1] == marker) {
							return uint8array.slice(head, endPoint);;
						}

						head = endPoint;
					}
				}

				return false;
			};

			this.jpgGetAllSegmentsUpToSOS = function(uint8array) {
				var head = 0,
					segments = {},
					order = [];

				while (head < uint8array.length) {
					// each segment starts with 255 (FF) and its segment marker
					if (uint8array[head] == 255 & uint8array[head + 1] == 218){ // 218 (DA): SOS = Start of Scan = image data (canvas works on)
						//top.console.log('found SOS at pos', head, segments);
						break;
					}
					if (uint8array[head] == 255 & uint8array[head + 1] == 216){ // omit 216 (D8): SOI = Start of Image (is empty so length = 2 bytes)
						head += 2;
					} else {
						var length = uint8array[head + 2] * 256 + uint8array[head + 3],
							endPoint = head + length + 2;

						order.push(uint8array[head + 1]);
						segments[uint8array[head + 1] + '_' + head] = uint8array.slice(head, endPoint);
						head = endPoint;
					}
				}
				
				return {order: order, segments: segments};
			};

			this.jpgGetSegmentsIfExist = function (uint8array, segments, concat) {
				var head = 0,
					searchObj = {},
					segmentsArr = [],
					controllArr = [];
			
				for (var i = 0; i < segments.length; i++) {
					searchObj[segments[i]] = true;
				}

				while (head < uint8array.length) {
					if (uint8array[head] == 255 & uint8array[head + 1] == 218){ // SOI = Scan of Image = image data (eg. canvas works on)!
						break;
					}

					if (uint8array[head] == 255 & uint8array[head + 1] == 216){ // omit 216 (D8): SOI = Start of Image (is empty so length = 2 bytes)
						head += 2;
					} else {
						var length = uint8array[head + 2] * 256 + uint8array[head + 3],
							endPoint = head + length + 2;

						if(searchObj[uint8array[head + 1]] === true) {
							segmentsArr.push(uint8array.slice(head, endPoint));
							controllArr.push({marker: uint8array[head + 1], head: head, length: (endPoint-head)});

							/*
							delete searchObj[uint8array[head + 1]]; // there can be duplicate segemnst: we want o restore them as is!
							if (searchObj === {}){ // nothing left to search for
								break;
							}
							*/
						}

						head = endPoint;
					}
				}
				//top.console.log('all custom segments', {list: controllArr, segments: segmentsArr});

				return concat ? this.concatTypedArrays(Uint8Array, segmentsArr) : segmentsArr;
			};

			this.jpgGetExifParsed = function(uint8array) {
				var exif;

				try {
					exif = new ExifReader();
					exif.load(event.target.result);
					// The MakerNote tag can be really large. Remove it to lower memory usage.
					exif.deleteTag('MakerNote');

					return exif.getAllTags();
				} catch (error) {
					top.console.debug('extract exif failed');
					return false;
				}
			};

			this.concatTypedArrays = function (resultConstructor, arrays) {
				var size = 0,
					pos = 0;

				for (var i = 0; i < arrays.length; i++) {
					size += arrays[i].length;
				}

				var result = new resultConstructor(size);

				for (var i = 0; i < arrays.length; i++) {
					result.set(arrays[i], pos);
					pos += arrays[i].length;
				}

				return result;
			};
		}

		this.getAbstractController = function () {
			return new AbstractController();
		};

		this.getAbstractSender = function () {
			return new AbstractSender();
		};

		this.getAbstractView = function () {
			return new AbstractView();
		};

		this.getAbstractUtils = function () {
			return new AbstractUtils();
		};

		//public functions (accessible from outside)
		this.startUpload = function () {
			if (_.sender.prepareUpload()) {
				//setTimeout(_.sender.sendNextFile, 100); // FIXME: check why this does not work!!
				setTimeout(function () {
					_.sender.sendNextFile();
				}, 100);
			} else {
				_.sender.processError({from: 'gui', msg: _.utils.gl.errorNoFileSelected});
			}
		};

		this.cancelUpload = function () {
			_.sender.cancel();
		};

		this.reset = function () {
			_.view.elems.fileSelect.value = null;
			_.view.repaintGUI({what: 'resetGui'});
		};

		this.deleteRow = function (index, but) {
			_.view.deleteRow(index, but);
		};

		this.setDoEditGlobal = function (fileobj, checkox) {
			_.view.setDoEditGlobal(fileobj, checkox);
		};

		this.setUseGeneralOpts = function (fileobj, checkox) {
			_.view.setUseGeneralOpts(fileobj, checkox);
		};

		this.setCustomEditOpts = function (fileobj, radio) {
			_.view.setCustomEditOpts(fileobj, radio);
		};

		this.getType = function () {
			return _.fileuploadType;
		};

		this.doUploadIfReady = function (callback) {
			callback();
			return;
		};

		this.reeditImage = function (fileObj, pos, all) {
			_.controller.reeditImage(fileObj, pos, all);
		};
		
		this.openImageEditor = function(pos){
			_.controller.openImageEditor(pos);
		};
	}

	function weFileUpload_base() {
		(function () {
			weFileUpload_abstract.call(this);

			Controller.prototype = this.getAbstractController();
			Sender.prototype = this.getAbstractSender();
			View.prototype = this.getAbstractView();
			Utils.prototype = this.getAbstractUtils();

			_.fileuploadType = 'base';
			_.self = this;
			_.controller = new Controller();
			_.sender = new Sender();
			_.view = new View();
			_.utils = new Utils();
		})();

		this.init = function (conf) {
			_.init_abstract(conf);
			_.view.uploadBtnName = conf.uploadBtnName || _.view.uploadBtnName;//disableUploadBtnOnInit
			_.view.isInternalBtnUpload = conf.isInternalBtnUpload || _.view.isInternalBtnUpload;
			_.view.disableUploadBtnOnInit = conf.disableUploadBtnOnInit || false;
		};

		_.onload = function (scope) {
			var that = scope;
			_.onload_abstract(that);

			//get references to some include-specific html elements
			_.view.elems.message = document.getElementById('div_' + _.fieldName + '_message');
			_.view.elems.progress = document.getElementById('div_' + _.fieldName + '_progress');
			_.view.elems.progressText = document.getElementById('span_' + _.fieldName + '_progress_text');
			_.view.elems.progressMoreText = document.getElementById('span_' + _.fieldName + '_progress_more_text');
			_.view.elems.fileName = document.getElementById('div_' + _.fieldName + '_fileName');
			_.view.elems.btnResetUpload = document.getElementById('div_' + _.fieldName + '_btnResetUpload');
			_.view.elems.btnCancel = document.getElementById('div_' + _.fieldName + '_btnCancel');
			_.view.repaintGUI({what: 'initGui'});

			_.controller.checkIsPresetFiles();
		};

		function Controller() {
		}

		function Sender() {
			this.totalWeight = 0;

			/* use parent (abstract)
			 this.appendMoreData = function (fd) {
			 for(var i = 0; i < this.moreFieldsToAppend.length; i++){
			 if(document.we_form.elements[this.moreFieldsToAppend[i]]){
			 fd.append(this.moreFieldsToAppend[i], document.we_form.elements[this.moreFieldsToAppend[i]].value);
			 }
			 }

			 return fd;
			 };
			 */

			this.postProcess = function (resp) {
				var that = _.sender,
								cur = this.currentFile;

				this.form.form.elements.weFileNameTemp.value = cur.fileNameTemp;
				this.form.form.elements.weFileCt.value = cur.mimePHP;
				this.form.form.elements.weFileName.value = cur.file.name;
				//this.form.form.elements.weIsUploadComplete.value = 1;
				//setTimeout(that.callback, 100, resp); // FIXME: check if this works
				setTimeout(function () {
					that.callback(resp);
				}, 100);
			};

			this.processError = function (arg) {
				switch (arg.from) {
					case 'gui' :
						top.we_showMessage(arg.msg, 4, window);
						return;
					case 'request' :
						_.view.repaintGUI({what: 'fileNOK'});
						_.view.repaintGUI({what: 'resetGui'});
						return;
					default :
						return;
				}
			};

			this.resetParams = function () {
				this.preparedFiles = [];
				this.totalWeight = 0;
				this.isCancelled = false;
				_.view.repaintGUI({what: 'resetGui'});
			};

			this.prepareUpload = function () {
				if (this.preparedFiles.length < 1) {
					return false;
				}
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
				_.view.repaintGUI({what: 'cancelUpload'});
			};
		}

		function View() {
			this.uploadBtnName = '';
			this.isInternalBtnUpload = false;
			this.disableUploadBtnOnInit = false;

			this.addFile = function (f) {
				var sizeText = f.isSizeOk ? _.utils.gl.sizeTextOk + _.utils.computeSize(f.size) + ', ' :
								'<span style="color:red;">' + _.utils.gl.sizeTextNok + '</span>';
				var typeText = f.isTypeOk ? _.utils.gl.typeTextOk + f.type :
								'<span style="color:red;">' + _.utils.gl.typeTextNok + f.type + '</span>';

				this.elems.message.innerHTML = sizeText + typeText;

				if (this.isDragAndDrop) {
					this.elems.fileDrag.innerHTML = f.file.name;
				} else {
					this.elems.fileName.innerHTML = f.file.name;
					this.elems.fileName.style.display = '';
				}
				_.controller.setWeButtonState('reset_btn', true);
				_.controller.setWeButtonState(_.view.uploadBtnName, f.uploadConditionsOk ? true : false, true);
			};

			this.setDisplay = function (elem, val) { // move to abstract (from binDoc too)
				if (this.elems[elem]) {
					this.elems[elem].style.display = val;
				}
			};

			this.repaintGUI = function (arg) {
				switch (arg.what) {
					case 'initGui' :
						_.controller.setWeButtonState(_.view.uploadBtnName, !this.disableUploadBtnOnInit, true);
						return;
					case 'chunkOK' :
						var prog = (100 / _.sender.currentFile.size) * _.sender.currentFile.currentWeightFile,
										digits = _.sender.currentFile.totalParts > 1000 ? 2 : (_.sender.currentFile.totalParts > 100 ? 1 : 0);

						if (this.elems.progress) {
							this.setInternalProgress(prog.toFixed(digits), false);
						}
						if (this.extProgress.isExtProgress) {
							//FIXME: use elems.footer (with elems.footer = top, when not in seperate iFrame!
							this.elems.footer['setProgress' + this.extProgress.name](prog.toFixed(digits));
						}
						return;
					case 'fileOK' :
						if (this.elems.progress) {
							this.setInternalProgressCompleted(true);
						}
						if (this.extProgress.isExtProgress) {
							//FIXME: use elems.footer (with elems.footer = top, when not in seperate iFrame!
							this.elems.footer['setProgress' + this.extProgress.name](100);
						}
						return;
					case 'fileNOK' :
						if (this.elems.progress) {
							this.setInternalProgressCompleted(false);
						}
						return;
					case 'startSendFile' :
						if (this.elems.progress) {
							this.elems.message.style.display = 'none';
							document.getElementById(_.fieldName + '_progress_image').className = "progress_image";
							this.elems.progress.style.display = '';
							this.elems.progressMoreText.style.display = '';
							this.elems.progressMoreText.innerHTML = '&nbsp;&nbsp;/ ' + _.utils.computeSize(_.sender.currentFile.size);
						}
						if (this.extProgress.isExtProgress) {
							this.elems.extProgressDiv.style.display = '';
						}
						_.controller.setWeButtonState('reset_btn', false);
						_.controller.setWeButtonState('browse_harddisk_btn', false);
						if (this.isInternalBtnUpload) {
							_.controller.setWeButtonState(_.view.uploadBtnName, false, true);
							this.setDisplay('btnResetUpload', 'none');
							this.setDisplay('btnCancel', 'inline-block');
						}
						return;
					case 'cancelUpload' :
						//this.elems.footer['setProgressText' + this.extProgress.name]('progress_text', _.utils.gl.cancelled);
						if (this.elems.progress) {
							this.setInternalProgressCompleted(false, false, _.utils.gl.cancelled);
						}
						_.controller.setWeButtonState('reset_btn', true);
						_.controller.setWeButtonState('browse_harddisk_btn', true);
						return;
					case 'resetGui' :
						_.sender.preparedFiles = [];
						this.currentFile = -1;
						_.view.elems.message.innerHTML = '';
						_.view.elems.message.innerHTML.display = 'none';
						if (_.view.elems.fileDrag) {
							_.view.elems.fileDrag.innerHTML = _.utils.gl.dropText;
						}
						if (_.view.elems.progress) {
							this.setInternalProgress(0);
							_.view.elems.progress.style.display = 'none';
						}
						if (this.extProgress.isExtProgress) {
							this.elems.footer['setProgress' + this.extProgress.name](0);
							_.view.elems.extProgressDiv.style.display = 'none';
						}
						_.controller.setWeButtonState('browse_harddisk_btn', true);
						_.controller.setWeButtonState('reset_btn', false);
						if (this.isInternalBtnUpload) {
							_.controller.setWeButtonState(_.view.uploadBtnName, false, true);
							this.setDisplay('btnResetUpload', 'inline-block');
							this.setDisplay('btnCancel', 'none');
						}
						return;
					default :
						return;
				}
			};
		}

		function Utils() {}
	}

	function weFileUpload_importer() {
		(function () {
			weFileUpload_abstract.call(this);

			Controller.prototype = this.getAbstractController();
			Sender.prototype = this.getAbstractSender();
			View.prototype = this.getAbstractView();
			Utils.prototype = this.getAbstractUtils();

			_.fileuploadType = 'importer';
			_.self = this;
			_.controller = new Controller();
			_.sender = new Sender();
			_.view = new View();
			_.utils = new Utils();
		})();

		this.init = function (conf) {
			_.init_abstract(conf);

			//initialize weFileUpload_importer properties: dispatch them to respective module-objects
			if (typeof conf !== 'undefined') {
				_.sender.isGdOk = typeof conf.isGdOk !== 'undefined' ? conf.isGdOk : _.sender.isGdOk;
				_.view.htmlFileRow = conf.htmlFileRow || _.view.htmlFileRow;
				_.utils.fileTable = conf.fileTable || _.view.fileTable;
			}
		};

		_.onload = function (scope) {
			var that = scope;

			_.onload_abstract(that);
			_.controller.setWeButtonText('next', 'upload');
			_.controller.enableWeButton('next', false);

			// add some listeners:
			var generalform = document.getElementById('filechooser');
			/*
			generalform.fu_doc_unitSelect.addEventListener('change', function(e) {_.view.syncCustomEditOpts(e.target);});
			generalform.fu_doc_resizeValue.addEventListener('keyup', function(e) {_.view.syncCustomEditOpts(e.target);});
			generalform.fu_doc_rotate.addEventListener('change', function(e) {_.view.syncCustomEditOpts(e.target);});
			*/
			generalform.fu_doc_resizeValue.addEventListener('keyup', function(e) {_.controller.editOptionsOnChange(e.target);});
			generalform.fu_doc_rotate.addEventListener('change', function(e) {_.controller.editOptionsOnChange(e.target);});
		};

		function Controller() {
			var that = _.controller;

			this.replaceSelectionHandler = function (e) {
				var files = e.target.files;
				var f;
				if (files[0] instanceof File && !_.utils.contains(_.sender.preparedFiles, files[0])) {
					f = _.controller.prepareFile(files[0]);
					_.controller.processImages(f);


					var inputId = 'fileInput_uploadFiles_',
						index = e.target.id.substring(inputId.length),
						entry = _.sender.preparedFiles[index].entry;

					_.sender.preparedFiles[index] = f; //.isSizeOk ? f : null;
					_.sender.preparedFiles[index].entry = entry;

					var transformables = ['image/jpeg', 'image/gif', 'image/png']; //TODO: add all transformable types and make "global" (_.editables)
					if (transformables.indexOf(f.type) !== -1) {
						_.controller.processImages(_.sender.preparedFiles[index]);
					} else {
						_.view.repaintEntry(f);
					}

					if (f.isSizeOk) {
						if (!_.view.isUploadEnabled) {
							_.controller.enableWeButton('next', true);
							_.view.isUploadEnabled = true;
							_.sender.isCancelled = false;
						}
					}
				}
			};

			this.enableWeButton = function (btn, enabled) {
				_.view.elems.footer[btn + '_enabled'] = top.WE().layout.button.switch_button_state(_.view.elems.footer.document, btn, (enabled ? 'enabled' : 'disabled'));
			};

			this.setWeButtonText = function (btn, text) {
				var replace = _.utils.gl.btnUpload;

				switch (text) {
					case 'close' :
						replace = _.utils.gl.btnClose;
						break;
					case 'cancel' :
						replace = _.utils.gl.btnCancel;
						break;
					case 'upload' :
						replace = _.utils.gl.btnUpload;
				}

				if (replace) {
					top.WE().layout.button.setText(_.view.elems.footer.document, btn, replace);
				}
			};

			this.resetImageEdit = function (fileobj) {
				fileobj.dataArray = null;
			};

			this.openImageEditor = function(index){
				if(_.sender.preparedFiles[index]){
					// TODO: use WE().util.jsWindow
					//var previewWin = new (WE().util.jsWindow)(this, '', "", -1, -1, 840, 400, true, false, true);
					var previewWin = window.open('', '', 'width=700,height=500');
					var img;

					if(_.sender.preparedFiles[index].isEdited && _.sender.preparedFiles[index].dataUrl){
						// IMPORTANT: try tpo load img from fileobj.dataArray to avoid saving dataUrl!!
						img = previewWin.document.createElement("img");
						img.src = _.sender.preparedFiles[index].dataUrl;
						previewWin.document.write('<h3>image editet</h3>');
						previewWin.document.write('<p>here we could edit/reedit image, applying filters, antialiasing and set focus!</p>');
						previewWin.document.body.appendChild(img);
						img = null;
					} else {
						var reader = new FileReader();
						reader.onload = function() {
							img = previewWin.document.createElement("img");
							img.src = reader.result;
							previewWin.document.write('<h3>image not editet</h3>');
							previewWin.document.write('<p>here we could edit/reedit image, applying filters, antialiasing and set focus!</p>');
							previewWin.document.body.appendChild(img);
							img = null;
						};
						reader.readAsDataURL(_.sender.preparedFiles[index].file);
					}
				}
			};
		}

		function Sender() {
			this.isGdOk = false;
			this.isCancelled = false;
			this.totalChunks = 0; //FIXME: apply consistent terminology to differ between currentfile and all files: and make it part of abstract
			this.mapFiles = [];

			this.prepareUpload = function () {
				if (this.currentFile === -1) {
					this.uploadFiles = [];
					this.mapFiles = [];
					for (var i = 0, c = 0; i < this.preparedFiles.length; i++) {
						if (typeof this.preparedFiles[i] === 'object' && this.preparedFiles[i] !== null && this.preparedFiles[i].isUploadable) {
							this.preparedFiles[i].fileNum = c++;
							this.uploadFiles.push(this.preparedFiles[i]);
							this.mapFiles.push(i);
							this.totalWeight += this.preparedFiles[i].size;//size?
							document.getElementById('div_rowButtons_' + i).style.display = 'none';
							document.getElementById('div_rowProgress_' + i).style.display = 'block';
						}
					}
					this.totalFiles = this.uploadFiles.length;

					if (this.totalFiles > 0) {
						this.currentWeight = 0;
						this.totalChunks = this.totalWeight / this.chunkSize;
						this.currentWeight = 0;
						this.currentWeightTag = 0;
						_.view.repaintGUI({what: 'startUpload'});

						return true;
					}
				}
				return false;
			};

			this.cancel = function () {
				if (!this.isUploading) {
					top.close();
				}
				this.isCancelled = true;
				this.isUploading = false;
				_.view.repaintGUI({what: 'cancelUpload'});
				this.postProcess('', true);
				//top.we_showMessage(_.utils.gl.uploadCancelled, 1, window);
			};

			this.appendMoreData = function (fd) { // TODO: set additional fields oninit
				var sf = document.we_startform,
					cur = this.currentFile;

				fd.append('weFormNum', cur.fileNum + 1);
				fd.append('weFormCount', this.totalFiles);
				fd.append('we_cmd[0]', 'import_files');
				fd.append('step', 1);

				fd.append('fu_file_sameName', sf.fu_file_sameName.value);
				fd.append('fu_file_parentID', sf.fu_file_parentID.value);
				fd.append('fu_doc_categories', sf.fu_doc_categories.value);
				fd.append('fu_doc_importMetadata', sf.fu_doc_importMetadata.value);
				fd.append('fu_doc_isSearchable', sf.fu_doc_isSearchable.value);

				var transformables = ['image/jpeg', 'image/gif', 'image/png'];//TODO: add all transformable types
				if (transformables.indexOf(cur.type) !== -1 && cur.partNum === cur.totalParts) {
					if (!_.EDIT_IMAGES_CLIENTSIDE) {
						fd.append('fu_doc_width', sf.fu_doc_width.value);
						fd.append('fu_doc_height', sf.fu_doc_height.value);
						fd.append('fu_doc_widthSelect', sf.fu_doc_widthSelect.value);
						fd.append('fu_doc_heightSelect', sf.fu_doc_heightSelect.value);
						fd.append('fu_doc_keepRatio', sf.fu_doc_keepRatio.value);
						fd.append('fu_doc_quality', sf.fu_doc_quality.value);
						fd.append('fu_doc_degrees', sf.fu_doc_degrees.value);
					} else {
						//fd.append('exif', JSON.stringify(cur.exif));
						fd.append('fu_doc_focusX', cur.img.focusX);
						fd.append('fu_doc_focusX', cur.img.focusY);
					}
					fd.append('fu_doc_thumbs', sf.fu_doc_thumbs.value);
				}

				return fd;
			};

			this.postProcess = function (resp) {
				var that = _.sender;
				_.sender.resp = resp;

				if (!this.isCancelled) {
					_.view.elems.footer.setProgress(100);
					_.view.elems.footer.setProgressText('progress_title', '');
					top.we_showMessage(resp.completed.message, resp.completed.type, window);

					setTimeout(function () {
						that.callback(_);
					}, 100);
				}
				_.view.reloadOpener();

				//reinitialize some vars to add and upload more files
				this.isUploading = false;
				this.resetSender();
				_.controller.setWeButtonText('cancel', 'close');
			};

			this.processError = function (arg) {
				switch (arg.from) {
					case 'gui' :
						top.we_showMessage(arg.msg, 4, window);
						return;
					case 'request' :
						//_.view.repaintGUI({what : 'fileNOK'});
						//this.resetSender();
						return;
					default :
						return;
				}
			};

			this.resetSender = function () {
				for (var i = 0; i < _.sender.preparedFiles.length; i++) {
					if (!this.isCancelled && _.sender.preparedFiles[i]) {
						_.sender.preparedFiles[i].isUploadable = false;
					} else {
						_.sender.preparedFiles[i] = null;
					}
				}
				this.uploadFiles = [];
				this.currentFile = -1;
				this.mapFiles = [];
				this.totalFiles = _.sender.totalWeight = _.sender.currentWeight = _.sender.currentWeightTag = 0;
				_.view.elems.footer.setProgress(0);
				_.view.elems.extProgressDiv.style.display = 'none';
				_.controller.setWeButtonState('reset_btn', true);
				_.controller.setWeButtonState('browse_harddisk_btn', true);
			};

		}

		function View() {
			this.fileTable = '';
			this.htmlFileRow = '';
			this.nextTitleNr = 1;
			this.isUploadEnabled = false;
			this.messageWindow = null;
			this.previewSize = 110;
			//this.useOriginalAsPreviewIfNotEdited = true;

			this.addFile = function (f, index) {
				this.appendRow(f, _.sender.preparedFiles.length - 1);//document.getElementById('name_uploadFiles_0').innerHTML = 'juhu';
			};

			this.repaintEntry = function (fileobj) {
				fileobj.entry.getElementsByClassName('elemSize')[0].innerHTML = (fileobj.isSizeOk ? _.utils.computeSize(fileobj.size) : '<span style="color:red">> ' + ((_.sender.maxUploadSize / 1024) / 1024) + ' MB</span>');
				_.view.addTextCutLeft(fileobj.entry.getElementsByClassName('elemFilename')[0], fileobj.file.name, 220);

				var transformables = ['image/jpeg', 'image/gif', 'image/png'];
				if(transformables.indexOf(fileobj.type) !== -1){
					fileobj.entry.getElementsByClassName('elemIcon')[0].style.display = 'none';
					fileobj.entry.getElementsByClassName('elemPreview')[0].style.display = 'block';
					fileobj.entry.getElementsByClassName('elemContentBottom')[0].style.display = 'block';
					fileobj.entry.getElementsByClassName('elemPreviewPreview')[0].innerHTML = '';
					fileobj.entry.getElementsByClassName('elemPreviewPreview')[0].appendChild(fileobj.img.previewImg ? fileobj.img.previewImg : fileobj.img.previewCanvas);

					fileobj.entry.getElementsByClassName('elemPreviewPreview')[0].firstChild.addEventListener('mouseenter', function(){_.view.setPreviewLoupe(fileobj);}, false);
					fileobj.entry.getElementsByClassName('elemPreviewPreview')[0].firstChild.addEventListener('mousemove', function(e){_.view.movePreviewLoupe(e, fileobj);}, false);
					fileobj.entry.getElementsByClassName('elemPreviewPreview')[0].firstChild.addEventListener('mouseleave', function(){_.view.unsetPreviewLoupe(fileobj);}, false);
					fileobj.entry.getElementsByClassName('elemPreviewPreview')[0].firstChild.addEventListener('click', function(e){_.view.grabFocusPoint(e,fileobj);}, false);

					fileobj.entry.getElementsByClassName('elemContentBottom')[0].style.backgroundColor = fileobj.isEdited ? 'rgb(216, 255, 216)' : '#ffffff';
					this.formCustomOptsSync(fileobj);

					// add mouseover listeners to preview
					var classes = ['elemPreview', 'elemPreviewPreview', 'elemPreviewBtn'];
					for(var i = 0; i < classes.length; i++){
						fileobj.entry.getElementsByClassName(classes[i])[0].addEventListener('mouseover', function(){
							//fileobj.entry.getElementsByClassName('elemPreviewBtn')[0].style.display = 'block';
						}, false);
						fileobj.entry.getElementsByClassName(classes[i])[0].addEventListener('mouseout', function(){
							//fileobj.entry.getElementsByClassName('elemPreviewBtn')[0].style.display = 'none';
						}, false);
					}

				} else {
					fileobj.entry.getElementsByClassName('elemIcon')[0].style.display = 'block';
					fileobj.entry.getElementsByClassName('elemPreview')[0].style.display = 'none';
					fileobj.entry.getElementsByClassName('elemContentBottom')[0].style.display = 'none';

					var ext = fileobj.file.name.substr(fileobj.file.name.lastIndexOf('.') + 1).toUpperCase();
					fileobj.entry.getElementsByClassName('elemIcon')[0].innerHTML = WE().util.getTreeIcon(fileobj.type) + ' ' + ext;
				}
			};

			this.setImageEditMessage = function () {
				var elem;
				if((elem = document.getElementById('we_fileUploadImporter_mask'))){
					document.getElementById('we_fileUploadImporter_messageNr').innerHTML = _.sender.imageFilesToProcess.length;
					document.getElementById('we_fileUploadImporter_message').style.display = 'block';
					document.getElementById('we_fileUploadImporter_message').style.zIndex = 800;
					elem.style.display = 'block';
				}
			};

			this.unsetImageEditMessage = function () {
				var elem;
				if((elem = document.getElementById('we_fileUploadImporter_mask'))){
					elem.style.display = 'none';
					document.getElementById('we_fileUploadImporter_message').style.display = 'none';
				}
			};

			this.repaintImageEditMessage = function(step) {
				if(step){
					
				} else {
					document.getElementById('we_fileUploadImporter_messageNr').innerHTML = _.sender.imageFilesToProcess.length;
				}
			};

			this.appendRow = function (f, index) {
				var div,
					entry,
					row = this.htmlFileRow.replace(/WEFORMNUM/g, index).replace(/WE_FORM_NUM/g, (this.nextTitleNr++)).
						replace(/FILENAME/g, (f.file.name)).
						replace(/FILESIZE/g, (f.isSizeOk ? _.utils.computeSize(f.size) : '<span style="color:red">> ' + ((_.sender.maxUploadSize / 1024) / 1024) + ' MB</span>'));

				weAppendMultiboxRow(row, '', 0, 0, 0, -1);
				entry = document.getElementById('div_uploadFiles_' + index);

				div = document.getElementById('div_upload_files');
				//div.scrollTop = getElementById('div_upload_files').div.scrollHeight;
				document.getElementById('fileInput_uploadFiles_' + index).addEventListener('change', _.controller.replaceSelectionHandler, false);

				_.view.addTextCutLeft(document.getElementById('name_uploadFiles_' + index), f.file.name, 220);

				var transformables = ['image/jpeg', 'image/gif', 'image/png'];
				if(transformables.indexOf(f.type) !== -1){
					document.getElementById('icon_uploadFiles_' + index).style.display = 'none';
					document.getElementById('preview_uploadFiles_' + index).style.display = 'block';
					document.getElementById('editoptions_uploadFiles_' + index).style.display = 'block';
				} else {
					var ext = f.file.name.substr(f.file.name.lastIndexOf('.') + 1).toUpperCase();
					document.getElementById('icon_uploadFiles_' + index).innerHTML = WE().util.getTreeIcon(f.type) + ' ' + ext;
				}

				

				this.elems.extProgressDiv.style.display = 'none';
				_.controller.setWeButtonText('cancel', 'cancel');

				if (f.isSizeOk) {
					if (!this.isUploadEnabled) {
						_.controller.setWeButtonState('reset_btn', true);
						_.controller.enableWeButton('next', true);
						this.isUploadEnabled = true;
						_.sender.isCancelled = false;
					}
				} else {
					_.sender.preparedFiles[index] = null;
				}
				f.index = index;
				f.entry = document.getElementById('div_uploadFiles_' + index);

				var form = document.getElementById('form_editOpts_' + index);
				form.elements['resizeValue'].addEventListener('keyup', function(e){_.controller.editOptionsOnChange(e.target);}, false);
				form.elements['rotateSelect'].addEventListener('change', function(e){_.controller.editOptionsOnChange(e.target);}, false);
				form.elements['quality'].addEventListener('change', function(e){_.controller.editOptionsOnChange(e.target);}, false);

			};

			this.deleteRow = function (index, button) {
				var prefix = 'div_uploadFiles_',
					num = 0,
					z = 1,
					i,
					sp,
					divs = document.getElementsByTagName('DIV');

				_.sender.preparedFiles[index] = null;
				weDelMultiboxRow(index);

				for (i = 0; i < divs.length; i++) {
					if (divs[i].id.length > prefix.length && divs[i].id.substring(0, prefix.length) === prefix) {
						num = divs[i].id.substring(prefix.length, divs[i].id.length);
						sp = document.getElementById('headline_uploadFiles_' + num);
						if (sp) {
							sp.innerHTML = z;
						}
						z++;
					}
				}
				this.nextTitleNr = z;
				if (!_.utils.containsFiles(_.sender.preparedFiles)) {
					_.controller.enableWeButton('next', false);
					_.controller.setWeButtonState('reset_btn', false);
					this.isUploadEnabled = false;
				}
			};

			this.reloadOpener = function () {
				try {
					var activeFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();

					if (document.we_startform.fu_file_parentID.value === activeFrame.EditorDocumentId && activeFrame.EditorEditPageNr === 16) {
						top.opener.top.we_cmd('switch_edit_page', 16, activeFrame.EditorTransaction);
					}
					top.opener.top.we_cmd('load', 'tblFile');
				} catch (e) {
					//
				}
			};

			this.repaintGUI = function (arg) {
				var i, j,
								s = _.sender,
								cur = s.currentFile,
								fileProg = 0,
								totalProg = 0,
								digits = 0,
								totalDigits = s.totalChunks > 1000 ? 2 : (s.totalChunks > 100 ? 1 : 0);

				switch (arg.what) {
					case 'chunkOK' :
						digits = cur.totalParts > 1000 ? 2 : (cur.totalParts > 100 ? 1 : 0);//FIXME: make fn on UtilsAbstract
						fileProg = (100 / cur.size) * cur.currentWeightFile;
						totalProg = (100 / s.totalWeight) * s.currentWeight;
						i = s.mapFiles[cur.fileNum];
						j = i + 1;
						this.setInternalProgress(fileProg.toFixed(digits), i);
						if (cur.partNum === 1) {
							this.elems.footer.setProgressText('progress_title', _.utils.gl.doImport + ' ' + _.utils.gl.file + ' ' + j);
						}
						this.elems.footer.setProgress(totalProg.toFixed(totalDigits));
						return;
					case 'fileOK' :
						i = s.mapFiles[cur.fileNum];
						try {
							document.getElementById('div_upload_files').scrollTop = document.getElementById('div_uploadFiles_' + i).offsetTop - 360;
						} catch (e) {
						}

						this.setInternalProgressCompleted(true, i, '');
						return;
					case 'chunkNOK' :
						totalProg = (100 / s.totalWeight) * s.currentWeight;
						i = s.mapFiles[cur.fileNum];
						j = i + 1;
						try {
							document.getElementById('div_upload_files').scrollTop = document.getElementById('div_uploadFiles_' + i).offsetTop - 200;
						} catch (e) {
						}
						this.setInternalProgressCompleted(false, i, arg.message);
						if (cur.partNum === 1) {
							this.elems.footer.setProgressText('progress_title', _.utils.gl.doImport + ' ' + _.utils.gl.file + ' ' + j);
						}
						this.elems.footer.setProgress(totalProg.toFixed(totalDigits));
						return;
					case 'startUpload' :
						//set buttons state and show initial progress bar
						_.controller.enableWeButton('back', false);
						_.controller.enableWeButton('next', false);
						_.controller.setWeButtonState('reset_btn', false);
						_.controller.setWeButtonState('browse_harddisk_btn', false);
						this.isUploadEnabled = false;
						this.elems.footer.document.getElementById('progressbar').style.display = '';
						this.elems.footer.setProgressText('progress_title', _.utils.gl.doImport + ' ' + _.utils.gl.file + ' 1');
						try {
							document.getElementById('div_upload_files').scrollTop = 0;
						} catch (e) {
						}

						return;
					case 'cancelUpload' :
						i = s.mapFiles[cur.fileNum];
						this.setInternalProgressCompleted(false, s.mapFiles[cur.fileNum], _.utils.gl.cancelled);
						try {
							document.getElementById('div_upload_files').scrollTop = document.getElementById('div_uploadFiles_' + i).offsetTop - 200;
						} catch (e) {
						}
						for (j = 0; j < s.uploadFiles.length; j++) {
							var file = s.uploadFiles[j];
							this.setInternalProgressCompleted(false, s.mapFiles[file.fileNum], _.utils.gl.cancelled);
						}

						_.controller.setWeButtonState('reset_btn', true);
						_.controller.setWeButtonState('browse_harddisk_btn', true);
						return;
					case 'resetGui' :
						try {
							document.getElementById('td_uploadFiles').innerHTML = '';
						} catch (e) {
						}
						_.sender.preparedFiles = [];
						this.nextTitleNr = 1;
						this.isUploadEnabled = false;
						_.controller.enableWeButton('next', false);
						_.sender.resetSender();
						return;
					default :
						return;
				}
			};

			this.setInternalProgressCompleted = function (success, index, txt) {
				if (success) {
					this.setInternalProgress(100, index);
					if (document.getElementById(_.fieldName + '_progress_image_' + index)) {
						document.getElementById(_.fieldName + '_progress_image_' + index).className = 'progress_finished';
					}
				} else {
					if (typeof document.images['alert_img_' + index] !== 'undefined') {
						document.images['alert_img_' + index].style.visibility = 'visible';
						document.images['alert_img_' + index].title = txt;
					}
					if (document.getElementById(_.fieldName + '_progress_image_' + index)) {
						document.getElementById(_.fieldName + '_progress_image_' + index).className = 'progress_failed';
					}
				}
			};

			this.setUseGeneralOpts = function (checkbox) {
				switch(checkbox.checked){
					case true:
						var radios = checkbox.form.editOpts;
						for(var i = 0; i < radios.length; i++){
							radios[i].disabled = true;
							if((index = radios[i].nextSibling.className.split(' ').indexOf('disabled')) === -1){
								radios[i].nextSibling.className += ' disabled';
							}
							
						}
						checkbox.form.unitSelect.disabled = true;
						checkbox.form.resizeValue.disabled = true;
						checkbox.form.rotateSelect.disabled = true;
						checkbox.form.getElementsByClassName('weBtn')[0].disabled = true;
						_.controller.reeditImage(null, checkbox.form.getAttribute('data-index'));
						break;
					default:
						var radios = checkbox.form.editOpts;
						var index, classes; 
						for(var i = 0; i < radios.length; i++){
							radios[i].disabled = false;
							if((index = radios[i].nextSibling.className.split(' ').indexOf('disabled')) !== -1){
								classes = radios[i].nextSibling.className.split(' ');
								classes.splice(index, 1);
								radios[i].nextSibling.className = classes.join(' ');
							}
						}
						_.view.setCustomEditOpts(checkbox.form);
						_.controller.reeditImage(null, checkbox.form.getAttribute('data-index'));
						break;
					
				}
			};

			this.setCustomEditOpts = function(form){
				switch(form.editOpts.value){
					case 'custom':
						form.unitSelect.disabled = false;
						form.resizeValue.disabled = false;
						form.rotateSelect.disabled = false;
						_.view.formCustomOptsReset(form);
						//form.getElementsByClassName('weBtn')[0].disabled = false;
						break;
					case 'expert':
						alert('not yet implemented');
						form.editOpts.value = 'custom';
						_.view.setCustomEditOpts(form);
				}
			};

			this.formCustomOptsSync = function (fileobj) {
				var form = document.getElementById('form_editOpts_' + fileobj.index);

				if(form && fileobj.img.editOptions){
					if(fileobj.img.editOptions.from === 'general' && !fileobj.img.editOptions.doEdit){
						form.unitSelect.value = '';
						form.resizeValue.value = '';
						form.rotateSelect.value = '';
					} else {
						form.unitSelect.value = fileobj.img.editOptions.scaleUnit;
						form.resizeValue.value = fileobj.img.editOptions.scaleValue;
						form.rotateSelect.value = fileobj.img.editOptions.rotateValue;
					}
				}
			};

			
			this.formCustomOptsReset = function (form) { // USED
				form.unitSelect.value = 'pixel_w';
				form.resizeValue.value = '';
				form.rotateSelect.value = '0';
				form.quality.value = '0';
				qualityOutput.value = '0';
			};

			/*
			this.syncCustomEditOpts = function (element){
				return;
				var optName = element.name,
					optValue = element.value;

				for(var i = 0; i < _.sender.preparedFiles.length; i++){
					if(_.sender.preparedFiles[i]){
						var form = document.getElementById('form_editOpts_' + i);

						if(form.doEdit.checked && form.editOpts.value === 'general'){
							switch(optName){
								case 'fu_doc_unitSelect':
									form.unitSelect.value = optValue;
									break;
								case 'fu_doc_resizeValue':
									form.resizeValue.value = optValue;
									break;
								case 'fu_doc_rotate':
									form.rotateSelect.value = optValue;
									break;
							}
						}
					}
				}
			};
			*/

			this.addTextCutLeft = function(elem, text, maxwidth){
				if(!elem){
					return;
				}

				maxwidth = maxwidth || 30;
				text = text ? text : '';
				var i = 200;
				elem.innerHTML = text;
				while(elem.offsetWidth > maxwidth && i > 0){
					text = text.substr(4);
					elem.innerHTML = '...' + text;
					--i;
				}
				return;
			};
		}

		function Utils() {
			this.setImageEditOptionsGeneral = function () {
				_.utils.abstractSetImageEditOptionsGeneral('filechooser');
			};

			this.setImageEditOptionsFile = function (fileobj) {
				var form,
					type = 'general';

				if((form = document.getElementById('form_editOpts_' + fileobj.index))){
										if(form.useGeneralOpts.checked === false){
						type = form.editOpts.value;
					}
				}

				switch(type){
					case 'general':
						fileobj.img.editOptions = JSON.parse(JSON.stringify(_.sender.imageEditOptions));
						break;
					case 'custom':
						fileobj.img.editOptions.scaleUnit = form.unitSelect.value;
						fileobj.img.editOptions.scaleValue = form.resizeValue.value;
						fileobj.img.editOptions.rotateValue = form.rotateSelect.value;
						fileobj.img.editOptions.quality = form.quality ? form.quality.value : 90;
						fileobj.img.editOptions.doEdit = fileobj.img.editOptions.scaleValue == '' && fileobj.img.editOptions.rotateValue == 0 && fileobj.img.editOptions.quality == 0 ? false : true;
						break;
					case 'expert': 
						fileobj.img.editOptions = {};
						fileobj.img.editOptions.scaleUnit = '';
						fileobj.img.editOptions.scaleValue = 0;
						fileobj.img.editOptions.rotateValue = 0;
						fileobj.img.editOptions.quality = 90;
						fileobj.img.editOptions.doEdit = false;
				}
				fileobj.img.editOptions.from = type;
			};
		}
	}

	function weFileUpload_binDoc() { // TODO: look for image edit params just onbefore processing files!
		(function () {
			weFileUpload_abstract.call(this);

			Controller.prototype = this.getAbstractController();
			Sender.prototype = this.getAbstractSender();
			View.prototype = this.getAbstractView();
			Utils.prototype = this.getAbstractUtils();

			_.fileuploadType = 'binDoc';
			_.self = this;
			_.controller = new Controller();
			_.sender = new Sender();
			_.view = new View();
			_.utils = new Utils();
		})();

		this.init = function (conf) {
			_.init_abstract(conf);
			_.sender.form.action = conf.form.action || _.sender.form.action;
			_.view.uploadBtnName = conf.uploadBtnName || _.view.uploadBtnName;
			_.fieldName = 'we_File';
			if (typeof conf.binDocProperties !== 'undefined') {
				_.view.icon = top.WE().util.getTreeIcon(conf.binDocProperties.ct);
				_.view.binDocType = conf.binDocProperties.type || _.view.binDocType;
			} else {
				_.view.icon = top.WE().util.getTreeIcon('text/plain');
			}
		};

		_.onload = function (scope) {
			var that = scope,
							v = _.view,
							i;
			_.onload_abstract(that);

			for (i = 0; i < document.forms.length; i++) {
				document.forms[i].addEventListener('submit', _.controller.formHandler, false);
			}
			var inputs = document.getElementsByTagName('input');
			for (i = 0; i < inputs.length; i++) {
				if (inputs[i].type === 'file') {
					inputs[i].addEventListener('change', _.controller.fileSelectHandler, false);
				}
			}

			document.we_form.elements['fu_doc_resizeValue'].addEventListener('keyup', function(e){_.controller.editOptionsOnChange(e.target);}, false);
			document.we_form.elements['fu_doc_rotate'].addEventListener('change', function(e){_.controller.editOptionsOnChange(e.target);}, false);
			document.we_form.elements['fu_doc_quality'].addEventListener('change', function(e){_.controller.editOptionsOnChange(e.target);}, false);

			v.elems.fileDrag_state_0 = document.getElementById('div_fileupload_fileDrag_state_0');
			v.elems.fileDrag_state_1 = document.getElementById('div_fileupload_fileDrag_state_1');
			v.elems.fileDrag_mask = document.getElementById('div_' + _.fieldName + '_fileDrag');
			v.elems.dragInnerRight = document.getElementById('div_upload_fileDrag_innerRight');
			v.elems.divRight = document.getElementById('div_fileupload_right');
			v.elems.txtFilename = document.getElementById('span_fileDrag_inner_filename');
			v.elems.txtFilename_1 = document.getElementById('span_fileDrag_inner_filename_1');//??
			v.elems.txtSize = document.getElementById('span_fileDrag_inner_size');
			v.elems.txtType = document.getElementById('span_fileDrag_inner_type');
			v.elems.txtEdit= document.getElementById('span_fileDrag_inner_edit');
			v.elems.divBtnReset = document.getElementById('div_fileupload_btnReset');
			v.elems.divBtnCancel = document.getElementById('div_fileupload_btnCancel');
			v.elems.divBtnUpload = document.getElementById('div_fileupload_btnUpload');
			v.elems.divProgressBar = document.getElementById('div_fileupload_progressBar');
			v.elems.divButtons = document.getElementById('div_fileupload_buttons');

			var ids = [
				'div_we_File_fileDrag',
				/*
				'div_fileupload_fileDrag_state_0',
				'div_filedrag_content_left',
				'div_filedrag_content_right',
				'div_fileupload_fileDrag_state_1',
				'div_upload_fileDrag_innerLeft',
				'span_fileDrag_inner_filename',
				'span_fileDrag_inner_size',
				'span_fileDrag_inner_type',
				'span_fileDrag_inner_edit',
				'div_upload_fileDrag_innerRight'
				*/
			];
			for(var i = 0; i < ids.length; i++){
				document.getElementById(ids[i]).addEventListener('dragover', _.controller.fileDragHover, false);
				document.getElementById(ids[i]).addEventListener('dragleave', _.controller.fileDragHover, false);
				document.getElementById(ids[i]).addEventListener('drop', _.controller.fileSelectHandler, false);
			}


			v.spinner = document.createElement("i");
			v.spinner.className = "fa fa-2x fa-spinner fa-pulse";

			_.controller.checkIsPresetFiles();
		};

		function Controller() {
			this.elemFileDragClasses = 'we_file_drag';
			this.doSubmit = false;

			this.fileDragHover = function (e) {
				e.preventDefault();
				_.view.elems.fileDrag.className = (e.type === 'dragover' ? _.controller.elemFileDragClasses + ' we_file_drag_hover' : _.controller.elemFileDragClasses);
			};

			this.setEditorIsHot = function () {
				if (_.uiType !== 'wedoc') {
					WE().layout.weEditorFrameController.setEditorIsHot(true, WE().layout.weEditorFrameController.ActiveEditorFrameId);
				}
			};
		}

		function Sender() {
			this.totalWeight = 0;
			this.callback = null;
			this.dialogCallback = null;

			this.doOnFileFinished = function (resp) {
			};

			this.postProcess = function (resp) {
				_.sender.preparedFiles = [];
				if (_.uiType !== 'wedoc') {
					var cur = this.currentFile;
					if (resp.status === 'failure') {
						_.sender.resetParams();
					} else {
						this.form.form.elements.weFileNameTemp.value = cur.fileNameTemp;
						this.form.form.elements.weFileCt.value = cur.mimePHP;
						this.form.form.elements.weFileName.value = cur.file.name;
						_.sender.currentFile = null;
						//setTimeout(_.sender.dialogCallback, 100, resp); // FIXME: check if this works
						setTimeout(function () {
							_.sender.dialogCallback(resp);
						}, 100);
					}
				} else if (resp.status === 'success') {
					_.sender.currentFile = null;
					if (WE(true)) {
						window.we_cmd('update_file');
						WE().layout.we_setPath(null, resp.weDoc.path, resp.weDoc.text, 0, "published");
					}

					this.fireCallback();

				}

			};

			this.processError = function (arg) {
				switch (arg.from) {
					case 'gui' :
						top.we_showMessage(arg.msg, 4, window);
						return;
					case 'request' :
						_.view.repaintGUI({what: 'fileNOK'});
						_.view.repaintGUI({what: 'resetGui'});
						return;
					default :
						return;
				}
			};

			this.resetParams = function () {
				this.preparedFiles = [];
				this.totalWeight = 0;
				this.isCancelled = false;
				_.view.repaintGUI({what: 'resetGui'});
			};

			this.prepareUpload = function () {
				if (this.preparedFiles.length < 1) {
					return false;
				}

				if (typeof this.preparedFiles[0] === 'object' && this.preparedFiles[0] !== null && this.preparedFiles[0].isUploadable) {
					this.preparedFiles[0].fileNum = 0;
					this.uploadFiles.push(this.preparedFiles[0]);
					this.totalWeight = this.preparedFiles[0].size;//size?
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
				var c = _.sender.callback;
				_.view.repaintGUI({what: 'resetGui'});
				this.fireCallback(c);
			};

			this.fireCallback = function (c) {
				var cb = c || _.sender.callback;

				if (cb) {
					_.sender.callback = null;
					cb();
				}
			};
		}

		function View() {
			this.uploadBtnName = '';
			this.icon = '';
			this.binDocType = 'other';
			this.previewSize = 116;
			this.useOriginalAsPreviewIfNotEdited = true;
			this.preview = null;
			this.STATE_RESET = 0;
			this.STATE_PREVIEW_OK = 1;
			this.STATE_PREVIEW_NOK = 2;
			this.STATE_UPLOAD = 3;

			this.addFile = function (f) {
				var sizeText = f.isSizeOk ? _.utils.gl.sizeTextOk + _.utils.computeSize(f.size) + ', ' :
						'<span style="color:red;">' + _.utils.gl.sizeTextNok + '</span>';
				var typeText = f.isTypeOk ? _.utils.gl.typeTextOk + (f.isTypeOk === 1 ? f.type : f.file.name.split('.').pop().toUpperCase()) :
						'<span style="color:red;">' + _.utils.gl.typeTextNok + f.type + '</span>';

				_.view.elems.fileDrag.style.backgroundColor = f.isEdited ? 'rgb(216, 255, 216)' : 'rgb(232, 232, 255)';

				var fn = f.file.name;
				var fe = '';
				if (fn.length > 27) {
					var farr = fn.split('.');
					fe = farr.pop();
					fn = farr.join('.');
					fn = fn.substr(0, 18) + '...' + fn.substring((fn.length - 2), fn.length) + '.';
				}

				this.elems.txtFilename.innerHTML = fn + fe;
				//this.elems.fileDrag_mask.title = f.file.name;
				this.elems.txtSize.innerHTML = sizeText;
				this.elems.txtType.innerHTML = typeText;
				if(f.isEdited){
					var edittext;
					switch(f.img.editOptions.scaleUnit){
						case 'pixel_w':
							edittext = f.img.editOptions.scaleValue + ' Prozent';
							break;
						case 'pixel_l':
							edittext = 'Längere Seite ' + f.img.editOptions.scaleValue + ' px';
							break;
						case 'pixel_h':
							edittext = 'Höhe ' + f.img.editOptions.scaleValue + ' px';
					}

					this.elems.txtEdit.innerHTML = '<strong>Skaliert</strong> auf ' + edittext;
					this.elems.txtEdit.style.display = 'block';
				} else {
					this.elems.txtEdit.style.display = 'none';
				}
				this.setDisplay('fileDrag_state_0', 'none');
				this.setDisplay('fileDrag_state_1', 'block');
				this.elems.dragInnerRight.innerHTML = '';

				if (f.uploadConditionsOk) {
					_.sender.isAutostartPermitted = true;
					_.controller.setEditorIsHot();
				} else {
					_.sender.isAutostartPermitted = false;
				}

				if (f.type.search("image/") !== -1){
					if(f.img.previewImg){
						_.view.preview = f.img.previewImg;
						_.view.elems.dragInnerRight.innerHTML = '';
						_.view.elems.dragInnerRight.appendChild(_.view.preview);
					} else if(f.img.previewCanvas){
						_.view.preview = f.img.previewCanvas;
						_.view.elems.dragInnerRight.innerHTML = '';
						_.view.elems.dragInnerRight.appendChild(_.view.preview);
					}
					this.setGuiState(f.uploadConditionsOk ? this.STATE_PREVIEW_OK : this.STATE_PREVIEW_NOK);
				} else {
					if (f.uploadConditionsOk) {
						this.elems.dragInnerRight.innerHTML = '<div class="largeicons" style="margin:24px 0 0 26px;height:62px;width:54px;">' + this.icon + '</div>';
						this.setGuiState(this.STATE_PREVIEW_OK);
					} else {
						this.elems.dragInnerRight.innerHTML = '<div class="bold" style="margin:18px 0 0 30px;height:62px;width:54px;border:dotted 1px gray;padding-top:14px;text-align:center;background-color:#f9f9f9;color:#ddd;font-size:32px;">!?</div>';
						this.setGuiState(this.STATE_PREVIEW_NOK);
					}
				}
			};

			this.setGuiState = function (state) {
				switch (state) {
					case this.STATE_RESET:
						this.setDisplay('fileDrag_state_0', 'block');
						this.setDisplay('fileDrag_state_1', 'none');
						this.elems.fileDrag.style.backgroundColor = 'transparent';
						this.setDisplay('fileInputWrapper', 'block');
						if (this.isDragAndDrop && this.elems.fileDrag) {
							this.setDisplay('fileDrag', 'block');
						}
						this.setDisplay('divBtnReset', 'none');
						this.setDisplay('divBtnUpload', '');
						this.setDisplay('divProgressBar', 'none');
						this.setDisplay('divBtnCancel', 'none');
						this.setDisplay('dragInnerRight', '');
						_.controller.setWeButtonState(_.view.uploadBtnName, false);
						_.controller.setWeButtonState('browse_harddisk_btn', true);
						return;
					case this.STATE_PREVIEW_OK:
						this.setDisplay('fileInputWrapper', 'none');
						this.setDisplay('divBtnReset', '');
						_.controller.setWeButtonState('reset_btn', true);
						_.controller.setWeButtonState(_.view.uploadBtnName, true);
						return;
					case this.STATE_PREVIEW_NOK:
						this.setDisplay('fileInputWrapper', 'none');
						this.setDisplay('divBtnReset', '');
						_.controller.setWeButtonState('reset_btn', true);
						_.controller.setWeButtonState(_.view.uploadBtnName, false);
						return;
					case this.STATE_UPLOAD:
						_.controller.setWeButtonState(_.view.uploadBtnName, false);
						_.controller.setWeButtonState('reset_btn', false);
						this.setDisplay('fileInputWrapper', 'none');
						if (_.uiType !== 'wedoc') {
							this.setDisplay('divBtnReset', 'none');
						}
						this.setDisplay('divBtnUpload', 'none');
						this.setDisplay('divBtnReset', 'none');
						this.setDisplay('divProgressBar', '');
						this.setDisplay('divBtnCancel', '');
						if (this.preview) {
							this.preview.style.opacity = 0.05;
						}
						_.controller.setWeButtonState('browse_harddisk_btn', false);
				}
			};

			this.repaintGUI = function (arg) {
				var cur = _.sender.currentFile,
					fileProg = 0,
					digits = 0,
					opacity = 0;

				switch (arg.what) {
					case 'chunkOK' :
						digits = cur.totalParts > 1000 ? 2 : (cur.totalParts > 100 ? 1 : 0);//FIXME: make fn on UtilsAbstract
						fileProg = (100 / cur.size) * cur.currentWeightFile;
						this.setInternalProgress(fileProg.toFixed(digits));
						opacity = fileProg / 100;
						if (this.preview) {
							this.preview.style.opacity = opacity.toFixed(2);
						}
						return;
					case 'fileOK' :
						_.sender.preparedFiles = [];
						if (this.preview) {
							this.preview.style.opacity = 1;
						}
						return;
					case 'startSendFile' :
						this.setInternalProgress(0);
						this.setGuiState(this.STATE_UPLOAD);
						return;
					case 'chunkNOK' :
						_.sender.processError({from: 'gui', msg: arg.message});
						/* falls through */
					case 'initGui' :
					case 'fileNOK' :
					case 'cancelUpload' :
					case 'resetGui' :
						/* falls through */
					default:
						_.sender.preparedFiles = [];
						_.sender.currentFile = -1;
						_.sender.callback = null;
						this.setInternalProgress(0);
						this.setGuiState(this.STATE_RESET);
						return;
				}
			};

			this.writeFocusToForm = function(fileobj){
				document.we_form.elements['fu_doc_focusX'].value = fileobj.img.focusX;
				document.we_form.elements['fu_doc_focusY'].value = fileobj.img.focusY;
			};

			//TODO: use progress fns from abstract after adapting them to standard progress
			this.setInternalProgress = function (progress, index) {
				var coef = this.intProgress.width / 100,
					mt = typeof _.sender.currentFile === 'object' ? ' / ' + _.utils.computeSize(_.sender.currentFile.size) : '';

				document.getElementById('progress_image_fileupload').style.width = coef * progress + "px";
				document.getElementById('progress_image_bg_fileupload').style.width = (coef * 100) - (coef * progress) + "px";
				document.getElementById('progress_text_fileupload').innerHTML = progress + '%' + mt;
			};

			this.setDisplay = function (elem, val) {
				if (this.elems[elem]) {
					this.elems[elem].style.display = val;
				}
			};
			
			this.setImageEditMessage = function () {
				var mask = document.getElementById('div_fileupload_fileDrag_mask');
				mask.style.display = 'block';
				mask.innerHTML = '<div style="margin:20px 0 10px 0;"><span style="font-size:2em;"><i class="fa fa-2x fa-spinner fa-pulse"></i></span></div><div style="font-size:1.6em;" id="image_edit_mask_text">Die Grafik wird bearbeitet</div>';
			};

			this.unsetImageEditMessage = function () {
				var mask = document.getElementById('div_fileupload_fileDrag_mask');
				mask.style.display = 'none';
			};

			this.repaintImageEditMessage = function () {
				var text = document.getElementById('image_edit_mask_text').innerHTML;
				text += '.';
				document.getElementById('image_edit_mask_text').innerHTML = text;
				
			};

			this.repaintEntry = function (fileobj) {
				this.addFile(fileobj);
				this.elems.dragInnerRight.firstChild.addEventListener('mouseenter', function(){_.view.setPreviewLoupe(fileobj);}, false);
				this.elems.dragInnerRight.firstChild.addEventListener('mousemove', function(e){_.view.movePreviewLoupe(e, fileobj);}, false);
				this.elems.dragInnerRight.firstChild.addEventListener('mouseleave', function(){_.view.unsetPreviewLoupe(fileobj);}, false);
				this.elems.dragInnerRight.firstChild.addEventListener('click', function(e){_.view.grabFocusPoint(e,fileobj);}, false);
			};
		}

		function Utils() {
			this.setImageEditOptionsFile = function (fileobj) {
				var type = 'general';

				switch(type){
					case 'general':
						fileobj.img.editOptions = JSON.parse(JSON.stringify(_.sender.imageEditOptions));
						break;
					case 'expert':
						// not implemented yet
						break;
				}
				fileobj.img.editOptions.from = type;
			};
		}

		this.doUploadIfReady = function (callback) {
			if (_.sender.isAutostartPermitted && _.sender.preparedFiles.length > 0 && _.sender.preparedFiles[0].uploadConditionsOk) {
				_.sender.callback = callback;
				_.sender.isAutostartPermitted = false;
				this.startUpload();
			} else {
				//there may be a file in preview with uploadConditions nok!
				_.view.repaintGUI({what: 'resetGui'});
				callback();
			}
		};
	}

	return Fabric;
}());