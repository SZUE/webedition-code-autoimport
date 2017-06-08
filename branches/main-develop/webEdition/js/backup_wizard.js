/* global WE, top */

/**
 * webEdition CMS
 *
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

WE().util.loadConsts(document, "g_l.backupWizard");
var backup = WE().util.getDynamicVar(document, 'loadVarBackup_wizard', 'data-backup');

function we_submitForm(target, url) {
	var f = window.document.we_form;
	if (!f.checkValidity()) {
		WE().util.showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
	return true;
}

function doClicked(checked, opt) {
	if (checked) {

		switch (opt) {
			case 101:
				if (!document.we_form.handle_core.checked) {
					document.we_form.handle_core.value = 1;
					document.we_form.handle_core.checked = true;
					WE().util.showMessage(WE().consts.g_l.backupWizard[backup.mode].temporary_dep, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
				break;
			case 12:
				if (!document.we_form.handle_core.checked || !document.we_form.handle_object.checked) {
					document.we_form.handle_core.value = 1;
					document.we_form.handle_core.checked = true;
					document.we_form.handle_object.value = 1;
					document.we_form.handle_object.checked = true;
					WE().util.showMessage(WE().consts.g_l.backupWizard[backup.mode].versions_dep, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
				break;
			case 13:
				if (!document.we_form.handle_core.checked || !document.we_form.handle_object.checked || !document.we_form.handle_versions.checked) {
					document.we_form.handle_core.value = 1;
					document.we_form.handle_core.checked = true;
					document.we_form.handle_object.value = 1;
					document.we_form.handle_object.checked = true;
					document.we_form.handle_versions.value = 1;
					document.we_form.handle_versions.checked = true;
					WE().util.showMessage(WE().consts.g_l.backupWizard[backup.mode].versions_binarys_dep, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
				break;
			case 14:
				if (!document.we_form.handle_core.checked) {
					document.we_form.handle_core.value = 1;
					document.we_form.handle_core.checked = true;
					WE().util.showMessage(WE().consts.g_l.backupWizard[backup.mode].binary_dep, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
				break;
			case 55:
				if (!document.we_form.handle_core.checked || !document.we_form.handle_object.checked) {
					document.we_form.handle_core.value = 1;
					document.we_form.handle_core.checked = true;
					document.we_form.handle_object.value = 1;
					document.we_form.handle_object.checked = true;
					WE().util.showMessage(WE().consts.g_l.backupWizard[backup.mode].schedule_dep, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
				break;
			case 30:
				if (WE().consts.modules.active.indexOf("shop") > 0 && WE().consts.modules.active.indexOf("customer") > 0 && !document.we_form.handle_customer.checked) {
					document.we_form.handle_customer.value = 1;
					document.we_form.handle_customer.checked = true;
					WE().util.showMessage(WE().consts.g_l.backupWizard[backup.mode].shop_dep, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
				break;
			case 35:
				if (WE().consts.modules.active.indexOf("workflow") > 0 && !document.we_form.handle_user.checked || !document.we_form.handle_core.checked) {
					document.we_form.handle_core.value = 1;
					document.we_form.handle_core.checked = true;
					document.we_form.handle_user.value = 1;
					document.we_form.handle_user.checked = true;
					WE().util.showMessage(WE().consts.g_l.backupWizard[backup.mode].workflow_dep, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
				break;
			case 45:
				if (WE().consts.modules.active.indexOf("newsletter") > 0 && WE().consts.modules.active.indexOf("customer") > 0 && !document.we_form.handle_customer.checked || !document.we_form.handle_core.checked || !document.we_form.handle_object.checked) {
					document.we_form.handle_core.value = 1;
					document.we_form.handle_core.checked = true;
					document.we_form.handle_object.value = 1;
					document.we_form.handle_object.checked = true;
					document.we_form.handle_customer.value = 1;
					document.we_form.handle_customer.checked = true;
					WE().util.showMessage(WE().consts.g_l.backupWizard[backup.mode].newsletter_dep, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
				break;
			case 50:
				if (WE().consts.modules.active.indexOf("banner") > 0 && !document.we_form.handle_core.checked) {
					document.we_form.handle_core.value = 1;
					document.we_form.handle_core.checked = true;
					WE().util.showMessage(WE().consts.g_l.backupWizard[backup.mode].banner_dep, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
				break;

		}
		return;
	}
	var mess = "",
					tmpMess;
	switch (opt) {
		case 10:
			if (WE().consts.modules.active.indexOf("workflow") > 0 && document.we_form.elements.handle_workflow.checked) {
				document.we_form.elements.handle_workflow.checked = false;
				mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].workflow_data;
			}
			if (WE().consts.modules.active.indexOf("newsletter") > 0 && document.we_form.elements.handle_newsletter.checked) {
				document.we_form.elements.handle_newsletter.checked = false;
				mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].newsletter_data;
			}
			if (WE().consts.modules.active.indexOf("banner") > 0 && document.we_form.elements.handle_banner.checked) {
				document.we_form.elements.handle_banner.checked = false;
				mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].newsletter_data;
			}
			if (WE().consts.modules.active.indexOf("schedule") > 0 && document.we_form.elements.handle_schedule.checked) {
				document.we_form.elements.handle_schedule.checked = false;
				mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].schedule_data;
			}
			if (document.we_form.elements.handle_versions.checked) {
				document.we_form.elements.handle_versions.checked = false;
				mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].versions_data;
			}

			if (document.we_form.elements.handle_versions_binarys.checked) {
				document.we_form.elements.handle_versions_binarys.checked = false;
				mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].versions_binarys_data;
			}
			if (document.we_form.elements.handle_temporary.checked) {
				document.we_form.elements.handle_temporary.checked = false;
				mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].temporary_data;
			}
			if (document.we_form.elements.handle_history.checked) {
				document.we_form.elements.handle_history.checked = false;
				mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].history_data;
			}
			if (mess !== "") {
				tmpMess = WE().util.sprintf(WE().consts.g_l.backupWizard.unselect_dep2, WE().consts.g_l.backupWizard[backup.mode].core_data) + mess + "\n" + WE().consts.g_l.backupWizard.unselect_dep3;
				WE().util.showMessage(tmpMess, WE().consts.message.WE_MESSAGE_NOTICE, window);
			}
			break;

		case 11:
			if (WE().consts.modules.active.indexOf("object") > 0) {
				if (WE().consts.modules.active.indexOf("schedule") > 0 && document.we_form.elements.handle_schedule.checked) {
					document.we_form.elements.handle_schedule.checked = false;
					mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].schedule_data;
				}
				if (document.we_form.elements.handle_versions.checked) {
					document.we_form.elements.handle_versions.checked = false;
					mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].versions_data;
				}
				if (document.we_form.elements.handle_versions_binarys.checked) {
					document.we_form.elements.handle_versions_binarys.checked = false;
					mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].versions_binarys_data;
				}
				if (mess !== "") {
					tmpMess = WE().util.sprintf(WE().consts.g_l.backupWizard.unselect_dep2, WE().consts.g_l.backupWizard[backup.mode].object_data) + mess + "\n" + WE().consts.g_l.backupWizard.unselect_dep3;
					WE().util.showMessage(tmpMess, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
			}
			break;
		case 12:
			if (WE().consts.modules.active.indexOf("object") > 0) {
				if (document.we_form.elements.handle_versions_binarys.checked) {
					document.we_form.elements.handle_versions_binarys.checked = false;
					mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].versions_binarys_data;
				}
				if (mess !== "") {
					tmpMess = WE().util.sprintf(WE().consts.g_l.backupWizard.unselect_dep2, WE().consts.g_l.backupWizard[backup.mode].versions_data) + mess + "\n" + WE().consts.g_l.backupWizard.unselect_dep3;
					WE().util.showMessage(tmpMess, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
			}
			break;

		case 14:
			if (mess !== "") {
				tmpMess = WE().util.sprintf(WE().consts.g_l.backupWizard.unselect_dep2, WE().consts.g_l.backupWizard[backup.mode].binary_data) + mess + "\n" + WE().consts.g_l.backupWizard.unselect_dep3;
				WE().util.showMessage(tmpMess, WE().consts.message.WE_MESSAGE_NOTICE, window);
			}
			break;
		case 20:
			if (WE().consts.modules.active.indexOf("workflow") > 0) {
				if (document.we_form.elements.handle_workflow.checked) {
					document.we_form.elements.handle_workflow.checked = false;
					mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].workflow_data;
				}

				if (mess !== "") {
					tmpMess = WE().util.sprintf(WE().consts.g_l.backupWizard.unselect_dep2, WE().consts.g_l.backupWizard[backup.mode].user_data) + mess + "\n" + WE().consts.g_l.backupWizard.unselect_dep3;
					WE().util.showMessage(tmpMess, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
			}
			break;
		case 25:
			if (WE().consts.modules.active.indexOf("customer") > 0) {
				if (WE().consts.modules.active.indexOf("shop") > 0 && document.we_form.elements.handle_shop.checked) {
					document.we_form.elements.handle_shop.checked = false;
					mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].shop_data;
				}
				if (WE().consts.modules.active.indexOf("newsletter") > 0 && document.we_form.elements.handle_newsletter.checked) {
					document.we_form.elements.handle_newsletter.checked = false;
					mess += "\n-" + WE().consts.g_l.backupWizard[backup.mode].newsletter_data;
				}
				if (mess !== "") {
					tmpMess = WE().util.sprintf(WE().consts.g_l.backupWizard.unselect_dep2, WE().consts.g_l.backupWizard[backup.mode].customer_data) + mess + "\n" + WE().consts.g_l.backupWizard.unselect_dep3;
					WE().util.showMessage(tmpMess, WE().consts.message.WE_MESSAGE_NOTICE, window);
				}
			}
			break;
	}
}

function delSelItem() {
	var sel = document.we_form.backup_select;
	if (sel.selectedIndex > -1) {
		sel.remove(sel.selectedIndex);
	}
}

function showAll() {
	var a = document.we_form.backup_select.options;
	var b = document.we_form.show_all,
					i, j;

	if (b.checked) {
		b.value = 1;
		for (i = 0; i < backup.extra_files.length; i++) {
			a[a.length] = new window.Option(backup.extra_files_desc[i], backup.extra_files[i]);
		}
	} else {
		b.value = 0;
		for (i = a.length - 1; i > -1; i--) {
			for (j = backup.extra_files.length - 1; j > -1; j--) {
				if (a[i].value == backup.extra_files[j]) {
					a[i] = null;
					break;
				}
			}
		}
	}
}

function setLocation(loc) {
	if (top.cmd.reloadTimer) {
		window.clearTimeout(top.cmd.reloadTimer);
	}

	document.location.href = loc;
}

function startStep(step, doImport) {
	window.focus();
	top.busy.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=" + backup.modeCmd + "&pnt=busy&step=" + step + (
					doImport !== undefined ? "&do_import_after_backup=" + doImport : "");
}

function startBusy() {
	top.busy.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=" + backup.modeCmd + "&pnt=busy&operation_mode=busy&step=4";
}

function stopBusy() {
	top.busy.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=" + backup.modeCmd + "&pnt=busy&step=5";
	top.cmd.location = "about:blank";
	window.focus();
}

function delOldFiles() {
	if (window.confirm(WE().consts.g_l.backupWizard.delold_confirm)) {
		top.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=" + backup.modeCmd + "&pnt=cmd&operation_mode=deleteall";
	}
}

function delSelected() {
	var sel = document.we_form.backup_select;
	if (sel.selectedIndex > -1) {
		if (window.confirm(WE().consts.g_l.backupWizard.del_backup_confirm)) {
			top.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=" + backup.modeCmd + "&pnt=cmd&operation_mode=deletebackup&bfile=" + sel.options[sel.selectedIndex].value;
		}
	} else {
		WE().util.showMessage(WE().consts.g_l.backupWizard.nothing_selected_fromlist, WE().consts.message.WE_MESSAGE_WARNING, window);
	}
}

function startImport(isFileReady) {
	var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
	isFileReady = isFileReady || false;
	for (var frameId in _usedEditors) {
		_usedEditors[frameId].setEditorIsHot(false);

	}
	WE().layout.weEditorFrameController.closeAllDocuments();

	if (backup.import_from === "import_upload") {
		if (isFileReady || document.we_form.we_upload_file.value) {
			startBusy();
			top.edbody.delete_enabled = WE().layout.button.switch_button_state(top.edbody.document, "delete", "disabled");
			document.we_form.action = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=backup_cmd";
			document.we_form.submit();
		} else {
			WE().util.showMessage(WE().consts.g_l.backupWizard.nothing_selected, WE().consts.message.WE_MESSAGE_WARNING, window);
		}
	} else {
		if (document.we_form.backup_select.value) {
			startBusy();
			top.edbody.delete_backup_enabled = WE().layout.button.switch_button_state(top.edbody.document, "delete_backup", "disabled");
			top.edbody.delete_enabled = WE().layout.button.switch_button_state(top.edbody.document, "delete", "disabled");
			document.we_form.action = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=backup_cmd";
			document.we_form.submit();
		} else {
			WE().util.showMessage(WE().consts.g_l.backupWizard.nothing_selected_fromlist, WE().consts.message.WE_MESSAGE_WARNING, window);
		}
	}
}

function doCheck(opt) {
	if (backup.form_properties[opt]) {
		document.we_form[backup.form_properties[opt]].checked = true;
		doClick(opt);
	}
}

function doUnCheck(opt) {
	if (backup.form_properties[opt]) {
		document.we_form[backup.form_properties[opt]].checked = false;
		doClick(opt);
	}
}

function doClick(opt) {
	if (backup.form_properties[opt]) {
		var a = document.we_form[backup.form_properties[opt]];
		doClicked(a.checked, opt);
		if (!a.checked) {

		}
	}
}

function reloadFrame() {
	if (backup.reload < 3) {
		top.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=backup_cmd&cmd=" + backup.mode + "&reload=" + backup.reload;
	} else {
		WE().util.showMessage(WE().consts.g_l.backupWizard.error_timeout, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function run(type, percent, desc) {
	updateProgress(percent, desc);
	if (top.cmd.reloadTimer) {
		clearTimeout(top.cmd.reloadTimer);
	}
	top.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=backup_cmd&cmd=" + type;
}

function updateProgress(percent, desc) {
	if (top.busy && top.busy.setProgressText) {
		top.busy.setProgressText("current_description", desc);
		top.busy.setProgress(percent);
	}
}

function backupFinished(text) {
	updateProgress(100, text);
	top.edbody.setLocation(WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=make_backup&pnt=edbody&step=2");
	top.cmd.location = "about:blank";
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "rebuild":
			new (WE().util.jsWindow)(top.opener, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_all&responseText=" + WE().consts.g_l.backupWizard.finished_success, "rebuildwin", WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, 0, true);
			top.close();
			break;
		case "deleteall":
			new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=delFrag&currentID=-1", "we_del", WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, true, true);
			break;
		case "deletebackup":
			if (top.edbody.delSelItem) {
				top.edbody.delSelItem();
			}
			break;
		case "importFinished":
			finishedImport(args[1].doRebuild, args[1].file);
			break;
		case "import_file_found":
			if (window.confirm(WE().consts.g_l.backupWizard.import_file_found)) {
				top.opener.top.we_cmd("import");
				top.close();
			} else {
				top.edbody.location = WE().consts.dirs + "we_cmd.php?we_cmd[0]=recover_backup&pnt=edbody&step=2";
			}
			break;
		default:
			top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function finishedImport(doRebuild, file) {
	top.opener.top.we_cmd("load", top.opener.top.treeData.table);
	top.busy.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=recover_backup&pnt=busy&operation_mode=busy&percent=100&current_description=" + WE().consts.g_l.backupWizard.finished;
	if (doRebuild) {
		top.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=recover_backup&pnt=cmd&operation_mode=rebuild";
	} else {
		top.edbody.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=recover_backup&pnt=edbody&step=4&temp_filename=" + file;
	}
	if (top.busy && top.busy.setProgressText) {
		top.busy.setProgressText("current_description", WE().consts.g_l.backupWizard.finished);
		top.busy.setProgress(100);
	}
}

//add reload timer if set
if (backup && backup.reloadTimer) {
	top.cmd.reloadTimer = setTimeout(reloadFrame, backup.reloadTimer);
}
