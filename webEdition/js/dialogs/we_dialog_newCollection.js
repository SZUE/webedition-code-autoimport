/* global WE, top */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 12706 $
 * $Author: mokraemer $
 * $Date: 2016-09-01 12:35:04 +0200 (Do, 01 Sep 2016) $
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
var dialog = WE().util.getDynamicVar(document, "loadVarWe_dialog_newCollection", "data-dialog");

var _EditorFrame = {};
_EditorFrame.setEditorIsHot = function(){};
var pathOfDocumentChanged = false;

function we_submitForm(url){
	var f = document.we_form;
	if (!f.checkValidity()) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	f.action = url;
	f.method = "post";
	f.submit();
	return true;
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_selector_directory":
			new (WE().util.jsWindow)(this, url, "we_fileselector", -1, -1,WE().consts.size.docSelect.width,WE().consts.size.docSelect.height, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(this, url, "we_catselector", -1, -1,WE().consts.size.docSelect.width,WE().consts.size.docSelect.height, true, true, true, true);
			break;
		case "close":
			window.close();
			break;
		case "save_notclose":
			if(document.we_form["we_" + top.dialog.name + "_Filename"].value){
				document.we_form["we_cmd[0]"].value = top.dialog.cmd;
				document.we_form["dosave"].value = 1;
				we_submitForm(top.dialog.scriptName);
			} else {
				alert("no name set");
			}
			break;
		case "do_onSuccess":
			var tmp = top.dialog.cmdOnSuccess.split(",");
			we_cmd.apply(this, tmp);
			break;
		case "write_back_to_opener":
			opener.we_form.elements[args[1]].value = top.dialog.data.id;
			opener.we_form.elements[args[2]].value = top.dialog.data.text;
			window.close();
			break;
		case "write_back_to_selector":
			opener.top.reloadDir();
			opener.top.unselectAllFiles();
			opener.top.doClick(top.dialog.data.id, 0);
			setTimeout(opener.top.selectFile, 200, top.dialog.data.text);
			window.close();
			break;
		default:
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}