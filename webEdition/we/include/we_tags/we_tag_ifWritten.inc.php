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
function we_tag_ifWritten(array $attribs){
	$type = (weTag_getAttribute('type', $attribs, '', we_base_request::STRING)? : weTag_getAttribute('var', $attribs, 'document', we_base_request::STRING))? : weTag_getAttribute('doc', $attribs, 'document', we_base_request::STRING);
	switch($type){
		case 'customer':
			return empty($GLOBALS['ERROR']['saveRegisteredUser']);
		default:
			$name = weTag_getAttribute('formname', $attribs, (empty($GLOBALS['WE_FORM']) ? 'we_global_form' : $GLOBALS['WE_FORM']), we_base_request::STRING);
			//we need isset if we:write was not called before
			return isset($GLOBALS['ERROR']['write'][$type][$name]) && empty($GLOBALS['ERROR']['write'][$type][$name]);
	}
}
