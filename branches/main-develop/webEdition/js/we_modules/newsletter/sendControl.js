/* global WE, top */

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
'use strict';
var control = WE().util.getDynamicVar(document, 'loadVarSendControl', 'data-control');

var to = null;
var param = 0;

function reinit() {
	top.send_body.document.we_form.details.value = top.send_body.document.we_form.details.value + "\n" + "' . g_l('modules_newsletter', '[retry]') . '...";
	document.we_form.submit();
	startTimeout();
}

function init() {
	document.we_form.ecs.value = top.send_cmd.document.we_form.ecs.value;
	startTimeout();
}

function startTimeout() {
	if (to) {
		stopTimeout();
	}
	to = setTimeout(reload, control.to);
}

function stopTimeout() {
	clearTimeout(to);
}

function reload() {
	var chk = document.we_form.ecs.value;
	if (parseInt(chk) > parseInt(param) && parseInt(chk) !== 0) {
		param = chk;
		startTimeout();
	} else {
		reinit();
	}
}
