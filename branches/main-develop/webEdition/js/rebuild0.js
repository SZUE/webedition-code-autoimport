/*
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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/* global top, WE */
'use strict';

function handle_event(what) {
	var f = document.we_form;
	switch (what) {
		case "previous":
			break;
		case "next":
			var selectedValue = "";
			for (var i = 0; i < f.type.length; i++) {
				if (f.type[i].checked) {
					selectedValue = f.type[i].value;
				}
			}
			goTo(selectedValue);
			break;
	}
}

function goTo(where) {
	var f = document.we_form;
	switch (where) {
		case "rebuild_thumbnails":
		case "rebuild_documents":
			f.target = "wizbody";
			break;
		case "rebuild_objects":
		case "rebuild_index":
		case "rebuild_navigation":
		case "rebuild_medialinks":
			set_button_state(1);
			f.target = "wizcmd";
			f.step.value = "2";
			break;
	}
	f.submit();
}
function set_button_state(alldis) {
	if (top.wizbusy) {
		top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "back", "disabled");
		if (alldis) {
			top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "disabled");
			top.wizbusy.showRefreshButton();
		} else {
			top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "enabled");
		}
	} else {
		window.setTimeout(set_button_state, 300, (alldis ? 1 : 0));
	}
}
function setNavStatDocDisabled() {
	var radio = document.getElementById("type");
	var check = document.getElementById("rebuildStaticAfterNavi");
	var checkLabel = document.getElementById("label_rebuildStaticAfterNavi");
	check.disabled = (!radio.checked);
	checkLabel.style.color = radio.checked ? "" : "grey";
}