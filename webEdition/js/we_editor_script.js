/* global top, WE, ImageEditTools */

/**
 * webEdition CMS
 *
 * webEdition CMS
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

var doc = WE().util.getDynamicVar(document, 'loadVarEditor_script', 'data-doc');

var _controller = WE().layout.weEditorFrameController;

var _EditorFrame = _controller.getEditorFrame(window.parent.name);
if (!_EditorFrame) {
	_EditorFrame = (doc && doc.we_transaction ?
		_controller.getEditorFrameByTransaction(doc.we_transaction) :
		_controller.getEditorFrame());

}

var we_mediaReferences = null;

if (doc) {
	if (doc.cmd) {
		top.we_cmd.apply(window, [doc.cmd]);
		doc.cmd = false;
	}

	if (doc.useSEE_MODE) {
		// add event-Handler, replace links after load
		window.addEventListener("load", seeMode_dealWithLinks, false);
	}

	window.addEventListener("load", init_editor, false);
}

function init_editor(){
	WE().util.setIconOfDocClass(document, 'tag_img_icon');
}

function seeMode_dealWithLinks() {
	var _aTags = document.getElementsByTagName("a");

	for (var i = 0; i < _aTags.length; i++) {
		var _href = _aTags[i].href;

		if (!(_href.indexOf("javascript:") === 0 ||
			_href.indexOf("#") === 0 ||
			(_href.indexOf("#") === document.URL.length && _href === (document.URL + _aTags[i].hash)) ||
			_href.indexOf(WE().consts.linkPrefix.TYPE_OBJ_PREFIX) === 0 ||
			_href.indexOf(WE().consts.linkPrefix.TYPE_INT_PREFIX) === 0 ||
			_href.indexOf(WE().consts.linkPrefix.TYPE_MAIL_PREFIX) === 0 ||
			_href.indexOf("?") === 0 ||
			_href === ""
			)
			) {
			_aTags[i].href = "javascript:seeMode_clickLink(\'" + _aTags[i].href + "\')";

		}
	}
}

function seeMode_clickLink(url) {
	top.we_cmd("open_url_in_editor", url);
}

function showhideLangLink(allnames, allvalues, deselect) {
	var arr = allvalues.split(","),
		e, w;

	for (var v in arr) {
		w = allnames + '[' + arr[v] + ']';
		e = document.getElementById(w);
		e.style.display = 'block';
	}
	w = allnames + '[' + deselect + ']';
	e = document.getElementById(w);
	e.style.display = 'none';


}

function doScrollTo() {
	if (window.parent.scrollToVal) {
		window.scrollTo(0, window.parent.scrollToVal);
		window.parent.scrollToVal = 0;
	}
}

function translate(c) {
	var f = c.form,
		n = c.name,
		n2 = n.replace(/tmp_/, "we_"),
		t, check;
	n = n2.replace(/^(.+)#.+\]$/, "$1]");
	t = f.elements[n];
	check = f.elements[n2].value;

	t.value = (check === "on") ? br2nl(t.value) : nl2br(t.value);

}
function nl2br(i) {
	return i.replace(/\r\n/g, "<br/>").replace(/\n/g, "<br/>").replace(/\r/g, "<br/>").replace(/<br\/>/g, "<br/>\n");
}

function br2nl(i) {
	return i.replace(/\n\r/g, "").replace(/\r\n/g, "").replace(/\n/g, "").replace(/\r/g, "").replace(/<br ?\/?>/gi, "\n");
}

function we_submitForm(target, url) {
	var f = window.document.we_form;
	if (!f) {
		return false;
	}

	window.parent.openedWithWe = true;
	if (url) {
		f.action = url;
	}
	if (target && url) {
		f.target = target;
		f.method = "post";
	}
	f.submit();
	return true;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function updateCustomerFilterIfNeeded() {
	var _elem;
	if ((_elem = document.we_form["we_" + doc.docName + "_ParentID"])) {
		var parentid = _elem.value;
		if (parentid !== doc.oldparentid) {
			WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + 'rpc.php?cmd=GetUpdateDocumentCustomerFilterQuestion', 'cns=customer&folderId=' + parentid + '&we_transaction=' + doc.we_transaction + '&table=' + doc.docTable + '&classname=' + doc.docClass, function (weResponse) {
				if (weResponse) {
					if (weResponse.DataArray.data === true) {
						var _question = doc.isFolder ? WE().consts.g_l.alert.confirm_applyFilterFolder : WE().consts.g_l.alert.confirm_applyFilterDocument;
						WE().util.showConfirm(window, "", _question, ["customer_applyWeDocumentCustomerFilterFromFolder"]);
					}
				}
			});
			doc.oldparentid = parentid;
		}
	}
}

// check If Filename was changed..
function pathOfDocumentChanged(setHot) {
	var filetext = '';
	var filepath = '';
	if (setHot) {
		we_cmd('setHot');
	}
	var elem = document.we_form["we_" + doc.docName + "_Filename"]; // documents
	if (!elem) { // object
		elem = document.we_form["we_" + doc.docName + "_Text"];
	}

	if (elem) {

		// text
		filetext = elem.value;
		// Extension if there
		if (document.we_form["we_" + doc.docName + "_Extension"]) {
			filetext += document.we_form["we_" + doc.docName + "_Extension"].value;
		}

		// path
		if ((elem = document.we_form["we_" + doc.docName + "_ParentPath"])) {
			filepath = elem.value;
		}
		if (filepath !== "/") {
			filepath += "/";
		}

		filepath += filetext;
		WE().layout.we_setPath(_EditorFrame, filepath, filetext, -1, "");
		if (doc.hasCustomerFilter) {
			updateCustomerFilterIfNeeded();
		}
	}
}

function setScrollTo() {
	window.parent.scrollToVal = window.pageYOffset;
}

function goTemplate(tid) {
	if (tid > 0) {
		WE().layout.weEditorFrameController.openDocument(WE().consts.tables.TEMPLATES_TABLE, tid, WE().consts.contentTypes.TEMPLATE);
	}
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	var contentEditor = WE().layout.weEditorFrameController.getVisibleEditorFrame();

	switch (args[0]) {
		case "edit_link":
		case "edit_link_at_class":
		case "edit_link_at_object":
			new (WE().util.jsWindow)(caller, "", "we_linkEdit", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true);
			if (contentEditor.we_submitForm) {
				contentEditor.we_submitForm("we_linkEdit", url);
			}
			break;
		case "edit_linklist":
			new (WE().util.jsWindow)(caller, "", "we_linklistEdit", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true);
			if (contentEditor.we_submitForm) {
				contentEditor.we_submitForm("we_linklistEdit", url);
			}
			break;
		case "openColorChooser":
			new (WE().util.jsWindow)(caller, "", "we_colorChooser", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			if (contentEditor.we_submitForm) {
				contentEditor.we_submitForm("we_colorChooser", url);
			}
			break;
		case "dirChooser_callback":
			// to be callable from selectors we skip args[1]
			we_cmd('setHot');
			if (args[2] === 'ParentPath' && pathOfDocumentChanged) {
				pathOfDocumentChanged();
			}
			if (args[3]) {
				we_cmd(args[3]);
			}
			break;
		case "pathOfDocumentChanged":
			pathOfDocumentChanged(true);
			break;
		case "we_selector_directory":
		case "we_selector_document":
		case "we_selector_image":
			new (WE().util.jsWindow)(caller, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_customer_selector":
		case "we_selector_file":
			new (WE().util.jsWindow)(caller, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(caller, url, "browse_server", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "we_users_selector":
			new (WE().util.jsWindow)(caller, url, "browse_users", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true);
			break;
		case "object_editObjectTextArea":
			new (WE().util.jsWindow)(caller, url, "edit_object_text", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, false, true);
			break;
		case "open_templateSelect":
			new (WE().util.jsWindow)(caller, "", "we_templateSelect", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			if (contentEditor.we_submitForm) {
				contentEditor.we_submitForm("we_templateSelect", url);
			}
			break;
		case "open_tag_wizzard":
			new (WE().util.jsWindow)(caller, url, "we_tag_wizzard", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true);
			break;
		case "glossary_check":
			if (doc.hasGlossary) {
				new (WE().util.jsWindow)(caller, url, "glossary_check", WE().consts.size.dialog.medium, WE().consts.size.dialog.smaller, true, false, true);
			}
			break;
		case "add_thumbnail":
			new (WE().util.jsWindow)(caller, url, "we_add_thumbnail", WE().consts.size.dialog.smaller, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "imageDocument_emptyLongdesk":
			document.we_form.elements[args[2]].value = '-1';
			document.we_form.elements[args[3]].value = '';
			we_cmd('setHot');
			WE().layout.weSuggest.checkRequired(window, args[4]);
			break;
		case "image_resize":
			if (WE().consts.graphic.gdSupportedTypes[doc.gdType]) {
				ImageEditTools.Resize.start(url, doc.gdType);
			} else {
				WE().util.showMessage(WE().util.sprintf(WE().consts.g_l.editorScript.gdTypeNotSupported, doc.gdType), WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			break;
		case "image_convertJPEG":
			ImageEditTools.ConvertJPEG.start(url);
			break;
		case "image_rotate":
			if (WE().consts.graphic.canRotate) {
				if (doc.gdSupport) {
					ImageEditTools.Rotate.start(url, doc.gdType);
				} else {
					WE().util.showMessage(WE().util.sprintf(WE().consts.g_l.editorScript.gdTypeNotSupported, doc.gdType), WE().consts.message.WE_MESSAGE_ERROR, window);
				}
			} else {
				WE().util.showMessage(WE().consts.g_l.editorScript.noRotate, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			break;
		case "image_focus":
			ImageEditTools.Focus.start();
			break;
		case "image_crop":
			if (doc.WE_EDIT_IMAGE) {
				if (doc.gdSupport) {
					ImageEditTools.Crop.crop();
				} else {
					WE().util.showMessage(WE().util.sprintf(WE().consts.g_l.editorScript.gdTypeNotSupported, doc.gdType), WE().consts.message.WE_MESSAGE_ERROR, window);
				}
			}
			break;
		case "crop_cancel":
			ImageEditTools.deactivateAll();
			break;
		case "imageEditTools_reset":
			ImageEditTools.deactivateAll();
			break;
		case "image_convertGIF":
		case "image_convertPNG":
			ImageEditTools.deactivateAll();
			window.parent.we_cmd.apply(caller, args);
			break;
		case "spellcheck":
			if (WE().consts.dirs.WE_SPELLCHECKER_MODULE_DIR) {
				new (WE().util.jsWindow)(caller, WE().consts.dirs.WE_SPELLCHECKER_MODULE_DIR + "/weSpellchecker.php?editname=" + (args[1]), "spellcheckdialog", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true, false);
			}
			break;
		case "updateCollectionItem":
			_EditorFrame.setEditorIsHot(true);
			window.weCollectionEdit.callForValidItemsAndInsert(args[2], -1, args[1].currentID, '', false, (args[4] ? parseInt(args[4]) : 0));
			break;
		case "import_files":
			new (WE().util.jsWindow)(top, url, "import_files", WE().consts.size.dialog.medium, WE().consts.size.dialog.medium, true, false, true); // be sure we have top as opener!
			break;
		case 'tag_weHref_selectorCallback':
			_EditorFrame.setEditorIsHot(true);
			if (args[3] === WE().consts.linkPrefix.TYPE_ALL) {
				caller.document.we_form.elements[args[4]][(args[2] === WE().consts.linkPrefix.TYPE_INT ? 0 : 1)].checked = true;
			}
			if (args[5]) {
				caller.setScrollTo();
				top.we_cmd('reload_editpage');
			}
			break;
		case 'tag_weHref_openDocument':
			var value;
			if ((value = caller.document.we_form.elements[args[1]].value)) {
				WE().layout.weEditorFrameController.openDocument(WE().consts.tables.FILE_TABLE, value, '');
			}
			break;
		case 'tag_weHref_trash':
			_EditorFrame.setEditorIsHot(true);
			if (args[1] === WE().consts.linkPrefix.TYPE_INT) {
				caller.document.we_form.elements[args[2]].value = '';
				caller.document.we_form.elements[args[3]].value = '';
				if (args[4]) {
					caller.setScrollTo();
					top.we_cmd('reload_editpage');
				}
			} else {
				caller.document.we_form.elements[args[2]].value = '';
			}
			break;
		case 'orderContainerAdd':
			window.orderContainer.add(document, args[1], null);
			break;
		case 'setHot':
			_EditorFrame.setEditorIsHot(true);
			break;
		case 'setMediaReferences':
			we_mediaReferences = args[1];
			break;
		case "object_TextArea_apply":
			opener._EditorFrame.setEditorIsHot(true);
			window.opener.setScrollTo();
			break;
		case 'object_switch_edit_page':
			opener.we_cmd("switch_edit_page", args[1], args[2]);
			break;
		case 'object_changeTextareaParams_at_class':
			args[0] = 'object_reload_entry_at_class';
			opener.parent.we_cmd.apply(window, Array.prototype.slice.call(args));
			break;
		case "delete_navi_ask":
			args[0] = 'delete_navi';
			WE().util.showConfirm(caller, "", WE().consts.g_l.editorScript.confirm_navDel, args);
			break;
		case "orderContainer_processCommand":
			var container;
			if((container = _EditorFrame.getContentEditor().orderContainer)){
				container.processCommand(window, args[1], args[2], args[3]);
			}
			break;
		default:
			window.parent.we_cmd.apply(caller, Array.prototype.slice.call(arguments));

	}
}

function fields_are_valid() {
	if (doc.isWEObject) {
		var theInputs = document.getElementsByTagName("input"),
			theType,
			theVal;

		for (var i = 0; i < theInputs.length; i++) {

			if ((theType = theInputs[i].getAttribute("weType")) && (theVal = theInputs[i].value)) {

				switch (theType) {
					case "int":
					case "integer":
						if (!theVal.match(/^-{0,1}\d+$/)) {
							WE().util.showMessage(WE().consts.g_l.editorScript.field_contains_incorrect_chars.replace(/%s/, theType), WE().consts.message.WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						} else if (theVal > 2147483647) {
							WE().util.showMessage(WE().consts.g_l.editorScript.field_int_value_to_height, WE().consts.message.WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
					case "float":
						if (isNaN(theVal)) {
							WE().util.showMessage(WE().consts.g_l.editorScript.field_int_value_to_height, WE().consts.message.WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
					case "weObject_input_length":
						if (!theVal.match(/^-{0,1}\d+$/) || theVal < 1 || theVal > 1023) {
							WE().util.showMessage(WE().consts.g_l.editorScript.field_input_contains_incorrect_length, WE().consts.message.WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
					case "weObject_int_length":
						if (!theVal.match(/^-{0,1}\d+$/) || theVal < 1 || theVal > 20) {
							WE().util.showMessage(WE().consts.g_l.editorScript.field_int_contains_incorrect_length, WE().consts.message.WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
				}
			}
		}

	}
	return document.forms[0].checkValidity();
}

function we_checkObjFieldname(i) {
	if (i.value.search(/^([a-zA-Z0-9_+-])*$/) || i.value === "0") {
		WE().util.showMessage(WE().consts.g_l.editorScript.fieldNameNotValid, WE().consts.message.WE_MESSAGE_ERROR, window);
		i.focus();
		i.select();
		i.value = i.getAttribute("oldValue");
	} else if (i.value === 'Title' || i.value === 'Description') {
		WE().util.showMessage(WE().consts.g_l.editorScript.fieldNameNotTitleDesc, WE().consts.message.WE_MESSAGE_ERROR, window);
		i.focus();
		i.select();
		i.value = i.getAttribute("oldValue");
	} else if (i.value.length === 0) {
		WE().util.showMessage(WE().consts.g_l.editorScript.fieldNameEmpty, WE().consts.message.WE_MESSAGE_ERROR, window);
		//		i.focus(); # 1052
		//		i.select();
		i.value = i.getAttribute("oldValue");
	} else {
		i.setAttribute("oldValue", i.value);
	}
}

function metaFieldSelectProposal(sel, inputName, isCsv) {
	_EditorFrame.setEditorIsHot(true);

	var valInput = document.forms[0].elements[inputName].value,
		newVal = valInput,
		valSel = sel.options[sel.selectedIndex].value;

	if (isCsv) {
		switch (valSel) {
			case '__del_last__':
				var arr = valInput.split(',');
				arr.pop();
				newVal = arr.join();
				break;
			case '__del__':
				newVal = '';
				break;
			case '__empty__':
				break;
			default:
				var valSelCsv = ', ' + valInput + ',';
				newVal = ((valInput === '' || (valSel === '')) ? valSel : (valSelCsv.search(' *, *' + valSel + ' *, *') === -1 ? (valInput + ', ' + valSel) : valInput));
		}
	} else {
		switch (valSel) {
			case '__del_last__':
			case '__del__':
				newVal = '';
				break;
			case '__empty__':
				break;
			default:
				newVal = sel.options[sel.selectedIndex].value;
		}
	}

	document.forms[0].elements[inputName].value = newVal;
	sel.selectedIndex = 0;
}

function changeOption(elem) {
	var cmnd = elem.options[elem.selectedIndex].value;
	if (cmnd) {
		switch (cmnd) {
			case "doImage_convertPNG":
			case "doImage_convertGIF":
				WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
		}
		we_cmd(cmnd, doc.we_transaction);
	}
	//elem.selectedIndex=0;
}

function reinitTiny(confName, transaction, isIEOpera) {
	var target = _EditorFrame.getContentEditor();
	var rawConfObject;

	/* if tinyMCE-field: re-write confObject on visible field and re-init editor
	 * ff and chrome only: on ie and opera we reload edit tab when saving properties
	 */
	if ((rawConfObject = typeof target.tinyMceRawConfigurations[confName] === 'object' ? target.tinyMceRawConfigurations[confName] : false)) {
		/* // probably obsolete, when we use rawConfObj
		 if (isIEOpera) {
		 if (typeof target.tinyMceRawConfigurations[confName] === 'object') {
		 for (var prop in rawConfObject) {
		 if (prop !== "setup") {
		 target.tinyMceRawConfigurations[confName][prop] = rawConfObject[prop];
		 }
		 }
		 WE().layout.we_tinyMCE.functions.initEditor(target, target.tinyMceRawConfigurations[confName], true);
		 } else {
		 setScrollTo();
		 top.we_cmd("switch_edit_page", 1, transaction);
		 }
		 } else {
		 */
		target.tinyMceRawConfigurations[confName] = rawConfObject;
		WE().layout.we_tinyMCE.functions.initEditor(target, target.tinyMceRawConfigurations[confName], true);
		//}
	} else if (typeof target.tinyMceRawConfigurations[confName] === 'object') {
		target.tinyMceRawConfigurations[confName] = undefined;
	}

}