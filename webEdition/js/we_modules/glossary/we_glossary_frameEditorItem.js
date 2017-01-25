/* global WE, top */

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
'use strict';

function toggle(id) {
	var elem = document.getElementById(id);
	elem.style.display = (elem.style.display === "none" ? "block" : "none");
}

function setVisible(id, visible) {
	var elem = document.getElementById(id);
	elem.style.display = (visible ? "block" : "none");
}

function showType(type) {
	document.getElementById("type_abbreviation").style.display = "none";
	document.getElementById("type_acronym").style.display = "none";
	document.getElementById("type_foreignword").style.display = "none";
	document.getElementById("type_link").style.display = "none";
	document.getElementById("type_textreplacement").style.display = "none";
	document.getElementById("type_" + type).style.display = "block";
	document.we_form.cmd.value = "edit_" + type;
	if (type === "link") {
		document.getElementsByClassName("btn_direction_weMultibox_table")[0].style.display = "inline";
		document.getElementById("text_weMultibox").style.display = "inline";
		document.getElementById("div_weMultibox_2").style.display = "block";
		document.getElementById("div_weMultibox_3").style.display = "block";
		document.getElementById("div_weMultibox_4").style.display = "block";
		document.getElementById("div_weMultibox_5").style.display = "block";
		document.getElementById("div_weMultibox_6").style.display = "block";
		document.getElementById("div_weMultibox_7").style.display = "block";
		showLinkMode("intern");
	} else {
		document.getElementsByClassName("btn_direction_weMultibox_table")[0].style.display = "none";
		document.getElementById("text_weMultibox").style.display = "none";
		document.getElementById("div_weMultibox_2").style.display = "none";
		document.getElementById("div_weMultibox_3").style.display = "none";
		document.getElementById("div_weMultibox_4").style.display = "none";
		document.getElementById("div_weMultibox_5").style.display = "none";
		document.getElementById("div_weMultibox_6").style.display = "none";
		document.getElementById("div_weMultibox_7").style.display = "none";
	}
}

function showLinkMode(mode) {
	document.getElementById("mode_intern").style.display = "none";
	document.getElementById("mode_extern").style.display = "none";
	document.getElementById("mode_object").style.display = "none";
	document.getElementById("mode_category").style.display = "none";
	document.getElementById("mode_" + mode).style.display = "block";
	if (mode === "category") {
		showLinkModeCategory("intern");
	}
}

function showLinkModeCategory(mode) {
	document.getElementById("mode_category_intern").style.display = "none";
	document.getElementById("mode_category_extern").style.display = "none";
	document.getElementById("mode_category_" + mode).style.display = "block";
}

function setDisplay(id, display) {
	document.getElementById(id).style.display = display;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function setHot() {
	top.content.editor.edheader.document.getElementById("mark").style.display = "inline";
	top.hot = true;
}

function we_cmd() {
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "populateWorkspaces":
			document.we_form.cmd.value = args[0];
			document.we_form.tabnr.value = top.content.activ_tab;
			document.we_form.pnt.value = "cmd";
			submitForm("cmd");
			break;
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(caller, url, "we_docselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));

	}
}

function submitForm(target, action, method) {
	var f = window.document.we_form;
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=glossary");
	f.method = (method ? method : "post");
	f.submit();
}

function loadHeaderFooter(type, link, category) {
	top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=glossary&pnt=edheader";
	top.content.editor.edfooter.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=glossary&pnt=edfooter";
	showType(type);
	if (link) {
		showLinkMode(link);
	}
	if (category) {
		showLinkModeCategory(category);
	}
}