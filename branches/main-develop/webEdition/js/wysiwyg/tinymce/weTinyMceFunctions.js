/* global top, WE, tinyMCE */

/**
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

var tinyEditors = {};
var tinyEditorsInPopup = {};

/* Re-/Initialize TinyMCE:
 * param (object) confObject: initialize Tiny instance using confObject
 *
 * If there is allready an instance applied to the textarea defined in confObject
 * this editor will be removed and re-initialized
 */
function tinyMceInitialize(confObject) {
	if (typeof tinyMCE === 'object') {
		if (typeof confObject === 'object') {
			if (tinyMCE.get(confObject.elements)) {
				tinyMCE.execCommand('mceRemoveControl', false, confObject.elements);
			}
			tinyMCE.init(confObject);
		}
	}
}

//FIXME check if we can return/use undefined instead of the string "undefined". Maybe we will get some errors, which would otherwise silently ignored -> frameid=getId()+"xx"; can result in "undefinedxx" - not in an error.

function TinyWrapper(fieldname) {
	if (!(this instanceof TinyWrapper)) {
		return new TinyWrapper(fieldname);
	}

	var _fn = fieldname;
	var _isInlineedit = typeof tinyEditors[_fn] === "object";
	var _id = _isInlineedit ? tinyEditors[_fn].id : (tinyEditors[_fn] === undefined ? undefined : tinyEditors[_fn]);

	this.getFieldName = function () {
		return _fn;
	};
	this.getId = function () {
		return _id;
	};
	this.getIsInlineedit = function () {
		return _isInlineedit;
	};

	this.getEditorInPopup = function () {
		if (tinyEditorsInPopup[_fn] !== undefined) {
			try {
				tinyEditorsInPopup[_fn].getContent();
				return tinyEditorsInPopup[_fn];
			} catch (err) {
				delete tinyEditorsInPopup[_fn];
				return undefined;
			}
		} else {
			return undefined;
		}
	};

	this.getEditor = function (tryPopup) {
		var _tryPopup = tryPopup === undefined ? false : tryPopup;
		if (tryPopup) {
			return this.getEditorInPopup();
		}

		return tinyEditors[_fn] === undefined ? undefined : (typeof tinyEditors[_fn] === "object" ? tinyEditors[_fn] : undefined);
	};


	this.getTextarea = function () {
		return tinyEditors[_fn] === undefined ? undefined : (typeof tinyEditors[_fn] === "object" ? "undefined" : document.getElementById(tinyEditors[_fn]));
	};
	this.getDiv = function () {
		return tinyEditors[_fn] === undefined ? undefined : (typeof tinyEditors[_fn] === "object" ? undefined : document.getElementById("div_wysiwyg_" + tinyEditors[_fn]));
	};

	this.getIFrame = function () {
		var frame_id = this.getId() + '_ifr';

		if (tinymce !== undefined && tinymce.DOM) {
			try {
				return tinymce.DOM.get(frame_id) !== null ? tinymce.DOM.get(frame_id) : undefined;
			} catch (e) {
				return undefined;
			}
		} else {
			return undefined;
		}
	};

	this.getContent = function (forcePopup) {
		var _forcePopup = forcePopup === undefined ? false : forcePopup;
		if (!_isInlineedit) {
			if (_forcePopup) {
				try {
					return tinyEditorsInPopup[_fn].getContent();
				} catch (err) {
				}
			}
			try {
				return document.getElementById(tinyEditors[_fn]).value;
			} catch (err) {
			}
		} else {
			try {
				return tinyEditors[_fn].getContent();
			} catch (err) {
			}
		}
	};

	this.setContent = function (cnt) {
		if (!_isInlineedit) {
			try {
				document.getElementById(tinyEditors[_fn]).value = cnt;
				document.getElementById("div_wysiwyg_" + tinyEditors[_fn]).innerHTML = cnt;
			} catch (err) {
			}
			try {
				tinyEditorsInPopup[_fn].setContent(cnt);
			} catch (err) {
			}
		} else {
			try {
				tinyEditors[_fn].setContent(cnt);
			} catch (err) {
			}
		}
	};

	this.on = function (sEvtObj, func) {
		var editor = this.getEditor(true);
		try {
			editor["on" + sEvtObj].add(func);
		} catch (e) {
			console.log("unable to add event");
		}
	};

	this.getParam = function (param) {
		if (this.getEditor(true) !== undefined) {
			if (this.getEditor().settings[param] === undefined) {
				console.log("function getParam(): The parameter you tried to derive is not defined: " + param);
				return undefined;
			}
			return this.getEditor().settings[param];
		}
		console.log("Editor not available");
		return undefined;
	};
}

