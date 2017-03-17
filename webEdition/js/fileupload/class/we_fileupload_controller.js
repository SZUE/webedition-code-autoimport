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

function weFileupload_controller_abstract(uploader) {
	var self = this;
	self.uploader = uploader;

	self.isPreset = false;
	self.cmdFileselectOnclick = '';
	self.elemFileDragClasses = 'we_file_drag';

	self.init = function (conf) {
		self.sender = self.uploader.sender; // on init all components are initialized
		self.view = self.uploader.view;
		self.imageEdit = self.uploader.imageEdit;
		self.utils = self.uploader.utils;

		self.isPreset = conf.isPreset ? conf.isPreset : self.isPreset;
		self.cmdFileselectOnclick = conf.cmdFileselectOnclick ? conf.cmdFileselectOnclick : self.cmdFileselectOnclick;

		self.init_sub(conf);
	};

	self.init_sub = function () {
		// to be overridden
	};

	self.onload = function () {
		var view = self.view;

		if (view.elems.fileSelect) {
			view.elems.fileSelect.addEventListener('change', self.fileSelectHandler, false);
			view.elems.fileInputWrapper.addEventListener('click', self.fileselectOnclick, false);
		}
		if (view.elems.fileDrag && view.isDragAndDrop) {
			view.elems.fileDrag.addEventListener('dragover', self.fileDragHover, false);
			view.elems.fileDrag.addEventListener('dragleave', self.fileDragHover, false);
			view.elems.fileDrag.addEventListener('drop', self.fileSelectHandler, false);
			view.elems.fileDrag.style.display = 'block';
		} else {
			view.isDragAndDrop = false;
			if (view.elems.fileDrag) {
				view.elems.fileDrag.style.display = 'none';
			}
		}

		self.onload_sub();
	};

	self.onload_sub = function(){
		// to be overridden
	};

	self.fileselectOnclick = function () {
		if(uploader.controller.cmdFileselectOnclick){
			var tmp = uploader.controller.cmdFileselectOnclick.split(',');
			var win = self.uploader.win;

			if(win.we_cmd){
				win.we_cmd.apply(win, tmp);
			} else { // FIXME: make sure have a function we_cmd on every opener!
				win.top.we_cmd.apply(win, tmp);
			}
		} else {
			return;
		}
	};

	self.checkIsPresetFiles = function () {
		if (self.isPreset && WE().layout.weEditorFrameController.getVisibleEditorFrame().document.presetFileupload) {
			self.fileSelectHandler(null, true, WE().layout.weEditorFrameController.getVisibleEditorFrame().document.presetFileupload);
		} else if (self.isPreset && self.uploader.win.opener.document.presetFileupload) {
			self.fileSelectHandler(null, true, self.uploader.win.opener.document.presetFileupload);
		}
	};

	self.fileSelectHandler = function (e, isPreset, presetFileupload) {
		var files = [];

		files = self.selectedFiles = isPreset ? (presetFileupload.length ? presetFileupload : files) : (e.target.files || e.dataTransfer.files);

		if (files.length) {
			if (!isPreset) {
				self.sender.resetParams();
				if (e.type === 'drop') {
					e.stopPropagation();
					e.preventDefault();
					self.sender.resetParams();
					self.fileDragHover(e);
				}
			}
			self.fileselectOnclick();

			self.imageEdit.imageFilesToProcess = [];
			for (var f, i = 0; i < files.length; i++) {
				if (!self.utils.contains(self.sender.preparedFiles, self.selectedFiles[i])) {
					f = self.prepareFile(self.selectedFiles[i]);
					self.sender.preparedFiles.push(f);
					self.view.addFile(f, self.sender.preparedFiles.length);
				}
			}

			if (uploader.EDIT_IMAGES_CLIENTSIDE && self.imageEdit.imageFilesToProcess.length) {
				self.imageEdit.processImages();
			}

			//IMI: REREGISTER MEMORY
		}
	};

	self.prepareFile = function (f, isUploadable) {
		var fileObj = {
			file: f,
			fileNum: 0,
			dataArray: null,
			dataUrl: null,
			isEdited: false,
			img: { // TODO: get empty object img from imageEdit
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
			fileNameTemp: '',
			fileName: ''
		},

		//TODO: make this OK-stuff more concise
		type = f.type ? f.type : 'text/plain',
		u = isUploadable || true,
		isTypeOk = self.utils.checkFileType(type, f.name),
		isSizeOk = (f.size <= self.sender.maxUploadSize || !self.sender.maxUploadSize) ? true : false,
		errorMsg = [
			self.utils.gl.errorNoFileSelected,
			self.utils.gl.errorFileSize,
			self.utils.gl.errorFileType,
			self.utils.gl.errorFileSizeType
		];

		fileObj.type = type;
		fileObj.isUploadable = isTypeOk && isSizeOk && u; //maybe replace uploadConditionOk by this
		fileObj.isTypeOk = isTypeOk;
		fileObj.isSizeOk = isSizeOk;
		fileObj.uploadConditionsOk = isTypeOk && isSizeOk;
		fileObj.error = errorMsg[isSizeOk && isTypeOk ? 0 : (!isSizeOk && isTypeOk ? 1 : (isSizeOk && !isTypeOk ? 2 : 3))];
		fileObj.size = f.size;
		fileObj.totalParts = Math.ceil(f.size / self.sender.chunkSize);
		fileObj.lastChunkSize = f.size % self.sender.chunkSize;
		fileObj.img.originalSize = f.size;

		if (uploader.EDIT_IMAGES_CLIENTSIDE && self.imageEdit.EDITABLE_CONTENTTYPES.indexOf(f.type) !== -1) {
			self.imageEdit.imageFilesToProcess.push(fileObj);
		}

		return fileObj;
	};

	self.fileDragHover = function (e) {
		e.preventDefault();
		e.target.className = (e.type === 'dragover' ? self.elemFileDragClasses + ' we_file_drag_hover' : self.elemFileDragClasses);
	};

	self.setWeButtonState = function (btn, enable, isFooter) {
		isFooter = isFooter ? true : false;
		var doc = self.uploader.doc;

		if (isFooter) {
			top.WE().layout.button[enable ? 'enable' : 'disable'](self.view.elems.footer.document, btn);
		} else {
			top.WE().layout.button[enable ? 'enable' : 'disable'](doc, btn);
			if (btn === 'browse_harddisk_btn') {
				top.WE().layout.button[enable ? 'enable' : 'disable'](doc, 'browse_btn');
			}
		}
	};

	this.editImageButtonOnClick = function(btn, index, general){
		btn.disabled = true;

		if(!btn.form){ // FIXME: this is a dirty fix: why can we have a button without form?
			return;
		}

		self.imageEdit.reeditImage(index, general);
	};

	self.editOptionsOnChange = function(event){
		var target = event.target,
			form = target.form,
			inputScale = form.elements.fuOpts_scale,
			inputRotate = form.elements.fuOpts_rotate,
			inputQuality = form.elements.fuOpts_quality,
			scale = inputScale.value,
			rotate = parseInt(inputRotate.value),
			quality = parseInt(inputQuality.value),
			pos = form.getAttribute('data-type') === 'importer_rowForm' ? form.getAttribute('data-index') : -1,
			//opttype = pos === -1 ? 'general' : 'custom';
			btnRefresh = form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0],
			imageEdit = self.imageEdit;

		switch (target.name){
			case 'fuOpts_scaleProps':
				inputScale.value = target.value;
				scale = target.value;
				inputScale.focus();
				target.value = 0;
				/* falls through*/
			case 'fuOpts_scale':
				if(scale || rotate){
					if(quality === imageEdit.OPTS_QUALITY_NEUTRAL_VAL){
						inputQuality.value = imageEdit.OPTS_QUALITY_DEFAULT_VAL;
						form.getElementsByClassName('qualityValueContainer')[0].innerHTML = imageEdit.OPTS_QUALITY_DEFAULT_VAL;
					}
					self.view.formCustomEditOptsSync(-1, true);
					self.imageEdit.uneditImage(pos, pos === -1 ? true : false);
					self.view.setEditStatus('', pos, pos === -1 ? true : false);
				} else {
					inputQuality.value = imageEdit.OPTS_QUALITY_NEUTRAL_VAL;
					form.getElementsByClassName('qualityValueContainer')[0].innerHTML = imageEdit.OPTS_QUALITY_NEUTRAL_VAL;
					self.view.formCustomEditOptsSync(-1, true);
					self.imageEdit.uneditImage(pos, pos === -1 ? true : false);
					self.view.setEditStatus('', pos, pos === -1 ? true : false);
				}
				btnRefresh.disabled = self.sender.preparedFiles.length === 0 || (!scale && !rotate);
				if(target.name === 'fuOpts_rotate'){
					self.view.formCustomEditOptsSync(-1, true, true);
					self.view.previewSyncRotation(pos, rotate);
				}
				break;
			case 'fuOpts_rotate':
				self.view.formCustomEditOptsSync(-1, true, true);
				if(scale || rotate){
					if(quality === imageEdit.OPTS_QUALITY_NEUTRAL_VAL){
						inputQuality.value = imageEdit.OPTS_QUALITY_DEFAULT_VAL;
						form.getElementsByClassName('qualityValueContainer')[0].innerHTML = imageEdit.OPTS_QUALITY_DEFAULT_VAL;
					}
				} else {
					inputQuality.value = imageEdit.OPTS_QUALITY_NEUTRAL_VAL;
					form.getElementsByClassName('qualityValueContainer')[0].innerHTML = imageEdit.OPTS_QUALITY_NEUTRAL_VAL;
				}

				self.imageEdit.uneditImage(pos, pos === -1 ? true : false);
				self.view.setEditStatus('', pos, pos === -1 ? true : false);
				self.view.previewSyncRotation(pos, rotate);
				btnRefresh.disabled = self.sender.preparedFiles.length === 0 || (!scale && !rotate);
				break;
			case 'fuOpts_scaleWhat':
				if(scale){
					btnRefresh.disabled = true;
					self.view.formCustomEditOptsSync(-1, true);
					self.imageEdit.uneditImage(pos, pos === -1 ? true : false);
					self.view.setEditStatus('notprocessed', pos, pos === -1 ? true : false);
				}
				break;
			case 'fuOpts_quality':
				form.getElementsByClassName('qualityValueContainer')[0].innerHTML = quality;
				if((quality === imageEdit.OPTS_QUALITY_NEUTRAL_VAL) && !scale && !rotate){
					//btnRefresh.disabled = true;
					self.view.formCustomEditOptsSync(-1, true);
					self.imageEdit.uneditImage(pos, pos === -1 ? true : false);
					self.view.setEditStatus('', pos, pos === -1 ? true : false);
				} else {
					self.view.formCustomEditOptsSync(-1, true);
					self.imageEdit.uneditImage(pos, pos === -1 ? true : false);
					btnRefresh.disabled = false;// sender.preparedFiles.length === 0;
					self.imageEdit.setImageEditOptionsFile(pos === -1 ? null : self.sender.preparedFiles[pos], pos === -1 ? true : false);
					self.view.setEditStatus('', pos, pos === -1 ? true : false);
				}
				break;
			case 'check_fuOpts_doEdit':
				// whenever we change this optoion we reset all vals and disable button
				self.imageEdit.isImageEditActive = target.checked;
				inputQuality.value = imageEdit.OPTS_QUALITY_NEUTRAL_VAL;
				form.getElementsByClassName('qualityValueContainer')[0].innerHTML = imageEdit.OPTS_QUALITY_NEUTRAL_VAL;
				self.view.disableCustomEditOpts(!target.checked);
				inputScale.value = '';

				inputRotate.value = 0;
				self.view.formCustomEditOptsSync(-1, true, true); // reset rotation on entries
				self.view.previewSyncRotation(-1, 0);

				self.imageEdit.uneditImage(-1, true);
				self.imageEdit.reeditImage(-1, true);
				self.view.setEditStatus('donotedit', -1, true);
				btnRefresh.disabled = true;
				break;
			/*
			case 'fuOpts_useCustomOpts':
				var fileobj = self.sender.preparedFiles[pos];
				if(target.checked){
					fileobj.img.editOptions.from = 'custom';
					self.imageEdit.uneditImage(pos, false);
					self.view.formCustomEditOptsDisable(form, false);
					self.view.formEditOptsReset(form);
					self.view.previewSyncRotation(pos, 0);
					self.imageEdit.setImageEditOptionsFile(fileobj);
					self.view.setEditStatus('', pos, false);
					btnRefresh.disabled = false;
				}else {
					fileobj.img.editOptions.from = 'general';
					self.imageEdit.uneditImage(pos, false);
					self.view.formCustomEditOptsDisable(form, true);
					self.view.formCustomEditOptsSync(pos, false);
					self.imageEdit.setImageEditOptionsFile(fileobj);
					self.view.previewSyncRotation(pos, fileobj.img.editOptions.rotate);
					self.view.setEditStatus('', pos, false);
					btnRefresh.disabled = false;
				}
				break;
			*/
		}
	};

	self.editOptionsHelp = function(target, dir){
		if(dir === 'enter'){
			var fileobj = self.sender.preparedFiles[target.getAttribute('data-index')];
			var scaleReference = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? fileobj.img.origWidth : (
					fileobj.img.editOptions.scaleWhat === 'pixel_h' ? fileobj.img.origHeight : Math.max(fileobj.img.origHeight, fileobj.img.origWidth));
			var text = self.utils.gl.editTargetsizeTooLarge;

			target.lastChild.innerHTML = text.replace('##ORIGSIZE##', scaleReference);
			target.lastChild.style.display = 'block';
		} else {
			target.lastChild.style.display = 'none';
		}
	};
}

