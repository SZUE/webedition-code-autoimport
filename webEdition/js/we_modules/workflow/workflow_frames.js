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

function setStatusCheck() {
	var a = document.we_form.status_workflow;
	var b;
	if (top.content.editor.edbody.loaded) {
		b = top.content.editor.edbody.getStatusContol();
	} else {
		window.setTimeout(setStatusCheck, 100);
	}

	a.checked = (b == 1);
}

function clearLog() {
	window.opener.top.content.cmd.document.we_form.wcmd.value = "empty_log";
	if (document.we_form.clear_opt.value == 1) {
		var day = document.we_form.log_time_day.options[document.we_form.log_time_day.selectedIndex].text;
		var month = document.we_form.log_time_month.options[document.we_form.log_time_month.selectedIndex].text;
		var year = document.we_form.log_time_year.options[document.we_form.log_time_year.selectedIndex].text;
		var hour = document.we_form.log_time_hour.options[document.we_form.log_time_hour.selectedIndex].text;
		var min = document.we_form.log_time_minute.options[document.we_form.log_time_minute.selectedIndex].text;

		var timearr = [day, month, year, hour, min];
		window.opener.top.content.cmd.document.we_form.wopt.value = timearr.join();
	} else if (!window.confirm(WE().consts.g_l.workflow.view.emty_log_question)) {
		return;
	}
	window.opener.top.content.cmd.submitForm();
	close();
}

function submitForm() {
	var f = self.document.we_form;
	f.target = "cmd";
	f.method = "post";
	f.submit();
}