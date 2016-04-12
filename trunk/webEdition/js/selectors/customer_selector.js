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

function setDir(id) {
	if (id >= 0 || id < 0) {
		e = getEntry(id);
		currentDir = id;
		path = e.path;
		if (path == "/") {
			path = 0;
		}
	} else {
		path = id;
	}
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.SETDIR, path));
}