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
var activ_tab = 1;

function toggle(id) {
	var elem = document.getElementById(id);
	elem.style.display = (elem.style.display == "none" ? "block" : "none");
}

function previewVersion(table, ID, version, newID) {
	top.opener.top.we_cmd("versions_preview", table, ID, version, newID);
}

function setTab(tab) {
	toggle("tab" + activ_tab);
	toggle("tab" + tab);
	activ_tab = tab;
}