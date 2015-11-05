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

var isTinyMCE = true;

var WegalleryDialog = { // TODO: clean code by using more vars

	sel : '',
	inst : '',
	elm : '',
	isGallery : false,

	init : function() {
		var idValue = '';
		var tmplValue = '';

		inst = tinyMCEPopup.editor;
		elm = inst.selection.getNode();
		sel = inst.selection.getContent({format : 'text'});

		if(elm.nodeName === 'WE-GALLERY'){
			this.isGallery = true;
		}

		if(this.isGallery){
			idValue = elm.getAttribute('id') ? elm.getAttribute('id') : 0;
			tmplValue = elm.getAttribute('tmpl') ? elm.getAttribute('tmpl') : 0;
		}

		document.forms.we_form.elements['we_dialog_args[collid]'].value = idValue;
		document.forms.we_form.elements['we_dialog_args[tmpl]'].value = tmplValue;
	},

	insert : function() {
		var idValue = document.forms.we_form.elements['we_dialog_args[collid]'].value;
		var tmplValue = document.forms.we_form.elements['we_dialog_args[tmpl]'].value;

		if(this.isGallery){
			if(idValue != 0 && tmplValue != 0){
				inst.selection.getNode().setAttribute('id', idValue);
				inst.selection.getNode().setAttribute('tmpl', tmplValue);
			} else{
				inst.dom.remove(inst.selection.getNode(), 1);
			}
		} else{
			if(idValue != 0 && tmplValue != 0){
				var blank = '';
				var isBlank = false;
				while(sel.charAt(sel.length-1) === ' '){
					sel = sel.substr(0,sel.length-1);
					isBlank = true;
					blank += '&nbsp;';
				}
				blank = isBlank ? blank.substr(0,blank.length-6) + ' ' : blank;

				var content = '<we-gallery id="' + idValue + '" tmpl="' + tmplValue + '"></we-gallery>' + blank;
				inst.execCommand('mceInsertContent', false, content);
			}
		}
		//tinyMCEPopup.close();
	}
};
tinyMCEPopup.onInit.add(WegalleryDialog.init, WegalleryDialog);