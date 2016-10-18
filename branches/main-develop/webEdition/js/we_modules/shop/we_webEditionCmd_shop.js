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

we_cmd_modules.shop = function (args, url) {
	switch (args[0]) {
		case "edit_settings_shop":
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=pref_shop", "pref_shop", -1, -1, 470, 600, true, false, true);
			break;
		case "shop_edit_ifthere":
		case "shop_edit":
			new (WE().util.jsWindow)(this, url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			break;
		case "pref_shop":
			WE().layout.pushCmdToModule(args);
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 470, 600, true, true, true, false);
			break;
		case "edit_shop_status":
			WE().layout.pushCmdToModule(args);
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 700, 580, true, true, true, false);
			break;
		case "edit_shop_vat_country":
			WE().layout.pushCmdToModule(args);
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 700, 780, true, true, true, false);
			break;
		case "edit_shop_categories":
			WE().layout.pushCmdToModule(args);
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 740, 650, true, false, true, false);
			break;
		case "edit_shop_vats":
			WE().layout.pushCmdToModule(args);
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 650, 650, true, false, true, false);
			break;
		case "edit_shop_shipping":
			WE().layout.pushCmdToModule(args);
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 700, 600, true, false, true, false);
			break;
		case "payment_val":
			WE().layout.pushCmdToModule(args);
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 520, 720, true, false, true, false);
			break;
		case 'yearCmd'://pseudocommand
		case "revenue_view":
		case "new_article":
		case "delete_shop":
			WE().layout.pushCmdToModule(args);
			return true;
		case "exit_shop":
			top.opener.top.we_cmd("exit_modules");
			break;
		default:
			return false;
	}
	return true;
};