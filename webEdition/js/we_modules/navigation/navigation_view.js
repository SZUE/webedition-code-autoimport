/* global WE, top, data */

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
var hot = false;
var makeNewDoc = false;

WE().util.loadConsts(document, "g_l.navigation");

function mark() {
	hot = true;
	top.content.editor.edheader.mark();
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var delay = Array.prototype.slice.call(arguments);
	var el, id;

	if (top.content.hot) {
		switch (args[0]) {
			case "module_navigation_edit":
			case "module_navigation_new":
			case "module_navigation_new_group":
			case "exit_navigation":
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
				setTimeout(top.content.we_cmd, 10, "module_navigation_edit", args[1]);
			}
			break;
		case "module_navigation_new":
		case "module_navigation_new_group":
			if (top.content.editor.edbody.loaded) {
				top.content.hot = false;
				if (top.content.editor.edbody.document.we_form.presetFolder !== undefined) {
					top.content.editor.edbody.document.we_form.presetFolder.value = false;
				}
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.document.we_form.tabnr.value = 1;
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(top.content.we_cmd, 10, args[0]);
			}
			if ((this.treeData !== undefined) && treeData) {
				treeData.unselectNode();
			}
			break;
		case "module_navigation_save":
			if (top.content.editor.edbody.document.we_form.cmd.value === "home") {
				return;
			}
			if (!top.content.editor.edbody.loaded) {
				WE().util.showMessage(WE().consts.g_l.navigation.view.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			}
			if (top.content.editor.edbody.document.we_form.presetFolder) {
				top.content.editor.edbody.document.we_form.presetFolder.value = makeNewDoc;
			}

			if (top.content.editor.edbody.document.we_form.Selection && top.content.editor.edbody.document.we_form.Selection.options) {
				if (top.content.editor.edbody.document.we_form.Selection.options[top.content.editor.edbody.document.we_form.Selection.selectedIndex].value === WE().consts.navigation.SELECTION_DYNAMIC && top.content.editor.edbody.document.we_form.IsFolder.value == "1") {
					WE().util.showConfirm(window, "", WE().consts.g_l.navigation.view.save_populate_question, ["module_navigation_save_do"]);
					break;
				}
			}
			/*falls through*/
		case "module_navigation_save_do":
			top.content.editor.edbody.document.we_form.cmd.value = "module_navigation_save";
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			top.content.editor.edbody.document.we_form.pnt.value = "edbody";
			top.content.editor.edbody.submitForm();
			break;
		case "populate":
		case "depopulate":
			if (top.content.editor.edbody.document.we_form.cmd.value === "home") {
				return;
			}
			if (!top.content.editor.edbody.loaded) {
				return;
			}
			var q = (args[0] === "populate" ?
				WE().consts.g_l.navigation.view.populate_question :
				WE().consts.g_l.navigation.view.depopulate_question);
			WE().util.showConfirm(window, "", q, ["depopulate_do", args[0]]);
			break;
		case "depopulate_do":
			top.content.editor.edbody.document.we_form.pnt.value = "edbody";
			top.content.editor.edbody.document.we_form.cmd.value = args[1];
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			if (top.content.editor.edbody.document.we_form.pnt.value === "previewIframe") {
				top.content.editor.edbody.document.we_form.pnt.value = "preview";
			}

			top.content.editor.edbody.submitForm();
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
			if (!top.content.editor.edbody.loaded) {
				WE().util.showMessage(WE().consts.g_l.navigation.view.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			}
			WE().util.showConfirm(window, "", WE().consts.g_l.navigation.view.delete_alert, ["module_navigation_delete_do"]);
			break;
		case "module_navigation_delete_do":
			top.content.editor.edbody.document.we_form.cmd.value = "module_navigation_delete";
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&home=1&pnt=edheader";
			top.content.editor.edfooter.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&home=1&pnt=edfooter";
			top.content.editor.edbody.submitForm();
			break;
		case "move_abs":
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=cmd&cmd=" + args[0] + "&pos=" + args[1];
			break;
		case "move_up":
		case "move_down":
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=cmd&cmd=" + args[0];
			break;
		case "enable_open_navigation_doc":
			WE().layout.button.switch_button_state(document, 'open_navigation_doc', document.we_form.elements.LinkID.value > 0 ? 'enabled' : 'disabled');
			break;
		case "populateText":
			we_cmd("setHot");
			top.content.editor.edbody.document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			top.content.editor.edbody.document.we_form.pnt.value = "cmd";
			top.content.editor.edbody.submitForm("cmd");
			break;
		case "dyn_preview":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=dyn_preview";
			new (WE().util.jsWindow)(window, url, "we_navigation_dyn_preview", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "populateWorkspaces":
			WE().layout.button.switch_button_state(document, 'open_navigation_obj', document.we_form.elements.LinkID.value > 0 ? 'enabled' : 'disabled');
			/*falls through*/
		case "create_template":
		case "populateFolderWs":
			top.content.editor.edbody.document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			top.content.editor.edbody.document.we_form.pnt.value = "cmd";
			top.content.editor.edbody.submitForm("cmd");
			break;
		case "doPopulateFolderWs":
			populateFolderWs(args[1]);
			if (args[1] === "values") {
				el = top.content.editor.edbody.document.we_form.WorkspaceID;
				for (id in args[2]) {
					el.options.add(new Option(args[2][id], id));
				}
			}
			break;
		case "doPopulateWs":
			populateWorkspaces(args[1], args[2]);
			if (args[1] === "values") {
				el = top.content.editor.edbody.document.we_form['WorkspaceID' + args[2]];
				for (id in args[3]) {
					el.options.add(new Option(args[3][id], id));
				}
			}
			break;
		case "del_mode":
			top.content.treeData.setState(treeData.tree_states.select);
			top.content.treeData.unselectNode();
			top.content.drawTree();
			/* falls through */
		case "move_mode":
			top.content.treeData.setState(treeData.tree_states.selectitem);
			top.content.treeData.unselectNode();
			top.content.drawTree();
			break;
		case "exit_navigation":
			if (!hot) {
				top.opener.top.we_cmd("exit_modules");
			}
			break;
		case "exit_doc_question":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=exit_doc_question";
			for (var i = 0; i < delay.length; i++) {
				url += "&delayCmd[]=" + delay[i];
			}
			new (WE().util.jsWindow)(window, url, "we_exit_doc_question", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, false, true);
			break;

		case "module_navigation_reset_customer_filter":
			WE().util.showConfirm(window, "", WE().consts.g_l.navigation.view.reset_customerfilter_question, ["module_navigation_do_reset_customer_filter"]);
			break;
		case "show_search":
			var keyword = top.content.we_form_treefooter.keyword.value;
			new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=search&search=1&keyword=" + keyword, "search", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true, false);
			break;
		case 'moveAbs':
			moveAbs(args[1], args[2], args[3]);
			break;
		case 'moveUp':
			moveUp(args[1], args[2], args[3]);
			break;
		case 'moveDown':
			moveDown(args[1], args[2], args[3]);
			break;
		case "module_navigation_do_reset_customer_filter":
			we_repl(window.load, url, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_navigation_do_reset_customer_filter");
			return true;
		default:
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));

	}
}

function moveAbs(pos, parent, selector) {
	top.content.editor.edbody.document.we_form.Ordn.value = pos;
	top.content.reloadGroup(parent);
	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "enabled");

	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", (top.content.editor.edbody.document.we_form.Ordn.value === "0" ? "disabled" : "enabled"));

	top.content.editor.edbody.document.we_form.Position.innerHTML = selector;
}

function moveUp(pos, parent, selector) {
	top.content.editor.edbody.document.we_form.Ordn.value = pos;
	top.content.reloadGroup(parent);
	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "enabled");

	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", (top.content.editor.edbody.document.we_form.Ordn.value === "0" ? "disabled" : "enabled"));

	top.content.editor.edbody.document.we_form.Position.innerHTML = selector;
}

