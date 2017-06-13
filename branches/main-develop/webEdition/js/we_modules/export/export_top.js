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

WE().util.loadConsts(document, "g_l.exports");

var get_focus = 1;
var activ_tab = 1;
var hot = false;
var scrollToVal = 0;
var table = WE().consts.tables.FILE_TABLE;

function loaded() {
	window.weTabs.setFrameSize();
	document.getElementById('tab_' + top.content.activ_tab).className = 'tabActive';
}

function setHot() {
	hot = true;
}

function unsetHot() {
	hot = false;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case 'unsetHot':
			unsetHot();
			break;
		case "exit_export":
			if (hot) {
				WE().util.showConfirm(window, '', WE().consts.g_l.exports.view.save_changed_export, ["processConfirmHot", "save_export"], ["processConfirmHot", "unsetHot"].concat(args), WE().consts.g_l.button.save, WE().consts.g_l.button.revert);
				break;
			}
			top.opener.top.we_cmd("exit_modules");
			break;
		case "setTab":
			top.content.activ_tab = args[1];
			break;
		case "new_export_group":
			if (hot) {
				WE().util.showConfirm(window, '', WE().consts.g_l.exports.view.save_changed_export, ["processConfirmHot", "save_export"], ["processConfirmHot", "unsetHot"].concat(args), WE().consts.g_l.button.save, WE().consts.g_l.button.revert);
				break;
			}
			if (!WE().util.hasPerm("NEW_EXPORT")) {
				WE().util.showMessage(WE().consts.g_l.exports.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.IsFolder.value = 1;
			}
			/* falls through */
		case "new_export":
			if (hot) {
				WE().util.showConfirm(window, '', WE().consts.g_l.exports.view.save_changed_export, ["processConfirmHot", "save_export"], ["processConfirmHot", "unsetHot"].concat(args), WE().consts.g_l.button.save, WE().consts.g_l.button.revert);
				break;
			}
			if (!WE().util.hasPerm("NEW_EXPORT")) {
				WE().util.showMessage(WE().consts.g_l.exports.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.cmdid.value = args[1];
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.document.we_form.tabnr.value = 1;
				top.content.editor.edbody.submitForm();
			} else {
				window.setTimeout(we_cmd, 10, args[0]);
			}
			break;
		case "delete_export":
			if (top.content.editor.edbody.document.we_form.cmd.value === "home") {
				return;
			}
			if (!WE().util.hasPerm("DELETE_EXPORT")) {
				WE().util.showMessage(WE().consts.g_l.exports.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}

			if (!top.content.editor.edbody.loaded) {
				WE().util.showMessage(WE().consts.g_l.exports.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}

			var message = (top.content.editor.edbody.document.we_form.IsFolder.value == 1 ?
				WE().consts.g_l.exports.delete_group_question :
				WE().consts.g_l.exports.delete_question);
			WE().util.showConfirm(window, "", message, ["delete_export_do"]);
			break;
		case "delete_export_do":
			top.content.editor.edbody.document.we_form.cmd.value = "delete_export";
			top.content.editor.edbody.document.we_form.pnt.value = "cmd";
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			top.content.editor.edbody.submitForm("cmd");
			break;
		case "start_export":
			if (hot) {
				WE().util.showConfirm(window, '', WE().consts.g_l.exports.view.save_changed_export, ["processConfirmHot", "save_export"], ["processConfirmHot", "unsetHot"].concat(args), WE().consts.g_l.button.save, WE().consts.g_l.button.revert);
				break;
			}
			if (!WE().util.hasPerm("NEW_EXPORT")) {
				WE().util.showMessage(WE().consts.g_l.exports.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			if (top.content.editor.edheader.setTab) {
				top.content.editor.edheader.weTabs.setActiveTab("tab_3");
			}
			if (top.content.editor.edheader.setTab) {
				top.content.editor.edheader.setTab(3);
			}
			top.content.editor.edfooter.setProgress(0);
			if (top.content.editor.edbody.clearLog) {
				top.content.editor.edbody.clearLog();
			}
			/* falls through */
		case "save_export":
			top.content.editor.edbody.document.we_form.Text.value = top.content.editor.edbody.document.we_form.Text_visible.value + top.content.editor.edbody.document.we_form.Extension.value;

			if (!WE().util.hasPerm("NEW_EXPORT")) {
				WE().util.showMessage(WE().consts.g_l.exports.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			if (top.content.editor.edbody.document.we_form.cmd.value === "home") {
				return;
			}

			if (top.content.editor.edbody.loaded) {
				if (top.content.editor.edbody.document.we_form.Text.value === '') {
					WE().util.showMessage(WE().consts.g_l.exports.name_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
					return;
				}

				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.pnt.value = args[0] === "start_export" ? "load" : "edbody";
				top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
				if (top.content.editor.edbody.document.we_form.IsFolder.value != 1) {
					top.content.editor.edbody.document.we_form.selDocs.value = top.content.editor.edbody.treeData.SelectedItems[WE().consts.tables.FILE_TABLE].join(",");
					top.content.editor.edbody.document.we_form.selTempl.value = top.content.editor.edbody.treeData.SelectedItems[WE().consts.tables.TEMPLATES_TABLE].join(",");
					top.content.editor.edbody.document.we_form.selDocs_open.value = top.content.editor.edbody.treeData.openFolders[WE().consts.tables.FILE_TABLE];
					top.content.editor.edbody.document.we_form.selTempl_open.value = top.content.editor.edbody.treeData.openFolders[WE().consts.tables.TEMPLATES_TABLE];

					if (WE().consts.modules.active.indexOf("object") > 0) {
						top.content.editor.edbody.document.we_form.selObjs.value = top.content.editor.edbody.treeData.SelectedItems[WE().consts.tables.OBJECT_FILES_TABLE].join(",");
						top.content.editor.edbody.document.we_form.selObjs_open.value = top.content.editor.edbody.treeData.openFolders[WE().consts.tables.OBJECT_FILES_TABLE];
					}
					if (WE().consts.modules.active.indexOf("object") > 0) {
						top.content.editor.edbody.document.we_form.selClasses.value = top.content.editor.edbody.treeData.SelectedItems[WE().consts.tables.OBJECT_TABLE].join(",");
						top.content.editor.edbody.document.we_form.selClasses_open.value = top.content.editor.edbody.treeData.openFolders[WE().consts.tables.OBJECT_TABLE];
					}
				}

				top.content.editor.edbody.submitForm(args[0] === "start_export" ? "cmd" : "edbody");
			} else {
				WE().util.showMessage(WE().consts.g_l.exports.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, window);

			}
			top.content.unsetHot();
			break;
		case "export_edit":
			if (hot) {
				WE().util.showConfirm(window, '', WE().consts.g_l.exports.view.save_changed_export, ["processConfirmHot", "save_export"], ["processConfirmHot", "unsetHot"].concat(args), WE().consts.g_l.button.save, WE().consts.g_l.button.revert);
				break;
			}
			if (!WE().util.hasPerm("EDIT_EXPORT")) {
				WE().util.showMessage(WE().consts.g_l.exports.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			top.content.hot = false;
			top.content.editor.edbody.document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.pnt.value = "edbody";
			top.content.editor.edbody.document.we_form.cmdid.value = args[1];
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;

			top.content.editor.edbody.submitForm();
			break;
		case "load":
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=cmd&pid=" + args[1] + "&offset=" + args[2] + "&sort=" + args[3];
			break;
		case "home":
			top.content.editor.edbody.parent.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=editor";
			break;
		case "loadHeaderFooter":
			top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=edheader&text=" + encodeURI(args[1]);
			top.content.editor.edfooter.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=edfooter";
			break;
		case 'setHot':
			hot = true;
			//top.content.editor.edbody.toggle(args[1]); // FIXME: where do we call setHot mit arguments?
			break;
		case "updateLog"://
			for (var i = 0; i < args[1].log.length; i++) {
				top.content.editor.edbody.addLog(args[1].log[i]);
			}
			top.content.editor.edfooter.setProgress(args[1].percent);
			top.content.editor.edfooter.setProgressText("current_description", args[1].text);
			return;
		case 'submitCmdForm'://
			top.content.cmd.document.we_form.submit();
			return;
		case 'startDownload'://
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=export&pnt=cmd&cmd=upload&exportfile=' + args[1];
			return;
		case 'setStatusEnd':
			WE().util.showMessage(WE().consts.g_l.exports.server_finished, WE().consts.message.WE_MESSAGE_NOTICE, window);
			top.content.editor.edfooter.hideProgress();
			return;
		case 'setIconOfDocClass':
			args[0] = 'loop_through';
			return;
		default:
			top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));

	}
}

function setTab(tab) {
	parent.edbody.toggle("tab" + top.content.activ_tab);
	parent.edbody.toggle("tab" + tab);
	top.content.activ_tab = tab;
}