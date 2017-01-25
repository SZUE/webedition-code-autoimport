/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
'use strict';
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "openOrder":
			//TODO: check this adress: mit oder ohne tree? Bisher: left
			if (top.content.doClick) {
				top.content.doClick(args[1], args[2], args[3]);//TODO: check this adress
			}
			break;
		default:
			// not needed yet
			top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}