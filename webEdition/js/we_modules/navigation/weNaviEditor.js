/* global WE, top, YAHOO, data, WE_NAVIID */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 11588 $
 * $Author: mokraemer $
 * $Date: 2016-03-07 13:46:42 +0100 (Mo, 07. Mär 2016) $
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

var ajaxObj = {
	handleSuccess: function (o) {
		this.processResult(o);
		if (o.responseText) {
			document.getElementById("details").innerHTML = "";
			eval(o.responseText);

			var items = weResponse.data.split(",");
			var i = 0;

			for (s in items) {
				i++;
				var row = items[s].split(":");
				if (row.length > 1) {
					document.getElementById("details").innerHTML += "<div style=\"width: 40px; float: left;\">" + i + "</div><div style=\"width: 220px;\">" + row[1] + "</div>";
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
		YAHOO.util.Connect.asyncRequest("POST", WE().consts.dirs.WEBEDITION_DIR + "rpc.php", callback, "cmd=GetNaviItems&nid=" + id);
	}
};

var callback = {
	success: ajaxObj.handleSuccess,
	failure: ajaxObj.handleFailure,
	scope: ajaxObj
};

function queryEntries(id) {
	ajaxObj.startRequest(id);
}