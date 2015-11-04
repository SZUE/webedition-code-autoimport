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
	try {
		if (document.JUpload === undefined || (typeof (document.JUpload.isActive) != "function") || document.JUpload.isActive() == false) {
			checkFileinput();
			window.setTimeout(function () {
				checkButtons()
			}, 1000);
			//recheck
		} else {
			setApplet();
		}
	} catch (e) {
		checkFileinput();
		window.setTimeout(function () {
			checkButtons()
		}, 1000);
	}
}

function setApplet() {
	var descDiv = document.getElementById("desc");
	if (descDiv.style.display != "none") {
		var descJUDiv = document.getElementById("descJupload");
		var buttDiv = top.imgimportbuttons.document.getElementById("normButton");
		var buttJUDiv = top.imgimportbuttons.document.getElementById("juButton");
		descDiv.style.display = "none";
		buttDiv.style.display = "none";
		descJUDiv.style.display = "block";
		buttJUDiv.style.display = "block";
	}

//setTimeout(document.JUpload.jsRegisterUploaded("refreshTree"),3000);
}

function makeArrayFromCSV(csv) {
	if (csv.length && csv.substring(0, 1) == ",") {
		csv = csv.substring(1, csv.length);
	}
	if (csv.length && csv.substring(csv.length - 1, csv.length) == ",") {
		csv = csv.substring(0, csv.length - 1);
	}
	if (csv.length == 0) {
		return [];
	}
	return csv.split(/,/);
}

function makeCSVFromArray(arr) {
	if (arr.length == 0) {
		return "";
	}
	return "," + arr.join(",") + ",";
}

function refreshTree() {
	//FIXME: this won\'t work in current version
	top.opener.top.we_cmd("load", WE().consts.tables.FILE_TABLE);
}

function checkFileinput() {
	var prefix = "trash_";
	var imgs = document.getElementsByTagName("IMG");
	if (document.forms[document.forms.length - 1].name.substring(0, 14) == "we_upload_form" && document.forms[document.forms.length - 1].elements.we_File.value) {
		for (var i = 0; i < imgs.length; i++) {
			if (imgs[i].id.length > prefix.length && imgs[i].id.substring(0, prefix.length) == prefix) {
				imgs[i].style.display = "";
			}
		}
		//weAppendMultiboxRow(we_fileinput.replace(/WEFORMNUM/g,weGetLastMultiboxNr()),\'' . g_l('importFiles', '[file]') . '\' + \' \' + (parseInt(weGetMultiboxLength())),80,1);
		var fi = we_fileinput.replace(/WEFORMNUM/g, weGetLastMultiboxNr());
		fi = fi.replace(/WE_FORM_NUM/g, (document.forms.length));
		weAppendMultiboxRow(fi, "", 0, 1);
		window.scrollTo(0, 1000000);
	}
}

function we_cmd() {
	var scope = window,
		url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";

	if(typeof arguments[0] === 'object' && arguments[0]['we_cmd[0]'] !== undefined){
		var args = {}, i = 0, tmp = arguments[0];
		scope = this;
		url += Object.keys(tmp).map(function(key){args[key] = tmp[key]; args[i++] = tmp[key]; return key + "=" + encodeURIComponent(tmp[key]);}).join("&");
	} else {
		if (typeof arguments[0] === 'object') {
			scope = arguments[0];
			i++;
		}
		var args = Array.prototype.slice.call(arguments, i);
		for (i = 0; i < args.length; i++) {
			url += "we_cmd[" + i + "]=" + encodeURIComponent(args[i]) + (i < (args.length - 1) ? "&" : "");
		}
	}

	switch (args[0]) {
		case 'we_selector_directory_':
			new (WE().util.jsWindow)(this, url, 'we_fileselector', -1, -1, WE().consts.size.windowDirSelect.width, WE().consts.size.windowDirSelect.height, true, true, true, true);
			break;
		case 'we_selector_category_':
			new (WE().util.jsWindow)(this, url, 'we_catselector', -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.height, true, true, true, true);
			break;
		default:
			if(typeof arguments[0] === 'object' && arguments[0]['we_cmd[0]'] !== undefined){
				top.opener.top.we_cmd.apply(this, arguments);
			} else {
				args.unshift(scope);
				top.opener.top.we_cmd.apply(this, arguments);
				//top.opener.top.we_cmd.apply(this, args);
			}
	}
}