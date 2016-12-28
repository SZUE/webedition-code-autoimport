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
var weTabs = new (WE().layout.we_tabs)(document, window);

function setTab(tab) {
	switch (tab) {

		// Add new tab handlers here

		default: // just toggle content to show
			window.parent.edbody.document.we_form.pnt.value = "edbody";
			window.parent.edbody.document.we_form.tabnr.value = tab;
			window.parent.edbody.submitForm();
			break;
	}
	top.content.activ_tab = tab;
}
