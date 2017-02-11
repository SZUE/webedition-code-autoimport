/* global top,WE, self */

/**
 * webEdition CMS
 *
 * $Rev: 13200 $
 * $Author: mokraemer $
 * $Date: 2016-12-28 01:33:14 +0100 (Mi, 28. Dez 2016) $
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

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case 'setProgress':
			var data = args[1];
			var win = (data.win ? parent[data.win] : parent);
			win.setProgress(data.progress);
			win.setProgressText(data.name, data.text);
			break;
		case 'disableBackNext':
			WE().layout.button.disable(top[args[1]].document, "back");
			WE().layout.button.disable(top.top[args[1]].document, "next");
			break;
		case 'delFilesNOK':
			new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=delInfo", "we_delinfo", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true);
			break;
		case 'reloadEditors':
			reloadEditors();
			break;
		default:
			window.parent.we_cmd.apply(caller, Array.prototype.slice.call(arguments));

	}
}

function reloadEditors() {
	var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
	for (var frameId in _usedEditors) {

		if (_usedEditors[frameId].getEditorIsActive()) { // reload active editor
			_usedEditors[frameId].setEditorReloadAllNeeded(true);
			_usedEditors[frameId].setEditorIsActive(true);

		} else {
			_usedEditors[frameId].setEditorReloadAllNeeded(true);
		}
	}

//reload tree
	top.opener.we_cmd("load", top.opener.top.treeData.table, 0);

}