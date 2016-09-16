/* global WE, YAHOO */

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
WE().util.loadConsts(document, "g_l.versions");
var logView = WE().util.getDynamicVar(document, 'loadVarVersions_logview', 'data-logView');

var currentId = 0;

var ajaxCallbackDetails = {
	success: function (o) {
		if (o.responseText !== undefined && o.responseText !== "") {
			document.getElementById("dataContent_" + currentId + "").innerHTML = o.responseText;
		}
	},
	failure: function (o) {
	}
};

function openDetails(id) {
	currentId = id;
	var dataContent = document.getElementById("dataContent_" + id + "");
	dataContent.innerHTML = "<table border='0' width='100%' height='100%'><tr><td style='text-align:center'><i class=\"fa fa-2x fa-spinner fa-pulse\"></i></td></tr></table>";
	var otherdataContents = document.getElementsByName("dataContent");
	for (var i = 0; i < otherdataContents.length; i++) {
		if (otherdataContents[i].id != "dataContent_" + id + "") {
			otherdataContents[i].innerHTML = "";
		}
	}

	YAHOO.util.Connect.asyncRequest("POST", WE().consts.dirs.WEBEDITION_DIR + "rpc.php", ajaxCallbackDetails, "protocol=json&cns=logging/versions&cmd=GetLogVersionDetails&id=" + id + "");

}

function showAll(id) {
	var Elements = document.getElementsByName(id + "_list");
	for (var i = 0; i < Elements.length; i++) {
		Elements[i].style.display = "";
	}

	var newstartNumber = 1;
	document.getElementById("startNumber_" + id).innerHTML = newstartNumber;

	var newshowNumber = Elements.length;
	document.getElementById("showNumber_" + id).innerHTML = newshowNumber;

	document.getElementById("showAll_" + id).innerHTML = WE().consts.g_l.versions.defaultView;
	document.getElementById("showAll_" + id).onclick = function () {
		showDefault(id);
	};
	document.getElementById("back_" + id).style.display = "none";
	document.getElementById("next_" + id).style.display = "none";

}

function showDefault(id) {
	var Elements = document.getElementsByName(id + "_list");
	for (var i = 0; i < Elements.length; i++) {
		if (i >= logView.versionPerPage) {
			Elements[i].style.display = "none";
		} else {
			Elements[i].style.display = "";
		}
	}

	var newstartNumber = 1;
	document.getElementById("startNumber_" + id).innerHTML = newstartNumber;

	var newshowNumber = logView.versionPerPage;
	document.getElementById("showNumber_" + id).innerHTML = newshowNumber;

	document.getElementById("back_" + id).style.display = "none";
	document.getElementById("next_" + id).style.display = "inline";

	document.getElementById("showAll_" + id).innerHTML = WE().consts.g_l.versions.all;
	document.getElementById("showAll_" + id).onclick = function () {
		showAll(id);
	};

	document.getElementsByName("start_" + id)[0].value = 0;

}

function next(id) {
	var start = document.getElementsByName("start_" + id)[0].value;
	var newStart = parseInt(start) + logView.versionPerPage;

	var Elements = document.getElementsByName(id + "_list");
	for (var i = 0; i < Elements.length; i++) {
		if (i >= newStart && i < (newStart + logView.versionPerPage)) {
			Elements[i].style.display = "";
		} else {
			Elements[i].style.display = "none";
		}

	}

	if (newStart > (Elements.length - logView.versionPerPage)) {
		document.getElementById("next_" + id).style.display = "none";
	} else {
		document.getElementById("next_" + id).style.display = "inline";
	}
	document.getElementById("back_" + id).style.display = "inline";

	var newstartNumber = newStart + 1;
	document.getElementById("startNumber_" + id).innerHTML = newstartNumber;

	var newshowNumber = Elements.length;
	if (Elements.length > (newStart + logView.versionPerPage)) {
		newshowNumber = (newStart + logView.versionPerPage);
	}

	document.getElementById("showNumber_" + id).innerHTML = newshowNumber;

	document.getElementsByName("start_" + id)[0].value = parseInt(newStart);


}

function back(id) {
	var start = document.getElementsByName("start_" + id)[0].value;
	var newStart = parseInt(start) - logView.versionPerPage;

	var Elements = document.getElementsByName(id + "_list");
	for (var i = 0; i < Elements.length; i++) {
		if (i >= newStart && i < (newStart + logView.versionPerPage)) {
			Elements[i].style.display = "";
		} else {
			Elements[i].style.display = "none";
		}

	}

	document.getElementById("back_" + id).style.display = (newStart === 0 ? "none" : "inline");
	document.getElementById("next_" + id).style.display = "inline";

	var newstartNumber = newStart + 1;
	document.getElementById("startNumber_" + id).innerHTML = newstartNumber;


	newshowNumber = (newstartNumber + logView.versionPerPage);
	document.getElementById("showNumber_" + id).innerHTML = newshowNumber;

	document.getElementsByName("start_" + id)[0].value = parseInt(newStart);

}