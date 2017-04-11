/* global tinyMCEPopup, tinymce, TinyMCE_EditableSelects */

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

/**
 * This source is based on tinyMCE-plugin "advimage":
 * Moxiecode Systems AB, http://tinymce.moxiecode.com/license.
 */

var ImageDialog = {
	init: function (ed) {
		var f = document.forms.we_form;
		var nl = f.elements;
		ed = tinyMCEPopup.editor;
		var dom = ed.dom;
		var n = ed.selection.getNode();
		var fl = tinyMCEPopup.getParam('external_image_list', 'tinyMCEImageList');
		tinyMCEPopup.resizeToInnerSize();
		TinyMCE_EditableSelects.init();
//		this.addClassesToList('we_dialog_args[cssclass]', 'advlink_styles'); // this one causes problems!
		if (nl["we_dialog_args[isPresetFromDnD]"].value == 1) {
			nl["we_dialog_args[isPresetFromDnD]"].value = 0;
			top.imageChanged();
		}

		if (n.nodeName === 'IMG' && !ed.isWeDataInitialized) {
			var imgWidth, imgHeight, longdesc;

			// load attributes into form
			imgWidth = dom.getAttrib(n, 'width');
			imgHeight = dom.getAttrib(n, 'height');
			nl["we_dialog_args[width]"].value = imgWidth;
			nl["we_dialog_args[height]"].value = imgHeight;
			nl["we_dialog_args[rendered_width]"].value = n.width;
			nl["we_dialog_args[rendered_height]"].value = n.height;
			nl["we_dialog_args[vspace]"].value = dom.getAttrib(n, 'vspace');
			nl["we_dialog_args[hspace]"].value = dom.getAttrib(n, 'hspace');
			nl["we_dialog_args[border]"].value = dom.getAttrib(n, 'border');
			nl["we_dialog_args[alt]"].value = dom.getAttrib(n, 'alt');
			nl["we_dialog_args[title]"].value = dom.getAttrib(n, 'title');
			nl["we_dialog_args[name]"].value = dom.getAttrib(n, 'name');
			this.selectOptionByValue(f, "we_dialog_args[align]", dom.getAttrib(n, 'align'));
			longdesc = dom.getAttrib(n, 'longdesc');
			nl["we_dialog_args[longdescsrc]"].value = longdesc.split('?id=', 2)[1] ? longdesc.split('?id=', 2)[0] : '';
			nl["we_dialog_args[longdescid]"].value = longdesc.split('?id=', 2)[1] ? longdesc.split('?id=', 2)[1] : '';
			this.selectOptionByValue(f, "we_dialog_args[cssclass]", dom.getAttrib(n, 'class'));

			// set some flags
			ed.isWeDataInitialized = true;
			f.isTinyMCEInitialization.value = "0";

			if (!(isNaN(imgWidth * imgHeight) || imgHeight === 0 || imgWidth === 0)) {
				nl.tinyMCEInitRatioH.value = imgWidth / imgHeight;
				nl.tinyMCEInitRatioW.value = imgHeight / imgWidth;
			}
		}

		// add options to css-Pulldown
		/*
		 if(typeof(ed.settings.theme_advanced_styles) !== 'undefined' && ed.settings.theme_advanced_styles != ''){
		 var cl = '';
		 for(var i=0; i < ed.settings.theme_advanced_styles.split(/;/).length; i++){
		 cl = ed.settings.theme_advanced_styles.split(/;/)[i].split(/=/)[0];
		 nl["we_dialog_args[cssclass]"].options[nl["we_dialog_args[cssclass]"].length] = new Option('.' + cl, cl);
		 }
		 }
		 */
	},

	writeBack: function (attributes) {
		if (!attributes) {
			top.close();
			return;
		}

		var ed = tinyMCEPopup.editor, attribs = attributes, v, args = {}, el;

		// remove <img> if src=""
		if (attributes.src === '' || attributes.src === 'http://') {
			if (ed.selection.getNode().nodeName == 'IMG') {
				ed.dom.remove(ed.selection.getNode());
				ed.execCommand('mceRepaint');
			}
			top.close();
			return;
		}

		if (false && tinyMCEPopup.getParam("accessibility_warnings", 1)) {
			if (!attributes.alt) {
				//tinyMCEPopup.confirm(tinyMCEPopup.getLang('advimage_dlg.missing_alt'), function(s) {
				//if (s){
				//t.insertAndClose();
				//}
				//});
				return;
			}
		}

		tinyMCEPopup.restoreSelection();

		// Fixes crash in Safari
		if (tinymce.isWebKit) {
			ed.getWin().focus();
		}

		if (!ed.settings.inline_styles) {
			args = {
				vspace: attribs.vspace,
				hspace: attribs.hspace,
				border: attribs.border,
				align: attribs.align
			};
		} else {
			// Remove deprecated values
			args = {
				vspace: '',
				hspace: '',
				border: '',
				align: ''
			};
		}

		tinymce.extend(args, {
			src: attribs.src.replace(/ /g, '%20'),
			width: attribs.width,
			height: attribs.height,
			hspace: attribs.hspace,
			vspace: attribs.vspace,
			//border: attribs.border,
			alt: attribs.alt,
			align: attribs.align,
			name: attribs.name,
			'class': attribs['class'], // 'class' is a reserved word in IE <= 8 and therefore needs wrapping
			title: attribs.title,
			longdesc: attribs.longdesc,
			style : (attribs.border ? 'border:' + attribs.border + 'px  solid black;' : '') // Tiny4: HTML5 // TODO: add color!

			//id : attribs.id,
			//dir : attribs.dir,
			//lang : attribs.lang,
			//usemap : attribs.usemap,
		});

		el = ed.selection.getNode();

		if (el && el.nodeName == 'IMG') {
			ed.dom.setAttribs(el, args);
		} else {
			ed.execCommand('mceInsertContent', false, '<img id="__mce_tmp" />', {skip_undo: 1});
			//ed.execCommand('mceInsertContent', false, '<img />', {skip_undo : 1});
			ed.dom.setAttribs('__mce_tmp', args);
			ed.dom.setAttrib('__mce_tmp', 'id', '');
			ed.undoManager.add();
		}

		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.editor.focus();
		//tinyMCEPopup.close();
		top.close();
	},

	selectOptionByValue: function (form, selName, val) {
		if (form === undefined || form.elements[selName] === undefined && val === undefined) {
			return;
		}

		var i;
		if (val === '') {
			form.elements[selName].options[0].selected = true;
			for (i = 1; i < form.elements[selName].options.length; i++) {
				form.elements[selName].options[i].selected = false;
			}
		} else {
			var found = false;
			for (i = 1; i < form.elements[selName].options.length; i++) {
				if (form.elements[selName].options[i].value == val) {
					form.elements[selName].options[i].selected = true;
					found = true;
				} else {
					form.elements[selName].options[i].selected = false;
				}
			}
			if (!found) {
				//i++;
				form.elements[selName].options[i] = new Option('--------------------------------------', '');
				form.elements[selName].options[i + 1] = new Option(val, val);
				form.elements[selName].options[i + 1].selected = true;
			}
		}
	},
	addClassesToList: function (list_id, specific_option) {
		var styleSelectElm = document.getElementById(list_id);
		var styles = tinyMCEPopup.getParam('theme_advanced_styles', false);
		styles = tinyMCEPopup.getParam(specific_option, styles);

		//TODO: Do not write classes in weDialog, so we do not need to delete them here...
		for (var i = styleSelectElm.length - 1; i > 0; i--) {
			styleSelectElm.remove(i);
		}

		if (styles) {
			var stylesAr = styles.split(';');

			for (i = 0; i < stylesAr.length; i++) {
				if (stylesAr !== "") {
					var key, value;

					key = stylesAr[i].split('=')[0];
					value = stylesAr[i].split('=')[1];

					styleSelectElm.options[styleSelectElm.length] = new Option(key, value);
				}
			}
		} else {
			tinymce.each(tinyMCEPopup.editor.dom.getClasses(), function (o) {
				styleSelectElm.options[styleSelectElm.length] = new Option(o.title || o['class'], o['class']);
			});
		}
	},
	getAttrib: function (e, at) {
		var ed = tinyMCEPopup.editor, dom = ed.dom, v, v2;

		if (ed.settings.inline_styles) {
			switch (at) {
				case 'align':
					if ((v = dom.getStyle(e, 'float'))) {
						return v;
					}

					if ((v = dom.getStyle(e, 'vertical-align'))) {
						return v;
					}

					break;

				case 'hspace':
					v = dom.getStyle(e, 'margin-left');
					v2 = dom.getStyle(e, 'margin-right');

					if (v && v == v2) {
						return parseInt(v.replace(/[^0-9]/g, ''));
					}

					break;

				case 'vspace':
					v = dom.getStyle(e, 'margin-top');
					v2 = dom.getStyle(e, 'margin-bottom');
					if (v && v == v2) {
						return parseInt(v.replace(/[^0-9]/g, ''));
					}

					break;

				case 'border':
					v = 0;

					tinymce.each(['top', 'right', 'bottom', 'left'], function (sv) {
						sv = dom.getStyle(e, 'border-' + sv + '-width');

						// False or not the same as prev
						if (!sv || (sv != v && v !== 0)) {
							v = 0;
							return false;
						}

						if (sv) {
							v = sv;
						}
					});

					if (v) {
						return parseInt(v.replace(/[^0-9]/g, ''));
					}

					break;
			}
		}

		if ((v = dom.getAttrib(e, at))) {
			return v;
		}

		return '';
	}

	// lots of tinyMCE-functions removed

};

function weTinyDialog_doOk(){
	top.opener.top.console.log('image dialog save?');
}

tinyMCEPopup.onInit.add(ImageDialog.init);