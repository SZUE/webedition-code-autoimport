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
'use strict';

WE().util.loadConsts(document, "g_l.spellcheck");
/*			dict_saved: "<?= (g_l('modules_spellchecker', '[dict_saved]')); ?>",
 ask_dict_del: "<?= g_l('modules_spellchecker', '[ask_dict_del]'); ?>"
 checking: "<?= g_l('modules_spellchecker', '[checking]'); ?>",
 finished: "<?= (g_l('modules_spellchecker', '[finished]')); ?>"
 */
var activ_tab = 0;

function loadTable() {
	/*if (hiddenCmd.dispatch) {
	 hiddenCmd.dispatch("refresh");
	 } else {
	 window.setTimeout(loadTable, 1000);
	 }*/
}
function setTab(tab) {
	toggle("tab" + activ_tab);
	toggle("tab" + tab);
	activ_tab = tab;
}

function selectDict(dict) {
	if (document.spellcheckerCmd.isReady) {
		if (document.spellcheckerCmd.isReady()) {
			document.spellcheckerCmd.setDict(dict);
			window.setTimeout(setStatusDone, 3000, dict);
		}
	}
}

function setStatusDone(dict) {
	if (document.spellcheckerCmd.isDictReady) {
		if (document.spellcheckerCmd.isDictReady()) {
			setVisible("updateBut_" + dict, true);
			setVisible("updateIcon_" + dict, false);
			return;
		}
	}
	window.setTimeout(setStatusDone, 3000);
}


function setVisible(id, visible) {
	var elem = document.getElementById(id);
	elem.style.display = (visible ? "block" : "none");
}

function toggle(id) {
	var elem = document.getElementById(id);
	elem.style.display = (elem.style.display == "none" ? "block" : "none");
}

function showDictSelector() {
	setVisible("addButt", false);
	document.getElementById("selector").style.height = "100px";
	setVisible("dictSelector", true);
}

function hideDictSelector() {
	setVisible("dictSelector", false);
	document.getElementById("selector").style.height = "320px";
	setVisible("addButt", true);
}

function checkApplet() {
	WE().util.showMessage(WE().consts.g_l.spellcheck.dict_saved, WE().consts.message.WE_MESSAGE_NOTICE, window);
	hideDictSelector();
	loadTable();
	return;
}

function deleteDict(name) {
	if (window.confirm(WE().util.sprintf(WE().consts.g_l.spellcheck.ask_dict_del, name))) {
		//hiddenCmd.dispatch("deleteDict", name);
	}
}

function updateDict(dict) {
	setVisible("updateBut_" + dict, false);
	setVisible("updateIcon_" + dict, true);
}

function dispatch(cmd) {
	document.dispatcherForm.elements["cmd[0]"].value = cmd;
	for (var i = 1; i < arguments.length; i++) {
		document.dispatcherForm.elements["cmd[" + i + "]"].value = arguments[i];
	}
	document.dispatcherForm.submit();
}
