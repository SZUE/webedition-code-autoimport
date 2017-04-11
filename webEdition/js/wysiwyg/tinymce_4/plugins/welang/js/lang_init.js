/* global tinyMCEPopup */

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
//tinyMCEPopup.requireLangPack();


var WelangDialog = { // TODO: clean code by using more vars

	sel : '',
	inst : '',
	elm : '',
	isSpan : false,
	isLangSpan : false,

	init : function() {
		var langValue = '';

		this.inst = tinyMCEPopup.editor;
		this.elm = this.inst.selection.getNode();
		this.sel = this.inst.selection.getContent({format : 'text'});

		var printAsSelection = '';

		if(this.sel === ''){
			// no selection, but cursor inside SPAN with lang-attribute (the only case where lang-Button is active without selection):
			// we will manipulate this lang-attribute
			this.sel = this.elm.innerHTML;
			this.isLangSpan = true;
		} else{
			if(this.elm.nodeName === 'SPAN' && this.sel.trim() === this.elm.innerHTML.trim()){ //exact selection is innerHTML of SPAN: we will add or manipulate lang-attrib of existing SPAN
				//printAsSelection = sel;
				this.isLangSpan = true;
			} else{ //exact selection is not innerHTML of SPAN: we will add new SPAN
				//printAsSelection = sel;
			}
		}

		if(this.isLangSpan){
			langValue = this.elm.getAttribute('lang') ? this.elm.getAttribute('lang') : '';
		}

		document.forms.we_form.elements['we_dialog_args[lang]'].value = langValue;
		document.forms.we_form.elements.text.value = this.sel; //Selected Text to insert into glossary
	},

	insert : function() {
		var lang = document.forms.we_form.elements['we_dialog_args[lang]'].value;
		if(this.isLangSpan){//if there is an existing SPAN selected: just manipulate lang-Attribute
			if(lang !== ''){
				this.inst.selection.getNode().setAttribute('lang', document.forms.we_form.elements['we_dialog_args[lang]'].value);

			} else{//remove attribute lang
				this.inst.selection.getNode().removeAttribute('lang');
				if(this.inst.selection.getNode().attributes.length === 0){//if no attributes left: remove SPAN
					this.inst.dom.remove(this.inst.selection.getNode(), 1);
				}
			}

		} else{//no SPAN selected: insert tight and move blanks to the right of SPAN
			if(lang !== ''){
				var blank = '';
				var isBlank = false;
				while(this.sel.charAt(this.sel.length-1) === ' '){
					this.sel = this.sel.substr(0, this.sel.length-1);
					isBlank = true;
					blank += '&nbsp;';
				}
				blank = isBlank ? blank.substr(0,blank.length-6) + ' ' : blank;

				var visual = this.inst.hasVisual ? ' class="mceItemWeLang"' : '';
				var content = '<span lang="' + document.forms.we_form.elements['we_dialog_args[lang]'].value + '"' + visual + '>' + this.sel + '</span>' + blank;
				this.inst.execCommand('mceInsertContent', false, content);
			}
		}
		//tinyMCEPopup.close();
	}
};

function weTinyDialog_doOk(){
	WelangDialog.insert();
	top.close();
}

tinyMCEPopup.onInit.add(WelangDialog.init, WelangDialog);
