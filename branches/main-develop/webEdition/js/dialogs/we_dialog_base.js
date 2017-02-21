/* global WE, top,weDoOk */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
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
'use strict';

var dialogVars = WE().util.getDynamicVar(document, 'loadVarDialog', 'data-vars');
var isWeDialog = true;
var dialogLoaded = false;

function weDialogBase_setLoaded(){
	dialogLoaded = true;
}

function doUnload() {
	if(WE() && WE().util){
		WE().util.jsWindow.prototype.closeAll(window);
	}
}

function weSaveToGlossaryFn() {
	document.we_form.elements.weSaveToGlossary.value = 1;
	document.we_form.submit();
}

function doKeyDown(e) {
	switch (e.keyCode) {
		case 27:
			top.close();
			break;
		case 13:
			if (dialogVars.onEnterKey) {
				weDoOk();
			}
			break;
	}
}

function weDoOk() {
	if(dialogVars.onEnterKey){
		if(weTinyDialog_doOk){
			weTinyDialog_doOk();
		} else if(weDialog_doOk){ // for use in other then tiny-dialogs
			weDialog_doOk();
		} else {
			WE().t_e('we_dialog_base.js: missing function doOk()');
		}
	}
}

function addKeyListener() {
	document.addEventListener("keyup", doKeyDown, true);
}

function openExtSource(argName) {
	if (argName && window.document.we_form.elements['we_dialog_args[' + argName + ']']) {
		var val = window.document.we_form.elements['we_dialog_args[' + argName + ']'].value;
		if (val && val !== '" . we_base_link::EMPTY_EXT . "') {
			window.open(val);
		}
	}
}

function we_cmd_dialogBase() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_selector_document":
		case "we_selector_image":
			new (WE().util.jsWindow)(caller, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(caller, url, "we_cateditor", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(caller, url, "browse_server", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "edit_new_collection":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=editNewCollection&we_cmd[1]=" + args[1] + "&we_cmd[2]=" + args[2] + "&fixedpid=" + args[3] + "&fixedremtable=" + args[4] + "&caller=" + args[5];
			new (WE().util.jsWindow)(caller, url, "weNewCollection", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "setFocus":
			var elem = document.forms[0].elements[args[1]];
			elem.focus();
			elem.select();
			break;
		default:
			window.opener.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

//is executed in case nothing else is present. call we_cmd_dialogBase if you override this
function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);

	window.we_cmd_dialogBase.apply(caller, Array.prototype.slice.call(arguments));
}

addKeyListener();
window.focus();