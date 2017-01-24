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

function wedelRow(nr, but) {
	if (but.src === undefined || but.src.indexOf("disabled") == -1) {
		var prefix = "div_uploadFiles_";
		var num = -1;
		var z = 0;
		weDelMultiboxRow(nr);
		var divs = document.getElementsByTagName("DIV");
		for (var i = 0; i < divs.length; i++) {
			if (divs[i].id.length > prefix.length && divs[i].id.substring(0, prefix.length) == prefix) {
				num = divs[i].id.substring(prefix.length, divs[i].id.length);
				if (parseInt(num)) {
					var sp = document.getElementById("headline_uploadFiles_" + (num - 1));
					if (sp) {
						sp.innerHTML = z;
					}
				}
				z++;
			}
		}
	}
}

function checkButtons() {
	checkFileinput();
	window.setTimeout(checkButtons, 1000);
	//recheck
}

function makeArrayFromCSV(csv) {
	if (csv.length && csv.substring(0, 1) === ",") {
		csv = csv.substring(1, csv.length);
	}
	if (csv.length && csv.substring(csv.length - 1, csv.length) === ",") {
		csv = csv.substring(0, csv.length - 1);
	}
	if (csv.length === 0) {
		return [];
	}
	return csv.split(/,/);
}

function checkFileinput() {
	var prefix = "trash_";
	var imgs = document.getElementsByTagName("IMG");
	if (document.forms[document.forms.length - 1].name.substring(0, 14) === "we_upload_form" && document.forms[document.forms.length - 1].elements.we_File.value) {
		for (var i = 0; i < imgs.length; i++) {
			if (imgs[i].id.length > prefix.length && imgs[i].id.substring(0, prefix.length) === prefix) {
				imgs[i].style.display = "";
			}
		}

		var fi = we_fileinput.replace(/WEFORMNUM/g, weGetLastMultiboxNr());
		fi = fi.replace(/WE_FORM_NUM/g, (document.forms.length));
		weAppendMultiboxRow(fi, "", 0, 1);
		window.scrollTo(0, 1000000);
	}
}

function we_cmd() {
	//var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);

	top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
}