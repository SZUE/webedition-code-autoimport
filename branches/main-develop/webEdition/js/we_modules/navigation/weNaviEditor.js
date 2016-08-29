/* global WE, top, YAHOO, data, WE_NAVIID */

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

function save() {
	var dir = document.we_form.ParentID;
	opener.we_cmd("add_navi", WE_NAVIID, encodeURIComponent(document.we_form.Text.value), dir.options[dir.selectedIndex].value, document.we_form.Ordn.value);
	self.close();
}

function setSaveState() {
	WE().layout.button.switch_button_state(document, 'save', (document.we_form.Text.value !== '' ? 'enabled' : 'disabled'));
}

function changeOrder(elem) {
	document.we_form.OrdnTxt.value = document.we_form.OrdnSelect.options[document.we_form.OrdnSelect.selectedIndex].text;
	document.we_form.Ordn.value = elem.value;
}

var ajaxObj = {
	handleSuccess: function (o) {
		this.processResult(o);
		if (o.responseText) {
			document.getElementById("details").innerHTML = "";
			var weResponse = JSON.parse(o.responseText);
			if (weResponse.Success) {
				var items = weResponse.DataArray.data;
				for (var s in items) {
					document.getElementById("details").innerHTML += '<div style="width: 40px; float: left;">' + s + '</div><div style="width: 220px;">' + items[s][1] + "</div>";
				}
			}
		}
	},
	handleFailure: function (o) {
		// Failure handler
	},
	processResult: function (o) {
		// This member is called by handleSuccess
	},
	startRequest: function (id) {
		YAHOO.util.Connect.asyncRequest("POST", WE().consts.dirs.WEBEDITION_DIR + "rpc.php", {
			success: this.handleSuccess,
			failure: this.handleFailure,
			scope: this
		}, "cmd=GetNaviItems&nid=" + id);
	}
};

function queryEntries(id) {
	ajaxObj.startRequest(id);
}