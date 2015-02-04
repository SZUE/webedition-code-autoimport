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

function addOption(txt, id) {
	var a = document.we_form.elements.filter;
	a.options[a.options.length] = new Option(txt, id);
	a.selectedIndex = 0;
}

function editFile() {
	if (!top.dirsel) {
		if ((top.currentID !== "") && (document.we_form.elements.fname.value !== "")) {
			if (document.we_form.elements.fname.value != top.currentName) {
				top.currentID = top.sitepath + top.rootDir + top.currentDir + "/" + document.we_form.elements.fname.value;
			}
			url = "we_sselector_editFile.php?id=" + top.currentID;
			new jsWindow(url, "we_fseditFile", -1, -1, 600, 500, true, false, true, true);
		}
		else {
			top.we_showMessage(g_l.edit_file_nok, WE_MESSAGE_ERROR, window);
		}
	}
	else {
		top.we_showMessage(g_l.edit_file_is_folder, WE_MESSAGE_ERROR, window);
	}
}

function doUnload() {
	if (jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}