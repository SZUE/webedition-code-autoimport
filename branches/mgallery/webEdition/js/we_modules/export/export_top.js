/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 10569 $
 * $Author: mokraemer $
 * $Date: 2015-10-12 19:56:23 +0200 (Mo, 12. Okt 2015) $
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

function setHot() {
	hot = 1;
}

function usetHot() {
	hot = 0;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}
