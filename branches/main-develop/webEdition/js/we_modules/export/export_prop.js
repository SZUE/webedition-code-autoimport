/* global top, WE */

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
var loaded = false;

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "switchPage":
			document.we_form.cmd.value = args[0];
			document.we_form.tabnr.value = args[1];
			submitForm();
			break;
		case "we_export_dirSelector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
			new (WE().util.jsWindow)(this, url, "we_exportselector", -1, -1, 600, 350, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(this, url, "we_catselector", -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.height, true, true, true, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(this, url, "we_selector", -1, -1, WE().consts.size.windowSelect.width, WE().consts.size.windowSelect.height, true, true, true, true);
			break;
		case "add_cat":
			document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.pnt.value = "edbody";
			document.we_form.tabnr.value = top.content.activ_tab;
			document.we_form.cat.value = args[1].allIDs.join(",");
			submitForm();
			break;
		case "del_cat":
		case "del_all_cats":
			document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.pnt.value = "edbody";
			document.we_form.tabnr.value = top.content.activ_tab;
			document.we_form.cat.value = args[1];
			submitForm();
			break;
		default:
			top.content.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

function submitForm(target, action, method) {
	var f = window.document.we_form;
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=export');
	f.method = (method ? method : "post");
	f.submit();
}
