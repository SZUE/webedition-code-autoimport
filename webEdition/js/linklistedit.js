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
 * @package    webEdition_tinymce
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function closeOnEscape() {
	return true;
}

function applyOnEnter(evt) {
	_elemName = "target";
	if (evt.srcElement !== undefined) { // IE
		_elemName = "srcElement";
	}

	if (!(evt[_elemName].tagName == "SELECT" ||
					(evt[_elemName].tagName == "INPUT" && (evt[_elemName].name == "href_int" || evt[_elemName].name == "href_obj" || evt[_elemName].name == "src_int"))
					)) {
		document.we_form.submit();
		return true;

	}
}

function changeTypeSelect(s) {
	for (var i = 0; i < s.options.length; i++) {
		var trObj = document.getElementById(s.options[i].value + "_tr");
		if (i != s.selectedIndex) {
			trObj.style.display = "none";
		} else {
			trObj.style.display = "";
		}
	}
	//added for #7269
	var emailTable = document.getElementById("emailOptions");
	if (emailTable) {
		if (s.value == "<?php echo we_base_link::TYPE_MAIL; ?>") {
			emailTable.style.display = "block";
		} else {
			emailTable.style.display = "none";
		}
	}
}
function changeCTypeSelect(s) {
	var imgPropsObj;
	var trObj;
	for (var i = 0; i < s.options.length; i++) {
		trObj = document.getElementById("c" + s.options[i].value + "_tr");
		imgPropsObj = document.getElementById("cimgprops_tr");
		if (i != s.selectedIndex) {
			trObj.style.display = "none";
		} else {
			trObj.style.display = "";
		}
	}
	if (s.options[s.selectedIndex].value == "<?php echo we_base_link::CONTENT_TEXT; ?>") {
		imgPropsObj.style.display = "none";
	} else {
		imgPropsObj.style.display = "";
	}
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}