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
 * @package    we_app
 * @subpackage we_app_js
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * mark application entry
 *
 * @return void
 */
function mark() {
	var elem = document.getElementById('mark');
	elem.style.display = 'inline';
}

/**
 * unmark application entry
 *
 * @return void
 */
function unmark() {
	var elem = document.getElementById('mark');
	elem.style.display = 'none';
}

/**
 * set the frame size after resizing
 *
 * @return void
 */
function setFrameSize() {
	if (document.getElementById('we_ui_controls_Tabs_Container').offsetWidth > 0) {
		var fs = parent.document.getElementsByTagName("FRAMESET")[0];
		var tabsHeight = document.getElementById('main').offsetHeight;
		var fsRows = fs.rows.split(',');
		fsRows[0] = tabsHeight;
		fs.rows = fsRows.join(",");
	} else {
		setTimeout(setFrameSize, 100);
	}
}

/**
 * name of the title path
 */
var titlePathName = '';

/**
 * group of the title path
 */
var titlePathGroup = '';

/**
 * set the title path of the entry
 *
 * @static
 * @param {string} group
 * @param {string} name
 * @return void
 */
function setTitlePath(group, name) {
	if (group) {
		titlePathGroup = group;
	}
	if (name) {
		titlePathName = name;
	}

	var titlePathGroupElem = document.getElementById('titlePathGroup');
	var titlePathNameElem = document.getElementById('titlePathName');


	if (titlePathGroupElem) {
		if (titlePathGroup === "") {
			titlePathGroup = titlePathGroupElem.innerHTML;
		}
		titlePathGroupElem.innerHTML = titlePathGroup;
	}
	if (titlePathNameElem) {
		if (titlePathName === "") {
			titlePathName = titlePathNameElem.innerHTML;
		}
		titlePathNameElem.innerHTML = titlePathName;
	}
}
