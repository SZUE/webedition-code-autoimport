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
var fdl = WE().util.getDynamicVar(document, 'loadVarFdl', 'data-fdl');


function init() {
	top.initPrefs();
}

function save() {
	top.savePrefs();
	top.previewPrefs();
	refresh();
	top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
	window.close();
}

function preview() {
	top.previewPrefs();
	refresh();
}

function exit_close() {
	//previewPrefs();
	refresh();
	top.exitPrefs();
	window.close();
}

function refresh() {
	WE().layout.cockpitFrame.rpc('', '', '', '', '', fdl.refreshCmd);
}

function ajaxCallbackResetLogins(weResponse) {
	if (weResponse) {
		if (weResponse.DataArray.data === "true") {
			refresh();
			top.we_showMessage(WE().consts.g_l.cockpit.fdl.kv_failedLogins, WE().consts.message.WE_MESSAGE_NOTICE, window);
			top.setTheme(_sObjId, _oSctCls[_oSctCls.selectedIndex].value);
		}
	}
}