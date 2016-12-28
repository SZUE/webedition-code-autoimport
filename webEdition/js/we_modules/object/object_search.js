/* global WE, top, _EditorFrame */

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
var searchObj = WE().util.getDynamicVar(document, 'loadVarObject_search', 'data-searchObj');

_EditorFrame.setEditorIsHot(false);

function next() {
	document.we_form.elements.SearchStart.value = parseInt(document.we_form.elements.SearchStart.value) + searchObj.anzahl;
	top.we_cmd("reload_editpage");
}
function back() {
	document.we_form.elements.SearchStart.value = parseInt(document.we_form.elements.SearchStart.value) - searchObj.anzahl;
	top.we_cmd("reload_editpage");
}

function setOrder(order) {
	var foo = document.we_form.elements.Order.value;

	if (((foo.substring(foo.length - 5, foo.length) == " DESC") && (foo.substring(0, order.length - 5) == order)) || foo != order) {
		document.we_form.elements.Order.value = order;
	} else {
		document.we_form.elements.Order.value = order + " DESC";
	}
	top.we_cmd("reload_editpage");
}

function setWs(path, id) {
	document.we_form.elements["we_" + searchObj.name + "_WorkspacePath"].value = path;
	document.we_form.elements["we_" + searchObj.name + "_WorkspaceID"].value = id;
	top.we_cmd("reload_editpage");
}

function toggleShowVisible(c) {
	c.value = (c.checked ? 1 : 0);
	document.we_form.elements.SearchStart.value = 0;
	top.we_cmd("reload_editpage");
}