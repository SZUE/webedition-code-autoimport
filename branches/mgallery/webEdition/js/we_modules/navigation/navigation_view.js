/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 9451 $
 * $Author: mokraemer $
 * $Date: 2015-03-02 01:57:24 +0100 (Mo, 02. Mär 2015) $
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

function switch_button_state(element, state) {
	switch (state) {
		case "enabled":
			weButton.enable(element);
			return true;
		case "disabled":
			weButton.disable(element);
			return false;
		default:

			return false;
	}
}

function setFieldValue(fieldNameTo, fieldFrom) {
	if (document.we_form.SelectionType.value === "doctype" && (fieldNameTo === "TitleField" || fieldNameTo === "SorrtField")) {
		eval("document.we_form." + fieldNameTo + ".value=fieldFrom.value");
		weInputRemoveClass(fieldFrom, "weMarkInputError");
	} else if (weNavTitleField[fieldFrom.value] != undefined) {
		eval("document.we_form." + fieldNameTo + ".value=\'" + weNavTitleField[fieldFrom.value] + "\'");
		weInputRemoveClass(fieldFrom, "weMarkInputError");
	} else if (fieldFrom.value == "") {
		eval("document.we_form." + fieldNameTo + ".value=\'\'");
		weInputRemoveClass(fieldFrom, "weMarkInputError");
	} else {
		weInputAppendClass(fieldFrom, "weMarkInputError");
	}
}

function toggle(id) {
	var elem = document.getElementById(id);
	if (elem) {
		elem.style.display = (elem.style.display == "none" ? "block" : "none");
	}
}

function setVisible(id, visible) {
	var elem = document.getElementById(id);
	if (elem) {
		elem.style.display = (visible == true ? "block" : "none");
	}
}

function populateVars() {
	if(window.categories_edit!==undefined && document.we_form.CategoriesCount!==undefined){
		document.we_form.CategoriesCount.value = categories_edit.itemCount;
	}
	if(window.sort_edit!==undefined && document.we_form.SortCount!==undefined){
		document.we_form.SortCount.value = sort_edit.itemCount;
	}
	if(window.specificCustomersEdit!==undefined && document.we_form.specificCustomersEditCount!==undefined){
		document.we_form.specificCustomersEditCount.value = specificCustomersEdit.itemCount;
	}
	if(window.blackListEdit!==undefined && document.we_form.blackListEditCount!==undefined){
		document.we_form.blackListEditCount.value = blackListEdit.itemCount;
	}
	if(window.whiteListEdit!==undefined && document.we_form.whiteListEditCount!==undefined){
		document.we_form.whiteListEditCount.value = whiteListEdit.itemCount;
	}
}