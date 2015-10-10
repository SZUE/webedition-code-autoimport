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

function doUnload() {
	jsWindow.prototype.closeAll();
}

function we_cmd() {
	var url = top.WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (arguments[0]) {
		case "we_users_selector":
			new jsWindow(url, "browse_users", -1, -1, 500, 300, true, false, true);
			break;
		case "we_selector_directory":
			new jsWindow(url, "we_fileselector", -1, -1, top.WE().consts.size.windowDirSelect.width, top.WE().consts.size.windowDirSelect.height, true, true, true, true);
			break;
		case "we_selector_category":
			new jsWindow(url, "we_catselector", -1, -1, top.WE().consts.size.catSelect.width, top.WE().consts.size.catSelect.height, true, true, true, true);
			break;
		case "openObjselector":
			url = top.WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[8]=object&we_cmd[2]=" + top.WE().consts.tables.OBJECT_TABLE + "&we_cmd[5]=" + arguments[5] + "&we_cmd[9]=1";
			new jsWindow(url, "we_objectselector", -1, -1, top.WE().consts.size.docSelect.width, top.WE().consts.size.docSelect.height, true, true, true);
			break;
		case "add_cat":
		case "del_cat":
		case "del_all_cats":
			document.we_form.wcmd.value = arguments[0];
			document.we_form.wcat.value = arguments[1];
			submitForm();
			break;
		case "add_objcat":
		case "del_objcat":
		case "del_all_objcats":
			document.we_form.wcmd.value = arguments[0];
			document.we_form.wocat.value = arguments[1];
			submitForm();
			break;
		case "add_folder":
		case "del_folder":
		case "del_all_folders":
			document.we_form.wcmd.value = arguments[0];
			document.we_form.wfolder.value = arguments[1];
			submitForm();
			break;
		case "add_object_file_folder":
		case "del_object_file_folder":
		case "del_all_object_file_folders":
			document.we_form.wcmd.value = arguments[0];
			document.we_form.woffolder.value = arguments[1];
			submitForm();
			break;
		case "add_object":
		case "del_object":
		case "del_all_objects":
			document.we_form.wcmd.value = arguments[0];
			document.we_form.wobject.value = arguments[1];
			submitForm();
			break;
		case "switchPage":
			document.we_form.wcmd.value = arguments[0];
			document.we_form.page.value = arguments[1];
			submitForm();
			break;
		default:
			var args = [];
			for (i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.content.we_cmd.apply(this, args);
	}
}

function submitForm() {
	var f = self.document.we_form;
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
		top.we_showMessage(g_l.del_last_step, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function delTask() {
	if (document.we_form.wtasks.value > 1) {
		document.we_form.wtasks.value--;
		document.we_form.wcmd.value = "reload_table";
		submitForm();
	} else {
		top.we_showMessage(g_l.del_last_task, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}


function checkData() {
	var nsteps = document.we_form.wsteps;
	var ntasks = document.we_form.wtasks;
	if (document.we_form[uid + "_Text"].value === "") {
		top.we_showMessage(g_l.name_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	if (document.we_form[uid + "_Folders"].value === "" && document.we_form[uid + "_Type"].value == 1) {
		top.we_showMessage(g_l.folders_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	if (document.we_form[uid + "_ObjectFileFolders"].value === "" && document.we_form[uid + "_Type"].value == 2) {
		top.we_showMessage(g_l.folders_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	if ((document.we_form[uid + "_DocType"].value === 0 && document.we_form[uid + "_Categories"].value === "") && document.we_form[uid + "_Type"].value === 0) {
		top.we_showMessage(g_l.doctype_empty, WE().consts.message.WE_MESSAGE_ERROR, window);

		return false;
	}

	if (document.we_form[uid + "_Objects"].value === "" && document.we_form[uid + "_Type"].value == 2) {
		top.we_showMessage(g_l.objects_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	var _txt;
	for (i = 0; i < nsteps.value; i++) {
		if (document.we_form[uid + '_step' + i + '_Worktime'].value === "") {
			_txt = g_l.worktime_empty;
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
			_txt = g_l.user_empty;
			top.we_showMessage(_txt.replace(/%s/, i + 1), WE().consts.message.WE_MESSAGE_ERROR, window);
			return false;
		}

	}
	return true;
}