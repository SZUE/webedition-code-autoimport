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
function clearBlockLog() {
	if (confirm(WE().consts.g_l.prefs.clear_log_question)) {
		document.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=show_formmail_block_log&clearlog=1";
	}
}

function clearEntry(id, ip) {
	var txt = WE().consts.g_l.prefs.clear_block_entry_question;

	if (confirm(txt.replace(/%s/, ip))) {
		document.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=show_formmail_block_log&clearEntry=" + id;
	}
}

function clearLog() {
	if (confirm(WE().consts.g_l.prefs.clear_log_question)) {
		document.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=show_formmail_log&clearlog=1";
	}
}