function tinyMCECallRegisterDialog(win, action) {
	if (top.isRegisterDialogHere !== undefined) {
		try {
			top.weRegisterTinyMcePopup(win, action);
		} catch (err) {
		}
	} else {
		if (top.opener.isRegisterDialogHere !== undefined) {
			try {
				top.opener.weRegisterTinyMcePopup(win, action);
			} catch (err) {
			}
		} else {
			try {
				top.opener.tinyMCECallRegisterDialog(win, action);
			} catch (err) {
			}
		}
	}
}

function tinyEdOnNodeChange(ed, cm, n) {
	var pc, tmp,
					td = ed.dom.getParent(n, "td");

	if (typeof td === "object" && td && td.getElementsByTagName("p").length === 1) {
		pc = td.getElementsByTagName("p")[0].cloneNode(true);
		tmp = document.createElement("div");
		tmp.appendChild(pc);

		if (tmp.innerHTML === td.innerHTML) {
			td.innerHTML = "";
			ed.selection.setContent(pc.innerHTML);
		}
	}
}

function tinyInit_instance_callback(ed) {
	ed.serializer.addNodeFilter("a", function (nodes) {
		tinymce.each(nodes, function (node) {
			if (!node.attr("href") && !node.attr("id")) {
				node.attr("id", node.attr("name"));
			}
		});
	});
}

function tinyOnCopyCut(ed, isCut){
	var selection = ed.getWin().getSelection();
	top.bm = tinyMCE.activeEditor.selection.getBookmark();
	var tmpDiv = ed.getDoc().createElement("div");

	tmpDiv.style.position = "absolute";
	tmpDiv.style.left = "-99999px"; // we must display it to select!
	tmpDiv.setAttribute("class", "FROM_INSIDE_TINYMCE");
	ed.getBody().appendChild(tmpDiv);

	tmpDiv.appendChild(selection.getRangeAt(0).cloneContents());
	tmpDiv.innerHTML += "##FROM_INSIDE_TINYMCE##";
	selection.selectAllChildren(tmpDiv);

	ed.getWin().setTimeout(function () {
		ed.getBody().removeChild(tmpDiv);
		tinyMCE.activeEditor.selection.moveToBookmark(top.bm);
		if(isCut){
			tinyMCE.activeEditor.selection.setContent("");
		}
	}, 100);
}

function tinyEdOnPaste(ed) {
	if (!ed.weEditorFrameIsHot && ed.editorLevel == "inline" && ed.isDirty()) {
		try {
			ed.weEditorFrame.setEditorIsHot(true);
		} catch (e) {
		}
		ed.weEditorFrameIsHot = true;
	}
}

function tinyEdOnSaveContent(ed, o) {
	ed.weEditorFrameIsHot = false;
	// if is popup and we click on ok
	if (ed.editorLevel == "popup" && ed.isDirty()) {
		try {
			ed.weEditorFrame.setEditorIsHot(true);
		} catch (e) {
		}
	}

	/*
	 // and we transform image sources to we format before writing it to session!
	 var div = document.createElement("div"),
	 imgs;

	 div.innerHTML = o.content;
	 if(imgs = div.getElementsByTagName("IMG")){
	 var matches;
	 for(var i = 0; i < imgs.length; i++){
	 if(matches = imgs[i].src.match(/[^?]+\?id=(\d+)/)){
	 imgs[i].src = WE().consts.linkPrefix.TYPE_INT_PREFIX+ matches[1];
	 }
	 if(matches = imgs[i].src.match(/[^?]+\?thumb=(\d+,\d+)/)){
	 imgs[i].src = WE().consts.linkPrefix.TYPE_THUMB_PREFIX + matches[1];
	 };
	 }
	 o.content = div.innerHTML;
	 div = imgs = matches = null;
	 }
	 */
}

