/* global WE */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

ImageEditTools = {
	activeTool: '',
	size: {
		origW: 0,
		origH: 0
	},
	deactivateAll: function () {
		switch (this.activeTool) {
			case 'focus':
				this.Focus.drop();
				break;
			case 'crop':
				this.Crop.drop();
				break;
			case 'resize':
				this.Resize.drop();
				break;
			case 'rotate':
				this.Rotate.drop();
				break;
			case 'convertJPEG':
				this.ConvertJPEG.drop();
				break;
			default:
				//
		}
		this.activeTool = '';
	},
	Crop: {
		imgSrc: "",
		imgW: 0,
		imgH: 0,
		defW: 0,
		defH: 0,
		imgDiv: "weImgDiv",
		imgBorder: "weImagePanelBorder",
		imgPanel: "weImagePanel",
		imgID: "weImage",
		coverID: "weCoverPanel",
		cropID: "weCropPanel",
		cropCtrl: "weControl",
		sel: null,
		scale: null,
		initScale: null,
		prevScale: {x: 0, y: 0},
		scaleX: 0,
		scaleY: 0,
		coords: null,
		posX1: 0,
		posX2: 0,
		posY1: 0,
		posY2: 0,
		snap: 8,
		altKey: false,
		shiftKey: false,
		ctrlKey: false,
		triggered: false,
		eventHandler: {},
		crop: function (x, y, w, h) {
			var CropTool = ImageEditTools.Crop;
			ImageEditTools.deactivateAll();
			ImageEditTools.activeTool = 'crop';

			this.triggered = true;
			var elIdDiv = document.getElementById(this.imgDiv);
			elIdDiv.style.border = "2px solid #CECECE";
			var elIdBorder = document.getElementById(this.imgBorder);
			elIdBorder.style.border = "1px solid #808080";
			var elIdImage = document.getElementById(this.imgID);
			this.defW = elIdImage.width;
			this.defH = elIdImage.height;
			elIdImage.width = ImageEditTools.size.origW;
			elIdImage.height = ImageEditTools.size.origH;
			this.imgW = elIdImage.offsetWidth;
			this.imgH = elIdImage.offsetHeight;
			this.imgSrc = elIdImage.src;
			elIdImage.style.cursor = "crosshair";
			var elCropCtrl = document.getElementById(this.cropCtrl);
			elCropCtrl.style.display = "block";
			this.getElements();
			var elIds = document.getElementById(this.imgPanel);
			elIds.style.cursor = "crosshair";
			var elIdCrop = document.getElementById(this.cropID);
			var elIdLeft = document.getElementById(this.sel.idLeft);
			var elIdTop = document.getElementById(this.sel.idTop);
			var elIdRight = document.getElementById(this.sel.idRight);
			var elIdBottom = document.getElementById(this.sel.idBottom);

			this.addLs(elIds, "onmousedown", CropTool.setSelectionEnabled);
			this.addLs(elIds, "onmouseup", CropTool.handleMouseReleased);
			this.addLs(document, "onmouseup", CropTool.handleMouseReleased);
			this.addLs(document, "onmousemove", CropTool.handleMouseDragged);
			this.addLs(elIdCrop, "onmousedown", CropTool.setDragEnabled);
			this.addLs(elIds, "onclick", CropTool.handleMousePressed);
			this.addLs(document, "onkeydown", CropTool.handleKeyPressed);
			this.addLs(document, "onkeyup", CropTool.handleKeyReleased);
			this.addLs(elIdLeft, "onmousedown", CropTool.dragLeftEnabled);
			this.addLs(elIdTop, "onmousedown", CropTool.dragTopEnabled);
			this.addLs(elIdRight, "onmousedown", CropTool.dragRightEnabled);
			this.addLs(elIdBottom, "onmousedown", CropTool.dragBottomEnabled);

			if (arguments.length > 0) {
				CropTool.sel.draw(x, y, w, h);
				document.getElementById(CropTool.coverID).style.visibility = "visible";
				CropTool.sel.setCursor("e-resize", "n-resize", "e-resize", "n-resize", "move");
				CropTool.setCropSize(x, y, w, h);
			}

			this.resetCropTool();
			imgDiv = null;
			imgBorder = null;
			elIdImage = null;
			elCropCtrl = null;
			elIds = null;
			elIdCrop = null;
			elIdLeft = null;
			elIdTop = null;
			elIdRight = null;
			elIdBottom = null;
		},
		addLs: function (object, eventName, listener) {
			var CropTool = ImageEditTools.Crop;
			if (CropTool === null || CropTool === undefined) {
				CropTool = window;
			}
			if (!object[eventName + "listeners"]) {
				this.getLs(object, eventName);
			}
			var listeners = object[eventName + "listeners"];
			var setListener = true;
			for (var i = 0; setListener && i < listeners.length; i++) {
				if (listeners[i][0] == listener && listeners[i][1] == CropTool) {
					setListener = false;
				}
			}
			if (setListener) {
				listeners[listeners.length] = [listener, CropTool];
			}
			return false;
		},
		clLs: function (object, eventName) {
			object[eventName + "listeners"] = [];
			return false;
		},
		getLs: function (object, eventName) {
			var CropTool = ImageEditTools.Crop;

			object[eventName + "listeners"] = [];
			if (typeof object[eventName] === "function") {
				object[eventName + "listeners"][0] = [object[eventName], object];
			}
			object[eventName] = function (a) {
				var i;
				var argumentsCopy = Array.prototype.slice.call(arguments);
				if (arguments.length === 0 && window.event) {
					argumentsCopy[0] = CropTool.patchEvent(window.event, this);
				} else if (a && typeof a === "object" && a.toString().search(/event/i) != -1) {
					argumentsCopy[0] = CropTool.patchEvent(a, this);
				}

				var listeners = this[eventName + "listeners"];
				var listenersCopy = [];
				for (i = 0; i < listeners.length; i++) {
					listenersCopy[i] = listeners[i];
				}
				for (i = 0; i < listenersCopy.length; i++) {
					listenersCopy[i][0].apply(listenersCopy[i][1], argumentsCopy);
				}
			};
		},
		patchEvent: function (evt, currentTarget) {
			if (!evt.target) {
				evt.target = evt.srcElement;
			}
			if (!evt.currentTarget) {
				evt.currentTarget = currentTarget;
			}
			if (evt.layerX === undefined) {
				evt.layerX = evt.offsetX;
			}
			if (evt.layerY === undefined) {
				evt.layerY = evt.offsetY;
			}
			if (evt.clientX === undefined) {
				evt.clientX = evt.pageX;
			}
			if (evt.clientY === undefined) {
				evt.clientY = evt.pageY;
			}
			if (!evt.stopPropagation) {
				evt.stopPropagation = function () {
					this.cancelBubble = true;
				};
			}
			if (!evt.preventDefault) {
				evt.preventDefault = function () {
					this.returnValue = false;
				};
			}
			return evt;
		},
		handleEvent: function (e, sourceElement) {
			if (!e.target) {
				e.target = e.srcElement;
			}
			if (!e.sourceElement) {
				e.sourceElement = sourceElement;
			}
			if (e.layerX === undefined) {
				e.layerX = e.offsetX;
			}
			if (e.layerY === undefined) {
				e.layerY = e.offsetY;
			}
			if (e.clientX === undefined) {
				e.clientX = e.pageX;
			}
			if (e.clientY === undefined) {
				e.clientY = e.pageY;
			}
			if (!e.stopPropagation) {
				e.stopPropagation = function () {
					this.cancelBubble = true;
				};
			}
			if (!e.preventDefault) {
				e.preventDefault = function () {
					this.returnValue = false;
				};
			}
			return e;
		},
		handleMouseDragged: function (e) {
			if (e.altKey) {
				this.altKey = e.altKey;
			}
			if (e.shiftKey) {
				this.shiftKey = e.shiftKey;
			}
			if (e.ctrlKey) {
				this.ctrlKey = e.ctrlKey;
			}
			if (this.sel.track || this.sel.drag || this.sel.dragLeft || this.sel.dragTop || this.sel.dragRight || this.sel.dragBottom) {
				e.preventDefault();
				this.eventHandler.clientX = e.clientX;
				this.eventHandler.clientY = e.clientY;
				if (this.sel.track) {
					this.repaint(this.eventHandler);
				} else if (this.sel.drag) {
					this.recalculateDrop(this.eventHandler);
				} else if (this.sel.dragLeft) {
					this.dragLeft(this.eventHandler);
					this.sel.setCursor("e-resize");
				} else if (this.sel.dragTop) {
					this.dragTop(this.eventHandler);
					this.sel.setCursor("n-resize");
				} else if (this.sel.dragRight) {
					this.dragRight(this.eventHandler);
					this.sel.setCursor("e-resize");
				} else if (this.sel.dragBottom) {
					this.dragBottom(this.eventHandler);
					this.sel.setCursor("n-resize");
				}
			}
			this.prevScale.x = e.clientX;
			this.prevScale.y = e.clientY;
			return false;
		},
		handleMouseReleased: function (e) {
			e.preventDefault();
			e.stopPropagation();
			var cover = document.getElementById(this.coverID);
			if (e.altKey) {
				this.altKey = e.altKey;
			}
			if (e.shiftKey) {
				this.shiftKey = e.shiftKey;
			}
			if (e.ctrlKey) {
				this.ctrlKey = e.ctrlKey;
			}
			if (this.sel.track) {
				if (this.initScale.x == e.clientX && this.initScale.y == e.clientY) {
					this.resetCropTool();
				} else {
					this.repaint(e);
					cover.style.visibility = "visible";
					this.prevScale.x = e.clientX;
					this.prevScale.y = e.clientY;
				}
			} else if (e.target.id == this.coverID || e.target.id == this.imgID) {
				this.resetCropTool();
				document.getElementById("console").style.display = "none";
				WE().layout.button.switch_button_state(document, "save", "disabled");
			}
			this.sel.setCursor("e-resize", "n-resize", "e-resize", "n-resize", "move", "pointer");
			this.sel.track = false;
			this.sel.drag = false;
			this.sel.dragLeft = false;
			this.sel.dragTop = false;
			this.sel.dragRight = false;
			this.sel.dragBottom = false;
			cover = null;
			return false;
		},
		handleMousePressed: function (e) {
			e.preventDefault();
			e.stopPropagation();
			return false;
		},
		handleKeyPressed: function (e) {
			if (!this.altKey && e.altKey || !this.shiftKey && e.shiftKey || !this.ctrlKey && e.ctrlKey) {
				this.handleKeystroke(e);
			}
		},
		handleKeyReleased: function (e) {
			if (this.altKey && !e.altKey || this.shiftKey && !e.shiftKey || this.ctrlKey && !e.ctrlKey) {
				this.handleKeystroke(e);
			}
		},
		handleKeystroke: function (e) {
			this.altKey = e.altKey;
			this.shiftKey = e.shiftKey;
			this.ctrlKey = e.ctrlKey;
			if (this.sel.track || this.sel.drag) {
				e.preventDefault();
				this.eventHandler.preventDefault = function () {
				};
				this.eventHandler.clientX = this.prevScale.x;
				this.eventHandler.clientY = this.prevScale.y;
				this.eventHandler.altKey = e.altKey;
				this.eventHandler.shiftKey = e.shiftKey;
				this.eventHandler.ctrlKey = e.ctrlKey;
			}
			if (this.sel.track) {
				this.repaint(this.eventHandler);
			} else if (this.sel.drag) {
				this.handleMouseDragged(this.eventHandler);
			}
		},
		setSelectionEnabled: function (e) {
			e.preventDefault();
			e.stopPropagation();
			if (e.altKey) {
				this.altKey = e.altKey;
			}
			if (e.shiftKey) {
				this.shiftKey = e.shiftKey;
			}
			if (e.ctrlKey) {
				this.ctrlKey = e.ctrlKey;
			}
			if (this.sel.getVisibility() != "visible") {
				this.scale = {x: e.layerX + 1, y: e.layerY + 1};
				this.initScale = {x: e.clientX, y: e.clientY};
				this.prevScale.x = e.clientX;
				this.prevScale.y = e.clientY;
				this.sel.draw(e.layerX, e.layerY, 1, 1);
				this.sel.track = true;
			}
			return false;
		},
		setDragEnabled: function (e) {
			e.preventDefault();
			if (e.altKey)
				this.altKey = e.altKey;
			if (e.shiftKey)
				this.shiftKey = e.shiftKey;
			if (e.ctrlKey)
				this.ctrlKey = e.ctrlKey;
			this.coords = {x: e.clientX, y: e.clientY};
			this.posX1 = this.sel.getLeft();
			this.posX2 = this.sel.getRight();
			this.posY1 = this.sel.getTop();
			this.posY2 = this.sel.getBottom();
			this.sel.drag = true;
			return false;
		},
		dragLeftEnabled: function (e) {
			e.preventDefault();
			this.coords = {x: e.clientX, y: e.clientY};
			this.posX1 = this.sel.getLeft();
			this.sel.dragLeft = true;
			return false;
		},
		dragLeft: function (e) {
			var CropTool = ImageEditTools.Crop;
			var x1 = this.posX1 + e.clientX - this.coords.x;
			var old_x1 = this.sel.getLeft();
			var old_w = this.sel.getWidth();
			var w = old_w - (x1 - old_x1);
			//this.sel.setLeft(x1);
			this.setCropCoordX(x1);
			CropTool.setCropWidth(w);
		},
		dragTopEnabled: function (e) {
			e.preventDefault();
			this.coords = {x: e.clientX, y: e.clientY};
			this.posY1 = this.sel.getTop();
			this.sel.dragTop = true;
			return false;
		},
		dragTop: function (e) {
			var CropTool = ImageEditTools.Crop;
			var y1 = this.posY1 + e.clientY - this.coords.y;
			var old_h = this.sel.getHeight();
			var old_y1 = this.sel.getTop();
			var h = old_h - (y1 - old_y1);
			//this.sel.setTop(y1);
			this.setCropCoordY(y1);
			CropTool.setCropHeight(h);
		},
		dragRightEnabled: function (e) {
			e.preventDefault();
			this.coords = {x: e.clientX, y: e.clientY};
			this.posX2 = this.sel.getRight();
			this.sel.dragRight = true;
			return false;
		},
		dragRight: function (e) {
			var CropTool = ImageEditTools.Crop;
			var x1 = this.sel.getLeft();
			var new_x1 = x1 + e.clientX - this.coords.x;
			var new_x2 = this.sel.getRight();
			var orig_x2 = this.posX2;
			var orig_w = orig_x2 - x1;
			var old_w = this.sel.getWidth();
			var w = old_w - (new_x2 - new_x1) + orig_w;
			//var x2 = x1 + w;
			this.sel.setRight(new_x2);
			CropTool.setCropWidth(w);
		},
		dragBottomEnabled: function (e) {
			e.preventDefault();
			this.coords = {x: e.clientX, y: e.clientY};
			this.posY2 = this.sel.getBottom();
			this.sel.dragBottom = true;
			return false;
		},
		dragBottom: function (e) {
			var CropTool = ImageEditTools.Crop;
			var y1 = this.sel.getTop();
			var new_y1 = y1 + e.clientY - this.coords.y;
			var new_y2 = this.sel.getBottom();
			var orig_y2 = this.posY2;
			var orig_h = orig_y2 - y1;
			var old_h = this.sel.getHeight();
			var h = old_h - (new_y2 - new_y1) + orig_h;
			//var y2 = y1 + h;
			this.sel.setBottom(new_y2);
			CropTool.setCropHeight(h);
		},
		recalculateDrop: function (e) {
			var x1 = this.posX1 + e.clientX - this.coords.x;
			var y1 = this.posY1 + e.clientY - this.coords.y;
			var w = this.sel.getWidth();
			var h = this.sel.getHeight();
			var edge_x1 = x1;
			if (edge_x1 < 0 || edge_x1 <= this.snap && !this.ctrlKey) {
				x1 = 0;
			}
			var edge_y1 = y1;
			if (edge_y1 < 0 || edge_y1 <= this.snap && !this.ctrlKey) {
				y1 = 0;
			}
			var edge_x2 = this.imgW - w - x1;
			if (edge_x2 < 0 || edge_x2 <= this.snap && !this.ctrlKey) {
				x1 += edge_x2;
			}
			var edge_y2 = this.imgH - h - y1;
			if (edge_y2 < 0 || edge_y2 <= this.snap && !this.ctrlKey) {
				y1 += edge_y2;
			}
			this.sel.moveTo(x1, y1);
			this.setCropSize(x1, y1);
		},
		repaint: function (e) {
			if (this.altKey) {
				this.scaleX += e.clientX - this.prevScale.x;
				this.scaleY += e.clientY - this.prevScale.y;
			}
			var invertH = (e.clientX - (this.initScale.x + this.scaleX) < 0) ? true : false;
			var invertV = (e.clientY - (this.initScale.y + this.scaleY) < 0) ? true : false;
			var w = Math.abs(e.clientX - (this.initScale.x + this.scaleX));
			var h = Math.abs(e.clientY - (this.initScale.y + this.scaleY));
			var x1 = this.scale.x + this.scaleX;
			var y1 = this.scale.y + this.scaleY;
			if (invertH) {
				x1 -= w;
			}
			if (invertV) {
				y1 -= h;
			}
			var edge_x1 = x1;
			if (edge_x1 < 0 || edge_x1 <= this.snap && !this.ctrlKey) {
				w += edge_x1;
				x1 = 0;
			}
			var edge_y1 = y1;
			if (edge_y1 < 0 || edge_y1 <= this.snap && !this.ctrlKey) {
				h += edge_y1;
				y1 = 0;
			}
			var edge_x2 = this.imgW - w - x1;
			if (edge_x2 < 0 || edge_x2 <= this.snap && !this.ctrlKey) {
				w += edge_x2;
			}
			var edge_y2 = this.imgH - h - y1;
			if (edge_y2 < 0 || edge_y2 <= this.snap && !this.ctrlKey) {
				h += edge_y2;
			}
			if (this.shiftKey) {
				if (w > h) {
					if (invertH) {
						x1 += w - h;
					}
					w = h;
				}
				if (h > w) {
					if (invertV) {
						y1 += h - w;
					}
					h = w;
				}
			}
			this.sel.draw(x1, y1, w, h);
			this.setCropSize(x1, y1, w, h);
			document.getElementById("console").style.display = "block";
			WE().layout.button.switch_button_state(document, "save", "enabled");
		},
		setCropSize: function (x1, y1, w, h) {
			document.forms.we_form.cropCoordX.value = x1;
			document.forms.we_form.cropCoordY.value = y1;
			if (arguments.length > 2) {
				this.setCropWidth(w);
				this.setCropHeight(h);
			}
		},
		setCropCoordX: function (x1) {
			x1 = Math.round(Number(x1));
			var y1 = this.sel.getTop();
			var w = this.sel.getWidth();
			var h = this.sel.getHeight();
			var cover = document.getElementById(this.coverID);


			if ((x1 - x1 !== 0) || (x1 < 0) || (x1 > this.imgW)) {
				if (typeof this.sel.getLeft() != "number") {
					document.forms.we_form.cropCoordX.value = "";
				} else {
					document.forms.we_form.cropCoordX.value = this.sel.getLeft();
				}
			} else {
				this.sel.setLeft(x1);
				this.sel.setRight(x1 + w);
				document.forms.we_form.cropCoordX.value = x1;
				if (typeof w === "number" && x1 + w > this.imgW) {
					w = this.imgW - x1;
				}
				if (typeof w === "number" && typeof y1 === "number" && typeof h === "number") {
					this.sel.draw(x1, y1, w, h);
					cover.style.visibility = "visible";
				}
			}
		},
		setCropCoordY: function (y1) {
			y1 = Math.round(Number(y1));
			var x1 = this.sel.getLeft();
			var w = this.sel.getWidth();
			var h = this.sel.getHeight();
			var cov = document.getElementById(this.coverID);


			if ((y1 - y1 !== 0) || (y1 < 0) || (y1 > this.imgH)) {
				if (typeof this.sel.getTop() != "number") {
					document.forms.we_form.cropCoordY.value = "";
				} else {
					document.forms.we_form.cropCoordY.value = this.sel.getTop();
				}
			} else {
				this.sel.setTop(y1);
				document.forms.we_form.cropCoordY.value = y1;
				if (typeof h == "number" && y1 + h > this.imgH) {
					h = this.imgH - y1;
				}
				if (typeof x1 == "number" && typeof w == "number" && typeof h == "number") {
					this.sel.draw(x1, y1, w, h);
					cov.style.visibility = "visible";
				}
			}
		},
		setCropWidth: function (w) {
			w = Math.round(Number(w));
			w = (w > 0) ? w : 1;
			var x1 = this.sel.getLeft();
			var y1 = this.sel.getTop();
			var h = this.sel.getHeight();
			var cov = document.getElementById(this.coverID);


			if ((w - w !== 0) || (w < 1) || (w > this.imgW)) {
				if (typeof this.sel.getWidth() !== "number") {
					document.forms.we_form.CropWidth.value = "";
				} else {
					document.forms.we_form.CropWidth.value = this.sel.getWidth();
				}
			} else {
				this.sel.setWidth(w);
				document.forms.we_form.CropWidth.value = w;
				if (typeof x1 === "number" && x1 + w > this.imgW) {
					x1 = this.imgW - w;
				}
				if (typeof x1 === "number" && typeof y1 === "number" && typeof h === "number") {
					this.sel.draw(x1, y1, w, h);
					cov.style.visibility = "visible";
				}
			}
		},
		setCropHeight: function (h) {
			h = Math.round(Number(h));
			h = (h > 0) ? h : 1;
			var x1 = this.sel.getLeft();
			var y1 = this.sel.getTop();
			var w = this.sel.getWidth();
			var cov = document.getElementById(this.coverID);

			if ((h - h !== 0) || (h < 1) || (h > this.imgH)) {
				if (typeof this.sel.getHeight() !== "number") {
					document.forms.we_form.CropHeight.value = "";
				} else {
					document.forms.we_form.CropHeight.value = this.sel.getHeight();
				}
			} else {
				this.sel.setHeight(h);
				document.forms.we_form.CropHeight.value = h;
				if (typeof y1 === "number" && y1 + h > this.imgH)
					y1 = this.imgH - h;
				if (typeof x1 === "number" && typeof y1 === "number" && typeof w === "number") {
					this.sel.draw(x1, y1, w, h);
					cov.style.visibility = "visible";
				}
			}
		},
		getElements: function () {
			var CropTool = ImageEditTools.Crop;
			var xy = document.getElementById("weImagePanel");

			this.sel = {
				left: undefined,
				top: undefined,
				right: undefined,
				bottom: undefined,
				width: undefined,
				height: undefined,
				idLeft: "divSelectionLeft",
				idTop: "divSelectionTop",
				idRight: "divSelectionRight",
				idBottom: "divSelectionBottom",
				visible: false
			};

			var x1 = document.createElement("div");
			x1.id = this.sel.idLeft;
			x1.className = "clVerLine";
			xy.appendChild(x1);

			var y1 = document.createElement("div");
			y1.id = this.sel.idTop;
			y1.className = "clHorLine";
			xy.appendChild(y1);

			var x2 = document.createElement("div");
			x2.id = this.sel.idRight;
			x2.className = "clVerLine";
			xy.appendChild(x2);

			var y2 = document.createElement("div");
			y2.id = this.sel.idBottom;
			y2.className = "clHorLine";
			xy.appendChild(y2);

			var img = document.createElement("img");
			img.id = this.cropID;
			img.src = this.imgSrc;
			xy.appendChild(img);

			var cov = document.createElement("div");
			cov.id = this.coverID;
			cov.style.width = this.imgW + "px";
			cov.style.height = this.imgH + "px";
			xy.appendChild(cov);

			this.sel.setVisibility = function (v) {
				document.getElementById(this.idLeft).style.visibility = v;
				document.getElementById(this.idTop).style.visibility = v;
				document.getElementById(this.idRight).style.visibility = v;
				document.getElementById(this.idBottom).style.visibility = v;
				document.getElementById(CropTool.cropID).style.visibility = v;
				this.visibility = v;
			};

			this.sel.setCursor = function (x1, y1, x2, y2, cr, co) {
				if (arguments.length === 1) {
					y1 = x2 = y2 = cr = co = x1;
				}
				document.getElementById(this.idLeft).style.cursor = x1;
				document.getElementById(this.idTop).style.cursor = y1;
				document.getElementById(this.idRight).style.cursor = x2;
				document.getElementById(this.idBottom).style.cursor = y2;
				document.getElementById(CropTool.cropID).style.cursor = cr;
				document.getElementById(CropTool.coverID).style.cursor = co;
			};

			this.sel.setLeft = function (x1) {
				this.left = this.right = x1;
			};
			this.sel.setTop = function (y1) {
				this.top = y1;
			};
			this.sel.setRight = function (x2) {
				this.right = x2;
			};
			this.sel.setBottom = function (y2) {
				this.bottom = y2;
			};
			this.sel.setWidth = function (w) {
				this.width = w;
			};
			this.sel.setHeight = function (h) {
				this.height = h;
			};

			this.sel.getVisibility = function () {
				return this.visibility;
			};
			this.sel.getLeft = function () {
				return this.left;
			};
			this.sel.getTop = function () {
				return this.top;
			};
			this.sel.getRight = function () {
				return this.right;
			};
			this.sel.getBottom = function () {
				return this.bottom;
			};
			this.sel.getWidth = function () {
				return this.width;
			};
			this.sel.getHeight = function () {
				return this.height;
			};

			this.sel.draw = function (x1, y1, w, h) {
				this.left = x1;
				this.top = y1;
				this.width = w;
				this.height = h;
				this.right = x1 + w;
				this.bottom = y1 + h;

				var elCrop = document.getElementById(CropTool.cropID);
				var elLeft = document.getElementById(this.idLeft);
				var elTop = document.getElementById(this.idTop);
				var elRight = document.getElementById(this.idRight);
				var elBottom = document.getElementById(this.idBottom);

				elLeft.style.left = x1 + "px";
				elTop.style.left = x1 + "px";
				elRight.style.left = x1 + w - 1 + "px";
				elBottom.style.left = x1 + "px";

				elLeft.style.top = y1 + "px";
				elTop.style.top = y1 + "px";
				elRight.style.top = y1 + "px";
				elBottom.style.top = y1 + h - 1 + "px";

				elLeft.style.height = h + "px";
				elTop.style.width = w + "px";
				elRight.style.height = h + "px";
				elBottom.style.width = w + "px";

				var clip = "rect(" + y1 + "px, " + (x1 + w) + "px, " + (y1 + h) + "px, " + x1 + "px)";
				elCrop.style.clip = clip;

				this.setVisibility("visible");
				elCrop = null;
				elLeft = null;
				elTop = null;
				elRight = null;
				elBottom = null;
			};

			this.sel.moveTo = function (x1, y1) {
				var w = this.getWidth();
				var h = this.getHeight();
				var elCrop = document.getElementById(CropTool.cropID);
				var elLeft = document.getElementById(this.idLeft);
				var elTop = document.getElementById(this.idTop);
				var elRight = document.getElementById(this.idRight);
				var elBottom = document.getElementById(this.idBottom);

				this.left = x1;
				this.right = x1 + w;
				this.top = y1;

				elLeft.style.left = x1 + "px";
				elTop.style.left = x1 + "px";
				elRight.style.left = x1 + w - 1 + "px";
				elBottom.style.left = x1 + "px";

				elLeft.style.top = y1 + "px";
				elTop.style.top = y1 + "px";
				elRight.style.top = y1 + "px";
				elBottom.style.top = y1 + h - 1 + "px";

				var clip = "rect(" + y1 + "px, " + (x1 + w) + "px, " + (y1 + h) + "px, " + x1 + "px)";
				elCrop.style.clip = clip;

				elCrop = null;
				elLeft = null;
				elTop = null;
				elRight = null;
				elBottom = null;
			};

			this.sel.reset = function () {
				this.left = undefined;
				this.top = undefined;
				this.right = undefined;
				this.bottom = undefined;
				this.width = undefined;
				this.height = undefined;
				this.setVisibility("hidden");
				this.setCursor("crosshair");
				this.track = false;
				this.drag = false;
			};
			xy = null;
		},
		zoom: function (px) {
			var w = this.sel.getWidth();
			var h = this.sel.getHeight();
			if (w !== undefined || h !== undefined) {
				this.setCropWidth(w + px);
				this.setCropHeight(h + px);
			}
		},
		zoomH: function (px) {
			var w = this.sel.getWidth();
			if (w !== undefined)
				this.setCropWidth(w + px);
		},
		zoomV: function (px) {
			var h = this.sel.getHeight();
			if (h !== undefined)
				this.setCropHeight(h + px);
		},
		switch_button_state: function (element, state) {
			if (state === "enabled") {
				WE().layout.button.enable(document, element);
				return true;
			}
			if (state === "disabled") {
				WE().layout.button.disable(document, element);
				return false;
			}
			return false;
		},
		catchKeystroke: function (e, inp) {
			var keynum;
			var keychar;
			var numcheck;
			if (window.event) {
				keynum = e.keyCode;
			} else if (e.which) {
				keynum = e.which;
			}
			switch (keynum) {
				case 13:
					CropTool["set" + inp.name](inp.value);
					return true;
				case 8:
					return true;
				default:
					keychar = String.fromCharCode(keynum);
					numcheck = /\d/;
					return numcheck.test(keychar);
			}
		},
		resetCropTool: function () {
			this.scale = null;
			this.initScale = null;
			this.setCropSize("", "", "", "");
			this.sel.reset();
			document.getElementById(this.coverID).style.visibility = "hidden";
			this.altKey = false;
			this.ctrlKey = false;
			this.shiftKey = false;
		},
		drop: function () {
			var elIdDiv = document.getElementById(this.imgDiv);
			var elIdBorder = document.getElementById(this.imgBorder);
			var elCropCtrl = document.getElementById(this.cropCtrl);
			var elIdImage = document.getElementById(this.imgID);
			var elIds = document.getElementById(this.imgPanel);
			var elIdCrop = document.getElementById(this.cropID);
			var elIdLeft = document.getElementById(this.sel.idLeft);
			var elIdTop = document.getElementById(this.sel.idTop);
			var elIdRight = document.getElementById(this.sel.idRight);
			var elIdBottom = document.getElementById(this.sel.idBottom);

			elIdImage.width = this.defW;
			elIdImage.height = this.defH;
			elIdDiv.style.border = "0px solid #CECECE";
			elIdBorder.style.border = "0px solid #808080";
			elCropCtrl.style.display = "none";
			elIdImage.style.cursor = "default";
			elIds.style.cursor = "default";

			this.clLs(elIds, "onmousedown");
			this.clLs(elIds, "onmouseup");
			this.clLs(window, "onmouseup");
			this.clLs(window, "onmousemove");
			this.clLs(elIdCrop, "onmousedown");
			this.clLs(elIds, "onclick");
			this.clLs(window, "onkeydown");
			this.clLs(window, "onkeyup");
			this.clLs(elIdLeft, "onmousedown");
			this.clLs(elIdTop, "onmousedown");
			this.clLs(elIdRight, "onmousedown");
			this.clLs(elIdBottom, "onmousedown");

			this.resetCropTool();
			elIdDiv = null;
			elIdBorder = null;
			elCropCtrl = null;
			elIdImage = null;
			elIds = null;
			elIdCrop = null;
			elIdLeft = null;
			elIdTop = null;
			elIdRight = null;
			elIdBottom = null;
		}
	},
	Focus: {
		state: {
			isMoveFocus: false
		},
		elems: {
			focusPoint: null,
			image: null,
			x_focus: null,
			y_focus: null
		},
		vals: {
			referenceX: 0,
			referenceY: 0,
			origLeft: 0,
			origTop: 0
		},
		boundFNs: {
			setFocus: null,
			moveFocus: null,
			startMove: null,
			stopMove: null,
			leaveBody: null
		},
		start: function () {
			ImageEditTools.deactivateAll();
			ImageEditTools.activeTool = 'focus';

			// references to some gui elements
			this.elems.focusPoint = document.getElementById('imgfocus_point');
			this.elems.image = document.getElementById('weImage');
			this.elems.x_focus = document.getElementById('x_focus');
			this.elems.y_focus = document.getElementById('y_focus');
			this.elems.info = document.getElementById('focus_info');

			// set tool visible
			this.elems.focusPoint.style.display = 'block';
			this.elems.focusPoint.style.cursor = 'move';
			this.elems.image.style.cursor = 'crosshair';
			this.elems.x_focus.parentNode.style.display = 'block';
			this.elems.info.style.display = 'block';

			// add listeners (using late binding to set scope, and hold reference to bound fns!)
			this.elems.image.addEventListener('click', (this.boundFNs.setFocus = this.setFocusPositionByMouse.bind(this)), false);
			this.elems.focusPoint.addEventListener('mousedown', (this.boundFNs.startMove = this.startMoveFocusPosition.bind(this)), false);
			document.body.addEventListener('mouseup', (this.boundFNs.stopMove = this.stopMoveFocusPosition.bind(this)), false);
			document.body.addEventListener('mouseleave', (this.boundFNs.leaveBody = this.mouseLeaveBody.bind(this)), false);

			this.elems.focusPoint.addEventListener('dragstart', function (e) {
				e.preventDefault();
			}, false);

			var hot = _EditorFrame.getEditorIsHot();
			this.setFocusPositionByValue();
			_EditorFrame.setEditorIsHot(hot);
		},
		drop: function () { // when leaving edit focus
			this.elems.focusPoint.style.display = 'none';
			this.elems.image.style.cursor = 'default';
			document.body.style.cursor = 'default';
			this.elems.image.removeEventListener('click', this.boundFNs.setFocus, false);
			this.elems.focusPoint.removeEventListener('mousedown', this.boundFNs.startMove, false);
			document.body.removeEventListener('mouseup', this.boundFNs.stopMove, false);
			document.body.removeEventListener('mousemove', this.boundFNs.moveFocus, false);
			this.elems.x_focus.parentNode.style.display = 'none';
			this.elems.info.style.display = 'none';
		},
		mouseLeaveBody: function (e) {
			if (this.state.isMoveFocus) {
				this.moveFocusPosition(e);
			}
			this.stopMoveFocusPosition();
		},
		startMoveFocusPosition: function (e) {
			this.state.isMoveFocus = true;
			this.elems.image.style.cursor = 'move';
			document.body.style.cursor = 'move';
			this.vals.referenceX = e.clientX;
			this.vals.referenceY = e.clientY;
			this.vals.origLeft = parseInt(this.elems.focusPoint.style.left);
			this.vals.origTop = parseInt(this.elems.focusPoint.style.top);
			this.boundFNs.moveFocus = this.moveFocusPosition.bind(this);
			document.body.addEventListener('mousemove', (this.boundFNs.moveFocus = this.moveFocusPosition.bind(this)), false);
		},
		stopMoveFocusPosition: function (e) {
			this.state.isMoveFocus = false;
			this.elems.image.style.cursor = 'crosshair';
			document.body.style.cursor = 'default';
			document.body.removeEventListener('mousemove', this.boundFNs.moveFocus, false);
		},
		moveFocusPosition: function (e) {
			var topVal = this.vals.origTop + (e.clientY - this.vals.referenceY),
							leftVal = this.vals.origLeft + (e.clientX - this.vals.referenceX);

			//window.getSelection().removeAllRanges();
			this.setFocusPositionByMouse(null, topVal, leftVal);
		},
		setFocusPositionByMouse: function (e, topVal, leftVal) {
			topVal = topVal !== undefined ? topVal : (e ? e.offsetY : false);
			leftVal = leftVal !== undefined ? leftVal : (e ? e.offsetX : false);

			topVal = topVal <= 0 ? 0 : (topVal >= this.elems.image.height ? this.elems.image.height : topVal);
			leftVal = leftVal <= 0 ? 0 : (leftVal >= this.elems.image.width ? this.elems.image.width : leftVal);

			this.elems.x_focus.value = ((leftVal - this.elems.image.width / 2) / (this.elems.image.width / 2)).toFixed(2);
			this.elems.y_focus.value = ((topVal - this.elems.image.height / 2) / (this.elems.image.height / 2)).toFixed(2);
			this.setFocusPositionByValue();
		},
		setFocusPositionByValue: function () {
			var x = document.getElementById('x_focus').value,
							y = document.getElementById('y_focus').value;

			if (Math.abs(this.elems.x_focus) > 1) {
				this.elems.x_focus = 0;
			}
			if (Math.abs(y) > 1) {
				y = 0;
			}
			document.getElementById('focus').value = '[' + x + ',' + y + ']';

			this.elems.focusPoint.style.left = ((this.elems.image.width / 2) + (x * (this.elems.image.width / 2))).toFixed(0) + 'px';
			this.elems.focusPoint.style.top = ((this.elems.image.height / 2) + (y * (this.elems.image.height / 2))).toFixed(0) + 'px';
			_EditorFrame.setEditorIsHot(true);
		}
	},
	Rotate: {
		win: null,
		start: function (url, gdType) {
			ImageEditTools.deactivateAll();
			ImageEditTools.activeTool = 'rotate';
			this.win = new (WE().util.jsWindow)(window, url, 'we_rotate', -1, -1, 300, (gdType === 'jpg' ? 230 : 170), true, false, true);
		},
		drop: function () {
			this.win.close();
		}
	},
	Resize: {
		win: null,
		start: function (url, gdType) {
			ImageEditTools.deactivateAll();
			ImageEditTools.activeTool = 'resize';
			this.win = new (WE().util.jsWindow)(window, url, 'we_image_resize', -1, -1, 260, (gdType === 'jpg' ? 250 : 190), true, false, true);
		},
		drop: function () {
			this.win.close();
		}
	},
	ConvertJPEG: {
		win: null,
		start: function (url) {
			ImageEditTools.deactivateAll();
			ImageEditTools.activeTool = 'convertJPEG';
			this.win = new (WE().util.jsWindow)(window, url, 'we_convert_jpg', -1, -1, 260, 160, true, false, true);
		},
		drop: function () {
			this.win.close();
		}
	}
};

/*
 ImageEditTools.convertPNG = {

 };

 ImageEditTools.convertGIF = {

 };
 */