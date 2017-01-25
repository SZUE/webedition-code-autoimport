/* global WE, top */

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

var loaded;

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_selector_file":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(caller, url, "we_docselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(caller, url, "we_dirselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_banner_dirSelector":
			new (WE().util.jsWindow)(caller, url, "we_bannerselector", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "switchPageSelect":
			document.we_form.ncmd.value = "switchPage";
			document.we_form.page.value = args[2];
			submitForm();
			break;
		case "switchPage":
			top.content.setHot();
			document.we_form.ncmd.value = args[0];
			document.we_form.page.value = args[1];
			submitForm();
			break;
		case "add_file":
		case "add_folder":
			top.content.setHot();
			document.we_form.ncmd.value = args[0];
			document.we_form.ncmdvalue.value = args[1].allIDs.join(",");
			submitForm();
			break;
		case "add_cat":
		case "add_customer":
			top.content.setHot();
			document.we_form.ncmd.value = args[0];
			document.we_form.ncmdvalue.value = args[1].allIDs.join(",");
			submitForm();
			break;
		case "del_cat":
		case "del_all_cats":
		case "del_file":
		case "del_all_files":
		case "del_folder":
		case "del_customer":
		case "del_all_customers":
		case "del_all_folders":
			top.content.setHot();
			document.we_form.ncmd.value = args[0];
			document.we_form.ncmdvalue.value = args[1];
			submitForm();
			break;
		case "delete_stat":
			top.content.setHot();
			WE().util.showConfirm(window, "", WE().consts.g_l.banner.view.deleteStatConfirm, ["delete_stat_do"]);
			break;
		case "delete_stat_do":
			document.we_form.ncmd.value = "delete_stat";
			submitForm();
			break;
		case "selector_intHrefCallback":
			// used as selector callback: args[1] is selector result
			caller.document.we_form.elements[args[2] + '_IntHref'][1].checked = true;
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function submitForm(target, action, method) {
	var f = window.document.we_form;
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=banner");
	f.method = (method ? method : "post");
	f.submit();
}

function checkData() {
	return true;
}