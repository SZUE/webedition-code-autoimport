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

function sprintf() {
	if (!arguments || arguments.length < 1)
		return;

	var argum = arguments[0];
	var regex = /([^%]*)%(%|d|s)(.*)/;
	var arr = new Array();
	var iterator = 0;
	var matches = 0;

	while (arr = regex.exec(argum)) {
		var left = arr[1];
		var type = arr[2];
		var right = arr[3];

		matches++;
		iterator++;

		var replace = arguments[iterator];

		if (type == "d")
			replace = parseInt(param) ? parseInt(param) : 0;
		else if (type == "s")
			replace = arguments[iterator];
		argum = left + replace + right;
	}
	return argum;
}
