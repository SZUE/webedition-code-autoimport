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

function Fileupload_controller_abstract(uploader) {
	this.uploader = uploader;

	this.isPreset = false;
	this.cmdFileselectOnclick = '';
	this.elemFileDragClasses = 'we_file_drag';

	this.init = function (conf) {
		this.sender = this.uploader.sender; // on init all components are initialized
		this.view = this.uploader.view;
		this.imageEdit = this.uploader.imageEdit;
		this.utils = this.uploader.utils;

		this.isPreset = conf.isPreset ? conf.isPreset : this.isPreset;
		this.cmdFileselectOnclick = conf.cmdFileselectOnclick ? conf.cmdFileselectOnclick : this.cmdFileselectOnclick;

		this.init_sub(conf);
	};

	this.init_sub = function () {
		// to be overridden
	};

	this.onload = function () {
		var view = this.view;

		if (view.elems.fileSelect) {
			view.elems.fileSelect.addEventListener('change', this.fileSelectHandler, false);
			view.elems.fileInputWrapper.addEventListener('click', this.fileselectOnclick, false);
		}
		if (view.elems.fileDrag && view.isDragAndDrop) {
			view.elems.fileDrag.addEventListener('dragover', this.fileDragHover, false);
			view.elems.fileDrag.addEventListener('dragleave', this.fileDragHover, false);
			view.elems.fileDrag.addEventListener('drop', this.fileSelectHandler, false);
			view.elems.fileDrag.style.display = 'block';
		} else {
			view.isDragAndDrop = false;
			if (view.elems.fileDrag) {
				view.elems.fileDrag.style.display = 'none';
			}
		}

		this.onload_sub();
	};

	this.onload_sub = function(){
		// to be overridden
	};

	this.fileselectOnclick = function () {
		if(uploader.controller.cmdFileselectOnclick){
			var tmp = uploader.controller.cmdFileselectOnclick.split(',');
			var win = this.uploader.win;

			if(win.we_cmd){
				win.we_cmd.apply(win, tmp);
			} else { // FIXME: make sure have a function we_cmd on every opener!
				win.top.we_cmd.apply(win, tmp);
			}
		} else {
			return;
		}
	};

	this.checkIsPresetFiles = function () {
		if (this.isPreset && WE().layout.weEditorFrameController.getVisibleEditorFrame().document.presetFileupload) {
			this.fileSelectHandler(null, true, WE().layout.weEditorFrameController.getVisibleEditorFrame().document.presetFileupload);
		} else if (this.isPreset && this.uploader.win.opener.document.presetFileupload) {
			this.fileSelectHandler(null, true, this.uploader.win.opener.document.presetFileupload);
		}
	};

	this.fileSelectHandler = function (e, isPreset, presetFileupload) {
		var files = [];

		files = this.selectedFiles = isPreset ? (presetFileupload.length ? presetFileupload : files) : (e.target.files || e.dataTransfer.files);

		if (files.length) {
			if (!isPreset) {
				this.sender.resetParams();
				if (e.type === 'drop') {
					e.stopPropagation();
					e.preventDefault();
					this.sender.resetParams();
					this.fileDragHover(e);
				}
			}
			this.fileselectOnclick();

			this.imageEdit.imageFilesToProcess = [];
			for (var f, i = 0; i < files.length; i++) {
				if (!this.utils.contains(this.sender.preparedFiles, this.selectedFiles[i])) {
					f = this.prepareFile(this.selectedFiles[i]);
					this.sender.preparedFiles.push(f);
					this.view.addFile(f, this.sender.preparedFiles.length);
				}
			}

			if (uploader.EDIT_IMAGES_CLIENTSIDE && this.imageEdit.imageFilesToProcess.length) {
				this.imageEdit.processImages();
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
			img: { // TODO: get empty object img from imageEdit
				processedOptions: {
					doEdit: false,
					from: 'general',
					scaleWhat: 'pixel_l',
					scale: 0,
					rotate: 0,
					quality: this.imageEdit.OPTS_QUALITY_NEUTRAL_VAL
				},
				editOptions: {
					doEdit: false,
					from: 'general',
					scaleWhat: 'pixel_l',
					scale: 0,
					rotate: 0,
					quality: this.imageEdit.OPTS_QUALITY_NEUTRAL_VAL
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
		isTypeOk = this.utils.checkFileType(type, f.name),
		isSizeOk = (f.size <= this.sender.maxUploadSize || !this.sender.maxUploadSize) ? true : false,
		errorMsg = [
			WE().consts.g_l.fileupload.errorNoFileSelected,
			WE().consts.g_l.fileupload.errorFileSize,
			WE().consts.g_l.fileupload.errorFileType,
			WE().consts.g_l.fileupload.errorFileSizeType
		];

		fileObj.type = type;
		fileObj.isUploadable = isTypeOk && isSizeOk && u; //maybe replace uploadConditionOk by this
		fileObj.isTypeOk = isTypeOk;
		fileObj.isSizeOk = isSizeOk;
		fileObj.uploadConditionsOk = isTypeOk && isSizeOk;
		fileObj.error = errorMsg[isSizeOk && isTypeOk ? 0 : (!isSizeOk && isTypeOk ? 1 : (isSizeOk && !isTypeOk ? 2 : 3))];
		fileObj.size = f.size;
		fileObj.totalParts = Math.ceil(f.size / this.sender.chunkSize);
		fileObj.lastChunkSize = f.size % this.sender.chunkSize;
		fileObj.img.originalSize = f.size;

		if (uploader.EDIT_IMAGES_CLIENTSIDE && this.imageEdit.EDITABLE_CONTENTTYPES.indexOf(f.type) !== -1) {
			this.imageEdit.imageFilesToProcess.push(fileObj);
		}

		return fileObj;
	};

	this.fileDragHover = function (e) {
		e.preventDefault();
		e.target.className = (e.type === 'dragover' ? this.elemFileDragClasses + ' we_file_drag_hover' : this.elemFileDragClasses);
	};

	this.editImageButtonOnClick = function(btn, index, general){
		btn.disabled = true;

		if(!btn.form){ // FIXME: this is a dirty fix: why can we have a button without form?
			return;
		}

		this.imageEdit.reeditImage(index, general);
	};

	this.editOptionsOnChange = function(event){
		var target = event.target,
			form = target.form,
			inputScale = form.elements.fuOpts_scale,
			inputRotate = form.elements.fuOpts_rotate,
			inputQuality = form.elements.fuOpts_quality,
			scale = inputScale.value,
			pos = form.getAttribute('data-type') === 'importer_rowForm' ? form.getAttribute('data-index') : -1,
			btnRefresh = form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0],
			imageEdit = this.imageEdit;

		var rotate = inputRotate ? parseInt(inputRotate.value) : 0;


		switch (target.name){
			case 'fuOpts_scaleWhat':
				if(scale){
					btnRefresh.disabled = false;
					this.view.formCustomEditOptsSync(-1, true);
					this.imageEdit.uneditImage(pos, pos === -1 ? true : false);
					this.view.setEditStatus('notprocessed', pos, pos === -1 ? true : false);
				}
				btnRefresh.disabled = this.sender.preparedFiles.length === 0 || !scale;
				break;
			case 'fuOpts_scaleProps':
				inputScale.value = target.value;
				scale = target.value;
				inputScale.focus();
				target.value = 0;
				/* falls through*/
			case 'fuOpts_scale':
				if(this.imageEdit.MAX_LONGEST !== -1){
					if(parseInt(scale) > this.imageEdit.MAX_LONGEST){
						WE().util.showMessage(WE().consts.g_l.fileupload.editMaxLongest.replace('##MAX_LONGEST##', this.imageEdit.MAX_LONGEST), WE().consts.message.WE_MESSAGE_INFO, this.uploader.win);
						scale = this.imageEdit.MAX_LONGEST;
						target.value = this.imageEdit.MAX_LONGEST;
					}
					if(!scale){
						WE().util.showMessage(WE().consts.g_l.fileupload.editScaleMandatory.replace('##MAX_LONGEST##', this.imageEdit.MAX_LONGEST), WE().consts.message.WE_MESSAGE_INFO, this.uploader.win);
						scale = this.imageEdit.MAX_LONGEST;
						target.value = this.imageEdit.MAX_LONGEST;
					}
				}
				this.view.formCustomEditOptsSync(-1, true);
				this.imageEdit.uneditImage(pos, pos === -1 ? true : false);
				this.view.setEditStatus('', pos, pos === -1 ? true : false);
				btnRefresh.disabled = this.sender.preparedFiles.length === 0;
				break;
			case 'fuOpts_rotate':
				this.view.formCustomEditOptsSync(-1, true, true);
				this.imageEdit.uneditImage(pos, pos === -1 ? true : false);
				this.view.setEditStatus('', pos, (pos === -1 ? true : false));
				this.view.previewSyncRotation(pos, rotate);
				btnRefresh.disabled = this.sender.preparedFiles.length === 0;
				break;
			case 'fuOpts_quality':
				this.view.formCustomEditOptsSync(-1, true);
				this.imageEdit.uneditImage(pos, pos === -1 ? true : false);
				this.view.setEditStatus('', pos, (pos === -1 ? true : false), 'keep');
				btnRefresh.disabled = this.sender.preparedFiles.length === 0;
				break;
			case 'check_fuOpts_doEdit':
				// whenever we change this optoion we reset all vals and disable button
				this.imageEdit.isImageEditActive = target.checked;
				inputQuality.value = imageEdit.OPTS_QUALITY_NEUTRAL_VAL;
				this.view.disableCustomEditOpts(!target.checked);
				inputScale.value = (target.checked && this.imageEdit.MAX_LONGEST !== -1) ? this.imageEdit.MAX_LONGEST : '';

				inputRotate.value = 0;
				this.view.formCustomEditOptsSync(-1, true, true); // reset rotation on entries
				this.view.previewSyncRotation(-1, 0);

				this.imageEdit.uneditImage(-1, true);
				this.imageEdit.reeditImage(-1, true);
				this.view.setEditStatus('donotedit', -1, true);
				btnRefresh.disabled = true;
				break;
		}
	};

	this.editOptionsHelp = function(target, dir){
		if(dir === 'enter'){
			var fileobj = this.sender.preparedFiles[target.getAttribute('data-index')];
			var scaleReference = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? fileobj.img.origWidth : (
					fileobj.img.editOptions.scaleWhat === 'pixel_h' ? fileobj.img.origHeight : Math.max(fileobj.img.origHeight, fileobj.img.origWidth));
			var text = WE().consts.g_l.fileupload.editTargetsizeTooLarge;

			target.lastChild.innerHTML = text.replace('##ORIGSIZE##', scaleReference);
			target.lastChild.style.display = 'block';
		} else {
			target.lastChild.style.display = 'none';
		}
	};
}

function Fileupload_controller_base(uploader) {
	this.uploader = uploader;
	Fileupload_controller_abstract.call(this, uploader);
}
Fileupload_controller_base.prototype = Object.create(Fileupload_controller_abstract.prototype);
Fileupload_controller_base.prototype.constructor = Fileupload_controller_base;

function Fileupload_controller_bindoc(uploader) {
	this.uploader = uploader;
	Fileupload_controller_abstract.call(this, uploader);

	this.elemFileDragClasses = 'we_file_drag';
	this.doSubmit = false;

	this.onload_sub = function () {
		var doc = this.uploader.doc;
		var i;

		for (i = 0; i < doc.forms.length; i++) {
			doc.forms[i].addEventListener('submit', this.formHandler, false);
		}

		var inputs = doc.getElementsByTagName('input');
		for (i = 0; i < inputs.length; i++) {
			if (inputs[i].type === 'file') {
				inputs[i].addEventListener('change', this.fileSelectHandler, false);
			}
		}

		if (this.uploader.EDIT_IMAGES_CLIENTSIDE) {
			if(doc.we_form.elements.check_fuOpts_doEdit){
				doc.we_form.elements.check_fuOpts_doEdit.addEventListener('change', this.editOptionsOnChange, false);
			}
			doc.we_form.elements.fuOpts_scaleProps.addEventListener('change', this.editOptionsOnChange);
			doc.we_form.elements.fuOpts_scale.addEventListener('keyup', this.editOptionsOnChange, false);
			doc.we_form.elements.fuOpts_scaleWhat.addEventListener('change', this.editOptionsOnChange, false);
			doc.we_form.elements.fuOpts_rotate.addEventListener('change', this.editOptionsOnChange, false);
			doc.we_form.elements.fuOpts_quality.addEventListener('change', this.editOptionsOnChange, false);
			doc.we_form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0].addEventListener('click', function(e){
				this.editImageButtonOnClick(e.target);
			}, false);

			var btn = doc.we_form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];
			btn.addEventListener('click', function(){
				this.editImageButtonOnClick(btn, -1, true);
			}, false);

			doc.we_form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseenter', function(e){
				this.editOptionsHelp(e.target, 'enter');
			}, false);
			doc.we_form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseleave', function(e){
				this.editOptionsHelp(e.target, 'leave');
			}, false);
		}

		var ids = [
			'div_we_File_fileDrag'
		];
		for (i = 0; i < ids.length; i++) {
			doc.getElementById(ids[i]).addEventListener('dragover', this.fileDragHover, false);
			doc.getElementById(ids[i]).addEventListener('dragleave', this.fileDragHover, false);
			doc.getElementById(ids[i]).addEventListener('drop', this.fileSelectHandler, false);
		}
	};

	this.fileDragHover = function (e) {
		e.preventDefault();
		this.view.elems.fileDrag.className = (e.type === 'dragover' ? this.elemFileDragClasses + ' we_file_drag_hover' : this.elemFileDragClasses);
	};

	this.setEditorIsHot = function () {
		if (uploader.uiType !== 'wedoc') {
			WE().layout.weEditorFrameController.setEditorIsHot(true, WE().layout.weEditorFrameController.ActiveEditorFrameId);
		}
	};
}
Fileupload_controller_bindoc.prototype = Object.create(Fileupload_controller_abstract.prototype);
Fileupload_controller_bindoc.prototype.constructor = Fileupload_controller_bindoc;

