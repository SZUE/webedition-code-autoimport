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

var WeabbrDialog = { // TODO: clean code by using more vars
	sel : '',
	inst : '',
	elm : '',
	isAbbr : false,

	init : function() {
		var langValue = '';
		var titleValue = '';

		this.inst = tinyMCEPopup.editor;
		this.elm = this.inst.selection.getNode();
		this.sel = this.inst.selection.getContent({format : 'text'});

		//var printAsSelection = '';

		if(this.sel === ''){
			// no selection, but cursor inside ABBR (the only case where acronym-Button is active without selection):
			this.sel = this.elm.innerHTML;
			this.isAbbr = true;
		} else{
			if(this.elm.nodeName === 'ABBR' && this.sel.trim() === this.elm.innerHTML.trim()){ //exact selection is innerHTML of ABBR
				this.isAbbr = true;
			}
		}

		if(this.isAbbr){
			langValue = this.elm.getAttribute('lang') ? this.elm.getAttribute('lang') : '';
			titleValue = this.elm.getAttribute('title') ? this.elm.getAttribute('title') : '';
		}

		document.forms.we_form.elements['we_dialog_args[lang]'].value = langValue;
		document.forms.we_form.elements['we_dialog_args[title]'].value = titleValue;
		document.forms.we_form.elements.text.value = this.sel; //Selected Text to insert into glossary
	},

	insert : function() {
		var langValue = document.forms.we_form.elements['we_dialog_args[lang]'].value;
		var titleValue = document.forms.we_form.elements['we_dialog_args[title]'].value;

		if(this.isAbbr){//if there is an existing ACRONYM selected: just manipulate lang-Attribute
			if(titleValue !== ''){
				this.inst.selection.getNode().setAttribute('title', titleValue);
				if(langValue !== ''){
					this.inst.selection.getNode().setAttribute('lang', langValue);
				} else{
					this.inst.selection.getNode().removeAttribute('lang');
				}
			} else{
				this.inst.dom.remove(this.inst.selection.getNode(), 1);
			}
		} else{//no ACRONYM selected: insert tight and move blanks to the right of ACRONYM
			if(titleValue !== ''){
				var blank = '';
				var isBlank = false;
				while(this.sel.charAt(this.sel.length-1) === ' '){
					this.sel = this.sel.substr(0,this.sel.length-1);
					isBlank = true;
					blank += '&nbsp;';
				}
				blank = isBlank ? blank.substr(0,blank.length-6) + ' ' : blank;

				var visual = this.inst.hasVisual ? ' class="mceItemWeAbbr"' : '';
				var content = '<abbr lang="' + langValue + '" title="' + titleValue + '"' + visual + '>' + this.sel + '</abbr>' + blank;
				this.inst.execCommand('mceInsertContent', false, content);
			}
		}
		//tinyMCEPopup.close();
	}
};

function weTinyDialog_doOk(){
	WeabbrDialog.insert();
	top.close();
}

tinyMCEPopup.onInit.add(WeabbrDialog.init, WeabbrDialog);