/* global tinyMCEPopup, tinymce,top, WE, tinyMCE */

/**
 * webEdition CMS
 *
 * $Rev: 13408 $
 * $Author: lukasimhof $
 * $Date: 2017-02-21 23:55:26 +0100 (Di, 21 Feb 2017) $
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

WE().layout.we_tinyMCE.getTinyWrapper = function(win, fieldname){
	return new TinyWrapper(win, fieldname);
};

function TinyWrapper(win, fieldname) {
	var win = win;
	var fn = fieldname;

	var tinyEditors = win.tinyEditors;
	var tinyEditorsInPopup = win.tinyEditorsInPopup;
	var tinymce = win.tinymce;
	var doc = win.document;
	var isInlineedit = typeof tinyEditors[fn] === 'object';
	var id = isInlineedit ? tinyEditors[fn].id : (tinyEditors[fn] === undefined ? undefined : tinyEditors[fn]);

	this.getFieldName = function () {
		return fn;
	};

	this.getId = function () {
		return id;
	};

	this.getIsInlineedit = function () {
		return isInlineedit;
	};

	this.getEditorInPopup = function () {
		if (tinyEditorsInPopup[fn] !== undefined) {
			try {
				tinyEditorsInPopup[fn].getContent();
				return tinyEditorsInPopup[fn];
			} catch (err) {
				delete tinyEditorsInPopup[fn];
				return undefined;
			}
		} else {
			return undefined;
		}
	};

	this.getEditor = function (tryPopup) {
		tryPopup = !tryPopup ? false : tryPopup;
		if (tryPopup) {
			return this.getEditorInPopup();
		}

		return tinyEditors[fn] === undefined ? undefined : (typeof tinyEditors[fn] === 'object' ? tinyEditors[fn] : undefined);
	};

	this.getTextarea = function () {
		return tinyEditors[fn] === undefined ? undefined : (typeof tinyEditors[fn] === 'object' ? 'undefined' : doc.getElementById(tinyEditors[fn]));
	};
	this.getDiv = function () {
		return tinyEditors[fn] === undefined ? undefined : (typeof tinyEditors[fn] === 'object' ? undefined : doc.getElementById('div_wysiwyg_' + tinyEditors[fn]));
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
		forcePopup = forcePopup === undefined ? false : forcePopup;
		if (!isInlineedit) {
			if (forcePopup) {
				try {
					return tinyEditorsInPopup[fn].getContent();
				} catch (err) {
				}
			}
			try {
				return doc.getElementById(tinyEditors[fn]).value;
			} catch (err) {
			}
		} else {
			try {
				return tinyEditors[fn].getContent();
			} catch (err) {
			}
		}
	};

	this.setContent = function (cnt) {
		if (!isInlineedit) {
			try {
				doc.getElementById(tinyEditors[fn]).value = cnt;
				doc.getElementById('div_wysiwyg_' + tinyEditors[fn]).innerHTML = cnt;
			} catch (err) {
			}
			try {
				tinyEditorsInPopup[fn].setContent(cnt);
			} catch (err) {
			}
		} else {
			try {
				tinyEditors[fn].setContent(cnt);
			} catch (err) {
			}
		}
	};

	this.on = function (sEvtObj, func) {
		var editor = this.getEditor(true);
		try {
			editor['on' + sEvtObj].add(func);
		} catch (e) {
			WE().t_e('unable to add event');
		}
	};

	this.getParam = function (param) {
		if (this.getEditor(true) !== undefined) {
			if (this.getEditor().settings[param] === undefined) {
				WE().t_e('function getParam(): The parameter you tried to derive is not defined: ' + param);
				return undefined;
			}
			return this.getEditor().settings[param];
		}
		WE().t_e('Editor not available');
		return undefined;
	};
}
