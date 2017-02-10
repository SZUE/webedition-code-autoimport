/* global WE, top */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 13291 $
 * $Author: mokraemer $
 * $Date: 2017-01-27 17:52:22 +0100 (Fr, 27 Jan 2017) $
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
document.addEventListener("keyup", doKeyDown, true);

function doKeyDown(e) {top.console.log(e.charCode);
	switch (e.charCode) {
		case 27:
			top.close();
			break;
	}
}

function weDoOk() {
	top.opener.tinyMCECallRegisterDialog({}, "unregisterDialog");
	top.WefullscreenDialog.writeback();
	top.close();
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}
