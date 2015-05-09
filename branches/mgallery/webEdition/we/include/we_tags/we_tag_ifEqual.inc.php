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
function we_tag_ifEqual($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return false;
	}
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$eqname = weTag_getAttribute('eqname', $attribs, '', we_base_request::STRING);
	$value = weTag_getAttribute('value', $attribs, '', we_base_request::RAW);

	if(!$eqname){
		if(($foo = attributFehltError($attribs, 'value', __FUNCTION__))){
			return false;
		}
		return ($GLOBALS['we_doc']->getElement($name) == $value);
	}

	if(($foo = attributFehltError($attribs, 'eqname', __FUNCTION__))){
		echo $foo;
		return false;
	}
	$elem = $GLOBALS['we_doc']->getElement($name);
	$blockeq = we_tag_getPostName($eqname);

	if($GLOBALS["WE_MAIN_DOC"]->getElement($blockeq)){//check if eqname is present in block
		return ($elem == $GLOBALS["WE_MAIN_DOC"]->getElement($blockeq));
	}
	if($GLOBALS["WE_MAIN_DOC"]->getElement($eqname)){//check if eqname is present in document
		return ($elem == $GLOBALS["WE_MAIN_DOC"]->getElement($eqname));
	}
//check if eqname is present in GLOBALS
	return (isset($GLOBALS[$eqname])) && ($GLOBALS[$eqname] == $elem);
}
