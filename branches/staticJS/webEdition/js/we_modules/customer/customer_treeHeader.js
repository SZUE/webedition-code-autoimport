/**
 * webEdition CMS
 *
 * $Rev: 8972 $
 * $Author: mokraemer $
 * $Date: 2015-01-13 21:33:12 +0100 (Di, 13. Jan 2015) $
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

function applySort() {
	document.we_form_treeheader.pnt.value = "cmd";
	document.we_form_treeheader.cmd.value = "applySort";
	submitForm("", "", "", "we_form_treeheader");
}

function addSorting(sortname) {
	len = document.we_form_treeheader.sort.options.length;
	for (i = 0; i < len; i++) {
		if (document.we_form_treeheader.sort.options[i].value == sortname) {
			return;
		}
	}
	document.we_form_treeheader.sort.options[len] = new Option(sortname, sortname);

}