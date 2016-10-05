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

	switch (args[0]) {
		default:
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}
