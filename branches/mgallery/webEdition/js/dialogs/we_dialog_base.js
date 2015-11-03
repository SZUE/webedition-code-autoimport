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

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function weSaveToGlossaryFn() {
	document.we_form.elements.weSaveToGlossary.value = 1;
	document.we_form.submit();
}

function doKeyDown(e) {
	var key = e.keyCode === undefined ? event.keyCode : e.keyCode;

	switch (key) {
		case 27:
			top.close();
			break;
		case 13:
			if (onEnterKey) {
				if (!textareaFocus) {
					weDoOk();
				}
			}
			break;
	}
}

function addKeyListener() {
	if (document.addEventListener) {
		document.addEventListener("keyup", doKeyDown, true);
	} else {
		document.onkeydown = doKeyDown;
	}
}

function we_cmd() {
	var scope = window,
		args = [],
		url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?",
		i = 0;

	if(typeof arguments[0] === 'object'){
		scope = arguments[0];
		i++;
	}

	for (i; i < arguments.length; i++) {
		args.push(arguments[i]);
		url += "we_cmd[]=" + escape(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (args[0]) {
		case "we_selector_document":
		case "we_selector_image":
			new (WE().util.jsWindow)(scope, url, "we_fileselector", -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(scope, url, "we_cateditor", -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.height, true, true, true, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(scope, url, "browse_server", -1, -1, 840, 400, true, false, true);
			break;
		case "edit_new_collection":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=editNewCollection&we_cmd[1]=" + args[1] + "&we_cmd[2]=" + args[2] + "&fixedpid=" + arguments[3] + "&fixedremtable=" + arguments[4] + "&caller=" + arguments[5];
			new (WE().util.jsWindow)(scope, url, "weNewCollection", -1, -1, 590, 560, true, true, true, true);
			break;
		default:
			args.unshift(scope);
			opener.we_cmd.apply(this, args);
	}
}