function weFileupload_controller_base(uploader) {
	var self = this;
	self.uploader = uploader;
	weFileupload_controller_abstract.call(self, uploader);
}
weFileupload_controller_base.prototype = Object.create(weFileupload_controller_abstract.prototype);
weFileupload_controller_base.prototype.constructor = weFileupload_controller_base;

function weFileupload_controller_bindoc(uploader) {
	var self = this;
	self.uploader = uploader;
	weFileupload_controller_abstract.call(self, uploader);

	self.elemFileDragClasses = 'we_file_drag';
	self.doSubmit = false;

	self.onload_sub = function () {
		var doc = self.uploader.doc;
		var i;

		for (i = 0; i < doc.forms.length; i++) {
			doc.forms[i].addEventListener('submit', self.formHandler, false);
		}

		var inputs = doc.getElementsByTagName('input');
		for (i = 0; i < inputs.length; i++) {
			if (inputs[i].type === 'file') {
				inputs[i].addEventListener('change', self.fileSelectHandler, false);
			}
		}

		if (self.uploader.EDIT_IMAGES_CLIENTSIDE) {
			doc.we_form.elements.check_fuOpts_doEdit.addEventListener('change', self.editOptionsOnChange, false);
			doc.we_form.elements.fuOpts_scaleProps.addEventListener('change', self.editOptionsOnChange);
			doc.we_form.elements.fuOpts_scale.addEventListener('keyup', self.editOptionsOnChange, false);
			doc.we_form.elements.fuOpts_scaleWhat.addEventListener('change', self.editOptionsOnChange, false);
			doc.we_form.elements.fuOpts_rotate.addEventListener('change', self.editOptionsOnChange, false);
			doc.we_form.elements.fuOpts_quality.addEventListener('change', self.editOptionsOnChange, false);
			doc.we_form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0].addEventListener('click', function(e){
				self.editImageButtonOnClick(e.target);
			}, false);

			var btn = doc.we_form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];
			btn.addEventListener('click', function(){
				self.editImageButtonOnClick(btn, -1, true);
			}, false);

			doc.we_form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseenter', function(e){
				self.editOptionsHelp(e.target, 'enter');
			}, false);
			doc.we_form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseleave', function(e){
				self.editOptionsHelp(e.target, 'leave');
			}, false);
		}

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
			doc.getElementById(ids[i]).addEventListener('dragover', self.fileDragHover, false);
			doc.getElementById(ids[i]).addEventListener('dragleave', self.fileDragHover, false);
			doc.getElementById(ids[i]).addEventListener('drop', self.fileSelectHandler, false);
		}
	};

	self.fileDragHover = function (e) {
		e.preventDefault();
		self.view.elems.fileDrag.className = (e.type === 'dragover' ? self.elemFileDragClasses + ' we_file_drag_hover' : self.elemFileDragClasses);
	};

	self.setEditorIsHot = function () {
		if (uploader.uiType !== 'wedoc') {
			WE().layout.weEditorFrameController.setEditorIsHot(true, WE().layout.weEditorFrameController.ActiveEditorFrameId);
		}
	};
}
weFileupload_controller_bindoc.prototype = Object.create(weFileupload_controller_abstract.prototype);
weFileupload_controller_bindoc.prototype.constructor = weFileupload_controller_bindoc;

