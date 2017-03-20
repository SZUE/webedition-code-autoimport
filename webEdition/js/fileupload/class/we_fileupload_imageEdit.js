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

function weFileupload_imageEdit_abstract(uploader) {
	var self = this;
	self.uploader = uploader;

	self.imageFilesToProcess = [];
	self.isImageEditActive = false;
	self.imageEditOptions = {
		doEdit: false,
		from: 'general',
		scaleWhat: 'pixel_l',
		scale: 0,
		rotate: 0,
		quality: 100
	};
	self.processimageRepeatLoadCanvas = 0;

	self.PICA_CONFIGURATION = {
		quality: 3, // [0,3]
		unsharpAmount: 0, // [0, 200]
		unsharpRadius: 0.5, // [0.5, 2]
		unsharpThreshold: 0, // [0, 255]
		alpha: false
	};

	self.IMG_NEXT = 0;
	self.IMG_START = 10;
	self.IMG_LOAD_CANVAS = 1;
	self.IMG_EXTRACT_METADATA = 2;
	self.IMG_SCALE = 3;
	self.IMG_ROTATE = 4;
	self.IMG_APPLY_FILTERS = 5;
	self.IMG_WRITE_IMAGE = 6;
	self.IMG_INSERT_METADATA = 7;
	self.IMG_MAKE_PREVIEW = 8;
	self.IMG_POSTPROCESS = 9;

	self.OPTS_QUALITY_NEUTRAL_VAL = 100;
	self.OPTS_QUALITY_DEFAULT_VAL = 90;
	self.PRESERVE_IMG_DATAURL = true;
	self.EDITABLE_CONTENTTYPES = ['image/jpeg', 'image/gif', 'image/png'];
	self.MAX_LONGEST = -1;

	self.IS_MEMORY_MANAGMENT = false;
	self.PROCESS_PREVIEWS_ONLY = false;
	//self.MEMORY_LIMIT = 31457280;
	self.MEMORY_LIMIT = 83886080; // 80 MB
	self.memoryManagement = {
		registeredSum: 0,
		registeredValues: {},
		queueEdited: [],
		queueNotEdited: []
	};

	self.init = function (conf) {
		self.imageEdit = self.uploader.imageEdit;// on init all components are initialized
		self.sender = self.uploader.sender;
		self.view = self.uploader.view;
		self.utils = self.uploader.utils;
		self.MAX_LONGEST = conf.imageeditMaxLongest ? parseInt(conf.imageeditMaxLongest) : -1;

		self.init_sub(conf);
	};

	self.init_sub = function () {
		// to be overridden
	};

	self.processImages = function() {
		self.PROCESS_PREVIEWS_ONLY = false;
		if (self.imageFilesToProcess && self.imageFilesToProcess.length) {
			self.setImageEditOptionsGeneral();
			self.view.setImageEditMessage();
			self.processNextImage();
		} else {
			self.view.unsetImageEditMessage();
		}
	};

	self.processNextImage = function() {
		if (self.imageFilesToProcess.length) {
			var fileobj = self.imageFilesToProcess.shift();
			self.utils.logTimeFromStart('start edit image', true, fileobj);
			self.setImageEditOptionsFile(fileobj);
			self.processImage(fileobj, self.IMG_START);
		} else {
			self.processImages();
		}
	};

	self.processSingleImage = function(fileobj, finishProcess){
		self.PROCESS_PREVIEWS_ONLY = false;
		if(finishProcess){
			fileobj.processSingleImage = false;
			self.view.unsetImageEditMessage(true, fileobj.preparedFilesIndex);//
			if (fileobj.img.callback === 'sendNextFile' && self.sender.prepareUpload(true)) {
				window.setTimeout(function () {
					self.sender.sendNextFile();
				}, 100);
			}
			return;
		}

		if(fileobj){
			fileobj.processSingleImage = true;
			self.view.setImageEditMessage(true, fileobj.preparedFilesIndex);
			self.utils.logTimeFromStart('start edit image', true);
			self.setImageEditOptionsFile(fileobj);
			self.processImage(fileobj, self.IMG_START);
		}
	};
	
	/*
	 * reedit a single image or all images using general options applying opts from GUI
	 *
	 */
	self.reeditImage = function (index, general) {
		var indices = self.getImageEditIndices(index, general, false);

		for(var i = 0; i < indices.length; i++){
			self.imageFilesToProcess.push(self.sender.preparedFiles[indices[i]]);
		}
		self.processImages();
	};

	/*
	 * set property isEdited of a single image or all images using general options to false
	 * -> empty props like dataArray, dataUrl
	 * -> do not change GUI (= edit opts)
	 *
	 * we use this to free disk space when edited images are not valid anymore after changing options
	 *
	 */
	self.uneditImage = function (index, general) {
		var indices = self.getImageEditIndices(index, general, false);
		var fileobj;

		for(var i = 0; i < indices.length; i++){
			fileobj = self.sender.preparedFiles[indices[i]];
			fileobj.dataArray = null;
			fileobj.dataUrl = null;
			fileobj.size = fileobj.img.originalSize;
			fileobj.img.previewImg = null;
			fileobj.img.fullPrev = null;
			fileobj.img.actualRotation = 0;
			self.setImageEditOptionsFile(fileobj); // write actually valid editoptions
			
			fileobj.img.processedOptions = { // reset last edited options
				doEdit: false,
				from: 'general',
				scaleWhat: 'pixel_l',
				scale: 0,
				rotate: 0,
				quality: 100
			};
			fileobj.isEdited = false;
			self.memorymanagerRegister(fileobj);
		}

		return;
	};

	self.openImageEditor = function(pos){
		// to be overridden
	};

	self.processImage = function(fileobj, task) {
		if(!fileobj){
			self.processNextImage();
			return;
		}

		switch(task) {
			case self.IMG_START:
				if(self.PROCESS_PREVIEWS_ONLY && fileobj.img.previewCanvas){
					self.processImage(fileobj, self.IMG_NEXT);
					return;
				}
				self.processimageExtractLandscape(fileobj, self.IMG_LOAD_CANVAS);
				break;
			case self.IMG_LOAD_CANVAS: // TODO: make IMG_START
				if(!fileobj.img.editOptions.doEdit && fileobj.size > 10485760){ // nothing to edit and image is too big for preview (>10MB)
					self.processImage(fileobj, self.IMG_NEXT);
					return;
				}
				self.processimageLoadCanvas(fileobj, fileobj.img.editOptions.doEdit ? self.IMG_EXTRACT_METADATA : self.IMG_MAKE_PREVIEW);
				break;
			case self.IMG_EXTRACT_METADATA:
				self.processimageExtractMetadata(fileobj, self.IMG_SCALE);
				break;
			case self.IMG_SCALE:
				self.processimageScale(fileobj, self.IMG_ROTATE);
				break;
			case self.IMG_ROTATE:
				self.processimageRotate(fileobj, self.IMG_APPLY_FILTERS);
				break;
			case self.IMG_APPLY_FILTERS:
				self.imageEdit.processimageApplyFilters(fileobj, self.IMG_WRITE_IMAGE);
				break;
			case self.IMG_WRITE_IMAGE:
				self.processimageWriteImage(fileobj, self.IMG_INSERT_METADATA);
				break;
			case self.IMG_INSERT_METADATA:
				self.processimagInsertMetadata(fileobj, self.IMG_MAKE_PREVIEW);
				break;
			case self.IMG_MAKE_PREVIEW:
				self.processimageMakePreview(fileobj, self.IMG_POSTPROCESS);
				break;
			case self.IMG_POSTPROCESS:
				self.processimagePostProcess(fileobj, self.IMG_NEXT);
				break;
			case self.IMG_NEXT:
			/*falls through*/
			default:
				self.processNextImage();
		}
	};

	self.resetImageEdit = function (fileobj) {
		fileobj.dataArray = null;
		fileobj.dataUrl = null;
		if(self.IS_MEMORY_MANAGMENT){
			self.memorymanagerRegister(fileobj);
		}
	};

	self.abstractSetImageEditOptionsGeneral = function (formname) {
		var form = self.uploader.doc.forms[(formname ? formname : 'we_form')],
			scale = form.elements.fuOpts_scale.value,
			deg = parseInt(form.elements.fuOpts_rotate.value),
			quality = parseInt(form.elements.fuOpts_quality.value),
			opts = self.imageEditOptions;

		if (parseInt(form.elements.fuOpts_doEdit.value) === 1 && (scale || deg || quality !== self.OPTS_QUALITY_NEUTRAL_VAL)) {
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
			opts.quality = self.OPTS_QUALITY_NEUTRAL_VAL;
		}
	};

	self.setImageEditOptionsGeneral = function () {
		self.abstractSetImageEditOptionsGeneral();
	};

	self.setImageEditOptionsFile = function (fileobj) {
		fileobj.img.editOptions = JSON.parse(JSON.stringify(self.imageEditOptions));
		//fileobj.img.editOptions.from = type; // FIXME: what type is this?
	};

	self.processimageExtractLandscape = function(fileobj, nexttask) {
		if(fileobj.type === 'image/jpeg' && !fileobj.img.isOrientationChecked){
			var reader = new FileReader(),
				exif, tags;

			fileobj.img.isOrientationChecked = true;
			reader.onloadend = function (event) {
				try {
					exif = new WE().layout.fileupload.ExifReader();
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
				self.processImage(fileobj, nexttask);
			};
			reader.readAsArrayBuffer(fileobj.file.slice(0, 128 * 1024));
		} else {
			fileobj.img.isOrientationChecked = true;
			self.processImage(fileobj, nexttask);
		}
	};

	self.processimageLoadCanvas = function(fileobj, nexttask) {
		var reader = new FileReader();

		reader.onload = function() {
			if(!fileobj.img.editOptions.doEdit){ // we will not edit and rewrite image so we hold original dataUrl in fileobj to be used by fullpreview
				fileobj.dataUrl = reader.result;

				if(fileobj.img.previewCanvas){ // preview done during earlier editing: directly jump top postprocess
					self.processimagePostProcess(fileobj, self.IMG_NEXT);
					return;
				}
			}

			fileobj.img.image = new Image();
			fileobj.img.image.onload = function() {
				fileobj.img.workingCanvas = self.uploader.doc.createElement('canvas');
				if(!fileobj.img.image && self.processimageRepeatLoadCanvas < 5){
					self.processimageRepeatLoadCanvas++;
					self.processimageLoadCanvas(fileobj, nexttask);
				}
				self.processimageRepeatLoadCanvas = 0;
				fileobj.img.workingCanvas.width = fileobj.img.image.width;
				fileobj.img.workingCanvas.height = fileobj.img.image.height;
				if(!fileobj.img.origWidth || !fileobj.img.origHeight){
					fileobj.img.origWidth = fileobj.img.image.width;
					fileobj.img.origHeight = fileobj.img.image.height;
					self.setImageEditOptionsFile(fileobj); // set editOptions again after orig dimensions are extracted
				}
				fileobj.img.workingCanvas.getContext("2d").drawImage(fileobj.img.image, 0, 0);
				fileobj.img.image = null;
				self.utils.logTimeFromStart('canvas loaded');
				nexttask = self.PROCESS_PREVIEWS_ONLY ? self.IMG_MAKE_PREVIEW : nexttask;
				self.processImage(fileobj, nexttask);
			};

			self.view.repaintImageEditMessage(true, true);
			fileobj.img.image.src = reader.result;
		};
		reader.readAsDataURL(fileobj.file);
	};

	self.processimageExtractMetadata = function(fileobj, nexttask) {
		switch(fileobj.type){
			case 'image/jpeg':
				self.processimageExtractMetadataJPG(fileobj, nexttask);
				break;
			case 'image/png':
				self.processimageExtractMetadataPNG(fileobj, nexttask);
				break;
			default:
				self.processImage(fileobj, nexttask);
		}
	};

	self.processimageExtractMetadataJPG = function(fileobj, nexttask) {
			var reader = new FileReader();

			reader.onloadend = function () {
				fileobj.img.jpgCustomSegments = self.jpgGetSegmentsIfExist(new Uint8Array(reader.result), [225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239], true);

				self.utils.logTimeFromStart('meta jpg exttracted');
				self.view.repaintImageEditMessage(true);
				self.processImage(fileobj, nexttask);
			};
			reader.readAsArrayBuffer(fileobj.file.slice(0, 128 * 1024));
	};

	self.processimageExtractMetadataPNG = function(fileobj, nexttask) {
		var reader = new FileReader();

		reader.onloadend = function () {
			fileobj.img.pngTextChunks = [];
			try{
				var chunks = WE().layout.fileupload.extractChunks(new Uint8Array(reader.result));
				for(var i = 0; i < chunks.length; i++){
					if(chunks[i].name === 'iTXt' || chunks[i].name === 'tEXt' || chunks[i].name === 'zTXt'){
						fileobj.img.pngTextChunks.push(chunks[i]);
						/*
						var decodedChunk = chunks[i].name !== 'iTXt' ? WE().layout.fileupload.decodeChunk(chunks[i]) :
								decodeURIComponent(String.fromCharCode.apply(null, chunks[i].data));
						*/
					}
				}
			} catch(e){
				WE().t_e('extracting png metadata failed');
			}

			self.utils.logTimeFromStart('meta png exttracted');
			self.view.repaintImageEditMessage(true);
			self.processImage(fileobj, nexttask);
		};
		reader.readAsArrayBuffer(fileobj.file);
	};

	self.processimageScale = function(fileobj, nexttask){
		if(!fileobj.img.editOptions.scale){
			self.utils.logTimeFromStart('scaling skipped');
			self.processImage(fileobj, nexttask);
			return; // IMPORTANT!
		}

		var scaleWhat = fileobj.img.editOptions.scaleWhat !== 'pixel_l' ? fileobj.img.editOptions.scaleWhat :
				(fileobj.img.workingCanvas.width >= fileobj.img.workingCanvas.height ? 'pixel_w' : 'pixel_h');
		var ratio = scaleWhat === 'pixel_w' ? fileobj.img.editOptions.scale/fileobj.img.workingCanvas.width :
					fileobj.img.editOptions.scale/fileobj.img.workingCanvas.height;
		if(ratio >= 1){
			self.utils.logTimeFromStart('scaling: image smaller than targetsize');
			self.processImage(fileobj, nexttask); // we do not upscale!
			return; // IMPORTANT!
		}

		var targetCanvas = self.uploader.doc.createElement('canvas');
		targetCanvas.width = fileobj.img.workingCanvas.width * ratio;
		targetCanvas.height = fileobj.img.workingCanvas.height * ratio;

		WE().layout.fileupload.pica.resizeCanvas(fileobj.img.workingCanvas, targetCanvas, self.PICA_CONFIGURATION, function (err) {
			if(err){
				WE().t_e('scaling image failed');
				fileobj.img.editedWidth = 'n.n.';
				fileobj.img.editedHeight = 'n.n.';
			} else {
				fileobj.img.workingCanvas = targetCanvas;
				fileobj.img.editedWidth = targetCanvas.width;
				fileobj.img.editedHeight = targetCanvas.height;
				targetCanvas = null;
			}

			self.utils.logTimeFromStart('scaling done');
			fileobj.isEdited = true;

			self.view.repaintImageEditMessage(true);
			self.processImage(fileobj, nexttask);
		});
	};

	self.processimageRotate = function(fileobj, nexttask, preview, degrees, correctPreviewOrientation){
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
				self.utils.logTimeFromStart('rotation skipped');
				self.processImage(fileobj, nexttask);
				return;
		}

		var targetCanvas = self.uploader.doc.createElement('canvas'),
			ctxTargetCanvas = targetCanvas.getContext("2d");
			targetCanvas.width = cw;
			targetCanvas.height = ch;

		ctxTargetCanvas.rotate(deg * Math.PI / 180);
		ctxTargetCanvas.drawImage(fileobj.img[target], cx, cy);

		if(!preview){
			fileobj.img.editedWidth = targetCanvas.width;
			fileobj.img.editedHeight = targetCanvas.height;
		}

		fileobj.img[target] = targetCanvas;
		targetCanvas = null;

		if(correctPreviewOrientation){
			return;
		}

		if(target === 'workingCanvas'){
			fileobj.isEdited = true;
			fileobj.img.actualRotation = deg;
			self.utils.logTimeFromStart('rotation done');
			self.view.repaintImageEditMessage(true);
			self.processImage(fileobj, nexttask);
		}
	};

	self.processimageApplyFilters = function(fileobj, nexttask){
		self.processImage(fileobj, nexttask);
	};

	self.processimageWriteImage_2 = function(fileobj, nexttask){
		fileobj.img.workingCanvas.toBlob(function (blob) { // DO WE NEED toBlob TO GET UINT8ARRAY?
															// THIS FN CAUASES PROBLEMS WITH PNG!
			var reader = new FileReader();
			reader.onload = function() {
				fileobj.dataArray = new Uint8Array(reader.result);
				self.utils.logTimeFromStart('image written');
				self.processImage(fileobj, nexttask);
			};
			reader.readAsArrayBuffer(blob);
		}, fileobj.type, (fileobj.img.editOptions.quality/100));
	};

	self.processimageWriteImage = function(fileobj, nexttask){
		/*
		if(fileobj.type !== 'image/png'){
			self.processimageWriteImage_2(fileobj, nexttask);
		}
		*/
		fileobj.dataUrl = fileobj.img.workingCanvas.toDataURL(fileobj.type, (fileobj.img.editOptions.quality/100));
		fileobj.dataArray = self.utils.dataURLToUInt8Array(fileobj.dataUrl);
		if(!self.PRESERVE_IMG_DATAURL){
			fileobj.dataUrl = null;
		}
		fileobj.isEdited = fileobj.img.editOptions.quality < 90 ? true : fileobj.isEdited;

		self.utils.logTimeFromStart('image written fn 2');
		self.processImage(fileobj, nexttask);
	};

	self.processimagInsertMetadata = function(fileobj, nexttask) {
		switch(fileobj.type){
			case 'image/jpeg':
				self.processimagInsertMetadataJPG(fileobj, nexttask);
				break;
			case 'image/png':
				self.processimagInsertMetadataPNG(fileobj, nexttask);
				break;
			default:
				self.utils.logTimeFromStart('no metadata to reinsert');
				self.processImage(fileobj, nexttask);
		}
	};

	self.processimagInsertMetadataJPG = function(fileobj, nexttask) {
		if(fileobj.img.jpgCustomSegments) {
			fileobj.dataArray = self.jpgInsertSegment(fileobj.dataArray, fileobj.img.jpgCustomSegments);
			fileobj.size = fileobj.dataArray.length;
			self.view.repaintImageEditMessage(true);
		}

		self.utils.logTimeFromStart('metadata reinserted');
		self.processImage(fileobj, nexttask);
	};

	self.processimagInsertMetadataPNG = function(fileobj, nexttask) {
		self.utils.logTimeFromStart('metadata reinsert skipped');
		//self.processImage(fileobj, nexttask);

		if(fileobj.img.pngTextChunks && fileobj.img.pngTextChunks.length){
			fileobj.dataArray = self.pngReinsertTextchunks(fileobj.dataArray, fileobj.img.pngTextChunks);

			/*
			var combinedChuks = [];
			try{
				var chunks = WE().layout.fileupload.extractChunks(fileobj.dataArray),
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
					newUInt8Array = WE().layout.fileupload.encodeChunks(combinedChuks);
				} catch (e) {
					newUInt8Array = false;
				}
			}

			fileobj.dataArray = newUInt8Array ? newUInt8Array : fileobj.dataArray;
			*/

			self.view.repaintImageEditMessage(true);
		}

		self.utils.logTimeFromStart('metadata reinserted');
		self.processImage(fileobj, nexttask);
	};

	self.processimageMakePreview = function(fileobj, nexttask){
		if(fileobj && fileobj.img.workingCanvas){
			if(fileobj.img.previewCanvas){
				self.processimageRotatePreview(fileobj, -1, nexttask);
				return;
			}

			var tmpCanvas = self.uploader.doc.createElement("canvas"),
				ctxTmpCanvas,
				previewCanvas = self.uploader.doc.createElement("canvas"),
				clone = null,
				prevWidth, prevHeight;

			if(fileobj.img.workingCanvas.width < self.view.previewSize && fileobj.img.workingCanvas.height < self.view.previewSize){
				prevHeight = fileobj.img.workingCanvas.width;
				prevWidth = fileobj.img.workingCanvas.height;
			} else {
				if(fileobj.img.workingCanvas.width > fileobj.img.workingCanvas.height){
					prevWidth = self.view.previewSize;
					prevHeight = Math.round(self.view.previewSize / fileobj.img.workingCanvas.width * fileobj.img.workingCanvas.height);

				} else {
					prevHeight = self.view.previewSize;
					prevWidth = Math.round(self.view.previewSize / fileobj.img.workingCanvas.height * fileobj.img.workingCanvas.width);
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
				self.processimageRotate(fileobj, -1, true, 0, true);
			}

			tmpCanvas = ctxTmpCanvas = previewCanvas = clone = null;

			self.utils.logTimeFromStart('preview done');
		}

		self.processImage(fileobj, nexttask);
		return;
	};

	self.processimageRotatePreview = function(fileobj, deg, nexttask){
			if(fileobj && fileobj.img.previewCanvas){
				var targetRotation = deg === -1 ? fileobj.img.actualRotation : deg;
				var realRotation = (targetRotation - fileobj.img.previewRotation + 360)  % 360;

				self.processimageRotate(fileobj, -1, true, realRotation);
				self.view.replacePreviewCanvas(fileobj);
				fileobj.img.previewRotation = targetRotation;
			}

			if(!nexttask){
				return;
			}
			self.processImage(fileobj, nexttask);
	};

	/* obsolete
	self.processimageReset = function(fileobj, nexttask){
		if(!fileobj){
			return;
		}
		fileobj.dataArray = null;
		fileobj.dataUrl = null;
		fileobj.size = fileobj.img.originalSize;
		fileobj.img.previewImg = null;
		fileobj.img.fullPrev = null;
		fileobj.img.actualRotation = 0;
		self.setImageEditOptionsFile(fileobj); // write correct actually valid editoptions
		fileobj.img.processedOptions = { // reset last edited options
			doEdit: false,
			from: 'general',
			scaleWhat: 'pixel_l',
			scale: 0,
			rotate: 0,
			quality: 100
		},
		self.processimageRotatePreview(fileobj, 0);
		view.replacePreviewCanvas(fileobj);
		fileobj.isEdited = false;

		if(nexttask){
			self.processImage(fileobj, nexttask);
		} else {
			view.repaintEntry(fileobj);
		}
		return;
	};
	*/

	self.processimagePostProcess = function(fileobj, nexttask){
		fileobj.img.originalSize = fileobj.file.size;

		if(fileobj.dataArray && fileobj.isEdited){
			fileobj.size = fileobj.dataArray.length;
		}
		fileobj.dataArray = null; // we recompute it from dataUrl and metas while uploading
		if(self.PROCESS_PREVIEWS_ONLY){
			fileobj.dataUrl = null;
		}

		fileobj.img.processedWidth = 0;//fileobj.img.fullPrev.width;
		fileobj.img.processedHeight = 0; //fileobj.img.fullPrev.height;

		fileobj.img.workingCanvas = null;
		fileobj.img.fullPrev = null;

		fileobj.img.processedOptions = JSON.parse(JSON.stringify(fileobj.img.editOptions));
		fileobj.totalParts = Math.ceil(fileobj.size / self.sender.chunkSize);
		fileobj.lastChunkSize = fileobj.size % self.sender.chunkSize;
		self.sender.preparedFiles[fileobj.preparedFilesIndex] = fileobj; // do we need this?
		self.view.repaintEntry(fileobj);
		self.view.repaintImageEditMessage();
		self.utils.logTimeFromStart('processing finished', false, fileobj);

		if(self.IS_MEMORY_MANAGMENT){
			self.memorymanagerRegister(fileobj);
			if(self.memorymanagerIsOverflow()){
				self.memorymanagerEmptySpace();
				//win.console.log('emptied space for this file');

				if(self.imageFilesToProcess.length){ //
					if(fileobj.img.editOptions.doEdit && self.memorymanagerIsUneditedPreviewToDelete){
						//win.console.log('dataURLs of unedited where deleted: go on');
					} else {
						//win.console.log('processing images must be stopped: we will make previews if needed and then stop');
						self.PROCESS_PREVIEWS_ONLY = true;
					}
				}
			}
		}

		if(fileobj.processSingleImage){
			self.processSingleImage(fileobj, true);
		} else {
			self.processImage(fileobj, nexttask);
		}
	};

	self.memorymanagerRegister = function(fileobj){
		var m = self.memoryManagement;
		self.memorymanagerUnregister(fileobj);

		if(fileobj.dataArray || fileobj.dataUrl){
			var size = parseInt((fileobj.dataArray ? fileobj.dataArray.length : 0)) + parseInt((fileobj.dataUrl ? fileobj.dataUrl.length : 0));

			m[fileobj.isEdited ? 'queueEdited' : 'queueNotEdited'].push(fileobj.index);
			m.registeredValues['o_' + fileobj.index] = size;
			m.registeredSum += size;
		}
		//win.console.log('register', m, sender.preparedFiles);
	};

	self.memorymanagerUnregister = function(fileobj){
		var m = self.memoryManagement, i;

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
		//win.console.log('unregister', m, sender.preparedFiles);
	};

	self.memorymanagerReregisterAll = function(){
		self.memorymanagerReset();

		for(var i = 0; i < self.sender.preparedFiles.length; i++){
			self.memorymanagerRegister(self.sender.preparedFiles[i]);
		}
		//win.console.log('register all', self.memoryManagement, sender.preparedFiles);
	};

	self.memorymanagerReset = function(){
		self.memoryManagement = {
			registeredSum: 0,
			registeredValues: {},
			queueEdited: [],
			queueNotEdited: []
		};
	};

	self.memorymanagerIsOverflow = function(){
		return self.memoryManagement.registeredSum > self.MEMORY_LIMIT;
	};

	self.memorymanagerIsUneditedPreviewToDelete = function(){
		return self.memoryManagement.queueNotEdited.length > 0;
	};

	self.memorymanagerEmptySpace = function(){
		var m = self.memoryManagement,
			fileobj, index;

		while((m.queueEdited.length || m.queueNotEdited.length) && m.registeredSum > self.MEMORY_LIMIT){
			index = m.queueNotEdited.length ? m.queueNotEdited.shift() : m.queueEdited.shift();
			fileobj = self.sender.preparedFiles[index];

			self.uneditImage(fileobj.index);
			self.view.setEditStatus('', fileobj.index);
		}
	};

	self.jpgInsertSegment = function (uint8array, exifSegment) {
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

			return self.utils.concatTypedArrays(Uint8Array, [head, exifSegment, segments]);
		}
	};

	self.jpgGetExifSegment = function (uint8array){ // unused?
		return self.jpgGetSegment(uint8array, 225);
	};

	// use this to extract a certain segment: not intended to be used several times in sequence
	self.jpgGetSegment = function (uint8array, marker) {
		var head = 0;

		while (head < uint8array.length) {
			if (uint8array[head] == 255 && uint8array[head + 1] == 218){ // SOI = Scan of Image = image data (eg. canvas works on)!
				break;
			}

			if (uint8array[head] == 255 && uint8array[head + 1] == 216){ // omit 216 (D8): SOI = Start of Image (is empty so length = 2 bytes)
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

	self.jpgGetAllSegmentsUpToSOS = function(uint8array) { // unused?
		var head = 0,
			segments = {},
			order = [];

		while (head < uint8array.length) {
			// each segment starts with 255 (FF) and its segment marker
			if (uint8array[head] == 255 && uint8array[head + 1] == 218){ // 218 (DA): SOS = Start of Scan = image data (canvas works on)
				//win.console.log('found SOS at pos', head, segments);
				break;
			}
			if (uint8array[head] == 255 && uint8array[head + 1] == 216){ // omit 216 (D8): SOI = Start of Image (is empty so length = 2 bytes)
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

	self.jpgGetSegmentsIfExist = function (uint8array, segments, concat) {
		var head = 0,
			searchObj = {},
			segmentsArr = [],
			controllArr = [];

		for (var i = 0; i < segments.length; i++) {
			searchObj[segments[i]] = true;
		}

		while (head < uint8array.length) {
			if (uint8array[head] == 255 && uint8array[head + 1] == 218){ // SOI = Scan of Image = image data (eg. canvas works on)!
				break;
			}

			if (uint8array[head] == 255 && uint8array[head + 1] == 216){ // omit 216 (D8): SOI = Start of Image (is empty so length = 2 bytes)
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
		//win.console.log('all custom segments', {list: controllArr, segments: segmentsArr});

		return concat ? self.utils.concatTypedArrays(Uint8Array, segmentsArr) : segmentsArr;
	};

	self.pngReinsertTextchunks = function(dataArray, pngTextChunks){
		var combinedChunks = [];
		try{
			var chunks = WE().layout.fileupload.extractChunks(dataArray);
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
				newUInt8Array = WE().layout.fileupload.encodeChunks(combinedChunks);
			} catch (e) {
				newUInt8Array = false;
			}
		}

		return newUInt8Array ? newUInt8Array : dataArray;
	};
}

function weFileupload_imageEdit_base(uploader) {
	var self = this;
	weFileupload_imageEdit_abstract.call(self, uploader);

	self.uploader = uploader;
}
weFileupload_imageEdit_base.prototype = Object.create(weFileupload_imageEdit_abstract.prototype);
weFileupload_imageEdit_base.prototype.constructor = weFileupload_imageEdit_base;

function weFileupload_imageEdit_bindoc(uploader) {
	var self = this;
	weFileupload_imageEdit_abstract.call(self, uploader);

	self.uploader = uploader;

	self.setImageEditOptionsFile = function () {
		self.setImageEditOptionsGeneral();
		if(self.sender.preparedFiles.length){
			var fileobj = self.sender.preparedFiles[0];

			fileobj.img.editOptions = JSON.parse(JSON.stringify(self.imageEditOptions));
			fileobj.img.editOptions.quality = fileobj.type === 'image/jpeg' ? fileobj.img.editOptions.quality : self.OPTS_QUALITY_NEUTRAL_VAL;
			fileobj.img.editOptions.from = 'general';

			// the following is identical in importer: move to new fn on abstract
			var scaleReference = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? fileobj.img.origWidth : (
					fileobj.img.editOptions.scaleWhat === 'pixel_h' ? fileobj.img.origHeight : Math.max(fileobj.img.origHeight, fileobj.img.origWidth));

			if(scaleReference && (scaleReference < fileobj.img.editOptions.scale)){
				fileobj.img.editOptions.scale = '';
				fileobj.img.tooSmallToScale = true;
				if(!fileobj.img.editOptions.rotate && fileobj.img.editOptions.quality >= self.OPTS_QUALITY_DEFAULT_VAL){
					fileobj.img.editOptions.quality = self.OPTS_QUALITY_NEUTRAL_VAL;
					self.uploader.doc.getElementsByName('fuOpts_quality')[0].value = self.OPTS_QUALITY_NEUTRAL_VAL;
					self.uploader.doc.getElementById('qualityValue').innerHTML = self.OPTS_QUALITY_NEUTRAL_VAL;
				}
			} else {
				fileobj.img.tooSmallToScale = false;
			}

			fileobj.img.editOptions.doEdit = fileobj.img.editOptions.scale || fileobj.img.editOptions.rotate || (fileobj.img.editOptions.quality !== self.OPTS_QUALITY_NEUTRAL_VAL) ? true : false;
		}
	};

	self.getImageEditIndices = function(pos, general){
		return self.sender.preparedFiles.length ? [0] : [];
	};
}
weFileupload_imageEdit_bindoc.prototype = Object.create(weFileupload_imageEdit_abstract.prototype);
weFileupload_imageEdit_bindoc.prototype.constructor = weFileupload_imageEdit_bindoc;

function weFileupload_imageEdit_import(uploader) {
	var self = this;
	weFileupload_imageEdit_abstract.call(self, uploader);

	self.PRESERVE_IMG_DATAURL = true;
	self.IS_MEMORY_MANAGMENT = true;

	/*
	self.setImageEditOptionsGeneral = function () { // obsolet: is in abstract identical
		self.abstractSetImageEditOptionsGeneral('we_form');
	};
	*/

	self.setImageEditOptionsFile = function (fileobj, general) {
		var indices = self.getImageEditIndices(general ? -1 : fileobj.index, general);

		for(var i = 0; i < indices.length; i++){
			fileobj = self.sender.preparedFiles[indices[i]];
			var form = self.uploader.doc.getElementById('form_editOpts_' + fileobj.index);
			var type = 'general';

			/*
			if (form && form.elements.fuOpts_useCustomOpts.checked) {
				type = 'custom';
			}
			*/

			switch(type){
				case 'general':
					// first global editOptiona are read out and all fileobject's editOptions are syncronized with these
					self.setImageEditOptionsGeneral();
					fileobj.img.editOptions = JSON.parse(JSON.stringify(self.imageEditOptions));

					// reset quality to neutral for all images but jpeg
					fileobj.img.editOptions.quality = fileobj.type === 'image/jpeg' ? fileobj.img.editOptions.quality : self.OPTS_QUALITY_NEUTRAL_VAL;

					// we sync general rotation to entries, thus fuOpts_rotate on entries is allways actual: take rotation from there
					if(parseInt(form.elements.fuOpts_rotate.value) === -1){ // initial value when entry is just added
						form.elements.fuOpts_rotate.value = fileobj.img.editOptions.rotate;
					} else {
						fileobj.img.editOptions.rotate = parseInt(form.elements.fuOpts_rotate.value);
					}
					break;
				case 'custom':
					/*
					fileobj.img.editOptions.scaleWhat = form.elements.fuOpts_scaleWhat.value;
					fileobj.img.editOptions.scale = form.elements.fuOpts_scale.value;
					fileobj.img.editOptions.rotate = parseInt(form.elements.fuOpts_rotate.value);
					fileobj.img.editOptions.quality = fileobj.type === 'image/jpeg' ? parseInt(form.elements.fuOpts_quality.value) : self.OPTS_QUALITY_NEUTRAL_VAL;
					*/
					break;
			}

			var scaleReference = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? fileobj.img.origWidth : (
					fileobj.img.editOptions.scaleWhat === 'pixel_h' ? fileobj.img.origHeight : Math.max(fileobj.img.origHeight, fileobj.img.origWidth));

			// check for tooSmallToScale
			if(/*fileobj.img.workingCanvas !== null &&*/ scaleReference < fileobj.img.editOptions.scale){
				fileobj.img.editOptions.scale = '';
				fileobj.img.tooSmallToScale = true;
				if(!fileobj.img.editOptions.rotate && fileobj.img.editOptions.quality >= self.OPTS_QUALITY_DEFAULT_VAL){
					fileobj.img.editOptions.quality = self.OPTS_QUALITY_NEUTRAL_VAL;
					//form.elements.fuOpts_quality.value = self.OPTS_QUALITY_NEUTRAL_VAL;
					//form.getElementsByClassName('optsQualityValue')[0].innerHTML = self.OPTS_QUALITY_NEUTRAL_VAL;
				}
			} else {
				fileobj.img.tooSmallToScale = false;
			}

			fileobj.img.editOptions.doEdit = parseInt(fileobj.img.editOptions.scale) || fileobj.img.editOptions.rotate || (parseInt(fileobj.img.editOptions.quality) < self.OPTS_QUALITY_DEFAULT_VAL) ? true : false;
		}
	};

	self.getImageEditIndices = function(index, general, formposition){
		var indices = [],
			forms, i;

		if(general){
			forms = self.uploader.doc.getElementsByName('form_editOpts');
			for(i = 0; i < forms.length; i++){
				index = forms[i].getAttribute('data-index');
				if (self.sender.preparedFiles[index] && self.EDITABLE_CONTENTTYPES.indexOf(self.sender.preparedFiles[index].type) !== -1 &&
								//!forms[i].elements.fuOpts_useCustomOpts.checked &&
								!self.sender.preparedFiles[index].isUploadStarted) {
					indices.push(formposition ? i : index);
				}
			}
		} else if (index !== undefined && index > -1 && self.sender.preparedFiles[index] &&
						self.EDITABLE_CONTENTTYPES.indexOf(self.sender.preparedFiles[index].type) !== -1) {
			indices.push(index);
			if(formposition){
				forms = self.uploader.doc.getElementsByName('form_editOpts');
				for(i = 0; i < forms.length; i++){
					if(forms[i].getAttribute('data-index') == index){
						return [index];
					}
				}
			}
		}

		return indices;
	};
}
weFileupload_imageEdit_import.prototype = Object.create(weFileupload_imageEdit_abstract.prototype);
weFileupload_imageEdit_import.prototype.constructor = weFileupload_imageEdit_import;
