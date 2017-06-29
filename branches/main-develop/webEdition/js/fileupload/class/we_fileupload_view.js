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

function Fileupload_view_abstract(uploader) {
	this.uploader = uploader;

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
		parentElemId: 'progressbar'
	};
	this.previewSize = 116;
	this.useOriginalAsPreviewIfNotEdited = false;
	this.lastLoupIndex = -1;
	this.loupeVisible = false;

	this.init = function (conf) {
		this.controller = this.uploader.controller; // on init all components are initialized
		this.sender = this.uploader.sender;
		this.imageEdit = this.uploader.imageEdit;
		this.utils = this.uploader.utils;

		this.isDragAndDrop = typeof conf.isDragAndDrop !== 'undefined' ? conf.isDragAndDrop : this.isDragAndDrop;
		this.footerName = conf.footerName ? conf.footerName : this.footerName;
		if (typeof conf.intProgress === 'object') {
			this.intProgress.isIntProgress = conf.intProgress.isInternalProgress || this.intProgress.isIntProgress;
			this.intProgress.width = conf.intProgress.width || this.intProgress.width;
		}
		if (typeof conf.extProgress === 'object') {
			this.extProgress.isExtProgress = conf.extProgress.isExternalProgress || this.extProgress.isExtProgress;
			this.extProgress.parentElemId = conf.extProgress.parentElemId || this.extProgress.parentElemId;
			this.extProgress.name = conf.extProgress.name;
		}
	};

	this.onload = function (){
		var doc = this.uploader.doc;

		this.elems.fileSelect = doc.getElementById(uploader.fieldName);
		this.elems.fileDrag = doc.getElementById('div_' + uploader.fieldName + '_fileDrag');//FIXME: change to div_fileDrag
		this.elems.fileInputWrapper = doc.getElementById('div_' + uploader.fieldName + '_fileInputWrapper');//FIXME: change to div_fileInputWrapper

		this.elems.footer = this.footerName ? this.uploader.win.parent.frames[this.footerName] : this.uploader.win;
		this.elems.extProgressDiv = this.elems.footer.document.getElementById(this.extProgress.parentElemId);
		this.extProgress.isExtProgress = !this.elems.extProgressDiv ? false : this.extProgress.isExtProgress;

		this.onload_sub();
	};

	this.onload_sub = function (){
		// to be overridden
	};

	this.setImageEditMessage = function (){
		// to be overridden
	};

	this.unsetImageEditMessage = function (){
		// to be overridden
	};

	this.repaintImageEditMessage = function (){
		// to be overridden
	};

	this.repaintGUI = function (){
		// to be overridden
	};

	this.repaintEntry = function (){
		// to be overridden
	};

	this.setPreviewLoupe = function(fileobj, pt){
		var doc = this.uploader.doc;

		if(fileobj.isUploadStarted){
			return;
		}
		pt = pt ? pt : 0;
		if(pt === 1){
			var info = '',
				dimension = fileobj.img.fullPrev.height + 'x' + fileobj.img.fullPrev.width + ' px';
			if(fileobj.isEdited){
				var deg = parseInt(fileobj.img.processedOptions.rotate),
					degText = deg === 0 ? '' : (deg === 90 ? '90&deg; ' + WE().consts.g_l.fileupload.editRotationRight : (deg === 270 ? '90&deg; ' + WE().consts.g_l.fileupload.editRotationLeft : '180&deg;'));
				info += (fileobj.img.processedOptions.scale ? WE().consts.g_l.fileupload.editScaled + ' ' : '') + dimension;
				info += deg ? (info ? ', ' : '') + WE().consts.g_l.fileupload.editRotation + ': ' + degText : '';
				info += fileobj.img.processedOptions.quality !== this.imageEdit.OPTS_QUALITY_NEUTRAL_VAL && fileobj.type === 'image/jpeg' ? (info ? ', ' : '') + WE().consts.g_l.fileupload.editQuality + ': ' + fileobj.img.processedOptions.quality + '%' : '';
			} else {
				info = WE().consts.g_l.fileupload.editNotEdited + ': ' + dimension;
			}
			doc.getElementById('we_fileUpload_loupeInfo').innerHTML = info;

			fileobj.loupInner.innerHTML = ''; // be sure img is appended as firstChild!
			fileobj.loupInner.appendChild(fileobj.img.fullPrev);
			doc.getElementById('we_fileUpload_loupeInfo').style.display = 'block';
			doc.getElementById('we_fileUpload_spinner').style.display = 'none';
			doc.getElementsByClassName('editorCrosshairH')[0].style.display = 'block';
			doc.getElementsByClassName('editorCrosshairV')[0].style.display = 'block';
			fileobj.focusPoint = doc.getElementById('we_fileUpload_focusPoint');
			fileobj.focusPointFixed = doc.getElementById('editorFocuspointFixed');
			fileobj.focusPoint.style.display = 'block';
			return;
		}

		if(this.lastLoupIndex !== -1 && this.lastLoupIndex !== fileobj.index && this.sender.preparedFiles[this.lastLoupIndex]){
			// in importer we delete fullPreview of an fileobj when moving to an other file
			this.sender.preparedFiles[this.lastLoupIndex].img.fullPrev = null;
		}
		this.lastLoupIndex = fileobj.index;


		var mask = doc.getElementById('we_fileUploadImporter_mask');
		if(mask){
			mask.style.display = 'block';
		}

		if(!(fileobj.img.fullPrev || fileobj.dataUrl || fileobj.dataArray)){
			doc.getElementById('we_fileUpload_loupeFallback').innerHTML = 'Für dieses Bild bzw. für die aktuellen Bearbeitungsoptionen<br/>wurde noch keine Vorschau erstellt.';
			doc.getElementById('we_fileUpload_loupeFallback').style.display = 'block';
			this.loupeVisible = false;
			return;
		}
		this.loupeVisible = true;

		fileobj.loupInner = doc.getElementById('we_fileUpload_loupeInner');
		fileobj.loupInner.style.display = 'block';
		doc.getElementById('we_fileUpload_loupe').style.display = 'block';
		doc.getElementById('we_fileUpload_spinner').style.display = 'block';

		if(fileobj.img.fullPrev){
			this.setPreviewLoupe(fileobj, 1); // we always keep 1 rendered image in loupe
		} else if(fileobj.dataUrl || fileobj.dataArray){ // we have dataURL or dataArray
			fileobj.img.fullPrev = new Image();
			this.utils.logTimeFromStart('start load fullpreview', true);
			fileobj.img.fullPrev.onload = function(){
				this.utils.logTimeFromStart('end load fullpreview');
				this.setPreviewLoupe(fileobj, 1);
			};
			window.setTimeout(function () {
				/*
				var bin = '', base64 = '';
				if(!fileobj.dataUrl){
					for (var i = 0; i < fileobj.dataArray.byteLength; i++) {
						bin += String.fromCharCode(fileobj.dataArray[ i ]);
					}
					base64 = 'data:' + fileobj.type + ';base64,' + self.uploader.win.btoa(bin);
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
					self.setPreviewLoupe(fileobj, 1);
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
			if(e.timeStamp - this.lastklick < 10){
				// in Chrome onclick fires mosemove too: this causes the newly set focuspoint to be slightly wrong...
				return;
			}

			if(fileobj.loupInner && fileobj.loupInner.firstChild){
				var offsetLeft = (-fileobj.loupInner.firstChild.width / fileobj.img.previewCanvas.width * e.offsetX) + (fileobj.loupInner.parentNode.offsetWidth / 2);
				var offsetTop = (-fileobj.loupInner.firstChild.height / fileobj.img.previewCanvas.height * e.offsetY) + (fileobj.loupInner.parentNode.offsetHeight / 2);
				this.offesetLeft = offsetLeft;
				this.offsetTop = offsetTop;

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
		var doc = this.uploader.doc;

		this.loupeVisible = false;
		if(fileobj.loupInner){
			fileobj.loupInner.style.display = 'none';
			fileobj.loupInner.parentNode.style.display = 'none';
			fileobj.loupInner.innerHTML = '';
		}
		if(fileobj.focusPoint){
			fileobj.focusPoint.style.display = 'none';
			fileobj.focusPointFixed.style.display = 'none';
		}

		doc.getElementById('we_fileUpload_loupeInfo').style.display = 'none';
		doc.getElementById('we_fileUpload_loupeFallback').style.display = 'none';
		doc.getElementsByClassName('editorCrosshairH')[0].style.display = 'none';
		doc.getElementsByClassName('editorCrosshairV')[0].style.display = 'none';

		var mask = doc.getElementById('we_fileUploadImporter_mask');
		if(mask){
			mask.style.display = 'none';
		}
	};

	this.grabFocusPoint = function(e, fileobj){
		if(!this.loupeVisible){
			return;
		}
		this.lastklick = e.timeStamp;
		if(fileobj.img.previewCanvas.width && fileobj.img.previewCanvas.height){
			fileobj.focusPoint.style.display = 'none';
			fileobj.focusPointFixed.style.display = 'block';
			var focusX = ((e.offsetX / fileobj.img.previewCanvas.width) * 2) - 1;
			var focusY = ((e.offsetY / fileobj.img.previewCanvas.height) * 2) - 1;
			fileobj.img.focusX = focusX.toFixed(2);
			fileobj.img.focusY = focusY.toFixed(2);
			this.writeFocusToForm(fileobj);
			window.setTimeout(function () {
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
		this.uploader.doc.getElementById('span_' + uploader.fieldName + '_' + name + p).innerHTML = text;
	};

	this.replacePreviewCanvas = function() {
		// to be overridden
	};

	this.setInternalProgress = function (progress, index) {
		try{
			var coef = this.intProgress.width / 100,
				i = typeof index !== 'undefined' || index === false ? index : false,
				p = i === false ? '' : '_' + i;

			this.uploader.doc.getElementById(uploader.fieldName + '_progress_image_bg' + p).style.width = ((coef * 100) - (coef * progress)) + "px";
			this.uploader.doc.getElementById(uploader.fieldName + '_progress_image' + p).style.width = coef * progress + "px";

			this.setInternalProgressText('progress_text', progress + '%', index);
		} catch(e){}
	};

	this.setInternalProgressCompleted = function (success, index, txt) {
		var s = success || false,
				i = index || false,
				p = !i ? '' : '_' + i;

		if (s) {
			this.setInternalProgress(100, i);
			this.uploader.doc.getElementById(uploader.fieldName + '_progress_image').className = 'progress_finished';
		} else {
			this.uploader.doc.getElementById(uploader.fieldName + '_progress_image' + p).className = 'progress_failed';
		}
	};

	this.formEditOptsReset = function (form) {
		form.elements.fuOpts_scaleWhat.value = 'pixel_l';
		form.elements.fuOpts_scale.value = '';
		form.elements.fuOpts_rotate.value = 0;
		form.elements.fuOpts_quality.value = this.imageEdit.OPTS_QUALITY_NEUTRAL_VAL;
	};

	this.formCustomEditOptsSync = function () {
		// to be overridden
	};
}

function Fileupload_view_base(uploader) {
	Fileupload_view_abstract.call(this, uploader);

	this.uploader = uploader;
	this.uploadBtnName = '';
	this.isInternalBtnUpload = false;
	this.disableUploadBtnOnInit = false;

	this.onload = function(){
		var doc = this.uploader.doc;

		this.elems.message = doc.getElementById('div_' + this.fieldName + '_message');
		this.elems.progress = doc.getElementById('div_' + this.fieldName + '_progress');
		this.elems.progressText = doc.getElementById('span_' + this.fieldName + '_progress_text');
		this.elems.progressMoreText = doc.getElementById('span_' + this.fieldName + '_progress_more_text');
		this.elems.fileName = doc.getElementById('div_' + this.fieldName + '_fileName');
		this.elems.btnResetUpload = doc.getElementById('div_' + this.fieldName + '_btnResetUpload');
		this.elems.btnCancel = doc.getElementById('div_' + this.fieldName + '_btnCancel');
		this.repaintGUI({what: 'initGui'});
	};

	this.addFile = function (f) {
		var sizeText = f.isSizeOk ? WE().consts.g_l.fileupload.sizeTextOk + this.utils.computeSize(f.size) + ', ' :
						'<span style="color:red;">' + WE().consts.g_l.fileupload.sizeTextNok + '</span>';
		var typeText = f.isTypeOk ? WE().consts.g_l.fileupload.typeTextOk + f.type :
						'<span style="color:red;">' + WE().consts.g_l.fileupload.typeTextNok + f.type + '</span>';

		this.elems.message.innerHTML = sizeText + typeText;

		if (this.isDragAndDrop) {
			this.elems.fileDrag.innerHTML = f.file.name;
		} else {
			this.elems.fileName.innerHTML = f.file.name;
			this.elems.fileName.style.display = '';
		}
		WE().layout.button.disable(this.uploader.doc, 'reset_btn', false);
		WE().layout.button.disable(this.elems.footer.document, this.uploadBtnName, f.uploadConditionsOk ? false : true);
	};

	this.setDisplay = function (elem, val) { // move to abstract (from binDoc too)
		if (this.elems[elem]) {
			this.elems[elem].style.display = val;
		}
	};

	this.repaintGUI = function (arg) {
		var doc = this.uploader.doc;

		switch (arg.what) {
			case 'initGui' :
				WE().layout.button.disable(this.elems.footer.document, this.uploadBtnName, this.disableUploadBtnOnInit);
				return;
			case 'chunkOK' :
				var prog = (100 / this.sender.currentFile.size) * this.sender.currentFile.currentWeightFile,
								digits = this.sender.currentFile.totalParts > 1000 ? 2 : (this.sender.currentFile.totalParts > 100 ? 1 : 0);

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
					doc.getElementById(uploader.fieldName + '_progress_image').className = "progress_image";
					this.elems.progress.style.display = '';
					this.elems.progressMoreText.style.display = '';
					this.elems.progressMoreText.innerHTML = '&nbsp;&nbsp;/ ' + this.utils.computeSize(this.sender.currentFile.size);
				}
				if (this.extProgress.isExtProgress) {
					this.elems.footer.setProgress(this.extProgress.name, 0);
					this.elems.extProgressDiv.style.display = '';
				}
				WE().layout.button.disable(this.uploader.doc, 'reset_btn', true);
				WE().layout.button.disable(this.uploader.doc, 'browse_harddisk_btn', true);
				if (this.isInternalBtnUpload) {
					WE().layout.button.disable(this.elems.footer.document, this.uploadBtnName, true);
					this.setDisplay('btnResetUpload', 'none');
					this.setDisplay('btnCancel', 'inline-block');
				}
				return;
			case 'cancelUpload' :
				//self.elems.footer['setProgressText' + self.extProgress.name]('progress_text', WE().consts.g_l.fileupload.cancelled);
				if (this.elems.progress) {
					this.setInternalProgressCompleted(false, false, WE().consts.g_l.fileupload.cancelled);
				}
				WE().layout.button.disable(this.uploader.doc, 'reset_btn', false);
				WE().layout.button.disable(this.uploader.doc, 'browse_harddisk_btn', false);
				return;
			case 'resetGui' :
				this.sender.preparedFiles = [];
				this.currentFile = -1;

				if(!this.elems.message){
					doc.getElementById('div_' + uploader.fieldName + '_message');
				}
				this.elems.message.innerHTML = '';
				this.elems.message.innerHTML.display = 'none';
				if (this.elems.fileDrag) {
					this.elems.fileDrag.innerHTML = WE().consts.g_l.fileupload.dropText;
				}
				if (this.elems.progress) {
					this.setInternalProgress(0);
					this.elems.progress.style.display = 'none';
				}
				if (this.extProgress.isExtProgress) {
					this.elems.footer.setProgress(this.extProgress.name, 0);
					this.elems.extProgressDiv.style.display = 'none';
				}
				WE().layout.button.disable(this.uploader.doc, 'reset_btn', true);
				WE().layout.button.disable(this.uploader.doc, 'browse_harddisk_btn', false);
				if (this.isInternalBtnUpload) {
					WE().layout.button.disable(this.elems.footer.document, this.uploadBtnName, true);
					this.setDisplay('btnResetUpload', 'inline-block');
					this.setDisplay('btnCancel', 'none');
				}
				return;
			default :
				return;
		}
	};
}
Fileupload_view_base.prototype = Object.create(Fileupload_view_abstract.prototype);
Fileupload_view_base.prototype.constructor = Fileupload_view_base;

function Fileupload_view_bindoc(uploader) {
	Fileupload_view_abstract.call(this, uploader);

	this.uploader = uploader;

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

	this.onload_sub = function () {
		var doc = this.uploader.doc;

		this.elems.fileDrag_state_0 = doc.getElementById('div_fileupload_fileDrag_state_0');
		this.elems.fileDrag_state_1 = doc.getElementById('div_fileupload_fileDrag_state_1');
		this.elems.fileDrag_mask = doc.getElementById('div_' + uploader.fieldName + '_fileDrag');
		this.elems.dragInnerRight = doc.getElementById('div_upload_fileDrag_innerRight');
		this.elems.divRight = doc.getElementById('div_fileupload_right');
		this.elems.txtFilename = doc.getElementById('span_fileDrag_inner_filename');
		this.elems.txtFilename_1 = doc.getElementById('span_fileDrag_inner_filename_1');//??
		this.elems.txtSize = doc.getElementById('span_fileDrag_inner_size');
		this.elems.txtType = doc.getElementById('span_fileDrag_inner_type');
		this.elems.txtEdit= doc.getElementById('span_fileDrag_inner_edit');
		this.elems.divBtnReset = doc.getElementById('div_fileupload_btnReset');
		this.elems.divBtnCancel = doc.getElementById('div_fileupload_btnCancel');
		this.elems.divBtnUpload = doc.getElementById('div_fileupload_btnUpload');
		this.elems.divProgressBar = doc.getElementById('div_fileupload_progressBar');
		this.elems.divButtons = doc.getElementById('div_fileupload_buttons');

		this.spinner = doc.createElement("i");
		this.spinner.className = "fa fa-2x fa-spinner fa-pulse";
	};

	this.addFile = function (f) {
		var doc = this.uploader.doc;
		var sizeText = f.isSizeOk ? WE().consts.g_l.fileupload.sizeTextOk + this.utils.computeSize(f.size) + ', ' :
				'<span style="color:red;">' + WE().consts.g_l.fileupload.sizeTextNok + '</span>';
		var typeText = f.isTypeOk ? WE().consts.g_l.fileupload.typeTextOk + (f.isTypeOk === 1 ? f.type : f.file.name.split('.').pop().toUpperCase()) :
				'<span style="color:red;">' + WE().consts.g_l.fileupload.typeTextNok + f.type + '</span>';

		this.elems.fileDrag.style.backgroundColor = f.isEdited ? 'rgb(216, 255, 216)' : 'rgb(232, 232, 255)';
		this.elems.fileDrag.style.backgroundImage = 'none';

		var fn = f.file.name;
		var fe = '';
		if (fn.length > 27) {
			var farr = fn.split('.');
			fe = farr.pop();
			fn = farr.join('.');
			fn = fn.substr(0, 18) + '...' + fn.substring((fn.length - 2), fn.length) + '.';
		}

		this.elems.txtFilename.innerHTML = fn + fe;
		//self.elems.fileDrag_mask.title = f.file.name;
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

			self.elems.txtEdit.innerHTML = '<strong>Skaliert</strong> auf ' + edittext;
			self.elems.txtEdit.style.display = 'block';
			*/
		} else {
			this.elems.txtEdit.style.display = 'none';
		}
		this.setDisplay('fileDrag_state_0', 'none');
		this.setDisplay('fileDrag_state_1', 'block');
		this.elems.dragInnerRight.innerHTML = '';

		if (f.uploadConditionsOk) {
			this.sender.isAutostartPermitted = true;
			this.controller.setEditorIsHot();
		} else {
			this.sender.isAutostartPermitted = false;
		}

		if (f.type.search("image/") !== -1){
			if(f.img.previewImg){
				this.preview = f.img.previewImg;
				this.elems.dragInnerRight.innerHTML = '';
				this.elems.dragInnerRight.appendChild(this.preview);
			} else if(f.img.previewCanvas){
				this.preview = f.img.previewCanvas;
				this.elems.dragInnerRight.innerHTML = '';
				this.elems.dragInnerRight.appendChild(this.preview);
			}
			this.setGuiState(f.uploadConditionsOk ? this.STATE_PREVIEW_OK : this.STATE_PREVIEW_NOK);
			if(f.type !== 'image/jpeg'){
				//doc.getElementsByClassName('optsQuality')[0].value = 50;
				doc.getElementsByClassName('optsQuality')[0].disabled = true;
				doc.getElementsByClassName('qualityIconRight')[0].style.color = 'lightgray';
			} else {
				//doc.getElementsByClassName('optsQuality')[0].value = f.img.editOptions.quality ? f.img.editOptions.quality : self.imageEdit.OPTS_QUALITY_NEUTRAL_VAL;
				doc.getElementsByClassName('optsQuality')[0].disabled = false;
				doc.getElementsByClassName('qualityIconRight')[0].style.color = 'black';
			}
			doc.getElementsByClassName('weFileupload_btnImgEditRefresh')[0].disable = false;
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
				if (uploader.EDIT_IMAGES_CLIENTSIDE) {
					doc.getElementById('make_preview_weFileupload').disabled = true;//make same as following
				}
				*/
				WE().layout.button.disable(this.uploader.doc, this.uploadBtnName, true);
				WE().layout.button.disable(this.uploader.doc, 'browse_harddisk_btn', false);
				return;
			case this.STATE_PREVIEW_OK:
				this.setDisplay('fileInputWrapper', 'none');
				this.setDisplay('divBtnReset', '');
				WE().layout.button.disable(this.uploader.doc, this.uploadBtnName, false);
				WE().layout.button.disable(this.uploader.doc, 'reset_btn', false);
				return;
			case this.STATE_PREVIEW_NOK:
				this.setDisplay('fileInputWrapper', 'none');
				this.setDisplay('divBtnReset', '');
				WE().layout.button.disable(this.uploader.doc, this.uploadBtnName, true);
				WE().layout.button.disable(this.uploader.doc, 'reset_btn', false);
				return;
			case this.STATE_UPLOAD:
				//WE().layout.button.disable(self.uploader.doc, self.uploadBtnName, true);
				//WE().layout.button.disable(self.uploader.doc, 'reset_btn', true);
				//WE().layout.button.disable(self.uploader.doc, 'browse_harddisk_btn', true);
				this.setDisplay('fileInputWrapper', 'none');
				if (uploader.uiType !== 'wedoc') {
					this.setDisplay('divBtnReset', 'none');
				}
				this.setDisplay('divBtnUpload', 'none');
				this.setDisplay('divBtnReset', 'none');
				this.setDisplay('divProgressBar', '');
				this.setDisplay('divBtnCancel', '');
				if (this.preview) {
					this.preview.style.opacity = 0.05;
				}
		}
	};

	this.repaintGUI = function (arg) {
		var cur = this.sender.currentFile,
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
				this.sender.preparedFiles = [];
				if (this.preview) {
					this.preview.style.opacity = 1;
				}
				return;
			case 'startSendFile' :
				this.setInternalProgress(0);
				this.setGuiState(this.STATE_UPLOAD);
				return;
			case 'chunkNOK' :
				this.sender.processError({from: 'gui', msg: arg.message});
				/* falls through */
			case 'initGui' :
			case 'fileNOK' :
			case 'cancelUpload' :
			case 'resetGui' :
				/* falls through */
			default:
				this.sender.preparedFiles = [];
				this.sender.currentFile = -1;
				this.setInternalProgress(0);
				this.setGuiState(this.STATE_RESET);
				return;
		}
	};

	this.previewSyncRotation = function(pos, rotation){
		if(this.sender.preparedFiles.length){
			this.imageEdit.processimageRotatePreview(this.sender.preparedFiles[0], rotation);
			this.replacePreviewCanvas(this.sender.preparedFiles[0]);
		}
	};

	this.setEditStatus = function(state){
		var doc = this.uploader.doc;
		var fileobj = this.sender.preparedFiles.length ? this.sender.preparedFiles[0] : null;
		var btn = doc.getElementsByClassName('weFileupload_btnImgEditRefresh ')[0];

		state = !fileobj ? 'empty' : state;
		state = state ? state : !fileobj ? 'empty' : (fileobj.isEdited ? 'processed' : (fileobj.img.editOptions.doEdit ? 'notprocessed' : 'donotedit'));

		if (this.uploader.uiType === 'wedoc') {
			this.hideOptions(false);
		}

		switch(state){
			case 'notprocessed':
				if(this.sender.preparedFiles.length){
					this.elems.fileDrag.style.backgroundColor = '#ffffff';
					this.elems.fileDrag.style.backgroundColor = 'rgb(244, 255, 244)'; //'rgb(rgb(244, 255, 244))';//notprocessed
					//self.elems.fileDrag.style.backgroundImage = 'repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255, 255, 255,1.0) 5px, rgba(216,255,216,.5) 10px)';
					this.elems.txtSize.innerHTML = WE().consts.g_l.fileupload.sizeTextOk + '--';
					btn.disabled = false;
				}
				break;
			case 'processed':
				if(this.sender.preparedFiles.length){
					this.elems.fileDrag.style.backgroundColor = 'rgb(216, 255, 216)';
					this.elems.fileDrag.style.backgroundImage =  'none';
					btn.disabled = true;
				}
				break;
			case 'donotedit':
				this.elems.fileDrag.style.backgroundColor = 'rgb(232, 232, 255)';
				this.elems.fileDrag.style.backgroundImage =  'none';
				btn.disabled = false;
				break;
			case 'empty':
				this.elems.fileDrag.style.backgroundColor = 'white';
				this.elems.fileDrag.style.backgroundImage =  'none';
				btn.disabled = true;
		}
		if(fileobj && fileobj.img.tooSmallToScale){
			doc.getElementsByName('fuOpts_scale')[0].style.color = '#aaaaaa';
			doc.getElementsByClassName('optsRowScaleHelp')[0].style.display = 'block';
		} else {
			doc.getElementsByName('fuOpts_scale')[0].style.color = 'black';
			doc.getElementsByClassName('optsRowScaleHelp')[0].style.display = 'none';
		}
	};

	this.hideOptions = function(hide){
		var doc = this.uploader.doc;
		if(hide){
			doc.getElementById('editImage').style.display = 'none';
			doc.getElementById('div_importMeta').style.display = 'none';
			doc.getElementById('tr_alert').style.display = 'none';
		} else {
			doc.getElementById('editImage').style.display = 'block';
			doc.getElementById('div_importMeta').style.display = 'block';
			doc.getElementById('tr_alert').style.display = 'table-row';
		}
	};

	this.disableCustomEditOpts = function () {
		// to be overridden
	};

	this.writeFocusToForm = function(fileobj){
		if (!uploader.EDIT_IMAGES_CLIENTSIDE) {
			return;
		}

		var doc = this.uploader.doc;

		doc.we_form.elements.fu_doc_focusX.value = fileobj.img.focusX;
		doc.we_form.elements.fu_doc_focusY.value = fileobj.img.focusY;
	};

	//TODO: use progress fns from abstract after adapting them to standard progress
	this.setInternalProgress = function (progress, index) {
		var coef = this.intProgress.width / 100;
		var mt = typeof this.sender.currentFile === 'object' ? ' / ' + this.utils.computeSize(this.sender.currentFile.size) : '';
		var doc = this.uploader.doc;

		doc.getElementById('progress_image_fileupload').style.width = coef * progress + "px";
		doc.getElementById('progress_image_bg_fileupload').style.width = (coef * 100) - (coef * progress) + "px";
		doc.getElementById('progress_text_fileupload').innerHTML = progress + '%' + mt;
	};

	this.setDisplay = function (elem, val) {
		if (this.elems[elem]) {
			this.elems[elem].style.display = val;
		}
	};

	this.setImageEditMessage = function (singleMode) {
		if (!uploader.EDIT_IMAGES_CLIENTSIDE) {
			return;
		}

		var mask = this.uploader.doc.getElementById('div_fileupload_fileDrag_mask'),
			text = this.uploader.doc.getElementById('image_edit_mask_text');

		mask.style.display = 'block';
		text.innerHTML = singleMode ? WE().consts.g_l.fileupload.maskProcessImage : WE().consts.g_l.fileupload.maskReadImage;
	};

	this.unsetImageEditMessage = function () {
		if (!uploader.EDIT_IMAGES_CLIENTSIDE) {
			return;
		}
		var mask = this.uploader.doc.getElementById('div_fileupload_fileDrag_mask');
		mask.style.display = 'none';
	};

	this.repaintImageEditMessage = function (empty, changeText) {
		if (!uploader.EDIT_IMAGES_CLIENTSIDE) {
			return;
		}

		var text = this.uploader.doc.getElementById('image_edit_mask_text').innerHTML;
		text = (changeText ? WE().consts.g_l.fileupload.maskProcessImage : text) + '.';
		text += '.';
		this.uploader.doc.getElementById('image_edit_mask_text').innerHTML = text;

	};

	this.repaintEntry = function (fileobj) {
		this.addFile(fileobj);
		if (!uploader.EDIT_IMAGES_CLIENTSIDE) {
			return;
		}
		this.replacePreviewCanvas(fileobj);
		this.setEditStatus();
	};

	this.formCustomEditOptsSync = function(){
		//
	};

	this.replacePreviewCanvas = function(fileobj) {
		this.elems.dragInnerRight.innerHTML = '';
		this.elems.dragInnerRight.appendChild(fileobj.img.previewCanvas);

		this.elems.dragInnerRight.firstChild.addEventListener('mouseenter', function(){this.setPreviewLoupe(fileobj);}, false);
		this.elems.dragInnerRight.firstChild.addEventListener('mousemove', function(e){this.movePreviewLoupe(e, fileobj);}, false);
		this.elems.dragInnerRight.firstChild.addEventListener('mouseleave', function(){this.unsetPreviewLoupe(fileobj);}, false);
		this.elems.dragInnerRight.firstChild.addEventListener('click', function(e){this.grabFocusPoint(e,fileobj);}, false);
	};
}
Fileupload_view_bindoc.prototype = Object.create(Fileupload_view_abstract.prototype);
Fileupload_view_bindoc.prototype.constructor = Fileupload_view_bindoc;

