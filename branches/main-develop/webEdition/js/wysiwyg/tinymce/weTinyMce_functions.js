/* global tinyMCEPopup, tinymce,top, WE, tinyMCE */

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
'use strict';


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

var tinyPluginManager = function (n, u, cb, s) {
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
};
