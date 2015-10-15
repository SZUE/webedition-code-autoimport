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

function TinyWrapper(fieldname) {
	if (!(this instanceof TinyWrapper)) {
		return new TinyWrapper(fieldname);
	}

	var _fn = fieldname;
	var _isInlineedit = typeof tinyEditors[_fn] === "object";
	var _id = _isInlineedit ? tinyEditors[_fn].id : (typeof tinyEditors[_fn] === "undefined" ? "undefined" : tinyEditors[_fn]);

	this.getFieldName = function () {
		return _fn;
	};
	this.getId = function () {
		return _id;
	};
	this.getIsInlineedit = function () {
		return _isInlineedit;
	};

	this.getEditor = function (tryPopup) {
		var _tryPopup = typeof tryPopup === "undefined" ? false : tryPopup;
		if (tryPopup && this.getEditorInPopup() !== "undefined") {
			return this.getEditorInPopup();
		}

		return typeof tinyEditors[_fn] === "undefined" ? "undefined" : (typeof tinyEditors[_fn] === "object" ? tinyEditors[_fn] : "undefined");
	};

	this.getEditorInPopup = function () {
		if (typeof tinyEditorsInPopup[_fn] !== "undefined") {
			try {
				tinyEditorsInPopup[_fn].getContent();
				return tinyEditorsInPopup[_fn];
			}
			catch (err) {
				delete tinyEditorsInPopup[_fn];
				return "undefined";
			}
		} else {
			return "undefined";
		}
	};

	this.getTextarea = function () {
		return typeof tinyEditors[_fn] === "undefined" ? "undefined" : (typeof tinyEditors[_fn] === "object" ? "undefined" : document.getElementById(tinyEditors[_fn]));
	};
	this.getDiv = function () {
		return typeof tinyEditors[_fn] === "undefined" ? "undefined" : (typeof tinyEditors[_fn] === "object" ? "undefined" : document.getElementById("div_wysiwyg_" + tinyEditors[_fn]));
	};

	this.getIFrame = function () {
		var frame_id = this.getId() + '_ifr';

		if (typeof tinymce !== "undefined" && tinymce.DOM) {
			try {
				return tinymce.DOM.get(frame_id) !== null ? tinymce.DOM.get(frame_id) : "undefined";
			} catch (e) {
				return "undefined";
			}
		} else {
			return "undefined";
		}
	}

	this.getContent = function (forcePopup) {
		var _forcePopup = typeof forcePopup === "undefined" ? false : forcePopup;
		if (!_isInlineedit) {
			if (_forcePopup) {
				try {
					return tinyEditorsInPopup[_fn].getContent();
				} catch (err) {
					//console.log("No Editor \'" + _fn + "\' in Popup found!");
				}
			}
			try {
				return document.getElementById(tinyEditors[_fn]).value;
			} catch (err) {
				//console.log("No Editor \'" + _fn + "\' found!");
			}
		} else {
			try {
				return tinyEditors[_fn].getContent();
			} catch (err) {
				//console.log("No Editor \'" + _fn + "\' found!");
			}
		}
	};

	this.setContent = function (cnt) {
		if (!_isInlineedit) {
			try {
				document.getElementById(tinyEditors[_fn]).value = cnt;
				document.getElementById("div_wysiwyg_" + tinyEditors[_fn]).innerHTML = cnt;
			} catch (err) {
				//console.log("No Editor \'" + _fn + "\' found!");
			}
			try {
				tinyEditorsInPopup[_fn].setContent(cnt);
			} catch (err) {
				//console.log("No Editor \'" + _fn + "\' in Popup found!");
			}
		} else {
			try {
				tinyEditors[_fn].setContent(cnt);
			} catch (err) {
				//console.log("No Editor \'" + _fn + "\' found!");
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
		if (this.getEditor(true) !== "undefined") {
			if (typeof this.getEditor().settings[param] == "undefined") {
				console.log("function getParam(): The parameter you tried to derive is not defined: " + param);
				return "undefined";
			}
			return this.getEditor().settings[param];
		}
		console.log("Editor not available");
		return "undefined";
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
function weWysiwygSetHiddenTextSync() {
	weWysiwygSetHiddenText();
	setTimeout(weWysiwygSetHiddenTextSync, 500);
}

function weWysiwygSetHiddenText(arg) {
	//FIXME: where is weWysiwygIsIntialized set????
	try {
		if (weWysiwygIsIntialized) {
			for (var i = 0; i < we_wysiwygs.length; i++) {
				we_wysiwygs[i].setHiddenText(arg);
			}
		} else {
		}
	} catch (e) {
		// Nothing
	}
}