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

WE().layout.we_tinyMCE.getTinyWrapper = function(win, fieldname){
	return new TinyWrapper(win, fieldname);
};

function TinyWrapper(win, fieldname) {
	this.win = win;
	this.fieldname = fieldname;

	this.tinyEditors = this.win.tinyEditors;
	this.tinyEditorsInlineFalse = this.win.tinyEditorsInlineFalse;
	this. tinymce = this.win.tinymce;
	this.doc = this.win.document;
	this.isInlineTrue = typeof this.tinyEditors[this.fieldname] === 'object';
	this.id = this.isInlineTrue ? this.tinyEditors[this.fieldname].id : (this.tinyEditors[this.fieldname] === undefined ? undefined : this.tinyEditors[this.fieldname]);

	this.getFieldName = function () {
		return this.fieldname;
	};

	this.getId = function () {
		return this.id;
	};

	this.getIsInlineedit = function () {
		return this.isInlineTrue;
	};

	this.isEditorInitialized = function () {
		if (typeof this.tinyEditors[this.fieldname] === 'object') {
			return true;
		}

		if (this.tinyEditorsInlineFalse[this.fieldname] !== undefined) {
			try {
				// if we can getContent editor is alive
				this.tinyEditorsInlineFalse[this.fieldname].getContent();
				return true;
			} catch (err) {
				// if it failed editor inlineFalse is not opened
				delete this.tinyEditorsInlineFalse[this.fieldname];
				return false;
			}
		}

		return false;
	};

	this.getEditorInPopup = function () {
		if(this.isInlineTrue) {
			return undefined;
		}

		if(this.isEditorInitialized()) {
			return this.tinyEditorsInlineFalse[this.fieldname];
		}

		return undefined;
	};

	this.getEditor = function (tryPopup) {
		tryPopup = tryPopup === false ? false : true;
		if (tryPopup) {
			return this.getEditorInPopup();
		}

		return this.tinyEditors[this.fieldname] === undefined ? undefined : (typeof this.tinyEditors[this.fieldname] === 'object' ? this.tinyEditors[this.fieldname] : undefined);
	};

	// why do we return textarea only if inlineFalse
	this.getTextarea = function () {
		return this.tinyEditors[this.fieldname] === undefined ? undefined : (typeof this.tinyEditors[this.fieldname] === 'object' ? undefined : this.doc.getElementById(this.tinyEditors[this.fieldname]));
	};

	this.getPreviewDiv = function () {
		return this.getDív();
	};

	this.getDiv = function () {
		return this.tinyEditors[this.fieldname] === undefined ? undefined : (typeof this.tinyEditors[this.fieldname] === 'object' ? undefined : this.doc.getElementById('div_wysiwyg_' + this.tinyEditors[this.fieldname]));
	};

	this.getIFrame = function () {
		var frame_id = this.getId() + '_ifr';

		if (this.tinymce !== undefined && this.tinymce.DOM) {
			try {
				return this.tinymce.DOM.get(frame_id) !== null ? this.tinymce.DOM.get(frame_id) : undefined;
			} catch (e) {
				return undefined;
			}
		} else {
			return undefined;
		}
	};

	this.getContent = function (forcePopup) { // forcePopup means we return unsynchronized content from popup if opened
		forcePopup = forcePopup === undefined ? false : forcePopup;
		if (!this.isInlineTrue) {
			if (forcePopup) {
				try {
					return this.tinyEditorsInlineFalse[this.fieldname].getContent();
				} catch (err) {
				}
			}
			// if not forcePopup or if it failed, we return content from previewDiv
			try {
				return this.doc.getElementById(this.tinyEditors[fieldname]).value;
			} catch (err) {
			}
		} else {
			try {
				return this.tinyEditors[this.fieldname].getContent();
			} catch (err) {
			}
		}
	};

	this.setContent = function (cnt) {
		if (!this.isInlineTrue) { // we try to set content both in previewDiv/textarea and editor if opened
			try {
				this.doc.getElementById(this.tinyEditors[this.fieldname]).value = cnt;
				this.doc.getElementById('div_wysiwyg_' + this.tinyEditors[this.fieldname]).innerHTML = cnt;
			} catch (err) {
			}
			try {
				this.tinyEditorsInlineFalse[this.fieldname].setContent(cnt);
			} catch (err) {
			}
		} else {
			try {
				this.tinyEditors[this.fieldname].setContent(cnt);
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
