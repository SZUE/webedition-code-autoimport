/**
 * webEdition CMS
 *
 * $Rev: 12857 $
 * $Author: mokraemer $
 * $Date: 2016-09-21 19:05:02 +0200 (Mi, 21 Sep 2016) $
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

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var cats, found, i;

	switch (args[0]) {
		case 'we_selector_directory':
		case 'we_selector_image':
		case 'we_selector_document':
			new (WE().util.jsWindow)(this, url, 'we_fileselector', -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true);
			break;
		case 'we_selector_file':
			new (WE().util.jsWindow)(this, url, 'we_selector', -1, -1, WE().consts.size.windowSelect.width, WE().consts.size.windowSelect.height, true, true, true, true);
			break;
		case "we_navigation_dirSelector":
			new (WE().util.jsWindow)(this, url, "we_navigation_dirselector", -1, -1, 600, 400, true, true, true);
			break;
		case 'browse_server':
			new (WE().util.jsWindow)(this, url, 'browse_server', -1, -1, 840, 400, true, false, true);
			break;
		case 'we_selector_category':
			new (WE().util.jsWindow)(this, url, 'we_catselector', -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.width, true, true, true);
			break;
		case 'fileupload_callbackCSVImport':
			top.console.log('drinne wizbody new!');
			top.wizbody.handle_eventNext();
			//top.handle_eventCSVImportStep1('next');
			break;
		default:
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}
