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
var prefData = WE().util.getDynamicVar(document, 'loadVarPreferences_frameset', 'data-prefData');
var weTabs = new (WE().layout.we_tabs)(document, window);

var countSaveTrys = 0;
function we_save() {
	for (var i = 0; i < prefData.tabs.length; i++) {
		document.getElementById('content').contentDocument.getElementById('setting_' + prefData.tabs[i]).style.display = 'none';
	}

	// update setting for message_reporting
	WE().session.messageSettings = document.getElementById('content').contentDocument.getElementById("message_reporting").value;

	if (WE().layout.weEditorFrameController.getActiveDocumentReference().quickstart) {
		var oCockpit = WE().layout.weEditorFrameController.getActiveDocumentReference();
		var _fo = document.getElementById('content').contentDocument.forms[0];
		var oSctCols = _fo.elements['newconf[cockpit_amount_columns]'];
		var iCols = oSctCols.options[oSctCols.selectedIndex].value;
		if (iCols != oCockpit._iLayoutCols) {
			oCockpit.modifyLayoutCols(iCols);
		}
	}

	document.getElementById('content').contentDocument.getElementById('setting_save').style.display = '';
	document.getElementById('content').contentDocument.we_form.save_settings.value = 1;

	document.getElementById('content').contentDocument.we_form.submit();
}

function closeOnEscape() {
	return true;
}

function saveOnKeyBoard() {
	this.we_save();
	return true;
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	for (var i = 0; i < prefData.validTabs.length; i++) {
		try {
			content.document.getElementById('setting_' + prefData.validTabs[i]).style.display = 'none';
		} catch (e) {
		}
	}
	try {
		content.document.getElementById('setting_' + args[0]).style.display = '';
	} catch (e) {
	}
}