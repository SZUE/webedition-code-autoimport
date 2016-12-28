/* Functions for the welink plugin popup */

/* global tinyMCEPopup, tinymce */
'use strict';

tinyMCEPopup.requireLangPack();

var LinkDialog = {

	preinit: function() {
		var url;
		if ((url = tinyMCEPopup.getParam("external_link_list_url"))) {
			document.write('<script src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
		}
	},

	getAnchorListHTML: function(id, target) {
		var ed = tinyMCEPopup.editor, nodes = ed.dom.select('a'), name, i, len, html = "";

		for (i = 0, len = nodes.length; i < len; i++) {
			if ((name = ed.dom.getAttrib(nodes[i], "name")) !== ""){
				html += '<option value="#' + name + '">' + name + '</option>';
			}
		}

		if (html === "") {
			return "";
		}

		html = '<select id="' + id + '" name="' + id + '" class="mceAnchorList"' +
						' onchange="this.form.' + target + '.value=this.options[this.selectedIndex].value"' +
						'>' +
						'<option value="">---</option>' +
						html +
						'</select>';

		return html;
	},

	writeBack: function(attribs) {top.console.log('attribs', attribs);
		this.preinit();

		var inst = tinyMCEPopup.editor;
		var elem, elementArray, i;

		elem = inst.selection.getNode();
		elem = inst.dom.getParent(elem, "A");

		// Remove element if there is no href
		if (!attribs.href) {
			i = inst.selection.getBookmark();
			inst.dom.remove(elem, 1);
			inst.selection.moveToBookmark(i);
			tinyMCEPopup.execCommand("mceEndUndoLevel");
			tinyMCEPopup.close();
			top.close();
			return;
		}

		// Create new anchor elements
		if (elem === null) {
			inst.getDoc().execCommand("unlink", false, null);
			tinyMCEPopup.execCommand("mceInsertLink", false, "#mce_temp_url#", {skip_undo: 1});
			elementArray = tinymce.grep(inst.dom.select("a"), function (n) {
				return inst.dom.getAttrib(n, 'href') == '#mce_temp_url#';
			});
			for (i = 0; i < elementArray.length; i++){
				this.setAllAttribs(elem = elementArray[i], attribs);
			}
		} else {
			this.setAllAttribs(elem, attribs);
		}

		// Don't move caret if selection was image
		if (elem.childNodes.length != 1 || elem.firstChild.nodeName != 'IMG') {
			inst.focus();
			inst.selection.select(elem);
			inst.selection.collapse(0);
			tinyMCEPopup.storeSelection();
		}

		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
		top.close();
	},

	setAllAttribs: function(elem, attribs) {
		//top.console.log(attribs);
		this.setAttrib(elem, 'href', attribs.href.replace(/ /g, '%20'));
		this.setAttrib(elem, 'title', attribs.title);
		this.setAttrib(elem, 'target', attribs.target === '_self' ? '' : attribs.target);
		this.setAttrib(elem, 'id', attribs.id);
		this.setAttrib(elem, 'style', attribs.style);
		this.setAttrib(elem, 'class', attribs.class);
		this.setAttrib(elem, 'rel', attribs.rel);
		this.setAttrib(elem, 'rev', attribs.rev);
		this.setAttrib(elem, 'hreflang', attribs.hreflang);
		this.setAttrib(elem, 'lang', attribs.lang);
		this.setAttrib(elem, 'tabindex', attribs.tabindex);
		this.setAttrib(elem, 'accesskey', attribs.accesskey);

		// Refresh in old MSIE
		if (tinyMCE.isMSIE5) {
			elem.outerHTML = elem.outerHTML;
		}
	},

	setAttrib: function(elem, attrib, value) {
		var dom = tinyMCEPopup.editor.dom;
		if (attrib === 'style') {
			value = dom.serializeStyle(dom.parseStyle(value), 'a');
		}

		dom.setAttrib(elem, attrib, value);
	}

};