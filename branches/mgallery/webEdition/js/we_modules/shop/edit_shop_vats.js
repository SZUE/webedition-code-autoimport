/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 9449 $
 * $Author: mokraemer $
 * $Date: 2015-03-02 00:36:19 +0100 (Mo, 02. Mär 2015) $
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
var hot = 0;
if (window.addEventListener) {
	document.addEventListener("keyup", doKeyDown, true);
} else {
	document.onkeydown = doKeyDown;
}


function we_submitForm(url) {
	var f = self.document.we_form;
	f.action = url;
	f.method = "post";

	f.submit();
}

function addListeners() {
	for (var i = 1; i < document.we_form.elements.length; i++) {
		document.we_form.elements[i].addEventListener("change", function () {
			hot = 1;
		})
	}
}

function doKeyDown(e) {
	var key = e.keyCode === undefined ? event.keyCode : e.keyCode;

	switch (key) {
		case 27:
			top.close();
			break;
	}
}

function IsDigit(e) {
	var key = e.charCode === undefined ? event.keyCode : e.charCode;
	return ((key == 46) || ((key >= 48) && (key <= 57)) || (key == 0) || (key == 13) || (key == 8) || (key <= 63235 && key >= 63232) || (key == 63272));
}

function changeFormTextField(theId, newVal) {
	if (document.getElementById(theId) == null) {
		console.log(theId);
	}
	document.getElementById(theId).value = newVal;
}

function changeFormSelect(theId, newVal) {
	elem = document.getElementById(theId);

	for (i = 0; i < elem.options.length; i++) {
		if (elem.options[i].value == newVal) {
			elem.selectedIndex = i;
		}
	}
}

function doUnload() {
	jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = "";
	var url = top.WE().consts.dirs + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	switch (arguments[0]) {
		case "save":
			document.we_form.onsaveclose.value = 1;
			we_submitForm(SCRIPT_NAME);
			break;

		case "save_notclose":
			we_submitForm(SCRIPT_NAME);
			break;

		case "close":
			if (hot) {
				new (WE().util.jsWindow)(top.window, top.WE().consts.dirs.WE_SHOP_MODULE_DIR + "edit_shop_exitQuestion.php", "we_exit_doc_question", -1, -1, 380, 130, true, false, true);
			} else {
				window.close();
			}
			break;

		case "edit":
			elem = document.getElementById("editShopVatForm");
			if (elem.style.display == "none") {
				elem.style.display = "";
			}

			if (theVat = allVats["vat_" + arguments[1]]) {
				changeFormTextField("weShopVatId", theVat["id"]);
				changeFormTextField("weShopVatText", theVat["text"]);
				changeFormTextField("weShopVatVat", theVat["vat"]);
				changeFormSelect("weShopVatStandard", theVat["standard"]);
				changeFormSelect("weShopVatCountry", theVat["country"]);
				changeFormTextField("weShopVatProvince", theVat["province"]);
				//changeFormTextField("weShopVatTextProvince", theVat["textProvince"]);
			}
			break;

		case "delete":
			if (confirm(top.WE().consts.g_l.shop.vat_confirm_delete)) {
				document.location = SCRIPT_NAME + "?we_cmd[0]=deleteVat&weShopVatId=" + arguments[1];
			}
			break;

		case "addVat":
			elem = document.getElementById("editShopVatForm");
			if (elem.style.display == "none") {
				elem.style.display = "";
			}
			if (theVat = allVats["vat_0"]) {
				changeFormTextField("weShopVatId", theVat["id"]);
				changeFormTextField("weShopVatText", theVat["text"]);
				changeFormTextField("weShopVatVat", theVat["vat"]);
				changeFormSelect("weShopVatStandard", theVat["standard"]);
				changeFormSelect("weShopVatCountry", theVat["country"]);
				changeFormTextField("weShopVatProvince", theVat["province"]);
				//changeFormTextField("weShopVatTextProvince", theVat["textProvince"]);
			}

			break;

		default :
			break;
	}
}
