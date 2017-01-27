/* global top,WE */

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

function doClick(id, ct) {
	if (ct == 1) {
		if (top.metaKeys.doubleClick) {
			setDir(id);
			window.setTimeout(function () {
				top.metaKeys.doubleClick = false;
			}, 400);
		}
	} else {
		var e = top.getEntry(id);
		if (e.isFolder) {
			if (top.fileSelect.data.currentID == id) {
				top.RenameFolder(id);
			}
		} else {
			top.selectFile(id);
		}
	}
}

function setDir(id) {
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.SETDIR, id));
}