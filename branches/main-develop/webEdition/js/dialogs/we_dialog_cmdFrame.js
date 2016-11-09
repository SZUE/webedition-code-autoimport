/* global WE, top */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 12906 $
 * $Author: lukasimhof $
 * $Date: 2016-09-30 14:54:08 +0200 (Fr, 30 Sep 2016) $
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
var payload = WE().util.getDynamicVar(document, 'loadVarDialog_cmdFrame','data-payload');


function we_cmd() {
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
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}
