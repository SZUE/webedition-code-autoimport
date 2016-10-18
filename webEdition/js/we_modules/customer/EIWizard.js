/* global WE, top */

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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

var wizzard = WE().util.getDynamicVar(document, 'loadVarEIWizard', 'data-wizzard');

function doNext() {
	switch (wizzard.art) {
		case 'export':
			top.body.document.we_form.step.value++;
			top.footer.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=" + wizzard.art + "&step=" + top.body.document.we_form.step.value;
			if (top.body.document.we_form.step.value > 3) {
				top.body.document.we_form.target = "load";
				top.body.document.we_form.pnt.value = "eiload";
				top.body.document.we_form.cmd.value = wizzard.art;
			}
			top.body.document.we_form.submit();
			break;
		case 'import':
			if (top.body.document.we_form.step.value === "2" &&
				top.body.weFileUpload_instance !== undefined &&
				top.body.document.we_form.import_from[1].checked) {
				top.body.weFileUpload_instance.startUpload()
				return;
			}
			doNextAction();
			break;
	}
}

function doNextBack() {
	top.body.document.we_form.step.value--;
	top.footer.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=" + wizzard.art + "&step=" + top.body.document.we_form.step.value;
	top.body.document.we_form.submit();
}

function doNextAction() {
	top.body.document.we_form.step.value++;
	top.footer.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=" + wizzard.art + "&step=" + top.body.document.we_form.step.value;
	if (top.body.document.we_form.step.value > 4) {
		top.body.document.we_form.target = "load";
		top.body.document.we_form.pnt.value = "eiload";
	}
	top.body.document.we_form.submit();
}

function selector_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	switch (args[0]) {
		case "we_selector_file":
			new (WE().util.jsWindow)(window, url, "we_selector", -1, -1, WE().consts.size.windowSelect.width, WE().consts.size.windowSelect.height, true, true, true, true);
			break;
		case "add_customer":
			document.we_form.wcmd.value = args[0];
			document.we_form.cus.value = args[1].allIDs.join(",");
			document.we_form.submit();
			break;
		case "del_customer":
		case "del_all_customers":
			document.we_form.wcmd.value = args[0];
			document.we_form.cus.value = args[1];
			document.we_form.submit();
			break;
	}
}