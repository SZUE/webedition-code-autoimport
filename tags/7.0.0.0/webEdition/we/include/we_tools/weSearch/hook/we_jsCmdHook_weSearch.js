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
function we_cmd_tool_weSearch(args, url) {
	switch (args[0]) {

/*		case "tool_weSearch_edit":
			new (WE().util.jsWindow)(window, url, "tool_window_weSearch", -1, -1, 970, 760, true, true, true, true);
			break;*/
		case "tool_weSearch_new_forDocuments":
		case "tool_weSearch_new_forTemplates":
		case "tool_weSearch_new_forObjects":
		case "tool_weSearch_new_advSearch":
		case "tool_weSearch_delete":
		case "tool_weSearch_save":
		case "tool_weSearch_exit":
			var wind = WE().util.jsWindow.prototype.find('tool_window_weSearch');
			if (wind) {
				wind.content.we_cmd(args[0]);
				wind.focus();
			}
			break;
		default:
			return false;
	}
	return true;
}