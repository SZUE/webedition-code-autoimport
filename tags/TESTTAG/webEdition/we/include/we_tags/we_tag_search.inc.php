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
function we_tag_search($attribs){

	$name = weTag_getAttribute('name', $attribs, 0, we_base_request::STRING);
	$xml = weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, we_base_request::BOOL);
	$value = weTag_getAttribute('value', $attribs, '', we_base_request::RAW);

	$searchValue = str_replace(array('"', '\\"',), '', trim(we_base_request::_(we_base_request::STRING, 'we_lv_search_' . $name, $value)));
	$attsHidden = array(
		'type' => 'hidden',
		'xml' => $xml,
		'name' => 'we_from_search_' . $name,
		'value' => (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode'] ? 0 : 1)
	);


	switch(weTag_getAttribute('type', $attribs, '', we_base_request::STRING)){
		case 'print':
			return $searchValue;
		case 'textinput':
			$atts = array_merge(
					removeAttribs($attribs, array(
				'type', 'onchange', 'name', 'cols', 'rows'
					)), array(
				'name' => 'we_lv_search_' . $name,
				'type' => 'text',
				'value' => $searchValue,
				'xml' => $xml
			));
			return getHtmlTag('input', $atts) . getHtmlTag('input', $attsHidden);

		case 'textarea':
			$atts = array_merge(
					removeAttribs(
							$attribs, array(
				'type', 'onchange', 'name', 'size', 'maxlength', 'value'
					)), array(
				'class' => 'defaultfont',
				'name' => 'we_lv_search_' . $name,
				'xml' => $xml
			));

			return getHtmlTag('textarea', $atts, $searchValue, true) . getHtmlTag('input', $attsHidden);
	}
}
