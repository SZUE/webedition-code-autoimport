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
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */


function imageChanged(wasThumbnailChange) {
	if (wasThumbnailChange !== null && wasThumbnailChange) {
		document.we_form.wasThumbnailChange.value = "1";
	}
	if (top.opener.tinyMCECallRegisterDialog) {
		top.opener.tinyMCECallRegisterDialog(null, "block");
	}
	//document.we_form.target = "we_weImageDialog_edit_area";
	document.we_form.target = "we_we_dialog_image_cmd_frame";//TODO: send form to iFrame cmd for and for not reloading whole editor
	document.we_form.we_what.value = "cmd";
	document.we_form["we_cmd[0]"].value = "update_editor";
	document.we_form.imgChangedCmd.value = "1";
	document.we_form.submit();
}

function checkWidthHeight(field) {
	var ratioCheckBox = document.getElementById("check_we_dialog_args[ratio]");
	if (ratioCheckBox.checked) {
		if (field.value.indexOf("%") == -1) {
			ratiow = ratiow ? ratiow :
							(field.form.elements.tinyMCEInitRatioW.value ? field.form.elements.tinyMCEInitRatioW.value : 0);
			ratioh = ratioh ? ratioh :
							(field.form.elements.tinyMCEInitRatioH.value ? field.form.elements.tinyMCEInitRatioH.value : 0);
			if (ratiow && ratioh) {
				if (field.name == "we_dialog_args[height]") {
					field.form.elements["we_dialog_args[width]"].value = Math.round(field.value * ratioh);
				} else {
					field.form.elements["we_dialog_args[height]"].value = Math.round(field.value * ratiow);
				}
			}
		} else {
			ratioCheckBox.checked = false;
		}
	}
	return true;
}

function fsubmit(e) {
	return false;
}

function we_cmd() {
	var args = "";
	var url = top.WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (arguments[0]) {
		case "we_selector_document":
		case "we_selector_image":
		case "we_selector_directory":
			new jsWindow(url, "we_fileselector", -1, -1, top.WE().consts.size.docSelect.width, top.WE().consts.size.docSelect.height, true, true, true, true);
			break;
		case "browse_server":
			new jsWindow(url, "browse_server", -1, -1, 840, 400, true, false, true);
			break;
	}
}

function showclasss(name, val, onCh) {
	document.writeln('<select class="defaultfont" style="width:200px" name="' + name + '" id="' + name + '" size="1"' + (onCh ? ' onchange="' + onCh + '"' : '') + '>');
	document.writeln('<option value="">' + g_l.wysiwyg_none + '</option>');
	if (classNames !== undefined) {
		for (var i = 0; i < classNames.length; i++) {
			var foo = classNames[i].substring(0, 1) === "." ?
							classNames[i].substring(1, classNames[i].length) :
							classNames[i];
			document.writeln('<option value="' + foo + '"' + ((val == foo) ? ' selected' : '') + '>.' + foo + '</option>');
		}
	}
	document.writeln('</select>');
}