function moveDown(pos, parent, selector, max) {
	top.content.editor.edbody.document.we_form.Ordn.value = pos;
	top.content.reloadGroup(parent);
	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");

	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", (top.content.editor.edbody.document.we_form.Ordn.value >= max ? "disabled" : "enabled"));

	top.content.editor.edbody.document.we_form.Position.innerHTML = selector;
}

function populateFolderWs(type, prefix) {
	switch (type) {
		case 'values':
			top.content.editor.edbody.setVisible("objLinkFolderWorkspace", true);
			top.content.editor.edbody.document.we_form.WorkspaceID.options.length = 0;
			return;
		case 'workspace':
			top.content.editor.edbody.document.we_form.WorkspaceID.options.length = 0;
			top.content.editor.edbody.document.we_form.WorkspaceID.options[top.content.editor.edbody.document.we_form.WorkspaceID.options.length] = new Option("/", 0);
			top.content.editor.edbody.document.we_form.WorkspaceID.selectedIndex = 0;
			top.content.editor.edbody.setVisible("objLinkFolderWorkspace", true);
			return;
		case 'noWorkspace':
			top.content.editor.edbody.setVisible("objLinkFolderWorkspace" + prefix, false);
			top.content.editor.edbody.document.we_form.WorkspaceID.options.length = 0;
			top.content.editor.edbody.document.we_form.WorkspaceID.options[top.content.editor.edbody.document.we_form.WorkspaceID.options.length] = new Option("-1", -1);
			top.content.editor.edbody.document.we_form.LinkID.value = "";
			top.content.editor.edbody.document.we_form.LinkPath.value = "";
			WE().util.showMessage(WE().consts.g_l.navigation.view.no_workspace, WE().consts.message.WE_MESSAGE_ERROR, this);
			return;
	}
}

function populateWorkspaces(type, prefix) {
	switch (type) {
		case 'values':
			top.content.editor.edbody.setVisible("objLinkWorkspace" + prefix, true);
			top.content.editor.edbody.document.we_form["WorkspaceID" + prefix].options.length = 0;
			return;
		case 'workspace':
			top.content.editor.edbody.document.we_form["WorkspaceID" + prefix].options.length = 0;
			top.content.editor.edbody.document.we_form["WorkspaceID" + prefix ].options[top.content.editor.edbody.document.we_form["WorkspaceID" + prefix].options.length] = new Option("/", 0);
			top.content.editor.edbody.document.we_form["WorkspaceID" + prefix ].selectedIndex = 0;
			//top.content.editor.edbody.setVisible("objLinkWorkspace"+prefix ,false);
			return;
		case 'noWorkspace':
			top.content.editor.edbody.setVisible("objLinkWorkspace" + prefix, false);
			top.content.editor.edbody.document.we_form["WorkspaceID" + prefix].options.length = 0;
			top.content.editor.edbody.document.we_form["WorkspaceID" + prefix].options[top.content.editor.edbody.document.we_form["WorkspaceID" + prefix].options.length] = new Option("-1", -1);
			top.content.editor.edbody.document.we_form.LinkID.value = "";
			top.content.editor.edbody.document.we_form.LinkPath.value = "";
			WE().util.showMessage(WE().consts.g_l.navigation.view.no_workspace, WE().consts.message.WE_MESSAGE_ERROR, this);
			return;
	}
}