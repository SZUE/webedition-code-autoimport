/* global WE */

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
we_cmd_modules.customer = function (args, url) {
	switch (args[0]) {
		case "edit_settings_customer":
			new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=settings", "customer_settings", 520, 300, true, false, true);
			break;
		case "customer_edit":
		case "customer_edit_ifthere":
			new (WE().util.jsWindow)(window, url, "edit_module", 970, 760, true, true, true, true);
			return true;
		case "new_customer":
		case "save_customer":
		case "delete_customer":
		case "exit_customer":
		case "show_search":
			WE().layout.pushCmdToModule(args);
			return true;
		case "unlock"://FIXME:????
			we_repl(window.load, url, args[0]);
			return true;
		case "customer_applyWeDocumentCustomerFilterFromFolder":
			if (!WE().util.we_sbmtFrm(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url)) {
				url += "&we_transaction=" + args[2];
				we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url, args[0]);
			}
			return true;
	}
	return false;
};