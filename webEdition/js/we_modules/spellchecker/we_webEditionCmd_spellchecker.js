/* global WE, we_cmd_modules */

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
we_cmd_modules.spellchecker = function (args, url, caller) {
	switch (args[0]) {
		case "edit_settings_spellchecker":
			window.we_cmd("spellchecker_edit");
			break;
		case "spellchecker_edit":
			new (WE().util.jsWindow)(caller, url, "spellcheckadmin", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, false, true, false);
			return true;
	}
	return false;
};