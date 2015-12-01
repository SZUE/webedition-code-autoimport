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

	if (win.console) {
		console = {
			log: function () {
			}
		};
	}
})(window);

var weFileUpload = (function () {
	var _ = {};

	function Fabric(type) {
		if (_.self) {
			//singleton: but we do not return the object, when constructor is called more than once!
			return false;
		}
		switch (type) {
			case 'inc' :
				weFileUpload_inc.prototype = new weFileUpload_abstract();
				weFileUpload_inc.prototype.constructor = weFileUpload_inc;
				return new weFileUpload_inc();
			case 'imp' :
				weFileUpload_imp.prototype = new weFileUpload_abstract();
				weFileUpload_imp.prototype.constructor = weFileUpload_imp;
				return new weFileUpload_imp();
			case 'tag' :
				//for userInput typ img
				/*
				 weFileUpload_tag.prototype = new weFileUpload_abstract;
				 weFileUpload_tag.prototype.constructor = weFileUpload_tag;
				 return new weFileUpload_tag;
				 */
			case 'binDoc' :
				weFileUpload_binDoc.prototype = new weFileUpload_abstract();
				weFileUpload_binDoc.prototype.constructor = weFileUpload_binDoc;
				return new weFileUpload_binDoc();
		}
	}

	function weFileUpload_abstract() {
		//declare "protected" members: they are accessible from weFileUpload_include/imp too!
		_.fieldName = '';
		_.genericFilename = '';
		_.fileuploadType = 'abstract';
		_.uiClass = 'we_fileupload_ui_base';

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
				_.uiClass = conf.uiClass || _.uiClass;
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
			this.fileselectOnclick = function () {
			};

			this.checkIsPresetFiles = function () {

				if (_.controller.isPreset && WE().layout.weEditorFrameController.getVisibleEditorFrame().document.presetFileupload) {
					_.controller.fileSelectHandler(null, true, WE().layout.weEditorFrameController.getVisibleEditorFrame().document.presetFileupload);
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
							e.target.className = _.controller.elemFileDragClasses;
							//_.controller.fileselectOnclick();
						}
					}
					_.controller.fileselectOnclick();

					_.sender.imageFilesNotProcessed = [];
					for (var f, i = 0; i < files.length; i++) {
						if (!_.utils.contains(_.sender.preparedFiles, _.controller.selectedFiles[i])) {
							f = _.controller.prepareFile(_.controller.selectedFiles[i]);
							_.sender.preparedFiles.push(f);
							_.view.addFile(f, _.sender.preparedFiles.length);
						}
					}
					if (_.sender.EDIT_IMAGES_CLIENTSIDE) {
						_.controller.processImages();
					}
				}
			};

			this.prepareFile = function (f, isUploadable) {
				var fileObj = {
					file: f,
					fileNum: 0,
					dataArray: null,
					currentPos: 0,
					partNum: 0,
					currentWeightFile: 0,
					mimePHP: 'none',
					fileNameTemp: ''
				},
				transformables = ['image/jpeg', 'image/gif', 'image/png'], //TODO: add all transformable types
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
					_.sender.imageFilesNotProcessed.push(fileObj);
				}

				return fileObj;
			};

			this.processImages = function () {
				_.view.setImageEditMessage();
				_.controller.processNextImage();
			};

			this.processNextImage = function () {
				if (_.sender.imageFilesNotProcessed.length === 0) {
					// unlock GUI
					_.view.unsetImageEditMessage();
					return;
				}

				_.view.repaintImageEditMessage();

				var fileObj = _.sender.imageFilesNotProcessed.shift(),
								transformables = ['image/jpeg', 'image/gif', 'image/png'];//TODO: add all transformable types;

				if (transformables.indexOf(fileObj.type) !== -1) {
					fileObj.exif = {};

					/* ExifReader */
					// we allways extract exif data: to check orientation (and fix it if neccessary)
					var reader = new FileReader();
					reader.onload = function (event) {
						var exif, tags = {};

						try {
							exif = new ExifReader();
							exif.load(event.target.result);
							// The MakerNote tag can be really large. Remove it to lower memory usage.
							exif.deleteTag('MakerNote');
							fileObj.exif = exif.getAllTags();
						} catch (error) {
							top.console.debug('failed');
						}

						//_.controller.transformImage(fileObj);
						/* START TRANSFORM IMAGE*/
						var innerReader = new FileReader();
						innerReader.onloadend = function () {
							var tempImg = new Image(),
											ratio = 1;

							tempImg.src = innerReader.result;
							tempImg.onload = function () {
								var canvas = document.createElement('canvas'),
												ctx = canvas.getContext("2d"),
												deg = _.sender.transformAll.degrees,
												x = 0, y = 0,
												transformedCanvas;

								if (_.sender.transformAll.width) {
									ratio = _.sender.transformAll.widthSelect === 'percent' ? _.sender.transformAll.width / 100 : _.sender.transformAll.width / tempImg.width;
								} else if (_.sender.transformAll.height) {
									ratio = _.sender.transformAll.heightSelect === 'percent' ? _.sender.transformAll.height / 100 : _.sender.transformAll.height / tempImg.height;
								} else {
									ratio = 1;
								}
								ratio = ratio > 0 && ratio < 1 ? ratio : 1;

								// correct landscape using exif data
								if (fileObj.exif.Orientation && fileObj.exif.Orientation.value !== 1) {
									switch (fileObj.exif.Orientation.value) {
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
								deg = deg > 360 ? deg - 360 : deg;

								if (_.sender.transformAll.doTrans || deg !== 0) {
									canvas.width = tempImg.width;
									canvas.height = tempImg.height;

									// prepare rotation
									switch (deg) {
										case 90:
											canvas.width = tempImg.height;
											canvas.height = tempImg.width;
											x = -tempImg.width;
											break;
										case 270:
											canvas.width = tempImg.height;
											canvas.height = tempImg.width;
											y = -tempImg.height;
											break;
										case 180:
											x = -tempImg.width;
											y = -tempImg.height;
											break;
										default:
									}
								} else {
									// we use canvas only to downscale preview image
									canvas.width = 100;
									canvas.height = (100 / canvas.width) * tempImg.height;
								}
								ctx.rotate(-Math.PI * deg / 180);
								ctx.drawImage(tempImg, x, y, tempImg.width, tempImg.height);// TODO: when not transformed use smaller width/height!

								/* var 1: GameAlchemist @ http://stackoverflow.com/questions/18922880/html5-canvas-resize-downscale-image-high-quality */
								//top.console.debug(_.sender.transformAll);
								transformedCanvas = ratio !== 1 ? _.utils.downScaleCanvas(canvas, ratio) : canvas;
								canvas = null;

								fileObj.dataURL = transformedCanvas.toDataURL(fileObj.type, _.sender.transformAll.doTrans ? _.sender.transformAll.quality / 10 : 1);
								if (_.sender.transformAll.doTrans || deg !== 0) {
									fileObj.dataArray = _.utils.dataURLToUInt8Array(fileObj.dataURL);
									fileObj.size = fileObj.dataArray.length;
								} else {
									// send original image
									fileObj.dataArray = null;
									fileObj.size = fileObj.file.size;
								}

								fileObj.totalParts = Math.ceil(fileObj.size / _.sender.chunkSize);
								fileObj.lastChunkSize = fileObj.size % _.sender.chunkSize;
								//TODO: check the following flags again
								fileObj.isUploadable = true;
								fileObj.isTypeOk = true;
								fileObj.isSizeOk = true;
								fileObj.uploadConditionsOk = true;

								_.view.repaintEntry(fileObj);
								_.controller.processNextImage();
							};
						};
						innerReader.readAsDataURL(fileObj.file);
						/* END */
					};
					reader.readAsArrayBuffer(fileObj.file.slice(0, 128 * 1024));
					/* END */
					return;
				} else {
					_.controller.processNextImage();
				}
			};

			//TODO: maybe reintegrate into fn processNextImage()
			/*
			 this.transformImage = function(fileObj){
			 var reader = new FileReader();
			 reader.onloadend = function () {
			 var tempImg = new Image(),
			 ratio = 1;

			 tempImg.src = reader.result;
			 tempImg.onload = function () {
			 var canvas = document.createElement('canvas'),
			 ctx = canvas.getContext("2d"),
			 deg = _.sender.transformAll.degrees,
			 x = 0, y = 0,
			 transformedCanvas, uInt8Array;

			 if(_.sender.transformAll.width){
			 ratio = _.sender.transformAll.widthSelect === 'percent' ? _.sender.transformAll.width / 100 : _.sender.transformAll.width / tempImg.width;
			 } else if(_.sender.transformAll.height){
			 ratio = _.sender.transformAll.heightSelect === 'percent' ? _.sender.transformAll.height / 100 : _.sender.transformAll.height / tempImg.height;
			 }
			 ratio = ratio < 1 ? ratio : 1;

			 canvas.width = tempImg.width;
			 canvas.height = tempImg.height;

			 // correct landscape using exif data
			 if(fileObj.exif.Orientation && fileObj.exif.Orientation.value !== 1){
			 switch(fileObj.exif.Orientation.value) {
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
			 deg = deg > 360 ? deg - 360 : deg;

			 // prepare rotation
			 switch (deg) {
			 case 90:
			 canvas.width = tempImg.height;
			 canvas.height = tempImg.width;
			 x = -tempImg.width;
			 break;
			 case 270:
			 canvas.width = tempImg.height;
			 canvas.height = tempImg.width;
			 y = -tempImg.height;
			 break;
			 case 180:
			 x = -tempImg.width;
			 y = -tempImg.height;
			 break;
			 default:
			 }
			 ctx.rotate(-Math.PI * deg / 180);
			 ctx.drawImage(tempImg, x, y, tempImg.width, tempImg.height);

			 // var 1: GameAlchemist @ http://stackoverflow.com/questions/18922880/html5-canvas-resize-downscale-image-high-quality
			 transformedCanvas = _.utils.downScaleCanvas(canvas, ratio);
			 canvas = null;

			 fileObj.dataURL = transformedCanvas.toDataURL(fileObj.type, _.sender.transformAll.quality / 10);
			 fileObj.dataArray = _.utils.dataURLToUInt8Array(fileObj.dataURL);
			 fileObj.size = fileObj.dataArray.length;
			 fileObj.totalParts = Math.ceil(fileObj.size / _.sender.chunkSize);
			 fileObj.lastChunkSize = fileObj.size % _.sender.chunkSize;
			 //TODO: check the following flags again
			 fileObj.isUploadable = true;
			 fileObj.isTypeOk = true;
			 fileObj.isSizeOk = true;
			 fileObj.uploadConditionsOk = true;

			 _.view.repaintEntry(fileObj);
			 _.controller.processNextImage();
			 };
			 };
			 reader.readAsDataURL(fileObj.file);
			 };
			 */

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
			this.imageFilesNotProcessed = [];
			this.uploadFiles = [];
			this.currentFile = -1;
			this.totalFiles = 0;
			this.totalWeight = 0;
			this.currentWeight = 0;
			this.currentWeightTag = 0;//FIXME: find better name
			this.isAutostartPermitted = false;
			this.transformAll = {
				doTrans: false,
				width: 0,
				height: 0,
				widthSelect: 'pixel',
				heightSelect: 'pixel',
				keepRatio: true,
				quality: 0.8,
				degrees: 0
			};
			this.moreFieldsToAppend = [];
			this.EDIT_IMAGES_CLIENTSIDE = false;

			this.resetParams = function () {
			};

			this.prepareUpload = function () {
				return true;
			};

			//new client side image editing
			/*
			 this.transformAndSendFile = function (cur) {
			 top.console.debug(this.transformAll);
			 var reader = new FileReader();
			 reader.onloadend = function () {
			 var tempImg = new Image(),
			 ratio = 1;

			 tempImg.src = reader.result;
			 tempImg.onload = function () {
			 if(_.sender.transformAll.width){
			 ratio = _.sender.transformAll.widthSelect === 'percent' ? _.sender.transformAll.width / 100 : _.sender.transformAll.width / tempImg.width;
			 } else if(_.sender.transformAll.height){
			 ratio = _.sender.transformAll.heightSelect === 'percent' ? _.sender.transformAll.height / 100 : _.sender.transformAll.height / tempImg.height;
			 }
			 ratio = ratio < 1 ? ratio : 1;

			 // var 1: GameAlchemist @ http://stackoverflow.com/questions/18922880/html5-canvas-resize-downscale-image-high-quality
			 var canv = document.createElement('canvas');
			 canv.width = tempImg.width;
			 canv.height = tempImg.height;
			 canv.getContext('2d').drawImage(tempImg, 0, 0);
			 var resulting_canvas = _.utils.downScaleCanvas(canv, ratio);
			 var canvas = resulting_canvas;

			 // var 2: K3N @ http://stackoverflow.com/questions/17861447/html5-canvas-drawimage-how-to-apply-antialiasing


			 // var 3: simple resize + rotation

			 var MAX_WIDTH = 600;
			 var MAX_HEIGHT = 600;
			 var tempW = tempImg.width;
			 var tempH = tempImg.height;
			 if (tempW > tempH) {
			 if (tempW > MAX_WIDTH) {
			 tempH *= MAX_WIDTH / tempW;
			 tempW = MAX_WIDTH;
			 }
			 } else {
			 if (tempH > MAX_HEIGHT) {
			 tempW *= MAX_HEIGHT / tempH;
			 tempH = MAX_HEIGHT;
			 }
			 }

			 var canvas = document.createElement('canvas'),
			 ctx = canvas.getContext("2d"),
			 deg = 0,
			 x = 0, y = 0;

			 canvas.width = tempW;
			 canvas.height = tempH;
			 switch (deg) {
			 case 90:
			 canvas.width = tempH;
			 canvas.height = tempW;
			 x = -tempW;
			 break;
			 case 270:
			 canvas.width = tempH;
			 canvas.height = tempW;
			 y = -tempH;
			 break;
			 case 180:
			 x = -tempW;
			 y = -tempH;
			 break;
			 default:
			 }
			 ctx.rotate(-Math.PI * deg / 180);
			 ctx.drawImage(tempImg, x, y, tempW, tempH);

			 canvas.toBlob(function (blob) {
			 var arrayBufferNew = null;
			 var fr = new FileReader();
			 fr.onload = function (e) {
			 arrayBufferNew = this.result;
			 cur.dataArray = new Uint8Array(arrayBufferNew);
			 _.sender.sendNextChunk(true);

			 };
			 cur.size = blob.size;
			 //_.view.setInternalProgress(0);
			 cur.totalParts = Math.ceil(blob.size / _.sender.chunkSize);
			 cur.lastChunkSize = blob.size % _.sender.chunkSize;
			 fr.readAsArrayBuffer(blob);
			 }, "image/jpeg", 1.0
			 );

			 };
			 };
			 reader.readAsDataURL(cur.file);
			 };
			 */

			this.sendNextFile = function () {
				var cur, fr = null, cnt,
								that = _.sender;//IMPORTANT: if we use that = this, then that is of type AbstractSender not knowing members of Sender!

				if (this.uploadFiles.length > 0) {
					this.currentFile = cur = this.uploadFiles.shift();
					if (cur.uploadConditionsOk) {
						this.isUploading = true;
						_.view.repaintGUI({what: 'startSendFile'});

						if (cur.size <= this.chunkSize && !this.transformAll.doTrans) {//&& !cur.doTrans!
							this.sendNextChunk(false);
						} else {
							if (_.view.elems.fileSelect && _.view.elems.fileSelect.value) {
								_.view.elems.fileSelect.value = '';
							}
							var transformables = ['image/jpeg', 'image/gif', 'image/png'];//TODO: add all transformable types

							//clientside editing disabled!
							if (false && this.EDIT_IMAGES_CLIENTSIDE && this.transformAll.doTrans && transformables.indexOf(cur.type) !== -1) {//TODO: && !cur.doTrans!)
								that.sendNextChunk(true);
							} else {
								fr = new FileReader();
								fr.onload = function (e) {
									cnt = e.target.result;
									cur.dataArray = new Uint8Array(cnt);
									//from inside FileReader we must reference sender by that (or _.sender)
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
								cur = this.currentFile;

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

			this.setImageEditMessage = function () {
			};

			this.unsetImageEditMessage = function () {
			};

			this.repaintImageEditMessage = function () {
			};

			this.repaintGUI = function (arg) {
			};

			this.repaintEntry = function (obj) {
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

				if (tc.accepted.mime && tc.accepted.mime.length > 0 && type === '') {
					return false;
				}
				if (tc.accepted.all && tc.accepted.all.length > 0 &&
						!WE().util.in_array(type, tc.accepted.all) &&
						!WE().util.in_array(typeGroup, tc.accepted.all) &&
						!WE().util.in_array(ext, tc.accepted.all)) {
					return false;
				}
				if (tc.forbidden.all && tc.forbidden.all.length > 0 &&
						(WE().util.in_array(type, tc.forbidden.all) ||
						WE().util.in_array(typeGroup, tc.forbidden.all) ||
						WE().util.in_array(ext, tc.forbidden.all))) {
					return false;
				}

				return true;

			};

			this.computeSize = function (size) {
				return (size / 1024 > 1023 ? ((size / 1024) / 1024).toFixed(1) + ' MB' : (size / 1024).toFixed(1) + ' KB');
			};

			/* GameAlchemist @ http://stackoverflow.com/questions/18922880/html5-canvas-resize-downscale-image-high-quality */
			//TODO: try to scale width and height by different ratio
			this.downScaleCanvas = function (cv, scale) {
				if (scale <= 0 || scale >= 1){
					throw ('scale must be a positive number <1 ');
				}
				var sqScale = scale * scale; // square scale = area of source pixel within target
				var sw = cv.width; // source image width
				var sh = cv.height; // source image height
				var tw = Math.floor(sw * scale); // target image width
				var th = Math.floor(sh * scale); // target image height
				var sx = 0, sy = 0, sIndex = 0; // source x,y, index within source array
				var tx = 0, ty = 0, yIndex = 0, tIndex = 0; // target x,y, x,y index within target array
				var tX = 0, tY = 0; // rounded tx, ty
				var w = 0, nw = 0, wx = 0, nwx = 0, wy = 0, nwy = 0; // weight / next weight x / y
				// weight is weight of current source point within target.
				// next weight is weight of current source point within next target's point.
				var crossX = false; // does scaled px cross its current px right border ?
				var crossY = false; // does scaled px cross its current px bottom border ?
				var sBuffer = cv.getContext('2d').getImageData(0, 0, sw, sh).data; // source buffer 8 bit rgba
				var tBuffer = new Float32Array(4 * sw * sh); // target buffer Float32 rgb
				var sR = 0, sG = 0, sB = 0; // source's current point r,g,b
				// untested !
				var sA = 0;  //source alpha

				for (sy = 0; sy < sh; sy++) {
					ty = sy * scale; // y src position within target
					tY = 0 | ty;     // rounded : target pixel's y
					yIndex = 4 * tY * tw;  // line index within target array
					crossY = (tY != (0 | ty + scale));
					if (crossY) { // if pixel is crossing botton target pixel
						wy = (tY + 1 - ty); // weight of point within target pixel
						nwy = (ty + scale - tY - 1); // ... within y+1 target pixel
					}
					for (sx = 0; sx < sw; sx++, sIndex += 4) {
						tx = sx * scale; // x src position within target
						tX = 0 | tx;    // rounded : target pixel's x
						tIndex = yIndex + tX * 4; // target pixel index within target array
						crossX = (tX != (0 | tx + scale));
						if (crossX) { // if pixel is crossing target pixel's right
							wx = (tX + 1 - tx); // weight of point within target pixel
							nwx = (tx + scale - tX - 1); // ... within x+1 target pixel
						}
						sR = sBuffer[sIndex    ];   // retrieving r,g,b for curr src px.
						sG = sBuffer[sIndex + 1];
						sB = sBuffer[sIndex + 2];
						sA = sBuffer[sIndex + 3];

						if (!crossX && !crossY) { // pixel does not cross
							// just add components weighted by squared scale.
							tBuffer[tIndex    ] += sR * sqScale;
							tBuffer[tIndex + 1] += sG * sqScale;
							tBuffer[tIndex + 2] += sB * sqScale;
							tBuffer[tIndex + 3] += sA * sqScale;
						} else if (crossX && !crossY) { // cross on X only
							w = wx * scale;
							// add weighted component for current px
							tBuffer[tIndex    ] += sR * w;
							tBuffer[tIndex + 1] += sG * w;
							tBuffer[tIndex + 2] += sB * w;
							tBuffer[tIndex + 3] += sA * w;
							// add weighted component for next (tX+1) px
							nw = nwx * scale;
							tBuffer[tIndex + 4] += sR * nw; // not 3
							tBuffer[tIndex + 5] += sG * nw; // not 4
							tBuffer[tIndex + 6] += sB * nw; // not 5
							tBuffer[tIndex + 7] += sA * nw; // not 6
						} else if (crossY && !crossX) { // cross on Y only
							w = wy * scale;
							// add weighted component for current px
							tBuffer[tIndex    ] += sR * w;
							tBuffer[tIndex + 1] += sG * w;
							tBuffer[tIndex + 2] += sB * w;
							tBuffer[tIndex + 3] += sA * w;
							// add weighted component for next (tY+1) px
							nw = nwy * scale;
							tBuffer[tIndex + 4 * tw    ] += sR * nw; // *4, not 3
							tBuffer[tIndex + 4 * tw + 1] += sG * nw; // *4, not 3
							tBuffer[tIndex + 4 * tw + 2] += sB * nw; // *4, not 3
							tBuffer[tIndex + 4 * tw + 3] += sA * nw; // *4, not 3
						} else { // crosses both x and y : four target points involved
							// add weighted component for current px
							w = wx * wy;
							tBuffer[tIndex    ] += sR * w;
							tBuffer[tIndex + 1] += sG * w;
							tBuffer[tIndex + 2] += sB * w;
							tBuffer[tIndex + 3] += sA * w;
							// for tX + 1; tY px
							nw = nwx * wy;
							tBuffer[tIndex + 4] += sR * nw; // same for x
							tBuffer[tIndex + 5] += sG * nw;
							tBuffer[tIndex + 6] += sB * nw;
							tBuffer[tIndex + 7] += sA * nw;
							// for tX ; tY + 1 px
							nw = wx * nwy;
							tBuffer[tIndex + 4 * tw    ] += sR * nw; // same for mul
							tBuffer[tIndex + 4 * tw + 1] += sG * nw;
							tBuffer[tIndex + 4 * tw + 2] += sB * nw;
							tBuffer[tIndex + 4 * tw + 3] += sA * nw;
							// for tX + 1 ; tY +1 px
							nw = nwx * nwy;
							tBuffer[tIndex + 4 * tw + 4] += sR * nw; // same for both x and y
							tBuffer[tIndex + 4 * tw + 5] += sG * nw;
							tBuffer[tIndex + 4 * tw + 6] += sB * nw;
							tBuffer[tIndex + 4 * tw + 7] += sA * nw;
						}
					} // end for sx
				} // end for sy

				// create result canvas
				var resCV = document.createElement('canvas');
				resCV.width = tw;
				resCV.height = th;
				var resCtx = resCV.getContext('2d');
				var imgRes = resCtx.getImageData(0, 0, tw, th);
				var tByteBuffer = imgRes.data;
				// convert float32 array into a UInt8Clamped Array
				var pxIndex = 0; //
				for (sIndex = 0, tIndex = 0; pxIndex < tw * th; sIndex += 4, tIndex += 4, pxIndex++) {
					tByteBuffer[tIndex] = Math.ceil(tBuffer[sIndex]);
					tByteBuffer[tIndex + 1] = Math.ceil(tBuffer[sIndex + 1]);
					tByteBuffer[tIndex + 2] = Math.ceil(tBuffer[sIndex + 2]);
					tByteBuffer[tIndex + 3] = Math.ceil(tBuffer[sIndex + 3]);
				}
				// writing result to canvas.
				resCtx.putImageData(imgRes, 0, 0);
				return resCV;
			};

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

		//public functions
		this.startUpload = function () {
			if (_.sender.prepareUpload()) {
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
	}

	function weFileUpload_inc() {
		(function () {
			weFileUpload_abstract.call(this);

			Controller.prototype = this.getAbstractController();
			Sender.prototype = this.getAbstractSender();
			View.prototype = this.getAbstractView();
			Utils.prototype = this.getAbstractUtils();

			_.fileuploadType = 'inc';
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
							this.elems.progressMoreText.innerHTML = ' / ' + _.utils.computeSize(_.sender.currentFile.size);
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

	function weFileUpload_imp() {
		(function () {
			weFileUpload_abstract.call(this);

			Controller.prototype = this.getAbstractController();
			Sender.prototype = this.getAbstractSender();
			View.prototype = this.getAbstractView();
			Utils.prototype = this.getAbstractUtils();

			_.fileuploadType = 'imp';
			_.self = this;
			_.controller = new Controller();
			_.sender = new Sender();
			_.view = new View();
			_.utils = new Utils();
		})();

		this.init = function (conf) {
			_.init_abstract(conf);

			//initialize weFileUpload_imp properties: dispatch them to respective module-objects
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

			//init transformAll object
			var sf = document.we_startform,
							t = _.sender.transformAll;

			t.width = sf.fu_doc_width.value ? parseInt(sf.fu_doc_width.value) : t.width;
			t.height = sf.fu_doc_height.value ? parseInt(sf.fu_doc_height.value) : t.height;
			t.degrees = sf.fu_doc_degrees.value ? parseInt(sf.fu_doc_degrees.value) : t.degrees;
			t.doTrans = t.degrees || t.width || t.height ? true : false;
			if (t.doTrans) {
				t.widthSelect = sf.fu_doc_widthSelect.value ? sf.fu_doc_widthSelect.value : t.widthSelect;
				t.heightSelect = sf.fu_doc_heightSelect.value ? sf.fu_doc_heightSelect.value : t.heightSelect;
				t.keepRatio = sf.fu_doc_keepRatio.value ? true : t.fu_doc_keepRatio;
				t.quality = sf.fu_doc_quality.value ? parseFloat(sf.fu_doc_quality.value) : t.quality;
			}
		};

		function Controller() {
			var that = _.controller;

			this.replaceSelectionHandler = function (e) {
				//FIXME: the code of this function is redundant: make new fn using parts of FileSelectHandler()
				var files = e.target.files;
				var f;
				if (files[0] instanceof File && !_.utils.contains(_.sender.preparedFiles, files[0])) {
					f = _.controller.prepareFile(files[0]);
					var inputId = 'fileInput_uploadFiles_',
									index = e.target.id.substring(inputId.length),
									nameField = document.getElementById('name_uploadFiles_' + index),
									sizeField = document.getElementById('size_uploadFiles_' + index);

					_.sender.preparedFiles[index] = f.isSizeOk ? f : null;
					nameField.value = f.file.name;
					sizeField.innerHTML = f.isSizeOk ? _.utils.computeSize(f.size) : '<span style="color:red"> ' + ((_.sender.maxUploadSize / 1024) / 1024) + ' MB</span>';
				}

				if (f.isSizeOk) {
					if (!_.view.isUploadEnabled) {
						_.controller.enableWeButton('next', true);
						_.view.isUploadEnabled = true;
						_.sender.isCancelled = false;
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
							document.getElementById('div_rowProgress_' + i).style.display = '';
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
					if (!this.EDIT_IMAGES_CLIENTSIDE) {
						fd.append('fu_doc_width', sf.fu_doc_width.value);
						fd.append('fu_doc_height', sf.fu_doc_height.value);
						fd.append('fu_doc_widthSelect', sf.fu_doc_widthSelect.value);
						fd.append('fu_doc_heightSelect', sf.fu_doc_heightSelect.value);
						fd.append('fu_doc_keepRatio', sf.fu_doc_keepRatio.value);
						fd.append('fu_doc_quality', sf.fu_doc_quality.value);
						fd.append('fu_doc_degrees', sf.fu_doc_degrees.value);
					} else {
						fd.append('exif', JSON.stringify(cur.exif));
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
					eval(resp.completed);

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

			this.addFile = function (f, index) {
				this.appendRow(f, _.sender.preparedFiles.length - 1);
			};

			this.repaintEntry = function (fileObj) {
				fileObj.entry.getElementsByClassName('weFileUploadEntry_size')[0].innerHTML = (fileObj.isSizeOk ? _.utils.computeSize(fileObj.size) : '<span style="color:red">> ' + ((_.sender.maxUploadSize / 1024) / 1024) + ' MB</span>');//style.backgroundColor = 'orange';
			};

			this.setImageEditMessage = function () {
				document.getElementById('we_fileUpload_messageBg').style.display = 'block';
				document.getElementById('we_fileUpload_message').style.display = 'block';

				/* Popup-JS is blocked too
				 var l = window.screenX + 200,
				 t = window.screenY + 200, x = 17;
				 this.messageWindow = window.open('', 'popwin', "left = " + l + ", top = " + t + ", width = 320, height = 210,  toolbar = no, location = no, directories = no, status = no, menubar = no, scrollbars = no, resizable = no");
				 var content = "<!DOCTYPE html><html><head>";
				 content += "<title>Example</title>";
				 content += '<link href="/webEdition/lib/additional/fontawesome/css/font-awesome.min.css?e453b0856c5227f6105a807a734c492c" rel="styleSheet" type="text/css">';
				 content += "</head><body bgcolor=”#ccc”>";
				 content += "<p>Any HTML will work, just make sure to escape \"quotes\",";
				 content += 'or use single-quotes instead.</p>';
				 content += "<p>You can even pass parameters… (" + x + ")</p>";
				 content += '<span id="numSpan"><i class="fa fa-2x fa-spinner fa-pulse"></i></span>';
				 content += "</body></html>";
				 this.messageWindow.document.open();
				 this.messageWindow.document.write(content);
				 this.messageWindow.document.close();
				 */
			};

			this.unsetImageEditMessage = function () {
				document.getElementById('we_fileUpload_messageBg').style.display = 'none';
				document.getElementById('we_fileUpload_message').style.display = 'none';
			};

			this.repaintImageEditMessage = function () {
				document.getElementById('we_fileUpload_messageNr').innerHTML = _.sender.imageFilesNotProcessed.length;
			};

			this.appendRow = function (f, index) {
				var div,
					row = this.htmlFileRow.replace(/WEFORMNUM/g, index).
					replace(/WE_FORM_NUM/g, (this.nextTitleNr++)).
					replace(/FILENAME/g, (f.file.name)).
					replace(/FILESIZE/g, (f.isSizeOk ? _.utils.computeSize(f.size) : '<span style="color:red">> ' + ((_.sender.maxUploadSize / 1024) / 1024) + ' MB</span>'));

				weAppendMultiboxRow(row, '', 0, 0, 0, -1);
				f.entry = document.getElementById('div_uploadFiles_' + index);

				div = document.getElementById('div_upload_files');
				div.scrollTop = div.scrollHeight;
				document.getElementById('fileInput_uploadFiles_' + index).addEventListener('change', _.controller.replaceSelectionHandler, false);

				var el = document.getElementById('div_rowButtons_' + index);
				//el.style.backgroundSize = 'contain';
				//el.style.backgroundImage = "url(" + f.dataURL + ")";

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
						document.getElementById('div_upload_files').scrollTop = document.getElementById('div_uploadFiles_' + i).offsetTop - 200;
						this.setInternalProgressCompleted(true, i, '');
						return;
					case 'chunkNOK' :
						totalProg = (100 / s.totalWeight) * s.currentWeight;
						i = s.mapFiles[cur.fileNum];
						j = i + 1;

						document.getElementById('div_upload_files').scrollTop = document.getElementById('div_uploadFiles_' + i).offsetTop - 200;
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
						//scroll to top of files list
						document.getElementById('div_upload_files').scrollTop = 0;
						return;
					case 'cancelUpload' :
						i = s.mapFiles[cur.fileNum];
						this.setInternalProgressCompleted(false, s.mapFiles[cur.fileNum], _.utils.gl.cancelled);
						document.getElementById('div_upload_files').scrollTop = document.getElementById('div_uploadFiles_' + i).offsetTop - 200;

						for (j = 0; j < s.uploadFiles.length; j++) {
							var file = s.uploadFiles[j];
							this.setInternalProgressCompleted(false, s.mapFiles[file.fileNum], _.utils.gl.cancelled);
						}

						_.controller.setWeButtonState('reset_btn', true);
						_.controller.setWeButtonState('browse_harddisk_btn', true);
						return;
					case 'resetGui' :
						document.getElementById('td_uploadFiles').innerHTML = '';
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
					document.getElementById(_.fieldName + '_progress_image_' + index).className = 'progress_finished';
				} else {
					if (typeof document.images['alert_img_' + index] !== 'undefined') {
						document.images['alert_img_' + index].style.visibility = 'visible';
						document.images['alert_img_' + index].title = txt;
					}
					document.getElementById(_.fieldName + '_progress_image_' + index).className = 'progress_failed';
				}
			};
		}

		function Utils() {
		}
	}

	function weFileUpload_binDoc() {
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
			v.elems.fileDrag_state_0 = document.getElementById('div_fileupload_fileDrag_state_0');
			v.elems.fileDrag_state_1 = document.getElementById('div_fileupload_fileDrag_state_1');
			v.elems.dragInnerRight = document.getElementById('div_upload_fileDrag_innerRight');
			v.elems.divRight = document.getElementById('div_fileupload_right');
			v.elems.txtFilename = document.getElementById('span_fileDrag_inner_filename');
			v.elems.txtFilename_1 = document.getElementById('span_fileDrag_inner_filename_1');//??
			v.elems.txtSize = document.getElementById('span_fileDrag_inner_size');
			v.elems.txtType = document.getElementById('span_fileDrag_inner_type');
			v.elems.divBtnReset = document.getElementById('div_fileupload_btnReset');
			v.elems.divBtnCancel = document.getElementById('div_fileupload_btnCancel');
			v.elems.divBtnUpload = document.getElementById('div_fileupload_btnUpload');
			v.elems.divProgressBar = document.getElementById('div_fileupload_progressBar');
			v.elems.divButtons = document.getElementById('div_fileupload_buttons');

			v.spinner = document.createElement("i");
			v.spinner.className = "fa fa-2x fa-spinner fa-pulse";

			_.controller.checkIsPresetFiles();
		};

		function Controller() {
			this.elemFileDragClasses = 'we_file_drag we_file_drag_mask';
			this.doSubmit = false;

			this.setEditorIsHot = function () {
				if (_.uiClass !== 'we_fileupload_ui_wedoc') {
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
				if (_.uiClass !== 'we_fileupload_ui_wedoc') {
					var cur = this.currentFile;

					this.form.form.elements.weFileNameTemp.value = cur.fileNameTemp;
					this.form.form.elements.weFileCt.value = cur.mimePHP;
					this.form.form.elements.weFileName.value = cur.file.name;
					_.sender.currentFile = null;
					setTimeout(function () {
						_.sender.dialogCallback(resp);
					}, 100);
				} else {
					if (resp.status === 'success') {
						_.sender.currentFile = null;
						if (WE()) {
							window.we_cmd('update_file');
							WE().layout.weEditorFrameController.getActiveEditorFrame().getDocumentReference().frames.editHeader.we_setPath(resp.weDoc.path, resp.weDoc.text, 0, "published");
						}

						this.fireCallback();
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
			this.preview = null;
			this.STATE_RESET = 0;
			this.STATE_PREVIEW_OK = 1;
			this.STATE_PREVIEW_NOK = 2;
			this.STATE_UPLOAD = 3;

			this.addFile = function (f) {
				var sizeText = f.isSizeOk ? _.utils.gl.sizeTextOk + _.utils.computeSize(f.size) + ', ' :
								'<span style="color:red;">' + _.utils.gl.sizeTextNok + '</span>';
				var typeText = f.isTypeOk ? _.utils.gl.typeTextOk + f.type :
								'<span style="color:red;">' + _.utils.gl.typeTextNok + f.type + '</span>';

				this.elems.fileDrag_state_1.style.backgroundColor = 'rgb(243, 247, 255)';
				this.elems.txtFilename.innerHTML = f.file.name.substring(0, 19) + (f.file.name.lenght > 20 ? '...' : '');
				this.elems.txtSize.innerHTML = sizeText;
				this.elems.txtType.innerHTML = typeText;
				this.setDisplay('fileDrag_state_0', 'none');
				this.setDisplay('fileDrag_state_1', 'block');
				this.elems.dragInnerRight.innerHTML = '';

				if (f.uploadConditionsOk) {
					_.sender.isAutostartPermitted = true;
					_.controller.setEditorIsHot();
				} else {
					_.sender.isAutostartPermitted = false;
				}

				if (f.type.search("image/") !== -1 && f.uploadConditionsOk && f.size < 4194304) {
					//if (this.binDocType === 'image' && f.uploadConditionsOk && f.size < 4194304) {
					var reader = new FileReader();
					reader.onloadstart = function (e) {
						_.view.elems.dragInnerRight.appendChild(_.view.spinner);
					};

					reader.onload = function (e) {
						var maxSize = 100,
										mode = 'resize',
										image = new Image();

						image.onload = function () {
							if (mode !== 'resize') {
								if (image.width > image.height) {
									image.width = maxSize;
								} else {
									image.height = maxSize;
								}
								_.view.preview = image;
								_.view.elems.dragInnerRight.innerHTML = '';
								_.view.elems.dragInnerRight.appendChild(_.view.preview);
							} else {
								var width = image.width,
												height = image.height,
												cv = document.createElement('canvas');

								if (width > height) {
									if (width > maxSize) {
										height *= maxSize / width;
										width = maxSize;
									}
								} else {
									if (height > maxSize) {
										width *= maxSize / height;
										height = maxSize;
									}
								}
								cv.width = width;
								cv.height = height;
								cv.getContext('2d').drawImage(image, 0, 0, width, height);

								_.view.preview = new Image();
								_.view.preview.src = cv.toDataURL('image/png');
								_.view.elems.dragInnerRight.innerHTML = '';
								_.view.elems.dragInnerRight.appendChild(_.view.preview);
							}
							_.view.setGuiState(f.uploadConditionsOk ? _.view.STATE_PREVIEW_OK : _.view.STATE_PREVIEW_NOK);
							image = reader = null;
						};
						image.src = e.target.result;
					};

					if (f.size < 4194304) {
						reader.readAsDataURL(f.file);
					} else {
						this.preview = new Image();
						this.preview.onload = function () {
							_.view.elems.dragInnerRight.appendChild(_.view.preview);
						};
						this.preview.src = this.icon;
						this.setGuiState(this.STATE_PREVIEW_OK);
					}
				} else {
					if (f.uploadConditionsOk) {
						this.elems.dragInnerRight.innerHTML = '<div class="largeicons" style="margin:0 0 0 30px;height:62px;width:54px;">' + this.icon + '</div>';
						this.setGuiState(this.STATE_PREVIEW_OK);
					} else {
						this.elems.dragInnerRight.innerHTML = '<div style="margin:0px 0 0 30px;height:62px;width:54px;border:dotted 1px gray;padding-top:14px;text-align:center;background-color:#f9f9f9;color:#ddd;font-size:32px;font-weight:bold">!?</div>';
						this.setGuiState(this.STATE_PREVIEW_NOK);
					}
				}
			};

			this.setGuiState = function (state) {
				switch (state) {
					case this.STATE_RESET:
						this.setDisplay('fileDrag_state_0', 'block');
						this.setDisplay('fileDrag_state_1', 'none');
						this.setDisplay('fileInputWrapper', 'block');
						if (this.isDragAndDrop && this.elems.fileDrag) {
							this.setDisplay('fileDrag', 'block');
							//this.elems.fileDrag.className = 'we_file_drag we_file_drag_mask';
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
						if (_.uiClass === 'we_fileupload_ui_wedoc') {
							this.setDisplay('divBtnReset', 'none');
						}
						this.setDisplay('divBtnUpload', 'none');
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
		}

		function Utils() {}

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