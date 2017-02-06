/* global top */

/**
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

function setDir(id) {
	if (currentType === "user") {
		currentDir = id;
		top.fscmd.location.replace(top.queryString(WE().consts.selectors.CMD, id));
	} else {
		top.fscmd.location.replace(top.queryString(WE().consts.selectors.SETDIR, id));
	}
}

function selectFile(id) {
	var a = top.document.getElementsByName("fname")[0];
	if (id) {
		e = top.getEntry(id);
		if (currentType !== "user" || !e.isFolder) {
			if (a.value != e.text &&
							a.value.indexOf(e.text + ",") == -1 &&
							a.value.indexOf("," + e.text + ",") == -1 &&
							a.value.indexOf("," + e.text + ",") == -1) {

				a.value = a.value ?
								(a.value + "," + e.text) :
								e.text;
			}
			top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "#DFE9F5";
			top.currentPath = e.path;
			top.currentID = id;
		}
	} else {
		a.value = "";
		top.currentPath = "";
	}
}

function queryString(what, id, o) {
	if (!o) {
		o = top.order;
	}
	return options.formtarget + 'what=' + what + '&table=' + options.table + '&id=' + id + "&order=" + o + "&filter=" + currentType;
}

function press_ok_button() {
	if (top.document.getElementsByName("fname")[0].value === '' && top.currentType != 'group') {
		top.exit_close();
	} else {
		top.exit_open();
	}
}