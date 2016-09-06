/*
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 12732 $
 * $Author: mokraemer $
 * $Date: 2016-09-06 15:05:35 +0200 (Di, 06. Sep 2016) $
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