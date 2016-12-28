/* global top, WE, tinyMCEPopup */

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
 * @package    webEdition_tinymce
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
'use strict';

var isTinyMCE = true;

var WegalleryDialog = {// TODO: clean code by using more vars

	sel: '',
	inst: '',
	isGallery: false,

	init: function () {
		var idValue = '';
		var tmplValue = '';

		this.inst = tinyMCEPopup.editor;
		var elm = this.inst.selection.getNode();
		this.sel = this.inst.selection.getContent({format: 'text'});

		if (elm.nodeName === 'WE-GALLERY') {
			this.isGallery = true;
		}

		if (this.isGallery) {
			idValue = elm.getAttribute('id') ? elm.getAttribute('id') : 0;
			tmplValue = elm.getAttribute('tmpl') ? elm.getAttribute('tmpl') : 0;
		}

		document.forms.we_form.elements['we_dialog_args[collid]'].value = idValue;
		document.forms.we_form.elements['we_dialog_args[tmpl]'].value = tmplValue;
	},

	insert: function () {
		var idValue = document.forms.we_form.elements['we_dialog_args[collid]'].value;
		var tmplValue = document.forms.we_form.elements['we_dialog_args[tmpl]'].value;

		if (this.isGallery) {
			if (idValue && tmplValue !== "0") {
				this.inst.selection.getNode().setAttribute('id', idValue);
				this.inst.selection.getNode().setAttribute('tmpl', tmplValue);
			} else {
				this.inst.dom.remove(this.inst.selection.getNode(), 1);
			}
			top.close();
			return;
		}
		if (idValue && tmplValue !== "0") {
			var blank = '';
			var isBlank = false;
			while (this.sel.charAt(sel.length - 1) === ' ') {
				this.sel = this.sel.substr(0, this.sel.length - 1);
				isBlank = true;
				blank += '&nbsp;';
			}
			blank = isBlank ? blank.substr(0, blank.length - 6) + ' ' : blank;

			var content = '<we-gallery id="' + idValue + '" tmpl="' + tmplValue + '"></we-gallery>' + blank;
			this.inst.execCommand('mceInsertContent', false, content);
			top.close();
		} else {
			top.we_showMessage(WE().consts.g_l.tinyMceTranslationObject[this.inst.getParam('language')].we.plugin_wegallery_values_nok, WE().consts.message.WE_MESSAGE_ERROR);
		}

		//tinyMCEPopup.close();
	}
};
tinyMCEPopup.onInit.add(WegalleryDialog.init, WegalleryDialog);