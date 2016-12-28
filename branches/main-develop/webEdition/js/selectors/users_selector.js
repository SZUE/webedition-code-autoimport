/* global top, fileSelect */

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
'use strict';

function setDir(id) {
	if (top.fileSelect.data.currentType === "user") {
		top.fileSelect.data.currentDir = id;
		top.fscmd.location.replace(top.queryString(WE().consts.selectors.CMD, id));
	} else {
		top.fscmd.location.replace(top.queryString(WE().consts.selectors.SETDIR, id));
	}
}

function selectFile(id) {
	var a = top.document.getElementsByName("fname")[0];
	if (id) {
		var e = top.getEntry(id);
		if (top.fileSelect.data.currentType !== "user" || !e.isFolder) {
			if (a.value != e.text &&
							a.value.indexOf(e.text + ",") == -1 &&
							a.value.indexOf("," + e.text + ",") == -1 &&
							a.value.indexOf("," + e.text + ",") == -1) {

				a.value = a.value ?
								(a.value + "," + e.text) :
								e.text;
			}
			top.fsbody.document.getElementById("line_" + id).classList.add("selected");
			top.fileSelect.data.currentPath = e.path;
			top.fileSelect.data.currentID = id;
		}
	} else {
		a.value = "";
		top.fileSelect.data.currentPath = "";
	}
}

function queryString(what, id, o) {
	if (!o) {
		o = top.fileSelect.data.order;
	}
	return top.fileSelect.options.formtarget + 'what=' + what + '&table=' + top.fileSelect.options.table + '&id=' + id + "&order=" + o + "&filter=" + top.fileSelect.data.currentType;
}

function press_ok_button() {
	if (top.document.getElementsByName("fname")[0].value === '' && top.fileSelect.data.currentType != 'group') {
		top.exit_close();
	} else {
		top.exit_open();
	}
}