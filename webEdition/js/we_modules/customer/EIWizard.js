/* global WE, top */

/**
 * webEdition CMS
 *
 * $Rev: 12760 $
 * $Author: mokraemer $
 * $Date: 2016-09-12 20:55:09 +0200 (Mo, 12. Sep 2016) $
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
	top.body.document.we_form.step.value++;
	top.footer.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=" + wizzard.ART_EXPORT + "&step=" + top.body.document.we_form.step.value;
	if (top.body.document.we_form.step.value > 3) {
		top.body.document.we_form.target = "load";
		top.body.document.we_form.pnt.value = "eiload";
		top.body.document.we_form.cmd.value = wizzard.ART_EXPORT;
	}
	top.body.document.we_form.submit();
}

function doNextBack() {
	top.body.document.we_form.step.value--;
	top.footer.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=" + wizzard.ART_EXPORT + "&step=" + top.body.document.we_form.step.value;
	top.body.document.we_form.submit();
}

function doNextAction() {
	top.body.document.we_form.step.value++;
	top.footer.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=" + wizzard.ART_EXPORT + "&step=" + top.body.document.we_form.step.value;
	if (top.body.document.we_form.step.value > 4) {
		top.body.document.we_form.target = "load";
		top.body.document.we_form.pnt.value = "eiload";
	}
	top.body.document.we_form.submit();
}