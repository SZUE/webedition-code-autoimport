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
	var args=[];
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[]=" + encodeURI(arguments[i]);
		args.push(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
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
			top.opener.we_cmd.apply(this, args);
			break;
	}
}

function we_submitForm(url) {
	var f = self.document.we_form;

	f.action = url;
	f.method = "post";

	f.submit();
}
