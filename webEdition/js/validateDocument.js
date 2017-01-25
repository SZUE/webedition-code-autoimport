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
var validate = WE().util.getDynamicVar(document, 'loadVarValidateDocument', 'data-validate');

function we_submitForm(target, url) {
	var f = window.document.we_form;
	if (!f.checkValidity()) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	f.target = target;
	f.action = url;
	f.method = "post";

	f.submit();
	return true;
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case 'checkDocument':
			if (WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1].we_submitForm) {
				WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1].we_submitForm("validation", url);
			}
			break;
		default:
			window.parent.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function switchPredefinedService(name) {
	var f = window.document.we_form;
	var el = validate[name];
	f.host.value = el.host;
	f.path.value = el.path;
	f.ctype.value = el.ctype;
	f.varname.value = el.varname;
	f.additionalVars.value = el.additionalVars;
	f.checkvia.value = el.checkvia;
	f.s_method.value = el.s_method;
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