function Fileupload_view_import(uploader) {
	Fileupload_view_abstract.call(this, uploader);

	this.fileTable = '';
	this.htmlFileRow = '';
	this.nextTitleNr = 1;
	this.isUploadEnabled = false;
	this.messageWindow = null;
	this.previewSize = 110;
	//self.useOriginalAsPreviewIfNotEdited = true;

	this.addFile = function (f, index) {
		this.appendRow(f, this.sender.preparedFiles.length - 1);
	};

	this.repaintEntry = function (fileobj) { // TODO: get rid of fileobj.entry
		if(!fileobj.entry){
			fileobj.entry = this.uploader.doc.getElementById('div_uploadFiles_' + fileobj.index);
			if(!fileobj.entry){
				this.uploader.win.console.log('an error occured: fileobj.entry is undefined');
				return;
			}
		}

		fileobj.entry.getElementsByClassName('elemSize')[0].innerHTML = (fileobj.isSizeOk ? this.utils.computeSize(fileobj.size) : '<span style="color:red">> ' + ((this.sender.maxUploadSize / 1024) / 1024) + ' MB</span>');

		if(this.imageEdit.EDITABLE_CONTENTTYPES.indexOf(fileobj.type) !== -1){
			fileobj.entry.getElementsByClassName('elemIcon')[0].style.display = 'none';
			fileobj.entry.getElementsByClassName('elemPreview')[0].style.display = 'block';
			fileobj.entry.getElementsByClassName('elemContentBottom')[0].style.display = 'block';
			fileobj.entry.getElementsByClassName('rowDimensionsOriginal')[0].innerHTML = (fileobj.img.origWidth ? fileobj.img.origWidth + ' x ' + fileobj.img.origHeight + ' px': '--') + ', ' + this.utils.computeSize(fileobj.img.originalSize);
			//fileobj.entry.getElementsByClassName('optsQualitySlide')[0].style.display = fileobj.type === 'image/jpeg' ? 'block' : 'none';
			this.replacePreviewCanvas(fileobj);
			//self.formCustomEditOptsSync(fileobj.index, false);
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

		elem.firstChild.addEventListener('mouseenter', function(){this.setPreviewLoupe(fileobj);}, false);
		elem.firstChild.addEventListener('mousemove', function(e){this.movePreviewLoupe(e, fileobj);}, false);
		elem.firstChild.addEventListener('mouseleave', function(){this.unsetPreviewLoupe(fileobj);}, false);
		elem.firstChild.addEventListener('click', function(e){this.grabFocusPoint(e,fileobj);}, false);
	};

	this.setImageEditMessage = function (singleMode, index) {
		var row, elem;
		var doc = this.uploader.doc;

		if(singleMode && (row = doc.getElementById('div_uploadFiles_' + index))){
			row.getElementsByClassName('elemContentTop')[0].style.display = 'none';
			row.getElementsByClassName('elemContentBottom')[0].style.display = 'none';
			row.getElementsByClassName('elemContentMask')[0].style.display = 'block';
			row.getElementsByClassName('we_file_drag_maskBusyText')[0].innerHTML = WE().consts.g_l.fileupload.maskProcessImage;
			return;
		}

		if((elem = doc.getElementById('we_fileUploadImporter_mask'))){
			doc.getElementById('we_fileUploadImporter_busyText').innerHTML = this.imageEdit.imageEditOptions.doEdit ? WE().consts.g_l.fileupload.maskImporterProcessImages : WE().consts.g_l.fileupload.maskImporterReadImages;
			try{
				doc.getElementById('we_fileUploadImporter_messageNr').innerHTML = this.imageEdit.imageFilesToProcess.length;
			} catch (e) {
			}
			doc.getElementById('we_fileUploadImporter_busyMessage').style.display = 'block';
			doc.getElementById('we_fileUploadImporter_busyMessage').style.zIndex = 800;
			elem.style.display = 'block';
		}
	};

	this.unsetImageEditMessage = function (singleMode, index) {
		var row, elem;
		var doc = this.uploader.doc;

		if(singleMode && (row = doc.getElementById('div_uploadFiles_' + index))){
			row.getElementsByClassName('elemContentTop')[0].style.display = 'block';
			row.getElementsByClassName('elemContentBottom')[0].style.display = 'block';
			row.getElementsByClassName('elemContentMask')[0].style.display = 'none';
			return;
		}

		if((elem = doc.getElementById('we_fileUploadImporter_mask'))){
			elem.style.display = 'none';
			doc.getElementById('we_fileUploadImporter_busyMessage').style.display = 'none';
		}
	};

	this.repaintImageEditMessage = function(step, singleMode, index) {
		var row;
		var doc = this.uploader.doc;

		try{
			if(step){
				if(false && singleMode && (row = doc.getElementById('div_uploadFiles_' + index))){
					row.getElementsByClassName('we_file_drag_maskBusyText')[0].innerHTML += this.imageEdit.imageEditOptions.doEdit ? '.' : '';
					return;
				}

				doc.getElementById('we_fileUploadImporter_busyText').innerHTML += this.imageEdit.imageEditOptions.doEdit ? '.' : '';
			} else {
				doc.getElementById('we_fileUploadImporter_busyText').innerHTML = this.imageEdit.imageEditOptions.doEdit ? WE().consts.g_l.fileupload.maskImporterProcessImages : WE().consts.g_l.fileupload.maskImporterReadImages;
				doc.getElementById('we_fileUploadImporter_messageNr').innerHTML = this.imageEdit.imageFilesToProcess.length;
			}
		} catch (e) {
		}
	};

	this.appendRow = function (f, index) {
		var doc = this.uploader.doc;
		var div, entry;
		var parts = f.file.name.split('.');
		var row = this.htmlFileRow.replace(/WEFORMNUM/g, index).replace(/WE_FORM_NUM/g, (this.nextTitleNr++)).
				replace(/FILENAME/g, (f.file.name)).
				replace(/FNAME/g, (parts[0])).
				replace(/FENDING/g, (parts[1])).
				replace(/FILESIZE/g, (f.isSizeOk ? this.utils.computeSize(f.size) : '<span style="color:red">> ' + ((this.sender.maxUploadSize / 1024) / 1024) + ' MB</span>'));

		this.uploader.win.weAppendMultiboxRow(row, '', 0, 0, 0, -1);
		entry = this.uploader.doc.getElementById('div_uploadFiles_' + index);

		div = doc.getElementById('div_upload_files');
		//div.scrollTop = getElementById('div_upload_files').div.scrollHeight;
//		doc.getElementById('fileInput_uploadFiles_' + index).addEventListener('change', self.controller.replaceSelectionHandler, false);

		this.addTextCutLeft(doc.getElementById('showName_uploadFiles_' + index), f.file.name, 230);

		if(uploader.EDIT_IMAGES_CLIENTSIDE){
			//doc.getElementById('customOpts_' + index).style.display = self.imageEdit.isImageEditActive ? 'inline-block' : 'none';
			if(this.imageEdit.EDITABLE_CONTENTTYPES.indexOf(f.type) !== -1){
				doc.getElementById('icon_uploadFiles_' + index).style.display = 'none';
				doc.getElementById('preview_uploadFiles_' + index).style.display = 'block';
				doc.getElementById('editoptions_uploadFiles_' + index).style.display = 'block';
			} else {
				var ext = f.file.name.substr(f.file.name.lastIndexOf('.') + 1).toUpperCase();
				doc.getElementById('icon_uploadFiles_' + index).innerHTML = WE().util.getTreeIcon(f.type) + ' ' + ext;
			}
		}

		this.elems.extProgressDiv.style.display = 'none';

		WE().layout.button.display(this.elems.footer.document, 'close', false);
		WE().layout.button.display(this.elems.footer.document, 'cancel', true);

		if (f.isSizeOk) {
			if (!this.isUploadEnabled) {
				WE().layout.button.disable(this.elems.footer.document, 'upload', false);
				WE().layout.button.disable(this.uploader.doc, 'reset_btn', false);
				this.isUploadEnabled = true;
				this.sender.isCancelled = false;
			}
		} else {
			this.sender.preparedFiles[index] = null;
		}

		f.index = index;
		f.entry = doc.getElementById('div_uploadFiles_' + index);

		var form = doc.getElementById('form_editOpts_' + index);

		form.elements.fuOpts_filenameInput.addEventListener('change', this.controller.editFilename, false);
		form.elements.fuOpts_filenameInput.addEventListener('blur', this.controller.editFilename, false);
		doc.getElementById('showName_uploadFiles_' + index).addEventListener('click', this.controller.editFilename, false);

		var btn = form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];
		btn.addEventListener('click', function(){this.controller.editImageButtonOnClick(btn, index, false);}, false);

		var rotLeft = form.getElementsByClassName('fuOpts_addRotationLeft')[0];
		rotLeft.addEventListener('click', function(){this.controller.customEditOptsOnChange(rotLeft, index, false);}, false);

		var rotRight = form.getElementsByClassName('fuOpts_addRotationRight')[0];
		rotRight.addEventListener('click', function(){this.controller.customEditOptsOnChange(rotRight, index, false);}, false);

		form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseenter', function(e){this.controller.editOptionsHelp(e.target, 'enter');}, false);
		form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseleave', function(e){this.controller.editOptionsHelp(e.target, 'leave');}, false);

	};

	this.deleteRow = function (index, button) {
		var prefix = 'div_uploadFiles_', num = 0, z = 1, i, sp;
		var divs = this.uploader.doc.getElementsByTagName('DIV');

		this.imageEdit.memorymanagerUnregister(this.sender.preparedFiles[index]);
		this.sender.preparedFiles[index] = null;

		this.uploader.win.weDelMultiboxRow(index);

		for (i = 0; i < divs.length; i++) {
			if (divs[i].id.length > prefix.length && divs[i].id.substring(0, prefix.length) === prefix) {
				num = divs[i].id.substring(prefix.length, divs[i].id.length);
				sp = this.uploader.doc.getElementById('headline_uploadFiles_' + num);
				if (sp) {
					sp.innerHTML = z;
				}
				z++;
			}
		}
		this.nextTitleNr = z;
		if (!this.utils.containsFiles(this.sender.preparedFiles)) {
			WE().layout.button.disable(this.elems.footer.document, 'upload', true);
			WE().layout.button.disable(this.uploader.doc, 'reset_btn', true);
			this.isUploadEnabled = false;
		}
	};

	this.reloadOpener = function () {
		try {
			var activeFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();
			if (parseInt(this.uploader.doc.we_form.fu_file_parentID.value) === activeFrame.EditorDocumentId && activeFrame.EditorEditPageNr === 16) {
				top.we_cmd('switch_edit_page', 16, activeFrame.EditorTransaction);
			}
			top.we_cmd('loadIfActive', WE().consts.tables.FILE_TABLE);
		} catch (e) {
			//
		}
	};

	this.repaintGUI = function (arg) {
		var i, j, fileProg = 0, totalProg = 0, digits = 0;
		var sender = this.sender;
		var cur = sender.currentFile;
		var totalDigits = sender.totalChunks > 1000 ? 2 : (sender.totalChunks > 100 ? 1 : 0);
		var doc = this.uploader.doc;

		switch (arg.what) {
			case 'startSendFile':
				i = sender.mapFiles[cur.fileNum];
				if(this.imageEdit.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1){
					doc.getElementById('image_edit_done_' + i).style.display = 'block';
				}
				doc.getElementById('showName_uploadFiles_'  + i).removeEventListener('click', this.controller.editFilename);
				break;
			case 'chunkOK' :
				digits = cur.totalParts > 1000 ? 2 : (cur.totalParts > 100 ? 1 : 0);//FIXME: make fn on UtilsAbstract
				fileProg = (100 / cur.size) * cur.currentWeightFile;
				totalProg = (100 / sender.totalWeight) * sender.currentWeight;
				i = sender.mapFiles[cur.fileNum];
				j = i + 1;
				this.setInternalProgress(fileProg.toFixed(digits), i);
				if (cur.partNum === 1) {
					this.elems.footer.setProgressText('progress_title', WE().consts.g_l.fileupload.doImport + ' ' + WE().consts.g_l.fileupload.file + ' ' + j);
				}
				this.elems.footer.setProgress("", totalProg.toFixed(totalDigits));
				return;
			case 'fileOK' :
				i = sender.mapFiles[cur.fileNum];
				try {
					doc.getElementById('div_upload_files').scrollTop = doc.getElementById('div_uploadFiles_' + i).offsetTop - 360;
				} catch (e) {}
				this.setInternalProgressCompleted(true, i, '');
				return;
			case 'chunkNOK' :
				totalProg = (100 / sender.totalWeight) * sender.currentWeight;
				i = sender.mapFiles[cur.fileNum];
				j = i + 1;
				try {
					doc.getElementById('div_upload_files').scrollTop = doc.getElementById('div_uploadFiles_' + i).offsetTop - 200;
				} catch (e) {
				}
				this.setInternalProgressCompleted(false, i, arg.message);
				if (cur.partNum === 1) {
					this.elems.footer.setProgressText('progress_title', WE().consts.g_l.fileupload.doImport + ' ' + WE().consts.g_l.fileupload.file + ' ' + j);
				}
				this.elems.footer.setProgress("", totalProg.toFixed(totalDigits));
				return;
			case 'startUpload' :
				//set buttons state and show initial progress bar
				WE().layout.button.disable(this.elems.footer.document, 'back', true);
				WE().layout.button.disable(this.elems.footer.document, 'upload', true);
				WE().layout.button.disable(this.uploader.doc, 'reset_btn', true);
				WE().layout.button.disable(this.uploader.doc, 'browse_harddisk_btn', true);
				this.isUploadEnabled = false;
				this.elems.footer.document.getElementById('progressbar').style.display = '';
				this.elems.footer.setProgressText('progress_title', WE().consts.g_l.fileupload.doImport + ' ' + WE().consts.g_l.fileupload.file + ' 1');
				try {
					doc.getElementById('div_upload_files').scrollTop = 0;
				} catch (e) {
				}

				return;
			case 'cancelUpload' :
				i = sender.mapFiles[cur.fileNum];
				this.setInternalProgressCompleted(false, sender.mapFiles[cur.fileNum], WE().consts.g_l.fileupload.cancelled);
				try {
					doc.getElementById('div_upload_files').scrollTop = doc.getElementById('div_uploadFiles_' + i).offsetTop - 200;
				} catch (e) {
				}
				for (j = 0; j < sender.uploadFiles.length; j++) {
					var file = sender.uploadFiles[j];
					this.setInternalProgressCompleted(false, sender.mapFiles[file.fileNum], WE().consts.g_l.fileupload.cancelled);
				}
				WE().layout.button.disable(this.uploader.doc, 'reset_btn', false);
				WE().layout.button.disable(this.uploader.doc, 'browse_harddisk_btn', false);
				return;
			case 'resetGui' :
				try {
					doc.getElementById('td_uploadFiles').innerHTML = '';
				} catch (e) {
				}
				this.sender.preparedFiles = [];
				this.nextTitleNr = 1;
				this.isUploadEnabled = false;
				WE().layout.button.disable(this.elems.footer.document, 'upload', true);
				WE().layout.button.disable(this.elems.footer.document, 'back', false);
				this.sender.resetSender();
				return;
			default :
				return;
		}
	};

	this.setInternalProgressCompleted = function (success, index, txt) {
		var doc = this.uploader.doc;

		if (success) {
			this.setInternalProgress(100, index);
			if (doc.getElementById(uploader.fieldName + '_progress_image_' + index)) {
				doc.getElementById(uploader.fieldName + '_progress_image_' + index).className = 'progress_finished';
			}
		} else {
			if (typeof doc.images['alert_img_' + index] !== 'undefined') {
				doc.images['alert_img_' + index].style.visibility = 'visible';
				doc.images['alert_img_' + index].title = txt;
			}
			if (doc.getElementById(uploader.fieldName + '_progress_image_' + index)) {
				doc.getElementById(uploader.fieldName + '_progress_image_' + index).className = 'progress_failed';
			}
		}
	};

	this.formCustomEditOptsSync = function (pos, general, initRotation) {
		if(initRotation){
			var indices;

			pos = general ? -1 : (pos && pos !== -1 ? pos : -1);
			indices = this.imageEdit.getImageEditIndices(pos, general, true);

			for(var i = 0; i < indices.length; i++){
				this.uploader.doc.getElementById('fuOpts_rotate_' + indices[i]).value = this.uploader.doc.we_form.elements.fuOpts_rotate.value;
			}
		}
	};

	this.disableCustomEditOpts = function (disable) {
			var customEdits = this.uploader.doc.getElementsByClassName('btnRefresh');

			for(var i = 0; i < customEdits.length; i++){
				customEdits[i].style.display = disable ? 'none' : 'inline-block';
			}
	};

	this.previewSyncRotation = function(pos, rotation){
		var indices = this.imageEdit.getImageEditIndices(pos, pos === -1, false);

		for(var i = 0; i < indices.length; i++){
			this.imageEdit.processimageRotatePreview(this.sender.preparedFiles[indices[i]], rotation);
			this.replacePreviewCanvas(this.sender.preparedFiles[indices[i]]);
		}
	};

	this.setEditStatus = function(preset, pos, general, setDimensions){
		var doc = this.uploader.doc;
		var divUploadFiles = doc.getElementById('div_upload_files');
		var indices = this.imageEdit.getImageEditIndices(pos, general, true);
		var elems = doc.getElementsByClassName('elemContentBottom');
		var sizes = doc.getElementsByClassName('weFileUploadEntry_size');
		var buttons = doc.getElementsByClassName('rowBtnProcess');
		var asteriskes = doc.getElementsByClassName('rowEditHot');
		var infoTops = doc.getElementsByClassName('rowDimensionsProcessed');
		var infoMiddles = doc.getElementsByClassName('infoMiddle');
		var infoMiddlesRight = doc.getElementsByClassName('rowRotation');
		var infoBottoms = doc.getElementsByClassName('infoBottom');
		// var scaleInputs = doc.getElementsByClassName('optsScaleInput_row');
		var scaleHelp = divUploadFiles.getElementsByClassName('optsRowScaleHelp');
		var fileobj, i, j, state, deg, dimensions;

		for(i = 0; i < indices.length; i++){
			j = indices[i];
			fileobj = this.sender.preparedFiles[j];
			state = preset ? preset : (fileobj.isEdited ? 'processed' : (fileobj.img.editOptions.doEdit ? 'notprocessed' : 'donotedit'));

			switch(state){
				case 'notprocessed':
						elems[j].style.backgroundColor = '#ffffff';
						elems[j].style.backgroundColor = 'rgb(244, 255, 244)';
						//elems[j].style.backgroundImage = 'repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255, 255, 255,1.0) 5px, rgba(244, 255, 244,.5) 10px)';
						sizes[j].innerHTML = WE().consts.g_l.fileupload.sizeTextOk + '--';

						if(setDimensions === 'flip'){
							switch (fileobj.img.editOptions.scaleWhat) {
								case 'pixel_l':
									dimensions = fileobj.img.lastHeightShown + ' x ' + fileobj.img.lastWidthShown + ' px';
									break;
								case 'pixel_w':
								case 'pixel_h':
									dimensions = fileobj.img.editOptions.scale ? '--' : fileobj.img.lastHeightShown + ' x ' + fileobj.img.lastWidthShown + ' px';
							}
						}

						infoTops[j].innerHTML = setDimensions ? (setDimensions === 'flip' ? (fileobj.img.editOptions.scale ? WE().consts.g_l.fileupload.editScaled + ' ' : '') + dimensions : infoTops[j].innerHTML) : '--';
						infoMiddles[j].style.display = 'block';
						deg = fileobj.img.editOptions.rotate;
						infoMiddlesRight[j].innerHTML = (deg === 0 ? '0&deg;' : (deg === 90 ? '90&deg; '/* + WE().consts.g_l.fileupload.editRotationRight*/ : (deg === 270 ? '270&deg; '/* + WE().consts.g_l.fileupload.editRotationLeft*/ : '180&deg;')));
						infoBottoms[j].style.display = 'block';
						scaleHelp[j].style.display = 'none';
						if(setDimensions === 'flip'){
							var tmp = fileobj.img.lastWidthShown;
							fileobj.img.lastWidthShown = fileobj.img.lastHeightShown;
							fileobj.img.lastHeightShown = tmp;
						}
					break;
				case 'processed':
						elems[j].style.backgroundColor = 'rgb(216, 255, 216)';
						elems[j].style.backgroundImage =  'none';

						// FIXME: fileobj.img.editedWidth is undefined when only qualitiy was changed
						var w, h;
						if(this.sender.preparedFiles[j].dataUrl){
							h = fileobj.img.editedHeight ? fileobj.img.editedHeight : (fileobj.img.lastHeightShown ? fileobj.img.lastHeightShown : fileobj.img.origHeight);
							w = fileobj.img.editedWidth ? fileobj.img.editedWidth : (fileobj.img.lastWidthtShown ? fileobj.img.lastWidthShown : fileobj.img.origWidth);
						}
						infoTops[j].innerHTML = (fileobj.img.editOptions.scale ? WE().consts.g_l.fileupload.editScaled + ' ' : '') + (this.sender.preparedFiles[j].dataUrl ? w + ' x ' + h + ' px' : '--');

						deg = fileobj.img.editOptions.rotate;
						scaleHelp[j].style.display = fileobj.img.tooSmallToScale ? 'inline-block' : 'none';
						infoMiddles[j].style.display = 'block';
						infoMiddlesRight[j].innerHTML = (deg === 0 ? '0&deg;' : (deg === 90 ? '90&deg; '/* + WE().consts.g_l.fileupload.editRotationRight*/ : (deg === 270 ? '270&deg; '/* + WE().consts.g_l.fileupload.editRotationLeft*/ : '180&deg;')));
						infoBottoms[j].style.display = 'block';
						// FIXME: fileobj.img.editedWidth is undefined when only qualitiy was changed
						fileobj.img.lastWidthShown = fileobj.img.editedWidth ? fileobj.img.editedWidth : fileobj.img.lastWidthShown;
						fileobj.img.lastHeightShown = fileobj.img.editedHeight ? fileobj.img.editedHeight : fileobj.img.lastHeightShown;
					break;
				case 'donotedit':
				/*falls through*/
				default:
					infoTops[j].innerHTML = fileobj.img.origWidth ? fileobj.img.origWidth + ' x ' + fileobj.img.origHeight  + ' px' : '--';
					elems[j].style.backgroundColor = 'white';
					elems[j].style.backgroundImage = 'none';
					scaleHelp[j].style.display = fileobj.img.tooSmallToScale ? 'inline-block' : 'none';
					infoMiddles[j].style.display = 'none';
					infoMiddlesRight[j].innerHTML = '';
					infoBottoms[j].style.display = 'none';
					fileobj.img.lastWidthShown = fileobj.img.origWidth;
					fileobj.img.lastHeightShown = fileobj.img.origHeight;
			}
			buttons[j].disabled = this.sender.preparedFiles[j].dataUrl ? true : false;
			asteriskes[j].style.color = this.sender.preparedFiles[j].dataUrl ? 'lightgray' : 'red';
		}
	};

	this.addTextCutLeft = function(elem, text, maxwidth){
		if(!elem){
			return;
		}

		maxwidth = maxwidth || 30;
		text = text ? text : '';
		var i = 500, first = true;
		elem.innerHTML = text;
		while(elem.offsetWidth > maxwidth && i > 0){
			text = text.substr(first ? 4 : 2);
			first = false;
			elem.innerHTML = '...' + text;
			--i;
		}
		return;
	};
}
Fileupload_view_import.prototype = Object.create(Fileupload_view_abstract.prototype);
Fileupload_view_import.prototype.constructor = Fileupload_view_import;
