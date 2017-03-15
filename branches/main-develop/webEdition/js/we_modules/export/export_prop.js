/* global top, WE */

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
var loaded = false;

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "switchPage":
			document.we_form.cmd.value = args[0];
			document.we_form.tabnr.value = args[1];
			submitForm();
			break;
		case "we_export_dirSelector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
			new (WE().util.jsWindow)(caller, url, "we_exportselector", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "add_cat":
			document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.pnt.value = "edbody";
			document.we_form.tabnr.value = top.content.activ_tab;
			document.we_form.cat.value = args[1].allIDs.join(",");
			submitForm();
			break;
		case "del_cat":
		case "del_all_cats":
			document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.pnt.value = "edbody";
			document.we_form.tabnr.value = top.content.activ_tab;
			document.we_form.cat.value = args[1];
			submitForm();
			break;
		case "updateLog":
			for (var i = 0; i < args[1].log.length; i++) {
				top.content.editor.edbody.addLog(args[1].log[i]);
			}
			top.content.editor.edfooter.setProgress(args[1].percent);
			top.content.editor.edfooter.setProgressText("current_description", args[1].text);
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function submitForm(target, action, method) {
	var f = window.document.we_form;
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=export');
	f.method = (method ? method : "post");
	f.submit();
}

function toggle(id) {
	var elem = document.getElementById(id);
	if (elem.style.display == "none") {
		elem.style.display = "";
	} else {
		elem.style.display = "none";
	}
}

function clearLog() {
	top.content.editor.edbody.document.getElementById("log").innerHTML = "";
}

function addLog(text) {
	top.content.editor.edbody.document.getElementById("log").innerHTML += text + "<br/>";
	top.content.editor.edbody.document.getElementById("log").scrollTop = 50000;
}

function closeAllSelection() {
	var elem = document.getElementById("auto");
	elem.style.display = "none";
	elem = document.getElementById("manual");
	elem.style.display = "none";
}

function closeAllType() {
	var elem = document.getElementById("doctype");
	elem.style.display = "none";
	if (WE().consts.modules.active.indexOf("object") > 0) {
		elem = document.getElementById("classname");
		elem.style.display = "none";
	}
}

function formFileChooser() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "browse_server":
			new (WE().util.jsWindow)(window, url, "server_selector", WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, false, true);
			break;
	}
}

function showEndStatus() {
	WE().util.showMessage(WE().consts.g_l.exports.server_finished, WE().consts.message.WE_MESSAGE_NOTICE, window);
	top.content.editor.edfooter.hideProgress();
}
