/* global WE */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
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
var loaded;
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_users_selector":
			new (WE().util.jsWindow)(window, url, "browse_users", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(window, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(window, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "openObjselector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[2]=" + WE().consts.tables.OBJECT_TABLE + "&we_cmd[5]=" + args[5] + "&we_cmd[8]=object&we_cmd[9]=1";
			new (WE().util.jsWindow)(window, url, "we_objectselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true);
			break;
		case "add_cat":
		case "add_objcat":
			document.we_form.wcmd.value = args[0];
			document.we_form.wcat.value = args[1].allIDs.join(",");
			submitForm();
			break;
		case "del_cat":
		case "del_all_cats":
			document.we_form.wcmd.value = args[0];
			document.we_form.wcat.value = args[1];
			submitForm();
			break;
		case "del_objcat":
		case "del_all_objcats":
			document.we_form.wcmd.value = args[0];
			document.we_form.wocat.value = args[1];
			submitForm();
			break;
		case "add_folder":
		case "add_object":
		case "add_object_file_folder":
			document.we_form.wcmd.value = args[0];
			document.we_form.wfolder.value = args[1].allIDs.join(',');
			submitForm();
			break;
		case "del_folder":
		case "del_all_folders":
			document.we_form.wcmd.value = args[0];
			document.we_form.wfolder.value = args[1];
			submitForm();
			break;
		case "del_object_file_folder":
		case "del_all_object_file_folders":
			document.we_form.wcmd.value = args[0];
			document.we_form.woffolder.value = args[1];
			submitForm();
			break;
		case "del_object":
		case "del_all_objects":
			document.we_form.wcmd.value = args[0];
			document.we_form.wobject.value = args[1];
			submitForm();
			break;
		case "switchPage":
			document.we_form.wcmd.value = args[0];
			document.we_form.page.value = args[1];
			submitForm();
			break;
		default:
			top.content.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

function submitForm() {
	var f = window.document.we_form;
	f.action = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=workflow";
	f.target = "edbody";
	f.method = "post";
	f.submit();
}

function setStatus(val) {
	document.we_form[uid + "_Status"].value = val;

}

function getStatusContol() {
	return document.we_form[uid + "_Status"].value;
}

function clickCheck(a) {
	a.value = (a.checked ? 1 : 0);
}

function addStep() {
	document.we_form.wsteps.value++;
	document.we_form.wcmd.value = "reload_table";
	submitForm();
}

function addTask() {
	document.we_form.wtasks.value++;
	document.we_form.wcmd.value = "reload_table";
	submitForm();
}


function delStep() {
	if (document.we_form.wsteps.value > 1) {
		document.we_form.wsteps.value--;
		document.we_form.wcmd.value = "reload_table";
		submitForm();
	} else {
		top.we_showMessage(WE().consts.g_l.workflow.prop.del_last_step, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function delTask() {
	if (document.we_form.wtasks.value > 1) {
		document.we_form.wtasks.value--;
		document.we_form.wcmd.value = "reload_table";
		submitForm();
	} else {
		top.we_showMessage(WE().consts.g_l.workflow.prop.del_last_task, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}


function checkData() {
	var nsteps = document.we_form.wsteps;
	var ntasks = document.we_form.wtasks;
	if (document.we_form[uid + "_Text"].value === "") {
		top.we_showMessage(WE().consts.g_l.workflow.prop.name_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	if (document.we_form[uid + "_Folders"].value === "" && document.we_form[uid + "_Type"].value == 1) {
		top.we_showMessage(WE().consts.g_l.workflow.prop.folders_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	if (document.we_form[uid + "_ObjectFileFolders"].value === "" && document.we_form[uid + "_Type"].value == 2) {
		top.we_showMessage(WE().consts.g_l.workflow.prop.folders_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	if ((document.we_form[uid + "_DocType"].value === 0 && document.we_form[uid + "_Categories"].value === "") && document.we_form[uid + "_Type"].value === 0) {
		top.we_showMessage(WE().consts.g_l.workflow.prop.doctype_empty, WE().consts.message.WE_MESSAGE_ERROR, window);

		return false;
	}

	if (document.we_form[uid + "_Objects"].value === "" && document.we_form[uid + "_Type"].value == 2) {
		top.we_showMessage(WE().consts.g_l.workflow.prop.objects_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	var _txt;
	for (i = 0; i < nsteps.value; i++) {
		if (document.we_form[uid + '_step' + i + '_Worktime'].value === "") {
			_txt = WE().consts.g_l.workflow.prop.worktime_empty;
			top.we_showMessage(_txt.replace(/%s/, i + 1), WE().consts.message.WE_MESSAGE_ERROR, window);
			return false;
		}
		userempty = true;
		for (j = 0; j < ntasks.value; j++) {
			if (document.we_form[uid + '_task_' + i + '_' + j + '_userid'].value !== 0) {
				userempty = false;
			}
		}
		if (userempty) {
			_txt = WE().consts.g_l.workflow.prop.user_empty;
			top.we_showMessage(_txt.replace(/%s/, i + 1), WE().consts.message.WE_MESSAGE_ERROR, window);
			return false;
		}

	}
	return true;
}