function Fileupload_controller_import(uploader) {
	Fileupload_controller_abstract.call(this, uploader);

	this.onload_sub = function () {
		WE().layout.button.display(this.view.elems.footer.document, 'next', false);
		WE().layout.button.display(this.view.elems.footer.document, 'upload', true);
		WE().layout.button.disable(this.view.elems.footer.document, 'upload', true);

		if (this.uploader.EDIT_IMAGES_CLIENTSIDE) {
			var generalform = this.uploader.doc.we_form;

			generalform.elements.fuOpts_scale.addEventListener('keyup', this.editOptionsOnChange);
			generalform.elements.fuOpts_scaleWhat.addEventListener('change', this.editOptionsOnChange);
			generalform.elements.fuOpts_scaleProps.addEventListener('change', this.editOptionsOnChange);
			generalform.elements.fuOpts_quality.addEventListener('change', this.editOptionsOnChange, false);

			var btn = generalform.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];
			btn.addEventListener('click', function(){
				this.editImageButtonOnClick(btn, -1, true);
			}, false);
		}
	};

	this.replaceSelectionHandler = function (e) {
		var files = e.target.files;
		var f;

		if (files[0] instanceof File && !this.utils.contains(this.sender.preparedFiles, files[0])) {
			f = this.prepareFile(files[0]);
			this.imageEdit.processImages(f);


			var inputId = 'fileInput_uploadFiles_',
				index = e.target.id.substring(inputId.length),
				entry = this.sender.preparedFiles[index].entry;

			this.sender.preparedFiles[index] = f; //.isSizeOk ? f : null;
			this.sender.preparedFiles[index].entry = entry;

			if (this.imageEdit.EDITABLE_CONTENTTYPES.indexOf(f.type) !== -1) {
				this.imageEdit.processImages(this.sender.preparedFiles[index]);
			} else {
				this.view.repaintEntry(f);
			}

			if (f.isSizeOk) {
				if (!this.view.isUploadEnabled) {
					WE().layout.button.disable(this.view.elems.footer.document, 'upload', false);
					this.view.isUploadEnabled = true;
					this.sender.isCancelled = false;
				}
			}

			if(this.imageEdit.IS_MEMORY_MANAGMENT){
				this.imageEdit.memorymanagerRegister(f);
			}
		}
	};

	this.editFilename = function(e){
		var doc = this.uploader.doc;

		switch(e.target.attributes['data-name'].value){
			case 'showName_uploadFiles':
				var input = doc.getElementById('fuOpts_filenameInput_' + e.target.attributes['data-index'].value);
				e.target.style.display = 'none';
				doc.getElementById('editName_uploadFiles_' + e.target.attributes['data-index'].value).style.display = 'block';
				this.tmpName = input.value;
				input.focus();
				break;
			case 'fuOpts_filenameInput':
				var index = e.target.attributes['data-index'].value;
				var showName = doc.getElementById('showName_uploadFiles_' + index);

				switch(e.type){
					case 'change':
						var fileName = (e.target.value || this.tmpName) + '.' + showName.attributes['data-ext'].value;

						this.view.addTextCutLeft(showName, fileName, 230);
						this.sender.preparedFiles[index].fileName = fileName;
						break;
					case 'blur':
						doc.getElementById('editName_uploadFiles_' + index).style.display = 'none';
						showName.style.display = 'block';
						break;
				}
				break;
		}
	};

	this.customEditOptsOnChange = function(target, index){
		var form = this.uploader.doc.getElementById('form_editOpts_' + index);
		var pos = index; // this may be wrong!!
		var btnRefresh = form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];

		switch (target.name){
			case 'fuOpts_addRotationLeft':
			case 'fuOpts_addRotationRight':
					var rotation = parseInt(form.elements.fuOpts_rotate.value);
					var newRotation = (rotation + (target.name === 'fuOpts_addRotationLeft' ? 270 : 90)) % 360;
					form.elements.fuOpts_rotate.value = newRotation;
					this.view.previewSyncRotation(pos, newRotation);

					this.imageEdit.uneditImage(pos, false);
					if(!newRotation && !parseInt(this.imageEdit.imageEditOptions.scale)){ // if there is no other editing from general we set entry on doEdit=false
						this.view.setEditStatus('', pos, false, 'flip');
					} else {
						this.view.setEditStatus('notprocessed', pos, false, 'flip');
					}
					btnRefresh.disabled = false;
				break;
		}
	};
}
Fileupload_controller_import.prototype = Object.create(Fileupload_controller_abstract.prototype);
Fileupload_controller_import.prototype.constructor = Fileupload_controller_import;
