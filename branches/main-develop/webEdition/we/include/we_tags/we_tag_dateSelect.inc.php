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

	$from = $to = [];
	$js = $checkDate = $minyear = $maxyear = '';
	if(!empty($tmp_from) && !empty($tmp_to)){
		$from = date_parse($tmp_from);
		$to = date_parse($tmp_to);
		$minyear = $from['year'];
		$maxyear = $to['year'];

		$js = we_html_element::jsScript(JS_DIR . 'tagCheckdate.js') .
			we_html_element::jsElement('
WE_checkDates["' . $name . '"]={
	from:new Date(' . $from['year'] . ', ' . $from['month'] . ', ' . $from['day'] . ', ' . $from['hour'] . ', ' . $from['minute'] . ', 0),
	to:new Date(' . $to['year'] . ', ' . $to['month'] . ', ' . $to['day'] . ', ' . $to['hour'] . ', ' . $to['minute'] . ', 59),
};

WE_checkDate("' . $name . '");');

		$checkDate = 'WE_checkDate("' . $name . '");';
	}

	$submitonchange = weTag_getAttribute('submitonchange', $attribs, false, we_base_request::BOOL);
	$time = we_base_request::_(we_base_request::HTML, $name, time());
	list(, $langcode) = getFieldOutLang($attribs, true);

	return we_html_tools::getDateInput($name . '%s', ($time == -1 ? time() : $time), false, "dmy", $submitonchange ? $checkDate . 'we_submitForm();' : $checkDate, $class, '', $minyear, $maxyear, $style, $langcode) . $js;
}
