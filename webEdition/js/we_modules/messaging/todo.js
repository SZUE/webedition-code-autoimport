/* global WE,chec,loadData,drawTree,check */

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
var rcpt_sel = [];

function do_confirm() {
	document.update_todo_form.submit();
}

function update_rcpts() {
	var rcpt_str = rcpt_sel[0][2];
	document.compose_form.mn_recipients.value = rcpt_str;
}

function selectRecipient() {
	var rs = encodeURI(document.compose_form.mn_recipients.value);
	new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "messaging_usel.php?we_transaction=<?= $transaction; ?>&maxsel=1&rs=" + rs, "messaging_usel", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true, false);
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function do_send(mode) {
	if (mode != 'reject') {
		var rcpt_s = encodeURI(document.compose_form.mn_recipients.value);
		document.compose_form.rcpts_string.value = rcpt_s;
	}
	document.compose_form.submit();
}
