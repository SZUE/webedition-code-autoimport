/* global WE, top, data, WE_NAVIID */

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
var naviEditor = WE().util.getDynamicVar(document, 'loadVarNavi', 'data-navi');

function save() {
	var dir = document.we_form.ParentID;
	window.opener.we_cmd("add_navi", naviEditor.naviID, encodeURIComponent(document.we_form.Text.value), dir.options[dir.selectedIndex].value, document.we_form.Ordn.value);
	window.close();
}

function setSaveState() {
	WE().layout.button.switch_button_state(document, 'save', (document.we_form.Text.value !== '' ? 'enabled' : 'disabled'));
}

function changeOrder(elem) {
	document.we_form.OrdnTxt.value = document.we_form.OrdnSelect.options[document.we_form.OrdnSelect.selectedIndex].text;
	document.we_form.Ordn.value = elem.value;
}

function queryEntries(id) {
		WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=GetNaviItems","nid=" + id, function (weResponse) {
		document.getElementById("details").innerHTML = "";
		if (weResponse.Success) {
				var items = weResponse.DataArray.data;
				for (var s in items) {
					document.getElementById("details").innerHTML += '<div style="width: 40px; float: left;">' + s + '</div><div style="width: 220px;">' + items[s][1] + "</div>";
				}
			}
	});
}