function tinyPluginManager(n, u, cb, s) {
	var t = this, url = u;
	function loadDependencies() {
		var dependencies = t.dependencies(n);
		tinymce.each(dependencies, function (dep) {
			var newUrl = t.createUrl(u, dep);
			t.load(newUrl.resource, newUrl, undefined, undefined);
		});
		if (cb) {
			if (s) {
				cb.call(s);
			} else {
				cb.call(tinymce.ScriptLoader);
			}
		}
	}
	if (t.urls[n]) {
		return;
	}
	if (typeof u === "object") {
		url = u.resource.indexOf("we") === 0 ? WE().consts.dirs.WE_JS_TINYMCE_DIR + "plugins/" + u.resource + u.suffix : u.prefix + u.resource + u.suffix;
	}
	if (url.indexOf("/") !== 0 && url.indexOf("://") === -1) {
		url = tinymce.baseURL + "/" + url;
	}
	t.urls[n] = url.substring(0, url.lastIndexOf("/"));
	if (t.lookup[n]) {
		loadDependencies();
	} else {
		tinymce.ScriptLoader.add(url, loadDependencies, s);
	}
}

function tinyOnPostProcess(ed, o) {
	var c = document.createElement("div");
	c.innerHTML = o.content;
	var first = c.firstChild;

	if (first) {
		if (first.innerHTML == "&nbsp;" && first == c.lastChild) {
			c.innerHTML = "";
		} else if (ed.settings.weRemoveFirstParagraph === "1" && first.nodeName == "P") {
			var useDiv = false,
							div = document.createElement("div"),
							attribs = ["style", "class", "dir"];
			div.innerHTML = first.innerHTML;

			for (var i = 0; i < attribs.length; i++) {
				if (first.hasAttribute(attribs[i])) {
					div.setAttribute(attribs[i], first.getAttribute(attribs[i]));
					useDiv = true;
				}
			}
			if (useDiv) {
				c.replaceChild(div, first);
			} else {
				c.removeChild(first);
				c.innerHTML = first.innerHTML + c.innerHTML;
			}
		}
	}

	// remove border="0" and border="" from table tags
	var tables;
	if (tables = c.getElementsByTagName("TABLE")) {
		for (var i = 0; i < tables.length; i++) {
			if (tables[i].getAttribute("border") === "0" || tables[i].getAttribute("border") === "") {
				tables[i].removeAttribute("border");
			}
		}
	}

	// write content back
	o.content = c.innerHTML;
}

