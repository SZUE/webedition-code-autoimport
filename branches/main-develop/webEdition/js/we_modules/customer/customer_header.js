/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 13162 $
 * $Author: mokraemer $
 * $Date: 2016-12-05 02:03:38 +0100 (Mo, 05. Dez 2016) $
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

var aTabs = WE().util.getDynamicVar(document, 'loadVarCustomerHeader', 'data-customerHeader');

function setTab(tab) {
	top.content.activ_tab = tab;
	parent.edbody.we_cmd("switchPage", tab);
}

function loaded() {
	weTabs.setFrameSize()
	if (top.content.activ_tab) {
		document.getElementById(aTabs[top.content.activ_tab]).className = "tabActive";
	} else {
		document.getElementById("common").className = "tabActive";
	}
}