function weFileupload_controller_import(uploader) {
	var self = this;
	weFileupload_controller_abstract.call(self, uploader);

	self.onload_sub = function () {
		self.setWeButtonText('next', 'upload');
		self.enableWeButton('next', false);

		if (self.uploader.EDIT_IMAGES_CLIENTSIDE) {
			var generalform = self.uploader.doc.we_form;

			generalform.elements.fuOpts_scale.addEventListener('keyup', self.editOptionsOnChange);
			generalform.elements.fuOpts_scaleWhat.addEventListener('change', self.editOptionsOnChange);
			generalform.elements.fuOpts_scaleProps.addEventListener('change', self.editOptionsOnChange);
			generalform.elements.fuOpts_rotate.addEventListener('change', self.editOptionsOnChange);
			generalform.elements.check_fuOpts_doEdit.addEventListener('change', self.editOptionsOnChange);
			generalform.elements.fuOpts_quality.addEventListener('change', self.editOptionsOnChange, false);

			var btn = generalform.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];
			btn.addEventListener('click', function(){
				self.editImageButtonOnClick(btn, -1, true);
			}, false);
		}
	};

	self.replaceSelectionHandler = function (e) {
		var files = e.target.files;
		var f;

		if (files[0] instanceof File && !self.utils.contains(self.sender.preparedFiles, files[0])) {
			f = self.prepareFile(files[0]);
			self.imageEdit.processImages(f);


			var inputId = 'fileInput_uploadFiles_',
				index = e.target.id.substring(inputId.length),
				entry = self.sender.preparedFiles[index].entry;

			self.sender.preparedFiles[index] = f; //.isSizeOk ? f : null;
			self.sender.preparedFiles[index].entry = entry;

			if (self.imageEdit.EDITABLE_CONTENTTYPES.indexOf(f.type) !== -1) {
				self.imageEdit.processImages(self.sender.preparedFiles[index]);
			} else {
				self.view.repaintEntry(f);
			}

			if (f.isSizeOk) {
				if (!self.view.isUploadEnabled) {
					self.enableWeButton('next', true);
					self.view.isUploadEnabled = true;
					self.sender.isCancelled = false;
				}
			}

			if(self.imageEdit.IS_MEMORY_MANAGMENT){
				self.imageEdit.memorymanagerRegister(f);
			}
		}
	};

	self.enableWeButton = function (btn, enabled) {
		self.view.elems.footer[btn + '_enabled'] = top.WE().layout.button.switch_button_state(self.view.elems.footer.document, btn, (enabled ? 'enabled' : 'disabled'));
	};

	self.setWeButtonText = function (btn, text) {
		var replace;
		try{
			var test = WE().consts.g_l.fileupload;
		} catch(e){
			WE().t_e("'WE().consts.g_l.fileupload' not loaded");
			return;
		}

		switch (text) {
			case 'close' :
				replace = WE().consts.g_l.fileupload.btnClose;
				break;
			case 'cancel' :
				replace = WE().consts.g_l.fileupload.btnCancel;
				break;
			case 'upload' :
			default:
				replace = WE().consts.g_l.fileupload.btnUpload;
		}

		if (replace) {
			top.WE().layout.button.setText(self.view.elems.footer.document, btn, replace);
		}
	};

	self.editFilename = function(e){
		var doc = self.uploader.doc;

		switch(e.target.attributes['data-name'].value){
			case 'showName_uploadFiles':
				var input = doc.getElementById('fuOpts_filenameInput_' + e.target.attributes['data-index'].value);
				e.target.style.display = 'none';
				doc.getElementById('editName_uploadFiles_' + e.target.attributes['data-index'].value).style.display = 'block';
				self.tmpName = input.value;
				input.focus();
				break;
			case 'fuOpts_filenameInput':
				var index = e.target.attributes['data-index'].value;
				var showName = doc.getElementById('showName_uploadFiles_' + index);

				switch(e.type){
					case 'change':
						var fileName = (e.target.value || self.tmpName) + '.' + showName.attributes['data-ext'].value;

						self.view.addTextCutLeft(showName, fileName, 230);
						self.sender.preparedFiles[index].fileName = fileName;
						break;
					case 'blur':
						doc.getElementById('editName_uploadFiles_' + index).style.display = 'none';
						showName.style.display = 'block';
						break;
				}
				break;
		}
	};

	self.customEditOptsOnChange = function(target, index){
		var form = self.uploader.doc.getElementById('form_editOpts_' + index);
		var pos = index; // this may be wrong!!
		var btnRefresh = form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];
		var fileobj = self.sender.preparedFiles[index];

		switch (target.name){
			case 'fuOpts_addRotationLeft':
			case 'fuOpts_addRotationRight':
					var rotation = parseInt(form.elements.fuOpts_rotate.value);
					var newRotation = (rotation + (target.name === 'fuOpts_addRotationLeft' ? 270 : 90)) % 360;
					form.elements.fuOpts_rotate.value = newRotation;
					self.view.previewSyncRotation(pos, newRotation);

					self.imageEdit.uneditImage(pos, false);
					if(!newRotation && !parseInt(self.imageEdit.imageEditOptions.scale)){ // if there is no other editing from general we set entry on doEdit=false
						self.view.setEditStatus('', pos, false);
					} else {
						self.view.setEditStatus('notprocessed', pos, false);
					}
					btnRefresh.disabled = false;
				break;
		}
	};
}
weFileupload_controller_import.prototype = Object.create(weFileupload_controller_abstract.prototype);
weFileupload_controller_import.prototype.constructor = weFileupload_controller_import;
