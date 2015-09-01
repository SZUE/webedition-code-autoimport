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

function weButton() {
}

weButton.disable = function (id) {
	var el = document.getElementById(id);
	if (el !== null) {
		if (el.tagName === "BUTTON") {
			el.disabled = true;
			return;
		}/*
		 el.className = "weBtnDisabled";
		 var tds = el.getElementsByTagName("TD");
		 tds[0].className = "weBtnLeftDisabled";
		 tds[1].className = "weBtnMiddleDisabled";
		 tds[2].className = "weBtnRightDisabled";
		 var img = document.getElementById(el.id + "_img");
		 if (img !== null && img.src.indexOf("Disabled.gif") == -1) {
		 img.src = img.src.replace(/\.gif/, "Disabled.gif");
		 }*/
	}
};

weButton.enable = function (id) {
	var el = document.getElementById(id);
	if (el !== null) {
		if (el.tagName === "BUTTON") {
			el.disabled = false;
			return;
		}
	}
};

weButton.setText = function (id, text) {
	var el = document.getElementById(id);
	if (el !== null && text !== undefined) {
		if (el.tagName === "BUTTON") {
			el.innerHTML = text;
			return;
		}
	}
};

weButton.hide = function (id) {
	var el = document.getElementById(id);
	if (el !== null) {
		el.style.display = "none";
	}
};

weButton.show = function (id) {
	var el = document.getElementById(id);
	if (el !== null) {
		el.style.display = "block";
	}
};

weButton.isDisabled = function (id) {
	var el = document.getElementById(id);
	return (el !== null && (el.tagName == "BUTTON" ? el.disabled : el.className == "weBtnDisabled"));
};

weButton.isEnabled = function (id) {
	return !this.isDisabled(id);
};

function switch_button_state(element, state) {
	switch (state) {
		case "enabled":
			weButton.enable(element);
			return true;
		case "disabled":
			weButton.disable(element);
	}

	return false;
}