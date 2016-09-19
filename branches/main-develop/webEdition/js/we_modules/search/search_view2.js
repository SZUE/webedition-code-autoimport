/* global WE, top */

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
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
var activ_tab = 1;
var hot = false;

WE().util.loadConsts(document, "g_l.weSearch");

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	if (top.content.hot) {
		switch (args[0]) {
			case "tool_weSearch_edit":
			case "tool_weSearch_new":
			case "tool_weSearch_new_group":
			case "tool_weSearch_exit":
				top.content.editor.edbody.document.we_form.delayCmd.value = args[0];
				top.content.editor.edbody.document.we_form.delayParam.value = args[1];
				args[0] = "exit_doc_question";
		}
	}

	switch (args[0]) {
		case "tool_weSearch_edit":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.cmdid.value = args[1];
				top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "tool_weSearch_edit", args[1]);
			}
			break;
		case "tool_weSearch_new":
		case "tool_weSearch_new_group":
			if (top.content.editor.edbody.loaded) {
				top.content.hot = false;
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.document.we_form.tabnr.value = 1;
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "tool_weSearch_new");
			}
			if (treeData) {
				treeData.unselectNode();
			}
			break;
		case "tool_weSearch_exit":
			top.close();
			break;
		case "exit_doc_question":
			url = WE().consts.dirs.WE_INCLUDES_DIR + "we_tools/weSearch/edit_weSearch_frameset.php?mod=weSearch&pnt=exit_doc_question&delayCmd=" + top.content.editor.edbody.document.getElementsByName("delayCmd")[0].value + "&delayParam=" + top.content.editor.edbody.document.getElementsByName("delayParam")[0].value;
			new (WE().util.jsWindow)(this, url, "we_exit_doc_question", -1, -1, 380, 130, true, false, true);
			break;
		case "tool_weSearch_save":
			if (top.content.editor.edbody.document.we_form.predefined.value == 1) {
				top.we_showMessage(WE().consts.g_l.weSearch.predefinedSearchmodify, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			} else if (top.content.editor.edbody.loaded) {
				if (top.content.editor.edbody.document.we_form.newone.value == 1) {
					var name = prompt(WE().consts.g_l.weSearch.nameForSearch, "");
					if (name === null) {
						break;
					} else {
						top.content.editor.edbody.document.we_form.savedSearchName.value = name;
					}
				}
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				//top.content.editor.edbody.document.we_form.tabnr.value=top.content.activ_tab;
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.submitForm();
			} else {
				top.we_showMessage(WE().consts.g_l.weSearch.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, this);
			}
			break;
		case "tool_weSearch_delete":
			if (top.content.editor.edbody.document.we_form.predefined.value == 1) {
				top.we_showMessage(WE().consts.g_l.weSearch.predefinedSearchdelete, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			}
			if (top.content.editor.edbody.document.we_form.newone.value == 1 || !top.content.editor.edbody.loaded) {
				top.we_showMessage(WE().consts.g_l.weSearch.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			}
			if (!WE().util.hasPerm("DELETE_WESEARCH")) {
				top.we_showMessage(WE().consts.g_l.weSearch.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			}
			if (top.content.editor.edbody.loaded && confirm(WE().consts.g_l.weSearch.confirmDel)) {
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
				top.content.editor.edheader.location = WE().consts.dirs.WE_INCLUDES_DIR + "we_tools/weSearch/edit_weSearch_frameset.php?mod=weSearch&home=0&pnt=edheader";
				top.content.editor.edfooter.location = WE().consts.dirs.WE_INCLUDES_DIR + "we_tools/weSearch/edit_weSearch_frameset.php?mod=weSearch&home=0&pnt=edfooter";
				top.content.editor.edbody.submitForm();

			}
			break;
		case "tool_weSearch_new_forDocuments":
			if (top.content.editor.edbody.loaded) {
				top.content.hot = false;
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.activ_tab = 1;
				top.content.editor.edbody.document.we_form.tabnr.value = 1;
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "tool_weSearch_new_forDocuments");
			}
			if (treeData) {
				treeData.unselectNode();
			}
			break;

		case "tool_weSearch_new_forTemplates":
			if (top.content.editor.edbody.loaded) {
				top.content.hot = false;
				top.content.activ_tab = 2;
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.tabnr.value = 2;
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "tool_weSearch_new_forTemplates");
			}
			if (treeData) {
				treeData.unselectNode();
			}
			break;

		case "tool_weSearch_new_forObjects":
			if (top.content.editor.edbody.loaded) {
				top.content.hot = false;
				top.content.activ_tab = 3;
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.tabnr.value = 3;
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "tool_weSearch_new_forObjects");
			}
			if (treeData) {
				treeData.unselectNode();
			}
			break;

		case "tool_weSearch_new_forMedia":
			if (top.content.editor.edbody.loaded) {
				top.content.hot = false;
				top.content.activ_tab = 5;
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.tabnr.value = 5;
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "tool_weSearch_new_forMedia");
			}
			if (treeData) {
				treeData.unselectNode();
			}
			break;

		case "tool_weSearch_new_advSearch":
			if (top.content.editor.edbody.loaded) {
				top.content.hot = false;
				top.content.activ_tab = 3;
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.tabnr.value = 3;
				top.content.editor.edbody.document.we_form.pnt.value = "edbody";
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "tool_weSearch_new_advSearch");
			}
			if (treeData) {
				treeData.unselectNode();
			}
			break;
		default:
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));

	}
}

function mark() {
	hot = true;
	top.content.editor.edheader.mark();
}