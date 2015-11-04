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

function we_cmd() {
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	if(typeof arguments[0] === "object" && arguments[0]["we_cmd[0]"] !== undefined){
		var args = {}, i = 0, tmp = arguments[0];
		url += Object.keys(tmp).map(function(key){args[key] = tmp[key]; args[i++] = tmp[key]; return key + "=" + encodeURIComponent(tmp[key]);}).join("&");
	} else {
		var args = Array.prototype.slice.call(arguments);
		for (var i = 0; i < args.length; i++) {
			url += "we_cmd[" + i + "]=" + encodeURIComponent(args[i]) + (i < (args.length - 1) ? "&" : "");
		}
	}

	switch (args[0]) {
		case "customValidationService":
			self.we_submitForm(url);
			we_cmd("reload_editpage");
			break;
		case "reload_editpage":
			if (WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1].we_cmd) {
				WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1].we_cmd("reload_editpage");
			}
			window.focus();
			break;
		case "close":
			window.close();
			break;
		default :
			top.opener.we_cmd.apply(this, arguments);
			break;
	}
}

function we_submitForm(url) {
	var f = self.document.we_form;

	f.action = url;
	f.method = "post";

	f.submit();
}
