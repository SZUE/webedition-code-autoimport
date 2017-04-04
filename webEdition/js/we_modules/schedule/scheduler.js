/* global WE, top, _EditorFrame */

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
var scheduler = WE().util.getDynamicVar(document, 'loadVarScheduler', 'data-scheduler');

function changeSchedOption(elem, nr) {
	_EditorFrame.setEditorIsHot(true);
	checkFooter();
	if (scheduler.we_hasExtraRow[nr] || elem.options[elem.selectedIndex].value == scheduler.selection.DOCTYPE || elem.options[elem.selectedIndex].value == scheduler.selection.CATEGORY || elem.options[elem.selectedIndex].value == scheduler.selection.DIR) {
		window.setScrollTo();
		window.we_cmd('reload_editpage');
	}
}

function checkFooter() {
	var button = window.parent.editFooter.document.getElementById("publish_" + scheduler.docID);
	var aEl = document.getElementsByClassName("we_schedule_active");
	var active = false;
	if (button) {
		button = button.getElementsByTagName("button")[0];
		for (var i = 0; i < aEl.length; ++i) {
			if (aEl[i].value == 1) {
				var no = aEl[i].name.split("we_schedule_active_");
				if (document.getElementsByName("we_schedule_task_" + no[1])[0].value == scheduler.selection.SCHEDULE_FROM) {
					active = true;
					break;
				}
			}
		}
		if (active) {
			button.title = WE().consts.g_l.scheduler.activeSchedule.title;
			button.innerHTML = '<i class="fa fa-lg fa-clock-o"></i> ' + WE().consts.g_l.scheduler.activeSchedule.value;
		} else {
			button.title = WE().consts.g_l.scheduler.inActiveSchedule.title;
			button.innerHTML = '<i class="fa fa-lg fa-globe"></i> ' + WE().consts.g_l.scheduler.inActiveSchedule.value;
		}
	}
//we_schedule_task
}