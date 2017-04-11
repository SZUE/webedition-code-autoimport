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

function weFileupload_view_abstract(uploader) {
	var self = this;
	self.uploader = uploader;

	self.isDragAndDrop = true;
	self.footerName = '';
	self.elems = {};
	self.intProgress = {
		isExtProgress: false,
		width: 0
	};
	self.extProgress = {
		isExtProgress: true,
		name: '',
		width: 0,
		parentElemId: 'progressbar'
	};
	self.previewSize = 116;
	self.useOriginalAsPreviewIfNotEdited = false;
	self.lastLoupIndex = -1;
	self.loupeVisible = false;

	self.init = function (conf) {
		self.controller = self.uploader.controller; // on init all components are initialized
		self.sender = self.uploader.sender;
		self.imageEdit = self.uploader.imageEdit;
		self.utils = self.uploader.utils;

		self.isDragAndDrop = typeof conf.isDragAndDrop !== 'undefined' ? conf.isDragAndDrop : self.isDragAndDrop;
		self.footerName = conf.footerName ? conf.footerName : self.footerName;
		if (typeof conf.intProgress === 'object') {
			self.intProgress.isIntProgress = conf.intProgress.isInternalProgress || self.intProgress.isIntProgress;
			self.intProgress.width = conf.intProgress.width || self.intProgress.width;
		}
		if (typeof conf.extProgress === 'object') {
			self.extProgress.isExtProgress = conf.extProgress.isExternalProgress || self.extProgress.isExtProgress;
			self.extProgress.parentElemId = conf.extProgress.parentElemId || self.extProgress.parentElemId;
			self.extProgress.name = conf.extProgress.name;
		}
	};

	self.onload = function (){
		var doc = self.uploader.doc;

		self.elems.fileSelect = doc.getElementById(uploader.fieldName);
		self.elems.fileDrag = doc.getElementById('div_' + uploader.fieldName + '_fileDrag');//FIXME: change to div_fileDrag
		self.elems.fileInputWrapper = doc.getElementById('div_' + uploader.fieldName + '_fileInputWrapper');//FIXME: change to div_fileInputWrapper

		self.elems.footer = self.footerName ? self.uploader.win.parent.frames[self.footerName] : self.uploader.win;
		self.elems.extProgressDiv = self.elems.footer.document.getElementById(self.extProgress.parentElemId);
		self.extProgress.isExtProgress = !self.elems.extProgressDiv ? false : self.extProgress.isExtProgress;

		self.onload_sub();
	};

	self.onload_sub = function (){
		// to be overridden
	};

	self.setImageEditMessage = function (){
		// to be overridden
	};

	self.unsetImageEditMessage = function (){
		// to be overridden
	};

	self.repaintImageEditMessage = function (){
		// to be overridden
	};

	self.repaintGUI = function (){
		// to be overridden
	};

	self.repaintEntry = function (){
		// to be overridden
	};

	self.setPreviewLoupe = function(fileobj, pt){
		var doc = self.uploader.doc;

		if(fileobj.isUploadStarted){
			return;
		}
		pt = pt ? pt : 0;
		if(pt === 1){
			var info = '',
				dimension = fileobj.img.fullPrev.height + 'x' + fileobj.img.fullPrev.width + ' px';
			if(fileobj.isEdited){
				var deg = parseInt(fileobj.img.processedOptions.rotate),
					degText = deg === 0 ? '' : (deg === 90 ? '90&deg; ' + self.utils.gl.editRotationRight : (deg === 270 ? '90&deg; ' + self.utils.gl.editRotationLeft : '180&deg;'));
				info += (fileobj.img.processedOptions.scale ? self.utils.gl.editScaled + ' ' : '') + dimension;
				info += deg ? (info ? ', ' : '') + self.utils.gl.editRotation + ': ' + degText : '';
				info += fileobj.img.processedOptions.quality !== self.imageEdit.OPTS_QUALITY_NEUTRAL_VAL && fileobj.type === 'image/jpeg' ? (info ? ', ' : '') + self.utils.gl.editQuality + ': ' + fileobj.img.processedOptions.quality + '%' : '';
			} else {
				info = self.utils.gl.editNotEdited + ': ' + dimension;
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

		if(self.lastLoupIndex !== -1 && self.lastLoupIndex !== fileobj.index && self.sender.preparedFiles[self.lastLoupIndex]){
			// in importer we delete fullPreview of an fileobj when moving to an other file
			self.sender.preparedFiles[self.lastLoupIndex].img.fullPrev = null;
		}
		self.lastLoupIndex = fileobj.index;


		var mask = doc.getElementById('we_fileUploadImporter_mask');
		if(mask){
			mask.style.display = 'block';
		}

		if(!(fileobj.img.fullPrev || fileobj.dataUrl || fileobj.dataArray)){
			doc.getElementById('we_fileUpload_loupeFallback').innerHTML = 'Für dieses Bild bzw. für die aktuellen Bearbeitungsoptionen<br/>wurde noch keine Vorschau erstellt.';
			doc.getElementById('we_fileUpload_loupeFallback').style.display = 'block';
			self.loupeVisible = false;
			return;
		}
		self.loupeVisible = true;

		fileobj.loupInner = doc.getElementById('we_fileUpload_loupeInner');
		fileobj.loupInner.style.display = 'block';
		doc.getElementById('we_fileUpload_loupe').style.display = 'block';
		doc.getElementById('we_fileUpload_spinner').style.display = 'block';

		if(fileobj.img.fullPrev){
			self.setPreviewLoupe(fileobj, 1); // we always keep 1 rendered image in loupe
		} else if(fileobj.dataUrl || fileobj.dataArray){ // we have dataURL or dataArray
			fileobj.img.fullPrev = new Image();
			self.utils.logTimeFromStart('start load fullpreview', true);
			fileobj.img.fullPrev.onload = function(){
				self.utils.logTimeFromStart('end load fullpreview');
				self.setPreviewLoupe(fileobj, 1);
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

	self.movePreviewLoupe = function(e, fileobj){
		if(fileobj.isUploadStarted){
			return;
		}
		try{
			if(e.timeStamp - self.lastklick < 10){
				// in Chrome onclick fires mosemove too: this causes the newly set focuspoint to be slightly wrong...
				return;
			}

			if(fileobj.loupInner && fileobj.loupInner.firstChild){
				var offsetLeft = (-fileobj.loupInner.firstChild.width / fileobj.img.previewCanvas.width * e.offsetX) + (fileobj.loupInner.parentNode.offsetWidth / 2);
				var offsetTop = (-fileobj.loupInner.firstChild.height / fileobj.img.previewCanvas.height * e.offsetY) + (fileobj.loupInner.parentNode.offsetHeight / 2);
				self.offesetLeft = offsetLeft;
				self.offsetTop = offsetTop;

				fileobj.loupInner.style.left = Math.round(offsetLeft) + 'px';
				fileobj.loupInner.style.top = Math.round(offsetTop) + 'px';

				fileobj.focusPoint.style.left = Math.round(offsetLeft + ((parseFloat(fileobj.img.focusX) + 1) / 2) * fileobj.img.fullPrev.width) + 'px';
				fileobj.focusPoint.style.top = Math.round(offsetTop + ((parseFloat(fileobj.img.focusY) + 1) / 2) * fileobj.img.fullPrev.height) + 'px';
			}
		} catch (ex) {
			//
		}
	};

	self.unsetPreviewLoupe = function(fileobj){
		var doc = self.uploader.doc;

		self.loupeVisible = false;
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

	self.grabFocusPoint = function(e, fileobj){
		if(!self.loupeVisible){
			return;
		}
		self.lastklick = e.timeStamp;
		if(fileobj.img.previewCanvas.width && fileobj.img.previewCanvas.height){
			fileobj.focusPoint.style.display = 'none';
			fileobj.focusPointFixed.style.display = 'block';
			var focusX = ((e.offsetX / fileobj.img.previewCanvas.width) * 2) - 1;
			var focusY = ((e.offsetY / fileobj.img.previewCanvas.height) * 2) - 1;
			fileobj.img.focusX = focusX.toFixed(2);
			fileobj.img.focusY = focusY.toFixed(2);
			self.writeFocusToForm(fileobj);
			window.setTimeout(function () {
				fileobj.focusPoint.style.top = (fileobj.loupInner.parentNode.offsetHeight / 2) + 'px';
				fileobj.focusPoint.style.left = (fileobj.loupInner.parentNode.offsetWidth / 2) + 'px';
				fileobj.focusPoint.style.display = 'block';
				fileobj.focusPointFixed.style.display = 'none';
			}, 400);
		}
	};

	self.writeFocusToForm = function(filobj){
		// to be overridden
	};

	//TODO: adapt these progress fns to standard progressbars
	self.setInternalProgressText = function (name, text, index) {
		var p = typeof index === 'undefined' || index === false ? '' : '_' + index;
		self.uploader.doc.getElementById('span_' + uploader.fieldName + '_' + name + p).innerHTML = text;
	};

	self.replacePreviewCanvas = function() {
		// to be overridden
	};

	self.setInternalProgress = function (progress, index) {
		try{
			var coef = self.intProgress.width / 100,
				i = typeof index !== 'undefined' || index === false ? index : false,
				p = i === false ? '' : '_' + i;

			self.uploader.doc.getElementById(uploader.fieldName + '_progress_image_bg' + p).style.width = ((coef * 100) - (coef * progress)) + "px";
			self.uploader.doc.getElementById(uploader.fieldName + '_progress_image' + p).style.width = coef * progress + "px";

			self.setInternalProgressText('progress_text', progress + '%', index);
		} catch(e){}
	};

	self.setInternalProgressCompleted = function (success, index, txt) {
		var s = success || false,
				i = index || false,
				p = !i ? '' : '_' + i;

		if (s) {
			self.setInternalProgress(100, i);
			self.uploader.doc.getElementById(uploader.fieldName + '_progress_image').className = 'progress_finished';
		} else {
			self.uploader.doc.getElementById(uploader.fieldName + '_progress_image' + p).className = 'progress_failed';
		}
	};

	self.formEditOptsReset = function (form) {
		form.elements.fuOpts_scaleWhat.value = 'pixel_l';
		form.elements.fuOpts_scale.value = '';
		form.elements.fuOpts_rotate.value = 0;
		form.elements.fuOpts_quality.value = self.imageEdit.OPTS_QUALITY_NEUTRAL_VAL;
	};

	self.formCustomEditOptsSync = function () {
		// to be overridden
	};
}

function weFileupload_view_base(uploader) {
	var self = this;
	weFileupload_view_abstract.call(self, uploader);

	self.uploader = uploader;
	self.uploadBtnName = '';
	self.isInternalBtnUpload = false;
	self.disableUploadBtnOnInit = false;

	self.onload = function(){
		var doc = self.uploader.doc;

		self.elems.message = doc.getElementById('div_' + self.fieldName + '_message');
		self.elems.progress = doc.getElementById('div_' + self.fieldName + '_progress');
		self.elems.progressText = doc.getElementById('span_' + self.fieldName + '_progress_text');
		self.elems.progressMoreText = doc.getElementById('span_' + self.fieldName + '_progress_more_text');
		self.elems.fileName = doc.getElementById('div_' + self.fieldName + '_fileName');
		self.elems.btnResetUpload = doc.getElementById('div_' + self.fieldName + '_btnResetUpload');
		self.elems.btnCancel = doc.getElementById('div_' + self.fieldName + '_btnCancel');
		self.repaintGUI({what: 'initGui'});
	};

	self.addFile = function (f) {
		var sizeText = f.isSizeOk ? self.utils.gl.sizeTextOk + self.utils.computeSize(f.size) + ', ' :
						'<span style="color:red;">' + self.utils.gl.sizeTextNok + '</span>';
		var typeText = f.isTypeOk ? self.utils.gl.typeTextOk + f.type :
						'<span style="color:red;">' + self.utils.gl.typeTextNok + f.type + '</span>';

		self.elems.message.innerHTML = sizeText + typeText;

		if (self.isDragAndDrop) {
			self.elems.fileDrag.innerHTML = f.file.name;
		} else {
			self.elems.fileName.innerHTML = f.file.name;
			self.elems.fileName.style.display = '';
		}
		self.controller.setWeButtonState('reset_btn', true);
		self.controller.setWeButtonState(self.uploadBtnName, f.uploadConditionsOk ? true : false, true);
	};

	self.setDisplay = function (elem, val) { // move to abstract (from binDoc too)
		if (self.elems[elem]) {
			self.elems[elem].style.display = val;
		}
	};

	self.repaintGUI = function (arg) {
		var doc = self.uploader.doc;

		switch (arg.what) {
			case 'initGui' :
				self.controller.setWeButtonState(self.uploadBtnName, !self.disableUploadBtnOnInit, true);
				return;
			case 'chunkOK' :
				var prog = (100 / self.sender.currentFile.size) * self.sender.currentFile.currentWeightFile,
								digits = self.sender.currentFile.totalParts > 1000 ? 2 : (self.sender.currentFile.totalParts > 100 ? 1 : 0);

				if (self.elems.progress) {
					self.setInternalProgress(prog.toFixed(digits), false);
				}
				if (self.extProgress.isExtProgress) {
					self.elems.footer.setProgress(self.extProgress.name, prog.toFixed(digits));
				}
				return;
			case 'fileOK' :
				if (self.elems.progress) {
					self.setInternalProgressCompleted(true);
				}
				if (self.extProgress.isExtProgress) {
					self.elems.footer.setProgress(self.extProgress.name, 100);
				}
				return;
			case 'fileNOK' :
				if (self.elems.progress) {
					self.setInternalProgressCompleted(false);
				}
				return;
			case 'startSendFile' :
				if (self.elems.progress) {
					self.elems.message.style.display = 'none';
					doc.getElementById(uploader.fieldName + '_progress_image').className = "progress_image";
					self.elems.progress.style.display = '';
					self.elems.progressMoreText.style.display = '';
					self.elems.progressMoreText.innerHTML = '&nbsp;&nbsp;/ ' + self.utils.computeSize(self.sender.currentFile.size);
				}
				if (self.extProgress.isExtProgress) {
					self.elems.footer.setProgress(self.extProgress.name, 0);
					self.elems.extProgressDiv.style.display = '';
				}
				self.controller.setWeButtonState('reset_btn', false);
				self.controller.setWeButtonState('browse_harddisk_btn', false);
				if (self.isInternalBtnUpload) {
					self.controller.setWeButtonState(self.uploadBtnName, false, true);
					self.setDisplay('btnResetUpload', 'none');
					self.setDisplay('btnCancel', 'inline-block');
				}
				return;
			case 'cancelUpload' :
				//self.elems.footer['setProgressText' + self.extProgress.name]('progress_text', utils.gl.cancelled);
				if (self.elems.progress) {
					self.setInternalProgressCompleted(false, false, self.utils.gl.cancelled);
				}
				self.controller.setWeButtonState('reset_btn', true);
				self.controller.setWeButtonState('browse_harddisk_btn', true);
				return;
			case 'resetGui' :
				self.sender.preparedFiles = [];
				self.currentFile = -1;

				if(!self.elems.message){
					doc.getElementById('div_' + uploader.fieldName + '_message');
				}
				self.elems.message.innerHTML = '';
				self.elems.message.innerHTML.display = 'none';
				if (self.elems.fileDrag) {
					self.elems.fileDrag.innerHTML = self.utils.gl.dropText;
				}
				if (self.elems.progress) {
					self.setInternalProgress(0);
					self.elems.progress.style.display = 'none';
				}
				if (self.extProgress.isExtProgress) {
					self.elems.footer.setProgress(self.extProgress.name, 0);
					self.elems.extProgressDiv.style.display = 'none';
				}
				self.controller.setWeButtonState('browse_harddisk_btn', true);
				self.controller.setWeButtonState('reset_btn', false);
				if (self.isInternalBtnUpload) {
					self.controller.setWeButtonState(self.uploadBtnName, false, true);
					self.setDisplay('btnResetUpload', 'inline-block');
					self.setDisplay('btnCancel', 'none');
				}
				return;
			default :
				return;
		}
	};
}
weFileupload_view_base.prototype = Object.create(weFileupload_view_abstract.prototype);
weFileupload_view_base.prototype.constructor = weFileupload_view_base;

function weFileupload_view_bindoc(uploader) {
	var self = this;
	weFileupload_view_abstract.call(self, uploader);

	self.uploader = uploader;

	self.uploadBtnName = '';
	self.icon = '';
	self.binDocType = 'other';
	self.previewSize = 116;
	self.useOriginalAsPreviewIfNotEdited = true;
	self.preview = null;
	self.STATE_RESET = 0;
	self.STATE_PREVIEW_OK = 1;
	self.STATE_PREVIEW_NOK = 2;
	self.STATE_UPLOAD = 3;

	self.onload_sub = function () {
		var doc = self.uploader.doc;

		self.elems.fileDrag_state_0 = doc.getElementById('div_fileupload_fileDrag_state_0');
		self.elems.fileDrag_state_1 = doc.getElementById('div_fileupload_fileDrag_state_1');
		self.elems.fileDrag_mask = doc.getElementById('div_' + uploader.fieldName + '_fileDrag');
		self.elems.dragInnerRight = doc.getElementById('div_upload_fileDrag_innerRight');
		self.elems.divRight = doc.getElementById('div_fileupload_right');
		self.elems.txtFilename = doc.getElementById('span_fileDrag_inner_filename');
		self.elems.txtFilename_1 = doc.getElementById('span_fileDrag_inner_filename_1');//??
		self.elems.txtSize = doc.getElementById('span_fileDrag_inner_size');
		self.elems.txtType = doc.getElementById('span_fileDrag_inner_type');
		self.elems.txtEdit= doc.getElementById('span_fileDrag_inner_edit');
		self.elems.divBtnReset = doc.getElementById('div_fileupload_btnReset');
		self.elems.divBtnCancel = doc.getElementById('div_fileupload_btnCancel');
		self.elems.divBtnUpload = doc.getElementById('div_fileupload_btnUpload');
		self.elems.divProgressBar = doc.getElementById('div_fileupload_progressBar');
		self.elems.divButtons = doc.getElementById('div_fileupload_buttons');

		self.spinner = doc.createElement("i");
		self.spinner.className = "fa fa-2x fa-spinner fa-pulse";
	};

	self.addFile = function (f) {
		var doc = self.uploader.doc;
		var sizeText = f.isSizeOk ? self.utils.gl.sizeTextOk + self.utils.computeSize(f.size) + ', ' :
				'<span style="color:red;">' + self.utils.gl.sizeTextNok + '</span>';
		var typeText = f.isTypeOk ? self.utils.gl.typeTextOk + (f.isTypeOk === 1 ? f.type : f.file.name.split('.').pop().toUpperCase()) :
				'<span style="color:red;">' + self.utils.gl.typeTextNok + f.type + '</span>';

		self.elems.fileDrag.style.backgroundColor = f.isEdited ? 'rgb(216, 255, 216)' : 'rgb(232, 232, 255)';
		self.elems.fileDrag.style.backgroundImage = 'none';

		var fn = f.file.name;
		var fe = '';
		if (fn.length > 27) {
			var farr = fn.split('.');
			fe = farr.pop();
			fn = farr.join('.');
			fn = fn.substr(0, 18) + '...' + fn.substring((fn.length - 2), fn.length) + '.';
		}

		self.elems.txtFilename.innerHTML = fn + fe;
		//self.elems.fileDrag_mask.title = f.file.name;
		self.elems.txtSize.innerHTML = sizeText;
		self.elems.txtType.innerHTML = typeText;
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
			self.elems.txtEdit.style.display = 'none';
		}
		self.setDisplay('fileDrag_state_0', 'none');
		self.setDisplay('fileDrag_state_1', 'block');
		self.elems.dragInnerRight.innerHTML = '';

		if (f.uploadConditionsOk) {
			self.sender.isAutostartPermitted = true;
			self.controller.setEditorIsHot();
		} else {
			self.sender.isAutostartPermitted = false;
		}

		if (f.type.search("image/") !== -1){
			if(f.img.previewImg){
				self.preview = f.img.previewImg;
				self.elems.dragInnerRight.innerHTML = '';
				self.elems.dragInnerRight.appendChild(self.preview);
			} else if(f.img.previewCanvas){
				self.preview = f.img.previewCanvas;
				self.elems.dragInnerRight.innerHTML = '';
				self.elems.dragInnerRight.appendChild(self.preview);
			}
			self.setGuiState(f.uploadConditionsOk ? self.STATE_PREVIEW_OK : self.STATE_PREVIEW_NOK);
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
				self.elems.dragInnerRight.innerHTML = '<div class="largeicons" style="margin:24px 0 0 26px;height:62px;width:54px;">' + self.icon + '</div>';
				self.setGuiState(self.STATE_PREVIEW_OK);
			} else {
				self.elems.dragInnerRight.innerHTML = '<div class="bold" style="margin:18px 0 0 30px;height:62px;width:54px;border:dotted 1px gray;padding-top:14px;text-align:center;background-color:#f9f9f9;color:#ddd;font-size:32px;">!?</div>';
				self.setGuiState(self.STATE_PREVIEW_NOK);
			}
		}
	};

	self.setGuiState = function (state) {
		switch (state) {
			case self.STATE_RESET:
				self.setDisplay('fileDrag_state_0', 'block');
				self.setDisplay('fileDrag_state_1', 'none');
				self.elems.fileDrag.style.backgroundColor = 'transparent';
				self.elems.fileDrag.style.backgroundImage = 'none';
				self.setDisplay('fileInputWrapper', 'block');
				if (self.isDragAndDrop && self.elems.fileDrag) {
					self.setDisplay('fileDrag', 'block');
				}
				self.setDisplay('divBtnReset', 'none');
				self.setDisplay('divBtnUpload', '');
				self.setDisplay('divProgressBar', 'none');
				self.setDisplay('divBtnCancel', 'none');
				self.setDisplay('dragInnerRight', '');
				/*
				if (uploader.EDIT_IMAGES_CLIENTSIDE) {
					doc.getElementById('make_preview_weFileupload').disabled = true;//make same as following
				}
				*/
				self.controller.setWeButtonState(self.uploadBtnName, false);
				self.controller.setWeButtonState('browse_harddisk_btn', true);
				return;
			case self.STATE_PREVIEW_OK:
				self.setDisplay('fileInputWrapper', 'none');
				self.setDisplay('divBtnReset', '');
				self.controller.setWeButtonState('reset_btn', true);
				self.controller.setWeButtonState(self.uploadBtnName, true);
				return;
			case self.STATE_PREVIEW_NOK:
				self.setDisplay('fileInputWrapper', 'none');
				self.setDisplay('divBtnReset', '');
				self.controller.setWeButtonState('reset_btn', true);
				self.controller.setWeButtonState(self.uploadBtnName, false);
				return;
			case self.STATE_UPLOAD:
				self.controller.setWeButtonState(self.uploadBtnName, false);
				self.controller.setWeButtonState('reset_btn', false);
				self.setDisplay('fileInputWrapper', 'none');
				if (uploader.uiType !== 'wedoc') {
					self.setDisplay('divBtnReset', 'none');
				}
				self.setDisplay('divBtnUpload', 'none');
				self.setDisplay('divBtnReset', 'none');
				self.setDisplay('divProgressBar', '');
				self.setDisplay('divBtnCancel', '');
				if (self.preview) {
					self.preview.style.opacity = 0.05;
				}
				self.controller.setWeButtonState('browse_harddisk_btn', false);
		}
	};

	self.repaintGUI = function (arg) {
		var cur = self.sender.currentFile,
			fileProg = 0,
			digits = 0,
			opacity = 0;

		switch (arg.what) {
			case 'chunkOK' :
				digits = cur.totalParts > 1000 ? 2 : (cur.totalParts > 100 ? 1 : 0);//FIXME: make fn on UtilsAbstract
				fileProg = (100 / cur.size) * cur.currentWeightFile;
				self.setInternalProgress(fileProg.toFixed(digits));
				opacity = fileProg / 100;
				if (self.preview) {
					self.preview.style.opacity = opacity.toFixed(2);
				}
				return;
			case 'fileOK' :
				self.sender.preparedFiles = [];
				if (self.preview) {
					self.preview.style.opacity = 1;
				}
				return;
			case 'startSendFile' :
				self.setInternalProgress(0);
				self.setGuiState(self.STATE_UPLOAD);
				return;
			case 'chunkNOK' :
				self.sender.processError({from: 'gui', msg: arg.message});
				/* falls through */
			case 'initGui' :
			case 'fileNOK' :
			case 'cancelUpload' :
			case 'resetGui' :
				/* falls through */
			default:
				self.sender.preparedFiles = [];
				self.sender.currentFile = -1;
				self.setInternalProgress(0);
				self.setGuiState(self.STATE_RESET);
				return;
		}
	};

	self.previewSyncRotation = function(pos, rotation){
		if(self.sender.preparedFiles.length){
			self.imageEdit.processimageRotatePreview(self.sender.preparedFiles[0], rotation);
			self.replacePreviewCanvas(self.sender.preparedFiles[0]);
		}
	};

	self.setEditStatus = function(state){
		var doc = self.uploader.doc;
		var fileobj = self.sender.preparedFiles.length ? self.sender.preparedFiles[0] : null;
		var btn = doc.getElementsByClassName('weFileupload_btnImgEditRefresh ')[0];

		state = !fileobj ? 'empty' : state;
		state = state ? state : !fileobj ? 'empty' : (fileobj.isEdited ? 'processed' : (fileobj.img.editOptions.doEdit ? 'notprocessed' : 'donotedit'));

		if (self.uploader.uiType === 'wedoc') {
			self.hideOptions(false);
		}

		switch(state){
			case 'notprocessed':
				if(self.sender.preparedFiles.length){
					self.elems.fileDrag.style.backgroundColor = '#ffffff';
					self.elems.fileDrag.style.backgroundColor = 'rgb(244, 255, 244)'; //'rgb(rgb(244, 255, 244))';//notprocessed
					//self.elems.fileDrag.style.backgroundImage = 'repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255, 255, 255,1.0) 5px, rgba(216,255,216,.5) 10px)';
					self.elems.txtSize.innerHTML = self.utils.gl.sizeTextOk + '--';
					btn.disabled = false;
				}
				break;
			case 'processed':
				if(self.sender.preparedFiles.length){
					self.elems.fileDrag.style.backgroundColor = 'rgb(216, 255, 216)';
					self.elems.fileDrag.style.backgroundImage =  'none';
					btn.disabled = true;
				}
				break;
			case 'donotedit':
				self.elems.fileDrag.style.backgroundColor = 'rgb(232, 232, 255)';
				self.elems.fileDrag.style.backgroundImage =  'none';
				btn.disabled = false;
				break;
			case 'empty':
				self.elems.fileDrag.style.backgroundColor = 'white';
				self.elems.fileDrag.style.backgroundImage =  'none';
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

	self.hideOptions = function(hide){
		var doc = self.uploader.doc;
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

	self.disableCustomEditOpts = function () {
		// to be overridden
	};

	self.writeFocusToForm = function(fileobj){
		if (!uploader.EDIT_IMAGES_CLIENTSIDE) {
			return;
		}

		var doc = self.uploader.doc;

		doc.we_form.elements.fu_doc_focusX.value = fileobj.img.focusX;
		doc.we_form.elements.fu_doc_focusY.value = fileobj.img.focusY;
	};

	//TODO: use progress fns from abstract after adapting them to standard progress
	self.setInternalProgress = function (progress, index) {
		var coef = self.intProgress.width / 100;
		var mt = typeof self.sender.currentFile === 'object' ? ' / ' + self.utils.computeSize(self.sender.currentFile.size) : '';
		var doc = self.uploader.doc;

		doc.getElementById('progress_image_fileupload').style.width = coef * progress + "px";
		doc.getElementById('progress_image_bg_fileupload').style.width = (coef * 100) - (coef * progress) + "px";
		doc.getElementById('progress_text_fileupload').innerHTML = progress + '%' + mt;
	};

	self.setDisplay = function (elem, val) {
		if (self.elems[elem]) {
			self.elems[elem].style.display = val;
		}
	};

	self.setImageEditMessage = function (singleMode) {
		if (!uploader.EDIT_IMAGES_CLIENTSIDE) {
			return;
		}

		var mask = self.uploader.doc.getElementById('div_fileupload_fileDrag_mask'),
			text = self.uploader.doc.getElementById('image_edit_mask_text');

		mask.style.display = 'block';
		text.innerHTML = singleMode ? self.utils.gl.maskProcessImage : self.utils.gl.maskReadImage;
	};

	self.unsetImageEditMessage = function () {
		if (!uploader.EDIT_IMAGES_CLIENTSIDE) {
			return;
		}
		var mask = self.uploader.doc.getElementById('div_fileupload_fileDrag_mask');
		mask.style.display = 'none';
	};

	self.repaintImageEditMessage = function (empty, changeText) {
		if (!uploader.EDIT_IMAGES_CLIENTSIDE) {
			return;
		}

		var text = self.uploader.doc.getElementById('image_edit_mask_text').innerHTML;
		text = (changeText ? self.utils.gl.maskProcessImage : text) + '.';
		text += '.';
		self.uploader.doc.getElementById('image_edit_mask_text').innerHTML = text;

	};

	self.repaintEntry = function (fileobj) {
		self.addFile(fileobj);
		if (!uploader.EDIT_IMAGES_CLIENTSIDE) {
			return;
		}
		self.replacePreviewCanvas(fileobj);
		self.setEditStatus();
	};

	self.formCustomEditOptsSync = function(){
		//
	};

	self.replacePreviewCanvas = function(fileobj) {
		self.elems.dragInnerRight.innerHTML = '';
		self.elems.dragInnerRight.appendChild(fileobj.img.previewCanvas);

		self.elems.dragInnerRight.firstChild.addEventListener('mouseenter', function(){self.setPreviewLoupe(fileobj);}, false);
		self.elems.dragInnerRight.firstChild.addEventListener('mousemove', function(e){self.movePreviewLoupe(e, fileobj);}, false);
		self.elems.dragInnerRight.firstChild.addEventListener('mouseleave', function(){self.unsetPreviewLoupe(fileobj);}, false);
		self.elems.dragInnerRight.firstChild.addEventListener('click', function(e){self.grabFocusPoint(e,fileobj);}, false);
	};
}
weFileupload_view_bindoc.prototype = Object.create(weFileupload_view_abstract.prototype);
weFileupload_view_bindoc.prototype.constructor = weFileupload_view_bindoc;

function weFileupload_view_import(uploader) {
	var self = this;
	weFileupload_view_abstract.call(self, uploader);

	self.fileTable = '';
	self.htmlFileRow = '';
	self.nextTitleNr = 1;
	self.isUploadEnabled = false;
	self.messageWindow = null;
	self.previewSize = 110;
	//self.useOriginalAsPreviewIfNotEdited = true;

	self.addFile = function (f, index) {
		self.appendRow(f, self.sender.preparedFiles.length - 1);
	};

	self.repaintEntry = function (fileobj) { // TODO: get rid of fileobj.entry
		if(!fileobj.entry){
			fileobj.entry = self.uploader.doc.getElementById('div_uploadFiles_' + fileobj.index);
			if(!fileobj.entry){
				self.uploader.win.console.log('an error occured: fileobj.entry is undefined');
				return;
			}
		}

		fileobj.entry.getElementsByClassName('elemSize')[0].innerHTML = (fileobj.isSizeOk ? self.utils.computeSize(fileobj.size) : '<span style="color:red">> ' + ((self.sender.maxUploadSize / 1024) / 1024) + ' MB</span>');

		if(self.imageEdit.EDITABLE_CONTENTTYPES.indexOf(fileobj.type) !== -1){
			fileobj.entry.getElementsByClassName('elemIcon')[0].style.display = 'none';
			fileobj.entry.getElementsByClassName('elemPreview')[0].style.display = 'block';
			fileobj.entry.getElementsByClassName('elemContentBottom')[0].style.display = 'block';
			fileobj.entry.getElementsByClassName('rowDimensionsOriginal')[0].innerHTML = (fileobj.img.origWidth ? fileobj.img.origWidth + ' x ' + fileobj.img.origHeight + ' px': '--') + ', ' + self.utils.computeSize(fileobj.img.originalSize);
			//fileobj.entry.getElementsByClassName('optsQualitySlide')[0].style.display = fileobj.type === 'image/jpeg' ? 'block' : 'none';
			self.replacePreviewCanvas(fileobj);
			//self.formCustomEditOptsSync(fileobj.index, false);
			self.setEditStatus('', fileobj.index, false);
		} else {
			fileobj.entry.getElementsByClassName('elemIcon')[0].style.display = 'block';
			fileobj.entry.getElementsByClassName('elemPreview')[0].style.display = 'none';
			fileobj.entry.getElementsByClassName('elemContentBottom')[0].style.display = 'none';

			var ext = fileobj.file.name.substr(fileobj.file.name.lastIndexOf('.') + 1).toUpperCase();
			fileobj.entry.getElementsByClassName('elemIcon')[0].innerHTML = WE().util.getTreeIcon(fileobj.type) + ' ' + ext;
		}
	};

	self.replacePreviewCanvas = function(fileobj) {
		var elem = fileobj.entry.getElementsByClassName('elemPreviewPreview')[0];
		elem.innerHTML = '';
		elem.appendChild(fileobj.img.previewCanvas);

		elem.firstChild.addEventListener('mouseenter', function(){self.setPreviewLoupe(fileobj);}, false);
		elem.firstChild.addEventListener('mousemove', function(e){self.movePreviewLoupe(e, fileobj);}, false);
		elem.firstChild.addEventListener('mouseleave', function(){self.unsetPreviewLoupe(fileobj);}, false);
		elem.firstChild.addEventListener('click', function(e){self.grabFocusPoint(e,fileobj);}, false);
	};

	self.setImageEditMessage = function (singleMode, index) {
		var row, elem;
		var doc = self.uploader.doc;

		if(singleMode && (row = doc.getElementById('div_uploadFiles_' + index))){
			row.getElementsByClassName('elemContentTop')[0].style.display = 'none';
			row.getElementsByClassName('elemContentBottom')[0].style.display = 'none';
			row.getElementsByClassName('elemContentMask')[0].style.display = 'block';
			row.getElementsByClassName('we_file_drag_maskBusyText')[0].innerHTML = self.utils.gl.maskProcessImage;
			return;
		}

		if((elem = doc.getElementById('we_fileUploadImporter_mask'))){
			doc.getElementById('we_fileUploadImporter_busyText').innerHTML = self.imageEdit.imageEditOptions.doEdit ? self.utils.gl.maskImporterProcessImages : self.utils.gl.maskImporterReadImages;
			try{
				doc.getElementById('we_fileUploadImporter_messageNr').innerHTML = self.imageEdit.imageFilesToProcess.length;
			} catch (e) {
			}
			doc.getElementById('we_fileUploadImporter_busyMessage').style.display = 'block';
			doc.getElementById('we_fileUploadImporter_busyMessage').style.zIndex = 800;
			elem.style.display = 'block';
		}
	};

	self.unsetImageEditMessage = function (singleMode, index) {
		var row, elem;
		var doc = self.uploader.doc;

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

	self.repaintImageEditMessage = function(step, singleMode, index) {
		var row;
		var doc = self.uploader.doc;

		try{
			if(step){
				if(false && singleMode && (row = doc.getElementById('div_uploadFiles_' + index))){
					row.getElementsByClassName('we_file_drag_maskBusyText')[0].innerHTML += self.imageEdit.imageEditOptions.doEdit ? '.' : '';
					return;
				}

				doc.getElementById('we_fileUploadImporter_busyText').innerHTML += self.imageEdit.imageEditOptions.doEdit ? '.' : '';
			} else {
				doc.getElementById('we_fileUploadImporter_busyText').innerHTML = self.imageEdit.imageEditOptions.doEdit ? self.utils.gl.maskImporterProcessImages : self.utils.gl.maskImporterReadImages;
				doc.getElementById('we_fileUploadImporter_messageNr').innerHTML = self.imageEdit.imageFilesToProcess.length;
			}
		} catch (e) {
		}
	};

	self.appendRow = function (f, index) {
		var doc = self.uploader.doc;
		var div, entry;
		var parts = f.file.name.split('.');
		var row = self.htmlFileRow.replace(/WEFORMNUM/g, index).replace(/WE_FORM_NUM/g, (self.nextTitleNr++)).
				replace(/FILENAME/g, (f.file.name)).
				replace(/FNAME/g, (parts[0])).
				replace(/FENDING/g, (parts[1])).
				replace(/FILESIZE/g, (f.isSizeOk ? self.utils.computeSize(f.size) : '<span style="color:red">> ' + ((self.sender.maxUploadSize / 1024) / 1024) + ' MB</span>'));

		self.uploader.win.weAppendMultiboxRow(row, '', 0, 0, 0, -1);
		entry = self.uploader.doc.getElementById('div_uploadFiles_' + index);

		div = doc.getElementById('div_upload_files');
		//div.scrollTop = getElementById('div_upload_files').div.scrollHeight;
//		doc.getElementById('fileInput_uploadFiles_' + index).addEventListener('change', self.controller.replaceSelectionHandler, false);

		self.addTextCutLeft(doc.getElementById('showName_uploadFiles_' + index), f.file.name, 230);

		if(uploader.EDIT_IMAGES_CLIENTSIDE){
			//doc.getElementById('customOpts_' + index).style.display = self.imageEdit.isImageEditActive ? 'inline-block' : 'none';
			if(self.imageEdit.EDITABLE_CONTENTTYPES.indexOf(f.type) !== -1){
				doc.getElementById('icon_uploadFiles_' + index).style.display = 'none';
				doc.getElementById('preview_uploadFiles_' + index).style.display = 'block';
				doc.getElementById('editoptions_uploadFiles_' + index).style.display = 'block';
			} else {
				var ext = f.file.name.substr(f.file.name.lastIndexOf('.') + 1).toUpperCase();
				doc.getElementById('icon_uploadFiles_' + index).innerHTML = WE().util.getTreeIcon(f.type) + ' ' + ext;
			}
		}

		self.elems.extProgressDiv.style.display = 'none';
		self.controller.setWeButtonText('cancel', 'cancel');

		if (f.isSizeOk) {
			if (!self.isUploadEnabled) {
				self.controller.setWeButtonState('reset_btn', true);
				self.controller.enableWeButton('next', true);
				self.isUploadEnabled = true;
				self.sender.isCancelled = false;
			}
		} else {
			self.sender.preparedFiles[index] = null;
		}

		f.index = index;
		f.entry = doc.getElementById('div_uploadFiles_' + index);

		var form = doc.getElementById('form_editOpts_' + index);

		form.elements.fuOpts_filenameInput.addEventListener('change', self.controller.editFilename, false);
		form.elements.fuOpts_filenameInput.addEventListener('blur', self.controller.editFilename, false);
		doc.getElementById('showName_uploadFiles_' + index).addEventListener('click', self.controller.editFilename, false);

		var btn = form.getElementsByClassName('weFileupload_btnImgEditRefresh')[0];
		btn.addEventListener('click', function(){self.controller.editImageButtonOnClick(btn, index, false);}, false);

		var rotLeft = form.getElementsByClassName('fuOpts_addRotationLeft')[0];
		rotLeft.addEventListener('click', function(){self.controller.customEditOptsOnChange(rotLeft, index, false);}, false);

		var rotRight = form.getElementsByClassName('fuOpts_addRotationRight')[0];
		rotRight.addEventListener('click', function(){self.controller.customEditOptsOnChange(rotRight, index, false);}, false);

		form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseenter', function(e){self.controller.editOptionsHelp(e.target, 'enter');}, false);
		form.getElementsByClassName('optsRowScaleHelp')[0].addEventListener('mouseleave', function(e){self.controller.editOptionsHelp(e.target, 'leave');}, false);

	};

	self.deleteRow = function (index, button) {
		var prefix = 'div_uploadFiles_', num = 0, z = 1, i, sp;
		var divs = self.uploader.doc.getElementsByTagName('DIV');

		self.imageEdit.memorymanagerUnregister(self.sender.preparedFiles[index]);
		self.sender.preparedFiles[index] = null;

		self.uploader.win.weDelMultiboxRow(index);

		for (i = 0; i < divs.length; i++) {
			if (divs[i].id.length > prefix.length && divs[i].id.substring(0, prefix.length) === prefix) {
				num = divs[i].id.substring(prefix.length, divs[i].id.length);
				sp = self.uploader.doc.getElementById('headline_uploadFiles_' + num);
				if (sp) {
					sp.innerHTML = z;
				}
				z++;
			}
		}
		self.nextTitleNr = z;
		if (!self.utils.containsFiles(self.sender.preparedFiles)) {
			self.controller.enableWeButton('next', false);
			self.controller.setWeButtonState('reset_btn', false);
			self.isUploadEnabled = false;
		}
	};

	self.reloadOpener = function () {
		try {
			var activeFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();

			if (self.uploader.doc.we_form.fu_file_parentID.value === activeFrame.EditorDocumentId && activeFrame.EditorEditPageNr === 16) {
				top.opener.top.we_cmd('switch_edit_page', 16, activeFrame.EditorTransaction);
			}
			top.opener.top.we_cmd('load', 'tblFile');
		} catch (e) {
			//
		}
	};

	self.repaintGUI = function (arg) {
		var i, j, fileProg = 0, totalProg = 0, digits = 0;
		var sender = self.sender;
		var cur = sender.currentFile;
		var totalDigits = sender.totalChunks > 1000 ? 2 : (sender.totalChunks > 100 ? 1 : 0);
		var doc = self.uploader.doc;

		switch (arg.what) {
			case 'startSendFile':
				i = sender.mapFiles[cur.fileNum];
				if(self.imageEdit.EDITABLE_CONTENTTYPES.indexOf(cur.type) !== -1){
					doc.getElementById('image_edit_done_' + i).style.display = 'block';
				}
				doc.getElementById('showName_uploadFiles_'  + i).removeEventListener('click', self.controller.editFilename);
				break;
			case 'chunkOK' :
				digits = cur.totalParts > 1000 ? 2 : (cur.totalParts > 100 ? 1 : 0);//FIXME: make fn on UtilsAbstract
				fileProg = (100 / cur.size) * cur.currentWeightFile;
				totalProg = (100 / sender.totalWeight) * sender.currentWeight;
				i = sender.mapFiles[cur.fileNum];
				j = i + 1;
				self.setInternalProgress(fileProg.toFixed(digits), i);
				if (cur.partNum === 1) {
					self.elems.footer.setProgressText('progress_title', self.utils.gl.doImport + ' ' + self.utils.gl.file + ' ' + j);
				}
				self.elems.footer.setProgress("", totalProg.toFixed(totalDigits));
				return;
			case 'fileOK' :
				i = sender.mapFiles[cur.fileNum];
				try {
					doc.getElementById('div_upload_files').scrollTop = doc.getElementById('div_uploadFiles_' + i).offsetTop - 360;
				} catch (e) {}
				self.setInternalProgressCompleted(true, i, '');
				return;
			case 'chunkNOK' :
				totalProg = (100 / sender.totalWeight) * sender.currentWeight;
				i = sender.mapFiles[cur.fileNum];
				j = i + 1;
				try {
					doc.getElementById('div_upload_files').scrollTop = doc.getElementById('div_uploadFiles_' + i).offsetTop - 200;
				} catch (e) {
				}
				self.setInternalProgressCompleted(false, i, arg.message);
				if (cur.partNum === 1) {
					self.elems.footer.setProgressText('progress_title', self.utils.gl.doImport + ' ' + self.utils.gl.file + ' ' + j);
				}
				self.elems.footer.setProgress("", totalProg.toFixed(totalDigits));
				return;
			case 'startUpload' :
				//set buttons state and show initial progress bar
				self.controller.enableWeButton('back', false);
				self.controller.enableWeButton('next', false);
				self.controller.setWeButtonState('reset_btn', false);
				self.controller.setWeButtonState('browse_harddisk_btn', false);
				self.isUploadEnabled = false;
				self.elems.footer.document.getElementById('progressbar').style.display = '';
				self.elems.footer.setProgressText('progress_title', self.utils.gl.doImport + ' ' + self.utils.gl.file + ' 1');
				try {
					doc.getElementById('div_upload_files').scrollTop = 0;
				} catch (e) {
				}

				return;
			case 'cancelUpload' :
				i = sender.mapFiles[cur.fileNum];
				self.setInternalProgressCompleted(false, sender.mapFiles[cur.fileNum], self.utils.gl.cancelled);
				try {
					doc.getElementById('div_upload_files').scrollTop = doc.getElementById('div_uploadFiles_' + i).offsetTop - 200;
				} catch (e) {
				}
				for (j = 0; j < sender.uploadFiles.length; j++) {
					var file = sender.uploadFiles[j];
					self.setInternalProgressCompleted(false, sender.mapFiles[file.fileNum], self.utils.gl.cancelled);
				}

				self.controller.setWeButtonState('reset_btn', true);
				self.controller.setWeButtonState('browse_harddisk_btn', true);
				return;
			case 'resetGui' :
				try {
					doc.getElementById('td_uploadFiles').innerHTML = '';
				} catch (e) {
				}
				self.sender.preparedFiles = [];
				self.nextTitleNr = 1;
				self.isUploadEnabled = false;
				self.controller.enableWeButton('next', false);
				self.sender.resetSender();
				return;
			default :
				return;
		}
	};

	self.setInternalProgressCompleted = function (success, index, txt) {
		var doc = self.uploader.doc;

		if (success) {
			self.setInternalProgress(100, index);
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

	self.formCustomEditOptsSync = function (pos, general, initRotation) {
		if(initRotation){
			var indices;

			pos = general ? -1 : (pos && pos !== -1 ? pos : -1);
			indices = self.imageEdit.getImageEditIndices(pos, general, true);

			for(var i = 0; i < indices.length; i++){
				self.uploader.doc.getElementById('fuOpts_rotate_' + indices[i]).value = self.uploader.doc.we_form.elements.fuOpts_rotate.value;
			}
		}
	};

	self.disableCustomEditOpts = function (disable) {
			var customEdits = self.uploader.doc.getElementsByClassName('btnRefresh');

			for(var i = 0; i < customEdits.length; i++){
				customEdits[i].style.display = disable ? 'none' : 'inline-block';
			}
	};

	self.previewSyncRotation = function(pos, rotation){
		var indices = self.imageEdit.getImageEditIndices(pos, pos === -1, false);

		for(var i = 0; i < indices.length; i++){
			self.imageEdit.processimageRotatePreview(self.sender.preparedFiles[indices[i]], rotation);
			self.replacePreviewCanvas(self.sender.preparedFiles[indices[i]]);
		}
	};

	self.setEditStatus = function(preset, pos, general, setDimensions){
		var doc = self.uploader.doc;
		var divUploadFiles = doc.getElementById('div_upload_files');
		var indices = self.imageEdit.getImageEditIndices(pos, general, true);
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
			fileobj = self.sender.preparedFiles[j];
			state = preset ? preset : (fileobj.isEdited ? 'processed' : (fileobj.img.editOptions.doEdit ? 'notprocessed' : 'donotedit'));

			switch(state){
				case 'notprocessed':
						elems[j].style.backgroundColor = '#ffffff';
						elems[j].style.backgroundColor = 'rgb(244, 255, 244)';
						//elems[j].style.backgroundImage = 'repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255, 255, 255,1.0) 5px, rgba(244, 255, 244,.5) 10px)';
						sizes[j].innerHTML = self.utils.gl.sizeTextOk + '--';

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

						infoTops[j].innerHTML = setDimensions ? (setDimensions === 'flip' ? (fileobj.img.editOptions.scale ? self.utils.gl.editScaled + ' ' : '') + dimensions : infoTops[j].innerHTML) : '--';
						infoMiddles[j].style.display = 'block';
						deg = fileobj.img.editOptions.rotate;
						infoMiddlesRight[j].innerHTML = (deg === 0 ? '0&deg;' : (deg === 90 ? '90&deg; '/* + self.utils.gl.editRotationRight*/ : (deg === 270 ? '270&deg; '/* + self.utils.gl.editRotationLeft*/ : '180&deg;')));
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
						if(self.sender.preparedFiles[j].dataUrl){
							h = fileobj.img.editedHeight ? fileobj.img.editedHeight : (fileobj.img.lastHeightShown ? fileobj.img.lastHeightShown : fileobj.img.origHeight);
							w = fileobj.img.editedWidth ? fileobj.img.editedWidth : (fileobj.img.lastWidthtShown ? fileobj.img.lastWidthShown : fileobj.img.origWidth);
						}
						infoTops[j].innerHTML = (fileobj.img.editOptions.scale ? self.utils.gl.editScaled + ' ' : '') + (self.sender.preparedFiles[j].dataUrl ? w + ' x ' + h + ' px' : '--');

						deg = fileobj.img.editOptions.rotate;
						scaleHelp[j].style.display = fileobj.img.tooSmallToScale ? 'inline-block' : 'none';
						infoMiddles[j].style.display = 'block';
						infoMiddlesRight[j].innerHTML = (deg === 0 ? '0&deg;' : (deg === 90 ? '90&deg; '/* + self.utils.gl.editRotationRight*/ : (deg === 270 ? '270&deg; '/* + self.utils.gl.editRotationLeft*/ : '180&deg;')));
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
			buttons[j].disabled = self.sender.preparedFiles[j].dataUrl ? true : false;
			asteriskes[j].style.color = self.sender.preparedFiles[j].dataUrl ? 'lightgray' : 'red';
		}
	};

	self.addTextCutLeft = function(elem, text, maxwidth){
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
weFileupload_view_import.prototype = Object.create(weFileupload_view_abstract.prototype);
weFileupload_view_import.prototype.constructor = weFileupload_view_import;
