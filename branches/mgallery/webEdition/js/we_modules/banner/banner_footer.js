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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function doUnload() {
	jsWindow.prototype.closeAll();
}

function we_cmd() {
	var url = top.WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURIComponent(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (arguments[0]) {
		case "empty_log":
			break;
		default:
			var args = [];
			for (i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			parent.edbody.we_cmd.apply(this, args);
	}
}

function we_save() {
	var acLoopCount = 0;
	var acIsRunning = false;
	if (!!top.content.editor.edbody.YAHOO && !!top.content.editor.edbody.YAHOO.autocoml) {
		while (acLoopCount < 20 && top.content.editor.edbody.YAHOO.autocoml.isRunnigProcess()) {
			acLoopCount++;
			acIsRunning = true;
			setTimeout(we_save, 100);
		}
		if (!acIsRunning) {
			if (top.content.editor.edbody.YAHOO.autocoml.isValid()) {
				_we_save();
			} else {
				top.we_showMessage(top.WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		}
	} else {
		_we_save();
	}
}