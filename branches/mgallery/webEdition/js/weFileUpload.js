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
		_.fileuploadType = 'abstract';
		_.isLegacyMode = false;

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
				_.isLegacyMode = !_.utils.checkBrowserCompatibility() || conf.isLegacyMode;
				c.fileselectOnclick = conf.fileselectOnclick || _.controller.fileselectOnclick;
				s.chunkSize = typeof conf.chunkSize !== 'undefined' ? (conf.chunkSize * 1024) : s.chunkSize;
				s.callback = conf.callback || s.callback;
				s.maxUploadSize = typeof conf.maxUploadSize !== 'undefined' ? conf.maxUploadSize : s.maxUploadSize;
				if (typeof conf.form !== 'undefined') {
					s.form.name = conf.form.name || s.form.name;
					s.form.action = conf.form.action || s.form.action;
				}
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

			this.outer = null;
			this.fileselectOnclick = function () {
			};

			this.fileSelectHandler = function (e) {
				var selectedFiles = e.target.files || e.dataTransfer.files,
								l = selectedFiles.length || 0,
								that = _.controller;

				if (l) {
					_.sender.resetParams();//inc: clear array first
					for (var f, i = 0; i < l; i++) {
						if (!_.utils.contains(_.sender.preparedFiles, selectedFiles[i])) {
							f = that.prepareFile(selectedFiles[i]);
							_.sender.preparedFiles.push(f);
							_.view.addFile(f, _.sender.preparedFiles.length);
						}
					}
					if (e.type === 'drop') {
						e.stopPropagation();
						e.preventDefault();
						e.target.className = 'we_file_drag';
						_.controller.fileselectOnclick();
					}
				}
			};

			this.fileDragHover = function (e) {
				e.stopPropagation();
				e.preventDefault();
				e.target.className = (e.type === 'dragover' ? 'we_file_drag we_file_drag_hover' : 'we_file_drag');
			};

			this.prepareFile = function (f, isUploadable) {
				var type = f.type ? f.type : 'text/plain',
								u = isUploadable || true,
								isTypeOk = _.utils.checkFileType(type, f.name),
								isSizeOk = (f.size <= _.sender.maxUploadSize || !_.sender.maxUploadSize) ? true : false,
								errorMsg = [
									_.utils.gl.errorNoFileSelected,
									_.utils.gl.errorFileSize,
									_.utils.gl.errorFileType,
									_.utils.gl.errorFileSizeType
								];

				return {
					file: f,
					type: type,
					isUploadable: isTypeOk && isSizeOk && u, //maybe replace uploadConditionOk by this
					isTypeOk: isTypeOk,
					isSizeOk: isSizeOk,
					uploadConditionsOk: isTypeOk && isSizeOk,
					error: errorMsg[isSizeOk && isTypeOk ? 0 : (!isSizeOk && isTypeOk ? 1 : (isSizeOk && !isTypeOk ? 2 : 3))],
					fileNum: 0,
					dataArray: null,
					currentPos: 0,
					size: f.size,
					partNum: 0,
					totalParts: Math.ceil(f.size / _.sender.chunkSize),
					lastChunkSize: f.size % _.sender.chunkSize,
					currentWeightFile: 0,
					mimePHP: 'none',
					fileNameTemp: ''
				};
			};

			this.setWeButtonState = function (btn, enable, isFooter) {
				isFooter = isFooter || false;

				if (isFooter) {
					_.view.elems.footer.weButton[enable ? 'enable' : 'disable'](btn);
				} else {
					weButton[enable ? 'enable' : 'disable'](btn);
					if (btn === 'browse_harddisk_btn') {
						weButton[enable ? 'enable' : 'disable']('browse_btn');
					}
				}
			};

		}

		function AbstractSender() {
			this.chunkSize = 256 * 1024;
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
			this.resetParams = function () {
			};

			this.prepareUpload = function () {
				return true;
			};


			//new client side image editing
			this.transformAndSendFile = function (cur) {
				var reader = new FileReader();
				reader.onloadend = function () {
					var tempImg = new Image();
					tempImg.src = reader.result;
					tempImg.onload = function () {
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
						}, "image/jpeg", 0.8
										);

					};
				};
				reader.readAsDataURL(cur.file);
			};

			this.sendNextFile = function () {
				var cur, fr = null, cnt,
								that = _.sender;//IMPORTANT: if we use that = this, then that is of type AbstractSender not knowing members of Sender!

				if (this.uploadFiles.length > 0) {
					this.currentFile = cur = this.uploadFiles.shift();
					if (cur.uploadConditionsOk) {
						this.isUploading = true;
						_.view.repaintGUI({what: 'startSendFile'});

						if (cur.size <= this.chunkSize && !this.transformAll.doTrans) {//&& !cur.doTrans!!
							this.sendNextChunk(false);
						} else {
							if (_.view.elems.fileSelect && _.view.elems.fileSelect.value) {
								_.view.elems.fileSelect.value = '';
							}

							var transformables = ['image/jpeg', 'image/gif', 'image/png'];//TODO: add all transformable types

							//clientside editing diabled!
							if (false && this.transformAll.doTrans && transformables.indexOf(cur.type) !== -1) {//TODO: && !cur.doTrans!!)
								_.sender.transformAndSendFile(cur);
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
							that.processResponse(JSON.parse(xhr.responseText), {partSize: partSize, partNum: partNum, totalParts: totalParts});
						} else {
							that.processError({type: 'request', msg: 'http request failed'});
						}
					}
				};

				fileCt = fileCt ? fileCt : 'text/plain';
				fd.append('uploadParts', 1);
				fd.append('wePartNum', partNum);
				fd.append('wePartCount', totalParts);
				fd.append('weFileNameTemp', fileNameTemp);
				fd.append('weFileSize', fsize);
				fd.append('weFileName', fileName);
				fd.append('weFileCt', fileCt);
				fd.append(typeof this.currentFile.field !== 'undefined' ? this.currentFile.field : _.fieldName, part, fileName);//FIXME: take fieldname allways from cur!
				fd.append('weIsUploading', 1);
				fd = this.appendMoreData(fd);
				xhr.open('POST', this.form.action, true);
				xhr.send(fd);
			};

			this.appendMoreData = function (fd) {
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

			this.repaintGUI = function (arg) {
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
								!this.inArray(type, tc.accepted.all) &&
								!this.inArray(typeGroup, tc.accepted.all) &&
								!this.inArray(ext, tc.accepted.all)) {
					return false;
				}
				if (tc.forbidden.all && tc.forbidden.all.length > 0 &&
								(this.inArray(type, tc.forbidden.all) ||
												this.inArray(typeGroup, tc.forbidden.all) ||
												this.inArray(ext, tc.forbidden.all))) {
					return false;
				}

				return true;

			};

			this.computeSize = function (size) {
				return (size / 1024 > 1023 ? ((size / 1024) / 1024).toFixed(1) + ' MB' : (size / 1024).toFixed(1) + ' KB');
			};

			this.inArray = function (needle, haystack) {
				var length = haystack.length;
				for (var i = 0; i < length; i++) {
					if (haystack[i] === needle) {
						return true;
					}
				}
				return false;
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
			if (_.isLegacyMode) {
				_.sender.callback();
				return;
			}

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

		this.getIsLegacyMode = function () {
			return _.isLegacyMode;
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

			_.view.repaintGUI({what: 'initGui'});

			if (_.isLegacyMode) {
				_.utils.makeLegacy();
			}
		};

		function Controller() {
		}

		function Sender() {
			this.totalWeight = 0;

			this.postProcess = function (resp) {
				var that = _.sender,
								cur = this.currentFile;

				this.form.form.elements.weFileNameTemp.value = cur.fileNameTemp;
				this.form.form.elements.weFileCt.value = cur.mimePHP;
				this.form.form.elements.weFileName.value = cur.file.name;
				this.form.form.elements.weIsUploadComplete.value = 1;
				setTimeout(function () {
					that.callback();
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
				this.totalWeight = this.preparedFiles[0].file.size;//size?
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
						_.controller.setWeButtonState(_.view.uploadBtnName, false, true);
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
						_.controller.setWeButtonState('reset_btn', false);
						_.controller.setWeButtonState(_.view.uploadBtnName, false, true);
						return;
					default :
						return;
				}
			};
		}

		function Utils() {
			this.makeLegacy = function () {
				var fs = _.view.elems.fileSelect,
								fsLegacy = document.getElementById(_.fieldName + '_legacy'),
								alertbox = document.getElementById(_.fieldName + '_alert'),
								alertboxLegacy = document.getElementById(_.fieldName + '_alert_legacy');

				fs.id = fs.name = _.fieldName + '_alt';
				fsLegacy.id = fsLegacy.name = _.fieldName;
				document.getElementById(_.fieldName).style.display = 'none';
				document.getElementById(_.fieldName + '_legacy').style.display = '';
				if (typeof alertbox !== 'undefined' && typeof alertboxLegacy !== 'undefined') {
					alertbox.style.display = 'none';
					alertboxLegacy.style.display = '';
				}
				_.sender.form.form.weIsFileInLegacy.value = 1;//FIXME: do we need this?
				_.sender.form.form.weIsUploading.value = 0;
			};
		}
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

			t.width = sf.width.value ? parseInt(sf.width.value) : t.width;
			t.height = sf.height.value ? parseInt(sf.height.value) : t.height;
			t.degrees = sf.degrees.value ? parseInt(sf.degrees.value) : t.degrees;
			t.doTrans = t.degrees || t.width || t.height ? true : false;
			if (t.doTrans) {
				t.widthSelect = sf.widthSelect.value ? sf.widthSelect.value : t.widthSelect;
				t.heightSelect = sf.heightSelect.value ? sf.heightSelect.value : t.heightSelect;
				t.keepRatio = sf.keepRatio.value ? true : t.keepRatio;
				t.quality = sf.quality.value ? parseFloat(sf.quality.value) : t.quality;
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
				_.view.elems.footer[btn + '_enabled'] = _.view.elems.footer.switch_button_state(btn, (enabled ? 'enabled' : 'disabled'));
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
					_.view.elems.footer.weButton.setText(btn, replace);
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
							this.totalWeight += this.preparedFiles[i].file.size;//size?
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

			this.appendMoreData = function (fd) {
				var sf = document.we_startform,
								cur = this.currentFile;

				fd.append('weFormNum', cur.fileNum + 1);
				fd.append('weFormCount', this.totalFiles);
				fd.append('we_cmd[0]', 'import_files');
				fd.append('cmd', 'buttons');
				fd.append('jsRequirementsOk', 1);
				fd.append('step', 1);

				fd.append('importToID', sf.importToID.value);
				fd.append('sameName', sf.sameName.value);
				fd.append('categories', sf.categories.value);
				fd.append('importMetadata', sf.importMetadata.value);
				fd.append('imgsSearchable', sf.imgsSearchable.value);
				fd.append('thumbs', sf.thumbs.value);

				var transformables = ['image/jpeg', 'image/gif', 'image/png'];//TODO: add all transformable types
				//clientside editing disabled!
				if (true || cur.partNum === cur.totalParts && this.isGdOk && transformables.indexOf(cur.type) === -1) {//transformables are transformed by js
					fd.append('width', sf.width.value);
					fd.append('height', sf.height.value);
					fd.append('widthSelect', sf.widthSelect.value);
					fd.append('heightSelect', sf.heightSelect.value);
					fd.append('keepRatio', sf.keepRatio.value);
					fd.append('quality', sf.quality.value);

					if (this.isGdOk) {
						fd.append('thumbs', sf.thumbs.value);
						fd.append('width', sf.width.value);
						fd.append('height', sf.height.value);
						fd.append('widthSelect', sf.widthSelect.value);
						fd.append('heightSelect', sf.heightSelect.value);
						fd.append('keepRatio', sf.keepRatio.value);
						fd.append('quality', sf.quality.value);
						fd.append('degrees', sf.degrees.value);
					}
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

			this.addFile = function (f, index) {
				this.appendRow(f, _.sender.preparedFiles.length - 1);
			};

			this.appendRow = function (f, index) {
				var div,
								row = this.htmlFileRow.replace(/WEFORMNUM/g, index).
								replace(/WE_FORM_NUM/g, (this.nextTitleNr++)).
								replace(/FILENAME/g, (f.file.name)).
								replace(/FILESIZE/g, (f.isSizeOk ? _.utils.computeSize(f.size) : '<span style="color:red">> ' + ((_.sender.maxUploadSize / 1024) / 1024) + ' MB</span>'));

				weAppendMultiboxRow(row, '', 0, 0, 0, -1);

				div = document.getElementById('div_upload_files');
				div.scrollTop = div.scrollHeight;
				document.getElementById('fileInput_uploadFiles_' + index).addEventListener('change', _.controller.replaceSelectionHandler, false);
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
					var activeFrame = top.opener.top.weEditorFrameController.getActiveEditorFrame();

					if (document.we_startform.importToID.value === activeFrame.EditorDocumentId && activeFrame.EditorEditPageNr === 16) {
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
			_.view.uploadBtnName = conf.uploadBtnName || _.view.uploadBtnName;
			_.fieldName = 'we_File';
			if (typeof conf.binDocProperties !== 'undefined') {
				_.view.icon = getTreeIcon(conf.binDocProperties.ct);
				_.view.binDocType = conf.binDocProperties.type || _.view.binDocType;
			}
			_.view.icon = conf.icon || _.view.icon;
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
			_.sender.form.action = '/webEdition/we_cmd.php?we_cmd[0]=do_upload_file&we_cmd[1]=binaryDoc';

			v.elems.fileDrag_state_0 = document.getElementById('div_fileupload_fileDrag_state_0');
			v.elems.fileDrag_state_1 = document.getElementById('div_fileupload_fileDrag_state_1');
			v.elems.dragInnerRight = document.getElementById('div_upload_fileDrag_innerRight');
			v.elems.divRight = document.getElementById('div_fileupload_right');
			v.elems.divRightLegacy = document.getElementById('div_fileupload_right_legacy');
			v.elems.txtFilename = document.getElementById('span_fileDrag_inner_filename');
			v.elems.txtFilename_1 = document.getElementById('span_fileDrag_inner_filename_1');//??
			v.elems.txtSize = document.getElementById('span_fileDrag_inner_size');
			v.elems.txtType = document.getElementById('span_fileDrag_inner_type');
			v.elems.divBtnReset = document.getElementById('div_fileupload_btnReset');
			v.elems.divBtnCancel = document.getElementById('div_fileupload_btnCancel');
			v.elems.divBtnUpload = document.getElementById('div_fileupload_btnUpload');
			v.elems.divBtnUploadLegacy = document.getElementById('div_fileupload_btnUploadLegacy');
			v.elems.divProgressBar = document.getElementById('div_fileupload_progressBar');
			v.elems.divButtons = document.getElementById('div_fileupload_buttons');

			v.spinner = document.createElement("i");
			v.spinner.className = "fa fa-2x fa-spinner fa-pulse";

			if (_.isLegacyMode) {
				_.utils.makeLegacy();
			}
		};

		function Controller() {
			this.doSubmit = false;

			this.setEditorIsHot = function () {
				top.weEditorFrameController.setEditorIsHot(true, top.weEditorFrameController.ActiveEditorFrameId);
			};
		}

		function Sender() {
			this.totalWeight = 0;
			this.callback = null;

			this.doOnFileFinished = function (resp) {
			};

			this.postProcess = function (resp) {
				_.sender.preparedFiles = [];
				_.sender.currentFile = null;
				if (resp.status === 'success') {
					var _EditorFrame = top.weEditorFrameController.getActiveEditorFrame();

					window.we_cmd('update_file');
					_EditorFrame.getDocumentReference().frames.editHeader.we_setPath(resp.weDoc.path, resp.weDoc.text, 0, "published");
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
					this.totalWeight = this.preparedFiles[0].file.size;//size?
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

			this.appendMoreData = function (fd) {
				var cur = this.currentFile;

				fd.append('we_transaction', document.we_form.we_transaction.value);
				fd.append('import_metadata', (typeof document.we_form.import_metadata !== 'undefined' &&
								document.we_form.import_metadata.checked) ? 1 : 0);
				fd.append('we_doc_ct', document.we_form.we_doc_ct.value);
				fd.append('we_doc_ext', document.we_form.we_doc_ext.value);

				return fd;
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
			this.icon = '/webEdition/images/icons/doc.gif';
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
				this.setDisplay('fileDrag_state_1', '');
				this.elems.dragInnerRight.innerHTML = '';

				if (f.uploadConditionsOk) {
					_.sender.isAutostartPermitted = true;
					_.controller.setEditorIsHot();
				} else {
					_.sender.isAutostartPermitted = false;
				}

				if (this.binDocType === 'image' && f.uploadConditionsOk && f.size < 4194304) {
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
						this.preview = new Image();
						this.preview.onload = function () {
							_.view.elems.dragInnerRight.appendChild(_.view.preview);
						};
						this.preview.src = this.icon;
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
						this.setDisplay('fileDrag_state_0', '');
						this.setDisplay('fileDrag_state_1', 'none');
						this.setDisplay('fileInputWrapper', '');
						if (this.isDragAndDrop && this.elems.fileDragfileDrag) {
							this.elems.fileDrag.style.display = '';
						}
						this.setDisplay('fileInputWrapper', '');
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
						this.setDisplay('divBtnReset', 'none');
						this.setDisplay('divBtnUpload', 'none');
						this.setDisplay('divProgressBar', '');
						this.setDisplay('divBtnCancel', '');
						if (this.preview) {
							this.preview.style.opacity = 0.05;
						}
						_.controller.setWeButtonState('browse_harddisk_btn', false);
						this.setDisplay('divBtnReset', 'none');
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
						//no break;
					case 'initGui' :
					case 'fileNOK' :
					case 'cancelUpload' :
					case 'resetGui' :
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

		function Utils() {
			this.makeLegacy = function () {
				var v = _.view;
				_.controller.setWeButtonState('upload_legacy_btn', true);
				v.setDisplay('divRight', 'none');
				v.setDisplay('divButtons', 'none');
				v.setDisplay('divRightLegacy', '');
				v.setDisplay('divBtnUploadLegacy', '');
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