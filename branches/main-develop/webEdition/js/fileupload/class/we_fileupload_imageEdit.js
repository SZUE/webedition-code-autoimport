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

function Fileupload_imageEdit_abstract(uploader) {
	this.uploader = uploader;

	this.imageFilesToProcess = [];
	this.isImageEditActive = false;

	this.PICA_CONFIGURATION = {
		quality: 3, // [0,3]
		unsharpAmount: 0, // [0, 200]
		unsharpRadius: 0.5, // [0.5, 2]
		unsharpThreshold: 0, // [0, 255]
		alpha: false
	};

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

	this.OPTS_QUALITY_NEUTRAL_VAL = 90;
	this.PRESERVE_IMG_DATAURL = true;
	this.EDITABLE_CONTENTTYPES = ['image/jpeg', 'image/gif', 'image/png'];
	this.MAX_LONGEST = -1;

	this.IS_MEMORY_MANAGMENT = false;
	this.PROCESS_PREVIEWS_ONLY = false;
	//self.MEMORY_LIMIT = 31457280;
	this.MEMORY_LIMIT = 83886080; // 80 MB

	this.imageEditOptions = {
		doEdit: false,
		from: 'general',
		scaleWhat: 'pixel_l',
		scale: 0,
		rotate: 0,
		quality: this.OPTS_QUALITY_NEUTRAL_VAL
	};

	this.processimageRepeatLoadCanvas = 0;
	this.memoryManagement = {
		registeredSum: 0,
		registeredValues: {},
		queueEdited: [],
		queueNotEdited: []
	};

	this.init = function (conf) {
		this.imageEdit = this.uploader.imageEdit;// on init all components are initialized
		this.sender = this.uploader.sender;
		this.view = this.uploader.view;
		this.utils = this.uploader.utils;
		this.MAX_LONGEST = conf.imageeditMaxLongest ? parseInt(conf.imageeditMaxLongest) : -1;

		this.init_sub(conf);
	};

	this.init_sub = function () {
		// to be overridden
	};

	this.processImages = function() {
		this.PROCESS_PREVIEWS_ONLY = false;
		if (this.imageFilesToProcess && this.imageFilesToProcess.length) {
			this.setImageEditOptionsGeneral();
			this.view.setImageEditMessage();
			this.processNextImage();
		} else {
			this.view.unsetImageEditMessage();
		}
	};

	this.processNextImage = function() {
		if (this.imageFilesToProcess.length) {
			var fileobj = this.imageFilesToProcess.shift();
			this.utils.logTimeFromStart('start edit image', true, fileobj);
			this.setImageEditOptionsFile(fileobj);
			this.processImage(fileobj, this.IMG_START);
		} else {
			this.processImages();
		}
	};

	this.processSingleImage = function(fileobj, finishProcess){
		this.PROCESS_PREVIEWS_ONLY = false;
		if(finishProcess){
			fileobj.processSingleImage = false;
			this.view.unsetImageEditMessage(true, fileobj.preparedFilesIndex);//
			if (fileobj.img.callback === 'sendNextFile' && this.sender.prepareUpload(true)) {
				window.setTimeout(function () {
					this.sender.sendNextFile();
				}, 100);
			}
			return;
		}

		if(fileobj){
			fileobj.processSingleImage = true;
			this.view.setImageEditMessage(true, fileobj.preparedFilesIndex);
			this.utils.logTimeFromStart('start edit image', true);
			this.setImageEditOptionsFile(fileobj);
			this.processImage(fileobj, this.IMG_START);
		}
	};

	/*
	 * reedit a single image or all images using general options applying opts from GUI
	 *
	 */
	this.reeditImage = function (index, general) {
		var indices = this.getImageEditIndices(index, general, false);

		for(var i = 0; i < indices.length; i++){
			this.imageFilesToProcess.push(this.sender.preparedFiles[indices[i]]);
		}
		this.processImages();
	};

	/*
	 * set property isEdited of a single image or all images using general options to false
	 * -> empty props like dataArray, dataUrl
	 * -> do not change GUI (= edit opts)
	 *
	 * we use this to free disk space when edited images are not valid anymore after changing options
	 *
	 */
	this.uneditImage = function (index, general) {
		var indices = this.getImageEditIndices(index, general, false);
		var fileobj;

		for(var i = 0; i < indices.length; i++){
			fileobj = this.sender.preparedFiles[indices[i]];
			fileobj.dataArray = null;
			fileobj.dataUrl = null;
			fileobj.size = fileobj.img.originalSize;
			fileobj.img.previewImg = null;
			fileobj.img.fullPrev = null;
			fileobj.img.actualRotation = 0;
			this.setImageEditOptionsFile(fileobj); // write actually valid editoptions

			fileobj.img.processedOptions = { // reset last edited options
				doEdit: false,
				from: 'general',
				scaleWhat: 'pixel_l',
				scale: 0,
				rotate: 0,
				quality: this.OPTS_QUALITY_NEUTRAL_VAL
			};
			fileobj.isEdited = false;
			this.memorymanagerRegister(fileobj);
		}

		return;
	};

	this.openImageEditor = function(pos){
		// to be overridden
	};

	this.processImage = function(fileobj, task) {
		if(!fileobj){
			this.processNextImage();
			return;
		}

		switch(task) {
			case this.IMG_START:
				if(this.PROCESS_PREVIEWS_ONLY && fileobj.img.previewCanvas){
					this.processImage(fileobj, this.IMG_NEXT);
					return;
				}
				this.processimageExtractLandscape(fileobj, this.IMG_LOAD_CANVAS);
				break;
			case this.IMG_LOAD_CANVAS: // TODO: make IMG_START
				if(!fileobj.img.editOptions.doEdit && fileobj.size > 10485760){ // nothing to edit and image is too big for preview (>10MB)
					this.processImage(fileobj, this.IMG_NEXT);
					return;
				}
				this.processimageLoadCanvas(fileobj, fileobj.img.editOptions.doEdit ? this.IMG_EXTRACT_METADATA : this.IMG_MAKE_PREVIEW);
				break;
			case this.IMG_EXTRACT_METADATA:
				this.processimageExtractMetadata(fileobj, this.IMG_SCALE);
				//self.processimageScale(fileobj, self.IMG_ROTATE);
				break;
			case this.IMG_SCALE:
				this.processimageScale(fileobj, this.IMG_ROTATE);
				//self.processimageRotate(fileobj, self.IMG_APPLY_FILTERS);
				break;
			case this.IMG_ROTATE:
				this.processimageRotate(fileobj, this.IMG_APPLY_FILTERS);
				//self.processimageExtractMetadata(fileobj, self.IMG_SCALE);
				break;
			case this.IMG_APPLY_FILTERS:
				this.imageEdit.processimageApplyFilters(fileobj, this.IMG_WRITE_IMAGE);
				break;
			case this.IMG_WRITE_IMAGE:
				this.processimageWriteImage(fileobj, this.IMG_INSERT_METADATA);
				//self.processimageWriteImage(fileobj, self.IMG_MAKE_PREVIEW);
				break;
			case this.IMG_INSERT_METADATA:
				this.processimagInsertMetadata(fileobj, this.IMG_MAKE_PREVIEW);
				break;
			case this.IMG_MAKE_PREVIEW:
				this.processimageMakePreview(fileobj, this.IMG_POSTPROCESS);
				break;
			case this.IMG_POSTPROCESS:
				this.processimagePostProcess(fileobj, this.IMG_NEXT);
				break;
			case this.IMG_NEXT:
			/*falls through*/
			default:
				this.processNextImage();
		}
	};

	this.resetImageEdit = function (fileobj) {
		fileobj.dataArray = null;
		fileobj.dataUrl = null;
		if(this.IS_MEMORY_MANAGMENT){
			this.memorymanagerRegister(fileobj);
		}
	};

	this.abstractSetImageEditOptionsGeneral = function (formname) {
		var form = this.uploader.doc.forms[(formname ? formname : 'we_form')],
			scale = form.elements.fuOpts_scale.value,
			deg = parseInt(form.elements.fuOpts_rotate.value),
			quality = parseInt(form.elements.fuOpts_quality.value),
			opts = this.imageEditOptions;

		scale = parseInt(scale ? scale : 0);

		if (parseInt(form.elements.fuOpts_doEdit.value) === 1 && (scale || deg || quality !== this.OPTS_QUALITY_NEUTRAL_VAL)) {
			opts.doEdit = true;
			opts.scaleWhat = form.elements.fuOpts_scaleWhat.value;
			opts.scale = scale;
			opts.rotate = deg;
			opts.quality = quality;
		} else {
			opts.doEdit = false;
			opts.scaleWhat = 'pixel_l';
			opts.scale = 0;
			opts.rotate = 0;
			opts.quality = this.OPTS_QUALITY_NEUTRAL_VAL;
		}
	};

	this.setImageEditOptionsGeneral = function () {
		this.abstractSetImageEditOptionsGeneral();
	};

	this.setImageEditOptionsFile = function (fileobj) {
		fileobj.img.editOptions = JSON.parse(JSON.stringify(this.imageEditOptions));
		//fileobj.img.editOptions.from = type; // FIXME: what type is this?
	};

	this.processimageExtractLandscape = function(fileobj, nexttask) {
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
				this.processImage(fileobj, nexttask);
			};
			reader.readAsArrayBuffer(fileobj.file.slice(0, 128 * 1024));
		} else {
			fileobj.img.isOrientationChecked = true;
			this.processImage(fileobj, nexttask);
		}
	};

	this.processimageLoadCanvas = function(fileobj, nexttask) {
		var reader = new FileReader();

		reader.onload = function() {
			if(!fileobj.img.editOptions.doEdit){ // we will not edit and rewrite image so we hold original dataUrl in fileobj to be used by fullpreview
				fileobj.dataUrl = reader.result;

				if(fileobj.img.previewCanvas){ // preview done during earlier editing: directly jump top postprocess
					this.processimagePostProcess(fileobj, this.IMG_NEXT);
					return;
				}
			}

			fileobj.img.image = new Image();
			fileobj.img.image.onload = function() {
				fileobj.img.workingCanvas = this.uploader.doc.createElement('canvas');
				if(!fileobj.img.image && this.processimageRepeatLoadCanvas < 5){
					this.processimageRepeatLoadCanvas++;
					this.processimageLoadCanvas(fileobj, nexttask);
				}
				this.processimageRepeatLoadCanvas = 0;
				fileobj.img.workingCanvas.width = fileobj.img.image.width;
				fileobj.img.workingCanvas.height = fileobj.img.image.height;
				if(!fileobj.img.origWidth || !fileobj.img.origHeight){
					fileobj.img.origWidth = fileobj.img.image.width;
					fileobj.img.origHeight = fileobj.img.image.height;
					this.setImageEditOptionsFile(fileobj); // set editOptions again after orig dimensions are extracted
				}
				fileobj.img.workingCanvas.getContext("2d").drawImage(fileobj.img.image, 0, 0);
				fileobj.img.image = null;
				this.utils.logTimeFromStart('canvas loaded');
				nexttask = this.PROCESS_PREVIEWS_ONLY ? this.IMG_MAKE_PREVIEW : nexttask;
				this.processImage(fileobj, nexttask);
			};

			this.view.repaintImageEditMessage(true, true);
			fileobj.img.image.src = reader.result;
		};
		reader.readAsDataURL(fileobj.file);
	};

	this.processimageExtractMetadata = function(fileobj, nexttask) {
		switch(fileobj.type){
			case 'image/jpeg':
				if(fileobj.img.jpgCustomSegments){
					this.processImage(fileobj, nexttask);
					return;
				}
				this.processimageExtractMetadataJPG(fileobj, nexttask);
				break;
			case 'image/png':
				this.processimageExtractMetadataPNG(fileobj, nexttask);
				break;
			default:
				this.processImage(fileobj, nexttask);
		}
	};

	this.processimageExtractMetadataJPG = function(fileobj, nexttask, complete) {
			var reader = new FileReader();
			var segments;

			reader.onloadend = function () {
				this.utils.logTimeFromStart('file read to extract meta');
				segments = this.jpgGetSegmentsIfExist(new Uint8Array(reader.result), [225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239], true);
				if(segments === false && !fileobj.img.extractMetaSecondTry){
					// 128 KB was not enough to reach SOS: we try again reading the whole file
					fileobj.img.extractMetaSecondTry = true;
					this.processimageExtractMetadataJPG(fileobj, nexttask, true);
				}
				fileobj.img.jpgCustomSegments = segments;
				this.utils.logTimeFromStart('meta jpg extracted');
				this.view.repaintImageEditMessage(true);
				if(nexttask){
					this.processImage(fileobj, nexttask);
				}
			};
			reader.readAsArrayBuffer((complete ? fileobj.file : fileobj.file.slice(0, 128 * 1024)));
	};

	this.processimageExtractMetadataPNG = function(fileobj, nexttask) {
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

			this.utils.logTimeFromStart('meta png exttracted');
			this.view.repaintImageEditMessage(true);
			this.processImage(fileobj, nexttask);
		};
		reader.readAsArrayBuffer(fileobj.file);
	};

	this.processimageScale = function(fileobj, nexttask){
		if(!fileobj.img.editOptions.scale){
			this.utils.logTimeFromStart('scaling skipped');
			this.processImage(fileobj, nexttask);
			return; // IMPORTANT!
		}

		var scaleWhat = fileobj.img.editOptions.scaleWhat !== 'pixel_l' ? fileobj.img.editOptions.scaleWhat :
				(fileobj.img.workingCanvas.width >= fileobj.img.workingCanvas.height ? 'pixel_w' : 'pixel_h');
		var ratio = scaleWhat === 'pixel_w' ? fileobj.img.editOptions.scale/fileobj.img.workingCanvas.width :
					fileobj.img.editOptions.scale/fileobj.img.workingCanvas.height;
		if(ratio >= 1){
			this.utils.logTimeFromStart('scaling: image smaller than targetsize');
			this.processImage(fileobj, nexttask); // we do not upscale!
			return; // IMPORTANT!
		}

		var targetCanvas = this.uploader.doc.createElement('canvas');
		targetCanvas.width = fileobj.img.workingCanvas.width * ratio;
		targetCanvas.height = fileobj.img.workingCanvas.height * ratio;

		WE().layout.fileupload.pica.resizeCanvas(fileobj.img.workingCanvas, targetCanvas, this.PICA_CONFIGURATION, function (err) {
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

			this.utils.logTimeFromStart('scaling done');
			fileobj.isEdited = true;

			this.view.repaintImageEditMessage(true);
			this.processImage(fileobj, nexttask);
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
				this.utils.logTimeFromStart('rotation skipped');
				this.processImage(fileobj, nexttask);
				return;
		}

		var targetCanvas = this.uploader.doc.createElement('canvas'),
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
			this.utils.logTimeFromStart('rotation done');
			this.view.repaintImageEditMessage(true);
			this.processImage(fileobj, nexttask);
		}
	};

	this.processimageApplyFilters = function(fileobj, nexttask){
		this.processImage(fileobj, nexttask);
	};

	this.processimageWriteImage_2 = function(fileobj, nexttask){
		fileobj.img.workingCanvas.toBlob(function (blob) { // DO WE NEED toBlob TO GET UINT8ARRAY?
															// THIS FN CAUASES PROBLEMS WITH PNG!
			var reader = new FileReader();
			reader.onload = function() {
				fileobj.dataArray = new Uint8Array(reader.result);
				this.utils.logTimeFromStart('image written');
				this.processImage(fileobj, nexttask);
			};
			reader.readAsArrayBuffer(blob);
		}, fileobj.type, (fileobj.img.editOptions.quality/100));
	};

	this.processimageWriteImage = function(fileobj, nexttask){
		/*
		if(fileobj.type !== 'image/png'){
			self.processimageWriteImage_2(fileobj, nexttask);
		}
		*/
		fileobj.dataUrl = fileobj.img.workingCanvas.toDataURL(fileobj.type, (fileobj.img.editOptions.quality/90));
		fileobj.dataArray = this.utils.dataURLToUInt8Array(fileobj.dataUrl);
		//self.jpgGetSegmentsIfExist(fileobj.dataArray, [225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239], true);

		if(!this.PRESERVE_IMG_DATAURL){
			fileobj.dataUrl = null;
		}
		fileobj.isEdited = fileobj.img.editOptions.quality < this.OPTS_QUALITY_NEUTRAL_VAL ? true : fileobj.isEdited;

		this.utils.logTimeFromStart('image written fn 2');
		this.processImage(fileobj, nexttask);
	};

	this.processimagInsertMetadata = function(fileobj, nexttask) {
		switch(fileobj.type){
			case 'image/jpeg':
				this.processimagInsertMetadataJPG(fileobj, nexttask);
				break;
			case 'image/png':
				this.processimagInsertMetadataPNG(fileobj, nexttask);
				break;
			default:
				this.utils.logTimeFromStart('no metadata to reinsert');
				this.processImage(fileobj, nexttask);
		}
	};

	this.processimagInsertMetadataJPG = function(fileobj, nexttask) {
		if(fileobj.img.jpgCustomSegments) {
			fileobj.dataArray = this.jpgInsertSegment(fileobj.dataArray, fileobj.img.jpgCustomSegments);
			fileobj.size = fileobj.dataArray.length;
			this.view.repaintImageEditMessage(true);
		}

		this.utils.logTimeFromStart('metadata reinserted');
		this.processImage(fileobj, nexttask);
	};

	this.processimagInsertMetadataPNG = function(fileobj, nexttask) {
		this.utils.logTimeFromStart('metadata reinsert skipped');
		//self.processImage(fileobj, nexttask);

		if(fileobj.img.pngTextChunks && fileobj.img.pngTextChunks.length){
			fileobj.dataArray = this.pngReinsertTextchunks(fileobj.dataArray, fileobj.img.pngTextChunks);

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

			this.view.repaintImageEditMessage(true);
		}

		this.utils.logTimeFromStart('metadata reinserted');
		this.processImage(fileobj, nexttask);
	};

	this.processimageMakePreview = function(fileobj, nexttask){
		if(fileobj && fileobj.img.workingCanvas){
			if(fileobj.img.previewCanvas){
				this.processimageRotatePreview(fileobj, -1, nexttask);
				return;
			}

			var tmpCanvas = this.uploader.doc.createElement("canvas"),
				ctxTmpCanvas,
				previewCanvas = this.uploader.doc.createElement("canvas"),
				clone = null,
				prevWidth, prevHeight;

			if(fileobj.img.workingCanvas.width < this.view.previewSize && fileobj.img.workingCanvas.height < this.view.previewSize){
				prevHeight = fileobj.img.workingCanvas.width;
				prevWidth = fileobj.img.workingCanvas.height;
			} else {
				if(fileobj.img.workingCanvas.width > fileobj.img.workingCanvas.height){
					prevWidth = this.view.previewSize;
					prevHeight = Math.round(this.view.previewSize / fileobj.img.workingCanvas.width * fileobj.img.workingCanvas.height);

				} else {
					prevHeight = this.view.previewSize;
					prevWidth = Math.round(this.view.previewSize / fileobj.img.workingCanvas.height * fileobj.img.workingCanvas.width);
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
				this.processimageRotate(fileobj, -1, true, 0, true);
			}

			tmpCanvas = ctxTmpCanvas = previewCanvas = clone = null;

			this.utils.logTimeFromStart('preview done');
		}

		this.processImage(fileobj, nexttask);
		return;
	};

	this.processimageRotatePreview = function(fileobj, deg, nexttask){
			if(fileobj && fileobj.img.previewCanvas){
				var targetRotation = deg === -1 ? fileobj.img.actualRotation : deg;
				var realRotation = (targetRotation - fileobj.img.previewRotation + 360)  % 360;

				this.processimageRotate(fileobj, -1, true, realRotation);
				this.view.replacePreviewCanvas(fileobj);
				fileobj.img.previewRotation = targetRotation;
			}

			if(!nexttask){
				return;
			}
			this.processImage(fileobj, nexttask);
	};

	this.processimagePostProcess = function(fileobj, nexttask){
		fileobj.img.originalSize = fileobj.file.size;

		if(fileobj.dataArray && fileobj.isEdited){
			fileobj.size = fileobj.dataArray.length;
		}
		fileobj.dataArray = null; // we recompute it from dataUrl and metas while uploading
		if(false && this.PROCESS_PREVIEWS_ONLY){
			fileobj.dataUrl = null;
		}

		fileobj.img.processedWidth = 0;//fileobj.img.fullPrev.width;
		fileobj.img.processedHeight = 0; //fileobj.img.fullPrev.height;

		fileobj.img.workingCanvas = null;
		fileobj.img.fullPrev = null;

		fileobj.img.processedOptions = JSON.parse(JSON.stringify(fileobj.img.editOptions));
		fileobj.totalParts = Math.ceil(fileobj.size / this.sender.chunkSize);
		fileobj.lastChunkSize = fileobj.size % this.sender.chunkSize;
		this.sender.preparedFiles[fileobj.preparedFilesIndex] = fileobj; // do we need this?
		this.view.repaintEntry(fileobj);
		this.view.repaintImageEditMessage();
		this.utils.logTimeFromStart('processing finished', false, fileobj);

		if(false && this.IS_MEMORY_MANAGMENT){
			this.memorymanagerRegister(fileobj);
			if(this.memorymanagerIsOverflow()){
				this.memorymanagerEmptySpace();
				//win.console.log('emptied space for this file');

				if(this.imageFilesToProcess.length){ //
					if(fileobj.img.editOptions.doEdit && this.memorymanagerIsUneditedPreviewToDelete){
						//win.console.log('dataURLs of unedited where deleted: go on');
					} else {
						//win.console.log('processing images must be stopped: we will make previews if needed and then stop');
						this.PROCESS_PREVIEWS_ONLY = true;
					}
				}
			}
		}

		if(fileobj.processSingleImage){
			this.processSingleImage(fileobj, true);
		} else {
			this.processImage(fileobj, nexttask);
		}
	};

	this.memorymanagerRegister = function(fileobj){
		var m = this.memoryManagement;
		this.memorymanagerUnregister(fileobj);

		if(fileobj.dataArray || fileobj.dataUrl){
			var size = parseInt((fileobj.dataArray ? fileobj.dataArray.length : 0)) + parseInt((fileobj.dataUrl ? fileobj.dataUrl.length : 0));

			m[fileobj.isEdited ? 'queueEdited' : 'queueNotEdited'].push(fileobj.index);
			m.registeredValues['o_' + fileobj.index] = size;
			m.registeredSum += size;
		}
		//win.console.log('register', m, sender.preparedFiles);
	};

	this.memorymanagerUnregister = function(fileobj){
		var m = this.memoryManagement, i;

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

	this.memorymanagerReregisterAll = function(){
		this.memorymanagerReset();

		for(var i = 0; i < this.sender.preparedFiles.length; i++){
			this.memorymanagerRegister(this.sender.preparedFiles[i]);
		}
		//win.console.log('register all', self.memoryManagement, sender.preparedFiles);
	};

	this.memorymanagerReset = function(){
		this.memoryManagement = {
			registeredSum: 0,
			registeredValues: {},
			queueEdited: [],
			queueNotEdited: []
		};
	};

	this.memorymanagerIsOverflow = function(){
		return this.memoryManagement.registeredSum > this.MEMORY_LIMIT;
	};

	this.memorymanagerIsUneditedPreviewToDelete = function(){
		return this.memoryManagement.queueNotEdited.length > 0;
	};

	this.memorymanagerEmptySpace = function(){
		var m = this.memoryManagement,
			fileobj, index;

		while((m.queueEdited.length || m.queueNotEdited.length) && m.registeredSum > this.MEMORY_LIMIT){
			index = m.queueNotEdited.length ? m.queueNotEdited.shift() : m.queueEdited.shift();
			fileobj = this.sender.preparedFiles[index];

			this.uneditImage(fileobj.index);
			this.view.setEditStatus('', fileobj.index);
		}
	};

	this.jpgInsertSegment = function (uint8array, exifSegment) {
		if(uint8array[0] === 255 && uint8array[1] === 216 && uint8array[2] === 255 && uint8array[3] === 224){ // type=jpg
			var pos = 0;
			if(!uint8array.prototype.indexOf){ // IE11
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

			return this.utils.concatTypedArrays(uint8array, [head, exifSegment, segments]);
		}
	};

	this.jpgGetExifSegment = function (uint8array){ // unused?
		return this.jpgGetSegment(uint8array, 225);
	};

	// use this to extract a certain segment: not intended to be used several times in sequence
	this.jpgGetSegment = function (uint8array, marker) {
		var head = 0;

		while (head < uint8array.length) {
			if (uint8array[head] === 255 && uint8array[head + 1] === 218){ // SOI = Scan of Image = image data (eg. canvas works on)!
				break;
			}

			if (uint8array[head] === 255 && uint8array[head + 1] === 216){ // omit 216 (D8): SOI = Start of Image (is empty so length = 2 bytes)
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

	this.jpgGetAllSegmentsUpToSOS = function(uint8array) { // unused?
		var head = 0,
			segments = {},
			order = [];

		while (head < uint8array.length) {
			// each segment starts with 255 (FF) and its segment marker
			if (uint8array[head] === 255 && uint8array[head + 1] === 218){ // 218 (DA): SOS = Start of Scan = image data (canvas works on)
				//win.console.log('found SOS at pos', head, segments);
				break;
			}
			if (uint8array[head] === 255 && uint8array[head + 1] === 216){ // omit 216 (D8): SOI = Start of Image (is empty so length = 2 bytes)
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
		var SOSfound;

		for (var i = 0; i < segments.length; i++) {
			searchObj[segments[i]] = true;
		}

		while (head < uint8array.length) {
			if (uint8array[head] === 255 && uint8array[head + 1] === 218){ // SOI = Scan of Image = image data (eg. canvas works on)!
				SOSfound = true;
				break;
			}

			if (uint8array[head] === 255 && uint8array[head + 1] === 216){ // omit 216 (D8): SOI = Start of Image (is empty so length = 2 bytes)
				head += 2;
			} else {
				var length = uint8array[head + 2] * 256 + uint8array[head + 3],
					endPoint = head + length + 2;

				if(searchObj[uint8array[head + 1]] === true) {
					segmentsArr.push(uint8array.subarray(head, endPoint));
					controllArr.push({marker: uint8array[head + 1], head: head, length: (endPoint-head)});

					/*
					delete searchObj[uint8array[head + 1]]; // there can be duplicate segements: we want o restore them as is!
					if (searchObj === {}){ // nothing left to search for
						break;
					}
					*/
				}

				head = endPoint;
			}
		}
		if(!SOSfound){
			return false;
		}

		return concat ? this.utils.concatTypedArrays(Uint8Array, segmentsArr) : segmentsArr;
	};

	this.pngReinsertTextchunks = function(dataArray, pngTextChunks){
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

function Fileupload_imageEdit_base(uploader) {
	Fileupload_imageEdit_abstract.call(this, uploader);

	this.uploader = uploader;
}
Fileupload_imageEdit_base.prototype = Object.create(Fileupload_imageEdit_abstract.prototype);
Fileupload_imageEdit_base.prototype.constructor = Fileupload_imageEdit_base;

function Fileupload_imageEdit_bindoc(uploader) {
	Fileupload_imageEdit_abstract.call(this, uploader);

	this.uploader = uploader;

	this.setImageEditOptionsFile = function () {
		this.setImageEditOptionsGeneral();
		if(this.sender.preparedFiles.length){
			var fileobj = this.sender.preparedFiles[0];

			fileobj.img.editOptions = JSON.parse(JSON.stringify(this.imageEditOptions));
			fileobj.img.editOptions.quality = fileobj.type === 'image/jpeg' ? fileobj.img.editOptions.quality : this.OPTS_QUALITY_NEUTRAL_VAL;
			fileobj.img.editOptions.from = 'general';

			if(fileobj.img.editOptions.rotate % 180 === 90 && fileobj.img.editOptions.scaleWhat !== 'pixel_l'){
				fileobj.img.editOptions.scaleWhat = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? 'pixel_h' : 'pixel_w';
			}

			// the following is identical in importer: move to new fn on abstract
			var scaleReference = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? fileobj.img.origWidth : (
					fileobj.img.editOptions.scaleWhat === 'pixel_h' ? fileobj.img.origHeight : Math.max(fileobj.img.origHeight, fileobj.img.origWidth));

			if(scaleReference && (scaleReference < fileobj.img.editOptions.scale)){
				fileobj.img.editOptions.scale = '';
				fileobj.img.tooSmallToScale = true;
			} else {
				fileobj.img.tooSmallToScale = false;
			}

			fileobj.img.editOptions.doEdit = fileobj.img.editOptions.scale || fileobj.img.editOptions.rotate || (fileobj.img.editOptions.quality !== this.OPTS_QUALITY_NEUTRAL_VAL) ? true : false;
		}
	};

	this.getImageEditIndices = function(pos, general){
		return this.sender.preparedFiles.length ? [0] : [];
	};
}
Fileupload_imageEdit_bindoc.prototype = Object.create(Fileupload_imageEdit_abstract.prototype);
Fileupload_imageEdit_bindoc.prototype.constructor = Fileupload_imageEdit_bindoc;

function Fileupload_imageEdit_import(uploader) {
	Fileupload_imageEdit_abstract.call(this, uploader);

	this.PRESERVE_IMG_DATAURL = true;
	this.IS_MEMORY_MANAGMENT = true;
	this.isImageEditActive = true;

	this.setImageEditOptionsFile = function (fileobj, general) {
		var indices = this.getImageEditIndices(general ? -1 : fileobj.index, general);

		// first global editOptiona are read out
		this.setImageEditOptionsGeneral();

		for(var i = 0; i < indices.length; i++){
			fileobj = this.sender.preparedFiles[indices[i]];
			var form = this.uploader.doc.getElementById('form_editOpts_' + fileobj.index);

			fileobj.img.editOptions = JSON.parse(JSON.stringify(this.imageEditOptions));

			// reset quality to neutral for all images but jpeg
			fileobj.img.editOptions.quality = fileobj.type === 'image/jpeg' ? fileobj.img.editOptions.quality : this.OPTS_QUALITY_NEUTRAL_VAL;

			// we sync general rotation to entries, thus fuOpts_rotate on entries is allways actual: take rotation from there
			if(parseInt(form.elements.fuOpts_rotate.value) === -1){ // initial value when entry is just added
				form.elements.fuOpts_rotate.value = fileobj.img.editOptions.rotate;
			} else {
				fileobj.img.editOptions.rotate = parseInt(form.elements.fuOpts_rotate.value);
			}

			if(fileobj.img.editOptions.rotate % 180 === 90 && fileobj.img.editOptions.scaleWhat !== 'pixel_l'){
				fileobj.img.editOptions.scaleWhat = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? 'pixel_h' : 'pixel_w';
			}

			// check for tooSmallToScale
			var scaleReference = fileobj.img.editOptions.scaleWhat === 'pixel_w' ? fileobj.img.origWidth : (
					fileobj.img.editOptions.scaleWhat === 'pixel_h' ? fileobj.img.origHeight : Math.max(fileobj.img.origHeight, fileobj.img.origWidth));

			if(scaleReference < fileobj.img.editOptions.scale){
				fileobj.img.editOptions.scale = '';
				fileobj.img.tooSmallToScale = true;
			} else {
				fileobj.img.tooSmallToScale = false;
			}

			fileobj.img.editOptions.doEdit = parseInt(fileobj.img.editOptions.scale) || fileobj.img.editOptions.rotate || (parseInt(fileobj.img.editOptions.quality) !== this.OPTS_QUALITY_NEUTRAL_VAL) ? true : false;
		}
	};

	this.getImageEditIndices = function(index, general, formposition){
		var indices = [],
			forms, i;

		if(general){
			forms = this.uploader.doc.getElementsByName('form_editOpts');
			for(i = 0; i < forms.length; i++){
				index = forms[i].getAttribute('data-index');
				if (this.sender.preparedFiles[index] && this.EDITABLE_CONTENTTYPES.indexOf(this.sender.preparedFiles[index].type) !== -1 &&
								//!forms[i].elements.fuOpts_useCustomOpts.checked &&
								!this.sender.preparedFiles[index].isUploadStarted) {
					indices.push(formposition ? i : index);
				}
			}
		} else if (index !== undefined && index > -1 && this.sender.preparedFiles[index] &&
						this.EDITABLE_CONTENTTYPES.indexOf(this.sender.preparedFiles[index].type) !== -1) {
			indices.push(index);
			if(formposition){
				forms = this.uploader.doc.getElementsByName('form_editOpts');
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
Fileupload_imageEdit_import.prototype = Object.create(Fileupload_imageEdit_abstract.prototype);
Fileupload_imageEdit_import.prototype.constructor = Fileupload_imageEdit_import;
