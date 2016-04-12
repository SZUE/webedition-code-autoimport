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
var unlock = false;
var scrollToVal = 0;
var editorScrollPosTop = 0;
var editorScrollPosLeft = 0;
var weAutoCompetionFields = [];
var openedInEditor = true;
//	SEEM
//	With this var we can see, if the document is opened via webEdition
//	or just opened in the bm_content Frame, p.ex javascript location.replace or reload or sthg..
//	we must check, if the tab is switched ... etc.
var openedWithWE = true;


function we_cmd() {
	if (!unlock) {
	//var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);

		if (top.we_cmd) {
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
		}
	}
}

function closeAllModalWindows() {
	try {
		var _editor1 = self.frames[1];
		var _editor2 = self.frames[2];
		WE().util.jsWindow.prototype.closeAll(_editor1);
		WE().util.jsWindow.prototype.closeAll(_editor2);
	} catch (e) {

	}
}

function setOpenedWithWE(val) {
	openedWithWE = val;
}
