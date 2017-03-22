/* Functions for the welink plugin popup */

/* global tinyMCEPopup, tinymce */
'use strict';

//tinyMCEPopup.requireLangPack();
var weFocusedField; // set inside LinkDialog!

var LinkDialog = {
	init: function () {top.opener.top.console.log('mce', tinymce);
	//	tinyMCEPopup.resizeToInnerSize();

		var formObj = document.forms.we_form;
		var inst = tinyMCEPopup.editor;
		var elm = inst.selection.getNode();
		var action = "insert";
		var html;

		// Anchor list
//		html = this.getAnchorListHTML('anchorlist', 'we_dialog_args[anchor]'); // this one makes troubles!
		if (html !== "") {
			document.getElementById("anchorlistcontainer").innerHTML = html;
		}

		elm = inst.dom.getParent(elm, "A");
		if (elm !== null && elm.nodeName === "A") {
			action = "update";
		}
		//this.addClassesToList('we_dialog_args[cssclass]', 'advlink_styles'); here too: this one causes problems!

		if (action === "update" && inst.isWeLinkInitialized === false && formObj) {alert('hooohe 5');
			inst.isWeLinkInitialized = true;

			//var href = inst.dom.getAttrib(elm, 'href');
			//var urlParts = this.getUrlParts(href);

			//formObj.elements['we_dialog_args[anchor]'].value = urlParts[1];
			//formObj.elements['we_dialog_args[param]'].value = urlParts[2];

			formObj.elements['we_dialog_args[title]'].value = inst.dom.getAttrib(elm, 'title');
			formObj.elements['we_dialog_args[target]'].value = inst.dom.getAttrib(elm, 'target');
			formObj.elements['we_dialog_args[rel]'].value = inst.dom.getAttrib(elm, 'rel');
			formObj.elements['we_dialog_args[lang]'].value = inst.dom.getAttrib(elm, 'lang');
			formObj.elements['we_dialog_args[hreflang]'].value = inst.dom.getAttrib(elm, 'hreflang');
			formObj.elements['we_dialog_args[rev]'].value = inst.dom.getAttrib(elm, 'rev');
			formObj.elements['we_dialog_args[accesskey]'].value = inst.dom.getAttrib(elm, 'accesskey', elm.accesskey !== undefined ? elm.accesskey : "");
			formObj.elements['we_dialog_args[tabindex]'].value = inst.dom.getAttrib(elm, 'tabindex', elm.tabindex !== undefined ? elm.tabindex : "");
			this.selectOptionByValue(formObj, "we_dialog_args[cssclass]", inst.dom.getAttrib(elm, 'class'));
		} else top.opener.topconsole.log('n drin');
		/*
		 if(typeof(inst.settings.theme_advanced_styles) !== 'undefined' && inst.settings.theme_advanced_styles != ''){
		 var cl = '';
		 for(var i=0; i < inst.settings.theme_advanced_styles.split(/;/).length; i++){
		 cl = inst.settings.theme_advanced_styles.split(/;/)[i].split(/=/)[0];
		 formObj.elements['we_dialog_args[cssclass]'].options[formObj.elements['we_dialog_args[cssclass]'].length] = new Option('.' + cl, cl);
		 }
		 }
		 */
	},

	writeBack: function(attribs) {top.opener.top.console.log('writeback link', attribs);
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
				return inst.dom.getAttrib(n, 'href') === '#mce_temp_url#';
			});
			for (i = 0; i < elementArray.length; i++){
				this.setAllAttribs(elem = elementArray[i], attribs);
			}
		} else {
			this.setAllAttribs(elem, attribs);
		}

		// Don't move caret if selection was image
		if (elem.childNodes.length != 1 || elem.firstChild.nodeName !== 'IMG') {
			inst.focus();
			inst.selection.select(elem);
			inst.selection.collapse(0);
			tinyMCEPopup.storeSelection();
		}

		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
		top.close();
	},

	// FIXME: doesn't work
	getAnchorListHTML: function(id, target) {
		var ed = tinyMCEPopup.editor, nodes = ed.dom.select('a'), name, i, len, html = "";

		for (i = 0, len = nodes.length; i < len; i++) {
			if ((name = ed.dom.getAttrib(nodes[i], 'name')) !== ''){
				html += '<option value="' + name + '">' + name + '</option>';
			}
		}

		if (html === "") {
			return "";
		}

		html = '<select id="' + id + '" name="' + id + '" class="defaultfont" style="width:100px"' +
			' onchange="this.form.elements[\'' + target + '\'].value=this.options[this.selectedIndex].value;this.selectedIndex=0;"' +
			'>' +
			'<option value=""></option>' +
			html +
			'</select>';

		return html;
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
	},

	setAttrib: function(elem, attrib, value) {
		var dom = tinyMCEPopup.editor.dom;
		if (attrib === 'style') {
			value = dom.serializeStyle(dom.parseStyle(value), 'a');
		}

		dom.setAttrib(elem, attrib, value);
	},

	addClassesToList: function(list_id, specific_option) {
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

	changeTypeSelect: function(s) {
		var elem = document.getElementsByClassName("we_change");
		for (var i = 0; i < elem.length; i++) {
			elem[i].style.display = (elem[i].className.match(s.value) ? "" : "none");
		}
	}

	/* we_functions */
	/*
	doReload: function(form) {
		form.elements.we_what.value = "dialog";//verhindert Neuladen
		form.target = 'we_weHyperlinkDialog_edit_area';
		form.submit();
	}
	*/

	/*
	getUrlParts: function(url) {
		var u = '', anch = '', param = '';

		var anchArr = url.split('#');
		u = anchArr.shift();
		anch = (anchArr[0]) ? anchArr.join('#') : anch;
		//var paramArr = u.split('?');
		//u = paramArr.shift();
		//param = (paramArr[0]) ? paramArr.join('?') : param;

		return [u, anch, param];
	}
	*/

};

tinyMCEPopup.onInit.add(function(){alert('huuuhu');});
tinyMCEPopup.onInit.add(LinkDialog.init);