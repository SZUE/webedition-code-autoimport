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

WE().layout.button = {
	disable: function (doc, id, disable) {
		disable = disable === undefined ? true : disable;
		var el = doc.getElementById(id);
		el = (el === null ? doc.getElementById("btn_" + id) : el);
		if (el !== null) {
			if (el.tagName === "BUTTON") {
				el.disabled = disable;
				return;
			}
		}
	},
	enable: function (doc, id) {
		var el = doc.getElementById(id);
		el = (el === null ? doc.getElementById("btn_" + id) : el);
		if (el !== null) {
			if (el.tagName === "BUTTON") {
				el.disabled = false;
				return;
			}
		}
	},
	setText: function (doc, id, text) {
		var el = doc.getElementById(id);
		el = (el === null ? doc.getElementById("btn_" + id) : el);
		if (el !== null && text !== undefined) {
			if (el.tagName === "BUTTON") {
				el.innerHTML = text;
				return;
			}
		}
	},
	display: function (doc, id, display) {
		var el = doc.getElementById(id);

		if ((el === null ? doc.getElementById("btn_" + id) : el)) {
			if(display){
				el.classList.remove("weHide");
			} else {
				el.classList.add("weHide");
			}
		}
	},
	isDisabled: function (doc, id) {
		var el = doc.getElementById(id);
		el = (el === null ? doc.getElementById("btn_" + id) : el);
		return (el !== null && (el.tagName == "BUTTON" ? el.disabled : el.className == "weBtnDisabled"));
	},
	isEnabled: function (doc, id) {
		return !this.isDisabled(doc, id);
	},
	switch_button_state: function (doc, element, state) {
		switch (state) {
			case "enabled":
				this.enable(doc, element);
				return true;
			case "disabled":
				this.disable(doc, element);
		}

		return false;
	}
};