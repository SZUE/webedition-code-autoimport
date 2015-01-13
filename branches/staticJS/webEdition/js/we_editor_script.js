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

var _controller = (opener && opener.top.weEditorFrameController) ? opener.top.weEditorFrameController : top.weEditorFrameController;

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
						_href.indexOf(linkPrefix.TYPE_OBJ_PREFIX) === 0 ||
						_href.indexOf(linkPrefix.TYPE_INT_PREFIX) === 0 ||
						_href.indexOf(linkPrefix.TYPE_MAIL_PREFIX) === 0 ||
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
		if (typeof (self.weEditorSetHiddenText) != "undefined") {
			self.weEditorSetHiddenText();
		}
	}
	f.submit();
}

function doUnload() {

	if (jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function updateCustomerFilterIfNeeded() {
	if ((_elem = document.we_form["we_" + docName + "_ParentID"])) {
		_parentid = _elem.value;
		if (_parentid !== _oldparentid) {
			top.YAHOO.util.Connect.asyncRequest('GET', '/webEdition/rpc/rpc.php?cmd=GetUpdateDocumentCustomerFilterQuestion&cns=customer&folderId=' + _parentid + '&we_transaction=' + we_transaction + '&table=' + docTable + '&classname=' + docClass, ajaxCallback);
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
		parent.frames[0].we_setPath(_filepath, _filetext, -1);
		if (hasCustomerFilter) {
			updateCustomerFilterIfNeeded();
		}
	}
}

// check if parentId was changed
var ajaxCallback = {
	success: function (o) {
		if (typeof (o.responseText) !== undefined && o.responseText !== '') {
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
				try {
					//console.log('error in return of GetUpdateDocumentCustomerFilterQuestion' + o.responseText);
				} catch (ex) {

				}

			}
		}
	},
	failure: function (o) {
	}
};

function setScrollTo() {
	parent.scrollToVal = isOldIE ? document.body.scrollTop : pageYOffset;
}

function goTemplate(tid) {
	if (tid > 0) {
		top.weEditorFrameController.openDocument(TEMPLATES_TABLE, tid, CTYPE_TEMPLATE);
	}
}

function we_cmd() {
	var args = "";
	var url = "/webEdition/we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURIComponent(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	var contentEditor = (top.weEditorFrameController === undefined ? opener.top : top).weEditorFrameController.getVisibleEditorFrame();

	switch (arguments[0]) {
		case "edit_link":
		case "edit_link_at_class":
		case "edit_link_at_object":
			new jsWindow("", "we_linkEdit", -1, -1, 615, 600, true, true, true);
			if (contentEditor.we_submitForm)
				contentEditor.we_submitForm("we_linkEdit", url);
			break;
		case "edit_linklist":
			new jsWindow("", "we_linklistEdit", -1, -1, 615, 600, true, true, true);
			if (contentEditor.we_submitForm)
				contentEditor.we_submitForm("we_linklistEdit", url);
			break;
		case "openColorChooser":
			new jsWindow("", "we_colorChooser", -1, -1, 430, 370, true, true, true);
			if (contentEditor.we_submitForm)
				contentEditor.we_submitForm("we_colorChooser", url);
			break;
		case "openDirselector":
		case "openDocselector":
			new jsWindow(url, "we_fileselector", -1, -1, winSelectSize.docSelect.width, winSelectSize.docSelect.height, true, true, true, true);
			break;
		case "openSelector":
			new jsWindow(url, "we_fileselector", -1, -1, 900, 685, true, true, true, true);
			break;
		case "openCatselector":
			new jsWindow(url, "we_catselector", -1, -1, winSelectSize.catSelect.width, winSelectSize.catSelect.height, true, true, true, true);
			break;
		case "browse_server":
			new jsWindow(url, "browse_server", -1, -1, 840, 400, true, false, true);
			break;
		case "browse_users":
			new jsWindow(url, "browse_users", -1, -1, 500, 300, true, false, true);
			break;
		case "object_editObjectTextArea":
			new jsWindow(url, "edit_object_text", -1, -1, 550, 455, true, false, true);
			break;
		case "editor_uploadFile":
			new jsWindow("", "we_uploadFile", -1, -1, 450, 320, true, true, true);
			if (contentEditor.we_submitForm)
				contentEditor.we_submitForm("we_uploadFile", url);
			break;
		case "open_templateSelect":
			new jsWindow("", "we_templateSelect", -1, -1, 600, 400, true, true, true);
			if (contentEditor.we_submitForm)
				contentEditor.we_submitForm("we_templateSelect", url);
			break;
		case "open_tag_wizzard":
			new jsWindow(url, "we_tag_wizzard", -1, -1, 600, 620, true, true, true);
			break;
		case "glossary_check":
			if (hasGlossary) {
				new jsWindow(url, "glossary_check", -1, -1, 730, 400, true, false, true);
			}
			break;
		case "add_thumbnail":
			new jsWindow(url, "we_add_thumbnail", -1, -1, 400, 410, true, true, true);
			break;
		case "image_resize":
			if (typeof CropTool == 'object' && CropTool.triggered)
				CropTool.drop();
			if (hasGD) {
				new jsWindow(url, "we_image_resize", -1, -1, 260, (gdType === "jpg" ? 250 : 190), true, false, true);
			} else {
				top.we_showMessage(g_l.gdTypeNotSupported, WE_MESSAGE_ERROR, window);
			}
			break;
		case "image_convertJPEG":
			if (typeof CropTool == 'object' && CropTool.triggered)
				CropTool.drop();
			new jsWindow(url, "we_convert_jpg", -1, -1, 260, 160, true, false, true);
			break;
		case "image_rotate":
			if (typeof CropTool == 'object' && CropTool.triggered) {
				CropTool.drop();
			}
			if (canRotate) {
				if (gdSupport) {
					new jsWindow(url, "we_rotate", -1, -1, 300, (gdType === "jpg" ? 230 : 170), true, false, true);
				} else {
					top.we_showMessage(g_l.gdTypeNotSupported, WE_MESSAGE_ERROR, window);
				}
			} else {
				top.we_showMessage(g_l.noRotate, WE_MESSAGE_ERROR, window);

			}
			break;

		case "image_crop":
			if (WE_EDIT_IMAGE) {
				if (gdSupport) {
					CropTool.crop();
				} else {
					top.we_showMessage(g_l.gdTypeNotSupported, WE_MESSAGE_ERROR, window);
				}
			}
			break;
		case "crop_cancel":
			CropTool.drop();
			break;
		case "spellcheck":
			if (WE_SPELLCHECKER_MODULE_DIR) {
				var win = new jsWindow(WE_SPELLCHECKER_MODULE_DIR + "/weSpellchecker.php?editname=" + (arguments[1]), "spellcheckdialog", -1, -1, 500, 450, true, false, true, false);
			}
			break;
			// it must be the last command
		case "delete_navi":
			for (var i = 0; i < arguments.length; i++) {
				arguments[i] = encodeURIComponent(arguments[i]);
			}
			if (!confirm(g_l.confirm_navDel)) {
				break;
			}
			//no break;
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += 'arguments[' + i + ']' + ((i < (arguments.length - 1)) ? ',' : '');
			}
			eval('parent.we_cmd(' + args + ')');
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
							top.we_showMessage(g_l.field_contains_incorrect_chars.replace(/%s/, theType), WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						} else if (theVal > 2147483647) {
							top.we_showMessage(g_l.field_int_value_to_height, WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
					case "float":
						if (isNaN(theVal)) {
							top.we_showMessage(g_l.field_int_value_to_height, WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
					case "weObject_input_length":
						if (!theVal.match(/^-{0,1}\d+$/) || theVal < 1 || theVal > 1023) {
							top.we_showMessage(g_l.field_input_contains_incorrect_length, WE_MESSAGE_ERROR, window);
							theInputs[i].focus();
							return false;
						}
						break;
					case "weObject_int_length":
						if (!theVal.match(/^-{0,1}\d+$/) || theVal < 1 || theVal > 20) {
							top.we_showMessage(g_l.field_int_contains_incorrect_length, WE_MESSAGE_ERROR, window);
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
		top.we_showMessage(g_l.fieldNameNotValid, WE_MESSAGE_ERROR, window);
		i.focus();
		i.select();
		i.value = i.getAttribute("oldValue");
	} else if (i.value === 'Title' || i.value === 'Description') {
		top.we_showMessage(g_l.fieldNameNotTitleDesc, WE_MESSAGE_ERROR, window);
		i.focus();
		i.select();
		i.value = i.getAttribute("oldValue");
	} else if (i.value.length === 0) {
		top.we_showMessage(g_l.fieldNameEmpty, WE_MESSAGE_ERROR, window);
		//		i.focus(); # 1052
		//		i.select();
		i.value = i.getAttribute("oldValue");
	} else {
		i.setAttribute("oldValue", i.value);
	}
}
