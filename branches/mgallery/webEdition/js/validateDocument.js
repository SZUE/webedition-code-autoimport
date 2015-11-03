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


function we_submitForm(target, url) {
	var f = self.document.we_form;
	f.target = target;
	f.action = url;
	f.method = "post";

	f.submit();
}

function we_cmd() {
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";

	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (arguments[0]) {
		case 'checkDocument':
			if (WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1].we_submitForm) {
				WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1].we_submitForm("validation", url);
			}
			break;
		default:
			var args = [];
			for (i = 0; i < arguments.length; i++)
			{
				args.push(arguments[i]);
			}
			parent.we_cmd.apply(this, args);

			break;
	}
}

function switchPredefinedService(name) {
	var f = self.document.we_form;

	f.host.value = host[name];
	f.path.value = path[name];
	f.ctype.value = ctype[name];
	f.varname.value = varname[name];
	f.additionalVars.value = additionalVars[name];
	f.checkvia.value = checkvia[name];
	f.s_method.value = s_method[name];


}
function setIFrameSize() {
	var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
	var w = window.innerWidth ? window.innerWidth : document.body.offsetWidth;
	w = Math.max(w, 680);
	var iframeWidth = w - 52;
	var validiframe = document.getElementById("validation");
	validiframe.style.width = iframeWidth + "px";
	if (h) { // h must be set (h!=0), if several documents are opened very fast -> editors are not loaded then => h = 0
		validiframe.style.height = (h - 185) + "px";
	}
}

