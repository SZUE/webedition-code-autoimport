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

var _controller = WE().layout.weEditorFrameController;

var _EditorFrame = _controller.getEditorFrame(parent.name);
if (!_EditorFrame) {
	_EditorFrame = (we_transaction ?
					_controller.getEditorFrameByTransaction(we_transaction) :
					_controller.getEditorFrame());

}

function we_rpc_dw_onload() {
	we_cmd = function () {
	};
	we_submitForm = function () {
	};
	doUnload = function () {
	};
}


function seeMode_dealWithLinks() {
	var _aTags = document.getElementsByTagName("a");

	for (i = 0; i < _aTags.length; i++) {
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
	var arr = allvalues.split(",");

	for (var v in arr) {
		w = allnames + '[' + arr[v] + ']';
		e = document.getElementById(w);
		e.style.display = 'block';
	}
	w = allnames + '[' + deselect + ']';
	e = document.getElementById(w);
	e.style.display = 'none';


}

function weDelCookie(name, path, domain) {
	if (getCookie(name)) {
		document.cookie = name + "=" +
						((path === null) ? "" : "; path=" + path) +
						((domain === null) ? "" : "; domain=" + domain) +
						"; expires=Thu, 01-Jan-70 00:00:01 GMT";
	}
}

function doScrollTo() {
	if (parent.scrollToVal) {
		window.scrollTo(0, parent.scrollToVal);
		parent.scrollToVal = 0;
	}
}

function translate(c) {
	f = c.form;
	n = c.name;
	n2 = n.replace(/tmp_/, "we_");
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
	var f = self.document.we_form;

	parent.openedWithWe = true;

	if (target && url) {
		f.target = target;
		f.action = url;
		f.method = "post";
		if (self.weWysiwygSetHiddenText && _EditorFrame.getEditorDidSetHiddenText() === false) {
			weWysiwygSetHiddenText();
		} else if (_EditorFrame.getEditorDidSetHiddenText()) {
			_EditorFrame.setEditorDidSetHiddenText(false);
		}
	}
	f.submit();
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function updateCustomerFilterIfNeeded() {
	if ((_elem = document.we_form["we_" + docName + "_ParentID"])) {
		_parentid = _elem.value;
		if (_parentid !== _oldparentid) {
			top.YAHOO.util.Connect.asyncRequest('GET', WE().consts.dirs.WEBEDITION_DIR + 'rpc/rpc.php?cmd=GetUpdateDocumentCustomerFilterQuestion&cns=customer&folderId=' + _parentid + '&we_transaction=' + we_transaction + '&table=' + docTable + '&classname=' + docClass, ajaxCallback);
			_oldparentid = _parentid;
		}
	}
}

// check If Filename was changed..
function pathOfDocumentChanged() {
	var _filetext = '';
	var _filepath = '';
	var elem = false;

	elem = document.we_form["we_" + docName + "_Filename"]; // documents
	if (!elem) { // object
		elem = document.we_form["we_" + docName + "_Text"];
	}

	if (elem) {

		// text
		_filetext = elem.value;
		// Extension if there
		if (document.we_form["we_" + docName + "_Extension"]) {
			_filetext += document.we_form["we_" + docName + "_Extension"].value;
		}

		// path
		if ((_elem = document.we_form["we_" + docName + "_ParentPath"])) {
			_filepath = _elem.value;
		}
		if (_filepath != "/") {
			_filepath += "/";
		}

		_filepath += _filetext;
		parent.frames.editHeader.we_setPath(_filepath, _filetext, -1, "");
		if (hasCustomerFilter) {
			updateCustomerFilterIfNeeded();
		}
	}
}

// check if parentId was changed
var ajaxCallback = {
	success: function (o) {
		if (o.responseText !== undefined && o.responseText !== '') {
			var weResponse = false;
			try {
				eval(o.responseText);
				if (weResponse) {
					if (weResponse.data === "true") {
						_question = g_l.confirm_applyFilter;
						if (confirm(_question)) {
							top.we_cmd("customer_applyWeDocumentCustomerFilterFromFolder");
						}
					}
				}
			} catch (exc) {
			}
		}
	},
	failure: function (o) {
	}
};

function setScrollTo() {
	parent.scrollToVal = pageYOffset;
}

function goTemplate(tid) {
	if (tid > 0) {
		WE().layout.weEditorFrameController.openDocument(TEMPLATES_TABLE, tid, CTYPE_TEMPLATE);
	}
}

function we_cmd() {
	var args = [];
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		args.push(arguments[i]);
		url += "we_cmd[]=" + encodeURIComponent(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	var contentEditor = WE().layout.weEditorFrameController.getVisibleEditorFrame();

	switch (args[0]) {
		case "edit_link":
		case "edit_link_at_class":
		case "edit_link_at_object":
			new (WE().util.jsWindow)(window, "", "we_linkEdit", -1, -1, 615, 600, true, true, true);
			if (contentEditor.we_submitForm)
				contentEditor.we_submitForm("we_linkEdit", url);
			break;
		case "edit_linklist":
			new (WE().util.jsWindow)(window, "", "we_linklistEdit", -1, -1, 615, 600, true, true, true);
			if (contentEditor.we_submitForm)
				contentEditor.we_submitForm("we_linklistEdit", url);
			break;
		case "openColorChooser":
			new (WE().util.jsWindow)(window, "", "we_colorChooser", -1, -1, 430, 370, true, true, true);
			if (contentEditor.we_submitForm)
				contentEditor.we_submitForm("we_colorChooser", url);
			break;
		case "we_selector_directory":
		case "we_selector_document":
		case "we_selector_image":
			new (WE().util.jsWindow)(window, url, "we_fileselector", -1, -1, winSelectSize.docSelect.width, winSelectSize.docSelect.height, true, true, true, true);
			break;
		case "we_customer_selector":
		case "we_selector_file":
			new (WE().util.jsWindow)(window, url, "we_fileselector", -1, -1, 900, 685, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(window, url, "we_catselector", -1, -1, winSelectSize.catSelect.width, winSelectSize.catSelect.height, true, true, true, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(window, url, "browse_server", -1, -1, 840, 400, true, false, true);
			break;
		case "we_users_selector":
			new (WE().util.jsWindow)(window, url, "browse_users", -1, -1, 500, 300, true, false, true);
			break;
		case "object_editObjectTextArea":
			new (WE().util.jsWindow)(window, url, "edit_object_text", -1, -1, 550, 455, true, false, true);
			break;
		case "open_templateSelect":
			new (WE().util.jsWindow)(window, "", "we_templateSelect", -1, -1, 600, 400, true, true, true);
			if (contentEditor.we_submitForm)
				contentEditor.we_submitForm("we_templateSelect", url);
			break;
		case "open_tag_wizzard":
			new (WE().util.jsWindow)(window, url, "we_tag_wizzard", -1, -1, 600, 620, true, true, true);
			break;
		case "glossary_check":
			if (hasGlossary) {
				new (WE().util.jsWindow)(window, url, "glossary_check", -1, -1, 730, 400, true, false, true);
			}
			break;
		case "add_thumbnail":
			new (WE().util.jsWindow)(window, url, "we_add_thumbnail", -1, -1, 400, 410, true, true, true);
			break;
		case "image_resize":
			if (hasGD) {
				ImageEditTools.Resize.start(url, gdType);
			} else {
				top.we_showMessage(g_l.gdTypeNotSupported, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			break;
		case "image_convertJPEG":
			ImageEditTools.ConvertJPEG.start(url);
			break;
		case "image_rotate":
			if (canRotate) {
				if (gdSupport) {
					ImageEditTools.Rotate.start(url, gdType);
				} else {
					top.we_showMessage(g_l.gdTypeNotSupported, WE().consts.message.WE_MESSAGE_ERROR, window);
				}
			} else {
				top.we_showMessage(g_l.noRotate, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			break;
		case "image_focus":
			ImageEditTools.Focus.start();
			break;
		case "image_crop":
			if (WE_EDIT_IMAGE) {
				if (gdSupport) {
					ImageEditTools.Crop.crop();
				} else {
					top.we_showMessage(g_l.gdTypeNotSupported, WE().consts.message.WE_MESSAGE_ERROR, window);
				}
			}
			break;
		case "crop_cancel":
			imageEditTools.deactivateAll();
			break;
		case "imageEditTools_reset":
			ImageEditTools.deactivateAll();
			break;
		case "image_convertGIF":
		case "image_convertPNG":
			ImageEditTools().deactivateAll();
			parent.we_cmd.apply(this, args);
			break;
		case "spellcheck":
			if (WE_SPELLCHECKER_MODULE_DIR) {
				var win = new (WE().util.jsWindow)(window, WE_SPELLCHECKER_MODULE_DIR + "/weSpellchecker.php?editname=" + (arguments[1]), "spellcheckdialog", -1, -1, 500, 450, true, false, true, false);
			}
			break;
			// it must be the last command
		case "delete_navi":
			for (i = 0; i < arguments.length; i++) {
				arguments[i] = encodeURIComponent(arguments[i]);
			}
			if (!confirm(g_l.confirm_navDel)) {
				break;
			}
			//no break;
		default:
			parent.we_cmd.apply(this, args);

	}
}


function fields_are_valid() {
	var _retVal = true;
	var objFieldErrorMsg = "";
	if (isWEObject) {

		var theInputs = document.getElementsByTagName("input");

		for (i = 0; i < theInputs.length; i++) {

			if ((theType = theInputs[i].getAttribute("weType")) && (theVal = theInputs[i].value)) {

				switch (theType) {
					case "int":
					case "integer":
						if (!theVal.match(/^-{0,1}\d+$/)) {
							top.we_showMessage(g_l.field_contains_incorrect_chars.replace(/%s/, theType), WE().consts.message.WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						} else if (theVal > 2147483647) {
							top.we_showMessage(g_l.field_int_value_to_height, WE().consts.message.WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
					case "float":
						if (isNaN(theVal)) {
							top.we_showMessage(g_l.field_int_value_to_height, WE().consts.message.WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
					case "weObject_input_length":
						if (!theVal.match(/^-{0,1}\d+$/) || theVal < 1 || theVal > 1023) {
							top.we_showMessage(g_l.field_input_contains_incorrect_length, WE().consts.message.WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
					case "weObject_int_length":
						if (!theVal.match(/^-{0,1}\d+$/) || theVal < 1 || theVal > 20) {
							top.we_showMessage(g_l.field_int_contains_incorrect_length, WE().consts.message.WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
				}
			}
		}

	}
	return true;
}

function we_checkObjFieldname(i) {
	if (i.value.search(/^([a-zA-Z0-9_+-])*$/)) {
		top.we_showMessage(g_l.fieldNameNotValid, WE().consts.message.WE_MESSAGE_ERROR, window);
		i.focus();
		i.select();
		i.value = i.getAttribute("oldValue");
	} else if (i.value === 'Title' || i.value === 'Description') {
		top.we_showMessage(g_l.fieldNameNotTitleDesc, WE().consts.message.WE_MESSAGE_ERROR, window);
		i.focus();
		i.select();
		i.value = i.getAttribute("oldValue");
	} else if (i.value.length === 0) {
		top.we_showMessage(g_l.fieldNameEmpty, WE().consts.message.WE_MESSAGE_ERROR, window);
		//		i.focus(); # 1052
		//		i.select();
		i.value = i.getAttribute("oldValue");
	} else {
		i.setAttribute("oldValue", i.value);
	}
}
