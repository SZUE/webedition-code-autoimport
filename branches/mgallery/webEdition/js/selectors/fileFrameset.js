/**
 * webEdition CMS
 *
 * $Rev: 9019 $
 * $Author: mokraemer $
 * $Date: 2015-01-16 23:04:21 +0100 (Fr, 16. Jan 2015) $
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

function applyOnEnter(evt) {
	_elemName = "target";
	if (evt.srcElement !== undefined) { // IE
		_elemName = "srcElement";
	}

	if (!(evt[_elemName].tagName == "SELECT" ||
					(evt[_elemName].tagName == "INPUT" && evt[_elemName].name != "fname")
					)) {
		top.fsfooter.press_ok_button();
		return true;
	}

}
function closeOnEscape() {
	top.exit_close();

}
function in_array(needle, haystack) {
	for (var i = 0; i < haystack.length; i++) {
		if (haystack[i] == needle) {
			return true;
		}
	}
	return false;
}

function orderIt(o) {
	order = o + (order == o ? " DESC" : "");
	top.fscmd.location.replace(top.queryString(queryType.CMD, top.currentDir, order));
}

function goBackDir() {
	setDir(parentID);
}

function getEntry(id) {
	for (var i = 0; i < entries.length; i++) {
		if (entries[i].ID == id) {
			return entries[i];
		}
	}
	return new entry(0, "", "/", 1, "/");
}

function cutText(text, l) {
	if (text.length > l) {
		return text.substring(0, l - 8) + "..." + text.substring(text.length - 5, text.length);
	}
	return text;

}

function clearEntries() {
	entries = [];
}