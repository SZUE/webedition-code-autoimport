/* global WE, top, YAHOO */

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

var Rows = 1;

function AllItems() {
	if (document.we_form.selectAll.value == 0) {
		temp = true;
		document.we_form.selectAll.value = 1;
	} else {
		temp = false;
		document.we_form.selectAll.value = 0;
	}
	for (var x = 0; x < document.we_form.elements.length; x++) {
		var y = document.we_form.elements[x];
		if (y.name == 'ID[]') {
			y.checked = temp;
		}
	}
}
function SubmitForm() {
	document.we_form.submit();
}

function next() {
	document.we_form.Offset.value = parseInt(document.we_form.Offset.value) + Rows;
	SubmitForm();
}
function prev() {
	document.we_form.Offset.value = parseInt(document.we_form.Offset.value) - Rows;
	SubmitForm();
}
function jump(val) {
	document.we_form.Offset.value = val;
	SubmitForm();
}

function loadHeaderFooter(cmd) {
	top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=glossary&pnt=edheader&cmd=glossary_view_type&cmdid=" + cmd;
	top.content.editor.edfooter.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=glossary&pnt=edfooter&cmd=glossary_view_type&cmdid=" + cmd;
}