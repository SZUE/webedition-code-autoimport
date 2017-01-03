/*
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
/* global WE, top */
'use strict';
WE().util.loadConsts(document, "g_l.liveUpdate");

function deleteEntries() {
	document.we_form.log_cmd.value = "deleteEntries";
	document.we_form.submit();
}

function lastEntries() {
	document.we_form.log_cmd.value = "lastEntries";
	document.we_form.submit();
}

function nextEntries() {
	document.we_form.log_cmd.value = "nextEntries";
	document.we_form.submit();
}

function confirmDelete() {
	if (window.confirm(WE().consts.g_l.liveUpdate.confirmDelete)) {
		deleteEntries();
	}
}

function setTab(tab) {
	top.updatecontent.location = "?section=" + tab;
}