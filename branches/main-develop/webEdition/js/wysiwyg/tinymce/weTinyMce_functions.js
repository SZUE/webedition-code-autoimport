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
