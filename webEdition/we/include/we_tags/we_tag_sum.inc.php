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
function we_tag_sum($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);

	$result = (isset($GLOBALS['summe'][$name]) ? we_base_util::std_numberformat($GLOBALS['summe'][$name]) : 0);

	return we_base_util::formatNumber($result, weTag_getAttribute('num_format', $attribs, '', we_base_request::STRING), weTag_getAttribute('decimals', $attribs, 2, we_base_request::INT));
}
