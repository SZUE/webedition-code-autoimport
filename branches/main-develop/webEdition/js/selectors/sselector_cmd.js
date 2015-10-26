/* global top, WE */

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

function drawNewFolder() {
	for (var i = 0; i < top.allentries.length; i++) {
		if ((elem = top.fsbody.document.getElementById(top.allentries[i]))) {
			elem.style.backgroundColor = 'white';
		}
	}
	drawDir(top.currentDir, "new_folder");
}

function setFilter(filter) {
	top.currentFilter = filter;
	drawDir(top.currentDir);
}


function selectFile(fid) {
	var i;
	if (fid !== "/") {
		top.currentID = top.sitepath + top.rootDir + top.currentDir + ((top.currentDir !== "/") ? "/" : "") + fid;
		top.currentName = fid;
		top.document.getElementsByName("fname")[0].value = fid;
		if (top.fsbody.document.getElementById(fid)) {
			for (i = 0; i < top.allentries.length; i++) {
				if (top.fsbody.document.getElementById(top.allentries[i]))
					top.fsbody.document.getElementById(top.allentries[i]).style.backgroundColor = 'white';
			}
			top.fsbody.document.getElementById(fid).style.backgroundColor = '#DFE9F5';
		}
	} else {
		top.currentID = top.sitepath;
		top.currentName = fid;
		top.document.getElementsByName("fname")[0].value = fid;
		if (top.fsbody.document.getElementById(fid)) {
			for (i = 0; i < top.allentries.length; i++) {
				if (top.fsbody.document.getElementById(top.allentries[i]))
					top.fsbody.document.getElementById(top.allentries[i]).style.backgroundColor = 'white';
			}
			top.fsbody.document.getElementById(fid).style.backgroundColor = '#DFE9F5';
		}
	}
}


function reorderDir(dir, order) {
	setTimeout('top.fsbody.location="we_sselector_body.php?dir=' + dir + '&ord=' + order + '&file=' + top.currentFilter + '&curID=' + encodeURI(top.currentID) + '"', 100);
}

function selectDir() {
	if (arguments[0]) {
		top.currentDir = top.currentDir + (top.currentDir === "/" ? "" : "/") + arguments[0];
		top.addOptionh(arguments[0], top.currentDir);
	}

	if (top.currentDir.substring(0, 12) === "/webEdition/" || top.currentDir === "/webEdition") {
		WE().layout.button.disable(document, "btn_new_dir_ss");
		WE().layout.button.disable(document, "btn_add_file_ss");
		WE().layout.button.disable(document, "btn_function_trash_ss");
	} else {
		WE().layout.button.enable(document, "btn_new_dir_ss");
		WE().layout.button.enable(document, "btn_add_file_ss");
		WE().layout.button.enable(document, "btn_function_trash_ss");
	}

	drawDir(top.currentDir);

}

function goUp() {
	var a = top.document.getElementById("lookin").options;
	if (a.length - 2 > -1) {
		setDir(a[a.length - 2].value);
	} else {
		top.we_showMessage(WE().consts.g_l.sfselector.already_root, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function delFile() {
	if ((top.currentID !== "") && (top.document.getElementsByName("fname")[0].value !== "")) {
		top.fscmd.location = "we_sselector_cmd.php?cmd=delete_file&fid=" + top.currentID + "&ask=" + arguments[0];
	} else {
		top.we_showMessage(WE().consts.g_l.sfselector.edit_file_nok, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}
