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

function IsDigitPercent(e) {
	var key;
	if (e.charCode === undefined) {
		key = event.keyCode;
	} else {
		key = e.charCode;
	}

	return (((key >= 48) && (key <= 57)) || (key === 37) || (key === 0) || (key === 46) || (key === 101) || (key === 109) || (key === 13) || (key === 8) || (key <= 63235 && key >= 63232) || (key === 63272));
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function IsDigit(e) {
	var key = (e.charCode === undefined ?
					event.keyCode :
					e.charCode);

	return (((key >= 48) && (key <= 57)) || (key === 0) || (key === 13) || (key === 8) || (key <= 63235 && key >= 63232) || (key === 63272));
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
	var args = "";
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (arguments[0]) {
		case "we_selector_document":
		case "we_selector_image":
			new (WE().util.jsWindow)(window, url, "we_fileselector", -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(window, url, "browse_server", -1, -1, 840, 400, true, false, true);
			break;
		case "edit_new_collection":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=editNewCollection&we_cmd[1]=" + arguments[1] + "&we_cmd[2]=" + arguments[2] + "&fixedpid=" + arguments[3] + "&fixedremtable=" + arguments[4] + "&caller=" + arguments[5];
			new (WE().util.jsWindow)(window, url, "weNewCollection", -1, -1, 590, 560, true, true, true, true);
			break;
	}
}