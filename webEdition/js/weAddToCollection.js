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

var initData = WE().util.getDynamicVar(document, 'loadVarWeAddToCollection', 'data-init');

var weAddToCollection = {
	conf: {
		table: '',
		targetInsertIndex: -1,
		targetInsertPosition: -1
	},
	init: function (conf) {
		this.conf = conf;

		top.treeData.setState(top.treeData.tree_states.select);
		if (top.treeData.table != this.conf.table) {
			top.treeData.table = this.conf.table;
			we_cmd("load", this.conf.table);
		} else {
			we_cmd("load", this.conf.table);
			top.drawTree();
		}
	},
	press_ok_add: function () {
		var selection = [];
		for (var i = 1; i <= top.treeData.len; i++) {
			if (top.treeData[i].checked == 1) {
				selection.push(top.treeData[i].id);
				top.treeData.checkNode('img_' + top.treeData[i].id);
			}
		}

		if (!selection) {
			top.we_showMessage(WE().consts.g_l.main.nothing_to_move, WE().consts.message.WE_MESSAGE_NOTICE, window);
			return;
		}

		// check if selected target exists
		var acStatus = WE().layout.weSuggest.checkRequired(window);
		if (acStatus.running) {
			window.setTimeout(weAddToCollection.press_ok_add, 100, '');
			return;
		}
		if (!acStatus.valid) {
			top.we_showMessage(WE().consts.g_l.main.notValidFolder, WE().consts.message.WE_MESSAGE_NOTICE, window);
			return;
		}

		we_cmd('collection_insertFiles',
			selection,
			document.getElementById('yuiAcResultDir').value,
			this.conf.targetInsertIndex,
			this.conf.targetInsertPosition,
			document.check_InsertRecursive ? document.we_form.check_InsertRecursive.value : 0
			);

		return;
	}
};
weAddToCollection.init(initData);

function we_cmd() {
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_selector_document":
			new (WE().util.jsWindow)(document, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		default:
			if (window.parent.we_cmd) {
				window.parent.we_cmd.apply(caller, args);
			}
	}
}
