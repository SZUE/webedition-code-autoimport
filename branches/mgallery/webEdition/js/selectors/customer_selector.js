/**
 * webEdition CMS
 *
 * $Rev: 9089 $
 * $Author: mokraemer $
 * $Date: 2015-01-21 16:07:44 +0100 (Mi, 21. Jan 2015) $
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
	top.fscmd.location.replace(top.queryString(queryType.SETDIR, path));
}