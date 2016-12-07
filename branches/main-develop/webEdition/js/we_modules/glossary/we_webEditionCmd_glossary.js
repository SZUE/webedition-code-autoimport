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
we_cmd_modules.glossary = function (args, url) {
	var wind;
	switch (args[0]) {
		case "edit_settings_glossary":
			we_cmd("glossary_settings");
			break;
		case "glossary_check":

			var _EditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();
			if (_EditorFrame !== false &&
							_EditorFrame.getEditorType() === "model" &&
							(
											_EditorFrame.getEditorContentType() === WE().consts.contentTypes.WEDOCUMENT ||
											_EditorFrame.getEditorContentType() === WE().consts.contentTypes.OBJECT_FILE
											)
							) {

				var transaction = _EditorFrame.getEditorTransaction();
				url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=glossary_check&we_cmd[2]=" + transaction + "&we_cmd[3]=checkOnly";
				new (WE().util.jsWindow)(window, url, "glossary_check", 730, 400, true, false, true);

			} else {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
			}
			break;
		case "glossary_edit_acronym":
		case "glossary_edit_abbreviation":
		case "glossary_edit_foreignword":
		case "glossary_edit_link":
		case "glossary_edit_textreplacement":
		case "glossary_edit_ifthere":
			new (WE().util.jsWindow)(window, url, "edit_module", 970, 760, true, true, true, true);
			break;
		case "glossary_settings":
			WE().util.jsWindow.prototype.focus('edit_module');
			new (WE().util.jsWindow)(window, url, "edit_glossary_settings", 490, 250, true, true, true, true);
			break;
		case "glossary_dictionaries":
			new (WE().util.jsWindow)(window, url, "edit_glossary_dictionaries", 490, 250, true, true, true, true);
			break;
		case "new_glossary_acronym":
		case "new_glossary_abbreviation":
		case "new_glossary_foreignword":
		case "new_glossary_link":
		case "new_glossary_textreplacement":
		case "exit_glossary":
		case "save_exception":
		case "save_glossary":
		case "delete_glossary":
			WE().layout.pushCmdToModule(args);
			return true;
		case "unlock"://FIXME:???
			we_repl(window.load, url, args[0]);
			break;
		default:
			if ((args[0].substr(0, 15) === "GlossaryXYZnew_")) {
				tempargs = args[0].split("\XYZ");

				wind = WE().util.jsWindow.prototype.find('edit_module');
				if (wind) {
					wind.content.we_cmd(tempargs[1], tempargs[2]);
					wind.focus();
				}
				break;
			}
			return false;
	}
	return true;
};