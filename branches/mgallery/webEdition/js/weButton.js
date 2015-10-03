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

var weButton = {
	disable: function (id) {
		var el = document.getElementById(id);
		if (el !== null) {
			if (el.tagName === "BUTTON") {
				el.disabled = true;
				return;
			}
		}
	},
	enable: function (id) {
		var el = document.getElementById(id);
		if (el !== null) {
			if (el.tagName === "BUTTON") {
				el.disabled = false;
				return;
			}
		}
	},
	setText: function (id, text) {
		var el = document.getElementById(id);
		if (el !== null && text !== undefined) {
			if (el.tagName === "BUTTON") {
				el.innerHTML = text;
				return;
			}
		}
	},
	hide: function (id) {
		var el = document.getElementById(id);
		if (el !== null) {
			el.style.display = "none";
		}
	},
	show: function (id) {
		var el = document.getElementById(id);
		if (el !== null) {
			el.style.display = "block";
		}
	},
	isDisabled: function (id) {
		var el = document.getElementById(id);
		return (el !== null && (el.tagName == "BUTTON" ? el.disabled : el.className == "weBtnDisabled"));
	},
	isEnabled: function (id) {
		return !this.isDisabled(id);
	},
	switch_button_state: function (element, state) {
		switch (state) {
			case "enabled":
				this.enable(element);
				return true;
			case "disabled":
				this.disable(element);
		}

		return false;
	}
};