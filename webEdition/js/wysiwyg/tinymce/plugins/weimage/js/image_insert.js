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

/**
 * This source is based on tinyMCE-plugin "advimage":
 * Moxiecode Systems AB, http://tinymce.moxiecode.com/license.
 */
var ImageDialog = {
	preInit: function () {
		var url;
		//tinyMCEPopup.requireLangPack();
		if ((url = tinyMCEPopup.getParam("external_image_list_url"))) {
			document.write('<script src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
		}
	},

	writeBack: function (attributes) {
		this.preInit;
		var ed = tinyMCEPopup.editor, t = this;

		if(!attributes){
			top.close();
			return;
		}

		// remove <img> if src=""
		if (attributes.src === '' || attributes.src === 'http://') {
			if (ed.selection.getNode().nodeName == 'IMG') {
				ed.dom.remove(ed.selection.getNode());
				ed.execCommand('mceRepaint');
			}
			top.close();
			return;
		}

		if (tinyMCEPopup.getParam("accessibility_warnings", 1)) {
			if (!attributes.alt) {
				//tinyMCEPopup.confirm(tinyMCEPopup.getLang('advimage_dlg.missing_alt'), function(s) {
				//if (s){
				//t.insertAndClose();
				//}
				//});
				return;
			}
		}
		t.writebackAndClose(attributes);
	},
	writebackAndClose: function (attributes) {
		var ed = tinyMCEPopup.editor, attribs = attributes, v, args = {}, el;


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
			border: attribs.border,
			alt: attribs.alt,
			align: attribs.align,
			name: attribs.name,
			'class': attribs['class'], // 'class' is a reserved word in IE <= 8 and therefore needs wrapping
			title: attribs.title,
			longdesc: attribs.longdesc
			//style : attribs.style,
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
	}

	// removed lots of original tinyMCE-functions
};
