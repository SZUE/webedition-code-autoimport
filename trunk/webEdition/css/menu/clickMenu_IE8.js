/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

var menuActive = false;

function topMenuClick(elem) {
	menuActive = (menuActive) ? false : true;
	if (menuActive) {
		elem.className = "top_div click";
	} else {
		elem.className = "top_div";
	}
}

function topMenuHover(elem) {
	var left = elem.firstChild.childNodes[1].offsetLeft;
	var liElems = elem.parentNode.childNodes;
	if (left < -1000) {
		for (var i = 0; i < liElems.length; i++) {
			liElems[i].firstChild.className = "top_div";
		}
		menuActive = false;
	}
}
