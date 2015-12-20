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


function loadTable() {
	if (hiddenCmd.dispatch) {
		hiddenCmd.dispatch("refresh");
	} else {
		setTimeout(loadTable, 1000);
	}
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
			setTimeout("setStatusDone(\"" + dict + "\")", 3000);
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
	setTimeout(setStatusDone, 3000);
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
	setTimeout(setAppletCode, 1000);
}

function hideDictSelector() {
	setVisible("dictSelector", false);
	document.getElementById("selector").style.height = "320px";
	setVisible("addButt", true);
}

function checkApplet() {
	if (appletActiv && document.spellchecker.uploadFinished && document.spellchecker.uploadFinished()) {
		if (document.spellchecker.packingFinished()) {
			top.we_showMessage(g_l.dict_saved, WE().consts.message.WE_MESSAGE_NOTICE, window);
		}
		hideDictSelector();
		appletActiv = false;
		loadTable();
		return;
	}

	setTimeout(checkApplet, 2000);
}

function deleteDict(name) {
	if (confirm(WE().util.sprintf(g_l.ask_dict_del, name))) {
		hiddenCmd.dispatch("deleteDict", name);
	}
}
