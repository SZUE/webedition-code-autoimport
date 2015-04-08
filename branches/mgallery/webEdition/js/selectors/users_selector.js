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
		top.fscmd.location.replace(top.queryString(top.queryType.CMD, id));
	} else {
		top.fscmd.location.replace(top.queryString(top.queryType.SETDIR, id));
	}
}

function selectFile(id) {
	if (id) {
		e = top.getEntry(id);
		if (currentType !== "user" || !e.isFolder) {
			if (top.fsfooter.document.we_form.fname.value != e.text &&
							top.fsfooter.document.we_form.fname.value.indexOf(e.text + ",") == -1 &&
							top.fsfooter.document.we_form.fname.value.indexOf("," + e.text + ",") == -1 &&
							top.fsfooter.document.we_form.fname.value.indexOf("," + e.text + ",") == -1) {

				top.fsfooter.document.we_form.fname.value = top.fsfooter.document.we_form.fname.value ?
								(top.fsfooter.document.we_form.fname.value + "," + e.text) :
								e.text;
			}
			top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "#DFE9F5";
			currentPath = e.path;
			currentID = id;
		}
	} else {
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
	}
}

function queryString(what, id, o) {
	if (!o) {
		o = top.order;
	}
	return options.formtarget + '?what=' + what + '&table=' + options.table + '&id=' + id + "&order=" + o + "&filter=" + currentType;
}