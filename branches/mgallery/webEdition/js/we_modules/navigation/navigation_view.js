/* global WE, top, YAHOO, data */

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
var activ_tab = 1;
var hot = 0;
var makeNewDoc = false;

function mark() {
	hot = 1;
	top.content.editor.edheader.mark();
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var arguments = args;

	if (top.content.hot) {
		switch (args[0]) {
			case "module_navigation_edit":
			case "module_navigation_new":
			case "module_navigation_new_group":
			case "exit_navigation":
				top.content.editor.edbody.document.getElementsByName("delayCmd")[0].value = args[0];
				top.content.editor.edbody.document.getElementsByName("delayParam")[0].value = args[1];
				args[0] = "exit_doc_question";
		}
	}
	switch (args[0]) {
		case "module_navigation_edit":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.cmdid.value = args[1];
				top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(function () {
					we_cmd("module_navigation_edit", args[1]);
				}, 10);
			}
			break;
		case "module_navigation_new":
		case "module_navigation_new_group":
			if (top.content.editor.edbody.loaded) {
				top.content.hot = 0;
				if (top.content.editor.edbody.document.we_form.presetFolder !== undefined) {
					top.content.editor.edbody.document.we_form.presetFolder.value = false;
				}
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.document.we_form.tabnr.value = 1;
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(function () {
					we_cmd("\' + args[0] + \'");
				}, 10);
			}
			if ((this.treeData !== undefined) && treeData) {
				treeData.unselectNode();
			}
			break;
		case "module_navigation_save":
			if (top.content.editor.edbody.document.we_form.cmd.value === "home")
				return;
			if (top.content.editor.edbody.loaded) {
				if (top.content.editor.edbody.document.we_form.presetFolder)
					top.content.editor.edbody.document.we_form.presetFolder.value = makeNewDoc;
				var cont = true;
				if (top.content.editor.edbody.document.we_form.Selection !== undefined) {
					if (top.content.editor.edbody.document.we_form.Selection.options[top.content.editor.edbody.document.we_form.Selection.selectedIndex].value === WE().consts.navigation.SELECTION_DYNAMIC && top.content.editor.edbody.document.we_form.IsFolder.value == "1") {
						cont = confirm(WE().consts.g_l.navigation.view.save_populate_question);
					}
				}
				if (cont) {
					top.content.editor.edbody.document.we_form.cmd.value = args[0];
					top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
					top.content.editor.edbody.document.we_form.pnt.value = "edbody";
					top.content.editor.edbody.submitForm();
				}
			} else {
				WE().util.showMessage(WE().consts.g_l.navigation.view.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, this);
			}
			break;
		case "populate":
		case "depopulate":
			if (top.content.editor.edbody.document.we_form.cmd.value === "home")
				return;
			if (top.content.editor.edbody.loaded) {
				q = (args[0] === "populate" ?
								WE().consts.g_l.navigation.view.populate_question :
								WE().consts.g_l.navigation.viewdepopulate_question);

				if (confirm(q)) {
					top.content.editor.edbody.document.we_form.pnt.value = "edbody";
					top.content.editor.edbody.document.we_form.cmd.value = args[0];
					top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
					if (top.content.editor.edbody.document.we_form.pnt.value === "previewIframe") {
						top.content.editor.edbody.document.we_form.pnt.value = "preview";
					}

					top.content.editor.edbody.submitForm();
				}
			}
			break;
		case "module_navigation_delete":
			if (top.content.editor.edbody.document.we_form.cmd.value === "home") {
				WE().util.showMessage(WE().consts.g_l.navigation.view.nothing_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			}
			if (top.content.editor.edbody.document.we_form.newone) {
				if (top.content.editor.edbody.document.we_form.newone.value == 1) {
					WE().util.showMessage(WE().consts.g_l.navigation.view.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, this);
					return;
				}
			}
			if (!WE().util.hasPerm("DELETE_NAVIGATION")) {
				WE().util.showMessage(WE().consts.g_l.navigation.view.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			}
			if (top.content.editor.edbody.loaded) {
				if (confirm(WE().consts.g_l.navigation.view.delete_alert)) {
					top.content.editor.edbody.document.we_form.cmd.value = args[0];
					top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
					top.content.editor.edheader.location = data.frameset + "?home=1&pnt=edheader";
					top.content.editor.edfooter.location = data.frameset + "?home=1&pnt=edfooter";
					top.content.editor.edbody.submitForm();
				}
			} else {
				WE().util.showMessage(WE().consts.g_l.navigation.view.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, this);
			}

			break;
		case "move_abs":
			top.content.cmd.location = data.frameset + "&pnt=cmd&cmd=" + args[0] + "&pos=" + args[1];
			break;
		case "move_up":
		case "move_down":
			top.content.cmd.location = data.frameset + "&pnt=cmd&cmd=" + args[0];
			break;
		case "dyn_preview":
		case "create_template":
		case "populateWorkspaces":
		case "populateFolderWs":
		case "populateText":
			top.content.editor.edbody.document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			top.content.editor.edbody.document.we_form.pnt.value = "cmd";
			top.content.editor.edbody.submitForm("cmd");
			break;
		case "del_mode":
			top.content.treeData.setState(treeData.tree_states["select"]);
			top.content.treeData.unselectNode();
			top.content.drawTree();
		case "move_mode":
			top.content.treeData.setState(treeData.tree_states["selectitem"]);
			top.content.treeData.unselectNode();
			top.content.drawTree();
			break;
		case "exit_navigation":
			if (hot !== 1) {
				top.opener.top.we_cmd("exit_modules");
			}
			break;
		case "exit_doc_question":
			url = data.frameset + "&pnt=exit_doc_question&delayCmd=" + top.content.editor.edbody.document.getElementsByName("delayCmd")[0].value + "&delayParam=" + top.content.editor.edbody.document.getElementsByName("delayParam")[0].value;
			new (WE().util.jsWindow)(this, url, "we_exit_doc_question", -1, -1, 380, 130, true, false, true);
			break;

		case "module_navigation_reset_customer_filter":
			if (confirm(WE().consts.g_l.navigation.view.reset_customerfilter_question)) {
				we_cmd("module_navigation_do_reset_customer_filter");
			}
			break;
		default:
			top.opener.top.we_cmd.apply(this, arguments);

	}
}