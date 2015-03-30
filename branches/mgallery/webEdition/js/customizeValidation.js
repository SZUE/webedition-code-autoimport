/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 8967 $
 * $Author: mokraemer $
 * $Date: 2015-01-13 12:56:41 +0100 (Di, 13. Jan 2015) $
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

	var args = "";
	var url = "/webEdition/we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	switch (arguments[0]) {

		case "customValidationService":
			self.we_submitForm(url);
			we_cmd("reload_editpage");
			break;
		case "reload_editpage":
			if (top.opener.top.weEditorFrameController.getActiveDocumentReference().frames[1].we_cmd) {
				top.opener.top.weEditorFrameController.getActiveDocumentReference().frames[1].we_cmd("reload_editpage");
			}
			window.focus();
			break;
		case "close":
			window.close();
			break;
		default :
			var args = [];
			for (var i = 0; i < arguments.length; i++)
			{
				args.push(arguments[i]);
			}
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
