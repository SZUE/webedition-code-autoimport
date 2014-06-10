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
function we_parse_tag_tr($attribs, $content){
	//NOTE: _type is an internal attribute.
	return '<?php printElement(' . we_tag_tagParser::printTag('tr', array('_type' => 'start')) . ');?>' . $content . '<?php printElement(' . we_tag_tagParser::printTag('tr', array('_type' => 'end')) . ');?>';
}

function we_tag_tr($attribs){
	$_type = weTag_getAttribute('_type', $attribs);
	$attribs = removeAttribs($attribs, array('_type'));

	switch($_type){
		case 'start':
			return ($GLOBALS["lv"]->shouldPrintStartTR() ? getHtmlTag('tr', $attribs, '', false, true) : '');
		case 'end':
			return ($GLOBALS["lv"]->shouldPrintEndTR() ? '</tr>' : '');
	}
}
