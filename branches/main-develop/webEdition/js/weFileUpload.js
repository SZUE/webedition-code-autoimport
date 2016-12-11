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
/* move to init js
(function (win) {
	win.we_FileUpload_addListeners = false;
	win.addEventListener('load', function () {
		win.we_FileUpload_addListeners = true;
	}, false);
})(_.window);
*/
WE().util.loadConsts(document, 'g_l.fileupload');

WE().layout.weFileUpload = (function () {
	var _ = {};

	function Fabric(type, win) {
		_.window = win;
		_.document = win.document;

		/* we now use several instances of this one weFileUpload object!
		if (_.self) {
			singleton: but we do not return the object, when constructor is called more than once!
			return false;
		}
		*/
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
				var ret = new weFileUpload_binDoc();
				return ret;
		}
	}

	function weFileUpload_abstract() {
		//declare "protected" members: they are accessible from weFileUpload_include/importer too!
		_.fieldName = '';
		_.genericFilename = '';
		_.fileuploadType = 'abstract';
		_.uiType = 'base';

		_.debug = false;

		_.EDIT_IMAGES_CLIENTSIDE = false;
		_.picaOptions = {
			quality: 3, // [0,3]
			unsharpAmount: 0, // [0, 200]
			unsharpRadius: 0.5, // [0.5, 2]
			unsharpThreshold: 0, // [0, 255]
			alpha: false
		};

		_.init_abstract = function (conf) {
			var that = _.self, c = _.controller, s = _.sender, v = _.view, u = _.utils;

			if(!_.onload(that)){
				_.window.addEventListener('load', function (e) {
					_.onload(that);
				}, true);
			}

			//initialize private var outer to be used inside modules instead of unaccessible this
			c.outer = s.outer = v.outer = u.outer = that;

			//initialize properties only when conf is defined: dispatch them to modules
			if (typeof conf !== 'undefined') {
				_.fieldName = conf.fieldName || _.fieldName;
				_.uiType = conf.uiType || _.uiType;
				_.genericFilename = conf.genericFilename || _.genericFilename;
				_.EDIT_IMAGES_CLIENTSIDE = conf.clientsideImageEditing ? true : false;

				c.isPreset = conf.isPreset || c.isPreset;
				c.cmdFileselectOnclick = conf.cmdFileselectOnclick;

				s.typeCondition = conf.typeCondition || s.typeCondition;
				s.doCommitFile = conf.doCommitFile !== undefined ? conf.doCommitFile : s.doCommitFile;
				s.chunkSize = typeof conf.chunkSize !== 'undefined' ? (conf.chunkSize * 1024) : s.chunkSize;
				s.callback = conf.callback || s.callback;
				s.nextCmd = conf.nextCmd || s.nextCmd;
				s.responseClass = conf.responseClass || s.responseClass;
				s.dialogCallback = conf.callback || s.dialogCallback;
				s.maxUploadSize = typeof conf.maxUploadSize !== 'undefined' ? conf.maxUploadSize : s.maxUploadSize;
				if (typeof conf.form !== 'undefined') {
					s.form.name = conf.form.name || s.form.name;
					s.form.action = conf.form.action || s.form.action;
				}
				s.moreFieldsToAppend = conf.moreFieldsToAppend || [];

				u.gl = WE().consts.g_l.fileupload; // FIXME: call translations on WE().consts.g_l.fileupload directly

				v.isDragAndDrop = typeof conf.isDragAndDrop !== 'undefined' ? conf.isDragAndDrop : v.isDragAndDrop;
				v.footerName = conf.footerName || v.footerName;
				if (typeof conf.intProgress === 'object') {
					v.intProgress.isIntProgress = conf.intProgress.isInternalProgress || v.intProgress.isIntProgress;
					v.intProgress.width = conf.intProgress.width || v.intProgress.width;
				}
				if (typeof conf.extProgress === 'object') {
					v.extProgress.isExtProgress = conf.extProgress.isExternalProgress || v.extProgress.isExtProgress;
					v.extProgress.parentElemId = conf.extProgress.parentElemId || v.extProgress.parentElemId;
					v.extProgress.name = conf.extProgress.name;
				}
			}
		};

		_.onload_abstract = function (scope) {
			if(!_.document.getElementById(_.fieldName)){
				return false;
			}

			var that = scope, s = _.sender, v = _.view;

			s.form.form = s.form.name ? _.document.forms[s.form.name] : _.document.forms[0];
			s.form.action = s.form.action ? s.form.action : (s.form.form.action ? s.form.form.action : _.window.location.href);

			//set references to some elements
			v.elems.fileSelect = _.document.getElementById(_.fieldName);
			v.elems.fileDrag = _.document.getElementById('div_' + _.fieldName + '_fileDrag');//FIXME: change to div_fileDrag
			v.elems.fileInputWrapper = _.document.getElementById('div_' + _.fieldName + '_fileInputWrapper');//FIXME: change to div_fileInputWrapper

			v.elems.footer = v.footerName ? _.window.parent.frames[v.footerName] : _.window;
			v.elems.extProgressDiv = v.elems.footer.document.getElementById(v.extProgress.parentElemId);
			v.extProgress.isExtProgress = !v.elems.extProgressDiv ? false : v.extProgress.isExtProgress;

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

			return true;
		};

		function AbstractController() {
			this.elemFileDragClasses = 'we_file_drag';
			this.outer = null;
			this.isPreset = false;
			this.cmdFileselectOnclick = '';

			this.IMG_NEXT = 0;
			this.IMG_START = 10;
			this.IMG_LOAD_CANVAS = 1;
			this.IMG_EXTRACT_METADATA = 2;
			this.IMG_SCALE = 3;
			this.IMG_ROTATE = 4;
			this.IMG_APPLY_FILTERS = 5;
			this.IMG_WRITE_IMAGE = 6;
			this.IMG_INSERT_METADATA = 7;
			this.IMG_MAKE_PREVIEW = 8;
			this.IMG_POSTPROCESS = 9;

			this.OPTS_QUALITY_NEUTRAL_VAL = 100;
			this.OPTS_QUALITY_DEFAULT_VAL = 90;
			this.PRESERVE_IMG_DATAURL = true;
			this.EDITABLE_CONTENTTYPES = ['image/jpeg', 'image/gif', 'image/png'];

			this.IS_MEMORY_MANAGMENT = false;
			this.PROCESS_PREVIEWS_ONLY = false;
			//this.MEMORY_LIMIT = 31457280;
			this.MEMORY_LIMIT = 83886080; // 80 MB
			this.memoryManagement = {
				registeredSum: 0,
				registeredValues: {},
				queueEdited: [],
				queueNotEdited: []
			};

			this.fileselectOnclick = function () {
				if(_.controller.cmdFileselectOnclick){
					var tmp = _.controller.cmdFileselectOnclick.split(',');
					if(_.window.we_cmd){
						_.window.we_cmd.apply(_.window, tmp);
					} else { // FIXME: make sure have a function we_cmd on every opener!
						_.window.top.we_cmd.apply(_.window, tmp);
					}
				} else {
					return;
				}
			};

			this.checkIsPresetFiles = function () {
				if (_.controller.isPreset && WE().layout.weEditorFrameController.getVisibleEditorFrame().document.presetFileupload) {
					_.controller.fileSelectHandler(null, true, WE().layout.weEditorFrameController.getVisibleEditorFrame().document.presetFileupload);
				} else if (_.controller.isPreset && _.window.opener.document.presetFileupload) {
					_.controller.fileSelectHandler(null, true, _.window.opener.document.presetFileupload);
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

					if (_.EDIT_IMAGES_CLIENTSIDE && _.sender.imageFilesToProcess.length) {
						_.controller.processImages();
					}

					//IMI: REREGISTER MEMORY
				}
			};

			this.prepareFile = function (f, isUploadable) {
				var fileObj = {
					file: f,
					fileNum: 0,
					dataArray: null,
					dataUrl: null,
					isEdited: false,
					img: {
						processedOptions: {
							doEdit: false,
							from: 'general',
							scaleWhat: 'pixel_l',
							scale: 0,
							rotate: 0,
							quality: 100
						},
						editOptions: {
							doEdit: false,
							from: 'general',
							scaleWhat: 'pixel_l',
							scale: 0,
							rotate: 0,
							quality: 100
						},
						origWidth: 0,
						origHeight: 0,
						tooSmallToScale: false,
						isUploadStarted: false,
						isOrientationChecked: false,
						orientationValue: 0,
						workingCanvas: null, // maybe call it workingcanvas
						previewCanvas: null,
						actualRotation: 0,
						previewRotation: 0,
						previewImg: null, // created only for edited images: unedited images can be very large...
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
				fileObj.img.originalSize = f.size;

				if (_.EDIT_IMAGES_CLIENTSIDE && _.controller.EDITABLE_CONTENTTYPES.indexOf(f.type) !== -1) {
					_.sender.imageFilesToProcess.push(fileObj);
				}

				return fileObj;
			};

			this.processImages = function() {
				_.controller.PROCESS_PREVIEWS_ONLY = false;
				if (_.sender.imageFilesToProcess && _.sender.imageFilesToProcess.length) {
					_.utils.setImageEditOptionsGeneral();
					_.view.setImageEditMessage();
					_.controller.processNextImage();
				} else {
					_.view.unsetImageEditMessage();
				}
			};

			this.processNextImage = function() {
				if (_.sender.imageFilesToProcess.length) {
					var fileobj = _.sender.imageFilesToProcess.shift();
					_.utils.logTimeFromStart('start edit image', true, fileobj);
					_.utils.setImageEditOptionsFile(fileobj);
					_.controller.processImage(fileobj, this.IMG_START);
				} else {
					_.controller.processImages();
				}
			};

			this.processSingleImage = function(fileobj, finishProcess){
				_.controller.PROCESS_PREVIEWS_ONLY = false;
				if(finishProcess){
					fileobj.processSingleImage = false;
					_.view.unsetImageEditMessage(true, fileobj.preparedFilesIndex);//
					if (fileobj.img.callback === 'sendNextFile' && _.sender.prepareUpload(true)) {
						setTimeout(function () {
							_.sender.sendNextFile();
						}, 100);
					}
					return;
				}

				if(fileobj){
					fileobj.processSingleImage = true;
					_.view.setImageEditMessage(true, fileobj.preparedFilesIndex);
					_.utils.logTimeFromStart('start edit image', true);
					_.utils.setImageEditOptionsFile(fileobj);
					_.controller.processImage(fileobj, this.IMG_START);
				}
			};

			this.processImage = function(fileobj, task) {
				if(!fileobj){
					_.controller.processNextImage();
					return;
				}

				switch(task) {
					case _.controller.IMG_START:
						if(_.controller.PROCESS_PREVIEWS_ONLY && fileobj.img.previewCanvas){
							_.controller.processImage(fileobj, _.controller.IMG_NEXT);
							return;
						}
						_.utils.processimageExtractLandscape(fileobj, _.controller.IMG_LOAD_CANVAS);
						break;
					case _.controller.IMG_LOAD_CANVAS: // TODO: make IMG_START
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
					/*falls through*/
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
					top.WE().layout.button[enable ? 'enable' : 'disable'](_.document, btn);
					if (btn === 'browse_harddisk_btn') {
						top.WE().layout.button[enable ? 'enable' : 'disable'](_.document, 'browse_btn');
					}
				}
			};

			/*
			 * reedit a single image or all images using general options applying opts from GUI
			 *
			 */
			this.reeditImage = function (index, general) {
				var indexes = _.utils.getImageEditIndexes(index, general, false);

				for(var i = 0; i < indexes.length; i++){
					_.sender.imageFilesToProcess.push(_.sender.preparedFiles[indexes[i]]);
				}
				_.controller.processImages();
			};

			/*
			 * set property isEdited of a single image or all images using general options to false
			 * -> empty props like dataArray, dataUrl
			 * -> do not change GUI (= edit opts)
			 *
			 * we use this to free disk space when edited images are not valid anymore after changing options
			 *
			 */
			this.uneditImage = function (index, general, isReset) {
				var indexes = _.utils.getImageEditIndexes(index, general, false),
					fileobj;

				for(var i = 0; i < indexes.length; i++){
					fileobj = _.sender.preparedFiles[indexes[i]];
					fileobj.dataArray = null;
					fileobj.dataUrl = null;
					fileobj.size = fileobj.img.originalSize;
					fileobj.img.previewImg = null;
					fileobj.img.fullPrev = null;
					fileobj.img.actualRotation = 0;
					_.utils.setImageEditOptionsFile(fileobj); // write correct actually valid editoptions
					fileobj.img.processedOptions = { // reset last edited options
						doEdit: false,
						from: 'general',
						scaleWhat: 'pixel_l',
						scale: 0,
						rotate: 0,
						quality: 100
					};
					fileobj.isEdited = false;
					_.utils.memorymanagerRegister(fileobj);
				}

				return;
			};

			this.openImageEditor = function(pos){
				// to be overridden
			};

			this.editImageButtonOnClick = function(btn, index, general){
				btn.disabled = true;

				if(!btn.form){ // FIXME: this is a dirty fix: why can we have a button without form?
					return;
				}

				_.controller.reeditImage(index, general);
			};

			this.editOptionsOnChange = function(target){
				var form = target.form,
								inputScale = form.elements.fuOpts_scale,
								inputRotate = form.elements.fuOpts_rotate,
								inputQuality = form.elements.fuOpts_quality,
					scale = inputScale.value,
					rotate = parseInt(inputRotate.value),
					quality = parseInt(inputQuality.value),
					pos = form.getAttribute('data-type') === 'importer_rowForm' ? form.getAttribute('data-index') : -1,
					//opttype = pos === -1 ? 'general' : 'custom';
					btnRefresh = form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];

				switch (target.name){
					case 'fuOpts_scaleProps':
						inputScale.value = target.value;
						scale = target.value;
						inputScale.focus();
						target.value = 0;
						/* falls through*/
					case 'fuOpts_scale':
					case 'fuOpts_rotate':
						if(scale || rotate){
							if(quality === _.controller.OPTS_QUALITY_NEUTRAL_VAL){
								inputQuality.value = _.controller.OPTS_QUALITY_DEFAULT_VAL;
								form.getElementsByClassName('qualityValueContainer')[0].innerHTML = _.controller.OPTS_QUALITY_DEFAULT_VAL;
							}
							_.view.formCustomEditOptsSync(-1, true);
							_.controller.uneditImage(pos, pos === -1 ? true : false);
							_.view.setEditStatus('', pos, pos === -1 ? true : false);
						} else {
							inputQuality.value = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
							form.getElementsByClassName('qualityValueContainer')[0].innerHTML = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
							_.view.formCustomEditOptsSync(-1, true);
							_.controller.uneditImage(pos, pos === -1 ? true : false);
							_.view.setEditStatus('', pos, pos === -1 ? true : false);
						}
						btnRefresh.disabled = _.sender.preparedFiles.length === 0 || (!scale && !rotate);
						if(target.name === 'fuOpts_rotate'){
							_.view.previewSyncRotation(pos, rotate);
						}
						break;
					case 'fuOpts_scaleWhat':
						if(scale){
							btnRefresh.disabled = true;
							_.view.formCustomEditOptsSync(-1, true);
							_.controller.uneditImage(pos, pos === -1 ? true : false);
							_.view.setEditStatus('notprocessed', pos, pos === -1 ? true : false);
						}
						break;
					case 'fuOpts_quality':
						form.getElementsByClassName('qualityValueContainer')[0].innerHTML = quality;
						if((quality === _.controller.OPTS_QUALITY_NEUTRAL_VAL) && !scale && !rotate){
							//btnRefresh.disabled = true;
							_.view.formCustomEditOptsSync(-1, true);
							_.controller.uneditImage(pos, pos === -1 ? true : false);
							_.view.setEditStatus('', pos, pos === -1 ? true : false);
						} else {
							_.view.formCustomEditOptsSync(-1, true);
							_.controller.uneditImage(pos, pos === -1 ? true : false);
							btnRefresh.disabled = _.sender.preparedFiles.length === 0;
							_.utils.setImageEditOptionsFile(pos === -1 ? null : _.sender.preparedFiles[pos], pos === -1 ? true : false);
							_.view.setEditStatus('', pos, pos === -1 ? true : false);
						}
						break;
					case 'check_fuOpts_doEdit':
						// whenever we change this optoion we reset all vals and disable button
						inputQuality.value = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
						form.getElementsByClassName('qualityValueContainer')[0].innerHTML = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
						inputRotate.value = 0;
						inputScale.value = '';
						btnRefresh.disabled = true;
						_.controller.uneditImage(-1, true);
						_.controller.reeditImage(-1, true);
						_.view.previewSyncRotation(-1, 0);
						_.view.setEditStatus('donotedit', -1, true);
						_.view.formCustomEditOptsSync(-1, true);
						break;
					case 'fuOpts_useCustomOpts':
						var fileobj = _.sender.preparedFiles[pos];
						if(target.checked){
							fileobj.img.editOptions.from = 'custom';
							_.controller.uneditImage(pos, false);
							_.view.formCustomEditOptsDisable(form, false);
							_.view.formEditOptsReset(form);
							_.view.previewSyncRotation(pos, 0);
							_.utils.setImageEditOptionsFile(fileobj);
							_.view.setEditStatus('', pos, false);
							btnRefresh.disabled = false;
						}else {
							fileobj.img.editOptions.from = 'general';
							_.controller.uneditImage(pos, false);
							_.view.formCustomEditOptsDisable(form, true);
							_.view.formCustomEditOptsSync(pos, false);
							_.utils.setImageEditOptionsFile(fileobj);
							_.view.previewSyncRotation(pos, fileobj.img.editOptions.rotate);
							_.view.setEditStatus('', pos, false);
							btnRefresh.disabled = false;
						}
						break;
				}
			};

			this.editOptionsHelp = function(target, dir){
				if(dir === 'enter'){
					var fileobj = _.sender.preparedFiles[target.getAttribute('data-index')];
					var scaleReference = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? fileobj.img.origWidth : (
							fileobj.img.editOptions.scaleWhat === 'pixel_h' ? fileobj.img.origHeight : Math.max(fileobj.img.origHeight, fileobj.img.origWidth));
					var text = _.utils.gl.editTargetsizeTooLarge;

					target.lastChild.innerHTML = text.replace('##ORIGSIZE##', scaleReference);
					target.lastChild.style.display = 'block';
				} else {
					target.lastChild.style.display = 'none';
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
				_.document.forms[0].submit();
			};
			this.nextCmd = '';
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
				scaleWhat: 'pixel_l',
				scale: 0,
				rotate: 0,
				quality: 100
			};
			this.moreFieldsToAppend = [];

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
				var cur, fr = null, cnt,
					that = _.sender, //IMPORTANT: if we use that = this, then that is of type AbstractSender not knowing members of Sender!
					editOptsLast, editOpts;

				if (this.uploadFiles.length > 0) {
					cur = this.uploadFiles[0];

					if (_.EDIT_IMAGES_CLIENTSIDE && _.controller.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1){
						editOptsLast = cur.img.processedOptions;//JSON.parse(JSON.stringify(cur.img.editOptions));
						_.utils.setImageEditOptionsFile(cur);
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
								_.sender.preparedFiles[cur.preparedFilesIndex].img.callback = 'sendNextFile';
								_.sender.preparedFiles[cur.preparedFilesIndex].tmpSize = _.sender.preparedFiles[cur.preparedFilesIndex].size;

								// we process this image and call sendNextFile again!
								_.controller.processSingleImage(_.sender.preparedFiles[cur.preparedFilesIndex]);
								return;
							}

							// dataUrl of edited file exists: make dataArray, reinsert meta if any and go on:
							cur.dataArray = _.utils.dataURLToUInt8Array(cur.dataUrl);
							if(cur.type === 'image/jpeg' && cur.img.jpgCustomSegments){
								cur.dataArray = _.utils.jpgInsertSegment(cur.dataArray, cur.img.jpgCustomSegments);
							} else if(cur.type === 'image/png' && cur.img.pngTextChunks){
								cur.dataArray = _.utils.pngReinsertTextchunks(cur.dataArray, cur.img.pngTextChunks);
							}
						}
						cur.dataUrl = null;
					}
					this.currentFile = cur = this.uploadFiles.shift();

					if (cur.uploadConditionsOk) {
						this.isUploading = true;
						cur.isUploadStarted = true;
						_.view.repaintGUI({what: 'startSendFile'});

						if (cur.size <= this.chunkSize && !cur.img.editOptions.doEdit) {
							this.sendNextChunk(false);
						} else {
							if (_.view.elems.fileSelect && _.view.elems.fileSelect.value) {
								_.view.elems.fileSelect.value = '';
							}

							if (_.EDIT_IMAGES_CLIENTSIDE && _.controller.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1 && cur.dataArray && cur.dataArray.length){
								// we have an edited image width dataArray already prepared
								that.sendNextChunk(true);
							} else {
								// we have an image not to be edited or other filetype
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
					if (_.document.we_form.elements[this.moreFieldsToAppend[i][0]]) {
						switch (this.moreFieldsToAppend[i][1]) {
							case 'check':
								fd.append(this.moreFieldsToAppend[i][0], ((_.document.we_form.elements[this.moreFieldsToAppend[i][0]].checked) ? 1 : 0));
								break;
							case 'multi_select':
								var sel = _.document.we_form.elements[this.moreFieldsToAppend[i][0]],
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
								fd.append(this.moreFieldsToAppend[i][0], _.document.we_form.elements[this.moreFieldsToAppend[i][0]].value);
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
							cur.dataArray = null;
							cur.dataUrl = null;
							if(_.controller.IS_MEMORY_MANAGMENT){
								_.utils.memorymanagerUnregister(cur);
							}
							_.view.repaintGUI({what: 'chunkOK'});
							_.view.repaintGUI({what: 'fileOK'});
							this.doOnFileFinished(resp);//FIXME: make this part of postProcess(resp, fileonly=true)
							if (this.uploadFiles.length !== 0) {
								this.sendNextFile();
							} else {
								_.sender.postProcess(resp);
							}
							return;
						case 'failure':
							this.currentWeight = this.currentWeightTag + cur.size;
							this.currentWeightTag = this.currentWeight;
							cur.dataArray = null;
							cur.dataUrl = null;
							if(_.controller.IS_MEMORY_MANAGMENT){
								_.utils.memorymanagerUnregister(cur);
							}
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
			};
			this.previewSize = 116;
			this.useOriginalAsPreviewIfNotEdited = false;
			this.lastLoupIndex = -1;
			this.loupeVisible = false;

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
				if(fileobj.isUploadStarted){
					return;
				}
				pt = pt ? pt : 0;
				if(pt === 1){
					var info = '',
						dimension = fileobj.img.fullPrev.height + 'x' + fileobj.img.fullPrev.width + ' px';
					if(fileobj.isEdited){
						var deg = parseInt(fileobj.img.processedOptions.rotate),
							degText = deg === 0 ? '' : (deg === 90 ? '90&deg; ' + _.utils.gl.editRotationRight : (deg === 270 ? '90&deg; ' + _.utils.gl.editRotationLeft : '180&deg;'));
						info += (fileobj.img.processedOptions.scale ? _.utils.gl.editScaled + ' ' : '') + dimension;
						info += deg ? (info ? ', ' : '') + _.utils.gl.editRotation + ': ' + degText : '';
						info += fileobj.img.processedOptions.quality !== _.controller.OPTS_QUALITY_NEUTRAL_VAL && fileobj.type === 'image/jpeg' ? (info ? ', ' : '') + _.utils.gl.editQuality + ': ' + fileobj.img.processedOptions.quality + '%' : '';
					} else {
						info = _.utils.gl.editNotEdited + ': ' + dimension;
					}
					_.document.getElementById('we_fileUpload_loupeInfo').innerHTML = info;

					fileobj.loupInner.innerHTML = ''; // be sure img is appended as firstChild!
					fileobj.loupInner.appendChild(fileobj.img.fullPrev);
					_.document.getElementById('we_fileUpload_loupeInfo').style.display = 'block';
					_.document.getElementById('we_fileUpload_spinner').style.display = 'none';
					_.document.getElementsByClassName('editorCrosshairH')[0].style.display = 'block';
					_.document.getElementsByClassName('editorCrosshairV')[0].style.display = 'block';
					fileobj.focusPoint = _.document.getElementById('we_fileUpload_focusPoint');
					fileobj.focusPointFixed = _.document.getElementById('editorFocuspointFixed');
					fileobj.focusPoint.style.display = 'block';
					return;
				}

				if(_.view.lastLoupIndex !== -1 && _.view.lastLoupIndex !== fileobj.index && _.sender.preparedFiles[_.view.lastLoupIndex]){
					// in importer we delete fullPreview of an fileobj when moving to an other file
					_.sender.preparedFiles[_.view.lastLoupIndex].img.fullPrev = null;
				}
				_.view.lastLoupIndex = fileobj.index;


				var mask = _.document.getElementById('we_fileUploadImporter_mask');
				if(mask){
					mask.style.display = 'block';
				}

				if(!(fileobj.img.fullPrev || fileobj.dataUrl || fileobj.dataArray)){
					_.document.getElementById('we_fileUpload_loupeFallback').innerHTML = 'Für dieses Bild bzw. für die aktuellen Bearbeitungsoptionen<br/>wurde noch keine Vorschau erstellt.';
					_.document.getElementById('we_fileUpload_loupeFallback').style.display = 'block';
					_.view.loupeVisible = false;
					return;
				}
				_.view.loupeVisible = true;

				fileobj.loupInner = _.document.getElementById('we_fileUpload_loupeInner');
				fileobj.loupInner.style.display = 'block';
				_.document.getElementById('we_fileUpload_loupe').style.display = 'block';
				_.document.getElementById('we_fileUpload_spinner').style.display = 'block';

				if(fileobj.img.fullPrev){
					_.view.setPreviewLoupe(fileobj, 1); // we always keep 1 rendered image in loupe
				} else if(fileobj.dataUrl || fileobj.dataArray){ // we have dataURL or dataArray
					fileobj.img.fullPrev = new Image();
					_.utils.logTimeFromStart('start load fullpreview', true);
					fileobj.img.fullPrev.onload = function(){
						_.utils.logTimeFromStart('end load fullpreview');
						_.view.setPreviewLoupe(fileobj, 1);
					};
					setTimeout(function () {
						/*
						var bin = '', base64 = '';
						if(!fileobj.dataUrl){
							for (var i = 0; i < fileobj.dataArray.byteLength; i++) {
								bin += String.fromCharCode(fileobj.dataArray[ i ]);
							}
							base64 = 'data:' + fileobj.type + ';base64,' + _.window.btoa(bin);
						} else {
							base64 = fileobj.dataUrl;
						}
						*/
						fileobj.img.fullPrev.src = fileobj.dataUrl;
					}, 10);
				} else {
					/*
					 * we sould not load data here: it lasts too long!
					 * as long as memory limit is not reached we save dataURLs to fileobjects
					 * => when limit is reached one must load dataURL using btnMakePreview!
					 *
					 */
					/*
					var reader = new FileReader();
					reader.onload = function() {
						fileobj.img.fullPrev = new Image();
						fileobj.img.fullPrev.onload = function(){
							_.view.setPreviewLoupe(fileobj, 1);
						};
						fileobj.img.fullPrev.src = reader.result;
					};
					reader.readAsDataURL(fileobj.file);
					*/
				}
			};

			this.movePreviewLoupe = function(e, fileobj){
				if(fileobj.isUploadStarted){
					return;
				}
				try{
					if(e.timeStamp - _.view.lastklick < 10){
						// in Chrome onclick fires mosemove too: this causes the newly set focuspoint to be slightly wrong...
						return;
					}

					if(fileobj.loupInner && fileobj.loupInner.firstChild){
						var offsetLeft = (-fileobj.loupInner.firstChild.width / fileobj.img.previewCanvas.width * e.offsetX) + (fileobj.loupInner.parentNode.offsetWidth / 2);
						var offsetTop = (-fileobj.loupInner.firstChild.height / fileobj.img.previewCanvas.height * e.offsetY) + (fileobj.loupInner.parentNode.offsetHeight / 2);
						_.view.offesetLeft = offsetLeft;
						_.view.offsetTop = offsetTop;

						fileobj.loupInner.style.left = Math.round(offsetLeft) + 'px';
						fileobj.loupInner.style.top = Math.round(offsetTop) + 'px';

						fileobj.focusPoint.style.left = Math.round(offsetLeft + ((parseFloat(fileobj.img.focusX) + 1) / 2) * fileobj.img.fullPrev.width) + 'px';
						fileobj.focusPoint.style.top = Math.round(offsetTop + ((parseFloat(fileobj.img.focusY) + 1) / 2) * fileobj.img.fullPrev.height) + 'px';
					}
				} catch (ex) {
					//
				}
			};

			this.unsetPreviewLoupe = function(fileobj){
				_.view.loupeVisible = false;
				if(fileobj.loupInner){
					fileobj.loupInner.style.display = 'none';
					fileobj.loupInner.parentNode.style.display = 'none';
					fileobj.loupInner.innerHTML = '';
				}
				if(fileobj.focusPoint){
					fileobj.focusPoint.style.display = 'none';
					fileobj.focusPointFixed.style.display = 'none';
				}

				_.document.getElementById('we_fileUpload_loupeInfo').style.display = 'none';
				_.document.getElementById('we_fileUpload_loupeFallback').style.display = 'none';
				_.document.getElementsByClassName('editorCrosshairH')[0].style.display = 'none';
				_.document.getElementsByClassName('editorCrosshairV')[0].style.display = 'none';

				var mask = _.document.getElementById('we_fileUploadImporter_mask');
				if(mask){
					mask.style.display = 'none';
				}
			};

			this.grabFocusPoint = function(e, fileobj){
				if(!_.view.loupeVisible){
					return;
				}
				_.view.lastklick = e.timeStamp;
				if(fileobj.img.previewCanvas.width && fileobj.img.previewCanvas.height){
					fileobj.focusPoint.style.display = 'none';
					fileobj.focusPointFixed.style.display = 'block';
					var focusX = ((e.offsetX / fileobj.img.previewCanvas.width) * 2) - 1;
					var focusY = ((e.offsetY / fileobj.img.previewCanvas.height) * 2) - 1;
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
				_.document.getElementById('span_' + _.fieldName + '_' + name + p).innerHTML = text;
			};

			this.replacePreviewCanvas = function() {
				// to be overridden
			};

			this.setInternalProgress = function (progress, index) {
				try{
					var coef = this.intProgress.width / 100,
						i = typeof index !== 'undefined' || index === false ? index : false,
						p = i === false ? '' : '_' + i;

					_.document.getElementById(_.fieldName + '_progress_image_bg' + p).style.width = ((coef * 100) - (coef * progress)) + "px";
					_.document.getElementById(_.fieldName + '_progress_image' + p).style.width = coef * progress + "px";

					this.setInternalProgressText('progress_text', progress + '%', index);
				} catch(e){}
			};

			this.setInternalProgressCompleted = function (success, index, txt) {
				var s = success || false,
						i = index || false,
						p = !i ? '' : '_' + i;

				if (s) {
					this.setInternalProgress(100, i);
					_.document.getElementById(_.fieldName + '_progress_image').className = 'progress_finished';
				} else {
					_.document.getElementById(_.fieldName + '_progress_image' + p).className = 'progress_failed';
				}
			};

			this.formEditOptsReset = function (form) { // USED
				form.elements.fuOpts_scaleWhat.value = 'pixel_l';
				form.elements.fuOpts_scale.value = '';
				form.elements.fuOpts_rotate.value = 0;
				form.elements.fuOpts_quality.value = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
				form.getElementsByClassName('qualityValueContainer')[0].innerHTML = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
			};

			this.formCustomEditOptsSync = function () {
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
			this.processimageRepeatLoadCanvas = 0;

			this.logTimeFromStart = function(text, resetStart, more){
				if(_.debug){
					var date = new Date();

					this.start = resetStart ? date.getTime() : this.start;
					top.console.log((text ? text : ''), (date.getTime() - this.start)/1000, more ? more : '');
				}
			};

			this.abstractSetImageEditOptionsGeneral = function (formname) {
				var form = _.document.forms[(formname ? formname : 'we_form')],
								scale = form.elements.fuOpts_scale.value,
								deg = parseInt(form.elements.fuOpts_rotate.value),
								quality = parseInt(form.elements.fuOpts_quality.value),
					opts = _.sender.imageEditOptions;

				if (parseInt(form.elements.fuOpts_doEdit.value) === 1 && (scale || deg || quality !== _.controller.OPTS_QUALITY_NEUTRAL_VAL)) {
					opts.doEdit = true;
					opts.scaleWhat = form.elements.fuOpts_scaleWhat.value;
					opts.scale = scale;
					opts.rotate = deg;
					opts.quality = form.elements.fuOpts_quality.value;
				} else {
					opts.doEdit = false;
					opts.scaleWhat = 'pixel_l';
					opts.scale = 0;
					opts.rotate = 0;
					opts.quality = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
				}
			};

			this.setImageEditOptionsGeneral = function () {
				_.utils.abstractSetImageEditOptionsGeneral();
			};

			this.setImageEditOptionsFile = function (fileobj) {
				fileobj.img.editOptions = JSON.parse(JSON.stringify(_.sender.imageEditOptions));
				fileobj.img.editOptions.from = type; // FIXME: what type is this?
			};

			this.processimageExtractLandscape = function(fileobj, nexttask) {
				if(fileobj.type === 'image/jpeg' && !fileobj.img.isOrientationChecked){
					var reader = new FileReader(),
						exif, tags;

					fileobj.img.isOrientationChecked = true;
					reader.onloadend = function (event) {
						try {
							exif = new window.ExifReader();
							exif.load(event.target.result);
							tags = exif.getAllTags();
							if (tags.Orientation) {
								switch (tags.Orientation.value) {
									case 3:
										fileobj.img.orientationValue = 180;
										break;
									case 6:
										fileobj.img.orientationValue = -90;
										break;
									case 8:
										fileobj.img.orientationValue = 90;
										break;
								}
							}
						} catch (error) {}
						_.controller.processImage(fileobj, nexttask);
					};
					reader.readAsArrayBuffer(fileobj.file.slice(0, 128 * 1024));
				} else {
					fileobj.img.isOrientationChecked = true;
					_.controller.processImage(fileobj, nexttask);
				}
			};

			this.processimageLoadCanvas = function(fileobj, nexttask) {
				var reader = new FileReader();

				reader.onload = function() {
					if(!fileobj.img.editOptions.doEdit){ // we will not edit and rewrite image so we hold original dataUrl in fileobj to be used by fullpreview
						fileobj.dataUrl = reader.result;

						if(fileobj.img.previewCanvas){ // preview done during earlier editing: directly jump top postprocess
							_.utils.processimagePostProcess(fileobj, _.controller.IMG_NEXT);
							return;
						}
					}

					fileobj.img.image = new Image();
					fileobj.img.image.onload = function() {
						fileobj.img.workingCanvas = _.document.createElement('canvas');
						if(!fileobj.img.image && _.utils.processimageRepeatLoadCanvas < 5){
							_.utils.processimageRepeatLoadCanvas++;
							_.utils.processimageLoadCanvas(fileobj, nexttask);
						}
						_.utils.processimageRepeatLoadCanvas = 0;
						fileobj.img.workingCanvas.width = fileobj.img.image.width;
						fileobj.img.workingCanvas.height = fileobj.img.image.height;
						if(!fileobj.img.origWidth || !fileobj.img.origHeight){
							fileobj.img.origWidth = fileobj.img.image.width;
							fileobj.img.origHeight = fileobj.img.image.height;
							_.utils.setImageEditOptionsFile(fileobj); // set editOptions again after orig dimensions are extracted
						}
						fileobj.img.workingCanvas.getContext("2d").drawImage(fileobj.img.image, 0, 0);
						fileobj.img.image = null;
						_.utils.logTimeFromStart('canvas loaded');
						nexttask = _.controller.PROCESS_PREVIEWS_ONLY ? _.controller.IMG_MAKE_PREVIEW : nexttask;
						_.controller.processImage(fileobj, nexttask);
					};

					_.view.repaintImageEditMessage(true, true);
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

						_.utils.logTimeFromStart('meta jpg exttracted');
						_.view.repaintImageEditMessage(true);
						_.controller.processImage(fileobj, nexttask);
					};
					reader.readAsArrayBuffer(fileobj.file.slice(0, 128 * 1024));
			};

			this.processimageExtractMetadataPNG = function(fileobj, nexttask) {
				var reader = new FileReader();

				reader.onloadend = function () {
					fileobj.img.pngTextChunks = [];
					try{
						var chunks = window.extractChunks(new Uint8Array(reader.result));
						for(var i = 0; i < chunks.length; i++){
							if(chunks[i].name === 'iTXt' || chunks[i].name === 'tEXt' || chunks[i].name === 'zTXt'){
								fileobj.img.pngTextChunks.push(chunks[i]);
								/*
								var decodedChunk = chunks[i].name !== 'iTXt' ? window.decodeChunk(chunks[i]) :
										decodeURIComponent(String.fromCharCode.apply(null, chunks[i].data));
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
				if(!fileobj.img.editOptions.scale){
					_.utils.logTimeFromStart('scaling skipped');
					_.controller.processImage(fileobj, nexttask);
					return; // IMPORTANT!
				}

				var scaleWhat = fileobj.img.editOptions.scaleWhat !== 'pixel_l' ? fileobj.img.editOptions.scaleWhat :
						(fileobj.img.workingCanvas.width >= fileobj.img.workingCanvas.height ? 'pixel_w' : 'pixel_h');
				var ratio = scaleWhat === 'pixel_w' ? fileobj.img.editOptions.scale/fileobj.img.workingCanvas.width :
							fileobj.img.editOptions.scale/fileobj.img.workingCanvas.height;
				if(ratio >= 1){
					_.utils.logTimeFromStart('scaling: image smaller than targetsize');
					_.controller.processImage(fileobj, nexttask); // we do not upscale!
					return; // IMPORTANT!
				}

				var targetCanvas = _.document.createElement('canvas');
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
					fileobj.isEdited = true;
					_.view.repaintImageEditMessage(true);
					_.controller.processImage(fileobj, nexttask);
				});
			};

			this.processimageRotate = function(fileobj, nexttask, preview, degrees, correctPreviewOrientation){
				var deg = (preview && degrees !== undefined) ? degrees : fileobj.img.editOptions.rotate,
					target = preview ? 'previewCanvas' : 'workingCanvas';

				/*
				 * IMPORTANT: NEVER automatically correct orientation of the preview!
				 * => we correct preview when creating it and then take it for not rotated!!
				 */
				if(target === 'workingCanvas'){
					deg = (deg + fileobj.img.orientationValue) % 360;
				}

				if(correctPreviewOrientation){
					deg = -fileobj.img.orientationValue;
					target = 'previewCanvas';
					nexttask = undefined;
					preview = true;
				}

				var cw = fileobj.img[target].width, ch = fileobj.img[target].height, cx = 0, cy = 0;
				switch(deg) {
					case 90 :
					case -270 :
						cw = fileobj.img[target].height;
						ch = fileobj.img[target].width;
						cy = -fileobj.img[target].height;
						break;
					case 180 :
					case -180 :
						cx = -fileobj.img[target].width;
						cy = -fileobj.img[target].height;
						break;
					case 270 :
					case -90 :
						cw = fileobj.img[target].height;
						ch = fileobj.img[target].width;
						cx = -fileobj.img[target].width;
						break;
					default:
						_.utils.logTimeFromStart('rotation skipped');
						_.controller.processImage(fileobj, nexttask);
						return;
				}

				var targetCanvas = _.document.createElement('canvas'),
					ctxTargetCanvas = targetCanvas.getContext("2d");
					targetCanvas.width = cw;
					targetCanvas.height = ch;

				ctxTargetCanvas.rotate(deg * Math.PI / 180);
				ctxTargetCanvas.drawImage(fileobj.img[target], cx, cy);

				fileobj.img[target] = targetCanvas;
				targetCanvas = null;

				if(correctPreviewOrientation){
					return;
				}

				if(target === 'workingCanvas'){
					fileobj.isEdited = true;
					fileobj.img.actualRotation = deg;
					_.utils.logTimeFromStart('rotation done');
					_.view.repaintImageEditMessage(true);
					_.controller.processImage(fileobj, nexttask);
				}
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
				/*
				if(fileobj.type !== 'image/png'){
					_.utils.processimageWriteImage_2(fileobj, nexttask);
				}
				*/
				fileobj.dataUrl = fileobj.img.workingCanvas.toDataURL(fileobj.type, (fileobj.img.editOptions.quality/100));
				fileobj.dataArray = _.utils.dataURLToUInt8Array(fileobj.dataUrl);
				if(!_.controller.PRESERVE_IMG_DATAURL){
					fileobj.dataUrl = null;
				}
				fileobj.isEdited = fileobj.img.editOptions.quality < 90 ? true : fileobj.isEdited;

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
				//_.controller.processImage(fileobj, nexttask);

				if(fileobj.img.pngTextChunks && fileobj.img.pngTextChunks.length){
					fileobj.dataArray = this.pngReinsertTextchunks(fileobj.dataArray, fileobj.img.pngTextChunks);

					/*
					var combinedChuks = [];
					try{
						var chunks = window.extractChunks(fileobj.dataArray),
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
							newUInt8Array = window.encodeChunks(combinedChuks);
						} catch (e) {
							newUInt8Array = false;
						}
					}

					fileobj.dataArray = newUInt8Array ? newUInt8Array : fileobj.dataArray;
					*/

					_.view.repaintImageEditMessage(true);
				}

				_.utils.logTimeFromStart('metadata reinserted');
				_.controller.processImage(fileobj, nexttask);
			};

			this.processimageMakePreview = function(fileobj, nexttask){
				if(fileobj && fileobj.img.workingCanvas){
					if(fileobj.img.previewCanvas){
						_.utils.processimageRotatePreview(fileobj, -1, nexttask);
						return;
					}

					var tmpCanvas = _.document.createElement("canvas"),
						ctxTmpCanvas,
						previewCanvas = _.document.createElement("canvas"),
						clone = null,
						prevWidth, prevHeight;

					if(fileobj.img.workingCanvas.width < _.view.previewSize && fileobj.img.workingCanvas.height < _.view.previewSize){
						prevHeight = fileobj.img.workingCanvas.width;
						prevWidth = fileobj.img.workingCanvas.height;
					} else {
						if(fileobj.img.workingCanvas.width > fileobj.img.workingCanvas.height){
							prevWidth = _.view.previewSize;
							prevHeight = Math.round(_.view.previewSize / fileobj.img.workingCanvas.width * fileobj.img.workingCanvas.height);

						} else {
							prevHeight = _.view.previewSize;
							prevWidth = Math.round(_.view.previewSize / fileobj.img.workingCanvas.height * fileobj.img.workingCanvas.width);
						}
					}

					tmpCanvas.width = fileobj.img.workingCanvas.width;
					tmpCanvas.height = fileobj.img.workingCanvas.height;
					ctxTmpCanvas = tmpCanvas.getContext("2d");
					ctxTmpCanvas.drawImage(fileobj.img.workingCanvas, 0, 0, tmpCanvas.width, tmpCanvas.height);

					while((tmpCanvas.width / 2) > prevWidth){
						clone = tmpCanvas.cloneNode(true);
						clone.getContext('2d').drawImage(tmpCanvas, 0, 0);
						tmpCanvas.width = clone.width / 2;
						tmpCanvas.height = clone.height / 2;
						ctxTmpCanvas.drawImage(clone, 0, 0, clone.width / 2, clone.height / 2);
					}

					previewCanvas.width = prevWidth;
					previewCanvas.height = prevHeight;
					previewCanvas.getContext('2d').drawImage(tmpCanvas, 0, 0, previewCanvas.width, previewCanvas.height);

					fileobj.img.previewCanvas = previewCanvas;
					fileobj.img.previewRotation = fileobj.img.actualRotation;

					if(fileobj.img.orientationValue){
						_.utils.processimageRotate(fileobj, -1, true, 0, true);
					}

					tmpCanvas = ctxTmpCanvas = previewCanvas = clone = null;

					_.utils.logTimeFromStart('preview done');
				}

				_.controller.processImage(fileobj, nexttask);
				return;
			};

			this.processimageRotatePreview = function(fileobj, deg, nexttask){
					if(fileobj && fileobj.img.previewCanvas){
						var targetRotation = deg === -1 ? fileobj.img.actualRotation : deg;
						var realRotation = (360 - fileobj.img.previewRotation + targetRotation) % 360;

						_.utils.processimageRotate(fileobj, -1, true, realRotation);
						_.view.replacePreviewCanvas(fileobj);
						fileobj.img.previewRotation = targetRotation;
					}

					if(!nexttask){
						return;
					}
					_.controller.processImage(fileobj, nexttask);
			};

			/* obsolete
			this.processimageReset = function(fileobj, nexttask){
				if(!fileobj){
					return;
				}
				fileobj.dataArray = null;
				fileobj.dataUrl = null;
				fileobj.size = fileobj.img.originalSize;
				fileobj.img.previewImg = null;
				fileobj.img.fullPrev = null;
				fileobj.img.actualRotation = 0;
				_.utils.setImageEditOptionsFile(fileobj); // write correct actually valid editoptions
				fileobj.img.processedOptions = { // reset last edited options
					doEdit: false,
					from: 'general',
					scaleWhat: 'pixel_l',
					scale: 0,
					rotate: 0,
					quality: 100
				},
				_.utils.processimageRotatePreview(fileobj, 0);
				_.view.replacePreviewCanvas(fileobj);
				fileobj.isEdited = false;

				if(nexttask){
					_.controller.processImage(fileobj, nexttask);
				} else {
					_.view.repaintEntry(fileobj);
				}
				return;
			};
			*/

			this.processimagePostProcess = function(fileobj, nexttask){
				fileobj.img.originalSize = fileobj.file.size;

				if(fileobj.dataArray && fileobj.isEdited){
					fileobj.size = fileobj.dataArray.length;
				}
				fileobj.dataArray = null; // we recompute it from dataUrl and metas while uploading
				if(_.controller.PROCESS_PREVIEWS_ONLY){
					fileobj.dataUrl = null;
				}
				fileobj.img.workingCanvas = null;
				fileobj.img.fullPrev = null;

				fileobj.img.processedOptions = JSON.parse(JSON.stringify(fileobj.img.editOptions));
				fileobj.totalParts = Math.ceil(fileobj.size / _.sender.chunkSize);
				fileobj.lastChunkSize = fileobj.size % _.sender.chunkSize;
				_.sender.preparedFiles[fileobj.preparedFilesIndex] = fileobj; // do we need this?
				_.view.repaintEntry(fileobj);
				_.view.repaintImageEditMessage();
				_.utils.logTimeFromStart('processing finished', false, fileobj);

				if(_.controller.IS_MEMORY_MANAGMENT){
					_.utils.memorymanagerRegister(fileobj);
					if(this.memorymanagerIsOverflow()){
						this.memorymanagerEmptySpace();
						//top.console.log('emptied space for this file');

						if(_.sender.imageFilesToProcess.length){ //
							if(fileobj.img.editOptions.doEdit && this.memorymanagerIsUneditedPreviewToDelete){
								//top.console.log('dataURLs of unedited where deleted: go on');
							} else {
								//top.console.log('processing images must be stopped: we will make previews if needed and then stop');
								_.controller.PROCESS_PREVIEWS_ONLY = true;
							}
						}
					}
				}

				if(fileobj.processSingleImage){
					_.controller.processSingleImage(fileobj, true);
				} else {
					_.controller.processImage(fileobj, nexttask);
				}
			};

			this.memorymanagerRegister = function(fileobj){
				var m = _.controller.memoryManagement;
				this.memorymanagerUnregister(fileobj);

				if(fileobj.dataArray || fileobj.dataUrl){
					var size = parseInt((fileobj.dataArray ? fileobj.dataArray.length : 0)) + parseInt((fileobj.dataUrl ? fileobj.dataUrl.length : 0));

					m[fileobj.isEdited ? 'queueEdited' : 'queueNotEdited'].push(fileobj.index);
					m.registeredValues['o_' + fileobj.index] = size;
					m.registeredSum += size;
				}
				//top.console.log('register', m, _.sender.preparedFiles);
			};

			this.memorymanagerUnregister = function(fileobj){
				var m = _.controller.memoryManagement,
					i;

				if(m.registeredValues['o_' + fileobj.index]){
					m.registeredSum -= m.registeredValues['o_' + fileobj.index];
					m.registeredValues['o_' + fileobj.index] = 0;
				}

				if((i = m.queueEdited.indexOf(fileobj.index)) !== -1){
					m.queueEdited.splice(i, 1);
				}
				if((i = m.queueNotEdited.indexOf(fileobj.index)) !== -1){
					m.queueNotEdited.splice(i, 1);
				}
				//top.console.log('unregister', m, _.sender.preparedFiles);
			};

			this.memorymanagerReregisterAll = function(){
				this.memorymanagerReset();

				for(var i = 0; i < _.sender.preparedFiles.length; i++){
					this.memorymanagerRegister(_.sender.preparedFiles[i]);
				}
				//top.console.log('register all', _.controller.memoryManagement, _.sender.preparedFiles);
			};

			this.memorymanagerReset = function(){
				_.controller.memoryManagement = {
					registeredSum: 0,
					registeredValues: {},
					queueEdited: [],
					queueNotEdited: []
				};
			};

			this.memorymanagerIsOverflow = function(){
				return _.controller.memoryManagement.registeredSum > _.controller.MEMORY_LIMIT;
			};

			this.memorymanagerIsUneditedPreviewToDelete = function(){
				return _.controller.memoryManagement.queueNotEdited.length > 0;
			};

			this.memorymanagerEmptySpace = function(){
				var m = _.controller.memoryManagement,
					fileobj, index;

				while((m.queueEdited.length || m.queueNotEdited.length) && m.registeredSum > _.controller.MEMORY_LIMIT){
					index = m.queueNotEdited.length ? m.queueNotEdited.shift() : m.queueEdited.shift();
					fileobj = _.sender.preparedFiles[index];

					_.controller.uneditImage(fileobj.index);
					_.view.setEditStatus('', fileobj.index);
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

			this.dataURLToUInt8Array = function (dataURL) {
				var BASE64_MARKER = ';base64,',
					parts = dataURL.split(BASE64_MARKER),
					//contentType = parts[0].split(':')[1],
					raw = _.window.atob(parts[1]),
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
					var pos = 0;
					if(!Uint8Array.prototype.indexOf){ // IE11
						for(var i = 4; i < uint8array.length; i++){
							if(uint8array[i] === 255){
								pos = i;
								break;
							}
						}
					} else {
						pos = uint8array.indexOf(255, 4);
					}

					var head = uint8array.subarray(0, pos),
						segments = uint8array.subarray(pos);

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
							return uint8array.subarray(head, endPoint);
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
						segments[uint8array[head + 1] + '_' + head] = uint8array.subarray(head, endPoint);
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
							segmentsArr.push(uint8array.subarray(head, endPoint));
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

			this.pngReinsertTextchunks = function(dataArray, pngTextChunks){
				var combinedChunks = [];
				try{
					var chunks = window.extractChunks(dataArray);
						combinedChunks = [];

					combinedChunks.push(chunks.shift()); // new IHDR
					while(pngTextChunks.length){
						combinedChunks.push(pngTextChunks.shift());
					}
					while(chunks.length){
						combinedChunks.push(chunks.shift());
					}
				} catch(e){
					combinedChunks = false;
				}

				var newUInt8Array = false;
				if(combinedChunks){
					try{
						newUInt8Array = window.encodeChunks(combinedChunks);
					} catch (e) {
						newUInt8Array = false;
					}
				}

				return newUInt8Array ? newUInt8Array : dataArray;
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

		this.getType = function () {
			return _.fileuploadType;
		};

		this.doUploadIfReady = function (callback) {
			callback();
			return;
		};

		this.reeditImage = function (index, general) {
			_.controller.reeditImage(index, general);
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

			if(!_.onload_abstract(that)){
				return false;
			}

			//get references to some include-specific html elements
			_.view.elems.message = _.document.getElementById('div_' + _.fieldName + '_message');
			_.view.elems.progress = _.document.getElementById('div_' + _.fieldName + '_progress');
			_.view.elems.progressText = _.document.getElementById('span_' + _.fieldName + '_progress_text');
			_.view.elems.progressMoreText = _.document.getElementById('span_' + _.fieldName + '_progress_more_text');
			_.view.elems.fileName = _.document.getElementById('div_' + _.fieldName + '_fileName');
			_.view.elems.btnResetUpload = _.document.getElementById('div_' + _.fieldName + '_btnResetUpload');
			_.view.elems.btnCancel = _.document.getElementById('div_' + _.fieldName + '_btnCancel');
			_.view.repaintGUI({what: 'initGui'});

			_.controller.checkIsPresetFiles();

			return true;
		};

		function Controller() {
		}

		function Sender() {
			this.totalWeight = 0;

			/* use parent (abstract)
			 this.appendMoreData = function (fd) {
			 for(var i = 0; i < this.moreFieldsToAppend.length; i++){
			 if(_.document.we_form.elements[this.moreFieldsToAppend[i]]){
			 fd.append(this.moreFieldsToAppend[i], _.document.we_form.elements[this.moreFieldsToAppend[i]].value);
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

				if(_.sender.nextCmd){
					setTimeout(function () {
						var tmp = _.sender.nextCmd.split(',');
						tmp.splice(1, 0, _.sender.resp);
						if(_.window.we_cmd){
							_.window.we_cmd.apply(_.window, tmp);
						} else { // FIXME: make sure have a function we_cmd on every opener!
							_.window.top.we_cmd.apply(_.window, tmp);
						}
					}, 100);
				}
			};

			this.processError = function (arg) {
				switch (arg.from) {
					case 'gui' :
						top.we_showMessage(arg.msg, 4, _.window);
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
							this.elems.footer.setProgress(this.extProgress.name, prog.toFixed(digits));
						}
						return;
					case 'fileOK' :
						if (this.elems.progress) {
							this.setInternalProgressCompleted(true);
						}
						if (this.extProgress.isExtProgress) {
							this.elems.footer.setProgress(this.extProgress.name, 100);
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
							_.document.getElementById(_.fieldName + '_progress_image').className = "progress_image";
							this.elems.progress.style.display = '';
							this.elems.progressMoreText.style.display = '';
							this.elems.progressMoreText.innerHTML = '&nbsp;&nbsp;/ ' + _.utils.computeSize(_.sender.currentFile.size);
						}
						if (this.extProgress.isExtProgress) {
							this.elems.footer.setProgress(this.extProgress.name, 0);
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
							this.elems.footer.setProgress(this.extProgress.name, 0);
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

			if(!_.onload_abstract(that)){
				return false;
			}

			_.controller.setWeButtonText('next', 'upload');
			_.controller.enableWeButton('next', false);

			// add some listeners:
			if (_.EDIT_IMAGES_CLIENTSIDE) {
				var generalform = _.document.we_form;
				generalform.elements.fuOpts_scale.addEventListener('keyup', function (e) {
					_.controller.editOptionsOnChange(e.target);
				});
				generalform.elements.fuOpts_scaleWhat.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				});
				generalform.elements.fuOpts_scaleProps.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				});
				generalform.elements.fuOpts_rotate.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				});
				generalform.elements.check_fuOpts_doEdit.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				});
				generalform.elements.fuOpts_quality.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);
				var btn = generalform.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];
				btn.addEventListener('click', function(){_.controller.editImageButtonOnClick(btn, -1, true);}, false);
			}

			return true;
		};

		function Controller() {
			var that = _.controller;
			this.IS_MEMORY_MANAGMENT = true;

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

					if (_.controller.EDITABLE_CONTENTTYPES.indexOf(f.type) !== -1) {
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

					if(_.controller.IS_MEMORY_MANAGMENT){
						_.utils.memorymanagerRegister(f);
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
				fileobj.dataUrl = null;
				if(_.controller.IS_MEMORY_MANAGMENT){
					_.utils.memorymanagerRegister(fileobj);
				}
			};

			this.openImageEditor = function(index){
				if(_.sender.preparedFiles[index]){
					// TODO: use WE().util.jsWindow
					//var previewWin = new (WE().util.jsWindow)(window, '', "", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, false, true);
					var previewWin = _.window.open('', '', 'width=700,height=500');
					var img;

					if(_.sender.preparedFiles[index].isEdited && _.sender.preparedFiles[index].dataUrl){
						// IMPORTANT: try to load img from fileobj.dataArray to avoid saving dataUrl!!
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

			this.prepareUpload = function (rePrepare) {
				if(rePrepare){ // first file has been reedited: we must recalculat totalWeight and set external progress
					this.totalWeight = this.totalWeight - this.uploadFiles[0].tmpSize + this.uploadFiles[0].size;
					this.totalChunks = this.totalWeight / this.chunkSize;
					_.view.repaintGUI({what: 'chunkOK'});
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
							_.document.getElementById('div_rowButtons_' + i).style.display = 'none';
							_.document.getElementById('div_rowProgress_' + i).style.display = 'block';
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
				var sf = _.document.we_form,
					cur = this.currentFile;

				fd.append('weFormNum', cur.fileNum + 1);
				fd.append('weFormCount', this.totalFiles);
				fd.append('we_cmd[0]', 'import_files');
				fd.append('step', 1);

				//if(!_.EDIT_IMAGES_CLIENTSIDE){
					fd.append('fu_file_sameName', sf.fu_file_sameName.value);
					fd.append('fu_file_parentID', sf.fu_file_parentID.value);
					fd.append('fu_doc_categories', sf.fu_doc_categories.value);
					fd.append('fu_doc_importMetadata', sf.fu_doc_importMetadata.value);
					fd.append('fu_doc_isSearchable', sf.fu_doc_isSearchable.value);
				//}

				if (_.controller.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1) {
					fd.append('fu_doc_focusX', cur.img.focusX);
					fd.append('fu_doc_focusX', cur.img.focusY);
					fd.append('fu_doc_thumbs', sf.fu_doc_thumbs.value);
				}

				return fd;
			};

			this.postProcess = function (resp) {
				var that = _.sender;
				_.sender.resp = resp;

				if (!this.isCancelled) {
					_.view.elems.footer.setProgress('', 100);
					_.view.elems.footer.setProgressText('progress_title', '');
					top.we_showMessage(resp.completed.message, top.WE().consts.message.WE_MESSAGE_INFO, window);

					if(_.sender.nextCmd){
						setTimeout(function () {
							var tmp = _.sender.nextCmd.split(',');
							tmp.splice(1, 0, _.sender.resp);
							top.we_cmd.apply(top, tmp);
						}, 100);
					}
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
						top.we_showMessage(arg.msg, top.WE().consts.message.WE_MESSAGE_ERROR, window);
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
				_.utils.memorymanagerReregisterAll();
				this.uploadFiles = [];
				this.currentFile = -1;
				this.mapFiles = [];
				this.totalFiles = _.sender.totalWeight = _.sender.currentWeight = _.sender.currentWeightTag = 0;
				_.view.elems.footer.setProgress("", 0);
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
				this.appendRow(f, _.sender.preparedFiles.length - 1);
			};

			this.repaintEntry = function (fileobj) { // TODO: get rid of fileobj.entry
				if(!fileobj.entry){
					fileobj.entry = _.document.getElementById('div_uploadFiles_' + fileobj.index);
					if(!fileobj.entry){
						top.console.log('an error occured: fileobj.entry is undefined');
						return;
					}
				}
				fileobj.entry.getElementsByClassName('elemSize')[0].innerHTML = (fileobj.isSizeOk ? _.utils.computeSize(fileobj.size) : '<span style="color:red">> ' + ((_.sender.maxUploadSize / 1024) / 1024) + ' MB</span>');
				_.view.addTextCutLeft(fileobj.entry.getElementsByClassName('elemFilename')[0], fileobj.file.name, 220);

				if(_.controller.EDITABLE_CONTENTTYPES.indexOf(fileobj.type) !== -1){
					fileobj.entry.getElementsByClassName('elemIcon')[0].style.display = 'none';
					fileobj.entry.getElementsByClassName('elemPreview')[0].style.display = 'block';
					fileobj.entry.getElementsByClassName('elemContentBottom')[0].style.display = 'block';
					fileobj.entry.getElementsByClassName('optsQualitySlide')[0].style.display = fileobj.type === 'image/jpeg' ? 'block' : 'none';
					_.view.replacePreviewCanvas(fileobj);
					this.formCustomEditOptsSync(fileobj.index, false);
					this.setEditStatus('', fileobj.index, false);
				} else {
					fileobj.entry.getElementsByClassName('elemIcon')[0].style.display = 'block';
					fileobj.entry.getElementsByClassName('elemPreview')[0].style.display = 'none';
					fileobj.entry.getElementsByClassName('elemContentBottom')[0].style.display = 'none';

					var ext = fileobj.file.name.substr(fileobj.file.name.lastIndexOf('.') + 1).toUpperCase();
					fileobj.entry.getElementsByClassName('elemIcon')[0].innerHTML = WE().util.getTreeIcon(fileobj.type) + ' ' + ext;
				}
			};

			this.replacePreviewCanvas = function(fileobj) {
					var elem = fileobj.entry.getElementsByClassName('elemPreviewPreview')[0];
					elem.innerHTML = '';
					elem.appendChild(fileobj.img.previewCanvas);

					elem.firstChild.addEventListener('mouseenter', function(){_.view.setPreviewLoupe(fileobj);}, false);
					elem.firstChild.addEventListener('mousemove', function(e){_.view.movePreviewLoupe(e, fileobj);}, false);
					elem.firstChild.addEventListener('mouseleave', function(){_.view.unsetPreviewLoupe(fileobj);}, false);
					elem.firstChild.addEventListener('click', function(e){_.view.grabFocusPoint(e,fileobj);}, false);
			};

			this.setImageEditMessage = function (singleMode, index) {
				var row, elem;
				if(singleMode && (row = _.document.getElementById('div_uploadFiles_' + index))){
					row.getElementsByClassName('elemContentTop')[0].style.display = 'none';
					row.getElementsByClassName('elemContentBottom')[0].style.display = 'none';
					row.getElementsByClassName('elemContentMask')[0].style.display = 'block';
					row.getElementsByClassName('we_file_drag_maskBusyText')[0].innerHTML = _.utils.gl.maskProcessImage;
					return;
				}

				if((elem = _.document.getElementById('we_fileUploadImporter_mask'))){
					_.document.getElementById('we_fileUploadImporter_busyText').innerHTML = _.sender.imageEditOptions.doEdit ? _.utils.gl.maskImporterProcessImages : _.utils.gl.maskImporterReadImages;
					try{
						_.document.getElementById('we_fileUploadImporter_messageNr').innerHTML = _.sender.imageFilesToProcess.length;
					} catch (e) {
					}
					_.document.getElementById('we_fileUploadImporter_busyMessage').style.display = 'block';
					_.document.getElementById('we_fileUploadImporter_busyMessage').style.zIndex = 800;
					elem.style.display = 'block';
				}
			};

			this.unsetImageEditMessage = function (singleMode, index) {
				var row, elem;
				if(singleMode && (row = _.document.getElementById('div_uploadFiles_' + index))){
					row.getElementsByClassName('elemContentTop')[0].style.display = 'block';
					row.getElementsByClassName('elemContentBottom')[0].style.display = 'block';
					row.getElementsByClassName('elemContentMask')[0].style.display = 'none';
					return;
				}

				if((elem = _.document.getElementById('we_fileUploadImporter_mask'))){
					elem.style.display = 'none';
					_.document.getElementById('we_fileUploadImporter_busyMessage').style.display = 'none';
				}
			};

			this.repaintImageEditMessage = function(step, singleMode, index) {
				var row;
				try{
					if(step){
						if(false && singleMode && (row = _.document.getElementById('div_uploadFiles_' + index))){
							row.getElementsByClassName('we_file_drag_maskBusyText')[0].innerHTML += _.sender.imageEditOptions.doEdit ? '.' : '';
							return;
						}

						_.document.getElementById('we_fileUploadImporter_busyText').innerHTML += _.sender.imageEditOptions.doEdit ? '.' : '';
					} else {
						_.document.getElementById('we_fileUploadImporter_busyText').innerHTML = _.sender.imageEditOptions.doEdit ? _.utils.gl.maskImporterProcessImages : _.utils.gl.maskImporterReadImages;
						_.document.getElementById('we_fileUploadImporter_messageNr').innerHTML = _.sender.imageFilesToProcess.length;
					}
				} catch (e) {
				}
			};

			this.appendRow = function (f, index) {
				var div,
					entry,
					row = this.htmlFileRow.replace(/WEFORMNUM/g, index).replace(/WE_FORM_NUM/g, (this.nextTitleNr++)).
						replace(/FILENAME/g, (f.file.name)).
						replace(/FILESIZE/g, (f.isSizeOk ? _.utils.computeSize(f.size) : '<span style="color:red">> ' + ((_.sender.maxUploadSize / 1024) / 1024) + ' MB</span>'));

				_.window.weAppendMultiboxRow(row, '', 0, 0, 0, -1);
				entry = _.document.getElementById('div_uploadFiles_' + index);

				div = _.document.getElementById('div_upload_files');
				//div.scrollTop = getElementById('div_upload_files').div.scrollHeight;
				_.document.getElementById('fileInput_uploadFiles_' + index).addEventListener('change', _.controller.replaceSelectionHandler, false);

				_.view.addTextCutLeft(_.document.getElementById('name_uploadFiles_' + index), f.file.name, 220);

				if(_.EDIT_IMAGES_CLIENTSIDE){
					if(_.controller.EDITABLE_CONTENTTYPES.indexOf(f.type) !== -1){
						_.document.getElementById('icon_uploadFiles_' + index).style.display = 'none';
						_.document.getElementById('preview_uploadFiles_' + index).style.display = 'block';
						_.document.getElementById('editoptions_uploadFiles_' + index).style.display = 'block';
					} else {
						var ext = f.file.name.substr(f.file.name.lastIndexOf('.') + 1).toUpperCase();
						_.document.getElementById('icon_uploadFiles_' + index).innerHTML = WE().util.getTreeIcon(f.type) + ' ' + ext;
					}
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
				f.entry = _.document.getElementById('div_uploadFiles_' + index);

				var form = _.document.getElementById('form_editOpts_' + index);
				form.elements.fuOpts_useCustomOpts.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);
				form.elements.fuOpts_scale.addEventListener('keyup', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);
				form.elements.fuOpts_scaleWhat.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);
				form.elements.fuOpts_scaleProps.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				});
				form.elements.fuOpts_rotate.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);
				form.elements.fuOpts_quality.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);

				var btn = form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];
				btn.addEventListener('click', function(){_.controller.editImageButtonOnClick(btn, index, false);}, false);

				form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseenter', function(e){_.controller.editOptionsHelp(e.target, 'enter');}, false);
				form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseleave', function(e){_.controller.editOptionsHelp(e.target, 'leave');}, false);
			};

			this.deleteRow = function (index, button) {
				var prefix = 'div_uploadFiles_',
					num = 0,
					z = 1,
					i,
					sp,
					divs = _.document.getElementsByTagName('DIV');
				_.utils.memorymanagerUnregister(_.sender.preparedFiles[index]);
				_.sender.preparedFiles[index] = null;

				_.window.weDelMultiboxRow(index);

				for (i = 0; i < divs.length; i++) {
					if (divs[i].id.length > prefix.length && divs[i].id.substring(0, prefix.length) === prefix) {
						num = divs[i].id.substring(prefix.length, divs[i].id.length);
						sp = _.document.getElementById('headline_uploadFiles_' + num);
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

					if (_.document.we_form.fu_file_parentID.value === activeFrame.EditorDocumentId && activeFrame.EditorEditPageNr === 16) {
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
					case 'startSendFile':
						i = s.mapFiles[cur.fileNum];
						if(_.controller.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1){
							_.document.getElementById('image_edit_done_' + i).style.display = 'block';
						}
						break;
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
						this.elems.footer.setProgress("", totalProg.toFixed(totalDigits));
						return;
					case 'fileOK' :
						i = s.mapFiles[cur.fileNum];
						try {
							_.document.getElementById('div_upload_files').scrollTop = _.document.getElementById('div_uploadFiles_' + i).offsetTop - 360;
						} catch (e) {}
						this.setInternalProgressCompleted(true, i, '');
						return;
					case 'chunkNOK' :
						totalProg = (100 / s.totalWeight) * s.currentWeight;
						i = s.mapFiles[cur.fileNum];
						j = i + 1;
						try {
							_.document.getElementById('div_upload_files').scrollTop = _.document.getElementById('div_uploadFiles_' + i).offsetTop - 200;
						} catch (e) {
						}
						this.setInternalProgressCompleted(false, i, arg.message);
						if (cur.partNum === 1) {
							this.elems.footer.setProgressText('progress_title', _.utils.gl.doImport + ' ' + _.utils.gl.file + ' ' + j);
						}
						this.elems.footer.setProgress("", totalProg.toFixed(totalDigits));
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
							_.document.getElementById('div_upload_files').scrollTop = 0;
						} catch (e) {
						}

						return;
					case 'cancelUpload' :
						i = s.mapFiles[cur.fileNum];
						this.setInternalProgressCompleted(false, s.mapFiles[cur.fileNum], _.utils.gl.cancelled);
						try {
							_.document.getElementById('div_upload_files').scrollTop = _.document.getElementById('div_uploadFiles_' + i).offsetTop - 200;
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
							_.document.getElementById('td_uploadFiles').innerHTML = '';
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
					if (_.document.getElementById(_.fieldName + '_progress_image_' + index)) {
						_.document.getElementById(_.fieldName + '_progress_image_' + index).className = 'progress_finished';
					}
				} else {
					if (typeof _.document.images['alert_img_' + index] !== 'undefined') {
						_.document.images['alert_img_' + index].style.visibility = 'visible';
						_.document.images['alert_img_' + index].title = txt;
					}
					if (_.document.getElementById(_.fieldName + '_progress_image_' + index)) {
						_.document.getElementById(_.fieldName + '_progress_image_' + index).className = 'progress_failed';
					}
				}
			};

			this.formCustomEditOptsDisable = function(form, disable){
				form.elements.fuOpts_scaleWhat.disabled = disable;
				form.elements.fuOpts_scale.disabled = disable;
				form.elements.fuOpts_scaleProps.disabled = disable;
				form.elements.fuOpts_rotate.disabled = disable;
				form.elements.fuOpts_quality.disabled = disable;//#eee

				var type = _.sender.preparedFiles[form.getAttribute('data-index')].type;
				form.getElementsByClassName('optsQualityBox')[0].style.backgroundColor = disable ? '#eee' : (type === 'image/jpeg' ? 'white' : '#eee');
				if(disable){
					//form.getElementsByClassName('weBtn')[0].disabled = true;
				}
			};

			this.formCustomEditOptsSync = function (pos, general) {
				var generalForm = _.document.we_form,
					form, indexes;

				pos = general ? -1 : (pos && pos !== -1 ? pos : -1);
				indexes = _.utils.getImageEditIndexes(pos, general, true);

				for(i = 0; i < indexes.length; i++){
					form = _.document.getElementById('form_editOpts_' + indexes[i]);
					if (form && !form.elements.fuOpts_useCustomOpts.checked) {
						form.elements.fuOpts_scaleWhat.value = generalForm.elements.fuOpts_scaleWhat.value;
						form.elements.fuOpts_scale.value = generalForm.elements.fuOpts_scale.value;
						form.elements.fuOpts_rotate.value = generalForm.elements.fuOpts_rotate.value;
						form.elements.fuOpts_quality.value = _.sender.preparedFiles[indexes[i]].type === 'image/jpeg' ? generalForm.elements.fuOpts_quality.value : 100;
						form.getElementsByClassName('qualityValueContainer')[0].innerHTML = _.sender.preparedFiles[indexes[i]].type === 'image/jpeg' ? generalForm.elements.fuOpts_quality.value : 100;
					}
				}
			};

			this.previewSyncRotation = function(pos, rotation){
				var indexes = _.utils.getImageEditIndexes(pos, pos === -1, false);

				for(var i = 0; i < indexes.length; i++){
					_.utils.processimageRotatePreview(_.sender.preparedFiles[indexes[i]], rotation);
					_.view.replacePreviewCanvas(_.sender.preparedFiles[indexes[i]]);
				}
			};

			this.setEditStatus = function(state, pos, general){ // TODO: maybe name it setGuiEditOtions
				var indexes = _.utils.getImageEditIndexes(pos, general, true),
					elems = _.document.getElementsByClassName('elemContentBottom'),
					sizes = _.document.getElementsByClassName('weFileUploadEntry_size'),
					buttons = _.document.getElementsByClassName('rowBtnProcess'),
					scaleInputs = _.document.getElementsByClassName('optsScaleInput_row'),
					scaleHelp = _.document.getElementsByClassName('optsRowScaleHelp'),
					fileobj, i, j, st;

				for(i = 0; i < indexes.length; i++){
					j = indexes[i];
					fileobj = _.sender.preparedFiles[j];
					st = state ? state : (fileobj.isEdited ? 'processed' : (fileobj.img.editOptions.doEdit ? 'notprocessed' : 'donotedit'));
					switch(st){
						case 'notprocessed':
								elems[j].style.backgroundColor = '#ffffff';
								elems[j].style.backgroundColor = 'rgb(216, 255, 216)';
								elems[j].style.backgroundImage = 'repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255, 255, 255,1.0) 5px, rgba(216,255,216,.5) 10px)';
								sizes[j].innerHTML = _.utils.gl.sizeTextOk + '--';
							break;
						case 'processed':
								elems[j].style.backgroundColor = 'rgb(216, 255, 216)';
								elems[j].style.backgroundImage =  'none';
							break;
						case 'donotedit':
						/*falls through*/
						default:
							elems[j].style.backgroundColor = 'white';
							elems[j].style.backgroundImage = 'none';
					}
					if(fileobj.img.tooSmallToScale){
						scaleInputs[j].style.color = '#aaaaaa';
						scaleHelp[j].style.display = 'block';
					} else {
						scaleInputs[j].style.color = 'black';
						scaleHelp[j].style.display = 'none';
					}
					buttons[j].disabled = _.sender.preparedFiles[j].dataUrl ? true : false;
				}
			};

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
				_.utils.abstractSetImageEditOptionsGeneral('we_form');
			};

			this.setImageEditOptionsFile = function (fileobj, general) {
				var indexes = this.getImageEditIndexes(general ? -1 : fileobj.index, general);

				for(var i = 0; i < indexes.length; i++){
					fileobj = _.sender.preparedFiles[indexes[i]];
					var form = _.document.getElementById('form_editOpts_' + fileobj.index),
						type = 'general';

					if (form && form.elements.fuOpts_useCustomOpts.checked) {
						type = 'custom';
					}

					switch(type){
						case 'general':
							_.utils.setImageEditOptionsGeneral();
							fileobj.img.editOptions = JSON.parse(JSON.stringify(_.sender.imageEditOptions));
							fileobj.img.editOptions.quality = fileobj.type === 'image/jpeg' ? fileobj.img.editOptions.quality : _.controller.OPTS_QUALITY_NEUTRAL_VAL;
							break;
						case 'custom':
							fileobj.img.editOptions.scaleWhat = form.elements.fuOpts_scaleWhat.value;
							fileobj.img.editOptions.scale = form.elements.fuOpts_scale.value;
							fileobj.img.editOptions.rotate = parseInt(form.elements.fuOpts_rotate.value);
							fileobj.img.editOptions.quality = fileobj.type === 'image/jpeg' ? parseInt(form.elements.fuOpts_quality.value) : _.controller.OPTS_QUALITY_NEUTRAL_VAL;
							break;
					}
					var scaleReference = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? fileobj.img.origWidth : (
							fileobj.img.editOptions.scaleWhat === 'pixel_h' ? fileobj.img.origHeight : Math.max(fileobj.img.origHeight, fileobj.img.origWidth));
					if(scaleReference < fileobj.img.editOptions.scale){
						fileobj.img.editOptions.scale = '';
						fileobj.img.tooSmallToScale = true;
						if(!fileobj.img.editOptions.rotate && fileobj.img.editOptions.quality !== _.controller.OPTS_QUALITY_NEUTRAL_VAL){
							fileobj.img.editOptions.quality = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
							form.elements.fuOpts_quality.value = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
							form.getElementsByClassName('optsQualityValue')[0].innerHTML = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
						}
					} else {
						fileobj.img.tooSmallToScale = false;
					}

					fileobj.img.editOptions.doEdit = fileobj.img.editOptions.scale || fileobj.img.editOptions.rotate || (fileobj.img.editOptions.quality !== _.controller.OPTS_QUALITY_NEUTRAL_VAL) ? true : false;
				}
			};

			this.getImageEditIndexes = function(index, general, formposition){
				var indexes = [],
					forms, i;

				if(general){
					forms = _.document.getElementsByName('form_editOpts');
					for(i = 0; i < forms.length; i++){
						index = forms[i].getAttribute('data-index');
						if (_.sender.preparedFiles[index] && _.controller.EDITABLE_CONTENTTYPES.indexOf(_.sender.preparedFiles[index].type) !== -1 &&
										!forms[i].elements.fuOpts_useCustomOpts.checked &&
										!_.sender.preparedFiles[index].isUploadStarted) {
							indexes.push(formposition ? i : index);
						}
					}
				} else if (index !== undefined && index > -1 && _.sender.preparedFiles[index] &&
								_.controller.EDITABLE_CONTENTTYPES.indexOf(_.sender.preparedFiles[index].type) !== -1) {
					indexes.push(index);
					if(formposition){
						forms = _.document.getElementsByName('form_editOpts');
						for(i = 0; i < forms.length; i++){
							if(forms[i].getAttribute('data-index') == index){
								return [index];
							}
						}
					}
				}

				return indexes;
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

			if(!_.onload_abstract(that)){
				return false;
			}

			for (i = 0; i < _.document.forms.length; i++) {
				_.document.forms[i].addEventListener('submit', _.controller.formHandler, false);
			}
			var inputs = _.document.getElementsByTagName('input');
			for (i = 0; i < inputs.length; i++) {
				if (inputs[i].type === 'file') {
					inputs[i].addEventListener('change', _.controller.fileSelectHandler, false);
				}
			}

			if (_.EDIT_IMAGES_CLIENTSIDE) {
				_.document.we_form.elements.check_fuOpts_doEdit.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);
				_.document.we_form.elements.fuOpts_scaleProps.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				});
				_.document.we_form.elements.fuOpts_scale.addEventListener('keyup', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);
				_.document.we_form.elements.fuOpts_scaleWhat.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);
				_.document.we_form.elements.fuOpts_rotate.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);
				_.document.we_form.elements.fuOpts_quality.addEventListener('change', function (e) {
					_.controller.editOptionsOnChange(e.target);
				}, false);
				_.document.we_form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0].addEventListener('click', function(e){_.controller.editImageButtonOnClick(e.target);}, false);
				var btn = _.document.we_form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];
				btn.addEventListener('click', function(){_.controller.editImageButtonOnClick(btn, -1, true);}, false);

				_.document.we_form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseenter', function(e){_.controller.editOptionsHelp(e.target, 'enter');}, false);
				_.document.we_form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseleave', function(e){_.controller.editOptionsHelp(e.target, 'leave');}, false);
			}

			v.elems.fileDrag_state_0 = _.document.getElementById('div_fileupload_fileDrag_state_0');
			v.elems.fileDrag_state_1 = _.document.getElementById('div_fileupload_fileDrag_state_1');
			v.elems.fileDrag_mask = _.document.getElementById('div_' + _.fieldName + '_fileDrag');
			v.elems.dragInnerRight = _.document.getElementById('div_upload_fileDrag_innerRight');
			v.elems.divRight = _.document.getElementById('div_fileupload_right');
			v.elems.txtFilename = _.document.getElementById('span_fileDrag_inner_filename');
			v.elems.txtFilename_1 = _.document.getElementById('span_fileDrag_inner_filename_1');//??
			v.elems.txtSize = _.document.getElementById('span_fileDrag_inner_size');
			v.elems.txtType = _.document.getElementById('span_fileDrag_inner_type');
			v.elems.txtEdit= _.document.getElementById('span_fileDrag_inner_edit');
			v.elems.divBtnReset = _.document.getElementById('div_fileupload_btnReset');
			v.elems.divBtnCancel = _.document.getElementById('div_fileupload_btnCancel');
			v.elems.divBtnUpload = _.document.getElementById('div_fileupload_btnUpload');
			v.elems.divProgressBar = _.document.getElementById('div_fileupload_progressBar');
			v.elems.divButtons = _.document.getElementById('div_fileupload_buttons');

			var ids = [
				'div_we_File_fileDrag'
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
			for (i = 0; i < ids.length; i++) {
				_.document.getElementById(ids[i]).addEventListener('dragover', _.controller.fileDragHover, false);
				_.document.getElementById(ids[i]).addEventListener('dragleave', _.controller.fileDragHover, false);
				_.document.getElementById(ids[i]).addEventListener('drop', _.controller.fileSelectHandler, false);
			}


			v.spinner = _.document.createElement("i");
			v.spinner.className = "fa fa-2x fa-spinner fa-pulse";

			_.controller.checkIsPresetFiles();

			return true;
		};

		function Controller() {
			this.elemFileDragClasses = 'we_file_drag';
			this.doSubmit = false;
			this.PRESERVE_IMG_DATAURL = true;

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

						for(var k in resp.weDoc){
							resp[k]=resp.weDoc[k];
						}

						setTimeout(function () {
							var tmp = _.sender.nextCmd.split(',');
							tmp.splice(1, 0, resp);
							_.window.opener.we_cmd.apply(_.window.opener, tmp);
							_.window.close();
						}, 100);

						//reload main tree and close!
						//or weFileUpload_instance.reset(); and let nextCmd close uploader!
					}
				} else if (resp.status === 'success') {
					_.sender.currentFile = null;
					if (WE(true)) {
						_.window.we_cmd('update_file');
						WE().layout.we_setPath(null, resp.weDoc.path, resp.weDoc.text, 0, "published");
					}
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
				_.view.repaintGUI({what: 'resetGui'});
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
				_.view.elems.fileDrag.style.backgroundImage = 'none';

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
					/*
					 * we may use this for img smaller than target size message
					var edittext;
					switch(f.img.editOptions.scaleWhat){
						case 'pixel_w':
							edittext = f.img.editOptions.scale + ' Prozent';
							break;
						case 'pixel_l':
							edittext = 'Längere Seite ' + f.img.editOptions.scale + ' px';
							break;
						case 'pixel_h':
							edittext = 'Höhe ' + f.img.editOptions.scale + ' px';
					}

					this.elems.txtEdit.innerHTML = '<strong>Skaliert</strong> auf ' + edittext;
					this.elems.txtEdit.style.display = 'block';
					*/
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
					if(f.type !== 'image/jpeg'){
						_.document.getElementsByClassName('optsQuality')[0].value = 100;
						_.document.getElementsByClassName('qualityValueContainer')[0].innerHTML = 100;
						_.document.getElementsByClassName('optsQuality')[0].style.display = 'none';
					} else {
						_.document.getElementsByClassName('optsQuality')[0].style.display = 'block';
					}
					_.document.getElementsByClassName('weFileupload_btnImgEditRefresh')[0].disable = false;
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
						this.elems.fileDrag.style.backgroundImage = 'none';
						this.setDisplay('fileInputWrapper', 'block');
						if (this.isDragAndDrop && this.elems.fileDrag) {
							this.setDisplay('fileDrag', 'block');
						}
						this.setDisplay('divBtnReset', 'none');
						this.setDisplay('divBtnUpload', '');
						this.setDisplay('divProgressBar', 'none');
						this.setDisplay('divBtnCancel', 'none');
						this.setDisplay('dragInnerRight', '');
						/*
						if (_.EDIT_IMAGES_CLIENTSIDE) {
							_.document.getElementById('make_preview_weFileupload').disabled = true;//make same as following
						}
						*/
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
						this.setInternalProgress(0);
						this.setGuiState(this.STATE_RESET);
						return;
				}
			};

			this.previewSyncRotation = function(pos, rotation){
				if(_.sender.preparedFiles.length){
					_.utils.processimageRotatePreview(_.sender.preparedFiles[0], rotation);
					_.view.replacePreviewCanvas(_.sender.preparedFiles[0]);
				}
			};

			this.setEditStatus = function(state){
				var fileobj = _.sender.preparedFiles.length ? _.sender.preparedFiles[0] : null,
					btn = _.document.getElementsByClassName('weFileupload_btnImgEditRefresh ')[0],
					st;

				state = !fileobj ? 'empty' : state;
				st = state ? state : !fileobj ? 'empty' : (fileobj.isEdited ? 'processed' : (fileobj.img.editOptions.doEdit ? 'notprocessed' : 'donotedit'));

				switch(st){
					case 'notprocessed':
						if(_.sender.preparedFiles.length){
							_.view.elems.fileDrag.style.backgroundColor = '#ffffff';
							_.view.elems.fileDrag.style.backgroundColor = 'rgb(216, 255, 216)';
							_.view.elems.fileDrag.style.backgroundImage = 'repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255, 255, 255,1.0) 5px, rgba(216,255,216,.5) 10px)';
							_.view.elems.txtSize.innerHTML = _.utils.gl.sizeTextOk + '--';
							btn.disabled = false;
						}
						break;
					case 'processed':
						if(_.sender.preparedFiles.length){
							_.view.elems.fileDrag.style.backgroundColor = 'rgb(216, 255, 216)';
							_.view.elems.fileDrag.style.backgroundImage =  'none';
							btn.disabled = true;
						}
						break;
					case 'donotedit':
						_.view.elems.fileDrag.style.backgroundColor = 'rgb(232, 232, 255)';
						_.view.elems.fileDrag.style.backgroundImage =  'none';
						btn.disabled = false;
						break;
					case 'empty':
						_.view.elems.fileDrag.style.backgroundColor = 'white';
						_.view.elems.fileDrag.style.backgroundImage =  'none';
						btn.disabled = true;
				}
				if(fileobj && fileobj.img.tooSmallToScale){
					_.document.getElementsByName('fuOpts_scale')[0].style.color = '#aaaaaa';
					_.document.getElementsByClassName('optsRowScaleHelp')[0].style.display = 'block';
				} else {
					_.document.getElementsByName('fuOpts_scale')[0].style.color = 'black';
					_.document.getElementsByClassName('optsRowScaleHelp')[0].style.display = 'none';
				}
			};

			this.writeFocusToForm = function(fileobj){
				if (!_.EDIT_IMAGES_CLIENTSIDE) {
					return;
				}
				_.document.we_form.elements.fu_doc_focusX.value = fileobj.img.focusX;
				_.document.we_form.elements.fu_doc_focusY.value = fileobj.img.focusY;
			};

			//TODO: use progress fns from abstract after adapting them to standard progress
			this.setInternalProgress = function (progress, index) {
				var coef = this.intProgress.width / 100,
					mt = typeof _.sender.currentFile === 'object' ? ' / ' + _.utils.computeSize(_.sender.currentFile.size) : '';

				_.document.getElementById('progress_image_fileupload').style.width = coef * progress + "px";
				_.document.getElementById('progress_image_bg_fileupload').style.width = (coef * 100) - (coef * progress) + "px";
				_.document.getElementById('progress_text_fileupload').innerHTML = progress + '%' + mt;
			};

			this.setDisplay = function (elem, val) {
				if (this.elems[elem]) {
					this.elems[elem].style.display = val;
				}
			};

			this.setImageEditMessage = function (singleMode) {
				if (!_.EDIT_IMAGES_CLIENTSIDE) {
					return;
				}

				var mask = _.document.getElementById('div_fileupload_fileDrag_mask'),
					text = _.document.getElementById('image_edit_mask_text');

				mask.style.display = 'block';
				text.innerHTML = singleMode ? _.utils.gl.maskProcessImage : _.utils.gl.maskReadImage;
			};

			this.unsetImageEditMessage = function () {
				if (!_.EDIT_IMAGES_CLIENTSIDE) {
					return;
				}
				var mask = _.document.getElementById('div_fileupload_fileDrag_mask');
				mask.style.display = 'none';
			};

			this.repaintImageEditMessage = function (empty, changeText) {
				if (!_.EDIT_IMAGES_CLIENTSIDE) {
					return;
				}

				var text = _.document.getElementById('image_edit_mask_text').innerHTML;
				text = (changeText ? _.utils.gl.maskProcessImage : text) + '.';
				text += '.';
				_.document.getElementById('image_edit_mask_text').innerHTML = text;

			};

			this.repaintEntry = function (fileobj) {
				this.addFile(fileobj);
				if (!_.EDIT_IMAGES_CLIENTSIDE) {
					return;
				}
				_.view.replacePreviewCanvas(fileobj);
				_.view.setEditStatus();
			};

			this.formCustomEditOptsSync = function(){
				if(_.sender.preparedFiles.length && _.sender.preparedFiles[0].type !== 'image/jpeg'){
					_.document.getElementsByClassName('qualityValueContainer')[0].innerHTML = 100;
				}
			};

			this.replacePreviewCanvas = function(fileobj) {
				this.elems.dragInnerRight.innerHTML = '';
				this.elems.dragInnerRight.appendChild(fileobj.img.previewCanvas);

				this.elems.dragInnerRight.firstChild.addEventListener('mouseenter', function(){_.view.setPreviewLoupe(fileobj);}, false);
				this.elems.dragInnerRight.firstChild.addEventListener('mousemove', function(e){_.view.movePreviewLoupe(e, fileobj);}, false);
				this.elems.dragInnerRight.firstChild.addEventListener('mouseleave', function(){_.view.unsetPreviewLoupe(fileobj);}, false);
				this.elems.dragInnerRight.firstChild.addEventListener('click', function(e){_.view.grabFocusPoint(e,fileobj);}, false);
			};
		}

		function Utils() {
			this.setImageEditOptionsFile = function () {
				_.utils.setImageEditOptionsGeneral();
				if(_.sender.preparedFiles.length){
					var fileobj = _.sender.preparedFiles[0];

					fileobj.img.editOptions = JSON.parse(JSON.stringify(_.sender.imageEditOptions));
					fileobj.img.editOptions.quality = fileobj.type === 'image/jpeg' ? fileobj.img.editOptions.quality : _.controller.OPTS_QUALITY_NEUTRAL_VAL;
					fileobj.img.editOptions.from = 'general';

					// the following is identical in importer: move to new fn on abstract
					var scaleReference = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? fileobj.img.origWidth : (
							fileobj.img.editOptions.scaleWhat === 'pixel_h' ? fileobj.img.origHeight : Math.max(fileobj.img.origHeight, fileobj.img.origWidth));

					if(scaleReference && (scaleReference < fileobj.img.editOptions.scale)){
						fileobj.img.editOptions.scale = '';
						fileobj.img.tooSmallToScale = true;
						if(!fileobj.img.editOptions.rotate && fileobj.img.editOptions.quality !== _.controller.OPTS_QUALITY_NEUTRAL_VAL){
							fileobj.img.editOptions.quality = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
							_.document.getElementsByName('fuOpts_quality')[0].value = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
							_.document.getElementById('qualityValue').innerHTML = _.controller.OPTS_QUALITY_NEUTRAL_VAL;
						}
					} else {
						fileobj.img.tooSmallToScale = false;
					}

					fileobj.img.editOptions.doEdit = fileobj.img.editOptions.scale || fileobj.img.editOptions.rotate || (fileobj.img.editOptions.quality !== _.controller.OPTS_QUALITY_NEUTRAL_VAL) ? true : false;
				}
			};

			this.getImageEditIndexes = function(pos, general){
				return _.sender.preparedFiles.length ? [0] : [];
			};
		}

		this.doUploadIfReady = function () {
			if (_.sender.isAutostartPermitted && _.sender.preparedFiles.length > 0 && _.sender.preparedFiles[0].uploadConditionsOk) {
				_.sender.isAutostartPermitted = false;
				this.startUpload();
			} else {
				//there may be a file in preview with uploadConditions nok!
				_.view.repaintGUI({what: 'resetGui'});
			}
		};
	}

	return Fabric;
}());