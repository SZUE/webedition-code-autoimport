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
function setInfoSize() {
	infoSize = document.body.clientHeight;
	if (infoElem = document.getElementById("info")) {
		infoElem.style.height = document.body.clientHeight - (prieviewpic = document.getElementById("previewpic") ? 160 : 0) + "px";
	}
}
function openToEdit(tab, id, contentType) {
	if (top.opener && top.opener.top.weEditorFrameController) {
		top.opener.top.weEditorFrameController.openDocument(tab, id, contentType);
	} else if (top.opener.top.opener && top.opener.top.opener.top.weEditorFrameController) {
		top.opener.top.opener.top.weEditorFrameController.openDocument(tab, id, contentType);
	} else if (top.opener.top.opener.top.opener && top.opener.top.opener.top.opener.top.weEditorFrameController) {
		top.opener.top.opener.top.opener.top.weEditorFrameController.openDocument(tab, id, contentType);
	}
}

function weWriteBreadCrumb(BreadCrumb) {
	//FIXME: this function should not need a timeout - check
	if (top.document.getElementById("fspath")) {
		top.document.getElementById("fspath").innerHTML = BreadCrumb;
	}
}