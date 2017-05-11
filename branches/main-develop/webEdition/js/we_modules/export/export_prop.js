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
		case 'switch_type':
			var type = args[1].value || WE().consts.import.TYPE_WE;
			var i = 0;
			var opts;

			var exportOptions = top.content.editor.edbody.document.getElementsByClassName('exportOptions');
			for(i = 0; i < exportOptions.length; i++){
				exportOptions[i].style.display = 'none';
			}

			var extensions = top.content.editor.edbody.document.getElementsByClassName('exportExtension');

			switch(type){
				case WE().consts.import.TYPE_XML:
					top.content.editor.edbody.document.we_form.SelectionType.value = 'doctype';
					top.content.editor.edbody.document.we_form.SelectionType.disabled = false;
					closeAllType();
					toggle('doctype');

					top.content.editor.edbody.document.we_form.headerSwitch.disabled = false;
					opts = top.content.editor.edbody.document.we_form.headerSwitch.options;
					for(i = 0; i < opts.length; i++){
						switch(opts[i].value){
							case WE().consts.tables.OBJECT_FILES_TABLE:
							case WE().consts.tables.FILE_TABLE:
								continue;
							default:
								opts[i].disabled = true;
						}
					}
					if(top.content.editor.edbody.document.we_form.headerSwitch.value !== WE().consts.tables.OBJECT_FILES_TABLE){
						setHead(WE().consts.tables.OBJECT_FILES_TABLE);
						top.content.editor.edbody.document.we_form.headerSwitch.value = WE().consts.tables.FILE_TABLE;
					}
					top.content.editor.edbody.document.getElementById('optionsGXML').style.display = 'block';

					for(i = 0; i < extensions.length; i++){
						extensions[i].innerHTML = '.xml';
					}
					top.content.editor.edbody.document.we_form.Extension.value = '.xml';
					break;
				case WE().consts.import.TYPE_CSV:
					top.content.editor.edbody.document.we_form.SelectionType.value = 'classname';
					top.content.editor.edbody.document.we_form.SelectionType.disabled = true;
					closeAllType();
					toggle('classname');

					setHead(WE().consts.tables.OBJECT_FILES_TABLE);
					top.content.editor.edbody.document.we_form.headerSwitch.value = WE().consts.tables.OBJECT_FILES_TABLE;
					top.content.editor.edbody.document.we_form.headerSwitch.disabled = true;
					top.content.editor.edbody.document.getElementById('optionsCSV').style.display = 'block';

					for(i = 0; i < extensions.length; i++){
						extensions[i].innerHTML = '.csv';
					}
					top.content.editor.edbody.document.we_form.Extension.value = '.csv';
					break;
				default:
					top.content.editor.edbody.document.we_form.SelectionType.value = 'doctype';
					top.content.editor.edbody.document.we_form.SelectionType.disabled = false;
					closeAllType();
					toggle('doctype');

					top.content.editor.edbody.document.we_form.headerSwitch.disabled = false;
					opts = top.content.editor.edbody.document.we_form.headerSwitch.options;
					for(i = 0; i < opts.length; i++){
						opts[i].disabled = false;
					}
					top.content.editor.edbody.document.getElementById('optionsWXML').style.display = 'block';

					for(i = 0; i < extensions.length; i++){
						extensions[i].innerHTML = '.xml';
					}
					top.content.editor.edbody.document.we_form.Extension.value = '.xml';
			}

			break;
		case "setTreeHead":
			document.we_form.XMLTable.value = args[1].replace(WE().consts.tables.TBL_PREFIX, '');
			setHead(args[1]);
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
		case 'submitCmdForm':
			top.content.cmd.document.we_form.submit();
			break;
		case 'startDownload':
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=export&pnt=cmd&cmd=upload&exportfile=' + args[1];
			break;
		case 'setStatusEnd':
			showEndStatus();
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
