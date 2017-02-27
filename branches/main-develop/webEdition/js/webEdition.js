/* global WE, errorHandler,treeData,drawTree, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
'use strict';

if (window.location !== top.location) {
	top.location = window.location;
}

var hot = false;
var last = 0;
var lastUsedLoadFrame = null;
var nlHTMLMail = false;
var we_cmd_modules = {};

/** webEdition Main Objekt
 */
var WebEdition = {
//all constants in WE used in JS
	layout: {
		editors: {
			CodeMirror: null,
		},
		button: null,
		sidebar: null,
		cockpitFrame: null,
		windows: [],
		focusedWindow: null,
		browserwind: null,
		regular_logout: false,
		dragNDrop: {
			dataTransfer: {
				text: ''
			}
		},
		openToEdit: function (tab, id, contentType) {
			if (id > 0) {
				WE().layout.weEditorFrameController.openDocument(tab, id, contentType);
			}
		},
		we_setPath: function (_EditorFrame, path, text, id, classname) {
			_EditorFrame = _EditorFrame ? _EditorFrame : WE().layout.weEditorFrameController.getActiveEditorFrame();
			// update document-tab
			_EditorFrame.initEditorFrameData({
				EditorDocumentText: text,
				EditorDocumentPath: path
			});

			if (classname) {
				WE().layout.multiTabs.setTextClass(_EditorFrame.FrameId, classname);
			}
			if (_EditorFrame.getDocumentReference().frames.editHeader === undefined) {
				return;
			}
			var doc = _EditorFrame.getDocumentReference().frames.editHeader.document;
			if (doc) {
				var div = doc.getElementById('h_path');
				if (div) {
					div.innerHTML = path.replace(/</g, '&lt;').replace(/>/g, '&gt;');
					if (id > 0) {
						div = doc.getElementById('h_id');
						div.innerHTML = id;
					}
				}
			}
		},
		reloadUsedEditors: function (reloadTabs) {
			var usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();

			for (var frameId in usedEditors) {
				if (reloadTabs[usedEditors[frameId].getEditorEditorTable()] && (reloadTabs[usedEditors[frameId].getEditorEditorTable()]).indexOf(',' + usedEditors[frameId].getEditorDocumentId() + ',') !== -1) {
					usedEditors[frameId].setEditorReloadNeeded(true);
				}
			}
		},
		makeNewDoc: function (_EditorFrame, curType, curID, savetmpl, noClose) {
			var _EditorFrameDocumentRef = _EditorFrame.getDocumentReference();

			switch (curType) {
				case WE().consts.contentTypes.TEMPLATE:
					if (WE().util.hasPerm("NEW_WEBEDITIONSITE")) {
						if (_EditorFrame.getEditorMakeNewDoc()) {
							if (savetmpl) {
								top.we_cmd('new', WE().consts.tables.FILE_TABLE, '', WE().consts.contentTypes.WEDOCUMENT, '', curID);
							}
						} else if (noClose && _EditorFrame.getEditorIsInUse()) {
							_EditorFrameDocumentRef.frames.editHeader.location.reload();
						}
					}
					break;
				case WE().consts.contentTypes.OBJECT:
					if (WE().util.hasPerm('NEW_OBJECTFILE')) {
						if (_EditorFrame.getEditorMakeNewDoc()) {
							top.we_cmd('new', WE().consts.tables.OBJECT_FILES_TABLE, '', WE().consts.contentTypes.OBJECT_FILE, curID);
						} else if (noClose && _EditorFrame.getEditorIsInUse()) {
							_EditorFrameDocumentRef.frames.editHeader.location.reload();
						}

					}
			}
		},
		propEdit: {
			switchExt: function (doc, id, name) {
				var a = doc.we_form.elements;
				var ok = true;
				if (id) {
					ok = window.confirm(WE().consts.g_l.main.confirm_ext_change);
				}
				if (ok) {
					a["we_" + name + "_Extension"].value = (a["we_" + name + "_IsDynamic"].value == 1 ? WE().consts.global.DEFAULT_DYNAMIC_EXT : WE().consts.global.DEFAULT_STATIC_EXT);
				}
			}
		},
		checkFileUpload: function (we_cmd_args) {
			var parentObj = WE().layout.weEditorFrameController;
			var frame = WE().layout.weEditorFrameController.getVisibleEditorFrame();

			if (parentObj !== undefined && (frame.weFileUpload_instance) !== undefined && frame.weFileUpload_instance.getType() === 'binDoc') {
				frame.weFileUpload_instance.doUploadIfReady(function () {
					top.we_cmd.apply(window, we_cmd_args);
				});
			} else {
				top.we_cmd.apply(window, we_cmd_args);
			}

		},
		tree: {
			oldWidth: 0,
			widthBeforeDeleteMode: 0,
			getWidth: function () {
				var w = document.getElementById("bframeDiv").style.width;
				return w.substr(0, w.length - 2);
			},
			toggle: function (setVisible) {
				var tfd = window.document.getElementById("treeFrameDiv");
				var w = WE().layout.tree.getWidth();

				if (setVisible || (tfd.style.display === "none" && setVisible !== false)) {
					WE().layout.tree.oldWidth = (WE().layout.tree.oldWidth < WE().consts.size.tree.min ? WE().consts.size.tree.defaultWidth : WE().layout.tree.oldWidth);
					WE().layout.tree.setWidth(WE().layout.tree.oldWidth);
					tfd.style.display = "block";
					//setTreeArrow("left");
					WE().layout.tree.storeWidth(WE().layout.tree.oldWidth);
					return true;
				}
				tfd.style.display = "none";
				WE().layout.tree.oldWidth = w;
				WE().layout.tree.setWidth(WE().consts.size.tree.hidden);
				//setTreeArrow("right");
				return false;
			},
			treeOut: function () {
				if (WE().layout.tree.getWidth() <= WE().consts.size.tree.min) {
					WE().layout.tree.toggle();
				}
			},
			setWidth: function (w) {
				document.getElementById("bframeDiv").style.width = w + "px";
				document.getElementById("bm_content_frameDiv").style.left = w + "px";
				if (w > WE().consts.size.tree.hidden) {
					WE().layout.tree.storeWidth(w);
				}
			},
			inc: function () {
				var w = parseInt(WE().layout.tree.getWidth());
				if ((w >= WE().consts.size.tree.min) && (w < WE().consts.size.tree.max)) {
					w += WE().consts.size.tree.step;
					WE().layout.tree.setWidth(w);
				}
				if (w >= WE().consts.size.tree.max) {
					w = WE().consts.size.tree.max;
					WE().layout.tree.setWidth(w);
					window.document.getElementById("incBaum").style.backgroundColor = "grey";
				} else
				if (w < WE().consts.size.tree.min) {
					WE().layout.tree.toggle(true);
				}
			},
			dec: function () {
				var w = parseInt(WE().layout.tree.getWidth());
				w -= WE().consts.size.tree.step;
				if (w > WE().consts.size.tree.min) {
					WE().layout.tree.setWidth(w);
					document.getElementById("incBaum").style.backgroundColor = "";
				}
				if (w <= WE().consts.size.tree.min && ((w + WE().consts.size.tree.step) >= WE().consts.size.tree.min)) {
					WE().layout.tree.toggle();
				}
			},
			storeWidth: function (w) {
				var ablauf = new Date();
				var newTime = ablauf.getTime() + 30758400000;
				ablauf.setTime(newTime);
				WE().util.weSetCookie(document, "treewidth_main", w, ablauf, WE().consts.dirs.WEBEDITION_DIR);
			},
		},
		vtab: {
			click: function (tab, table) {
				if (WE().session.deleteMode) {
					we_cmd('exit_delete', table);
				}
				if (tab.classList.contains("tabActive")) {
					if (WE().layout.tree.toggle()) {
						we_cmd('loadVTab', table, 0);
					}
				} else {
					WE().layout.vtab.setActiveTab(table);
					WE().layout.tree.treeOut();
					we_cmd('loadVTab', table, 0);
				}
			},
			setActiveTab: function (table) {
				var allTabs = document.getElementById("vtabs").getElementsByClassName("tab");
				for (var i = 0; i < allTabs.length; i++) {
					allTabs[i].className = "tab " + (allTabs[i].getAttribute("data-table") === table ? "tabActive" : "tabNorm");
				}
			}
		},
		openBrowser: function (url) {
			if (!url) {
				url = "/";
			}
			try {
				WE().layout.browserwind = window.open(WE().consts.dirs.WEBEDITION_DIR + "openBrowser.php?url=" + encodeURI(url), "browser", "menubar=yes,resizable=yes,scrollbars=yes,location=yes,status=yes,toolbar=yes");
			} catch (e) {
				top.we_showMessage(WE().consts.g_l.alert.browser_crashed, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		},
		pushCmdToModule: function (args) {
			var wind = WE().util.jsWindow.prototype.find('edit_module');
			if (wind) {
				wind.content.we_cmd(args[0]);
				if (args[0] !== "empty_log") {
					wind.focus();
				}
			}
		},
		weSuggest: {
			/*config for init jquery plugin*/
			config: {
				delay: 150,
				minLength: 2,
				autoFocus: true,
				source: function (request, response) {
					var el = this.element[0];
					var term = request.term;
					if (term in el.cache) {
						response(el.cache[ term ]);
						return;
					}
					var target = WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=SelectorSuggest" +
									"&we_cmd[table]=" + el.getAttribute('data-table') +
									"&we_cmd[contenttypes]=" + el.getAttribute('data-contenttype') +
									"&we_cmd[basedir]=" + el.getAttribute('data-basedir') +
									"&we_cmd[max]=" + el.getAttribute('data-max') +
									"&we_cmd[currentDocumentType]=" + el.getAttribute('data-currentDocumentType') +
									"&we_cmd[currentDocumentID]=" + el.getAttribute('data-currentDocumentID') +
									"&we_cmd[query]=" + request.term;
					$.getJSON(target, request, function (weResponse, status, xhr) {
						el.cache[term] = weResponse.DataArray.suggest;
						response(weResponse.DataArray.suggest);
					});
				},
				/*fired on create of widget*/
				create: function () {
					var res = this.getAttribute('data-result');
					this.result = this.ownerDocument.getElementById(res);
					this.cache = {};
				},
				/*called if search via rpc is done*/
				search: function (event, ui) {
					//FIXME result value -1
					this.result.value = 0;
				},
				/*called if an item was selected from suggest*/
				select: function (event, ui) {
					this.result.value = ui.item.ID;
					this.result.setAttribute('data-contenttype', ui.item.contenttype);
					this.classList.remove("weMarkInputError");
					var cmd = this.getAttribute('data-onSelect');
					if (cmd) {
						event.target.ownerDocument.defaultView.we_cmd(cmd);
					}
				},
				/*called if the element was modified*/
				change: function (event, ui) {
					//set path to / if no path is given
					if (this.value === "" && !this.getAttribute("required")) {//is this correct?!
						this.value = "/";
						this.result.value = 0;
						this.result.setAttribute('data-contenttype', WE().consts.contentTypes.FOLDER);
					}
					if (
									!this.getAttribute("disabled") &&
									this.offsetParent !== null /*returns null if parent is hidden*/ && (
													this.value && this.value !== "/" && !parseInt(this.result.value) || //sth. was typed, but not selected
													!parseInt(this.result.value) && this.getAttribute("required") || //a required field has no value
													this.value.indexOf(this.getAttribute("data-basedir")) !== 0 || //basedir must match the selected path
													(this.getAttribute("data-selector") === "docSelector" && this.result.getAttribute('data-contenttype') === WE().consts.contentTypes.FOLDER) //we need a document, but only a folder is selected
													)
									) {
						this.classList.add("weMarkInputError");
					} else {
						this.classList.remove("weMarkInputError");
					}
				}
			},
			/*if an input element is reused, you can update its config - see source function
			 * possible elements: required, table,contenttypes,basedir,max
			 * */
			updateSelectorConfig: function (win, id, config) {
				var el = win.document.getElementById(id);
				if (!el) {
					WE().t_e('weSuggest unable to update config of ' + id);
					return;
				}
				if (config.required !== undefined) {
					if (config.required) {
						el.setAttribute("required", "required");
					} else {
						el.removeAttribute("required");
					}
					delete config.required;
				}
				for (var c in config) {
					el.setAttribute("data" + c, config[c]);
				}
			},
			/*ported from old yahoo - removed?*/
			openSelectionToEdit: function (win, elID) {
				var el = win.document.getElementById(elID);
				var table = el.getAttribute('data-table'),
								id = el.result.value,
								type = el.result.getAttribute('data-contenttype');

				if (table && id && type) {
					WE().layout.openToEdit(table, id, type);
				}
			},
			/*Check if all required fields are set*/
			checkRequired: function (win, id) {
				var isValid = true;
				win.$((id === undefined ? '.weSuggest' : '#' + id)).each(function () {
					if (
									!this.getAttribute("disabled") &&
									this.offsetParent !== null /*returns null if parent is hidden*/ && (
													this.value && this.value !== "/" && !parseInt(this.result.value) || //sth. was typed, but not selected
													!parseInt(this.result.value) && this.getAttribute("required") || //a required field has no value
													(this.value && this.value.indexOf(this.getAttribute("data-basedir")) !== 0) || //basedir must match the selected path
													(this.getAttribute("data-selector") === "docSelector" && this.result.getAttribute('data-contenttype') === WE().consts.contentTypes.FOLDER) //we need a document, but only a folder is selected
													)
									) {
						this.classList.add("weMarkInputError");
						isValid = false;
					} else {
						this.classList.remove("weMarkInputError");
					}
				});
				//FIXME: do we need running?
				return {'running': false, 'valid': isValid};
			},
			/*???*/
			writebackExternalSelection: function (win, result, acId) {
				if (!result || !result.currentID || !result.currentPath || !result.currentType || !acId) {
					WE().t_e('suggestor function "writebackExternalSelection": parameters missing');
				}

				win.document.we_form.elements['yuiAcResult' + acId].value = result.currentID;
				win.document.we_form.elements['yuiAcInput' + acId].value = result.currentPath;
				win.document.we_form.elements['yuiAcContentType' + acId].value = result.currentType;
//FIXME:
				//YAHOO.autocoml.doOnAcResultChange('yuiAcInput' + acId, result);
			}
		}
	},
	handler: {
		errorHandler: errorHandler,
		dealWithKeyboardShortCut: null
	},
	t_e: function () {
		var msg = '';
		for (var i = 0; i < arguments.length; i++) {
			msg += JSON.stringify(arguments[i]) + (i < (arguments.length - 1) ? "\n" : "");
		}
		WE().handler.errorHandler(msg, '', 0, 0, new Error()/*Array.prototype.slice.call(arguments)*/);
	},
	util: {
		weSetCookie: function (doc, name, value, expires, path, domain) {
			doc.cookie = name + "=" + encodeURI(value) +
							((expires === undefined) ? "" : "; expires=" + expires.toGMTString()) +
							((path === undefined) ? "" : "; path=" + path) +
							((domain === undefined) ? "" : "; domain=" + domain);
		},
		weGetCookie: function (doc, name) {
			var cname = name + "=";
			var dc = doc.cookie;
			if (dc.length > 0) {
				var begin = dc.indexOf(cname);
				if (begin !== -1) {
					begin += cname.length;
					var end = dc.indexOf(";", begin);
					if (end === -1) {
						end = dc.length;
					}
					return dc.substring(begin, end);
				}
			}
			return null;
		},
		hashCode: function (s) {
			/*jshint bitwise:false */
			return s.split("").reduce(function (a, b) {
				a = ((a << 5) - a) + b.charCodeAt(0);
				return a & a;
			}, 0);
		},
		we_sbmtFrm: function (target, url, source) {
			if (source === undefined) {
				source = WE().layout.weEditorFrameController.getVisibleEditorFrame();
			}

			if (source) {
				if (source.we_submitForm !== undefined && source.we_submitForm) {
					return source.we_submitForm(target.name, url);
				}
				if (source.contentWindow && source.contentWindow.we_submitForm) {
					return source.contentWindow.we_submitForm(target.name, url);
				}
			}
			return false;
		},
		hasPerm: function (perm) {
			return (WE().session.permissions.ADMINISTRATOR || WE().session.permissions[perm] ? true : false);
		},
		/**
		 * This function sets incons inside elements of a given class. The element must have the property data-contenttype and data-extension set to determine the correct icon
		 * @param string classname the elements classname
		 * @returns noting
		 */
		setIconOfDocClass: function (doc, classname) {
			var elements = doc.getElementsByClassName(classname);
			for (var i = 0; i < elements.length; i++) {
				elements[i].innerHTML = this.getTreeIcon(elements[i].getAttribute("data-contenttype"), false, elements[i].getAttribute("data-extension"));
			}
		},
		/**
		 * Get a file icon out of a given type, used in tree, selectors & tabs
		 * @param {type} contentType
		 * @param {type} open
		 * @returns icon to be drawn as html-code
		 */
		getTreeIcon: function (contentType, open, extension) {
			var simplepre = '<span class="fa-stack fa-lg fileicon">';
			var pre = simplepre + '<i class="fa fa-file fa-inverse fa-stack-2x fa-fw"></i>',
							post = '</span>';
			switch (contentType) {
				case 'cockpit':
					return simplepre + '<i class="fa fa-th-large fa-stack-2x"></i>' + post;
				case 'class_folder'://FIXME: this contenttype is not set
				case 'we/bannerFolder':
				case 'folder':
					return simplepre + '<i class="fa fa-folder' + (open ? '-open' : '') + ' fa-stack-2x"></i><i class="fa fa-folder' + (open ? '-open' : '') + '-o fa-stack-2x"></i>' + post;
				case  'image/*':
					return pre + '<i class="fa fa-file-image-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case 'text/js':
					return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-otherfiles"><i class="fa fa-stack-1x">js</i></span>' + post;
				case 'text/css':
					return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-otherfiles"><i class="fa fa-stack-1x">cs</i></span>' + post;
				case 'text/htaccess':
					return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-otherfiles"><i class="fa fa-stack-1x">ht</i></span>' + post;
				case 'text/weTmpl':
					return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span><span class="we-classification"><i class="fa fa-stack-1x">T</i></span>' + post;
				case 'text/webedition':
					return pre + '<i class="fa fa-file-text-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span>' + post;
				case 'text/xml':
				case 'text/html':
					return pre + '<i class="fa fa-file-code-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case 'application/x-shockwave-flash':
				case 'video/*':
					return pre + '<i class="fa fa-file-video-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case 'audio/*':
					return pre + '<i class="fa fa-file-sound-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case 'text/plain':
					return pre + '<i class="fa fa-file-text-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case 'file':
				case 'application/*':
					switch (extension) {
						case '.pdf':
							return pre + '<i class="fa fa-file-pdf-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
						case '.zip' :
						case '.sit' :
						case '.hqx' :
						case '.bin' :
							return pre + '<i class="fa fa-file-archive-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
						case '.odg':
						case '.otg':
						case '.odt':
						case '.ott':
						case '.dot' :
						case '.doc' :
							return pre + '<i class="fa fa-file-word-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
						case '.ods':
						case '.ots':
						case '.xlt' :
						case '.xls' :
							return pre + '<i class="fa fa-table fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
						case '.odp':
						case '.otp':
						case '.ppt' :
							return pre + '<i class="fa fa-line-chart fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
						default:
							return pre + '<i class="fa fa-file-o fa-stack-2x"></i>' + post;
					}
					return '';
				case 'object':
					return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span><span class="we-classification"><i class="fa fa-stack-1x">C</i></span>' + post;
				case 'objectFile':
					return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span><span class="we-classification"><i class="fa fa-stack-1x">O</i></span>' + post;
				case 'text/weCollection':
					return simplepre + '<i class="fa fa-archive fa-stack-2x we-color"></i>' + post;
//Banner module
				case 'we/banner':
					return simplepre + '<i class="fa fa-flag-checkered fa-stack-2x we-color"></i>' + post;
				case 'we/customerGroup':
				case 'we/userGroup':
					return simplepre + '<i class="fa fa-users fa-stack-2x we-color"></i>' + post;
				case 'we/alias':
					return simplepre + '<i class="fa fa-user fa-stack-2x" style="color:grey"></i>' + post;
				case 'we/customer':
				case 'we/user':
					return simplepre + '<i class="fa fa-user fa-stack-2x we-color"></i>' + post;
				case 'we/export':
					return pre + '<i class="fa fa-download fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case 'we/glossar':
					return simplepre + '<i class="fa fa-commenting fa-stack-2x we-color"></i>' + post;
				case 'we/newsletter':
					return simplepre + '<i class="fa fa-newspaper-o fa-stack-2x we-color"></i>' + post;
				case 'we/voting':
					return simplepre + '<i class="fa fa-thumbs-up fa-stack-2x we-color"></i>' + post;
				case 'we/navigation':
					return simplepre + '<i class="fa fa-compass fa-stack-2x we-color"></i>' + post;
				case 'we/search':
					return pre + '<i class="fa fa-search fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case 'we/shop':
					return simplepre + '<i class="fa fa-shopping-cart fa-stack-2x we-color"></i></i>' + post;
				case 'we/workflow':
					return simplepre + '<i class="fa fa-fa-gears fa-stack-2x we-color"></i></i>' + post;
				case 'we/categories':
					return simplepre + '<i class="fa fa-tags fa-stack-2x we-color"></i>' + post;
				case 'we/category':
					return simplepre + '<i class="fa fa-tag fa-stack-2x we-color"></i>' + post;
				case 'symlink':
					return pre + '<i class="fa fa-link fa-stack-2x we-color"></i>' + post;
				case 'settings':
					return simplepre + '<i class="fa fa-list fa-stack-2x we-color"></i>' + post;

				default:
					return pre + '<i class="fa fa-file-o fa-stack-2x ' + contentType + '"></i>' + post;
			}
		},
		sprintf: function (argum) {
			if (!arguments || arguments.length === 0) {
				return;
			}

			var regex = /([^%]*)%(%|d|s)(.*)/;
			var arr = [];
			var iterator = 0;
			var matches = 0;

			while ((arr = regex.exec(argum))) {
				var left = arr[1];
				var type = arr[2];
				var right = arr[3];

				matches++;
				iterator++;

				var replace = arguments[iterator];

				switch (type) {
					case "d":
						replace = parseInt(arguments[iterator]) ? parseInt(arguments[iterator]) : 0;
						break;
					case "s":
						replace = arguments[iterator];
						break;
				}
				argum = left + replace + right;
			}
			return argum;
		},
		IsDigitPercent: function (e) {
			var key = e.charCode;

			return (((key >= 48) && (key <= 57)) || (key === 37) || (key === 0) || (key === 46) || (key === 101) || (key === 109) || (key === 13) || (key === 8) || (key <= 63235 && key >= 63232) || (key === 63272));
		},
		IsDigit: function (e) {
			var key = e.charCode === undefined ? window.event.keyCode : e.charCode;
			return ((key === 46) || ((key >= 48) && (key <= 57)) || (key === 0) || (key === 13) || (key === 8) || (key <= 63235 && key >= 63232) || (key === 63272));
		},
		getWe_cmdArgsArray: function (arr) {
			if (arr.lenght > 0 && typeof arr[0] === "object") {
				return arr[0];
			}
			return arr;
		},
		getWe_cmdArgsUrl: function (args, base) {
			var url = (base === undefined ? WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?" : base);

			if (Array.isArray(args)) {
				var pos = 0;
				for (var i = 0; i < args.length; i++) {
					if (typeof (args[i]) !== 'object') {
						url += "we_cmd[" + pos + "]=" + encodeURIComponent(args[i]) + (i < (args.length - 1) ? "&" : "");
						pos++;
					}
				}
			} else {
				url += Object.keys(args).map(function (key) {
					return "we_cmd[" + key + "]=" + encodeURIComponent(args[key]);
				}).join("&");
			}

			return url;
		}, /**
		 * show a confirm dialog
		 * @param {window} win window calling
		 * @param {string} title title of the dialog, if none "question" is set
		 * @param {string} message the message to display
		 * @param {array} yesCmd we-command on yes
		 * @param {array} noCmd we-command on no
		 * @param {string} yesText text for button yes, if other
		 * @param {string} noText text for button no, if other
		 * @returns {undefined}
		 */
		showConfirm: function (win, title, message, yesCmd, noCmd, yesText, noText) {
			title = title ? title : WE().consts.g_l.message_reporting.question;
			var ab;

			if (win.top.top.$) {
				ab = win.top.top.$("#alertBox");
				if (!ab.length) {
					var alertDiv = win.top.top.document.createElement('div');
					alertDiv.id = "alertBox";
					win.top.top.document.body.appendChild(alertDiv);
					ab = win.top.top.$("#alertBox");
				}
				ab[0].data = {
					win: win,
					yesCmd: yesCmd,
					noCmd: noCmd,
				};
				var yesBut = {
					text: (yesText ? yesText : WE().consts.g_l.message_reporting.yes),
					icons: {
						primary: "fa fa-check fa-ok"
					},
					click: function () {
						var ab = this.ownerDocument.defaultView.$("#alertBox");
						if (ab[0].data.yesCmd) {
							ab[0].data.win.we_cmd.apply(ab[0].data.win, ab[0].data.yesCmd);
						}
						ab[0].data = null;
						ab.dialog("close");
					}
				};
				var noBut = {
					text: (noText ? noText : WE().consts.g_l.message_reporting.no),
					icons: {
						primary: "fa fa-close fa-cancel"
					},
					click: function () {
						var ab = this.ownerDocument.defaultView.$("#alertBox");
						if (ab[0].data.noCmd) {
							ab[0].data.win.we_cmd.apply(ab[0].data.win, ab[0].data.noCmd);
						}
						ab[0].data = null;
						ab.dialog("close");
					}
				};
				var cancelBut = {
					text: WE().consts.g_l.message_reporting.cancel,
					icons: {
						primary: "fa fa-close fa-ban"
					},
					click: function () {
						var ab = this.ownerDocument.defaultView.$("#alertBox");
						ab[0].data = null;
						ab.dialog("close");
					}
				};

				ab.html('<span class="alertIcon fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span><div>' + message.replace(/\n/, "<br/>") + "</div>");
				ab.dialog({
					dialogClass: "no-close",
					modal: true,
					title: title,
					height: "auto",
					maxHeight: 400,
					width: "auto",
					maxWidth: 400,
					closeOnEscape: false,
					buttons: (WE().session.isMac ?
									(noCmd ? [noBut, cancelBut, yesBut] : [noBut, yesBut]) :
									(noCmd ? [yesBut, noBut, cancelBut] : [yesBut, noBut])
									)
				});
			} else {
				message = (title ? title + ":\n" : "") + message;
				if (win.confirm(message)) {
					if (yesCmd) {
						win.we_cmd(yesCmd);
					}
				} else {
					if (noCmd) {
						win.we_cmd(noCmd);
					}
				}
			}

		},
		/**
		 * setting is built like the unix file system privileges with the 3 options
		 * see notices, see warnings, see errors
		 *
		 * 1 => see Notices
		 * 2 => see Warnings
		 * 4 => see Errors
		 *
		 * @param message string
		 * @param prio integer one of the values 1,2,4
		 * @param win object reference to the calling window
		 */
		showMessage: function (message, prio, win, timeout/*currently unsupprted*/) {
			//FIXME:change this from alert to a dynamic window with timeout
			win = (win ? win : this.window);
			// default is error, to avoid missing messages
			prio = prio ? prio : WE().consts.message.WE_MESSAGE_ERROR;
			var ab;

			// always show in console !
			WE().layout.messageConsole.addMessage(prio, message);
			/*jshint bitwise:false */
			if (prio & WE().session.messageSettings) { // show it, if you should
				var title = "";
				var icon = "";
				switch (prio) {
					// Notice
					case WE().consts.message.WE_MESSAGE_INFO:
					case WE().consts.message.WE_MESSAGE_NOTICE:
						title = WE().consts.g_l.message_reporting.notice;
						break;

						// Warning
					case WE().consts.message.WE_MESSAGE_WARNING:
						title = WE().consts.g_l.message_reporting.warning;
						icon = '<span class="alertIcon fa-stack fa-lg alertIcon" style="color:#007de3;"><i class="fa fa-circle fa-stack-2x" ></i><i class="fa fa-info fa-stack-1x fa-inverse"></i></span>';
						break;

						// Error
					case WE().consts.message.WE_MESSAGE_ERROR:
						title = WE().consts.g_l.message_reporting.error;
						icon = '<span class="alertIcon fa-stack fa-lg alertIcon"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>';
						break;
				}
				//try Jquery
				if (win.top.top.$) {
					ab = win.top.top.$("#alertBox");
					if (!ab.length) {
						var alertDiv = win.top.top.document.createElement('div');
						alertDiv.id = "alertBox";
						win.top.top.document.body.appendChild(alertDiv);
						ab = win.top.top.$("#alertBox");
					}

					ab.html(icon + "<div>" + message.replace(/\n/, "<br/>") + "</div>");
					ab.dialog({
						dialogClass: "no-close",
						modal: true,
						title: title,
						height: "auto",
						maxHeight: 400,
						width: "auto",
						maxWidth: 400,
						closeOnEscape: true,
						buttons: [{
								text: WE().consts.g_l.message_reporting.ok,
								icons: {
									primary: "fa fa-check fa-ok"
								},
								click: function () {
									this.ownerDocument.defaultView.$("#alertBox").dialog("close");
								}
							}]
					});
					if (timeout) {
						win.top.top.setTimeout(function (win) {
							win.$("#alertBox").dialog("close");
						}, timeout, win.top.top);
					}
				} else {
					message = title + ":\n" + message;
					win.alert(message);
				}
			}
		},
		clip: function (doc, unique, width) {
			var text = doc.getElementById("td_" + unique);
			var btn = doc.getElementById("btn_" + unique).firstChild;

			if (text.classList.contains("cutText")) {
				text.classList.remove("cutText");
				text.style.maxWidth = "";
				btn.classList.remove("fa-caret-right");
				btn.classList.add("fa-caret-down");
			} else {
				text.classList.add("cutText");
				text.style.maxWidth = width + "ex";
				btn.classList.remove("fa-caret-down");
				btn.classList.add("fa-caret-right");
			}
		},
		hasPermDelete: function (eTable, isFolder) {
			if (eTable === "") {
				return false;
			}
			if (WE().session.permissions.ADMINISTRATOR) {
				return true;
			}
			switch (eTable) {
				case WE().consts.tables.FILE_TABLE:
					return (isFolder ? WE().session.permissions.DELETE_DOC_FOLDER : WE().session.permissions.DELETE_DOCUMENT);
				case WE().consts.tables.TEMPLATES_TABLE:
					return (isFolder ? WE().session.permissions.DELETE_TEMP_FOLDER : WE().session.permissions.DELETE_TEMPLATE);
				case WE().consts.tables.OBJECT_FILES_TABLE:
					return (isFolder ? WE().session.permissions.DELETE_OBJECTFILE : WE().session.permissions.DELETE_OBJECTFILE);
				case WE().consts.tables.OBJECT_TABLE:
					return (isFolder ? false : WE().session.permissions.DELETE_OBJECT);
				default:
					return false;
			}
		},
		validate: {
			email: function (email) {
				email = email.replace(/".*"/g, "y");
				email = email.replace(/\\./g, "z");
				var parts = email.split("@");
				if (parts.length !== 2) {
					return false;
				}
				if (!WE().util.validate.domain(parts[1])) {
					return false;
				}
				if (!parts[0].match(/(.+)/)) {
					return false;
				}
				return true;
			},
			domain: function (domain) {
				var parts = domain.split(".");
				//if(parts.length>3 || parts.length<1) return false;
				//if(parts.length===1 && !WE().util.validate.domainname(parts[0])) return false;
				for (var i = 0; i < (parts.length - 1); i++) {
					if (!WE().util.validate.domainname(parts[i])) {
						return false;
					}
				}
				if (!parts[parts.length - 1].match(/^[a-z][a-z]+$/i)) {
					return false;
				}
				return true;
			},
			domainname: function (domainname) {
				var pattern = /^[^_\-\s/=?\*"'#!§$%&;()\[\]\{\};:,°<>\|][^\s/=?\*"'#!§$%&;()\[\]\{\};:,°<>\|]+$/i;
				if (domainname.match(pattern)) {
					return true;
				}
				return false;
			},
			date: function () {
				// TODO
			},
			currency: function () {
				// TODO
			}
		},
		loadConsts: function (doc, check) {
			var cur = WE().consts;
			var found = true;
			var what = check.split(".");
			for (var i = 0; i < what.length; i++) {
				if (cur[what[i]] === undefined || !cur[what[i]]) {
					found = false;
					break;
				}
				cur = cur[what[i]];
			}
			if (found) {
				return;
			}
			//load consts
			var fileref = doc.createElement('script');
			fileref.setAttribute("src", WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=loadJSConsts&we_cmd[1]=" + check);
			doc.getElementsByTagName("head")[0].appendChild(fileref);
		},
		getDynamicVar: function (doc, id, dataname) {
			var el = doc.getElementById(id);
			return (el ?
							this.decodeDynamicVar(el, dataname) :
							null
							);
		},
		decodeDynamicVar: function (el, dataname) {
			var data = el.getAttribute(dataname);
			return data ? JSON.parse(window.atob(data)) : null;
		},
		rpc: function (url, data, success) {
			return $.ajax({
				type: "POST",
				url: url,
				data: data,
				success: success,
				dataType: "json"
								//timeout: 2000
			}).fail(function (jqxhr, textStatus, error) {
				WE().t_e('JS rpc failed', textStatus, error, jqxhr.responseText, this.url);
			});
		}
	},
	/*all session/user specific stuff goes here */
	session: {},
	/*all consts in WE (e.g. language, paths, ...) */
	consts: {},
};

WebEdition.consts = WebEdition.util.getDynamicVar(document, 'loadWEData', 'data-consts');
WebEdition.session = WebEdition.util.getDynamicVar(document, 'loadWEData', 'data-session');
//finally load language files
WebEdition.util.loadConsts(document, 'g_l.main');

function doClickWithParameters(id, ct, table, parameters) {
	WE().layout.weEditorFrameController.openDocument(table, id, ct, '', '', '', '', '', parameters);
}

function doExtClick(url) {
	// split url in url and parameters !
	var parameters = "",
					_position = 0;

	if ((_position = url.indexOf("?")) !== -1) {
		parameters = url.substring(_position);
		url = url.substring(0, _position);
	}

	WE().layout.weEditorFrameController.openDocument('', '', '', '', '', url, '', '', parameters);
}

function we_repl(target, url) {
	if (target) {
		try {
			// use 2 loadframes to avoid missing cmds
			if (target.name === "load" || target.name === "load2") {
				if (top.lastUsedLoadFrame === target.name) {
					target = (target.name === "load" ?
									window.load2 :
									window.load);
				}
				top.lastUsedLoadFrame = target.name;
			}
		} catch (e) {
			// Nothing
		}
		if (target.location === undefined) {
			target.src = url;
		} else {
			target.location.replace(url);
		}
	}
}

function doSave(url, trans) {
	var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrameByTransaction(trans);
	// _EditorFrame.setEditorIsHot(false);
	if (_EditorFrame.getEditorAutoRebuild()) {
		url += "&we_cmd[8]=1";
	}
	if (!WE().util.we_sbmtFrm(window.load, url)) {
		url += "&we_transaction=" + trans;
		we_repl(window.load, url);
	}
}

function doPublish(url, trans) {
	if (!WE().util.we_sbmtFrm(window.load, url)) {
		url += "&we_transaction=" + trans;
		we_repl(window.load, url);
	}
}

function start(table_to_load) {
	if (table_to_load) {
		we_cmd("load", table_to_load);
	}
}

function doUnloadSEEM(whichWindow) {
	// unlock all open documents
	var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();

	var docIds = "";
	var docTables = "";

	for (var frameId in _usedEditors) {
		if (_usedEditors[frameId].EditorType != "cockpit") {
			docIds += _usedEditors[frameId].getEditorDocumentId() + ",";
			docTables += _usedEditors[frameId].getEditorEditorTable() + ",";
		}
	}

	if (docIds) {
		top.we_cmd('users_unlock', docIds, WE().session.userID, docTables);
		if (top.opener) {
			top.opener.focus();

		}
	}
	//  close the SEEM-edit-include when exists
	if (top.edit_include) {
		top.edit_include.close();
	}
	try {
		WE().util.jsWindow.prototype.closeAll();
		if (WE().layout.browserwind) {
			WE().layout.browserwind.close();
		}
	} catch (e) {

	}

	//  only when no SEEM-edit-include window is closed

	if (whichWindow !== "include") {
		if (window.opener) {
			window.opener.location.replace(WE().consts.dirs.WEBEDITION_DIR + 'we_loggingOut.php');
		}
	}
}

function doUnloadNormal(whichWindow) {
	var tinyDialog;
	if (!WE().layout.regular_logout) {

		if (window.tinyMceDialog !== undefined && window.tinyMceDialog !== null) {
			tinyDialog = tinyMceDialog;
			try {
				tinyDialog.close();
			} catch (err) {
			}
		}

		if (tinyMceSecondaryDialog !== undefined && tinyMceSecondaryDialog !== null) {
			tinyDialog = tinyMceSecondaryDialog;
			try {
				tinyDialog.close();
			} catch (err) {
			}
		}

		try {
			WE().util.jsWindow.prototype.closeAll();
			if (WE().layout.browserwind) {
				WE().layout.browserwind.close();
			}
		} catch (e) {
		}
		if (whichWindow !== "include") { 	// only when no SEEM-edit-include window is closed
			// FIXME: closing-actions for SEEM
			var logoutpopup;
			if (top.opener) {
				if (specialUnload) {
					top.opener.location.replace(WE().consts.dirs.WEBEDITION_DIR + 'we_loggingOut.php?isopener=1');
					top.opener.focus();
				} else {
					top.opener.history.back();
					logoutpopup = window.open(WE().consts.dirs.WEBEDITION_DIR + 'we_loggingOut.php?isopener=0', "webEdition", "width=350,height=70,toolbar=no,menubar=no,directories=no,location=no,resizable=no,status=no,scrollbars=no,top=300,left=500");
					if (logoutpopup) {
						logoutpopup.focus();
					}
				}
			} else {
				logoutpopup = window.open(WE().consts.dirs.WEBEDITION_DIR + 'we_loggingOut.php?isopener=0', "webEdition", "width=350,height=70,toolbar=no,menubar=no,directories=no,location=no,resizable=no,status=no,scrollbars=no,top=300,left=500");
				if (logoutpopup) {
					logoutpopup.focus();
				}
			}
		}
	}

}

function doUnload(whichWindow) { // triggered when webEdition-window is closed
	if (!WE().layout.weEditorFrameController.closeAllDocuments()) {
		return "x";
	}
	if (WE().session.seemode) {
		doUnloadSEEM(whichWindow);
	} else {
		doUnloadNormal(whichWindow);
	}
}

function loadCloseFolder(args) {
	WE().util.rpc(WE().util.getWe_cmdArgsUrl(args, WE().consts.dirs.WEBEDITION_DIR + 'rpc.php?cmd=LoadMainTree&'), null, function (weResponse) {
		if (weResponse && weResponse.Success) {
			if (weResponse.DataArray.treeName) {
				top.document.getElementById("treeName").innerHTML = weResponse.DataArray.treeName;
			}
			if (weResponse.DataArray.items) {
				if (!weResponse.DataArray.parentFolder) {
					top.treeData.clear();
					top.treeData.add(top.node.prototype.rootEntry(0, 'root', 'root', weResponse.DataArray.offset));
				}
				for (var i = 0; i < weResponse.DataArray.items.length; i++) {
					if (!weResponse.DataArray.parentFolder || top.treeData.indexOfEntry(weResponse.DataArray.items[i].id) < 0) {
						top.treeData.add(new top.node(weResponse.DataArray.items[i]));
					}
				}
			}
			top.drawTree();
		}
		top.scrollToY();
	});
}

function getActiveEditors() {
	var activeEditors = {};
	for (var ed in WE().layout.editors) {
		activeEditors[ed] = (WE().layout.editors[ed] !== null);
	}
	return "&activeEditors=" + window.btoa(JSON.stringify(activeEditors));
}

function wecmd_editDocument(args, url) {
	try {
		if ((window.treeData !== undefined) && treeData) {
			treeData.unselectNode();
			if (args[1]) {
				treeData.selection_table = args[1];
			}
			if (args[2]) {
				treeData.selection = args[2];
			}
			if (treeData.selection_table === treeData.table) {
				treeData.selectNode(treeData.selection);
			}
		}
	} catch (e) {
	}
	url += getActiveEditors();

	var ctrl = WE().layout.weEditorFrameController;
	var nextWindow, nextContent;
	if ((nextWindow = ctrl.getFreeWindow())) {
		nextContent = nextWindow.getDocumentReference();
		// activate tab and set state to loading
		WE().layout.multiTabs.addTab(nextWindow.getFrameId(), nextWindow.getFrameId(), nextWindow.getFrameId());
		// use Editor Frame
		nextWindow.initEditorFrameData(
						{
							EditorType: "model",
							EditorEditorTable: args[1],
							EditorDocumentId: args[2],
							EditorContentType: args[3]
						}
		);
		// set Window Active and show it
		ctrl.setActiveEditorFrame(nextWindow.FrameId);
		ctrl.toggleFrames();
		if (nextContent.frames && nextContent.frames[1]) {
			if (!WE().util.we_sbmtFrm(nextContent, url)) {
				we_repl(nextContent, url + "&frameId=" + nextWindow.getFrameId());
			}
		} else {
			we_repl(nextContent, url + "&frameId=" + nextWindow.getFrameId());
		}
	} else {
		top.we_showMessage(WE().consts.g_l.main.no_editor_left, WE().consts.message.WE_MESSAGE_ERROR);
	}
}

function we_openMediaReference(id) {
	id = id ? id : 0;

	if (window.we_mediaReferences && window.we_mediaReferences['id_' + id]) {
		var ref = window.we_mediaReferences['id_' + id];
		switch (ref.type) {
			case 'module':
				top.we_cmd(ref.mod + '_edit_ifthere', ref.id);
				break;
			case 'cat':
				top.we_cmd('editCat', ref.id);
				break;
			default:
				if (ref.isTempPossible && ref.referencedIn === 'main' && ref.isModified) {
					top.we_showMessage('Der Link wurde bei einer unveröffentlichten Änderung entfernt: Er existiert nur noch in der veröffentlichten Version!', WE().consts.message.WE_MESSAGE_ERROR, window);// FIXME: GL()
				} else {
					WE().layout.weEditorFrameController.openDocument(ref.table, ref.id, ref.ct);
				}
		}
	}
}

function we_showInNewTab(args, url) {
	var ctrl = WE().layout.weEditorFrameController;
	var nextWindow;
	if ((nextWindow = ctrl.getFreeWindow())) {
		we_repl(nextWindow.getDocumentReference(), url);
		// activate tab
		var pos = (args[0] === "open_cockpit" ? 0 : undefined);
		WE().layout.multiTabs.addTab(nextWindow.getFrameId(), ' &hellip; ', ' &hellip; ', pos);
		// set Window Active and show it
		ctrl.setActiveEditorFrame(nextWindow.FrameId);
		ctrl.toggleFrames();
	} else {
		WE().util.showMessage(WE().consts.g_l.main.no_editor_left, WE().consts.message.WE_MESSAGE_INFO, window);
	}
}


function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	//	When coming from a we_cmd, always mark the document as opened with we !!!!
	if (WE().layout.weEditorFrameController.getActiveDocumentReference()) {
		switch (args[0]) {
			case 'edit_document':
			case 'new_document':
			case 'open_extern_document':
			case 'edit_document_with_parameters':
			case 'new_folder':
			case 'edit_folder':
				break;
			default:
				WE().layout.weEditorFrameController.getActiveDocumentReference().openedWithWE = true;
		}

	}
	/* all base commands should be added to we_webEditionCmd_base.js */
	var i, mods = WE().consts.modules.jsmods;
	for (i = 0; i < mods.length; i++) {
		if (we_cmd_modules[mods[i]].apply(caller, [args, url, caller])) {
			return true;
		}
		//if a tool window is requested, we have to open it
		if (args[0] === (mods[i] + "_edit")) {
			new (WE().util.jsWindow)(caller, url, "tool_window", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			return true;
		}
	}
	// deal with not activated modules
	mods = WE().consts.modules.inactive;
	for (i = 0; i < mods.length; i++) {
		if (args[0] === (mods[i] + "_edit_ifthere")) {
			new (WE().util.jsWindow)(caller, url, "module_info", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, true, true);
			return true;
		}
	}

	//log this, as it might be an error
	//WE().t_e('cmd opens new tab', args, url);
	//finally open new window
	//make this only a fallback - everthing should be catched, so we add an error for now!
	WE().t_e('js-command not found, open a new window', args);
	we_showInNewTab(args, url);
}

function objectAssign(target, src) {
	if (Object.assign) {
		return Object.assign.call(Array.prototype.slice.call(arguments));
	}
	for (var key in src) {
		target[key] = src[key];
	}
	return target;
}

function updateMainTree(select, attribs, adv) {
	if (top.treeData) {
		if (select) {
			top.treeData.selection_table = attribs.table;
			top.treeData.selection = attribs.id;
		} else {
			top.treeData.unselectNode();
		}
		if (top.treeData.table === attribs.table) {
			if (top.treeData[top.treeData.indexOfEntry(attribs.parentid)]) {

				/*var visible = (top.treeData.indexOfEntry(attribs.parentid) !== -1 ?
				 top.treeData[top.treeData.indexOfEntry(attribs.parentid)].open :
				 0);*/
				if (top.treeData.indexOfEntry(attribs.id) !== -1) {
					top.treeData.updateEntry(attribs);
				} else {
					top.treeData.addSort(new top.node(objectAssign(attribs, adv)));
				}
				top.drawTree();
			} else if (top.treeData.indexOfEntry(attribs.id) !== -1) {
				top.treeData.deleteEntry(attribs.id);
			}
		}
	}
}

function getHotDocumentsString() {
	var allHotDocuments = WE().layout.weEditorFrameController.getEditorsInUse();
	var ct, ulCtElem, i;
	var ret = "";
	var hotDocumentsOfCt = {};
	for (var frameId in allHotDocuments) {
		ct = allHotDocuments[frameId].getEditorContentType();
		if (!hotDocumentsOfCt[ct]) {
			hotDocumentsOfCt[ct] = [];
		}
		hotDocumentsOfCt[ct].push(allHotDocuments[frameId]);
	}

	for (ct in hotDocumentsOfCt) {
		ulCtElem = "";

		for (i = 0; i < hotDocumentsOfCt[ct].length; i++) {
			ulCtElem += "<li>" + (hotDocumentsOfCt[ct][i].getEditorDocumentText() ?
							hotDocumentsOfCt[ct][i].getEditorDocumentPath() :
							"<em>" + WE().consts.g_l.main.untitled + "</em>") + "</li>";
		}

		ret += "<li>" + WE().consts.g_l.contentTypes[ct] + "<ul>" + ulCtElem + "</ul></li>";
	}
	return ret;
}

function switchEditPage(args, url) {
	// get editor root frame of active tab
	var currentEditorRootFrame = WE().layout.weEditorFrameController.getActiveDocumentReference();
	// get visible frame for displaying editor page
	var visibleEditorFrame = WE().layout.weEditorFrameController.getVisibleEditorFrame();
	// frame where the form should be sent from
	var sendFromFrame = visibleEditorFrame;
	// set flag to true if active frame is frame nr 2 (frame for displaying editor page 1 with content editor)
	var isEditpageContent = (visibleEditorFrame === currentEditorRootFrame.frames[2]);
	//var _isEditpageContent = _visibleEditorFrame == _currentEditorRootFrame.document.getElementsByTagName("div")[2].getElementsByTagName("iframe")[0];

	// if we switch from we_base_constants::WE_EDITPAGE_CONTENT to another page
	if (isEditpageContent && args[1] !== WE().consts.global.WE_EDITPAGE_CONTENT) {
		// clean body to avoid flickering
		try {
			currentEditorRootFrame.frames[1].document.body.innerHTML = "";
		} catch (e) {
			//can be caused by not loaded content
		}
		// switch to normal frame
		WE().layout.weEditorFrameController.switchToNonContentEditor();
		// set var to new active editor frame
		visibleEditorFrame = currentEditorRootFrame.frames[1];
		//_visibleEditorFrame = _currentEditorRootFrame.document.getElementsByTagName("div")[1].getElementsByTagName("iframe")[0];

		// set flag to false
		isEditpageContent = false;
		// if we switch to we_base_constants::WE_EDITPAGE_CONTENT from another page
	} else if (!isEditpageContent && args[1] === WE().consts.global.WE_EDITPAGE_CONTENT) {
		// switch to content editor frame
		WE().layout.weEditorFrameController.switchToContentEditor();
		// set var to new active editor frame
		visibleEditorFrame = currentEditorRootFrame.frames[2];
		//_visibleEditorFrame = _currentEditorRootFrame.document.getElementsByTagName("div")[2].getElementsByTagName("iframe")[0];
		// set flag to false
		isEditpageContent = true;
	}

	// frame where the form should be sent to
	var _sendToFrame = visibleEditorFrame;
	// get active transaction
	var _we_activeTransaction = WE().layout.weEditorFrameController.getActiveEditorFrame().getEditorTransaction();
	// if there are parameters, attach them to the url
	if (currentEditorRootFrame.parameters) {
		url += currentEditorRootFrame.parameters;
	}
	url += getActiveEditors();

	// focus the frame
	if (_sendToFrame) {
		_sendToFrame.focus();
	}
	// if visible frame equals to editpage content and there is already content loaded
	if (isEditpageContent && visibleEditorFrame && visibleEditorFrame.weIsTextEditor !== undefined && currentEditorRootFrame.frames[2].location !== "about:blank") {
		// tell the backend the right edit page nr and break (don't send the form)
		WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=SetPageNr", "transaction=" + _we_activeTransaction + "&editPageNr=" + args[1]);
		//FAIL: top.we_showMessage(WE().consts.g_l.main.unable_to_call_setpagenr, WE().consts.message.WE_MESSAGE_ERROR);
		if (visibleEditorFrame.reloadContent === false) {
			return;
		}
		visibleEditorFrame.reloadContent = false;
	}

	if (currentEditorRootFrame) {
		if (!WE().util.we_sbmtFrm(_sendToFrame, url, sendFromFrame)) {
			// add we_transaction, if not set
			if (!args[2]) {
				args[2] = _we_activeTransaction;
			}
			url += "&we_transaction=" + args[2];
			we_repl(_sendToFrame, url);
		}
	}
}

function collection_insertFiles(args) {
	if (args[1] === undefined || args[2] === undefined) {
		return;
	}

	var collection = parseInt(args[2]);
	var ids = (args[1].success !== undefined ? args[1].success : (args[1].currentID !== undefined ? [
		args[1].currentID] : args[1]));

	if (collection && ids) {
		var usedEditors = WE().layout.weEditorFrameController.getEditorsInUse(),
						editor = null,
						index = args[3] !== undefined ? args[3] : -1,
						recursive = args[5] !== undefined ? args[5] : false,
						transaction, frameId, candidate;

		for (frameId in usedEditors) {
			candidate = usedEditors[frameId];
			if (candidate.getEditorEditorTable() === WE().consts.tables.VFILE_TABLE && parseInt(candidate.getEditorDocumentId()) === collection) {
				if (candidate.getEditorEditPageNr() == 1) {
					editor = candidate;
				} else {
					transaction = candidate.getEditorTransaction();
				}
				break;
			}
		}

		if (editor) {
			// FIXME: we need a consize distinction between index and position
			//var index = editor.getContentEditor().weCollectionEdit.getItemId(editor.getContentEditor().document.getElementById('collectionItem_staticIndex_' + index))
			editor.getContentEditor().weCollectionEdit.callForValidItemsAndInsert(index, ids.join(), 'bla', recursive, true);
		} else {
			var position = args[4] !== undefined ? args[4] : index;
			we_cmd('collection_insertFiles_rpc', ids, collection, transaction, position, recursive);
		}
	}
}

function addToCollection(args, url) {
	if (WE().session.seemode) {
		//
	} else {
		if (WE().session.deleteMode != args[1]) {
			WE().session.deleteMode = args[1];
		}
		if (!WE().session.deleteMode && treeData.state == treeData.tree_states.select) {
			treeData.setState(treeData.tree_states.edit);
			drawTree();
		}
		window.document.getElementById("bm_treeheaderDiv").classList.add('collectionSelector');
		window.document.getElementById("treetable").classList.add('collectionSelector');
		WE().layout.tree.toggle(true);
		var width = WE().layout.tree.getWidth();
		WE().layout.tree.widthBeforeDeleteMode = width;
		if (width < WE().consts.size.tree.moveWidth) {
			WE().layout.tree.setWidth(WE().consts.size.tree.moveWidth);
		}
		WE().layout.tree.storeWidth(WE().layout.tree.widthBeforeDeleteMode);

		WE().layout.sidebar.widthBeforeDeleteMode = WE().layout.sidebar.getWidth();

		if (args[2] != 1) {
			we_repl(document.getElementsByName("treeheader")[0], url);
		}
	}
}

function we_cmd_move(args, url) {
	if (WE().session.seemode) {
		if (WE().session.deleteMode != args[1]) {
			WE().session.deleteMode = args[1];
		}
		if (args[2] != 1) {
			we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference(), url);
		}
	} else {
		if (WE().session.deleteMode != args[1]) {
			WE().session.deleteMode = args[1];
		}
		if (!WE().session.deleteMode && treeData.state == treeData.tree_states.selectitem) {
			treeData.setState(treeData.tree_states.edit);
			drawTree();
		}
		window.document.getElementById("bm_treeheaderDiv").classList.add('moveSelector');
		window.document.getElementById("treetable").classList.add('moveSelector');
		WE().layout.tree.toggle(true);
		var width = WE().layout.tree.getWidth();

		WE().layout.tree.widthBeforeDeleteMode = width;

		if (width < WE().consts.size.tree.moveWidth) {
			WE().layout.tree.setWidth(WE().consts.size.tree.moveWidth);
		}
		WE().layout.tree.storeWidth(WE().layout.tree.widthBeforeDeleteMode);

		WE().layout.sidebar.widthBeforeDeleteMode = WE().layout.sidebar.getWidth();

		if (args[2] != 1) {
			we_repl(document.getElementsByName("treeheader")[0], url);
		}
	}
}

function we_cmd_delete(args, url) {
	if (WE().session.seemode) {
		if (WE().session.deleteMode != args[1]) {
			WE().session.deleteMode = args[1];
		}
		if (args[2] != 1) {
			we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference(), url);
		}
		return;
	}
	if (WE().session.deleteMode != args[1]) {
		WE().session.deleteMode = args[1];
	}
	if (!WE().session.deleteMode && treeData.state == treeData.tree_states.select) {
		treeData.setState(treeData.tree_states.edit);
		drawTree();
	}
	window.document.getElementById("bm_treeheaderDiv").classList.add('deleteSelector');
	window.document.getElementById("treetable").classList.add('deleteSelector');
	WE().layout.tree.toggle(true);
	var width = WE().layout.tree.getWidth();

	WE().layout.tree.widthBeforeDeleteMode = width;

	if (width < WE().consts.size.tree.deleteWidth) {
		WE().layout.tree.setWidth(WE().consts.size.tree.deleteWidth);
	}
	WE().layout.tree.storeWidth(WE().layout.tree.widthBeforeDeleteMode);

	WE().layout.sidebar.widthBeforeDeleteMode = WE().layout.sidebar.getWidth();

	if (args[2] != 1) {
		we_repl(document.getElementsByName("treeheader")[0], url);
	}

}

function open_wysiwyg_window(args, caller) {
	if (WE().layout.weEditorFrameController.getActiveDocumentReference()) {
		WE().layout.weEditorFrameController.getActiveDocumentReference().openedWithWE = false;
	}

	var wyw = args[2];
	wyw = Math.max((wyw ? wyw : 0), 400);
	var wyh = args[3];
	wyh = Math.max((wyh ? wyh : 0), 300);
	if (window.screen) {
		var screen_height = ((screen.height - 50) > screen.availHeight) ? screen.height - 50 : screen.availHeight;
		screen_height = screen_height - 100;
		var screen_width = screen.availWidth - 10;
		wyw = Math.min(screen_width, wyw);
		wyh = Math.min(screen_height, wyh);
	}

	var url = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd_frontend.php?we_cmd[0]=open_wysiwyg_window&we_dialog_args[dialogProperties]=' + args[1];
	new (WE().util.jsWindow)(caller, url, "we_wysiwygWin", Math.max(220, wyw + (document.all ? 0 : ((navigator.userAgent.toLowerCase().indexOf('safari') > -1) ? 20 : 4))), Math.max(100, wyh + 60), true, false, true);
}

function we_cmd_new_document(url) {
	var ctrl = WE().layout.weEditorFrameController,
					nextWindow, nextContent;
	if ((nextWindow = ctrl.getFreeWindow())) {
		nextContent = nextWindow.getDocumentReference();
		// activate tab and set it status loading ...
		WE().layout.multiTabs.addTab(nextWindow.getFrameId(), nextWindow.getFrameId(), nextWindow.getFrameId());
		nextWindow.updateEditorTab();
		// set Window Active and show it
		ctrl.setActiveEditorFrame(nextWindow.getFrameId());
		ctrl.toggleFrames();
		// load new document editor
		we_repl(nextContent, url + "&frameId=" + nextWindow.getFrameId());
	} else {
		top.we_showMessage(WE().consts.g_l.main.no_editor_left, WE().consts.message.WE_MESSAGE_ERROR);
	}
}

function we_cmd_delete_single_document_question(url) {
	var ctrl = WE().layout.weEditorFrameController;
	var cType = ctrl.getActiveEditorFrame().getEditorContentType();
	var eTable = ctrl.getActiveEditorFrame().getEditorEditorTable();
	var path = ctrl.getActiveEditorFrame().getEditorDocumentPath();

	if (ctrl.getActiveDocumentReference()) {
		if (!WE().util.hasPermDelete(eTable, (cType === WE().consts.contentTypes.FOLDER))) {
			top.we_showMessage(WE().consts.g_l.main.no_perms_action, WE().consts.message.WE_MESSAGE_ERROR, window);
		} else if (window.confirm(WE().consts.g_l.main.delete_single_confirm_delete + path)) {
			var url2 = url.replace(/we_cmd\[0\]=delete_single_document_question/g, "we_cmd[0]=delete_single_document");
			WE().util.we_sbmtFrm(window.load, url2 + "&we_cmd[2]=" + ctrl.getActiveEditorFrame().getEditorEditorTable(), ctrl.getActiveDocumentReference().frames.editFooter);
		}
	} else {
		top.we_showMessage(WE().consts.g_l.main.no_document_opened, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function we_cmd_delete_single_document(url) {
	var ctrl = WE().layout.weEditorFrameController;
	var cType = ctrl.getActiveEditorFrame().getEditorContentType();
	var eTable = ctrl.getActiveEditorFrame().getEditorEditorTable();

	if (ctrl.getActiveDocumentReference()) {
		if (!WE().util.hasPermDelete(eTable, (cType === WE().consts.contentTypes.FOLDER))) {
			top.we_showMessage(WE().consts.g_l.main.no_perms_action, WE().consts.message.WE_MESSAGE_ERROR, window);
		} else {
			WE().util.we_sbmtFrm(window.load, url + "&we_cmd[2]=" + ctrl.getActiveEditorFrame().getEditorEditorTable(), ctrl.getActiveDocumentReference().editFooter);
		}
	} else {
		top.we_showMessage(WE().consts.g_l.main.no_document_opened, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function doReloadCmd(args, url, hot) {
	if (hot) {
		// set Editor hot
		var _EditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();
		_EditorFrame.setEditorIsHot(true);
	}
	if (top.setScrollTo) {
		window.setScrollTo();
	}
	// get editor root frame of active tab
	var _currentEditorRootFrame = WE().layout.weEditorFrameController.getActiveDocumentReference();
	// get visible frame for displaying editor page
	var _visibleEditorFrame = WE().layout.weEditorFrameController.getVisibleEditorFrame();

	// focus visible editor frame
	if (_visibleEditorFrame) {
		_visibleEditorFrame.focus();
	}

	url += getActiveEditors();

	// if cmd equals "reload_editpage" and there are parameters, attach them to the url
	if (args[0] === "reload_editpage" || args[0] === "reload_hot_editpage") {
		url += (_currentEditorRootFrame.parameters ? _currentEditorRootFrame.parameters : '') +
						(args[1] ? '#f' + args[1] : '');
	} else if (args[0] === "remove_image" && args[2]) {
		url += '#f' + args[2];
	}

	if (_currentEditorRootFrame) {
		if (!WE().util.we_sbmtFrm(_visibleEditorFrame, url, _visibleEditorFrame)) {
			if (args[0] !== "update_image") {
				// add we_transaction, if not set
				if (!args[2]) {
					args[2] = WE().layout.weEditorFrameController.getActiveEditorFrame().getEditorTransaction();
				}
				url += "&we_transaction=" + args[2];
			}
			we_repl(_visibleEditorFrame, url);
		}
	}
}

function startMsg() {
	top._console_ = new (WE().layout.messageConsoleView)("mainWindow", window);
	top._console_.register();
	window.document.body.addEventListener("onunload", top._console_.unregister);
}

function checkPwd(sufficient) {
	if (!sufficient) {
		top.we_showMessage(WE().consts.g_l.alert.pwd_startupRegExFailed, WE().consts.message.WE_MESSAGE_ERROR);
	}
}

function updateCheck(avail, version, date) {
	if (avail) {
		top.we_showMessage(WE().util.sprintf(WE().consts.g_l.alert.newWEAvailable, version, date), WE().consts.message.WE_MESSAGE_INFO, window);
	}
}