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

WE().layout.we_tinyMCE.do.afterPastePlugin = function(pl, o) {
	var pattImg = /<img [^>]*src=["\']data:[^>]*>/gi;
	if (o.content.match(pattImg)) {
		o.content = o.content.replace(pattImg, '');
		top.we_showMessage(WE().consts.g_l.tinyMceTranslationObject.removedInlinePictures, WE().consts.message.WE_MESSAGE_ERROR);
	}
	var patScript=/<script[^>]*.*< ?\/script[^>]*>/gi;
	o.content.replace(patScript, "");
	var patStyle=/<style[^>]*.*< ?\/style[^>]*>/gi;
	//o.content.replace(patStyle, '');
};

WE().layout.we_tinyMCE.do.beforePastePlugin = function(ed) {
	WE().layout.we_tinyMCE.functions.setHotEditorAndFrame(ed);
};

WE().layout.we_tinyMCE.do.onKeyUp = function(ed) {
	WE().layout.we_tinyMCE.functions.setHotEditorAndFrame(ed);
};

WE().layout.we_tinyMCE.do.onChange = function(ed) {
	WE().layout.we_tinyMCE.functions.setHotEditorAndFrame(ed);
};

WE().layout.we_tinyMCE.do.onClick = function(ed) {
	WE().layout.we_tinyMCE.functions.setHotEditorAndFrame(ed);
};

WE().layout.we_tinyMCE.do.onSaveContent = function(ed, o) {
	ed.weEditorFrameIsHot = false;
	// if is popup and we click on ok
	if (ed.editorLevel === "popup" && ed.isDirty()) {
		try {
			ed.weEditorFrame.setEditorIsHot(true);
		} catch (e) {
		}
	}

	/*
	 // and we transform image sources to we format before writing it to session!
	 var div = document.createElement("div"),
	 imgs;

	 div.innerHTML = o.content;
	 if(imgs = div.getElementsByTagName("IMG")){
	 var matches;
	 for(var i = 0; i < imgs.length; i++){
	 if(matches = imgs[i].src.match(/[^?]+\?id=(\d+)/)){
	 imgs[i].src = WE().consts.linkPrefix.TYPE_INT_PREFIX+ matches[1];
	 }
	 if(matches = imgs[i].src.match(/[^?]+\?thumb=(\d+,\d+)/)){
	 imgs[i].src = WE().consts.linkPrefix.TYPE_THUMB_PREFIX + matches[1];
	 };
	 }
	 o.content = div.innerHTML;
	 div = imgs = matches = null;
	 }
	 */
};

WE().layout.we_tinyMCE.do.onKeyDown = function(ed, e){
	var conf = ed.settings;

	if(e.ctrlKey || e.metaKey){
		switch(e.keyCode){
			case 68:
			case 79:
			case 82:
			case 87:
				//set keyCode = -1 to just let WE-keyListener cancel event
				if(e.keyCode !== 87 || conf.weIsFullscreen || conf.settings.weIsInPopup){
					e.keyCode = -1;
				}
			case 83:
				e.stopPropagation();
				e.preventDefault();
				WE().handler.dealWithKeyboardShortCut(e,window);
				return false;
			case 87:
				if(conf.weIsFullscreen || conf.weIsInPopup){
					e.keyCode = -1;
				}
			default:
				//let tiny do its job
		}
	}
};

WE().layout.we_tinyMCE.do.onDblClick = function(ed, e) {
	var openDialogsOnDblClick = true;

	if (openDialogsOnDblClick) {
		if (ed.selection.getNode().nodeName === "IMG" && ed.dom.getAttrib(ed.selection.getNode(), "src", "")) {
			ed.execCommand("mceWeimage");
		}
		if (ed.selection.getNode().nodeName === "A" && ed.dom.getAttrib(ed.selection.getNode(), "href", "")) {
			ed.execCommand("mceWelink");
		}
		return;
	}
	
	// old reaction on dblclick: open linked doc/image in multieditor or external
	var match,src, href;
	var frameControler = WE().layout.weEditorFrameController;

	if (!frameControler) {
		return;
	}

	if (ed.selection.getNode().nodeName === "IMG" && (src = ed.dom.getAttrib(ed.selection.getNode(), "src", ""))) {
		var regex = new RegExp('[^" >]*\?id=(\d+)[^" >]*');
		if ((match = src.match(regex))) {
			if (match[1] && parseInt(match[1]) !== 0) {
				frameControler.openDocument(WE().consts.tables.FILE_TABLE, match[1], "");
			}
		} else {
			new (WE().util.jsWindow)(ed.settings.win, src, "_blank", WE().consts.size.dialog.fullScreen, WE().consts.size.dialog.fullScreen, true, true, true);
		}
	}
	if (ed.selection.getNode().nodeName === "A" && (href = ed.dom.getAttrib(ed.selection.getNode(), "href", ""))) {
		var regex = new RegExp("(" + WE().consts.linkPrefix.TYPE_INT_PREFIX + "|" + WE().consts.linkPrefix.TYPE_OBJ_PREFIX + '|)(\d+)[^" >]*');
		if ((match = href.match(regex))) {
			if (match[1]) {
				switch (match[1]) {
					case WE().consts.linkPrefix.TYPE_INT_PREFIX:
						frameControler.openDocument(WE().consts.tables.FILE_TABLE, match[2], "");
						break;
					case WE().consts.linkPrefix.TYPE_OBJ_PREFIX:
						frameControler.openDocument(WE().consts.tables.OBJECT_FILES_TABLE, match[2], "");
						break;
				}
			}
		} else {
			new (WE().util.jsWindow)(ed.settings.win, href, "_blank", WE().consts.size.dialog.fullScreen, WE().consts.size.dialog.fullScreen, true, true, true);
		}
	}
};

WE().layout.we_tinyMCE.do.onNodeChange = function(ed, cm, n) {
	var pc, tmp;
	var td = ed.dom.getParent(n, "td");

	if (typeof td === "object" && td && td.getElementsByTagName("p").length === 1) {
		pc = td.getElementsByTagName("p")[0].cloneNode(true);
		tmp = document.createElement("div");
		tmp.appendChild(pc);

		if (tmp.innerHTML === td.innerHTML) {
			td.innerHTML = "";
			ed.selection.setContent(pc.innerHTML);
		}
	}
}

WE().layout.we_tinyMCE.do.onPostRender = function(ed, cm) {
	var win = ed.settings.win;

	ed.settings.win.addEventListener("resize", function (e) {
		ed.settings.win.tinyMCE.weResizeEditor();
	});
	if (typeof ed.settings.win.tinyMCE.weResizeEditor === "function") {
		ed.settings.win.tinyMCE.weResizeEditor(true);
	}
};

WE().layout.we_tinyMCE.do.onPostProcess = function(ed, o) {
	var c = document.createElement("div");
	c.innerHTML = o.content;
	var first = c.firstChild;
	var i;

	if (first) {
		if (first.innerHTML == '&nbsp;' && first == c.lastChild) {
			c.innerHTML = '';
		} else if (ed.settings.weRemoveFirstParagraph === '1' && first.nodeName == 'P') {
			var useDiv = false,
				div = document.createElement('div'),
				attribs = ['style', 'class', 'dir'];
			div.innerHTML = first.innerHTML;

			for (i = 0; i < attribs.length; i++) {
				if (first.hasAttribute(attribs[i])) {
					div.setAttribute(attribs[i], first.getAttribute(attribs[i]));
					useDiv = true;
				}
			}
			if (useDiv) {
				c.replaceChild(div, first);
			} else {
				c.removeChild(first);
				c.innerHTML = first.innerHTML + c.innerHTML;
			}
		}
	}

	// remove border="0" and border="" from table tags
	var tables;
	if ((tables = c.getElementsByTagName("TABLE"))) {
		for (i = 0; i < tables.length; i++) {
			if (tables[i].getAttribute("border") === "0" || tables[i].getAttribute("border") === "") {
				tables[i].removeAttribute("border");
			}
		}
	}

	// write content back
	o.content = c.innerHTML;
};

WE().layout.we_tinyMCE.do.onDrop = function(e, ed) {
	if (e.dataTransfer && e.dataTransfer.getData('text')) {
		var data = e.dataTransfer.getData('text').split(',');

		// dragging from WE (when permitted) comes with transfer text starting with "dragItem": we handle it
		if(data[0] && data[0] === 'dragItem' && data[1] === WE().consts.tables.FILE_TABLE){
			e.preventDefault();
			e.stopPropagation();
			if(data[3] === WE().consts.contentTypes.IMAGE){
				ed.execCommand('mceWeimage', true, data[2]);
			} else {
				ed.execCommand('mceWelink', true, data[2]);
			}
			return false;
		}

		// dragging inside tiny comes with transfer text not starting with "dragItem": let tiny handle it
		return true;
	}

	// dragging images from os comes witout transfer text: we prevent it!
	e.preventDefault();
	e.stopPropagation();
	return false;
};

WE().layout.we_tinyMCE.do.onCopyCut = function(ed, isCut) {
	var selection = ed.getWin().getSelection();
	var tmpDiv = ed.getDoc().createElement('div');

	tmpDiv.appendChild(selection.getRangeAt(0).cloneContents());

	if (tmpDiv.firstElementChild !== null){
		tmpDiv.firstElementChild.setAttribute('we-tiny', '1');
		// mark tmpDiv too: in FF tmpDiv is written to clipboard when using selection.selectAllChildren(tmpDiv)!
		tmpDiv.setAttribute('name', 'we-tiny-tmpDiv');

		// to get marked content into the clipboard, we must
		// - set tiny-"bookmark" to original selection (i.e. set two spans as start- and end-marker around selection)
		// - append tmpDiv to tiny content
		// - select its innerHTML,
		// - remove tmpDiv, and
		// - reselect original selection using bookmark

		WE().layout.we_tinyMCE.vars.bm = ed.settings.win.tinyMCE.activeEditor.selection.getBookmark();
		tmpDiv.style.position = 'absolute';
		tmpDiv.style.left = '-99999px';
		ed.getBody().appendChild(tmpDiv);
		selection.selectAllChildren(tmpDiv);

		ed.getWin().setTimeout(function () {
			ed.getBody().removeChild(tmpDiv);
			tmpDiv = null;
			ed.settings.win.tinyMCE.activeEditor.selection.moveToBookmark(WE().layout.we_tinyMCE.vars.bm);
			WE().layout.we_tinyMCE.vars.bm = null;
			if(isCut){
				ed.settings.win.tinyMCE.activeEditor.selection.setContent('');
			}
			// we mus repaint tiny-path too
		}, 100);
	}
};

WE().layout.we_tinyMCE.functions.setHotEditorAndFrame = function(ed) {
	if (!ed.weEditorFrameIsHot && ed.editorLevel === 'inline' && ed.isDirty()) {
		try {
			ed.weEditorFrame.setEditorIsHot(true);
		} catch (e) {
		}
		ed.weEditorFrameIsHot = true;
	}
};

WE().layout.we_tinyMCE.functions.tinyWeResizeEditor = function(render, name, win) {
	var el = win.tinyMCE.DOM.get(name + "_toolbargroup");
	var h = el ? el.parentNode.offsetHeight : 0;
	if ((render || !el) && --win.tinyMCE.weResizeLoops && h < 24) {
		window.setTimeout(tinyWeResizeEditor, 10, true);
		return;
	}

	win.tinyMCE.DOM.setStyle(
		win.tinyMCE.DOM.get(name + "_ifr"),
		"height",
		(win.innerHeight - h - 60) + "px"
		);
};

WE().layout.we_tinyMCE.functions.customNodeFilter_A = function(nodes) {
	for(var i = 0; i < nodes.length; i++){
		if (!nodes[i].attr("href") && !nodes[i].attr("id")) {
			nodes[i].attr("id", nodes[i].attr("name"));
		}
	}
};

WE().layout.we_tinyMCE.functions.tinySetEditorLevel = function(ed) {
	/* set EditorFrame.setEditorIsHot(true) */

	// we look for editorLevel and weEditorFrameController just once at editor init
	ed.editorLevel = '';
	ed.weEditorFrame = null;
	// if editorLevel = "inline" we use a local copy of weEditorFrame.EditorIsHot
	ed.weEditorFrameIsHot = false;

	//FIXME: this doesn't work: we need to know wheter it is inline, popup or fullscreen and wheter we are on a document/object in multieditor
	//simply mark inline-false-popup and fullscreen with some js var and then check for multieditor on the respective level

	// FIXME: why doesn't ed not know it's level after init: tell it it's level and done!
	if (ed.settings.win._EditorFrame !== undefined) {
		ed.editorLevel = "inline";
		ed.weEditorFrame = ed.settings.win._EditorFrame;
	} else if (ed.settings.win.opener !== null && ed.settings.win.opener.top.WebEdition && ed.settings.win.opener.top.WebEdition.layout.weEditorFrameController !== undefined && ed.settings.win.isWeDialog === undefined) {
		ed.editorLevel = "popup";
		ed.weEditorFrame = ed.settings.win.opener.top.WebEdition.layout.weEditorFrameController;
	} else if (ed.settings.win.isWeDialog) {
		ed.editorLevel = "fullscreen";
		ed.weEditorFrame = null;
	} else {
		ed.editorLevel = "popup";
		ed.weEditorFrame = null;
	}
};

WE().layout.we_tinyMCE.functions.getDocumentCss = function(win, preKomma){
	var doc = win.document;
	var styles=[];
	if(doc.styleSheets){
		for(var i=0;i<doc.styleSheets.length;i++){
			if(doc.styleSheets[i].href && doc.styleSheets[i].href.indexOf("&wysiwyg=0")===-1 && !doc.styleSheets[i].href.match(/webEdition\//) && (doc.styleSheets[i].media.length==0||doc.styleSheets[i].media.mediaText.indexOf("all")>=0 || doc.styleSheets[i].media.mediaText.indexOf("screen")>=0)){
				styles.push(doc.styleSheets[i].href);
			}
		}
	}
	return styles.length?((preKomma?",":"")+styles.join(",")):"";
};
