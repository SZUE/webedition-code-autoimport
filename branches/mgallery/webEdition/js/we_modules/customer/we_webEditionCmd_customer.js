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
function we_cmd_customer() {
	var args = arguments[0],
		url = arguments[1];

	switch (args[0]) {

		case "customer_edit":
		case "customer_edit_ifthere":
			new (WE().util.jsWindow)(this, url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			return true;
		case "new_customer":
		case "save_customer":
		case "delete_customer":
		case "exit_customer":
		case "show_admin":
		case "show_sort_admin":
		case "show_customer_settings":
		case "show_search":
		case "import_customer":
		case "export_customer":
			var wind = WE().util.jsWindow.prototype.find('edit_module');
			if (wind) {
				wind.content.we_cmd(args[0]);
				wind.focus();
			}
			return true;
		case "unlock"://FIXME:????
			we_repl(self.load, url, args[0]);
			return true;
		case "customer_applyWeDocumentCustomerFilterFromFolder":
			if (!we_sbmtFrm(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url)) {
				url += "&we_transaction=" + args[2];
				we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url, args[0]);
			}
			return true;
	}
	return false;
}