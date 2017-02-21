/* global WE, top,weDoOk */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 13357 $
 * $Author: lukasimhof $
 * $Date: 2017-02-13 22:29:45 +0100 (Mo, 13 Feb 2017) $
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

var payload = WE().util.getDynamicVar(document, 'loadVarDialog', 'data-vars');

document.addEventListener("keyup", doKeyDown, true);
function doKeyDown(e) {
	switch (e.charCode) {
		case 27:
			top.close();
			break;
	}
}

function weTinyDialog_doOk() {
	top.WefullscreenDialog.writeback();
	top.close();
}

function doUnload() {
	return;
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		default:
			window.opener.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}
