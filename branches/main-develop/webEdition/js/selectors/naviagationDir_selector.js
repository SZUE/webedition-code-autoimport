/* global top, WE, fileSelect, entries,getEntry,writeBodyDir */

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

function selectFile(id) {
	var a = top.document.getElementsByName("fname")[0];
	if (id) {
		var e = getEntry(id);
		if (a.value != e.text &&
			a.value.indexOf(e.text + ",") == -1 &&
			a.value.indexOf("," + e.text + ",") == -1 &&
			a.value.indexOf("," + e.text + ",") == -1) {

			a.value = a.value ?
				(a.value + "," + e.text) :
				e.text;

			var show = top.document.getElementById("showDiv");
			if (show) {
				show.innerHTML = top.document.getElementsByName("fname")[0].value;
			}

		}
		if (top.fsbody.document.getElementById("line_" + id)) {
			top.fsbody.document.getElementById("line_" + id).classList.add("selected");
		}
		top.fileSelect.data.currentPath = e.path;
		top.fileSelect.data.currentID = id;
		top.fileSelect.data.we_editDirID = 0;
	} else {
		top.document.getElementsByName("fname")[0].value = "";
		top.fileSelect.data.currentPath = "";
		top.fileSelect.data.we_editDirID = 0;
	}
}

function addEntry(ID, text, isFolder, path) {
	entries.push({
		ID: ID,
		text: text,
		isFolder: isFolder,
		path: path,
		contentType: (isFolder ? WE().consts.contentTypes.FOLDER : 'we/navigation')
	});
}

function writeBody(d) {
	writeBodyDir(d, WE().consts.g_l.fileselector.newFolderNavigation, false);
}

function queryString(what, id, o, we_editDirID) {
	if (!o) {
		o = top.fileSelect.data.order;
	}
	return top.fileSelect.options.formtarget + 'what=' + what + '&rootDirID=' + top.fileSelect.options.rootDirID + '&open_doc=' + top.fileSelect.options.open_doc + '&table=' + top.fileSelect.options.table + '&id=' + id + (o ? ("&order=" + o) : "") + (we_editDirID ? ("&we_editDirID=" + we_editDirID) : "");
}
