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
	var fieldname = fieldname;

	var tinyEditors = win.tinyEditors;
	var tinyEditorsInlineFalse = win.tinyEditorsInlineFalse;
	var tinymce = win.tinymce;
	var doc = win.document;
	var isInlineTrue = typeof tinyEditors[fieldname] === 'object';
	var id = isInlineTrue ? tinyEditors[fieldname].id : (tinyEditors[fieldname] === undefined ? undefined : tinyEditors[fieldname]);

	this.getFieldName = function () {
		return fieldname;
	};

	this.getId = function () {
		return id;
	};

	this.getIsInlineedit = function () {
		return isInlineTrue;
	};

	this.isInlineTrue = function () {
		return isInlineTrue;
	};
	
	this.isEditorInitialized = function () {
		if (typeof tinyEditors[fieldname] === 'object') {
			return true;
		}

		if (tinyEditorsInlineFalse[fieldname] !== undefined) {
			try {
				// if we can getContent editor is alive
				tinyEditorsInlineFalse[fieldname].getContent();
				return true;
			} catch (err) {
				// if it failed editor inlineFalse is not opened
				delete tinyEditorsInlineFalse[fieldname];
				return false;
			}
		}

		return false;
	};

	this.getEditorInPopup = function () {
		if(isInlineTrue) {
			return undefined;
		}

		if(this.isEditorInitialized()) {
			return tinyEditorsInlineFalse[fieldname];
		}
		
		return undefined;
	};

	this.getEditor = function (tryPopup) {
		tryPopup = tryPopup === false ? false : true;
		if (tryPopup) {
			return this.getEditorInPopup();
		}

		return tinyEditors[fieldname] === undefined ? undefined : (typeof tinyEditors[fieldname] === 'object' ? tinyEditors[fieldname] : undefined);
	};

	// why do we return textarea only if inlineFalse
	this.getTextarea = function () {
		return tinyEditors[fieldname] === undefined ? undefined : (typeof tinyEditors[fieldname] === 'object' ? undefined : doc.getElementById(tinyEditors[fieldname]));
	};

	this.getPreviewDiv = function () {
		return this.getDív();
	};

	this.getDiv = function () {
		return tinyEditors[fieldname] === undefined ? undefined : (typeof tinyEditors[fieldname] === 'object' ? undefined : doc.getElementById('div_wysiwyg_' + tinyEditors[fieldname]));
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

	this.getContent = function (forcePopup) { // forcePopup means we return unsynchronized content from popup if opened
		forcePopup = forcePopup === undefined ? false : forcePopup;
		if (!isInlineTrue) {
			if (forcePopup) {
				try {
					return tinyEditorsInlineFalse[fieldname].getContent();
				} catch (err) {
				}
			}
			// if not forcePopup or if it failed, we return content from previewDiv
			try {
				return doc.getElementById(tinyEditors[fieldname]).value;
			} catch (err) {
			}
		} else {
			try {
				return tinyEditors[fieldname].getContent();
			} catch (err) {
			}
		}
	};

	this.setContent = function (cnt) {
		if (!isInlineTrue) { // we try to set content both in previewDiv/textarea and editor if opened
			try {
				doc.getElementById(tinyEditors[fieldname]).value = cnt;
				doc.getElementById('div_wysiwyg_' + tinyEditors[fieldname]).innerHTML = cnt;
			} catch (err) {
			}
			try {
				tinyEditorsInlineFalse[fieldname].setContent(cnt);
			} catch (err) {
			}
		} else {
			try {
				tinyEditors[fieldname].setContent(cnt);
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
