/* global self, WE, top */

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

var WE_checkDates;
function WE_checkDate(name) {
	var from = WE_checkDates[name].from;
	var to = WE_checkDates[name].to;

	var now = new Date();

	var year = now.getFullYear();
	var month = now.getMonth();
	var day = now.getDate();
	var hour = now.getHours();
	var minute = now.getMinutes();
	var second = 30;

	for (i = 0; i < document.getElementById(name + '_month').length; ++i) {
		if (document.getElementById(name + '_month').options[i].selected == true) {
			month = document.getElementById(name + '_month').options[i].value - 1;
		}
	}
	for (i = 0; i < document.getElementById(name + '_year').length; ++i) {
		if (document.getElementById(name + '_year').options[i].selected == true) {
			year = document.getElementById(name + '_year').options[i].value;
		}
	}
	for (i = 0; i < document.getElementById(name + '_day').length; ++i) {
		if (document.getElementById(name + '_day').options[i].selected == true) {
			day = document.getElementById(name + '_day').options[i].value;
		}
	}
	if (document.getElementById(name + '_hour').type == 'select-one') {
		for (i = 0; i < document.getElementById(name + '_hour').length; ++i) {
			if (document.getElementById(name + '_hour').options[i].selected == true) {
				hour = document.getElementById(name + '_hour').options[i].value;
			}
		}
	}
	if (document.getElementById(name + '_minute').type == 'select-one') {
		for (i = 0; i < document.getElementById(name + '_minute').length; ++i) {
			if (document.getElementById(name + '_minute').options[i].selected == true) {
				minute = document.getElementById(name + '_minute').options[i].value;
			}
		}
	}

	var test = new Date(year, month, day, hour, minute, second);

	if (!(test.getTime() >= from.getTime() && test.getTime() < to.getTime())) {
		if (test.getTime() < from.getTime()) {
			correct = from;
		} else {
			correct = to;
		}
	} else {
		correct = test;
		while (correct.getMonth() != month) {
			correct.setDate(correct.getDate() - 1);
		}
	}
	for (i = 0; i < document.getElementById(name + '_year').length; ++i) {
		if (document.getElementById(name + '_year').options[i].value == correct.getFullYear()) {
			document.getElementById(name + '_year').options[i].selected = true;
		}
	}
	for (i = 0; i < document.getElementById(name + '_month').length; ++i) {
		if (document.getElementById(name + '_month').options[i].value == correct.getMonth() + 1) {
			document.getElementById(name + '_month').options[i].selected = true;
		}
	}
	for (i = 0; i < document.getElementById(name + '_day').length; ++i) {
		if (document.getElementById(name + '_day').options[i].value == correct.getDate()) {
			document.getElementById(name + '_day').options[i].selected = true;
		}
	}
	if (document.getElementById(name + '_hour').type == 'select-one') {
		for (i = 0; i < document.getElementById(name + '_hour').length; ++i) {
			if (document.getElementById(name + '_hour').options[i].value == correct.getHours()) {
				document.getElementById(name + '_hour').options[i].selected = true;
			}
		}
	}
	if (document.getElementById(name + '_minute').type == 'select-one') {
		for (i = 0; i < document.getElementById(name + '_minute').length; ++i) {
			if (document.getElementById(name + '_minute').options[i].value == correct.getMinutes()) {
				document.getElementById(name + '_minute').options[i].selected = true;
			}
		}
	}

}