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

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "clear_blocklog_yes":
			document.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=show_formmail_block_log&clearlog=1";
			break;
		case "clear_block_entry_yes":
			document.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=show_formmail_block_log&clearEntry=" + args[1];
			break;
		case "clear_log_yes":
			document.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=show_formmail_log&clearlog=1";
			break;
		default :
			top.opener.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
			break;
	}
}

function clearBlockLog() {
	WE().util.showConfirm(window, "", WE().consts.g_l.prefs.clear_log_question, ["clear_blocklog_yes"]);
}

function clearEntry(id, ip) {
	var txt = WE().consts.g_l.prefs.clear_block_entry_question;
	WE().util.showConfirm(window, "", txt.replace(/%s/, ip), ["clear_block_entry_yes", id]);
}

function clearLog() {
	WE().util.showConfirm(window, "", WE().consts.g_l.prefs.clear_log_question, ["clear_log_yes"]);
}