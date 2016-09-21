/* global WE */

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
var weAcCheckLoop = 0;

function checkAnchor(el) {
	if (el.value && !new RegExp('#?[a-z]+[a-z0-9_:.-=]*$', 'i').test(el.value)) {
		alert(WE().consts.g_l.alert.anchor_invalid);
		window.setTimeout(function () {
			el.focus();
		}, 10);
		return false;
	}
}

function weCheckAcFields() {
	if (weFocusedField !== undefined) {
		weFocusedField.blur();
	}
	if (document.getElementById("weDialogType").value === WE().consts.linkPrefix.TYPE_INT) {
		setTimeout(weDoCheckAcFields, 100);
	} else {
		document.we_form.submit();
	}
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(this, url, "we_docselector", -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, false, true, true);
			break;

		case "browse_server":
			new (WE().util.jsWindow)(this, url, "browse_server", -1, -1, 800, 400, true, false, true);
			break;
		default :
			top.opener.we_cmd.apply(this, Array.prototype.slice.call(arguments));
			break;
	}
}

function showanchors(name, val, onCh) {
	var pageAnchors = top.opener.document.getElementsByTagName("A");
	var objAnchors = top.opener["weWysiwygObject_" + editname].eDocument.getElementsByTagName("A");
	var allAnchors = [];
	var i;

	for (i = 0; i < pageAnchors.length; i++) {
		if (!pageAnchors[i].href && pageAnchors[i].name !== "") {
			allAnchors.push(pageAnchors[i].name);
		}
	}

	for (i = 0; i < objAnchors.length; i++) {
		if (!objAnchors[i].href && objAnchors[i].name !== "") {
			allAnchors.push(objAnchors[i].name);
		}
	}
	if (allAnchors.length) {
		document.writeln('<select class="defaultfont" style="width:100px" name="' + name + '" id="' + name + '"' + (onCh ? ' onchange="' + onCh + '"' : '') + '>');
		document.writeln('<option value="">');

		for (i = 0; i < allAnchors.length; i++) {
			document.writeln('<option value="' + allAnchors[i] + '"' + ((val === allAnchors[i]) ? ' selected' : '') + '>' + allAnchors[i]);
		}

		document.writeln('</select>');
	}
}

function checkMakeEmptyHrefExt() {
	var f = document.we_form,
					hrefField = f.elements["we_dialog_args[extHref]"],
					anchor = f.elements["we_dialog_args[anchor]"].value,
					params = f.elements["we_dialog_args[param]"].value;

	if ((anchor || params) && hrefField.value === WE().consts.linkPrefix.EMPTY_EXT) {
		hrefField.value = "";
	} else if (!(anchor || params) && !hrefField.value) {
		hrefField.value = WE().consts.linkPrefix.EMPTY_EXT;
	}

}

function weDoCheckAcFields() {
	acStatus = YAHOO.autocoml.checkACFields();
	acStatusType = typeof acStatus;
	if (weAcCheckLoop > 10) {
		WE().util.showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		weAcCheckLoop = 0;
	} else if (acStatusType.toLowerCase() == "object") {
		if (acStatus.running) {
			weAcCheckLoop++;
			setTimeout(weDoCheckAcFields, 100);
		} else if (!acStatus.valid) {
			WE().util.showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
			weAcCheckLoop = 0;
		} else {
			weAcCheckLoop = 0;
			document.we_form.submit();
		}
	} else {
		WE().util.showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE_WE().consts.message.MESSAGE_ERROR, window);
	}
}