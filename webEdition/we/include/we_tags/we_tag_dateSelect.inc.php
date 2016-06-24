<?php

/**
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
function we_tag_dateSelect(array $attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$class = weTag_getAttribute('class', $attribs, '', we_base_request::STRING);
	$style = weTag_getAttribute('style', $attribs, '', we_base_request::STRING);

	$tmp_from = weTag_getAttribute('start', $attribs, '', we_base_request::STRING);
	$tmp_to = weTag_getAttribute('end', $attribs, '', we_base_request::STRING);

	$from = [];
	$to = [];
	$js = $checkDate = $minyear = $maxyear = '';
	if(!empty($tmp_from) && !empty($tmp_to)){
		$from = date_parse($tmp_from);
		$to = date_parse($tmp_to);
		$minyear = $from['year'];
		$maxyear = $to['year'];

		$js = we_html_element::jsElement('
function WE_checkDate_' . $name . '() {

	var name = \'' . $name . '\';

	var from = new Date(' . $from['year'] . ', ' . $from['month'] . ', ' . $from['day'] . ', ' . $from['hour'] . ', ' . $from['minute'] . ', 0);
	var to   = new Date(' . $to['year'] . ', ' . $to['month'] . ', ' . $to['day'] . ', ' . $to['hour'] . ', ' . $to['minute'] . ', 59);

	var now = new Date();

	var year = now.getFullYear();
	var month = now.getMonth();
	var day = now.getDate();
	var hour = now.getHours();
	var minute = now.getMinutes();
	var second = 30;

	for (i = 0; i < document.getElementById(name + \'_month\').length; ++i) {
		if (document.getElementById(name + \'_month\').options[i].selected == true) {
			month = document.getElementById(name + \'_month\').options[i].value-1;
		}
	}
	for (i = 0; i < document.getElementById(name + \'_year\').length; ++i) {
		if (document.getElementById(name + \'_year\').options[i].selected == true) {
			year = document.getElementById(name + \'_year\').options[i].value;
		}
	}
	for (i = 0; i < document.getElementById(name + \'_day\').length; ++i) {
		if (document.getElementById(name + \'_day\').options[i].selected == true) {
			day = document.getElementById(name + \'_day\').options[i].value;
		}
	}
	if(document.getElementById(name + \'_hour\').type == \'select-one\') {
		for (i = 0; i < document.getElementById(name + \'_hour\').length; ++i) {
			if (document.getElementById(name + \'_hour\').options[i].selected == true) {
				hour = document.getElementById(name + \'_hour\').options[i].value;
			}
		}
	}
	if(document.getElementById(name + \'_minute\').type == \'select-one\') {
		for (i = 0; i < document.getElementById(name + \'_minute\').length; ++i) {
			if (document.getElementById(name + \'_minute\').options[i].selected == true) {
				minute = document.getElementById(name + \'_minute\').options[i].value;
			}
		}
	}

	var test = new Date(year, month, day, hour, minute, second);

	if(!(test.getTime() >= from.getTime() && test.getTime() < to.getTime())) {
		if(test.getTime() < from.getTime()) {
			correct = from;
		} else {
			correct = to;
		}
	} else {
		correct = test;
		while(correct.getMonth() != month) {
			correct.setDate(correct.getDate()-1);
		}
	}
	for (i = 0; i < document.getElementById(name + \'_year\').length; ++i) {
		if (document.getElementById(name + \'_year\').options[i].value == correct.getFullYear()) {
			document.getElementById(name + \'_year\').options[i].selected = true;
		}
	}
	for (i = 0; i < document.getElementById(name + \'_month\').length; ++i) {
		if (document.getElementById(name + \'_month\').options[i].value == correct.getMonth()+1) {
			document.getElementById(name + \'_month\').options[i].selected = true;
		}
	}
	for (i = 0; i < document.getElementById(name + \'_day\').length; ++i) {
		if (document.getElementById(name + \'_day\').options[i].value == correct.getDate()) {
			document.getElementById(name + \'_day\').options[i].selected = true;
		}
	}
	if(document.getElementById(name + \'_hour\').type == \'select-one\') {
		for (i = 0; i < document.getElementById(name + \'_hour\').length; ++i) {
			if (document.getElementById(name + \'_hour\').options[i].value == correct.getHours()) {
				document.getElementById(name + \'_hour\').options[i].selected = true;
			}
		}
	}
	if(document.getElementById(name + \'_minute\').type == \'select-one\') {
		for (i = 0; i < document.getElementById(name + \'_minute\').length; ++i) {
			if (document.getElementById(name + \'_minute\').options[i].value == correct.getMinutes()) {
				document.getElementById(name + \'_minute\').options[i].selected = true;
			}
		}
	}

}
WE_checkDate_' . $name . '();');


		$checkDate = 'WE_checkDate_' . $name . '();';
	}

	$submitonchange = weTag_getAttribute('submitonchange', $attribs, false, we_base_request::BOOL);
	$time = we_base_request::_(we_base_request::HTML, $name, time());
	return we_html_tools::getDateInput($name . '%s', ($time == -1 ? time() : $time), false, "dmy", $submitonchange ? $checkDate . 'we_submitForm();' : $checkDate, $class, '', $minyear, $maxyear, $style) . $js;
}
