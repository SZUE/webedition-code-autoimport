/* global WE, top, LinkDialog, ImageDialog */

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
var payload = WE().util.getDynamicVar(document, 'loadVarDialog_cmdFrame','data-payload');


function we_cmd() {
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "image_update_editor":
			top.update_editor(payload);
			break;
		case 'image_writeback':
			ImageDialog.writeBack(payload.attributes);
			break;
		case 'link_writeback':
			LinkDialog.writeBack(payload.attributes);
			break;
		default:
			top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}
