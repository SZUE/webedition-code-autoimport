/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 13894 $
 * $Author: mokraemer $
 * $Date: 2017-06-27 18:38:14 +0200 (Di, 27. Jun 2017) $
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
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);
	var i;

	switch (args[0]) {
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));

	}
}


function submitForm(target, action, method, form) {
	var f = form ? window.document.forms[form] : window.document.we_form;
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=doctype");
	f.method = (method ? method : "post");

	f.submit();
}