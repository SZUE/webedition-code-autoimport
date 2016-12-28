/* global WE */

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
var all_groups = WE().util.getDynamicVar(document, 'loadVarDynamicControls', 'data-groups');

var opened_group = "";
/**
 * This function opens or closes one group
 *
 * @param      image_name                              string
 * @param      display_style                           string
 *
 * @see        toggle()
 * @see        toggle_all()
 *
 * @return     void
 */

function toggle_arrow(image_name, display_style) {
	// Check state of arrow
	if (display_style === "closed") {
		// Group is folded
		document.getElementsByName(image_name)[0].classList.remove("fa-caret-down");
		document.getElementsByName(image_name)[0].classList.add("fa-caret-right");
	} else {
		// Group is expanded
		document.getElementsByName(image_name)[0].classList.remove("fa-caret-right");
		document.getElementsByName(image_name)[0].classList.add("fa-caret-down");
	}
}

/**
 * This function opens or closes one group
 *
 * @param      group_id                                string
 * @param      display_style                           string
 * @param      use_form                                bool
 * @param      form_name                               string
 * @param      form_group_name                         string
 *
 * @see        toggle_arrow()
 * @see        toggle_all()
 *
 * @return     void
 */

function toggle(group_id, display_style, use_form, form_name, form_group_name) {
	// Check if to close all other groups
	if (display_style == "show_single") {
		// Remember old group state
		var _old_display_style = document.getElementById("group_" + group_id).style.display;
		// Close all other groups an show only the requested one
		toggle_all();

		// Check, if we need to open the current group
		if (_old_display_style == "none" && document.getElementById("group_" + group_id).style.display == "none") {
			// Show the group
			toggle(group_id, "open", use_form, form_name, form_group_name);
		} else {
			// Reset the arrow
			toggle_arrow("arrow_" + group_id, "closed");
		}
	} else {
		// Check if to hide or to unhide the group
		if (document.getElementById("group_" + group_id).style.display == "none" || display_style == "open") {
			// Show the group
			document.getElementById("group_" + group_id).style.display = "block";

			// set the var to locate which group is opened
			opened_group = group_id;

			// Set value for arrow
			display_style = "opened";

			// Check if forms should be used
			if (use_form) {
				// Tell the form which group is open
				var _document_form = document[form_name][form_group_name];
				_document_form.value = group_id;
			}
		} else {
			// Hide the group
			document.getElementById("group_" + group_id).style.display = "none";

			// Set value for arrow
			display_style = "closed";
		}

		// Change the arrow
		toggle_arrow("arrow_" + group_id, display_style);
	}
}

/**
 * This function closes all groups
 *
 * @see        toggle()
 * @see        toggle_arrow()
 *
 * @return     void
 */

function toggle_all() {
	// Hide all groups
	for (var i = 0; i < all_groups.length; i++) {
		// Check if that group is open
		if (document.getElementById("group_" + all_groups[i]).style.display === "block") {
			// Hide the group
			toggle(all_groups[i], "close");
		}
	}
}