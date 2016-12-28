/* global WE, top */

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
'use strict';

function closeOnEscape() {
	return true;
}

function showPhpInfo() {
	document.getElementById("info").style.display = "none";
	document.getElementById("more").style.display = "block";
	document.getElementById("phpinfo").src = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=phpinfo";
}

function showInfoTable() {
	document.getElementById("info").style.display = "block";
	document.getElementById("more").style.display = "none";
}