function tinyEdonDblClick(ed, e) {
	var openDialogsOnDblClick = true;

	if (openDialogsOnDblClick) {
		if (ed.selection.getNode().nodeName === "IMG" && ed.dom.getAttrib(ed.selection.getNode(), "src", "")) {
			tinyMCE.execCommand("mceWeimage");
		}
		if (ed.selection.getNode().nodeName === "A" && ed.dom.getAttrib(ed.selection.getNode(), "href", "")) {
			tinyMCE.execCommand("mceWelink");
		}
	} else {
		var match,
						frameControler = WE().layout.weEditorFrameController;

		if (!frameController) {
			return;
		}
		if (ed.selection.getNode().nodeName === "IMG" && (src = ed.dom.getAttrib(ed.selection.getNode(), "src", ""))) {
			var regex = new RegExp('[^" >]*\?id=(\d+)[^" >]*');
			if ((match = src.match(regex))) {
				if (match[1] && parseInt(match[1]) !== 0) {
					frameControler.openDocument(WE().consts.tables.FILE_TABLE, match[1], "");
				}
			} else {
				new (WE().util.jsWindow)(window, src, "_blank", -1, -1, 2500, 2500, true, true, true);
			}
		}
		if (ed.selection.getNode().nodeName === "A" && (href = ed.dom.getAttrib(ed.selection.getNode(), "href", ""))) {
			var regex = new RegExp("(" + WE().consts.linkPrefix.TYPE_INT_PREFIX + "|" + WE().consts.linkPrefix.TYPE_OBJ_PREFIX + '|)(\d+)[^" >]*');
			if ((match = href.match(regex))) {
				if (match[1]) {
					switch (match[1]) {
						case WE().consts.linkPrefix.TYPE_INT_PREFIX:
							frameControler.openDocument(WE().consts.tables.FILE_TABLE, match[2], "");
							break;
						case WE().consts.linkPrefix.TYPE_OBJ_PREFIX:
							frameControler.openDocument(WE().consts.tables.OBJECT_FILES_TABLE, match[2], "");
							break;
					}
				}
			} else {
				new (WE().util.jsWindow)(window, href, "_blank", -1, -1, 2500, 2500, true, true, true);
			}
		}
	}
}

function tinyEdOnPostRender(ed, cm) {
	window.addEventListener("resize", function (e) {
		tinyMCE.weResizeEditor();
	});
	if (typeof tinyMCE.weResizeEditor === "function") {
		tinyMCE.weResizeEditor(true);
	}
}

function tinyWeResizeEditor(render, name) {
	var el = tinyMCE.DOM.get(name + "_toolbargroup");
	var h = el ? el.parentNode.offsetHeight : 0;
	if ((render || !el) && --tinyMCE.weResizeLoops && h < 24) {
		setTimeout(tinyWeResizeEditor, 10, true);
		return;
	}

	tinyMCE.DOM.setStyle(
					tinyMCE.DOM.get(name + "_ifr"),
					"height",
					(window.innerHeight - h - 60) + "px"
					);
}

function tinySetEditorLevel(ed) {
	/* set EditorFrame.setEditorIsHot(true) */

	// we look for editorLevel and weEditorFrameController just once at editor init
	ed.editorLevel = "";
	ed.weEditorFrame = null;
	// if editorLevel = "inline" we use a local copy of weEditorFrame.EditorIsHot
	ed.weEditorFrameIsHot = false;

	//FIXME: this doesn't work: we need to know wheter it is inline, popup or fullscreen and wheter we are on a document/object in multieditor
	//simply mark inline-false-popup and fullscreen with some js var and then check for multieditor on the respective level
	if (window._EditorFrame !== undefined) {
		ed.editorLevel = "inline";
		ed.weEditorFrame = window._EditorFrame;
	} else if (top.opener !== null && top.opener.top.WebEdition && top.opener.top.WebEdition.layout.weEditorFrameController !== undefined && top.isWeDialog === undefined) {
		ed.editorLevel = "popup";
		ed.weEditorFrame = top.opener.top.WebEdition.layout.weEditorFrameController;
	} else if (top.isWeDialog) {
		ed.editorLevel = "fullscreen";
		ed.weEditorFrame = null;
	} else {
		ed.editorLevel = "popup";
		ed.weEditorFrame = null;
	}

	try {
		ed.weEditorFrameIsHot = ed.editorLevel === "inline" ? ed.weEditorFrame.EditorIsHot : false;
	} catch (e) {
	}

	// listeners for editorLevel = "inline"
	//could be rather CPU-intensive. But weEditorFrameIsHot is nearly allways true, so we could try
	ed.onKeyDown.add(function (ed) {
		if (!ed.weEditorFrameIsHot && ed.editorLevel === "inline") {
			try {
				ed.weEditorFrame.setEditorIsHot(true);
			} catch (e) {
			}
			ed.weEditorFrameIsHot = true;
		}
	});
}