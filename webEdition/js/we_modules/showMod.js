/*
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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/* global WE, top */
'use strict';

var moduleData = WE().util.getDynamicVar(document, 'loadVarShowMod', 'data-moduleData');

var weTabs = new (WE().layout.we_tabs)(document, window);

var makeNewEntryCheck = 0;
var publishWhenSave = 0;
var weModuleWindow = true;
var current = moduleData.mod;

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	var doc;

	switch (args[0]) {
		case 'setHot':
			doc = top.content;
			doc = doc.document.getElementById("mark") ? doc : doc.editor.edheader;
			doc.document.getElementById("mark").style.display = "inline";
			top.content.hot = true;
			break;
		case 'unsetHot':
			doc = top.content;
			doc = doc.document.getElementById("mark") ? doc : doc.editor.edheader;
			doc.document.getElementById("mark").style.display = "none";
			top.content.hot = false;
			break;
		case 'loadTree':
			var clear = args[1].clear;
			var items = args[1].items;
			var sorted = args[1].sorted;
			if (clear) {
				top.content.treeData.clear();
				top.content.treeData.add(top.content.Node.prototype.rootEntry(0, 'root', 'root'));
			}

			for (var i = 0; i < items.length; i++) {
				if (clear || top.content.treeData.indexOfEntry(items[i].id) < 0) {
					if (sorted) {
						top.content.treeData.addSort(new top.content.Node(items[i]));
					} else {
						top.content.treeData.add(new top.content.Node(items[i]));
					}
				}
			}

			top.content.drawTree();
			break;
		case 'drawTree':
			top.content.drawTree();
			break;
		case 'makeTreeEntry':
			top.content.treeData.makeNewEntry(args[1]);
			break;
		case 'updateTreeEntry':
			top.content.treeData.updateEntry(args[1]);
			break;
		case 'deleteTreeEntry':
			top.content.treeData.deleteEntry(args[1]);
			break;
		case "exit_doc_question":
			var yes = args, no = args;
			yes[0] = 'exit_doc_question_yes';
			no[0] = 'exit_doc_question_no';
			WE().util.showConfirm(caller, "", WE().consts.g_l.alert.exit_doc_question.tools, yes, no);
			break;
		case "updateTitle":
			top.content.editor.edheader.document.getElementById("titlePath").innerText = args[1];
			break;
		case "setTool":
			current = args[1];
			break;
		case "revertTab":
			weTabs.setActiveTab(current);
			break;
		case "setHotTab":
			top.content.hot = false;
			setTab(args[1]);
			break;
		case "setIconOfDocClass":
		case "we_customer_selector":
			top.opener.top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
			break;
		default:
			WE().t_e("non explicit module command to main frame", args);
			top.opener.top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function setTab(module) {
	if (top.content.hot) {
		WE().util.showConfirm(window, "", WE().consts.g_l.alert.discard_changed_data, ["setHotTab", module], ["revertTab"]);
		return;
	}
	if (typeof "top.content.usetHot" == "function") {
		top.content.usetHot();
	}
	current = module;
	top.content.location.replace(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=" + module);
}

function getFrameset() {//FIXME: we use this function temporary until frames in modules are obsolete
	return WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=" + current;
}