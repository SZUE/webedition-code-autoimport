/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 12758 $
 * $Author: mokraemer $
 * $Date: 2016-09-12 13:43:21 +0200 (Mo, 12. Sep 2016) $
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
WE().util.jsWindow.prototype.closeAll();

if (top.tinyMceDialog !== undefined && top.tinyMceDialog !== null) {
	var tinyDialog = top.tinyMceDialog;
	try {
		tinyDialog.close();
	} catch (err) {
	}
}

if (top.tinyMceSecondaryDialog !== undefined && top.tinyMceSecondaryDialog !== null) {
	var tinyDialog = top.tinyMceSecondaryDialog;
	try {
		tinyDialog.close();
	} catch (err) {
	}
}

if (top.opener) { // we was opened in popup
	top.opener.location.replace(WE().consts.dirs.WEBEDITION_DIR);
	top.close();
	top.opener.focus();
} else {
	top.location.replace(WE().consts.dirs.WEBEDITION_DIR);
}
