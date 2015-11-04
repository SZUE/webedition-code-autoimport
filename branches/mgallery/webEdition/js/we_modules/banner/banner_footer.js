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
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getArgsUrl(args);
	var arguments = args;

	switch (args[0]) {
		case "empty_log":
			break;
		default:
			parent.edbody.we_cmd.apply(this, arguments);
	}
}

function we_save() {
	var acLoopCount = 0;
	var acIsRunning = false;
	if (top.content.editor.edbody.YAHOO !== undefined && top.content.editor.edbody.YAHOO.autocoml !== undefined) {
		while (acLoopCount < 20 && top.content.editor.edbody.YAHOO.autocoml.isRunnigProcess()) {
			acLoopCount++;
			acIsRunning = true;
			setTimeout(we_save, 100);
		}
		if (!acIsRunning) {
			if (top.content.editor.edbody.YAHOO.autocoml.isValid()) {
				_we_save();
			} else {
				top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		}
	} else {
		_we_save();
	}
}