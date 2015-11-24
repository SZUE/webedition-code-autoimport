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
		});
	}
}

function closeOnEscape() {
	return true;
}

function changeFormTextField(theId, newVal) {
	if (document.getElementById(theId) === null) {
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
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "save":
			document.we_form.onsaveclose.value = 1;
			we_submitForm(SCRIPT_NAME);
			break;

		case "save_notclose":
			we_submitForm(SCRIPT_NAME);
			break;

		case "close":
			if (hot) {
				new (WE().util.jsWindow)(this, WE().consts.dirs.WE_SHOP_MODULE_DIR + "edit_shop_exitQuestion.php", "we_exit_doc_question", -1, -1, 380, 130, true, false, true);
			} else {
				this.close();
			}
			break;

		case "edit":
			elem = document.getElementById("editShopVatForm");
			if (elem.style.display == "none") {
				elem.style.display = "";
			}

			if ((theVat = allVats["vat_" + args[1]])) {
				changeFormTextField("weShopVatId", theVat.id);
				changeFormTextField("weShopVatText", theVat.text);
				changeFormTextField("weShopVatVat", theVat.vat);
				changeFormSelect("weShopVatStandard", theVat.standard);
				changeFormSelect("weShopVatCountry", theVat.country);
				changeFormTextField("weShopVatProvince", theVat.province);
				//changeFormTextField("weShopVatTextProvince", theVat.textProvince);
			}
			break;

		case "delete":
			if (confirm(WE().consts.g_l.shop.vat_confirm_delete)) {
				document.location = SCRIPT_NAME + "?we_cmd[0]=deleteVat&weShopVatId=" + args[1];
			}
			break;

		case "addVat":
			elem = document.getElementById("editShopVatForm");
			if (elem.style.display == "none") {
				elem.style.display = "";
			}
			if ((theVat = allVats.vat_0)) {
				changeFormTextField("weShopVatId", theVat.id);
				changeFormTextField("weShopVatText", theVat.text);
				changeFormTextField("weShopVatVat", theVat.vat);
				changeFormSelect("weShopVatStandard", theVat.standard);
				changeFormSelect("weShopVatCountry", theVat.country);
				changeFormTextField("weShopVatProvince", theVat.province);
				//changeFormTextField("weShopVatTextProvince", theVat.textProvince);
			}
			break;
		default :
			break;
	}
}
